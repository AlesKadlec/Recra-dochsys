<?php
// Zahrnutí souboru s údaji o připojení
include('db.php');

// Funkce pro načtení dat z tabulky 'zdroj'
function nacti() {
    global $conn;
    
    $sql = "SELECT * FROM zdroj"; // Nahraďte 'zdroj' názvem vaší tabulky

    $result = $conn->query($sql);

    // Zpracování výsledků
    $data = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    
    return $data;
}

// Funkce pro vložení dat do tabulky 'zdroj'
function Vloz($nazev, $popis) {
    global $conn;

    // Použijte připravený dotaz pro bezpečné vkládání dat
    $stmt = $conn->prepare("INSERT INTO zdroj (nazev, popis) VALUES (?, ?)");

    // Vložení hodnot
    $stmt->bind_param("ss", $nazev, $popis);

    if ($stmt->execute()) {
        return true; // Úspěšně vloženo
    } else {
        return false; // Chyba vložení
    }
}

// Použití funkce pro vložení dat
if (isset($_POST['nazev']) && isset($_POST['popis'])) {
    $nazev = $_POST['nazev'];
    $popis = $_POST['popis'];
    
    if (Vloz($nazev, $popis)) {
        echo "Data byla úspěšně vložena.";
    } else {
        echo "Chyba při vkládání dat.";
    }
}

// Načtení dat z tabulky 'zdroj'
$data = nacti();

// Zpracování načtených dat
if (!empty($data)) {
    foreach ($data as $row) {
        echo "ID: " . $row["id"]. " - Název: " . $row["nazev"]. " - Popis: " . $row["popis"]. "<br>";
    }
} else {
    echo "Nebyly nalezeny žádné záznamy.";
}

// Uzavření připojení k databázi
$conn->close();
?>
