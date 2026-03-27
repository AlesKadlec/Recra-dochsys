<?php
// Zahrnutí souboru s údaji o připojení
include('db.php');
include ('funkce.php');

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

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap-datepicker CSS (verze 1.9.0) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.4.0/css/fixedHeader.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* DataTables tlačítka vlevo nahoře */
        .dataTables_wrapper .top .dt-buttons {
            float: left;
            margin-right: 10px;
        }
    </style>


    <title>ZAMĚSTNANCI</title>
</head>
<body>

<script type="text/javascript">
function change_pomer(p1)
{
  v = document.zamestnanci.pomer.value;
   
  if (v == 0)
  {
    document.zamestnanci.smena.selectedIndex = 13;
  }
  else if (v == 1)
  {
    document.zamestnanci.smena.selectedIndex = 0;
  }
  else if (v == 33)
  {
    document.zamestnanci.smena.selectedIndex = 13;
  }
  else if (v == 44)
  {
    document.zamestnanci.smena.selectedIndex = 13;
  }
   
}
</script>

<?php

global $conn;

menu();

if (kontrola_prihlaseni() == "OK")
{   

    if (isset($_GET["typ"]))
    {
        if (isset($_GET["typ"]) && $_GET["typ"] === "savezamestnance" && in_array($_SESSION["typ"], ["1","4","5"])) {

            // === VALIDACE & SANITACE ===
            $prijmeni = trim($_POST["prijmeni"] ?? '');
            $jmeno = trim($_POST["jmeno"] ?? '');
            $rfid = trim($_POST["rfid"] ?? '');
            $nastup = intval($_POST["nastup"] ?? 0);
            $telefon = trim($_POST["telefon"] ?? '');
            $adresa = trim($_POST["adresa"] ?? '');
            $firma = intval($_POST["firma"] ?? 0);
            $smena = $_POST["smena"] ?? 'N/A';
            $smena2 = $_POST["smena2"] ?? 'N/A';
            $nepritomnost = $_POST["nepritomnost"] ?? '';
            $cilova = trim($_POST["cilova"] ?? '');

            // === DATUMY ===
            $date1 = DateTime::createFromFormat('d.m.Y', $_POST["datepicker"] ?? '');
            $date2 = DateTime::createFromFormat('d.m.Y', $_POST["datepicker2"] ?? '');

            $vstup = $date1 ? $date1->format('Y-m-d') : null;
            $vystup = $date2 ? $date2->format('Y-m-d') : null;

            // === OS ČÍSLO ===
            $os_cislo = zjisti_osobni_cislo();

            // === INSERT ===
            $stmt = $conn->prepare("
                INSERT INTO zamestnanci 
                (os_cislo, prijmeni, jmeno, rfid, nastup, telefon, adresa, firma, smena, aktivni, nepritomnost, smena2, cilova, vstup, vystup, smennost) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?, ?, ?, ?, '3SM')
            ");

            if (!$stmt) {
                die("Chyba prepare: " . $conn->error);
            }

            $stmt->bind_param("ssssississssss",$os_cislo,$prijmeni,$jmeno,$rfid,$nastup,$telefon,$adresa,$firma,$smena,$nepritomnost,$smena2,$cilova,$vstup,$vystup);

            if (!$stmt->execute()) {
                die("Chyba execute: " . $stmt->error);
            }

            $stmt->close();

            // === LOG ===
            $now = new DateTime('now', new DateTimeZone('Europe/Prague'));

            $logText = "Vytvořen nový zaměstnanec " . $prijmeni . " " . $jmeno;

            $stmtLog = $conn->prepare("
                INSERT INTO logs (kdo, typ, infotext, datumcas)
                VALUES (?, 'Nový zaměstnanec', ?, ?)
            ");

            if (!$stmtLog) {
                die("Chyba prepare log: " . $conn->error);
            }

            $stmtLog->bind_param(
                "iss",
                $_SESSION["log_id"],
                $logText,
                $now->format('Y-m-d H:i:s')
            );

            if (!$stmtLog->execute()) {
                die("Chyba log execute: " . $stmtLog->error);
            }

            $stmtLog->close();
            ?>

            <div class="container">
                <h3 class="text-center m-2 p-2 text-success">
                    <i class="bi bi-check-circle"></i> Zaměstnanec přidán
                </h3>

                <div class="text-center text-muted">
                    Probíhá přesměrování...
                </div>
            </div>

            <meta http-equiv="refresh" content="2;url=zamestnanci.php">

            <?php
        }
        elseif (($_GET["typ"] == "updatezamestnance" and $_SESSION["typ"] == "1") or ($_GET["typ"] == "updatezamestnance" and $_SESSION["typ"] == "4") or ($_GET["typ"] == "updatezamestnance" and $_SESSION["typ"] == "5"))
        {          
            // --- převod dat (pokud ještě nejsou) ---
            $date1_obj = DateTime::createFromFormat('d.m.Y', $_POST['datepicker']);
            $date1_str = $date1_obj ? $date1_obj->format('Y-m-d') : '0000-00-00';

            // zjistíme checkboxy (true/false)
            $chk_vystup = isset($_POST['checkbox_vystup']) && $_POST['checkbox_vystup'] === 'on';
            //$chk_anulace = isset($_POST['checkbox_anulace']) && $_POST['checkbox_anulace'] === 'on';

            // vystup a anulace podle checkboxu (pokud checkbox není, vloží se 0000-00-00)
            if ($chk_vystup && !empty($_POST['datepicker2'])) {
                $d2 = DateTime::createFromFormat('d.m.Y', $_POST['datepicker2']);
                $date2_str = $d2 ? $d2->format('Y-m-d') : '0000-00-00';
            } else {
                $date2_str = '0000-00-00';
            }

            // dpn_od (nepritomnost) - pokud prázdné, 0000-00-00
            if (empty($_POST['nepritomnost'])) {
                $date4_str = '0000-00-00';
            } else {
                $d4 = DateTime::createFromFormat('d.m.Y', $_POST['datepicker4']);
                $date4_str = $d4 ? $d4->format('Y-m-d') : '0000-00-00';
            }

            // aktivni != 0
            if ($_POST['nepritomnost'] == '') {
                // není nepritomnost
                $smena_val = $_POST['smena'];
                $nepritomnost_val = $_POST['nepritomnost']; // prázdné
                $smena2_val = $_POST['smena2'];
                $dpn_od_val = '0000-00-00';
            } 
            else 
            {
                // je nastaven nepritomnost
                $smena_val = 'N/A';
                $nepritomnost_val = $_POST['nepritomnost'];
                $smena2_val = 'N/A';
                $dpn_od_val = $date4_str;
            }
            
            // připravíme binding a vykonáme jeden prepared UPDATE
            $sql = "UPDATE zamestnanci SET
                        os_cislo = ?,
                        os_cislo_klient = ?,
                        prijmeni = ?,
                        jmeno = ?,
                        rfid = ?,
                        nastup = ?,
                        telefon = ?,
                        adresa = ?,
                        firma = ?,
                        smena = ?,
                        nepritomnost = ?,
                        smena2 = ?,
                        cilova = ?,
                        vstup = ?,
                        vystup = ?,
                        dpn_od = ?,
                        smennost = ?,
                        email = ?
                    WHERE id = ?";

            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }

            // typy: první 10 poli jsou string (s), 11. aktivni je integer (i), další 9 string (s), nakonec id int (i)
            // tedy typový řetězec: 10x s + i + 9x s + i => "ssssssssssisssssssssi"
            $types = "ssssssssssssssssssi";

            $aktivni_int = (int)$_POST['aktivni'];
            $id_zam = (int)$_POST['id_zam'];

            // bindujeme v pořadí dle SQL
            $bound = $stmt->bind_param(
                $types,
                $_POST['oscislo'],        // s
                $_POST['oscisloklient'],  // s
                $_POST['prijmeni'],       // s
                $_POST['jmeno'],          // s
                $_POST['rfid'],           // s
                $_POST['nastup'],         // s
                $_POST['telefon'],        // s
                $_POST['adresa'],         // s
                $_POST['firma'],          // s
                $smena_val,               // s
                $nepritomnost_val,        // s
                $smena2_val,              // s
                $_POST['cilova'],         // s
                $date1_str,               // s
                $date2_str,               // s
                $dpn_od_val,              // s
                $_POST['smennost'],       // s
                $_POST['email'],          // s
                $id_zam                   // i
            );

            if ($bound === false) {
                die("bind_param failed: " . $stmt->error);
            }

            if ($stmt->execute()) {
                // úspěch
                // případně: echo "Uloženo.";
            } else {
                // chyba při execute
                error_log("MySQL execute error: " . $stmt->error);
                // případně: echo "Chyba při ukládání: " . $stmt->error;
            }

            $stmt->close();

            //zaznam do logu
            $now = new DateTime('now', new DateTimeZone('Europe/Prague'));
    
            if (!($vysledek = mysqli_query($conn, 
            "insert into logs (kdo,typ,infotext,datumcas) values ('" . $_SESSION["log_id"] . "','Editace zaměstnance','Editován zaměstnanec " . $_POST["prijmeni"] . " " . $_POST["jmeno"] . "','" . $now->format('Y-m-d H:i:s') . "')")))
            {
            die("Nelze provést dotaz.</body></html>");
            }
            
            $_SESSION["vyberfirma"] = $_POST['firma'];

      
            // když je uložené → zobrazíš hlášku:
            echo "<div class='alert alert-success text-center m-3'>
                    Údaje byly úspěšně uloženy.
                </div>";

            // ------------------------------
            // 2) AUTOMATICKÝ REDIRECT
            // ------------------------------

            // pokud byl modal otevřen z filtru → vrať se na ?typ=filtr
            if (isset($_SESSION["typ_modal"]) && $_SESSION["typ_modal"] === "filtr") {
                echo '<meta http-equiv="refresh" content="5;url=zamestnanci.php?typ=filtr">';
                //echo "11111";
            } 
            else {
                // jinak normální návrat
                echo '<meta http-equiv="refresh" content="5;url=zamestnanci.php">';
                //echo "22222";
            }        

        }
    elseif ((($_GET["typ"] == "filtr" and $_SESSION["typ"] == "1") or ($_GET["typ"] == "filtr" and $_SESSION["typ"] == "3") or ($_GET["typ"] == "filtr" and $_SESSION["typ"] == "4") or ($_GET["typ"] == "filtr" and $_SESSION["typ"] == "5")))
    {
        if (isset($_GET['typ'])) {
            $_SESSION['typ_modal'] = $_GET['typ'];
        }

        if (isset($_GET['reset']) && $_GET['reset'] == '1') {
            unset($_SESSION['filtry']);
            echo "<meta http-equiv='refresh' content='0;url=zamestnanci.php'>";
            exit;
        }

        // --- 1) Uložení filtrů z POST do SESSION ---
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // ✅ datum filtru (výchozí dnes), validace YYYY-MM-DD
            $datum_post = $_POST['datum'] ?? date('Y-m-d');
            $datum_post = trim((string)$datum_post);
            if (!preg_match('~^\d{4}-\d{2}-\d{2}$~', $datum_post)) {
                $datum_post = date('Y-m-d');
            }

            $_SESSION['filtry'] = [
                'datum'        => $datum_post,
                'cilova'       => $_POST['cilova'] ?? 'ALL',
                'pomer'        => $_POST['pomer'] ?? '',
                'smena'        => $_POST['smena'] ?? 'VŠE',
                'nepritomnost' => $_POST['nepritomnost'] ?? 'ALL',
            ];
        }

        // --- 2) Načtení filtrů ze SESSION (nebo default) ---
        $datum_filtru = $_SESSION['filtry']['datum'] ?? date('Y-m-d');
        if (!preg_match('~^\d{4}-\d{2}-\d{2}$~', (string)$datum_filtru)) {
            $datum_filtru = date('Y-m-d');
        }

        $cilova       = $_SESSION['filtry']['cilova']       ?? 'ALL';
        $pomer        = $_SESSION['filtry']['pomer']        ?? '';
        $smena        = $_SESSION['filtry']['smena']        ?? 'VŠE';
        $nepritomnost = $_SESSION['filtry']['nepritomnost'] ?? 'ALL';

        // bezpečné použití v SQL stringu (máš validaci regex, ale tohle neuškodí)
        $datum_safe = mysqli_real_escape_string($conn, $datum_filtru);

        // =====================================================
        // --- 3) Základní SELECT + nepřítomnost pro vybrané datum z tabulky nepritomnost ---
        // =====================================================
        $sql = "SELECT 
                    zamestnanci.id, prijmeni, jmeno, os_cislo, os_cislo_klient, rfid, telefon, adresa,
                    firmy.firma, zastavky.zastavka, zamestnanci.smena, zamestnanci.smena2, auta.spz,
                    zamestnanci.aktivni, cilova, vstup, vystup, radneukoncen, anulace,
                    nep.nepritomnost_today,
                    (SELECT cas FROM dochazka 
                        WHERE zamestnanec = zamestnanci.id 
                        AND now() <= DATE_ADD(concat(datum, ' ', cas), INTERVAL 15 HOUR) 
                        ORDER BY datum DESC, cas DESC LIMIT 1) AS dochazka
                FROM zamestnanci
                LEFT JOIN firmy ON zamestnanci.firma = firmy.id
                LEFT JOIN zastavky ON zamestnanci.nastup = zastavky.id
                LEFT JOIN auta ON zastavky.auto = auta.id

                /* nepřítomnost ke zvolenému datu (max 1 řádek na zaměstnance) */
                LEFT JOIN (
                    SELECT n.zamestnanec, MAX(n.nepritomnost) AS nepritomnost_today
                    FROM nepritomnost n
                    WHERE n.datum = '$datum_safe'
                    GROUP BY n.zamestnanec
                ) nep ON nep.zamestnanec = zamestnanci.id

                WHERE 1=1";

        // --- filtr cílové stanice ---
        if (!empty($cilova) && $cilova != "ALL") {
            $cilova_safe = mysqli_real_escape_string($conn, $cilova);
            $sql .= " AND zamestnanci.cilova = '$cilova_safe'";
        } elseif ($_SESSION['typ'] != 5) {

            $raw = (string)($_SESSION["cilova"] ?? '');
            $parts = array_filter(array_map('trim', explode(',', $raw)), fn($x) => $x !== '');

            if (count($parts) > 0) {
                $cilova_list = array_map(function($v) use($conn) {
                    return "'" . mysqli_real_escape_string($conn, $v) . "'";
                }, $parts);
                $sql .= " AND zamestnanci.cilova IN (" . implode(',', $cilova_list) . ")";
            }
        }

        // --- filtr poměru (✅ počítá se k vybranému datu) ---
        // pokud chceš ponechat staré chování vůči dnešku, stačí tady vrátit CURDATE()
        switch ($pomer) {

            // 1 = aktivní k datu_filtru
            case "1":
                $sql .= " AND vstup <= '$datum_safe'
                        AND (vystup IS NULL OR vystup='' OR vystup='0000-00-00' OR vystup >= '$datum_safe')";
                break;

            // 0 = neaktivní k datu_filtru
            case "0":
                $sql .= " AND vystup < '$datum_safe'
                        AND vystup NOT IN ('', '0000-00-00')
                        AND vystup IS NOT NULL";
                break;

            // 33 = neaktivní + řádné ukončení
            case "33":
                $sql .= " AND vystup < '$datum_safe'
                        AND vystup NOT IN ('', '0000-00-00')
                        AND radneukoncen='ANO'";
                break;

            // 44 = neaktivní + neřádné ukončení
            case "44":
                $sql .= " AND vystup < '$datum_safe'
                        AND vystup NOT IN ('', '0000-00-00')
                        AND radneukoncen='NE'";
                break;

            // 55 = anulace — vstup = vystup + poměr už skončil
            case "55":
                $sql .= " AND vstup = vystup
                        AND vystup < '$datum_safe'
                        AND vystup NOT IN ('', '0000-00-00')";
                break;

            default:
                break;
        }

        // --- filtr směny ---
        if (!empty($smena) && $smena !== 'VŠE') {
            $smena_safe = mysqli_real_escape_string($conn, $smena);
            $sql .= " AND zamestnanci.smena = '$smena_safe'";
        }

        // --- filtr nepřítomnosti (ke zvolenému datu) ---
        if (!empty($nepritomnost) && $nepritomnost != 'ALL') {
            if ($nepritomnost == 'Vše') {
                $sql .= " AND nep.nepritomnost_today IN ('DPN','OČR','DOV','ABS','NAR','LEK','NEM','NEO','NEP','PRO','NEPV','NAHV','SVA','VOL')";
            } else {
                $nep_safe = mysqli_real_escape_string($conn, $nepritomnost);
                $sql .= " AND nep.nepritomnost_today = '$nep_safe'";
            }
        }

        // --- řazení ---
        $sql .= " ORDER BY prijmeni ASC";

        //echo $sql;

        $editRole = in_array($_SESSION['typ'], [1,4,5], true);

        // Hlavička tabulky podle $pomer
        if (in_array($pomer, [0,33,44,55], true)) {
            $cols = ['ID','Příjmení','Jméno','Os.č.','Os.č.kl.','Telefon','Řádně ukončen','Vstup','Výstup','Anulace','Odpracováno'];
        } else {
            $cols = ['ID','Příjmení','Jméno','Os.č.','Os.č.kl.','RFID','Telefon','Vstup','Výstup','Doprava','Nástup','Směna','Cílová st.','Docházka'];
        }
        ?>

        <h3 class="text-center m-2 p-2">Přehled zaměstnanců</h3>

        <div class="container-fluid">
        <div class="row justify-content-md-center">
        <div class="col col-md-12">

        <div class="d-flex justify-content-center my-2">
            <button type="button"
                    class="btn btn-outline-success px-4"
                    data-bs-toggle="collapse"
                    href="#filtry"
                    aria-expanded="true">
                <i class="bi bi-funnel-fill me-1"></i> Filtry
            </button>
        </div>

        <?php filtry_zamestnanci(); ?>

        <br>
        <div class="table-responsive-lg text-center">
        <table id="dalsiTable" class="table table-sm table-hover">
        <thead>
            <tr class="table-active">
                <?php
                foreach ($cols as $col) echo "<th>$col</th>";
                if ($editRole) echo "<th>Editace</th>";
                ?>
            </tr>
            <tr>
                <?php
                foreach ($cols as $col) echo "<th><input type='text' class='form-control form-control-sm' placeholder='Hledat $col'></th>";
                if ($editRole) echo "<th></th>";
                ?>
            </tr>
        </thead>
        <tbody>

        <?php
        $cislo = 1;

        if (!($vysledek = mysqli_query($conn, $sql))) die("Nelze provést dotaz");

        while ($radek = mysqli_fetch_assoc($vysledek)) {

            // nepřítomnost ke zvolenému datu
            $nep_today = (string)($radek["nepritomnost_today"] ?? '');

            // -----------------------------
            // BARVA ŘÁDKU
            // -----------------------------
            if ($radek["aktivni"] != "1") {
                $barva = "table-danger";
            } elseif (strpos($radek["zastavka"] ?? "", "Vlastní auto") !== false) {
                $barva = "table-dark opacity-50";
            } elseif (!empty($radek['dochazka'])) {
                $barva = "table-success";
            } else {
                $barva = "";
            }

            // barvy podle nepřítomnosti ke zvolenému datu
            if (in_array($nep_today, ["ABS","DPN","OČR"], true)) {
                $barva = "table-secondary";
            } elseif ($nep_today === "DOV") {
                $barva = "table-warning";
            }

            // -----------------------------
            // ODPRACOVÁNO (ponechávám jak máš, ale je to stále vůči dnešku)
            // pokud chceš, můžeme to taky přepočítat k $datum_filtru
            // -----------------------------
            $vstup = ($radek['vstup'] ?? '') != '0000-00-00' ? $radek['vstup'] : date('Y-m-d');
            $vystup = (($radek['vystup'] ?? '') != '0000-00-00' && date('Y-m-d') > ($radek['vystup'] ?? '')) ? $radek['vystup'] : date('Y-m-d');

            $interval = (new DateTime($vstup))->diff(new DateTime($vystup));
            $odpracovano = $interval->days;

            $fwClass = $odpracovano <= 90 ? 'fw-bold' : '';
            ?>

            <tr class="<?= $barva ?> <?= $fwClass ?>">
                <td class="text-center fw-bold"><?= $cislo ?></td>

                <?php if (in_array($pomer, [0,33,44,55], true)): ?>
                    <td class='text-start'><?= $radek["prijmeni"] ?></td>
                    <td class='text-start'><?= $radek["jmeno"] ?></td>
                    <td class='text-center'><?= $radek["os_cislo"] ?></td>
                    <td class='text-center'><?= $radek["os_cislo_klient"] ?></td>
                    <td class='text-center'><?= $radek["telefon"] ?></td>
                    <td class='text-center'>
                        <?php
                        $id = $radek['id'];
                        if ($radek['radneukoncen'] == 'ANO') {
                            echo '<a class="btn btn-success opacity-75">ANO</a> ';
                            echo '<a class="btn btn-outline-danger opacity-75" href="zamestnanci.php?typ=filtr&idemp='.$id.'&ukoncen=NE">NE</a>';
                        } elseif ($radek['radneukoncen'] == 'NE') {
                            echo '<a class="btn btn-outline-success opacity-75" href="zamestnanci.php?typ=filtr&idemp='.$id.'&ukoncen=ANO">ANO</a> ';
                            echo '<a class="btn btn-danger opacity-75">NE</a>';
                        } else {
                            echo '<a class="btn btn-outline-success opacity-75" href="zamestnanci.php?typ=filtr&idemp='.$id.'&ukoncen=ANO">ANO</a> ';
                            echo '<a class="btn btn-outline-danger opacity-75" href="zamestnanci.php?typ=filtr&idemp='.$id.'&ukoncen=NE">NE</a>';
                        }
                        ?>
                    </td>
                    <td class='text-center'><?= $radek["vstup"] ?></td>
                    <td class='text-center'><?= $radek["vystup"] ?></td>
                    <td class='text-center'><?= $radek["anulace"] ?></td>
                    <td class='text-center'><?= $odpracovano ?></td>
                <?php else: ?>
                    <td class='text-start'><?= $radek["prijmeni"] ?></td>
                    <td class='text-start'><?= $radek["jmeno"] ?></td>
                    <td class='text-center'><?= $radek["os_cislo"] ?></td>
                    <td class='text-center'><?= $radek["os_cislo_klient"] ?></td>
                    <td class='text-start'><?= $radek["rfid"] ?></td>
                    <td class='text-center'><?= $radek["telefon"] ?></td>
                    <td class='text-center'><?= $radek["vstup"] ?></td>
                    <td class='text-center'><?= $radek["vystup"] ?></td>
                    <td class='text-center'></td>
                    <td class='text-start'></td>
                    <td class='text-center'><?= $radek["smena"] . " / " . ($radek["smena2"] ?: "-") ?></td>
                    <td class='text-center'><?= $radek["cilova"] ?></td>
                    <td class='text-center'><?= $radek['dochazka'] ?></td>
                <?php endif; ?>

                <?php if ($editRole): ?>
                <td>
                    <button type="button" class="btn p-0 border-0 bg-transparent"
                            data-bs-toggle="modal"
                            data-bs-target="#ModalPrihlasitVedoucim<?= $radek['id'] ?>"
                            onclick="loadModalContent('<?= $radek['id'] ?>')">
                        <i class="bi bi-pencil-square fs-5"></i>
                    </button>
                </td>
                <?php endif; ?>
            </tr>

            <?php $cislo++; } mysqli_free_result($vysledek); ?>
        </tbody>
        </table>
        </div>
        </div>
        </div>
        </div>

        <?php
        novy_zamestnanec_modal();
    }

    elseif (($_GET["typ"] == "nastoupil" && $_SESSION["typ"] == "1") || ($_GET["typ"] == "nastoupil" && $_SESSION["typ"] == "5")) 
        {
            global $conn;

            // ✅ validace ID (zabraňuje blbostem + injection ještě před SQL)
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            if ($id <= 0) {
                die("Neplatné ID.</body></html>");
            }

            // =====================================================
            // 1) Načti data z nabory bezpečně
            // =====================================================
            $sql = "SELECT id, jmeno, prijmeni, telefon, adresa, nastup, nastupmisto, smena, klient, firma, cilova, oscislo
                    FROM nabory
                    WHERE id = ?
                    LIMIT 1";

            $stmt = $conn->prepare($sql);
            if (!$stmt) die("Chyba prepare (select).</body></html>");
            $stmt->bind_param("i", $id);

            if (!$stmt->execute()) die("Nelze provést dotaz (select).</body></html>");

            $res = $stmt->get_result();
            $row = $res ? $res->fetch_assoc() : null;
            $stmt->close();

            if (!$row) {
                die("Záznam v náborech nenalezen.</body></html>");
            }

            // =====================================================
            // 2) Insert do zamestnanci (os_cislo + os_cislo_klient + rfid = oscislo)
            // =====================================================
            $oscislo = (string)($row['oscislo'] ?? '');

            $ins = "INSERT INTO zamestnanci
                    (os_cislo, os_cislo_klient, rfid, prijmeni, jmeno, vstup, nastup, telefon, adresa, firma, smena, smena2, cilova, aktivni, nabor, smennost)
                    VALUES
                    (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'N/A', ?, '1', ?, '3SM')";

            $stmt = $conn->prepare($ins);
            if (!$stmt) die("Chyba prepare (insert).</body></html>");

            // bindy (vše jako string kromě nabor ID, ten jako int)
            $prijmeni = (string)$row['prijmeni'];
            $jmeno    = (string)$row['jmeno'];
            $vstup    = (string)$row['nastup'];
            $nastup   = (string)$row['nastupmisto'];
            $telefon  = (string)$row['telefon'];
            $adresa   = (string)$row['adresa'];
            $firma    = (string)$row['firma'];
            $smena    = (string)$row['smena'];
            $cilova   = (string)$row['cilova'];
            $naborId  = (int)$row['id'];

            $stmt->bind_param(
                "ssssssssssssi",
                $oscislo,      // os_cislo
                $oscislo,      // os_cislo_klient
                $oscislo,      // rfid
                $prijmeni,
                $jmeno,
                $vstup,
                $nastup,
                $telefon,
                $adresa,
                $firma,
                $smena,
                $cilova,
                $naborId
            );

            if (!$stmt->execute()) {
                $stmt->close();
                die("Nelze provést dotaz (insert zamestnanci).</body></html>");
            }
            $stmt->close();

            // =====================================================
            // 3) Update nabory (vysledek = Nastoupil)
            // =====================================================
            $upd = "UPDATE nabory SET vysledek = 'Nastoupil' WHERE id = ? LIMIT 1";
            $stmt = $conn->prepare($upd);
            if (!$stmt) die("Chyba prepare (update).</body></html>");
            $stmt->bind_param("i", $naborId);

            if (!$stmt->execute()) {
                $stmt->close();
                die("Nelze provést dotaz (update nabory).</body></html>");
            }
            $stmt->close();

            // =====================================================
            // 4) Log
            // =====================================================
            $now = new DateTime('now', new DateTimeZone('Europe/Prague'));
            $kdo = (string)($_SESSION["log_id"] ?? '');
            $typ = "Přijat zaměstnanec";
            $infotext = "Přijat zaměstnanec " . $prijmeni . " " . $jmeno;
            $datumcas = $now->format('Y-m-d H:i:s');

            $log = "INSERT INTO logs (kdo, typ, infotext, datumcas) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($log);
            if (!$stmt) die("Chyba prepare (log).</body></html>");
            $stmt->bind_param("ssss", $kdo, $typ, $infotext, $datumcas);

            if (!$stmt->execute()) {
                $stmt->close();
                die("Nelze provést dotaz (log).</body></html>");
            }
            $stmt->close();
            ?>

            <div class="container">
                <h3 class="text-center m-2 p-2">NÁSTUP PROVEDEN, DATA PŘEKLOPENA DO TABULKY ZAMĚSTNANCŮ</h3>
                <h3 class="text-center m-2 p-2">Budete přesměrování zpět INFORMACE K NÁBORŮM</h3>
            </div>

            <meta http-equiv="refresh" content="5;url=informace.php">

            <?php
        }
        else
        {   ?>
            <div class="container">
            <h3 class="text-center m-2 p-2">NEPOVOLENÁ OPERACE</h3>

            <h3 class="text-center m-2 p-2">Budete přesměrování zpět HLAVNÍ OBRAZOVKU</h3>

            </div>

            <meta http-equiv="refresh" content="5;url=main.php">
            <?php
        }
    }
    else
    {   ?>

        <?php
        // -------------------------------
        // ÚVOD
        // -------------------------------
        if (isset($_GET['typ'])) {
            $_SESSION['typ_modal'] = $_GET['typ'];
        }

        $typ = $_SESSION['typ'] ?? 0;
        $editRole = in_array($typ, [1,4,5]);
        ?>

        <h3 class="text-center m-2 p-2">Přehled zaměstnanců</h3>

        <div class="container-fluid">

        <?php if (in_array($typ, [1,3,4,5])): ?>

            <?php if ($editRole): ?>
                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                    <button type="button" class="btn btn-outline-success text-center m-2"
                        data-bs-toggle="collapse" href="#filtry" aria-expanded="true">
                        Filtry
                    </button>
                </div>
            <?php endif; ?>

            <?php filtry_zamestnanci(); ?>

        <?php endif; ?>

        <br>

        <div class="table-responsive-lg text-center">
        <table id="zamestnanciTable" class="table table-sm table-hover">
        <thead>
            <tr class="table-active">
                <th>ID</th><th>Příjmení</th><th>Jméno</th><th>Os.č.</th><th>Os.č.kl.</th>
                <th>RFID</th><th>Telefon</th><th>Vstup</th><th>Výstup</th><th>Doprava</th>
                <th>Nástup</th><th>Směna</th><th>Cílová st.</th><th>Docházka</th>
                <?php if ($editRole): ?><th>Editace</th><?php endif; ?>
            </tr>

            <!-- Řádek filtrů -->
            <tr>
                <?php
                $inputs = ["ID","Příjmení","Jméno","Os.č.","Os.č.kl.","RFID","Telefon","Vstup","Výstup","Doprava","Nástup","Směna","Cílová","Docházka"];
                foreach ($inputs as $inp) {
                    echo "<th><input type='text' class='form-control form-control-sm' placeholder='Hledat $inp'></th>";
                }
                if ($editRole) echo "<th></th>";
                ?>
            </tr>
        </thead>

        <tbody>

        <?php
        $cislo = 1;

        // DVA SQL DOTAZY — podle tvého přání zůstávají
        if ($typ == 5) {
            $sql = "SELECT zamestnanci.id, prijmeni, jmeno, os_cislo, os_cislo_klient, rfid, telefon,
                    adresa, firmy.firma, zastavky.zastavka, zamestnanci.smena, zamestnanci.smena2,
                    auta.spz, zamestnanci.aktivni, nepritomnost, cilova, vstup, vystup, radneukoncen
                    FROM zamestnanci
                    LEFT JOIN firmy ON zamestnanci.firma = firmy.id
                    LEFT JOIN zastavky ON zamestnanci.nastup = zastavky.id
                    LEFT JOIN auta ON zastavky.auto = auta.id
                    WHERE (vstup <= CURDATE() AND (vystup IS NULL OR vystup='' OR vystup='0000-00-00' OR vystup >= CURDATE()))
                    AND zamestnanci.firma >= 0
                    ORDER BY prijmeni, zamestnanci.smena";
        } 
        else 
        {
            $sql = "SELECT zamestnanci.id, prijmeni, jmeno, os_cislo, os_cislo_klient, rfid, telefon,
                    adresa, firmy.firma, zastavky.zastavka, zamestnanci.smena, zamestnanci.smena2,
                    auta.spz, zamestnanci.aktivni, nepritomnost, cilova, vstup, vystup, radneukoncen
                    FROM zamestnanci
                    LEFT JOIN firmy ON zamestnanci.firma = firmy.id
                    LEFT JOIN zastavky ON zamestnanci.nastup = zastavky.id
                    LEFT JOIN auta ON zastavky.auto = auta.id
                    WHERE zamestnanci.firma IN ({$_SESSION["firma"]}) 
                    AND (vstup <= CURDATE() AND (vystup IS NULL OR vystup='' OR vystup='0000-00-00' OR vystup >= CURDATE()))
                    ORDER BY prijmeni, zamestnanci.smena";
        }

        //echo $sql;

        if (!($vysl = mysqli_query($conn, $sql))) {
            die("Nelze provést dotaz");
        }

        while ($radek = mysqli_fetch_assoc($vysl)) {

            $dochazka = zjisti_dochazku_agenturnika($radek["id"]);
            $barva = "";

            // -----------------------------
            // LOGIKA BARVY
            // -----------------------------
            if ($radek["aktivni"] != "1") {
                $barva = "table-danger";
            }
            else {
                if (strpos($radek["zastavka"] ?? "", "Vlastní auto") !== false) {
                    $barva = "table-dark opacity-50";
                } elseif ($dochazka != "") {
                    $barva = "table-success";
                }

                if ($radek["nepritomnost"] == "ABS" || $radek["nepritomnost"] == "DPN") {
                    $barva = "table-secondary";
                } elseif ($radek["nepritomnost"] == "DOV") {
                    $barva = "table-warning";
                }
            }
            ?>

            <tr class="<?= $barva ?>">
                <td class="fw-bold"><?= $cislo ?></td>
                <td><?= $radek["prijmeni"] ?></td>
                <td><?= $radek["jmeno"] ?></td>
                <td><?= $radek["os_cislo"] ?></td>
                <td><?= $radek["os_cislo_klient"] ?></td>
                <td><?= $radek["rfid"] ?></td>
                <td><?= $radek["telefon"] ?></td>
                <td><?= prevod_data($radek["vstup"],1) ?></td>
                <td><?= prevod_data($radek["vystup"],1) ?></td>
                <td></td>
                <td></td>
                <td><?= $radek["smena"] . " / " . ($radek["smena2"] ?: "-") ?></td>
                <td><?= $radek["cilova"] ?></td>
                <td><?= $dochazka ?></td>

                <?php if ($editRole): ?>
                <td>
                    <button type="button" class="btn p-0 border-0 bg-transparent"
                            onclick="loadModalContent('<?= $radek['id'] ?>')">
                        <i class="bi bi-pencil-square fs-5"></i>
                    </button>
                </td>
                <?php endif; ?>
            </tr>

            <?php
            $cislo++;
        }

        mysqli_free_result($vysl);
        ?>
        </tbody>
        </table>
        </div>

        <?php novy_zamestnanec_modal();


    }
       
}
else
{
    ?>

    <h1 class="text-center m-2 p-2">NEAUTORIZOVANÝ PŘÍSTUP</h1>
    <meta http-equiv="refresh" content="5;url=login.php">

    <?php
}
?>         
   
