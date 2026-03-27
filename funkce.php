<?php
//error_reporting(E_ALL);             // Hlásí všechny chyby a upozornění
//ini_set('display_errors', 1);       // Zobrazí chyby na obrazovku
//ini_set('display_startup_errors', 1); // Zobrazí chyby při startu PHP
require_once 'init.php';
require_once 'db.php';
//require_once __DIR__ . '/init.php';

if (isset($_GET['functionName'])) {
    // Získat název funkce a ID z GET požadavku
    $functionName = $_GET['functionName'];
    $id = $_GET['ID'];

    // Zkontrolovat, zda existuje požadovaná funkce
    if (function_exists($functionName)) {
        // Spustit požadovanou funkci s předaným ID
        $result = call_user_func($functionName, $id);
        echo $result; // Vrátit výsledek jako odpověď na AJAX požadavek
    } else {
        // Pokud požadovaná funkce neexistuje, vrátit chybovou zprávu
        echo "Požadovaná funkce neexistuje.";
    }
} 
else 
{
    // Pokud nebyla předána funkce a ID, vrátit chybovou zprávu
    //echo "Chybí požadovaná data 1.";
}

if (isset($_GET['functionName2'])) {
  // Získat název funkce a ID z GET požadavku
  //$functionName = $_GET['functionName2'];
  $id = $_GET['ID'];
  $id2 = $_GET['ID2'];

  //echo $id;
  //echo $functionName;

  // Zkontrolovat, zda existuje požadovaná funkce
  if (function_exists($_GET['functionName2'])) {
      // Spustit požadovanou funkci s předaným ID
      $result = call_user_func($_GET['functionName2'], $id, $id2);
      echo $result; // Vrátit výsledek jako odpověď na AJAX požadavek
  } else {
      // Pokud požadovaná funkce neexistuje, vrátit chybovou zprávu
      //echo "Požadovaná funkce neexistuje.";
  }
} 
else 
{
  // Pokud nebyla předána funkce a ID, vrátit chybovou zprávu
  //echo "Chybí požadovaná data 1.";
}

if (isset($_GET['functionName3'])) {
    // Získat název funkce a ID z GET požadavku
    $functionName = $_GET['functionName3'];
    $id = $_GET['ID'];
    $id2 = $_GET['ID2'];
    $id3 = $_GET['ID3'];

    // Zkontrolovat, zda existuje požadovaná funkce
    if (function_exists($functionName)) {
        // Spustit požadovanou funkci s předaným ID
        $result = call_user_func($functionName, $id, $id2, $id3);
        echo $result; // Vrátit výsledek jako odpověď na AJAX požadavek
    } else {
        // Pokud požadovaná funkce neexistuje, vrátit chybovou zprávu
        echo "Požadovaná funkce neexistuje.";
    }
} 
else 
{
    // Pokud nebyla předána funkce a ID, vrátit chybovou zprávu
    //echo "Chybí požadovaná data 2.";
}

function kontrola_prihlaseni()
{
    if (isset($_SESSION["logged"]))
    {
        return "OK";    
    }
    else
    {
        return "NOK";
    }
}

function get_info_bus($id)
{
    global $conn;

    $sql = "select spz,oznaceni from auta where id='" . $id . "';";

    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            

    while ($radek = mysqli_fetch_array($vysledek))
    {  
        $text = $radek["spz"] . "<br>" . $radek["oznaceni"];
    }
    
    mysqli_free_result($vysledek);

    return $text;
}

function menu()
{
    $logged = (($_SESSION["logged"] ?? null) === "ANO");
    $typ    = $_SESSION["typ"] ?? null;
    ?>
    <nav class="navbar navbar-expand-lg bg-light fixed-top d-print-none">
        <div class="container-fluid">

            <a class="navbar-brand ms-2">
                <img src="img/logo.png" width="122" height="36" alt="LOGO RECRA" loading="lazy">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNav" aria-controls="navbarNav"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">

                    <!-- DOMŮ -->
                    <li class="nav-item">
                        <a class="nav-link active" href="main.php">
                            <i class="bi bi-house me-1"></i>Domů
                        </a>
                    </li>

                    <?php if ($logged) : ?>

                        <!-- PROVOZ -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="dropdownProvoz"
                               role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-gear me-1"></i>Provoz
                            </a>

                            <ul class="dropdown-menu" aria-labelledby="dropdownProvoz">
                                <li>
                                    <a class="dropdown-item" href="zamestnanci.php">
                                        <i class="bi bi-person-lines-fill me-2"></i>Přehled zaměstnanců
                                    </a>
                                </li>

                                <?php if (in_array($typ, ["1","2","3","5","6"], true)) : ?>
                                    <li>
                                        <a class="dropdown-item" href="firmy.php">
                                            <i class="bi bi-building me-2"></i>Přehled firem (směny)
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <li>
                                    <a class="dropdown-item" href="dochazka.php">
                                        <i class="bi bi-calendar-check me-2"></i>Docházka
                                    </a>
                                </li>

                                <?php if (in_array($typ, ["1","5","7"], true)) : ?>
                                    <li>
                                        <a class="dropdown-item" href="report4.php">
                                            <i class="bi bi-table me-2"></i>Report docházek
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="report5.php">
                                            <i class="bi bi-calendar3 me-2"></i>Nastavení kalendářů
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </li>

                        <!-- NÁBOR -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="dropdownNabor"
                               role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-plus me-1"></i>Nábor
                            </a>

                            <ul class="dropdown-menu" aria-labelledby="dropdownNabor">
                                <?php if (in_array($typ, ["1","5","6","7"], true)) : ?>
                                    <li>
                                        <a class="dropdown-item" href="nabory.php">
                                            <i class="bi bi-card-list me-2"></i>Evidence uchazečů CZ / PL
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="informace.php">
                                            <i class="bi bi-info-circle me-2"></i>Informace k nástupům
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </li>

                        <!-- DOPRAVA -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="dropdownDoprava"
                               role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-truck me-1"></i>Doprava
                            </a>

                            <ul class="dropdown-menu" aria-labelledby="dropdownDoprava">
                                <?php if (in_array($typ, ["1","5"], true)) : ?>
                                    <li>
                                        <a class="dropdown-item" href="vozovypark.php">
                                            <i class="bi bi-car-front me-2"></i>Vozový park
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (in_array($typ, ["1","2","3","5"], true)) : ?>
                                    <li>
                                        <a class="dropdown-item" href="report2.php">
                                            <i class="bi bi-graph-up me-2"></i>Report dopravy
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </li>

                        <!-- REPORTY -->
                        <?php if ($typ === "5") : ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="dropdownReporty"
                                   role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-bar-chart me-1"></i>Reporty
                                </a>

                                <ul class="dropdown-menu" aria-labelledby="dropdownReporty">
                                    <li>
                                        <a class="dropdown-item" href="report3.php">
                                            <i class="bi bi-clipboard-data me-2"></i>Report pro vedení
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="logs.php">
                                            <i class="bi bi-journal-text me-2"></i>Logy
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <!-- ZAMĚSTNANCI -->
                        <?php if ($typ === "5") : ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                    <i class="bi bi-people-fill me-1"></i>Zaměstnanci
                                </a>

                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="sazby.php">
                                            <i class="bi bi-cash-stack me-2"></i>Hodinové sazby
                                        </a>
                                    </li>

                                    <li>
                                        <a class="dropdown-item" href="xml.php">
                                            <i class="bi bi-cash-stack me-2"></i>XML převody
                                        </a>
                                    </li>
                                </ul>

                            </li>
                        <?php endif; ?>

                        <!-- UŽIVATELÉ -->
                        <?php if (in_array($typ, ["1","4","5"], true)) : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="uzivatele.php">
                                    <i class="bi bi-people me-1"></i>Uživatelé
                                </a>
                            </li>
                        <?php endif; ?>

                        <!-- ODHLAŠENÍ -->
                        <li class="nav-item">
                            <a class="nav-link" href="odhlasit.php">
                                <i class="bi bi-box-arrow-right me-1"></i>Odhlásit
                            </a>
                        </li>

                    <?php endif; ?>

                </ul>
            </div>

            <?php if ($logged) : ?>
                <span class="nav-link d-print-none">
                    <i class="bi bi-person-circle me-1"></i>
                    <?= htmlspecialchars($_SESSION['log_name'] ?? '') ?>
                    / <?= htmlspecialchars($_SESSION['typ_uctu'] ?? '') ?>
                </span>
            <?php endif; ?>

        </div>
    </nav>

    <br><br>
    <?php
}

function vytvor_tlacitka_pro_smeny($firma, $uzivatel, $autobus)
{
    if ($_SESSION["autobus"] == "") { ?>
        <span class='text-center'>
            <button class="btn btn-outline-primary btn-sm m-2" onclick="loadModalDoprava()">
                <i class="bi bi-truck"></i> Není vybrána doprava, kliknutím vyberte
            </button>
        </span>
    <?php
    } else { ?>
        <a class="btn btn-primary btn-lg m-1" href="main.php?typ=dochazka&firma=<?php echo $firma;?>&bus=<?php echo $autobus;?>&smena=R">
            <i class="bi bi-sunrise"></i> R
        </a>
        <a class="btn btn-primary btn-lg m-1" href="main.php?typ=dochazka&firma=<?php echo $firma;?>&bus=<?php echo $autobus;?>&smena=O">
            <i class="bi bi-sun"></i> O
        </a>
        <a class="btn btn-primary btn-lg m-1" href="main.php?typ=dochazka&firma=<?php echo $firma;?>&bus=<?php echo $autobus;?>&smena=N">
            <i class="bi bi-moon"></i> N
        </a>

        <a class="btn btn-warning btn-lg m-1" href="main.php?typ=dpn">
            DPN <span class="badge text-bg-success"><?php echo pocet_kontrol_dpn_user($uzivatel); ?></span>
        </a>
    <?php
    }
}


function get_firma_from_id($firma_id)
{
    global $conn;

    $sql = "select firma from firmy where id='" . $firma_id . "'";

    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            

    while ($radek = mysqli_fetch_array($vysledek))
    {  
        $text = $radek["firma"];
    }
    
    mysqli_free_result($vysledek);
    
    return $text;

}

function get_objednavka($firma_id)
{
    global $conn;

    $sql = "select objednavka from firmy where id='" . $firma_id . "'";

    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            

    while ($radek = mysqli_fetch_array($vysledek))
    {  
        $text = $radek["objednavka"];
    }
    
    mysqli_free_result($vysledek);
    
    return $text;

}

function get_spz_from_id($bus_id)
{
    global $conn;

    $sql = "select spz from auta where id='" . $bus_id . "'";
    
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            

    while ($radek = mysqli_fetch_array($vysledek))
    {  
        $text = $radek["spz"];
    }
    
    mysqli_free_result($vysledek);
    
    return $text;

}

function zjisti_pocet_nastupujicich($firma, $smena, $zastavka)
{
    global $conn;

    // Bezpečné zpracování vstupů
    $firma = trim($firma);
    $smena = trim($smena);
    $zastavka = trim($zastavka);

    // Dynamicky aktuální rok a týden
    $rok = date('Y');                 // aktuální rok
    $tyden = date('W');               // aktuální týden ISO-8601

    // SQL dotaz
    $sql = "
        SELECT COUNT(*) AS pocet
        FROM plan_smen
        LEFT JOIN zamestnanci ON plan_smen.jmeno = zamestnanci.id
        WHERE plan_smen.smena IN (?, 'X')
          AND plan_smen.rok = ?
          AND plan_smen.tyden = ?
          AND zamestnanci.nastup = ?
          AND zamestnanci.firma = ?
          AND (zamestnanci.nepritomnost = '' OR zamestnanci.nepritomnost IS NULL)
          AND DATE(zamestnanci.vstup) <= CURDATE()
          AND (zamestnanci.vystup = '0000-00-00' OR DATE(zamestnanci.vystup) >= CURDATE())
    ";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("DB prepare error: " . $conn->error);
        return 0;
    }

    // Parametry: smena, rok, tyden, nastup, firma
    $stmt->bind_param("siiii", $smena, $rok, $tyden, $zastavka, $firma);

    if (!$stmt->execute()) {
        error_log("DB execute error: " . $stmt->error);
        $stmt->close();
        return 0;
    }

    $res = $stmt->get_result();
    $pocet = 0;
    if ($row = $res->fetch_assoc()) {
        $pocet = (int)$row['pocet'];
    }

    $res->free();
    $stmt->close();

    return $pocet;
}

function zjisti_pocet_autobusu($firma, $smena, $zastavka)
{
    global $conn;

    // Ošetření / konverze vstupů
    $firma = intval($firma);
    $zastavka = intval($zastavka);
    $smena = trim((string)$smena);

    $sql = "SELECT COUNT(*) AS pocet
            FROM dochazka
            WHERE firma = ?
              AND zastavka = ?
              AND smena IN (?, 'X')
              AND NOW() <= DATE_ADD(CONCAT(datum, ' ', cas), INTERVAL 3 HOUR)";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("DB prepare error: " . $conn->error);
        return 0;
    }

    // parametry: firma (i), zastavka (i), smena (s)
    if (!$stmt->bind_param("iis", $firma, $zastavka, $smena)) {
        error_log("DB bind_param error: " . $stmt->error);
        $stmt->close();
        return 0;
    }

    if (!$stmt->execute()) {
        error_log("DB execute error: " . $stmt->error);
        $stmt->close();
        return 0;
    }

    $res = $stmt->get_result();
    $pocet = 0;
    if ($row = $res->fetch_assoc()) {
        $pocet = (int)$row['pocet'];
    }

    $res->free();
    $stmt->close();

    return $pocet;
}

function get_name_from_rfid($rfid, $firma)
{
    global $conn;

    // Ošetření: RFID musí mít alespoň 4 znaky
    $rfid = trim($rfid);
    if (strlen($rfid) < 3) {
        return "neznámý kód";
    }

    $text = "neznámý kód";

    $sql = "SELECT id, os_cislo, jmeno, prijmeni, rfid 
        FROM zamestnanci 
        WHERE (UPPER(rfid) = UPPER(?) OR os_cislo = ?)
            AND DATE(vstup) <= CURDATE()
            AND (vystup = '0000-00-00' OR DATE(vystup) >= CURDATE())
            AND firma = ?
        LIMIT 1";

    if ($stmt = $conn->prepare($sql)) {
        //$stmt->bind_param("ss", $rfid, $firma);
        $stmt->bind_param("ssi", $rfid, $rfid, $firma);

        if ($stmt->execute()) {
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) {
                $text = $row["os_cislo"] . "," . $row["jmeno"] . "," . $row["prijmeni"] . "," . $row["rfid"] . "," . $row["id"];
            }
            $res->free();
        } else {
            error_log("DB execute error: " . $stmt->error);
        }

        $stmt->close();
    } else {
        error_log("DB prepare error: " . $conn->error);
    }

    return $text;
}


function get_name_from_personal_number($personal_number,$firma)
{
    global $conn;
    $text = "neznámý kód";

    $sql = "select id,os_cislo,jmeno,prijmeni,rfid from zamestnanci where os_cislo='" . $personal_number . "' and aktivni='1' and firma='" . $firma . "' limit 1";
    
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            

    while ($radek = mysqli_fetch_array($vysledek))
    {  
        $text = $radek["os_cislo"] . "," . $radek["jmeno"] . "," . $radek["prijmeni"] . "," . $radek["rfid"] . "," . $radek["id"];
    }
    
    mysqli_free_result($vysledek);
        
    return $text;

}

function get_name_from_personal_number2($personal_number)
{
    global $conn;
    $text = "neznámý kód";

    $sql = "select id,os_cislo,jmeno,prijmeni,rfid,smena from zamestnanci where os_cislo='" . $personal_number . "' and aktivni='1' limit 1";
    
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            

    while ($radek = mysqli_fetch_array($vysledek))
    {  
        $text = $radek["os_cislo"] . "," . $radek["jmeno"] . "," . $radek["prijmeni"] . "," . $radek["rfid"] . "," . $radek["smena"];
    }
    
    mysqli_free_result($vysledek);
        
    return $text;

}

function get_only_name_from_personal_number($personal_number)
{
    global $conn;
    $text = "neznámý kód";

    $sql = "select id,os_cislo,jmeno,prijmeni,rfid,smena from zamestnanci where os_cislo='" . $personal_number . "' and aktivni='1' limit 1";
    
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            

    while ($radek = mysqli_fetch_array($vysledek))
    {  
        $text = $radek["prijmeni"] . " " . $radek["jmeno"];
    }
    
    mysqli_free_result($vysledek);
        
    return $text;

}

function get_name_from_id_zam($id_zam)
{
    global $conn;
    $text = "";

    $sql = "select id,os_cislo,jmeno,prijmeni,rfid,smena from zamestnanci where id='" . $id_zam . "' limit 1";
    
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            

    while ($radek = mysqli_fetch_array($vysledek))
    {  
        $text = $radek["prijmeni"] . " " . $radek["jmeno"];
    }
    
    mysqli_free_result($vysledek);
        
    return $text;

}

function get_shift_from_id_zam($id_zam)
{
    global $conn;
    $text = "";

    $sql = "select id,os_cislo,jmeno,prijmeni,rfid,smena from zamestnanci where id='" . $id_zam . "' and aktivni='1' limit 1";
    
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            

    while ($radek = mysqli_fetch_array($vysledek))
    {  
        $text = $radek["smena"];
    }
    
    mysqli_free_result($vysledek);
        
    return $text;

}

function get_zastavka_from_id($id_zastavky)
{
    global $conn;
    $text = "";

    if (($_GET["smena"] == "R") or ($_GET["smena"] == "S-R") or ($_GET["smena"] == "N-R"))
    {
        $cas = "cas1";
    }
    elseif (($_GET["smena"] == "O") or ($_GET["smena"] == "S-O") or ($_GET["smena"] == "N-O"))
    {
        $cas = "cas2";
    }
    elseif (($_GET["smena"] == "N") or ($_GET["smena"] == "S-N") or ($_GET["smena"] == "N-N"))
    {
        $cas = "cas3";
    }
    elseif ($_GET["smena"] == "NN")
    {
        $cas = "cas4";
    }
    elseif ($_GET["smena"] == "NR")
    {
        $cas = "cas5";
    }
    elseif ($_GET["smena"] == "VK")
    {
        $cas = "cas6";
    }
    elseif ($_GET["smena"] == "PR")
    {
        $cas = "cas7";
    }
    else
    {
        $cas = "cas1";
    }

    $sql = "select zastavka," . $cas . " as cas from zastavky where id='" .$id_zastavky . "'";
    
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            

    while ($radek = mysqli_fetch_array($vysledek))
    {  
        $text = $radek["zastavka"] . " - " . $radek["cas"];
    }
    
    mysqli_free_result($vysledek);
        
    return $text;
  
}

function get_zastavka_from_id2($id_zastavky)
{
    global $conn;
    $text = "";  

    $sql = "select zastavka from zastavky where id='" . $id_zastavky . "'";
    
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            

    while ($radek = mysqli_fetch_array($vysledek))
    {  
        $text = $radek["zastavka"];
    }
    
    mysqli_free_result($vysledek);
        
    return $text;
  
}

function get_zastavka_from_id3($id_zastavky, $auto, $smena = "R")
{
    global $conn;
    $text = "";

    // Určení sloupce podle směny
    if ($smena == "R") {
        $cas_sloupec = "R";
    } elseif ($smena == "O") {
        $cas_sloupec = "O";
    } elseif ($smena == "N") {
        $cas_sloupec = "N";
    } else {
        $cas_sloupec = "R"; // výchozí
    }

    $sql = "SELECT nastupy.zastavka, trasy.$cas_sloupec AS cas
            FROM trasy
            LEFT JOIN nastupy ON trasy.zastavka = nastupy.id
            WHERE trasy.auto = ? AND trasy.zastavka = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $auto, $id_zastavky);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $text = ($row["zastavka"] ?? 'NULL') . " - " . ($row["cas"] ?? 'NULL');
        }

        $stmt->close();
    } else {
        die("Chyba prepare: " . $conn->error);
    }

    return $text;
}


function get_bus_from_zastavky($id_zastavky)
{
    global $conn;
    $text = "";  

    $sql = "select auto from zastavky where id='" . $id_zastavky . "'";
    
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            

    while ($radek = mysqli_fetch_array($vysledek))
    {  
        $text = $radek["auto"];
    }
    
    mysqli_free_result($vysledek);
        
    return $text;
  
}

function insert_attandance($id_emp,$bus,$zastavka,$firma,$smena,$cron,$nepritomnost) 
{
    global $conn;

    $id_last = 0;
    $date_last = '';

    // 1) poslední záznam pro dnešek (bus+smena+firma)
    $sql = "
        SELECT zamestnanec, DATE(datum) AS datum_den
        FROM dochazka
        WHERE bus = ?
          AND smena = ?
          AND firma = ?
          AND DATE(datum) = CURDATE()
        ORDER BY id DESC
        LIMIT 1
    ";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die("Nelze připravit dotaz: " . mysqli_error($conn) . "</body></html>");
    }

    mysqli_stmt_bind_param($stmt, "sss", $bus, $smena, $firma);
    mysqli_stmt_execute($stmt);

    $res = mysqli_stmt_get_result($stmt);
    if ($res && ($radek = mysqli_fetch_assoc($res))) {
        $id_last   = (int)$radek["zamestnanec"];
        $date_last = $radek["datum_den"];
    }
    mysqli_stmt_close($stmt);

    // 2) blokace duplicity ve stejný den hned za sebou
    if ((int)$id_emp === $id_last && $date_last === date('Y-m-d')) {
        ?>
        <div class="container">
            <h5 class='text-center text-danger m-2 p-2 bg-danger text-dark bg-opacity-50'>
                NELZE NAČÍST DVAKRÁT STEJNÝ ZÁZNAM VE STEJNÝ DEN HNED ZA SEBOU !
            </h5>
        </div>
        <?php
        return;
    }

    // 3) insert
    $dotaz = "
        INSERT INTO dochazka
            (zamestnanec, datum, cas, smena, bus, firma, zastavka, ip, cron, nepritomnost)
        VALUES
            (?, NOW(), NOW(), ?, ?, ?, ?, ?, ?, ?)
    ";

    $stmt2 = mysqli_prepare($conn, $dotaz);
    if (!$stmt2) {
        die("Nelze připravit INSERT: " . mysqli_error($conn) . "</body></html>");
    }

    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $id_emp = (int)$id_emp;

    mysqli_stmt_bind_param($stmt2, "isssssss",
        $id_emp, $smena, $bus, $firma, $zastavka, $ip, $cron, $nepritomnost
    );

    if (!mysqli_stmt_execute($stmt2)) {
        die("Nelze provést dotaz: " . mysqli_error($conn) . "</body></html>");
    }

    mysqli_stmt_close($stmt2);
}

function insert_attandance_manually($id_emp, $bus, $zastavka, $firma, $smena, $datum, $cas, $nepritomnost, $poznamka, $cron = 2)
{
    global $conn;

    $id_last = 0;

    // ----------------------------
    // 1) Zjištění posledního ID
    // ----------------------------
    $sql = "SELECT zamestnanec 
            FROM dochazka 
            WHERE bus = ? 
              AND smena = ? 
              AND firma = ? 
              AND datum = ?
            ORDER BY id DESC 
            LIMIT 1";

    if ($stmt = $conn->prepare($sql)) {

        $stmt->bind_param("isis", $bus, $smena, $firma, $datum);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $id_last = $row["zamestnanec"];
        }

        $stmt->close();
    }

    // Pokud je stejný zaměstnanec → chyba
    if ($id_emp == $id_last) {
        ?>
        <div class="container">
            <h5 class='text-center text-danger m-2 p-2 bg-danger p-2 text-dark bg-opacity-50'>
                NELZE NAČÍST DVAKRÁT STEJNÝ ZÁZNAM HNED ZA SEBOU !
            </h5>
        </div>
        <?php
        return;
    }

    // ----------------------------
    // 2) INSERT do docházky
    // ----------------------------
    $insert = "INSERT INTO dochazka 
               (zamestnanec, datum, cas, smena, bus, firma, zastavka, ip, cron, nepritomnost, poznamka)
               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt2 = $conn->prepare($insert)) 
    {

        $ip = $_SERVER['REMOTE_ADDR'];

        $stmt2->bind_param(
            "isssssssiss",
            $id_emp,
            $datum,
            $cas,
            $smena,
            $bus,
            $firma,
            $zastavka,
            $ip,
            $cron,
            $nepritomnost,
            $poznamka
        );

        $stmt2->execute();
        $stmt2->close();
    }
}


function insert_attandance_manually_aaa($id_emp,$bus,$zastavka,$firma,$smena,$datum,$cas,$nepritomnost,$poznamka) 
{
  
    global $conn;
    $id_last = 0;
    $vysledek = 0;
  
    $sql = "select zamestnanec from dochazka where bus='" . $bus . "' and smena='" . $smena . "' and firma='" . $firma . "' and datum='" . $datum . "' order by id desc limit 1";
    
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }    
    
    while ($radek = mysqli_fetch_array($vysledek))
    {   
      $id_last = $radek["zamestnanec"];
    }

    //echo $sql;
  
    mysqli_free_result($vysledek);
  
    if ($id_emp == $id_last)
    {   ?>
            <div class="container">
                <h5 class='text-center text-danger m-2 p-2 bg-danger p-2 text-dark bg-opacity-50'>NELZE NAČÍST DVAKRÁT STEJNÝ ZÁZNAM HNED ZA SEBOU !</h5>
            </div>
      <?php
    }
    else
    {
      $dotaz="insert into dochazka (zamestnanec,datum,cas,smena,bus,firma,zastavka,ip,cron,nepritomnost,poznamka) values ('" . $id_emp . "','" . $datum . "','" . $cas . "','" . $smena . "','" . $bus . "','" . $firma . "','" . $zastavka . "','" . $_SERVER['REMOTE_ADDR'] . "','2','" . $nepritomnost . "','" . $poznamka . "')"; 
            
      if (!($vysledek = mysqli_query($conn, $dotaz)))
      {
      die("Nelze provést dotaz.</body></html>");
      }
    }
}

function vyrob_modal_k_nastupnimu_mistu($firma,$nastup,$smena,$bus)
{ ?>

  <!-- Modal -->
  <div class="modal fade" id="nastup<?php echo $nastup;?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">
        
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel"><?php echo get_zastavka_from_id3($nastup,$bus,$smena);?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
              
        <table class="table">
          <thead>
            <tr>
              <th scope="col" class="text-center">Osobní číslo</th>
              <th scope="col">Příjmení</th>
              <th scope="col">Jméno</th>
              <th scope="col" class="text-center">Nastoupil</th>
              <th scope="col" class="text-center">Datum a Čas</th>
              <th scope="col" class="text-center">Nástup</th>
              
            </tr>
          </thead>

              <?php

               global $conn;

                // Dynamicky aktuální rok a týden
                $rok = date('Y');
                $tyden = date('W');

                $sql = "
                    SELECT 
                        zamestnanci.id,
                        zamestnanci.os_cislo,
                        zamestnanci.prijmeni,
                        zamestnanci.jmeno,
                        zamestnanci.rfid,
                        zamestnanci.nastup,
                        zamestnanci.telefon,
                        zamestnanci.adresa,
                        zamestnanci.firma,
                        plan_smen.smena,
                        zamestnanci.nepritomnost
                    FROM plan_smen
                    LEFT JOIN zamestnanci ON plan_smen.jmeno = zamestnanci.id
                    WHERE plan_smen.smena IN (?, 'X')
                    AND plan_smen.rok = ?
                    AND plan_smen.tyden = ?
                    AND zamestnanci.nastup = ?
                    AND zamestnanci.firma = ?
                    AND (zamestnanci.nepritomnost = '' OR zamestnanci.nepritomnost IS NULL)
                    AND DATE(zamestnanci.vstup) <= CURDATE()
                    AND (zamestnanci.vystup = '0000-00-00' OR DATE(zamestnanci.vystup) >= CURDATE())";

                // Připravený statement pro bezpečné parametry
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("siiii", $smena, $rok, $tyden, $nastup, $firma);
                $stmt->execute();
                $vysledek = $stmt->get_result();

                while ($radek = mysqli_fetch_array($vysledek))
                {                    
                    //$pole = explode (";",zjisti_cas_nastupu($radek["id"],$radek["smena"],$radek["nastup"]));
                    $pole = explode (";",zjisti_cas_nastupu($radek["id"],$smena,$radek["nastup"]));        

                    if ($pole[0] == "ANO")
                    {
                        $barva = "table-success bg-opacity-25";
                    }
                    else
                    {
                        if ($radek["nepritomnost"] <> "")
                        {
                            $barva = "table-primary bg-opacity-25";
                        }
                        else
                        {
                            $barva = "table-danger bg-opacity-25";
                        }
                    }
                    ?>   

                    <tr class='<?php echo $barva;?>'>
                    
                    <td scope="col" class="text-center"><?php echo $radek["os_cislo"];?></td>
                    <td scope="col" class="fs-5 fw-bold"><?php echo $radek["prijmeni"];?></td>
                    <td scope="col"class="fs-5 fw-bold"><?php echo $radek["jmeno"];?></td>                   
                    <td scope="col" class="text-center"><?php echo $pole[0];?></td>
                    <td scope="col" class="text-center"><?php echo $pole[1] . " " . $pole[2];?></td>                                  

                    <td class="text-center">
                        <?php if ($pole[0] != 'ANO' && $_GET['zastavka'] == $radek['nastup']) : ?>
                            <form id="form_<?php echo $radek['id']; ?>" method="POST" action="main.php?typ=dochazka&firma=<?php echo urlencode($_GET['firma']); ?>&bus=<?php echo urlencode($_GET['bus']); ?>&smena=<?php echo urlencode($_GET['smena']); ?>&zastavka=<?php echo urlencode($_GET['zastavka']); ?>">
                                <input type="hidden" name="barcode" value="<?php echo htmlspecialchars($radek['rfid'] ?? ''); ?>">
                                <button type="submit" class="btn btn-primary">Nastoupil</button>
                            </form>
                        <?php endif; ?>
                    </td>

                    </tr>
                    <?php
                }

                mysqli_free_result($vysledek);

              ?>
         
        </table>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavřít</button>
        </div>

      </div>
    </div>
  </div>
  
  <?php
}

