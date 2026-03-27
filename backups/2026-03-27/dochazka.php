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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.4.0/css/fixedHeader.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">


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
            // Zpracování filtru dne
            $datum = !empty($_POST["datepicker"]) ? date_format(date_create($_POST["datepicker"]), 'Y-m-d') : null;

            // Zpracování filtru měsíce (YYYY-mm)
            $mesic = !empty($_POST["mesic"]) ? $_POST["mesic"] : null;

            // Směna
            $smena = $_POST["smena"];
            $cilova_filtr = isset($_POST["cilova_filtr"]) ? trim((string)$_POST["cilova_filtr"]) : "ALL";

            // Vytvoření SQL podmínek
            $where = [];

            // Pokud je vybrán měsíc → filtrujeme celý měsíc
            if ($mesic) {
                // měsíc = "2025-10"
                $start = $mesic . "-01";
                $end = $mesic . "-31";    // stačí pro MySQL i když měsíc nemá 31 dní
                $where[] = "datum BETWEEN '$start' AND '$end'";
            } 
            // Jinak filtrujeme konkrétní den
            elseif ($datum) {
                $where[] = "datum = '$datum'";
            }

            // Filtr směny
            if ($smena === "RON_NOAUTO") {
                $where[] = "dochazka.smena IN ('R','O','N')";
                $where[] = "(auta.spz IS NULL OR auta.spz <> 'Vlastní auto')";
                $where[] = "(nastupy.zastavka IS NULL OR nastupy.zastavka <> 'Vlastní auto')";
            } elseif ($smena === "RON") {
                $where[] = "dochazka.smena IN ('R','O','N')";
            } elseif ($smena !== "ALL") {
                $where[] = "dochazka.smena = '$smena'";
            }

            // Filtr zakazky (cilove stanice)
            if ($cilova_filtr !== "ALL" && $cilova_filtr !== "") {
                $cilovaSafe = mysqli_real_escape_string($conn, $cilova_filtr);
                $where[] = "TRIM(REPLACE(zamestnanci.cilova, CHAR(194,160), ' ')) = TRIM(REPLACE('$cilovaSafe', CHAR(194,160), ' '))";
            }

            // Filtr firmy podle typu uživatele
            if ($_SESSION["typ"] != "5") {
                $where[] = "dochazka.firma IN (" . $_SESSION['firma'] . ")";
            }

            // Sestavení WHERE části
            $where_sql = "";
            if (count($where) > 0) {
                $where_sql = "WHERE " . implode(" AND ", $where);
            }

            ?>

            <h3 class="text-center m-2 p-2">
                Zobrazení docházky dle filtru:
                <?php 
                if ($mesic) {
                    echo "měsíc: <strong>$mesic</strong>";
                } else {
                    echo "den: <strong>$datum</strong>";
                }
                ?>
                &nbsp;|&nbsp; směna: <?php echo $smena; ?>
                &nbsp;|&nbsp; zakázka: <?php echo htmlspecialchars($cilova_filtr, ENT_QUOTES, 'UTF-8'); ?>
            </h3>

            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                <form class="row g-3" action="dochazka.php?typ=filtr" method="post">

                    <!-- Výběr dne -->
                    <div class="col-auto">
                        <label for="datepicker">Výběr dne</label>
                        <input type="date" class="form-control mt-2" id="datepicker" name="datepicker"
                            value="<?php echo $datum ?? ''; ?>">
                    </div>

                    <!-- Výběr směny -->
                    <div class="col-auto">
                        <label for="smena">Výběr směny</label>
                        <select class="form-select mt-2" id="smena" name="smena">
                            <option value="R" <?php if($smena == "R") echo "selected"; ?>>Ranní</option>
                            <option value="O" <?php if($smena == "O") echo "selected"; ?>>Odpolední</option>
                            <option value="N" <?php if($smena == "N") echo "selected"; ?>>Noční</option>
                            <option value="RON" <?php if($smena == "RON") echo "selected"; ?>>R+O+N vše</option>
                            <option value="RON_NOAUTO" <?php if($smena == "RON_NOAUTO") echo "selected"; ?>>R+O+N bez vlastní dopravy</option>
                            <option value="ALL" <?php if($smena == "ALL") echo "selected"; ?>>Všechny směny</option>
                        </select>
                    </div>

                    <!-- Filtr zakazky (cilova) -->
                    <div class="col-auto">
                        <label for="cilova_filtr">Zakázka</label>
                        <select class="form-select mt-2" id="cilova_filtr" name="cilova_filtr">
                            <option value="ALL" <?php if($cilova_filtr == "ALL") echo "selected"; ?>>Vše</option>
                            <?php
                            if ($_SESSION["typ"] == "5")
                            {
                                $sql_cilova = "SELECT DISTINCT(cilova) AS cilova FROM zamestnanci WHERE cilova <> '' ORDER BY cilova";
                            }
                            else
                            {
                                $sql_cilova = "SELECT DISTINCT(cilova) AS cilova FROM zamestnanci WHERE cilova <> '' AND firma IN (" . $_SESSION["firma"] . ") ORDER BY cilova";
                            }

                            if ($vysl_cilova = mysqli_query($conn, $sql_cilova)) {
                                while ($r_c = mysqli_fetch_assoc($vysl_cilova)) {
                                    $c = (string)($r_c['cilova'] ?? '');
                                    if ($c === '') continue;
                                    $sel = ($cilova_filtr === $c) ? "selected" : "";
                                    echo "<option value=\"" . htmlspecialchars($c, ENT_QUOTES, 'UTF-8') . "\" $sel>" . htmlspecialchars($c, ENT_QUOTES, 'UTF-8') . "</option>";
                                }
                                mysqli_free_result($vysl_cilova);
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Nový výběr měsíce -->
                    <div class="col-auto">
                        <label for="mesic">Výběr měsíce</label>
                        <input type="month" class="form-control mt-2" id="mesic" name="mesic"
                            value="<?php echo $mesic ?? ''; ?>">
                    </div>

                    <!-- Tlačítko -->
                    <div class="col-auto">
                        <label>&nbsp;</label>
                        <button type="submit" class="form-control btn btn-primary mt-2">Proveď výběr</button>
                    </div>

                </form>
            </div>

            <div class="container-fluid">
                <div class="row justify-content-md-center">
                    <div class="col col-md-12">

                        <div class="table-responsive-lg text-center mt-3">
                            <table id="planTable" class="datatable table table-hover table-striped table-sm align-middle"
                                style="table-layout: fixed; width:100%; font-size: 0.8rem; line-height: 1;--bs-table-cell-padding-y: .1rem; --bs-table-cell-padding-x: .3rem;">
                                <thead class="table-active">
                                    <tr>
                                        <th>#</th>
                                        <th>Příjmení</th>
                                        <th>Jméno</th>
                                        <th>Os. číslo</th>
                                        <th>Datum</th>
                                        <th>Čas</th>
                                        <th>Směna</th>
                                        <th>Trasa</th>
                                        <th>Zastávka</th>
                                        <th>Cílová</th>
                                        <th>Nepřítomnost</th>
                                        <th>Pozn.</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $cislo = 1;

                                $limit_sql = "";

                                // Pokud NENÍ filtr podle měsíce → omezíme počet záznamů
                                if (!$mesic) {
                                    $limit_sql = "LIMIT 1000";
                                }

                                $sql = "
                                    SELECT dochazka.id, zamestnanci.prijmeni, zamestnanci.jmeno, zamestnanci.os_cislo,
                                        datum, cas, dochazka.smena, auta.spz, nastupy.zastavka, firmy.firma, cilova, dochazka.nepritomnost, dochazka.poznamka
                                    FROM dochazka
                                    LEFT JOIN zamestnanci ON dochazka.zamestnanec = zamestnanci.id
                                    LEFT JOIN auta ON dochazka.bus = auta.id
                                    LEFT JOIN nastupy ON dochazka.zastavka = nastupy.id
                                    LEFT JOIN firmy ON dochazka.firma = firmy.id
                                    $where_sql
                                    ORDER BY datum DESC, cas DESC
                                    $limit_sql
                                ";

                                //echo $sql;

                                if (!($vysledek = mysqli_query($conn, $sql))) {
                                    die("Nelze provést dotaz");
                                }

                                while ($radek = mysqli_fetch_array($vysledek)) {
                                    echo "<tr>
                                            <td class='text-center'>{$cislo}</td>
                                            <td class='text-start'>{$radek['prijmeni']}</td>
                                            <td class='text-start'>{$radek['jmeno']}</td>
                                            <td class='text-center'>{$radek['os_cislo']}</td>
                                            <td class='text-center'>{$radek['datum']}</td>
                                            <td class='text-center'>{$radek['cas']}</td>
                                            <td class='text-center'>{$radek['smena']}</td>
                                            <td class='text-center'>{$radek['spz']}</td>
                                            <td class='text-start'>{$radek['zastavka']}</td>
                                            <td class='text-start'>{$radek['cilova']}</td>
                                            <td class='text-start'>{$radek['nepritomnost']}</td>
                                            <td class='text-start'>{$radek['poznamka']}</td>
                                            <td class='text-start'></td>
                                        </tr>";
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
                
                insert_attandance_manually($_POST['jmeno'],$bus,$zastavka,$firma,$smena,$_POST['datum'],$cas_nastupu,'');

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

                <!-- Výběr dne -->
                <div class="col-auto">
                    <label for="datepicker">Výběr dne</label>
                    <input type="date" class="form-control mt-2" id="datepicker" name="datepicker">
                </div>

                <!-- Výběr směny -->
                <div class="col-auto">
                    <label for="smena">Výběr směny</label>
                    <select class="form-select mt-2" id="smena" name="smena">
                        <option value="R">Ranní</option>
                        <option value="O">Odpolední</option>
                        <option value="N">Noční</option>
                        <option value="RON">R + O + N</option>
                        <option value="RON_NOAUTO">Bez vlastní dopravy</option>
                        <option value="ALL" selected>Všechny směny</option>
                    </select>
                </div>

                <!-- Filtr zakazky (cilova) -->
                <div class="col-auto">
                    <label for="cilova_filtr">Zakázka</label>
                    <select class="form-select mt-2" id="cilova_filtr" name="cilova_filtr">
                        <option value="ALL" selected>Vše</option>
                        <?php
                        if ($_SESSION["typ"] == "5")
                        {
                            $sql_cilova = "SELECT DISTINCT(cilova) AS cilova FROM zamestnanci WHERE cilova <> '' ORDER BY cilova";
                        }
                        else
                        {
                            $sql_cilova = "SELECT DISTINCT(cilova) AS cilova FROM zamestnanci WHERE cilova <> '' AND firma IN (" . $_SESSION["firma"] . ") ORDER BY cilova";
                        }

                        if ($vysl_cilova = mysqli_query($conn, $sql_cilova)) {
                            while ($r_c = mysqli_fetch_assoc($vysl_cilova)) {
                                $c = (string)($r_c['cilova'] ?? '');
                                if ($c === '') continue;
                                echo "<option value=\"" . htmlspecialchars($c, ENT_QUOTES, 'UTF-8') . "\">" . htmlspecialchars($c, ENT_QUOTES, 'UTF-8') . "</option>";
                            }
                            mysqli_free_result($vysl_cilova);
                        }
                        ?>
                    </select>
                </div>

                <!-- Nový výběr měsíce -->
                <div class="col-auto">
                    <label for="mesic">Výběr měsíce</label>
                    <input type="month" class="form-control mt-2" id="mesic" name="mesic">
                </div>

                <!-- Tlačítko -->
                <div class="col-auto">
                    <label>&nbsp;</label>
                    <button type="submit" class="form-control btn btn-primary mt-2">Proveď výběr</button>
                </div>

                <!-- Vložení docházky – jen pro definované role -->
                <?php if (in_array($_SESSION["typ"], ["1", "4", "5"])) { ?>
                    <div class="col-auto">
                        <label>&nbsp;</label>
                        <button type="button" class="form-control btn btn-success mt-2"
                                data-bs-toggle='modal' data-bs-target='#exampleModal1'>
                            Vložení docházky
                        </button>
                    </div>
                <?php } ?>

            </form>
        </div>

        <div class="row justify-content-md-center">
        <div class="col col-md-12">
 
        <br>

        <div id="tableScrollContainer" class="container-fluid mt-3">
        <table id="planTable" class="datatable table table-hover table-striped table-sm align-middle"
                style="table-layout: fixed; width:100%; font-size: 0.8rem; line-height: 1;--bs-table-cell-padding-y: .1rem; --bs-table-cell-padding-x: .3rem;">

        <thead>
            <tr class='table-active'>
                <th scope='col'>#</th>
                <th scope='col'>Příjmení</th>
                <th scope='col'>Jméno</th>
                <th scope='col'>Os. číslo</th>
                <th scope='col'>Datum</th>
                <th scope='col'>Čas</th>
                <th scope='col'>Směna</th>
                <th scope='col'>Trasa</th>
                <th scope='col'>Zastávka</th>
                <th scope='col'>Cílová</th>
                <th scope='col'>Nepřítomnost</th>
                <th scope='col'>Pozn.</th>
                
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
                $sql = "select dochazka.id,zamestnanci.prijmeni,zamestnanci.jmeno,zamestnanci.os_cislo,datum,cas,dochazka.smena,auta.spz,nastupy.zastavka,firmy.firma,cilova,dochazka.nepritomnost,dochazka.poznamka from dochazka left join zamestnanci on dochazka.zamestnanec = zamestnanci.id left join auta on dochazka.bus=auta.id left join nastupy on dochazka.zastavka = nastupy.id left join firmy on dochazka.firma = firmy.id order by datum desc,cas desc limit 1000";
            }
            else
            {
                $sql = "select dochazka.id,zamestnanci.prijmeni,zamestnanci.jmeno,zamestnanci.os_cislo,datum,cas,dochazka.smena,auta.spz,nastupy.zastavka,firmy.firma,cilova,dochazka.nepritomnost,dochazka.poznamka from dochazka left join zamestnanci on dochazka.zamestnanec = zamestnanci.id left join auta on dochazka.bus=auta.id left join nastupy on dochazka.zastavka = nastupy.id left join firmy on dochazka.firma = firmy.id where dochazka.firma in(" . $_SESSION['firma'] . ") order by datum desc,cas desc limit 1000";
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
                    <td class='text-start'><?php echo $radek["poznamka"];?></td>

                    <?php
                    if (($_SESSION["typ"] == "1") or ($_SESSION["typ"] == "4") or ($_SESSION["typ"] == "5"))
                    {   ?>
                            <!-- <td class='text-start'><button type="button" class="form-control btn btn-sm btn-success mt-2" data-bs-toggle='modal' data-bs-target='#editModal<?php echo $radek['id'];?>'>EDIT</button></td> -->

                            <td class='text-start'>
                                <button type="button" 
                                        class="btn btn-success btn-sm py-0 px-2"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal<?php echo $radek['id']; ?>">
                                    EDIT
                                </button>
                            </td>


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

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.cs.min.js"></script>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script src="https://cdn.datatables.net/fixedheader/3.4.0/js/dataTables.fixedHeader.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>

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
$(document).ready(function() {
    $('.datatable').each(function() {
        $(this).DataTable({
            paging: true,
            pageLength: 200,
            lengthMenu: [[200, 500, -1], [200, 500, "Vše"]],

            info: true,
            orderCellsTop: true,
            fixedHeader: { header: true, headerOffset: 60 },
            processing: true,

            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/cs.json"
            },

            // 🔹 Buttons vlevo — Search uprostřed — Pagination vpravo
            dom:
                "<'row mb-2'" +
                    "<'col-auto'B>" +          /* vlevo ovládací tlačítka */
                    "<'col text-center'f>" +   /* uprostřed vyhledávání */
                    "<'col-auto text-end'p>" + /* vpravo stránkování */
                ">" +
                "<'row'<'col'i>>" +
                "<'row'<'col'tr>>" +
                "<'row mt-2'<'col'p>>",        /* stránkování dole */

            buttons: [
                { extend: 'excelHtml5', text: 'Excel', className: 'btn btn-primary btn-sm' },
                { extend: 'pdfHtml5', text: 'PDF', orientation: 'landscape', pageSize: 'A4', className: 'btn btn-primary btn-sm' },
                { extend: 'print', text: 'Tisk', className: 'btn btn-primary btn-sm' },
                { extend: 'colvis', text: 'Sloupce', className: 'btn btn-primary btn-sm' },
                {
                    text: 'Reset filtrů',
                    className: 'btn btn-primary btn-sm',
                    action: function ( e, dt, node, config ) {
                        $(dt.table().header()).find('input').val('');
                        dt.search('').columns().search('').draw();
                    }
                }
            ],

            columnDefs: [
                { width: '3%',  targets: 0 },              // #
                { width: '12%', targets: [1,2] },           // Příjmení, Jméno
                { width: '6%',  targets: 3 },               // Os. číslo

                { width: '6%',  targets: 4 },               // Datum (UŽŠÍ)
                { width: '5%',  targets: 5 },               // Čas (UŽŠÍ)
                { width: '5%',  targets: 6 },               // Směna (UŽŠÍ)

                { width: '11%', targets: 7 },               // Trasa
                { width: '16%', targets: 8 },               // Zastávka (ROZŠÍŘENÁ)
                { width: '8%',  targets: 9 },               // Cílová
                { width: '8%',  targets: 10 },              // Nepřítomnost
                { width: '16%', targets: 11 },              // Pozn. (STEJNÁ JAKO ZASTÁVKA)
                { width: '4%',  targets: 12 }               // Edit
            ]

        });
    });
});
</script>




<?php modal_vlozeni_dochazky();?>