</div>

<!-- jQuery (musí být první) -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<!-- Popper a Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js"></script>

<!-- Bootstrap-datepicker JS + český locale (verze 1.9.0) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.cs.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.4.0/js/dataTables.fixedHeader.min.js"></script>

<!-- DataTables Buttons plugin -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>

<script>

$(document).ready(function() {
    var table = $('#zamestnanciTable').DataTable({
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Vše"]],
        order: [[1, "asc"]],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/cs.json"
        },
        orderCellsTop: true,   // filtr nahoře
        fixedHeader: {
        header: true,
            headerOffset: 60  // stejná hodnota jako výška menu
        },
        dom: "<'row mb-2'<'col-auto'B><'col-auto'l><'col'f><'col'p>>" +  // horní řádek: tlačítka, select, filtr
             "<'row'<'col'tr>>" +                                  // tabulka
             "<'row mt-2'<'col'i><'col'p>>",                       // spodní řádek: info + stránkování
        buttons: [
            { extend: 'excelHtml5', text: 'Excel' },
            { extend: 'pdfHtml5', text: 'PDF', orientation: 'landscape', pageSize: 'A4' },
            { extend: 'print', text: 'Tisk' },
            { 
                text: 'Nový záznam', 
                className: 'btn btn-success btn-sm', 
                action: function () { $('#ModalNovyZam').modal('show'); } 
            },
            { extend: 'colvis', text: 'Sloupce' },  // ← tady je ColVis
            {
                text: 'Reset filtrů',
                action: function ( e, dt, node, config ) {
                    // Vymažeme všechna input pole ve druhém řádku thead
                    $('#zamestnanciTable thead tr:eq(1) th input').val('');
                    // Reset vyhledávání v DataTables a redraw
                    dt.columns().search('').draw();
                }
            }
        ]
    });

    // filtr pro každý sloupec
    $('#zamestnanciTable thead tr:eq(1) th').each(function(i) {
        $('input', this).on('keyup change', function() {
            if (table.column(i).search() !== this.value) {
                table.column(i).search(this.value).draw();
            }
        });
    });
});

