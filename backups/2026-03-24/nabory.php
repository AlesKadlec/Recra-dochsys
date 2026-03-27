<?php
include('db.php');
include('funkce.php');
if (isset($_GET['akce']) && $_GET['akce'] === 'kontrola_duplicit') {
    $prijmeni = $_GET['prijmeni'] ?? '';
    $doklad = $_GET['doklad'] ?? ''; // nově kontrolujeme doklad
    $id = $_GET['id'] ?? null;

    if (kontrola_naboru($prijmeni, $doklad, $id, 'doklad')) { // předáváme typ kontroly
        echo "duplicita";
    } else {
        echo "ok";
    }
    exit; // velmi důležité – zastaví vykreslení HTML
}
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
    
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js"></script>

    <!-- Datepicker JS a lokalizace -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.cs.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

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
                (prijmeni, jmeno, telefon, adresa, dat_narozeni, stat, dat_evidence, zdroj_inzerce, pozice, klient, klient2, souhlas, rekruter, vysledek, nastup, vystup, koordinator, duvod_ukonceni, poznamka, doklad)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "ssssssssssssssssssss",
                $_POST["prijmeni"], $_POST["jmeno"], $_POST["telefon"], $_POST["adresa"],
                date_format($date1, "Y-m-d"),
                $_POST["stat"],
                date_format($date2, "Y-m-d"),
                $_POST["zdroj"], $_POST["pozice"], $_POST["klient"], $_POST["klient2"],
                $_POST["souhlas"], $_POST["rekruter"], $_POST["vysledek"],
                date_format($date3, "Y-m-d"), date_format($date4, "Y-m-d"),
                $_POST["koordinator"], $_POST["duvod_ukonceni"], $_POST["poznamka"], $_POST["doklad"]
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
                nastup=?, vystup=?, koordinator=?, duvod_ukonceni=?, poznamka=?, doklad=? 
                WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "ssssssssssssssssssssi",
                $_POST["prijmeni"], $_POST["jmeno"], $_POST["telefon"], $_POST["adresa"],
                date_format($date1,"Y-m-d"),
                $_POST["stat"],
                date_format($date2,"Y-m-d"),
                $_POST["zdroj"], $_POST["pozice"], $_POST["klient"], $_POST["klient2"],
                $_POST["souhlas"], $_POST["rekruter"], $_POST["vysledek"],
                date_format($date3,"Y-m-d"), date_format($date4,"Y-m-d"),
                $_POST["koordinator"], $_POST["duvod_ukonceni"], $_POST["poznamka"],$_POST["doklad"],
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
                        <table id="naboryTable" class='table table-hover table-striped table-sm align-middle' style="width:100%">

                            <thead>
                                <tr class='table-active'>
                                    <th>#</th>
                                    <th></th>
                                    <th class='text-center'>Příjmení</th>
                                    <th class='text-centert'>Jméno</th>
                                    <th class='text-centert'>Telefon</th>
                                    <th class='text-centert'>Datum evidence</th>
                                    <th class='text-centert'>Vstup</th>
                                    <th class='text-centert'>Výstup</th>
                                    <th class='text-centert'>Klient</th>
                                    <th class='text-centert'>Rekrutér</th>
                                    <th class='text-centert'>Výsledek</th>
                                    <th class='text-centert'>Poznámka</th>
                                    <th class='text-centert'></th>
                                </tr>
                                <!-- ✅ druhý řádek s inputy -->
                                <tr>
                                    <th><input type="text" class="form-control form-control-sm" placeholder="#"></th>
                                    <th></th>
                                    <th><input type="text" class="form-control form-control-sm" placeholder="Příjmení"></th>
                                    <th><input type="text" class="form-control form-control-sm" placeholder="Jméno"></th>
                                    <th><input type="text" class="form-control form-control-sm" placeholder="Telefon"></th>
                                    <th>
                                        <div class="d-flex align-items-center">
                                            <input type="text" id="min" name="min" class="form-control form-control-sm me-2" placeholder="Ev. od" readonly>
                                            <input type="text" id="max" name="max" class="form-control form-control-sm" placeholder="Ev. do" readonly>
                                        </div>
                                    </th>
                                    <th><input type="text" id="datumVstup" class="form-control form-control-sm" placeholder="Vstup" readonly></th>
                                    <th><input type="text" id="datumVystup" class="form-control form-control-sm" placeholder="Výstup" readonly></th>
                                    <th><input type="text" class="form-control form-control-sm" placeholder="Klient"></th>
                                    <th><input type="text" class="form-control form-control-sm" placeholder="Rekrutér"></th>
                                    <th><input type="text" class="form-control form-control-sm" placeholder="Výsledek"></th>
                                    <th><input type="text" class="form-control form-control-sm" placeholder="Poznámka"></th>
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
                                        <td class='text-start' width="30"><img src="vlajky/<?php echo strtolower($radek["stat"]);?>.png" class="img-thumbnail" width="30"></td>
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

        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>


        <script>
        $(document).ready(function() {
        var table = $('#naboryTable').DataTable({
            dom: "<'row mb-2'<'col-auto'B><'col-auto'l><'col'f><'col'p>>" +  // horní řádek: tlačítka, select, filtr
             "<'row'<'col'tr>>" +                                        // tabulka
             "<'row mt-2'<'col'i><'col'p>>",                             // spodní řádek
            pageLength: 50,
            scrollX: true,
            order: [[2, 'asc']],
            columnDefs: [
                { orderable: true, targets: [0,2,3,4,5,6,7,8,9,10,11] }, // povolit řazení jen pro nadpisy
                { orderable: false, targets: [1,12] }               // neřadit pro ostatní
                ],
            buttons: [
            { extend: 'excelHtml5', text: 'Excel', className: 'btn btn-primary' },
            { extend: 'pdfHtml5', text: 'PDF', className: 'btn btn-primary', orientation: 'landscape', pageSize: 'A4' },
            { extend: 'print', text: 'Tisk', className: 'btn btn-primary' },
            { 
                text: 'Nový záznam', 
                className: 'btn btn-success btn-sm', 
                action: function () { $('#nabor_new').modal('show'); } 
            },
            {
                text: 'Reset filtrů',
                action: function ( e, dt, node, config ) {
                    console.log("🔹 Reset tlačítko stisknuto");

                    // 1️⃣ vymazat všechny inputy ve viditelném scrollovaném theadu
                    $('#naboryTable_wrapper .dataTables_scrollHead tr:eq(1) input').each(function(){
                        $(this).val('');
                        $(this).trigger('keyup'); // spustí standardní filtr
                    });

                    // 2️⃣ reset interních filtrů DataTables
                    dt.search('').columns().search('');

                    // 3️⃣ vymazat min/max inputy
                    $('#min, #max').val('');

                    // 4️⃣ překreslit tabulku
                    dt.draw();

                    console.log("🔹 Reset hotov");
                }
            }
            ],
            language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/cs.json'
        }
        });

        // Odpojit řazení z druhého řádku (filtry)
        $('#naboryTable_wrapper .dataTables_scrollHead tr:eq(1) th').css('pointer-events','none');


        // Standardní filtr po sloupcích
        table.columns().every(function () {
            var that = this;
            $('input', this.header())
                .on('keyup change clear', function () {
                    if (that.search() !== this.value) {
                        that.search(this.value).draw();
                    }
                })
                .on('click', function(e) { e.stopPropagation(); });
        });

        $('#min, #max, #datumVstup, #datumVystup').datepicker({
            format: 'dd.mm.yyyy',
            autoclose: true,
            todayHighlight: true,
            language: 'cs',
            orientation: "bottom", // zobrazovat pod polem
            zIndexOffset: 9999,     // zajistí, že kalendář nebude překryt záhlavím
            clearBtn: true // ✅ přidá tlačítko pro vymazání

        });


        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var rowId = data[0];       // ID z prvního sloupce
            var surname = data[2];     // Příjmení z druhého sloupce

            var vstup = data[5];       // sloupec "Datum evidence"
            if (!vstup) vstup = '01.01.1900'; // pokud prázdné, použij default pro test

            var vstupParts = vstup.split('.');
            var rowDate = new Date(vstupParts[2], vstupParts[1]-1, vstupParts[0]);

            var minVal = $('#min').val() || '01.01.1900';
            var maxVal = $('#max').val() || '31.12.2099';

            var minParts = minVal.split('.');
            var maxParts = maxVal.split('.');

            var minDate = new Date(minParts[2], minParts[1]-1, minParts[0]);
            var maxDate = new Date(maxParts[2], maxParts[1]-1, maxParts[0]);

            var pass = rowDate >= minDate && rowDate <= maxDate;

            return pass; // filtr podle min/max
        });

            // redraw tabulky při změně inputů
            $('#min, #max').on('changeDate change', function() {
                table.search('').columns().search('').draw(); // reset filtrů
                table.draw(); // znovu aplikovat filtr podle min/max
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

        $(document).ready(function () {

            function kontrolaDuplicit() {
                let prijmeni = $("#prijmeni").val();
                let doklad = $("#doklad").val(); // nově kontrolujeme doklad
                let id = $("#id").val(); // pokud existuje hidden input pro ID

                console.log("AJAX data:", { prijmeni, doklad, id });

                // pokud není dostatečně vyplněno, nic nekontrolujeme
                if (prijmeni.length < 2 || doklad.length < 1) {
                    $("#dupInfo").hide();
                    $("#prijmeni, #doklad").css("border", "");
                    $(".btn-primary").prop("disabled", false);
                    return;
                }

                $.ajax({
                    url: "nabory.php",
                    method: "GET",
                    data: {
                        akce: "kontrola_duplicit",
                        prijmeni: prijmeni,
                        doklad: doklad, // posíláme místo dat_narozeni
                        id: id
                    },
                    success: function(resp) {
                        resp = resp.trim();
                        console.log("Výsledek testu duplicit:", resp);

                        if (resp === "duplicita") {
                            $("#dupInfo")
                                .text("Duplicitní záznam!")
                                .css({
                                    "display": "block",
                                    "background-color": "#f8d7da",
                                    "color": "#721c24",
                                    "font-weight": "bold",
                                    "padding": "5px 10px",
                                    "border-radius": "4px",
                                    "border": "1px solid #f5c6cb",
                                    "margin-top": "5px"
                                });

                            // zvýraznění polí
                            $("#prijmeni, #doklad").css("border", "2px solid red");

                            // zakázání tlačítka
                            $(".btn-primary").prop("disabled", true);
                        } else {
                            $("#dupInfo").hide();
                            $("#prijmeni, #doklad").css("border", "");
                            $(".btn-primary").prop("disabled", false);
                        }
                    }
                });
            }

            // spouštíme kontrolu při změně polí
            $("#prijmeni, #doklad").on("keyup change", kontrolaDuplicit);

        });


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
