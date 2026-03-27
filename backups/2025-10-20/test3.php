<?php
include('db.php');
include ('funkce.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tabulka s modálním oknem</title>
<!-- Odkazy na knihovny Bootstrap 5.3 pro stylování a jQuery pro manipulaci s DOM -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<div class="container">
    <h2>Tabulka s tlačítky</h2>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Název</th>
                <th>Popis</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Položka 1</td>
                <td>Popis položky 1</td>
                <td><button class="btn btn-primary" onclick="loadModalContent('modalContent1')">Zobrazit detail</button></td>
            </tr>
            <tr>
                <td>2</td>
                <td>Položka 2</td>
                <td>Popis položky 2</td>
                <td><button class="btn btn-primary" onclick="loadModalContent('modalContent2')">Zobrazit detail</button></td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Prázdné modální okno, které se naplní obsahem až po kliknutí na tlačítko -->
<div class="modal fade" id="dynamicModal" tabindex="-1" aria-labelledby="dynamicModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dynamicModalLabel">Detail položky</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavřít</button>
            </div>
        </div>
    </div>
</div>


<script>
    function loadModalContent(modalId) {
        var functionName = 'test_modal'; // Název funkce, kterou chcete volat
        var ID = modalId.substring(12); // Získání ID modálního okna z modalId

        $.ajax({
            url: 'funkce.php', // Cesta k externímu skriptu
            type: 'GET',
            data: { functionName: functionName, ID: ID }, // Předání funkce a ID
            success: function(response) {
                $('#modalContent').html(response);
                $('#dynamicModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }
</script>

</body>
</html>
