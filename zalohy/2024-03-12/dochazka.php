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
    <title>DOCHÁZKA</title>
</head>
<body>

<?php

global $conn;

menu();

if (kontrola_prihlaseni() == "OK")
{   
    if (isset($_GET["typ"]))
    {
        if ($_GET["typ"] == "filtr")
        {   
            $datum = date_format(date_create($_POST["datepicker"]),'Y-m-d');
            $smena = $_POST["smena"];
            ?>
            
            <h3 class="text-center m-2 p-2">Zobrazení docházky dle filtru:
            <?php echo $datum;?> a směna: <?php echo $smena;?></h3>
            
            <?php

            $html = "<h1 class='text-center m-2 p-2'>DOCHÁZKA ZA DEN " . $datum . " SMĚNA: " . $smena . "</h1>";

            $html .= "<div class='container-fluid'>";
            $html .= "<div class='row justify-content-md-center'>";
            $html .= "<div class='col col-md-12'>";
    
            $html .= "<br>";
            $html .= "<div class='table-responsive-lg text-center'>";
            $html .= "<table style='border: 3px solid green'>";
            ?>

            <div class='container-fluid'>
            <div class='row justify-content-md-center'>
            <div class='col col-md-12'>
    
            <br>
            <div class='table-responsive-lg text-center'>
            <table class='table table-hover'>          

            <?php
            $html .= "<thead>";   
            $html .= "<tr style='border-collapse: collapse'>";
            $html .= "<th scope='col' style='background-color: #98AFC7'>#</th>";
            $html .= "<th scope='col' style='background-color: #98AFC7'>Příjmení</th>";
            $html .= "<th scope='col' style='background-color: #98AFC7'>Jméno</th>";
            $html .= "<th scope='col' style='background-color: #98AFC7'>Os. číslo</th>";
            $html .= "<th scope='col' style='background-color: #98AFC7'>Datum</th>";
            $html .= "<th scope='col' style='background-color: #98AFC7'>Čas</th>";
            $html .= "<th scope='col' style='background-color: #98AFC7'>Směna</th>";
            $html .= "<th scope='col' style='background-color: #98AFC7'>Bus</th>";
            $html .= "<th scope='col' style='background-color: #98AFC7'>Zastávka</th>";
            $html .= "<th scope='col' style='background-color: #98AFC7'>Cílová</th>";
            $html .= "</tr>";
            $html .= "</thead>";
            $html .= "<tbody>";
            ?>

                <thead>
                <tr class='table-active'>
                <th scope='col'>#</th>
                <th scope='col'>Příjmení</th>
                <th scope='col'>Jméno</th>
                <th scope='col'>Os. číslo</th>
                <th scope='col'>Datum</th>
                <th scope='col'>Čas</th>
                <th scope='col'>Směna</th>
                <th scope='col'>Bus</th>
                <th scope='col'>Zastávka</th>
                <th scope='col'>Firma</th>
                </tr>
                </thead>
                <tbody>

            <?php
                         
                $cislo = 1;
                if ($_SESSION["typ"] <> "5")
                {
                    if ($smena == "ALL")
                    {
                        $sql = "select dochazka.id,zamestnanci.prijmeni,zamestnanci.jmeno,zamestnanci.os_cislo,datum,cas,dochazka.smena,auta.spz,zastavky.zastavka,firmy.firma,cilova from dochazka left join zamestnanci on dochazka.zamestnanec = zamestnanci.id left join auta on dochazka.bus=auta.id left join zastavky on dochazka.zastavka = zastavky.id left join firmy on dochazka.firma = firmy.id where datum='" . $datum . "' and dochazka.firma in (" . $_SESSION['firma'] . ") order by datum desc,cas desc limit 1000";
                    }
                    else
                    {
                        $sql = "select dochazka.id,zamestnanci.prijmeni,zamestnanci.jmeno,zamestnanci.os_cislo,datum,cas,dochazka.smena,auta.spz,zastavky.zastavka,firmy.firma,cilova from dochazka left join zamestnanci on dochazka.zamestnanec = zamestnanci.id left join auta on dochazka.bus=auta.id left join zastavky on dochazka.zastavka = zastavky.id left join firmy on dochazka.firma = firmy.id where dochazka.smena='" . $smena . "' and datum='" . $datum . "' and dochazka.firma in (" . $_SESSION['firma'] . ") order by datum desc,cas desc limit 1000";
                    }

                }
                else
                {
                    if ($smena == "ALL")
                    {
                        $sql = "select dochazka.id,zamestnanci.prijmeni,zamestnanci.jmeno,zamestnanci.os_cislo,datum,cas,dochazka.smena,auta.spz,zastavky.zastavka,firmy.firma,cilova from dochazka left join zamestnanci on dochazka.zamestnanec = zamestnanci.id left join auta on dochazka.bus=auta.id left join zastavky on dochazka.zastavka = zastavky.id left join firmy on dochazka.firma = firmy.id where datum='" . $datum . "' order by datum desc,cas desc limit 1000";
                    }
                    else
                    {
                        $sql = "select dochazka.id,zamestnanci.prijmeni,zamestnanci.jmeno,zamestnanci.os_cislo,datum,cas,dochazka.smena,auta.spz,zastavky.zastavka,firmy.firma,cilova from dochazka left join zamestnanci on dochazka.zamestnanec = zamestnanci.id left join auta on dochazka.bus=auta.id left join zastavky on dochazka.zastavka = zastavky.id left join firmy on dochazka.firma = firmy.id where dochazka.smena='" . $smena . "' and datum='" . $datum . "' order by datum desc,cas desc limit 1000";
                    }
                }

                //echo $sql;

                if (!($vysledek = mysqli_query($conn, $sql)))
                {
                die("Nelze provést dotaz</body></html>");
                }

                $num = 1;
                $barva = "D1D0CE";

                while ($radek = mysqli_fetch_array($vysledek))
                {        
                    if ($num == 1)
                    {
                        $barva = "D1D0CE";
                        $num = 2;
                    }
                    else
                    {
                        $barva = "E5E4E2";
                        $num = 1;
                    }                                
                                        
                    $html .= "<tr style = 'background-color: #" . $barva . "'>";      
                    $html .= "<td class='text-center'>" . $cislo . "</td>";
                    $html .= "<td class='text-start'>" . $radek["prijmeni"] . "</td>";
                    $html .= "<td class='text-start'>" . $radek["jmeno"] . "</td>";
                    $html .= "<td class='text-center'>" . $radek["os_cislo"] . "</td>";
                    $html .= "<td class='text-center'>" . $radek["datum"] . "</td>";
                    $html .= "<td class='text-center'>" . $radek["cas"] . "</td>";
                    $html .= "<td class='text-center'>" . $radek["smena"] . "</td>";
                    $html .= "<td class='text-center'>" . $radek["spz"] . "</td>";
                    $html .= "<td class='text-start'>" . $radek["zastavka"] . "</td>";
                    $html .= "<td class='text-start'>" . $radek["cilova"] . "</td>";
                    $html .= "</tr>";
                    ?>

                    <tr>
                    <td class='text-center'><?php echo $cislo;?></td>
                    <td class='text-start'><?php echo $radek["prijmeni"];?></td>
                    <td class='text-start'><?php echo $radek["jmeno"];?></td>
                    <td class='text-center'><?php echo $radek["os_cislo"];?></td>
                    <td class='text-center'><?php echo $radek["datum"];?></td>
                    <td class='text-center'><?php echo $radek["cas"];?></td>
                    <td class='text-center'><?php echo $radek["smena"];?></td>
                    <td class='text-center'><?php echo $radek["spz"];?></td>
                    <td class='text-start'><?php echo $radek["zastavka"];?></td>
                    <td class='text-start'><?php echo $radek["cilova"];?></td>
                    </tr>
                    
                    <?php
                    $cislo ++;
                }
                
                mysqli_free_result($vysledek);
            
                $html .= "</tbody>";
                $html .= "</table>";
                $html .= "</div>";

                $html .= "</div>";
                $html .= "</div>";
                $html .= "</div>";
                ?>

                </tbody>
                </table>
                </div>

                </div>
                </div>
                </div>
                
                <?php
                //echo $html;
                     
            if (isset($_POST["mailem"]) and $_POST["mailem"] == "1")
            {
                $headers  = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n"; 
                $predmet = "Přehled docházky";
                
                $html = wrapMailMessage($html);
    
                mail($_SESSION["email"], $predmet, $html, $headers);
            }
        }
        elseif (($_GET["typ"] == "insertattandance" and $_SESSION["typ"] == "1") or ($_GET["typ"] == "insertattandance" and $_SESSION["typ"] == "4") or ($_GET["typ"] == "insertattandance" and $_SESSION["typ"] == "5"))
        {
            $id_zaznamu = 0;
            $sql = "select id from dochazka where zamestnanec='" . $_POST['jmeno'] . "' and datum='" . $_POST['datum'] . "'";    

            if (!($vysledek = mysqli_query($conn, $sql)))
            {
            die("Nelze provést dotaz</body></html>");
            }

            while ($radek = mysqli_fetch_array($vysledek))
            {
                $id_zaznamu = $radek['id'];
            }

            //echo "<br>";
            //echo "<br>";
            //echo $sql;

            mysqli_free_result($vysledek);

            if ($id_zaznamu == 0)
            {
                $smena = get_shift_from_id_zam($_POST['jmeno']);
                $zastavka = get_info_from_zamestnanci_table($_POST['jmeno'],'nastup');
                $firma = get_info_from_zamestnanci_table($_POST['jmeno'],'firma');
                $bus = get_bus_from_zastavky($zastavka);
                $cas_nastupu = get_time_nastupu($zastavka,$smena);
                
                insert_attandance_manually($_POST['jmeno'],$bus,$zastavka,$firma,$smena,$_POST['datum'],$cas_nastupu);

                //zaznam do logu
                $now = new DateTime('now', new DateTimeZone('Europe/Prague'));
        
                if (!($vysledek = mysqli_query($conn, 
                "insert into logs (kdo,typ,infotext,datumcas) values ('" . $_SESSION["log_id"] . "','Manuálně vložena docházka','Záznam pro " . get_name_from_id_zam($_POST["jmeno"])  . " - " . $_POST['datum'] . " " . $cas_nastupu . "','" . $now->format('Y-m-d H:i:s') . "')")))
                {
                die("Nelze provést dotaz.</body></html>");
                }

                ?>
                    <div class="container mt-5">
                        <h2 class='text-center text-danger m-2 p-2 bg-success p-2 text-dark bg-opacity-50'>Docházkový záznam úspěšně vložen !</h2>
                    </div>

                    <h4 class="text-center m-2 p-2">Za 5 vteřin budete přesměrování zpět na docházku</h4>

                    <meta http-equiv="refresh" content="5;url=dochazka.php">

                <?php

            }
            else
            {   ?>
                   <div class="container mt-5">
                        <h2 class='text-center text-danger m-2 p-2 bg-danger p-2 text-dark bg-opacity-50'>Docházka nebyla vložena, zvolená osoba již záznam pro tento den má !</h2>
                    </div>

                    <h4 class="text-center m-2 p-2">Za 5 vteřin budete přesměrování zpět na docházku</h4>

                    <meta http-equiv="refresh" content="5;url=dochazka.php">
                <?php
            }     

        }
        elseif (($_GET["typ"] == "editattandance" and $_SESSION["typ"] == "1") or ($_GET["typ"] == "editattandance" and $_SESSION["typ"] == "4") or ($_GET["typ"] == "editattandance" and $_SESSION["typ"] == "5"))
        {
            $radek_v_db = $_POST['radek_v_db'];
            $dochazka = $_POST['dochazka'];
            $datumcas = $_POST['datumcas'];
 
            global $conn;

            $now = new DateTime('now', new DateTimeZone('Europe/Prague'));
            
            if ($dochazka == 'SMAZ')
            {
                $dotaz = "delete from dochazka where id='" . $radek_v_db . "'";
            }
            else
            {
                $dotaz = "update dochazka set nepritomnost='" . $dochazka . "' where id='" . $radek_v_db . "'";
            }           
               
            if (!($vysledek = mysqli_query($conn, $dotaz)))
            {
            die("Nelze provést dotaz.</body></html>");
            }
            
            ?>
            <div class="container mt-5">
                <?php
                if ($dochazka == 'SMAZ')
                {   ?>
                        <h2 class='text-center text-danger m-2 p-2 bg-danger p-2 text-dark bg-opacity-50'>Docházkový záznam byl smazán !</h2>
                    <?php

                    //zaznam do logu
                    $now = new DateTime('now', new DateTimeZone('Europe/Prague'));
            
                    if (!($vysledek = mysqli_query($conn, 
                    "insert into logs (kdo,typ,infotext,datumcas) values ('" . $_SESSION["log_id"] . "','Smazána docházka','Záznam " . get_name_from_id_zam($_POST["id_name"])  . $datumcas . " byl smazán','" . $now->format('Y-m-d H:i:s') . "')")))
                    {
                    die("Nelze provést dotaz.</body></html>");
                    }

                }
                else
                {   ?>
                        <h2 class='text-center text-danger m-2 p-2 bg-success p-2 text-dark bg-opacity-50'>Docházkový záznam úspěšně upraven !</h2>
                    <?php

                    //zaznam do logu
                    $now = new DateTime('now', new DateTimeZone('Europe/Prague'));

                    if (!($vysledek = mysqli_query($conn, 
                    "insert into logs (kdo,typ,infotext,datumcas) values ('" . $_SESSION["log_id"] . "','Editována docházka','Záznam od " . get_name_from_id_zam($_POST["id_name"])  . " - " . $datumcas . " byl editován na " . $dochazka . "','" . $now->format('Y-m-d H:i:s') . "')")))
                    {
                    die("Nelze provést dotaz.</body></html>");
                    }
                }
                ?>
            </div>

            <h4 class="text-center m-2 p-2">Za 5 vteřin budete přesměrování zpět na docházku</h4>

            <meta http-equiv="refresh" content="5;url=dochazka.php">

            <?php           
   
        }
    }
    else
    {   ?>

        <h3 class="text-center m-2 p-2">Přehled docházky</h3>

        <div class="container-fluid">

        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
            <form class="row g-3" action="dochazka.php?typ=filtr" method="post">
                <div class="col-auto">
                    <label for="datepicker">Výběr dne</label>
                    <input type="text" class="form-control mt-2" id="datepicker" name="datepicker"  placeholder="Vyber datum" required>
                </div>

                <div class="col-auto">
                    <label for="datepicker">Výběr směny</label>

                    <select class="form-select mt-2" id="smena" name="smena">
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
                        <option value="ALL" selected>Všechny směny</option>
                    </select>
                </div>

                <div class="col-auto">
                    <label for="datepicker">Poslat na mail</label>
                    <div class="form-check mt-2">
                        <input class="form-check-input" value = "1" type="checkbox" id="mailem" name="mailem">
                    </div>
                </div>
                                
                <div class="col-auto">
                    <label for="vyber"></label>
                    <button type="submit" class="form-control btn btn-primary mt-2">Proveď výběr</button>
                </div> 

                <?php
                if (($_SESSION["typ"] == "1") or ($_SESSION["typ"] == "4") or ($_SESSION["typ"] == "5"))
                {   ?>
                        <div class="col-auto">
                            <label for="vyber"></label>
                            <button type="button" class="form-control btn btn-success mt-2" data-bs-toggle='modal' data-bs-target='#exampleModal1'>Vložení docházky</button>
                        </div> 
                    <?php
                }
                ?>              
            </form>
        </div>

        <div class="row justify-content-md-center">
        <div class="col col-md-12">
 
        <br>
        <div class='table-responsive-lg text-center'>
        <table class='table table-hover'>
        <thead>
            <tr class='table-active'>
                <th scope='col'>#</th>
                <th scope='col'>Příjmení</th>
                <th scope='col'>Jméno</th>
                <th scope='col'>Os. číslo</th>
                <th scope='col'>Datum</th>
                <th scope='col'>Čas</th>
                <th scope='col'>Směna</th>
                <th scope='col'>Bus</th>
                <th scope='col'>Zastávka</th>
                <th scope='col'>Cílová</th>
                <th scope='col'>Nepřítomnost</th>
                
                <?php
                if (($_SESSION["typ"] == "1") or ($_SESSION["typ"] == "4") or ($_SESSION["typ"] == "5"))
                {   ?>

                    <th scope='col'>Edit</th>


                    <?php
                }
                ?>

            </tr>
        </thead>
        <tbody>

        <?php  
            $cislo = 1;          
        
            if ($_SESSION["typ"] == "5")
            {
                $sql = "select dochazka.id,zamestnanci.prijmeni,zamestnanci.jmeno,zamestnanci.os_cislo,datum,cas,dochazka.smena,auta.spz,zastavky.zastavka,firmy.firma,cilova,dochazka.nepritomnost from dochazka left join zamestnanci on dochazka.zamestnanec = zamestnanci.id left join auta on dochazka.bus=auta.id left join zastavky on dochazka.zastavka = zastavky.id left join firmy on dochazka.firma = firmy.id order by datum desc,cas desc limit 1000";
            }
            else
            {
                $sql = "select dochazka.id,zamestnanci.prijmeni,zamestnanci.jmeno,zamestnanci.os_cislo,datum,cas,dochazka.smena,auta.spz,zastavky.zastavka,firmy.firma,cilova,dochazka.nepritomnost from dochazka left join zamestnanci on dochazka.zamestnanec = zamestnanci.id left join auta on dochazka.bus=auta.id left join zastavky on dochazka.zastavka = zastavky.id left join firmy on dochazka.firma = firmy.id where dochazka.firma in(" . $_SESSION['firma'] . ") order by datum desc,cas desc limit 1000";
            }           

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
                    <td class='text-center'><?php echo $radek["os_cislo"];?></td>
                    <td class='text-center'><?php echo prevod_data($radek["datum"],1);?></td>
                    <td class='text-center'><?php echo $radek["cas"];?></td>
                    <td class='text-center'><?php echo $radek["smena"];?></td>
                    <td class='text-center'><?php echo $radek["spz"];?></td>
                    <td class='text-start'><?php echo $radek["zastavka"];?></td>
                    <td class='text-start'><?php echo $radek["cilova"];?></td>
                    <td class='text-start'><?php echo $radek["nepritomnost"];?></td>

                    <?php
                    if (($_SESSION["typ"] == "1") or ($_SESSION["typ"] == "4") or ($_SESSION["typ"] == "5"))
                    {   ?>
                            <td class='text-start'><button type="button" class="form-control btn btn-sm btn-success mt-2" data-bs-toggle='modal' data-bs-target='#editModal<?php echo $radek['id'];?>'>EDIT</button></td>
                        <?php
                            edit_dochazky($radek['id']);
                    }
                    ?>
                      
                </tr>
                
                <?php
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
        $('#datepicker').datepicker({
            format: 'dd.mm.yyyy',
            autoclose: true,
            todayHighlight: true,
            language: 'cs',
            allowInputToggle: false
        });
    });
</script>


<?php modal_vlozeni_dochazky();?>

