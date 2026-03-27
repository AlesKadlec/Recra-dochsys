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
    <title>RECRA - agenturní zaměstnávání - měsíční předpoklad tržeb (v tisících Kč bez DPH)</title>
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
        if ($_SESSION["typ"] == "5")
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

            if(isset($_POST['hid_mesic']) and isset($_POST['fakturace'])) 
            {
                if ($_POST["hid_mesic"] == ($rok . "-" . $mesic))
                {
                    if ($_POST["hid_fakturace"] == 0)
                    {
                        insert_castka_fakturace($rok . "-" . $mesic,$_POST["fakturace"]);
                    }                    
                    elseif ($_POST["hid_fakturace"] <> $_POST["fakturace"])
                    {
                        update_castka_fakturace($rok . "-" . $mesic,$_POST["fakturace"]); 
                    }
                    else
                    {
                        //nic
                    }
                }
            }

            $fakturace = get_castka_fakturace($rok . "-" . $mesic);
            ?>

            <h2 class='text-center m-2 p-2 d-print-none'>RECRA - agenturní zaměstnávání - měsíční předpoklad tržeb (v tisících Kč bez DPH)</h2>
         
            <h4 class='text-center m-2 p-2 d-print-none'>Denní obsazenost zaměstnanců na odpracovaných 7,5 hodin v systému N-O-R (noční,odpolední,ranní)</h4>     

            <!-- <div class="d-none d-print-block">Print Only (Hide on screen only)</div> -->


            <div class="container-fluid">

            <div class="d-grid gap-2 d-md-flex justify-content-md-center d-print-none">
                <form class="row g-3" action="report.php?typ=filtr" method="post">              
        
                    <div class="col-auto">
                        <label for="text">Fakturace v Kč/hod</label>
                        <input type="text" class="form-control mt-2" id="fakturace" name="fakturace" placeholder="" value="<?php echo get_castka_fakturace($rok . "-" . $mesic);?>">
                   
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
                    <input type="hidden" class="form-control" id="hid_fakturace" name="hid_fakturace" placeholder="" value=<?php echo $fakturace;?>>
                                    
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
            $dotaz = "SELECT DISTINCT(dochazka.firma) as firma_id,firmy.firma from dochazka left join firmy on dochazka.firma = firmy.id where datum between '" . $start_day . "' and '" . $end_day . "' order by firmy.firma";

            //echo $dotaz;

            if (!($vysledek = mysqli_query($conn, $dotaz)))
            {
            die("Nelze provést dotaz.</body></html>");
            }

            $num_id = array();

            while ($radek = mysqli_fetch_array($vysledek))
            {   
                array_push($num_id, $radek["firma_id"]);
                $firma_nazev = $radek["firma"];
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
            echo "<thead><tr class='horizontal-line-hore'><th scope='col' class='text-center'>Den</th><th></th>";

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

            echo "<tr class='horizontal-line-dole'><th scope='col' class='text-center'>Název zakázky</th><th>Obj.</th>";

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
                echo "<td class='table-warning text-start'>" . get_firma_from_id($value) . "</td>";
                echo "<td class='table-warning text-center'>" . get_objednavka($value) . "</td>";
                
                $celkem_objednavka = $celkem_objednavka + get_objednavka($value);

                //prectu pole daneho zamestnance a pokud je vikend tak jej ignoruji, pro zbytek zobrazim zda ma ci nema dovolenou
                for ($x = 0; $x <= $dnu; $x++) 
                {

                    $datum = date('Y-m-d', strtotime($start_day . '+ '.$x.'days'));
                    $weekDay = date('N', strtotime($datum));

                    $pocet = pocet_zam_firma_den($value,$datum);

                    if (($weekDay % 7) == 6 or ($weekDay % 7) == 0 )
                    { 
                        if ($pocet > 0)
                        {   ?>
                                <td class='text-center table-warning' id=''><b><?php echo $pocet;?></b></td>
                            <?php

                            $suma[$x] = $suma[$x] + $pocet;
                        }
                        else
                        {   ?>
                                <td class='text-center table-light' id=''><b></b></td>
                            <?php
                        }
                    }
                    else
                    {
                        if ($pocet > 0)
                        {
                            ?>
                                <td class='text-center table-success' id=''><b><?php echo $pocet;?></b></td>
                            <?php

                            $suma[$x] = $suma[$x] + $pocet;

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

            }   
            
            
            //Dovolená
            ?>
            <tr class="horizontal-line-hore">
            <td class='table-danger text-end'>Dovolená</td>
            <td class='table-danger text-center'></td>

            <?php
            //prectu pole daneho zamestnance a pokud je vikend tak jej ignoruji, pro zbytek zobrazim zda ma ci nema dovolenou
            for ($x = 0; $x <= $dnu; $x++) 
            {

                $datum = date('Y-m-d', strtotime($start_day . '+ '.$x.'days'));
                $weekDay = date('N', strtotime($datum));

                $pocet = pocet_zam_nepritomnych_za_den($datum,"DOV");

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
                            <td class='text-center table-success' id=''><b><?php echo $pocet;?></b></td>
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
            <td class='table-danger text-center'></td>

            <?php
            //prectu pole daneho zamestnance a pokud je vikend tak jej ignoruji, pro zbytek zobrazim zda ma ci nema dovolenou
            for ($x = 0; $x <= $dnu; $x++) 
            {

                $datum = date('Y-m-d', strtotime($start_day . '+ '.$x.'days'));
                $weekDay = date('N', strtotime($datum));

                $pocet = pocet_zam_nepritomnych_za_den($datum,"DPN");

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
                            <td class='text-center table-success' id=''><b><?php echo $pocet;?></b></td>
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
            <td class='table-danger text-center'></td>

            <?php
            //prectu pole daneho zamestnance a pokud je vikend tak jej ignoruji, pro zbytek zobrazim zda ma ci nema dovolenou
            for ($x = 0; $x <= $dnu; $x++) 
            {

                $datum = date('Y-m-d', strtotime($start_day . '+ '.$x.'days'));
                $weekDay = date('N', strtotime($datum));

                $pocet = pocet_zam_nepritomnych_za_den($datum,"ABS");

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
                            <td class='text-center table-success' id=''><b><?php echo $pocet;?></b></td>
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


            <tr class='table-success fw-bold horizontal-line-hore'>
            <td>Denní počet zaměstnanců</td>
            <td><?php echo $celkem_objednavka;?></td>
                
            <?php
            $fakturace = get_castka_fakturace($rok . "-" . $mesic);

            for ($x = 0; $x <= $dnu; $x++) 
            {   ?>
                    <td class='text-danger'><?php echo $suma[$x];?></td> 
                <?php
            } ?>
                
            </tr>

            <tr class='table-success fw-bold'>
            <td>Denní počet nepřítomných</td>
            <td></td>
                
            <?php
            
            for ($x = 0; $x <= $dnu; $x++) 
            {   ?>
                    <td class='text-danger'><?php echo $suma_nepritomnost[$x];?></td> 
                <?php
            } ?>
                
            </tr>

            <tr class='table-success fw-bold'>
            <td>Denní celkový počet hodin</td>
            <td><?php echo $celkem_objednavka * 7.5;?></td>
                
            <?php
            for ($x = 0; $x <= $dnu; $x++) 
            {   ?>
                    <td><?php echo $suma[$x] * 7.5;?></td> 
                <?php
            } ?>
                
            </tr>

            <tr class='table-success'>
            <td>Denní tržba bez DPH</td>
            <td><?php echo round(($celkem_objednavka * 7.5 * $fakturace)/1000,1);?></td>
                
            <?php
            for ($x = 0; $x <= $dnu; $x++) 
            {   ?>
                    <td class='fs-6 text-danger'><?php echo round(($suma[$x] * 7.5 * $fakturace)/1000,1);?></td> 
                <?php
            } ?>
                
            </tr>

            <tr class='table-success fs-6'>
            <td>Ztráta zaměstnanců za den</td>
            <td></td>
                
            <?php
            for ($x = 0; $x <= $dnu; $x++) 
            {   ?>
                    <td class='fs-6 text-danger'><?php echo $celkem_objednavka - $suma[$x];?></td> 
                <?php
            } ?>
                
            </tr>

            <tr class='table-success fs-6 horizontal-line-dole'>
            <td>Ztráta denní tržby bez DPH</td>
            <td></td>
                
            <?php
            for ($x = 0; $x <= $dnu; $x++) 
            {   ?>
                    <td class='fs-6 text-danger'><?php echo round((($celkem_objednavka - $suma[$x]) * $fakturace)/1000,1);?></td> 
                <?php
            } ?>
                
            </tr>

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