<?php
$servername = "localhost";
$username = "recraczdochazky";
$password = "3boV3tWJGK";
$dbname = "recraczdochazky";

// Vytvoření připojení
$conn = new mysqli($servername, $username, $password, $dbname);
mysqli_query($conn,"SET NAMES 'utf8'");

// Kontrola připojení
if ($conn->connect_error) {
    die("Chyba připojení k databázi: " . $conn->connect_error);
}
?>
