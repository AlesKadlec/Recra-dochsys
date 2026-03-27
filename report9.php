<?php
require_once __DIR__ . '/init.php';   // session_start()
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/funkce.php';
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

    .border-vstup {
        border: 3px solid green !important;
    }
    .border-vystup {
        border: 3px solid red !important;
    }

    .poznamka-symbol {
        position: absolute;
        top: 2px;
        left: 2px;
        font-size: 0.7em;          /* menší než text buňky */
        cursor: pointer;
        color: rgba(0, 0, 0, 0.5); /* tmavě šedá, průhledná */
        background-color: transparent; /* úplně průhledné pozadí */
        padding: 0 2px;
        border-radius: 2px;
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

            if(isset($_GET['typ']) && $_GET['typ'] == 'savechange') 
            {

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
                            $id_zam = $_POST['id_zam'];
                            $stmt->bind_param("is", $id_zam, $datum);
                            $stmt->execute();
                            $stmt->close();
            
                            $stmt = $conn->prepare("DELETE FROM dochazka WHERE zamestnanec=? AND datum=?");
                            $id_zam = $_POST['id_zam'];
                            $stmt->bind_param("is", $id_zam, $datum);
                            $stmt->execute();
                            $stmt->close();
            
                            $nepritomnosti_toggle = ['DPN','OČR','DOV','ABS','NAR','LEK','NEM','NEO','NEP','PRO','NEPV','NAHV','OABS'];
                            
                            // Pokud je vybraná nepřítomnost
                            if(in_array($smena_den, $nepritomnosti_toggle)) 
                            {
                                $stmt = $conn->prepare("INSERT INTO nepritomnost(zamestnanec,datum,nepritomnost,zadal,poznamka) VALUES (?,?,?,?,?)");
                                $id_zam = $_POST['id_zam'];
                                $log_id = $_SESSION['log_id'];

                                $stmt->bind_param(
                                    "issss",
                                    $id_zam,
                                    $datum,
                                    $smena_den,
                                    $log_id,
                                    $poznamka_dne
                                );
                                $stmt->execute();
                                $stmt->close();
                            } 
                       
                            // Pokud je vybraná běžná směna (R,O,N) nebo N/A ('' ponecháme prázdné)
                            if($smena_den != '' && !in_array($smena_den, $nepritomnosti_toggle)) 
                            {
                                $firma = get_info_from_zamestnanci_table($_POST['id_zam'], 'firma');
                                $zastavka = get_info_from_zamestnanci_table($_POST['id_zam'], 'nastup');
                                $cas_nastupu = get_time_nastupu($zastavka, $smena_den);
                                $auto = get_bus_from_zastavky($zastavka);
            
                                insert_attandance_manually($_POST['id_zam'], $auto, $zastavka, $firma, $smena_den, $datum, $cas_nastupu, '', $poznamka_dne,'3');
                            }
            
                            // Log
                            $now = new DateTime('now', new DateTimeZone('Europe/Prague'));
                            $typ = "Editace docházky";
                            $info_text = "Záznam pro " . get_name_from_id_zam($_POST["id_zam"]) . " - $datum " . ($smena_den ?: 'N/A') . ", upravena";
                            $datumcas = $now->format('Y-m-d H:i:s'); // ❗ proměnná
                            $stmt = $conn->prepare("INSERT INTO logs(`kdo`,`typ`,`infotext`,`datumcas`) VALUES (?,?,?,?)");

                            // Nyní předáváme jen proměnné
                            $log_id = $_SESSION['log_id'];
                            $stmt->bind_param("isss", $log_id, $typ, $info_text, $datumcas);
                            $stmt->execute();
                            $stmt->close();

                        }
                    }
                }              
            
                $currentYear = (int)$_POST['vybrany_rok']; // kalendářní rok podle vybraného měsíce
                $prevWeek = null;

                if (!empty($_POST['tydenni_smena']) && is_array($_POST['tydenni_smena'])) {
                    foreach ($_POST['tydenni_smena'] as $week => $smena_tydne) {
                    $trasa_tydne = $_POST['tydenni_trasa'][$week] ?? '';
                    $last_smena = $_POST['last_tydni_smena'][$week] ?? '';
                    $last_trasa = $_POST['last_tydni_trasa'][$week] ?? '';

                    // Pokud přecházíme z vysokého týdne na týden 1 → nový rok
                    if ($prevWeek !== null && $week < $prevWeek) {
                        $currentYear++;
                    }

                    //echo "Týden: $week → Upravený rok: $currentYear<br>";

                    if ($smena_tydne != $last_smena || $trasa_tydne != $last_trasa) {
                        if ($smena_tydne === '' && $trasa_tydne === '') {
                            $stmt = $conn->prepare("DELETE FROM plan_smen WHERE rok=? AND tyden=? AND jmeno=?");
                            $stmt->bind_param("iis", $currentYear, $week, $_POST['id_zam']);
                            $stmt->execute();
                            $stmt->close();
                        } else {
                            $stmt = $conn->prepare(
                                "INSERT INTO plan_smen (rok, tyden, smena, trasa, jmeno) 
                                VALUES (?, ?, ?, ?, ?)
                                ON DUPLICATE KEY UPDATE smena=VALUES(smena), trasa=VALUES(trasa)"
                            );
                            $stmt->bind_param("iisss", $currentYear, $week, $smena_tydne, $trasa_tydne, $_POST['id_zam']);
                            $stmt->execute();
                            $stmt->close();
                        }
                    }

                    $prevWeek = $week; // uložíme týden pro další iteraci
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
      
            <?php


            $kalendarskySystem = CAL_GREGORIAN;
            $pocetDniVMesici = cal_days_in_month($kalendarskySystem, $mesic, $rok);
            $start_day = $rok . "-" . $mesic . "-01"; 
            $dnu = $pocetDniVMesici-1;

            //$end_day = date('Y-m-d', strtotime($start_day . '+ ' . $dnu . ' days'));
            $end_day = $rok . "-" . $mesic . "-" . $pocetDniVMesici;
    
   /*           $dotaz = "SELECT id AS zamestnanec, cilova, vstup, vystup
                FROM zamestnanci
                WHERE
                    nastup <= LAST_DAY('" . $start_day . "')
                    AND (vystup IS NULL OR vystup >= DATE_FORMAT('" . $start_day . "', '%Y-%m-01'))
                    AND cilova = '" . $cilova . "'
                    AND os_cislo REGEXP '^[0-9]+$'
                ORDER BY prijmeni"; */

            $dotaz = "SELECT id AS zamestnanec, cilova, vstup, vystup, os_cislo
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
                //array_push($num_id, $radek["zamestnanec"]);
                $num_id[] = $radek;

            }

            mysqli_free_result($vysledek);

            //print_r($num_id);


            ?>

            <div class="container-fluid">
            <div class="row justify-content-md-center">
            <div class="col col-md-12">

            <?php
            echo "<br>";
            echo "<div class='table-responsive-lg text-center'>";
            echo "<table class='table table-hover'>";
            echo "<thead>";

            // první řádek – den v měsíci
            echo "<tr class='horizontal-line-hore'><th scope='col' class='text-center'>Den</th>";
            for ($x = 0; $x <= $dnu; $x++) {
                $datum   = date('Y-m-d', strtotime("$start_day +$x days"));
                $weekDay = date('N', strtotime($datum));
                $barva   = ($weekDay >= 6) ? 'table-warning' : '';
                echo "<th scope='col' class='text-center $barva'>" . date('d', strtotime($datum)) . "</th>";
            }
            echo "</tr>";

            // druhý řádek – den v týdnu
            echo "<tr class='horizontal-line-dole'><th scope='col' class='text-center' id='hlavickaPoradi'>Jméno a příjmení</th>";
            $dnyTydne = ['1'=>'Po','2'=>'Út','3'=>'St','4'=>'Čt','5'=>'Pá','6'=>'So','7'=>'Ne'];

            for ($x = 0; $x <= $dnu; $x++) {
                $datum   = date('Y-m-d', strtotime("$start_day +$x days"));
                $weekDay = date('N', strtotime($datum));
                $barva   = ($weekDay >= 6) ? 'table-warning' : '';
                echo "<th scope='col' class='text-center $barva'>" . ($dnyTydne[$weekDay] ?? '') . "</th>";
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

            $ids = array_column($num_id, 'zamestnanec');
            $dochazka = nacti_dochazku_mesic($ids, $start_day, $dnu);
            $nepritomnosti = nacti_nepritomnosti_mesic($ids, $start_day, $dnu);
            
            // $kontrola_dochazky obsahuje doplňkové kontroly/nepřítomnosti za měsíc

            $suma_typy = []; // [TYP][INDEX_DNE] => počet
            $kontrola_dochazky = $kontrola_dochazky ?? [];
            
            foreach ($num_id as $value) 
            {
                $id = $value["zamestnanec"];
                
                // --- kontrola, jestli je os_cislo číselné ---
                $is_numeric_os_cislo = preg_match('/^[0-9]+$/', $value['os_cislo']);
                $first_td_class = $is_numeric_os_cislo ? '' : 'table-primary';

                // --- formátování vstup / výstup ---
                $vstup  = (empty($value['vstup']) || $value['vstup'] === '0000-00-00') ? '—' : date('d.m.Y', strtotime($value['vstup']));
                $vystup = (empty($value['vystup']) || $value['vystup'] === '0000-00-00') ? '—' : date('d.m.Y', strtotime($value['vystup']));

                // --- definice tooltipu (včetně osobního čísla na první řádek) ---
                $os_cislo = !empty($value['os_cislo']) ? $value['os_cislo'] : '—';
                $tooltip_text = "Osobní číslo: $os_cislo<br>Nástup: $vstup<br>Výstup: $vystup";

                // --- platný pracovní poměr ---
                $mesic_start = strtotime($rok . '-' . $mesic . '-01');
                $mesic_end   = strtotime(date('Y-m-t', $mesic_start));

                $nastup_ts  = !empty($value['vstup']) && $value['vstup'] !== '0000-00-00' ? strtotime($value['vstup']) : false;
                $vystup_ts  = !empty($value['vystup']) && $value['vystup'] !== '0000-00-00' ? strtotime($value['vystup']) : false;

                if (($nastup_ts && $nastup_ts > $mesic_end) || ($vystup_ts && $vystup_ts < $mesic_start)) {
                    continue;
                }

                // --- kontrola docházky ---
                $ma_dochazku = !empty($dochazka[$id]) || !empty($kontrola_dochazky[$id]) || !empty($nepritomnosti[$id]);
                $vstup_vystup_stejny = ($value["vstup"] !== null && $value["vystup"] !== null && $value["vstup"] == $value["vystup"]);
                if (!$ma_dochazku && $vstup_vystup_stejny) {
                    continue;
                }

                echo "<tr>";
                echo "<td class='$first_td_class'>"; // jen první buňka má třídu

                $pocet_zamestnancu += 1;

                $tooltip_span = "<span class='tooltip-symbol ms-1' data-bs-toggle='tooltip' data-bs-placement='top' data-bs-html='true' title='" 
                                . htmlspecialchars($tooltip_text, ENT_QUOTES) . "'>⏱</span>";
                ?>
                <a class="form-control btn <?php echo $is_numeric_os_cislo ? 'bg-warning' : 'bg-primary text-white'; ?> text-start"
                onclick="vyrob_modal('<?php echo $rok; ?>','<?php echo $mesic; ?>','<?php echo $id; ?>')">
                    <?php echo $pocet_zamestnancu; ?>. <?php echo get_name_from_id_zam($id); ?>
                    <?php echo $tooltip_span; ?>
                </a>
                <?php

                echo "</td>";

                // --- zbytek řádku ---
                for ($x = 0; $x <= $dnu; $x++) 
                {
                    $datum = date('Y-m-d', strtotime($start_day . '+ '.$x.'days'));
                    $weekDay = date('N', strtotime($datum));

                    if (isset($dochazka[$id][$datum])) {
                        $hodnota = $dochazka[$id][$datum]['hodnota'];
                        $poznamka = $dochazka[$id][$datum]['poznamka'];
                    } elseif (isset($kontrola_dochazky[$id][$datum])) {
                        $hodnota = $kontrola_dochazky[$id][$datum];
                        $poznamka = '';
                    } elseif (isset($nepritomnosti[$id][$datum])) {
                        $hodnota = $nepritomnosti[$id][$datum]['hodnota'];
                        $poznamka = $nepritomnosti[$id][$datum]['poznamka'];
                    } else {
                        $hodnota = '';
                        $poznamka = '';
                    }

                    if ($hodnota !== '') {

                        if (!isset($suma_typy[$hodnota][$x])) {
                            $suma_typy[$hodnota][$x] = 0;
                        }

                        $suma_typy[$hodnota][$x]++;
                    }

                    if (($weekDay % 7) == 6 || ($weekDay % 7) == 0) {
                        $barva = 'table-warning';
                    } elseif (in_array($hodnota, $smeny)) {
                        $barva = 'table-success';
                    } elseif (in_array($hodnota, $nepritomnosti_warning)) {
                        $barva = 'table-secondary';
                    } elseif (in_array($hodnota, $nepritomnosti_other)) {
                        $barva = 'table-primary';
                    } else {
                        $barva = 'table-secondary-subtle';
                    }

                    $extra_border = '';
                    if ($datum == $value["vstup"]) $extra_border = 'border-vstup';
                    if ($value["vystup"] !== null && $datum == $value["vystup"]) $extra_border = 'border-vystup';

                    $symbol_html = '';
                    if (!empty($poznamka)) {
                        $symbol_html = "<span class='poznamka-symbol' data-bs-toggle='tooltip' data-bs-placement='top' title='" 
                                        . htmlspecialchars($poznamka, ENT_QUOTES) . "'>&#x1F4DD;</span>";
                    }

                    echo "<td class='text-center $barva $extra_border' style='position: relative;'>$symbol_html<b>$hodnota</b></td>";
                }

                echo "</tr>";
            }

            // inicializace všech tooltipů (Bootstrap 5)
            echo <<<HTML
            <script>
            document.addEventListener("DOMContentLoaded", function() {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            });
            </script>
            HTML;

           $priorita_typu = ['R', 'O', 'N'];
            $serazene_typy = [];

            // --- nejdřív R, O, N (pokud existují) ---
            foreach ($priorita_typu as $p) {
                if (isset($suma_typy[$p])) {
                    $serazene_typy[$p] = $suma_typy[$p];
                }
            }

            // --- potom všechny ostatní ---
            $ostatni = [];
            foreach ($suma_typy as $typ => $dny) {
                if (!in_array($typ, $priorita_typu)) {
                    $ostatni[$typ] = $dny;
                }
            }

            // seřadíme ostatní abecedně podle klíče
            ksort($ostatni);

            // přidáme ostatní za R,O,N
            $serazene_typy += $ostatni;
            
            // --- hlavička tabulky ---
            echo "<tr class='horizontal-line-hore'>";
            echo "<th scope='col' class='text-center' rowspan='2'>Sumarizační tabulka</th>";

            // první řádek: den v měsíci
            for ($x = 0; $x <= $dnu; $x++) {
                $datum   = date('Y-m-d', strtotime("$start_day +$x days"));
                $weekDay = date('N', strtotime($datum));
                $barva   = ($weekDay >= 6) ? 'table-warning' : '';
                echo "<th class='text-center $barva'>" . date('d', strtotime($datum)) . "</th>";
            }
            echo "</tr>";

            // druhý řádek: den v týdnu
            echo "<tr class='horizontal-line-dole'>";
            $dnyTydne = ['1'=>'Po','2'=>'Út','3'=>'St','4'=>'Čt','5'=>'Pá','6'=>'So','7'=>'Ne'];

            for ($x = 0; $x <= $dnu; $x++) {
                $datum   = date('Y-m-d', strtotime("$start_day +$x days"));
                $weekDay = date('N', strtotime($datum));
                $barva   = ($weekDay >= 6) ? 'table-warning' : '';
                echo "<th class='text-center $barva'>" . ($dnyTydne[$weekDay] ?? '') . "</th>";
            }
            echo "</tr>";


            // --- vykreslení tabulky ---
            foreach ($serazene_typy as $typ => $dny) {
                echo "<tr>";
                echo "<td class='table-danger text-end'><b>$typ</b></td>";

                for ($x = 0; $x <= $dnu; $x++) {
                    $datum   = date('Y-m-d', strtotime("$start_day +$x days"));
                    $weekDay = date('N', strtotime($datum));
                    $pocet   = $dny[$x] ?? 0;

                    // určení barvy
                    $barva = ($weekDay == 6 || $weekDay == 7) ? 'table-warning' :
                            ($pocet > 0 ? 'table-primary' : 'table-secondary-subtle');

                    // vykreslení buňky
                    echo "<td class='text-center $barva'>" . ($pocet > 0 ? "<b>$pocet</b>" : "") . "</td>";
                }

                echo "</tr>";
            }

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