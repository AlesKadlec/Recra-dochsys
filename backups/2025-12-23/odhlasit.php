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
    <title>ODHLÁŠENÍ</title>
</head>
<body>
<?php

    global $conn;
    $now = new DateTime('now', new DateTimeZone('Europe/Prague'));

    if (!($vysledek = mysqli_query($conn, 
    "insert into logs (kdo,typ,infotext,datumcas) values ('" . $_SESSION["log_id"] . "','Log Out','Odhlásil se uživatel " . $_SESSION["log_name"] . "','" . $now->format('Y-m-d H:i:s') . "')")))
    {
    die("Nelze provést dotaz.</body></html>");
    }   

    session_unset();
    session_destroy();
    
?>

<h2 class="text-center m-2 p-2">ODHLÁŠENÍ</h2>
<p class="text-center m-2 p-2">Za 5 s budete přesměrování na přihlašovací obrazovku</p>

<meta http-equiv="refresh" content="5;url=login.php"> 
<script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>