function zjisti_cas_nastupu($id_cloveka,$smena,$zastavka)
{
    global $conn;
  
    $text = "NE;;";
    $sql = "select datum,cas from dochazka where zamestnanec = '" . $id_cloveka . "' and zastavka='" . $zastavka . "' and smena='" . $smena . "' and now() <= DATE_ADD(concat(datum,' ', cas), INTERVAL 3 HOUR)";

    //echo $sql;

    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            

    while ($radek = mysqli_fetch_array($vysledek))
    {  
        $text = "ANO;" . $radek["datum"] . ";" . $radek["cas"];
    }
    
    mysqli_free_result($vysledek);
    
    return $text;

}

function zjisti_pocet_zamestnancu_ve_firme($firma)
{
    global $conn;
    $pocet = 0;

    $sql = "SELECT COUNT(*) AS pocet 
            FROM zamestnanci 
            WHERE firma = ? 
              AND vstup <= CURDATE() 
              AND (vystup IS NULL OR vystup = '' OR vystup = '0000-00-00' OR vystup >= CURDATE())";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $firma);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $pocet = $row["pocet"];
        }

        $stmt->close();
    } else {
        die("Nelze provést dotaz: " . $conn->error);
    }

    return $pocet;
}

function zjisti_pocet_zamestnancu_ve_firme_objednavka($firma)
{
    global $conn;
    $pocet = 0;

    $sql = "SELECT objednavka FROM firmy WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $firma); // ošetření parametru
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $pocet = $row["objednavka"];
        }

        $stmt->close();
    } else {
        die("Nelze provést dotaz: " . $conn->error);
    }

    return $pocet;
}

function zjisti_preklopeni_smen($firma)
{
    global $conn;
    $datum = "";
      
    $sql = "select zmenasmen,zmenastatus,zmenaprovedena from firmy where id = '" . $firma . "'";
   
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            

    while ($radek = mysqli_fetch_array($vysledek))
    {  

        $date=date_create($radek["zmenaprovedena"]);
        
        if ($radek["zmenastatus"] == 1)
        {
            $kec = date_format(date_create($radek["zmenasmen"]),'d.m.Y H:i');
            $datum = "<span class='text-center fw-bold text-success'>Plánované překlopení směn: " . $kec . "</span>";
        }
        else
        {
            if (date_format($date,"d.m.Y") == "30.11.-0001")
            {
                $datum = "<span class='text-center fw-bold text-danger'>Neaktivní</span>";
            }
            else
            {
                $kec = date_format(date_create($radek["zmenaprovedena"]),'d.m.Y H:i');
                $datum = "<span class='text-center fw-bold text-primary'>Překlopení směn provedeno: " . $kec . "</span>";
            }
            
        }       
    }

    //if (date_format($date,"d.m.Y") == "30.11.-0001")
    
    mysqli_free_result($vysledek);
    
    return $datum;

}

function wrapMailMessage($string, $length=980, $splitchar="\n ") {
if (strlen($string) <= $length) {
    $output = $string; //do nothing
} else {
    $output = wordwrap($string, $length, $splitchar);
}
return $output;                        

}

function zmena_smeny($os_cislo,$smena) 
{
  
    $os_cislo = preg_replace('/[\x00-\x1F\x7F]/', '', $os_cislo);
    $smena = preg_replace('/[\x00-\x1F\x7F]/', '', $smena);

    global $conn;
    
    $id = 0;
    $sql = "select id from zamestnanci where os_cislo='" . $os_cislo . "'";
   
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            

    while ($radek = mysqli_fetch_array($vysledek))
    {  
        $id = $radek["id"];
    }
    
    mysqli_free_result($vysledek);

    if ($id > 0)
    {
        if (($smena == "R") or ($smena == "O") or ($smena == "N") or ($smena == "NN") or ($smena == "NR") or ($smena == "VK") or ($smena == "PR") or ($smena == "S-R") or ($smena == "S-O") or ($smena == "S-N") or ($smena == "N-R") or ($smena == "N-O") or ($smena == "N-N"))
        {
            $dotaz="update zamestnanci set smena='" . $smena . "' where os_cislo='" . $os_cislo . "'" ; 
            
            if (!($vysledek = mysqli_query($conn, $dotaz)))
            {
            die("Nelze provést dotaz.</body></html>");
            }

            //vlozim zaznam do logu
            $now = new DateTime('now', new DateTimeZone('Europe/Prague'));

            if (!($vysledek = mysqli_query($conn, 
            "insert into logs (kdo,typ,infotext,datumcas) values ('" . $_SESSION["log_id"] . "','Změna směny','Změna směnnosti u " . get_only_name_from_personal_number($os_cislo) . " na směnu " . $smena . "','" . $now->format('Y-m-d H:i:s') . "')")))
            {
            die("Nelze provést dotaz.</body></html>");
            } 

            return "OK";
        }
        else
        {
            return "Zadána nepodporovaná směna " . $smena;
        }
    
    }
    else
    {
        return "Zadáno špatné / neznámé osobní číslo";
    }   
   
}

function zmena_smeny2($os_cislo,$smena) 
{
  
    $os_cislo = preg_replace('/[\x00-\x1F\x7F]/', '', $os_cislo);
    $smena = preg_replace('/[\x00-\x1F\x7F]/', '', $smena);

    global $conn;
    
    $id = 0;
    $sql = "select id from zamestnanci where os_cislo='" . $os_cislo . "'";
   
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            

    while ($radek = mysqli_fetch_array($vysledek))
    {  
        $id = $radek["id"];
    }
    
    mysqli_free_result($vysledek);

    if ($id > 0)
    {
        if (($smena == "R") or ($smena == "O") or ($smena == "N") or ($smena == "NN") or ($smena == "NR") or ($smena == "VK") or ($smena == "PR") or ($smena == "S-R") or ($smena == "S-O") or ($smena == "S-N") or ($smena == "N-R") or ($smena == "N-O") or ($smena == "N-N") or ($smena == "N/A"))
        {
            $dotaz="update zamestnanci set smena2='" . $smena . "' where os_cislo='" . $os_cislo . "'" ; 
            
            if (!($vysledek = mysqli_query($conn, $dotaz)))
            {
            die("Nelze provést dotaz.</body></html>");
            }

            //vlozim zaznam do logu
            $now = new DateTime('now', new DateTimeZone('Europe/Prague'));

            if (!($vysledek = mysqli_query($conn, 
            "insert into logs (kdo,typ,infotext,datumcas) values ('" . $_SESSION["log_id"] . "','Změna směny 2','Změna směnnosti u " . get_only_name_from_personal_number($os_cislo) . " na směnu " . $smena . "','" . $now->format('Y-m-d H:i:s') . "')")))
            {
            die("Nelze provést dotaz.</body></html>");
            } 

            //echo "insert into logs (kdo,typ,infotext,datumcas) values ('" . $_SESSION["log_id"] . "','Změna směny 2','Změna směnnosti u " . get_only_name_from_personal_number($os_cislo) . " na směnu " . $smena . "','" . $now->format('Y-m-d H:i:s') . "')";

            return "OK";
        }
        else
        {
            return "Zadána nepodporovaná směna " . $smena;
        }
    
    }
    else
    {
        return "Zadáno špatné / neznámé osobní číslo";
    }   
   
}

function pocet_zam_firma_den($firma,$datum)
{
    global $conn;
    $pocet = 0;
      
    $sql = "select count(*) as pocet from dochazka where firma = '" . $firma . "' and datum='" . $datum . "' and nepritomnost=''";
   
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            

    while ($radek = mysqli_fetch_array($vysledek))
    {  
        $pocet = $radek["pocet"];
    }
    
    mysqli_free_result($vysledek);
    
    return $pocet;

}

function pocet_zam_cilovou_stanici_den($cilova, $datum)
{
    global $conn;
    $pocet = 0;

    $sql = "SELECT COUNT(*) AS pocet
            FROM dochazka
            LEFT JOIN zamestnanci ON dochazka.zamestnanec = zamestnanci.id
            WHERE zamestnanci.cilova = ?
              AND datum = ?
              AND dochazka.nepritomnost = ''
              AND zamestnanci.os_cislo REGEXP '^[0-9]+$'";

    // Prepare
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Chyba při přípravě dotazu: " . $conn->error);
    }

    // Bind param — oba jsou stringy
    $stmt->bind_param("ss", $cilova, $datum);

    // Execute
    $stmt->execute();

    // Bind result
    $stmt->bind_result($pocet);

    // Fetch
    if ($stmt->fetch()) {
        // $pocet je načten
    }

    // Close
    $stmt->close();

    return $pocet;
}

function pocet_zam_nepritomnych_za_den($datum,$nepritomnost)
{
    global $conn;
    $pocet = 0;
      
    $sql = "select count(*) as pocet from dochazka where datum='" . $datum . "' and nepritomnost='" . $nepritomnost . "'";
   
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            

    while ($radek = mysqli_fetch_array($vysledek))
    {  
        $pocet = $radek["pocet"];
    }
    
    mysqli_free_result($vysledek);
    
    return $pocet;

}

function pocet_zam_nepritomnych_za_den_firma($datum, $nepritomnost, $firma)
{
    global $conn;
    $pocet = 0;

    $sql = "SELECT COUNT(*) AS pocet
            FROM dochazka
            LEFT JOIN zamestnanci ON dochazka.zamestnanec = zamestnanci.id
            WHERE datum = ?
              AND dochazka.nepritomnost = ?
              AND zamestnanci.cilova = ?
              AND zamestnanci.os_cislo REGEXP '^[0-9]+$'";

    // Prepare
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Chyba při přípravě dotazu: " . $conn->error);
    }

    // všechny tři parametry jsou stringy → "sss"
    $stmt->bind_param("sss", $datum, $nepritomnost, $firma);

    // Execute
    $stmt->execute();

    // Bind result
    $stmt->bind_result($pocet);

    // Fetch
    $stmt->fetch();

    // Close
    $stmt->close();

    return $pocet;
}

function nacti_nepritomnosti_mesic_sumy($firma, $mesic_start, $mesic_end) 
{

    global $conn;
    $data = [];

    $sql = "SELECT datum, dochazka.nepritomnost, COUNT(*) AS pocet
            FROM dochazka
            LEFT JOIN zamestnanci ON dochazka.zamestnanec = zamestnanci.id
            WHERE zamestnanci.cilova = ?
              AND zamestnanci.os_cislo REGEXP '^[0-9]+$'
              AND datum BETWEEN ? AND ?
              AND dochazka.nepritomnost IN ('DOV','DPN','ABS') 
            GROUP BY datum, dochazka.nepritomnost";

    $stmt = $conn->prepare($sql);
    if (!$stmt) die("Chyba při přípravě dotazu: " . $conn->error);

    $stmt->bind_param("sss", $firma, $mesic_start, $mesic_end);
    $stmt->execute();
    $stmt->bind_result($datum, $nepritomnost, $pocet);

    while ($stmt->fetch()) {
        $data[$nepritomnost][$datum] = $pocet;
    }

    $stmt->close();

    return $data;
}

function get_castka_fakturace($mesic)
{
    global $conn;
    $cena = 0;
      
    $sql = "select fakturace from fakturace where mesic ='" . $mesic . "'";
   
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            

    while ($radek = mysqli_fetch_array($vysledek))
    {  
        $cena = $radek["fakturace"];
    }
    
    mysqli_free_result($vysledek);
    
    return $cena;

}

function get_car_from_zastavka($zastavka)
{
    global $conn;
    $auto = "";
      
    $sql = "select auto from zastavky where id ='" . $zastavka . "'";
   
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            

    while ($radek = mysqli_fetch_array($vysledek))
    {  
        $auto = $radek["auto"];
    }
    
    mysqli_free_result($vysledek);
    
    return $auto;

}

function update_castka_fakturace($mesic,$castka) 
{
  
    global $conn;
      
    $dotaz="update fakturace set fakturace='" . $castka . "' where mesic='" . $mesic . "'"; 
        
    if (!($vysledek = mysqli_query($conn, $dotaz)))
    {
    die("Nelze provést dotaz.</body></html>");
    }

    //vlozim zaznam do logu
    $now = new DateTime('now', new DateTimeZone('Europe/Prague'));

    if (!($vysledek = mysqli_query($conn, 
    "insert into logs (kdo,typ,infotext,datumcas) values ('" . $_SESSION["log_id"] . "','Změna fakturace','Změna částky fakturace za měsíc " . $mesic . " na částku " . $castka . " Kč','" . $now->format('Y-m-d H:i:s') . "')")))
    {
    die("Nelze provést dotaz.</body></html>");
    } 
 
}

function insert_castka_fakturace($mesic,$castka) 
{
  
    global $conn;
      
    $dotaz="insert into fakturace(mesic,fakturace) values ('" . $mesic . "','" . $castka . "')"; 
    
    if (!($vysledek = mysqli_query($conn, $dotaz)))
    {
    die("Nelze provést dotaz.</body></html>");
    }

    //vlozim zaznam do logu
    $now = new DateTime('now', new DateTimeZone('Europe/Prague'));

    if (!($vysledek = mysqli_query($conn, 
    "insert into logs (kdo,typ,infotext,datumcas) values ('" . $_SESSION["log_id"] . "','Nová fakturace','Vložená nová částka fakturace za měsíc " . $mesic . " na částku " . $castka . " Kč','" . $now->format('Y-m-d H:i:s') . "')")))
    {
    die("Nelze provést dotaz.</body></html>");
    } 
 
}

function zjisti_dochazku_agenturnika($id_emp) {

    global $conn;

    $dochazka = "";
          
    $sql = "SELECT zamestnanec,datum,cas FROM dochazka WHERE zamestnanec='" . $id_emp . "' and now() <= DATE_ADD(concat(datum,' ', cas), INTERVAL 15 HOUR) order by datum desc,cas desc limit 1";
   
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            

    while ($radek = mysqli_fetch_array($vysledek))
    {  
        $dochazka = $radek["cas"];
    }
    
    mysqli_free_result($vysledek);
    
    return $dochazka;
  
}

function pocet_dopravcu() {

global $conn;

$pocet = 0;
        
$sql = "select max(id) as pocet from auta";

if (!($vysledek = mysqli_query($conn, $sql)))
{
die("Nelze provést dotaz</body></html>");
}            

while ($radek = mysqli_fetch_array($vysledek))
{  
    $pocet = $radek["pocet"];
}

mysqli_free_result($vysledek);

return $pocet;

}

function get_firmy_from_id_IN($parametr) {

    global $conn;
    
    $firmy = "";
            
    $sql = "select firma from firmy where id in(" .$parametr . ")";
    
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            
    
    while ($radek = mysqli_fetch_array($vysledek))
    {  
        $firmy = $firmy . $radek["firma"] . "<br>";
    }
    
    mysqli_free_result($vysledek);

    if ($firmy == "")
    {
        $firmy = "<span class='text-center fw-bold text-danger'>žádná</span>";
    }
    
    return $firmy;
    
}

function test_cron() 
{
  
    global $conn;
      
    $dotaz="insert into opravneni (uzivatel,firma,permition) values ('1','1','1')"; 
        
    if (!($vysledek = mysqli_query($conn, $dotaz)))
    {
    die("Nelze provést dotaz.</body></html>");
    }

}

function cron_na_vlastni_dopravu(): void
{
    global $conn;

    // ---- Konfigurace
    date_default_timezone_set('Europe/Prague');

    $DEBUG = false; // <-- přepni na false v produkci
    $debug = function(string $msg) use ($DEBUG) {
        if ($DEBUG) {
            echo "[DEBUG] " . htmlspecialchars($msg, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "<br>";
            // Alternativa: log do souboru místo echo
            // error_log("[cron_vlastni_doprava] $msg");
        }
    };

    $now = new DateTimeImmutable('now');
    $debug("Start funkce, now=" . $now->format('Y-m-d H:i:s'));

    $weekDay = (int)$now->format('N'); // 1..7
    $debug("Den v týdnu N=" . $weekDay);

    if ($weekDay > 5) {
        echo "O víkendu se nepracuje !";
        $debug("Konec: víkend");
        return;
    }

    // ---- Helpers (mysqli prepared)
    $fetchOneAssoc = function(string $sql, string $types = "", array $params = []) use ($conn, $debug): ?array {
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            $debug("SQL prepare failed: " . mysqli_error($conn) . " | SQL=" . $sql);
            return null;
        }

        if ($types !== "") {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }

        if (!mysqli_stmt_execute($stmt)) {
            $debug("SQL execute failed: " . mysqli_stmt_error($stmt) . " | SQL=" . $sql);
            mysqli_stmt_close($stmt);
            return null;
        }

        $res = mysqli_stmt_get_result($stmt);
        $row = $res ? mysqli_fetch_assoc($res) : null;

        mysqli_stmt_close($stmt);
        return $row ?: null;
    };

    $fetchAllAssoc = function(string $sql, string $types = "", array $params = []) use ($conn, $debug): array {
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            $debug("SQL prepare failed: " . mysqli_error($conn) . " | SQL=" . $sql);
            return [];
        }

        if ($types !== "") {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }

        if (!mysqli_stmt_execute($stmt)) {
            $debug("SQL execute failed: " . mysqli_stmt_error($stmt) . " | SQL=" . $sql);
            mysqli_stmt_close($stmt);
            return [];
        }

        $res = mysqli_stmt_get_result($stmt);
        $rows = [];
        if ($res) {
            while ($r = mysqli_fetch_assoc($res)) {
                $rows[] = $r;
            }
        }
        mysqli_stmt_close($stmt);
        return $rows;
    };

    // ---- 1) Načtu "preklopeni"
    $row = $fetchOneAssoc(
        "SELECT parametr1 FROM nastaveni WHERE hodnota = 'vlastnidoprava' LIMIT 1"
    );
    if (!$row) {
        echo "Chyba: nenalezen záznam v nastaveni (vlastnidoprava).";
        $debug("Konec: chybí nastaveni vlastnidoprava");
        return;
    }
    $preklopeni = (string)($row['parametr1'] ?? '');
    $debug("preklopeni (parametr1) = '" . $preklopeni . "'");

    // ---- 2) Načtu ID auta
    $row = $fetchOneAssoc(
        "SELECT id FROM auta WHERE spz LIKE ? ORDER BY id ASC LIMIT 1",
        "s",
        ["%auto%"]
    );
    if (!$row) {
        echo "Chyba: nenalezeno auto (auta.spz LIKE '%auto%').";
        $debug("Konec: nenalezeno auto podle LIKE %auto%");
        return;
    }
    $id_auta = (int)$row['id'];
    $debug("id_auta = " . $id_auta);

    // ---- 3) Načtu trasu (zastavka + časy R/O/N)
    $row = $fetchOneAssoc(
        "SELECT zastavka, R, O, N FROM trasy WHERE auto = ? LIMIT 1",
        "i",
        [$id_auta]
    );
    if (!$row) {
        echo "Chyba: nenalezena trasa pro auto ID $id_auta.";
        $debug("Konec: nenalezena trasa pro auto=" . $id_auta);
        return;
    }

    $id_zastavky = (int)$row['zastavka'];
    $debug("id_zastavky = " . $id_zastavky);
    $debug("Časy z DB: R='" . ($row['R'] ?? '') . "', O='" . ($row['O'] ?? '') . "', N='" . ($row['N'] ?? '') . "'");

    // ---- 4) Vyhodnotím časové okno pro spuštění směny
    $windowMinutes = 10; // okno po plánovaném čase
    $debug("windowMinutes = " . $windowMinutes);

    $parseShiftTime = function($timeStr) use ($now, $debug): ?DateTimeImmutable {
        $timeStr = trim((string)$timeStr);
        if ($timeStr === '') return null;

        // normalizace: když přijde "HH:MM:SS", necháme; když "HH:MM", taky ok
        $date = $now->format('Y-m-d');

        $dt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $date . ' ' . $timeStr);
        if ($dt instanceof DateTimeImmutable) {
            return $dt;
        }

        // fallback na format bez sekund
        $dt2 = DateTimeImmutable::createFromFormat('Y-m-d H:i', $date . ' ' . $timeStr);
        if ($dt2 instanceof DateTimeImmutable) {
            return $dt2;
        }

        $debug("parseShiftTime: nelze parse '" . $timeStr . "' (čekám H:i:s nebo H:i)");
        return null;
    };

    $shiftTimes = [
        'R' => $parseShiftTime($row['R'] ?? ''),
        'O' => $parseShiftTime($row['O'] ?? ''),
        'N' => $parseShiftTime($row['N'] ?? ''),
    ];

    $debug("Aktuální čas: " . $now->format('H:i:s'));
    foreach ($shiftTimes as $code => $dt) {
        if ($dt) {
            $debug("Směna $code → plánovaný čas: " . $dt->format('H:i'));
        } else {
            $debug("Směna $code → NENÍ definovaný čas / špatný formát");
        }
    }

    // Najdu směnu, do jejíž okna teď spadám
    $smena = 'XX';
    foreach ($shiftTimes as $code => $dt) {
        if (!$dt) {
            $debug("Směna $code → přeskočeno (neplatný čas)");
            continue;
        }

        $diff = round(($now->getTimestamp() - $dt->getTimestamp()) / 60, 2);
        $debug("Směna $code → diff=$diff min (okno 0–$windowMinutes)");

        if ($diff >= 0 && $diff < $windowMinutes) {
            $smena = $code;
            $debug("âžˇď¸Ź AKTIVNÍ SMĚNA = $smena");
            break;
        }
    }

    if ($smena === 'XX') {
        echo "Není čas na vložení automatické docházky - auta";
        $debug("Konec: žádná směna v okně");
        return;
    }

    // ---- 5) Kontrola, zda už nebyla vložena stejná směna
    if ($smena === $preklopeni) {
        echo "Nelze dvakrát vložit vlastní dopravu na stejné směně, čeká se na další směnu";
        $debug("Konec: smena=$smena == preklopeni=$preklopeni");
        return;
    }

    // ---- 6) Výběr zaměstnanců pro zastávku a směnu v aktuálním ISO týdnu
    $rok = (int)$now->format('o');  // ISO rok
    $tyden = (int)$now->format('W'); // ISO týden
    $datum = $now->format('Y-m-d');

    $debug("ISO rok=$rok, ISO tyden=$tyden, datum=$datum");

    $sqlEmp = "
        SELECT z.id, z.firma, z.nepritomnost
        FROM plan_smen ps
        LEFT JOIN zamestnanci z ON ps.jmeno = z.id
        WHERE z.nastup = ?
          AND ps.rok = ?
          AND ps.tyden = ?
          AND ps.smena = ?
          AND DATE(z.vstup) <= CURDATE()
          AND (z.vystup = '0000-00-00' OR DATE(z.vystup) >= CURDATE())
    ";

    $zam = $fetchAllAssoc($sqlEmp, "iiis", [$id_zastavky, $rok, $tyden, $smena]);
    $debug("Nalezeno zaměstnanců: " . count($zam));

    if (!$zam) {
        echo "Pro zastávku $id_zastavky a směnu $smena nebyli nalezeni žádní zaměstnanci.";
        $debug("Konec: 0 zaměstnanců pro vložení");
        // Schválně neukládám preklopeni, aby to šlo ještě zkusit v okně.
        return;
    }

    // ---- 7) Vložím docházku, pokud nemá nepřítomnost
    $vlozeno = 0;
    $preskocenoNepritomnost = 0;

    foreach ($zam as $radek) {
        $idEmp = (int)($radek['id'] ?? 0);
        if ($idEmp <= 0) {
            $debug("Přeskočen řádek s neplatným id zaměstnance");
            continue;
        }

        $nepr = kontrola_nepritomnosti($idEmp, $datum);

        if ($nepr === '') {
            $debug("Insert docházky: emp=$idEmp, firma=" . ($radek['firma'] ?? '') . ", smena=$smena");
            insert_attandance($idEmp, $id_auta, $id_zastavky, $radek['firma'], $smena, '1', '');
            $vlozeno++;
        } else {
            $debug("Přeskočeno (nepřítomnost): emp=$idEmp, nepr='$nepr'");
            $preskocenoNepritomnost++;
        }
    }

    $debug("Vložené záznamy: $vlozeno, přeskočeno kvůli nepřítomnosti: $preskocenoNepritomnost");

    // ---- 8) Uložím preklopeni (smena) do nastavení
    $stmt = mysqli_prepare($conn, "UPDATE nastaveni SET parametr1 = ? WHERE hodnota = 'vlastnidoprava'");
    if (!$stmt) {
        $debug("SQL prepare failed (update nastaveni): " . mysqli_error($conn));
        echo "Vložení proběhlo, ale nepovedlo se uložit stav směny do nastaveni.";
        return;
    }

    mysqli_stmt_bind_param($stmt, "s", $smena);

    if (!mysqli_stmt_execute($stmt)) {
        $debug("SQL execute failed (update nastaveni): " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        echo "Vložení proběhlo, ale nepovedlo se uložit stav směny do nastaveni.";
        return;
    }
    mysqli_stmt_close($stmt);

    echo "Vlastní doprava: vloženo $vlozeno záznamů za směnu $smena.";
    $debug("Konec OK: uloženo preklopeni='$smena'");
}


function cron_na_zmenu_smennosti()
{
    global $conn;

    date_default_timezone_set('Europe/Prague'); // Nastavte svou časovou zónu

    // Získání aktuálního času
    $currentHour = date('H'); // Získání hodiny
    $currentMinute = date('i'); // Získání minut
    $currentDate = date('Y-m-d'); // Získaní aktuálního data

    $weekDay = date('N');  
    
    $sql = "SELECT id,zmenasmen,zmenastatus FROM firmy WHERE zmenastatus='1' and aktivni='1'";

    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }

    while ($radek = mysqli_fetch_array($vysledek))
    {  
        $date=date_create($radek["zmenasmen"]);

        $datum=date_format($date,"Y-m-d");
        $hodina=date_format($date,"H");
        $minuta=date_format($date,"i");

        if ($currentDate == $datum)
        {
            if (($currentHour == $hodina and $currentMinute >= $minuta) and ($currentHour == $hodina and $currentMinute <= $minuta+5))
            {
                // vše cajk, provedu změnu směn
                hromadna_zmena_smen($radek["id"]);
                zmena_smen_provedena($radek["id"]); 
            }
        }
    }

    mysqli_free_result($vysledek);
       
}

