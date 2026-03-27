<?php
// -----------------------------------------------------
// Includes
// -----------------------------------------------------
include('db.php');
include('funkce.php');

global $conn;

// -----------------------------------------------------
// Helpery - obecné
// -----------------------------------------------------
function h(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function is_weekend(int $weekDayN): bool
{
    return ($weekDayN === 6 || $weekDayN === 7);
}

function cz_day_short(int $weekDayN): string
{
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

function safe_month_value(?string $ym): ?array
{
    if (!$ym) {
        return null;
    }

    if (!preg_match('~^(\d{4})-(\d{2})$~', $ym, $m)) {
        return null;
    }

    $rok = (int)$m[1];
    $mesic = (int)$m[2];

    if ($rok < 2000 || $rok > 2100) {
        return null;
    }

    if ($mesic < 1 || $mesic > 12) {
        return null;
    }

    return [$rok, $mesic];
}

function build_days(int $rok, int $mesic): array
{
    $pocetDniVMesici = cal_days_in_month(CAL_GREGORIAN, $mesic, $rok);

    $days = [];
    $dayIndex = [];

    for ($d = 1; $d <= $pocetDniVMesici; $d++) {
        $date = sprintf("%04d-%02d-%02d", $rok, $mesic, $d);
        $weekDayN = (int)date('N', strtotime($date));

        $days[] = [
            'date'    => $date,
            'd'       => $d,
            'weekN'   => $weekDayN,
            'cz'      => cz_day_short($weekDayN),
            'weekend' => is_weekend($weekDayN),
        ];

        $dayIndex[$date] = count($days) - 1;
    }

    return [$days, $dayIndex, $pocetDniVMesici];
}

function load_month_options(mysqli $conn): array
{
    $out = [];

    $sql = "
        SELECT LEFT(datum, 7) AS datumek
        FROM dochazka
        GROUP BY LEFT(datum, 7)
        ORDER BY datumek DESC
    ";

    if ($res = mysqli_query($conn, $sql)) {
        while ($row = mysqli_fetch_assoc($res)) {
            $out[] = $row['datumek'];
        }
        mysqli_free_result($res);
    }

    return $out;
}

function load_station_counts(mysqli $conn, string $startDay, string $endDay): array
{
    $counts = [];
    $cilove = [];
    $cilovaSet = [];

    $sql = "
        SELECT 
            UPPER(TRIM(z.cilova)) AS cilova_norm,
            DATE(d.datum) AS den,
            COUNT(DISTINCT d.zamestnanec) AS pocet
        FROM dochazka d
        INNER JOIN zamestnanci z ON d.zamestnanec = z.id
        WHERE d.datum BETWEEN ? AND ?
          AND TRIM(z.cilova) <> ''
          AND z.os_cislo REGEXP '^[0-9]+$'
          AND COALESCE(UPPER(TRIM(d.smena)), '') <> 'VOL'
        GROUP BY UPPER(TRIM(z.cilova)), DATE(d.datum)
        ORDER BY cilova_norm, den
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Nelze připravit dotaz (counts)</body></html>");
    }

    $stmt->bind_param("ss", $startDay, $endDay);

    if (!$stmt->execute()) {
        $stmt->close();
        die("Nelze provést dotaz (counts)</body></html>");
    }

    $res = $stmt->get_result();

    while ($row = mysqli_fetch_assoc($res)) {
        $c = $row['cilova_norm'];
        $den = $row['den'];
        $p = (int)$row['pocet'];

        $counts[$c][$den] = $p;

        if (!isset($cilovaSet[$c])) {
            $cilovaSet[$c] = true;
            $cilove[] = $c;
        }
    }

    $res->free();
    $stmt->close();

    return [$counts, $cilove];
}

function load_day_totals(mysqli $conn, string $startDay, string $endDay, array $dayIndex, int $pocetDniVMesici): array
{
    $denVlastni = array_fill(0, $pocetDniVMesici, 0);
    $denDopravce = array_fill(0, $pocetDniVMesici, 0);

    $sql = "
        SELECT
            DATE(d.datum) AS den,
            COUNT(DISTINCT IF(d.bus = 16, d.zamestnanec, NULL)) AS vlastni,
            COUNT(DISTINCT IF(d.bus <> 16 OR d.bus IS NULL, d.zamestnanec, NULL)) AS dopravce
        FROM dochazka d
        INNER JOIN zamestnanci z ON d.zamestnanec = z.id
        WHERE d.datum BETWEEN ? AND ?
          AND TRIM(z.cilova) <> ''
          AND z.os_cislo REGEXP '^[0-9]+$'
          AND COALESCE(UPPER(TRIM(d.smena)), '') <> 'VOL'
        GROUP BY DATE(d.datum)
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Nelze připravit dotaz (day totals)</body></html>");
    }

    $stmt->bind_param("ss", $startDay, $endDay);

    if (!$stmt->execute()) {
        $stmt->close();
        die("Nelze provést dotaz (day totals)</body></html>");
    }

    $res = $stmt->get_result();

    while ($row = mysqli_fetch_assoc($res)) {
        $den = $row['den'];
        if (!isset($dayIndex[$den])) {
            continue;
        }

        $i = $dayIndex[$den];
        $denVlastni[$i] = (int)$row['vlastni'];
        $denDopravce[$i] = (int)$row['dopravce'];
    }

    $res->free();
    $stmt->close();

    return [$denVlastni, $denDopravce];
}

