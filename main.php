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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <title>HLAVNÍ STRÁNKA</title>
</head>
<body>

<?php require_once 'init.php'; ?>

<?php

global $conn;

menu();

if (kontrola_prihlaseni() == "OK")
{   
    
    if (isset($_GET["typ"]))
    {
        
        if ($_GET["typ"] == "dochazka" and $_SESSION["typ"] == "2")
        {
            if ($_GET["bus"] == "0")
            {   ?>
                    <h3 class="text-center m-2 p-2">Vyber prosím dopravu</h3>

                    <div class="container">
                        <div class="row justify-content-md-center">
                        <div class="col col-md-12">

                        <br>
                        <div class='table-responsive-lg text-center'>
                        <table class='table table-hover'>
                        <thead>
                            <tr class='table-active'>
                                <th scope='col'>ID</th>
                                <th scope='col'>SPZ</th>
                                <th scope='col'>Označení</th>
                                <th scope='col'></th>
                            </tr>
                        </thead>
                        <tbody>
                        
                        <?php

                            $sql = "select id,spz,oznaceni from auta order by id";

                            if (!($vysledek = mysqli_query($conn, $sql)))
                            {
                            die("Nelze provést dotaz</body></html>");
                            }            

                            while ($radek = mysqli_fetch_array($vysledek))
                            {   ?>
                                
                                <tr>
                                    <td class='text-center'><?php echo $radek["id"];?></td>
                                    <td class='text-center'><?php echo $radek["spz"];?></td>
                                    <td class='text-start'><?php echo $radek["oznaceni"];?></td>
                                    <td><a type="button" class="btn btn-outline-primary btn-lg" href="main.php?typ=dochazka&firma=<?php echo $_GET["firma"];?>&bus=<?php echo $radek["id"];?>&smena=<?php echo $_GET["smena"];?>">Vyber</button></a></td>
                                </tr>
                                
                                <?php          
                            }
                            
                            mysqli_free_result($vysledek);

                        //zapati tabulky ?>
                        </tbody>
                        </table>
                        </div>

                        </div>
                        </div>

                    </div>

                    <?php
            }
            else
            {       ?>
                    
                    <div class="container">
                        <h2 class='text-center text-success m-2 p-2 bg-success text-white'>
                            <span id="datum"><?php echo date("l, d.m.Y"); ?></span> 
                            - <span id="cas"><?php echo date("H:i:s"); ?></span>
                        </h2>

                        <h3 class="text-center m-2 p-2 bg-info bg-opacity-25">
                            <?php
                            if (!isset($_GET["zastavka"])) {
                                echo "Docházka";
                            } else {
                                echo "Zastávka - " . htmlspecialchars(get_zastavka_from_id3($_GET['zastavka'],$_GET['bus'],$_GET['smena']));
                            }
                            ?>
                        </h3>
                    </div>
      
                    <script>
                    // Serverový timestamp (v sekundách)
                    let serverTimestamp = <?php echo time(); ?>;

                    // Funkce pro aktualizaci času a datumu
                    function aktualizujCas() {
                        serverTimestamp++; // přičteme 1 sekundu

                        let serverDate = new Date(serverTimestamp * 1000); // timestamp → Date

                        // Hodiny, minuty, sekundy
                        let h = serverDate.getHours().toString().padStart(2,'0');
                        let m = serverDate.getMinutes().toString().padStart(2,'0');
                        let s = serverDate.getSeconds().toString().padStart(2,'0');

                        // Dny v týdnu v češtině
                        const dny = ["Neděle","Pondělí","Úterý","Středa","Čtvrtek","Pátek","Sobota"];
                        let den = dny[serverDate.getDay()];

                        // Datum
                        let datum = serverDate.getDate().toString().padStart(2,'0') + '.' +
                                    (serverDate.getMonth()+1).toString().padStart(2,'0') + '.' +
                                    serverDate.getFullYear();

                        document.getElementById("cas").textContent = `${h}:${m}:${s}`;
                        document.getElementById("datum").textContent = `${den}, ${datum}`;
                    }

                    // Aktualizace každou sekundu
                    aktualizujCas();
                    setInterval(aktualizujCas, 1000);
                    </script>
               
                    <?php
                    if (!isset($_POST["barcode"])) {
                        // Nic se neděje, kód nebyl zaslán
                    } elseif (!isset($_GET["zastavka"])) {
                        ?>
                        <div class="container">
                            <h5 class="text-center text-success m-2 p-2 bg-danger text-dark bg-opacity-50">
                                NENÍ VYBRÁNA ZASTÁVKA
                            </h5>
                        </div>
                        <?php
                    } else {
                        // Získání informací z RFID nebo osobního čísla
                        $str_arr = explode(",", get_name_from_rfid($_POST["barcode"], $_GET["firma"]));
                        if ($str_arr[0] === "neznámý kód") {
                            $str_arr = explode(",", get_name_from_personal_number($_POST["barcode"], $_GET["firma"]));
                        }

                        if ($str_arr[0] !== "neznámý kód") {
                            // Kód nalezen
                            ?>
                            <div class="container">
                                <h5 class="text-center text-success m-2 p-2 bg-success text-dark bg-opacity-10">
                                    NASKENOVÁNA INFORMACE OD
                                </h5>

                                <table class="table text-center">
                                    <thead>
                                        <tr>
                                            <th>Osobní číslo</th>
                                            <th>ID</th>
                                            <th>Jméno</th>
                                            <th>Příjmení</th>
                                            <th>RFID</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th><?php echo $str_arr[0]; ?></th>
                                            <td><?php echo $str_arr[4]; ?></td>
                                            <td><?php echo $str_arr[1]; ?></td>
                                            <td><?php echo $str_arr[2]; ?></td>
                                            <td><?php echo $str_arr[3]; ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <?php
                            // Vložení docházky
                            insert_attandance($str_arr[4], $_GET["bus"], $_GET["zastavka"], $_GET["firma"], $_GET["smena"], '0', '');
                        } else {
                            // Neznámý kód nebo osobní číslo
                            ?>
                            <div class="container">
                                <h5 class="text-center text-danger m-2 p-2 bg-danger text-dark bg-opacity-50">
                                    NEZNÁMÝ KÓD NEBO OSOBNÍ ČÍSLO
                                </h5>
                            </div>
                            <?php
                        }
                    }
                    ?>

                    <h3 class="text-center m-2 p-2"><?php echo get_firma_from_id($_GET["firma"]);?> - <?php echo get_spz_from_id($_GET["bus"]);?>, směna: <?php echo $_GET["smena"];?></h3>               

                    <div class="container">

                        <form name="barkod" method="POST" action="">
                            <div class="form-group mt-2 text-center col-form-label-lg">
                                <label for="barcodeInput">Barcode nebo os. číslo</label>
                                <input type="text" class="form-control mt-2 form-control-lg" 
                                    name="barcode" id="barcodeInput" 
                                    placeholder="RFID kód z kartičky nebo OSOBNÍ ČÍSLO" autofocus>
                            </div>
                        </form>

                        <div class="row justify-content-md-center mt-4">
                            <div class="col-md-12">

                                <div class="table-responsive-lg text-center">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr class="table-active">
                                                <th>#</th>
                                                <th>Čas</th>
                                                <th>Zastávka</th>
                                                <th>Počet nastupujících</th>
                                                <th>V autobusu</th>
                                                <th>Vyber</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        <?php
                                        $modaly_id = "";

                                        // připravíme parametry pro prepared statement
                                        $bus_id = $_GET['bus'] ?? '';
                                        $smena = $_GET['smena'] ?? '';
                                        $firma = $_GET['firma'] ?? '';

                                        if ($bus_id && $smena) {
                                            $sql = "SELECT nastupy.id, trasy.{$smena} AS cas, nastupy.zastavka, trasy.auto
                                                    FROM trasy 
                                                    LEFT JOIN nastupy ON trasy.zastavka = nastupy.id
                                                    WHERE trasy.auto = ?
                                                    ORDER BY trasy.{$smena}";

                                            if ($stmt = $conn->prepare($sql)) {
                                                $stmt->bind_param("i", $bus_id);
                                                $stmt->execute();
                                                $result = $stmt->get_result();

                                                $poradi = 1;
                                                while ($radek = $result->fetch_assoc()) {

                                                    // cache funkcí, aby se volaly jen jednou
                                                    $ma_nastoupit = zjisti_pocet_nastupujicich($firma, $smena, $radek['id']);
                                                    $nastoupilo = zjisti_pocet_autobusu($firma, $smena, $radek['id']);

                                                    if ($ma_nastoupit > 0) {
                                                        $modaly_id .= $radek['id'] . ";";
                                                    }

                                                    // nastavení fontu pro vybranou zastávku
                                                    $fontClass = (isset($_GET['zastavka']) && $_GET['zastavka'] == $radek['id']) ? " fw-bold fs-3" : "";

                                                    // barva řádku podle počtu nastupujících
                                                    $rowClass = ($ma_nastoupit == $nastoupilo) ? "table-success opacity-10" : "table-danger opacity-10";
                                                    $rowClass .= $fontClass;
                                                    ?>

                                                    <tr class="<?php echo $rowClass; ?> align-middle">
                                                        <td class="text-center"><?php echo $poradi; ?></td>
                                                        <td class="text-center"><?php echo htmlspecialchars($radek['cas']); ?></td>
                                                        <td class="text-start"><?php echo htmlspecialchars($radek['zastavka']); ?></td>
                                                        <td>
                                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" 
                                                                    data-bs-target="#nastup<?php echo $radek['id']; ?>">
                                                                <?php echo $ma_nastoupit; ?>
                                                            </button>
                                                        </td>
                                                        <td><?php echo $nastoupilo; ?></td>
                                                        <td>
                                                            <a class="btn btn-outline-primary" 
                                                            href="main.php?typ=dochazka&firma=<?php echo urlencode($firma); ?>&bus=<?php echo urlencode($bus_id); ?>&smena=<?php echo urlencode($smena); ?>&zastavka=<?php echo $radek['id']; ?>">
                                                                Vyber
                                                            </a>
                                                        </td>
                                                    </tr>

                                                    <?php
                                                    $poradi++;
                                                }

                                                $stmt->close();
                                            }
                                        }
                                        ?>

                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>

                    </div>

                <?php

                //vyrob_modal_k_nastupnimu_mistu($firma,$nastup,$smena)
                $pole_rozdelene = explode(";", $modaly_id);

                foreach($pole_rozdelene as $i =>$key) 
                {
                    vyrob_modal_k_nastupnimu_mistu($_GET["firma"],$key,$_GET["smena"],$_GET['bus']);
                }
                
            }
        }
        elseif ($_GET["typ"] == "zmenabus" and $_SESSION["typ"] == "2")
        {
            $_SESSION["autobus"] = $_GET['bus'];

            ?>

            <meta http-equiv="refresh" content="0;url=main.php">

            <?php
        }
        elseif ($_GET["typ"] == "dpn" and $_SESSION["typ"] == "2")
        {   ?>
 
            <h1 class="text-center m-2 p-2">KONTROLA DPN</h1>

            <div class="container-fluid">
        
            <div class="row justify-content-md-center">
            <div class="col col-md-12">
    
            <br>
            <div class='table-responsive-lg text-center'>
            <table class='table table-hover'>
            <thead>
                <tr class='table-active'>
                    <th scope='col'>#</th>
                    <th scope='col' class='text-start'>Příjmení</th>
                    <th scope='col' class='text-start'>Jméno</th>
                    <th scope='col' class='text-start'>DPN Od</th>
                    <th scope='col' class='text-start'>DPN Do</th>
                    <th scope='col'>Zadal</th>
                    <th scope='col'>Adresa</th>
                    <th scope='col'></th>
                </tr>
            </thead>
            <tbody>
    
            <?php  
                $cislo = 1;          
            
                $sql = "select kontroly.id,id_zam,kontroly.dpn_od,kontroly.dpn_do,kontrola,kontrolacas,zadal,provedl,vysledek,kontroly.adresa,jmeno,prijmeni,cilova from kontroly left join zamestnanci on kontroly.id_zam = zamestnanci.id where provedl='" . $_SESSION["log_id"] . "' and kontrola='0000-00-00' order by id desc";
    
                //echo $sql;

                if (!($vysledek = mysqli_query($conn, $sql)))
                {
                die("Nelze provést dotaz</body></html>");
                }            
    
                while ($radek = mysqli_fetch_array($vysledek))
                {                                        
                    ?>
                    
                    <tr>
                        <td class='text-center fw-bold'><?php echo $cislo;?></td>
                        <td class='text-start'><?php echo $radek["prijmeni"];?></td>
                        <td class='text-start'><?php echo $radek["jmeno"];?></td>
                        <td class='text-start'><?php echo prevod_data($radek["dpn_od"],1);?></td>
                        <td class='text-start'><?php echo prevod_data($radek["dpn_do"],1);?></td>
                        <td class='text-center'><?php echo get_user_from_id($radek["zadal"]);?></td>
                        <td class='text-center'><?php echo $radek["adresa"];?></td>
                        <td class='text-start' width="50"><button type="button" class="form-control btn btn-sm btn-warning" data-bs-toggle='modal' data-bs-target='#ModalKontrolaRidic<?php echo $radek['id'];?>'>ZKONTROLOVAT</button></td>
                    </tr>
                    
                    <?php

                    kontrola_dpn_ridic($radek["id"]);
                
                    $cislo ++;
            
                }
                
                mysqli_free_result($vysledek);
    
            ?>
    
            </tbody>
            </table>
            </div>
    
            </div>
            </div>
            </div>


            <?php
        }
        elseif ($_GET["typ"] == "dpn_ok" and $_SESSION["typ"] == "2")
        {
            //vlozim zaznam o kontrole
            $now = new DateTime('now', new DateTimeZone('Europe/Prague'));

            if (!($vysledek = mysqli_query($conn, 
            "update kontroly set vysledek='zastižen',kontrola='" . $now->format('Y-m-d') . "',kontrolacas='" . $now->format('H:i:s') . "' where id='" . $_GET['id'] . "'")))
            {
            die("Nelze provést dotaz.</body></html>");
            } 
            ?>

            <div class="container">
            <h3 class="text-center m-2 p-2">VÝSLEDEK KONTROLY ULOŽEN</h3>

            <h3 class="text-center m-2 p-2">Budete přesměrování zpět HLAVNÍ OBRAZOVKU</h3>

            </div>

            <meta http-equiv="refresh" content="5;url=main.php">
            <?php
        }
        elseif ($_GET["typ"] == "dpn_ng" and $_SESSION["typ"] == "2")
        {
            //vlozim zaznam o kontrole
            $now = new DateTime('now', new DateTimeZone('Europe/Prague'));

            if (!($vysledek = mysqli_query($conn, 
            "update kontroly set vysledek='nezastižen',kontrola='" . $now->format('Y-m-d') . "',kontrolacas='" . $now->format('H:i:s') . "' where id='" . $_GET['id'] . "'")))
            {
            die("Nelze provést dotaz.</body></html>");
            } 

            ?>

            <div class="container">
            <h3 class="text-center m-2 p-2">VÝSLEDEK KONTROLY ULOŽEN</h3>

            <h3 class="text-center m-2 p-2">Budete přesměrování zpět HLAVNÍ OBRAZOVKU</h3>

            </div>

            <meta http-equiv="refresh" content="5;url=main.php">
            <?php
        }
        elseif ($_GET["typ"] == "saveukol")
        {
            // Kontrola, zda jsou všechna potřebná data z formuláře dostupná
            if (isset($_POST['zakazka'], $_POST['kdo'], $_POST['komu'], $_POST['ukol'])) {
                // Přiřazení proměnných z POST dat
                $zakazka = trim($_POST['zakazka']);
                $kdo = (int)$_POST['kdo'];
                $komu = (int)$_POST['komu'];
                $ukol = trim($_POST['ukol']);
                $hotovo = 0; // Přednastavený stav "Ne" (úkol není hotový)

                // Připravení SQL dotazu pro vložení dat
                $query = "INSERT INTO ukoly (zakazka, kdo, komu, ukol, hotovo) 
                        VALUES (?, ?, ?, ?, ?)";
                
                if ($stmt = $conn->prepare($query)) {
                    // Navázání parametrů k dotazu
                    $stmt->bind_param('siisi', $zakazka, $kdo, $komu, $ukol, $hotovo);

                    // Spuštění dotazu
                    if ($stmt->execute()) {
                        //echo "Úkol byl úspěšně uložen.";
                        
                    } else {
                        //echo "Chyba při ukládání úkolu: " . $stmt->error;
                    }

                    // Uzavření připraveného dotazu
                    $stmt->close();
                } else {
                    //echo "Chyba při přípravě dotazu: " . $conn->error;
                }
            } else {
                //echo "Chyba: Nebyla odeslána všechna potřebná data.";
            }

            ?>

            <meta http-equiv="refresh" content="0;url=main.php">

            <?php
        }
        elseif ($_GET["typ"] == "changeukol")
        {
            if (isset($_GET['id']) && isset($_GET['stav'])) {
                $id = intval($_GET['id']); // Zabezpečení ID
                $stav = intval($_GET['stav']); // Zabezpečení stavu
                
                // Vytvoření objektu DateTime s nastavením časového pásma na Europe/Prague
                $now = new DateTime('now', new DateTimeZone('Europe/Prague'));
                $dokonceno = ($stav == 1) ? $now->format('Y-m-d H:i:s') : NULL;
            
                // Aktualizace stavu v tabulce ukoly
                $sql = "UPDATE ukoly SET hotovo = ?, dokonceno = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("isi", $stav, $dokonceno, $id);
            
                if ($stmt->execute()) {
                    echo "Stav úkolu byl úspěšně aktualizován.";
                } else {
                    echo "Chyba při aktualizaci úkolu: " . $stmt->error;
                }
            
                $stmt->close();
            } 
            else 
            {
                echo "Chybí parametry ID nebo STAV.";
            }
            ?>

            <meta http-equiv="refresh" content="0;url=main.php">
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
        
        <h1 class="text-center m-2 p-2">HLAVNÍ STRÁNKA RECRA SYSTÉMU</h1>

        <?php
        if ($_SESSION["typ"] == '2')
        {   ?>

            <h3 class="text-center m-2 p-2">Přehled firem</h3>     

            <div class="container">
                <div class="row justify-content-md-center">
                <div class="col col-md-12">

                <?php
                if ($_SESSION["typ"] == '2')
                {   ?>

                    <div class="container">
                        <div class="row justify-content-md-center">
                            <span class="text-center">
                                <button class="btn btn-lg btn-outline-primary p-1 m-2" onclick="loadModalDoprava()">
                                    <i class="bi bi-truck"></i> Změna trasy - kliknutím vybereš
                                </button>
                            </span>
                        </div>
                    </div>

                    <?php
                }
                ?>

                <br>
                <div class="table-responsive text-center">
                    <table class="table table-hover align-middle">
                        <thead class="table-secondary text-white fs-6">
                            <tr>
                                <th scope="col" style="width:5%;">#</th>
                                <th scope="col" class="text-start" style="width:25%;">Firma</th>
                                <th scope="col" style="width:25%;">Zaměstnanců / Objednávka</th>
                                <th scope="col" style="width:45%;">Docházky</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $cislo = 1;
                            $sql = "SELECT id, firma FROM firmy WHERE id IN (" . $_SESSION["firma"] . ") ORDER BY id";

                            if (!($vysledek = mysqli_query($conn, $sql))) {
                                die("Nelze provést dotaz</body></html>");
                            }

                            while ($radek = mysqli_fetch_assoc($vysledek)) {
                                $pocet_zam = zjisti_pocet_zamestnancu_ve_firme($radek["id"]);
                                $pocet_obj = zjisti_pocet_zamestnancu_ve_firme_objednavka($radek["id"]);
                                ?>
                                <tr>
                                    <td class="text-center fw-bold fs-2"><?php echo $cislo; ?></td>
                                    <td class="text-start fw-bold fs-2"><?php echo htmlspecialchars($radek["firma"]); ?></td>
                                    <td>
                                        <span class="badge bg-primary fs-5"><?php echo $pocet_zam; ?></span>
                                        /
                                        <span class="badge bg-secondary fs-5"><?php echo $pocet_obj; ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        if ($_SESSION["typ"] == '2') {
                                            vytvor_tlacitka_pro_smeny($radek["id"], $_SESSION["log_id"], $_SESSION["autobus"]);
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                                $cislo++;
                            }

                            mysqli_free_result($vysledek);
                            ?>
                        </tbody>
                    </table>
                </div>


                </div>
                </div>

            </div>

            <?php
        }
        elseif (($_SESSION["typ"] == '1') or ($_SESSION["typ"] == '5'))
        {   ?>
        
            <div class="container">   

                <div class="d-flex justify-content-center mt-5">
                    <img src="img\logo.png" class="img-fluid" alt="...">
                </div>

            </div>

            <a class="btn btn-success text-center m-1" href="#" role="button" data-bs-toggle='modal' onclick="loadModalContentNew(5,'novy_ukol','#ModalNovyUkol')">Nový úkol</a>


            <div class="container-fluid">

                <div class="row row-cols-1 row-cols-md-1 row-cols-xxl-2 g-4">

                    <div class="col">
                        <div class="card h-100">
                        
                            <div class="card-body">

                            <?php 

                            $pocetUkolu = ziskejPocetUkoluDleStavu(1);
                            echo "<div class='text-center mb-2'>
                            <img src='img/icons/none.png' alt='Nedokončeno' title='Nedokončeno' height='25'> {$pocetUkolu[0]}
                            <img src='img/icons/process.png' alt='Rozpracováno' title='Rozpracováno' height='25'> {$pocetUkolu[2]}
                            <img src='img/icons/done.png' alt='Hotovo' title='Hotovo' height='25'> {$pocetUkolu[1]}
                            </div>";

                            Tabulka_Ukoly(1);
                            ?>
                         
                            </div>

                        </div>
                    </div>

                    <div class="col">
                        <div class="card h-100">
                        
                            <div class="card-body">

                            <?php 

                            $pocetUkolu = ziskejPocetUkoluDleStavu(2);
                            echo "<div class='text-center mb-2'>
                            <img src='img/icons/none.png' alt='Nedokončeno' title='Nedokončeno' height='25'> {$pocetUkolu[0]}
                            <img src='img/icons/process.png' alt='Rozpracováno' title='Rozpracováno' height='25'> {$pocetUkolu[2]}
                            <img src='img/icons/done.png' alt='Hotovo' title='Hotovo' height='25'> {$pocetUkolu[1]}
                            </div>";

                            Tabulka_Ukoly(2);
                            ?>

                            </div>

                        </div>
                    </div>

                </div>
       
            </div>

            <?php
        }
    }

    ?>
    
    <div id="modalContent"></div>

    <script>
    function loadModalDoprava() {
        var functionName = 'modal_doprava'; // Název funkce, kterou chcete volat
        
        $.ajax({
            url: 'funkce.php', // Cesta k externímu skriptu
            type: 'GET',
            data: { functionName: functionName}, // Předání funkce a ID
            success: function(response) {
                $('#modalContent').html(response);
                $('#modal_doprava').modal('show');
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }
    </script>

    <script>
        function loadModalContentNew(modalId, functionName, modalName) 
        {
            console.log("Funkce loadModalContentNew byla spuštěna"); // zpráva do konzole

            var ID = modalId; // Získání ID modálního okna z modalId

            $.ajax({
                url: 'funkce.php', // Cesta k externímu skriptu
                type: 'GET',
                data: { functionName: functionName, ID: ID }, // Předání funkce a ID
                success: function(response) {
                    $('#modalContent').html(response);
                    $(modalName).modal('show'); // Zobrazí modální okno podle jména proměnné
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }
    </script>

    <?php
}
else
{
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
        $('.common').datepicker({
            format: 'dd.mm.yyyy',
            autoclose: true,
            todayHighlight: true,
            language: 'cs',
            allowInputToggle: false,
            startDate: '01.01.2024', // Nastavte výchozí hodnotu dle potřeby
            endDate: '31.12.2099'    // Nastavte výchozí hodnotu dle potřeby
        });
    });
</script>