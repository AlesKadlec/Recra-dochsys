<?php
// -----------------------------------------------------
// Includes
// -----------------------------------------------------
include('db.php');
include('funkce.php');

global $conn;

// -----------------------------------------------------
// Helpery
// -----------------------------------------------------
function is_weekend(int $weekDayN): bool {
    return ($weekDayN === 6 || $weekDayN === 7); // So/Ne
}

function cz_day_short(int $weekDayN): string {
    return match ($weekDayN) {
        1 => "Po",
        2 => "Út",
        3 => "St",
        4 => "Čt",
        5 => "Pá",
        6 => "So",
        7 => "Ne",
        default => "",
    };
}

function safe_month_post(?string $ym): ?array {
    // očekává "YYYY-MM"
    if (!$ym) return null;
    if (!preg_match('~^(\d{4})-(\d{2})$~', $ym, $m)) return null;
    $rok = (int)$m[1];
    $mesic = (int)$m[2];
    if ($rok < 2000 || $rok > 2100) return null;
    if ($mesic < 1 || $mesic > 12) return null;
    return [$rok, $mesic];
}

// -----------------------------------------------------
// Start
// -----------------------------------------------------
menu();

// Auth
if (kontrola_prihlaseni() !== "OK" || !isset($_SESSION["typ"]) || $_SESSION["typ"] !== "5") {
    echo "<h1 class='text-center m-2 p-2'>NEAUTORIZOVANÝ PŘÍSTUP</h1>";
    $target = (kontrola_prihlaseni() === "OK") ? "main.php" : "login.php";
    echo "<meta http-equiv='refresh' content='5;url={$target}'>";
    exit;
}

// -----------------------------------------------------
// Month/year selection
// -----------------------------------------------------
$rok = (int)date('Y');
$mesic = (int)date('n');

if (!empty($_POST['mesic'])) {
    $parsed = safe_month_post($_POST['mesic']);
    if ($parsed) {
        [$rok, $mesic] = $parsed;
    }
}

$ym = sprintf("%04d-%02d", $rok, $mesic);

// -----------------------------------------------------
// Save fakturace (only if same month is still selected)
// -----------------------------------------------------
$fakturace = get_castka_fakturace($ym);

if (isset($_POST['hid_mesic'], $_POST['fakturace'], $_POST['hid_fakturace'])) {
    if ($_POST['hid_mesic'] === $ym) {
        $newFakturace = str_replace(',', '.', (string)$_POST['fakturace']);
        $newFakturace = is_numeric($newFakturace) ? (float)$newFakturace : 0.0;

        $oldFakturace = (float)$_POST['hid_fakturace'];

        if ((float)$fakturace === 0.0 && $newFakturace > 0) {
            insert_castka_fakturace($ym, $newFakturace);
            $fakturace = $newFakturace;
        } elseif (abs($oldFakturace - $newFakturace) > 0.00001) {
            update_castka_fakturace($ym, $newFakturace);
            $fakturace = $newFakturace;
        }
    }
}

// -----------------------------------------------------
// Days in month precompute
// -----------------------------------------------------
$kalendarskySystem = CAL_GREGORIAN;
$pocetDniVMesici = cal_days_in_month($kalendarskySystem, $mesic, $rok);

$start_day = sprintf("%04d-%02d-01", $rok, $mesic);
$end_day   = sprintf("%04d-%02d-%02d", $rok, $mesic, $pocetDniVMesici);

$days = [];
for ($d = 1; $d <= $pocetDniVMesici; $d++) {
    $date = sprintf("%04d-%02d-%02d", $rok, $mesic, $d);
    $weekDayN = (int)date('N', strtotime($date));
    $days[] = [
        'date' => $date,
        'd' => $d,
        'weekN' => $weekDayN,
        'cz' => cz_day_short($weekDayN),
        'weekend' => is_weekend($weekDayN),
    ];
}

// -----------------------------------------------------
// Month selector options
// -----------------------------------------------------
$monthOptions = [];
$sqlMonths = "SELECT LEFT(datum,7) AS datumek
              FROM dochazka
              GROUP BY LEFT(datum,7)
              ORDER BY datumek DESC";