function cron_vkladani_nepritomnosti()
{
    global $conn;

    date_default_timezone_set('Europe/Prague'); // Nastavte svou časovou zónu

    // Získání aktuálního času
    $currentHour = date('H'); // Získání hodiny
    $currentMinute = date('i'); // Získání minut

    $weekDay = date('N');

    $dneska = date_format(date_create('now'),"Y-m-d");
    
    if (($weekDay > 0) and ($weekDay <= 5))
    { 
        //nactu zaznamy z nepritomnosti, ktere odpovidaji dnesnimu dni
        $sql = "SELECT id,zamestnanec,datum,nepritomnost from nepritomnost where datum='" . $dneska . "' and dochazka='0'";

        echo $sql . "<br><br>";

        if (!($vysledek = mysqli_query($conn, $sql)))
        {
        die("Nelze provést dotaz</body></html>");
        }

        while ($radek = mysqli_fetch_array($vysledek))
        {  
            {
                // zjistim si cas, kdy by mel zamestnanec nastupovat
                $firma = get_info_from_zamestnanci_table($radek['zamestnanec'],'firma');
                //$smena = get_info_from_zamestnanci_table($radek['zamestnanec'],'smena');
                $smena = getSmena($radek['zamestnanec']);
                //echo "<br>Směna: " . $smena . "<br>";
                $zastavka = get_info_from_zamestnanci_table($radek['zamestnanec'],'nastup');
                //echo "Zastávka: " . $zastavka . "<br>";
                $cas_nastupu = get_time_nastupu($zastavka,$smena);
                //echo "Nástup: " . $cas_nastupu . "<br>";
                $auto = get_bus_from_zastavky($zastavka);
                //echo "Bus: " . $auto . "<br>";
                //echo "Zaměstnanec: " . get_name_from_id_zam($radek['zamestnanec'])  . "<br>";
                $ip = $_SERVER['REMOTE_ADDR'];
                    
                // --- OCHRANA PROTI NEPLATNÉMU ČASU ---
                if (empty($cas_nastupu) || strpos($cas_nastupu, ":") === false) {
                    echo "ID {$radek['id']} zam: {$radek['zamestnanec']} – čas nástupu není dostupný.<br>";
                    continue; // přeskočí tento záznam
                }

                // explode
                $cas = explode(":", $cas_nastupu);

                // další kontrola, jestli má explode správný výstup
                if (count($cas) < 2 || !is_numeric($cas[0]) || !is_numeric($cas[1])) {
                    echo "ID {$radek['id']} zam: {$radek['zamestnanec']} – neplatný formát času ($cas_nastupu).<br>";
                    continue;
                }

                // posun hodiny
                $cas[0] = intval($cas[0]) + 1;
                $cas[1] = intval($cas[1]);

                if ($currentHour == $cas[0] and $currentMinute >= $cas[1])
                {
                    //zjistim zda je dochazka pro daneho zamestnance a den
                    //vysledkem je bud smena nebo hodnota nepritomnosti pokud je <> ''
                    $hodnota = check_dochazky($radek['zamestnanec'],$dneska);

                    //echo "hodnota:" . $hodnota;

                    if ($hodnota == '')
                    {
                        //vlozim smenu i s nepritomnosti do tabulky dochazka
                        insert_attandance_manually($radek['zamestnanec'],$auto,$zastavka,$firma,$smena,$dneska,$cas_nastupu,$radek['nepritomnost']);

                        //radek o nepritomnosti v tabulce nepritomnost zmenim na 1
                        update_nepritomnost_radek($radek['id'],$radek['zamestnanec'],$radek['nepritomnost'],$dneska);

                        echo "<br>Vložena manuálně docházka - id " . $radek['id'] . "<br>";
                    }
                    else
                    {
                        // v tabulce dochazek uz neco je
                    }
                }
                else
                {
                    echo "ID " . $radek['id'] . " zam: " . $radek['zamestnanec'] . " by chtělo, ale není správný čas " . $cas[0] . ":" . $cas[1] . "<br>";
                }
            }
        }

        mysqli_free_result($vysledek);
    }
    else
    {
        echo "O víkendu se nepracuje !";
    }
}

function hromadna_zmena_smen($firma) {

    global $conn;
                
    $sql = "select id from zamestnanci where firma='" . $firma . "' and smena2<>'' and smena2<>'N/A'";  

    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            
    
    while ($radek = mysqli_fetch_array($vysledek))
    {  
        update_smena_from_smena2($radek["id"]);
    }
    
    mysqli_free_result($vysledek);
    
}

function update_smena_from_smena2($id_zam) 
{
    global $conn;
      
    $dotaz="update zamestnanci set smena=smena2,smena2='N/A' where id='" . $id_zam . "'";   
        
    if (!($vysledek = mysqli_query($conn, $dotaz)))
    {
    die("Nelze provést dotaz.</body></html>");
    }

    //vlozim zaznam do logu
    $now = new DateTime('now', new DateTimeZone('Europe/Prague'));

    if (!($vysledek = mysqli_query($conn, 
    "insert into logs (kdo,typ,infotext,datumcas) values ('systém','Překlopení směny2 --> směnu','Změna směnnosti u " . get_name_from_id_zam($id_zam) . " na směnu " . get_shift_from_id_zam($id_zam) . "','" . $now->format('Y-m-d H:i:s') . "')")))
    {
    die("Nelze provést dotaz.</body></html>");
    } 

}

function zmena_smen_provedena($firma) 
{
    global $conn;

    $now = new DateTime('now', new DateTimeZone('Europe/Prague'));
      
    $dotaz="update firmy set zmenastatus='0',zmenaprovedena='" . $now->format('Y-m-d H:i:s') . "' where id='" . $firma . "'"; 
        
    if (!($vysledek = mysqli_query($conn, $dotaz)))
    {
    die("Nelze provést dotaz.</body></html>");
    }

}

function je_datum($value)
{
  if (!$value) {
    return false;
  }

  try {
    new \DateTime($value);
    return true; }
  catch (\Exception $e) {
    return false;
  }
}

function modal_vlozeni_dochazky()
{  
    
    global $conn;

    ?>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Manuální vložení docházky do systému</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            
            <form name="vlozenidochazky" method="POST" action="dochazka.php?typ=insertattandance">

            <?php

            if ($_SESSION['typ'] == 5)
            {
                $sql = "select zamestnanci.id,prijmeni,jmeno,firmy.firma from zamestnanci left join firmy on zamestnanci.firma = firmy.id where zamestnanci.aktivni='1' order by prijmeni";
            }
            else
            {
                $sql = "select zamestnanci.id,prijmeni,jmeno,firmy.firma from zamestnanci left join firmy on zamestnanci.firma = firmy.id where zamestnanci.firma in (" . $_SESSION['firma'] . ") and zamestnanci.aktivni='1' order by prijmeni";
            }

            if (!($vysledek = mysqli_query($conn, $sql)))
            {
            die("Nelze provést dotaz</body></html>");
            } 

            $dneska = date_create('now');      
            $vcera = date_create('now');
                 
            $vcera = date_add($vcera,date_interval_create_from_date_string("-1 days"));

            ?>           

            <div class="mb-3">
                <label for="jmeno" class="form-label">Příjmení jméno, firma</label>
                <select class="form-select mt-2 bg-primary-subtle" aria-label="Typ nepřítomnosti" name="jmeno" id="jmeno">
              
                    <?php
                    while ($radek = mysqli_fetch_array($vysledek))
                    {   ?>
                            <option value='<?php echo $radek['id'];?>'><?php echo $radek['prijmeni'] . " " . $radek['jmeno'] . " (" . $radek['firma'] . ")";?></option>    
                        <?php
                    }
                    ?>
                                    
                </select>
            </div>
    
            <?php $nepritomnost = 1;?>

            <div class="mb-3">
                <label for="datum" class="form-label">Den v docházce</label>
                <select class="form-select mt-2 bg-primary-subtle" aria-label="Typ nepřítomnosti" name="datum" id="datum">
                    <option value='<?php echo date_format($dneska,"Y-m-d");?>'>Dnešní den <?php echo date_format($dneska,"d.m.Y");?></option>
                    <option value='<?php echo date_format($vcera,"Y-m-d");?>'>Včerejší den <?php echo date_format($vcera,"d.m.Y");?></option>
                </select>
            </div>

            <div class="mb-3">
                <label for="poznamka" class="form-label">Směna, nástupní místo a čas nástupu se bude brát z aktuálně nastavených parametrů u zaměstnance, vložena bude pouze za předpokladu, že zvolený zaměstnanec v daný den nemá žádný docházkový záznam!</label>
            </div>
            
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary mb-3">Vložit docházkový záznam</button>
            <button type="button" class="btn btn-secondary mb-3" data-bs-dismiss="modal">Zavřít</button>
        </div>
        </div>

        </form>

    </div>
    </div>

    <?php
}

function get_info_from_zamestnanci_table($id_zam,$sloupec) {

    global $conn;
    
    $hodnota = "";
            
    $sql = "select " . $sloupec . " as zaznam from zamestnanci where id='" . $id_zam . "'";
    
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            
    
    while ($radek = mysqli_fetch_array($vysledek))
    {  
        $hodnota = $radek['zaznam'];
    }
    
    mysqli_free_result($vysledek);
    
    return $hodnota;
    
}

function get_time_nastupu($id_zastavky, $smena)
{
    global $conn;

    $hodnota = "";

    // Připravený dotaz
    $sql = "SELECT cas1, cas2, cas3 FROM zastavky WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {

        // Navázání parametru (id_zastavky je číslo → "i")
        $stmt->bind_param("i", $id_zastavky);

        $stmt->execute();

        // Získání výsledku
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {

            // Výběr času podle směny
            if ($smena === "R") {
                $hodnota = $row['cas1'];
            } 
            elseif ($smena === "O") {
                $hodnota = $row['cas2'];
            } 
            elseif ($smena === "N") {
                $hodnota = $row['cas3'];
            }
        }

        $stmt->close();
    }

    return $hodnota;
}

function edit_dochazky($id_radku)
{  
    
    global $conn;  
    
    $hodnota = "";
            
    $sql = "select zamestnanec,nepritomnost,datum,cas from dochazka where id='" . $id_radku . "'";
    
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            
    
    while ($radek = mysqli_fetch_array($vysledek))
    {  
        $zam_id = $radek['zamestnanec'];
        $nepritomnost = $radek['nepritomnost'];
        $datum = $radek['datum'];
        $cas = $radek['cas'];
    }
    
    mysqli_free_result($vysledek);

    ?>

    <!-- Modal -->
    <div class="modal fade" id="editModal<?php echo $id_radku;?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Editace docházkového záznamu</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            
            <form name="editdochazky" method="POST" action="dochazka.php?typ=editattandance">

            <div class="mb-3 text-start">
                <label for="disabledTextInput" class="form-label">Příjmení jméno, firma</label>
                <input type="text" id="disabledTextInput" class="form-control bg-primary-subtle" placeholder="<?php echo get_name_from_id_zam($zam_id);?>" disabled>
            </div>

            <div class="mb-3 text-start">
                <label for="disabledTextInput" class="form-label">Datum a čas</label>
                <input type="text" id="aaa" name="aaa" class="form-control bg-primary-subtle" placeholder="<?php echo $datum . " " . $cas;?>" disabled>
            </div>     

            <div class="mb-3 text-start">
                <label for="datum" class="form-label">Nepřítomnost</label>
                <select class="form-select mt-2 bg-primary-subtle" aria-label="Docházka" name="dochazka" id="dochazka">
                               
                    <?php
                    echo ($nepritomnost == '') ? "<option value='' class='bg-success-subtle' selected>Přítomen</option>" : "<option value='' class='bg-success-subtle'>Přítomen</option>";
                    echo ($nepritomnost == 'DPN') ? "<option value='DPN' class='bg-warning-subtle' selected>Dočasná pracovní neschopnost</option>" : "<option value='DPN' class='bg-warning-subtle'>Dočasná pracovní neschopnost</option>";
                    echo ($nepritomnost == 'DOV') ? "<option value='DOV' class='bg-warning-subtle' selected>Dovolená</option>" : "<option value='DOV' class='bg-warning-subtle'>Dovolená</option>";
                    echo ($nepritomnost == 'ABS') ? "<option value='ABS' class='bg-warning-subtle' selected>Absence</option>" : "<option value='ABS' class='bg-warning-subtle'>Absence</option>";
                    ?>
            
                    <option value="SMAZ" class='bg-danger-subtle'>Záznam úplně smazat</option>

                </select>
            </div>

            <input type="hidden" class="form-control" id="radek_v_db" name="radek_v_db" placeholder="" value=<?php echo $id_radku;?>>
            <input type="hidden" class="form-control" id="id_name" name="id_name" placeholder="" value=<?php echo $zam_id;?>>
            <input type="hidden" class="form-control" id="datumcas" name="datumcas" placeholder="" value="<?php echo $datum . " " . $cas;?>">
            
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary mb-3">Ulož změnu</button>
            <button type="button" class="btn btn-secondary mb-3" data-bs-dismiss="modal">Zavřít</button>
        </div>
        </div>

        </form>

    </div>
    </div>

    <?php
}


function dochazka_zamestnanec_den($zamestnanec, $datum)
{
    global $conn;
    $hodnota = "";

    // Připravený dotaz
    $sql = "SELECT smena, nepritomnost 
            FROM dochazka 
            WHERE zamestnanec = ? 
              AND datum = ?";

    if ($stmt = $conn->prepare($sql)) {

        // Navázání parametrů (zamestnanec = int, datum = string)
        $stmt->bind_param("is", $zamestnanec, $datum);

        $stmt->execute();

        // Získání výsledku
        $result = $stmt->get_result();

        while ($radek = $result->fetch_assoc()) {
            if ($radek["nepritomnost"] !== '') {
                $hodnota = $radek["nepritomnost"];
            } else {
                $hodnota = $radek["smena"];
            }
        }

        $stmt->close();
    } else {
        die("Nelze připravit dotaz.");
    }

    return $hodnota;
}

function nacti_dochazku_mesic($zamestnanci, $start_day, $dnu)
{
    global $conn;

    $dochazka = [];

    if (empty($zamestnanci)) return $dochazka;

    // připravíme seznam ID pro IN
    $ids = implode(',', array_map('intval', $zamestnanci));

    // od-do datum pro měsíc
    $konec_mesice = date('Y-m-d', strtotime($start_day . ' + ' . $dnu . ' days'));

    $sql = "SELECT zamestnanec, datum, smena, nepritomnost, poznamka
            FROM dochazka
            WHERE zamestnanec IN ($ids)
              AND datum BETWEEN ? AND ?";

    if ($stmt = $conn->prepare($sql)) {

        $stmt->bind_param("ss", $start_day, $konec_mesice);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $id = $row['zamestnanec'];
            $datum = $row['datum'];
            $hodnota = $row['nepritomnost'] !== '' ? $row['nepritomnost'] : $row['smena'];
            $poznamka = $row['poznamka'];

            // asociativní pole pro každý den
            $dochazka[$id][$datum] = [
                'hodnota' => $hodnota,
                'poznamka' => $poznamka
            ];
        }

        $stmt->close();
    } else {
        die("Nelze připravit dotaz pro docházku.");
    }

    return $dochazka;
}

function nacti_nepritomnosti_mesic($zamestnanci, $start_day, $dnu)
{
    global $conn;

    $nepritomnosti = [];

    if (empty($zamestnanci)) return $nepritomnosti;

    // připravíme seznam ID pro IN
    $ids = implode(',', array_map('intval', $zamestnanci));

    // od-do datum pro měsíc
    $konec_mesice = date('Y-m-d', strtotime($start_day . ' + ' . $dnu . ' days'));

    $sql = "SELECT zamestnanec, datum, nepritomnost, poznamka
            FROM nepritomnost
            WHERE zamestnanec IN ($ids)
              AND datum BETWEEN ? AND ?";

    if ($stmt = $conn->prepare($sql)) {

        $stmt->bind_param("ss", $start_day, $konec_mesice);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $id = $row['zamestnanec'];
            $datum = $row['datum'];
            $hodnota = $row['nepritomnost'];
            $poznamka = $row['poznamka'];

            // pokud zaměstnanec má více typů nepřítomnosti ve stejný den
            if (isset($nepritomnosti[$id][$datum])) {
                $nepritomnosti[$id][$datum]['hodnota'] .= ',' . $hodnota;
                $nepritomnosti[$id][$datum]['poznamka'] .= ($poznamka !== '' ? ','.$poznamka : '');
            } else {
                $nepritomnosti[$id][$datum] = [
                    'hodnota' => $hodnota,
                    'poznamka' => $poznamka
                ];
            }
        }

        $stmt->close();
    } else {
        die("Nelze připravit dotaz pro nepřítomnosti.");
    }

    return $nepritomnosti;
}