function load_absence_counts(mysqli $conn, string $startDay, string $endDay): array
{
    $nepritCounts = [];
    $nepritTypes = [];
    $nepritTypeSet = [];

    $sql = "
        SELECT datum, nepritomnost, COUNT(*) AS pocet
        FROM nepritomnost
        WHERE datum BETWEEN ? AND ?
        GROUP BY datum, nepritomnost
        ORDER BY nepritomnost
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Nelze připravit dotaz (nepritomnost)</body></html>");
    }

    $stmt->bind_param("ss", $startDay, $endDay);

    if (!$stmt->execute()) {
        $stmt->close();
        die("Nelze provést dotaz (nepritomnost)</body></html>");
    }

    $res = $stmt->get_result();

    while ($row = mysqli_fetch_assoc($res)) {
        $datum = $row['datum'];
        $typ = $row['nepritomnost'];
        $pocet = (int)$row['pocet'];

        $nepritCounts[$datum][$typ] = $pocet;

        if (!isset($nepritTypeSet[$typ])) {
            $nepritTypeSet[$typ] = true;
            $nepritTypes[] = $typ;
        }
    }

    $res->free();
    $stmt->close();

    return [$nepritCounts, $nepritTypes];
}

// -----------------------------------------------------
// Start
// -----------------------------------------------------
menu();

// -----------------------------------------------------
// Auth
// -----------------------------------------------------
$auth = kontrola_prihlaseni();

if ($auth !== "OK" || !isset($_SESSION["typ"]) || $_SESSION["typ"] !== "5") {
    echo "<h1 class='text-center m-2 p-2'>NEAUTORIZOVANÝ PŘÍSTUP</h1>";
    $target = ($auth === "OK") ? "main.php" : "login.php";
    echo "<meta http-equiv='refresh' content='5;url={$target}'>";
    exit;
}

// -----------------------------------------------------
// Month/year selection
// -----------------------------------------------------
$rok = (int)date('Y');
$mesic = (int)date('n');

$mesicRequest = $_POST['mesic'] ?? $_GET['mesic'] ?? null;
$parsed = safe_month_value($mesicRequest);

if ($parsed) {
    [$rok, $mesic] = $parsed;
}

$ym = sprintf("%04d-%02d", $rok, $mesic);

// -----------------------------------------------------
// Save fakturace
// -----------------------------------------------------
$fakturace = (float)get_castka_fakturace($ym);

