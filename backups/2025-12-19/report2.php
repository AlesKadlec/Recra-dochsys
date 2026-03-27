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

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap-datepicker CSS (verze 1.9.0) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.4.0/css/fixedHeader.bootstrap5.min.css">

    <title>Report dopravy</title>

</head>
<body>

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

        // aktuální týden a rok
        $tyden = isset($_GET['tyden']) ? (int)$_GET['tyden'] : date('W');
        $rok = isset($_GET['rok']) ? (int)$_GET['rok'] : date('Y');       

        // předchozí týden
        $predchoziTyden = $tyden - 1;
        $predchoziRok = $rok;
        if ($predchoziTyden < 1) {
            $predchoziRok--;
            $predchoziTyden = (int) date('W', strtotime("$predchoziRok-12-28"));
        }

        // následující týden
        $nasledujiciTyden = $tyden + 1;
        $nasledujiciRok = $rok;
        if ($nasledujiciTyden > date('W', strtotime("$rok-12-28"))) {
            $nasledujiciRok++;
            $nasledujiciTyden = 1;
        }

        // aktuální týden (dnes)
        $aktualniTyden = date('W');
        $aktualniRok = date('Y');

        $zobrazitTlacitko = false;
        if ($rok > $aktualniRok || ($rok == $aktualniRok && $tyden >= $aktualniTyden)) {
            $zobrazitTlacitko = true;
        }

        // vytvoříme objekt DateTime podle ISO týdne
        $datum = new DateTime();
        $datum->setISODate($rok, $tyden); // pondělí daného týdne

        $datum = $datum->format('Y-m-d'); // vypíše např. 2025-11-03
     
        // výchozí směna
        $smena = isset($_GET['smena']) && in_array($_GET['smena'], ['R','O','N', 'CH']) ? $_GET['smena'] : 'R';
        ?>

        <div class="container-fluid mt-3">
            <div class="d-flex justify-content-between align-items-center">

                <!-- Levá část: tlačítka pro navigaci -->
                <div class="d-flex align-items-center gap-2">

                    <!-- Šipka předchozí týden -->
                    <a href="?rok=<?= $predchoziRok ?>&tyden=<?= $predchoziTyden ?>&smena=<?= $smena ?>" class="btn btn-sm btn-outline-primary">◀</a>

                    <!-- Tlačítka R, O, N -->
                    <?php
                    $smenyButtons = ['R', 'O', 'N' , 'CH'];
                    foreach ($smenyButtons as $s):
                        $activeClass = ($smena === $s) ? 'btn-primary' : 'btn-outline-secondary';
                    ?>
                        <a href="?rok=<?= $rok ?>&tyden=<?= $tyden ?>&smena=<?= $s ?>" class="btn btn-sm <?= $activeClass ?>"><?= $s ?></a>
                    <?php endforeach; ?>

                    <!-- Šipka následující týden -->
                    <a href="?rok=<?= $nasledujiciRok ?>&tyden=<?= $nasledujiciTyden ?>&smena=<?= $smena ?>" class="btn btn-sm btn-outline-primary">▶</a>

                    <!-- Tlačítko aktuální týden -->
                    <?php if ($tyden != $aktualniTyden || $rok != $aktualniRok): ?>
                        <a href="?rok=<?= $aktualniRok ?>&tyden=<?= $aktualniTyden ?>&smena=<?= $smena ?>" class="btn btn-sm btn-outline-secondary">🕒 Aktuální týden</a>
                    <?php endif; ?>
                </div>

                <!-- Střední část: nadpis -->
                <div class="text-center">
                    <h1 class="m-0 p-0">PLÁN DOPRAVY na týden <?= htmlspecialchars($tyden . "/" . $rok) ?></h1>
                </div>

                <!-- Pravá část: může být prázdná -->
                <div class="text-center"></div>

            </div>
        </div>


        <div id="tableScrollContainer" class="container-fluid mt-3">
            <table id="planTable" class="table table-hover table-striped table-sm align-middle"
                style="table-layout: fixed; width:100%; font-size: 0.8rem; line-height: 1;
                    --bs-table-cell-padding-y: .1rem; --bs-table-cell-padding-x: .3rem;">

                <thead style="position: sticky; top: 0; z-index: 100; background-color: #f8f9fa;">
                    <!-- Hlavní záhlaví -->
                    <tr class="text-center align-middle table-active" style="font-weight:700; font-size:14px;">
                        <th>Jméno</th>
                        <th>Příjmení</th>
                        <th>Firma</th>
                        <th>Cílová</th>
                        <th>Zastávka</th>
                        <th>SPZ</th>
                    </tr>

                    <!-- Vyhledávací řádek -->
                    <tr class="text-center">
                        <th><input type="text" placeholder="Hledat..." style="width:100%;" class="form-control form-control-sm" /></th>
                        <th><input type="text" placeholder="Hledat..." style="width:100%;" class="form-control form-control-sm"/></th>
                        <th><input type="text" placeholder="Hledat..." style="width:100%;" class="form-control form-control-sm"/></th>
                        <th><input type="text" placeholder="Hledat..." style="width:100%;" class="form-control form-control-sm"/></th>
                        <th><input type="text" placeholder="Hledat..." style="width:100%;" class="form-control form-control-sm"/></th>
                        <th><input type="text" placeholder="Hledat..." style="width:100%;" class="form-control form-control-sm"/></th>
                    </tr>
                </thead>

                <tbody>
                <?php
                $smenaFilter = $_GET['smena'] ?? 'R';

                // První den týdne (pondělí)
                $zacatekTydne = new DateTime();
                $zacatekTydne->setISODate($rok, $tyden, 1); // pondělí
                $zacatekTydneStr = $zacatekTydne->format('Y-m-d');

                // Poslední den týdne (neděle)
                $konecTydne = clone $zacatekTydne;
                $konecTydne->modify('+6 days');
                $konecTydneStr = $konecTydne->format('Y-m-d');

                if ($smenaFilter === 'CH') {
                     // Zaměstnanci bez záznamu ve plan_smen pro daný rok a týden
                    $sql = "SELECT z.jmeno, z.prijmeni, f.firma, z.cilova, za.zastavka, a.spz
                            FROM zamestnanci z
                            LEFT JOIN plan_smen p ON z.id = p.jmeno AND p.rok = ? AND p.tyden = ?
                            LEFT JOIN firmy f ON z.firma = f.id
                            LEFT JOIN zastavky za ON z.nastup = za.id
                            LEFT JOIN auta a ON za.auto = a.id
                            WHERE p.jmeno IS NULL
                            AND DATE(z.vstup) <= ?
                            AND (z.vystup = '0000-00-00' OR DATE(z.vystup) >= ?)
                            ORDER BY z.prijmeni";

                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iiss", $rok, $tyden, $konecTydneStr, $zacatekTydneStr);
                } 
                else 
                {
                    // Standardní filtr podle směny
                    $sql = "SELECT z.jmeno, z.prijmeni, f.firma, z.cilova, za.zastavka, a.spz
                            FROM plan_smen p
                            LEFT JOIN zamestnanci z ON p.jmeno = z.id
                            LEFT JOIN firmy f ON z.firma = f.id
                            LEFT JOIN zastavky za ON z.nastup = za.id
                            LEFT JOIN auta a ON za.auto = a.id
                            WHERE p.rok = ? AND p.tyden = ? AND p.smena = ?";
                            
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iis", $rok, $tyden, $smenaFilter);
                }

                $stmt->execute();
                $stmt->bind_result($jmeno, $prijmeni, $firma, $cilova, $zastavka, $spz);

                while ($stmt->fetch()):
                ?>
                    <tr class="text-center">
                        <td class='text-start'><?= htmlspecialchars($jmeno ?? '') ?></td>
                        <td class='text-start'><?= htmlspecialchars($prijmeni ?? '') ?></td>
                        <td class='text-start'><?= htmlspecialchars($firma ?? '') ?></td>
                        <td class='text-start'><?= htmlspecialchars($cilova ?? '') ?></td>
                        <td class='text-start'><?= htmlspecialchars($zastavka ?? '') ?></td>
                        <td class='text-start'><?= htmlspecialchars($spz ?? '') ?></td>
                    </tr>
                <?php
                endwhile;

                $stmt->close();
                ?>
                </tbody>
            </table>
        </div>
        
        <!-- jQuery (musí být první) -->
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

        <!-- Popper a Bootstrap 5 JS -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js"></script>

        <!-- Bootstrap-datepicker JS + český locale (verze 1.9.0) -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.cs.min.js"></script>

        <!-- DataTables JS -->
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.datatables.net/fixedheader/3.4.0/js/dataTables.fixedHeader.min.js"></script>

        <!-- DataTables Buttons plugin -->
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>

        <script>
        $(document).ready(function() {
            var table = $('#planTable').DataTable({
                paging: false,           // vypnutí stránkování
                info: true,             // skrytí info řádku
                order: [[1, "asc"]],
                orderCellsTop: true,
                fixedHeader: {
                    header: true,
                    headerOffset: 60
                },
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/cs.json"
                },
                dom: "<'row mb-2'<'col-auto'B><'col-auto'f>>" +  // tlačítka vlevo, filtr vpravo
                    "<'row'<'col'i>>" +                         // info o počtu záznamů
                    "<'row'<'col'tr>>",                           // samotná tabulka
                columns: [
                    { width: "10%" }, // Jméno
                    { width: "10%" }, // Příjmení
                    { width: "15%" }, // Firma
                    { width: "15%" }, // Cílová
                    { width: "35%" }, // Zastávka – nejsirší
                    { width: "15%" }  // SPZ
                ],
                buttons: [
                    { extend: 'excelHtml5', text: 'Excel', className: 'btn btn-primary btn-sm' },
                    { extend: 'pdfHtml5', text: 'PDF', orientation: 'landscape', pageSize: 'A4', className: 'btn btn-primary btn-sm' },
                    { extend: 'print', text: 'Tisk', className: 'btn btn-primary btn-sm' },
                    { extend: 'colvis', text: 'Sloupce', className: 'btn btn-primary btn-sm' },
                    {
                        text: 'Reset filtrů',
                        className: 'btn btn-primary btn-sm',
                        action: function ( e, dt, node, config ) {
                            // Vymažeme všechna input pole ve druhém řádku thead (filtry jednotlivých sloupců)
                            $('#planTable thead tr:eq(1) th input').val('');
                            // Reset globálního vyhledávání
                            dt.search('').columns().search('').draw();
                        }
                    }
                ]
            });

            // aktivace filtrů pro každý sloupec
            $('#planTable thead tr:eq(1) th').each(function(i) {
                $('input', this).on('keyup change', function() {
                    if (table.column(i).search() !== this.value) {
                        table.column(i).search(this.value).draw();
                    }
                });
            });
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