function novy_nabor($id = '')
{
    global $conn;

    $polehodnot = [];

    if ($id <> '') {
        $sql = "select id,prijmeni,jmeno,telefon,adresa,dat_narozeni,stat,dat_evidence,zdroj_inzerce,pozice,klient,klient2,souhlas,rekruter,vysledek,nastup,vystup,koordinator,poznamka,duvod_ukonceni,doklad from nabory where id='" . $id . "'";

        if (!($vysledek = mysqli_query($conn, $sql))) {
            die("Nelze provést dotaz</body></html>");
        }

        while ($radek = mysqli_fetch_array($vysledek, MYSQLI_ASSOC)) {
            $polehodnot[] = $radek;
        }

        mysqli_free_result($vysledek);
        ?>
        <form name="nabor_new" method="POST" action="nabory.php?typ=updatenabor">
        <?php
    } else {
        ?>
        <form name="nabor_new" method="POST" action="nabory.php?typ=savenabor">
        <?php
    }

    $isEdit = ($id != '' && isset($polehodnot[0]));
    $row = $isEdit ? $polehodnot[0] : [];

    $modalId = $isEdit ? "ModalNaborInfo" : "nabor_new";
    $title = $isEdit ? "Editace uchazeče o zaměstnání" : "Nový nábor uchazeče o zaměstnání";
    $subtitle = $isEdit ? "Úprava údajů o uchazeči" : "Založení nového uchazeče";

    function nv($row, $key, $default = '')
    {
        return htmlspecialchars($row[$key] ?? $default, ENT_QUOTES, 'UTF-8');
    }
    ?>

    <div class="modal fade" id="<?php echo $modalId; ?>" tabindex="-1" aria-labelledby="<?php echo $modalId; ?>Label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow rounded-4 modal-compact">

                <div class="modal-header text-white border-0"
                     style="background: linear-gradient(135deg,#0d6efd,#0b5ed7);">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-person-lines-fill"></i>
                        <div>
                            <div class="fw-semibold" id="<?php echo $modalId; ?>Label"><?php echo $title; ?></div>
                            <div class="small text-white-50"><?php echo $subtitle; ?></div>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <div class="card border-0 shadow-sm rounded-3 mb-2">
                        <div class="card-body">
                            <div class="fw-semibold small mb-2">
                                <i class="bi bi-person-badge text-primary me-1"></i> Osobní údaje
                            </div>

                            <div class="row g-2">
                                <div class="col-md-3">
                                    <label class="form-label">Příjmení</label>
                                    <input type="text" class="form-control" id="prijmeni" name="prijmeni" value="<?php echo nv($row, 'prijmeni'); ?>" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Jméno</label>
                                    <input type="text" class="form-control" id="jmeno" name="jmeno" value="<?php echo nv($row, 'jmeno'); ?>" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Stát</label>
                                    <select class="form-select" name="stat" id="stat">
                                        <?php
                                        $vybrany = $row['stat'] ?? '';
                                        $stati = [
                                            'PL' => 'Polsko',
                                            'CZ' => 'Česká republika',
                                            'SK' => 'Slovenská republika'
                                        ];

                                        foreach ($stati as $kod => $nazev) {
                                            $selected = ($kod === $vybrany) ? 'selected' : '';
                                            echo "<option value='" . htmlspecialchars($kod, ENT_QUOTES, 'UTF-8') . "' $selected>" . htmlspecialchars($nazev, ENT_QUOTES, 'UTF-8') . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Telefon</label>
                                    <input type="text" class="form-control" id="telefon" name="telefon" value="<?php echo nv($row, 'telefon'); ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Adresa</label>
                                    <input type="text" class="form-control" id="adresa" name="adresa" value="<?php echo nv($row, 'adresa'); ?>">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Doklad</label>
                                    <input type="text" class="form-control" id="doklad" name="doklad" value="<?php echo nv($row, 'doklad'); ?>">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Datum narození</label>
                                    <input type="date"
                                           class="form-control text-center"
                                           id="dat_narozeni"
                                           name="dat_narozeni"
                                           value="<?php echo $isEdit ? htmlspecialchars(date_format(date_create($row['dat_narozeni']), 'Y-m-d'), ENT_QUOTES, 'UTF-8') : date('Y-m-d', strtotime('1983-06-28')); ?>"
                                           required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-3 mb-2">
                        <div class="card-body">
                            <div class="fw-semibold small mb-2">
                                <i class="bi bi-megaphone text-primary me-1"></i> Náborové informace
                            </div>

                            <div class="row g-2">
                                <div class="col-md-3">
                                    <label class="form-label">Datum evidence</label>
                                    <input type="date"
                                           class="form-control text-center"
                                           id="dat_evidence"
                                           name="dat_evidence"
                                           value="<?php echo $isEdit ? htmlspecialchars(date_format(date_create($row['dat_evidence']), 'Y-m-d'), ENT_QUOTES, 'UTF-8') : date('Y-m-d'); ?>"
                                           required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Zdroj inzerce</label>
                                    <select class="form-select" name="zdroj" id="zdroj" required>
                                        <?php
                                        $vybrany = $row['zdroj_inzerce'] ?? '';
                                        $zdroje = ['OLX', 'Praca.pl', 'GoWork.pl', 'Doporučení', 'Televizní reklama', 'Novinová reklama', 'Facebook', 'Náborový leták'];

                                        foreach ($zdroje as $zdroj) {
                                            $selected = ($zdroj === $vybrany) ? 'selected' : '';
                                            echo "<option value='" . htmlspecialchars($zdroj, ENT_QUOTES, 'UTF-8') . "' $selected>" . htmlspecialchars($zdroj, ENT_QUOTES, 'UTF-8') . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Pozice</label>
                                    <select class="form-select" name="pozice" id="pozice" required>
                                        <?php
                                        $vybrany = $row['pozice'] ?? '';
                                        $pozice = ['OPV', 'Man.Dělník', 'Stroj.Dělník', 'Skladník', 'Skladník VZV', 'Svářeč', 'Řidič zakázky'];

                                        foreach ($pozice as $p) {
                                            $selected = ($p === $vybrany) ? 'selected' : '';
                                            echo "<option value='" . htmlspecialchars($p, ENT_QUOTES, 'UTF-8') . "' $selected>" . htmlspecialchars($p, ENT_QUOTES, 'UTF-8') . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Klient</label>
                                    <select class="form-select" name="klient" id="klient" required>
                                        <?php
                                        $sql = "SELECT cilova FROM zamestnanci WHERE cilova <> '' GROUP BY cilova ORDER BY cilova";
                                        $result = mysqli_query($conn, $sql);

                                        if (!$result) {
                                            echo "<option disabled>Chyba dotazu</option>";
                                        } else {
                                            $vybrany = $row['klient'] ?? '';
                                            while ($r = mysqli_fetch_assoc($result)) {
                                                $cilova = $r['cilova'];
                                                $selected = ($cilova == $vybrany) ? 'selected' : '';
                                                echo "<option value='" . htmlspecialchars($cilova, ENT_QUOTES, 'UTF-8') . "' $selected>" . htmlspecialchars($cilova, ENT_QUOTES, 'UTF-8') . "</option>";
                                            }
                                            mysqli_free_result($result);
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Klient 2</label>
                                    <select class="form-select" name="klient2" id="klient2">
                                        <?php
                                        $vybrany = $row['klient2'] ?? '';

                                        $sql = "SELECT cilova FROM zamestnanci WHERE cilova <> '' GROUP BY cilova ORDER BY cilova";
                                        $result = mysqli_query($conn, $sql);

                                        if (!$result) {
                                            echo "<option disabled>Chyba dotazu</option>";
                                        } else {
                                            echo "<option value=''" . ($vybrany === '' ? ' selected' : '') . ">-</option>";

                                            while ($r = mysqli_fetch_assoc($result)) {
                                                $cilova = $r['cilova'];
                                                $selected = ($cilova === $vybrany) ? ' selected' : '';
                                                echo "<option value='" . htmlspecialchars($cilova, ENT_QUOTES, 'UTF-8') . "'$selected>" . htmlspecialchars($cilova, ENT_QUOTES, 'UTF-8') . "</option>";
                                            }
                                            mysqli_free_result($result);
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Souhlas</label>
                                    <select class="form-select" name="souhlas" id="souhlas" required>
                                        <?php
                                        $moznosti = ['NE', 'ANO'];
                                        $vybrany = $row['souhlas'] ?? '';
                                        foreach ($moznosti as $opt) {
                                            $sel = ($opt == $vybrany) ? 'selected' : '';
                                            echo "<option value='" . htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') . "' $sel>" . htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Rekrutér</label>
                                    <select class="form-select" name="rekruter" id="rekruter" required>
                                        <?php
                                        $sql = "SELECT uzivatel FROM uzivatele WHERE typ = 6 ORDER BY uzivatel";
                                        $result = mysqli_query($conn, $sql);
                                        $vybrany = $row['rekruter'] ?? '';

                                        while ($r = mysqli_fetch_assoc($result)) {
                                            $user = $r['uzivatel'];
                                            $selected = ($user == $vybrany) ? 'selected' : '';
                                            echo "<option value='" . htmlspecialchars($user, ENT_QUOTES, 'UTF-8') . "' $selected>" . htmlspecialchars($user, ENT_QUOTES, 'UTF-8') . "</option>";
                                        }
                                        mysqli_free_result($result);
                                        ?>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Výsledek</label>
                                    <select class="form-select" name="vysledek" id="vysledek" required>
                                        <?php
                                        $moznosti = ['Přijat', 'Zamítnut', 'Čeká se', 'Nedostavil se', 'Emergency'];
                                        $vybrany = $row['vysledek'] ?? '';
                                        foreach ($moznosti as $opt) {
                                            $sel = ($opt == $vybrany) ? 'selected' : '';
                                            echo "<option value='" . htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') . "' $sel>" . htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Koordinátor</label>
                                    <select class="form-select" name="koordinator" id="koordinator" required>
                                        <?php
                                        $sql = "SELECT uzivatel FROM uzivatele WHERE typ = 1 ORDER BY uzivatel";
                                        $result = mysqli_query($conn, $sql);
                                        $vybrany = $row['koordinator'] ?? '';

                                        while ($r = mysqli_fetch_assoc($result)) {
                                            $user = $r['uzivatel'];
                                            $selected = ($user == $vybrany) ? 'selected' : '';
                                            echo "<option value='" . htmlspecialchars($user, ENT_QUOTES, 'UTF-8') . "' $selected>" . htmlspecialchars($user, ENT_QUOTES, 'UTF-8') . "</option>";
                                        }
                                        mysqli_free_result($result);
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-3 mb-2">
                        <div class="card-body">
                            <div class="fw-semibold small mb-2">
                                <i class="bi bi-calendar-range text-primary me-1"></i> Průběh spolupráce
                            </div>

                            <div class="row g-2">
                                <div class="col-md-3">
                                    <label class="form-label">Nástup</label>
                                    <input
                                        type="date"
                                        class="form-control text-center"
                                        id="dat_nastup"
                                        name="dat_nastup"
                                        value="<?php echo ($isEdit && $row['nastup'] != '0000-00-00') ? htmlspecialchars(date_format(date_create($row['nastup']), 'Y-m-d'), ENT_QUOTES, 'UTF-8') : ''; ?>">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Výstup</label>
                                    <input
                                        type="date"
                                        class="form-control text-center"
                                        id="dat_vystup"
                                        name="dat_vystup"
                                        value="<?php echo ($isEdit && $row['vystup'] != '0000-00-00') ? htmlspecialchars(date_format(date_create($row['vystup']), 'Y-m-d'), ENT_QUOTES, 'UTF-8') : ''; ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Důvod ukončení</label>
                                    <input type="text" class="form-control" id="duvod_ukonceni" name="duvod_ukonceni" value="<?php echo nv($row, 'duvod_ukonceni'); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body">
                            <div class="fw-semibold small mb-2">
                                <i class="bi bi-journal-text text-primary me-1"></i> Poznámka
                            </div>

                            <div class="row g-2">
                                <div class="col-md-12">
                                    <label class="form-label">Poznámka</label>
                                    <textarea class="form-control" id="poznamka" name="poznamka" style="height: 70px"><?php echo $isEdit ? htmlspecialchars($row['poznamka'] ?? '', ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="radek_v_db" name="radek_v_db" value="<?php echo $isEdit ? (int)$row['id'] : ''; ?>">

                    <div id="dupInfo" style="display:none; font-weight:bold; margin-top:5px;"></div>

                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                        <i class="bi bi-x"></i> Zavřít
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnUlozit">
                        <i class="bi bi-check2-circle"></i> <?php echo $isEdit ? 'Ulož změnu' : 'Vytvořit nábor'; ?>
                    </button>
                </div>

            </div>
        </div>
    </div>

    </form>

    <?php
}

function nove_auto($id = '', $id2 = '')
{    

    if ($id <> '')
    {
        global $conn;

        $sql = "select id,spz,oznaceni from auta where id='" . $id . "'";
       
        if (!($vysledek = mysqli_query($conn, $sql)))
        {
        die("Nelze provést dotaz</body></html>");
        }            
    
        $polehodnot = array();

        while ($radek = mysqli_fetch_array($vysledek, MYSQLI_ASSOC)) {
            $polehodnot[] = $radek;
        }
        
        mysqli_free_result($vysledek);

        //print_r($polehodnot);
        ?>

        <form name="nabor_new" method="POST" action="vozovypark.php?typ=updateauto">
        
        <?php
    }
    else
    {   ?>

        <form name="nabor_new" method="POST" action="vozovypark.php?typ=vytvorauto">

        <?php
    }

    ?>

    <!-- Modal -->

    <?php
    if ($id == '')
    {   ?>
            <div class="modal fade" id="auto_new" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <?php
    }
    else
    {   ?>
            <div class="modal fade" id="ModalAutoInfo<?php echo $polehodnot[0]['id'];?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <?php
    }
    ?>
    
    <div class="modal-dialog modal-dialog-centered modal-lg">
        
        <div class="modal-content">

            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel"><?php echo ($id == '') ? "Nové auto ve vozovém parku" : "Editace auta";?></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">

                <div class="row">
                    <div class="col col-md-12 mt-2 text-start">
                        <label class="form-label">Trasa</label>
                        <input type="text" class="form-control bg-primary-subtle" id="spz" name="spz" value="<?php echo ($id == '') ? "" : $polehodnot[0]['spz'];?>" required>
                    </div>
                    
                    <div class="col col-md-12 mt-2 text-start">
                        <label class="form-label">Označení auta / krátký popis</label>  
                        <input type="text" class="form-control bg-primary-subtle" id="oznaceni" name="oznaceni" value="<?php echo ($id == '') ? "" : $polehodnot[0]['oznaceni'];?>" required>
                    </div>

                </div>
 
                <input type="hidden" class="form-control" id="id_auta" name="id_auta" placeholder="" value="<?php echo ($id == '') ? "" : $polehodnot[0]['id'];?>">
                
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary mb-3">Ulož změnu</button>
                <button type="button" class="btn btn-secondary mb-3" data-bs-dismiss="modal">Zavřít</button>
            </div>

        </div>

    </div>
    </div>

    </form>

    <?php
}

function nova_zastavka($id = '')
{    

    if ($id <> '')
    {
        global $conn;

        $sql = "select id,auto,zastavka,cas1,cas2,cas3,cas4,cas5,cas6,cas7 from zastavky where id='" . $id . "'";
       
        if (!($vysledek = mysqli_query($conn, $sql)))
        {
        die("Nelze provést dotaz</body></html>");
        }            
    
        $polehodnot = array();

        while ($radek = mysqli_fetch_array($vysledek, MYSQLI_ASSOC)) {
            $polehodnot[] = $radek;
        }
        
        mysqli_free_result($vysledek);

        //print_r($polehodnot);
        ?>

        <form name="zastavka_new" method="POST" action="vozovypark.php?typ=updatezastavky">
        
        <?php

        $auto = get_spz_from_id($polehodnot[0]['auto']);
    }
    else
    {   ?>

        <form name="zastavka_new" method="POST" action="vozovypark.php?typ=savezastavka">

        <?php

        $auto = get_spz_from_id($_GET['id']);
    }    
    ?>

    <!-- Modal -->

    <?php
    if ($id == '')
    {   ?>
            <div class="modal fade" id="zastavka_new" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <?php
    }
    else
    {   ?>
            <div class="modal fade" id="ModalZastavkaInfo<?php echo $polehodnot[0]['id'];?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <?php
    }
    ?>
    
    <div class="modal-dialog modal-dialog-centered modal-lg">
        
        <div class="modal-content">

            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel"><?php echo ($id == '') ? "Nová zastávka pro auto " . $auto : "Editace zastávky pro auto " . $auto;?></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">

                <div class="row">
                    <div class="col col-md-12 mt-2 text-start">
                        <label class="form-label">Název zastávky</label>
                        <input type="text" class="form-control bg-primary-subtle" id="zastavka" name="zastavka" value="<?php echo ($id == '') ? "" : $polehodnot[0]['zastavka'];?>" required>
                    </div>
                </div>
 
                <div class="row">
                    <div class="col col-md-4 mt-2 text-start">
                        <label class="form-label">Čas Ranní</label>
                        <input type="text" class="form-control bg-primary-subtle text-center" id="cas1" name="cas1" value="<?php echo ($id == '') ? "05:00:00" : $polehodnot[0]['cas1'];?>" required>
                    </div>

                    <div class="col col-md-4 mt-2 text-start">
                        <label class="form-label">Čas Odpolední</label>
                        <input type="text" class="form-control bg-primary-subtle text-center" id="cas2" name="cas2" value="<?php echo ($id == '') ? "13:00:00" : $polehodnot[0]['cas2'];?>" required>
                    </div>

                    <div class="col col-md-4 mt-2 text-start">
                        <label class="form-label">Čas Noční</label>
                        <input type="text" class="form-control bg-primary-subtle text-center" id="cas3" name="cas3" value="<?php echo ($id == '') ? "21:00:00" : $polehodnot[0]['cas3'];?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col col-md-3 mt-2 text-start">
                        <label class="form-label">Čas NN</label>
                        <input type="text" class="form-control bg-primary-subtle text-center" id="cas4" name="cas4" value="<?php echo ($id == '') ? "05:00:00" : $polehodnot[0]['cas4'];?>" required>
                    </div>

                    <div class="col col-md-3 mt-2 text-start">
                        <label class="form-label">Čas NR</label>
                        <input type="text" class="form-control bg-primary-subtle text-center" id="cas5" name="cas5" value="<?php echo ($id == '') ? "05:00:00" : $polehodnot[0]['cas5'];?>" required>
                    </div>

                    <div class="col col-md-3 mt-2 text-start">
                        <label class="form-label">Čas víkend</label>
                        <input type="text" class="form-control bg-primary-subtle text-center" id="cas6" name="cas6" value="<?php echo ($id == '') ? "05:00:00" : $polehodnot[0]['cas6'];?>" required>
                    </div>

                    <div class="col col-md-3 mt-2 text-start">
                        <label class="form-label">Čas přesčas</label>
                        <input type="text" class="form-control bg-primary-subtle text-center" id="cas7" name="cas7" value="<?php echo ($id == '') ? "05:00:00" : $polehodnot[0]['cas7'];?>" required>
                    </div>
                </div>
                
            </div>

            <input type="hidden" class="form-control" id="id_zastavky" name="id_zastavky" placeholder="" value="<?php echo ($id == '') ? "" : $polehodnot[0]['id'];?>">
            <input type="hidden" class="form-control" id="auto" name="auto" placeholder="" value="<?php echo ($id == '') ? $_GET['id'] : $polehodnot[0]['auto'];?>">

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary mb-3">Ulož změnu</button>
                <button type="button" class="btn btn-secondary mb-3" data-bs-dismiss="modal">Zavřít</button>
            </div>

        </div>

    </div>
    </div>

    </form>

    <?php
}

function nova_firma($id = '')
{
    global $conn;

    $polehodnot = array();

    if ($id <> '') {
        $sql = "SELECT id, firma, objednavka, aktivni 
                FROM firmy 
                WHERE id='" . $id . "'";

        if (!($vysledek = mysqli_query($conn, $sql))) {
            die("Nelze provést dotaz</body></html>");
        }

        while ($radek = mysqli_fetch_array($vysledek, MYSQLI_ASSOC)) {
            $polehodnot[] = $radek;
        }

        mysqli_free_result($vysledek);
        ?>
        <form name="firma_new" method="POST" action="firmy.php?typ=updatefirma">
        <?php
    } else {
        ?>
        <form name="firma_new" method="POST" action="firmy.php?typ=vytvorfirmu">
        <?php
    }

    $isEdit = ($id != '' && !empty($polehodnot));
    $modalId = $isEdit ? "ModalFirmaInfo" . $polehodnot[0]['id'] : "firma_new";
    $title = $isEdit ? "Editace firmy" : "Nová firma";

    $firma = $isEdit ? htmlspecialchars($polehodnot[0]['firma']) : "";
    $objednavka = $isEdit ? htmlspecialchars($polehodnot[0]['objednavka']) : "";
    $aktivni = $isEdit ? $polehodnot[0]['aktivni'] : "1";
    $idFirmy = $isEdit ? $polehodnot[0]['id'] : "";
    ?>

    <div class="modal fade" id="<?php echo $modalId; ?>" tabindex="-1" aria-labelledby="<?php echo $modalId; ?>Label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

                <div class="modal-header border-0 text-white"
                     style="background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center"
                             style="width:42px; height:42px;">
                            <i class="bi bi-buildings fs-5"></i>
                        </div>
                        <div>
                            <h1 class="modal-title fs-5 fw-semibold mb-0" id="<?php echo $modalId; ?>Label">
                                <?php echo $title; ?>
                            </h1>
                            <div class="small text-white-50">
                                <?php echo $isEdit ? "Úprava existující firmy" : "Založení nové firmy"; ?>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4 bg-light-subtle">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <div class="row g-3">

                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-building me-2 text-primary"></i>Název firmy
                                    </label>
                                    <input type="text"
                                           class="form-control form-control-lg rounded-3"
                                           id="nazevfirmy"
                                           name="nazevfirmy"
                                           value="<?php echo $firma; ?>"
                                           placeholder="Zadejte název firmy"
                                           required>
                                </div>

                                <div class="col-md-7">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-file-earmark-text me-2 text-primary"></i>Objednávka
                                    </label>
                                    <input type="text"
                                           class="form-control rounded-3"
                                           id="objednavka"
                                           name="objednavka"
                                           value="<?php echo $objednavka; ?>"
                                           placeholder="Zadejte číslo nebo označení objednávky"
                                           required>
                                </div>

                                <div class="col-md-5">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-toggle-on me-2 text-primary"></i>Stav firmy
                                    </label>
                                    <select class="form-select rounded-3" name="aktivni" id="aktivni" required>
                                        <option value="1" <?php echo ($aktivni == '1') ? 'selected' : ''; ?>>Aktivní</option>
                                        <option value="0" <?php echo ($aktivni == '0') ? 'selected' : ''; ?>>Neaktivní</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>

                    <input type="hidden"
                           id="id_firmy"
                           name="id_firmy"
                           value="<?php echo htmlspecialchars($idFirmy); ?>">
                </div>

                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light border rounded-3 px-4" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>Zavřít
                    </button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 shadow-sm">
                        <i class="bi bi-check-circle me-2"></i><?php echo $isEdit ? "Uložit změny" : "Vytvořit firmu"; ?>
                    </button>
                </div>

            </div>
        </div>
    </div>

    </form>
    <?php
}

function novy_zamestnanec_modal()
{
    global $conn;

    $sql = "SELECT MAX(CAST(os_cislo AS UNSIGNED))+1 AS maximalni_cislo 
            FROM zamestnanci 
            WHERE CAST(os_cislo AS UNSIGNED) IS NOT NULL";

    if (!($vysledek = mysqli_query($conn, $sql))) {
        die("Nelze provést dotaz</body></html>");
    }

    $polehodnot = array();

    while ($radek = mysqli_fetch_array($vysledek, MYSQLI_ASSOC)) {
        $polehodnot[] = $radek;
    }

    mysqli_free_result($vysledek);

    $noveOsCislo = !empty($polehodnot[0]['maximalni_cislo']) ? $polehodnot[0]['maximalni_cislo'] : '';
?>

<form name="zamestnanec" method="POST" action="zamestnanci.php?typ=savezamestnance">

<div class="modal fade" id="ModalNovyZam" tabindex="-1">
<div class="modal-dialog modal-dialog-centered modal-lg">

<div class="modal-content border-0 shadow rounded-4 modal-compact">

<!-- HEADER -->
<div class="modal-header text-white border-0"
     style="background: linear-gradient(135deg,#0d6efd,#0b5ed7);">
    
    <div class="d-flex align-items-center gap-2">
        <i class="bi bi-person-plus-fill"></i>
        <div>
            <div class="fw-semibold">Nový zaměstnanec</div>
            <div class="small text-white-50">Rychlé zadání údajů</div>
        </div>
    </div>

    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<!-- BODY -->
<div class="modal-body">

<!-- OSOBNÍ -->
<div class="card border-0 shadow-sm rounded-3 mb-2">
<div class="card-body">

<div class="fw-semibold small mb-2">
<i class="bi bi-person-badge text-primary me-1"></i> Osobní údaje
</div>

<div class="row g-2">
    <div class="col-md-3">
        <label class="form-label">Příjmení</label>
        <input type="text" class="form-control" name="prijmeni" required>
    </div>

    <div class="col-md-3">
        <label class="form-label">Jméno</label>
        <input type="text" class="form-control" name="jmeno" required>
    </div>

    <div class="col-md-2">
        <label class="form-label">Os. číslo</label>
        <input type="text" class="form-control" name="oscislo2" required>
    </div>

    <div class="col-md-4">
        <label class="form-label">Adresa</label>
        <input type="text" class="form-control" name="adresa" required>
    </div>
</div>

</div>
</div>

<!-- KONTAKT -->
<div class="card border-0 shadow-sm rounded-3 mb-2">
<div class="card-body">

<div class="fw-semibold small mb-2">
<i class="bi bi-telephone text-primary me-1"></i> Kontakt
</div>

<div class="row g-2">
    <div class="col-md-6">
        <label class="form-label">RFID</label>
        <input type="text" class="form-control" name="rfid" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Telefon</label>
        <input type="text" class="form-control" name="telefon">
    </div>
</div>

</div>
</div>

<!-- STAV -->
<div class="card border-0 shadow-sm rounded-3 mb-2">
<div class="card-body">

<div class="fw-semibold small mb-2">
<i class="bi bi-calendar-check text-primary me-1"></i> Stav
</div>

<div class="row g-2">
    <div class="col-md-4">
        <label class="form-label">Vstup</label>
        <input type="text" class="form-control" name="datepicker" value="<?php echo date("d.m.Y");?>" required>
    </div>

    <div class="col-md-4">
        <label class="form-label">Výstup</label>
        <input type="text" class="form-control" name="datepicker2" value="31.12.2023">
    </div>

    <div class="col-md-4">
        <label class="form-label">Nepřítomnost</label>
        <select class="form-select" name="nepritomnost">
            <option value="">Přítomen</option>
            <option value="DPN">DPN</option>
            <option value="DOV">Dovolená</option>
            <option value="ABS">Absence</option>
        </select>
    </div>
</div>

</div>
</div>

<!-- ZAŘAZENÍ -->
<div class="card border-0 shadow-sm rounded-3">
<div class="card-body">

<div class="fw-semibold small mb-2">
<i class="bi bi-diagram-3 text-primary me-1"></i> Zařazení
</div>

<div class="row g-2">
    <div class="col-md-5">
        <label class="form-label">Firma</label>
        <select class="form-select" name="firma">
            <option value="0">Nepřiřazen</option>

            <?php
            if ($_SESSION["typ"] == "5") {
                $sql = "SELECT firma,id FROM firmy WHERE aktivni=1 ORDER BY firma";
            } else {
                $sql = "SELECT firma,id FROM firmy WHERE aktivni=1 AND id IN (" . $_SESSION["firma"] . ") ORDER BY firma";
            }

            $vysledek = mysqli_query($conn, $sql);
            while ($r = mysqli_fetch_array($vysledek)) {
                echo "<option value='".$r["id"]."'>".htmlspecialchars($r["firma"])."</option>";
            }
            ?>
        </select>
    </div>

    <div class="col-md-5">
        <label class="form-label">Zastávka</label>
        <select class="form-select" name="nastup">
            <option value="0">Nepřiřazena</option>

            <?php
            $sql = "SELECT id,zastavka FROM nastupy ORDER BY zastavka";
            $vysledek = mysqli_query($conn, $sql);

            while ($r = mysqli_fetch_array($vysledek)) {
                echo "<option value='".$r["id"]."'>".htmlspecialchars($r["zastavka"])."</option>";
            }
            ?>
        </select>
    </div>

    <div class="col-md-2">
        <label class="form-label">Cílová</label>
        <input type="text" class="form-control" name="cilova">
    </div>
</div>

</div>
</div>

<input type="hidden" name="smena" value="N/A">
<input type="hidden" name="smena2" value="N/A">
<input type="hidden" name="oscislo" value="<?php echo htmlspecialchars($noveOsCislo); ?>">

</div>

<!-- FOOTER -->
<div class="modal-footer border-0">
    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
        <i class="bi bi-x"></i>
    </button>
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check"></i> Uložit
    </button>
</div>

</div>
</div>
</div>

</form>

<?php
}

function dopln_nabor($id)
{
    global $conn;

    // -------------------------------------------------
    // 1) Bezpečné ID + SELECT přes prepared statement
    // -------------------------------------------------
    $id = (int)$id;
    if ($id <= 0) {
        return; // nebo echo alert a return
    }

    $sql = "SELECT id, prijmeni, jmeno, telefon, adresa, dat_narozeni, stat, dat_evidence, zdroj_inzerce,
                   pozice, klient, klient2, souhlas, rekruter, vysledek, nastup, vystup, koordinator,
                   poznamka, duvod_ukonceni, boty, obleceni, telinfo, smena, nastupmisto, firma, cilova, oscislo
            FROM nabory
            WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die("Nelze připravit dotaz</body></html>");
    }
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $res = mysqli_stmt_get_result($stmt);
    if (!$res) {
        mysqli_stmt_close($stmt);
        die("Nelze provést dotaz</body></html>");
    }

    $row = mysqli_fetch_assoc($res);
    mysqli_free_result($res);
    mysqli_stmt_close($stmt);

    if (!$row) {
        return; // nenašlo se ID
    }

    // -------------------------------------------------
    // 2) Helpery pro čistější HTML + XSS ochrana
    // -------------------------------------------------
    $h = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');

    $opt = function ($value, $label, $selectedValue) use ($h) {
        $sel = ((string)$value === (string)$selectedValue) ? " selected" : "";
        return "<option value=\"{$h($value)}\" class=\"bg-primary-subtle\"{$sel}>{$h($label)}</option>";
    };

    $datum = "";
    if (!empty($row['nastup'])) {
        $dt = date_create($row['nastup']);
        if ($dt) {
            $tmp = date_format($dt, "d.m.Y");
            if ($tmp !== "30.11.-0001") $datum = $tmp;
        }
    }

    // -------------------------------------------------
    // 3) Načtení selectů (nastupy, firmy) – jednoduché dotazy
    //    (tady není user input, ale držíme lepší styl)
    // -------------------------------------------------
    $nastupy = [];
    $q = mysqli_query($conn, "SELECT id, zastavka FROM nastupy ORDER BY zastavka");
    if (!$q) die("Nelze provést dotaz</body></html>");
    while ($r = mysqli_fetch_assoc($q)) $nastupy[] = $r;
    mysqli_free_result($q);

    $firmy = [];
    $q = mysqli_query($conn, "SELECT id, firma FROM firmy WHERE aktivni = 1 ORDER BY firma");
    if (!$q) die("Nelze provést dotaz</body></html>");
    while ($r = mysqli_fetch_assoc($q)) $firmy[] = $r;
    mysqli_free_result($q);

    $cilove = [];

    $sql = "
        SELECT DISTINCT UPPER(TRIM(cilova)) AS cilova
        FROM zamestnanci
        WHERE aktivni = 1
        AND TRIM(cilova) <> ''
        ORDER BY cilova
    ";

    $q = mysqli_query($conn, $sql);
    if (!$q) {
        die("Nelze načíst cílové stanice</body></html>");
    }

    while ($r = mysqli_fetch_assoc($q)) {
        $cilove[] = $r['cilova'];
    }
    mysqli_free_result($q);

    // -------------------------------------------------
    // 4) Render
    // -------------------------------------------------
    ?>
    <form name="nabor_new" method="POST" action="informace.php?typ=updatedata">

        <div class="modal fade" id="ModalDoplnNabor<?= $h($row['id']); ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">

                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Doplnění informace k náboru</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">

                        <div class="row mt-3">
                            <div class="col-md-3">
                                <label class="form-label">Příjmení</label>
                                <input type="text" class="form-control bg-primary-subtle text-center"
                                       id="prijmeni" name="prijmeni" value="<?= $h($row['prijmeni']); ?>" required disabled>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Jméno</label>
                                <input type="text" class="form-control bg-primary-subtle text-center"
                                       id="jmeno" name="jmeno" value="<?= $h($row['jmeno']); ?>" required disabled>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Os. č.</label>
                                <input type="text" class="form-control bg-primary-subtle text-center"
                                       id="oscislo" name="oscislo" value="<?= $h($row['oscislo']); ?>">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Telefon</label>
                                <input type="text" class="form-control bg-primary-subtle text-center"
                                       id="telefon" name="telefon" value="<?= $h($row['telefon']); ?>" disabled>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-3">
                                <label class="form-label">Datum nástupu</label>
                                <input type="text" class="form-control bg-primary-subtle text-center common"
                                       id="dat_nastup" name="dat_nastup" placeholder="Vyber datum"
                                       value="<?= $h($datum); ?>" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Velikost bot</label>
                                <select class="form-select bg-primary-subtle text-center" name="boty" id="boty" required>
                                    <?= $opt("", "N/A", (string)($row['boty'] ?? "")); ?>
                                    <?php for ($i = 35; $i <= 50; $i++): ?>
                                        <?= $opt($i, $i, (string)($row['boty'] ?? "")); ?>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Velikost oblečení</label>
                                <select class="form-select bg-primary-subtle text-center" name="obleceni" id="obleceni" required>
                                    <?= $opt("", "N/A", (string)($row['obleceni'] ?? "")); ?>
                                    <?= $opt("S", "S", (string)($row['obleceni'] ?? "")); ?>
                                    <?= $opt("M", "M", (string)($row['obleceni'] ?? "")); ?>
                                    <?= $opt("L", "L", (string)($row['obleceni'] ?? "")); ?>
                                    <?= $opt("XL", "XL", (string)($row['obleceni'] ?? "")); ?>
                                    <?= $opt("XXL", "XXL", (string)($row['obleceni'] ?? "")); ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Telefonát</label>
                                <select class="form-select bg-primary-subtle text-center" name="telinfo" id="telinfo" required>
                                    <?= $opt("", "N/A", (string)($row['telinfo'] ?? "")); ?>
                                    <?= $opt("neproběhl", "neproběhl", (string)($row['telinfo'] ?? "")); ?>
                                    <?= $opt("proběhl", "proběhl", (string)($row['telinfo'] ?? "")); ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-3">
                                <label class="form-label">Směna</label>
                                <select class="form-select bg-primary-subtle text-center" name="smena" id="smena" required>
                                    <?= $opt("", "N/A", (string)($row['smena'] ?? "")); ?>
                                    <?= $opt("R", "R", (string)($row['smena'] ?? "")); ?>
                                    <?= $opt("O", "O", (string)($row['smena'] ?? "")); ?>
                                    <?= $opt("N", "N", (string)($row['smena'] ?? "")); ?>
                                </select>
                            </div>

                            <div class="col-md-9">
                                <label class="form-label">Nástupní místoxyx</label>
                                <select class="form-select bg-primary-subtle text-center" id="nastupmisto" name="nastupmisto">
                                    <option value="0" <?= ((int)($row['nastupmisto'] ?? 0) === 0) ? "selected" : ""; ?>>Zatím nepřiřazena</option>
                                    <?php foreach ($nastupy as $n): ?>
                                        <option value="<?= $h($n['id']); ?>" <?= ((int)$row['nastupmisto'] === (int)$n['id']) ? "selected" : ""; ?>>
                                            <?= $h($n['zastavka']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label">Firma</label>
                                <select class="form-select bg-primary-subtle" id="firma" name="firma">
                                    <option value="0" <?= ((int)($row['firma'] ?? 0) === 0) ? "selected" : ""; ?>>Zatím nepřiřazen</option>
                                    <?php foreach ($firmy as $f): ?>
                                        <option value="<?= $h($f['id']); ?>" <?= ((int)$row['firma'] === (int)$f['id']) ? "selected" : ""; ?>>
                                            <?= $h($f['firma']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Cílová stanice</label>
                                <select class="form-select bg-primary-subtle text-center" id="cilova" name="cilova">
                                    <option value="">— nevybráno —</option>

                                    <?php
                                    $aktualniCilova = strtoupper(trim((string)$row['cilova']));

                                    foreach ($cilove as $c) {
                                        $selected = ($c === $aktualniCilova) ? 'selected' : '';
                                        echo "<option value=\"{$h($c)}\" {$selected}>{$h($c)}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                        </div>

                        <input type="hidden" id="idnabor" name="idnabor" value="<?= $h($row['id']); ?>">

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary mb-3">Ulož změny</button>
                        <button type="button" class="btn btn-secondary mb-3" data-bs-dismiss="modal">Zavřít</button>
                    </div>

                </div>
            </div>
        </div>

    </form>
    <?php
}


function dopln_naborxxx($id)
{    

    global $conn;

    $sql = "select id,prijmeni,jmeno,telefon,adresa,dat_narozeni,stat,dat_evidence,zdroj_inzerce,pozice,klient,klient2,souhlas,rekruter,vysledek,nastup,vystup,koordinator,poznamka,duvod_ukonceni,boty,obleceni,telinfo,smena,nastupmisto,firma,cilova,oscislo from nabory where id='" . $id . "'";
    
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }

    $polehodnot = array();

    while ($radek = mysqli_fetch_array($vysledek, MYSQLI_ASSOC)) {
        $polehodnot[] = $radek;
    }
    
    mysqli_free_result($vysledek);
    ?>

    <form name="nabor_new" method="POST" action="informace.php?typ=updatedata">

    <!-- Modal -->
    <div class="modal fade" id="ModalDoplnNabor<?php echo $polehodnot[0]['id'];?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
   
    <div class="modal-dialog modal-dialog-centered modal-xl">
        
        <div class="modal-content">

            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Doplnění informace k náboru</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">

                <div class="row mt-3">
                    <div class="col-md-3">
                        <label class="form-label">Příjmení</label>
                        <input type="text" class="form-control bg-primary-subtle text-center" id="prijmeni" name="prijmeni" value="<?php echo ($id == '') ? "" : $polehodnot[0]['prijmeni'];?>" required disabled>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Jméno</label>  
                        <input type="text" class="form-control bg-primary-subtle text-center" id="jmeno" name="jmeno" value="<?php echo ($id == '') ? "" : $polehodnot[0]['jmeno'];?>" required disabled>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Os. č.</label>  
                        <input type="text" class="form-control bg-primary-subtle text-center" id="oscislo" name="oscislo" value="<?php echo ($id == '') ? "" : $polehodnot[0]['oscislo'];?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Telefon</label>  
                        <input type="text" class="form-control bg-primary-subtle text-center" id="telefon" name="telefon" value="<?php echo ($id == '') ? "" : $polehodnot[0]['telefon'];?>" disabled>
                    </div>
                </div>

                <div class="row mt-3">

                    <?php
                    $datum = date_format(date_create($polehodnot[0]['nastup']),"d.m.Y");
                    if ($datum == "30.11.-0001")
                    {
                        $datum = "";
                    }
                    ?>

                    <div class="col-md-3">
                        <label class="form-label">Datum nástupu</label>  
                        <input type="text" class="form-control bg-primary-subtle text-center common" id="dat_nastup" name="dat_nastup"  placeholder="Vyber datum" value="<?php echo $datum;?>" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Velikost bot</label>  
                        <select class="form-select bg-primary-subtle text-center" name="boty" id="boty" required>
                            <?php
                                echo (isset($polehodnot) && $polehodnot[0]['boty'] == '') ? "<option value='' class='bg-primary-subtle' selected>N/A</option>" : "<option value='' class='bg-primary-subtle'>N/A</option>";

                                for ($i = 35; $i <= 50; $i++) 
                                {
                                    echo (isset($polehodnot) && $polehodnot[0]['boty'] == $i) ? "<option value='" . $i . "' class='bg-primary-subtle' selected>" . $i . "</option>" : "<option value='" . $i . "' class='bg-primary-subtle'>" . $i . "</option>";
                                }
                            ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Velikost oblečení</label>  
                        <select class="form-select bg-primary-subtle text-center" name="obleceni" id="obleceni" required>
                            <?php
                                echo (isset($polehodnot) && $polehodnot[0]['obleceni'] == '') ? "<option value='' class='bg-primary-subtle' selected>N/A</option>" : "<option value='N/A' class='bg-primary-subtle'>N/A</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['obleceni'] == 'S') ? "<option value='S' class='bg-primary-subtle' selected>S</option>" : "<option value='S' class='bg-primary-subtle'>S</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['obleceni'] == 'M') ? "<option value='M' class='bg-primary-subtle' selected>M</option>" : "<option value='M' class='bg-primary-subtle'>M</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['obleceni'] == 'L') ? "<option value='L' class='bg-primary-subtle' selected>S</option>" : "<option value='L' class='bg-primary-subtle'>L</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['obleceni'] == 'XL') ? "<option value='XL' class='bg-primary-subtle' selected>XL</option>" : "<option value='XL' class='bg-primary-subtle'>XL</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['obleceni'] == 'XXL') ? "<option value='XXL' class='bg-primary-subtle' selected>XXL</option>" : "<option value='XXL' class='bg-primary-subtle'>XXL</option>";
                            ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Telefonát</label>  
                        <select class="form-select bg-primary-subtle text-center" name="telinfo" id="telinfo" required>
                            <?php
                                echo (isset($polehodnot) && $polehodnot[0]['telinfo'] == '') ? "<option value='' class='bg-primary-subtle' selected>N/A</option>" : "<option value='' class='bg-primary-subtle'>N/A</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['telinfo'] == 'neproběhl') ? "<option value='neproběhl' class='bg-primary-subtle' selected>neproběhl</option>" : "<option value='neproběhl' class='bg-primary-subtle'>neproběhl</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['telinfo'] == 'proběhl') ? "<option value='proběhl' class='bg-primary-subtle' selected>proběhl</option>" : "<option value='proběhl' class='bg-primary-subtle'>proběhl</option>";
                            ?>
                        </select>
                    </div>

                </div>

                <div class="row mt-3">

                    <div class="col-md-3">
                        <label class="form-label">Směna</label>
                        <select class="form-select bg-primary-subtle text-center" name="smena" id="smena" required>
                            <?php
                                echo (isset($polehodnot) && $polehodnot[0]['smena'] == '') ? "<option value='' class='bg-primary-subtle' selected>N/A</option>" : "<option value='' class='bg-primary-subtle'>N/A</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['smena'] == 'R') ? "<option value='R' class='bg-primary-subtle' selected>R</option>" : "<option value='R' class='bg-primary-subtle'>R</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['smena'] == 'O') ? "<option value='O' class='bg-primary-subtle' selected>O</option>" : "<option value='O' class='bg-primary-subtle'>O</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['smena'] == 'N') ? "<option value='N' class='bg-primary-subtle' selected>N</option>" : "<option value='N' class='bg-primary-subtle'>N</option>";
                            ?>
                        </select>
                    </div>

                    <div class="col-md-9">

                        <label class="form-label">Nástupní místoxyx</label>

                        <select class="form-select bg-primary-subtle text-center" id="nastupmisto" name="nastupmisto" aria-label="Floating label select example">
                        <option value="0" selected>Zatím nepřiřazena</option>

                        <?php
                        $sql = "select id,zastavka from nastupy order by zastavka";

                        if (!($vysledek = mysqli_query($conn, $sql)))
                        {
                        die("Nelze provést dotaz</body></html>");
                        }            

                        while ($radek = mysqli_fetch_array($vysledek))
                        {   
                            if ($polehodnot[0]['nastupmisto'] == $radek["id"])
                            {   ?>
                                    <option value="<?php echo $radek["id"];?>" selected><?php echo $radek["zastavka"];?></option>
                                <?php
                            }
                            else
                            {   ?>
                                    <option value="<?php echo $radek["id"];?>"><?php echo $radek["zastavka"];?></option>
                                <?php
                            }     
                                
                        }

                        mysqli_free_result($vysledek);
                        ?>
                        
                        </select>

                    </div>                   

                </div>

                <div class="row mt-3">

                    <div class="col-md-6">
                            
                        <label class="form-label">Firma</label>
                        <select class="form-select bg-primary-subtle" id="firma" name="firma" aria-label="Floating label select example">
                            <option value="0" selected>Zatím nepřiřazen</option>

                            <?php
                            $sql = "select firma,id,aktivni from firmy where aktivni='1' order by firma";           

                            if (!($vysledek = mysqli_query($conn, $sql)))
                            {
                            die("Nelze provést dotaz</body></html>");
                            }

                            while ($radek = mysqli_fetch_array($vysledek))
                            {   
                                echo ($radek['id'] == $polehodnot[0]['firma']) ? "<option value='" . $radek['id'] . "' class='bg-primary-subtle' selected>" . $radek['firma'] . "</option>" : "<option value='" . $radek['id'] . "' class='bg-primary-subtle'>" . $radek['firma'] . "</option>";
                            }

                            mysqli_free_result($vysledek);
                            ?>
                            
                        </select>
                    </div>

                    <div class="col-md-6">

                        <label class="form-label">Cílová stanice</label>  
                        <input type="text" class="form-control bg-primary-subtle text-center" id="cilova" name="cilova" value="<?php echo ($id == '') ? "" : $polehodnot[0]['cilova'];?>">
                    </div>
                                    

                </div>
 
                <input type="hidden" class="form-control" id="idnabor" name="idnabor" placeholder="" value="<?php echo $polehodnot[0]['id'];?>">
                
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary mb-3">Ulož změny</button>
                <button type="button" class="btn btn-secondary mb-3" data-bs-dismiss="modal">Zavřít</button>
            </div>

        </div>

    </div>
    </div>

    </form>

    <?php
}

function zjisti_osobni_cislo()
{
    global $conn;

    $sql = "SELECT MAX(CAST(os_cislo AS UNSIGNED))+1 AS maximalni_cislo FROM zamestnanci WHERE CAST(os_cislo AS UNSIGNED) IS NOT NULL";
    
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }   
    
    while ($radek = mysqli_fetch_array($vysledek)) 
    {
        $hodnota = $radek['maximalni_cislo'];
    }
    
    mysqli_free_result($vysledek);
    
    return $hodnota;

}

function zjisti_firmu_z_klienta($klient)
{
    global $conn;

    $sql = "select firma from zamestnanci where cilova='" . $klient . "' and firma > 0 order by id desc limit 1";
    
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }   
    
    while ($radek = mysqli_fetch_array($vysledek)) 
    {
        $hodnota = $radek['firma'];
    }
    
    mysqli_free_result($vysledek);
    
    return $hodnota;

}

function prevod_data($datum,$format)
{
    $date=date_create($datum);
    if ($format == 1)
    {
        $datum=date_format($date,"d.m.Y");

        if ($datum == "30.11.-0001")
        {
            $datum = "";
        }
    }
    elseif ($format == 2)
    {
        $datum=date_format($date,"d.m.Y H:i:s");
    }
    elseif ($format == 3)
    {
        $datum=date_format($date,"H:i:s");

        if ($datum == "00:00:00")
        {
            $datum = "";
        }
    }

    return $datum;
}

function kontrola_dpn($id)
{    

    global $conn;

    $sql = "select id,prijmeni,jmeno,adresa,cilova,dpn_od from zamestnanci where id='" . $id . "'";
    
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }

    $polehodnot = array();

    while ($radek = mysqli_fetch_array($vysledek, MYSQLI_ASSOC)) {
        $polehodnot[] = $radek;
    }
    
    mysqli_free_result($vysledek);
    ?>

    <form name="zamestnanci" method="POST" action="kontroly.php?typ=pridejkontrolu">

    <!-- Modal -->
    <div class="modal fade" id="ModalKontrola<?php echo $polehodnot[0]['id'];?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
   
    <div class="modal-dialog modal-dialog-centered modal-xl">
        
        <div class="modal-content">

            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Doplnění ke kontrole zaměstnance na DPN</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">

                <div class="row mt-3">
                    <div class="col col-md-5">
                        <label class="form-label">Příjmení</label>
                        <input type="text" class="form-control bg-primary-subtle" id="prijmeni" name="prijmeni" value="<?php echo $polehodnot[0]['prijmeni'];?>" required disabled>
                    </div>
                    
                    <div class="col col-md-5">
                        <label class="form-label">Jméno</label>  
                        <input type="text" class="form-control bg-primary-subtle" id="jmeno" name="jmeno" value="<?php echo $polehodnot[0]['jmeno'];?>" required disabled>
                    </div>

                    <div class="col col-md-2">
                        <label class="form-label">Zakázka</label>  
                        <input type="text" class="form-control bg-primary-subtle" id="jmeno" name="jmeno" value="<?php echo $polehodnot[0]['cilova'];?>" required disabled>
                    </div>

                </div>

                <div class="row mt-3">

                    <div class="col col-md-3">
                        <label class="form-label">DPN Od</label>  
                        <input type="text" class="form-control bg-primary-subtle text-center common" id="dpn_od" name="dpn_od"  placeholder="Vyber datum" value="<?php echo prevod_data($polehodnot[0]['dpn_od'],1);?>" required>
                    </div>                          

                    <div class="col col-md-3">
                        <label class="form-label">DPN Do</label>  
                        <input type="text" class="form-control bg-primary-subtle text-center common" id="dpn_do" name="dpn_do"  placeholder="Vyber datum" value="" required>
                    </div>

                    <div class="col col-md-6">

                        <label class="form-label">Řidič / autobus</label>

                        <select class="form-select bg-primary-subtle" id="ridic" name="ridic" aria-label="Floating label select example" required>
                        <option value="0" selected>Zatím nepřiřazena</option>

                        <?php
                        $sql = "select uzivatele.id,uzivatel,autobus,auta.oznaceni from uzivatele left join auta on uzivatele.autobus = auta.id where aktivni='1' and typ='2'";

                        if (!($vysledek = mysqli_query($conn, $sql)))
                        {
                        die("Nelze provést dotaz</body></html>");
                        }            

                        while ($radek = mysqli_fetch_array($vysledek))
                        {   
                            ?>
                                <option value="<?php echo $radek["id"];?>"><?php echo $radek['uzivatel'] . " - " . $radek['oznaceni'];?></option>
                            <?php
                        }

                        mysqli_free_result($vysledek);
                        ?>

                        </select>

                    </div>

                </div>

                <div class="row mt-3">
          
                    <div class="col col-md-12">
                        <label class="form-label">Adresa pro kontrolu v době PN</label>
                        <input type="text" class="form-control bg-primary-subtle" id="adresa" name="adresa" value="<?php echo $polehodnot[0]['adresa'];?>" required>
                    </div>

                </div>              
 
                <input type="hidden" class="form-control" id="id_zam" name="id_zam" placeholder="" value="<?php echo $id;?>">
                
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary mb-3">Přidej ke kontrole</button>
                <button type="button" class="btn btn-secondary mb-3" data-bs-dismiss="modal">Zavřít</button>
            </div>

        </div>

    </div>
    </div>

    </form>

    <?php
}

function over_kontrolu($id_emp)
{
    global $conn;

    $hodnota = "";

    $sql = "select vysledek from kontroly where id_zam='" . $id_emp . "' and kontrola='0000-00-00'";
    
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }   
    
    while ($radek = mysqli_fetch_array($vysledek)) 
    {
        $hodnota = $radek['vysledek'];
    }
    
    mysqli_free_result($vysledek);
    
    return $hodnota;

}

function get_user_from_id($id)
{
    global $conn;
    $hodnota = "";

    $sql = "select uzivatel from uzivatele where id='" . $id . "'";
    
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            

    while ($radek = mysqli_fetch_array($vysledek))
    {  
        $hodnota = $radek['uzivatel'];
    }
    
    mysqli_free_result($vysledek);
        
    return $hodnota;

}

function pocet_kontrol_dpn_user($id)
{
    global $conn;
    $hodnota = "";

    $sql = "select count(*) as pocet from kontroly where provedl='" . $id . "' and kontrola='0000-00-00'";
    
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            

    while ($radek = mysqli_fetch_array($vysledek))
    {  
        $hodnota = $radek['pocet'];
    }
    
    mysqli_free_result($vysledek);
        
    return $hodnota;

}

function kontrola_dpn_ridic($id)
{    

    global $conn;

    $sql = "select kontroly.id,id_zam,kontroly.dpn_od,kontroly.dpn_do,zadal,kontroly.adresa,jmeno,prijmeni from kontroly left join zamestnanci on kontroly.id_zam = zamestnanci.id where kontroly.id='" . $id . "'";
    
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }

    $polehodnot = array();

    while ($radek = mysqli_fetch_array($vysledek, MYSQLI_ASSOC)) {
        $polehodnot[] = $radek;
    }
    
    mysqli_free_result($vysledek);
    ?>

    <form name="zamestnanci" method="POST" action="">

    <!-- Modal -->
    <div class="modal fade" id="ModalKontrolaRidic<?php echo $polehodnot[0]['id'];?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
   
    <div class="modal-dialog modal-dialog-centered modal-xl">
        
        <div class="modal-content">

            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Vlož výsledek provedené kontroly</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">

                <div class="row mt-3">
                    <div class="col col-md-6">
                        <label class="form-label">Jméno Příjmení</label>
                        <input type="text" class="form-control bg-primary-subtle" id="prijmeni" name="prijmeni" value="<?php echo $polehodnot[0]['prijmeni'];?> <?php echo $polehodnot[0]['jmeno'];?>" required disabled>
                    </div>
                    
                    <div class="col col-md-6">
                        <label class="form-label">DPN</label>  
                        <input type="text" class="form-control bg-primary-subtle text-center" id="dpn" name="dpn"  value="<?php echo prevod_data($polehodnot[0]['dpn_od'],1);?> - <?php echo prevod_data($polehodnot[0]['dpn_do'],1);?>" required disabled>
                    </div>    
                          

                </div>

                <div class="row mt-3">
          
                    <div class="col col-md-12">
                        <label class="form-label">Adresa pro kontrolu v době PN</label>
                        <input type="text" class="form-control bg-primary-subtle" id="adresa" name="adresa" value="<?php echo $polehodnot[0]['adresa'];?>" required disabled>
                    </div>

                </div>              
 
                <input type="hidden" class="form-control" id="id_kontroly" name="id_kontroly" value="<?php echo $id;?>">
                
            </div>

            <div class="row mt-6 mb-3">
          
                <div class="col col-md-6">
                    <a type="button" class="btn btn-outline-danger btn-lg" href="main.php?typ=dpn_ng&id=<?php echo $id;?>">NEZASTIŽEN NA ADRESE</button></a>
                </div>

                <div class="col col-md-6">
                    <a type="button" class="btn btn-outline-success btn-lg" href="main.php?typ=dpn_ok&id=<?php echo $id;?>">ZASTIŽEN NA ADRESE</button></a>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary mb-3" data-bs-dismiss="modal">Zavřít</button>
            </div>

        </div>

    </div>
    </div>

    </form>

    <?php
}

