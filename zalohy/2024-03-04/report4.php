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
            }
            else
            {
                $mesic = date('n');
                $rok = date('Y');
            }    
            
            if(isset($_POST['cilova']))
            {
                $cilova = $_POST['cilova'];
            }
            else
            {
                $cilova = "BATZ";
            }
           
            ?>

            <h2 class='text-center m-2 p-2 d-print-none'>Docházka pracovníků ve třísměnném provoze</h2>
        
            <!-- <div class="d-none d-print-block">Print Only (Hide on screen only)</div> -->


            <div class="container-fluid">

            <div class="d-grid gap-2 d-md-flex justify-content-md-center d-print-none">
                <form class="row g-3" action="report4.php?typ=filtr" method="post">                        

                    <div class="col-auto">
                        <label for="datepicker">Výběr cílové st.</label>

                        <select class="form-select mt-2" id="cilova" name="cilova">

                            <?php
                            global $conn;

                            $sql = "SELECT DISTINCT(cilova) as cilova FROM zamestnanci where cilova <> '' order by cilova";

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
                            global $conn;

                            $sql = "SELECT left(datum,7) as datumek FROM dochazka group by left(datum,7) order by datumek desc";

                            if (!($vysledek = mysqli_query($conn, $sql)))
                            {
                            die("Nelze provést dotaz</body></html>");
                            }            

                            while ($radek = mysqli_fetch_array($vysledek))
                            {   
                                if (($rok . "-" . $mesic) == ($radek["datumek"]))
                                {
                                    ?>
                                        <option value="<?php echo $radek["datumek"];?>" selected><?php echo $radek["datumek"];?></option>
                                    <?php
                                }
                                else
                                {
                                    ?>
                                        <option value="<?php echo $radek["datumek"];?>"><?php echo $radek["datumek"];?></option>
                                    <?php
                                }           
                            }

                            mysqli_free_result($vysledek);               

                            ?>

                        </select>
                    </div>

                    <input type="hidden" class="form-control" id="hid_mesic" name="hid_mesic" placeholder="" value=<?php echo $rok . "-" . $mesic;?>>
                                    
                    <div class="col-auto">
                        <label for="vyber"></label>
                        <button type="submit" class="form-control btn btn-primary mt-2">Proveď výběr</button>
                    </div> 

                </form>
            </div>
           

            <?php

            $kalendarskySystem = CAL_GREGORIAN;
            $pocetDniVMesici = cal_days_in_month($kalendarskySystem, $mesic, $rok);
            $start_day = $rok . "-" . $mesic . "-01"; 
            $dnu = $pocetDniVMesici-1;

            //$end_day = date('Y-m-d', strtotime($start_day . '+ ' . $dnu . ' days'));
            $end_day = $rok . "-" . $mesic . "-" . $pocetDniVMesici;

            //nactu seznam firem ktere maji nejakou dochazku
            $dotaz = "SELECT DISTINCT(dochazka.zamestnanec),zamestnanci.cilova from dochazka left join firmy on dochazka.firma = firmy.id left join zamestnanci on dochazka.zamestnanec = zamestnanci.id where datum between '" . $start_day . "' and '" . $end_day . "' and zamestnanci.cilova='" . $cilova . "' order by zamestnanci.prijmeni";

            //echo $dotaz;

            if (!($vysledek = mysqli_query($conn, $dotaz)))
            {
            die("Nelze provést dotaz.</body></html>");
            }

            $num_id = array();

            while ($radek = mysqli_fetch_array($vysledek))
            {   
                array_push($num_id, $radek["zamestnanec"]);
                //$firma_nazev = $radek["firma"];
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
            echo "<thead><tr class='horizontal-line-hore'><th scope='col' class='text-center'>Den</th>";

            // prehled datumu prvni radek tabulky
            for ($x = 0; $x <= $dnu; $x++) {
                $datum = date('Y-m-d', strtotime($start_day . '+ '.$x.'days'));
                //echo "<th scope='col' class='text-center'>" . $datum . "</th>";  
                $weekDay = date('N', strtotime($datum));

                if (($weekDay % 7) == 6 or ($weekDay % 7) == 0 )
                {
                    //echo "<th scope='col' class='text-center'>" . date('d.m', strtotime($start_day . '+ '.$x.'days')) . "</th>";  
                    echo "<th scope='col' class='text-center'>" . date('d', strtotime($start_day . '+ '.$x.'days')) . "</th>";  
                }
                else
                {
                    //echo "<th scope='col' class='text-center'>" . date('d.m', strtotime($start_day . '+ '.$x.'days')) . "</th>";  
                    echo "<th scope='col' class='text-center'>" . date('d', strtotime($start_day . '+ '.$x.'days')) . "</th>";  
                }
            }

            echo "</tr>";

            echo "<tr class='horizontal-line-dole'><th scope='col' class='text-center'>Jméno a příjmení</th>";

            // prehled dnu v tydnu
            for ($x = 0; $x <= $dnu; $x++) {
                $datum = date('Y-m-d', strtotime($start_day . '+ '.$x.'days'));

                $weekDay = date('N', strtotime($datum));

                if (($weekDay % 7) == 1)
                {
                $den = "Po";
                }
                elseif (($weekDay % 7) == 2)
                {
                $den = "Út";
                }
                elseif (($weekDay % 7) == 3)
                {
                $den = "St";
                }
                elseif (($weekDay % 7) == 4)
                {
                $den = "Čt";
                }
                elseif (($weekDay % 7) == 5)
                {
                $den = "Pá";
                }
                elseif (($weekDay % 7) == 6)
                {
                $den = "So";
                }
                elseif (($weekDay % 7) == 0)
                {
                $den = "Ne";
                }

                if (($weekDay % 7) == 6 or ($weekDay % 7) == 0 )
                {
                    echo "<th scope='col' class='text-center'>" .  $den . "</th>"; 
                }
                else
                {
                echo "<th scope='col' class='text-center'>" .  $den . "</th>"; 
                }       
                
            }

            echo "</tr></thead>";
            echo "<tbody>";

            $suma = array_fill(0, $dnu+1, 0);
            $suma_nepritomnost = array_fill(0, $dnu+1, 0);

            $celkem_objednavka = 0;

            foreach ($num_id as $value) 
            {
            
                echo "<tr>";
                echo "<td class='table-warning text-start'>" . get_name_from_id_zam($value) . "</td>";
                //echo "<td class='table-warning text-center'>" . get_objednavka($value) . "</td>";
                //echo "<td class='table-warning text-center'></td>";
                
                //$celkem_objednavka = $celkem_objednavka + get_objednavka($value);

                //prectu pole daneho zamestnance a pokud je vikend tak jej ignoruji, pro zbytek zobrazim zda ma ci nema dovolenou
                for ($x = 0; $x <= $dnu; $x++) 
                {

                    $datum = date('Y-m-d', strtotime($start_day . '+ '.$x.'days'));
                    $weekDay = date('N', strtotime($datum));

                    $hodnota = dochazka_zamestnanec_den($value,$datum);

                    if (($weekDay % 7) == 6 or ($weekDay % 7) == 0 )
                    {   ?>
                    
                        <td class='text-center table-warning' id=''><b><?php echo $hodnota;?></b></td>
                        <?php

                        //$suma[$x] = $suma[$x] + $pocet;
                    }
                    else
                    {
                        if (($hodnota == 'DOV') or ($hodnota == 'ABS') or ($hodnota == 'DPN'))
                        {   ?>
                                <td class='text-center table-primary' id=''><b><?php echo $hodnota;?></b></td>
                            <?php
                        }
                        else
                        {   ?>
                                <td class='text-center table-success' id=''><b><?php echo $hodnota;?></b></td>
                            <?php
                        }


                        //$suma[$x] = $suma[$x] + $pocet;
                    }
                }
                    
                echo "</tr>";

            }   
            
            //Dovolená
            ?>
            <tr class='horizontal-line-hore'>
            <td class='table-danger text-end'>Dovolená</td>

            <?php
            //prectu pole daneho zamestnance a pokud je vikend tak jej ignoruji, pro zbytek zobrazim zda ma ci nema dovolenou
            for ($x = 0; $x <= $dnu; $x++) 
            {

                $datum = date('Y-m-d', strtotime($start_day . '+ '.$x.'days'));
                $weekDay = date('N', strtotime($datum));

                $pocet = pocet_zam_nepritomnych_za_den_firma($datum,"DOV",$cilova);
                //echo pocet_zam_nepritomnych_za_den_firma($datum,"DOV",$cilova);

                if (($weekDay % 7) == 6 or ($weekDay % 7) == 0 )
                { 
                    ?>
                        <td class='text-center table-light' id=''><b></b></td>
                    <?php
                }
                else
                {
                    if ($pocet > 0)
                    {
                        ?>
                            <td class='text-center table-primary' id=''><b><?php echo $pocet;?></b></td>
                        <?php

                        $suma_nepritomnost[$x] = $suma_nepritomnost[$x] + $pocet;

                    }
                    else
                    {
                        ?>
                            <td class='text-center table-info' id=''><b></b></td>
                        <?php
                    }
                
                }
            }
                
            echo "</tr>";            
            

            //DPN
            ?>
            <tr>
            <td class='table-danger text-end'>DPN</td>

            <?php
            //prectu pole daneho zamestnance a pokud je vikend tak jej ignoruji, pro zbytek zobrazim zda ma ci nema dovolenou
            for ($x = 0; $x <= $dnu; $x++) 
            {

                $datum = date('Y-m-d', strtotime($start_day . '+ '.$x.'days'));
                $weekDay = date('N', strtotime($datum));

                $pocet = pocet_zam_nepritomnych_za_den_firma($datum,"DPN",$cilova);

                if (($weekDay % 7) == 6 or ($weekDay % 7) == 0 )
                { 
                    ?>
                        <td class='text-center table-light' id=''><b></b></td>
                    <?php
                }
                else
                {
                    if ($pocet > 0)
                    {
                        ?>
                            <td class='text-center table-primary' id=''><b><?php echo $pocet;?></b></td>
                        <?php

                        $suma_nepritomnost[$x] = $suma_nepritomnost[$x] + $pocet;

                    }
                    else
                    {
                        ?>
                            <td class='text-center table-info' id=''><b></b></td>
                        <?php
                    }
                
                }
            }
                
            echo "</tr>";            
            

            //Absence
            ?>
            <tr>
            <td class='table-danger text-end'>Absence</td>

            <?php
            //prectu pole daneho zamestnance a pokud je vikend tak jej ignoruji, pro zbytek zobrazim zda ma ci nema dovolenou
            for ($x = 0; $x <= $dnu; $x++) 
            {

                $datum = date('Y-m-d', strtotime($start_day . '+ '.$x.'days'));
                $weekDay = date('N', strtotime($datum));

                $pocet = pocet_zam_nepritomnych_za_den_firma($datum,"ABS",$cilova);

                if (($weekDay % 7) == 6 or ($weekDay % 7) == 0 )
                { 
                    ?>
                        <td class='text-center table-light' id=''><b></b></td>
                    <?php
                }
                else
                {
                    if ($pocet > 0)
                    {
                        ?>
                            <td class='text-center table-primary' id=''><b><?php echo $pocet;?></b></td>
                        <?php

                        $suma_nepritomnost[$x] = $suma_nepritomnost[$x] + $pocet;

                    }
                    else
                    {
                        ?>
                            <td class='text-center table-info' id=''><b></b></td>
                        <?php
                    }
                
                }
            }
                
            echo "</tr>";            
            ?>

            </tbody>
            </table>
            </div>

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

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>