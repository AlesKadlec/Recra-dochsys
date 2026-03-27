<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

include('db.php');
include('funkce.php');

global $conn;

/* =========================
   DEBUG (zapni přes ?debug=1)
========================= */
$DEBUG = (isset($_GET['debug']) && $_GET['debug'] === '1');

function dbg(string $msg): void {
    if (!empty($_SESSION['premier_debug_enabled'])) {
        $_SESSION['premier_debug_log'][] = '[' . date('H:i:s') . '] ' . $msg;
    }
    error_log('[xml.php] ' . $msg);
}

if ($DEBUG) {
    $_SESSION['premier_debug_enabled'] = true;
    if (!isset($_SESSION['premier_debug_log'])) $_SESSION['premier_debug_log'] = [];
} else {
    unset($_SESSION['premier_debug_enabled']);
}

/* =========================
   AUTH HELPERS
========================= */
function unauthorized(string $redirect): void
{
    echo '<h1 class="text-center m-2 p-2">NEAUTORIZOVANÝ PŘÍSTUP</h1>';
    echo '<meta http-equiv="refresh" content="5;url=' . htmlspecialchars($redirect, ENT_QUOTES, 'UTF-8') . '">';
}

/* =========================
   1) MAP: customer_id -> Premier ID
========================= */
function get_premier_id_from_customer_id(string $os_cislo_klient): array
{
    global $conn;

    $raw = trim($os_cislo_klient);
    if ($raw === '') {
        return ['value' => 'Nezjištěno', 'found' => false];
    }

    // "R1234-01" => "R1234"
    $base = preg_split('/\s*-\s*/', $raw, 2)[0] ?? $raw;
    $base = trim((string)$base);

    // hledat jen prvních 5 znaků (např. "R0923")
    $key5 = mb_substr($base, 0, 5, 'UTF-8');
    $key5 = trim((string)$key5);

    if ($key5 === '') {
        return ['value' => 'Nezjištěno', 'found' => false];
    }

    $stmt = $conn->prepare("
        SELECT os_cislo
        FROM zamestnanci
        WHERE LEFT(os_cislo_klient, 5) = ?
           OR os_cislo_klient = ?
        LIMIT 1
    ");
    if (!$stmt) {
        return ['value' => 'Nezjištěno', 'found' => false];
    }

    $stmt->bind_param("ss", $key5, $raw);
    $stmt->execute();
    $res = $stmt->get_result();

    $val = '';
    if ($res && ($row = $res->fetch_assoc())) {
        $val = (string)($row['os_cislo'] ?? '');
    }
    $stmt->close();

    $val = trim($val);
    if ($val === '' || $val === '0') {
        return ['value' => 'Nezjištěno', 'found' => false];
    }

    return ['value' => $val, 'found' => true];
}

/* =========================
   XML READ
========================= */
function readXmlFileString(string $path): string
{
    if (!is_file($path)) {
        throw new RuntimeException("Soubor nenalezen.");
    }
    $s = file_get_contents($path);
    if ($s === false) {
        throw new RuntimeException("Nelze číst XML soubor.");
    }
    return $s;
}

/* =========================
   2) SCAN missing (po uploadu)
   - jen zjišťuje, nic nemění
========================= */
function scanMissingPremierIds(string $xmlContent): array
{
    libxml_use_internal_errors(true);

    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = true;
    $dom->formatOutput = false;

    if (!$dom->loadXML($xmlContent, LIBXML_NONET)) {
        $errs = libxml_get_errors();
        libxml_clear_errors();
        $msg = "Chyba při parsování XML:\n";
        foreach ($errs as $e) {
            $msg .= "- " . trim($e->message) . " (line {$e->line})\n";
        }
        throw new RuntimeException(strip_tags($msg));
    }

    $root = $dom->documentElement;
    $ns = $root?->namespaceURI ?? '';

    $xp = new DOMXPath($dom);
    if ($ns !== '') {
        $xp->registerNamespace('ns', $ns);
        $hlavicky = $xp->query('//ns:dochazka_zamestnance/ns:hlavicka');
    } else {
        $hlavicky = $xp->query('//dochazka_zamestnance/hlavicka');
    }

    $missing = [];
    if ($hlavicky) {
        foreach ($hlavicky as $h) {
            $nodeOsList = ($ns !== '')
                ? $xp->query('ns:osobni_cislo', $h)
                : $xp->query('osobni_cislo', $h);

            if (!$nodeOsList || $nodeOsList->length === 0) continue;

            $orig = trim((string)$nodeOsList->item(0)?->textContent);
            if ($orig === '') continue;

            $map = get_premier_id_from_customer_id($orig);
            if (!$map['found']) {
                $missing[$orig] = true;
            }
        }
    }

    $list = array_keys($missing);
    sort($list, SORT_NATURAL);
    return $list;
}

/* =========================
   3) BUILD Export (pro Premier)
   - osobni_cislo => Premier ID
   - cislo_pracovniho_pomeru => stejné jako osobni_cislo (Premier ID)
   - pracovni_pomer self-closing => párový tag
   - vrací [xmlOut1250, missingList]
========================= */
function buildPremierExport(string $xmlContent): array
{
    libxml_use_internal_errors(true);

    // ------------------------------------------------------------
    // 0) NORMALIZACE VSTUPNÍHO XML DO UTF-8 (kvůli diakritice)
    // ------------------------------------------------------------
    $declEnc = null;
    if (preg_match('/<\?xml\s+version="1\.[0-9]+"\s+encoding="([^"]+)"\s*\?>/i', $xmlContent, $m)) {
        $declEnc = strtoupper(trim($m[1]));
    }

    // Pokud deklarace není, ber jako UTF-8 (většinou ok)
    if ($declEnc && $declEnc !== 'UTF-8') {
        // Překlop do UTF-8, aby DOM pracoval korektně
        $converted = @iconv($declEnc, 'UTF-8//TRANSLIT', $xmlContent);
        if ($converted === false) {
            // když by TRANSLIT selhal, zkus bez translitu
            $converted = @iconv($declEnc, 'UTF-8', $xmlContent);
        }
        if ($converted === false) {
            throw new RuntimeException("Nelze převést vstupní XML z encodingu {$declEnc} do UTF-8 (iconv selhalo).");
        }

        // Přepiš deklaraci na UTF-8, aby libxml neřešil původní encoding
        $xmlContent = preg_replace(
            '/<\?xml\s+version="1\.[0-9]+"\s+encoding="[^"]+"\s*\?>/i',
            '<?xml version="1.0" encoding="UTF-8"?>',
            $converted,
            1
        ) ?? $converted;
    }

    // ------------------------------------------------------------
    // 1) PARSE DOM
    // ------------------------------------------------------------
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = true;
    $dom->formatOutput = false;

    if (!$dom->loadXML($xmlContent, LIBXML_NONET)) {
        $errs = libxml_get_errors();
        libxml_clear_errors();
        $msg = "Chyba při parsování XML:\n";
        foreach ($errs as $e) {
            $msg .= "- " . trim($e->message) . " (line {$e->line})\n";
        }
        throw new RuntimeException(nl2br(htmlspecialchars($msg, ENT_QUOTES, 'UTF-8')));
    }

    $root = $dom->documentElement;
    $ns = $root?->namespaceURI ?? '';

    $xp = new DOMXPath($dom);
    if ($ns !== '') {
        $xp->registerNamespace('ns', $ns);
        $hlavicky = $xp->query('//ns:dochazka_zamestnance/ns:hlavicka');
    } else {
        $hlavicky = $xp->query('//dochazka_zamestnance/hlavicka');
    }

    $missing = [];

    if ($hlavicky) {
        foreach ($hlavicky as $h) {

            // osobni_cislo
            $nodeOs = null;
            $nodeOsList = ($ns !== '')
                ? $xp->query('ns:osobni_cislo', $h)
                : $xp->query('osobni_cislo', $h);

            if ($nodeOsList && $nodeOsList->length > 0) {
                $nodeOs = $nodeOsList->item(0);
            }
            if (!$nodeOs) continue;

            $orig = trim((string)$nodeOs->textContent);
            if ($orig === '') continue;

            $map = get_premier_id_from_customer_id($orig);

            if ($map['found']) {
                $premierId = (string)$map['value'];

                // 1) osobni_cislo
                $nodeOs->textContent = $premierId;

                // 2) cislo_pracovniho_pomeru
                $ppList = ($ns !== '')
                    ? $xp->query('ns:cislo_pracovniho_pomeru', $h)
                    : $xp->query('cislo_pracovniho_pomeru', $h);

                if ($ppList && $ppList->length > 0) {
                    $nodePP = $ppList->item(0);
                    if ($nodePP) $nodePP->textContent = $premierId;
                }

            } else {
                $missing[$orig] = true;
            }
        }
    }

    if (!empty($missing)) {
        $list = array_keys($missing);
        sort($list, SORT_NATURAL);

        $max = 50;
        $short = array_slice($list, 0, $max);
        $note = "Missing Premier IDs for: " . implode(', ', $short);
        if (count($list) > $max) $note .= " ... (+" . (count($list) - $max) . " more)";

        $comment = $dom->createComment($note);
        if ($root && $root->firstChild) $root->insertBefore($comment, $root->firstChild);
        elseif ($root) $root->appendChild($comment);
    }

    // ------------------------------------------------------------
    // 2) OUTPUT UTF-8 + úpravy a až pak převod do Windows-1250
    // ------------------------------------------------------------
    $outUtf8 = $dom->saveXML();
    if ($outUtf8 === false || $outUtf8 === null) {
        throw new RuntimeException("Nepovedlo se vytvořit výstupní XML (saveXML).");
    }

    // deklarace pro finální soubor
    $outUtf8 = preg_replace(
        '/<\?xml\s+version="1\.[0-9]+"\s+encoding="[^"]*"\s*\?>/i',
        '<?xml version="1.0" encoding="Windows-1250"?>',
        $outUtf8,
        1
    ) ?? $outUtf8;

    // <pracovni_pomer/>
    $outUtf8 = preg_replace('/<pracovni_pomer\s*\/>/i', '<pracovni_pomer></pracovni_pomer>', $outUtf8) ?? $outUtf8;

    // KLÍČ: nepoužívat //IGNORE (to zahodí diakritiku!)
    $out1250 = @iconv('UTF-8', 'Windows-1250//TRANSLIT', $outUtf8);
    if ($out1250 === false) {
        // když tohle selže, radši spadnout než vyexportovat “osekaný” text
        throw new RuntimeException("Konverze do Windows-1250 selhala (iconv). Vstup nejspíš obsahuje nevalidní znaky/encoding.");
    }

    return [$out1250, array_keys($missing)];
}

/* =========================
   4) TEMP FILE HELPERS (export bez bílé stránky)
========================= */
function ensureTmpDir(): string
{
    $dir = __DIR__ . '/uploads_tmp';
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    return $dir;
}

function safeFilename(string $name): string
{
    return preg_replace('/[^a-zA-Z0-9._-]+/', '_', $name) ?: 'dochazka.xml';
}

/* =========================
   AUTH + REQUEST HANDLING
   (NESMÍ být output před headery)
========================= */

// AUTH (PRG)
if (kontrola_prihlaseni() !== "OK") {
    $_SESSION['premier_auth_error'] = 'login';
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}
if (!isset($_SESSION["typ"]) || !in_array((string)$_SESSION["typ"], ["5","1","4"], true)) {
    $_SESSION['premier_auth_error'] = 'main';
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// DOWNLOAD export (GET ?download=1)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['download']) && $_GET['download'] === '1') {
    $exportPath = (string)($_SESSION['premier_export_path'] ?? '');
    $exportName = (string)($_SESSION['premier_export_name'] ?? 'dochazka_premier.xml');

    if ($exportPath !== '' && is_file($exportPath)) {
        dbg("DOWNLOAD: streaming $exportPath");

        header('Content-Type: application/xml; charset=Windows-1250');
        header('Content-Disposition: attachment; filename="' . $exportName . '"');
        header('X-Content-Type-Options: nosniff');
        header('Content-Length: ' . filesize($exportPath));

        readfile($exportPath);

        // uklidit (jednorázově)
        @unlink($exportPath);
        unset($_SESSION['premier_export_path'], $_SESSION['premier_export_name']);
        exit;
    }

    $_SESSION['premier_flash_error'] = 'Exportní soubor už není dostupný (zkus export znovu).';
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// POST akce
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    // UPLOAD
    if ($action === 'upload' && isset($_FILES['file'])) {

        dbg("UPLOAD start");

        if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['premier_flash_error'] = 'Chyba při nahrávání souboru.';
            header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
            exit;
        }

        $origName = (string)($_FILES['file']['name'] ?? 'dochazka.xml');
        if (!preg_match('/\.xml$/i', $origName)) {
            $_SESSION['premier_flash_error'] = 'Soubor musí být .xml';
            header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
            exit;
        }

        $maxBytes = 10 * 1024 * 1024; // 10MB
        $size = (int)($_FILES['file']['size'] ?? 0);
        if ($size > $maxBytes) {
            $_SESSION['premier_flash_error'] = 'Soubor je příliš velký (max 10MB).';
            header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
            exit;
        }

        $tmp = (string)($_FILES['file']['tmp_name'] ?? '');
        if ($tmp === '' || !is_uploaded_file($tmp)) {
            $_SESSION['premier_flash_error'] = 'Upload tmp soubor není validní.';
            header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
            exit;
        }

        $dir = ensureTmpDir();
        $safeName = safeFilename($origName);
        $target = $dir . '/' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '_' . $safeName;

        if (!move_uploaded_file($tmp, $target)) {
            $_SESSION['premier_flash_error'] = 'Nelze uložit nahraný soubor.';
            header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
            exit;
        }

        $_SESSION['premier_xml_path'] = $target;
        $_SESSION['premier_xml_name'] = $safeName;

        // ✅ hned po uploadu zjisti missing
        try {
            $xmlIn = readXmlFileString($target);
            $missingUpload = scanMissingPremierIds($xmlIn);
            $_SESSION['premier_upload_missing'] = $missingUpload;

            $_SESSION['premier_flash_ok'] = 'Soubor nahrán. Nepřeložených osobních čísel: ' . count($missingUpload) . '.';
            dbg("UPLOAD ok, missing=" . count($missingUpload));
        } catch (Throwable $e) {
            $_SESSION['premier_flash_error'] = 'Soubor nahrán, ale parsování selhalo: ' . strip_tags((string)$e->getMessage());
            dbg("UPLOAD parse error: " . $e->getMessage());
        }

        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?') . ($DEBUG ? '?debug=1' : ''));
        exit;
    }

    // EXPORT (vygeneruje soubor + redirect zpět)
    if ($action === 'export') {

        dbg("EXPORT start");

        $path = (string)($_SESSION['premier_xml_path'] ?? '');
        $orig = (string)($_SESSION['premier_xml_name'] ?? 'dochazka.xml');

        if ($path === '' || !is_file($path)) {
            $_SESSION['premier_flash_error'] = 'Nejdřív nahraj XML soubor.';
            header("Location: " . strtok($_SERVER["REQUEST_URI"], '?') . ($DEBUG ? '?debug=1' : ''));
            exit;
        }

        try {
            $xmlIn = readXmlFileString($path);
            [$xmlOut1250, $missing] = buildPremierExport($xmlIn);

            // uložit missing pro zobrazení hned po exportu
            $_SESSION['premier_export_missing'] = $missing;

            // uložit export do tmp souboru
            $dir = ensureTmpDir();
            $downloadName = preg_replace('/\.xml$/i', '', $orig) . '_premier.xml';
            $downloadName = safeFilename($downloadName);

            $exportPath = $dir . '/' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '_' . $downloadName;

            $w = file_put_contents($exportPath, $xmlOut1250);
            if ($w === false) {
                throw new RuntimeException("Nepovedlo se uložit export do dočasného souboru.");
            }

            $_SESSION['premier_export_path'] = $exportPath;
            $_SESSION['premier_export_name'] = $downloadName;

            $_SESSION['premier_flash_ok'] = 'Export vygenerován. Klikni na „Stáhnout export“. Nepřeložených: ' . count($missing) . '.';
            dbg("EXPORT ok, saved=$exportPath missing=" . count($missing));

            header("Location: " . strtok($_SERVER["REQUEST_URI"], '?') . ($DEBUG ? '?debug=1' : ''));
            exit;

        } catch (Throwable $e) {
            $_SESSION['premier_flash_error'] = 'Chyba exportu: ' . strip_tags((string)$e->getMessage());
            dbg("EXPORT error: " . $e->getMessage());
            header("Location: " . strtok($_SERVER["REQUEST_URI"], '?') . ($DEBUG ? '?debug=1' : ''));
            exit;
        }
    }

    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?') . ($DEBUG ? '?debug=1' : ''));
    exit;
}

