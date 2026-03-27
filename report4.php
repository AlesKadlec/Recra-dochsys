<?php
// Zahrnutí souboru s údaji o připojení
include('db.php');
include('funkce.php');

global $conn;

/**
 * =====================================================
 * ✅ AJAX: ukončení pracovního poměru (uložení vystup)
 * - vystup je DATE, prázdné je 0000-00-00
 * - ošetřeno prepared statement (SQL injection)
 * =====================================================
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['action'] ?? '') === 'set_vystup')) {
    header('Content-Type: application/json; charset=utf-8');

    // základní autorizace (stejná role logika jako zbytek stránky)
    if (kontrola_prihlaseni() !== "OK" || !isset($_SESSION["typ"]) || !in_array($_SESSION["typ"], ["1","4","5"], true)) {
        echo json_encode(['ok' => false, 'msg' => 'Neautorizováno.']);
        exit;
    }

    $zam_id = isset($_POST['zam_id']) ? (int)$_POST['zam_id'] : 0;
    $vystup = trim((string)($_POST['vystup'] ?? ''));

    if ($zam_id <= 0) {
        echo json_encode(['ok' => false, 'msg' => 'Neplatné ID zaměstnance.']);
        exit;
    }

    // pokud uživatel zruší ukončení → uložíme 0000-00-00
    if ($vystup === '') {
        $vystup = '0000-00-00';
    } else {
        // validace formátu
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $vystup)) {
            echo json_encode(['ok' => false, 'msg' => 'Neplatné datum (očekávám YYYY-MM-DD).']);
            exit;
        }
        // validace reálného data
        [$yy,$mm,$dd] = array_map('intval', explode('-', $vystup));
        if (!checkdate($mm, $dd, $yy)) {
            echo json_encode(['ok' => false, 'msg' => 'Neplatné datum.']);
            exit;
        }
    }

    // UPDATE přes prepared statement
    $stmt = $conn->prepare("UPDATE zamestnanci SET vystup = ? WHERE id = ? LIMIT 1");
    if (!$stmt) {
        echo json_encode(['ok' => false, 'msg' => 'Chyba prepare.']);
        exit;
    }
    $stmt->bind_param("si", $vystup, $zam_id);

    if (!$stmt->execute()) {
        $stmt->close();
        echo json_encode(['ok' => false, 'msg' => 'Nelze uložit datum výstupu.']);
        exit;
    }
    $stmt->close();

    // log (volitelné, ale doporučené)
    $now = new DateTime('now', new DateTimeZone('Europe/Prague'));
    $typ = "Ukončení poměru";
    $info_text = "Ukončen poměr: " . get_name_from_id_zam($zam_id) . " (vystup: $vystup)";
    $datumcas = $now->format('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO logs(`kdo`,`typ`,`infotext`,`datumcas`) VALUES (?,?,?,?)");
    if ($stmt) {
        $stmt->bind_param("isss", $_SESSION["log_id"], $typ, $info_text, $datumcas);
        $stmt->execute();
        $stmt->close();
    }

    echo json_encode(['ok' => true, 'saved' => $vystup]);
    exit;
}
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
    <title>Docházka pracovníků ve třísměnném provoze</title>
</head>
<body>

<style>
    .horizontal-line-hore {
      border-top: 2px solid black;
    }
    .horizontal-line-dole {
      border-bottom: 2px solid black;
    }

    .border-vstup {
        border: 3px solid green !important;
    }
    .border-vystup {
        border: 3px solid red !important;
    }

    .poznamka-symbol {
        position: absolute;
        top: 2px;
        left: 2px;
        font-size: 0.7em;
        cursor: pointer;
        color: rgba(0, 0, 0, 0.5);
        background-color: transparent;
        padding: 0 2px;
        border-radius: 2px;
    }
</style>

<?php

global $conn;

menu();

if (kontrola_prihlaseni() == "OK")
{
    if (isset($_SESSION["typ"]))
    {
        if (($_SESSION["typ"] == "5") or ($_SESSION["typ"] == "1") or ($_SESSION["typ"] == "4"))
        {

            if(isset($_POST['mesic']))
            {
                $str_arr = explode ("-", $_POST["mesic"]);
                $rok = $str_arr[0];
                $mesic = $str_arr[1];
                $_SESSION["vybranymesic"] = $_POST['mesic'];
            }
            elseif (isset($_SESSION["vybranymesic"]))
            {
                $str_arr = explode ("-", $_SESSION["vybranymesic"]);
                $rok = $str_arr[0];
                $mesic = $str_arr[1];
            }
            else
            {
                $mesic = date('n');
                $rok = date('Y');
            }

            if(isset($_POST['cilova']))
            {
                $cilova = $_POST['cilova'];
                $_SESSION["vybranafirma"] = $_POST['cilova'];
            }
            elseif (isset($_SESSION["vybranafirma"]))
            {
                $cilova = $_SESSION["vybranafirma"];
            }
            else
            {
                $cilova = "BATZ";
            }

            if(isset($_GET['typ']) && $_GET['typ'] == 'savechange')
            {
                // 1) Ukládání denních směn
                for($i = 1; $i <= $_POST['max_den']; $i++) {
                    if(isset($_POST['toggle' . $i])) {
                        if (
                            $_POST['toggle' . $i] != $_POST['lasttoggle' . $i] ||
                            ($_POST['poznamka' . $i] ?? '') != ($_POST['lastpoznamka' . $i] ?? '')
                        ) {
                            $smena_den = $_POST['toggle' . $i];
                            $datum = $_POST['vybrany_rok'] . "-" . $_POST['vybrany_mesic'] . "-" . $i;
                            $poznamka_dne = $_POST['poznamka' . $i] ?? '';

                            // Smazání starých záznamů
                            $stmt = $conn->prepare("DELETE FROM nepritomnost WHERE zamestnanec=? AND datum=?");
                            $stmt->bind_param("is", $_POST['id_zam'], $datum);
                            $stmt->execute();
                            $stmt->close();

                            $stmt = $conn->prepare("DELETE FROM dochazka WHERE zamestnanec=? AND datum=?");
                            $stmt->bind_param("is", $_POST['id_zam'], $datum);
                            $stmt->execute();
                            $stmt->close();

                            $nepritomnosti_toggle = ['DPN','OČR','DOV','ABS','NAR','LEK','PRO','NEPV','NAHV','SVA'];

                            // Pokud je vybraná nepřítomnost
                            if(in_array($smena_den, $nepritomnosti_toggle))
                            {
                                $stmt = $conn->prepare("INSERT INTO nepritomnost(zamestnanec,datum,nepritomnost,zadal,poznamka) VALUES (?,?,?,?,?)");
                                $stmt->bind_param("issss", $_POST['id_zam'], $datum, $smena_den, $_SESSION["log_id"],$poznamka_dne);
                                $stmt->execute();
                                $stmt->close();
                            }

                            // Pokud je vybraná běžná směna (R,O,N,VOL) nebo N/A
                            if($smena_den != '' && !in_array($smena_den, $nepritomnosti_toggle))
                            {
                                $firma = get_info_from_zamestnanci_table($_POST['id_zam'], 'firma');
                                $zastavka = get_info_from_zamestnanci_table($_POST['id_zam'], 'nastup');
                                $cas_nastupu = get_time_nastupu($zastavka, $smena_den);
                                
                                // ISO týden + ISO rok pro dané datum
                                $dt = DateTime::createFromFormat('Y-m-d', $datum, new DateTimeZone('Europe/Prague'));
                                $weekNumber  = (int)$dt->format('W'); // ISO week
                                $realIsoYear = (int)$dt->format('o'); // ISO year (pozor: může se lišit od kalendářního roku)

                                // Načtení výchozí směny a trasy z plánů (trasa použijeme jako $auto)
                                $vychozi_smena = null;
                                $vychozi_trasa = null;

                                $stmt = $conn->prepare("SELECT smena, trasa FROM plan_smen WHERE rok=? AND tyden=? AND jmeno=?");
                                $stmt->bind_param("iis", $realIsoYear, $weekNumber, $_POST['id_zam']); // jmeno = id_zam dle tvého kódu
                                $stmt->execute();
                                $stmt->bind_result($vychozi_smena, $vychozi_trasa);
                                $stmt->fetch();
                                $stmt->close();

                                // tady mapování: trasa => auto
                                $auto = $vychozi_trasa ?? '';   // když nic nenajde, bude prázdné
                                // případně fallback, pokud chceš:
                                // if ($auto === '') { $auto = get_bus_from_zastavky($zastavka); }

                                insert_attandance_manually($_POST['id_zam'], $auto, $zastavka, $firma, $smena_den, $datum, $cas_nastupu, '', $poznamka_dne,'3');
                            }

                            // Log
                            $now = new DateTime('now', new DateTimeZone('Europe/Prague'));
                            $typ = "Editace docházky";
                            $info_text = "Záznam pro " . get_name_from_id_zam($_POST["id_zam"]) . " - $datum " . ($smena_den ?: 'N/A') . ", upravena";
                            $datumcas = $now->format('Y-m-d H:i:s');
                            $stmt = $conn->prepare("INSERT INTO logs(`kdo`,`typ`,`infotext`,`datumcas`) VALUES (?,?,?,?)");
                            $stmt->bind_param("isss", $_SESSION["log_id"], $typ, $info_text, $datumcas);
                            $stmt->execute();
                            $stmt->close();
                        }
                    }
                }

                $currentYear = (int)$_POST['vybrany_rok'];
                $prevWeek = null;

                foreach ($_POST['tydenni_smena'] as $week => $smena_tydne) {
                    $trasa_tydne = $_POST['tydenni_trasa'][$week] ?? '';
                    $last_smena = $_POST['last_tydni_smena'][$week] ?? '';
                    $last_trasa = $_POST['last_tydni_trasa'][$week] ?? '';

                    if ($prevWeek !== null && $week < $prevWeek) {
                        $currentYear++;
                    }

                    if ($smena_tydne != $last_smena || $trasa_tydne != $last_trasa) {
                        if ($smena_tydne === '' && $trasa_tydne === '') {
                            $stmt = $conn->prepare("DELETE FROM plan_smen WHERE rok=? AND tyden=? AND jmeno=?");
                            $stmt->bind_param("iis", $currentYear, $week, $_POST['id_zam']);
                            $stmt->execute();
                            $stmt->close();
                        } else {
                            $stmt = $conn->prepare(
                                "INSERT INTO plan_smen (rok, tyden, smena, trasa, jmeno)
                                VALUES (?, ?, ?, ?, ?)
                                ON DUPLICATE KEY UPDATE smena=VALUES(smena), trasa=VALUES(trasa)"
                            );
                            $stmt->bind_param("iisss", $currentYear, $week, $smena_tydne, $trasa_tydne, $_POST['id_zam']);
                            $stmt->execute();
                            $stmt->close();
                        }
                    }

                    $prevWeek = $week;
                }

                // ulozime zvoleny mesic, aby po ulozeni zustal zachovany
                if (!empty($_POST['vybrany_rok']) && !empty($_POST['vybrany_mesic'])) {
                    $_SESSION["vybranymesic"] = sprintf('%04d-%02d', (int)$_POST['vybrany_rok'], (int)$_POST['vybrany_mesic']);
                }

                //echo '<meta http-equiv="refresh" content="0;url=report4.php?typ=filtr">';
                $scrollId = (int)($_POST['id_zam'] ?? 0);
                $mesicRedirect = $_SESSION["vybranymesic"] ?? '';
                $redirectUrl = 'report4.php?typ=filtr&scroll_id=' . $scrollId;
                if ($mesicRedirect !== '') {
                    $redirectUrl .= '&mesic=' . urlencode($mesicRedirect);
                }
                echo '<meta http-equiv="refresh" content="0;url=' . $redirectUrl . '">';
            }
            ?>

            <h2 class='text-center m-2 p-2 d-print-none'>Docházka pracovníků ve třísměnném provoze</h2>

            <?php
            // --- výchozí měsíc = aktuální ---
            $vybranyMesic = date('Y-m');

            // --- pokud přišel z formuláře, tak přepiš ---
            if (!empty($_POST['mesic'])) {
                $vybranyMesic = $_POST['mesic'];
            } elseif (!empty($_GET['mesic'])) {
                $vybranyMesic = $_GET['mesic'];
            } elseif (!empty($_POST['hid_mesic'])) {
                $vybranyMesic = $_POST['hid_mesic'];
            } elseif (!empty($_SESSION["vybranymesic"])) {
                $vybranyMesic = $_SESSION["vybranymesic"];
            }

            [$rok, $mesic] = explode('-', $vybranyMesic);
            $rok = (int)$rok;
            $mesic = (int)$mesic;
            ?>

            <div class="container-fluid">

            <div class="d-grid gap-2 d-md-flex justify-content-md-center d-print-none">
                <form class="row g-3" action="report4.php?typ=filtr" method="post">

                    <div class="col-auto">
                        <label for="datepicker">Výběr cílové st.</label>

                        <select class="form-select mt-2" id="cilova" name="cilova">

                            <?php
                            global $conn;

                            if ($_SESSION["typ"] == "5")
                            {
                                $sql = "SELECT DISTINCT(cilova) as cilova FROM zamestnanci where cilova <> '' order by cilova";
                            }
                            else
                            {
                                $sql = "SELECT DISTINCT(cilova) as cilova FROM zamestnanci where cilova <> '' and firma in (" . $_SESSION["firma"] . ") order by cilova";
                            }

                            if (!($vysledek = mysqli_query($conn, $sql)))
                            {
                            die("Nelze provést dotaz</body></html>");
                            }

                            while ($radek = mysqli_fetch_array($vysledek))
                            {
                                if ($cilova == ($radek["cilova"]))
                                {
                                    ?>
                                        <option value="<?php echo $radek["cilova"];?>" selected><?php echo $radek["cilova"];?></option>
                                    <?php
                                }
                                else
                                {
                                    ?>
                                        <option value="<?php echo $radek["cilova"];?>"><?php echo $radek["cilova"];?></option>
                                    <?php
                                }
                            }

                            mysqli_free_result($vysledek);
                            ?>

                        </select>
                    </div>

                    <div class="col-auto">
                        <label for="datepicker">Výběr měsíce</label>

                        <select class="form-select mt-2" id="mesic" name="mesic">

                        <?php
                        $start = new DateTime("2023-10-01");
                        $end = new DateTime("first day of this month");
                        $end->modify("+2 months");

                        $interval = new DateInterval("P1M");
                        $period = new DatePeriod($start, $interval, $end->modify("+1 month"));

                        $mesice = [];
                        foreach ($period as $dt) {
                            $mesice[] = $dt->format("Y-m");
                        }
                        $mesice = array_reverse($mesice);

                        foreach ($mesice as $value) {
                            if ($vybranyMesic === $value) {
                                echo '<option value="' . $value . '" selected>' . $value . '</option>';
                            } else {
                                echo '<option value="' . $value . '">' . $value . '</option>';
                            }
                        }
                        ?>
                        </select>

                    </div>

                    <input type="hidden" id="hid_mesic" name="hid_mesic" value="<?php echo htmlspecialchars($vybranyMesic, ENT_QUOTES); ?>">

                    <div class="col-auto">
                        <label for="vyber"> </label>
                        <button type="submit" class="form-control btn btn-primary mt-2">Proveď výběr</button>
                    </div>

                </form>
            </div>

            <?php

            $kalendarskySystem = CAL_GREGORIAN;
            $pocetDniVMesici = cal_days_in_month($kalendarskySystem, $mesic, $rok);
            $start_day = $rok . "-" . $mesic . "-01";
            $dnu = $pocetDniVMesici-1;
            $end_day = $rok . "-" . $mesic . "-" . $pocetDniVMesici;

            $dotaz = "SELECT zamestnanci.id AS zamestnanec, cilova, vstup, vystup, os_cislo, nastupy.zastavka, telefon
                FROM zamestnanci
                LEFT JOIN nastupy ON zamestnanci.nastup = nastupy.id
                WHERE
                    nastup <= LAST_DAY('" . $start_day . "')
                    AND (vystup IS NULL OR vystup >= DATE_FORMAT('" . $start_day . "', '%Y-%m-01'))
                    AND cilova = '" . $cilova . "'
                ORDER BY prijmeni";

            echo "<br>";

            if (!($vysledek = mysqli_query($conn, $dotaz)))
            {
            die("Nelze provést dotaz.</body></html>");
            }

            $num_id = array();

            while ($radek = mysqli_fetch_array($vysledek))
            {
                $num_id[] = $radek;
            }

            mysqli_free_result($vysledek);

            ?>

            <div class="container-fluid">
            <div class="row justify-content-md-center">
            <div class="col col-md-12">

            <?php
            echo "<br>";
            echo "<div class='table-responsive-lg text-center'>";
            echo "<table class='table table-hover'>";
            echo "<thead>";

            echo "<tr class='horizontal-line-hore'><th scope='col' class='text-center'>Den</th>";
            for ($x = 0; $x <= $dnu; $x++) {
                $datum   = date('Y-m-d', strtotime("$start_day +$x days"));
                $weekDay = date('N', strtotime($datum));
                $barva   = ($weekDay >= 6) ? 'table-warning' : '';
                echo "<th scope='col' class='text-center $barva'>" . date('d', strtotime($datum)) . "</th>";
            }
            echo "</tr>";

            echo "<tr class='horizontal-line-dole'><th scope='col' class='text-center' id='hlavickaPoradi'>Jméno a příjmení</th>";
            $dnyTydne = ['1'=>'Po','2'=>'Út','3'=>'St','4'=>'Čt','5'=>'Pá','6'=>'So','7'=>'Ne'];

            for ($x = 0; $x <= $dnu; $x++) {
                $datum   = date('Y-m-d', strtotime("$start_day +$x days"));
                $weekDay = date('N', strtotime($datum));
                $barva   = ($weekDay >= 6) ? 'table-warning' : '';
                echo "<th scope='col' class='text-center $barva'>" . ($dnyTydne[$weekDay] ?? '') . "</th>";
            }

            echo "</tr></thead>";
            echo "<tbody>";

            $suma = array_fill(0, $dnu+1, 0);
            $suma_nepritomnost = array_fill(0, $dnu+1, 0);
            $pocet_zamestnancu = 0;
            $celkem_objednavka = 0;

            $smeny = ['R','O','N'];
            $nepritomnosti_warning = ['DPN','OČR','ABS','LEK'];
            $nepritomnosti_other = ['DOV','NAR','NEM','NEO','NEP','PRO','NEPV','NAHV','SVA','VOL'];

            echo "<tbody>";

            $suma_nepritomnost = array_fill(0, $dnu+1, 0);

            $ids = array_column($num_id, 'zamestnanec');
            $dochazka = nacti_dochazku_mesic($ids, $start_day, $dnu);
            $nepritomnosti = nacti_nepritomnosti_mesic($ids, $start_day, $dnu);

            // --- aktuální ISO týden/rok (pozor: ISO rok je date('o'), ne date('Y')) ---
            $curWeek = (int)date('W');
            $curYear = (int)date('o');

            // --- načtení plánované směny pro aktuální týden pro všechny zobrazené zaměstnance ---
            $plan_smeny = []; // [zam_id => 'R'/'O'/'N'/'X'/'CH']
            $plan_trasy = []; // [zam_id => trasa]

            if (!empty($ids)) {
                // připrav IN (?, ?, ?, ...)
                $placeholders = implode(',', array_fill(0, count($ids), '?'));

                $sqlPlan = "SELECT jmeno, smena, trasa
                            FROM plan_smen
                            WHERE rok = ?
                            AND tyden = ?
                            AND jmeno IN ($placeholders)";

                $stmtPlan = $conn->prepare($sqlPlan);
                if ($stmtPlan) {
                    // bind_param: ii + N×i
                    $types = 'ii' . str_repeat('i', count($ids));
                    $params = array_merge([$curYear, $curWeek], array_map('intval', $ids));

                    // bind_param přes ... (PHP 5.6+)
                    $stmtPlan->bind_param($types, ...$params);

                    $stmtPlan->execute();
                    $resPlan = $stmtPlan->get_result();
                    while ($r = $resPlan->fetch_assoc()) {
                        $plan_smeny[(int)$r['jmeno']] = (string)$r['smena'];
                        $plan_trasy[(int)$r['jmeno']] = (string)($r['trasa'] ?? '');
                    }
                    $stmtPlan->close();
                }
            }

            $suma_typy = [];

            foreach ($num_id as $value)
            {
                $id = $value["zamestnanec"];

                $is_numeric_os_cislo = preg_match('/^[0-9]+$/', $value['os_cislo']);
                $first_td_class = $is_numeric_os_cislo ? '' : 'table-primary';

                $vstup  = (empty($value['vstup']) || $value['vstup'] === '0000-00-00') ? '—' : date('d.m.Y', strtotime($value['vstup']));
                $vystup = (empty($value['vystup']) || $value['vystup'] === '0000-00-00') ? '—' : date('d.m.Y', strtotime($value['vystup']));

                $os_cislo = !empty($value['os_cislo']) ? $value['os_cislo'] : '—';
                $tooltip_text = "Osobní číslo: $os_cislo<br>Nástup: $vstup<br>Výstup: $vystup";

                $mesic_start = strtotime($rok . '-' . $mesic . '-01');
                $mesic_end   = strtotime(date('Y-m-t', $mesic_start));

                $nastup_ts  = !empty($value['vstup']) && $value['vstup'] !== '0000-00-00' ? strtotime($value['vstup']) : false;
                $vystup_ts  = !empty($value['vystup']) && $value['vystup'] !== '0000-00-00' ? strtotime($value['vystup']) : false;

                if (($nastup_ts && $nastup_ts > $mesic_end) || ($vystup_ts && $vystup_ts < $mesic_start)) {
                    continue;
                }

                $ma_dochazku = !empty($dochazka[$id]) || !empty($kontrola_dochazky[$id]) || !empty($nepritomnosti[$id]);
                $vstup_vystup_stejny = ($value["vstup"] !== null && $value["vystup"] !== null && $value["vstup"] == $value["vystup"]);
                if (!$ma_dochazku && $vstup_vystup_stejny) {
                    continue;
                }

                echo "<tr id='zam-" . (int)$id . "'>";
                echo "<td class='$first_td_class'>";

                $pocet_zamestnancu += 1;

                // --- zastavka (doprava) ---
                $zastavka_raw = (string)($value['zastavka'] ?? '');
                $zastavka_trim = trim($zastavka_raw);

                if ($zastavka_trim === '') $zastavka_trim = '—';

                $is_car = (mb_strtolower($zastavka_trim, 'UTF-8') === mb_strtolower('Vlastní auto', 'UTF-8'));

                if ($is_car) {
                    $transport_icon = "🚗";
                    $transport_tooltip = "Vlastní doprava";
                } else {
                    $transport_icon = "🚌";
                    $transport_tooltip = "Nástupní stanice:<br>" . $zastavka_trim;
                }

                // --- telefon ---
                $telefon_raw = trim((string)($value['telefon'] ?? ''));
                if ($telefon_raw !== '') {
                    $telefon_tooltip = "Telefon:<br>" . $telefon_raw;
                    $telefon_span = "<span class='tooltip-symbol ms-1'
                        data-bs-toggle='tooltip'
                        data-bs-placement='top'
                        data-bs-html='true'
                        title='" . htmlspecialchars($telefon_tooltip, ENT_QUOTES, 'UTF-8') . "'>📞</span>";
                } else {
                    $telefon_span = "";
                }

                $transport_span = "<span class='tooltip-symbol ms-1' data-bs-toggle='tooltip' data-bs-placement='top' data-bs-html='true' title='"
                                . htmlspecialchars($transport_tooltip, ENT_QUOTES, 'UTF-8') . "'>$transport_icon</span>";

                $tooltip_span = "<span class='tooltip-symbol ms-1' data-bs-toggle='tooltip' data-bs-placement='top' data-bs-html='true' title='"
                            . htmlspecialchars($tooltip_text, ENT_QUOTES, 'UTF-8') . "'>⏱</span>";

                // aktuální vystup pro modal (YYYY-MM-DD nebo prázdné)
                $current_vystup_raw = (string)($value['vystup'] ?? '');
                $current_vystup_modal = ($current_vystup_raw && $current_vystup_raw !== '0000-00-00') ? $current_vystup_raw : '';

                // --- "monterky" ikonka: pokud je konec PP a délka < 3 měsíce ---
                $monterky_span = '';
                $show_monterky = false;

                $vstup_raw  = (string)($value['vstup'] ?? '');
                $vystup_raw = (string)($value['vystup'] ?? '');

                $has_vstup  = ($vstup_raw !== '' && $vstup_raw !== '0000-00-00');
                $has_vystup = ($vystup_raw !== '' && $vystup_raw !== '0000-00-00');

                if ($has_vstup && $has_vystup) {
                    try {
                        $dtStart = new DateTimeImmutable($vstup_raw);
                        $dtEnd   = new DateTimeImmutable($vystup_raw);

                        // bezpečnost: jen když konec je po začátku
                        if ($dtEnd >= $dtStart) {
                            $limit = $dtStart->modify('+3 months');
                            if ($dtEnd < $limit) {
                                $show_monterky = true;
                            }
                        }
                    } catch (Exception $e) {
                        // ignoruj chybné datum
                    }
                }

                if ($show_monterky) {
                    $monterky_icon = "👖";
                    $monterky_tooltip = "Pracovní poměr kratší než 3 měsíce<br>($vstup_raw → $vystup_raw)";

                    $monterky_span = "<span class='tooltip-symbol ms-1'
                        data-bs-toggle='tooltip'
                        data-bs-placement='top'
                        data-bs-html='true'
                        title='" . htmlspecialchars($monterky_tooltip, ENT_QUOTES, 'UTF-8') . "'>
                        $monterky_icon
                    </span>";
                }
                                
                // --- badge směny pro aktuální týden ---
                $shift_span = '';
                $shift = $plan_smeny[$id] ?? '';
                $route = trim((string)($plan_trasy[$id] ?? ''));

                $route_tooltip = ($route !== '')
                    ? ("Trasa tento tyden: <b>" . htmlspecialchars($route, ENT_QUOTES, 'UTF-8') . "</b><br>Tyden: $curWeek/$curYear")
                    : ("Trasa tento tyden: <b>chybi</b><br>Tyden: $curWeek/$curYear");

                $route_span = ($route !== '')
                    ? ("<span class='ms-1 text-success tooltip-symbol' data-bs-toggle='tooltip' data-bs-placement='top' data-bs-html='true' title='" . htmlspecialchars($route_tooltip, ENT_QUOTES, 'UTF-8') . "'><i class='bi bi-check2-circle-fill'></i></span>")
                    : ("<span class='ms-1 text-danger tooltip-symbol' data-bs-toggle='tooltip' data-bs-placement='top' data-bs-html='true' title='" . htmlspecialchars($route_tooltip, ENT_QUOTES, 'UTF-8') . "'><i class='bi bi-exclamation-circle-fill'></i></span>");

                if ($shift !== '') {

                    switch ($shift) {
                        case 'R':
                            $badgeClass = 'bg-success';
                            break;
                        case 'O':
                            $badgeClass = 'bg-primary';
                            break;
                        case 'N':
                            $badgeClass = 'bg-danger';
                            break;
                        case 'X':
                            $badgeClass = 'bg-dark';
                            break;
                        case 'CH':
                            $badgeClass = 'bg-purple'; // nemá default → vyřešíme níže
                            break;
                        default:
                            $badgeClass = 'bg-secondary';
                    }

                    // pokud chceš hezčí fialovou pro CH:
                    if ($shift === 'CH') {
                        $badgeClass = 'bg-secondary';
                    }

                    $safeShift = htmlspecialchars($shift, ENT_QUOTES, 'UTF-8');

                    $shift_tooltip = "Směna tento týden: <b>$safeShift</b><br>Týden: $curWeek/$curYear";

                    $shift_span = "<span class='badge $badgeClass ms-1 tooltip-symbol'
                        data-bs-toggle='tooltip'
                        data-bs-placement='top'
                        data-bs-html='true'
                        title='" . htmlspecialchars($shift_tooltip, ENT_QUOTES, 'UTF-8') . "'>
                        $safeShift
                    </span>";
                }

                ?>
                <div class="d-flex align-items-center gap-1">
                    <a class="form-control btn <?php echo $is_numeric_os_cislo ? 'bg-warning' : 'bg-primary text-white'; ?> text-start flex-grow-1"
                       onclick="vyrob_modal('<?php echo $rok; ?>','<?php echo $mesic; ?>','<?php echo $id; ?>')">
                        <?php echo $pocet_zamestnancu; ?>. <?php echo get_name_from_id_zam($id); ?>
                        <?php echo $tooltip_span; ?>
                        <?php echo $transport_span; ?>
                        <?php echo $telefon_span; ?>
                        <?php echo $monterky_span; ?>
                        <?php echo $shift_span; ?>
                        <?php echo $route_span; ?>
                    </a>

                    <!-- ✅ nové tlačítko "Ukončit poměr" -->
                    <button type="button"
                            class="btn btn-outline-danger btn-sm"
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="Ukončit poměr"
                            onclick="openVystupModal(event, <?php echo (int)$id; ?>, '<?php echo htmlspecialchars(get_name_from_id_zam($id), ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($current_vystup_modal, ENT_QUOTES, 'UTF-8'); ?>')">
                        <i class="bi bi-door-closed"></i>
                    </button>
                </div>
                <?php

                echo "</td>";

                for ($x = 0; $x <= $dnu; $x++)
                {
                    $datum = date('Y-m-d', strtotime($start_day . '+ '.$x.'days'));
                    $weekDay = date('N', strtotime($datum));

                    if (isset($dochazka[$id][$datum])) {
                        $hodnota = $dochazka[$id][$datum]['hodnota'];
                        $poznamka = $dochazka[$id][$datum]['poznamka'];
                    } elseif (isset($kontrola_dochazky[$id][$datum])) {
                        $hodnota = $kontrola_dochazky[$id][$datum];
                        $poznamka = '';
                    } elseif (isset($nepritomnosti[$id][$datum])) {
                        $hodnota = $nepritomnosti[$id][$datum]['hodnota'];
                        $poznamka = $nepritomnosti[$id][$datum]['poznamka'];
                    } else {
                        $hodnota = '';
                        $poznamka = '';
                    }

                    if ($hodnota !== '') {
                        if (!isset($suma_typy[$hodnota][$x])) {
                            $suma_typy[$hodnota][$x] = 0;
                        }
                        $suma_typy[$hodnota][$x]++;
                    }

                    if (($weekDay % 7) == 6 || ($weekDay % 7) == 0) {
                        $barva = 'table-warning';
                    } elseif (in_array($hodnota, $smeny)) {
                        $barva = 'table-success';
                    } elseif (in_array($hodnota, $nepritomnosti_warning)) {
                        $barva = 'table-secondary';
                    } elseif (in_array($hodnota, $nepritomnosti_other)) {
                        $barva = 'table-primary';
                    } else {
                        $barva = 'table-secondary-subtle';
                    }

                    $extra_border = '';
                    if ($datum == $value["vstup"]) $extra_border = 'border-vstup';
                    if ($value["vystup"] !== null && $datum == $value["vystup"]) $extra_border = 'border-vystup';

                    $symbol_html = '';
                    if (!empty($poznamka)) {
                        $symbol_html = "<span class='poznamka-symbol' data-bs-toggle='tooltip' data-bs-placement='top' title='"
                                        . htmlspecialchars($poznamka, ENT_QUOTES) . "'>&#x1F4DD;</span>";
                    }

                    echo "<td class='text-center $barva $extra_border' style='position: relative;'>$symbol_html<b>$hodnota</b></td>";
                }

                echo "</tr>";
            }

            // inicializace všech tooltipů (Bootstrap 5)
            echo <<<HTML
            <script>
            document.addEventListener("DOMContentLoaded", function() {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            });
            </script>
            HTML;

            $priorita_typu = ['R', 'O', 'N'];
            $serazene_typy = [];

            foreach ($priorita_typu as $p) {
                if (isset($suma_typy[$p])) {
                    $serazene_typy[$p] = $suma_typy[$p];
                }
            }

            $ostatni = [];
            foreach ($suma_typy as $typ => $dny) {
                if (!in_array($typ, $priorita_typu)) {
                    $ostatni[$typ] = $dny;
                }
            }
            ksort($ostatni);
            $serazene_typy += $ostatni;

            echo "<tr class='horizontal-line-hore'>";
            echo "<th scope='col' class='text-center' rowspan='2'>Sumarizační tabulka</th>";

            for ($x = 0; $x <= $dnu; $x++) {
                $datum   = date('Y-m-d', strtotime("$start_day +$x days"));
                $weekDay = date('N', strtotime($datum));
                $barva   = ($weekDay >= 6) ? 'table-warning' : '';
                echo "<th class='text-center $barva'>" . date('d', strtotime($datum)) . "</th>";
            }
            echo "</tr>";

            echo "<tr class='horizontal-line-dole'>";
            $dnyTydne = ['1'=>'Po','2'=>'Út','3'=>'St','4'=>'Čt','5'=>'Pá','6'=>'So','7'=>'Ne'];

            for ($x = 0; $x <= $dnu; $x++) {
                $datum   = date('Y-m-d', strtotime("$start_day +$x days"));
                $weekDay = date('N', strtotime($datum));
                $barva   = ($weekDay >= 6) ? 'table-warning' : '';
                echo "<th class='text-center $barva'>" . ($dnyTydne[$weekDay] ?? '') . "</th>";
            }
            echo "</tr>";

            foreach ($serazene_typy as $typ => $dny) {
                echo "<tr>";
                echo "<td class='table-danger text-end'><b>$typ</b></td>";

                for ($x = 0; $x <= $dnu; $x++) {
                    $datum   = date('Y-m-d', strtotime("$start_day +$x days"));
                    $weekDay = date('N', strtotime($datum));
                    $pocet   = $dny[$x] ?? 0;

                    $barva = ($weekDay == 6 || $weekDay == 7) ? 'table-warning' :
                            ($pocet > 0 ? 'table-primary' : 'table-secondary-subtle');

                    echo "<td class='text-center $barva'>" . ($pocet > 0 ? "<b>$pocet</b>" : "") . "</td>";
                }

                echo "</tr>";
            }

            echo "</tbody>";
            ?>

            <input type="hidden" id="celkovyPocet" value="<?= $pocet_zamestnancu ?>">

            </table>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const celkem = document.getElementById('celkovyPocet').value;
                document.getElementById('hlavickaPoradi').textContent = 'Jméno a příjmení (počet: ' + celkem + ')';
            });
            </script>

        <?php

        }
        else
        {   ?>
                <h1 class="text-center m-2 p-2">NEAUTORIZOVANÝ PŘÍSTUP</h1>
                <meta http-equiv="refresh" content="5;url=main.php">
            <?php
        }
    }
    else
    {   ?>
            <h1 class="text-center m-2 p-2">NEAUTORIZOVANÝ PŘÍSTUP</h1>
            <meta http-equiv="refresh" content="5;url=main.php">
        <?php
    }

}
else
{   ?>
        <h1 class="text-center m-2 p-2">NEAUTORIZOVANÝ PŘÍSTUP</h1>
        <meta http-equiv="refresh" content="5;url=login.php">
    <?php
}
?>

<script>
    function cleanupDochazkaModalArtifacts() {
        document.querySelectorAll('.modal-backdrop').forEach(function(el) { el.remove(); });
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('padding-right');
        document.body.style.removeProperty('overflow');
    }

    function vyrob_modal(rok, mesic, zamestnanec) {
        var functionName = 'vyrob_kalendar';

        var oldModalEl = document.getElementById('kalendar_dochazka');
        if (oldModalEl) {
            try {
                var oldInst = bootstrap.Modal.getInstance(oldModalEl);
                if (oldInst) {
                    oldInst.hide();
                    oldInst.dispose();
                }
            } catch (e) {
                console.warn(e);
            }
        }
        cleanupDochazkaModalArtifacts();

        $.ajax({
            url: 'funkce.php',
            type: 'GET',
            data: { functionName3: functionName, ID: rok, ID2: mesic, ID3: zamestnanec },
            success: function(response) {
                $('#modalContent').html(response);

                var newModalEl = document.getElementById('kalendar_dochazka');
                if (!newModalEl) return;

                var inst = new bootstrap.Modal(newModalEl, {
                    backdrop: 'static',
                    keyboard: false
                });
                inst.show();
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    function vyrob_modal_nav(delta) {
        var modal = document.getElementById('kalendar_dochazka');
        if (!modal) return;

        var rok = parseInt(modal.getAttribute('data-rok') || '0', 10);
        var mesic = parseInt(modal.getAttribute('data-mesic') || '0', 10);
        var zamestnanec = parseInt(modal.getAttribute('data-zam') || '0', 10);

        if (!rok || !mesic || !zamestnanec) return;

        mesic = mesic + delta;
        if (mesic < 1) {
            mesic = 12;
            rok--;
        } else if (mesic > 12) {
            mesic = 1;
            rok++;
        }

        vyrob_modal(String(rok), String(mesic), String(zamestnanec));
    }
</script>

<!-- ✅ MODAL pro ukončení poměru -->
<div class="modal fade" id="modalVystup" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-door-closed me-2"></i>Ukončit pracovní poměr</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zavřít"></button>
      </div>

      <div class="modal-body">
        <div class="mb-2 text-muted small" id="vystupEmployeeName"></div>

        <input type="hidden" id="vystupZamId" value="0">

        <label class="form-label">Datum výstupu</label>
        <input type="date" class="form-control" id="vystupDate">

        <div class="form-check mt-2">
          <input class="form-check-input" type="checkbox" id="vystupClear">
          <label class="form-check-label" for="vystupClear">Zrušit ukončení (nastaví 0000-00-00)</label>
        </div>

        <div class="text-danger small mt-2 d-none" id="vystupErr"></div>
        <div class="text-success small mt-2 d-none" id="vystupOk"></div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Zrušit</button>
        <button type="button" class="btn btn-danger" onclick="saveVystup()">Uložit</button>
      </div>

    </div>
  </div>
</div>

<script>
let vystupModal;

document.addEventListener('DOMContentLoaded', () => {
  const el = document.getElementById('modalVystup');
  if (el) vystupModal = new bootstrap.Modal(el);

  // pokud zaškrtnu "zrušit", deaktivuj date input
  const cb = document.getElementById('vystupClear');
  const date = document.getElementById('vystupDate');
  if (cb && date) {
    cb.addEventListener('change', () => {
      date.disabled = cb.checked;
      if (cb.checked) date.value = '';
    });
  }
});

function openVystupModal(ev, zamId, zamName, currentVystup) {
  if (ev) ev.stopPropagation(); // ať se neotevře kalendář

  const err = document.getElementById('vystupErr');
  const ok  = document.getElementById('vystupOk');
  err.classList.add('d-none'); err.textContent = '';
  ok.classList.add('d-none');  ok.textContent = '';

  document.getElementById('vystupZamId').value = zamId;
  document.getElementById('vystupEmployeeName').textContent = zamName;

  const cb = document.getElementById('vystupClear');
  const inp = document.getElementById('vystupDate');

  cb.checked = false;
  inp.disabled = false;
  inp.value = (currentVystup || '').trim();

  vystupModal.show();
}

async function saveVystup() {
  const zamId = parseInt(document.getElementById('vystupZamId').value || '0', 10);
  const cb = document.getElementById('vystupClear');
  const vystup = cb.checked ? '' : (document.getElementById('vystupDate').value || '').trim();

  const err = document.getElementById('vystupErr');
  const ok  = document.getElementById('vystupOk');
  err.classList.add('d-none'); err.textContent = '';
  ok.classList.add('d-none');  ok.textContent = '';

  if (!zamId) {
    err.textContent = 'Neplatné ID zaměstnance.';
    err.classList.remove('d-none');
    return;
  }
  if (!cb.checked && !vystup) {
    err.textContent = 'Vyplň datum výstupu, nebo zaškrtni "Zrušit ukončení".';
    err.classList.remove('d-none');
    return;
  }

  const fd = new FormData();
  fd.append('action', 'set_vystup');
  fd.append('zam_id', String(zamId));
  fd.append('vystup', vystup);

  try {
    const res = await fetch(window.location.pathname, {
      method: 'POST',
      body: fd,
      credentials: 'same-origin'
    });
    const data = await res.json();

    if (!data.ok) {
      err.textContent = data.msg || 'Uložení se nezdařilo.';
      err.classList.remove('d-none');
      return;
    }

    ok.textContent = 'Uloženo.';
    ok.classList.remove('d-none');

    setTimeout(() => {
    const url = new URL(window.location.href);
    url.searchParams.set('scroll_id', String(zamId));
    window.location.href = url.toString();
    }, 250);

  } catch (e) {
    err.textContent = 'Chyba komunikace se serverem.';
    err.classList.remove('d-none');
  }
}

document.addEventListener('DOMContentLoaded', function () {
  const url = new URL(window.location.href);
  const sid = url.searchParams.get('scroll_id');
  if (!sid) return;

  const el = document.getElementById('zam-' + sid);
  if (el) {
    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }

  // volitelné: uklidit URL, aby to po F5 neskákalo znovu
  url.searchParams.delete('scroll_id');
  window.history.replaceState({}, '', url.toString());
});
</script>

<div id="modalContent"></div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

</body>
</html>