if (isset($_POST['hid_mesic'], $_POST['fakturace'], $_POST['hid_fakturace'])) {
    if ((string)$_POST['hid_mesic'] === $ym) {
        $newFakturaceRaw = str_replace(',', '.', trim((string)$_POST['fakturace']));
        $newFakturace = is_numeric($newFakturaceRaw) ? (float)$newFakturaceRaw : 0.0;
        $oldFakturace = (float)$_POST['hid_fakturace'];

        if ($fakturace === 0.0 && $newFakturace > 0) {
            insert_castka_fakturace($ym, $newFakturace);
            $fakturace = $newFakturace;
        } elseif (abs($oldFakturace - $newFakturace) > 0.00001) {
            update_castka_fakturace($ym, $newFakturace);
            $fakturace = $newFakturace;
        }
    }
}

// -----------------------------------------------------
// Days in month
// -----------------------------------------------------
[$days, $dayIndex, $pocetDniVMesici] = build_days($rok, $mesic);

$start_day = sprintf("%04d-%02d-01", $rok, $mesic);
$end_day = sprintf("%04d-%02d-%02d", $rok, $mesic, $pocetDniVMesici);

// -----------------------------------------------------
// Month selector options
// -----------------------------------------------------
$monthOptions = load_month_options($conn);

// -----------------------------------------------------
// Data loads
// -----------------------------------------------------
[$counts, $cilove] = load_station_counts($conn, $start_day, $end_day);
[$den_vlastni, $den_dopravce] = load_day_totals($conn, $start_day, $end_day, $dayIndex, $pocetDniVMesici);
[$nepritCounts, $nepritTypes] = load_absence_counts($conn, $start_day, $end_day);

