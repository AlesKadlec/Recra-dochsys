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

// HILITE v 1h rano, nastavit směnu X a dopravu zkopírovat z minulého týdne jen u těch co jezdi busem;
cron_plan_smen_firma('HILITE', 'X');

// ALLIANCE LAUNDRY v 1h rano, nastavit směnu R a dopravu zkopírovat z minulého týdne jen u těch co jezdi busem;
cron_plan_smen_firma('ALLIANCE LAUNDRY', 'R');

// TATRA v 1h rano, nastavit směnu R a dopravu zkopírovat z minulého týdne jen u těch co jezdi autem;
cron_plan_smen_firma_tatra();

?>