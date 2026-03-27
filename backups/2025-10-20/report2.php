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
    <title>Report dopravy</title>
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
        //vedeni, koordinator, ridic, manazer dopravy
        if (($_SESSION["typ"] == "5") or ($_SESSION["typ"] == "1") or ($_SESSION["typ"] == "2") or ($_SESSION["typ"] == "3") or ($_SESSION["typ"] == "4"))
        {
        ?>

        <h1 class='text-center m-2 p-2'>REPORT DOPRAVY</h1>

        <div class="container-fluid">
        <div class="row justify-content-md-center">
        <div class="col col-md-12">
    
        <br>

        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
            <form class="row g-3" action="report2.php?typ=filtr" method="post">
     
               <div class="col-auto">
                    <label for="datepicker">Výběr směny aktuální</label>

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
                    <label for="datepicker">Výběr směny příští týden</label>

                    <select class="form-select mt-2" id="smena2" name="smena2">
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
                    <label for="floatingSelect">Firma</label>
                   
                    <select class="form-select mt-2" id="firma" name="firma" aria-label="Floating label select example">
                        <option value="ALL" selected>Všechny firmy</option>

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
                            if ((isset($_POST["firma"])) and ($_POST["firma"] == $radek["id"]))
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
                    <label for="vyber"></label>
                    <button type="submit" class="form-control btn btn-primary mt-2">Proveď výběr</button>
                </div> 

            </form>
        </div>

        <?php

        global $conn;

        for ($dopravce = 1; $dopravce <= pocet_dopravcu(); $dopravce++)
        {  

            $tabulka = "";
            $nadpis2 ="";

            if (isset($_POST["smena"]))
            {
                if ($_POST["smena"] == "ALL")
                {
                    $podm1 = "smena <> ''";
                }
                else
                {
                    $podm1 = "smena = '" . $_POST["smena"] . "'";
                }

                if ($_POST["smena2"] == "ALL")
                {
                    $podm2 = "smena2 <> ''";
                }
                else
                {
                    $podm2 = "smena2 = '" . $_POST["smena2"] . "'";
                }

                if ($_POST["firma"] == "ALL")
                {
                    if ($_SESSION["typ"] == "5")
                    {
                        $podm3 = "zamestnanci.firma > 0";
                    }
                    else
                    {
                        $podm3 = "zamestnanci.firma in (" . $_SESSION["firma"] . ")";
                    }   
                    $nadpis2 = ", Firma: Všechny firmy";
                }
                else
                {
                    $podm3 = "zamestnanci.firma in (" . $_POST["firma"] . ")";
                    $nadpis2 = ", Firma: " . get_firma_from_id($_POST["firma"]);
                }

                $sql = "select prijmeni,jmeno,smena,smena2,auta.id,auta.spz,auta.oznaceni,zastavky.zastavka,cilova,zastavky.cas1,cas2,cas3 from zamestnanci left join zastavky on zamestnanci.nastup = zastavky.id left join auta on zastavky.auto = auta.id where zamestnanci.aktivni = '1' and cilova <>'' and nastup > 0 and auta.id = '" . $dopravce . "' and " . $podm1 . " and " . $podm2 . " and " . $podm3 . " order by cas1,prijmeni,cilova";

                $nadpis = "Směna 1: " . $_POST["smena"] . ", směna 2: " . $_POST["smena2"] . $nadpis2;
            }
            else
            {
                if ($_SESSION["typ"] == "5")
                {
                    $sql = "select prijmeni,jmeno,smena,smena2,auta.id,auta.spz,auta.oznaceni,zastavky.zastavka,cilova,zastavky.cas1,cas2,cas3 from zamestnanci left join zastavky on zamestnanci.nastup = zastavky.id left join auta on zastavky.auto = auta.id where zamestnanci.aktivni = '1' and cilova <>'' and nastup > 0 and auta.id = '" . $dopravce . "' order by cas1,prijmeni,cilova";
                }
                else
                {
                    $sql = "select prijmeni,jmeno,smena,smena2,auta.id,auta.spz,auta.oznaceni,zastavky.zastavka,cilova,zastavky.cas1,cas2,cas3 from zamestnanci left join zastavky on zamestnanci.nastup = zastavky.id left join auta on zastavky.auto = auta.id where zamestnanci.aktivni = '1' and cilova <>'' and nastup > 0 and auta.id = '" . $dopravce . "' and zamestnanci.firma in (" . $_SESSION["firma"] . ") order by cas1,prijmeni,cilova";
                }
                

                $nadpis = "Všechno" . $nadpis2;
            }            

            //echo $sql;
            //echo "<br>";
            //echo $podm3;
            
            $tabulka .= "<h3 class='text-center m-2 p-2'>" . get_info_bus($dopravce) . "</h3>";
            
            if ($nadpis <> "")
            {   
                $tabulka .= "<h5 class='text-center m-2 p-2'>" . $nadpis . "</h5>";
            }

            $tabulka .= "<div class='table-responsive-lg text-center'>";
            $tabulka .= "<table class='table table-hover'>";
            $tabulka .= "<thead>";
            $tabulka .= "<tr class='table-active'>";
            $tabulka .= "<th scope='col'>#</th>";
            $tabulka .= "<th scope='col'>Příjmení</th>";
            $tabulka .= "<th scope='col'>Jméno</th>";
            $tabulka .= "<th scope='col'>Směna</th>";
            $tabulka .= "<th scope='col'>SPZ</th>";
            $tabulka .= "<th scope='col'>Označení auta</th>";
            $tabulka .= "<th scope='col'>Zastávka</th>";
            $tabulka .= "<th scope='col'>Cílová stanice</th>";
            $tabulka .= "</tr>";
            $tabulka .= "</thead>";
            $tabulka .= "<tbody>";
 
            //echo $sql;
            
            $cislo = 1;

            if (!($vysledek = mysqli_query($conn, $sql)))
            {
            die("Nelze provést dotaz</body></html>");
            }   
            
            $predchozi = "";

            while ($radek = mysqli_fetch_array($vysledek))
            {   
                $predchozi = $radek["cilova"];
                
                $tabulka .= "<tr class=''>";
                $tabulka .= "<td class='text-center fw-bold'>" . $cislo . "</td>";
                $tabulka .= "<td class='text-start'>" . $radek["prijmeni"] . "</td>";
                $tabulka .= "<td class='text-start'>" . $radek["jmeno"] . "</td>";

                if (isset($_POST["smena"]))
                {
                    if ($_POST["smena"] <> "ALL")
                    {
                        $tabulka .= "<td class='text-center'>" . $radek["smena"] . "</td>";
                    }
                    else
                    {
                        if ($_POST["smena2"] <> "ALL")
                        {
                            $tabulka .= "<td class='text-center'>" . $radek["smena2"] . "</td>";
                        }
                        else
                        {
                            $tabulka .= "<td class='text-center'>" . $radek["smena"] . "</td>";
                        }

                    }
                }
                else
                {
                    $tabulka .= "<td class='text-center'>" . $radek["smena"] . "</td>";
                }

                $tabulka .= "<td class='text-center'>" . $radek["spz"] . "</td>";
                $tabulka .= "<td class='text-center'>" . $radek["oznaceni"] . "</td>";

                if (isset($_POST["smena"]))
                {
                    if ($_POST["smena"] <> "ALL")
                    {
                        if (($_POST["smena"] == "R") or ($_POST["smena"] == "S-R") or ($_POST["smena"] == "N-R"))
                        {
                            $tabulka .= "<td class='text-center'>" . $radek["zastavka"] . " - " . $radek["cas1"] . "</td>";
                        }
                        elseif (($_POST["smena"] == "O") or ($_POST["smena"] == "S-O") or ($_POST["smena"] == "N-O"))
                        {
                            $tabulka .= "<td class='text-center'>" . $radek["zastavka"] . " - " . $radek["cas2"] . "</td>";
                        }
                        elseif (($_POST["smena"] == "N") or ($_POST["smena"] == "S-N") or ($_POST["smena"] == "N-N"))
                        {
                            $tabulka .= "<td class='text-center'>" . $radek["zastavka"] . " - " . $radek["cas3"] . "</td>";
                        }
                    }
                    else
                    {
                        if ($_POST["smena2"] <> "ALL")
                        {
                            if (($_POST["smena2"] == "R") or ($_POST["smena2"] == "S-R") or ($_POST["smena2"] == "N-R"))
                            {
                                $tabulka .= "<td class='text-center'>" . $radek["zastavka"] . " - " . $radek["cas1"] . "</td>";
                            }
                            elseif (($_POST["smena2"] == "O") or ($_POST["smena2"] == "S-O") or ($_POST["smena2"] == "N-O"))
                            {
                                $tabulka .= "<td class='text-center'>" . $radek["zastavka"] . " - " . $radek["cas2"] . "</td>";
                            }
                            elseif (($_POST["smena2"] == "N") or ($_POST["smena2"] == "S-N") or ($_POST["smena2"] == "N-N"))
                            {
                                $tabulka .= "<td class='text-center'>" . $radek["zastavka"] . " - " . $radek["cas3"] . "</td>";
                            }
                        }
                        else
                        {
                            $tabulka .= "<td class='text-center'>" . $radek["zastavka"] . " - " . $radek["cas1"] . "</td>";
                        }
                    }
                }
                else
                {
                    $tabulka .= "<td class='text-center'>" . $radek["zastavka"] . " - " . $radek["cas1"] . "</td>";
                }
                
                $tabulka .= "<td class='text-center'>" . $radek["cilova"] . "</td>";
                $tabulka .= "</tr>";
                
                $cislo = $cislo + 1;
            }

            mysqli_free_result($vysledek);               
      
            $tabulka .= "</tbody>";
            $tabulka .= "</table>";
            $tabulka .= "</div>";

            if ($cislo > 1)
            {
                echo $tabulka;
            }
        }

        ?>

        </div>
        </div>
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