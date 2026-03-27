<?php
// Zahrnutí souboru s údaji o připojení
include('db.php');
include ('funkce.php');

global $conn;

// automaticke odbiti nastupu do prace pro lidi co jezdi autem
cron_na_vlastni_dopravu();

// automaticke preklopeni smen smena2 --> smena
cron_na_zmenu_smennosti();

?>