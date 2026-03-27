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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">


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
        if (($_GET["typ"] == "savezamestnance" and $_SESSION["typ"] == "1") or ($_GET["typ"] == "savezamestnance" and $_SESSION["typ"] == "4") or ($_GET["typ"] == "savezamestnance" and $_SESSION["typ"] == "5"))
        {
            $date1=date_create($_POST["datepicker"]);
            $date2=date_create($_POST["datepicker2"]);

            $sql= "insert into zamestnanci (os_cislo,prijmeni,jmeno,rfid,nastup,telefon,adresa,firma,smena,aktivni,nepritomnost,smena2,cilova,vstup,vystup,smennost) values ('" . zjisti_osobni_cislo() . "','" . $_POST["prijmeni"] . "','" . $_POST["jmeno"] . "','" . $_POST["rfid"] . "','" . $_POST["nastup"] . "','" . $_POST["telefon"] . "','" . $_POST["adresa"] . "','" . $_POST["firma"] . "','" . $_POST["smena"] . "','1','" . $_POST["nepritomnost"] . "','" . $_POST["smena2"] . "','" . $_POST["cilova"] . "','" . date_format($date1,"Y-m-d") . "','" . date_format($date2,"Y-m-d") . "','3SM')"; 

            //echo $sql;

            if (!($vysledek = mysqli_query($conn, $sql)))
            {
            die("Nelze provést dotaz.</body></html>");
            }
       
            //zaznam do logu
            $now = new DateTime('now', new DateTimeZone('Europe/Prague'));
               
            if (!($vysledek = mysqli_query($conn, 
            "insert into logs (kdo,typ,infotext,datumcas) values ('" . $_SESSION["log_id"] . "','Nový zaměstnanec','Vytvořen nový zaměstnanec " . $_POST["prijmeni"] . " " . $_POST["jmeno"] . "','" . $now->format('Y-m-d H:i:s') . "')")))
            {
            die("Nelze provést dotaz.</body></html>");
            }

            ?>

            <div class="container">
                <h3 class="text-center m-2 p-2">Zaměstnanec přidán</h3>

                <h3 class="text-center m-2 p-2">Budete přesměrování zpět na hlavní stránku</h3>

            </div>

            <meta http-equiv="refresh" content="5;url=zamestnanci.php">
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

/*             if ($chk_anulace && !empty($_POST['datepicker3'])) {
                $d3 = DateTime::createFromFormat('d.m.Y', $_POST['datepicker3']);
                $date3_str = $d3 ? $d3->format('Y-m-d') : '0000-00-00';
            } else {
                $date3_str = '0000-00-00';
            } */

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
        /* elseif (($_GET["typ"] == "editzam" and $_SESSION["typ"] == "1") or ($_GET["typ"] == "editzam" and $_SESSION["typ"] == "4") or ($_GET["typ"] == "editzam" and $_SESSION["typ"] == "5"))
        {   
            global $conn;

            $sql = "select id,os_cislo,prijmeni,jmeno,rfid,nastup,telefon,adresa,firma,smena,aktivni,nepritomnost,smena2,cilova,vstup,vystup,anulace,radneukoncen from zamestnanci where id='" . $_GET["id"] . "'";
        
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
            }
            
            mysqli_free_result($vysledek);
                          
            ?>
            
            <h3 class="text-center m-2 p-2">Editace zaměstnance</h3>

            <div class="container">
                            
                <form class="row g-3" action="zamestnanci.php?typ=updatezamestnance" method="post">

                <input type="hidden" class="form-control" id="id_zam" name="id_zam" placeholder="" value=<?php echo $_GET["id"];?>>

                <div class="col-md-3 mt-5">
                    <div class="form-floating">
                    <input type="text" class="form-control" id="prijmeni" name="prijmeni" placeholder="" value="<?php echo $prijmeni;?>" required>
                    <label for="floatingInputGrid">Příjmení</label>
                    </div>
                </div>

                <div class="col-md-3 mt-5">
                    <div class="form-floating">
                    <input type="text" class="form-control" id="jmeno" name="jmeno" placeholder="" value="<?php echo $jmeno;?>" required>
                    <label for="floatingInputGrid">Jméno</label>
                    </div>
                </div>

                <div class="col-md-2 mt-5">
                    <div class="form-floating">
                    <input type="text" class="form-control" id="oscislo" name="oscislo" placeholder="" value="<?php echo $os_cislo;?>" required>
                    <label for="floatingInputGrid">Os. číslo</label>
                    </div>
                </div>

                <div class="col-md-4 mt-5">
                    <div class="form-floating">
                    <input type="text" class="form-control" id="adresa" name="adresa" placeholder="" value="<?php echo $adresa;?>" required>
                    <label for="floatingInputGrid">Adresa</label>
                    </div>
                </div>
      
                <div class="col-md-4 mt-5">
                    <div class="form-floating">
                    <input type="text" class="form-control" id="rfid" name="rfid" placeholder="" value="<?php echo $rfid;?>" required>
                    <label for="floatingInputGrid">RFID</label>
                    </div>
                </div>
            
                <div class="col-md-4 mt-5">
                    <div class="form-floating">
                    <input type="text" class="form-control" id="telefon" name="telefon" placeholder="" value="<?php echo $telefon;?>">
                    <label for="floatingInputGrid">Telefon</label>
                    </div>
                </div>

                <div class="col-md-2 mt-5">
                    <div class="form-floating">
                    <select class="form-select" id="smena" name="smena" aria-label="Floating label select example">
                        <?php 
                        $shiftOptions = [
                            "R"    => "Ranní",
                            "O"    => "Odpolední",
                            "N"    => "Noční",
                            "NN"   => "NN",
                            "NR"   => "NR",
                            "S-R"  => "Sobota Ranní",
                            "S-O"  => "Sobota Odpolední",
                            "S-N"  => "Sobota Noční",
                            "N-R"  => "Neděle Ranní",
                            "N-O"  => "Neděle Odpolední",
                            "N-N"  => "Neděle Noční",
                            "PR"   => "Přesčas",
                            "N/A"  => "N/A"
                        ];

                        foreach ($shiftOptions as $value => $label) {
                            $selected = ($smena == $value) ? "selected" : "";
                            echo "<option value=\"$value\" $selected>$label</option>";
                        }
                        ?>
                    </select>

                    <label for="floatingSelect">Směna</label>
                    </div>
                </div>

                <div class="col-md-2 mt-5">
                    <div class="form-floating">
                    <select class="form-select" id="smena2" name="smena2" aria-label="Floating label select example">                 

                        <?php 
                        $options = [
                            "R" => "Ranní",
                            "O" => "Odpolední",
                            "N" => "Noční",
                            "NN" => "NN",
                            "NR" => "NR",
                            "S-R" => "Sobota Ranní",
                            "S-O" => "Sobota Odpolední",
                            "S-N" => "Sobota Noční",
                            "N-R" => "Neděle Ranní",
                            "N-O" => "Neděle Odpolední",
                            "N-N" => "Neděle Noční",
                            "PR" => "Přesčas",
                            "N/A" => "N/A"
                        ];

                        foreach ($options as $value => $label) {
                            $selected = ($smena2 == $value) ? ' selected' : '';
                            echo "<option value=\"$value\"$selected>$label</option>";
                        }
                        ?>

                    </select>

                    <label for="floatingSelect">Směna další týden</label>
                    </div>
                </div>

                <div class="col-md-2 mt-5">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="datepicker" name="datepicker"  placeholder="Vyber datum" value="<?php echo date_format($vstup,"d.m.Y");?>" required>
                        <label for="datepicker">Vstup</label>
                    </div>
                </div>

                <div class="col-md-2 mt-5">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="datepicker2" name="datepicker2"  placeholder="Vyber datum" value="<?php echo date_format($vystup,"d.m.Y");?>">
                        <label for="datepicker2">Výstup</label>
                    </div>
                </div>

                <div class="col-md-2 mt-5">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="datepicker3" name="datepicker3"  placeholder="Vyber datum" value="<?php echo date_format($anulace,"d.m.Y");?>">
                        <label for="datepicker3">Anulace</label>
                    </div>
                </div>

                <div class="col-md-2 mt-5">
                    <div class="form-floating">
                   
                    <select class="form-select" id="nepritomnost" name="nepritomnost" aria-label="Floating label select example">
                        <?php
                        // Define the options and labels
                        $options = [
                            "" => "Přítomen",
                            "DPN" => "DPN",
                            "OČR" => "OČR",
                            "DOV" => "Dovolená",
                            "ABS" => "Absence"
                        ];

                        // Generate the <option> elements
                        foreach ($options as $value => $label) {
                            $selected = ($nepritomnost == $value) ? ' selected' : '';
                            echo "<option value=\"$value\"$selected>$label</option>";
                        }
                        ?>
                    </select>

                    <label for="floatingSelect">Nepřítomnost</label>
                    </div>
                </div>


                <div class="col-md-2 mt-5">
                    <div class="form-floating">
                    <select class="form-select" id="aktivni" name="aktivni" aria-label="Floating label select example">
                        
                    <?php
                    if ($aktivni == "0")
                    {   ?>
                            <option value="0" selected>Neaktivní</option>
                            <option value="1">Aktivní</option>
                        <?php
                    }
                    elseif ($aktivni == "1")
                    {   ?>
                            <option value="0">Neaktivní</option>
                            <option value="1" selected>Aktivní</option>
                        <?php
                    }
                    ?>   
           
                    </select>
                    <label for="floatingSelect">Aktivní / neaktivní</label>
                    </div>
                </div>

                <div class="col-md-2 mt-5">
                    <div class="form-floating">
                    
                    <select class="form-select" id="ukoncen" name="ukoncen" aria-label="Floating label select example">
                        <?php
                        // Define the options and labels
                        $options = [
                            "" => "N/A",
                            "ANO" => "ANO",
                            "NE" => "NE"
                        ];

                        // Generate the <option> elements
                        foreach ($options as $value => $label) {
                            $selected = ($ukoncen == $value) ? ' selected' : '';
                            echo "<option value=\"$value\"$selected>$label</option>";
                        }
                        ?>
                    </select>

                    <label for="floatingSelect">Řádně ukončen</label>
                    </div>
                </div>

                <div class="col-md-5 mt-5">
                    <div class="form-floating">
                    <select class="form-select" id="firma" name="firma" aria-label="Floating label select example">
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
                        {   
                            if ($firma == $radek["id"])
                            {   ?>
                                    <option value="<?php echo $radek["id"];?>" selected><?php echo $radek["firma"];?></option>    
                                <?php
                            }
                            else
                            {   ?>
                                    <option value="<?php echo $radek["id"];?>"><?php echo $radek["firma"];?></option>
                                <?php
                            }          
                        }

                        mysqli_free_result($vysledek);
                        ?>
                        
                    </select>
                    <label for="floatingSelect">Firma</label>
                    </div>
                </div>

                <div class="col-md-5 mt-5">
                    <div class="form-floating">
                    <select class="form-select" id="nastup" name="nastup" aria-label="Floating label select example">
                        <option value="0" selected>Zatím nepřiřazena</option>

                        <?php
                        $sql = "select zastavky.id,auta.spz,zastavka from zastavky left join auta on zastavky.auto = auta.id order by spz,zastavka";

                        if (!($vysledek = mysqli_query($conn, $sql)))
                        {
                        die("Nelze provést dotaz</body></html>");
                        }            

                        while ($radek = mysqli_fetch_array($vysledek))
                        {   
                            if ($nastup == $radek["id"])
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
                    <label for="floatingSelect">Zastávka</label>
                    </div>
                </div>     

                <div class="col-md-2 mt-5">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="cilova" name="cilova" placeholder="" value="<?php echo $cilova;?>">
                        <label for="floatingInputGrid">Cílová stanice</label>
                    </div>
                </div>
                
      
                <div class="row g-2 mt-5">
                    <button type="submit" class="btn btn-primary btn-block">Ulož změny do databáze</button>
                </div>

                </form>

            </div>

            <?php
        } */
/*         elseif (($_GET["typ"] == "zmenasmennosti" and $_SESSION["typ"] == "1") or ($_GET["typ"] == "zmenasmennosti" and $_SESSION["typ"] == "4") or ($_GET["typ"] == "zmenasmennosti" and $_SESSION["typ"] == "5"))
        {   
            global $conn;   
                          
            ?>
            
            <h3 class="text-center m-2 p-2">Hromadná změna směnnosti - aktuální směna</h3>

            <h5 class="text-center m-2">Do textového pole níže zadej na každý řádek jednu změnu směn ve tvaru osobní číslo;směna</h5>
            <h5 class="text-center m-2">123;R</h5>
            <h5 class="text-center m-2">Osobní číslo ve tvaru uvedeného v sekci Zaměstnanci, směny jsou pak R,O,N,NN,NR,PR,S-R,S-O,S-N,N-R,N-O,N-N</h5>
        
            <div class="container">
                            
                <form class="row g-3" action="zamestnanci.php?typ=provedzmenusmen" method="post">           

                <div class="col-md-12">
                    <div class="form">
            
                    <label for="textik" class="form-label"></label>
                    <textarea class="form-control" id="textik" name="textik" rows="20" placeholder="123;R&#10;541;N&#10;236;O&#10;"></textarea>
                    
                    </div>
                </div>
      
                <div class="row g-2 mt-2">
                    <button type="submit" class="btn btn-primary btn-block">Proveď hromadnou změnu směn (aktuální týden)</button>
                </div>

                </form>

            </div>

            <?php
        } */
       /*  elseif (($_GET["typ"] == "zmenasmennosti2" and $_SESSION["typ"] == "1") or ($_GET["typ"] == "zmenasmennosti2" and $_SESSION["typ"] == "4") or ($_GET["typ"] == "zmenasmennosti2" and $_SESSION["typ"] == "5"))
        {   
            global $conn;   
                          
            ?>
            
            <h3 class="text-center m-2 p-2">Hromadná změna směnnosti - směna následující týden</h3>

            <h5 class="text-center m-2">Do textového pole níže zadej na každý řádek jednu změnu směn ve tvaru osobní číslo;směna</h5>
            <h5 class="text-center m-2">123;R</h5>
            <h5 class="text-center m-2">Osobní číslo ve tvaru uvedeného v sekci Zaměstnanci, směny jsou pak R,O,N,NN,NR,PR,S-R,S-O,S-N,N-R,N-O,N-N</h5>
        
            <div class="container">
                            
                <form class="row g-3" action="zamestnanci.php?typ=provedzmenusmen2" method="post">           

                <div class="col-md-12">
                    <div class="form">
            
                    <label for="textik" class="form-label"></label>
                    <textarea class="form-control" id="textik" name="textik" rows="20" placeholder="123;R&#10;541;N&#10;236;O&#10;"></textarea>
                    
                    </div>
                </div>
      
                <div class="row g-2 mt-2">
                    <button type="submit" class="btn btn-primary btn-block">Proveď hromadnou změnu směn (následující týden)</button>
                </div>

                </form>

            </div>

            <?php
        } */
        /* elseif (($_GET["typ"] == "zmenasmennosti3" and $_SESSION["typ"] == "1") or ($_GET["typ"] == "zmenasmennosti3" and $_SESSION["typ"] == "4") or ($_GET["typ"] == "zmenasmennosti3" and $_SESSION["typ"] == "5"))
        {
            global $conn;
            
            $cislo = 0;
                          
            ?>
            
            <h3 class="text-center m-2 p-2">Hromadná změna směnnosti - směna následující týden</h3>

            <form name="myForm" action="zamestnanci.php?typ=zmenasmennosti3" method="post">

            <div class="container">
            <div class="row justify-content-md-center">
            <div class="col col-lg-12">

            <div class="row mb-3">
                <div class="col-md-8">
                    <label for="datepicker">Výběr cílovou firmu</label>

                    <select class="form-select" id="firmasmeny" name="firmasmeny">                      

                    <option value='-1'>Vyber cílovou firmu</option>
                    
                    <?php
                        global $conn;

                        if ($_SESSION["typ"] == "5")
                        {
                            //$sql = "select firma,id,aktivni from firmy where aktivni='1' order by firma";
                            $sql = "SELECT DISTINCT(cilova) FROM `zamestnanci` WHERE cilova<>'' order by cilova";
                        }
                        else
                        {
                            //$sql = "select firma,id,aktivni from firmy where aktivni='1' and firmy.id in (" . $_SESSION["firma"] . ") order by firma";
                            $sql = "SELECT DISTINCT(cilova) FROM `zamestnanci` WHERE cilova<>'' and firma in (" . $_SESSION["firma"] . ")order by cilova";
                        }  

                        if (!($vysledek = mysqli_query($conn, $sql)))
                        {
                        die("Nelze provést dotaz</body></html>");
                        }            

                        if(isset($_POST['firmasmeny']))
                        {
                            $vyberfirma = $_POST['firmasmeny'];
                        }
                        else
                        {
                            $vyberfirma = "www";
                        }   

                        while ($radek = mysqli_fetch_array($vysledek))
                        {  
                            echo (isset($_POST['firmasmeny']) && ($_POST['firmasmeny'] == $radek['cilova'])) ? "<option value='" . $radek['cilova'] . "' class='' selected>" . $radek['cilova'] . "</option>" : "<option value='" . $radek['cilova'] . "' class=''>" . $radek['cilova'] . "</option>";
                        }

                        mysqli_free_result($vysledek);

                        ?>
                       
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="vyber"></label>
                    <button type="submit" class="form-control btn btn-primary">Proveď výběr</button>
                </div>

            </div>

            </div> 
            </div> 
            </div> 

            </form>

            <form name="myForm" action="zamestnanci.php?typ=provedzmenusmen3" method="post" enctype="multipart/form-data">

            <div class="container">
            <div class="row justify-content-md-center">
            <div class="col col-lg-12">  

            <div class='table-responsive-lg text-center'>
            <table class='table table-hover'>
               
                <thead><tr class='table-active'><th scope='col' class='text-center'>#</th><th scope='col' class='text-center'>Os. číslo</th><th scope='col' class='text-center'>Příjmení a jméno</th><th scope='col' class='text-center'>Směna</th></tr></thead>

                <tbody>

                <?php
            
                $dotaz="select id,os_cislo,prijmeni,jmeno,smena2 from zamestnanci where cilova ='" . $vyberfirma . "' and aktivni='1' order by prijmeni"; 
                //echo $dotaz; 

                //echo "tutaj" . $podminka;

                //echo $dotaz;
                if (!($vysledek = mysqli_query($conn, $dotaz)))
                {
                die("Nelze provést dotaz.</body></html>");
                }

                while ($radek = mysqli_fetch_array($vysledek))
                {           
                    $cislo = $cislo + 1;                                           
                    ?>
                    <tr class='table-warning'>

                        <td class='text-center'><?php echo $cislo;?></td>
                        <td class='text-center fw-bold'><?php echo $radek["os_cislo"];?></td>
                        <td class='text-start'><?php echo $radek["prijmeni"];?> <?php echo $radek["jmeno"];?></td>
                        <td class='text-center'>

                        <select name="smena_<?php echo $radek['os_cislo'];?>" class="form-select mt-2 text-center">      
                            
                            <?php
                            echo ($radek['smena2'] == 'N/A') ? "<option value='N/A' class='bg-primary-subtle' selected>N/A</option>" : "<option value='N/A' class='bg-primary-subtle'>N/A</option>";
                            echo ($radek['smena2'] == 'R') ? "<option value='R' class='bg-primary-subtle' selected>Ranní</option>" : "<option value='R' class='bg-primary-subtle'>Ranní</option>";
                            echo ($radek['smena2'] == 'O') ? "<option value='O' class='bg-primary-subtle' selected>Odpolední</option>" : "<option value='O' class='bg-primary-subtle'>Odpolední</option>";
                            echo ($radek['smena2'] == 'N') ? "<option value='N' class='bg-primary-subtle' selected>Noční</option>" : "<option value='N' class='bg-primary-subtle'>Noční</option>";
                            ?>

                        </select>

                        </td>                      

                        <input type="hidden" class="form-control" name="typ_prescasu" value="hromadny">
                        <input type="hidden" class="form-control" name="firma" value="<?php echo $vyberfirma;?>">
                                            
                    </tr>
                    <?php
                }
                ?>

                </tbody>
              
            </table>      
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary">ULOŽ ZMĚNY SMĚN DO DATABÁZE</button>
            </div>

            </div>
            </div>
            </div>

            </form>

            <?php
        } */
        /* elseif (($_GET["typ"] == "provedzmenusmen" and $_SESSION["typ"] == "1") or ($_GET["typ"] == "provedzmenusmen" and $_SESSION["typ"] == "4") or ($_GET["typ"] == "provedzmenusmen" and $_SESSION["typ"] == "5"))
        {   
            global $conn;   
                          
            ?>
            
            <h3 class="text-center m-2 p-2">Měním směny</h3>

            <div class="container">
                            
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") 
                {
                    // Získání textu z <textarea>
                    $text = $_POST["textik"];
                    
                    // rozdeleni textu na radky
                    $lines = explode("\n", $text);
                    $numberOfLines = count($lines);

                    if ($numberOfLines > 0)
                    {   ?>

                        <div class="container-fluid">
                        <div class="row justify-content-md-center">
                        <div class="col col-md-12">
                    
                            <div class='table-responsive-lg text-center'>
                            <table class='table table-hover'>
                            <thead>
                                <tr class='table-active'>
                                    <th scope='col'>#</th>
                                    <th scope='col'>Příjmení</th>
                                    <th scope='col'>Jméno</th>
                                    <th scope='col'>Os. číslo</th>
                                    <th scope='col'>Dosavadní směna</th>
                                    <th scope='col'>Nová směna</th>
                                    <th scope='col'>Stav</th>
                                </tr>
                            </thead>
                            <tbody> 
                                
                            <?php
                            // Zpracování každého řádku zvlášť
                            $cislo = 1;

                            foreach ($lines as $index => $line) 
                            {
                                $poleradek = explode(";",$line); 

                                if (isset($poleradek[0]) and isset($poleradek[1])) 
                                {  
                                    $text = get_name_from_personal_number2($poleradek[0]);
                                    $exploded_text = explode(",",$text); 

                                    if (isset($exploded_text[0]) and isset($exploded_text[1]) and isset($exploded_text[2]) and isset($exploded_text[3]) and isset($exploded_text[4])) 
                                    {   ?>

                                        <tr>
                                            <td class='text-center fw-bold'><?php echo $cislo;?></td>
                                            <td class='text-start'><?php echo $exploded_text[2];?></td>
                                            <td class='text-start'><?php echo $exploded_text[1];?></td>
                                            <td class='text-center'><?php echo $exploded_text[0];?></td>
                                            <td class='text-center'><?php echo $exploded_text[4];?></td>
                                            <td class='text-center'><?php echo $poleradek[1];?></td>
                                            <td class='text-center'><?php echo zmena_smeny($exploded_text[0],$poleradek[1]);?></td>
                                        </tr>

                                        <?php
                                    }
                                    else
                                    {   ?>

                                        <tr>
                                            <td class='text-center fw-bold'><?php echo $cislo;?></td>
                                            <td class='text-start'></td>
                                            <td class='text-start'></td>
                                            <td class='text-center'><?php echo $poleradek[0];?></td>
                                            <td class='text-center'></td>
                                            <td class='text-center'></td>
                                            <td class='text-center'><?php echo zmena_smeny($exploded_text[0],$poleradek[1]);?></td>
                                        </tr>

                                        <?php
                                    }

                                } 
                                else 
                                {
                                    echo "nesmysl<br>";
                                }
                            
                                $cislo +=1;
                            }
                            ?>
                    
                            </tbody>
                            </table>
                            </div>
                
                        </div>
                        </div>
                        </div>

                        <?php
                    }
                    else
                    {

                    }                                                       
          
                }
                ?>

            </div>

            <?php
        } */
        /* elseif (($_GET["typ"] == "provedzmenusmen2" and $_SESSION["typ"] == "1") or ($_GET["typ"] == "provedzmenusmen2" and $_SESSION["typ"] == "4") or ($_GET["typ"] == "provedzmenusmen2" and $_SESSION["typ"] == "5"))
        {   
            global $conn;   
                          
            ?>
            
            <h3 class="text-center m-2 p-2">Měním směny na následující týden</h3>

            <div class="container">
                            
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") 
                {
                    // Získání textu z <textarea>
                    $text = $_POST["textik"];
                    
                    // rozdeleni textu na radky
                    $lines = explode("\n", $text);
                    $numberOfLines = count($lines);

                    if ($numberOfLines > 0)
                    {   ?>

                        <div class="container-fluid">
                        <div class="row justify-content-md-center">
                        <div class="col col-md-12">
                    
                            <div class='table-responsive-lg text-center'>
                            <table class='table table-hover'>
                            <thead>
                                <tr class='table-active'>
                                    <th scope='col'>#</th>
                                    <th scope='col'>Příjmení</th>
                                    <th scope='col'>Jméno</th>
                                    <th scope='col'>Os. číslo</th>
                                    <th scope='col'>Dosavadní směna</th>
                                    <th scope='col'>Nová směna</th>
                                    <th scope='col'>Stav</th>
                                </tr>
                            </thead>
                            <tbody> 
                                
                            <?php
                            // Zpracování každého řádku zvlášť
                            $cislo = 1;

                            foreach ($lines as $index => $line) 
                            {
                                $poleradek = explode(";",$line); 

                                if (isset($poleradek[0]) and isset($poleradek[1])) 
                                {  
                                    $text = get_name_from_personal_number2($poleradek[0]);
                                    $exploded_text = explode(",",$text); 

                                    if (isset($exploded_text[0]) and isset($exploded_text[1]) and isset($exploded_text[2]) and isset($exploded_text[3]) and isset($exploded_text[4])) 
                                    {   ?>

                                        <tr>
                                            <td class='text-center fw-bold'><?php echo $cislo;?></td>
                                            <td class='text-start'><?php echo $exploded_text[2];?></td>
                                            <td class='text-start'><?php echo $exploded_text[1];?></td>
                                            <td class='text-center'><?php echo $exploded_text[0];?></td>
                                            <td class='text-center'><?php echo $exploded_text[4];?></td>
                                            <td class='text-center'><?php echo $poleradek[1];?></td>
                                            <td class='text-center'><?php echo zmena_smeny2($exploded_text[0],$poleradek[1]);?></td>
                                        </tr>

                                        <?php
                                    }
                                    else
                                    {   ?>

                                        <tr>
                                            <td class='text-center fw-bold'><?php echo $cislo;?></td>
                                            <td class='text-start'></td>
                                            <td class='text-start'></td>
                                            <td class='text-center'><?php echo $poleradek[0];?></td>
                                            <td class='text-center'></td>
                                            <td class='text-center'></td>
                                            <td class='text-center'><?php echo zmena_smeny2($exploded_text[0],$poleradek[1]);?></td>
                                        </tr>

                                        <?php
                                    }

                                } 
                                else 
                                {
                                    echo "nesmysl<br>";
                                }
                            
                                $cislo +=1;
                            }
                            ?>
                    
                            </tbody>
                            </table>
                            </div>
                
                        </div>
                        </div>
                        </div>

                        <?php
                    }
                    else
                    {

                    }                                                       
          
                }
                ?>

            </div>

            <?php
        } */
        /* elseif (($_GET["typ"] == "provedzmenusmen3" and $_SESSION["typ"] == "1") or ($_GET["typ"] == "provedzmenusmen3" and $_SESSION["typ"] == "4") or ($_GET["typ"] == "provedzmenusmen3" and $_SESSION["typ"] == "5"))
        { 
       
            $sql = "SELECT os_cislo FROM zamestnanci where cilova='" . $_POST['firma'] . "' and aktivni='1'";
    
            if (!($vysledek = mysqli_query($conn, $sql)))
            {
            die("Nelze provést dotaz</body></html>");
            }   
            
            while ($radek = mysqli_fetch_array($vysledek)) 
            {
                if(isset($_POST['smena_' . $radek['os_cislo']]))
                {
                    zmena_smeny2($radek['os_cislo'],$_POST['smena_' . $radek['os_cislo']]);
                }
            }
            
            mysqli_free_result($vysledek);
  
            ?>

            <div class="container">
            <h3 class="text-center m-2 p-2">SMĚNY BYLY ÚSPĚŠNĚ ZMĚNĚNY</h3>

            <h3 class="text-center m-2 p-2">Budete přesměrování zpět PŘEHLED ZAMĚSTNANCŮ</h3>

            </div>

            <meta http-equiv="refresh" content="5;url=zamestnanci.php">

            <?php
        } */
        elseif (($_GET["typ"] == "filtr" and $_SESSION["typ"] == "1") or ($_GET["typ"] == "filtr" and $_SESSION["typ"] == "3") or ($_GET["typ"] == "filtr" and $_SESSION["typ"] == "4") or ($_GET["typ"] == "filtr" and $_SESSION["typ"] == "5"))
        {   
        
            if (isset($_GET['typ'])) {
                $_SESSION['typ_modal'] = $_GET['typ'];
            }

            // --- 1) Uložení filtrů z POST do SESSION ---
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $_SESSION['filtry'] = [
                    'cilova'        => $_POST['cilova'] ?? 'ALL',
                    'pomer'         => $_POST['pomer'] ?? '',
                    'smena'         => $_POST['smena'] ?? 'VŠE',
                    'nepritomnost'  => $_POST['nepritomnost'] ?? 'ALL'
                ];
            }

            // --- 2) Načtení filtrů ze SESSION (nebo default) ---
            $cilova        = $_SESSION['filtry']['cilova']        ?? 'ALL';
            $pomer         = $_SESSION['filtry']['pomer']         ?? '';
            $smena         = $_SESSION['filtry']['smena']         ?? 'VŠE';
            $nepritomnost  = $_SESSION['filtry']['nepritomnost']  ?? 'ALL';

            // --- 3) Základní SELECT ---
            $sql = "SELECT zamestnanci.id, prijmeni, jmeno, os_cislo, os_cislo_klient, rfid, telefon, adresa,
                    firmy.firma, zastavky.zastavka, zamestnanci.smena, zamestnanci.smena2, auta.spz,
                    zamestnanci.aktivni, nepritomnost, cilova, vstup, vystup, radneukoncen, anulace,
                    (SELECT cas FROM dochazka WHERE zamestnanec = zamestnanci.id 
                    AND now() <= DATE_ADD(concat(datum, ' ', cas), INTERVAL 15 HOUR) 
                    ORDER BY datum DESC, cas DESC LIMIT 1) AS dochazka
                    FROM zamestnanci
                    LEFT JOIN firmy ON zamestnanci.firma = firmy.id
                    LEFT JOIN zastavky ON zamestnanci.nastup = zastavky.id
                    LEFT JOIN auta ON zastavky.auto = auta.id
                    WHERE 1=1";

            // --- filtr cílové stanice ---
            $cilova = $_SESSION['filtry']['cilova'] ?? 'ALL';

            if (!empty($cilova) && $cilova != "ALL") {
                $cilova_safe = mysqli_real_escape_string($conn, $cilova);
                $sql .= " AND zamestnanci.cilova = '$cilova_safe'";
            } elseif ($_SESSION['typ'] != 5) {
                $cilova_list = array_map(function($v) use($conn) { 
                    return "'" . mysqli_real_escape_string($conn, $v) . "'"; 
                }, explode(',', $_SESSION["cilova"]));
                $sql .= " AND zamestnanci.cilova IN (" . implode(',', $cilova_list) . ")";
            }

            // --- filtr poměru ---
            switch ($pomer) {

                // 1 = aktivní podle data (vstup <= dnes && vystup >= dnes nebo NULL)
                case "1":
                    $sql .= " AND vstup <= CURDATE() AND (vystup IS NULL OR vystup='' OR vystup='0000-00-00' OR vystup >= CURDATE())";
                    break;

                // 0 = neaktivní podle data (vystup < dnes)
                case "0":
                    $sql .= " AND vystup < CURDATE() AND vystup NOT IN ('', '0000-00-00') AND vystup IS NOT NULL";
                    break;

                // 33 = neaktivní + řádné ukončení
                case "33":
                    $sql .= " AND vystup < CURDATE() AND vystup NOT IN ('', '0000-00-00') AND radneukoncen='ANO'";
                    break;

                // 44 = neaktivní + neřádné ukončení
                case "44":
                    $sql .= " AND vystup < CURDATE() AND vystup NOT IN ('', '0000-00-00') AND radneukoncen='NE'";
                    break;

                // 55 = ANULACE — vstup = vystup + poměr už skončil
                case "55":
                    $sql .= " AND vstup = vystup AND vystup < CURDATE() AND vystup NOT IN ('', '0000-00-00')";
                    break;

                default:
                    break;
            }


            // --- filtr směny ---
            if (!empty($smena) && $smena !== 'VŠE') {
                $smena_safe = mysqli_real_escape_string($conn, $smena);
                $sql .= " AND zamestnanci.smena = '$smena_safe'";
            }

            // --- filtr nepřítomnosti ---
            if (!empty($nepritomnost) && $nepritomnost != 'ALL') {
                if ($nepritomnost == 'Vše') {
                    $sql .= " AND nepritomnost IN ('DPN','OČR','DOV','ABS','NAR','LEK','NEM','NEO','NEP','PRO')";
                } else {
                    $nep_safe = mysqli_real_escape_string($conn, $nepritomnost);
                    $sql .= " AND zamestnanci.nepritomnost = '$nep_safe'";
                }
            }

            // --- řazení podle příjmení ---
            $sql .= " ORDER BY prijmeni ASC";

            //echo $sql;
           
            $editRole = in_array($_SESSION['typ'], [1,4,5]);

            // Hlavička tabulky podle $pomer
            if (in_array($pomer, [0,33,44,55])) {
                $cols = ['ID','Příjmení','Jméno','Os.č.','Os.č.kl.','Telefon','Řádně ukončen','Vstup','Výstup','Anulace','Odpracováno'];
            } else {
                $cols = ['ID','Příjmení','Jméno','Os.č.','Os.č.kl.','RFID','Telefon','Vstup','Výstup','Doprava','Nástup','Směna','Cílová st.','Docházka'];
            }
            ?>

            <h3 class="text-center m-2 p-2">Přehled zaměstnanců</h3>

            <div class="container-fluid">
            <div class="row justify-content-md-center">
            <div class="col col-md-12">

            <button type="button" class="btn btn-outline-success text-center m-2" 
                    data-bs-toggle="collapse" href="#filtry" aria-expanded="true">
                Filtry
            </button>

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
                    if ($editRole) echo "<th></th>"; // Editace nefiltrujeme
                    ?>
                </tr>
            </thead>
            <tbody>

            <?php
            $cislo = 1;

            if (!($vysledek = mysqli_query($conn, $sql))) die("Nelze provést dotaz");

            while ($radek = mysqli_fetch_assoc($vysledek)) {

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

                if (in_array($radek["nepritomnost"], ["ABS","DPN","OČR"])) {
                    $barva = "table-secondary";
                } elseif ($radek["nepritomnost"] == "DOV") {
                    $barva = "table-warning";
                }

                // -----------------------------
                // ODPRACOVÁNO
                // -----------------------------
                $vstup = $radek['vstup'] != '0000-00-00' ? $radek['vstup'] : date('Y-m-d');
                $vystup = ($radek['vystup'] != '0000-00-00' && date('Y-m-d') > $radek['vystup']) ? $radek['vystup'] : date('Y-m-d');

                $interval = (new DateTime($vstup))->diff(new DateTime($vystup));
                $odpracovano = $interval->days;

                $fwClass = $odpracovano <= 90 ? 'fw-bold' : '';
                ?>

                <tr class="<?= $barva ?> <?= $fwClass ?>">
                    <td class="text-center fw-bold"><?= $cislo ?></td>

                    <?php if (in_array($pomer, [0,33,44,55])): ?>
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
                        <td class='text-center'><?= $radek["spz"] ?></td>
                        <td class='text-start'><?= $radek["zastavka"] ?></td>
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

            <?php novy_zamestnanec_modal(); 


        }
        elseif (($_GET["typ"] == "nastoupil" and $_SESSION["typ"] == "1") or ($_GET["typ"] == "nastoupil" and $_SESSION["typ"] == "5"))
        {
            global $conn;

            $sql = "select id,jmeno,prijmeni,telefon,adresa,nastup,nastupmisto,smena,klient,firma,cilova,oscislo from nabory where id='" . $_GET['id'] . "'";

            if (!($vysledek = mysqli_query($conn, $sql)))
            {
            die("Nelze provést dotaz</body></html>");
            }

            $pole = array();

            while ($radek = mysqli_fetch_array($vysledek, MYSQLI_ASSOC)) 
            {
                $pole[] = $radek;
            }

            mysqli_free_result($vysledek);

            //vlozeni noveho řádku do tabulky zaměstnanců
            $dotaz="insert into zamestnanci (os_cislo,prijmeni,jmeno,vstup,nastup,telefon,adresa,firma,smena,smena2,cilova,aktivni,nabor,smennost) values ('" . $pole[0]['oscislo'] . "','" . $pole[0]['prijmeni'] . "','" . $pole[0]['jmeno'] . "','" . $pole[0]['nastup'] . "','" . $pole[0]['nastupmisto'] . "','" . $pole[0]['telefon'] . "','" . $pole[0]['adresa'] . "','" . $pole[0]['firma'] . "','" . $pole[0]['smena'] . "','N/A','" . $pole[0]['cilova'] . "','1','" . $pole[0]['id'] . "','3SM')";
            
            //echo $dotaz;

            if (!($vysledek = mysqli_query($conn, $dotaz)))
            {
            die("Nelze provést dotaz.</body></html>");
            }

            //upraveni zaznamu v naborech
            $dotaz="update nabory set vysledek='Nastoupil' where id='" . $pole[0]['id'] . "'";
    
            if (!($vysledek = mysqli_query($conn, $dotaz)))
            {
            die("Nelze provést dotaz.</body></html>");
            }

            //vlozim zaznam do logu
            $now = new DateTime('now', new DateTimeZone('Europe/Prague'));

            if (!($vysledek = mysqli_query($conn, 
            "insert into logs (kdo,typ,infotext,datumcas) values ('" . $_SESSION["log_id"] . "','Přijat zaměstnanec','Přijat zaměstnanec " . $pole[0]['prijmeni'] . " " . $pole[0]['jmeno'] . "','" . $now->format('Y-m-d H:i:s') . "')")))
            {
            die("Nelze provést dotaz.</body></html>");
            } 

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
                <td><?= $radek["spz"] ?></td>
                <td><?= $radek["zastavka"] ?></td>
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