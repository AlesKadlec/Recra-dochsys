<?php
$servername = "192.168.105.4";
$username = "intra";
$password = "F*Qtn4o5bY0dhc@a";
$dbname = "recra";

// Vytvoření připojení
$conn = new mysqli($servername, $username, $password, $dbname);
mysqli_query($conn,"SET NAMES 'utf8'");

// Kontrola připojení
if ($conn->connect_error) {
    die("Chyba připojení k databázi: " . $conn->connect_error);
}
?>
