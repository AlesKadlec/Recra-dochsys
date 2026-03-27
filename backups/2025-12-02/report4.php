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
                $_SESSION["vybranymesic"] = $_POST['mesic'];
            }
            elseif (isset($_SESSION["vybranymesic"]))
            {
                $str_arr = explode ("-", $_SESSION["vybranymesic"]); 
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
                $_SESSION["vybranafirma"] = $_POST['cilova'];
            }
            elseif (isset($_SESSION["vybranafirma"]))
            {
                $cilova = $_SESSION["vybranafirma"];
            }
            else
            {
                $cilova = "BATZ";
            }

            if(isset($_GET['typ']) && $_GET['typ'] == 'savechange') {

                // 1) Ukládání denních směn
                for($i = 1; $i <= $_POST['max_den']; $i++) {
                    if(isset($_POST['toggle' . $i])) {
                        if (
                            $_POST['toggle' . $i] != $_POST['lasttoggle' . $i] ||
                            ($_POST['poznamka' . $i] ?? '') != ($_POST['lastpoznamka' . $i] ?? '')
                        ) {
                            $smena_den = $_POST['toggle' . $i];
                            $datum = $_POST['vybrany_rok'] . "-" . $_POST['vybrany_mesic'] . "-" . $i;
                            $poznamka_dne = $_POST['poznamka' . $i] ?? '';
            
                            // Smazání starých záznamů
                            $stmt = $conn->prepare("DELETE FROM nepritomnost WHERE zamestnanec=? AND datum=?");
                            $stmt->bind_param("is", $_POST['id_zam'], $datum);
                            $stmt->execute();
                            $stmt->close();
            
                            $stmt = $conn->prepare("DELETE FROM dochazka WHERE zamestnanec=? AND datum=?");
                            $stmt->bind_param("is", $_POST['id_zam'], $datum);
                            $stmt->execute();
                            $stmt->close();
            
                            $nepritomnosti_toggle = ['DPN','OČR','DOV','ABS','NAR','LEK','NEM','NEO','NEP','PRO'];
                            
                            // Pokud je vybraná nepřítomnost
                            if(in_array($smena_den, $nepritomnosti_toggle)) {
                                $stmt = $conn->prepare("INSERT INTO nepritomnost(zamestnanec,datum,nepritomnost,zadal) VALUES (?,?,?,?)");
                                $stmt->bind_param("isss", $_POST['id_zam'], $datum, $smena_den, $_SESSION["log_id"]);
                                $stmt->execute();
                                $stmt->close();
                            } 
                            
                            // Pokud je vybraná běžná směna (R,O,N) nebo N/A ('' ponecháme prázdné)
                            if($smena_den != '' && !in_array($smena_den, $nepritomnosti_toggle)) {
                                $firma = get_info_from_zamestnanci_table($_POST['id_zam'], 'firma');
                                $zastavka = get_info_from_zamestnanci_table($_POST['id_zam'], 'nastup');
                                $cas_nastupu = get_time_nastupu($zastavka, $smena_den);
                                $auto = get_bus_from_zastavky($zastavka);
            
                                insert_attandance_manually($_POST['id_zam'], $auto, $zastavka, $firma, $smena_den, $datum, $cas_nastupu, '', $poznamka_dne);
                            }
            
                            // Log
                            $now = new DateTime('now', new DateTimeZone('Europe/Prague'));
                            $info_text = "Záznam pro " . get_name_from_id_zam($_POST["id_zam"]) . " - $datum " . ($smena_den ?: 'N/A') . ", upravena";
                            $stmt = $conn->prepare("INSERT INTO logs(kdo,typ,infotext,datumcas) VALUES (?,?,?,?)");
                            $stmt->bind_param("isss", $_SESSION["log_id"], $typ='Editace docházky', $info_text, $now->format('Y-m-d H:i:s'));
                            $stmt->execute();
                            $stmt->close();
                        }
                    }
                }
            
                // 2) Ukládání týdenních směn (plan_smen)
                if(isset($_POST['tydenni_smena']) && is_array($_POST['tydenni_smena'])) {
                    foreach($_POST['tydenni_smena'] as $week => $smena_tydne) {
                        $last = $_POST['last_tydni_smena'][$week] ?? ''; // hodnota z DB při načtení

                        if($smena_tydne != $last) { // změna nastala
                            if($smena_tydne == '') {
                                // pokud je vybrána prázdná hodnota, smažeme řádek z DB
                                $stmt = $conn->prepare("DELETE FROM plan_smen WHERE rok = ? AND tyden = ? AND jmeno = ?");
                                $stmt->bind_param("iis", $_POST['vybrany_rok'], $week, $_POST['id_zam']);
                                $stmt->execute();
                                $stmt->close();
                            } else {
                                // jinak vložíme nebo updatujeme
                                $stmt = $conn->prepare("
                                    INSERT INTO plan_smen (rok, tyden, smena, jmeno)
                                    VALUES (?, ?, ?, ?)
                                    ON DUPLICATE KEY UPDATE smena = VALUES(smena)
                                ");
                                $stmt->bind_param("iiss", $_POST['vybrany_rok'], $week, $smena_tydne, $_POST['id_zam']);
                                $stmt->execute();
                                $stmt->close();
                            }
                        }
                    }
                }
            
                // Přesměrování po uložení
                echo '<meta http-equiv="refresh" content="0;url=report4.php?typ=filtr">';
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

                            if ($_SESSION["typ"] == "5")
                            {
                                $sql = "SELECT DISTINCT(cilova) as cilova FROM zamestnanci where cilova <> '' order by cilova";
                            }
                            else
                            {
                                $sql = "SELECT DISTINCT(cilova) as cilova FROM zamestnanci where cilova <> '' and firma in (" . $_SESSION["firma"] . ") order by cilova";
                            }
                            
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
                        // Výchozí začátek
                        $start = new DateTime("2023-10-01");

                        // Aktuální měsíc + 2 měsíce
                        $end = new DateTime("first day of this month");
                        $end->modify("+2 months");

                        // Uložíme si všechny měsíce do pole
                        $interval = new DateInterval("P1M");
                        $period = new DatePeriod($start, $interval, $end->modify("+1 month"));

                        $mesice = [];
                        foreach ($period as $dt) {
                            $mesice[] = $dt->format("Y-m");
                        }

                        // Obrátíme pořadí
                        $mesice = array_reverse($mesice);

                        // Vypisujeme
                        foreach ($mesice as $value) {

                            if (($rok . "-" . $mesic) == $value) {
                                echo '<option value="' . $value . '" selected>' . $value . '</option>';
                            } else {
                                echo '<option value="' . $value . '">' . $value . '</option>';
                            }
                        }
                        ?>
                        </select>

                    </div>

                    <input type="hidden" class="form-control" id="hid_mesic" name="hid_mesic" placeholder="" value=<?php echo $rok . "-" . $mesic;?>>
                                    
                    <div class="col-auto">
                        <label for="vyber"> </label>
                        <button type="submit" class="form-control btn btn-primary mt-2">Proveď výběr</button>
                    </div> 

                </form>
            </div>
           
            <!-- <a class="form-control btn btn-success" data-bs-toggle='modal' onclick="vyrob_modal('<?php echo $rok; ?>','<?php echo $mesic; ?>','542')">Nový záznam</a> -->

            <?php //vyrob_kalendar($rok,$mesic,542);?>

            <?php

            $kalendarskySystem = CAL_GREGORIAN;
            $pocetDniVMesici = cal_days_in_month($kalendarskySystem, $mesic, $rok);
            $start_day = $rok . "-" . $mesic . "-01"; 
            $dnu = $pocetDniVMesici-1;

            //$end_day = date('Y-m-d', strtotime($start_day . '+ ' . $dnu . ' days'));
            $end_day = $rok . "-" . $mesic . "-" . $pocetDniVMesici;

            //$dotaz = "SELECT id as zamestnanec,cilova FROM zamestnanci WHERE (nastup <= LAST_DAY('" . $start_day . "')) AND (vystup IS NULL OR vystup >= '" . $start_day . "') and cilova='" . $cilova . "' and aktivni = 1 order by zamestnanci.prijmeni";

            $dotaz = "SELECT id AS zamestnanec, cilova
                FROM zamestnanci
                WHERE
                    nastup <= LAST_DAY('" . $start_day . "')
                    AND (vystup IS NULL OR vystup >= DATE_FORMAT('" . $start_day . "', '%Y-%m-01'))
                    AND cilova = '" . $cilova . "'
                ORDER BY prijmeni";
         
            //echo $dotaz;

            echo "<br>";
     
            if (!($vysledek = mysqli_query($conn, $dotaz)))
            {
            die("Nelze provést dotaz.</body></html>");
            }

            $num_id = array();

            while ($radek = mysqli_fetch_array($vysledek))
            {   
                array_push($num_id, $radek["zamestnanec"]);
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

            echo "<tr class='horizontal-line-dole'><th scope='col' class='text-center' id='hlavickaPoradi'>Jméno a příjmení</th>";

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
            $pocet_zamestnancu = 0;

            $celkem_objednavka = 0;
       
            // Kategorie
            $smeny = ['R','O','N','NN','NR'];
            $nepritomnosti_warning = ['DPN','OČR','ABS','LEK'];
            $nepritomnosti_other = ['DOV','NAR','NEM','NEO','NEP','PRO','NEPV','NAHV','OABS'];

            echo "<tbody>";

            $suma_nepritomnost = array_fill(0, $dnu+1, 0);

            foreach ($num_id as $value) 
            {
                echo "<tr>";
                echo "<td>";
                
                $pocet_zamestnancu += 1;

                // Odkaz se jménem zaměstnance
                ?>
                <a class="form-control btn bg-warning text-start" 
                data-bs-toggle='modal' 
                onclick="vyrob_modal('<?php echo $rok; ?>','<?php echo $mesic; ?>','<?php echo $value; ?>')">
                    <?php echo $pocet_zamestnancu;?>. <?php echo get_name_from_id_zam($value);?>
                </a>
                <?php
                echo "</td>";

                // Pro každý den v měsíci
                for ($x = 0; $x <= $dnu; $x++) 
                {
                    $datum = date('Y-m-d', strtotime($start_day . '+ '.$x.'days'));
                    $weekDay = date('N', strtotime($datum));

                    $hodnota = dochazka_zamestnanec_den($value, $datum);
                    if ($hodnota == '') {
                        $hodnota = kontrola_dochazky($value, $datum);
                    }

                    // Určení barvy buňky
                    if (($weekDay % 7) == 6 || ($weekDay % 7) == 0) {
                        $barva = 'table-warning'; // víkend – žlutý
                        $text = $hodnota;
                    } elseif (in_array($hodnota, $smeny)) {
                        $barva = 'table-success'; // směny – zelená
                        $text = $hodnota;
                    } elseif (in_array($hodnota, $nepritomnosti_warning)) {
                        $barva = 'table-secondary'; // varovné nepřítomnosti – světle šedá
                        $text = $hodnota;
                    } elseif (in_array($hodnota, $nepritomnosti_other)) {
                        $barva = 'table-primary'; // ostatní nepřítomnosti – modrá
                        $text = $hodnota;
                    } else {
                        $barva = 'table-secondary-subtle'; // prázdné pole – světle šedá
                        $text = ''; // žádný text
                    }

                    echo "<td class='text-center $barva'><b>$text</b></td>";
                }

                echo "</tr>";
            }

            // ---------- Dovolená ----------
            ?>
            <tr class='horizontal-line-hore'>
                <td class='table-danger text-end'>Dovolená</td>
            <?php
            for ($x = 0; $x <= $dnu; $x++) {
                $datum = date('Y-m-d', strtotime($start_day . '+ '.$x.'days'));
                $weekDay = date('N', strtotime($datum));

                $pocet = pocet_zam_nepritomnych_za_den_firma($datum,"DOV",$cilova);

                if (($weekDay % 7) == 6 || ($weekDay % 7) == 0) {
                    echo "<td class='text-center table-warning'></td>";
                } elseif ($pocet > 0) {
                    echo "<td class='text-center table-primary'><b>$pocet</b></td>"; // DOV – modrá
                    $suma_nepritomnost[$x] += $pocet;
                } else {
                    echo "<td class='text-center table-secondary-subtle'></td>"; // prázdné – světle šedá
                }
            }
            echo "</tr>";

            // ---------- DPN ----------
            ?>
            <tr>
                <td class='table-danger text-end'>DPN</td>
            <?php
            for ($x = 0; $x <= $dnu; $x++) {
                $datum = date('Y-m-d', strtotime($start_day . '+ '.$x.'days'));
                $weekDay = date('N', strtotime($datum));

                $pocet = pocet_zam_nepritomnych_za_den_firma($datum,"DPN",$cilova);

                if (($weekDay % 7) == 6 || ($weekDay % 7) == 0) {
                    echo "<td class='text-center table-warning'></td>";
                } elseif ($pocet > 0) {
                    echo "<td class='text-center table-secondary-subtle'><b>$pocet</b></td>"; // DPN – světle šedá
                    $suma_nepritomnost[$x] += $pocet;
                } else {
                    echo "<td class='text-center table-secondary-subtle'></td>"; // prázdné – světle šedá
                }
            }
            echo "</tr>";

            // ---------- Absence ----------
            ?>
            <tr>
                <td class='table-danger text-end'>Absence</td>
            <?php
            for ($x = 0; $x <= $dnu; $x++) {
                $datum = date('Y-m-d', strtotime($start_day . '+ '.$x.'days'));
                $weekDay = date('N', strtotime($datum));

                $pocet = pocet_zam_nepritomnych_za_den_firma($datum,"ABS",$cilova);

                if (($weekDay % 7) == 6 || ($weekDay % 7) == 0) {
                    echo "<td class='text-center table-warning'></td>";
                } elseif ($pocet > 0) {
                    echo "<td class='text-center table-secondary-subtle'><b>$pocet</b></td>"; // ABS – světle šedá
                    $suma_nepritomnost[$x] += $pocet;
                } else {
                    echo "<td class='text-center table-secondary-subtle'></td>"; // prázdné – světle šedá
                }
            }
            echo "</tr>";

            echo "</tbody>";
            ?>

            <input type="hidden" id="celkovyPocet" value="<?= $pocet_zamestnancu ?>">

            </table>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const celkem = document.getElementById('celkovyPocet').value;
                document.getElementById('hlavickaPoradi').textContent = 'Jméno a příjmení (počet: ' + celkem + ')';
            });
            </script>


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

<script>
    function vyrob_modal(rok,mesic,zamestnanec) {
        var functionName = 'vyrob_kalendar'; // Název funkce, kterou chcete volat
        var ID = rok; // Získání ID modálního okna z modalId
        var ID2 = mesic; // Získání ID modálního okna z modalId
        var ID3 = zamestnanec; // Získání ID modálního okna z modalId      

        $.ajax({
            url: 'funkce.php', // Cesta k externímu skriptu
            type: 'GET',
            data: { functionName3: functionName, ID: ID, ID2: ID2, ID3 : ID3 }, // Předání funkce a ID
            success: function(response) {
                $('#modalContent').html(response);
                $('#kalendar_dochazka').modal('show');
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }
</script>

<div id="modalContent"></div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>