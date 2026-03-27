<?php require_once 'init.php'; ?>


<?php
// Test session bez session_start()
// session.auto_start musí být = 1

if (!isset($_SESSION['count'])) {
    $_SESSION['count'] = 1;
} else {
    $_SESSION['count']++;
}

echo "<h3>Test automatického startu session</h3>";
echo "session.auto_start = <strong>" . ini_get('session.auto_start') . "</strong><br>";
echo "Session ID: <strong>" . session_id() . "</strong><br>";
echo "Počet zobrazení této stránky: <strong>" . $_SESSION['count'] . "</strong><br>";