/* =========================
   RENDER (odtud už může být HTML)
========================= */

$auth_err = $_SESSION['premier_auth_error'] ?? null;
unset($_SESSION['premier_auth_error']);

$flash_ok = $_SESSION['premier_flash_ok'] ?? null;
unset($_SESSION['premier_flash_ok']);

$flash_err = $_SESSION['premier_flash_error'] ?? null;
unset($_SESSION['premier_flash_error']);

$upload_missing = $_SESSION['premier_upload_missing'] ?? null; // zobrazení po uploadu
unset($_SESSION['premier_upload_missing']);

$export_missing = $_SESSION['premier_export_missing'] ?? null; // zobrazení po exportu
unset($_SESSION['premier_export_missing']);

$exportReady = (!empty($_SESSION['premier_export_path']) && is_file((string)$_SESSION['premier_export_path']));
$exportName = (string)($_SESSION['premier_export_name'] ?? '');

$debugLog = $_SESSION['premier_debug_log'] ?? [];
if ($DEBUG) {
    // necháme log být vidět, ale nezvětšujme donekonečna
    if (count($debugLog) > 200) {
        $debugLog = array_slice($debugLog, -200);
        $_SESSION['premier_debug_log'] = $debugLog;
    }
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
    <title>Zpracování docházkových dat</title>
</head>
<body>
<?php
menu();

if ($auth_err === 'login') {
    unauthorized('login.php');
    echo "</body></html>";
    exit;
}
if ($auth_err === 'main') {
    unauthorized('main.php');
    echo "</body></html>";
    exit;
}
?>

<div class="container my-3">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h1 class="mb-0">Nahrání XML souboru</h1>
        <div class="small">
            <?php if ($DEBUG): ?>
                <span class="badge bg-warning text-dark">DEBUG ON</span>
            <?php else: ?>
                <a class="btn btn-sm btn-outline-secondary" href="?debug=1">Zapnout debug</a>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($flash_ok): ?>
        <div class="alert alert-success mt-3"><?= htmlspecialchars((string)$flash_ok, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if ($flash_err): ?>
        <div class="alert alert-danger mt-3"><?= htmlspecialchars((string)$flash_err, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data" class="card p-3 mt-3 mb-3">
        <input type="hidden" name="action" value="upload">
        <label for="file" class="form-label">Vyberte XML soubor:</label>
        <input type="file" id="file" name="file" accept=".xml" required class="form-control">
        <div class="mt-3 d-flex gap-2 flex-wrap">
            <button type="submit" class="btn btn-primary">Nahrát</button>
        </div>
        <?php if (!empty($_SESSION['premier_xml_name'])): ?>
            <div class="small text-muted mt-2">
                Poslední nahraný soubor: <code><?= htmlspecialchars((string)$_SESSION['premier_xml_name'], ENT_QUOTES, 'UTF-8') ?></code>
            </div>
        <?php endif; ?>
    </form>

    <form action="" method="post" class="mb-2">
        <input type="hidden" name="action" value="export">
        <button type="submit" class="btn btn-success"
            <?= (empty($_SESSION['premier_xml_path']) || !is_file((string)$_SESSION['premier_xml_path'])) ? 'disabled' : '' ?>>
            Vygenerovat export pro Premier
        </button>
        <div class="small text-muted mt-2">
            Export zachová zdrojové XML, nahradí <code>&lt;osobni_cislo&gt;</code> Premier ID a nastaví
            <code>&lt;cislo_pracovniho_pomeru&gt;</code> na stejné ID. Tag <code>&lt;pracovni_pomer/&gt;</code> se převede na párový.
        </div>
    </form>

    <?php if ($exportReady): ?>
        <div class="card p-3 mb-3 border-success">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <div class="fw-bold">Export je připraven</div>
                    <div class="small text-muted">Soubor: <code><?= htmlspecialchars($exportName, ENT_QUOTES, 'UTF-8') ?></code></div>
                </div>
                <a class="btn btn-success" href="?download=1<?= $DEBUG ? '&debug=1' : '' ?>">
                    Stáhnout export
                </a>
            </div>
        </div>
    <?php endif; ?>

    <?php if (is_array($upload_missing) && !empty($upload_missing)): ?>
        <div class="alert alert-warning">
            <div class="fw-bold mb-2">Po nahrání: nepodařilo se přeložit některá osobní čísla</div>
            <div class="small text-muted mb-2">Tyto hodnoty nemají odpovídající Premier ID v tabulce <code>zamestnanci</code>:</div>
            <ul class="mb-0">
                <?php foreach ($upload_missing as $m): ?>
                    <li><code><?= htmlspecialchars((string)$m, ENT_QUOTES, 'UTF-8') ?></code></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php elseif (is_array($upload_missing) && empty($upload_missing)): ?>
        <div class="alert alert-success">
            <div class="fw-bold">Po nahrání: vše OK</div>
            <div class="small text-muted">Všechna osobní čísla šla přeložit na Premier ID.</div>
        </div>
    <?php endif; ?>

    <?php if (is_array($export_missing) && !empty($export_missing)): ?>
        <div class="alert alert-warning">
            <div class="fw-bold mb-2">Po exportu: některé hodnoty nešly přeložit na Premier ID</div>
            <div class="small text-muted mb-2">V exportu zůstaly původní:</div>
            <ul class="mb-0">
                <?php foreach ($export_missing as $m): ?>
                    <li><code><?= htmlspecialchars((string)$m, ENT_QUOTES, 'UTF-8') ?></code></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php elseif (is_array($export_missing) && empty($export_missing)): ?>
        <div class="alert alert-success">
            <div class="fw-bold">Po exportu: vše OK</div>
            <div class="small text-muted">Všechny hodnoty byly přeloženy.</div>
        </div>
    <?php endif; ?>

    <?php if ($DEBUG): ?>
        <div class="card p-3 mt-3">
            <div class="fw-bold mb-2">Debug log</div>
            <?php if (!empty($debugLog)): ?>
                <pre class="mb-0" style="max-height:260px; overflow:auto; font-size:0.85rem;"><?= htmlspecialchars(implode("\n", $debugLog), ENT_QUOTES, 'UTF-8') ?></pre>
            <?php else: ?>
                <div class="text-muted small">Zatím nic.</div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</body>
</html>