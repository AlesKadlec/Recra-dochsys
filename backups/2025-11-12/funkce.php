<?php
//error_reporting(E_ALL);             // Hlásí všechny chyby a upozornění
//ini_set('display_errors', 1);       // Zobrazí chyby na obrazovku
//ini_set('display_startup_errors', 1); // Zobrazí chyby při startu PHP
?>
<?php

require_once 'init.php';
include('db.php');

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

// Zahrnutí souboru s údaji o připojení


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
{   ?>
    <nav class="navbar navbar-expand-lg bg-light fixed-top d-print-none">
    <div class="container-fluid">

    <a class="navbar-brand ml-2">
      <img src="img/logo.png" width="122" height="36" alt="LOGO RECRA" loading="lazy">
    </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">

            <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="main.php">Domů</a>
            </li>

            <?php
            if (isset($_SESSION["logged"]))
            {
                if ($_SESSION["logged"] == "ANO")
                {   ?>

                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Provoz</a>
            
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        
                        <li class="nav-item">
                            <li class="nav-item"><a class="dropdown-item text-primary" href="zamestnanci.php">Přehled zaměstnanců</a></li>
                        </li>

                        <li class="nav-item">
                            <li class="nav-item"><a class="dropdown-item text-primary" href="firmy.php">Přehled firem (překlápění směn)</a></li>
                        </li>

                        <?php
                        if (($_SESSION["typ"] == "5") or ($_SESSION["typ"] == "1"))
                        {   ?>
                            <li class="nav-item">
                                <li class="nav-item"><a class="dropdown-item text-primary" href="kontroly.php">Kontrola DPN</a></li>
                                <li class="nav-item"><a class="dropdown-item text-primary" href="kontrolyprehled.php">Přehled a výsledky kontrol DPN</a></li>
                            </li>
                            <?php
                        }
                        ?>                        

                        <li class="nav-item">
                            <li class="nav-item"><a class="dropdown-item text-primary" href="dochazka.php">Docházka</a></li>
                        </li>

                        <?php
                            if (($_SESSION["typ"] == "5") or ($_SESSION["typ"] == "1") or ($_SESSION["typ"] == "4"))
                            {   ?>
                                    <li class="nav-item">
                                        <li class="nav-item"><a class="dropdown-item text-primary" href="report4.php">Report docházek</a></li>
                                        <li class="nav-item"><a class="dropdown-item text-primary" href="report5.php">Nastavení kalendářů ve čtyřsměnném provozu</a></li>
                                        <li class="nav-item"><a class="dropdown-item text-primary" href="xml.php">Zpracování XML souborů DSC (testovací provoz)</a></li>
                                    </li>
                                <?php
                            }
                        ?>    

                        </ul>
                    </li>

                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Nábor</a>

                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">

                        <?php
                        if (($_SESSION["typ"] == "5") or ($_SESSION["typ"] == "1") or ($_SESSION["typ"] == "4") or ($_SESSION["typ"] == "6"))
                        {   ?>
                                <li class="nav-item">
                                    <li class="nav-item"><a class="dropdown-item text-primary" href="nabory.php">Evidence uchazečů CZ a PL</a></li>
                                </li>
                            <?php
                        }
                        ?>

                        <?php
                        if (($_SESSION["typ"] == "5") or ($_SESSION["typ"] == "1") or ($_SESSION["typ"] == "4") or ($_SESSION["typ"] == "6"))
                        {   ?>
                                <li class="nav-item">
                                    <li class="nav-item"><a class="dropdown-item text-primary" href="informace.php">Informace k nástupům</a></li>
                                    <li class="nav-item"><a class="dropdown-item text-primary" href="emergency.php">Emergency</a></li>
                                </li>
                            <?php
                        }
                        ?>              

                        <li class="nav-item">
                            <li class="nav-item"><a class="dropdown-item text-danger" href="#">Požadavky na nábor</a></li>
                        </li> 

                        </ul>
                    </li>

                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Doprava</a>

                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">

                        <li class="nav-item">
                            <li class="nav-item"><a class="dropdown-item text-primary" href="vozovypark.php">Přehled vozového parku</a></li>
                        </li>

                        <li class="nav-item">
                            <li class="nav-item"><a class="dropdown-item text-danger" href="#">Přehled řidičů</a></li>
                        </li> 

                        <?php
                            if (($_SESSION["typ"] == "5") or ($_SESSION["typ"] == "1") or ($_SESSION["typ"] == "2") or ($_SESSION["typ"] == "3") or ($_SESSION["typ"] == "4"))
                            {   ?>
                                    <li class="nav-item">
                                        <li class="nav-item"><a class="dropdown-item text-primary" href="report2.php">Report dopravy</a></li>
                                    </li>
                                <?php
                            }
                        ?>  
                        </ul>
                    </li>

                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Reporty</a>

                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">

                        <?php
                            if (($_SESSION['typ'] == 5))
                            {   ?>
                                    <li class="nav-item">
                                        <li class="nav-item"><a class="dropdown-item text-primary" href="report3.php">Report pro vedení dle cílové stanice</a></li>
                                    </li>
                                <?php
                            }
                        ?>

                        <?php
                            if (($_SESSION['typ'] == 5))
                            {   ?>
                                    <li class="nav-item">
                                        <li class="nav-item"><a class="dropdown-item text-primary" href="report.php">Report pro vedení dle dopravy</a></li>
                                    </li>
                                <?php
                            }
                        ?>

                        <?php
                            if (($_SESSION['typ'] == 5))
                            {   ?>
                                    <li class="nav-item">
                                        <li class="nav-item"><a class="dropdown-item text-primary" href="logs.php">Logy</a></li>
                                    </li>
                                <?php
                            }
                        ?>

                        </ul>
                    </li>

                    <?php
                    if (isset($_SESSION["typ"]))
                    {
                        if (($_SESSION['typ'] == 5) or ($_SESSION['typ'] == 7))                        
                        {   ?>

                        <li class="nav-item dropdown">

                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Zaměstnanci</a>

                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">

                                <li class="nav-item">
                                    <li class="nav-item"><a class="dropdown-item text-primary" href="sazby.php">Hodinové sazby</a></li>
                                </li>
  
                            </ul>
                        </li>

                        <?php
                        }
                    }
                    ?>

                    <?php
                    if (isset($_SESSION["typ"]))
                    {
                        if (($_SESSION['typ'] == 1) or ($_SESSION['typ'] == 4) or ($_SESSION['typ'] == 5))                        
                        {   ?>
                                <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="uzivatele.php">Uživatelé</a>
                                </li>
                            <?php
                        }
                    }
                    ?>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="odhlasit.php">Odhlásit</a>
                    </li>

                    <?php
                }
            }
            ?>         
           
        </ul>

        </div>
        
        <?php
        if (isset($_SESSION["logged"]))
        {
            if ($_SESSION["logged"] == "ANO")
            {   ?>
                    <class="nav-item"><a class="nav-item nav-link d-print-none">Přihlášen: <?php echo $_SESSION['log_name'] . " / " . $_SESSION['typ_uctu'];?></a>
                <?php
            }
        }
        ?>       

    </div>
    </nav>

    <br>
    <br>
    <?php
}

function menu_old()
{   ?>
    <nav class="navbar navbar-expand-lg bg-light fixed-top d-print-none">
    <div class="container-fluid">

    <a class="navbar-brand ml-2">
      <img src="img/logo.png" width="122" height="36" alt="LOGO RECRA" loading="lazy">
    </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">

            <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="main.php">Domů</a>
            </li>

            <?php
            if (isset($_SESSION["logged"]))
            {
                if ($_SESSION["logged"] == "ANO")
                {   ?>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Zaměstnanci
                        </a>
            
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">                           
                        
                        <li class="nav-item">
                            <li class="nav-item"><a class="dropdown-item text-primary" href="zamestnanci.php">Přehled zaměstnanců</a></li>
                        </li>

                        <?php
                        if (($_SESSION["typ"] == "5") or ($_SESSION["typ"] == "1") or ($_SESSION["typ"] == "4") or ($_SESSION["typ"] == "6"))
                        {   ?>
                                <li class="nav-item">
                                    <li class="nav-item"><a class="dropdown-item text-primary" href="nabory.php">Nábory zaměstnanců</a></li>
                                </li>
                            <?php
                        }
                        ?>

                        </ul>
                    </li>

                    <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="dochazka.php">Docházka</a>
                    </li>

                    <?php

                    if (isset($_SESSION["typ"]))
                    {
                        if (($_SESSION['typ'] == 1) or ($_SESSION['typ'] == 4) or ($_SESSION['typ'] == 5))
                        {   ?>
                                <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="uzivatele.php">Uživatelé</a>
                                </li>
                            <?php
                        }

                        ?>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Reporty
                            </a>
              
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">

                            <?php
                            if (($_SESSION['typ'] == 5))
                            {   ?>
                                    <li class="nav-item">
                                        <li class="nav-item"><a class="dropdown-item text-primary" href="report.php">Report pro vedení dle dopravy</a></li>
                                    </li>
                                <?php
                            }
                            ?>

                            <?php
                            if (($_SESSION['typ'] == 5))
                            {   ?>
                                    <li class="nav-item">
                                        <li class="nav-item"><a class="dropdown-item text-primary" href="report3.php">Report pro vedení dle cílové stanice</a></li>
                                    </li>
                                <?php
                            }
                            ?>

                            <?php
                            if (($_SESSION["typ"] == "5") or ($_SESSION["typ"] == "1") or ($_SESSION["typ"] == "2") or ($_SESSION["typ"] == "3") or ($_SESSION["typ"] == "4"))
                            {   ?>
                                    <li class="nav-item">
                                        <li class="nav-item"><a class="dropdown-item text-primary" href="report2.php">Report dopravy</a></li>
                                    </li>
                                <?php
                            }
                            ?>          
                            
                            <?php
                            if (($_SESSION["typ"] == "5") or ($_SESSION["typ"] == "1") or ($_SESSION["typ"] == "4"))
                            {   ?>
                                    <li class="nav-item">
                                        <li class="nav-item"><a class="dropdown-item text-primary" href="report4.php">Report docházky ve třísměnném provoze</a></li>
                                    </li>
                                <?php
                            }
                            ?>     

                            </ul>
                        </li>

                        <?php
                        if (($_SESSION['typ'] == 5))
                        {   ?>
                                <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="logs.php">Logy</a>
                                </li>
                            <?php
                        }
                    }
                    ?>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="odhlasit.php">Odhlásit</a>
                    </li>

                    <?php
                }
            }
            ?>         
           
        </ul>

        </div>
        
        <?php
        if (isset($_SESSION["logged"]))
        {
            if ($_SESSION["logged"] == "ANO")
            {   ?>
                    <class="nav-item"><a class="nav-item nav-link d-print-none">Přihlášen: <?php echo $_SESSION['log_name'] . " / " . $_SESSION['typ_uctu'];?></a>
                <?php
            }
        }
        ?>       

    </div>
    </nav>

    <br>
    <br>
    <?php
}

