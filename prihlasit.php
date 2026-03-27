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
    <title>PŘIHLÁŠENÍ</title>
</head>
<body>

<?php require_once 'init.php'; ?>

<?php

global $conn;
    
$sql = "SELECT id,autobus,firma,typ,email FROM uzivatele where uzivatel = '" . $_POST['name'] . "' and heslo='" . hash('md5',$_POST['password']) . "' and aktivni='1'";

$text = '';

//echo $sql;

if (!($vysledek = mysqli_query($conn, $sql)))
{
die("Nelze provést dotaz</body></html>");
} 

while ($radek = mysqli_fetch_array($vysledek))
{    
    $_SESSION['LAST'] = time();
    $_SESSION["log_id"] = $radek["id"];
    //$_SESSION["autobus"] = $radek["autobus"];
    $_SESSION["autobus"] = "";
    $_SESSION["log_name"] = $_POST['name'];
    $_SESSION["firma"] = $radek['firma'];
    $_SESSION["typ"] = $radek['typ'];
    $_SESSION["email"] = $radek['email'];

    if ($radek['typ'] == 1)
    {
        $_SESSION["typ_uctu"] = "koordinátor";
    }
    elseif ($radek['typ'] == 2)
    {
        $_SESSION["typ_uctu"] = "řidič";
    }
    elseif ($radek['typ'] == 3)
    {
        $_SESSION["typ_uctu"] = "teamleader";
    }
    elseif ($radek['typ'] == 4)
    {
        $_SESSION["typ_uctu"] = "manažer dopravy";
    }
    elseif ($radek['typ'] == 5)
    {
        $_SESSION["typ_uctu"] = "admin";
    }
    elseif ($radek['typ'] == 6)
    {
        $_SESSION["typ_uctu"] = "náborářka";
    }
    elseif ($radek['typ'] == 7)
    {
        $_SESSION["typ_uctu"] = "mzdový účetní";
    }

    $_SESSION["logged"] = "ANO";
    $text = $_POST['name']; 

}

mysqli_free_result($vysledek);

if ($text <> '')
{   ?>
    <meta http-equiv="refresh" content="0;url=main.php">
    <?php

    //zmenim cas prihlaseni v db
    $now = new DateTime('now', new DateTimeZone('Europe/Prague'));

    if (!($vysledek = mysqli_query($conn, 
    "update uzivatele set lastlog='" . $now->format('Y-m-d H:i:s') . "' where id='" . $_SESSION["log_id"] . "'")))
    {
    die("Nelze provést dotaz.</body></html>");
    }

    if (!($vysledek = mysqli_query($conn, 
    "insert into logs (kdo,typ,infotext,datumcas) values ('" . $_SESSION["log_id"] . "','Log In','Přihlásil se uživatel " . $_SESSION["log_name"] . "','" . $now->format('Y-m-d H:i:s') . "')")))
    {
    die("Nelze provést dotaz.</body></html>");
    }   

}
else
{   ?>
    <h2 class="text-center m-2 p-2">NEPLATNÉ UŽIVATELSKÉ JMÉNO NEBO HESLO</h2>
    <p class="text-center m-2 p-2">Zadano neplatne heslo za 5 s budete přesměrováni na index.php</p>
    <meta http-equiv="refresh" content="5;url=login.php">
    <?php

    session_unset();
    session_destroy();

}
    
// Uzavření připojení k databázi
$conn->close();

?>
   
<script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>