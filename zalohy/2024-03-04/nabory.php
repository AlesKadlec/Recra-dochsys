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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
    <title>NÁBORY</title>
</head>
<body>

<?php

global $conn;

menu();

if (kontrola_prihlaseni() == "OK")
{   
    if (isset($_GET["typ"]))
    {
        if (($_GET["typ"] == "savenabor" and $_SESSION["typ"] == "1") or ($_GET["typ"] == "savenabor" and $_SESSION["typ"] == "4") or ($_GET["typ"] == "savenabor" and $_SESSION["typ"] == "5") or ($_GET["typ"] == "savenabor" and $_SESSION["typ"] == "6"))
        {
            $date1=date_create($_POST["dat_narozeni"]); //narozen
            $date2=date_create($_POST["dat_evidence"]); //dat evidence
            
            if ($_POST["dat_nastup"] == '')
            {
                $date3=date_create('0000-00-00'); //nastup
            }
            else
            {
                $date3=date_create($_POST["dat_nastup"]); //nastup
            }

            if ($_POST["dat_vystup"] == '')
            {
                $date4=date_create('0000-00-00'); //vystup
            }
            else
            {
                $date4=date_create($_POST["dat_vystup"]); //vystup
            }

            $sql= "insert into nabory (prijmeni,jmeno,telefon,adresa,dat_narozeni,stat,dat_evidence,zdroj_inzerce,pozice,klient,klient2,souhlas,rekruter,vysledek,nastup,vystup,koordinator,duvod_ukonceni,poznamka) values ('" . $_POST["prijmeni"] . "','" . $_POST["jmeno"] . "','" . $_POST["telefon"] . "','" . $_POST["adresa"] . "','" . date_format($date1,"Y-m-d") . "','" . $_POST["stat"] . "','" . date_format($date2,"Y-m-d") . "','" . $_POST["zdroj"] . "','" . $_POST["pozice"] . "','" . $_POST["klient"] . "','" . $_POST["klient2"] . "','" . $_POST["souhlas"] . "','" . $_POST["rekruter"] . "','" . $_POST["vysledek"] . "','" . date_format($date3,"Y-m-d") . "','" . date_format($date4,"Y-m-d") . "','" . $_POST["koordinator"] . "','" . $_POST["duvod_ukonceni"] . "','" . $_POST["poznamka"] . "')"; 

            if (!($vysledek = mysqli_query($conn, $sql)))
            {
            die("Nelze provést dotaz.</body></html>");
            }           

            //zaznam do logu
            $now = new DateTime('now', new DateTimeZone('Europe/Prague'));
               
            if (!($vysledek = mysqli_query($conn, 
            "insert into logs (kdo,typ,infotext,datumcas) values ('" . $_SESSION["log_id"] . "','Nový nábor','Vytvořen nový nábor " . $_POST["prijmeni"] . " " . $_POST["jmeno"] . " - " . date_format($date2,"Y-m-d") . "','" . $now->format('Y-m-d H:i:s') . "')")))
            {
            die("Nelze provést dotaz.</body></html>");
            }
            ?>
       
            <meta http-equiv="refresh" content="0;url=nabory.php">

        <?php
        }
        elseif (($_GET["typ"] == "updatenabor" and $_SESSION["typ"] == "1") or ($_GET["typ"] == "updatenabor" and $_SESSION["typ"] == "4") or ($_GET["typ"] == "updatenabor" and $_SESSION["typ"] == "5"))
        {
            $date1=date_create($_POST["dat_narozeni"]); //narozen
            $date2=date_create($_POST["dat_evidence"]); //dat evidence

            if ($_POST["dat_nastup"] == '')
            {
                $date3=date_create('0000-00-00'); //nastup
            }
            else
            {
                $date3=date_create($_POST["dat_nastup"]); //nastup
            }

            if ($_POST["dat_vystup"] == '')
            {
                $date4=date_create('0000-00-00'); //vystup
            }
            else
            {
                $date4=date_create($_POST["dat_vystup"]); //vystup
            }

            $sql= "update nabory set prijmeni = '" . $_POST["prijmeni"] . "',jmeno='" . $_POST["jmeno"] . "',telefon='" . $_POST["telefon"] . "',adresa='" . $_POST["adresa"] . "',dat_narozeni='" . date_format($date1,"Y-m-d") . "',stat='" . $_POST["stat"] . "',dat_evidence='" . date_format($date2,"Y-m-d") . "',zdroj_inzerce='" . $_POST["zdroj"] . "',pozice='" . $_POST["pozice"] . "',klient='" . $_POST["klient"] . "',klient2='" . $_POST["klient2"] . "',souhlas='" . $_POST["souhlas"] . "',rekruter='" . $_POST["rekruter"] . "',vysledek='" . $_POST["vysledek"] . "',nastup='" . date_format($date3,"Y-m-d") . "',vystup='" . date_format($date4,"Y-m-d") . "',koordinator='" . $_POST["koordinator"] . "',duvod_ukonceni='" . $_POST["duvod_ukonceni"] . "',poznamka='" . $_POST["poznamka"] . "' where id='" . $_POST["radek_v_db"] . "'"; 
            
            if (!($vysledek = mysqli_query($conn, $sql)))
            {
            die("Nelze provést dotaz.</body></html>");
            }        
            
            //zaznam do logu
            $now = new DateTime('now', new DateTimeZone('Europe/Prague'));
               
            if (!($vysledek = mysqli_query($conn, 
            "insert into logs (kdo,typ,infotext,datumcas) values ('" . $_SESSION["log_id"] . "','Aktualizován nábor','Nábor upraven u " . $_POST["prijmeni"] . " " . $_POST["jmeno"] . " - " . date_format($date2,"Y-m-d") . "','" . $now->format('Y-m-d H:i:s') . "')")))
            {
            die("Nelze provést dotaz.</body></html>");
            }
            ?>
       
            <meta http-equiv="refresh" content="0;url=nabory.php">

        <?php
        }
    }
    else
    {
    
    ?>

        <h3 class="text-center m-2 p-2">Nábory</h3>

        <div class="container-fluid">
            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                <div class="col-auto">
                    <label for="vyber"></label>
                    <button type="button" class="form-control btn btn-success mt-2" data-bs-toggle='modal' data-bs-target='#nabor_new'>Nový záznam</button>
                </div> 
            </div> 
        </div> 

        <div class="container-fluid">
      

        <div class="row justify-content-md-center">
        <div class="col col-md-12">
 
        <br>
        <div class='table-responsive-lg text-center'>
        <table class='table table-hover'>
        <thead>
            <tr class='table-active'>
                <th scope='col'>#</th>
                <th scope='col'></th>
                <th scope='col' class='text-start'>Příjmení</th>
                <th scope='col' class='text-start'>Jméno</th>
                <th scope='col'>Telefon</th>
                <th scope='col'>Datum<br>evidence</th>
                <th scope='col'>Vstup</th>
                <th scope='col'>Výstup</th>
                <th scope='col'>Klient</th>
                <th scope='col'>Rekrutér</th>
                <th scope='col'>Výsledek</th>
                <th scope='col'>Poznámka</th>
                <th scope='col'></th>
            </tr>
        </thead>
        <tbody>

        <?php  
            $cislo = 1;          
        
            $sql = "select id,prijmeni,jmeno,telefon,adresa,dat_narozeni,stat,dat_evidence,zdroj_inzerce,pozice,klient,klient2,souhlas,rekruter,vysledek,nastup,vystup,koordinator,poznamka from nabory order by dat_evidence";
           

            if (!($vysledek = mysqli_query($conn, $sql)))
            {
            die("Nelze provést dotaz</body></html>");
            }            

            while ($radek = mysqli_fetch_array($vysledek))
            {                                        
                ?>
                
                <tr>
                    <td class='text-center fw-bold'><?php echo $cislo;?></td>
                    <td class='text-start' width="50"><img src="vlajky/<?php echo strtolower($radek["stat"]);?>.png" class="img-thumbnail" width="50"></td>
                    <td class='text-start'><?php echo $radek["prijmeni"];?></td>
                    <td class='text-start'><?php echo $radek["jmeno"];?></td>
                    <td class='text-start'><?php echo $radek["telefon"];?></td>
                    <td class='text-center'><?php echo $radek["dat_evidence"];?></td>
                    <td class='text-center'><?php echo $radek["nastup"];?></td>
                    <td class='text-center'><?php echo $radek["vystup"];?></td>
                    <td class='text-center'><?php echo $radek["klient"];?><?php echo ($radek['klient2'] <> '') ? " / " . $radek['klient2'] : "";?>
                </td>
                    <td class='text-center'><?php echo $radek["rekruter"];?></td>
                    <td class='text-center'><?php echo $radek["vysledek"];?></td>
                    <td class='text-center'><?php echo $radek["poznamka"];?></td>
                    <td class='text-start' width="50"><button type="button" class="form-control btn btn-sm btn-success mt-2" data-bs-toggle='modal' data-bs-target='#ModalNaborInfo<?php echo $radek['id'];?>'>EDIT</button></td>

                </tr>
                
                <?php
                $cislo ++;

                novy_nabor($radek['id']);
        
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
        $('.common-datepicker1').datepicker({
            format: 'dd.mm.yyyy',
            autoclose: true,
            todayHighlight: true,
            language: 'cs',
            allowInputToggle: false,
            startDate: '01.01.1930', // Nastavte výchozí hodnotu dle potřeby
            endDate: '31.12.2020'    // Nastavte výchozí hodnotu dle potřeby
        });
    });
</script>

<script>
    $(document).ready(function(){
        $('.common-datepicker2').datepicker({
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
        $('.common-datepicker3').datepicker({
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
        $('.common-datepicker4').datepicker({
            format: 'dd.mm.yyyy',
            autoclose: true,
            todayHighlight: true,
            language: 'cs',
            allowInputToggle: false
        });
    });
</script>

<?php novy_nabor();?>