function vytvor_tlacitka_pro_smeny($firma,$uzivatel,$autobus)
{   
    if ($_SESSION["autobus"] == "")
    {   ?>
            <span class = 'text-center'><button class="btn btn-outline-primary text-center m-2" onclick="loadModalDoprava()">Není vybrána doprava, kliknutím vyberte</button></span>
        <?php
    }
    else
    {   ?>
        <a type="button" class="btn btn-primary m-1" href="main.php?typ=dochazka&firma=<?php echo $firma;?>&bus=<?php echo $autobus;?>&smena=R">R</button></a>
        <a type="button" class="btn btn-primary m-1" href="main.php?typ=dochazka&firma=<?php echo $firma;?>&bus=<?php echo $autobus;?>&smena=O">O</button></a>
        <a type="button" class="btn btn-primary m-1" href="main.php?typ=dochazka&firma=<?php echo $firma;?>&bus=<?php echo $autobus;?>&smena=N">N</button></a>
        <a type="button" class="btn btn-primary m-1" href="main.php?typ=dochazka&firma=<?php echo $firma;?>&bus=<?php echo $autobus;?>&smena=NN">NN</button></a>
        <a type="button" class="btn btn-primary m-1" href="main.php?typ=dochazka&firma=<?php echo $firma;?>&bus=<?php echo $autobus;?>&smena=NR">NR</button></a>
        <a type="button" class="btn btn-primary m-1" href="main.php?typ=dochazka&firma=<?php echo $firma;?>&bus=<?php echo $autobus;?>&smena=S-R">S-R</button></a>
        <a type="button" class="btn btn-primary m-1" href="main.php?typ=dochazka&firma=<?php echo $firma;?>&bus=<?php echo $autobus;?>&smena=S-O">S-O</button></a>
        <a type="button" class="btn btn-primary m-1" href="main.php?typ=dochazka&firma=<?php echo $firma;?>&bus=<?php echo $autobus;?>&smena=S-N">S-N</button></a>
        <a type="button" class="btn btn-primary m-1" href="main.php?typ=dochazka&firma=<?php echo $firma;?>&bus=<?php echo $autobus;?>&smena=N-R">N-R</button></a>
        <a type="button" class="btn btn-primary m-1" href="main.php?typ=dochazka&firma=<?php echo $firma;?>&bus=<?php echo $autobus;?>&smena=N-O">N-O</button></a>
        <a type="button" class="btn btn-primary m-1" href="main.php?typ=dochazka&firma=<?php echo $firma;?>&bus=<?php echo $autobus;?>&smena=N-N">N-N</button></a>       
        <a type="button" class="btn btn-primary m-1" href="main.php?typ=dochazka&firma=<?php echo $firma;?>&bus=<?php echo $autobus;?>&smena=PR">Přesčas</button></a>

        <a type="button" class="btn btn-warning m-1" href="main.php?typ=dpn">DPN <span class="badge text-bg-success"><?php echo pocet_kontrol_dpn_user($uzivatel);?></span></button></a>
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

    // Bezpečné zpracování vstupů (nepovinné: trim)
    $firma = trim($firma);
    $smena = trim($smena);
    $zastavka = trim($zastavka);

    // SQL - používáme CURDATE() pro porovnání s daty vstup/vystup.
    // Podmínka: vstup <= CURDATE() AND (vystup = '0000-00-00' OR vystup >= CURDATE())
    $sql = "
        SELECT COUNT(*) AS pocet
        FROM zamestnanci
        WHERE firma = ?
          AND nastup = ?
          AND smena = ?
          AND aktivni = 1
          AND (nepritomnost = '' OR nepritomnost IS NULL)
          AND DATE(vstup) <= CURDATE()
          AND (vystup = '0000-00-00' OR DATE(vystup) >= CURDATE())
    ";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        // V případě chyby při přípravě dotazu vrátíme 0 nebo můžete logovat chybu
        error_log("DB prepare error: " . $conn->error);
        return 0;
    }

    // Všechny parametry předáváme jako stringy - je to bezpečné.
    $stmt->bind_param("sss", $firma, $zastavka, $smena);

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
              AND smena = ?
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

function get_name_from_rfid($rfid,$firma)
{
    global $conn;
    $text = "neznámý kód";

    $sql = "select id,os_cislo,jmeno,prijmeni,rfid from zamestnanci where (upper(rfid)=upper('" . $rfid . "')) and aktivni='1' and firma='" . $firma . "' limit 1";
    
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
  
    $sql = "select zamestnanec from dochazka where bus='" . $bus . "' and smena='" . $smena . "' and firma='" . $firma . "' order by id desc limit 1";
    
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }    
    
    while ($radek = mysqli_fetch_array($vysledek))
    {   
      $id_last = $radek["zamestnanec"];
    }
  
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
      $dotaz="insert into dochazka (zamestnanec,datum,cas,smena,bus,firma,zastavka,ip,cron,nepritomnost) values ('" . $id_emp . "',now(),now(),'" . $smena . "','" . $bus . "','" . $firma . "','" . $zastavka . "','" . $_SERVER['REMOTE_ADDR'] . "','" . $cron . "','" . $nepritomnost . "')"; 
            
      if (!($vysledek = mysqli_query($conn, $dotaz)))
      {
      die("Nelze provést dotaz.</body></html>");
      }
    }
}

function insert_attandance_manually($id_emp,$bus,$zastavka,$firma,$smena,$datum,$cas,$nepritomnost,$poznamka) 
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

