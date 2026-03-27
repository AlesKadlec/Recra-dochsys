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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
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
            $date1=date_create($_POST["datepicker"]);
            $date2=date_create($_POST["datepicker2"]);

            $date3=date_create($_POST["datepicker3"]);

            if ($_POST["nepritomnost"] == '')
            {
                $date4 = "0000-00-00";
            }
            else
            {
                $date4=date_format(date_create($_POST["datepicker4"]),'Y-m-d');
            }           
        
            if ($_POST["aktivni"] == 0) // kdyz je neaktivni, nastavim mu nepritomnost na prazdnou hodnotu
            {
                $sql = "update zamestnanci set os_cislo='" . $_POST["oscislo"] . "',os_cislo_klient='" . $_POST["oscisloklient"] . "',prijmeni='" . $_POST["prijmeni"] . "',jmeno='" . $_POST["jmeno"] . "',rfid='" . $_POST["rfid"] . "',nastup='" . $_POST["nastup"] . "',telefon='" . $_POST["telefon"] . "', adresa='" . $_POST["adresa"] . "',firma='" . $_POST["firma"] . "',smena='N/A',aktivni='" . $_POST["aktivni"] . "',nepritomnost='',smena2='N/A',cilova='" . $_POST["cilova"] . "',vstup='" . date_format($date1,"Y-m-d") . "',vystup='" . date_format($date2,"Y-m-d") . "',anulace='" . date_format($date3,"Y-m-d") . "',smennost='" . $_POST["smennost"] . "' where id='" . $_POST["id_zam"] . "'";
            }
            else
            {
                if ($_POST["nepritomnost"] == '')
                {
                    $sql = "update zamestnanci set os_cislo='" . $_POST["oscislo"] . "',os_cislo_klient='" . $_POST["oscisloklient"] . "',prijmeni='" . $_POST["prijmeni"] . "',jmeno='" . $_POST["jmeno"] . "',rfid='" . $_POST["rfid"] . "',nastup='" . $_POST["nastup"] . "',telefon='" . $_POST["telefon"] . "', adresa='" . $_POST["adresa"] . "',firma='" . $_POST["firma"] . "',smena='" . $_POST["smena"] . "',aktivni='" . $_POST["aktivni"] . "',nepritomnost='" . $_POST["nepritomnost"] . "',smena2='" . $_POST["smena2"] . "',cilova='" . $_POST["cilova"] . "',vstup='" . date_format($date1,"Y-m-d") . "',vystup='" . date_format($date2,"Y-m-d") . "',anulace='0000-00-00',dpn_od='" . $date4 . "',smennost='" . $_POST["smennost"] . "' where id='" . $_POST["id_zam"] . "'";
                }
                else
                {
                    $sql = "update zamestnanci set os_cislo='" . $_POST["oscislo"] . "',os_cislo_klient='" . $_POST["oscisloklient"] . "',prijmeni='" . $_POST["prijmeni"] . "',jmeno='" . $_POST["jmeno"] . "',rfid='" . $_POST["rfid"] . "',nastup='" . $_POST["nastup"] . "',telefon='" . $_POST["telefon"] . "', adresa='" . $_POST["adresa"] . "',firma='" . $_POST["firma"] . "',smena='N/A',aktivni='" . $_POST["aktivni"] . "',nepritomnost='" . $_POST["nepritomnost"] . "',smena2='N/A',cilova='" . $_POST["cilova"] . "',vstup='" . date_format($date1,"Y-m-d") . "',vystup='" . date_format($date2,"Y-m-d") . "',anulace='0000-00-00',dpn_od='" . $date4 . "',smennost='" . $_POST["smennost"] . "' where id='" . $_POST["id_zam"] . "'";
                }
            }
                        
            if (!($vysledek = mysqli_query($conn, $sql)))
            {
            die("Nelze provést dotaz.</body></html>");
            }

            //echo $sql;

            //zaznam do logu
            $now = new DateTime('now', new DateTimeZone('Europe/Prague'));
    
            if (!($vysledek = mysqli_query($conn, 
            "insert into logs (kdo,typ,infotext,datumcas) values ('" . $_SESSION["log_id"] . "','Editace zaměstnance','Editován zaměstnanec " . $_POST["prijmeni"] . " " . $_POST["jmeno"] . "','" . $now->format('Y-m-d H:i:s') . "')")))
            {
            die("Nelze provést dotaz.</body></html>");
            }
            
            $_SESSION["vyberfirma"] = $_POST['firma'];
            ?>

            <div class="container">
                <h3 class="text-center m-2 p-2">Zaměstnanec byl upraven</h3>

                <h3 class="text-center m-2 p-2">Budete přesměrování zpět na hlavní stránku</h3>

            </div>          

            <meta http-equiv="refresh" content="5;url=zamestnanci.php?typ=filtr">
            
            <?php
        }
        elseif (($_GET["typ"] == "editzam" and $_SESSION["typ"] == "1") or ($_GET["typ"] == "editzam" and $_SESSION["typ"] == "4") or ($_GET["typ"] == "editzam" and $_SESSION["typ"] == "5"))
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
        }
        elseif (($_GET["typ"] == "zmenasmennosti" and $_SESSION["typ"] == "1") or ($_GET["typ"] == "zmenasmennosti" and $_SESSION["typ"] == "4") or ($_GET["typ"] == "zmenasmennosti" and $_SESSION["typ"] == "5"))
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
        }
        elseif (($_GET["typ"] == "zmenasmennosti2" and $_SESSION["typ"] == "1") or ($_GET["typ"] == "zmenasmennosti2" and $_SESSION["typ"] == "4") or ($_GET["typ"] == "zmenasmennosti2" and $_SESSION["typ"] == "5"))
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
        }
        elseif (($_GET["typ"] == "zmenasmennosti3" and $_SESSION["typ"] == "1") or ($_GET["typ"] == "zmenasmennosti3" and $_SESSION["typ"] == "4") or ($_GET["typ"] == "zmenasmennosti3" and $_SESSION["typ"] == "5"))
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
        }
        elseif (($_GET["typ"] == "provedzmenusmen" and $_SESSION["typ"] == "1") or ($_GET["typ"] == "provedzmenusmen" and $_SESSION["typ"] == "4") or ($_GET["typ"] == "provedzmenusmen" and $_SESSION["typ"] == "5"))
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
        }
        elseif (($_GET["typ"] == "provedzmenusmen2" and $_SESSION["typ"] == "1") or ($_GET["typ"] == "provedzmenusmen2" and $_SESSION["typ"] == "4") or ($_GET["typ"] == "provedzmenusmen2" and $_SESSION["typ"] == "5"))
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
        }
        elseif (($_GET["typ"] == "provedzmenusmen3" and $_SESSION["typ"] == "1") or ($_GET["typ"] == "provedzmenusmen3" and $_SESSION["typ"] == "4") or ($_GET["typ"] == "provedzmenusmen3" and $_SESSION["typ"] == "5"))
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
        }
        elseif (($_GET["typ"] == "filtr" and $_SESSION["typ"] == "1") or ($_GET["typ"] == "filtr" and $_SESSION["typ"] == "3") or ($_GET["typ"] == "filtr" and $_SESSION["typ"] == "4") or ($_GET["typ"] == "filtr" and $_SESSION["typ"] == "5"))
        {   

            if (isset($_POST["firma"]))
            {
                $_SESSION["vyberfirma"] = $_POST["firma"];
                $_SESSION["vyberpomer"] = $_POST["pomer"];
                $_SESSION["vybersmena"] = $_POST["smena"];
                $_SESSION["vybernepritomnost"] = $_POST["nepritomnost"];
                $_SESSION["razeni"] = "prijmeni,zamestnanci.smena";
            }
            else
            {
                $_POST["firma"] = $_SESSION["vyberfirma"];
                $_POST["pomer"] = $_SESSION["vyberpomer"];
                $_POST["smena"] = $_SESSION["vybersmena"];
                $_POST["nepritomnost"] = $_SESSION["vybernepritomnost"];
                $_POST["razeni"] = $_SESSION["razeni"];
            }


            if (isset($_GET["sort"]))
            {
    
                if ($_GET["sort"] == "jmenoasc")
                {
                    $sort = "prijmeni asc";
                    $sort_link = "&sort=jmenoasc";
                }
                elseif ($_GET["sort"] == "jmenodesc")
                {
                    $sort = "prijmeni desc";
                    $sort_link = "&sort=jmenodesc";
                }
                elseif ($_GET["sort"] == "dochazkaasc")
                {
                    $sort = "dochazka asc,prijmeni desc";
                    $sort_link = "&sort=dochazkaasc";
                }
                elseif ($_GET["sort"] == "dochazkadesc")
                {
                    $sort = "dochazka desc,prijmeni desc";
                    $sort_link = "&sort=dochazkadesc";
                }
                else
                {
                    $sort = "prijmeni,zamestnanci.smena";
                    $sort_link = "&sort=datedesc";
                }
            }
            else
            {
                $sort = "prijmeni,zamestnanci.smena";
                $sort_link = "&sort=datedesc";
            }
    
            if (isset($_GET["sort"]))
            {
                $odkaz = "zamestnanci.php?typ=filtr" . $sort_link;
            }
            else
            {
                $odkaz = "zamestnanci.php?typ=filtr" . $sort_link;
            }
            ?>

            <h3 class="text-center m-2 p-2">Přehled zaměstnanců</h3>

            <div class="container-fluid">
            <div class="row justify-content-md-center">
            <div class="col col-md-12">

            <div class="d-grid gap-2 d-md-flex justify-content-md-center">

                <?php
                if (isset($_GET["sort"]))
                {   ?>
                        <form class="row g-3" action="zamestnanci.php?typ=filtr<?php echo $sort_link;?>" method="post" name="zamestnanci">
                    <?php
                }
                else
                {   ?>
                        <form class="row g-3" action="zamestnanci.php?typ=filtr" method="post" name="zamestnanci">
                    <?php
                }
                ?>              

                    <div class="col-auto">
                        <label for="datepicker">Výběr firmy</label>

                        <select class="form-select mt-2" id="firma" name="firma">

                            <option value="ALL">Všechny firmy</option>
                            
                            <?php
                            global $conn;

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
                                if ($_POST["firma"] == $radek["id"])
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
                    </div>

                    <div class="col-auto">
                        <label for="datepicker">Pracovní poměr</label>

                        <select class="form-select mt-2" id="pomer" name="pomer" onChange="change_pomer(document.zamestnanci.pomer.value);">
                            <?php
                            $options = [
                                "1"  => "Aktivní",
                                "0"  => "Neaktivní",
                                "33" => "Řádně ukončen",
                                "44" => "Řádně neukončen",
                                "55" => "Anulován",
                            ];

                            foreach ($options as $value => $label) {
                                $selected = ($_POST["pomer"] == $value) ? "selected" : "";
                                echo "<option value=\"$value\" $selected>$label</option>";
                            }
                            ?>
                        </select>

                    </div>

                    <div class="col-auto">
                        <label for="datepicker">Směna</label>

                        <select class="form-select mt-2" id="smena" name="smena">
                            <?php
                            $shifts = [
                                "R"   => "Ranní",
                                "O"   => "Odpolední",
                                "N"   => "Noční",
                                "NN"  => "NN",
                                "NR"  => "NR",
                                "S-R" => "Sobota Ranní",
                                "S-O" => "Sobota Odpolední",
                                "S-N" => "Sobota Noční",
                                "N-R" => "Neděle Ranní",
                                "N-O" => "Neděle Odpolední",
                                "N-N" => "Neděle Noční",
                                "PR"  => "Přesčas",
                                "N/A" => "N/A",
                                "VŠE" => "Všechno",
                            ];

                            foreach ($shifts as $value => $label) {
                                $selected = ($_POST["smena"] == $value) ? "selected" : "";
                                echo "<option value=\"$value\" $selected>$label</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-auto">
                        <label for="datepicker">Nepřítomnost</label>


                        <select class="form-select mt-2" id="nepritomnost" name="nepritomnost">
                            <?php
                            $absenceOptions = [
                                ""     => "Přítomní",
                                "DPN"  => "DPN",
                                "OČR"  => "OČR",
                                "DOV"  => "Dovolená",
                                "ABS"  => "Absence",
                            ];

                            foreach ($absenceOptions as $value => $label) {
                                $selected = ($_POST["nepritomnost"] == $value) ? "selected" : "";
                                echo "<option value=\"$value\" $selected>$label</option>";
                            }
                            ?>
                        </select>

                    </div>
                            
                    <div class="col-auto">
                        <label for="vyber"></label>
                        <button type="submit" class="form-control btn btn-primary mt-2">Proveď výběr</button>
                    </div> 

                    <div class="col-auto">                    
           
                        <a href="zamestnanci.php?typ=filtr&sort=jmenoasc"><button type="button" class="btn btn-sm align-middle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-sort-alpha-down" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M10.082 5.629 9.664 7H8.598l1.789-5.332h1.234L13.402 7h-1.12l-.419-1.371zm1.57-.785L11 2.687h-.047l-.652 2.157z"/>
                                <path d="M12.96 14H9.028v-.691l2.579-3.72v-.054H9.098v-.867h3.785v.691l-2.567 3.72v.054h2.645zM4.5 2.5a.5.5 0 0 0-1 0v9.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L4.5 12.293z"/>
                            </svg>
                        </button></a>

                        <a href="zamestnanci.php?typ=filtr&sort=jmenodesc"><button type="button" class="btn btn-sm align-middle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-sort-alpha-down-alt" viewBox="0 0 16 16">
                            <path d="M12.96 7H9.028v-.691l2.579-3.72v-.054H9.098v-.867h3.785v.691l-2.567 3.72v.054h2.645z"/>
                            <path fill-rule="evenodd" d="M10.082 12.629 9.664 14H8.598l1.789-5.332h1.234L13.402 14h-1.12l-.419-1.371zm1.57-.785L11 9.688h-.047l-.652 2.156z"/>
                            <path d="M4.5 2.5a.5.5 0 0 0-1 0v9.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L4.5 12.293z"/>
                            </svg>
                        </button></a>

                        <a href="zamestnanci.php?typ=filtr&sort=dochazkaasc"><button type="button" class="btn btn-sm align-middle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-sort-numeric-down-alt" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M11.36 7.098c-1.137 0-1.708-.657-1.762-1.278h1.004c.058.223.343.45.773.45.824 0 1.164-.829 1.133-1.856h-.059c-.148.39-.57.742-1.261.742-.91 0-1.72-.613-1.72-1.758 0-1.148.848-1.836 1.973-1.836 1.09 0 2.063.637 2.063 2.688 0 1.867-.723 2.848-2.145 2.848zm.062-2.735c.504 0 .933-.336.933-.972 0-.633-.398-1.008-.94-1.008-.52 0-.927.375-.927 1 0 .64.418.98.934.98"/>
                            <path d="M12.438 8.668V14H11.39V9.684h-.051l-1.211.859v-.969l1.262-.906h1.046zM4.5 2.5a.5.5 0 0 0-1 0v9.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L4.5 12.293z"/>
                            </svg>
                        </button></a>

                        <a href="zamestnanci.php?typ=filtr&sort=dochazkadesc"><button type="button" class="btn btn-sm align-middle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-sort-numeric-down" viewBox="0 0 16 16">
                            <path d="M12.438 1.668V7H11.39V2.684h-.051l-1.211.859v-.969l1.262-.906h1.046z"/>
                            <path fill-rule="evenodd" d="M11.36 14.098c-1.137 0-1.708-.657-1.762-1.278h1.004c.058.223.343.45.773.45.824 0 1.164-.829 1.133-1.856h-.059c-.148.39-.57.742-1.261.742-.91 0-1.72-.613-1.72-1.758 0-1.148.848-1.835 1.973-1.835 1.09 0 2.063.636 2.063 2.687 0 1.867-.723 2.848-2.145 2.848zm.062-2.735c.504 0 .933-.336.933-.972 0-.633-.398-1.008-.94-1.008-.52 0-.927.375-.927 1 0 .64.418.98.934.98"/>
                            <path d="M4.5 2.5a.5.5 0 0 0-1 0v9.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L4.5 12.293z"/>
                            </svg>
                        </button></a>

                    </div>

                </form>
            </div>

            <br>
            <div class='table-responsive-lg text-center'>
            <table class='table table-hover'>
            <thead>
                <tr class='table-active'>
                            
                <?php
                if (($_POST['pomer'] == 0) or ($_POST['pomer'] == 33) or ($_POST['pomer'] == 44) or ($_POST['pomer'] == 55))
                {   ?>
                        <th scope='col'>ID</th>
                        <th scope='col'>Příjmení</th>
                        <th scope='col'>Jméno</th>
                        <th scope='col'>Os.č.</th>
                        <th scope='col'>Os.č.kl.</th>
                        <th scope='col'>Telefon</th>
                        <th scope='col'>Řádně ukončen</th>
                        <th scope='col'>Vstup</th>
                        <th scope='col'>Výstup</th>
                        <th scope='col'>Anulace</th>
                        <th scope='col'>Odpracováno</th>
                    <?php
                }
                else
                {   ?>
                        <th scope='col'>ID</th>
                        <th scope='col'>Příjmení</th>
                        <th scope='col'>Jméno</th>
                        <th scope='col'>Os.č.</th>
                        <th scope='col'>Os.č.kl.</th>
                        <th scope='col'>RFID</th>
                        <th scope='col'>Telefon</th>
                        <th scope='col'>Vstup</th>
                        <th scope='col'>Výstup</th>
                        <th scope='col'>Doprava</th>
                        <th scope='col'>Nástup</th>
                        <th scope='col'>Směna</th>
                        <th scope='col'>Cílová st.</th>
                        <th scope='col'>Docházka</th>
                    <?php
                }

                if (($_SESSION['typ'] == 1) or ($_SESSION['typ'] == 4) or ($_SESSION['typ'] == 5))
                {   ?>
                        <th scope='col'>Editace</th>
                    <?php
                }
                ?>
                    
                </tr>
            </thead>
            <tbody>

            <?php
                if ((isset($_GET["idemp"])) and (isset($_GET["ukoncen"])))
                {
                    $dotaz="update zamestnanci set radneukoncen='" . $_GET['ukoncen'] . "' where id='" . $_GET['idemp'] . "'"; 

                    if (!($vysledek = mysqli_query($conn, $dotaz)))
                    {
                    die("Nelze provést dotaz.</body></html>");
                    } 
                }

                $cislo = 1;
                        
                if ($_POST['smena'] == "VŠE")
                {
                    $smena = "<> ''";
                }
                else
                {
                    $smena = "= '" . $_POST['smena'] . "'";
                }

                if ($_POST['nepritomnost'] == "")
                {
                    $nepritomnost = "= ''";
                }
                else
                {
                    $nepritomnost = "= '" . $_POST['nepritomnost'] . "'";
                }

                if ($_POST['firma'] == "ALL")
                {
                    //$sql = "select firma,id,aktivni from firmy where aktivni='1' and firmy.id in (" . $_SESSION["firma"] . ") order by firma";
                    if ($_SESSION['typ'] == 5)
                    {
                        if ($_POST['pomer'] == "33")
                        {
                            $sql = "select zamestnanci.id,prijmeni,jmeno,os_cislo,os_cislo_klient,rfid,telefon,adresa,firmy.firma,zastavky.zastavka,zamestnanci.smena,zamestnanci.smena2,auta.spz,zamestnanci.aktivni,nepritomnost,cilova,vstup,vystup,radneukoncen,anulace,(SELECT cas FROM dochazka WHERE zamestnanec = zamestnanci.id AND now() <= DATE_ADD(concat(datum, ' ', cas), INTERVAL 15 HOUR) ORDER BY datum DESC, cas DESC LIMIT 1) AS dochazka from zamestnanci left join firmy on zamestnanci.firma = firmy.id left join zastavky on zamestnanci.nastup = zastavky.id left join auta on zastavky.auto = auta.id where zamestnanci.firma >= 0 and zamestnanci.aktivni='0' and radneukoncen='ANO' and smena " . $smena . " and nepritomnost " . $nepritomnost . " order by " . $sort;
                        }
                        elseif ($_POST['pomer'] == "44")
                        {
                            $sql = "select zamestnanci.id,prijmeni,jmeno,os_cislo,os_cislo_klient,rfid,telefon,adresa,firmy.firma,zastavky.zastavka,zamestnanci.smena,zamestnanci.smena2,auta.spz,zamestnanci.aktivni,nepritomnost,cilova,vstup,vystup,radneukoncen,anulace,(SELECT cas FROM dochazka WHERE zamestnanec = zamestnanci.id AND now() <= DATE_ADD(concat(datum, ' ', cas), INTERVAL 15 HOUR) ORDER BY datum DESC, cas DESC LIMIT 1) AS dochazka from zamestnanci left join firmy on zamestnanci.firma = firmy.id left join zastavky on zamestnanci.nastup = zastavky.id left join auta on zastavky.auto = auta.id where zamestnanci.firma >= 0 and zamestnanci.aktivni='0' and radneukoncen='NE' and smena " . $smena . " and nepritomnost " . $nepritomnost . " order by " . $sort;
                        }
                        elseif ($_POST['pomer'] == "55")
                        {
                            $sql = "select zamestnanci.id,prijmeni,jmeno,os_cislo,os_cislo_klient,rfid,telefon,adresa,firmy.firma,zastavky.zastavka,zamestnanci.smena,zamestnanci.smena2,auta.spz,zamestnanci.aktivni,nepritomnost,cilova,vstup,vystup,radneukoncen,anulace,(SELECT cas FROM dochazka WHERE zamestnanec = zamestnanci.id AND now() <= DATE_ADD(concat(datum, ' ', cas), INTERVAL 15 HOUR) ORDER BY datum DESC, cas DESC LIMIT 1) AS dochazka from zamestnanci left join firmy on zamestnanci.firma = firmy.id left join zastavky on zamestnanci.nastup = zastavky.id left join auta on zastavky.auto = auta.id where zamestnanci.firma >= 0 and zamestnanci.aktivni='0' and anulace<>'0000-00-00' and smena " . $smena . " and nepritomnost " . $nepritomnost . " order by " . $sort;
                        }
                        else
                        {
                            $sql = "select zamestnanci.id,prijmeni,jmeno,os_cislo,os_cislo_klient,rfid,telefon,adresa,firmy.firma,zastavky.zastavka,zamestnanci.smena,zamestnanci.smena2,auta.spz,zamestnanci.aktivni,nepritomnost,cilova,vstup,vystup,radneukoncen,anulace,(SELECT cas FROM dochazka WHERE zamestnanec = zamestnanci.id AND now() <= DATE_ADD(concat(datum, ' ', cas), INTERVAL 15 HOUR) ORDER BY datum DESC, cas DESC LIMIT 1) AS dochazka from zamestnanci left join firmy on zamestnanci.firma = firmy.id left join zastavky on zamestnanci.nastup = zastavky.id left join auta on zastavky.auto = auta.id where zamestnanci.firma >= 0 and zamestnanci.aktivni='" . $_POST['pomer'] . "' and smena " . $smena . " and nepritomnost " . $nepritomnost . " order by " . $sort;
                        }
                    }
                    else
                    {
                        if ($_POST['pomer'] == "33")
                        {
                            $sql = "select zamestnanci.id,prijmeni,jmeno,os_cislo,os_cislo_klient,rfid,telefon,adresa,firmy.firma,zastavky.zastavka,zamestnanci.smena,zamestnanci.smena2,auta.spz,zamestnanci.aktivni,nepritomnost,cilova,vstup,vystup,radneukoncen,anulace,(SELECT cas FROM dochazka WHERE zamestnanec = zamestnanci.id AND now() <= DATE_ADD(concat(datum, ' ', cas), INTERVAL 15 HOUR) ORDER BY datum DESC, cas DESC LIMIT 1) AS dochazka from zamestnanci left join firmy on zamestnanci.firma = firmy.id left join zastavky on zamestnanci.nastup = zastavky.id left join auta on zastavky.auto = auta.id where zamestnanci.firma in (" . $_SESSION["firma"] . ") and zamestnanci.aktivni='0' and radneukoncen='ANO' and smena " . $smena . " and nepritomnost " . $nepritomnost . " order by " . $sort;
                        }
                        elseif ($_POST['pomer'] == "44")
                        {
                            $sql = "select zamestnanci.id,prijmeni,jmeno,os_cislo,os_cislo_klient,rfid,telefon,adresa,firmy.firma,zastavky.zastavka,zamestnanci.smena,zamestnanci.smena2,auta.spz,zamestnanci.aktivni,nepritomnost,cilova,vstup,vystup,radneukoncen,anulace,(SELECT cas FROM dochazka WHERE zamestnanec = zamestnanci.id AND now() <= DATE_ADD(concat(datum, ' ', cas), INTERVAL 15 HOUR) ORDER BY datum DESC, cas DESC LIMIT 1) AS dochazka from zamestnanci left join firmy on zamestnanci.firma = firmy.id left join zastavky on zamestnanci.nastup = zastavky.id left join auta on zastavky.auto = auta.id where zamestnanci.firma in (" . $_SESSION["firma"] . ") and zamestnanci.aktivni='0' and radneukoncen='NE' and smena " . $smena . " and nepritomnost " . $nepritomnost . " order by " . $sort;
                        }
                        elseif ($_POST['pomer'] == "55")
                        {
                            $sql = "select zamestnanci.id,prijmeni,jmeno,os_cislo,os_cislo_klient,rfid,telefon,adresa,firmy.firma,zastavky.zastavka,zamestnanci.smena,zamestnanci.smena2,auta.spz,zamestnanci.aktivni,nepritomnost,cilova,vstup,vystup,radneukoncen,anulace,(SELECT cas FROM dochazka WHERE zamestnanec = zamestnanci.id AND now() <= DATE_ADD(concat(datum, ' ', cas), INTERVAL 15 HOUR) ORDER BY datum DESC, cas DESC LIMIT 1) AS dochazka from zamestnanci left join firmy on zamestnanci.firma = firmy.id left join zastavky on zamestnanci.nastup = zastavky.id left join auta on zastavky.auto = auta.id where zamestnanci.firma in (" . $_SESSION["firma"] . ") and zamestnanci.aktivni='0' and anulace<>'0000-00-00' and smena " . $smena . " and nepritomnost " . $nepritomnost . " order by " . $sort;
                        }
                        else
                        {
                            $sql = "select zamestnanci.id,prijmeni,jmeno,os_cislo,os_cislo_klient,rfid,telefon,adresa,firmy.firma,zastavky.zastavka,zamestnanci.smena,zamestnanci.smena2,auta.spz,zamestnanci.aktivni,nepritomnost,cilova,vstup,vystup,radneukoncen,anulace,(SELECT cas FROM dochazka WHERE zamestnanec = zamestnanci.id AND now() <= DATE_ADD(concat(datum, ' ', cas), INTERVAL 15 HOUR) ORDER BY datum DESC, cas DESC LIMIT 1) AS dochazka from zamestnanci left join firmy on zamestnanci.firma = firmy.id left join zastavky on zamestnanci.nastup = zastavky.id left join auta on zastavky.auto = auta.id where zamestnanci.firma in (" . $_SESSION["firma"] . ") and zamestnanci.aktivni='" . $_POST['pomer'] . "' and smena " . $smena . " and nepritomnost " . $nepritomnost . " order by " . $sort;
                        }
                    }
                }
                else
                {
                    if ($_POST['pomer'] == "33")
                    {
                        $sql = "select zamestnanci.id,prijmeni,jmeno,os_cislo,os_cislo_klient,rfid,telefon,adresa,firmy.firma,zastavky.zastavka,zamestnanci.smena,zamestnanci.smena2,auta.spz,zamestnanci.aktivni,nepritomnost,cilova,vstup,vystup,radneukoncen,anulace,(SELECT cas FROM dochazka WHERE zamestnanec = zamestnanci.id AND now() <= DATE_ADD(concat(datum, ' ', cas), INTERVAL 15 HOUR) ORDER BY datum DESC, cas DESC LIMIT 1) AS dochazka from zamestnanci left join firmy on zamestnanci.firma = firmy.id left join zastavky on zamestnanci.nastup = zastavky.id left join auta on zastavky.auto = auta.id where zamestnanci.firma='" . $_POST['firma'] . "' and zamestnanci.aktivni='0' and radneukoncen='ANO' and smena " . $smena . " and nepritomnost " . $nepritomnost . " order by " . $sort;
                    }
                    elseif ($_POST['pomer'] == "44")
                    {
                        $sql = "select zamestnanci.id,prijmeni,jmeno,os_cislo,os_cislo_klient,rfid,telefon,adresa,firmy.firma,zastavky.zastavka,zamestnanci.smena,zamestnanci.smena2,auta.spz,zamestnanci.aktivni,nepritomnost,cilova,vstup,vystup,radneukoncen,anulace,(SELECT cas FROM dochazka WHERE zamestnanec = zamestnanci.id AND now() <= DATE_ADD(concat(datum, ' ', cas), INTERVAL 15 HOUR) ORDER BY datum DESC, cas DESC LIMIT 1) AS dochazka from zamestnanci left join firmy on zamestnanci.firma = firmy.id left join zastavky on zamestnanci.nastup = zastavky.id left join auta on zastavky.auto = auta.id where zamestnanci.firma='" . $_POST['firma'] . "' and zamestnanci.aktivni='0' and radneukoncen='NE' and smena " . $smena . " and nepritomnost " . $nepritomnost . " order by " . $sort;
                    }
                    elseif ($_POST['pomer'] == "55")
                    {
                        $sql = "select zamestnanci.id,prijmeni,jmeno,os_cislo,os_cislo_klient,rfid,telefon,adresa,firmy.firma,zastavky.zastavka,zamestnanci.smena,zamestnanci.smena2,auta.spz,zamestnanci.aktivni,nepritomnost,cilova,vstup,vystup,radneukoncen,anulace,(SELECT cas FROM dochazka WHERE zamestnanec = zamestnanci.id AND now() <= DATE_ADD(concat(datum, ' ', cas), INTERVAL 15 HOUR) ORDER BY datum DESC, cas DESC LIMIT 1) AS dochazka from zamestnanci left join firmy on zamestnanci.firma = firmy.id left join zastavky on zamestnanci.nastup = zastavky.id left join auta on zastavky.auto = auta.id where zamestnanci.firma='" . $_POST['firma'] . "' and zamestnanci.aktivni='0' and anulace<>'0000-00-00' and smena " . $smena . " and nepritomnost " . $nepritomnost . " order by " . $sort;
                    }
                    else
                    {
                        $sql = "select zamestnanci.id,prijmeni,jmeno,os_cislo,os_cislo_klient,rfid,telefon,adresa,firmy.firma,zastavky.zastavka,zamestnanci.smena,zamestnanci.smena2,auta.spz,zamestnanci.aktivni,nepritomnost,cilova,vstup,vystup,radneukoncen,anulace,(SELECT cas FROM dochazka WHERE zamestnanec = zamestnanci.id AND now() <= DATE_ADD(concat(datum, ' ', cas), INTERVAL 15 HOUR) ORDER BY datum DESC, cas DESC LIMIT 1) AS dochazka
                         from zamestnanci left join firmy on zamestnanci.firma = firmy.id left join zastavky on zamestnanci.nastup = zastavky.id left join auta on zastavky.auto = auta.id where zamestnanci.firma='" . $_POST['firma'] . "' and zamestnanci.aktivni='" . $_POST['pomer'] . "' and smena " . $smena . " and nepritomnost " . $nepritomnost . " order by " . $sort;
                    }  
                }
                
                //echo $sql;

                if (!($vysledek = mysqli_query($conn, $sql)))
                {
                die("Nelze provést dotaz</body></html>");
                }            

                while ($radek = mysqli_fetch_array($vysledek))
                {   
                    
                    //$dochazka = zjisti_dochazku_agenturnika($radek["id"]);

                    if ($radek["aktivni"] == "1")
                    {

                        if ($radek["zastavka"] == null)
                        {
                            $haystack = "";
                        }
                        else
                        {
                            $haystack = $radek["zastavka"];
                        }
                        
                        $needle   = 'Vlastní auto';
                        
                        if (strpos($haystack, $needle) !== false) 
                        {
                            $barva = "table-dark opacity-50";
                        }
                        else
                        {
                            if ($radek['dochazka'] <> "")
                            {
                                $barva = "table-success";
                            }
                            else
                            {
                                $barva = "";
                            }
                        }  
                    }
                    else
                    {
                        $barva = "table-danger";
                    }  
                    
                    if (($radek["nepritomnost"] == "ABS") or ($radek["nepritomnost"] == "DPN") or ($radek["nepritomnost"] == "OČR"))
                    {
                        $barva = "table-secondary";
                    }
                    elseif ($radek["nepritomnost"] == "DOV")
                    {
                        $barva = "table-warning";
                    }

                    // Předpokládejme, že máte dvě proměnné s daty ve formátu 'Y-m-d'.
                    if ($radek['vstup'] == '0000-00-00')
                    {
                        $date1_str = date('Y-m-d',strtotime('now'));
                    }
                    else
                    {
                        $date1_str = $radek['vstup'];
                    }
                    
                    if ($radek['vystup'] == '0000-00-00')
                    {
                        $date2_str = date('Y-m-d',strtotime('now'));
                    }
                    elseif (date('Y-m-d') > $radek['vystup'])
                    {
                        $date2_str = date('Y-m-d',strtotime($radek['vystup']));
                    }
                    else
                    {
                        $date2_str = date('Y-m-d',strtotime('now'));
                    }                                                        
                    
                    // Vytvoření objektů DateTime pro oba daty.
                    $date1 = new DateTime($date1_str);
                    $date2 = new DateTime($date2_str);
                    
                    // Výpočet rozdílu mezi daty.
                    $interval = $date1->diff($date2);
                    
                    // Získání počtu dnů z intervalu.
                    $odpracovano = $interval->days;   
                    
                    if ($odpracovano <= 90)
                    {   ?>
                            <tr class='<?php echo $barva;?> fw-bold'>
                        <?php
                    }
                    else
                    {   ?>
                            <tr class='<?php echo $barva;?>'>
                        <?php
                    }
                    ?>
                                        
                        <td class='text-center fw-bold'><?php echo $cislo;?></td>

                        <?php
                        if (($_POST['pomer'] == 0) or ($_POST['pomer'] == 33) or ($_POST['pomer'] == 44) or ($_POST['pomer'] == 55))
                        {  
                                                   
                            ?>
                                <td class='text-start'><?php echo $radek["prijmeni"];?></td>
                                <td class='text-start'><?php echo $radek["jmeno"];?></td>
                                <td class='text-center'><?php echo $radek["os_cislo"];?></td>
                                <td class='text-center'><?php echo $radek["os_cislo_klient"];?></td>
                                <td class='text-center'><?php echo $radek["telefon"];?></td>
                                <td class='text-center'>
                                                                    
                                <?php 
                                if ($radek['radneukoncen'] == 'ANO')
                                {   ?>
                                        <a class="btn btn-success opacity-75" role="button">ANO</a>
                                        <a class="btn btn-outline-danger opacity-75" href="zamestnanci.php?typ=filtr&idemp=<?php echo $radek['id'];?>&ukoncen=NE" role="button">NE</a> 
                                    <?php
                                }
                                elseif ($radek['radneukoncen'] == 'NE')
                                {   ?>
                                        <a class="btn btn-outline-success opacity-75" href="zamestnanci.php?typ=filtr&idemp=<?php echo $radek['id'];?>&ukoncen=ANO" role="button">ANO</a>
                                        <a class="btn btn-danger opacity-75" role="button">NE</a> 
                                    <?php
                                }
                                else
                                {   ?>
                                        <a class="btn btn-outline-success opacity-75" href="zamestnanci.php?typ=filtr&idemp=<?php echo $radek['id'];?>&ukoncen=ANO" role="button">ANO</a>
                                        <a class="btn btn-outline-danger opacity-75" href="zamestnanci.php?typ=filtr&idemp=<?php echo $radek['id'];?>&ukoncen=NE" role="button">NE</a>
                                    <?php
                                }
                                ?>
    
                                </td>

                                <td class='text-center'><?php echo $radek["vstup"];?></td>
                                <td class='text-center'><?php echo $radek["vystup"];?></td>
                                <td class='text-center'><?php echo $radek["anulace"];?></td>
                                <td class='text-center'><?php echo $odpracovano;?></td>
                            <?php
                        }
                        else
                        {   ?>
                                <td class='text-start'><?php echo $radek["prijmeni"];?></td>
                                <td class='text-start'><?php echo $radek["jmeno"];?></td>
                                <td class='text-center'><?php echo $radek["os_cislo"];?></td>
                                <td class='text-center'><?php echo $radek["os_cislo_klient"];?></td>
                                <td class='text-start'><?php echo $radek["rfid"];?></td>
                                <td class='text-center'><?php echo $radek["telefon"];?></td>
                                <td class='text-center'><?php echo $radek["vstup"];?></td>
                                <td class='text-center'><?php echo $radek["vystup"];?></td>
                                <td class='text-center'><?php echo $radek["spz"];?></td>
                                <td class='text-start'><?php echo $radek["zastavka"];?></td>
                                <?php 
                                if ($radek["smena2"] == "")
                                {   ?>
                                        <td class='text-center'><?php echo $radek["smena"] . " / -";?></td>
                                    <?php
                                }
                                else
                                {   ?>
                                        <td class='text-center'><?php echo $radek["smena"] . " / " . $radek["smena2"];?></td>
                                    <?php
                                }
                                ?>
                                <td class='text-center'><?php echo $radek["cilova"];?></td>
                                <td class='text-center'><?php echo $radek['dochazka'];?></td>
                            <?php
                        }
        
                        if (($_SESSION['typ'] == 1) or ($_SESSION['typ'] == 4) or ($_SESSION['typ'] == 5))
                        {   ?>
                                <td>
                                    
                                <!-- <a type="button" class="btn btn-outline-primary" href="zamestnanci.php?typ=editzam&id=<?php echo $radek["id"];?>">Edit</button></a> -->
                            
                                <!-- <button type="button" class="btn btn-outline-primary text-center m-2" data-bs-toggle="modal" data-bs-target="#ModalEditZam<?php echo $radek['id'];?>">Edit</button> -->

                                <!-- <button class="btn btn-outline-primary text-center m-2" onclick="loadModalContent('<?php echo $radek['id']; ?>')">EDIT NEW</button> -->

                                <a><button type="button" class="btn btn-sm align-middle bg-light" data-bs-toggle='modal' data-bs-target='#ModalPrihlasitVedoucim<?php echo $radek['id'];?>' onclick="loadModalContent('<?php echo $radek['id']; ?>')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-person-vcard" viewBox="0 0 16 16">
                                    <path d="M5 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4m4-2.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5M9 8a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4A.5.5 0 0 1 9 8m1 2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5"/>
                                    <path d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM1 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H8.96q.04-.245.04-.5C9 10.567 7.21 9 5 9c-2.086 0-3.8 1.398-3.984 3.181A1 1 0 0 1 1 12z"/>
                                    </svg>
                                </button></a>

                                </td>
                            <?php
                        }
                        ?>                    
                    </tr>
                    
                    <?php

                    //edit_zamestnanec_modal($radek['id']);

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

        <h3 class="text-center m-2 p-2">Přehled zaměstnanců</h3>

        <div class="container-fluid">
        <div class="row justify-content-md-center">
        <div class="col col-md-12">

        <?php
        if (($_SESSION['typ'] == 1) or ($_SESSION['typ'] == 3) or ($_SESSION['typ'] == 4) or ($_SESSION['typ'] == 5))
        {   
            
            if (($_SESSION['typ'] == 1) or ($_SESSION['typ'] == 4) or ($_SESSION['typ'] == 5))
            {   ?>
                <div class="d-grid gap-2 d-md-flex justify-content-md-center">                

                    <button type="button" class="btn btn-outline-success text-center m-2" data-bs-toggle="modal" data-bs-target="#ModalNovyZam">Nový zaměstnanec</button>
                    <a class="btn btn-outline-primary text-center m-2" href="zamestnanci.php?typ=zmenasmennosti" role="button">Změna směnnosti<br>(aktuální týden)</a>
                    <a class="btn btn-outline-primary text-center m-2" href="zamestnanci.php?typ=zmenasmennosti2" role="button">Změna směnnosti<br>(následující týden)</a>
                    <a class="btn btn-outline-primary text-center m-2" href="zamestnanci.php?typ=zmenasmennosti3" role="button">Změna směnnosti<br>(následující týden)<br>s formulářem</a>
                                        
                    <button type="button" class="btn btn-outline-success text-center m-2" data-bs-toggle="collapse" href="#filtry" role="button" aria-expanded="true" aria-controls="filtry">Filtry</button>

                </div>  
            
                <?php
            }  
            ?>

                <div class="collapse" id="filtry">

                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <form class="row g-3" action="zamestnanci.php?typ=filtr" method="post" name="zamestnanci">                    

                            <div class="col-md-3">
                                <label for="datepicker">Výběr firmy</label>

                                <select class="form-select mt-2" id="firma" name="firma">

                                    <option value="ALL">Všechny firmy</option>

                                    <?php
                                    global $conn;

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
                                        if (isset($_SESSION["vyberfirma"]))
                                        {
                                            if ($_SESSION["vyberfirma"] == $radek["id"])
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
                                        else
                                        {   ?>
                                                <option value="<?php echo $radek["id"];?>"><?php echo $radek["firma"];?></option>
                                            <?php
                                        }    
                                    }

                                    mysqli_free_result($vysledek);               

                                    ?>

                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="datepicker">Pracovní poměr</label>
                                
                                <select class="form-select mt-2" id="pomer" name="pomer" onChange="change_pomer(document.zamestnanci.pomer.value);">
                                    <option value="1" selected>Aktivní</option>
                                    <option value="0">Neaktivní</option>
                                    <option value="33">Řádně ukončen</option>
                                    <option value="44">Řádně neukončen</option>
                                    <option value="55">Anulován</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="datepicker">Směna</label>

                                <select class="form-select mt-2" id="smena" name="smena">
                            
                                    <option value="R" selected>Ranní</option>
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
                                    <option value="N/A">N/A</option>
                                    <option value="VŠE">Všechno</option>

                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="datepicker">Nepřítomnost</label>

                                <select class="form-select mt-2" id="nepritomnost" name="nepritomnost">
                            
                                    <option value="" selected>Přítomní</option>
                                    <option value="DPN">DPN</option>
                                    <option value="OČR">OČR</option>
                                    <option value="DOV">Dovolená</option>
                                    <option value="ABS">Absence</option>

                                </select>
                            </div>
                                    
                            <div class="col-md-12">
                                <label for="vyber"></label>
                                <button type="submit" class="form-control btn btn-primary">Proveď výběr</button>
                            </div> 

                        </form>
                    </div>

                </div>

            <?php
        }
        ?>  

        <br>
        <div class='table-responsive-lg text-center'>
        <table class='table table-hover'>
        <thead>
            <tr class='table-active'>
                <th scope='col'>ID</th>
                <th scope='col'>Příjmení</th>
                <th scope='col'>Jméno</th>
                <th scope='col'>Os.č.</th>
                <th scope='col'>Os.č.kl.</th>
                <th scope='col'>RFID</th>
                <th scope='col'>Telefon</th>
                <th scope='col'>Vstup</th>
                <th scope='col'>Výstup</th>
                <th scope='col'>Doprava</th>
                <th scope='col'>Nástup</th>
                <th scope='col'>Směna</th>
                <th scope='col'>Cílová st.</th>
                <th scope='col'>Docházka</th>

                <?php
                if (($_SESSION['typ'] == 1) or ($_SESSION['typ'] == 4) or ($_SESSION['typ'] == 5))
                {   ?>
                        <th scope='col'>Editace</th>
                    <?php
                }
                ?>
                
            </tr>
        </thead>
        <tbody>

        <?php
            $cislo = 1;
          
            if (isset($_SESSION["vyberfirma"]))
            {
                if ($_SESSION['typ'] == 5)
                {
                    if ($_SESSION["vyberfirma"] == "ALL")
                    {
                        $sql = "select zamestnanci.id,prijmeni,jmeno,os_cislo,os_cislo_klient,rfid,telefon,adresa,firmy.firma,zastavky.zastavka,zamestnanci.smena,zamestnanci.smena2,auta.spz,zamestnanci.aktivni,nepritomnost,cilova,vstup,vystup,radneukoncen from zamestnanci left join firmy on zamestnanci.firma = firmy.id left join zastavky on zamestnanci.nastup = zastavky.id left join auta on zastavky.auto = auta.id where zamestnanci.aktivni='1' and zamestnanci.firma >= 0 order by prijmeni,zamestnanci.smena";
                    }
                    else
                    {
                        $sql = "select zamestnanci.id,prijmeni,jmeno,os_cislo,os_cislo_klient,rfid,telefon,adresa,firmy.firma,zastavky.zastavka,zamestnanci.smena,zamestnanci.smena2,auta.spz,zamestnanci.aktivni,nepritomnost,cilova,vstup,vystup,radneukoncen from zamestnanci left join firmy on zamestnanci.firma = firmy.id left join zastavky on zamestnanci.nastup = zastavky.id left join auta on zastavky.auto = auta.id where zamestnanci.aktivni='1' and zamestnanci.firma = '" . $_SESSION["vyberfirma"] . "' order by prijmeni,zamestnanci.smena";
                    }
                    
                }
                else
                {
                    if ($_SESSION["vyberfirma"] == "ALL")
                    {
                        $sql = "select zamestnanci.id,prijmeni,jmeno,os_cislo,os_cislo_klient,rfid,telefon,adresa,firmy.firma,zastavky.zastavka,zamestnanci.smena,zamestnanci.smena2,auta.spz,zamestnanci.aktivni,nepritomnost,cilova,vstup,vystup,radneukoncen from zamestnanci left join firmy on zamestnanci.firma = firmy.id left join zastavky on zamestnanci.nastup = zastavky.id left join auta on zastavky.auto = auta.id where zamestnanci.firma in (" . $_SESSION["firma"] . ") and zamestnanci.aktivni='1' order by prijmeni,zamestnanci.smena";
                    }
                    else
                    {
                        $sql = "select zamestnanci.id,prijmeni,jmeno,os_cislo,os_cislo_klient,rfid,telefon,adresa,firmy.firma,zastavky.zastavka,zamestnanci.smena,zamestnanci.smena2,auta.spz,zamestnanci.aktivni,nepritomnost,cilova,vstup,vystup,radneukoncen from zamestnanci left join firmy on zamestnanci.firma = firmy.id left join zastavky on zamestnanci.nastup = zastavky.id left join auta on zastavky.auto = auta.id where zamestnanci.firma='" . $_SESSION["vyberfirma"] . "' and zamestnanci.aktivni='1' order by prijmeni,zamestnanci.smena";
                    }
                }
            }
            else
            {
                if ($_SESSION['typ'] == 5)
                {
                    $sql = "select zamestnanci.id,prijmeni,jmeno,os_cislo,os_cislo_klient,rfid,telefon,adresa,firmy.firma,zastavky.zastavka,zamestnanci.smena,zamestnanci.smena2,auta.spz,zamestnanci.aktivni,nepritomnost,cilova,vstup,vystup,radneukoncen from zamestnanci left join firmy on zamestnanci.firma = firmy.id left join zastavky on zamestnanci.nastup = zastavky.id left join auta on zastavky.auto = auta.id where zamestnanci.aktivni='1' order by prijmeni,zamestnanci.smena";
                }
                else
                {
                    $sql = "select zamestnanci.id,prijmeni,jmeno,os_cislo,os_cislo_klient,rfid,telefon,adresa,firmy.firma,zastavky.zastavka,zamestnanci.smena,zamestnanci.smena2,auta.spz,zamestnanci.aktivni,nepritomnost,cilova,vstup,vystup,radneukoncen from zamestnanci left join firmy on zamestnanci.firma = firmy.id left join zastavky on zamestnanci.nastup = zastavky.id left join auta on zastavky.auto = auta.id where zamestnanci.firma in (" . $_SESSION['firma'] . ") and zamestnanci.aktivni='1' order by prijmeni,zamestnanci.smena";
                }
            }

            //echo $sql;

            if (!($vysledek = mysqli_query($conn, $sql)))
            {
            die("Nelze provést dotaz</body></html>");
            }            

            while ($radek = mysqli_fetch_array($vysledek))
            {   
                
                $dochazka = zjisti_dochazku_agenturnika($radek["id"]);

                if ($radek["aktivni"] == "1")
                {

                    if ($radek["zastavka"] == null)
                    {
                        $haystack = "";
                    }
                    else
                    {
                        $haystack = $radek["zastavka"];
                    }
                    
                    $needle   = 'Vlastní auto';
                    
                    if (strpos($haystack, $needle) !== false) 
                    {
                        //$barva = "table-danger";
                        $barva = "table-dark opacity-50";
                    }
                    else
                    {
                        if ($dochazka <> "")
                        {
                            $barva = "table-success";
                        }
                        else
                        {
                            $barva = "";
                        } 
                    }  
                }
                else
                {
                    $barva = "table-danger";
                }  
                
                if (($radek["nepritomnost"] == "ABS") or ($radek["nepritomnost"] == "DPN"))
                {
                    $barva = "table-secondary";
                }
                elseif ($radek["nepritomnost"] == "DOV")
                {
                    $barva = "table-warning";
                }
                
                ?>
                
                <tr class='<?php echo $barva;?>'>
                    <td class='text-center fw-bold'><?php echo $cislo;?></td>
                    <td class='text-start'><?php echo $radek["prijmeni"];?></td>
                    <td class='text-start'><?php echo $radek["jmeno"];?></td>
                    <td class='text-center'><?php echo $radek["os_cislo"];?></td>
                    <td class='text-center'><?php echo $radek["os_cislo_klient"];?></td>
                    <td class='text-start'><?php echo $radek["rfid"];?></td>
                    <td class='text-center'><?php echo $radek["telefon"];?></td>
                    <td class='text-center'><?php echo prevod_data($radek["vstup"],1);?></td>
                    <td class='text-center'><?php echo prevod_data($radek["vystup"],1);?></td>
                    <td class='text-center'><?php echo $radek["spz"];?></td>
                    <td class='text-start'><?php echo $radek["zastavka"];?></td>

                    <?php 
                    if ($radek["smena2"] == "")
                    {   ?>
                            <td class='text-center'><?php echo $radek["smena"] . " / -";?></td>
                        <?php
                    }
                    else
                    {   ?>
                            <td class='text-center'><?php echo $radek["smena"] . " / " . $radek["smena2"];?></td>
                        <?php
                    }
                    ?>

                    <td class='text-center'><?php echo $radek["cilova"];?></td>
                    <td class='text-center'><?php echo $dochazka;?></td>

                    <?php
                    if (($_SESSION['typ'] == 1) or ($_SESSION['typ'] == 4) or ($_SESSION['typ'] == 5))
                    {   ?>
                            <td>
                                
                            <!-- <a type="button" class="btn btn-outline-primary" href="zamestnanci.php?typ=editzam&id=<?php echo $radek["id"];?>">Edit</button></a> -->
                        
                            <!-- <button type="button" class="btn btn-outline-primary text-center m-2" data-bs-toggle="modal" data-bs-target="#ModalEditZam<?php echo $radek['id'];?>">Edit</button> -->

                            <!-- <button class="btn btn-outline-primary text-center m-2" onclick="loadModalContent('<?php echo $radek['id']; ?>')">EDIT NEW</button> -->

                            <a><button type="button" class="btn btn-sm align-middle bg-light" data-bs-toggle='modal' onclick="loadModalContent('<?php echo $radek['id']; ?>')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-person-vcard" viewBox="0 0 16 16">
                                <path d="M5 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4m4-2.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5M9 8a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4A.5.5 0 0 1 9 8m1 2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5"/>
                                <path d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM1 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H8.96q.04-.245.04-.5C9 10.567 7.21 9 5 9c-2.086 0-3.8 1.398-3.984 3.181A1 1 0 0 1 1 12z"/>
                                </svg>
                            </button></a>

                            </td>
                        <?php
                    }
                    ?>                    
                </tr>
                
                <?php

                //edit_zamestnanec_modal($radek['id']);

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

        novy_zamestnanec_modal();

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
        $('#datepicker2').datepicker({
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
        $('#datepicker3').datepicker({
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

<script>
    function loadModalContent(modalId) {
        var functionName = 'edit_zamestnanec_modal'; // Název funkce, kterou chcete volat
        var ID = modalId; // Získání ID modálního okna z modalId

        $.ajax({
            url: 'funkce.php', // Cesta k externímu skriptu
            type: 'GET',
            data: { functionName: functionName, ID: ID }, // Předání funkce a ID
            success: function(response) {
                $('#modalContent').html(response);
                $('#ModalEditZam').modal('show');
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }
</script>

<div id="modalContent"></div>