$(document).ready(function() {
    var table = $('#dalsiTable').DataTable({
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Vše"]],
        order: [[1, "asc"]],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/cs.json"
        },
        orderCellsTop: true,
        fixedHeader: {
        header: true,
            headerOffset: 60  // stejná hodnota jako výška menu
        },
        dom: "<'row mb-2'<'col-auto'B><'col-auto'l><'col'f><'col'p>>" +  // horní řádek: tlačítka, select, filtr
             "<'row'<'col'tr>>" +                                  // tabulka
             "<'row mt-2'<'col'i><'col'p>>",                       // spodní řádek: info + stránkování
        buttons: [
            { extend: 'excelHtml5', text: 'Excel' },
            { extend: 'pdfHtml5', text: 'PDF', orientation: 'landscape', pageSize: 'A4' },
            { extend: 'print', text: 'Tisk' },
            { 
                text: 'Nový záznam', 
                className: 'btn btn-success btn-sm', 
                action: function () { $('#ModalNovyZam').modal('show'); } 
            },
            { extend: 'colvis', text: 'Sloupce' },  // ← tady je ColVis
            {
                text: 'Reset filtrů',
                action: function ( e, dt, node, config ) {
                    // Vymažeme všechna input pole ve druhém řádku thead
                    $('#dalsiTable thead tr:eq(1) th input').val('');
                    // Reset vyhledávání v DataTables a redraw
                    dt.columns().search('').draw();
                }
            }
        ]
    });

    $('#dalsiTable thead tr:eq(1) th').each(function(i) {
        $('input', this).on('keyup change', function() {
            if (table.column(i).search() !== this.value) {
                table.column(i).search(this.value).draw();
            }
        });
    });
});

