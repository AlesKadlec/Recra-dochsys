<?php
// Zahrnutí souboru s údaji o připojení
include('db.php');

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
                                        <li class="nav-item"><a class="dropdown-item text-primary" href="report4.php">Report docházky ve třísměnném provoze</a></li>
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

function zjisti_pocet_nastupujicich($firma,$smena,$zastavka)
{
    global $conn;

    $sql = "select count(*) as pocet from zamestnanci where firma='" . $firma . "' and nastup='" . $zastavka . "' and smena='" . $smena . "' and nepritomnost=''";

    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            

    while ($radek = mysqli_fetch_array($vysledek))
    {  
        $text = $radek["pocet"];
    }
    
    mysqli_free_result($vysledek);
    
    return $text;

}

function zjisti_pocet_autobusu($firma,$smena,$zastavka)
{
    global $conn;
  
    $sql = "select count(*) as pocet from dochazka where firma='" . $firma . "' and zastavka='" . $zastavka . "' and smena='" . $smena . "' and now() <= DATE_ADD(concat(datum,' ', cas), INTERVAL 3 HOUR)";

    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            

    while ($radek = mysqli_fetch_array($vysledek))
    {  
        $text = $radek["pocet"];
    }
    
    mysqli_free_result($vysledek);
    
    return $text;

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

function insert_attandance_manually($id_emp,$bus,$zastavka,$firma,$smena,$datum,$cas) 
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
      $dotaz="insert into dochazka (zamestnanec,datum,cas,smena,bus,firma,zastavka,ip,cron,nepritomnost) values ('" . $id_emp . "','" . $datum . "','" . $cas . "','" . $smena . "','" . $bus . "','" . $firma . "','" . $zastavka . "','" . $_SERVER['REMOTE_ADDR'] . "','2','')"; 
            
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
              <th scope="col">Osobní číslo</th>
              <th scope="col">Příjmení</th>
              <th scope="col">Jméno</th>
              <th scope="col">Nastoupil</th>
              <th scope="col">Datum a Čas</th>
              <th scope="col">DPN/DOV</th>
              
            </tr>
          </thead>

              <?php

                global $conn;
                $sql = "select id,os_cislo,prijmeni,jmeno,rfid,nastup,telefon,adresa,firma,smena,nepritomnost from zamestnanci where firma='" . $firma . "' and smena='" . $smena . "' and nastup='" . $nastup . "' and aktivni='1'";

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
                    
                    <td scope="col"><?php echo $radek["os_cislo"];?></td>
                    <td scope="col"><?php echo $radek["prijmeni"];?></td>
                    <td scope="col"><?php echo $radek["jmeno"];?></td>                   
                    <td scope="col"><?php echo $pole[0];?></td>
                    <td scope="col"><?php echo $pole[1] . " " . $pole[2];?></td>                                  
                    <td scope="col"><?php echo $radek["nepritomnost"];?></td>          
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
        if (($smena == "R") or ($smena == "O") or ($smena == "N") or ($smena == "NN") or ($smena == "NR") or ($smena == "VK") or ($smena == "PR") or ($smena == "S-R") or ($smena == "S-O") or ($smena == "S-N") or ($smena == "N-R") or ($smena == "N-O") or ($smena == "N-N"))
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
            echo "Není čas na vložení automatické docházky";
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
                insert_attandance($radek["id"],$id_auta,$id_zastavky,$radek["firma"],$smena,'1',$radek["nepritomnost"]);
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

    if ($id <> '')
    {
        global $conn;

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
            <div class="modal fade" id="ModalNaborInfo<?php echo $polehodnot[0]['id'];?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                echo (isset($polehodnot) && $polehodnot[0]['stat'] == 'PL' && $id <> '') ? "<option value='PL' class='bg-primary-subtle' selected>Polsko</option>" : "<option value='PL' class='bg-primary-subtle'>Polsko</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['stat'] == 'CZ' && $id <> '') ? "<option value='CZ' class='bg-primary-subtle' selected>Česká republika</option>" : "<option value='CZ' class='bg-primary-subtle'>Česká republika</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['stat'] == 'SK' && $id <> '') ? "<option value='SK' class='bg-primary-subtle' selected>Slovenská republika</option>" : "<option value='SK' class='bg-primary-subtle'>Slovenská republika</option>";
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
                        <input type="text" class="form-control bg-primary-subtle text-center common-datepicker1" id="dat_narozeni" name="dat_narozeni"  placeholder="Vyber datum" value="<?php echo ($id == '') ? "28.6.1983" : date_format(date_create($polehodnot[0]['dat_narozeni']),"d.m.Y");?>" required>                   
                    </div>

                </div>

                <hr>

                <div class="row mt-2">
                    <div class="col-md-3">
                        <label class="form-label">Datum evidence</label>  
                        <input type="text" class="form-control bg-primary-subtle text-center common-datepicker2" id="dat_evidence" name="dat_evidence"  placeholder="Vyber datum" value="<?php echo ($id == '') ? date("d.m.Y") : date_format(date_create($polehodnot[0]['dat_evidence']),"d.m.Y");?>" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Zdroj inzerce</label>  
                        <select class="form-select bg-primary-subtle" name="zdroj" id="zdroj" required>
                            <?php
                                echo (isset($polehodnot) && $polehodnot[0]['zdroj_inzerce'] == 'OLX' && $id <> '') ? "<option value='OLX' class='bg-primary-subtle' selected>OLX</option>" : "<option value='OLX' class='bg-primary-subtle'>OLX</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['zdroj_inzerce'] == 'Praca.pl' && $id <> '') ? "<option value='Praca.pl' class='bg-primary-subtle' selected>Praca.pl</option>" : "<option value='Praca.pl' class='bg-primary-subtle'>Praca.pl</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['zdroj_inzerce'] == 'GoWork.pl' && $id <> '') ? "<option value='GoWork.pl' class='bg-primary-subtle' selected>GoWork.pl</option>" : "<option value='GoWork.pl' class='bg-primary-subtle'>GoWork.pl</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['zdroj_inzerce'] == 'Doporučení' && $id <> '') ? "<option value='Doporučení' class='bg-primary-subtle' selected>Doporučení</option>" : "<option value='Doporučení' class='bg-primary-subtle'>Doporučení</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['zdroj_inzerce'] == 'Televizní reklama' && $id <> '') ? "<option value='Televizní reklama' class='bg-primary-subtle' selected>Televizní reklama</option>" : "<option value='Televizní reklama' class='bg-primary-subtle'>Televizní reklama</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['zdroj_inzerce'] == 'Novinová reklama' && $id <> '') ? "<option value='Novinová reklama' class='bg-primary-subtle' selected>Novinová reklama</option>" : "<option value='Novinová reklama' class='bg-primary-subtle'>Novinová reklama</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['zdroj_inzerce'] == 'Facebook' && $id <> '') ? "<option value='Facebook' class='bg-primary-subtle' selected>Facebook</option>" : "<option value='Facebook' class='bg-primary-subtle'>Facebook</option>";
                            ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Pozice</label>  
                        <select class="form-select bg-primary-subtle" name="pozice" id="pozice" required>
                            <?php
                                echo (isset($polehodnot) && $polehodnot[0]['pozice'] == 'OPV' && $id <> '') ? "<option value='OPV' class='bg-primary-subtle' selected>OPV</option>" : "<option value='OPV' class='bg-primary-subtle'>OPV</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['pozice'] == 'Man.Dělník' && $id <> '') ? "<option value='Man.Dělník' class='bg-primary-subtle' selected>Man.Dělník</option>" : "<option value='Man.Dělník' class='bg-primary-subtle'>Man.Dělník</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['pozice'] == 'Stroj.Dělník' && $id <> '') ? "<option value='Stroj.Dělník' class='bg-primary-subtle' selected>Stroj.Dělník</option>" : "<option value='Stroj.Dělník' class='bg-primary-subtle'>Stroj.Dělník</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['pozice'] == 'Skladník' && $id <> '') ? "<option value='Skladník' class='bg-primary-subtle' selected>Skladník</option>" : "<option value='Skladník' class='bg-primary-subtle'>Skladník</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['pozice'] == 'Skladník VZV' && $id <> '') ? "<option value='Skladník VZV' class='bg-primary-subtle' selected>Skladník VZV</option>" : "<option value='Skladník VZV' class='bg-primary-subtle'>Skladník VZV</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['pozice'] == 'Svářeč' && $id <> '') ? "<option value='Svářeč' class='bg-primary-subtle' selected>Svářeč</option>" : "<option value='Svářeč' class='bg-primary-subtle'>Svářeč</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['pozice'] == 'Řidič zakázky' && $id <> '') ? "<option value='Řidič zakázky' class='bg-primary-subtle' selected>Řidič zakázky</option>" : "<option value='Řidič zakázky' class='bg-primary-subtle'>Řidič zakázky</option>";
                            ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Klient</label>
                        <select class="form-select bg-primary-subtle" name="klient" id="klient" required>
                            <?php
                                echo (isset($polehodnot) && $polehodnot[0]['klient'] == 'BATZ' && $id <> '') ? "<option value='BATZ' class='bg-primary-subtle' selected>BATZ</option>" : "<option value='BATZ' class='bg-primary-subtle'>BATZ</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['klient'] == 'CENTRÁLA' && $id <> '') ? "<option value='CENTRÁLA' class='bg-primary-subtle' selected>CENTRÁLA</option>" : "<option value='CENTRÁLA' class='bg-primary-subtle'>CENTRÁLA</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['klient'] == 'DONGHEE' && $id <> '') ? "<option value='DONGHEE' class='bg-primary-subtle' selected>DONGHEE</option>" : "<option value='DONGHEE' class='bg-primary-subtle'>DONGHEE</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['klient'] == 'DSC' && $id <> '') ? "<option value='DSC' class='bg-primary-subtle' selected>DSC</option>" : "<option value='DSC' class='bg-primary-subtle'>DSC</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['klient'] == 'HANON' && $id <> '') ? "<option value='HANON' class='bg-primary-subtle' selected>HANON</option>" : "<option value='HANON' class='bg-primary-subtle'>HANON</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['klient'] == 'HILITE' && $id <> '') ? "<option value='HILITE' class='bg-primary-subtle' selected>HILITE</option>" : "<option value='HILITE' class='bg-primary-subtle'>HILITE</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['klient'] == 'KMJ' && $id <> '') ? "<option value='KMJ' class='bg-primary-subtle' selected>KMJ</option>" : "<option value='KMJ' class='bg-primary-subtle'>KMJ</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['klient'] == 'TATRA' && $id <> '') ? "<option value='TATRA' class='bg-primary-subtle' selected>TATRA</option>" : "<option value='TATRA' class='bg-primary-subtle'>TATRA</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['klient'] == 'THERMA FM' && $id <> '') ? "<option value='THERMA FM' class='bg-primary-subtle' selected>THERMA FM</option>" : "<option value='THERMA FM' class='bg-primary-subtle'>THERMA FM</option>";
                            ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Klient 2</label>
                        <select class="form-select bg-primary-subtle" name="klient2" id="klient2">
                            <?php
                                echo (isset($polehodnot) && $polehodnot[0]['klient2'] == '' && $id <> '') ? "<option value='' class='bg-primary-subtle' selected>-</option>" : "<option value='' class='bg-primary-subtle'>-</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['klient2'] == 'BATZ' && $id <> '') ? "<option value='BATZ' class='bg-primary-subtle' selected>BATZ</option>" : "<option value='BATZ' class='bg-primary-subtle'>BATZ</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['klient2'] == 'CENTRÁLA' && $id <> '') ? "<option value='CENTRÁLA' class='bg-primary-subtle' selected>CENTRÁLA</option>" : "<option value='CENTRÁLA' class='bg-primary-subtle'>CENTRÁLA</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['klient2'] == 'DONGHEE' && $id <> '') ? "<option value='DONGHEE' class='bg-primary-subtle' selected>DONGHEE</option>" : "<option value='DONGHEE' class='bg-primary-subtle'>DONGHEE</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['klient2'] == 'DSC' && $id <> '') ? "<option value='DSC' class='bg-primary-subtle' selected>DSC</option>" : "<option value='DSC' class='bg-primary-subtle'>DSC</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['klient2'] == 'HANON' && $id <> '') ? "<option value='HANON' class='bg-primary-subtle' selected>HANON</option>" : "<option value='HANON' class='bg-primary-subtle'>HANON</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['klient2'] == 'HILITE' && $id <> '') ? "<option value='HILITE' class='bg-primary-subtle' selected>HILITE</option>" : "<option value='HILITE' class='bg-primary-subtle'>HILITE</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['klient2'] == 'KMJ' && $id <> '') ? "<option value='KMJ' class='bg-primary-subtle' selected>KMJ</option>" : "<option value='KMJ' class='bg-primary-subtle'>KMJ</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['klient2'] == 'TATRA' && $id <> '') ? "<option value='TATRA' class='bg-primary-subtle' selected>TATRA</option>" : "<option value='TATRA' class='bg-primary-subtle'>TATRA</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['klient2'] == 'THERMA FM' && $id <> '') ? "<option value='THERMA FM' class='bg-primary-subtle' selected>THERMA FM</option>" : "<option value='THERMA FM' class='bg-primary-subtle'>THERMA FM</option>";
                            ?>
                        </select>
                    </div>

                </div>

                <div class="row mt-2">
                    <div class="col-md-3">
                        <label class="form-label">Souhlas</label>  
                        <select class="form-select bg-primary-subtle" name="souhlas" id="souhlas" required>
                            <?php
                                echo (isset($polehodnot) && $polehodnot[0]['souhlas'] == 'NE' && $id <> '') ? "<option value='NE' class='bg-primary-subtle' selected>NE</option>" : "<option value='NE' class='bg-primary-subtle'>NE</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['souhlas'] == 'ANO' && $id <> '') ? "<option value='ANO' class='bg-primary-subtle' selected>ANO</option>" : "<option value='ANO' class='bg-primary-subtle'>ANO</option>";
                            ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Rekrutér</label>  
                        <select class="form-select bg-primary-subtle" name="rekruter" id="rekruter" required>
                            <?php
                                echo (isset($polehodnot) && $polehodnot[0]['rekruter'] == 'Sztymelska J.' && $id <> '') ? "<option value='Sztymelska J.' class='bg-primary-subtle' selected>Sztymelska J.</option>" : "<option value='Sztymelska J.' class='bg-primary-subtle'>Sztymelska J.</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['rekruter'] == 'David R.' && $id <> '') ? "<option value='David R.' class='bg-primary-subtle' selected>David R.</option>" : "<option value='David R.' class='bg-primary-subtle'>David R.</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['rekruter'] == 'Kostelecký J.' && $id <> '') ? "<option value='Kostelecký J.' class='bg-primary-subtle' selected>Kostelecký J.</option>" : "<option value='Kostelecký J.' class='bg-primary-subtle'>Kostelecký J.</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['rekruter'] == 'Lawniczak P.' && $id <> '') ? "<option value='Lawniczak P.' class='bg-primary-subtle' selected>Lawniczak P.</option>" : "<option value='Lawniczak P.' class='bg-primary-subtle'>Lawniczak P.</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['rekruter'] == 'Majcher D.' && $id <> '') ? "<option value='Majcher D.' class='bg-primary-subtle' selected>Majcher D.</option>" : "<option value='Majcher D.' class='bg-primary-subtle'>Majcher D.</option>";
                            ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Výsledek</label>  
                        <select class="form-select bg-primary-subtle" name="vysledek" id="vysledek" required>
                            <?php
                                echo (isset($polehodnot) && $polehodnot[0]['vysledek'] == 'Přijat' && $id <> '') ? "<option value='Přijat' class='bg-primary-subtle' selected>Přijat</option>" : "<option value='Přijat' class='bg-primary-subtle'>Přijat</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['vysledek'] == 'Zamítnut' && $id <> '') ? "<option value='Zamítnut' class='bg-primary-subtle' selected>Zamítnut</option>" : "<option value='Zamítnut' class='bg-primary-subtle'>Zamítnut</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['vysledek'] == 'Čeká se' && $id <> '') ? "<option value='Čeká se' class='bg-primary-subtle' selected>Čeká se</option>" : "<option value='Čeká se' class='bg-primary-subtle'>Čeká se</option>";
                            ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Koordinátor</label>  
                        <select class="form-select bg-primary-subtle" name="koordinator" id="koordinator" required>
                            <?php
                                echo (isset($polehodnot) && $polehodnot[0]['koordinator'] == 'Kostelecký J.' && $id <> '') ? "<option value='Kostelecký J.' class='bg-primary-subtle' selected>Kostelecký J.</option>" : "<option value='Kostelecký J.' class='bg-primary-subtle'>Kostelecký J.</option>";
                                echo (isset($polehodnot) && $polehodnot[0]['koordinator'] == 'Lawniczak P.' && $id <> '') ? "<option value='Lawniczak P.' class='bg-primary-subtle' selected>Lawniczak P.</option>" : "<option value='Lawniczak P.' class='bg-primary-subtle'>Lawniczak P.</option>";
                            ?>
                        </select>
                    </div>

                </div>

                <hr>   

                <div class="row mt-2">
                                 
                    <div class="col-md-3">
                        <label class="form-label">Nástup</label>  
                        <input type="text" class="form-control bg-primary-subtle text-center common-datepicker3" id="dat_nastup" name="dat_nastup"  placeholder="Vyber datum" value="<?php echo ($id == '') ? "" : (($polehodnot[0]['nastup'] == '0000-00-00') ? "" : date_format(date_create($polehodnot[0]['nastup']),"d.m.Y"));?>">  
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Výstup</label>  
                        <input type="text" class="form-control bg-primary-subtle text-center common-datepicker4" id="dat_vystup" name="dat_vystup"  placeholder="Vyber datum" value="<?php echo ($id == '') ? "" : (($polehodnot[0]['vystup'] == '0000-00-00') ? "" : date_format(date_create($polehodnot[0]['vystup']),"d.m.Y"));?>">                      
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
                            <label for="floatingInputGrid">Os. číslo</label>
                            <input type="text" class="form-control bg-primary-subtle" id="oscislo2" name="oscislo2" placeholder="" value="<?php echo $polehodnot[0]['maximalni_cislo'];?>" required disabled>
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

    $sql = "select id,prijmeni,jmeno,telefon,adresa,dat_narozeni,stat,dat_evidence,zdroj_inzerce,pozice,klient,klient2,souhlas,rekruter,vysledek,nastup,vystup,koordinator,poznamka,duvod_ukonceni,boty,obleceni,telinfo,smena,nastupmisto from nabory where id='" . $id . "'";
    
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
                    <div class="col-md-4">
                        <label class="form-label">Příjmení</label>
                        <input type="text" class="form-control bg-primary-subtle text-center" id="prijmeni" name="prijmeni" value="<?php echo ($id == '') ? "" : $polehodnot[0]['prijmeni'];?>" required disabled>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Jméno</label>  
                        <input type="text" class="form-control bg-primary-subtle text-center" id="jmeno" name="jmeno" value="<?php echo ($id == '') ? "" : $polehodnot[0]['jmeno'];?>" required disabled>
                    </div>

                    <div class="col-md-4">
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

    $sql = "select id,os_cislo,prijmeni,jmeno,rfid,nastup,telefon,adresa,firma,smena,aktivni,nepritomnost,smena2,cilova,vstup,vystup,anulace,radneukoncen,dpn_od from zamestnanci where id='" . $id . "'";
        
    if (!($vysledek = mysqli_query($conn, $sql)))
    {
    die("Nelze provést dotaz</body></html>");
    }            

    while ($radek = mysqli_fetch_array($vysledek))
    {  
        $id = $radek["id"];
        $prijmeni = $radek["prijmeni"];
        $jmeno = $radek["jmeno"];
        $os_cislo = $radek["os_cislo"];
        $rfid = $radek["rfid"];
        $nastup = $radek["nastup"];
        $telefon = $radek["telefon"];
        $adresa = $radek["adresa"];
        $firma = $radek["firma"];
        $smena = $radek["smena"];
        $smena2 = $radek["smena2"];
        $aktivni = $radek["aktivni"];
        $nepritomnost = $radek["nepritomnost"];
        $cilova = $radek["cilova"];
        $ukoncen = $radek["radneukoncen"];

        if ($radek["vstup"] <> "0000-00-00")
        {
            $vstup=date_create($radek["vstup"]);
        }
        else
        {
            $vstup=date_create("1.1.2023");
        }

        if ($radek["vystup"] <> "0000-00-00")
        {
            $vystup=date_create($radek["vystup"]);
        }
        else
        {
            $vystup=date_create("31.12.2099");
        }

        if ($radek["anulace"] <> "0000-00-00")
        {
            $anulace=date_create($radek["anulace"]);
        }
        else
        {
            $anulace=date_create("31.12.2099");
        }

        //$now = new DateTime('now', new DateTimeZone('Europe/Prague'));

        if ($radek["dpn_od"] <> "0000-00-00")
        {
            $dpn_od=date_create($radek["dpn_od"]);
        }
        else
        {
            $dpn_od=new DateTime('now', new DateTimeZone('Europe/Prague'));
        }
    }
    
    mysqli_free_result($vysledek);
                    
    ?> 

    <!-- Modal -->
    <div class="modal fade" id="ModalEditZam<?php echo $id;?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  
    <div class="modal-dialog modal-dialog-centered modal-xl">
        
        <div class="modal-content">

            <div class="modal-header">
                <h1 class="modal-title fs-5 text-dark" id="exampleModalLabel">Editace zaměstnance</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">

                <div class="container">
                                
                    <form class="row g-3" action="zamestnanci.php?typ=updatezamestnance" method="post">

                    <div class="row mt-3">

                        <div class="col-md-4">
                            <label for="floatingInputGrid">Příjmení</label>
                            <input type="text" class="form-control bg-primary-subtle" id="prijmeni" name="prijmeni" placeholder="" value="<?php echo $prijmeni;?>" required>
                        </div>

                        <div class="col-md-4">
                            <label for="floatingInputGrid">Jméno</label>
                            <input type="text" class="form-control bg-primary-subtle text-center" id="jmeno" name="jmeno" placeholder="" value="<?php echo $jmeno;?>" required>
                        </div>

                        <div class="col-md-2">
                            <label for="floatingInputGrid">Os. číslo</label>
                            <input type="text" class="form-control bg-primary-subtle" id="oscislo" name="oscislo" placeholder="" value="<?php echo $os_cislo;?>" required>
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
                        <div class="col-md-12">
                            <label for="floatingInputGrid">Adresa</label>
                            <input type="text" class="form-control bg-primary-subtle" id="adresa" name="adresa" placeholder="" value="<?php echo $adresa;?>" required>
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
                            <label for="floatingSelect">Směna</label>
                            <select class="form-select bg-primary-subtle" name="smena" id="smena" required>
                                <?php
                                    echo (isset($smena) && $smena == 'N/A') ? "<option value='N/A' class='bg-primary-subtle' selected>N/A</option>" : "<option value='N/A' class='bg-primary-subtle'>N/A</option>";
                                    echo (isset($smena) && $smena == 'R') ? "<option value='R' class='bg-primary-subtle' selected>Ranní</option>" : "<option value='R' class='bg-primary-subtle'>Ranní</option>";
                                    echo (isset($smena) && $smena == 'O') ? "<option value='O' class='bg-primary-subtle' selected>Odpolední</option>" : "<option value='O' class='bg-primary-subtle'>Odpolední</option>";
                                    echo (isset($smena) && $smena == 'N') ? "<option value='N' class='bg-primary-subtle' selected>Noční</option>" : "<option value='N' class='bg-primary-subtle'>Noční</option>";

                                    echo (isset($smena) && $smena == 'NN') ? "<option value='NN' class='bg-primary-subtle' selected>NN</option>" : "<option value='NN' class='bg-primary-subtle'>NN</option>";
                                    echo (isset($smena) && $smena == 'NR') ? "<option value='NR' class='bg-primary-subtle' selected>NR</option>" : "<option value='NR' class='bg-primary-subtle'>NR</option>";

                                    echo (isset($smena) && $smena == 'S-R') ? "<option value='S-R' class='bg-primary-subtle' selected>Sobota Ranní</option>" : "<option value='S-R' class='bg-primary-subtle'>Sobota Ranní</option>";
                                    echo (isset($smena) && $smena == 'S-O') ? "<option value='S-O' class='bg-primary-subtle' selected>Sobota Odpolední</option>" : "<option value='S-O' class='bg-primary-subtle'>Sobota Odpolední</option>";
                                    echo (isset($smena) && $smena == 'S-N') ? "<option value='S-N' class='bg-primary-subtle' selected>Sobota Noční</option>" : "<option value='S-N' class='bg-primary-subtle'>Sobota Noční</option>";
                                    
                                    echo (isset($smena) && $smena == 'N-R') ? "<option value='N-R' class='bg-primary-subtle' selected>Neděle Ranní</option>" : "<option value='N-R' class='bg-primary-subtle'>Neděle Ranní</option>";
                                    echo (isset($smena) && $smena == 'N-O') ? "<option value='N-O' class='bg-primary-subtle' selected>Neděle Odpolední</option>" : "<option value='N-O' class='bg-primary-subtle'>Neděle Odpolední</option>";
                                    echo (isset($smena) && $smena == 'N-N') ? "<option value='N-N' class='bg-primary-subtle' selected>Neděle Noční</option>" : "<option value='N-N' class='bg-primary-subtle'>Neděle Noční</option>";

                                ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="floatingSelect">Směna další týden</label>
                            <select class="form-select bg-primary-subtle" name="smena2" id="smena2" required>
                                <?php
                                    echo (isset($smena2) && $smena2 == 'N/A') ? "<option value='N/A' class='bg-primary-subtle' selected>N/A</option>" : "<option value='N/A' class='bg-primary-subtle'>N/A</option>";
                                    echo (isset($smena2) && $smena2 == 'R') ? "<option value='R' class='bg-primary-subtle' selected>Ranní</option>" : "<option value='R' class='bg-primary-subtle'>Ranní</option>";
                                    echo (isset($smena2) && $smena2 == 'O') ? "<option value='O' class='bg-primary-subtle' selected>Odpolední</option>" : "<option value='O' class='bg-primary-subtle'>Odpolední</option>";
                                    echo (isset($smena2) && $smena2 == 'N') ? "<option value='N' class='bg-primary-subtle' selected>Noční</option>" : "<option value='N' class='bg-primary-subtle'>Noční</option>";

                                    echo (isset($smena2) && $smena2 == 'NN') ? "<option value='NN' class='bg-primary-subtle' selected>NN</option>" : "<option value='NN' class='bg-primary-subtle'>NN</option>";
                                    echo (isset($smena2) && $smena2 == 'NR') ? "<option value='NR' class='bg-primary-subtle' selected>NR</option>" : "<option value='NR' class='bg-primary-subtle'>NR</option>";

                                    echo (isset($smena2) && $smena2 == 'S-R') ? "<option value='S-R' class='bg-primary-subtle' selected>Sobota Ranní</option>" : "<option value='S-R' class='bg-primary-subtle'>Sobota Ranní</option>";
                                    echo (isset($smena2) && $smena2 == 'S-O') ? "<option value='S-O' class='bg-primary-subtle' selected>Sobota Odpolední</option>" : "<option value='S-O' class='bg-primary-subtle'>Sobota Odpolední</option>";
                                    echo (isset($smena2) && $smena2 == 'S-N') ? "<option value='S-N' class='bg-primary-subtle' selected>Sobota Noční</option>" : "<option value='S-N' class='bg-primary-subtle'>Sobota Noční</option>";
                                    
                                    echo (isset($smena2) && $smena2 == 'N-R') ? "<option value='N-R' class='bg-primary-subtle' selected>Neděle Ranní</option>" : "<option value='N-R' class='bg-primary-subtle'>Neděle Ranní</option>";
                                    echo (isset($smena2) && $smena2 == 'N-O') ? "<option value='N-O' class='bg-primary-subtle' selected>Neděle Odpolední</option>" : "<option value='N-O' class='bg-primary-subtle'>Neděle Odpolední</option>";
                                    echo (isset($smena2) && $smena2 == 'N-N') ? "<option value='N-N' class='bg-primary-subtle' selected>Neděle Noční</option>" : "<option value='N-N' class='bg-primary-subtle'>Neděle Noční</option>";
                                ?>
                            </select>
                        </div>   

                    </div>

                    <div class="row mt-3">

                        <div class="col-md-2">
                            
                            <label for="datepicker">Vstup</label>   
                            <input type="text" class="form-control bg-primary-subtle common" id="datepicker" name="datepicker"  placeholder="Vyber datum" value="<?php echo date_format($vstup,'d.m.Y');?>" required>
                        </div>

                        <div class="col-md-2">
                            <label for="datepicker2">Výstup</label>
                            <input type="text" class="form-control bg-primary-subtle common" id="datepicker2" name="datepicker2"  placeholder="Vyber datum" value="<?php echo date_format($vystup,'d.m.Y');?>">
                        </div>

                        <div class="col-md-2">
                            <label for="datepicker2">Anulace</label>
                            <input type="text" class="form-control bg-primary-subtle common" id="datepicker3" name="datepicker3"  placeholder="Vyber datum" value="<?php echo date_format($anulace,'d.m.Y');?>">
                        </div>
                       
                        <div class="col-md-3">
                            
                            <label for="floatingSelect">Nepřítomnost</label>
                            <select class="form-select bg-primary-subtle" id="nepritomnost" name="nepritomnost" aria-label="Floating label select example">
                                <?php
                                echo (isset($nepritomnost) && $nepritomnost == '') ? "<option value='' class='bg-primary-subtle' selected>Přítomen</option>" : "<option value='' class='bg-primary-subtle'>Přítomen</option>";
                                echo (isset($nepritomnost) && $nepritomnost == 'DPN') ? "<option value='DPN' class='bg-primary-subtle' selected>DPN</option>" : "<option value='DPN' class='bg-primary-subtle'>DPN</option>";
                                echo (isset($nepritomnost) && $nepritomnost == 'DOV') ? "<option value='DOV' class='bg-primary-subtle' selected>Dovolená</option>" : "<option value='DOV' class='bg-primary-subtle'>Dovolená</option>";
                                echo (isset($nepritomnost) && $nepritomnost == 'ABS') ? "<option value='ABS' class='bg-primary-subtle' selected>Absence</option>" : "<option value='ABS' class='bg-primary-subtle'>Absence</option>";
                                ?>
                            </select>
                            
                        </div>

                        <div class="col-md-3">
                            <label for="datepicker2">DPN od (pouze u nepřítomnosti)</label>
                            <input type="text" class="form-control bg-primary-subtle common" id="datepicker4" name="datepicker4"  placeholder="Vyber datum" value="<?php echo ($nepritomnost == '') ? "" : date_format($dpn_od,'d.m.Y');?>">
                        </div>

                    </div>

                    <div class="row mt-3">
                        <div class="col-md-5">
                            
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

                        <div class="col-md-5">
                            
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
                       
                        <div class="col-md-2">
                            <label for="floatingInputGrid">Cílová stanice</label>
                            <input type="text" class="form-control bg-primary-subtle" id="cilova" name="cilova" placeholder="" value="<?php echo $cilova;?>">     
                        </div>

                        <input type="hidden" class="form-control" id="id_zam" name="id_zam" placeholder="" value=<?php echo $id;?>>

                    </div>

                    </div>
                    </div>
            
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary mb-3">Uložit změnu do databáze</button>
                        <button type="button" class="btn btn-secondary mb-3" data-bs-dismiss="modal">Zavřít</button>
                    </div>
           
                    </form>

                </div>  
                         
            </div>

        </div>

    </div>
    </div>

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

?>