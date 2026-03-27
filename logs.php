<?php
// Zahrnutí souboru s údaji o připojení
include('db.php');
include ('funkce.php');

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
    <title>Logy</title>
</head>
<body>

<?php

global $conn;

menu();

if (kontrola_prihlaseni() == "OK")
{
    if (isset($_SESSION["typ"]))
    {
        if ($_SESSION["typ"] == "5")
        {     
            ?>
            <h1 class='text-center m-2 p-2'>LOGY SYSTÉMU</h1>

            <h4 class='text-center m-2 p-2'>Přehled všech změn v systému DOCHÁZKY RECRA</h4>     

            <div class="container-fluid">

            <div class='table-responsive-lg text-center'>
            <table class='table table-hover'>
                <thead>
                    <tr class='table-active'>
                        <th scope='col'>#</th>
                        <th scope='col'>Uživatel</th>
                        <th scope='col'>Typ operace</th>
                        <th scope='col'>Změna v systému</th>
                        <th scope='col'>Datum čas</th>                    
                    </tr>
                </thead>
            <tbody>

            <?php
            $sql = "select logs.id,uzivatele.uzivatel,logs.typ,infotext,datumcas from logs left join uzivatele on logs.kdo = uzivatele.id order by datumcas desc limit 500";

            if (!($vysledek = mysqli_query($conn, $sql)))
            {
            die("Nelze provést dotaz</body></html>");
            }   

            while ($radek = mysqli_fetch_array($vysledek))
            {              
                
                ?>
                
                <tr class='table-success opacity-10'>
                    <td class='text-center'><?php echo $radek["id"];?></td>
                    
                    <?php
                    if ($radek["uzivatel"] == "")
                    {   ?>
                            <td class='text-center'>system</td>
                        <?php
                    }
                    else
                    {   ?>
                            <td class='text-center'><?php echo $radek["uzivatel"];?></td>
                        <?php
                    }

                    ?>
                    <td class='text-start'><?php echo $radek["typ"];?></td>
                    <td class='text-start'><?php echo $radek["infotext"];?></td>
                    <td class='text-center'><?php echo $radek["datumcas"];?></td>                     
                    
                </tr>
                
                <?php
            }
            
            mysqli_free_result($vysledek);

            ?>

            </div>

        <?php

        }
        else
        {   ?>
                <h1 class="text-center m-2 p-2">NEAUTORIZOVANÝ PŘÍSTUP</h1>
                <meta http-equiv="refresh" content="5;url=main.php">
            <?php
        }
    }
    else
    {   ?>
            <h1 class="text-center m-2 p-2">NEAUTORIZOVANÝ PŘÍSTUP</h1>
            <meta http-equiv="refresh" content="5;url=main.php">
        <?php
    }

}
else
{   ?>
        <h1 class="text-center m-2 p-2">NEAUTORIZOVANÝ PŘÍSTUP</h1>
        <meta http-equiv="refresh" content="5;url=login.php">
    <?php
}
?>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>