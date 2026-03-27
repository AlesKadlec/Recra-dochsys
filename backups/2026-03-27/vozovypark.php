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
    <title>Přehled vozového parku</title>
</head>
<body>

<?php

global $conn;

menu();

if (kontrola_prihlaseni() == "OK")
{
    if (isset($_SESSION["typ"]))
    {
        if (($_SESSION["typ"] == '1') or ($_SESSION["typ"] == '4') or ($_SESSION["typ"] == '5'))
        {    
            
            if (isset($_GET["typ"]))
            {
                if ($_GET["typ"] === "updateauto" && in_array($_SESSION["typ"], [1, 4, 5])) 
                {
                    $id_auta = $_POST["id_auta"];
                    $spz = $_POST["spz"];
                    $oznaceni = $_POST["oznaceni"];

                    // --- Aktualizace auta ---
                    $sql = "UPDATE auta SET spz = ?, oznaceni = ? WHERE id = ?";
                    if ($stmt = $conn->prepare($sql)) {
                        $stmt->bind_param("ssi", $spz, $oznaceni, $id_auta);
                        if (!$stmt->execute()) {
                            die("Nelze provést dotaz.</body></html>");
                        }
                        $stmt->close();
                    }

                    // --- Vložení do logu ---
                    $now = new DateTime('now', new DateTimeZone('Europe/Prague'));
                    $logText = "Editace dopravy $spz - $oznaceni";
                    $sqlLog = "INSERT INTO logs (kdo, typ, infotext, datumcas) VALUES (?, 'Editace dopravy', ?, ?)";
                    if ($stmt = $conn->prepare($sqlLog)) {
                        $stmt->bind_param("iss", $_SESSION["log_id"], $logText, $now->format('Y-m-d H:i:s'));
                        if (!$stmt->execute()) {
                            die("Nelze provést dotaz.</body></html>");
                        }
                        $stmt->close();
                    }
                    ?>

                    <meta http-equiv="refresh" content="0;url=vozovypark.php">
                    <?php
                }
                elseif (($_GET["typ"] == "save_trasa" && in_array($_SESSION["typ"], [1,4,5]))) 
                {
                    global $conn;

                    if (!empty($_POST['id_auta'])) {
                        $id_auta = intval($_POST['id_auta']);

                        // --- MAZÁNÍ TRASY ---
                        if (!empty($_POST['akce']) && str_starts_with($_POST['akce'], 'delete_trasa_')) {
                            $trasa_id = intval(str_replace('delete_trasa_', '', $_POST['akce']));

                            $sql = "DELETE FROM trasy WHERE id = ?";
                            if ($stmt = $conn->prepare($sql)) {
                                $stmt->bind_param("i", $trasa_id);
                                $stmt->execute();
                                $stmt->close();
                            }

                            // Přesměrování zpět
                            ?>
                            <meta http-equiv="refresh" content="0;url=vozovypark.php">
                            <?php
                            exit;
                        }

                        // --- ULOŽENÍ ČASŮ EXISTUJÍCÍCH TRAS ---
                        $R = $_POST['R'] ?? [];
                        $O = $_POST['O'] ?? [];
                        $N = $_POST['N'] ?? [];

                        $sql = "UPDATE trasy SET R = ?, O = ?, N = ? WHERE id = ?";
                        if ($stmt = $conn->prepare($sql)) {
                            foreach ($R as $trasa_id => $ranni_val) {
                                $trasa_id = intval($trasa_id);
                                $ranni_val = !empty($ranni_val) ? $ranni_val : null;
                                $odpoledni_val = $O[$trasa_id] ?? null;
                                $nocni_val = $N[$trasa_id] ?? null;

                                $stmt->bind_param("sssi", $ranni_val, $odpoledni_val, $nocni_val, $trasa_id);
                                $stmt->execute();
                            }
                            $stmt->close();
                        }

                        // --- NOVÁ ZASTÁVKA DO TRASY ---
                        if (!empty($_POST['id_zastavky'])) {
                            $id_zastavky = intval($_POST['id_zastavky']);
                            $sql_insert = "INSERT INTO trasy (auto, zastavka, R, O, N) VALUES (?, ?, '00:00', '00:00', '00:00')";
                            if ($stmt_ins = $conn->prepare($sql_insert)) {
                                $stmt_ins->bind_param("ii", $id_auta, $id_zastavky);
                                $stmt_ins->execute();
                                $stmt_ins->close();
                            }
                        }

                        // Přesměrování zpět
                        ?>
                        <meta http-equiv="refresh" content="0;url=vozovypark.php">
                        <?php
                        exit;
                    }
                }
                elseif (($_GET["typ"] == "add_zastavka" && in_array($_SESSION["typ"], [1,4,5]))) 
                {
                    if (!empty($_POST['nova_zastavka'])) {
                        $nova_zastavka = trim($_POST['nova_zastavka']);

                        // Prepared statement pro vložení nové zastávky
                        $sql = "INSERT INTO nastupy (zastavka) VALUES (?)";
                        if ($stmt = $conn->prepare($sql)) {
                            $stmt->bind_param("s", $nova_zastavka);
                            if ($stmt->execute()) {
                                //echo '<div class="alert alert-success text-center">Zastávka byla úspěšně přidána.</div>';
                            } else {
                                //echo '<div class="alert alert-danger text-center">Chyba při ukládání zastávky.</div>';
                            }
                            $stmt->close();
                        } else {
                            //echo '<div class="alert alert-danger text-center">Chyba při přípravě dotazu.</div>';
                        }
                    } else {
                        //echo '<div class="alert alert-warning text-center">Zadejte název zastávky.</div>';
                    }

                    // Přesměrování zpět na stránku (např. po 2 sekundách)
                    echo '<meta http-equiv="refresh" content="0;url=vozovypark.php">';
                }
                elseif (($_GET["typ"] == "delete_zastavka" && in_array($_SESSION["typ"], [1,4,5]))) 
                {
                    if (!empty($_POST['id_zastavky'])) {
                        $id_zastavky = (int) $_POST['id_zastavky'];

                        // 1) Smazání záznamů z trasy
                        $sql_trasy = "DELETE FROM trasy WHERE zastavka = ?";
                        if ($stmt_trasy = $conn->prepare($sql_trasy)) {
                            $stmt_trasy->bind_param("i", $id_zastavky);
                            $stmt_trasy->execute();
                            $stmt_trasy->close();
                        }

                        // 2) Resetování nastupu u zaměstnanců, kteří měli tuto zastávku
                        $sql_zam = "UPDATE zamestnanci SET nastup = 0 WHERE nastup = ?";
                        if ($stmt_zam = $conn->prepare($sql_zam)) {
                            $stmt_zam->bind_param("i", $id_zastavky);
                            $stmt_zam->execute();
                            $stmt_zam->close();
                        }

                        // 3) Smazání samotné zastávky
                        $sql_nastupy = "DELETE FROM nastupy WHERE id = ?";
                        if ($stmt_nastupy = $conn->prepare($sql_nastupy)) {
                            $stmt_nastupy->bind_param("i", $id_zastavky);
                            if ($stmt_nastupy->execute()) {
                                echo '<div class="alert alert-success text-center">Zastávka byla úspěšně smazána, všechny trasy a nastupy byly aktualizovány.</div>';
                            } else {
                                echo '<div class="alert alert-danger text-center">Chyba při mazání zastávky.</div>';
                            }
                            $stmt_nastupy->close();
                        } else {
                            echo '<div class="alert alert-danger text-center">Chyba při přípravě dotazu.</div>';
                        }

                    } else {
                        echo '<div class="alert alert-warning text-center">Nevybrali jste zastávku k odstranění.</div>';
                    }

                    // Přesměrování zpět na stránku po 2 sekundách
                    echo '<meta http-equiv="refresh" content="2;url=vozovypark.php">';
                }
                
            }
            else
            {   ?>

               <h3 class="text-center m-2 p-2">Přehled vozového parku</h3>

                <?php if (in_array($_SESSION["typ"] ?? '', ['5'])): ?>
                    <div class="text-center my-2">
                        <button type="button" class="btn btn-outline-primary btn-lg"
                                onclick="loadModalContent('<?= $radek['id'] ?>', '1', 'zastavky_overview_modal', '#ModalZastavkyOverview');">
                            🚌 Přehled zastávek
                        </button>
                    </div>
                <?php endif; ?>

                <div class="container">
                    <div class="row justify-content-md-center">
                        <div class="col-md-12">

                            <div class="table-responsive-lg text-center mt-3">
                                <table class="table table-hover">
                                    <thead>
                                        <tr class="table-active">
                                            <th scope="col">ID</th>
                                            <th scope="col">Trasa</th>
                                            <th scope="col">Označení prostředku</th>
                                            <?php if (in_array($_SESSION["typ"], ['1','4','5'])): ?>
                                                <th scope="col">Zastávky</th>
                                                <th scope="col">Editace</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT id, spz, oznaceni FROM auta ORDER BY id";
                                        $vysledek = mysqli_query($conn, $sql) or die("Nelze provést dotaz");

                                        $cislo = 1;
                                        while ($radek = mysqli_fetch_assoc($vysledek)):
                                        ?>
                                            <tr>
                                                <td class="text-center fw-bold"><?= $cislo ?></td>
                                                <td class="text-center"><?= htmlspecialchars($radek["spz"]) ?></td>
                                                <td><?= htmlspecialchars($radek["oznaceni"]) ?></td>

                                                <?php if (in_array($_SESSION["typ"], ['1','4','5'])): ?>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                                                onclick="loadModalContent('<?= $radek['id'] ?>', '1', 'zastavky_modal', '#ModalZastavky');">
                                                            ✏️ ZASTÁVKY
                                                        </button>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                                                onclick="loadModalContent('<?= $radek['id'] ?>', '1', 'nove_auto', '#ModalAutoInfo<?= $radek['id'] ?>');">
                                                            🚗 AUTO
                                                        </button>
                                                    </td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php
                                            $cislo++;
                                        endwhile;
                                        mysqli_free_result($vysledek);
                                        ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>

                <div id="modalContent"></div>

                <script>
                function loadModalContent(modalId, modalId2, functionName, modalName) 
                {
                    var ID = modalId; // Získání ID modálního okna z modalId
                    var ID2 = modalId2; // Získání ID modálního okna z modalId

                    $.ajax({
                        url: 'funkce.php', // Cesta k externímu skriptu
                        type: 'GET',
                        data: { functionName2: functionName, ID: ID, ID2: ID2 }, // Předání funkce a ID
                        success: function(response) {
                            // Přidat nový obsah
                            $('#modalContent').html(response);
                            // Otevřít modal
                            $(modalName).modal('show');
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                        }
                    });
                }
                </script>

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>


</body>
</html>