function edit_zamestnanec_modal($id)
{
    global $conn;

    $stmt = $conn->prepare("SELECT id, os_cislo, os_cislo_klient, prijmeni, jmeno, rfid, nastup, telefon, adresa, firma, smena, nepritomnost, smena2, cilova, vstup, vystup, radneukoncen, dpn_od, smennost, email FROM zamestnanci WHERE id=?");
    $stmt->bind_param("i", $id);

    if (!$stmt->execute()) {
        die("Nelze provést dotaz</body></html>");
    }

    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $id = $row["id"];
        $prijmeni = $row["prijmeni"];
        $jmeno = $row["jmeno"];
        $os_cislo = $row["os_cislo"];
        $os_cislo_kl = $row["os_cislo_klient"];
        $rfid = $row["rfid"];
        $nastup = $row["nastup"];
        $telefon = $row["telefon"];
        $adresa = $row["adresa"];
        $firma = $row["firma"];
        $smena = $row["smena"];
        $smena2 = $row["smena2"];
        $nepritomnost = $row["nepritomnost"];
        $cilova = $row["cilova"];
        $ukoncen = $row["radneukoncen"];
        $smennost = $row["smennost"];
        $email = $row["email"];

        $vstup  = ($row["vstup"] !== "0000-00-00" && !empty($row["vstup"])) ? DateTime::createFromFormat('Y-m-d', $row["vstup"]) : null;
        $vystup = ($row["vystup"] !== "0000-00-00" && !empty($row["vystup"])) ? DateTime::createFromFormat('Y-m-d', $row["vystup"]) : null;
        $dpn_od = ($row["dpn_od"] !== "0000-00-00" && !empty($row["dpn_od"])) ? DateTime::createFromFormat('Y-m-d', $row["dpn_od"]) : new DateTime('now', new DateTimeZone('Europe/Prague'));
    }

    $result->free();
    $stmt->close();

    $cilove = [];

    $sql = "
        SELECT DISTINCT UPPER(TRIM(cilova)) AS cilova
        FROM zamestnanci
        WHERE aktivni = 1
          AND TRIM(cilova) <> ''
        ORDER BY cilova
    ";

    $q = mysqli_query($conn, $sql);
    if (!$q) {
        die("Nelze načíst cílové stanice</body></html>");
    }

    while ($r = mysqli_fetch_assoc($q)) {
        $cilove[] = $r['cilova'];
    }
    mysqli_free_result($q);

    $aktualniCilova = strtoupper(trim((string)$cilova));
    ?>

    <div class="modal fade" id="ModalEditZam" tabindex="-1" aria-labelledby="ModalEditZamLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow rounded-4 modal-compact">

                <div class="modal-header text-white border-0"
                     style="background: linear-gradient(135deg,#0d6efd,#0b5ed7);">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-pencil-square"></i>
                        <div>
                            <div class="fw-semibold" id="ModalEditZamLabel">Editace zaměstnance</div>
                            <div class="small text-white-50">Úprava personálních a kontaktních údajů</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <form action="zamestnanci.php?typ=updatezamestnance" method="post">

                        <div class="card border-0 shadow-sm rounded-3 mb-2">
                            <div class="card-body">
                                <div class="fw-semibold small mb-2">
                                    <i class="bi bi-person-badge text-primary me-1"></i> Osobní údaje
                                </div>

                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label for="prijmeni" class="form-label">Příjmení</label>
                                        <input type="text" class="form-control" id="prijmeni" name="prijmeni"
                                               value="<?php echo htmlspecialchars($prijmeni ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="jmeno" class="form-label">Jméno</label>
                                        <input type="text" class="form-control" id="jmeno" name="jmeno"
                                               value="<?php echo htmlspecialchars($jmeno ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="oscislo" class="form-label">Os. č. Premier</label>
                                        <input type="text" class="form-control" id="oscislo" name="oscislo"
                                               value="<?php echo htmlspecialchars($os_cislo ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="oscisloklient" class="form-label">Os. č. klient</label>
                                        <input type="text" class="form-control" id="oscisloklient" name="oscisloklient"
                                               value="<?php echo htmlspecialchars($os_cislo_kl ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-3 mb-2">
                            <div class="card-body">
                                <div class="fw-semibold small mb-2">
                                    <i class="bi bi-envelope-paper text-primary me-1"></i> Kontakt a přístup
                                </div>

                                <div class="row g-2">
                                    <div class="col-md-8">
                                        <label for="adresa" class="form-label">Adresa</label>
                                        <input type="text" class="form-control" id="adresa" name="adresa"
                                               value="<?php echo htmlspecialchars($adresa ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="text" class="form-control" id="email" name="email"
                                               value="<?php echo htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>

                                    <div class="col-md-4">
                                        <label for="rfid" class="form-label">RFID</label>
                                        <input type="text" class="form-control" id="rfid" name="rfid"
                                               value="<?php echo htmlspecialchars($rfid ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="telefon" class="form-label">Telefon</label>
                                        <input type="text" class="form-control" id="telefon" name="telefon"
                                               value="<?php echo htmlspecialchars($telefon ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>

                                    <div class="col-md-4">
                                        <label for="nastup" class="form-label">Zastávka</label>
                                        <select class="form-select" id="nastup" name="nastup">
                                            <option value="0" <?php echo ((int)$nastup === 0) ? "selected" : ""; ?>>Zatím nepřiřazena</option>
                                            <?php
                                            $sql = "SELECT id, zastavka FROM nastupy ORDER BY zastavka";
                                            $vysledek = mysqli_query($conn, $sql) or die("Nelze provést dotaz");

                                            while ($radek = mysqli_fetch_assoc($vysledek)) {
                                                $selected = ((int)$radek['id'] === (int)$nastup) ? "selected" : "";
                                                echo "<option value='" . (int)$radek['id'] . "' $selected>" . htmlspecialchars($radek['zastavka'], ENT_QUOTES, 'UTF-8') . "</option>";
                                            }

                                            mysqli_free_result($vysledek);
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <input type="hidden" name="smena" id="smena" value="<?php echo htmlspecialchars($smena ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="smena2" id="smena2" value="<?php echo htmlspecialchars($smena2 ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-3 mb-2">
                            <div class="card-body">
                                <div class="fw-semibold small mb-2">
                                    <i class="bi bi-calendar-check text-primary me-1"></i> Pracovní stav
                                </div>

                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <label for="datepicker" class="form-label">Vstup</label>
                                        <input type="text" class="form-control datepicker" id="datepicker" name="datepicker"
                                               placeholder="Vyber datum"
                                               value="<?php echo $vstup ? htmlspecialchars($vstup->format('d.m.Y'), ENT_QUOTES, 'UTF-8') : ''; ?>"
                                               required>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="datepicker2" class="form-label">Výstup</label>
                                        <div class="input-group">
                                            <input type="text"
                                                   class="form-control datepicker"
                                                   id="datepicker2"
                                                   name="datepicker2"
                                                   placeholder="Vyber datum"
                                                   value="<?php echo ($vystup && $vystup->format('Y-m-d') != '2099-12-31') ? htmlspecialchars($vystup->format('d.m.Y'), ENT_QUOTES, 'UTF-8') : ''; ?>"
                                                   <?php echo ($vystup && $vystup->format('Y-m-d') != '2099-12-31') ? '' : 'disabled'; ?>
                                                   autocomplete="off" autocorrect="off">
                                            <div class="input-group-text">
                                                <input type="checkbox"
                                                       id="checkbox_vystup"
                                                       name="checkbox_vystup"
                                                       <?php echo ($vystup && $vystup->format('Y-m-d') != '2099-12-31') ? 'checked' : ''; ?>>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="nepritomnost" class="form-label">Nepřítomnost</label>
                                        <select class="form-select" id="nepritomnost" name="nepritomnost">
                                            <?php
                                            $moznosti = [
                                                ''     => 'Přítomen',
                                                'DPN'  => 'DPN',
                                                'OČR'  => 'OČR',
                                                'DOV'  => 'Dovolená',
                                                'ABS'  => 'Absence'
                                            ];

                                            foreach ($moznosti as $hodnota => $text) {
                                                $selected = ((string)$nepritomnost === (string)$hodnota) ? 'selected' : '';
                                                echo "<option value='" . htmlspecialchars($hodnota, ENT_QUOTES, 'UTF-8') . "' $selected>" . htmlspecialchars($text, ENT_QUOTES, 'UTF-8') . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="datepicker4" class="form-label">DPN od</label>
                                        <input type="text"
                                               class="form-control common"
                                               id="datepicker4"
                                               name="datepicker4"
                                               placeholder="Vyber datum"
                                               value="<?php echo ($nepritomnost == '') ? "" : htmlspecialchars($dpn_od->format('d.m.Y'), ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-body">
                                <div class="fw-semibold small mb-2">
                                    <i class="bi bi-diagram-3 text-primary me-1"></i> Zařazení
                                </div>

                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label for="firma" class="form-label">Firma</label>
                                        <select class="form-select" id="firma" name="firma">
                                            <option value="0">Zatím nepřiřazen</option>
                                            <?php
                                            $sql = "SELECT firma, id, aktivni FROM firmy WHERE aktivni='1' ORDER BY firma";

                                            if (!($vysledek = mysqli_query($conn, $sql))) {
                                                die("Nelze provést dotaz</body></html>");
                                            }

                                            while ($radek = mysqli_fetch_array($vysledek)) {
                                                $selected = ((int)$radek['id'] === (int)$firma) ? "selected" : "";
                                                echo "<option value='" . (int)$radek['id'] . "' $selected>" . htmlspecialchars($radek['firma'], ENT_QUOTES, 'UTF-8') . "</option>";
                                            }

                                            mysqli_free_result($vysledek);
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="cilova" class="form-label">Cílová stanice</label>
                                        <select class="form-select" id="cilova" name="cilova">
                                            <option value="">— nevybráno —</option>
                                            <?php foreach ($cilove as $c): ?>
                                                <option value="<?php echo htmlspecialchars($c, ENT_QUOTES, 'UTF-8'); ?>"
                                                    <?php echo ($c === $aktualniCilova) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($c, ENT_QUOTES, 'UTF-8'); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="smennost" class="form-label">Směnnost</label>
                                        <select class="form-select" id="smennost" name="smennost">
                                            <?php
                                            $moznosti = ['3SM', '3SM 8h', '3SM 12h', '4SM A', '4SM B', '4SM C', '4SM D', '4SM A 12h', '4SM B 12h', '4SM C 12h', '4SM D 12h', '4SM A 8h', '4SM B 8h', '4SM C 8h', '4SM D 8h'];
                                            foreach ($moznosti as $moznost) {
                                                $selected = ((string)$smennost === (string)$moznost) ? 'selected' : '';
                                                echo "<option value='" . htmlspecialchars($moznost, ENT_QUOTES, 'UTF-8') . "' $selected>" . htmlspecialchars($moznost, ENT_QUOTES, 'UTF-8') . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" id="id_zam" name="id_zam" value="<?php echo (int)$id; ?>">
                        <input type="hidden" name="redirect_from" value="<?php echo htmlspecialchars($_SESSION['typ_modal'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

                        <div class="modal-footer border-0 px-0 pt-3 pb-0">
                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                                <i class="bi bi-x"></i> Zavřít
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check2-circle"></i> Uložit změnu
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('shown.bs.modal', function (event) {
        const modal = event.target;
        if (!modal || modal.id !== 'ModalEditZam') return;

        function initDateInput(inputId, checkboxId) {
            const dateInput = modal.querySelector('#' + inputId);
            const checkbox = modal.querySelector('#' + checkboxId);
            if (!dateInput || !checkbox) return;

            if (typeof $ !== 'undefined' && typeof $(dateInput).datepicker === 'function') {
                $(dateInput).datepicker({
                    format: 'dd.mm.yyyy',
                    autoclose: true,
                    todayHighlight: true,
                    container: 'body'
                });

                if (dateInput.value) {
                    const parts = dateInput.value.split('.');
                    if (parts.length === 3) {
                        const existingDate = new Date(parts[2], parts[1] - 1, parts[0]);
                        $(dateInput).datepicker('setDate', existingDate);
                        dateInput.disabled = false;
                    }
                }
            }

            dateInput.disabled = !checkbox.checked;

            if (!checkbox.dataset.bound) {
                checkbox.addEventListener('change', function () {
                    if (checkbox.checked) {
                        dateInput.disabled = false;
                        if (typeof $ !== 'undefined' && typeof $(dateInput).datepicker === 'function') {
                            const today = new Date();
                            $(dateInput).datepicker('setDate', today);
                        }
                    } else {
                        dateInput.disabled = true;
                        dateInput.value = '';
                        if (typeof $ !== 'undefined' && typeof $(dateInput).datepicker === 'function') {
                            $(dateInput).datepicker('clearDates');
                        }
                    }
                });
                checkbox.dataset.bound = '1';
            }
        }

        initDateInput('datepicker2', 'checkbox_vystup');
    });
    </script>

    <?php
}

function addDaysToDate($date, $days) {
    // Převedení data na objekt typu DateTime
    $dateObject = new DateTime($date);
    
    // Přidání zadaného počtu dní
    $dateObject->modify("+$days days");
    
    // Vrácení nového data jako řetězec
    return $dateObject->format('Y-m-d');
}

function vyrob_kalendar($year, $month, $id_zam)
{
    $year = (int)$year;
    $month = (int)$month;
    $id_zam = (int)$id_zam;

    if (!checkdate($month, 1, $year)) {
        $month = (int)date('n');
        $year = (int)date('Y');
    }

    $mesiceNazvy = [
        1 => 'Leden', 2 => 'Únor', 3 => 'Březen', 4 => 'Duben',
        5 => 'Květen', 6 => 'Červen', 7 => 'Červenec', 8 => 'Srpen',
        9 => 'Září', 10 => 'Říjen', 11 => 'Listopad', 12 => 'Prosinec'
    ];
    $nazevMesice = ($mesiceNazvy[$month] ?? (string)$month) . ' ' . $year;

    ?>
    <div class="container-fluid">
        <!-- HLAVNÍ MODAL -->
        <div class="modal fade" id="kalendar_dochazka" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
            data-rok="<?php echo (int)$year; ?>" data-mesic="<?php echo (int)$month; ?>" data-zam="<?php echo (int)$id_zam; ?>"
            data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title" id="exampleModalLabel"><?php echo get_name_from_id_zam($id_zam); ?></h5>
                            <div class="small text-muted"><?php echo htmlspecialchars($nazevMesice, ENT_QUOTES, 'UTF-8'); ?></div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="vyrob_modal_nav(-1)">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="vyrob_modal_nav(1)">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>

                    <div class="modal-body">
                        <form name="kalendar" method="POST" action="report4.php?typ=savechange">
                            <table class="table text-center align-middle">
                        
                            <thead>
                                <tr>
                                    <th>Týden</th>
                                    <th>Pondělí</th>
                                    <th>Úterý</th>
                                    <th>Středa</th>
                                    <th>Čtvrtek</th>
                                    <th>Pátek</th>
                                    <th>Sobota</th>
                                    <th>Neděle</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            global $conn;

                            $first_day = strtotime("$year-$month-01");
                            $daysInMonth = (int)date("t", $first_day);
                            $startWeekday = (int)date("N", $first_day); // 1=pondělí .. 7=neděle

                            $smeny = ['R','O','N'];
                            $nepritomnosti_warning = ['DPN','OČR','ABS','LEK'];
                            $nepritomnosti_other = ['DOV','NAR','PRO','NEPV','NAHV','SVA', 'VOL'];
                            $tydenni_smeny = ['R','O','N', 'X'];

                            $start_calendar = $first_day - ($startWeekday - 1) * 86400;
                            $last_day_of_month = strtotime(sprintf('%04d-%02d-%02d', $year, $month, $daysInMonth));

                            $current = $start_calendar;
                                while ($current <= $last_day_of_month) {

                                    $currentDate = new DateTime();
                                    $currentDate->setTimestamp($current);

                                    // číslo týdne
                                    $weekNumber = (int)$currentDate->format('W');

                                    // pondělí aktuálního týdne
                                    $pondeli = clone $currentDate;
                                    $pondeli->modify('-' . ($currentDate->format('N') - 1) . ' days');
                                    
                                    // správný rok pro tento týden
                                    $realIsoYear = (int)$pondeli->format('o');

                                    echo "<tr>";

                                    // --- Načtení výchozích hodnot směny + trasy podle ISO roku a čísla týdne ---
                                    $vychozi_smena = '';
                                    $vychozi_trasa = '';

                                    $stmt = $conn->prepare("SELECT smena, trasa FROM plan_smen WHERE rok=? AND tyden=? AND jmeno=?");
                                    $stmt->bind_param("iis", $realIsoYear, $weekNumber, $id_zam);
                                    $stmt->execute();
                                    $stmt->bind_result($vychozi_smena, $vychozi_trasa);
                                    $stmt->fetch();
                                    $stmt->close();

                                    // --- Zjištění nástupu zaměstnance ---
                                    $stmtN = $conn->prepare("SELECT nastup FROM zamestnanci WHERE id = ?");
                                    $stmtN->bind_param("i", $id_zam);
                                    $stmtN->execute();
                                    $stmtN->bind_result($nastup_id);
                                    $stmtN->fetch();
                                    $stmtN->close();

                                    // --- Buňka s číslem týdne + směnou + trasou ---
                                    echo "<td class='align-middle text-center bg-light fw-bold'>";
                                    echo "<div>{$weekNumber}</div>";

                                    echo "<input type='hidden' name='last_tydni_smena[{$weekNumber}]' value='" . htmlspecialchars($vychozi_smena) . "'>";
                                    echo "<select class='form-select form-select-sm mt-1 text-center' name='tydenni_smena[{$weekNumber}]'>";
                                    echo "<option value=''>--</option>";
                                    foreach ($tydenni_smeny as $opt) {
                                        $selected = ($vychozi_smena === $opt) ? 'selected' : '';
                                        echo "<option value='{$opt}' {$selected}>{$opt}</option>";
                                    }
                                    echo "</select>";

                                    // ===== TRASA =====
                                    echo "<input type='hidden' name='last_tydni_trasa[{$weekNumber}]' value='" . htmlspecialchars($vychozi_trasa) . "'>";

                                    echo "<select class='form-select form-select-sm mt-1 text-center' name='tydenni_trasa[{$weekNumber}]'>";
                                    echo "<option value=''>Vyber trasu</option>";

                                    $stmt2 = $conn->prepare("
                                        SELECT DISTINCT a.id, a.spz
                                        FROM trasy t
                                        LEFT JOIN auta a ON t.auto = a.id
                                        WHERE t.zastavka = ?
                                        ORDER BY a.spz
                                    ");
                                    $stmt2->bind_param("i", $nastup_id);
                                    $stmt2->execute();
                                    $stmt2->bind_result($auto_id, $auto_spz);

                                    while ($stmt2->fetch()) {
                                        $selected = ($vychozi_trasa == $auto_id) ? 'selected' : '';
                                        echo "<option value='{$auto_id}' {$selected}>{$auto_spz}</option>";
                                    }
                                    $stmt2->close();

                                    echo "</select>";
                                    echo "</td>";

                                    // --- 7 buněk pro dny (pondělí..neděle) ---
                                    for ($d = 0; $d < 7; $d++) {
                                        $dayTimestamp = $current + $d * 86400;
                                        $dayYear  = (int) date('Y', $dayTimestamp);
                                        $dayMonth = (int) date('n', $dayTimestamp);
                                        $dayDay   = (int) date('j', $dayTimestamp);
                                        $dayOfWeek = (int) date('N', $dayTimestamp);

                                        if ($dayYear === (int)$year && $dayMonth === (int)$month) {
                                            $datum = sprintf('%04d-%02d-%02d', $year, $month, $dayDay);
                                            $je_vikend = ($dayOfWeek >= 6);
                                            $smena = kontrola_dochazky($id_zam, $datum);
                                            $poznamka = kontrola_poznamky($id_zam, $datum);

                                            if ($je_vikend) $barva = 'bg-warning-subtle';
                                            elseif ($smena === '' || $smena === null) $barva = 'bg-danger-subtle';
                                            elseif (in_array($smena, $smeny)) $barva = 'bg-success-subtle';
                                            elseif (in_array($smena, $nepritomnosti_warning)) $barva = 'bg-secondary-subtle';
                                            elseif (in_array($smena, $nepritomnosti_other)) $barva = 'bg-primary-subtle';
                                            else $barva = '';
                                            ?>
                                            <td class="position-relative text-center <?php echo $barva; ?>">
                                                <div class="position-relative">
                                                    <label class="form-label fw-bold fs-3 <?php if($je_vikend) echo 'text-danger'; ?>">
                                                        <?php echo $dayDay; ?>
                                                    </label>
                                                    <span role="button"
                                                        class="poznamkaIcon <?php echo trim($poznamka) !== '' ? 'text-warning' : 'text-muted'; ?>"
                                                        style="position:absolute; top:2px; right:2px; cursor:pointer;"
                                                        data-den="<?php echo $dayDay; ?>"
                                                        data-poznamka="<?php echo htmlspecialchars($poznamka); ?>"
                                                        title="<?php echo trim($poznamka) !== '' ? htmlspecialchars($poznamka) : 'Přidej poznámku'; ?>"><i class="bi bi-pencil-square"></i>
                                                    </span>
                                                </div>

                                                <input type="hidden" name="lasttoggle<?php echo $dayDay; ?>" value="<?php echo $smena; ?>">
                                                <input type="hidden" name="poznamka<?php echo $dayDay; ?>" id="poznamkaInput<?php echo $dayDay; ?>" value="<?php echo htmlspecialchars($poznamka); ?>">
                                                <input type="hidden" name="lastpoznamka<?php echo $dayDay; ?>" value="<?php echo htmlspecialchars($poznamka); ?>">

                                                <select class="form-select text-center fw-bold fs-5" name="toggle<?php echo $dayDay; ?>" id="toggle<?php echo $dayDay; ?>">
                                                    <?php
                                                    $selected = ($smena === '') ? 'selected' : '';
                                                    echo "<option value='' class='bg-danger-subtle' $selected>N/A</option>";
                                                    foreach ($smeny as $opt) {
                                                        $sel = ($smena === $opt) ? 'selected' : '';
                                                        echo "<option value='$opt' class='bg-success-subtle' $sel>$opt</option>";
                                                    }
                                                    foreach ($nepritomnosti_warning as $opt) {
                                                        $sel = ($smena === $opt) ? 'selected' : '';
                                                        echo "<option value='$opt' class='bg-secondary-subtle' $sel>$opt</option>";
                                                    }
                                                    foreach ($nepritomnosti_other as $opt) {
                                                        $sel = ($smena === $opt) ? 'selected' : '';
                                                        echo "<option value='$opt' class='bg-primary-subtle' $sel>$opt</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <?php
                                        } else {
                                            echo "<td></td>";
                                        }
                                    }

                                    echo "</tr>";
                                    $current = $current + 7 * 86400;
                                }


                                ?>
                                </tbody>


                            </table>

                            <input type="hidden" name="vybrany_mesic" value="<?php echo $month; ?>">
                            <input type="hidden" name="vybrany_rok" value="<?php echo $year; ?>">
                            <input type="hidden" name="max_den" value="<?php echo $daysInMonth; ?>">
                            <input type="hidden" name="id_zam" value="<?php echo $id_zam; ?>">

                            <div class="d-grid gap-2 mt-2">
                                <button type="submit" class="btn btn-primary">Uložit změny do databáze</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL PRO POZNÁMKU -->
    <div class="modal fade" id="poznamkaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Poznámka ke dni <span id="poznamkaDen"></span></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <textarea id="poznamkaText" class="form-control" rows="3" placeholder="Zadej poznámku..."></textarea>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="ulozPoznamkuBtn" data-bs-dismiss="modal">Záznam</button>
        </div>
        </div>
    </div>
    </div>

    <script>
    window.__aktualniDen = window.__aktualniDen || null;

    // bind jen jednou (modal se načítá přes AJAX opakovaně)
    if (!window.__poznamkaEventsBound) {
        window.__poznamkaEventsBound = true;

        document.addEventListener('click', function(e) {
            const icon = e.target.closest('.poznamkaIcon');
            if (!icon) return;

            window.__aktualniDen = icon.getAttribute('data-den');
            const poznamka = icon.getAttribute('data-poznamka') || '';

            const denEl = document.getElementById('poznamkaDen');
            const txtEl = document.getElementById('poznamkaText');
            if (denEl) denEl.textContent = window.__aktualniDen + '.';
            if (txtEl) txtEl.value = poznamka;

            const modalEl = document.getElementById('poznamkaModal');
            if (modalEl) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        });

        document.addEventListener('click', function(e) {
            if (!e.target || e.target.id !== 'ulozPoznamkuBtn') return;

            const txtEl = document.getElementById('poznamkaText');
            const den = window.__aktualniDen;
            if (!txtEl || !den) return;

            const text = txtEl.value || '';
            const inputEl = document.getElementById('poznamkaInput' + den);
            if (inputEl) inputEl.value = text;

            const icon = document.querySelector(`.poznamkaIcon[data-den='${den}']`);
            if (icon) {
                icon.setAttribute('data-poznamka', text);
                icon.classList.toggle('text-warning', text.trim() !== '');
            }
        });
    }
</script>
    <?php
}


function vyrob_kalendar_backup($year, $month, $id_zam)
{ ?>
<div class="container-fluid">
    <!-- HLAVNÍ MODAL -->
    <div class="modal fade" id="kalendar_dochazka" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"`n            data-rok="<?php echo (int)$year; ?>" data-mesic="<?php echo (int)$month; ?>" data-zam="<?php echo (int)$id_zam; ?>"`n            data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><?php echo get_name_from_id_zam($id_zam); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form name="kalendar" method="POST" action="report4.php?typ=savechange">
                        <table class="table text-center align-middle">
                            <thead>
                                <tr>
                                    <th>Pondělí</th>
                                    <th>Úterý</th>
                                    <th>Středa</th>
                                    <th>Čtvrtek</th>
                                    <th>Pátek</th>
                                    <th>Sobota</th>
                                    <th>Neděle</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $first_day = date_create_from_format('Y-n-j', "$year-$month-1");
                            $daysInMonth = (int) date_format($first_day, 't');
                            $startWeekday = (int) date_format($first_day, 'N'); // 1=pondělí ... 7=neděle

                            $smeny = ['R','O','N','NN','NR'];
                            $nepritomnosti_warning = ['DPN','OČR','ABS','LEK'];
                            $nepritomnosti_other = ['DOV','NAR','NEM','NEO','NEP','PRO','NEPV','NAHV','OABS'];

                            echo "<tr>";

                            // vložíme prázdné buňky před prvním dnem (padding do prvního týdne)
                            for ($empty = 1; $empty < $startWeekday; $empty++) {
                                echo "<td></td>";
                            }

                            // projdeme všechny dny měsíce
                            for ($day = 1; $day <= $daysInMonth; $day++) {
                                $datum = sprintf('%04d-%02d-%02d', $year, $month, $day);
                                $den_v_tydnu = (int) date('N', strtotime($datum)); // 1..7
                                $je_vikend = ($den_v_tydnu >= 6);

                                $smena = kontrola_dochazky($id_zam, $datum);
                                $poznamka = kontrola_poznamky($id_zam, $datum); // DB funkce pro poznámku

                                // Barva buňky
                                if ($je_vikend) $barva = 'bg-warning-subtle';
                                elseif ($smena === '' || $smena === null) $barva = 'bg-danger-subtle';
                                elseif (in_array($smena, $smeny)) $barva = 'bg-success-subtle';
                                elseif (in_array($smena, $nepritomnosti_warning)) $barva = 'bg-secondary-subtle';
                                elseif (in_array($smena, $nepritomnosti_other)) $barva = 'bg-primary-subtle';
                                else $barva = '';

                                // vykreslení buňky
                                ?>
                                <td class="position-relative text-center <?php echo $barva; ?>">
                                    <div class="position-relative">
                                        <label class="form-label fw-bold fs-3 <?php if($je_vikend) echo 'text-danger'; ?>">
                                            <?php echo $day; ?>
                                        </label>
                                        <span role="button"
                                            class="poznamkaIcon <?php echo trim($poznamka) !== '' ? 'text-warning' : 'text-muted'; ?>"
                                            style="position:absolute; top:2px; right:2px; cursor:pointer;"
                                            data-den="<?php echo $day; ?>"
                                            data-poznamka="<?php echo htmlspecialchars($poznamka); ?>"
                                            title="<?php echo trim($poznamka) !== '' ? htmlspecialchars($poznamka) : 'Přidej poznámku'; ?>">âśŹď¸Ź
                                        </span>
                                    </div>

                                    <input type="hidden" name="lasttoggle<?php echo $day; ?>" value="<?php echo $smena; ?>">
                                    <input type="hidden" name="poznamka<?php echo $day; ?>" id="poznamkaInput<?php echo $day; ?>" value="<?php echo htmlspecialchars($poznamka); ?>">
                                    <input type="hidden" name="lastpoznamka<?php echo $day; ?>" value="<?php echo htmlspecialchars($poznamka); ?>">

                                    <select class="form-select text-center fw-bold fs-5" name="toggle<?php echo $day; ?>" id="toggle<?php echo $day; ?>">
                                        <?php
                                        $selected = ($smena === '') ? 'selected' : '';
                                        echo "<option value='' class='bg-danger-subtle' $selected>N/A</option>";
                                        foreach ($smeny as $opt) {
                                            $selected = ($smena === $opt) ? 'selected' : '';
                                            echo "<option value='$opt' class='bg-success-subtle' $selected>$opt</option>";
                                        }
                                        foreach ($nepritomnosti_warning as $opt) {
                                            $selected = ($smena === $opt) ? 'selected' : '';
                                            echo "<option value='$opt' class='bg-secondary-subtle' $selected>$opt</option>";
                                        }
                                        foreach ($nepritomnosti_other as $opt) {
                                            $selected = ($smena === $opt) ? 'selected' : '';
                                            echo "<option value='$opt' class='bg-primary-subtle' $selected>$opt</option>";
                                        }
                                        ?>
                                    </select>
                                </td>
                                <?php
                                // pokud je neděle, ukončíme řádek (nový řádek začne automaticky v dalším průchodu/při doplnění)
                                if ($den_v_tydnu == 7) {
                                    echo "</tr>";
                                    // pokud nejsou poslední dny, otevřeme nový řádek (pokud je den < poslední, nebo pokud další den existuje)
                                        if ($day != $daysInMonth) echo "<tr>";
                                    }
                                }

                                // pokud poslední den nespadá na neděli, doplníme prázdné buňky až do konce týdne a zavřeme řádek
                                $lastDayWeekday = (int) date('N', strtotime(sprintf('%04d-%02d-%02d', $year, $month, $daysInMonth)));
                                if ($lastDayWeekday != 7) {
                                    for ($fill = $lastDayWeekday + 1; $fill <= 7; $fill++) {
                                        echo "<td></td>";
                                    }
                                    echo "</tr>";
                                }
                            ?>
                            </tbody>
                        </table>

                        <input type="hidden" name="vybrany_mesic" value="<?php echo $month; ?>">
                        <input type="hidden" name="vybrany_rok" value="<?php echo $year; ?>">
                        <input type="hidden" name="max_den" value="<?php echo $daysInMonth; ?>">
                        <input type="hidden" name="id_zam" value="<?php echo $id_zam; ?>">

                        <div class="d-grid gap-2 mt-2">
                            <button type="submit" class="btn btn-primary">Uložit změny do databáze</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PRO POZNÁMKU -->
<div class="modal fade" id="poznamkaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Poznámka ke dni <span id="poznamkaDen"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <textarea id="poznamkaText" class="form-control" rows="3" placeholder="Zadej poznámku..."></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="ulozPoznamkuBtn" data-bs-dismiss="modal">Záznam</button>
      </div>
    </div>
  </div>
</div>

<script>
let aktualniDen2 = null;

// EVENT DELEGATION: funguje pro všechny nové i staré hlavní modaly
document.addEventListener('click', function(e) {
    const icon = e.target.closest('.poznamkaIcon');
    if (!icon) return;

    aktualniDen = icon.getAttribute('data-den');
    const poznamka = icon.getAttribute('data-poznamka') || '';

    document.getElementById('poznamkaDen').textContent = aktualniDen + '.';
    document.getElementById('poznamkaText').value = poznamka;

    const modal = new bootstrap.Modal(document.getElementById('poznamkaModal'));
    modal.show();
});

// uložení poznámky
document.getElementById('ulozPoznamkuBtn').addEventListener('click', () => {
    const text = document.getElementById('poznamkaText').value;
    document.getElementById('poznamkaInput' + aktualniDen).value = text;

    const icon = document.querySelector(`.poznamkaIcon[data-den='${aktualniDen}']`);
    if (icon) {
        icon.setAttribute('data-poznamka', text);
        icon.classList.toggle('text-warning', text.trim() !== '');
    }
});
</script>
<?php
}



function kontrola_poznamky($id_zam, $datum)
{
    global $conn;

    $dotaz = "SELECT poznamka FROM dochazka WHERE zamestnanec = '$id_zam' AND datum = '$datum' LIMIT 1";
    $vysledek = mysqli_query($conn, $dotaz);

    if ($vysledek && mysqli_num_rows($vysledek) > 0) {
        $zaznam = mysqli_fetch_assoc($vysledek);
        return $zaznam['poznamka'] ?? '';
    } else {
        return ''; // žádná poznámka
    }
}

function vyrob_kalendar_4sm($year, $month,$smena)
{   ?>

<div class="container-fluid">         

    <form name="kalendar" method="POST" action="report4.php?typ=savechange">

    <h2 class='text-center m-2 p-2 d-print-none'>Plánovací kalendář - směna <?php echo $smena;?> </h2>

    <table class="table">

        <thead>
            <tr>
                <th scope="col" class="text-center">Pondělí</th>
                <th scope="col" class="text-center">Úterý</th>
                <th scope="col" class="text-center">Středa</th>
                <th scope="col" class="text-center">Čtvrtek</th>
                <th scope="col" class="text-center">Pátek</th>
                <th scope="col" class="text-center">Sobota</th>
                <th scope="col" class="text-center">Neděle</th>
            </tr>
        </thead>

        <?php $currentMonth = date('N'); 
        $dt = date("Y/m/d"); //aktualni den
        $max_den = date("t", strtotime($dt)); //pocet dnu v mesici?>

        <tbody>
            <tr>
            <?php

            if (!checkdate($month, 1, $year))
            {
            $month = null;
            $year = null;
            
            }

            // pokud datum není zadané
            if (empty($year))
            $year = idate("Y");

            if (empty($month))
            $month = idate('m');
            $today = idate("d");
            $day = "1";
                
            $date = date_create_from_format('Y-n-j', $year . '-' . $month . '-' . $day)->format('Y-m-d');
            list($y, $m, $d) = explode('-', $date);
            $first_day = $y . '-' . $m . '-01';
                
            $today = date("Y/m/d"); //aktualni den
            $daysInMonth = date("t", strtotime($first_day)); //pocet dnu v mesici
                                                                        
            // zjištění začátku týdne
            $fd = date_create($y . '-' . $m . '-01');
            $weekDay=date_format($fd,"N"); //zjistim den v tydnu u prvniho dne v mesici
                
            if ($weekDay == 0) // začíná nedělí, ta je u nás však 7., 0. pozici vynecháme
                $weekDay = 7;
                        
            // vynecháme místo
                
            for ($i = $weekDay; $i > 1; $i--)
                echo('<td>&nbsp;</td>');

                // vypsání jednotlivých dnů
                for ($day = 1; $day <= $daysInMonth; $day++)
                {
                                                                
                if (($weekDay % 7) == 6 or ($weekDay % 7) == 0 )
                {   ?>
                        <td class="text-center bg-warning-subtle">
                        <label class="form-label fw-bold fs-3 text-danger"><?php echo $day;?></label>                                                    
                    <?php
                }
                else
                {   ?>
                        <td class="text-center">
                        <label class="form-label fw-bold fs-3"><?php echo $day;?></label>
                    <?php
                }

                $hodnota = zjisti_data_ze_smenneho_kalendare($year . "-" . $month . "-" . $day,$smena);
                ?>

                <div class="container">
                    
                    <div class="btn-group" role="group" aria-label="Basic outlined example">
                    
                        <?php
                        if ($hodnota == 'R')
                        {   ?>
                            <a class='btn btn-danger btn-sm' role='button'>R</a>
                            <a class='btn btn-outline-dark btn-sm' href='report5.php?shift=<?php echo $smena;?>&date=<?php echo $year . '-' . $month . "-" . $day;?>&smena=N' role='button'>N</a>
                            <a class='btn btn-outline-info btn-sm' href='report5.php?shift=<?php echo $smena;?>&date=<?php echo $year . '-' . $month . '-' . $day;?>&smena=V' role='button'>V</a>
                            <?php
                        }
                        elseif ($hodnota == 'N')
                        {   ?>
                            <a class='btn btn-outline-danger btn-sm' href='report5.php?shift=<?php echo $smena;?>&date=<?php echo $year . '-' . $month . '-' . $day;?>&smena=R' role='button'>R</a>
                            <a class='btn btn-dark btn-sm' role='button'>N</a>
                            <a class='btn btn-outline-info btn-sm' href='report5.php?shift=<?php echo $smena;?>&date=<?php echo $year . '-' . $month . '-' . $day;?>&smena=V' role='button'>V</a>
                            <?php
                        }
                        elseif ($hodnota == 'V')
                        {   ?>
                            <a class='btn btn-outline-danger btn-sm' href='report5.php?shift=<?php echo $smena;?>&date=<?php echo $year . '-' . $month . '-' . $day;?>&smena=R' role='button'>R</a>
                            <a class='btn btn-outline-dark btn-sm' href='report5.php?shift=<?php echo $smena;?>&date=<?php echo $year . '-' . $month . "-" . $day;?>&smena=N' role='button'>N</a>
                            <a class='btn btn-info btn-sm' role='button'>V</a>
                            <?php
                        }
                        else
                        {   ?>
                            <a class='btn btn-outline-danger btn-sm' href='report5.php?shift=<?php echo $smena;?>&date=<?php echo $year . '-' . $month . '-' . $day;?>&smena=R' role='button'>R</a>
                            <a class='btn btn-outline-dark btn-sm' href='report5.php?shift=<?php echo $smena;?>&date=<?php echo $year . '-' . $month . "-" . $day;?>&smena=N' role='button'>N</a>
                            <a class='btn btn-outline-info btn-sm' href='report5.php?shift=<?php echo $smena;?>&date=<?php echo $year . '-' . $month . '-' . $day;?>&smena=V' role='button'>V</a>
                            <?php
                        }
                        ?>
                    

                    </div>

                </div>
              
                </td>  

                <?php
                // řádkování podle týdnů
                if (($weekDay % 7) == 0)
                {?>
                
                </tr>

                <tr>
                <?php
                }
                
                $weekDay++;

                if ($weekDay > 7)
                    $weekDay = 1;
                }?>
                </tr>
        </tbody>

    </table>                

    </form>

</div>   

<?php

}

function kontrola_dochazky($id_zam,$datum)
{
    global $conn;
    $hodnota = "";

    $sql = "select smena,nepritomnost from dochazka where zamestnanec ='" . $id_zam . "' and datum='" . $datum . "'";
    
    //echo $sql;

    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            

    while ($radek = mysqli_fetch_array($vysledek))
    {  
        if ($radek['nepritomnost'] == '')
        {
            $hodnota = $radek['smena'];
        }
        else
        {
            $hodnota = $radek['nepritomnost'];
        }
    }
    
    mysqli_free_result($vysledek);

    if ($hodnota == '')
    {
        $sql = "select nepritomnost from nepritomnost where zamestnanec ='" . $id_zam . "' and datum='" . $datum . "'";
    
        //echo $sql;
    
        if (!($vysledek = mysqli_query($conn, $sql)))
        {
        die("Nelze provést dotaz</body></html>");
        }            
    
        while ($radek = mysqli_fetch_array($vysledek))
        {  
            $hodnota = $radek['nepritomnost'];
        }
        
        mysqli_free_result($vysledek);
    }
        
    return $hodnota;

}

function nacti_kontrolu_dochazky_mesic($zamestnanci, $start_day, $dnu)
{
    global $conn;
    $kontrola = [];

    if (empty($zamestnanci)) return $kontrola;

    // seznam ID pro IN
    $ids = implode(',', array_map('intval', $zamestnanci));

    // od-do datum pro měsíc
    $konec_mesice = date('Y-m-d', strtotime($start_day . ' + ' . $dnu . ' days'));

    // --- 1) Načteme všechny záznamy z docházky, kde hodnota ještě nebyla nastavena ---
    $sql = "SELECT zamestnanec, datum, smena, nepritomnost 
            FROM dochazka 
            WHERE zamestnanec IN ($ids) 
              AND datum BETWEEN ? AND ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ss", $start_day, $konec_mesice);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $id = $row['zamestnanec'];
            $datum = $row['datum'];

            if ($row['nepritomnost'] === '') {
                $kontrola[$id][$datum] = $row['smena'];
            } else {
                $kontrola[$id][$datum] = $row['nepritomnost'];
            }
        }
        $stmt->close();
    } else {
        die("Nelze připravit dotaz na docházku.");
    }

    // --- 2) Načteme případné nepřítomnosti z tabulky nepritomnost, pokud není již vyplněno ---
    $sql2 = "SELECT zamestnanec, datum, nepritomnost 
             FROM nepritomnost 
             WHERE zamestnanec IN ($ids)
               AND datum BETWEEN ? AND ?";

    if ($stmt2 = $conn->prepare($sql2)) {
        $stmt2->bind_param("ss", $start_day, $konec_mesice);
        $stmt2->execute();
        $result2 = $stmt2->get_result();

        while ($row = $result2->fetch_assoc()) {
            $id = $row['zamestnanec'];
            $datum = $row['datum'];

            // pouze pokud ještě není nastavena hodnota
            if (!isset($kontrola[$id][$datum]) || $kontrola[$id][$datum] === '') {
                $kontrola[$id][$datum] = $row['nepritomnost'];
            }
        }
        $stmt2->close();
    } else {
        die("Nelze připravit dotaz na nepřítomnosti.");
    }

    return $kontrola;
}

function check_dochazky($id_zam,$datum)
{
    global $conn;
    $hodnota = "";

    $sql = "select smena,nepritomnost from dochazka where zamestnanec ='" . $id_zam . "' and datum='" . $datum . "'";
    
    //echo $sql;

    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            

    while ($radek = mysqli_fetch_array($vysledek))
    {  
        if ($radek['nepritomnost'] == '')
        {
            $hodnota = $radek['smena'];
        }
        else
        {
            $hodnota = $radek['nepritomnost'];
        }
    }
    
    mysqli_free_result($vysledek);
        
    return $hodnota;

}

function kontrola_nepritomnosti($id_zam,$datum)
{
    global $conn;
    $hodnota = "";
 
    $sql = "select nepritomnost from nepritomnost where zamestnanec ='" . $id_zam . "' and datum='" . $datum . "' and dochazka='0'";

    //echo $sql;

    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            

    while ($radek = mysqli_fetch_array($vysledek))
    {  
        $hodnota = $radek['nepritomnost'];
    }
    
    mysqli_free_result($vysledek);   
       
    return $hodnota;

}

function update_nepritomnost_radek($id_radek,$id_zam,$nepritomnost,$datum) 
{
    global $conn;
      
    $dotaz="update nepritomnost set dochazka='1' where id = '" . $id_radek . "'";   
        
    if (!($vysledek = mysqli_query($conn, $dotaz)))
    {
    die("Nelze provést dotaz.</body></html>");
    }

    //vlozim zaznam do logu
    $now = new DateTime('now', new DateTimeZone('Europe/Prague'));

    if (!($vysledek = mysqli_query($conn, 
    "insert into logs (kdo,typ,infotext,datumcas) values ('systém','Vložení nepřítomnosti','Nepřítomnost u " . get_name_from_id_zam($id_zam) . " nastavena na " . $nepritomnost . " - " . $datum . "','" . $now->format('Y-m-d H:i:s') . "')")))
    {
    die("Nelze provést dotaz.</body></html>");
    } 

}

function update_nepritomnost_datum_zam($id_zam,$nepritomnost,$datum) 
{
    global $conn;
      
    $dotaz="update nepritomnost set dochazka='1' where zamestnanec = '" . $id_zam . "' and datum='" . $datum . "'";   
        
    if (!($vysledek = mysqli_query($conn, $dotaz)))
    {
    die("Nelze provést dotaz.</body></html>");
    }

    //vlozim zaznam do logu
    $now = new DateTime('now', new DateTimeZone('Europe/Prague'));

    if (!($vysledek = mysqli_query($conn, 
    "insert into logs (kdo,typ,infotext,datumcas) values ('systém','Vložení nepřítomnosti','Nepřítomnost u " . get_name_from_id_zam($id_zam) . " nastavena na " . $nepritomnost . " - " . $datum . "','" . $now->format('Y-m-d H:i:s') . "')")))
    {
    die("Nelze provést dotaz.</body></html>");
    } 

}

function vloz_data_do_smenneho_kalendare($datum,$smena,$hodnota) 
{
    global $conn;
      
    $exist = 0;

    $sql = "select id from kalendar_4sm where datum='" . $datum . "'";
    
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }

    while ($radek = mysqli_fetch_array($vysledek))
    {
        if ($radek['id'] > 0)
        {
            $exist = 1;
        }
    }
    
    mysqli_free_result($vysledek);
        
    if ($exist == 0)
    {
        //zalozim prazdny radek
        $dotaz="insert into kalendar_4sm (datum,A,B,C,D) values ('" . $datum . "','','','','')";   
        
        if (!($vysledek = mysqli_query($conn, $dotaz)))
        {
        die("Nelze provést dotaz.</body></html>");
        }

        //upravim dle pozadavku
        $dotaz="update kalendar_4sm set " . $smena . "='" . $hodnota . "' where datum='" . $datum . "'";
        
        if (!($vysledek = mysqli_query($conn, $dotaz)))
        {
        die("Nelze provést dotaz.</body></html>");
        }
    }
    elseif ($exist == 1)
    {
        $dotaz="update kalendar_4sm set " . $smena . "='" . $hodnota . "' where datum='" . $datum . "'";
        
        if (!($vysledek = mysqli_query($conn, $dotaz)))
        {
        die("Nelze provést dotaz.</body></html>");
        }
    }

}

function zjisti_data_ze_smenneho_kalendare($datum,$smena)
{
    global $conn;
      
    $hodnota = "R";

    $sql = "select " . $smena . " as hodnota from kalendar_4sm where datum='" . $datum . "'";
    
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }

    while ($radek = mysqli_fetch_array($vysledek))
    {
        $hodnota = $radek['hodnota'];
    }
    
    mysqli_free_result($vysledek);

    return $hodnota;
}

