<?php
include('db.php');
include('funkce.php');
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
    <title>NÁBORY</title>
</head>
<body>

<?php
global $conn;
menu();

if (kontrola_prihlaseni() == "OK") {

    // Uložení nebo aktualizace náboru
    if (isset($_GET["typ"])) {
        if (($_GET["typ"] == "savenabor" && in_array($_SESSION["typ"], [1,4,5,6]))) {
            $date1 = date_create($_POST["dat_narozeni"]);
            $date2 = date_create($_POST["dat_evidence"]);
            $date3 = ($_POST["dat_nastup"] == '') ? date_create('0000-00-00') : date_create($_POST["dat_nastup"]);
            $date4 = ($_POST["dat_vystup"] == '') ? date_create('0000-00-00') : date_create($_POST["dat_vystup"]);

            $sql = "INSERT INTO nabory 
                (prijmeni, jmeno, telefon, adresa, dat_narozeni, stat, dat_evidence, zdroj_inzerce, pozice, klient, klient2, souhlas, rekruter, vysledek, nastup, vystup, koordinator, duvod_ukonceni, poznamka)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "sssssssssssssssssss",
                $_POST["prijmeni"], $_POST["jmeno"], $_POST["telefon"], $_POST["adresa"],
                date_format($date1, "Y-m-d"),
                $_POST["stat"],
                date_format($date2, "Y-m-d"),
                $_POST["zdroj"], $_POST["pozice"], $_POST["klient"], $_POST["klient2"],
                $_POST["souhlas"], $_POST["rekruter"], $_POST["vysledek"],
                date_format($date3, "Y-m-d"), date_format($date4, "Y-m-d"),
                $_POST["koordinator"], $_POST["duvod_ukonceni"], $_POST["poznamka"]
            );
            $stmt->execute();
            $stmt->close();

            // Log
            $now = new DateTime('now', new DateTimeZone('Europe/Prague'));
            $infotext = "Vytvořen nový nábor " . $_POST["prijmeni"] . " " . $_POST["jmeno"] . " - " . date_format($date2,"Y-m-d");
            $datumcas = $now->format('Y-m-d H:i:s');
            $log_sql = "INSERT INTO logs (kdo, typ, infotext, datumcas) VALUES (?, 'Nový nábor', ?, ?)";
            $log_stmt = $conn->prepare($log_sql);
            $log_stmt->bind_param("iss", $_SESSION["log_id"], $infotext, $datumcas);
            $log_stmt->execute();
            $log_stmt->close();

            echo '<meta http-equiv="refresh" content="0;url=nabory.php">';
        } 
        elseif (($_GET["typ"] == "updatenabor" && in_array($_SESSION["typ"], [1,4,5,6]))) {
            $date1 = date_create($_POST["dat_narozeni"]);
            $date2 = date_create($_POST["dat_evidence"]);
            $date3 = ($_POST["dat_nastup"] == '') ? date_create('0000-00-00') : date_create($_POST["dat_nastup"]);
            $date4 = ($_POST["dat_vystup"] == '') ? date_create('0000-00-00') : date_create($_POST["dat_vystup"]);

            $sql = "UPDATE nabory SET 
                prijmeni=?, jmeno=?, telefon=?, adresa=?, dat_narozeni=?, stat=?, dat_evidence=?, 
                zdroj_inzerce=?, pozice=?, klient=?, klient2=?, souhlas=?, rekruter=?, vysledek=?, 
                nastup=?, vystup=?, koordinator=?, duvod_ukonceni=?, poznamka=? 
                WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "sssssssssssssssssssi",
                $_POST["prijmeni"], $_POST["jmeno"], $_POST["telefon"], $_POST["adresa"],
                date_format($date1,"Y-m-d"),
                $_POST["stat"],
                date_format($date2,"Y-m-d"),
                $_POST["zdroj"], $_POST["pozice"], $_POST["klient"], $_POST["klient2"],
                $_POST["souhlas"], $_POST["rekruter"], $_POST["vysledek"],
                date_format($date3,"Y-m-d"), date_format($date4,"Y-m-d"),
                $_POST["koordinator"], $_POST["duvod_ukonceni"], $_POST["poznamka"],
                $_POST["radek_v_db"]
            );
            $stmt->execute();
            $stmt->close();

            // Log
            $now = new DateTime('now', new DateTimeZone('Europe/Prague'));
            $infotext = "Nábor upraven u " . $_POST["prijmeni"] . " " . $_POST["jmeno"] . " - " . date_format($date2,"Y-m-d");
            $datumcas = $now->format('Y-m-d H:i:s');
            $log_sql = "INSERT INTO logs (kdo, typ, infotext, datumcas) VALUES (?, 'Aktualizován nábor', ?, ?)";
            $log_stmt = $conn->prepare($log_sql);
            $log_stmt->bind_param("iss", $_SESSION["log_id"], $infotext, $datumcas);
            $log_stmt->execute();
            $log_stmt->close();

            echo '<meta http-equiv="refresh" content="0;url=nabory.php">';
        }
    }
    else { 
        // Zobrazení tabulky
        ?>
        <h3 class="text-center mt-3">Nábory</h3> 

        <div class="container-fluid mt-2">

            <div class="row justify-content-md-center">
                <div class="col col-md-12">
                    <div class='table-responsive-lg text-center'>
                        <table id="naboryTable" class='table table-hover display nowrap' style="width:100%">
                            <thead>
                                <tr class='table-active'>
                                    <th>#</th>
                                    <th></th>
                                    <th class='text-start'>Příjmení</th>
                                    <th class='text-start'>Jméno</th>
                                    <th>Telefon</th>
                                    <th>Datum evidence</th>
                                    <th>Vstup</th>
                                    <th>Výstup</th>
                                    <th>Klient</th>
                                    <th>Rekrutér</th>
                                    <th>Výsledek</th>
                                    <th>Poznámka</th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th><input type="text" placeholder="#" style="width:100%"></th>
                                    <th></th>
                                    <th><input type="text" placeholder="Příjmení" style="width:100%"></th>
                                    <th><input type="text" placeholder="Jméno" style="width:100%"></th>
                                    <th><input type="text" placeholder="Telefon" style="width:100%"></th>
                                    <th><input type="text" placeholder="Datum evidence" style="width:100%"></th>
                                    <th><input type="text" placeholder="Vstup" style="width:100%"></th>
                                    <th><input type="text" placeholder="Výstup" style="width:100%"></th>
                                    <th><input type="text" placeholder="Klient" style="width:100%"></th>
                                    <th><input type="text" placeholder="Rekrutér" style="width:100%"></th>
                                    <th><input type="text" placeholder="Výsledek" style="width:100%"></th>
                                    <th><input type="text" placeholder="Poznámka" style="width:100%"></th>
                                    <th></th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php  
                                $cislo = 1;               
                                $sql = "SELECT id,prijmeni,jmeno,telefon,adresa,dat_narozeni,stat,dat_evidence,zdroj_inzerce,pozice,klient,klient2,souhlas,rekruter,vysledek,nastup,vystup,koordinator,poznamka 
                                        FROM nabory 
                                        ORDER BY prijmeni ASC";
                                $vysledek = mysqli_query($conn, $sql);
                                while ($radek = mysqli_fetch_array($vysledek)) { ?>
                                    <tr>
                                        <td class='text-center fw-bold'><?php echo $cislo;?></td>
                                        <td class='text-start' width="50"><img src="vlajky/<?php echo strtolower($radek["stat"]);?>.png" class="img-thumbnail" width="50"></td>
                                        <td class='text-start'><?php echo $radek["prijmeni"];?></td>
                                        <td class='text-start'><?php echo $radek["jmeno"];?></td>
                                        <td class='text-start'><?php echo $radek["telefon"];?></td>
                                        <td class='text-center'><?php echo prevod_data($radek["dat_evidence"],1);?></td>
                                        <td class='text-center'><?php echo prevod_data($radek["nastup"],1);?></td>
                                        <td class='text-center'><?php echo prevod_data($radek["vystup"],1);?></td>
                                        <td class='text-center'><?php echo $radek["klient"];?><?php echo ($radek['klient2'] <> '') ? " / " . $radek['klient2'] : "";?></td>
                                        <td class='text-center'><?php echo $radek["rekruter"];?></td>
                                        <td class='text-center'><?php echo $radek["vysledek"];?></td>
                                        <td class='text-center'><?php echo $radek["poznamka"];?></td>
                                        <td class='text-start' width="50">
                                            <?php if (($radek['vysledek'] != "Nastoupil") && ($radek['vysledek'] != "Zamítnut")) { ?>
                                                <button type="button" class="btn btn-sm align-middle" data-bs-toggle='modal' onclick="loadModalContent2('<?php echo $radek['id']; ?>')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-person-vcard" viewBox="0 0 16 16">
                                                        <path d="M5 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4m4-2.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5M9 8a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4A.5.5 0 0 1 9 8m1 2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5"/>
                                                        <path d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM1 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H8.96q.04-.245.04-.5C9 10.567 7.21 9 5 9c-2.086 0-3.8 1.398-3.984 3.181A1 1 0 0 1 1 12z"/>
                                                    </svg>
                                                </button>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php $cislo++; } mysqli_free_result($vysledek); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.cs.min.js"></script>

        <script>
        $(document).ready(function() {
            var table = $('#naboryTable').DataTable({
                dom: 'Bfrtip',
                pageLength: 50,
                buttons: [
                    { extend: 'copy', text: 'Kopírovat' },
                    { extend: 'excel', text: 'Export do Excelu' },
                    { extend: 'pdf', text: 'Export do PDF' },
                    { extend: 'print', text: 'Tisk' }, 
                    {
                        text: 'Nový záznam',
                        className: 'btn btn-success',
                        attr: {
                            style: 'margin-top: 2px; vertical-align: middle;'
                        },
                        action: function () {
                            $('#nabor_new').modal('show');
                        }
                    }
                ],
                scrollX: true,
                order: [[2, 'asc']],
                columnDefs: [
                    { orderable: false, targets: [0,1,12] }
                ],
                language: {
                    search: "Vyhledávání:"
                }
            });

            // Vyhledávání po sloupcích v hlavičce
            table.columns().every(function () {
                var that = this;
                $('input', this.header())
                    .on('keyup change clear', function () {
                        if (that.search() !== this.value) {
                            that.search(this.value).draw();
                        }
                    })
                    .on('click', function(e) {
                        e.stopPropagation(); // kliknutí do inputu nemění řazení
                    });
            });
        });

        function loadModalContent2(modalId) {
            $.ajax({
                url: 'funkce.php',
                type: 'GET',
                data: { functionName: 'novy_nabor', ID: modalId },
                success: function(response) {
                    $('#modalContent').html(response);
                    $('#ModalNaborInfo').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }
        </script>

        <div id="modalContent"></div>

        <?php novy_nabor(); ?>

    <?php
    }
} else { ?>
    <h1 class="text-center m-2 p-2">NEAUTORIZOVANÝ PŘÍSTUP</h1>
    <meta http-equiv="refresh" content="5;url=login.php">
<?php } ?>
</body>
</html>