if ($res = mysqli_query($conn, $sqlMonths)) {
    while ($row = mysqli_fetch_assoc($res)) {
        $monthOptions[] = $row['datumek'];
    }
    mysqli_free_result($res);
}

// -----------------------------------------------------
// 1) Core: counts for ALL (cilova_norm, den) in ONE SQL
// -----------------------------------------------------
$counts = [];     // $counts[cilova_norm][YYYY-MM-DD] = pocet
$cilove = [];     // seznam cílových stanic
$cilovaSet = [];  // set pro unikáty

$sqlCounts = "
    SELECT 
        UPPER(TRIM(z.cilova)) AS cilova_norm,
        DATE(d.datum) AS den,
        COUNT(DISTINCT d.zamestnanec) AS pocet
    FROM dochazka d
    INNER JOIN zamestnanci z ON d.zamestnanec = z.id
    WHERE d.datum BETWEEN '{$start_day}' AND '{$end_day}'
      AND TRIM(z.cilova) <> ''
      AND z.os_cislo REGEXP '^[0-9]+$'
    GROUP BY UPPER(TRIM(z.cilova)), DATE(d.datum)
    ORDER BY cilova_norm, den
";

if (!($res = mysqli_query($conn, $sqlCounts))) {
    die("Nelze provést dotaz (counts)</body></html>");
}

while ($row = mysqli_fetch_assoc($res)) {
    $c   = $row['cilova_norm'];
    $den = $row['den'];
    $p   = (int)$row['pocet'];

    $counts[$c][$den] = $p;

    if (!isset($cilovaSet[$c])) {
        $cilovaSet[$c] = true;
        $cilove[] = $c;
    }
}
mysqli_free_result($res);

// -----------------------------------------------------
// 2) Nepřítomnosti: ALL (datum, typ) in ONE SQL + typy dynamicky
// -----------------------------------------------------
$nepritCounts   = []; // [datum][typ] = pocet
$nepritTypes    = []; // seznam všech nalezených typů
$nepritTypeSet  = []; // set pro unikáty

$sqlNeprit = "
    SELECT datum, nepritomnost, COUNT(*) AS pocet
    FROM nepritomnost
    WHERE datum BETWEEN '{$start_day}' AND '{$end_day}'
    GROUP BY datum, nepritomnost
    ORDER BY nepritomnost
";

if (!($res = mysqli_query($conn, $sqlNeprit))) {
    die("Nelze provést dotaz (nepritomnost)</body></html>");
}

while ($row = mysqli_fetch_assoc($res)) {
    $datum = $row['datum'];
    $typ   = $row['nepritomnost'];
    $pocet = (int)$row['pocet'];

    $nepritCounts[$datum][$typ] = $pocet;

    if (!isset($nepritTypeSet[$typ])) {
        $nepritTypeSet[$typ] = true;
        $nepritTypes[] = $typ;
    }
}
mysqli_free_result($res);

// (volitelné) pokud chceš typy seřadit jinak než abecedně, můžeš tady přeuspořádat $nepritTypes

// -----------------------------------------------------
// Render
// -----------------------------------------------------
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
    <title>RECRA - agenturní zaměstnávání - měsíční předpoklad tržeb (v tisících Kč bez DPH)</title>

    <style>
        .horizontal-line-hore { border-top: 2px solid black; }
        .horizontal-line-dole { border-bottom: 2px solid black; }
    </style>
</head>
<body>

<h2 class='text-center m-2 p-2 d-print-none'>RECRA - agenturní zaměstnávání - měsíční předpoklad tržeb (v tisících Kč bez DPH)</h2>
<h4 class='text-center m-2 p-2 d-print-none'>Denní obsazenost zaměstnanců na odpracovaných 7,5 hodin v systému N-O-R (noční,odpolední,ranní)</h4>