</script>

<script>
$(document).ready(function() {
    $('#datepicker, #datepicker2, #datepicker3').datepicker({
        format: 'dd.mm.yyyy',
        autoclose: true,
        todayHighlight: true,
        language: 'cs',
        startDate: '01.01.2024',
        endDate: '31.12.2099'
    });
});
</script>


<script>

function loadModalContent(modalId) {
    var functionName = 'edit_zamestnanec_modal'; // Název funkce, kterou chcete volat
    var ID = modalId; // Získání ID modálního okna z modalId

    $.ajax({
        url: 'funkce.php', // Cesta k externímu skriptu
        type: 'GET',
        data: { functionName: functionName, ID: ID }, // Předání funkce a ID
        success: function(response) {
            // ✅ debug: vypíše, co AJAX vrátil
            console.log('AJAX loaded content:', response);

            // vloží obsah do modalu
            $('#modalContent').html(response);

            // najde všechny inputy s class "datepicker" v modalu
            let inputs = $('#modalContent').find('input.datepicker');
            console.log('Inputs found:', inputs.length); // kolik jich našlo

            // inicializuje datepicker jen na tyto inputy
            inputs.datepicker({
                format: 'dd.mm.yyyy',
                autoclose: true,
                todayHighlight: true,
                language: 'cs'
            });

            // zobrazí modal
            $('#ModalEditZam').modal('show');
        },
        error: function(xhr, status, error) {
            console.error(error);
        }
    });
}

</script>

<div id="modalContent"></div>