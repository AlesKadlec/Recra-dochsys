<?php
// Zahrnutí souboru s údaji o připojení
include('db.php');
include ('funkce.php');

global $conn;

cron_na_vlastni_dopravu();

?>