<div class="container-fluid">

    <div class="d-grid gap-2 d-md-flex justify-content-md-center d-print-none">
        <form class="row g-3" action="report3.php?typ=filtr" method="post">
            <div class="col-auto">
                <label for="fakturace">Fakturace v Kč/hod</label>
                <input type="text" class="form-control mt-2" id="fakturace" name="fakturace"
                       value="<?php echo htmlspecialchars((string)$fakturace); ?>">
            </div>

            <div class="col-auto">
                <label for="mesic">Výběr měsíce</label>
                <select class="form-select mt-2" id="mesic" name="mesic">
                    <?php foreach ($monthOptions as $opt): ?>
                        <option value="<?php echo htmlspecialchars($opt); ?>" <?php echo ($opt === $ym ? "selected" : ""); ?>>
                            <?php echo htmlspecialchars($opt); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <input type="hidden" id="hid_mesic" name="hid_mesic" value="<?php echo htmlspecialchars($ym); ?>">
            <input type="hidden" id="hid_fakturace" name="hid_fakturace" value="<?php echo htmlspecialchars((string)$fakturace); ?>">

            <div class="col-auto">
                <label>&nbsp;</label>
                <button type="submit" class="form-control btn btn-primary mt-2">Proveď výběr</button>
            </div>
        </form>
    </div>

    <br>

    <div class="row justify-content-md-center">
    <div class="col col-md-12">
        <div class='table-responsive-lg text-center'>
            <table class='table table-hover'>
                <thead>
                <tr class='horizontal-line-hore'>
                    <th scope='col' class='text-center'>Den</th>
                    <?php foreach ($days as $di): ?>
                        <th scope='col' class='text-center'><?php echo (int)$di['d']; ?></th>
                    <?php endforeach; ?>
                    <th scope='col' class='text-center'></th>
                </tr>

                <tr class='horizontal-line-dole'>
                    <th scope='col' class='text-center'>Cílová stanice</th>
                    <?php foreach ($days as $di): ?>
                        <th scope='col' class='text-center'><?php echo htmlspecialchars($di['cz']); ?></th>
                    <?php endforeach; ?>
                    <th scope='col' class='text-center'>Suma</th>
                </tr>
                </thead>

                <tbody>
                <?php
                $suma = array_fill(0, $pocetDniVMesici, 0);
                $suma_nepritomnost = array_fill(0, $pocetDniVMesici, 0);

                // celkové sumy přes všechny dny (pro poslední sloupec souhrnů)
                $suma_mesic = 0;
                $suma_nepritomnost_mesic = 0;
                ?>

                <?php foreach ($cilove as $cilova): ?>
                    <?php $rowSum = 0; ?>
                    <tr>
                        <td class='table-warning text-start'><?php echo htmlspecialchars($cilova); ?></td>

                        <?php foreach ($days as $idx => $di):
                            $pocet = (int)($counts[$cilova][$di['date']] ?? 0);
                            $rowSum += $pocet;

                            if ($pocet > 0) $suma[$idx] += $pocet;

                            if ($di['weekend']) {
                                if ($pocet > 0) {
                                    echo "<td class='text-center table-warning'><b>{$pocet}</b></td>";
                                } else {
                                    echo "<td class='text-center table-light'></td>";
                                }
                            } else {
                                if ($pocet > 0) {
                                    echo "<td class='text-center table-success'><b>{$pocet}</b></td>";
                                } else {
                                    echo "<td class='text-center table-info'></td>";
                                }
                            }
                        endforeach; ?>

                        <td class="text-center table-primary fw-bold"><?php echo (int)$rowSum; ?></td>
                    </tr>
                <?php endforeach; ?>

                <?php
                // řádky nepřítomností dynamicky
                $first = true;
                foreach ($nepritTypes as $typ):
                    $trClass = $first ? "horizontal-line-hore" : "";
                    $first = false;

                    $rowSum = 0;
                    ?>
                    <tr class="<?php echo $trClass; ?>">
                        <td class='table-danger text-end'><?php echo htmlspecialchars($typ); ?></td>

                        <?php foreach ($days as $idx => $di):
                            if ($di['weekend']) {
                                echo "<td class='text-center table-light'></td>";
                                continue;
                            }

                            $pocet = (int)($nepritCounts[$di['date']][$typ] ?? 0);
                            $rowSum += $pocet;

                            if ($pocet > 0) {
                                $suma_nepritomnost[$idx] += $pocet;
                                echo "<td class='text-center table-success'><b>{$pocet}</b></td>";
                            } else {
                                echo "<td class='text-center table-info'></td>";
                            }
                        endforeach; ?>

                        <td class="text-center table-primary fw-bold"><?php echo (int)$rowSum; ?></td>
                    </tr>
                <?php endforeach; ?>

                <?php
                // dopočítáme měsíční sumy pro souhrnné řádky (poslední sloupec)
                $suma_mesic = array_sum($suma);
                $suma_nepritomnost_mesic = array_sum($suma_nepritomnost);

                // objednávka (pokud ji později napojíš na DB, tady jen nechávám tvoje původní)
                $celkem_objednavka = 0;
                ?>

                <tr class='table-success fw-bold horizontal-line-hore'>
                    <td>Denní počet zaměstnanců</td>
                    <?php foreach ($suma as $v): ?>
                        <td class='text-center text-danger'><?php echo (int)$v; ?></td>
                    <?php endforeach; ?>
                    <td class="text-center table-primary fw-bold"><?php echo (int)$suma_mesic; ?></td>
                </tr>

                <tr class='table-success fw-bold'>
                    <td>Denní počet nepřítomných</td>
                    <?php foreach ($suma_nepritomnost as $v): ?>
                        <td class='text-center text-danger'><?php echo (int)$v; ?></td>
                    <?php endforeach; ?>
                    <td class="text-center table-primary fw-bold"><?php echo (int)$suma_nepritomnost_mesic; ?></td>
                </tr>

                <tr class='table-success fw-bold'>
                    <td>Denní celkový počet hodin</td>
                    <?php foreach ($suma as $v): ?>
                        <td class='text-center'><?php echo (float)($v * 7.5); ?></td>
                    <?php endforeach; ?>
                    <td class="text-center table-primary fw-bold"><?php echo (float)($suma_mesic * 7.5); ?></td>
                </tr>

                <tr class='table-success'>
                    <td>Denní tržba bez DPH</td>
                    <?php foreach ($suma as $v): ?>
                        <td class='text-center fs-6 text-danger'><?php echo round(($v * 7.5 * (float)$fakturace) / 1000, 1); ?></td>
                    <?php endforeach; ?>
                    <td class="text-center table-primary fw-bold">
                        <?php echo round(($suma_mesic * 7.5 * (float)$fakturace) / 1000, 1); ?>
                    </td>
                </tr>

                <tr class='table-success fs-6'>
                    <td>Ztráta zaměstnanců za den</td>
                    <?php
                    $rowLossSum = 0;
                    foreach ($suma as $v):
                        $loss = (int)$celkem_objednavka - (int)$v;
                        $rowLossSum += $loss;
                        ?>
                        <td class='text-center fs-6 text-danger'><?php echo (int)$loss; ?></td>
                    <?php endforeach; ?>
                    <td class="text-center table-primary fw-bold"><?php echo (int)$rowLossSum; ?></td>
                </tr>

                <tr class='table-success fs-6 horizontal-line-dole'>
                    <td>Ztráta denní tržby bez DPH</td>
                    <?php
                    $rowLossSalesSum = 0.0;
                    foreach ($suma as $v):
                        $lossSales = round((((int)$celkem_objednavka - (int)$v) * (float)$fakturace) / 1000, 1);
                        $rowLossSalesSum += $lossSales;
                        ?>
                        <td class='text-center fs-6 text-danger'><?php echo $lossSales; ?></td>
                    <?php endforeach; ?>
                    <td class="text-center table-primary fw-bold"><?php echo round($rowLossSalesSum, 1); ?></td>
                </tr>

                </tbody>
            </table>
        </div>
    </div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>

</body>
</html>