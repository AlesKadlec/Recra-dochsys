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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
    <title>Hodinové sazby zaměstnanců</title>
</head>
<body>

<?php

global $conn;

menu();

if (kontrola_prihlaseni() == "OK")
{

    if (($_SESSION["typ"] == "5") or ($_SESSION["typ"] == "7")) 
    {
        if (isset($_GET["typ"]) && $_GET["typ"] == "updatesazba") 
        {
            // Kontrola, jestli přišel POST
            if ($_SERVER['REQUEST_METHOD'] === 'POST') 
            {

                // Ošetření vstupů
                $id_sazby       = isset($_POST['id_sazby']) ? intval($_POST['id_sazby']) : 0;
                $id_zamestnance  = isset($_POST['id_zamestnance']) ? intval($_POST['id_zamestnance']) : 0;
                $sazba           = isset($_POST['sazba']) ? floatval(str_replace(',', '.', $_POST['sazba'])) : 0;
                $obdobi_od       = isset($_POST['obdobi_od']) ? $_POST['obdobi_od'] : null;
                $obdobi_do       = isset($_POST['obdobi_do']) && $_POST['obdobi_do'] !== '' ? $_POST['obdobi_do'] : null;
                $poznamka        = isset($_POST['poznamka']) ? mysqli_real_escape_string($conn, $_POST['poznamka']) : '';

                if ($id_sazby > 0 && $id_zamestnance > 0 && $sazba > 0 && $obdobi_od) {

                    // SQL UPDATE
                    $sql = "UPDATE hodinove_sazby SET
                                id_zamestnance = $id_zamestnance,
                                sazba = $sazba,
                                obdobi_od = '$obdobi_od',
                                obdobi_do = " . ($obdobi_do ? "'$obdobi_do'" : "NULL") . ",
                                poznamka = '$poznamka'
                            WHERE id = $id_sazby";

                    if (mysqli_query($conn, $sql)) 
                    {
                        //echo "<div class='alert alert-success'>Sazba byla úspěšně aktualizována.</div>";
                    } else {
                        //echo "<div class='alert alert-danger'>Chyba při aktualizaci: " . mysqli_error($conn) . "</div>";
                    }
                    ?>

                    <meta http-equiv="refresh" content="0;url=sazby.php">

                    <?php
                } 
                else 
                {   ?>

                    <meta http-equiv="refresh" content="0;url=sazby.php">
                    
                    <?php
                }
            }

        }
        elseif (isset($_GET["typ"]) && $_GET["typ"] == "vytvorsazbu") 
        {

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
                // Ošetření vstupů
                $id_zamestnance  = isset($_POST['id_zamestnance']) ? intval($_POST['id_zamestnance']) : 0;
                $sazba           = isset($_POST['sazba']) ? floatval(str_replace(',', '.', $_POST['sazba'])) : 0;
                $obdobi_od       = isset($_POST['obdobi_od']) ? $_POST['obdobi_od'] : null;
                $obdobi_do       = isset($_POST['obdobi_do']) && $_POST['obdobi_do'] !== '' ? $_POST['obdobi_do'] : null;
                $poznamka        = isset($_POST['poznamka']) ? mysqli_real_escape_string($conn, $_POST['poznamka']) : '';
        
                if ($id_zamestnance > 0 && $sazba > 0 && $obdobi_od) {
        
                    // INSERT do tabulky hodinove_sazby
                    $sql = "INSERT INTO hodinove_sazby
                                (id_zamestnance, sazba, obdobi_od, obdobi_do, poznamka)
                            VALUES
                                ($id_zamestnance, $sazba, '$obdobi_od', " . ($obdobi_do ? "'$obdobi_do'" : "NULL") . ", '$poznamka')";
        
                    if (mysqli_query($conn, $sql)) 
                    {
                        //echo "<div class='alert alert-success'>Nová sazba byla úspěšně vložena.</div>";
                    } else {
                        //echo "<div class='alert alert-danger'>Chyba při vložení: " . mysqli_error($conn) . "</div>";
                    }
                    ?>

                    <meta http-equiv="refresh" content="0;url=sazby.php">

                    <?php
        
                } else 
                {   ?>
                    
                    <meta http-equiv="refresh" content="0;url=sazby.php">

                    <?php
                }
            }
        }
        else
        {
            ?>

            <h1 class='text-center m-2 p-2'>HODINOVÉ SAZBY</h1>
            <h4 class='text-center m-2 p-2'>Přehled změn sazeb zaměstnanců</h4>
                            
            <div class="container-fluid">

            <button type="button" class="btn btn-sm align-middle bg-light" onclick="loadModalContent('')">Nová sazba</button>

            <div class='table-responsive-lg text-center'>           
    
            <table class='table table-hover align-middle'>
                <thead>
                    <tr class='table-active'>
                        <th scope='col'>#</th>
                        <th scope='col'>Zaměstnanec</th>
                        <th scope='col'>Sazba (Kč/hod)</th>
                        <th scope='col'>Platnost od</th>
                        <th scope='col'>Platnost do</th>
                        <th scope='col'>Poznámka</th>
                        <th scope='col'></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // SQL dotaz – připojí jméno zaměstnance
                $sql = "
                    SELECT s.id, 
                           z.jmeno, 
                           z.prijmeni, 
                           s.sazba, 
                           s.obdobi_od, 
                           s.obdobi_do, 
                           s.poznamka
                    FROM hodinove_sazby s
                    LEFT JOIN zamestnanci z ON s.id_zamestnance = z.id
                    ORDER BY z.prijmeni, z.jmeno, s.obdobi_od DESC";
    
                if (!($vysledek = mysqli_query($conn, $sql))) {
                    die('Nelze provést dotaz</body></html>');
                }
    
                $poradi = 1;
                while ($radek = mysqli_fetch_array($vysledek)) {
                    ?>
                    <tr>
                        <td><?= $poradi++ ?></td>
                        <td class='text-start'>
                            <?= htmlspecialchars($radek['prijmeni'] . ' ' . $radek['jmeno']) ?>
                        </td>
                        <td class='text-center'>
                            <?= number_format($radek['sazba'], 2, ',', ' ') ?>
                        </td>
                        <td><?= htmlspecialchars($radek['obdobi_od']) ?></td>
                        <td>
                            <?= $radek['obdobi_do'] ? htmlspecialchars($radek['obdobi_do']) : '<i>aktuální</i>' ?>
                        </td>
                        <td class='text-start'>
                            <?= htmlspecialchars($radek['poznamka']) ?>
                        </td>

                        <td class='text-start'>
                        
                            <button type="button" class="btn btn-sm align-middle bg-light" onclick="loadModalContent('<?php echo $radek['id'];?>')">E</button>

                        </td>
                     
                    </tr>
                    <?php
                }
    
                mysqli_free_result($vysledek);
                ?>
                </tbody>
            </table>
            </div>
            </div>

            <?php

        }
        
        ?>
        <script>

        function loadModalContent(modalId) {
            var functionName = 'nova_sazba'; // Název funkce, kterou chcete volat
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
         
                    // zobrazí modal
                    $('#nova_sazba').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }

        </script>

        <div id="modalContent"></div>

        <?php

    }
    else
    {
        ?>
        <h1 class="text-center m-2 p-2">NEAUTORIZOVANÝ PŘÍSTUP</h1>
        <meta http-equiv="refresh" content="5;url=main.php">
        <?php
    }
  
}

?>

<!-- jQuery (musí být první) -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>