function vyrob_modal_k_nastupnimu_mistu($firma,$nastup,$smena)
{ ?>

  <!-- Modal -->
  <div class="modal fade" id="nastup<?php echo $nastup;?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">
        
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel"><?php echo get_zastavka_from_id($nastup);?></h5>
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
                $sql = "select id,os_cislo,prijmeni,jmeno,rfid,nastup,telefon,adresa,firma,smena,nepritomnost from zamestnanci where firma='" . $firma . "' and smena='" . $smena . "' and nastup='" . $nastup . "' and aktivni='1' AND (nepritomnost = '' OR nepritomnost IS NULL)
                AND DATE(vstup) <= CURDATE()
                AND (vystup = '0000-00-00' OR DATE(vystup) >= CURDATE())";

                //echo $sql;

                if (!($vysledek = mysqli_query($conn, $sql)))
                {
                die("Nelze provést dotaz.</body></html>");
                }

                while ($radek = mysqli_fetch_array($vysledek))
                {                    
                    $pole = explode (";",zjisti_cas_nastupu($radek["id"],$radek["smena"],$radek["nastup"]));
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
      
    $sql = "select count(*) as pocet from zamestnanci where firma = '" . $firma . "' and aktivni='1'";
   
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

function zjisti_pocet_zamestnancu_ve_firme_objednavka($firma)
{
    global $conn;
    $pocet = 0;
      
    $sql = "select objednavka from firmy where id = '" . $firma . "'";
   
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            

    while ($radek = mysqli_fetch_array($vysledek))
    {  
        $pocet = $radek["objednavka"];
    }
    
    mysqli_free_result($vysledek);
    
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

function pocet_zam_cilovou_stanici_den($cilova,$datum)
{
    global $conn;
    $pocet = 0;
      
    $sql = "select count(*) as pocet from dochazka left join zamestnanci on dochazka.zamestnanec = zamestnanci.id where zamestnanci.cilova = '" . $cilova . "' and datum='" . $datum . "' and dochazka.nepritomnost=''";
   
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

function pocet_zam_nepritomnych_za_den_firma($datum,$nepritomnost,$firma)
{
    global $conn;
    $pocet = 0;
      
    //$sql = "select count(*) as pocet from dochazka where datum='" . $datum . "' and nepritomnost='" . $nepritomnost . "' and firma='" . $firma . "'";
   
    $sql = "select count(*) as pocet from dochazka left join zamestnanci on dochazka.zamestnanec = zamestnanci.id where datum='" . $datum . "' and dochazka.nepritomnost='" . $nepritomnost . "' and zamestnanci.cilova='" . $firma . "'";

    //echo $sql;

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
    //return $sql;

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

function cron_na_vlastni_dopravu()
{
    global $conn;

    date_default_timezone_set('Europe/Prague'); // Nastavte svou časovou zónu

    // Získání aktuálního času
    $currentHour = date('H'); // Získání hodiny
    $currentMinute = date('i'); // Získání minut

    $weekDay = date('N');
    
    if (($weekDay > 0) and ($weekDay <= 5))
    {

        // zjistim smenu ktera byla automaticky vlozena
        $sql = "SELECT parametr1 FROM nastaveni WHERE hodnota='vlastnidoprava'";

        if (!($vysledek = mysqli_query($conn, $sql)))
        {
        die("Nelze provést dotaz</body></html>");
        }            

        while ($radek = mysqli_fetch_array($vysledek))
        {  
            $preklopeni = $radek["parametr1"];
        }

        mysqli_free_result($vysledek);

        // zjistim ID dopravce    
        $sql = "SELECT id FROM auta WHERE spz LIKE '%auto%'";

        if (!($vysledek = mysqli_query($conn, $sql)))
        {
        die("Nelze provést dotaz</body></html>");
        }            

        while ($radek = mysqli_fetch_array($vysledek))
        {  
            $id_auta = $radek["id"];
        }

        mysqli_free_result($vysledek);

        // zjistim ID zastavky pro vlastni dopravu a zjistim casy pro jednotlive smeny
        $sql = "SELECT id,cas1,cas2,cas3 FROM zastavky WHERE auto = '" . $id_auta . "' limit 1";

        if (!($vysledek = mysqli_query($conn, $sql)))
        {
        die("Nelze provést dotaz</body></html>");
        }            

        while ($radek = mysqli_fetch_array($vysledek))
        {  
            $id_zastavky = $radek["id"];
            
            $cas1 = explode(":", $radek["cas1"]);
            $cas2 = explode(":", $radek["cas2"]);
            $cas3 = explode(":", $radek["cas3"]);
            
            if ($currentHour == $cas1[0] and $currentMinute >= $cas1[1])
            {
                $smena = "R";
                $hh = $cas1[0];
                $mm = $cas1[1];
            }
            elseif ($currentHour == $cas2[0] and $currentMinute >= $cas2[1])
            {
                $smena = "O";
                $hh = $cas2[0];
                $mm = $cas2[1];
            }
            elseif ($currentHour == $cas3[0] and $currentMinute >= $cas3[1])
            {
                $smena = "N";
                $hh = $cas3[0];
                $mm = $cas3[1];
            }
            else
            {
                $smena = "XX";
            }
        }

        mysqli_free_result($vysledek);

        if ($smena == "XX")
        {
            echo "Není čas na vložení automatické docházky - auta";
        }
        elseif ($smena <> $preklopeni)
        {                    
            
            $sql = "select id,firma,nepritomnost from zamestnanci where nastup='" . $id_zastavky . "' and smena='" . $smena . "' and aktivni='1'";

            if (!($vysledek = mysqli_query($conn, $sql)))
            {
            die("Nelze provést dotaz</body></html>");
            }

            while ($radek = mysqli_fetch_array($vysledek))
            {

                $datum = date_format(date_create('now'),"Y-m-d");
                $nepr = kontrola_nepritomnosti($radek["id"],$datum);

                if ($nepr == '')
                {
                    insert_attandance($radek["id"],$id_auta,$id_zastavky,$radek["firma"],$smena,'1','');
                }
                else
                {
                    //kdyz je nepritomnost, tak se dochazka vkladat nebude, uz to resi cron pro vkladani nepritomnosti kde jsou vsichni, co jezdi busem i ti co jezdi autem.
                    //vlozim dochazku i s nepritomnosti
                    //insert_attandance($radek["id"],$id_auta,$id_zastavky,$radek["firma"],$smena,'1',$hodnota);

                    //provedu zmenu priznaku v tabulce nepritomnost
                    //update_nepritomnost_datum_zam($radek["id"],$hodnota,$datum);
                }
            }

            mysqli_free_result($vysledek);

            // provedu ulozeni do tabulky nastaveni
            $dotaz="update nastaveni set parametr1 = '" . $smena . "' where hodnota='vlastnidoprava'"; 
            
            if (!($vysledek = mysqli_query($conn, $dotaz)))
            {
            die("Nelze provést dotaz.</body></html>");
            }

            echo "Vlastní doprava za " . $smena . " směnu byly vloženy";
            
        }
        else
        {
            echo "Nelze dvakrát vložit vlastní dopravu na stejné směně, čeká se na další směnu";
        }

    }
    else
    {
        echo "O víkendu se nepracuje !";
    }

     
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


        //echo $sql;

        if (!($vysledek = mysqli_query($conn, $sql)))
        {
        die("Nelze provést dotaz</body></html>");
        }

        while ($radek = mysqli_fetch_array($vysledek))
        {  
            {
                // zjistim si cas, kdy by mel zamestnanec nastupovat
                $firma = get_info_from_zamestnanci_table($radek['zamestnanec'],'firma');
                $smena = get_info_from_zamestnanci_table($radek['zamestnanec'],'smena');
                $zastavka = get_info_from_zamestnanci_table($radek['zamestnanec'],'nastup');
                $cas_nastupu = get_time_nastupu($zastavka,$smena);
                $auto = get_bus_from_zastavky($zastavka);
                $ip = $_SERVER['REMOTE_ADDR'];
                
                $cas = explode(":", $cas_nastupu);

                $cas[0] = $cas[0]+1;

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
                    echo "<br>ID " . $radek['id'] . " zam: " . $radek['zamestnanec'] . " by chtělo, ale není správný čas " . $cas[0] . ":" . $cas[1] . "<br>";
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

function get_time_nastupu($id_zastavky,$smena) 
{

    global $conn;
    
    $hodnota = "";
            
    $sql = "select cas1,cas2,cas3,cas4,cas5,cas6,cas7 from zastavky where id='" . $id_zastavky . "'";
    
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            
    
    while ($radek = mysqli_fetch_array($vysledek))
    {  
        if (($smena == "R") or ($smena == "S-R") or ($smena == "N-R"))
        {
            $hodnota = $radek['cas1'];
        }
        elseif (($smena == "O") or ($smena == "S-O") or ($smena == "N-O"))
        {
            $hodnota = $radek['cas2'];
        }
        elseif (($smena == "N") or ($smena == "S-N") or ($smena == "N-N"))
        {
            $hodnota = $radek['cas3'];
        }
        elseif ($smena == "NN")
        {
            $hodnota = $radek['cas4'];
        }
        elseif ($smena == "NR")
        {
            $hodnota = $radek['cas5'];
        }
        elseif ($smena == "VK")
        {
            $hodnota = $radek['cas6'];
        }
        elseif ($smena == "PR")
        {
            $hodnota = $radek['cas7'];
        }
    }
    
    mysqli_free_result($vysledek);
    
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

function dochazka_zamestnanec_den($zamestnanec,$datum)
{
    global $conn;
    $hodnota = "";
      
    $sql = "select smena,nepritomnost from dochazka where zamestnanec='" . $zamestnanec . "' and datum='" . $datum . "'";
   
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            

    while ($radek = mysqli_fetch_array($vysledek))
    {  
        if ($radek['nepritomnost'] <> '')
        {
            $hodnota = $radek["nepritomnost"];
        }
        else
        {
            $hodnota = $radek["smena"];
        }
    }
    
    mysqli_free_result($vysledek);
    
    return $hodnota;

}

function novy_nabor($id = '')
{    

    global $conn;

    if ($id <> '')
    {
        
        $sql = "select id,prijmeni,jmeno,telefon,adresa,dat_narozeni,stat,dat_evidence,zdroj_inzerce,pozice,klient,klient2,souhlas,rekruter,vysledek,nastup,vystup,koordinator,poznamka,duvod_ukonceni from nabory where id='" . $id . "'";
       
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

        <form name="nabor_new" method="POST" action="nabory.php?typ=updatenabor">
        
        <?php
    }
    else
    {   ?>

        <form name="nabor_new" method="POST" action="nabory.php?typ=savenabor">

        <?php
    }

    ?>

    <!-- Modal -->

    <?php
    if ($id == '')
    {   ?>
            <div class="modal fade" id="nabor_new" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <?php
    }
    else
    {   ?>
            <div class="modal fade" id="ModalNaborInfo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <?php
    }
    ?>
    
    <div class="modal-dialog modal-dialog-centered modal-xl">
        
        <div class="modal-content">

            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel"><?php echo ($id == '') ? "Nový nábor uchazeče o zaměstnání" : "Editace uchazeče o zaměstnání";?></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Příjmení</label>
                        <input type="text" class="form-control bg-primary-subtle" id="prijmeni" name="prijmeni" value="<?php echo ($id == '') ? "" : $polehodnot[0]['prijmeni'];?>" required>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Jméno</label>  
                        <input type="text" class="form-control bg-primary-subtle" id="jmeno" name="jmeno" value="<?php echo ($id == '') ? "" : $polehodnot[0]['jmeno'];?>" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Stát</label>
                        <select class="form-select bg-primary-subtle" name="stat" id="stat">
                            <?php
                                $vybrany = ($id != '' && isset($polehodnot[0]['stat'])) ? $polehodnot[0]['stat'] : '';
                                $stati = [
                                    'PL' => 'Polsko',
                                    'CZ' => 'Česká republika',
                                    'SK' => 'Slovenská republika'
                                ];

                                foreach ($stati as $kod => $nazev) {
                                    $selected = ($kod === $vybrany) ? 'selected' : '';
                                    echo "<option value='" . htmlspecialchars($kod) . "' class='bg-primary-subtle' $selected>$nazev</option>";
                                }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Telefon</label>  
                        <input type="text" class="form-control bg-primary-subtle" id="telefon" name="telefon" value="<?php echo ($id == '') ? "" : $polehodnot[0]['telefon'];?>">
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-9">
                        <label class="form-label">Adresa</label>
                        <input type="text" class="form-control bg-primary-subtle" id="adresa" name="adresa" value="<?php echo ($id == '') ? "" : $polehodnot[0]['adresa'];?>">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Datum narození</label>
                        <input type="date" class="form-control bg-primary-subtle text-center" id="dat_narozeni" name="dat_narozeni"  value="<?php echo ($id == '') ? date('Y-m-d', strtotime('1983-06-28')) : date_format(date_create($polehodnot[0]['dat_narozeni']), 'Y-m-d'); ?>" required>
                    </div>


                </div>
                
                <hr>

                <div class="row mt-2">
                    <div class="col-md-3">
                        <label class="form-label">Datum evidence</label>
                        <input type="date" class="form-control bg-primary-subtle text-center" id="dat_evidence" name="dat_evidence" value="<?php echo ($id == '') ? date('Y-m-d') : date_format(date_create($polehodnot[0]['dat_evidence']), 'Y-m-d'); ?>" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Zdroj inzerce</label>
                        <select class="form-select bg-primary-subtle" name="zdroj" id="zdroj" required>
                            <?php
                                $vybrany = ($id != '' && isset($polehodnot[0]['zdroj_inzerce'])) ? $polehodnot[0]['zdroj_inzerce'] : '';
                                $zdroje = ['OLX', 'Praca.pl', 'GoWork.pl', 'Doporučení', 'Televizní reklama', 'Novinová reklama', 'Facebook', 'Náborový leták'];

                                foreach ($zdroje as $zdroj) {
                                    $selected = ($zdroj === $vybrany) ? 'selected' : '';
                                    echo "<option value='" . htmlspecialchars($zdroj) . "' class='bg-primary-subtle' $selected>$zdroj</option>";
                                }
                            ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Pozice</label>
                        <select class="form-select bg-primary-subtle" name="pozice" id="pozice" required>
                            <?php
                                $vybrany = ($id != '' && isset($polehodnot[0]['pozice'])) ? $polehodnot[0]['pozice'] : '';
                                $pozice = ['OPV', 'Man.Dělník', 'Stroj.Dělník', 'Skladník', 'Skladník VZV', 'Svářeč', 'Řidič zakázky'];

                                foreach ($pozice as $p) {
                                    $selected = ($p === $vybrany) ? 'selected' : '';
                                    echo "<option value='" . htmlspecialchars($p) . "' class='bg-primary-subtle' $selected>$p</option>";
                                }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Klient</label>
                        <select class="form-select bg-primary-subtle" name="klient" id="klient" required>
                            <?php
                                $sql = "SELECT cilova FROM zamestnanci WHERE cilova <> '' GROUP BY cilova ORDER BY cilova";
                                $result = mysqli_query($conn, $sql);

                                if (!$result) {
                                    echo "<option disabled>Chyba dotazu: " . htmlspecialchars(mysqli_error($conn)) . "</option>";
                                } else {
                                    $vybrany = ($id != '' && isset($polehodnot)) ? $polehodnot[0]['klient'] : '';

                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $cilova = htmlspecialchars($row['cilova']);
                                        $selected = ($cilova == $vybrany) ? 'selected' : '';
                                        echo "<option value='$cilova' class='bg-primary-subtle' $selected>$cilova</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Klient 2</label>
                        <select class="form-select bg-primary-subtle" name="klient2" id="klient2">
                            <?php
                                $vybrany = ($id != '' && isset($polehodnot[0]['klient2'])) ? $polehodnot[0]['klient2'] : '';

                                $sql = "SELECT cilova FROM zamestnanci WHERE cilova <> '' GROUP BY cilova ORDER BY cilova";
                                $result = mysqli_query($conn, $sql);

                                if (!$result) {
                                    echo "<option disabled>Chyba dotazu: " . htmlspecialchars(mysqli_error($conn)) . "</option>";
                                } else {
                                    // Přidej prázdnou možnost na začátek
                                    echo "<option value=''" . ($vybrany === '' ? ' selected' : '') . ">-</option>";

                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $cilova = htmlspecialchars($row['cilova']);
                                        $selected = ($cilova === $vybrany) ? ' selected' : '';
                                        echo "<option value='$cilova' class='bg-primary-subtle'$selected>$cilova</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>

                </div>

                <div class="row mt-2">
                    
                    <div class="col-md-3">
                        <label class="form-label">Souhlas</label>
                        <select class="form-select bg-primary-subtle" name="souhlas" id="souhlas" required>
                            <?php
                                $moznosti = ['NE', 'ANO'];
                                $vybrany = ($id != '' && isset($polehodnot)) ? $polehodnot[0]['souhlas'] : '';
                                foreach ($moznosti as $opt) {
                                    $sel = ($opt == $vybrany) ? 'selected' : '';
                                    echo "<option value='$opt' class='bg-primary-subtle' $sel>$opt</option>";
                                }
                            ?>
                        </select>
                    </div>


                    <div class="col-md-3">
                        <label class="form-label">Rekrutér</label>
                        <select class="form-select bg-primary-subtle" name="rekruter" id="rekruter" required>
                            <?php
                                $sql = "SELECT uzivatel FROM uzivatele WHERE typ = 6 ORDER BY uzivatel";
                                $result = mysqli_query($conn, $sql);
                                $vybrany = ($id != '' && isset($polehodnot)) ? $polehodnot[0]['rekruter'] : '';

                                while ($row = mysqli_fetch_assoc($result)) {
                                    $user = htmlspecialchars($row['uzivatel']);
                                    $selected = ($user == $vybrany) ? 'selected' : '';
                                    echo "<option value='$user' class='bg-primary-subtle' $selected>$user</option>";
                                } 
                            ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Výsledek</label>
                        <select class="form-select bg-primary-subtle" name="vysledek" id="vysledek" required>
                            <?php
                                $moznosti = ['Přijat', 'Zamítnut', 'Čeká se', 'Nedostavil se', 'Emergency'];
                                $vybrany = ($id != '' && isset($polehodnot)) ? $polehodnot[0]['vysledek'] : '';
                                foreach ($moznosti as $opt) {
                                    $sel = ($opt == $vybrany) ? 'selected' : '';
                                    echo "<option value='$opt' class='bg-primary-subtle' $sel>$opt</option>";
                                }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Koordinátor</label>
                        <select class="form-select bg-primary-subtle" name="koordinator" id="koordinator" required>
                            <?php
                                $sql = "SELECT uzivatel FROM uzivatele WHERE typ = 1 ORDER BY uzivatel";
                                $result = mysqli_query($conn, $sql);
                                $vybrany = ($id != '' && isset($polehodnot)) ? $polehodnot[0]['koordinator'] : '';

                                while ($row = mysqli_fetch_assoc($result)) {
                                    $user = htmlspecialchars($row['uzivatel']);
                                    $selected = ($user == $vybrany) ? 'selected' : '';
                                    echo "<option value='$user' class='bg-primary-subtle' $selected>$user</option>";
                                }
                            ?>
                        </select>
                    </div>

                </div>

                <hr>   

                <div class="row mt-2">
                                 
                    <div class="col-md-3">
                        <label class="form-label">Nástup</label>
                        <input 
                            type="date" 
                            class="form-control bg-primary-subtle text-center" 
                            id="dat_nastup" 
                            name="dat_nastup"  
                            value="<?php echo ($id == '') ? '' : (($polehodnot[0]['nastup'] == '0000-00-00') ? '' : date_format(date_create($polehodnot[0]['nastup']), 'Y-m-d')); ?>"
                        >
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Výstup</label>
                        <input 
                            type="date" 
                            class="form-control bg-primary-subtle text-center" 
                            id="dat_vystup" 
                            name="dat_vystup"  
                            value="<?php echo ($id == '') ? '' : (($polehodnot[0]['vystup'] == '0000-00-00') ? '' : date_format(date_create($polehodnot[0]['vystup']), 'Y-m-d')); ?>"
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Důvod ukončení</label>
                        <input type="text" class="form-control bg-primary-subtle" id="duvod_ukonceni" name="duvod_ukonceni" value="<?php echo ($id == '') ? "" : $polehodnot[0]['duvod_ukonceni'];?>">
                    </div>

                </div>

                <hr>

                <div class="row mt-2">
                                 
                    <div class="col-md-12">
                        <label class="form-label">Poznámka</label>  
                        <textarea class="form-control bg-primary-subtle" id="poznamka" name="poznamka" style="height: 100px" value="<?php echo ($id == '') ? "" : $polehodnot[0]['poznamka'];?>"></textarea>
                    </div>        

                </div>
 
                <input type="hidden" class="form-control" id="radek_v_db" name="radek_v_db" placeholder="" value="<?php echo ($id == '') ? "" : $polehodnot[0]['id'];?>">
                
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

function nove_auto($id = '')
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
                        <label class="form-label">SPZ</label>
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

    if ($id <> '')
    {
        global $conn;

        $sql = "select id,firma,objednavka,zmenasmen,zmenaprovedena,zmenastatus,aktivni from firmy where id='" . $id . "'";
       
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

        <form name="firma_new" method="POST" action="firmy.php?typ=updatefirma">
        
        <?php
    }
    else
    {   ?>

        <form name="firma_new" method="POST" action="firmy.php?typ=vytvorfirmu">

        <?php
    }

    ?>

    <!-- Modal -->

    <?php
    if ($id == '')
    {   ?>
            <div class="modal fade" id="firma_new" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <?php
    }
    else
    {   ?>
            <div class="modal fade" id="ModalFirmaInfo<?php echo $polehodnot[0]['id'];?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <?php
    }
    ?>
    
    <div class="modal-dialog modal-dialog-centered modal-lg">
        
        <div class="modal-content">

            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel"><?php echo ($id == '') ? "Nová firma" : "Editace firmy";?></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">

                <div class="row">
                    <div class="col col-md-12 mt-2 text-start">
                        <label class="form-label">Název firmy</label>
                        <input type="text" class="form-control bg-primary-subtle" id="nazevfirmy" name="nazevfirmy" value="<?php echo ($id == '') ? "" : $polehodnot[0]['firma'];?>" required>
                    </div>
                    
                    <div class="col col-md-6 mt-2 text-start">
                        <label class="form-label">Objednávka</label>  
                        <input type="text" class="form-control bg-primary-subtle" id="objednavka" name="objednavka" value="<?php echo ($id == '') ? "" : $polehodnot[0]['objednavka'];?>" required>
                    </div>
        
                    <div class="col col-md-6 mt-2 text-start">
                        <label class="form-label">Aktivní / neaktivní</label>  
                        <select class="form-select bg-primary-subtle" name="aktivni" id="aktivni" required>
                            <?php
                                echo (isset($polehodnot) && $polehodnot[0]['aktivni'] == '1' && $id <> '') ? "<option value='1' class='bg-primary-subtle' selected>Aktivní</option>" : "<option value='1' class='bg-primary-subtle'>Aktivní</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['aktivni'] == '0' && $id <> '') ? "<option value='0' class='bg-primary-subtle' selected>Neaktivní</option>" : "<option value='0' class='bg-primary-subtle'>Neaktivní</option>";
                            ?>
                        </select>
                    </div>

                </div>

                <?php
                if (isset($polehodnot))
                {   ?>

                    <div class="row">
                        <div class="col col-md-6 mt-2 text-start">
                            
                            <?php
                                $date=date_format(date_create($polehodnot[0]['zmenasmen']),"d.m.Y");
                            ?>

                            <label for="datepicker">Datum pro překlopení směn</label>
                            <input type="text" class="form-control mt-2 common-datepicker bg-primary-subtle" id="zmenasmen" name="zmenasmen" placeholder="Vyber datum" value="<?php echo $date;?>">
                        
                        </div>

                        <div class="col col-md-6 mt-2 text-start">

                            <label for="datepicker">Čas překlopení směn</label>
                    
                            <select class="form-select bg-primary-subtle mt-2" name="caszmeny" id="caszmeny" required>
                                <?php
                                   
                                    $hodiny = date_format(date_create($polehodnot[0]['zmenasmen']),"H");
                                    $minuty = date_format(date_create($polehodnot[0]['zmenasmen']),"i");
                            
                                    for ($x = 0; $x <= 23; $x++) 
                                    {
                                        echo ($minuty == "00" && $x == $hodiny) ? "<option value='" . $x . ":00' class='bg-primary-subtle' selected>" . $hodiny . ":00</option>" : "<option value='" . $x . ":00' class='bg-primary-subtle'>" . $x . ":00</option>";
                                        echo ($minuty == "10" && $x == $hodiny) ? "<option value='" . $x . ":10' class='bg-primary-subtle' selected>" . $hodiny . ":10</option>" : "<option value='" . $x . ":10' class='bg-primary-subtle'>" . $x . ":10</option>";
                                        echo ($minuty == "20" && $x == $hodiny) ? "<option value='" . $x . ":20' class='bg-primary-subtle' selected>" . $hodiny . ":20</option>" : "<option value='" . $x . ":20' class='bg-primary-subtle'>" . $x . ":20</option>";
                                        echo ($minuty == "30" && $x == $hodiny) ? "<option value='" . $x . ":30' class='bg-primary-subtle' selected>" . $hodiny . ":30</option>" : "<option value='" . $x . ":30' class='bg-primary-subtle'>" . $x . ":30</option>";
                                        echo ($minuty == "40" && $x == $hodiny) ? "<option value='" . $x . ":40' class='bg-primary-subtle' selected>" . $hodiny . ":40</option>" : "<option value='" . $x . ":40' class='bg-primary-subtle'>" . $x . ":40</option>";
                                        echo ($minuty == "50" && $x == $hodiny) ? "<option value='" . $x . ":50' class='bg-primary-subtle' selected>" . $hodiny . ":50</option>" : "<option value='" . $x . ":50' class='bg-primary-subtle'>" . $x . ":50</option>";
                                    }
                                ?>
                            </select>

                        </div>

                    </div>

                    <?php
                }
                ?>               
 
                <input type="hidden" class="form-control" id="id_firmy" name="id_firmy" placeholder="" value="<?php echo ($id == '') ? "" : $polehodnot[0]['id'];?>">
                
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

function novy_zamestnanec_modal()
{    
  
    global $conn;

    $sql = "SELECT MAX(CAST(os_cislo AS UNSIGNED))+1 AS maximalni_cislo FROM zamestnanci WHERE CAST(os_cislo AS UNSIGNED) IS NOT NULL";
    
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }

    $polehodnot = array();

    while ($radek = mysqli_fetch_array($vysledek, MYSQLI_ASSOC)) 
    {
        $polehodnot[] = $radek;
    }
    
    mysqli_free_result($vysledek);

    ?>

    <form name="zamestnanec" method="POST" action="zamestnanci.php?typ=savezamestnance">

    <!-- Modal -->
    <div class="modal fade" id="ModalNovyZam" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  
    <div class="modal-dialog modal-dialog-centered modal-xl">
        
        <div class="modal-content">

            <div class="modal-header">
                <h1 class="modal-title fs-5 text-dark" id="exampleModalLabel">Přidání nového zaměstnance</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">

                <div class="container">
                                
                    <form class="row g-3" action="zamestnanci.php?typ=savezamestnance" method="post">

                    <div class="row mt-3">

                        <div class="col col-md-3">
                            <label for="floatingInputGrid">Příjmení</label>
                            <input type="text" class="form-control bg-primary-subtle" id="prijmeni" name="prijmeni" placeholder="" value="" required>
                        </div>

                        <div class="col col-md-3">
                            <label for="floatingInputGrid">Jméno</label>
                            <input type="text" class="form-control bg-primary-subtle text-center" id="jmeno" name="jmeno" placeholder="" value="" required>
                        </div>

                        <div class="col col-md-2">
                            <label for="floatingInputGrid">Os. číslo Premier</label>
                            <input type="text" class="form-control bg-primary-subtle" id="oscislo2" name="oscislo2" placeholder="" value="" required>
                        </div>

                        <div class="col col-md-4">
                            <label for="floatingInputGrid">Adresa</label>
                            <input type="text" class="form-control bg-primary-subtle" id="adresa" name="adresa" placeholder="" value="" required>
                        </div>
                        
                    </div>

                    <div class="row mt-3">
                        <div class="col col-md-4">
                            <label for="floatingInputGrid">RFID</label>
                            <input type="text" class="form-control bg-primary-subtle" id="rfid" name="rfid" placeholder="" value="" required>
                        </div>
                    
                        <div class="col col-md-4">
                            <label for="floatingInputGrid">Telefon</label>
                            <input type="text" class="form-control bg-primary-subtle" id="telefon" name="telefon" placeholder="" value="">
                        </div>

                        <div class="col col-md-2">
                            
                            <label for="floatingSelect">Směna</label>
                            <select class="form-select bg-primary-subtle" id="smena" name="smena" aria-label="Floating label select example">
                                <option value="N/A" selected>N/A</option>
                                <option value="R">Ranní</option>
                                <option value="O">Odpolední</option>
                                <option value="N">Noční</option>
                                <option value="NN">NN</option>
                                <option value="NR">NR</option>
                                <option value="S-R">Sobota Ranní</option>
                                <option value="S-O">Sobota Odpolední</option>
                                <option value="S-N">Sobota Noční</option>
                                <option value="N-R">Neděle Ranní</option>
                                <option value="N-O">Neděle Odpolední</option>
                                <option value="N-N">Neděle Noční</option>
                                <option value="PR">Přesčas</option>
                            </select>
                        </div>

                        <div class="col col-md-2">
                            
                            <label for="floatingSelect">Směna další týden</label>
                            <select class="form-select bg-primary-subtle" id="smena2" name="smena2" aria-label="Floating label select example">
                                <option value="N/A" selected>N/A</option>
                                <option value="R">Ranní</option>
                                <option value="O">Odpolední</option>
                                <option value="N">Noční</option>
                                <option value="NN">NN</option>
                                <option value="NR">NR</option>
                                <option value="S-R">Sobota Ranní</option>
                                <option value="S-O">Sobota Odpolední</option>
                                <option value="S-N">Sobota Noční</option>
                                <option value="N-R">Neděle Ranní</option>
                                <option value="N-O">Neděle Odpolední</option>
                                <option value="N-N">Neděle Noční</option>
                                <option value="PR">Přesčas</option>
                            </select>
                        </div>

                    </div>

                    <div class="row mt-3">

                        <div class="col col-md-4">
                            
                            <label for="datepicker">Vstup</label>   
                            <input type="text" class="form-control bg-primary-subtle" id="datepicker" name="datepicker"  placeholder="Vyber datum" value="<?php echo date("d.m.Y");?>" required>
                            
                        </div>

                        <div class="col col-md-4">
                            
                            <label for="datepicker2">Výstup</label>
                            <input type="text" class="form-control bg-primary-subtle" id="datepicker2" name="datepicker2"  placeholder="Vyber datum" value="31.12.2023">
                        </div>
                        
                        <div class="col col-md-4">
                            
                            <label for="floatingSelect">Nepřítomnost</label>
                            <select class="form-select bg-primary-subtle" id="nepritomnost" name="nepritomnost" aria-label="Floating label select example">
                                <option value="" selected>Přítomen</option>                
                                <option value="DPN">DPN</option>                            
                                <option value="DOV">Dovolená</option>
                                <option value="ABS">Absence</option>
                            </select>
                            
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col col-md-5">
                            
                            <label for="floatingSelect">Firma</label>
                            <select class="form-select bg-primary-subtle" id="firma" name="firma" aria-label="Floating label select example">
                                <option value="0" selected>Zatím nepřiřazen</option>

                                <?php
                                if ($_SESSION["typ"] == "5")
                                {
                                    $sql = "select firma,id,aktivni from firmy where aktivni='1' order by firma";
                                }
                                else
                                {
                                    $sql = "select firma,id,aktivni from firmy where aktivni='1' and firmy.id in (" . $_SESSION["firma"] . ") order by firma";
                                }                       

                                if (!($vysledek = mysqli_query($conn, $sql)))
                                {
                                die("Nelze provést dotaz</body></html>");
                                }            

                                while ($radek = mysqli_fetch_array($vysledek))
                                {   ?>
                                        <option value="<?php echo $radek["id"];?>"><?php echo $radek["firma"];?></option>                            
                                    <?php          
                                }

                                mysqli_free_result($vysledek);
                                ?>
                                
                            </select>
                        </div>

                        <div class="col col-md-5">
                            
                            <label for="floatingSelect">Zastávka</label>
                            <select class="form-select bg-primary-subtle" id="nastup" name="nastup" aria-label="Floating label select example">
                                <option value="0" selected>Zatím nepřiřazena</option>

                                <?php
                                $sql = "select zastavky.id,auta.spz,zastavka from zastavky left join auta on zastavky.auto = auta.id order by spz,zastavka";

                                if (!($vysledek = mysqli_query($conn, $sql)))
                                {
                                die("Nelze provést dotaz</body></html>");
                                }            

                                while ($radek = mysqli_fetch_array($vysledek))
                                {   ?>
                                        <option value="<?php echo $radek["id"];?>"><?php echo $radek["spz"] . " - " . $radek["zastavka"];?></option>                            
                                    <?php          
                                }

                                mysqli_free_result($vysledek);
                                ?>
                                
                            </select>
                            
                        </div>  
                       
                        <div class="col col-md-2">
                            
                            <label for="floatingInputGrid">Cílová stanice</label>
                            <input type="text" class="form-control bg-primary-subtle" id="cilova" name="cilova" placeholder="" value="">     
                            
                        </div>
                    </div>

                    </div>
                    </div>
            
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary mb-3">Vložit do databáze</button>
                        <button type="button" class="btn btn-secondary mb-3" data-bs-dismiss="modal">Zavřít</button>
                    </div>
           
                    </form>

                </div>              
          
                <input type="hidden" class="form-control" id="oscislo" name="oscislo" placeholder="" value="<?php echo $polehodnot[0]['maximalni_cislo'];?>">
                
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

                        <label class="form-label">Nástupní místo</label>

                        <select class="form-select bg-primary-subtle text-center" id="nastupmisto" name="nastupmisto" aria-label="Floating label select example">
                        <option value="0" selected>Zatím nepřiřazena</option>

                        <?php
                        $sql = "select zastavky.id,auta.spz,zastavka from zastavky left join auta on zastavky.auto = auta.id order by spz,zastavka";

                        if (!($vysledek = mysqli_query($conn, $sql)))
                        {
                        die("Nelze provést dotaz</body></html>");
                        }            

                        while ($radek = mysqli_fetch_array($vysledek))
                        {   
                            if ($polehodnot[0]['nastupmisto'] == $radek["id"])
                            {   ?>
                                    <option value="<?php echo $radek["id"];?>" selected><?php echo $radek["spz"] . " - " . $radek["zastavka"];?></option>
                                <?php
                            }
                            else
                            {   ?>
                                    <option value="<?php echo $radek["id"];?>"><?php echo $radek["spz"] . " - " . $radek["zastavka"];?></option>
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

    // připravíme SQL přes bind_param (bezpečně)
    $stmt = $conn->prepare("SELECT id, os_cislo, os_cislo_klient, prijmeni, jmeno, rfid, nastup, telefon, adresa, firma, smena, aktivni, nepritomnost, smena2, cilova, vstup, vystup, anulace, radneukoncen, dpn_od, smennost, email FROM zamestnanci WHERE id=?");
    $stmt->bind_param("i", $id);
    
    if (!$stmt->execute()) {
        die("Nelze provést dotaz</body></html>");
    }
    
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
    
        // jednoduché přiřazení ostatních polí
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
        $aktivni = $row["aktivni"];
        $nepritomnost = $row["nepritomnost"];
        $cilova = $row["cilova"];
        $ukoncen = $row["radneukoncen"];
        $smennost = $row["smennost"];
        $email = $row["email"];
    
        // datumy s ošetřením 0000-00-00
        $vstup   = ($row["vstup"]   !== "0000-00-00") ? DateTime::createFromFormat('Y-m-d', $row["vstup"])   : null;
        $vystup  = ($row["vystup"]  !== "0000-00-00") ? DateTime::createFromFormat('Y-m-d', $row["vystup"])  : null;
        $anulace = ($row["anulace"] !== "0000-00-00") ? DateTime::createFromFormat('Y-m-d', $row["anulace"]) : null;
        $dpn_od  = ($row["dpn_od"]  !== "0000-00-00") ? DateTime::createFromFormat('Y-m-d', $row["dpn_od"])  : new DateTime('now', new DateTimeZone('Europe/Prague'));
    
    }
    
    $result->free();
    $stmt->close();
                  
    ?> 

    <!-- Modal -->
    <div class="modal fade" id="ModalEditZam" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  
    <div class="modal-dialog modal-dialog-centered modal-xl">
        
        <div class="modal-content">

            <div class="modal-header">
                <h1 class="modal-title fs-5 text-dark" id="exampleModalLabel">Editace zaměstnance</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">

                <form class="row g-3" action="zamestnanci.php?typ=updatezamestnance" method="post">

                <div class="container">           
                    
                    <div class="row mt-3">

                        <div class="col-md-3">
                            <label for="floatingInputGrid">Příjmení</label>
                            <input type="text" class="form-control bg-primary-subtle" id="prijmeni" name="prijmeni" placeholder="" value="<?php echo $prijmeni;?>" required>
                        </div>

                        <div class="col-md-3">
                            <label for="floatingInputGrid">Jméno</label>
                            <input type="text" class="form-control bg-primary-subtle" id="jmeno" name="jmeno" placeholder="" value="<?php echo $jmeno;?>" required>
                        </div>

                        <div class="col-md-2">
                            <label for="floatingInputGrid">Os. č. Premier</label>
                            <input type="text" class="form-control bg-primary-subtle" id="oscislo" name="oscislo" placeholder="" value="<?php echo $os_cislo;?>" required>
                        </div>

                        <div class="col-md-2">
                            <label for="floatingInputGrid">Os. č. klient</label>
                            <input type="text" class="form-control bg-primary-subtle" id="oscislo" name="oscisloklient" placeholder="" value="<?php echo $os_cislo_kl;?>" required>
                        </div>

                        <div class="col-md-2">
                            <label for="floatingSelect">Aktivní</label>
                            <select class="form-select bg-primary-subtle" name="aktivni" id="aktivni" required>
                                <?php
                                    echo (isset($aktivni) && $aktivni == '1') ? "<option value='1' class='bg-primary-subtle' selected>Aktivní</option>" : "<option value='1' class='bg-primary-subtle'>Aktivní</option>";
                                    echo (isset($aktivni) && $aktivni == '0') ? "<option value='0' class='bg-primary-subtle' selected>Neaktivní</option>" : "<option value='0' class='bg-primary-subtle'>Neaktivní</option>";
                                ?>
                            </select>
                        </div> 
                        
                    </div>

                    <div class="row mt-3">

                        <div class="col-md-8">
                            <label for="floatingInputGrid">Adresa</label>
                            <input type="text" class="form-control bg-primary-subtle" id="adresa" name="adresa" placeholder="" value="<?php echo $adresa;?>" required>
                        </div>

                        <div class="col-md-4">
                            <label for="floatingInputGrid">Email</label>
                            <input type="text" class="form-control bg-primary-subtle" id="email" name="email" placeholder="" value="<?php echo $email;?>">
                        </div>

                    </div>

                    <div class="row mt-3">
                        <div class="col-md-3">
                            <label for="floatingInputGrid">RFID</label>
                            <input type="text" class="form-control bg-primary-subtle" id="rfid" name="rfid" placeholder="" value="<?php echo $rfid;?>" required>
                        </div>
                    
                        <div class="col-md-3">
                            <label for="floatingInputGrid">Telefon</label>
                            <input type="text" class="form-control bg-primary-subtle" id="telefon" name="telefon" placeholder="" value="<?php echo $telefon;?>">
                        </div>


                        <div class="col-md-3">

                            <label for="smena">Směna</label>
                            <select class="form-select bg-primary-subtle" name="smena" id="smena" required>
                                <?php
                                $moznosti = [
                                    'N/A' => 'N/A',
                                    'R'   => 'Ranní',
                                    'O'   => 'Odpolední',
                                    'N'   => 'Noční',
                                    'NN'  => 'NN',
                                    'NR'  => 'NR',
                                    'S-R' => 'Sobota Ranní',
                                    'S-O' => 'Sobota Odpolední',
                                    'S-N' => 'Sobota Noční',
                                    'N-R' => 'Neděle Ranní',
                                    'N-O' => 'Neděle Odpolední',
                                    'N-N' => 'Neděle Noční'
                                ];

                                foreach ($moznosti as $hodnota => $text) {
                                    $selected = (isset($smena) && $smena === $hodnota) ? 'selected' : '';
                                    echo "<option value='$hodnota' class='bg-primary-subtle' $selected>$text</option>";
                                }
                                ?>
                            </select>

                        </div>
                        
                        <div class="col-md-3">

                            <label for="smena2">Směna další týden</label>
                            <select class="form-select bg-primary-subtle" name="smena2" id="smena2" required>
                                <?php
                                $moznosti = [
                                    'N/A' => 'N/A',
                                    'R'   => 'Ranní',
                                    'O'   => 'Odpolední',
                                    'N'   => 'Noční',
                                    'NN'  => 'NN',
                                    'NR'  => 'NR',
                                    'S-R' => 'Sobota Ranní',
                                    'S-O' => 'Sobota Odpolední',
                                    'S-N' => 'Sobota Noční',
                                    'N-R' => 'Neděle Ranní',
                                    'N-O' => 'Neděle Odpolední',
                                    'N-N' => 'Neděle Noční'
                                ];

                                foreach ($moznosti as $hodnota => $text) {
                                    $selected = (isset($smena2) && $smena2 === $hodnota) ? 'selected' : '';
                                    echo "<option value='$hodnota' class='bg-primary-subtle' $selected>$text</option>";
                                }
                                ?>
                            </select>

                        </div>   

                    </div>

                    <div class="row mt-3">               

                        <div class="col-md-2">
                            <label for="datepicker">Vstup</label>   
                            <input type="text" class="form-control bg-primary-subtle datepicker" id="datepicker" name="datepicker" placeholder="Vyber datum" value="<?php echo date_format($vstup,'d.m.Y');?>" required>
                        </div>

                        <!-- Výstup s checkboxem -->
                        <div class="col-md-2">
                            <label for="datepicker2">Výstup</label>
                            <div class="input-group">
                                <input type="text" class="form-control bg-primary-subtle datepicker" id="datepicker2" name="datepicker2"
                                    placeholder="Vyber datum"
                                    value="<?php echo ($vystup && $vystup->format('Y-m-d') != '2099-12-31') ? $vystup->format('d.m.Y') : ''; ?>"
                                    <?php echo ($vystup && $vystup->format('Y-m-d') != '2099-12-31') ? '' : 'disabled'; ?>
                                    autocomplete="off" autocorrect="off">
                                <div class="input-group-text">
                                    <input type="checkbox" id="checkbox_vystup" name="checkbox_vystup"
                                        <?php echo ($vystup && $vystup->format('Y-m-d') != '2099-12-31') ? 'checked' : ''; ?>>
                                </div>
                            </div>
                        </div>

                        <!-- Anulace s checkboxem -->
                        <div class="col-md-2">
                            <label for="datepicker3">Anulace</label>
                            <div class="input-group">
                                <input type="text" class="form-control bg-primary-subtle datepicker" id="datepicker3" name="datepicker3"
                                    placeholder="Vyber datum"
                                    value="<?php echo ($anulace && $anulace->format('Y-m-d') != '2099-12-31') ? $anulace->format('d.m.Y') : ''; ?>"
                                    <?php echo ($anulace && $anulace->format('Y-m-d') != '2099-12-31') ? '' : 'disabled'; ?>
                                    autocomplete="off" autocorrect="off">
                                <div class="input-group-text">
                                    <input type="checkbox" id="checkbox_anulace" name="checkbox_anulace"
                                        <?php echo ($anulace && $anulace->format('Y-m-d') != '2099-12-31') ? 'checked' : ''; ?>>
                                </div>
                            </div>
                        </div>

                        <script>
                        document.addEventListener('shown.bs.modal', function (event) {
                            const modal = event.target;

                            // Funkce pro oba inputy
                            function initDateInput(inputId, checkboxId) {
                                const dateInput = modal.querySelector('#' + inputId);
                                const checkbox = modal.querySelector('#' + checkboxId);
                                if (!dateInput || !checkbox) return;

                                console.log("Inicializuji:", inputId);

                                // Inicializace datepickeru
                                $(dateInput).datepicker({
                                    format: 'dd.mm.yyyy',
                                    autoclose: true,
                                    todayHighlight: true,
                                    container: 'body'
                                });

                                // Pokud input už má hodnotu, nastav ji do datepickeru
                                if (dateInput.value) {
                                    const parts = dateInput.value.split('.');
                                    if (parts.length === 3) {
                                        const existingDate = new Date(parts[2], parts[1]-1, parts[0]);
                                        $(dateInput).datepicker('setDate', existingDate);
                                        dateInput.disabled = false;
                                    }
                                }

                                // Inicializace podle checkboxu
                                dateInput.disabled = !checkbox.checked;
                                if (!checkbox.checked) $(dateInput).datepicker('clearDates');

                                // Změna checkboxu
                                checkbox.addEventListener('change', function() {
                                    if (checkbox.checked) {
                                        dateInput.disabled = false;
                                        const today = new Date();
                                        $(dateInput).datepicker('setDate', today);
                                    } else {
                                        dateInput.disabled = true;
                                        $(dateInput).datepicker('clearDates');
                                    }
                                    console.log(checkboxId + " změněn. Disabled:", dateInput.disabled);
                                });
                            }

                            // Inicializace pro Výstup a Anulaci
                            initDateInput('datepicker2', 'checkbox_vystup');
                            initDateInput('datepicker3', 'checkbox_anulace');
                        });
                        </script>
                                           
                        <div class="col-md-3">
                            
                            <label for="nepritomnost">Nepřítomnost</label>
                            <select class="form-select bg-primary-subtle" id="nepritomnost" name="nepritomnost" aria-label="Floating label select example">
                                <?php
                                $moznosti = [
                                    ''     => 'Přítomen',
                                    'DPN'  => 'DPN',
                                    'OČR'  => 'OČR',
                                    'DOV'  => 'Dovolená',
                                    'ABS'  => 'Absence'
                                ];

                                foreach ($moznosti as $hodnota => $text) {
                                    $selected = (isset($nepritomnost) && $nepritomnost === $hodnota) ? 'selected' : '';
                                    echo "<option value='$hodnota' class='bg-primary-subtle' $selected>$text</option>";
                                }
                                ?>
                            </select>
                            
                        </div>

                        <div class="col-md-3">
                            <label for="datepicker2">DPN od (pouze u nepřítomnosti)</label>
                            <input type="text" class="form-control bg-primary-subtle common" id="datepicker4" name="datepicker4"  placeholder="Vyber datum" value="<?php echo ($nepritomnost == '') ? "" : date_format($dpn_od,'d.m.Y');?>">
                        </div>

                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            
                            <label for="floatingSelect">Firma</label>
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
                                    echo ($radek['id'] == $firma) ? "<option value='" . $radek['id'] . "' class='bg-primary-subtle' selected>" . $radek['firma'] . "</option>" : "<option value='" . $radek['id'] . "' class='bg-primary-subtle'>" . $radek['firma'] . "</option>";
                                }

                                mysqli_free_result($vysledek);
                                ?>
                                
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="floatingInputGrid">Cílová stanice</label>
                            <input type="text" class="form-control bg-primary-subtle" id="cilova" name="cilova" placeholder="" value="<?php echo $cilova;?>">     
                        </div>

                        <div class="col-md-3">
                            
                            <label for="smennost">Směnnost</label>
                            <select class="form-select bg-primary-subtle" id="smennost" name="smennost" aria-label="Floating label select example">
                                <?php
                                $moznosti = ['3SM', '3SM 8h', '3SM 12h' ,'4SM A', '4SM B', '4SM C', '4SM D', '4SM A 12h', '4SM B 12h', '4SM C 12h', '4SM D 12h', '4SM A 8h', '4SM B 8h', '4SM C 8h', '4SM D 8h'];
                                foreach ($moznosti as $moznost) {
                                    $selected = (isset($smennost) && $smennost === $moznost) ? 'selected' : '';
                                    echo "<option value='$moznost' class='bg-primary-subtle' $selected>$moznost</option>";
                                }
                                ?>
                            </select>
                            
                        </div>

                    </div>

                    <div class="row mt-3">

                        <div class="col-md-12">
                            
                            <label for="floatingSelect">Zastávka</label>
                            <select class="form-select bg-primary-subtle" id="nastup" name="nastup" aria-label="Floating label select example">
                                <option value="0" selected>Zatím nepřiřazena</option>

                                <?php
                                $sql = "select zastavky.id,auta.spz,zastavka from zastavky left join auta on zastavky.auto = auta.id order by spz,zastavka";

                                if (!($vysledek = mysqli_query($conn, $sql)))
                                {
                                die("Nelze provést dotaz</body></html>");
                                }

                                while ($radek = mysqli_fetch_array($vysledek))
                                {   
                                    echo ($radek['id'] == $nastup) ? "<option value='" . $radek['id'] . "' class='bg-primary-subtle' selected>" . $radek['spz'] . " - " . $radek['zastavka'] . "</option>" : "<option value='" . $radek['id'] . "' class='bg-primary-subtle'>" . $radek['spz'] . " - " . $radek['zastavka'] . "</option>";                                          
                                }

                                mysqli_free_result($vysledek);
                                ?>
                                
                            </select>
                            
                        </div>  
                       
                        <input type="hidden" class="form-control" id="id_zam" name="id_zam" placeholder="" value=<?php echo $id;?>>
                       
                        <div class="modal-footer mt-3">
                            <button type="submit" class="btn btn-primary mb-3">Uložit změnu do databáze</button>
                            <button type="button" class="btn btn-secondary mb-3" data-bs-dismiss="modal">Zavřít</button>
                        </div>                                   

                    </div>

                    </div>
                    </div>            
           
                    </form>

                </div>  
                         
            </div>

        </div>

    </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chkVystup = document.getElementById('chkVystup');
            const txtVystup = document.getElementById('txtVystup');
            const hidVystup = document.getElementById('hidVystup');

            if (chkVystup && txtVystup && hidVystup) {
                // nastavíme počáteční stav podle checkboxu
                txtVystup.disabled = !chkVystup.checked;
                txtVystup.classList.toggle('bg-primary-subtle', chkVystup.checked);
                txtVystup.classList.toggle('bg-secondary-subtle', !chkVystup.checked);

                // listener pro změnu checkboxu
                chkVystup.addEventListener('change', function() {
                    txtVystup.disabled = !this.checked;
                    txtVystup.classList.toggle('bg-primary-subtle', this.checked);
                    txtVystup.classList.toggle('bg-secondary-subtle', !this.checked);

                    if (!this.checked) {
                        txtVystup.value = '';
                        hidVystup.value = '0000-00-00';
                    } else {
                        // pokud zaškrtnuto, nastavíme hidden podle aktuálního inputu
                        hidVystup.value = txtVystup.value ? txtVystup.value.split('.').reverse().join('-') : '0000-00-00';
                    }
                });

                // listener pro změnu inputu, aby se aktualizoval hidden
                txtVystup.addEventListener('change', function() {
                    if (chkVystup.checked) {
                        hidVystup.value = this.value ? this.value.split('.').reverse().join('-') : '0000-00-00';
                    }
                });
            }
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

function vyrob_kalendar_ttt($year, $month, $id_zam)
{ ?>
<div class="container-fluid">
    <div class="modal fade" id="kalendar_dochazka" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                <tr>
                                <?php
                                if (!checkdate($month, 1, $year)) { $month = date('m'); $year = date('Y'); }
                                $first_day = date_create_from_format('Y-n-j', "$year-$month-1");
                                $daysInMonth = date_format($first_day, 't');
                                $weekDay = date_format($first_day, 'N'); // 1=pondělí ... 7=neděle

                                // Kategorie
                                $smeny = ['R','O','N','NN','NR'];
                                $nepritomnosti_warning = ['DPN','OČR','ABS','LEK'];
                                $nepritomnosti_other = ['DOV','NAR','NEM','NEO','NEP','PRO','NEPV','NAHV','OABS'];

                                for ($day = 1; $day <= $daysInMonth; $day++) {
                                    $datum = sprintf('%04d-%02d-%02d', $year, $month, $day);
                                    $den_v_tydnu = date('N', strtotime($datum));
                                    $je_vikend = ($den_v_tydnu >= 6);

                                    $smena = kontrola_dochazky($id_zam, $datum);

                                    // Určení barvy buňky
                                    if ($je_vikend) {
                                        $barva = 'bg-warning-subtle'; // víkend – žlutý
                                    } elseif ($smena === '' || $smena === null) {
                                        $barva = 'bg-danger-subtle'; // N/A – červená
                                    } elseif (in_array($smena, $smeny)) {
                                        $barva = 'bg-success-subtle'; // směny – zelená
                                    } elseif (in_array($smena, $nepritomnosti_warning)) {
                                        $barva = 'bg-secondary-subtle'; // varovné nepřítomnosti – šedá
                                    } elseif (in_array($smena, $nepritomnosti_other)) {
                                        $barva = 'bg-primary-subtle'; // ostatní nepřítomnosti – modrá
                                    } else {
                                        $barva = ''; // výchozí
                                    }
                                    ?>

                                    <td class="text-center <?php echo $barva; ?>">
                                        <label class="form-label fw-bold fs-3 <?php if($je_vikend) echo 'text-danger'; ?>">
                                            <?php echo $day; ?>
                                        </label>

                                        <input type="hidden" name="lasttoggle<?php echo $day; ?>" value="<?php echo $smena; ?>">

                                        <select class="form-select text-center fw-bold fs-5" name="toggle<?php echo $day; ?>" id="toggle<?php echo $day; ?>">
                                            <?php
                                            // N/A – červená
                                            $selected = ($smena === '') ? 'selected' : '';
                                            echo "<option value='' class='bg-danger-subtle' $selected>N/A</option>";

                                            // Směny – zelené
                                            foreach ($smeny as $opt) {
                                                $selected = ($smena === $opt) ? 'selected' : '';
                                                echo "<option value='$opt' class='bg-success-subtle' $selected>$opt</option>";
                                            }

                                            // Varovné nepřítomnosti – šedé
                                            foreach ($nepritomnosti_warning as $opt) {
                                                $selected = ($smena === $opt) ? 'selected' : '';
                                                echo "<option value='$opt' class='bg-secondary-subtle' $selected>$opt</option>";
                                            }

                                            // Ostatní nepřítomnosti – modré
                                            foreach ($nepritomnosti_other as $opt) {
                                                $selected = ($smena === $opt) ? 'selected' : '';
                                                echo "<option value='$opt' class='bg-primary-subtle' $selected>$opt</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>

                                    <?php
                                    // Po neděli nový řádek
                                    if ($den_v_tydnu == 7) echo '</tr><tr>';
                                }
                                ?>

                                </tr>
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
<?php
}



function vyrob_kalendar($year, $month, $id_zam)
{ ?>
<div class="container-fluid">
    <!-- HLAVNÍ MODAL -->
    <div class="modal fade" id="kalendar_dochazka" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
         data-bs-backdrop="static" data-bs-keyboard="false">
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
                            if (!checkdate($month, 1, $year)) { $month = date('m'); $year = date('Y'); }
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
                                            title="<?php echo trim($poznamka) !== '' ? htmlspecialchars($poznamka) : 'Přidej poznámku'; ?>">✏️
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
let aktualniDen = null;

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

function loadXMLFile($filePath, $encoding = 'Windows-1250') 
{
    $xmlContent = file_get_contents($filePath);
    //$xmlContent = iconv($encoding, 'UTF-8', $xmlContent);
    return simplexml_load_string($xmlContent);
}

function processXMLFile($filePath) 
{
// Načtení XML souboru
$xml = loadXMLFile($filePath);

// Definice namespace (pokud je třeba, zde je příklad, jak jej použít)
$namespaces = $xml->getNamespaces(true);
$xml->registerXPathNamespace('ns', $namespaces['']);

// Iterace přes jednotlivé bloky <dochazka_zamestnance>
foreach ($xml->xpath('//ns:dochazka_zamestnance') as $dochazka)
 {
    $hlavicka = $dochazka->hlavicka;
    echo "Měsíc: " . $hlavicka->mesic . "<br>";
    echo "Rok: " . $hlavicka->rok . "<br>";
    echo "Osobní číslo: " . $hlavicka->osobni_cislo . "<br>";
    echo "Osobní Premier: " . get_premier_id_from_customer_id($hlavicka->cislo_pracovniho_pomeru) . "<br>";
    echo "Rodné číslo: " . $hlavicka->rodne_cislo . "<br>";
    echo "Jméno: " . $hlavicka->jmeno . "<br>";
    echo "Příjmení: " . $hlavicka->prijmeni . "<br>";
    echo "Číslo pracovního poměru: " . $hlavicka->cislo_pracovniho_pomeru . "<br>";
    echo "Druh mzdy: " . $hlavicka->druh_mzdy . "<br>";
    echo "Režim denně: " . $hlavicka->rezim_denne . "<br><br>";

    echo "Rozvrh:<br>";
    foreach ($dochazka->rozvrh->uvazek as $uvazek) {
        echo "Datum: " . $uvazek['datum'] . " - " . $uvazek . "<br>";
    }

    echo "<br>Přítomnost:<br>";
    echo "Přesčas pracovní den: " . $dochazka->pritomnost->prescas_pracovni_den->hodiny . "<br>";
    echo "Přesčas den klidu: " . $dochazka->pritomnost->prescas_den_klidu->hodiny . "<br>";

    echo "<br>Mzdy:<br>";
    foreach ($dochazka->mzdy->priplatek as $priplatek) {
        echo "Kód: " . $priplatek->kod . "<br>";
        echo "Název: " . $priplatek->nazev . "<br>";
        echo "Hodiny: " . $priplatek->hodiny . "<br><br>";
    }

    // Zpracování části <nepritomnosti>
    echo "<br>Nepřítomnosti:<br>";
    foreach ($dochazka->nepritomnosti->nepritomnost as $nepritomnost) {
        echo "Docházka kód: " . $nepritomnost->dochazka_kod . "<br>";
        echo "Docházka název: " . $nepritomnost->dochazka_nazev . "<br>";
        echo "Od: " . $nepritomnost->od . "<br>";
        echo "Do: " . $nepritomnost->do . "<br><br>";
    }

    echo "------------------------------<br><br>";
 }
}

function generateXMLFile($data, $outputFile) {
    // Vytvoření nového XML dokumentu
    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="Windows-1250"?><dochazky_zamestnancu xmlns="http://www.stormware.cz/schema/pamica/dochazka.xsd" version="1.0"></dochazky_zamestnancu>');

    // Přidání zaměstnanců do XML
    foreach ($data as $zamestnanec) {
        $dochazka = $xml->addChild('dochazka_zamestnance');
        $hlavicka = $dochazka->addChild('hlavicka');
        
        $hlavicka->addChild('mesic', $zamestnanec['hlavicka']['mesic']);
        $hlavicka->addChild('rok', $zamestnanec['hlavicka']['rok']);
        $hlavicka->addChild('osobni_cislo', $zamestnanec['hlavicka']['osobni_cislo']);
        $hlavicka->addChild('rodne_cislo', $zamestnanec['hlavicka']['rodne_cislo']);
        $hlavicka->addChild('jmeno', $zamestnanec['hlavicka']['jmeno']);
        $hlavicka->addChild('prijmeni', $zamestnanec['hlavicka']['prijmeni']);
        $hlavicka->addChild('pracovni_pomer', $zamestnanec['hlavicka']['pracovni_pomer']);
        $hlavicka->addChild('cislo_pracovniho_pomeru', $zamestnanec['hlavicka']['cislo_pracovniho_pomeru']);
        $hlavicka->addChild('druh_mzdy', $zamestnanec['hlavicka']['druh_mzdy']);
        $hlavicka->addChild('rezim_denne', $zamestnanec['hlavicka']['rezim_denne']);
        
        $rozvrh = $dochazka->addChild('rozvrh');
        foreach ($zamestnanec['rozvrh'] as $uvazek) {
            $uvazekNode = $rozvrh->addChild('uvazek', $uvazek['hodiny']);
            $uvazekNode->addAttribute('datum', $uvazek['datum']);
        }
        
        $nepritomnosti = $dochazka->addChild('nepritomnosti');
        foreach ($zamestnanec['nepritomnosti'] as $nepritomnost) {
            $nepritomnostNode = $nepritomnosti->addChild('nepritomnost');
            $nepritomnostNode->addChild('dochazka_kod', $nepritomnost['dochazka_kod']);
            $nepritomnostNode->addChild('dochazka_nazev', $nepritomnost['dochazka_nazev']);
            $nepritomnostNode->addChild('od', $nepritomnost['od']);
            $nepritomnostNode->addChild('do', $nepritomnost['do']);
        }
        
        $pritomnost = $dochazka->addChild('pritomnost');
        $pritomnost->addChild('prescas_pracovni_den')->addChild('hodiny', $zamestnanec['pritomnost']['prescas_pracovni_den']);
        $pritomnost->addChild('prescas_den_klidu')->addChild('hodiny', $zamestnanec['pritomnost']['prescas_den_klidu']);
        
        $mzdy = $dochazka->addChild('mzdy');
        foreach ($zamestnanec['mzdy'] as $priplatek) {
            $priplatekNode = $mzdy->addChild('priplatek');
            $priplatekNode->addChild('kod', $priplatek['kod']);
            $priplatekNode->addChild('nazev', $priplatek['nazev']);
            $priplatekNode->addChild('hodiny', $priplatek['hodiny']);
        }
    }

    // Uložení XML do souboru
    $xml->asXML($outputFile);
}

function get_premier_id_from_customer_id($os_cislo_klient)
{
    global $conn;
    
    $hodnota = "Nezjištěno";
    $sql = "select os_cislo from zamestnanci where os_cislo_klient='" . $os_cislo_klient . "'";
    
    //echo $sql;

    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }

    while ($radek = mysqli_fetch_array($vysledek))
    {
        $hodnota = $radek['os_cislo'];
    }
    
    mysqli_free_result($vysledek);

    return "<b>" . $hodnota . "</b>";
}

function modal_doprava()
{ ?>

  <!-- Modal -->
  <div class="modal fade" id="modal_doprava" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">
        
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel"></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
              
        <table class="table table-sm">
          <thead>
            <tr>
              <th scope="col">Auto</th>
              <th scope="col">Trasa</th>
              <th scope="col"></th>
            </tr>
          </thead>

              <?php

                global $conn;
                $sql = "select id,spz,oznaceni from auta order by spz";

                if (!($vysledek = mysqli_query($conn, $sql)))
                {
                die("Nelze provést dotaz.</body></html>");
                }

                while ($radek = mysqli_fetch_array($vysledek))
                {                    
                    ?>
                    <tr>
                    <td scope="col"><?php echo $radek["spz"];?></td>
                    <td scope="col"><?php echo $radek["oznaceni"];?></td>
                    <td><a type="button" class="btn btn-sm btn-primary m-1" href="main.php?typ=zmenabus&bus=<?php echo $radek['id'];?>">Vyber</button></a></td>
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
    ?>

    <div class="collapse" id="filtry">
        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
            <form class="row g-3" action="zamestnanci.php?typ=filtr" method="post" name="zamestnanci">

                <?php
                global $conn;

                // Načtení uložených filtrů ze SESSION
                $vybercilova = $_SESSION['filtry']['cilova'] ?? 'ALL';
                $vyberpomer = $_SESSION['filtry']['pomer'] ?? '1';
                $vybersmena = $_SESSION['filtry']['smena'] ?? 'R';
                $vybernepritomnost = $_SESSION['filtry']['nepritomnost'] ?? '';

                // Výběr cílových stanic
                $sql = "SELECT cilova FROM zamestnanci WHERE cilova <> '' GROUP BY cilova ORDER BY cilova";
                $vysledek = mysqli_query($conn, $sql) or die("Nelze provést dotaz</body></html>");
                $stanice = mysqli_fetch_all($vysledek, MYSQLI_ASSOC);
                mysqli_free_result($vysledek);
                ?>
                
                <div class="col-md-3">
                    <label for="cilova">Okruh dopravy</label>
                    <select class="form-select mt-2" id="cilova" name="cilova">
                        <option value="ALL" <?= ($vybercilova == 'ALL') ? 'selected' : '' ?>>Všechny cílové stanice</option>
                        <?php foreach($stanice as $s) : ?>
                            <option value="<?= htmlspecialchars($s['cilova']) ?>" <?= ($vybercilova == $s['cilova']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['cilova']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="pomer">Pracovní poměr</label>
                    <?php
                    $pomery = [
                        'Vše' => 'Všechny poměry',
                        '1' => 'Aktivní',
                        '0' => 'Neaktivní',
                        '33' => 'Řádně ukončen',
                        '44' => 'Řádně neukončen',
                        '55' => 'Anulován',
                    ];
                    ?>
                    <select class="form-select mt-2" id="pomer" name="pomer" onchange="change_pomer(this.value);">
                        <?php foreach($pomery as $val => $txt) : ?>
                            <option value="<?= $val ?>" <?= ($val == $vyberpomer) ? 'selected' : '' ?>><?= $txt ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="smena">Směna</label>
                    <?php
                    $smeny = ['VŠE'=>'Všechny směny', 'R'=>'Ranní','O'=>'Odpolední','N'=>'Noční','NN'=>'NN','NR'=>'NR','S-R'=>'Sobota Ranní','S-O'=>'Sobota Odpolední','S-N'=>'Sobota Noční','N-R'=>'Neděle Ranní','N-O'=>'Neděle Odpolední','N-N'=>'Neděle Noční','PR'=>'Přesčas','N/A'=>'N/A'];
                    ?>
                    <select class="form-select mt-2" id="smena" name="smena">
                        <?php foreach($smeny as $val => $txt) : ?>
                            <option value="<?= $val ?>" <?= ($val == $vybersmena) ? 'selected' : '' ?>><?= $txt ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="nepritomnost">Nepřítomnost</label>
                    <?php
                    $nepritomnosti = 
                        ['ALL' => 'Všechny záznamy',
                        '' => 'Přítomní',
                        'DPN' => 'DPN',
                        'OČR' => 'OČR',
                        'DOV' => 'Dovolená',
                        'ABS' => 'Absence',
                        'NAR' => 'Narozeniny',
                        'LEK' => 'Lékař',
                        'NEM' => 'Nemoc',
                        'NEO' => 'Neomluvená absence',
                        'NEP' => 'Neplacené volno',
                        'PRO' => 'Prostoj',
                        'Vše' => 'Všechny nepřítomnosti'];
                    ?>
                    <select class="form-select mt-2" id="nepritomnost" name="nepritomnost">
                        <?php foreach($nepritomnosti as $val => $txt) : ?>
                            <option value="<?= $val ?>" <?= ($val === $vybernepritomnost) ? 'selected' : '' ?>><?= $txt ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-12 mt-2">
                    <button type="submit" class="form-control btn btn-primary">Proveď výběr</button>
                </div>

            </form>
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
?>

