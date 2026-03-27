<?php
// Zahrnutí souboru s údaji o připojení
include('db.php');
include ('funkce.php');

global $conn;

// automaticke odbiti nastupu do prace pro lidi co jezdi autem
cron_na_vlastni_dopravu();

// automaticke preklopeni smen smena2 --> smena
//cron_na_zmenu_smennosti();

// cron na automaticke vkladani naplanovane nepritomnosti
cron_vkladani_nepritomnosti();

// cron co vklada VOL u lidi co maji v dany tyden nastavenou smenu X
cron_vloz_volno_pro_X_vcera();
// cron na zmenu smen pro lidi se ctyrsmennym provozem
//cron_na_4sm();

//test_cron();

?>