function getNextMonth() {
    // Nastavení aktuálního data
    $currentDate = new DateTime();
    
    // Přidání jednoho měsíce
    $currentDate->modify('+1 month');
    
    // Formátování data
    $formattedDate = $currentDate->format('Y-m');
    
    // Výpis formátovaného data
    return $formattedDate;
}

function cron_na_4sm()
{
    global $conn;

    date_default_timezone_set('Europe/Prague'); // Nastavte svou časovou zónu

    // Získání aktuálního času
    $currentHour = date('G'); // Získání hodiny
    $currentMinute = date('i'); // Získání minut
 
    $dneska = date_format(date_create('now'),"Y-m-d");
    
    //nactu zaznamy ze zamestnancu, ktere odpovidaji lidem co maji 4SM
    $sql = "SELECT id,jmeno,prijmeni,id,right(smennost,1) as 4sm from zamestnanci where aktivni='1' and left(smennost,3)='4SM'";
 
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }

    while ($radek = mysqli_fetch_array($vysledek))
    {  
        {                  
            if ($currentHour == 0 and $currentMinute == 10)
            {
                $smena = zjisti_data_ze_smenneho_kalendare($dneska,$radek['4sm']);
                update_smena_4sm($radek['id'],$dneska,$smena);
            }
        }
    }

    mysqli_free_result($vysledek);

}

