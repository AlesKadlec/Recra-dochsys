<?php
// Zkontrolovat, zda byla předána funkce a ID v GET požadavku
if (isset($_GET['functionName']) && isset($_GET['ID'])) {
    // Získat název funkce a ID z GET požadavku
    $functionName = $_GET['functionName'];
    $id = $_GET['ID'];

    // Zkontrolovat, zda existuje požadovaná funkce
    if (function_exists($functionName)) {
        // Spustit požadovanou funkci s předaným ID
        $result = call_user_func($functionName, $id);
        echo $result; // Vrátit výsledek jako odpověď na AJAX požadavek
    } else {
        // Pokud požadovaná funkce neexistuje, vrátit chybovou zprávu
        echo "Požadovaná funkce neexistuje.";
    }
} else {
    // Pokud nebyla předána funkce a ID, vrátit chybovou zprávu
    echo "Chybí požadovaná data.";
}

// Funkce pro načtení zaměstnance s daným ID
function nacti_zamestnance($id) {
    // Zde můžete implementovat logiku pro načtení zaměstnance s daným ID

    $dnesniDatum = date("Y-m-d");
    $aktualniCas = date("H:i:s");
    echo "Aktuální čas je: " . $aktualniCas;

    return "Zaměstnanec s ID $id byl načten.";

}
?>