// -----------------------------------------------------
// Výpočty souhrnů
// -----------------------------------------------------
$denniObsazenostStanice = array_fill(0, $pocetDniVMesici, 0);
$denniNepritomnost = array_fill(0, $pocetDniVMesici, 0);

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
        body {
            background: #f8f9fa;
        }

        .report-title {
            font-weight: 700;
            letter-spacing: 0.02em;
            margin: 0 0 0.5rem 0;
        }

        .report-toolbar {
            background: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 1rem;
            padding: 0.75rem 1rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.04);
            margin-bottom: 0.75rem;
        }

        .report-table-wrap {
            overflow-x: auto;
            overflow-y: visible;
            border: 1px solid #dee2e6;
            border-radius: 1rem;
            background: #fff;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.04);
        }

        .report-table {
            font-size: 0.85rem;
            white-space: nowrap;
            margin-bottom: 0;
        }

        .report-table th,
        .report-table td {
            vertical-align: middle;
            text-align: center;
            padding: 0.4rem 0.45rem;
        }

        .report-table thead th {
            position: sticky;
            z-index: 5;
            background: #dbeafe;
            color: #0f172a;
            border-bottom: 1px solid #93c5fd;
        }

        .report-table thead tr:first-child th {
            top: 0;
        }

        .report-table thead tr:nth-child(2) th {
            top: 38px;
        }

        .report-table .sticky-col {
            position: sticky;
            left: 0;
            z-index: 4;
            background: #fff;
            text-align: left;
            width: 230px;
            min-width: 230px;
            max-width: 230px;
            white-space: normal;
            overflow-wrap: anywhere;
            word-break: normal;
            line-height: 1.15;
            box-shadow: 2px 0 0 #dee2e6;
        }

        .report-table thead .sticky-col {
            z-index: 6;
            background: #bfdbfe;
            color: #0f172a;
        }

        .report-table td:not(.sticky-col),
        .report-table th:not(.sticky-col) {
            min-width: 46px;
        }

        .report-table .sum-col {
            background: #e7f1ff !important;
            font-weight: 700;
        }

        .report-table .row-label-warning {
            background: #fff3cd !important;
            font-weight: 600;
        }

        .report-table .row-label-danger {
            background: #f8d7da !important;
            font-weight: 600;
        }

        .report-table .row-total {
            background: #d1e7dd !important;
            font-weight: 700;
        }

        .report-table .row-grand-total td,
        .report-table .row-grand-total th {
            background: #198754 !important;
            color: #fff !important;
            font-weight: 800;
            font-size: 0.92rem;
            border-top: 2px solid #146c43 !important;
            border-bottom: 2px solid #146c43 !important;
        }

        .report-table .row-grand-total .sticky-col,
        .report-table .row-grand-total .sum-col {
            background: #198754 !important;
            color: #fff !important;
        }

        .horizontal-line-hore td,
        .horizontal-line-hore th {
            border-top: 2px solid #212529 !important;
        }

        .horizontal-line-dole td,
        .horizontal-line-dole th {
            border-bottom: 2px solid #212529 !important;
        }

        .metric-value {
            font-weight: 700;
        }

        .report-footer-note {
            color: #6c757d;
            font-size: 0.92rem;
            text-align: center;
            margin-top: 0.75rem;
            margin-bottom: 0.25rem;
        }

        @media print {
            body {
                background: #fff;
            }

            .report-toolbar,
            .report-footer-note {
                display: none !important;
            }

            .report-table-wrap {
                overflow: visible;
                border: 0;
                box-shadow: none;
            }

            .report-table thead tr:first-child th,
            .report-table thead tr:nth-child(2) th {
                top: auto !important;
            }

            .report-table .sticky-col {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>

<div class="container-fluid py-2">

    <div class="report-toolbar d-print-none">
        <h2 class="report-title text-center">RECRA - agenturní zaměstnávání</h2>

        <form class="row g-2 align-items-end" action="report3.php?typ=filtr" method="post">
            <div class="col-lg-3 col-md-4">
                <label for="fakturace" class="form-label fw-semibold mb-1">
                    <i class="bi bi-cash-coin me-1"></i>Fakturace v Kč/hod
                </label>
                <input type="text" class="form-control" id="fakturace" name="fakturace" value="<?php echo h((string)$fakturace); ?>">
            </div>

            <div class="col-lg-3 col-md-4">
                <label for="mesic" class="form-label fw-semibold mb-1">
                    <i class="bi bi-calendar3 me-1"></i>Výběr měsíce
                </label>
                <select class="form-select" id="mesic" name="mesic">
                    <?php foreach ($monthOptions as $opt): ?>
                        <option value="<?php echo h($opt); ?>" <?php echo ($opt === $ym ? "selected" : ""); ?>>
                            <?php echo h($opt); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <input type="hidden" id="hid_mesic" name="hid_mesic" value="<?php echo h($ym); ?>">
            <input type="hidden" id="hid_fakturace" name="hid_fakturace" value="<?php echo h((string)$fakturace); ?>">

            <div class="col-lg-3 col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i>Proveď výběr
                </button>
            </div>

            <div class="col-lg-3 col-md-12 text-lg-end text-md-start">
                <div class="small text-muted">
                    Aktuální měsíc: <span class="metric-value"><?php echo h($ym); ?></span>
                </div>
            </div>
        </form>
    </div>

    <div class="report-table-wrap">
        <table class="table table-sm table-hover report-table align-middle">
            <thead>
                <tr class="horizontal-line-hore">
                    <th scope="col" class="sticky-col text-center">Den</th>
                    <?php foreach ($days as $di): ?>
                        <th scope="col"><?php echo (int)$di['d']; ?></th>
                    <?php endforeach; ?>
                    <th scope="col" class="sum-col">Suma</th>
                </tr>

                <tr class="horizontal-line-dole">
                    <th scope="col" class="sticky-col text-center">Cílová stanice</th>
                    <?php foreach ($days as $di): ?>
                        <th scope="col"><?php echo h($di['cz']); ?></th>
                    <?php endforeach; ?>
                    <th scope="col" class="sum-col">Suma</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($cilove as $cilova): ?>
                    <?php $rowSum = 0; ?>
                    <tr>
                        <td class="sticky-col row-label-warning"><?php echo h($cilova); ?></td>

                        <?php foreach ($days as $idx => $di): ?>
                            <?php
                            $pocet = (int)($counts[$cilova][$di['date']] ?? 0);
                            $rowSum += $pocet;

                            if ($pocet > 0) {
                                $denniObsazenostStanice[$idx] += $pocet;
                            }

                            if ($di['weekend']) {
                                $tdClass = $pocet > 0 ? 'table-warning' : 'table-light';
                            } else {
                                $tdClass = $pocet > 0 ? 'table-success' : 'table-info';
                            }
                            ?>
                            <td class="<?php echo $tdClass; ?>">
                                <?php echo $pocet > 0 ? "<b>{$pocet}</b>" : ""; ?>
                            </td>
                        <?php endforeach; ?>

                        <td class="sum-col"><?php echo (int)$rowSum; ?></td>
                    </tr>
                <?php endforeach; ?>

                <?php
                $firstAbsenceRow = true;
                foreach ($nepritTypes as $typ):
                    $rowSum = 0;
                    $trClass = $firstAbsenceRow ? "horizontal-line-hore" : "";
                    $firstAbsenceRow = false;
                ?>
                    <tr class="<?php echo $trClass; ?>">
                        <td class="sticky-col row-label-danger text-end"><?php echo h($typ); ?></td>

                        <?php foreach ($days as $idx => $di): ?>
                            <?php
                            if ($di['weekend']) {
                                echo "<td class='table-light'></td>";
                                continue;
                            }

                            $pocet = (int)($nepritCounts[$di['date']][$typ] ?? 0);
                            $rowSum += $pocet;

                            if ($pocet > 0) {
                                $denniNepritomnost[$idx] += $pocet;
                                echo "<td class='table-success'><b>{$pocet}</b></td>";
                            } else {
                                echo "<td class='table-info'></td>";
                            }
                            ?>
                        <?php endforeach; ?>

                        <td class="sum-col"><?php echo (int)$rowSum; ?></td>
                    </tr>
                <?php endforeach; ?>

                <?php
                $mesicObsazenostStanice = array_sum($denniObsazenostStanice);
                $mesicNepritomnost = array_sum($denniNepritomnost);

                $mesicVlastni = array_sum($den_vlastni);
                $mesicDopravce = array_sum($den_dopravce);
                $mesicObsazenostCelkem = $mesicVlastni + $mesicDopravce;

                $denniObsazenostCelkem = [];
                for ($i = 0; $i < $pocetDniVMesici; $i++) {
                    $denniObsazenostCelkem[$i] = (int)$den_vlastni[$i] + (int)$den_dopravce[$i];
                }

                $denniHodiny = [];
                foreach ($denniObsazenostStanice as $v) {
                    $denniHodiny[] = $v * 7.5;
                }

                $mesicHodiny = $mesicObsazenostCelkem * 7.5;

                $celkem_objednavka = 0;
                ?>

                <tr class="table-warning fw-bold horizontal-line-hore">
                    <td class="sticky-col row-label-warning">Denní počet zaměstnanců – vlastní doprava</td>
                    <?php foreach ($den_vlastni as $idx => $v): ?>
                        <td class="<?php echo $days[$idx]['weekend'] ? 'table-light' : 'table-warning'; ?>">
                            <span class="text-danger"><?php echo (int)$v; ?></span>
                        </td>
                    <?php endforeach; ?>
                    <td class="sum-col"><?php echo (int)$mesicVlastni; ?></td>
                </tr>

                <tr class="table-warning fw-bold">
                    <td class="sticky-col row-label-warning">Denní počet zaměstnanců – dopravce</td>
                    <?php foreach ($den_dopravce as $idx => $v): ?>
                        <td class="<?php echo $days[$idx]['weekend'] ? 'table-light' : 'table-warning'; ?>">
                            <span class="text-danger"><?php echo (int)$v; ?></span>
                        </td>
                    <?php endforeach; ?>
                    <td class="sum-col"><?php echo (int)$mesicDopravce; ?></td>
                </tr>

                <tr class="row-grand-total">
                    <td class="sticky-col">Denní počet zaměstnanců – celkem</td>
                    <?php foreach ($denniObsazenostCelkem as $idx => $v): ?>
                        <td><?php echo (int)$v; ?></td>
                    <?php endforeach; ?>
                    <td class="sum-col"><?php echo (int)$mesicObsazenostCelkem; ?></td>
                </tr>

                <tr class="table-success fw-bold horizontal-line-dole">
                    <td class="sticky-col row-total">Denní počet nepřítomných</td>
                    <?php foreach ($denniNepritomnost as $idx => $v): ?>
                        <td class="<?php echo $days[$idx]['weekend'] ? 'table-light' : 'table-success'; ?>">
                            <span class="text-danger"><?php echo (int)$v; ?></span>
                        </td>
                    <?php endforeach; ?>
                    <td class="sum-col"><?php echo (int)$mesicNepritomnost; ?></td>
                </tr>

                <tr class="table-success fw-bold horizontal-line-dole">
                    <td class="sticky-col row-total">Denní celkový počet hodin</td>
                    <?php foreach ($denniHodiny as $idx => $v): ?>
                        <td class="<?php echo $days[$idx]['weekend'] ? 'table-light' : 'table-success'; ?>">
                            <?php echo (float)$v; ?>
                        </td>
                    <?php endforeach; ?>
                    <td class="sum-col"><?php echo (float)$mesicHodiny; ?></td>
                </tr>

                <tr class="table-success">
                    <td class="sticky-col row-total">Denní tržba bez DPH</td>
                    <?php foreach ($denniObsazenostStanice as $idx => $v): ?>
                        <td class="<?php echo $days[$idx]['weekend'] ? 'table-light' : 'table-success'; ?>">
                            <span class="fs-6 text-danger">
                                <?php echo round(($v * 7.5 * (float)$fakturace) / 1000, 1); ?>
                            </span>
                        </td>
                    <?php endforeach; ?>
                    <td class="sum-col">
                        <?php echo round(($mesicObsazenostCelkem * 7.5 * (float)$fakturace) / 1000, 1); ?>
                    </td>
                </tr>

                <tr class="table-success fs-6">
                    <td class="sticky-col row-total">Ztráta zaměstnanců za den</td>
                    <?php
                    $rowLossSum = 0;
                    foreach ($denniObsazenostStanice as $idx => $v):
                        $loss = (int)$celkem_objednavka - (int)$v;
                        $rowLossSum += $loss;
                    ?>
                        <td class="<?php echo $days[$idx]['weekend'] ? 'table-light' : 'table-success'; ?>">
                            <span class="text-danger"><?php echo (int)$loss; ?></span>
                        </td>
                    <?php endforeach; ?>
                    <td class="sum-col"><?php echo (int)$rowLossSum; ?></td>
                </tr>

                <tr class="table-success fs-6 horizontal-line-dole">
                    <td class="sticky-col row-total">Ztráta denní tržby bez DPH</td>
                    <?php
                    $rowLossSalesSum = 0.0;
                    foreach ($denniObsazenostStanice as $idx => $v):
                        $lossSales = round((((int)$celkem_objednavka - (int)$v) * (float)$fakturace) / 1000, 1);
                        $rowLossSalesSum += $lossSales;
                    ?>
                        <td class="<?php echo $days[$idx]['weekend'] ? 'table-light' : 'table-success'; ?>">
                            <span class="text-danger"><?php echo $lossSales; ?></span>
                        </td>
                    <?php endforeach; ?>
                    <td class="sum-col"><?php echo round($rowLossSalesSum, 1); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="report-footer-note d-print-none">
        <div>Měsíční předpoklad tržeb (v tisících Kč bez DPH)</div>
        <div>Denní obsazenost zaměstnanců na odpracovaných 7,5 hodin v systému N-O-R (noční, odpolední, ranní)</div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>

</body>
</html>