function update_smena_4sm($id_zam,$datum,$smena) 
{
    global $conn;
        
    if ($smena == 'R')
    {
        $dotaz="update zamestnanci set smena='" . $smena . "' where id = '" . $id_zam . "'";
        
        if (!($vysledek = mysqli_query($conn, $dotaz)))
        {
        die("Nelze provést dotaz.</body></html>");
        }

        //vlozim zaznam do logu
        $now = new DateTime('now', new DateTimeZone('Europe/Prague'));

        if (!($vysledek = mysqli_query($conn, 
        "insert into logs (kdo,typ,infotext,datumcas) values ('systém','Změna 4 směnny','U zaměstnance " . get_name_from_id_zam($id_zam) . " nastavena 4 sm na " . $smena . " - " . $datum . "','" . $now->format('Y-m-d H:i:s') . "')")))
        {
        die("Nelze provést dotaz.</body></html>");
        } 
    }
    elseif ($smena == 'N')
    {
        $dotaz="update zamestnanci set smena='NN' where id = '" . $id_zam . "'";
        
        if (!($vysledek = mysqli_query($conn, $dotaz)))
        {
        die("Nelze provést dotaz.</body></html>");
        }

        //vlozim zaznam do logu
        $now = new DateTime('now', new DateTimeZone('Europe/Prague'));

        if (!($vysledek = mysqli_query($conn, 
        "insert into logs (kdo,typ,infotext,datumcas) values ('systém','Změna 4 směnny','U zaměstnance " . get_name_from_id_zam($id_zam) . " nastavena 4 sm na NN - " . $datum . "','" . $now->format('Y-m-d H:i:s') . "')")))
        {
        die("Nelze provést dotaz.</body></html>");
        } 
    }
    else
    {
        $dotaz="update zamestnanci set smena='N/A' where id = '" . $id_zam . "'";
        
        if (!($vysledek = mysqli_query($conn, $dotaz)))
        {
        die("Nelze provést dotaz.</body></html>");
        }

        //vlozim zaznam do logu
        $now = new DateTime('now', new DateTimeZone('Europe/Prague'));

        if (!($vysledek = mysqli_query($conn, 
        "insert into logs (kdo,typ,infotext,datumcas) values ('systém','Změna 4 směnny','U zaměstnance " . get_name_from_id_zam($id_zam) . " nastavena 4 sm na N/A - " . $datum . "','" . $now->format('Y-m-d H:i:s') . "')")))
        {
        die("Nelze provést dotaz.</body></html>");
        } 
    }
}

function modal_doprava() {
    global $conn;

    // Načtení aut
    $auta = [];
    $sql = "SELECT id, spz, oznaceni FROM auta ORDER BY spz";
    if ($result = mysqli_query($conn, $sql)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $auta[] = $row;
        }
        mysqli_free_result($result);
    } else {
        die("Nelze provést dotaz.");
    }
    ?>

    <!-- Modal -->
    <div class="modal fade" id="modal_doprava" tabindex="-1" aria-labelledby="modalDopravaLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalDopravaLabel">Seznam tras</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zavřít"></button>
                </div>

                <div class="modal-body p-2">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Auto</th>
                                    <th>Trasa</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($auta as $auto): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($auto['spz']); ?></td>
                                        <td><?php echo htmlspecialchars($auto['oznaceni']); ?></td>
                                        <td>
                                            <a class="btn btn-primary btn-sm p-1" href="main.php?typ=zmenabus&bus=<?php echo $auto['id']; ?>">
                                                Vyber
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Zavřít</button>
                </div>

            </div>
        </div>
    </div>

<?php
}

function ziskejPocetUkoluDleStavu($parametr) 
{
    // Předpokládáme, že máš připojení k databázi v proměnné $conn (např. mysqli)
    global $conn;

    // Zjisti ID přihlášeného uživatele
    $userId = $_SESSION["log_id"];

    // Základ dotazu (část WHERE se liší podle $parametr)
    if ($parametr == 1) {
        $where = "ukoly.kdo = '$userId'";
    } elseif ($parametr == 2) {
        $where = "ukoly.komu = '$userId'";
    } else {
        return []; // neplatný parametr
    }

    // SQL dotaz
    $query = "SELECT ukoly.hotovo, COUNT(*) AS pocet
        FROM ukoly
        WHERE $where
        AND (
            ukoly.hotovo = 2
            OR ukoly.hotovo = 0
            OR (ukoly.hotovo = 1 AND TIMESTAMPDIFF(HOUR, ukoly.dokonceno, NOW()) <= 72)
        )
        GROUP BY ukoly.hotovo";

    // Provedení dotazu
    $result = mysqli_query($conn, $query);

    // Zpracování výsledků do pole asociované podle stavu hotovo
    $vysledky = [
        0 => 0,
        1 => 0,
        2 => 0
    ];

    while ($row = mysqli_fetch_assoc($result)) {
        $stav = $row['hotovo'];
        $pocet = $row['pocet'];
        $vysledky[$stav] = $pocet;
    }

    return $vysledky;
}

function ziskejPocetUkoluCelkove() 
{
    // Předpokládáme, že máš připojení k databázi v proměnné $conn (např. mysqli)
    global $conn;   

    // SQL dotaz
    $query = "SELECT ukoly.hotovo, COUNT(*) AS pocet
        FROM ukoly
        WHERE 
        (
            ukoly.hotovo = 2
            OR ukoly.hotovo = 0
            OR (ukoly.hotovo = 1)
        )
        GROUP BY ukoly.hotovo";

    // Provedení dotazu
    $result = mysqli_query($conn, $query);

    // Zpracování výsledků do pole asociované podle stavu hotovo
    $vysledky = [
        0 => 0,
        1 => 0,
        2 => 0
    ];

    while ($row = mysqli_fetch_assoc($result)) {
        $stav = $row['hotovo'];
        $pocet = $row['pocet'];
        $vysledky[$stav] = $pocet;
    }

    return $vysledky;
}

function novy_ukol($id)
{   
    global $conn;  // Použití globální proměnné $conn
    ?>
    <!-- Modal -->

    <form name="novapolozka" method="POST" action="main.php?typ=saveukol">

    <div class="modal fade" id="ModalNovyUkol" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">

    <div class="modal-content">
        
        <div class="modal-header">
            <h3 class='text-center'>Přidání nového úkolu</h3>
        </div>

        <div class="modal-body">      

            <div class="row mt-3">

                <div class="col-md-3">
                    <label for="floatingInputGrid">Úkol od</label>
                    <input type="text" class="form-control bg-primary-subtle mt-2" id="odkoho" name="odkoho" placeholder="" value="<?php echo $_SESSION["log_name"];?>" disabled>
                </div>

                <div class="col-md-3">
                    <label for="floatingInputGrid">Úkol pro</label>
                    
                    <select class="form-select bg-primary-subtle mt-2" name="komu" id="komu" required>
                        <?php

                        $sql = "SELECT id,uzivatel as jmeno FROM uzivatele WHERE aktivni = '1' ORDER BY uzivatel"; // Tabulka se 
                        $result = $conn->query($sql);
                        
                        while ($row = $result->fetch_assoc()) 
                        {
                            echo "<option value='" . $row['id'] . "' class='bg-primary-subtle'>" . $row['jmeno'] . "</option>";
                        }
                       
                        $conn->close();
                        ?>
                    </select>
                </div>

                <div class="col-md-12 mb-3 mt-2">
                    <label for="floatingInputGrid">Úkol</label>
                    <textarea class="form-control bg-primary-subtle mt-2" id="ukol" name="ukol" style="height: 100px" required></textarea>
                </div>  
          
            </div>
           
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary mt-3" id="saveButton">Nový úkol</button>
        </div>
            
    </div>

    </div>
    </div>

    <input type="hidden" class="form-control" id="kdo" name="kdo" placeholder="" value="<?php echo $_SESSION["log_id"];?>">
    <input type="hidden" class="form-control" id="zakazka" name="zakazka" placeholder="" value="0">

    </form>

    <?php
}

function zmen_stav_ukolu($id)
{   
    global $conn;  // Použití globální proměnné $conn
    ?>
    <!-- Modal -->

    <form name="zmenukol" method="POST" action="main.php?typ=changeukol">

    <div class="modal fade" id="ModalChangeUkol" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">

    <div class="modal-content">
        
        <div class="modal-header">
            <h3 class='text-center'>Změna stavu úkolu</h3>
        </div>

        <div class="modal-body">
            <div class="col-md-12 mb-3 mt-2">
                <label for="floatingInputGrid" class='fw-bold'>Zadal:</label>

                <?php
                //$sql = "SELECT ukol from ukoly where id = '" . $id . "'";

                $sql = "SELECT ukol,uzivatele.uzivatel,datumcas from ukoly left join uzivatele on ukoly.kdo = uzivatele.id where ukoly.id = '" . $id . "'";

                $result = $conn->query($sql);
                
                while ($row = $result->fetch_assoc()) 
                {
                    $ukol = $row['ukol'];
                    $zadavatel = $row['uzivatel'];
                    $datumcas = $row['datumcas'];

                    // Převod data na požadovaný formát
                    $dateTime = new DateTime($datumcas);
                    $datumcas = $dateTime->format('d.m.Y H:i:s');
                }
                
                $conn->close();
                ?>
                
                <label id="ukol" class="form-control bg-transparent border-0"><?php echo htmlspecialchars($zadavatel); ?></label>

            </div>

            <div class="col-md-12 mb-3 mt-2">
                <label for="floatingInputGrid" class='fw-bold'>Datum a čas zadání:</label>   
                <label id="ukol" class="form-control bg-transparent border-0"><?php echo $datumcas; ?></label>
            </div> 

            <div class="col-md-12 mb-3 mt-2">
                <label for="floatingInputGrid" class='fw-bold'>Úkol</label>   
                <label id="ukol" class="form-control bg-transparent border-0"><?php echo htmlspecialchars($ukol); ?></label>
            </div> 

            <div class="row mt-3">
                <div class="d-flex justify-content-center gap-3">
                    <a href="main.php?typ=changeukol&id=<?php echo $id;?>&stav=1" id="1" class="btn btn-success">ANO</a>
                    <a href="main.php?typ=changeukol&id=<?php echo $id;?>&stav=0" id="0" class="btn btn-danger">NE</a>
                    <a href="main.php?typ=changeukol&id=<?php echo $id;?>&stav=2" id="2" class="btn btn-warning">ROZPRACOVÁNO</a>
                </div>
            </div>
           
        </div>
            
    </div>

    </div>
    </div>
    
    </form>

    <?php
}

function Tabulka_Ukoly($parametr) 
{

    global $conn;  // Použití globální proměnné $conn
   
    if ($parametr == 1) {
        $query = "SELECT ukoly.id, ukoly.zakazka, 
            u1.uzivatel AS kdo_jmeno, 
            u1.uzivatel AS kdo_prijmeni, 
            u2.uzivatel AS komu_jmeno, 
            u2.uzivatel AS komu_prijmeni, 
            ukoly.ukol, ukoly.datumcas, ukoly.hotovo
          FROM ukoly
          LEFT JOIN uzivatele AS u1 ON ukoly.kdo = u1.id
          LEFT JOIN uzivatele AS u2 ON ukoly.komu = u2.id
          WHERE ukoly.kdo='" . $_SESSION["log_id"] . "'
          AND (
              ukoly.hotovo = 2 -- rozpracované úkoly
              OR ukoly.hotovo = 0 -- nedokončené úkoly
              OR (ukoly.hotovo = 1 AND TIMESTAMPDIFF(HOUR, ukoly.dokonceno, NOW()) <= 72) -- dokončené, ale ne starší než 72 hodin
          )
          ORDER BY
              CASE ukoly.hotovo
                  WHEN 0 THEN 1
                  WHEN 2 THEN 2
                  WHEN 1 THEN 3
              END,
              CASE WHEN ukoly.hotovo = 0 THEN ukoly.id END DESC";
    } 
    elseif ($parametr == 2) {
        $query = "SELECT ukoly.id, ukoly.zakazka, 
        u1.uzivatel AS kdo_jmeno, 
        u1.uzivatel AS kdo_prijmeni, 
        u2.uzivatel AS komu_jmeno, 
        u2.uzivatel AS komu_prijmeni, 
        ukoly.ukol, ukoly.datumcas, ukoly.hotovo
      FROM ukoly
      LEFT JOIN uzivatele AS u1 ON ukoly.kdo = u1.id
      LEFT JOIN uzivatele AS u2 ON ukoly.komu = u2.id
      WHERE ukoly.komu='" . $_SESSION["log_id"] . "'
      AND (
          ukoly.hotovo = 2 -- rozpracované úkoly
          OR ukoly.hotovo = 0 -- nedokončené úkoly
          OR (ukoly.hotovo = 1 AND TIMESTAMPDIFF(HOUR, ukoly.dokonceno, NOW()) <= 72) -- dokončené, ale ne starší než 72 hodin
      )
      ORDER BY
          CASE ukoly.hotovo
              WHEN 0 THEN 1
              WHEN 2 THEN 2
              WHEN 1 THEN 3
          END,
          CASE WHEN ukoly.hotovo = 0 THEN ukoly.id END DESC";
    }
    elseif ($parametr == 3) {
        $query = "SELECT ukoly.id, ukoly.zakazka, 
            u1.uzivatel AS kdo_jmeno, 
            u1.uzivatel AS kdo_prijmeni, 
            u2.uzivatel AS komu_jmeno, 
            u2.uzivatel AS komu_prijmeni, 
            ukoly.ukol, ukoly.datumcas, ukoly.hotovo
          FROM ukoly
          LEFT JOIN uzivatele AS u1 ON ukoly.kdo = u1.id
          LEFT JOIN uzivatele AS u2 ON ukoly.komu = u2.id
          ORDER BY
              CASE ukoly.hotovo
                  WHEN 0 THEN 1
                  WHEN 2 THEN 2
                  WHEN 1 THEN 3
              END,
              ukoly.id DESC";
    }
     
    // Příprava dotazu
    if ($stmt = $conn->prepare($query)) {
        $stmt->execute();
        $stmt->bind_result($id, $zakazka, $kdoJmeno, $kdoPrijmeni, $komuJmeno, $komuPrijmeni, $ukol, $datumcas, $hotovo);
        
        // Začátek HTML tabulky
        echo "<table class='table table-sm table-striped table-hover'>
                <tr class='text-center'>
                    <th>#</th>    
                    <th>Kdo</th>
                    <th>Komu</th>
                    <th>Úkol</th>
                    <th>Hotovo</th>
                </tr>";
        
        // Výpis řádků tabulky
        while ($stmt->fetch()) 
        {
            // Spojení jména a příjmení
            $kdoCelkem = $kdoPrijmeni;
            $komuCelkem = $komuPrijmeni;

            if ($_SESSION["log_name"] == $komuCelkem)
            {
                $styl = "fw-bold";
            }
            elseif ($_SESSION["log_name"] == $kdoCelkem)
            {
                $styl = "fst-italic";
            }
            else
            {
                $styl = "fw-light";
            }

            if ($hotovo == 1)
            {
                $pozadi = 'table-success';
            }
            elseif ($hotovo == 2)
            {
                $pozadi = "table-warning";
            }
            elseif ($hotovo == 0)
            {
                $pozadi = "";
            }
            
            // Převod stavu "hotovo" na text
            $hotovoText = ($hotovo == 1) ? "Ano" : (($hotovo == 0) ? "Ne" : "V procesu");
            $zakazkaText = ($zakazka == 0) ? "" : $zakazka;
           
            // Výpis jednoho řádku
            ?>           
     
            <tr class="<?php echo $styl . ' ' . $pozadi; ?>">
                <td class="fw-bold text-center"><?php echo $id; ?></td>

                <td><?php echo $kdoCelkem; ?></td>
                <td><?php echo $komuCelkem; ?></td>
                <td><?php echo $ukol; ?></td>

                <td class="fw-bold text-center">
                    <a data-bs-toggle="modal" onclick="loadModalContentNew(<?php echo $id; ?>,'zmen_stav_ukolu','#ModalChangeUkol')">
                        <?php 
                        if ($hotovo == 1) {
                            echo "<img src='img/icons/done.png' alt='Hotovo' title='Hotovo' height='25'>";
                        } elseif ($hotovo == 2) {
                            echo "<img src='img/icons/process.png' alt='Rozpracováno' title='Rozpracováno' height='25'>";
                        } else {
                            echo "<img src='img/icons/none.png' alt='Nedokončeno' title='Nedokončeno' height='25'>";
                        }
                        ?>
                    </a>
                </td>
            </tr>

            <?php
            
        }
        
        // Konec tabulky
        echo '</table>';
       
        $stmt->close();
    } 
    else 
    {
        echo "Chyba při přípravě dotazu.";
    }

}

function filtry_zamestnanci()
{
    global $conn;

    // Načtení uložených filtrů ze SESSION
    $vybercilova       = $_SESSION['filtry']['cilova'] ?? 'ALL';
    $vyberpomer        = $_SESSION['filtry']['pomer'] ?? '1';
    $vybersmena        = $_SESSION['filtry']['smena'] ?? 'VŠE';
    $vybernepritomnost = $_SESSION['filtry']['nepritomnost'] ?? 'ALL';

    // ✅ datum filtru (výchozí dnešek)
    $vyberdatum = $_SESSION['filtry']['datum'] ?? date('Y-m-d');
    if (!preg_match('~^\d{4}-\d{2}-\d{2}$~', (string)$vyberdatum)) {
        $vyberdatum = date('Y-m-d');
    }

    // Výběr cílových stanic
    $sql = "SELECT cilova FROM zamestnanci WHERE cilova <> '' GROUP BY cilova ORDER BY cilova";
    $vysledek = mysqli_query($conn, $sql) or die("Nelze provést dotaz</body></html>");
    $stanice = mysqli_fetch_all($vysledek, MYSQLI_ASSOC);
    mysqli_free_result($vysledek);

    // Helper pro HTML
    $h = function($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };

    // Popisky (na badge summary)
    $pomery = [
        'Vše' => 'Všechny',
        '1'   => 'Aktivní',
        '0'   => 'Neaktivní',
        '33'  => 'Řádně ukončen',
        '44'  => 'Neřádně ukončen',
        '55'  => 'Anulován',
    ];
    $smeny = ['VŠE'=>'Vše', 'R'=>'Ranní','O'=>'Odpolední','N'=>'Noční'];

    $nepritomnosti = [
        'ALL' => 'Vše',
        ''    => 'Přítomní',
        'DPN' => 'DPN',
        'OČR' => 'OČR',
        'DOV' => 'Dovolená',
        'ABS' => 'Absence',
        'NAR' => 'Narozeniny',
        'LEK' => 'Lékař',
        'NEM' => 'Nemoc',
        'NEO' => 'Neomluvená',
        'NEP' => 'Neplacené volno',
        'PRO' => 'Prostoj',
        'Vše' => 'Všechny nepřítomnosti',
    ];

    // Souhrn filtrů (včetně datumu)
    $sum = [];
    $sum[] = ['Datum', date('d.m.Y', strtotime($vyberdatum)) ?: $vyberdatum];

    if ($vybercilova !== 'ALL' && $vybercilova !== '') $sum[] = ['Okruh', $vybercilova];
    if (($vyberpomer ?? '') !== '' && ($vyberpomer ?? '') !== 'Vše') $sum[] = ['Poměr', $pomery[$vyberpomer] ?? $vyberpomer];
    if ($vybersmena !== 'VŠE' && $vybersmena !== '') $sum[] = ['Směna', $smeny[$vybersmena] ?? $vybersmena];
    if ($vybernepritomnost !== 'ALL') $sum[] = ['Nepřít.', $nepritomnosti[$vybernepritomnost] ?? $vybernepritomnost];
    ?>

    <div class="collapse" id="filtry">
      <div class="container-fluid px-0">
        <div class="row justify-content-center">
          <div class="col-12 col-xxl-11">

            <div class="card shadow-sm border-0 mb-3">
              <div class="card-header bg-light border-0 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                  <i class="bi bi-funnel-fill text-primary"></i>
                  <div class="fw-semibold">Filtry</div>
                  <span class="badge text-bg-primary"><?= count($sum) ?> aktivní</span>
                </div>

                <div class="d-none d-md-flex flex-wrap gap-1 justify-content-end">
                  <?php foreach ($sum as [$k,$v]): ?>
                    <span class="badge rounded-pill text-bg-secondary">
                      <?= $h($k) ?>: <span class="fw-semibold"><?= $h($v) ?></span>
                    </span>
                  <?php endforeach; ?>
                </div>
              </div>

              <div class="card-body">
                <form class="row g-3 align-items-end"
                      action="zamestnanci.php?typ=filtr"
                      method="post"
                      name="zamestnanci"
                      autocomplete="off">

                  <!-- ✅ 1 ŘÁDEK na lg+: 2 + 3 + 2 + 2 + 3 = 12 -->
                  <!-- DATUM -->
                  <div class="col-12 col-lg-2">
                    <label for="datum" class="form-label mb-1">
                      <i class="bi bi-calendar3 me-1 text-muted"></i>Datum
                    </label>
                    <input type="date" class="form-control" id="datum" name="datum"
                           value="<?= $h($vyberdatum) ?>">
                    <div class="form-text d-lg-none">Filtry (např. nepřítomnost) se vyhodnotí k tomuto dni.</div>
                  </div>

                  <!-- OKRUH -->
                  <div class="col-12 col-lg-3">
                    <label for="cilova" class="form-label mb-1">
                      <i class="bi bi-geo-alt me-1 text-muted"></i>Okruh dopravy
                    </label>
                    <select class="form-select" id="cilova" name="cilova">
                      <option value="ALL" <?= ($vybercilova == 'ALL') ? 'selected' : '' ?>>Všechny cílové stanice</option>
                      <?php foreach($stanice as $s): $c = (string)($s['cilova'] ?? ''); ?>
                        <option value="<?= $h($c) ?>" <?= ($vybercilova == $c) ? 'selected' : '' ?>><?= $h($c) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <!-- POMĚR -->
                  <div class="col-12 col-lg-2">
                    <label for="pomer" class="form-label mb-1">
                      <i class="bi bi-person-badge me-1 text-muted"></i>Pracovní poměr
                    </label>
                    <select class="form-select" id="pomer" name="pomer" onchange="change_pomer(this.value);">
                      <?php foreach($pomery as $val => $txt): ?>
                        <option value="<?= $h($val) ?>" <?= ((string)$val === (string)$vyberpomer) ? 'selected' : '' ?>><?= $h($txt) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <!-- SMĚNA -->
                  <div class="col-12 col-lg-2">
                    <label for="smena" class="form-label mb-1">
                      <i class="bi bi-clock-history me-1 text-muted"></i>Směna
                    </label>
                    <select class="form-select" id="smena" name="smena">
                      <?php foreach($smeny as $val => $txt): ?>
                        <option value="<?= $h($val) ?>" <?= ((string)$val === (string)$vybersmena) ? 'selected' : '' ?>><?= $h($txt) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <!-- NEPŘÍTOMNOST -->
                  <div class="col-12 col-lg-3">
                    <label for="nepritomnost" class="form-label mb-1">
                      <i class="bi bi-exclamation-triangle me-1 text-muted"></i>Nepřítomnost
                    </label>
                    <select class="form-select" id="nepritomnost" name="nepritomnost">
                      <?php foreach($nepritomnosti as $val => $txt): ?>
                        <option value="<?= $h($val) ?>" <?= ((string)$val === (string)$vybernepritomnost) ? 'selected' : '' ?>><?= $h($txt) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <!-- TLAČÍTKA: samostatný řádek -->
                  <div class="col-12">
                    <div class="d-flex flex-column flex-md-row gap-2 mt-1">
                      <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-check2-circle me-1"></i>Proveď výběr
                      </button>

                      <a href="zamestnanci.php?typ=filtr&reset=1"
                         class="btn btn-outline-secondary flex-grow-1"
                         title="Vynulovat filtry">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset filtrů
                      </a>
                    </div>
                  </div>

                </form>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

    <?php
}

