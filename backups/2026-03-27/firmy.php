<?php
include('db.php');
include('funkce.php');

function h($s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

/**
 * Bezpečně převede seznam ID typu "1,2,3" na pole intů.
 */
function parseIdList($value): array
{
    if (is_array($value)) {
        $items = $value;
    } else {
        $items = explode(',', (string)$value);
    }

    $out = [];
    foreach ($items as $item) {
        $id = (int)trim((string)$item);
        if ($id > 0) {
            $out[] = $id;
        }
    }

    return array_values(array_unique($out));
}

/**
 * Vrátí string "?,?,?" pro IN klauzuli.
 */
function makePlaceholders(int $count): string
{
    return implode(',', array_fill(0, $count, '?'));
}

/**
 * Helper pro bind_param s proměnným počtem parametrů.
 */
function stmt_bind_params(mysqli_stmt $stmt, string $types, array &$params): void
{
    $bindNames = [];
    $bindNames[] = $types;

    foreach ($params as $key => &$value) {
        $bindNames[] = &$params[$key];
    }

    call_user_func_array([$stmt, 'bind_param'], $bindNames);
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>HLAVNÍ STRÁNKA</title>
</head>
<body>

<script>
    function aktualizujCas() {
        $.ajax({
            url: 'cas.php',
            success: function(data) {
                $('#cas').text(data);
            }
        });
    }

    $(document).ready(function() {
        aktualizujCas();
        setInterval(aktualizujCas, 1000);
    });
</script>

<?php

global $conn;

menu();

if (kontrola_prihlaseni() == "OK") {

    if (isset($_GET["typ"])) {

        $typ = (string)$_GET["typ"];
        $canManageFirms = in_array((string)($_SESSION["typ"] ?? ''), ['1', '4', '5'], true);

        if ($typ === "updatefirma" && $canManageFirms) {

            $idFirmy = isset($_POST["id_firmy"]) ? (int)$_POST["id_firmy"] : 0;
            $nazevFirmy = trim((string)($_POST["nazevfirmy"] ?? ''));
            $aktivni = isset($_POST["aktivni"]) ? (int)$_POST["aktivni"] : 0;
            $objednavka = trim((string)($_POST["objednavka"] ?? ''));

            if ($idFirmy <= 0 || $nazevFirmy === '') {
                echo "<div class='container'><h3 class='text-center m-2 p-2'>Neplatná data formuláře</h3></div>";
                echo '<meta http-equiv="refresh" content="3;url=firmy.php">';
            } else {
                $stmt = $conn->prepare("UPDATE firmy SET firma = ?, aktivni = ?, objednavka = ? WHERE id = ?");
                if (!$stmt) {
                    die("Nelze připravit dotaz.</body></html>");
                }

                $stmt->bind_param("sisi", $nazevFirmy, $aktivni, $objednavka, $idFirmy);

                if (!$stmt->execute()) {
                    die("Nelze provést dotaz.</body></html>");
                }

                $stmt->close();

                $now = new DateTime('now', new DateTimeZone('Europe/Prague'));
                $kdo = (string)($_SESSION["log_id"] ?? '');

                $stmtLog = $conn->prepare("INSERT INTO logs (kdo, typ, infotext, datumcas) VALUES (?, ?, ?, ?)");
                if (!$stmtLog) {
                    die("Nelze připravit log dotaz.</body></html>");
                }

                $logTyp = 'Editace firmy';
                $logInfo = 'Editace firmy ' . $nazevFirmy;
                $datumCas = $now->format('Y-m-d H:i:s');

                $stmtLog->bind_param("ssss", $kdo, $logTyp, $logInfo, $datumCas);

                if (!$stmtLog->execute()) {
                    die("Nelze provést log dotaz.</body></html>");
                }

                $stmtLog->close();
                ?>

                <div class="container">
                    <h3 class="text-center m-2 p-2">Firma byla upravena</h3>
                    <h3 class="text-center m-2 p-2">Budete přesměrování zpět na firmy</h3>
                </div>

                <meta http-equiv="refresh" content="5;url=firmy.php">

                <?php
            }

        } elseif ($typ === "vytvorfirmu" && $canManageFirms) {

            $nazevFirmy = trim((string)($_POST["nazevfirmy"] ?? ''));
            $aktivni = isset($_POST["aktivni"]) ? (int)$_POST["aktivni"] : 0;
            $objednavka = trim((string)($_POST["objednavka"] ?? ''));

            if ($nazevFirmy === '') {
                echo "<div class='container'><h3 class='text-center m-2 p-2'>Neplatná data formuláře</h3></div>";
                echo '<meta http-equiv="refresh" content="3;url=firmy.php">';
            } else {
                $stmt = $conn->prepare("INSERT INTO firmy (firma, aktivni, objednavka) VALUES (?, ?, ?)");
                if (!$stmt) {
                    die("Nelze připravit dotaz.</body></html>");
                }

                $stmt->bind_param("sis", $nazevFirmy, $aktivni, $objednavka);

                if (!$stmt->execute()) {
                    die("Nelze provést dotaz.</body></html>");
                }

                $stmt->close();

                $now = new DateTime('now', new DateTimeZone('Europe/Prague'));
                $kdo = (string)($_SESSION["log_id"] ?? '');

                $stmtLog = $conn->prepare("INSERT INTO logs (kdo, typ, infotext, datumcas) VALUES (?, ?, ?, ?)");
                if (!$stmtLog) {
                    die("Nelze připravit log dotaz.</body></html>");
                }

                $logTyp = 'Nová firma';
                $logInfo = 'Vytvořena nová firma ' . $nazevFirmy;
                $datumCas = $now->format('Y-m-d H:i:s');

                $stmtLog->bind_param("ssss", $kdo, $logTyp, $logInfo, $datumCas);

                if (!$stmtLog->execute()) {
                    die("Nelze provést log dotaz.</body></html>");
                }

                $stmtLog->close();
                ?>

                <div class="container">
                    <h3 class="text-center m-2 p-2">Firma byla založena</h3>
                    <h3 class="text-center m-2 p-2">Budete přesměrování zpět na firmy</h3>
                </div>

                <meta http-equiv="refresh" content="5;url=firmy.php">

                <?php
            }

        } else { ?>
            <div class="container">
                <h3 class="text-center m-2 p-2">NEPOVOLENÁ OPERACE</h3>
                <h3 class="text-center m-2 p-2">Budete přesměrování zpět HLAVNÍ OBRAZOVKU</h3>
            </div>

            <meta http-equiv="refresh" content="5;url=main.php">
            <?php
        }

    } else { ?>

        <h1 class="text-center m-2 p-2">HLAVNÍ STRÁNKA RECRA SYSTÉMU</h1>
        <h3 class="text-center m-2 p-2">Přehled firem</h3>

        <?php
        if (isset($_POST["radioGroup"])) {
            $selectedOption = (int)$_POST["radioGroup"];
            if ($selectedOption !== 2) {
                $selectedOption = 1;
            }
        } else {
            $selectedOption = 1;
        }

        $vyber1 = ($selectedOption === 1) ? "checked" : "";
        $vyber2 = ($selectedOption === 2) ? "checked" : "";

        if (in_array((string)($_SESSION["typ"] ?? ''), ['1', '4', '5'], true)) { ?>

            <form action="firmy.php" method="post">
                <div class="d-grid gap-2 d-md-flex justify-content-md-center">

                    <button type="button" class="btn btn-outline-primary text-center m-2" data-bs-toggle="modal" data-bs-target="#firma_new">
                        Nová firma
                    </button>

                    <div class="form-check m-2">
                        <input class="form-check-input m-2" type="radio" name="radioGroup" id="firmaaktiv" value="1" <?= $vyber1; ?>>
                        <label class="form-check-label m-2" for="firmaaktiv">
                            Aktivní firmy
                        </label>
                    </div>

                    <div class="form-check m-2">
                        <input class="form-check-input m-2" type="radio" name="radioGroup" id="firmaall" value="2" <?= $vyber2; ?>>
                        <label class="form-check-label m-2" for="firmaall">
                            Všechny firmy
                        </label>
                    </div>

                    <button type="submit" class="btn btn-outline-primary">Vyber</button>
                </div>
            </form>

        <?php } ?>

        <div class="container">
            <div class="row justify-content-md-center">
                <div class="col col-md-12">

                    <br>
                    <div class="table-responsive-lg text-center">
                        <table class="table table-hover">
                            <thead>
                                <tr class="table-active">
                                    <th scope="col">ID</th>
                                    <th scope="col">Firma</th>
                                    <th scope="col">Aktivní<br>Neaktivní</th>
                                    <th scope="col">Zaměstnanců<br>Objednávka</th>
                                    <th scope="col">Docházky</th>

                                    <?php if (in_array((string)($_SESSION["typ"] ?? ''), ['1', '4', '5'], true)) { ?>
                                        <th scope="col">Editace</th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>

                            <?php
                            $cislo = 1;
                            $sessionTyp = (string)($_SESSION["typ"] ?? '');

                            if ($sessionTyp === '5') {
                                if ($selectedOption === 1) {
                                    $stmt = $conn->prepare("SELECT id, firma, aktivni FROM firmy WHERE aktivni = ? ORDER BY id");
                                    $aktivniFilter = 1;
                                    $stmt->bind_param("i", $aktivniFilter);
                                } else {
                                    $stmt = $conn->prepare("SELECT id, firma, aktivni FROM firmy ORDER BY id");
                                }
                            } else {
                                $firmyIds = parseIdList($_SESSION["firma"] ?? '');

                                if (count($firmyIds) === 0) {
                                    $stmt = null;
                                } else {
                                    $placeholders = makePlaceholders(count($firmyIds));

                                    if ($selectedOption === 1) {
                                        $sql = "SELECT id, firma, aktivni FROM firmy WHERE id IN ($placeholders) AND aktivni = ? ORDER BY id";
                                        $stmt = $conn->prepare($sql);

                                        if ($stmt) {
                                            $params = $firmyIds;
                                            $params[] = 1;
                                            $types = str_repeat('i', count($params));
                                            stmt_bind_params($stmt, $types, $params);
                                        }
                                    } else {
                                        $sql = "SELECT id, firma, aktivni FROM firmy WHERE id IN ($placeholders) ORDER BY id";
                                        $stmt = $conn->prepare($sql);

                                        if ($stmt) {
                                            $params = $firmyIds;
                                            $types = str_repeat('i', count($params));
                                            stmt_bind_params($stmt, $types, $params);
                                        }
                                    }
                                }
                            }

                            if ($stmt) {
                                if (!$stmt->execute()) {
                                    die("Nelze provést dotaz</body></html>");
                                }

                                $result = $stmt->get_result();

                                while ($radek = $result->fetch_assoc()) { ?>
                                    <tr>
                                        <td class="text-center fw-bold"><?= $cislo; ?></td>
                                        <td class="text-start">
                                            <b><?= h($radek["firma"]); ?></b>
                                        </td>
                                        <td>
                                            <?php if ((int)$radek['aktivni'] === 1) { ?>
                                                <h4><span class="badge bg-primary">Aktivní</span></h4>
                                            <?php } else { ?>
                                                <h4><span class="badge bg-warning">Neaktivní</span></h4>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?= zjisti_pocet_zamestnancu_ve_firme((int)$radek["id"]); ?>
                                            /
                                            <?= zjisti_pocet_zamestnancu_ve_firme_objednavka((int)$radek["id"]); ?>
                                        </td>
                                        <td>
                                            <?php
                                            if ($sessionTyp === '2') {
                                                vytvor_tlacitka_pro_smeny($radek["id"], $_SESSION["log_id"], $_SESSION["autobus"]);
                                            }
                                            ?>
                                        </td>

                                        <?php if (in_array($sessionTyp, ['1', '4', '5'], true)) { ?>
                                            <td class="text-start" width="50">
                                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#ModalFirmaInfo<?= (int)$radek['id']; ?>">
                                                    Editace
                                                </button>
                                            </td>
                                            <?php
                                            nova_firma((int)$radek['id']);
                                        } ?>
                                    </tr>
                                    <?php
                                    $cislo++;
                                }

                                $stmt->close();
                            }
                            ?>

                                <tr>
                                    <td class="text-center"></td>
                                    <td class="text-start">Nepřiřazení zaměstnanci</td>
                                    <td></td>
                                    <td><?= zjisti_pocet_zamestnancu_ve_firme(0); ?></td>
                                    <td></td>
                                    <?php if (in_array((string)($_SESSION["typ"] ?? ''), ['1', '4', '5'], true)) { ?>
                                        <td></td>
                                    <?php } ?>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        <?php
    }

    nova_firma();

} else {
    ?>
    <h1 class="text-center m-2 p-2">NEAUTORIZOVANÝ PŘÍSTUP</h1>
    <meta http-equiv="refresh" content="5;url=login.php">
    <?php
}
?>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.cs.min.js"></script>

<script>
    $(document).ready(function(){
        $('#datepicker').datepicker({
            format: 'dd.mm.yyyy',
            autoclose: true,
            todayHighlight: true,
            language: 'cs',
            allowInputToggle: false
        });
    });
</script>

<script>
    $(document).ready(function(){
        $('.common-datepicker').datepicker({
            format: 'dd.mm.yyyy',
            autoclose: true,
            todayHighlight: true,
            language: 'cs',
            allowInputToggle: false,
            startDate: '01.01.1930',
            endDate: '31.12.2099'
        });
    });
</script>

</body>
</html>