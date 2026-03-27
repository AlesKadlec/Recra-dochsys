<?php
// Zahrnutí souboru s údaji o připojení
include('db.php');
include ('funkce.php');

global $conn;

$res = cron_hilite_smeny();

if (!$res['ok']) {
    echo "CRON chyba: " . $res['error'] . PHP_EOL;
    exit(1);
}

if (!empty($res['skipped'])) {
    echo "Přeskočeno: " . $res['reason'] . PHP_EOL;
    exit(0);
}

echo "Hotovo. Rok {$res['rok']}, týden {$res['tyden']}, zkontrolováno {$res['checked']}, vloženo {$res['inserted']}, upraveno {$res['updated']}" . PHP_EOL;

?>