function nova_sazba($id = '')
{    
    global $conn;

    if ($id <> '') {
        $sql = "SELECT id, id_zamestnance, sazba, obdobi_od, obdobi_do, poznamka 
                FROM hodinove_sazby 
                WHERE id = '" . $id . "'";
        
        if (!($vysledek = mysqli_query($conn, $sql))) {
            die("Nelze provést dotaz</body></html>");
        }            

        $polehodnot = array();

        while ($radek = mysqli_fetch_array($vysledek, MYSQLI_ASSOC)) {
            $polehodnot[] = $radek;
        }

        mysqli_free_result($vysledek);

        $action = "sazby.php?typ=updatesazba";
    } else {
        $action = "sazby.php?typ=vytvorsazbu";
    }
    ?>

    <form name="nova_sazba_form" method="POST" action="<?php echo $action; ?>">

    <!-- Modal -->
    <div class="modal fade" id="nova_sazba" tabindex="-1" aria-labelledby="novaSazbaLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">
                    <?php echo ($id == '') ? "Nová hodinová sazba" : "Editace sazby";?>
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <div class="row">

                    <!-- Výběr zaměstnance -->
                    <div class="col-md-12 mt-2 text-start">
                        <label class="form-label">Zaměstnanec</label>
                        <select class="form-select bg-primary-subtle" id="id_zamestnance" name="id_zamestnance" required>
                            <option value="">-- vyber zaměstnance --</option>
                            <?php
                            $sql2 = "SELECT id, jmeno, prijmeni 
                                    FROM zamestnanci ORDER BY prijmeni, jmeno";
                            $vysl = mysqli_query($conn, $sql2);
                            while ($z = mysqli_fetch_array($vysl)) {
                                $selected = ($id <> '' && $z['id'] == $polehodnot[0]['id_zamestnance']) ? "selected" : "";
                                echo "<option value='{$z['id']}' $selected>{$z['jmeno']} {$z['prijmeni']}</option>";
                            }
                            mysqli_free_result($vysl);
                            ?>
                        </select>
                    </div>

                    <!-- Sazba -->
                    <div class="col-md-4 mt-2 text-start">
                        <label class="form-label">Hodinová sazba (Kč)</label>
                        <input type="number" step="0.01" class="form-control bg-primary-subtle"
                               id="sazba" name="sazba"
                               value="<?php echo ($id == '') ? "" : htmlspecialchars($polehodnot[0]['sazba']); ?>"
                               required>
                    </div>

                    <!-- Platnost od -->
                    <div class="col-md-4 mt-2 text-start">
                        <label class="form-label">Platnost od</label>
                        <input type="date" class="form-control bg-primary-subtle"
                               id="obdobi_od" name="obdobi_od"
                               value="<?php echo ($id == '') ? "" : htmlspecialchars($polehodnot[0]['obdobi_od']); ?>"
                               required>
                    </div>

                    <!-- Platnost do -->
                    <div class="col-md-4 mt-2 text-start">
                        <label class="form-label">Platnost do</label>
                        <input type="date" class="form-control bg-primary-subtle"
                               id="obdobi_do" name="obdobi_do"
                               value="<?php echo ($id == '') ? "" : htmlspecialchars($polehodnot[0]['obdobi_do']); ?>">
                    </div>

                    <!-- Poznámka -->
                    <div class="col-md-12 mt-2 text-start">
                        <label class="form-label">Poznámka</label>
                        <input type="text" class="form-control bg-primary-subtle"
                               id="poznamka" name="poznamka"
                               value="<?php echo ($id == '') ? "" : htmlspecialchars($polehodnot[0]['poznamka']); ?>">
                    </div>

                    <input type="hidden" id="id_sazby" name="id_sazby"
                           value="<?php echo ($id == '') ? "" : $polehodnot[0]['id']; ?>">

                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary mb-3">Ulož změnu</button>
                <button type="button" class="btn btn-secondary mb-3" data-bs-dismiss="modal">Zavřít</button>
            </div>

        </div>
    </div>
    </div>

    </form>

    <?php
}

function kontrola_naboru($prijmeni, $doklad, $id = null) 
{
    global $conn;

    $prijmeni = trim($prijmeni);
    $doklad = trim($doklad);

    // pokud některý údaj chybí, není duplicita
    if ($prijmeni === '' || $doklad === '') {
        return false;
    }

    if ($id) {
        $stmt = $conn->prepare("SELECT COUNT(*) AS pocet FROM nabory WHERE prijmeni=? AND doklad=? AND id<>?");
        $stmt->bind_param("ssi", $prijmeni, $doklad, $id);
    } else {
        $stmt = $conn->prepare("SELECT COUNT(*) AS pocet FROM nabory WHERE prijmeni=? AND doklad=?");
        $stmt->bind_param("ss", $prijmeni, $doklad);
    }

    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return ($result['pocet'] > 0);
}


function getSmena($id_zamestnance)
{
    global $conn;

    // Aktuální ISO rok a týden
    $rok   = date('o');
    $tyden = date('W');

    $sql = "
        SELECT 
            zamestnanci.id,
            zamestnanci.firma,
            zamestnanci.nepritomnost,
            plan_smen.smena
        FROM plan_smen
        LEFT JOIN zamestnanci 
            ON plan_smen.jmeno = zamestnanci.id
        WHERE plan_smen.rok = '$rok'
          AND plan_smen.tyden = '$tyden'
          AND DATE(zamestnanci.vstup) <= CURDATE()
          AND (zamestnanci.vystup = '0000-00-00' OR DATE(zamestnanci.vystup) >= CURDATE())
          AND zamestnanci.id = '$id_zamestnance'
        LIMIT 1
    ";

    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        return $row['smena'];     // vrátí R/O/N/D apod.
    } else {
        return null;              // nic nenalezeno
    }
}


function zastavky_modal($id = '')
{
    global $conn; // připojení k DB

    $planData = [];
    $spz = '';
    $oznaceni = '';

    // --- Načtení SPZ a označení auta vždy ---
    $sql_auto = "SELECT spz, oznaceni FROM auta WHERE id = ?";
    if ($stmt0 = $conn->prepare($sql_auto)) {
        $stmt0->bind_param("i", $id);
        $stmt0->execute();
        $stmt0->bind_result($spz, $oznaceni);
        $stmt0->fetch();
        $stmt0->close();
    }

    // --- Načtení dat tras přiřazených k autu ---
    $sql = "SELECT trasy.id, nastupy.zastavka, trasy.R AS ranni, trasy.O AS odpoledni, trasy.N AS nocni
            FROM trasy
            LEFT JOIN nastupy ON trasy.zastavka = nastupy.id
            WHERE trasy.auto = ?
            ORDER BY trasy.R";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $planData[] = [
                'trasa_id' => $row['id'],
                'zastavka' => $row['zastavka'],
                'ranni' => $row['ranni'],
                'odpoledni' => $row['odpoledni'],
                'nocni' => $row['nocni']
            ];
        }

        $stmt->close();
    }

    // --- Texty pro směny ---
    $smena_text = [
        "ranni" => "Ranní",
        "odpoledni" => "Odpolední",
        "nocni" => "Noční"
    ];

    // --- Seznam nepoužitých zastávek ---
    $unused = [];
    $sql_unused = "
        SELECT id, zastavka 
        FROM nastupy 
        WHERE id NOT IN (
            SELECT zastavka FROM trasy WHERE auto = ?
        )
        ORDER BY zastavka
    ";
    if ($stmt2 = $conn->prepare($sql_unused)) {
        $stmt2->bind_param("i", $id);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        while ($r = $result2->fetch_assoc()) {
            $unused[] = $r;
        }
        $stmt2->close();
    }
    ?>

    <!-- Modal -->
    <div class="modal fade" id="ModalZastavky" tabindex="-1" aria-labelledby="ModalZastavkyLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Trasa: <?php echo htmlspecialchars($spz . ' - ' . $oznaceni); ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- ******** HLAVNÍ FORMULÁŘ – ukládá časy ******** -->
            <form method="POST" action="vozovypark.php?typ=save_trasa">

                <div class="modal-body">

                    <input type="hidden" name="id_auta" value="<?php echo $id; ?>">

                    <?php if (empty($planData)): ?>

                        <div class="alert alert-info text-center mb-3">
                            Žádné zastávky přiřazené k trase.
                        </div>

                    <?php else: ?>

                        <table class="table table-sm table-bordered table-striped w-100">
                            <thead>
                                <tr>
                                    <th>Zastávka</th>
                                    <?php foreach ($smena_text as $t): ?>
                                        <th class="text-center"><?php echo $t; ?></th>
                                    <?php endforeach; ?>
                                    <th class="text-center" style="width:40px;">đź—‘</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($planData as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['zastavka']); ?></td>

                                        <td class="text-center">
                                            <input type="time" class="form-control form-control-sm"
                                                name="R[<?php echo $row['trasa_id']; ?>]"
                                                value="<?php echo htmlspecialchars($row['ranni']); ?>">
                                        </td>

                                        <td class="text-center">
                                            <input type="time" class="form-control form-control-sm"
                                                name="O[<?php echo $row['trasa_id']; ?>]"
                                                value="<?php echo htmlspecialchars($row['odpoledni']); ?>">
                                        </td>

                                        <td class="text-center">
                                            <input type="time" class="form-control form-control-sm"
                                                name="N[<?php echo $row['trasa_id']; ?>]"
                                                value="<?php echo htmlspecialchars($row['nocni']); ?>">
                                        </td>

                                        <td class="text-center">
                                            <button type="submit" name="akce" value="delete_trasa_<?php echo $row['trasa_id']; ?>" 
                                                    class="btn btn-sm btn-danger py-0 px-1" 
                                                    onclick="return confirm('Odebrat tuto zastávku z trasy?');">
                                                X
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                    <?php endif; ?>


                    <!-- *** PŘIDÁNÍ ZASTÁVKY – bez vnitřního <form> *** -->
                    <div class="mt-3 p-2 border rounded bg-light">

                        <div class="row g-2 align-items-center">

                            <div class="col-8">
                                <select name="id_zastavky" class="form-select form-select-sm">
                                    <option value="">-- Vyber zastávku k přidání --</option>
                                    <?php foreach ($unused as $u): ?>
                                        <option value="<?php echo $u['id']; ?>">
                                            <?php echo htmlspecialchars($u['zastavka']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-4 d-grid">
                                <button type="submit"
                                        name="akce"
                                        value="add_zastavka"
                                        class="btn btn-success btn-sm">
                                    + Přidat zastávku
                                </button>
                            </div>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <?php if (!empty($planData)): ?>
                        <button type="submit" class="btn btn-primary">Uložit změny</button>
                    <?php endif; ?>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavřít</button>
                </div>

            </form>

        </div>
    </div>
    </div>


<?php
}

function zastavky_overview_modal($id = '',$id2 = '') 
{
    global $conn; // připojení k DB
    ?>

    <!-- Modal -->
    <div class="modal fade" id="ModalZastavkyOverview" tabindex="-1" aria-labelledby="ModalZastavkyOverviewLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="ModalZastavkyOverviewLabel">Přehled zastávek</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <!-- Formulář pro přidání nové zastávky -->
                    <form method="POST" action="vozovypark.php?typ=add_zastavka" class="mb-3 d-flex gap-2">
                        <input type="text" name="nova_zastavka" class="form-control" placeholder="Nová zastávka" required>
                        <button type="submit" class="btn btn-success">Přidat</button>
                    </form>

                    <!-- Tabulka stávajících zastávek -->
                    <?php
                    $sql = "SELECT id, zastavka FROM nastupy ORDER BY zastavka";
                    if ($stmt = $conn->prepare($sql)) {
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows === 0) 
                        {
                            echo "<div>Žádné zastávky.</div>";
                        } 
                        else 
                        {
                            echo '<table class="table table-sm table-bordered table-striped w-100">';
                            echo '<thead><tr>';
                            echo '<th style="text-align:left; width:80%;">Zastávka</th>';
                            echo '<th style="text-align:center; width:20%;">Akce</th>';
                            echo '</tr></thead>';
                            echo '<tbody>';

                            while ($row = $result->fetch_assoc()) {
                                echo '<tr>';
                                echo '<td style="width:80%;">' . htmlspecialchars($row['zastavka']) . '</td>';
                                echo '<td class="text-center" style="width:20%;">';
                                echo '<form method="POST" action="vozovypark.php?typ=delete_zastavka" style="display:inline-block;">';
                                echo '<input type="hidden" name="id_zastavky" value="' . $row['id'] . '">';
                                echo '<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Opravdu smazat tuto zastávku? Bude odstraněna i ze všech tras, a zaměstnanci, co mají tuto zastávku přiřazenou, bude rovněž vymazána.\');">Smazat</button>';
                                echo '</form>';
                                echo '</td>';
                                echo '</tr>';
                            }

                            echo '</tbody></table>';
                        }


                        $stmt->close();
                    }
                    ?>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavřít</button>
                </div>

            </div>
        </div>
    </div>

<?php
}

function cron_vloz_volno_pro_X_vcera(): void
{
    global $conn;

    date_default_timezone_set('Europe/Prague');

    $currentHour = (int)date('H');
    $currentMinute = (int)date('i');

    // spouštěj jen kolem 06:00 (cron běží každou minutu)
    if (!($currentHour === 6 && $currentMinute >= 0 && $currentMinute <= 10)) {
        return;
    }

    $yesterday = new DateTimeImmutable('yesterday', new DateTimeZone('Europe/Prague'));
    $datum = $yesterday->format('Y-m-d');

    $isoWeek = (int)$yesterday->format('W');
    $isoYear = (int)$yesterday->format('o');

    echo "CRON VOLNO X | datum=$datum | ISO=$isoWeek/$isoYear\n";

    $sql = "
        SELECT z.id
        FROM plan_smen p
        JOIN zamestnanci z ON z.id = p.jmeno
        WHERE p.rok = ?
          AND p.tyden = ?
          AND p.smena = 'X'
          AND DATE(z.vstup) <= ?
          AND (z.vystup IS NULL OR z.vystup = '0000-00-00' OR DATE(z.vystup) >= ?)
          AND NOT EXISTS (
              SELECT 1 FROM dochazka d
              WHERE d.zamestnanec = z.id
                AND d.datum = ?
          )
          AND NOT EXISTS (
              SELECT 1 FROM nepritomnost n
              WHERE n.zamestnanec = z.id
                AND n.datum = ?
          )
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "Prepare error: " . $conn->error . "\n";
        return;
    }

    $stmt->bind_param("iissss", $isoYear, $isoWeek, $datum, $datum, $datum, $datum);
    $stmt->execute();
    $res = $stmt->get_result();

    $inserted = 0;

    while ($row = $res->fetch_assoc()) {

        $zamId = (int)$row['id'];

        $firma = get_info_from_zamestnanci_table($zamId, 'firma');
        $zastavka = get_info_from_zamestnanci_table($zamId, 'nastup');
        $auto = get_bus_from_zastavky($zastavka);

        // VOL nemá reálný čas → bezpečně nastavíme 00:00
        $cas = '00:00:00';

        $smena = 'VOL';
        $poznamka = '';
        $nepritomnost = '';

        // Přímý INSERT podle struktury tabulky
        $insert = $conn->prepare("
            INSERT INTO dochazka
            (zamestnanec, datum, cas, smena, bus, zastavka, firma, cron, nepritomnost, poznamka, ip)
            VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?, ?, 'CRON')
        ");

        if (!$insert) {
            echo "Insert prepare error: " . $conn->error . "\n";
            continue;
        }

        $insert->bind_param(
            "isssiiiss",
            $zamId,
            $datum,
            $cas,
            $smena,
            $auto,
            $zastavka,
            $firma,
            $nepritomnost,
            $poznamka
        );

        if ($insert->execute()) {
            $inserted++;
            echo "INSERTED VOL | zam=$zamId | datum=$datum\n";
        } else {
            echo "FAILED INSERT | zam=$zamId | error=" . $insert->error . "\n";
        }

        $insert->close();
    }

    $stmt->close();

    echo "DONE | inserted=$inserted\n";
}

function cron_plan_smen_firma(string $firmaNazev, string $defaultShift): array
{
    $now = new DateTimeImmutable('now', new DateTimeZone('Europe/Prague'));
    $currentHour   = (int)$now->format('G');
    $currentMinute = (int)$now->format('i');

    // spouštěj jen mezi 01:00 a 01:10
    if (!($currentHour === 1 && $currentMinute >= 0 && $currentMinute <= 10)) {
        return [
            'ok'      => true,
            'skipped' => true,
            'reason'  => 'Mimo povolený čas spuštění 01:00-01:10.',
            'firma'   => $firmaNazev,
        ];
    }

    global $conn;

    if (!isset($conn) || !($conn instanceof mysqli)) {
        throw new RuntimeException("Chyba: \$conn není platné mysqli připojení.");
    }

    $conn->set_charset('utf8');

    $FALLBACK_TRASA_ID = 0;

    $currentYear = (int)$now->format('o');
    $currentWeek = (int)$now->format('W');
    $today       = $now->format('Y-m-d');

    $prevDate = $now->modify('-7 days');
    $prevYear = (int)$prevDate->format('o');
    $prevWeek = (int)$prevDate->format('W');

    $inserted = 0;
    $updated  = 0;
    $checked  = 0;

    try {
        $conn->begin_transaction();

        $sqlEmployees = "
            SELECT z.id
            FROM zamestnanci z
            INNER JOIN firmy f ON f.id = z.firma
            LEFT JOIN nastupy n ON z.nastup = n.id
            WHERE f.aktivni = 1
              AND f.firma = ?
              AND DATE(z.vstup) <= ?
              AND (z.vystup IS NULL OR z.vystup = '0000-00-00' OR DATE(z.vystup) >= ?)
              AND n.zastavka IS NOT NULL
              AND n.zastavka <> 'Vlastní auto'
            ORDER BY z.id
        ";
        $stmtEmployees = $conn->prepare($sqlEmployees);
        if (!$stmtEmployees) {
            throw new RuntimeException("Prepare employees failed: " . $conn->error);
        }
        $stmtEmployees->bind_param("sss", $firmaNazev, $today, $today);
        $stmtEmployees->execute();
        $resEmployees = $stmtEmployees->get_result();

        $sqlCheckCurrent = "
            SELECT id, smena, trasa
            FROM plan_smen
            WHERE rok = ?
              AND tyden = ?
              AND jmeno = ?
            LIMIT 1
        ";
        $stmtCheckCurrent = $conn->prepare($sqlCheckCurrent);
        if (!$stmtCheckCurrent) {
            throw new RuntimeException("Prepare current week check failed: " . $conn->error);
        }

        $sqlCheckPrev = "
            SELECT trasa
            FROM plan_smen
            WHERE rok = ?
              AND tyden = ?
              AND jmeno = ?
              AND trasa > 0
            LIMIT 1
        ";
        $stmtCheckPrev = $conn->prepare($sqlCheckPrev);
        if (!$stmtCheckPrev) {
            throw new RuntimeException("Prepare previous week check failed: " . $conn->error);
        }

        $sqlInsert = "
            INSERT INTO plan_smen (rok, tyden, smena, trasa, jmeno)
            VALUES (?, ?, ?, ?, ?)
        ";
        $stmtInsert = $conn->prepare($sqlInsert);
        if (!$stmtInsert) {
            throw new RuntimeException("Prepare insert failed: " . $conn->error);
        }

        $sqlUpdate = "
            UPDATE plan_smen
            SET smena = ?, trasa = ?
            WHERE id = ?
        ";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        if (!$stmtUpdate) {
            throw new RuntimeException("Prepare update failed: " . $conn->error);
        }

        while ($emp = $resEmployees->fetch_assoc()) {
            $employeeId = (int)$emp['id'];
            $checked++;

            $prevTrasa = $FALLBACK_TRASA_ID;
            $stmtCheckPrev->bind_param("iii", $prevYear, $prevWeek, $employeeId);
            $stmtCheckPrev->execute();
            $resPrev = $stmtCheckPrev->get_result();
            $prevRow = $resPrev->fetch_assoc();
            if ($prevRow && (int)$prevRow['trasa'] > 0) {
                $prevTrasa = (int)$prevRow['trasa'];
            }

            $stmtCheckCurrent->bind_param("iii", $currentYear, $currentWeek, $employeeId);
            $stmtCheckCurrent->execute();
            $resCurrent = $stmtCheckCurrent->get_result();
            $existing = $resCurrent->fetch_assoc();

            if (!$existing) {
                $stmtInsert->bind_param("iisii", $currentYear, $currentWeek, $defaultShift, $prevTrasa, $employeeId);
                $stmtInsert->execute();
                $inserted++;
            } else {
                $planId = (int)$existing['id'];
                $shift  = trim((string)$existing['smena']);
                $trasa  = (int)$existing['trasa'];

                $missingShift = ($shift === '');
                $missingTrasa = ($trasa <= 0);

                if ($missingShift || $missingTrasa) {
                    $newShift = $missingShift ? $defaultShift : $shift;
                    $newTrasa = $missingTrasa ? $prevTrasa : $trasa;

                    $stmtUpdate->bind_param("sii", $newShift, $newTrasa, $planId);
                    $stmtUpdate->execute();
                    $updated++;
                }
            }
        }

        $conn->commit();

        return [
            'ok'       => true,
            'skipped'  => false,
            'firma'    => $firmaNazev,
            'rok'      => $currentYear,
            'tyden'    => $currentWeek,
            'checked'  => $checked,
            'inserted' => $inserted,
            'updated'  => $updated,
        ];

    } catch (Throwable $e) {
        $conn->rollback();

        return [
            'ok'    => false,
            'firma' => $firmaNazev,
            'error' => $e->getMessage(),
        ];
    }
}

function cron_plan_smen_firma_tatra(): array
{
    $now = new DateTimeImmutable('now', new DateTimeZone('Europe/Prague'));
    $currentHour   = (int)$now->format('G');
    $currentMinute = (int)$now->format('i');

    if (!($currentHour === 1 && $currentMinute >= 0 && $currentMinute <= 10)) {
        return [
            'ok'      => true,
            'skipped' => true,
            'reason'  => 'Mimo povolený čas.',
        ];
    }

    global $conn;

    $conn->set_charset('utf8');

    $DEFAULT_SHIFT = 'R';
    $FALLBACK_TRASA_ID = 0;

    $currentYear = (int)$now->format('o');
    $currentWeek = (int)$now->format('W');
    $today       = $now->format('Y-m-d');

    $prevDate = $now->modify('-7 days');
    $prevYear = (int)$prevDate->format('o');
    $prevWeek = (int)$prevDate->format('W');

    $inserted = 0;
    $updated  = 0;
    $checked  = 0;

    try {
        $conn->begin_transaction();

        $sqlEmployees = "
            SELECT z.id
            FROM zamestnanci z
            INNER JOIN firmy f ON f.id = z.firma
            LEFT JOIN nastupy n ON z.nastup = n.id
            WHERE f.aktivni = 1
              AND f.firma = 'TATRA'
              AND DATE(z.vstup) <= ?
              AND (z.vystup IS NULL OR z.vystup = '0000-00-00' OR DATE(z.vystup) >= ?)
              AND n.zastavka = 'Vlastní auto'
            ORDER BY z.id
        ";

        $stmtEmployees = $conn->prepare($sqlEmployees);
        $stmtEmployees->bind_param("ss", $today, $today);
        $stmtEmployees->execute();
        $resEmployees = $stmtEmployees->get_result();

        $stmtCheckCurrent = $conn->prepare("
            SELECT id, smena, trasa
            FROM plan_smen
            WHERE rok = ?
              AND tyden = ?
              AND jmeno = ?
            LIMIT 1
        ");

        $stmtCheckPrev = $conn->prepare("
            SELECT trasa
            FROM plan_smen
            WHERE rok = ?
              AND tyden = ?
              AND jmeno = ?
              AND trasa > 0
            LIMIT 1
        ");

        $stmtInsert = $conn->prepare("
            INSERT INTO plan_smen (rok, tyden, smena, trasa, jmeno)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmtUpdate = $conn->prepare("
            UPDATE plan_smen
            SET smena = ?, trasa = ?
            WHERE id = ?
        ");

        while ($emp = $resEmployees->fetch_assoc()) {
            $employeeId = (int)$emp['id'];
            $checked++;

            // minulá trasa
            $prevTrasa = $FALLBACK_TRASA_ID;
            $stmtCheckPrev->bind_param("iii", $prevYear, $prevWeek, $employeeId);
            $stmtCheckPrev->execute();
            $resPrev = $stmtCheckPrev->get_result();
            $prevRow = $resPrev->fetch_assoc();
            if ($prevRow && (int)$prevRow['trasa'] > 0) {
                $prevTrasa = (int)$prevRow['trasa'];
            }

            // aktuální týden
            $stmtCheckCurrent->bind_param("iii", $currentYear, $currentWeek, $employeeId);
            $stmtCheckCurrent->execute();
            $resCurrent = $stmtCheckCurrent->get_result();
            $existing = $resCurrent->fetch_assoc();

            if (!$existing) {
                $stmtInsert->bind_param("iisii", $currentYear, $currentWeek, $DEFAULT_SHIFT, $prevTrasa, $employeeId);
                $stmtInsert->execute();
                $inserted++;
            } else {
                $planId = (int)$existing['id'];
                $shift  = trim((string)$existing['smena']);
                $trasa  = (int)$existing['trasa'];

                $missingShift = ($shift === '');
                $missingTrasa = ($trasa <= 0);

                if ($missingShift || $missingTrasa) {
                    $newShift = $missingShift ? $DEFAULT_SHIFT : $shift;
                    $newTrasa = $missingTrasa ? $prevTrasa : $trasa;

                    $stmtUpdate->bind_param("sii", $newShift, $newTrasa, $planId);
                    $stmtUpdate->execute();
                    $updated++;
                }
            }
        }

        $conn->commit();

        return [
            'ok'       => true,
            'checked'  => $checked,
            'inserted' => $inserted,
            'updated'  => $updated,
        ];

    } catch (Throwable $e) {
        $conn->rollback();

        return [
            'ok'    => false,
            'error' => $e->getMessage(),
        ];
    }
}

function cron_batz_patecni_volno(): array
{
    $now = new DateTimeImmutable('now', new DateTimeZone('Europe/Prague'));
    $currentHour   = (int)$now->format('G');
    $currentMinute = (int)$now->format('i');
    $currentDow    = (int)$now->format('N'); // 1=Po

    // jen pondělí 01:00–01:10
    if (!($currentDow === 1 && $currentHour === 1 && $currentMinute >= 0 && $currentMinute <= 10)) {
        return [
            'ok'      => true,
            'skipped' => true,
            'reason'  => 'Mimo povolený čas (pondělí 01:00-01:10)'
        ];
    }

    global $conn;

    if (!isset($conn) || !($conn instanceof mysqli)) {
        throw new RuntimeException("Chyba: \$conn není mysqli");
    }

    $conn->set_charset('utf8');

    $currentYear = (int)$now->format('o');
    $currentWeek = (int)$now->format('W');
    $today       = $now->format('Y-m-d');

    // pátek aktuálního týdne
    $fridayDate = $now->setISODate($currentYear, $currentWeek, 5)->format('Y-m-d');

    $checked  = 0;
    $inserted = 0;
    $skipped  = 0;

    try {
        $conn->begin_transaction();

        // ===== 1) zaměstnanci BATZ - THERMA s noční směnou =====
        $sqlEmployees = "
            SELECT z.id, z.firma
            FROM zamestnanci z
            INNER JOIN firmy f ON f.id = z.firma
            INNER JOIN plan_smen ps
                ON ps.jmeno = z.id
               AND ps.rok = ?
               AND ps.tyden = ?
               AND ps.smena = 'N'
            WHERE f.aktivni = 1
              AND f.firma = ?
              AND DATE(z.vstup) <= ?
              AND (z.vystup IS NULL OR z.vystup = '0000-00-00' OR DATE(z.vystup) >= ?)
        ";

        $stmtEmployees = $conn->prepare($sqlEmployees);
        if (!$stmtEmployees) {
            throw new RuntimeException("Prepare employees failed: " . $conn->error);
        }

        $firma = 'BATZ - THERMA';
        $stmtEmployees->bind_param("iisss", $currentYear, $currentWeek, $firma, $today, $today);
        $stmtEmployees->execute();
        $resEmployees = $stmtEmployees->get_result();

        // ===== 2) kontrola existence =====
        $stmtCheck = $conn->prepare("
            SELECT id FROM dochazka
            WHERE zamestnanec = ? AND datum = ?
            LIMIT 1
        ");

        // ===== 3) insert =====
        $stmtInsert = $conn->prepare("
            INSERT INTO dochazka
            (zamestnanec, datum, cas, smena, bus, zastavka, firma, cron, nepritomnost, poznamka, ip)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        if (!$stmtCheck || !$stmtInsert) {
            throw new RuntimeException("Prepare failed: " . $conn->error);
        }

        while ($emp = $resEmployees->fetch_assoc())
        {
            $employeeId = (int)$emp['id'];
            $firmaId    = (int)$emp['firma'];
            $checked++;

            // kontrola duplicity
            $stmtCheck->bind_param("is", $employeeId, $fridayDate);
            $stmtCheck->execute();
            $resCheck = $stmtCheck->get_result();

            if ($resCheck->fetch_assoc()) {
                $skipped++;
                continue;
            }

            // hodnoty
            $cas          = '01:00:00';
            $smena        = 'VOL';
            $bus          = 0;
            $zastavka     = 0;
            $cron         = 1;
            $nepritomnost = '';
            $poznamka     = 'Cron BATZ - páteční volno po noční směně';
            $ip           = 'cron';

            $stmtInsert->bind_param("isssiiiisss",$employeeId,$fridayDate,$cas,$smena,$bus,$zastavka,$firmaId,$cron,$nepritomnost,$poznamka,$ip);

            $stmtInsert->execute();
            $inserted++;
        }

        $conn->commit();

        return [
            'ok'        => true,
            'rok'       => $currentYear,
            'tyden'     => $currentWeek,
            'checked'   => $checked,
            'inserted'  => $inserted,
            'skipped'   => $skipped,
            'datum'     => $fridayDate
        ];

    } catch (Throwable $e) {
        $conn->rollback();

        return [
            'ok'    => false,
            'error' => $e->getMessage()
        ];
    }
}

?>





