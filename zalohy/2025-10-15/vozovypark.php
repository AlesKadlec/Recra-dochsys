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
    <link rel="icon" href="favicon.ico" type="image/x-icon"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
    <title>Přehled vozového parku</title>
</head>
<body>

<?php

global $conn;

menu();

if (kontrola_prihlaseni() == "OK")
{
    if (isset($_SESSION["typ"]))
    {
        if (($_SESSION["typ"] == '1') or ($_SESSION["typ"] == '4') or ($_SESSION["typ"] == '5'))
        {    
            
            if (isset($_GET["typ"]))
            {
                if ($_GET["typ"] == "zastavky")
                {   ?>

                    <h3 class="text-center m-2 p-2">Přehled zastávek</h3>

                    <h3 class="text-center m-2 p-2"><?php echo get_info_bus($_GET['id']);?></h3>

                    <?php
                    if (($_SESSION["typ"] == '1') or ($_SESSION["typ"] == '4') or ($_SESSION["typ"] == '5'))
                    {   ?>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-center">  
                                <button type="button" class="btn btn-outline-primary text-center m-2" data-bs-toggle='modal' data-bs-target='#zastavka_new'>Nová zastávka</button>
                            </div>
                        <?php
                    }
                    ?>
        
                    <div class="container">
                        <div class="row justify-content-md-center">
                        <div class="col col-md-12">

                        <br>
                        <div class='table-responsive-lg text-center'>
                        <table class='table table-hover'>
                        <thead>
                            <tr class='table-active'>
                                <th scope='col'>#</th>
                                <th scope='col'>Zastávka</th>
                                <th scope='col'>Čas R</th>
                                <th scope='col'>Čas O</th>
                                <th scope='col'>Čas N</th>
                                <th scope='col'>Čas NN</th>
                                <th scope='col'>Čas NR</th>
                                <th scope='col'>Čas VK</th>
                                <th scope='col'>Čas PR</th>
                                <?php
                                if (($_SESSION["typ"] == '1') or ($_SESSION["typ"] == '4') or ($_SESSION["typ"] == '5'))
                                {   ?>
                                        <th scope='col'>Editace</th>
                                        <th scope='col'>Smaž</th>
                                    <?php
                                }
                                ?>
                                
                            </tr>
                        </thead>
                        <tbody>

                        <?php
                            $cislo = 1;
                            $sql = "select zastavky.id,auto,zastavka,cas1,cas2,cas3,cas4,cas5,cas6,cas7 from zastavky left join auta on zastavky.auto = auta.id where auto='" . $_GET['id'] . "' order by cas1";

                            if (!($vysledek = mysqli_query($conn, $sql)))
                            {
                            die("Nelze provést dotaz</body></html>");
                            }            

                            while ($radek = mysqli_fetch_array($vysledek))
                            {   ?>
                                
                                <tr>
                                <td class='text-center fw-bold'><?php echo $cislo;?></td>
                                <td class='text-start'><?php echo $radek["zastavka"];?></td>
                                <td><?php echo $radek["cas1"];?></td>
                                <td><?php echo $radek["cas2"];?></td>
                                <td><?php echo $radek["cas3"];?></td>
                                <td><?php echo $radek["cas4"];?></td>
                                <td><?php echo $radek["cas5"];?></td>
                                <td><?php echo $radek["cas6"];?></td>
                                <td><?php echo $radek["cas7"];?></td>
                                <?php
                                if (($_SESSION["typ"] == '1') or ($_SESSION["typ"] == '4') or ($_SESSION["typ"] == '5'))
                                {   ?>
                                        <td class='text-start' width="50"><button type="button" class="btn btn-outline-primary" data-bs-toggle='modal' data-bs-target='#ModalZastavkaInfo<?php echo $radek['id'];?>'>Edit</button></td>
                                        <td><a type="button" class="btn btn-outline-primary" href="vozovypark.php?typ=smazzastavku&id=<?php echo $radek["id"];?>">Smaž</button></a></td>
                                    <?php

                                    nova_zastavka($radek['id']);

                                }
                                ?>
                        
                                </tr>
                                
                                <?php    
                                $cislo ++;      
                            }
                            
                            mysqli_free_result($vysledek);

                        //zapati tabulky ?>
                        </tbody>
                        </table>
                        </div>

                        </div>
                        </div>

                    </div>

                    <?php

                    nova_zastavka();

                }
                elseif (($_GET["typ"] == "smazzastavku" and $_SESSION["typ"] == "1") or ($_GET["typ"] == "smazzastavku" and $_SESSION["typ"] == "4") or ($_GET["typ"] == "smazzastavku" and $_SESSION["typ"] == "5"))
                {

                    $zastavka = get_zastavka_from_id2($_GET["id"]);

                    $sql = "delete from zastavky where id='" . $_GET["id"] . "'";
                    
                    if (!($vysledek = mysqli_query($conn, $sql)))
                    {
                    die("Nelze provést dotaz.</body></html>");
                    }

                    //vlozim zaznam do logu
                    $now = new DateTime('now', new DateTimeZone('Europe/Prague'));

                    if (!($vysledek = mysqli_query($conn, 
                    "insert into logs (kdo,typ,infotext,datumcas) values ('" . $_SESSION["log_id"] . "','Smazána zastávka','Smazána zastávka " . $zastavka . "','" . $now->format('Y-m-d H:i:s') . "')")))
                    {
                    die("Nelze provést dotaz.</body></html>");
                    }  

                    ?>

                    <div class="container">
                        <h3 class="text-center m-2 p-2">Zastávka byla smazána</h3>

                        <h3 class="text-center m-2 p-2">Budete přesměrování zpět přehled zastávek</h3>

                    </div>

                    <meta http-equiv="refresh" content="5;url=vozovypark.php">

                    <?php
                }
                elseif (($_GET["typ"] == "updatezastavky" and $_SESSION["typ"] == "1") or ($_GET["typ"] == "updatezastavky" and $_SESSION["typ"] == "4") or ($_GET["typ"] == "updatezastavky" and $_SESSION["typ"] == "5"))
                {
                    $sql = "update zastavky set zastavka='" . $_POST["zastavka"] . "',cas1='" . $_POST["cas1"] . "',cas2='" . $_POST["cas2"] . "',cas3='" . $_POST["cas3"] . "',cas4='" . $_POST["cas4"] . "',cas5='" . $_POST["cas5"] . "',cas6='" . $_POST["cas6"] . "',cas7='" . $_POST["cas7"] . "' where id='" . $_POST["id_zastavky"] . "'";
                    

                    if (!($vysledek = mysqli_query($conn, $sql)))
                    {
                    die("Nelze provést dotaz.</body></html>");
                    }

                    //vlozim zaznam do logu
                    $now = new DateTime('now', new DateTimeZone('Europe/Prague'));

                    if (!($vysledek = mysqli_query($conn, 
                    "insert into logs (kdo,typ,infotext,datumcas) values ('" . $_SESSION["log_id"] . "','Editace zastávky','Editace zastávky " . $_POST["zastavka"] . "','" . $now->format('Y-m-d H:i:s') . "')")))
                    {
                    die("Nelze provést dotaz.</body></html>");
                    }  

                    ?>

                    <div class="container">
                        <h3 class="text-center m-2 p-2">Zastávka byla upravena</h3>

                        <h3 class="text-center m-2 p-2">Budete přesměrování zpět přehled zastávek</h3>

                    </div>

                    <meta http-equiv="refresh" content="5;url=vozovypark.php?typ=zastavky&id=<?php echo $_POST["auto"];?>"

                    <?php
                }
                elseif (($_GET["typ"] == "vytvorauto" and $_SESSION["typ"] == "1") or ($_GET["typ"] == "vytvorauto" and $_SESSION["typ"] == "4") or ($_GET["typ"] == "vytvorauto" and $_SESSION["typ"] == "5"))
                {
            
                    $sql= "insert into auta (spz,oznaceni) values ('" . $_POST["spz"] . "','" . $_POST["oznaceni"] . "')"; 
                
                    if (!($vysledek = mysqli_query($conn, $sql)))
                    {
                    die("Nelze provést dotaz.</body></html>");
                    }

                    //vlozim zaznam do logu
                    $now = new DateTime('now', new DateTimeZone('Europe/Prague'));

                    if (!($vysledek = mysqli_query($conn, 
                    "insert into logs (kdo,typ,infotext,datumcas) values ('" . $_SESSION["log_id"] . "','Nová doprava','Vytvořena nová doprava " . $_POST["spz"] . " - " . $_POST["oznaceni"] . "','" . $now->format('Y-m-d H:i:s') . "')")))
                    {
                    die("Nelze provést dotaz.</body></html>");
                    }  

                    ?>

                    <div class="container">
                        <h3 class="text-center m-2 p-2">Auto bylo založeno</h3>

                        <h3 class="text-center m-2 p-2">Budete přesměrování zpět na hlavní stránku</h3>

                    </div>

                    <meta http-equiv="refresh" content="5;url=vozovypark.php">

                    <?php
                }
                elseif (($_GET["typ"] == "updateauto" and $_SESSION["typ"] == "1") or ($_GET["typ"] == "updateauto" and $_SESSION["typ"] == "4") or ($_GET["typ"] == "updateauto" and $_SESSION["typ"] == "5"))
                {
                    $sql = "update auta set spz='" . $_POST["spz"] . "',oznaceni='" . $_POST["oznaceni"] . "' where id='" . $_POST["id_auta"] . "'";
                    
                    if (!($vysledek = mysqli_query($conn, $sql)))
                    {
                    die("Nelze provést dotaz.</body></html>");
                    }

                    //vlozim zaznam do logu
                    $now = new DateTime('now', new DateTimeZone('Europe/Prague'));

                    if (!($vysledek = mysqli_query($conn, 
                    "insert into logs (kdo,typ,infotext,datumcas) values ('" . $_SESSION["log_id"] . "','Editace dopravy','Editace dopravy " . $_POST["spz"] . " - " . $_POST["oznaceni"] . "','" . $now->format('Y-m-d H:i:s') . "')")))
                    {
                    die("Nelze provést dotaz.</body></html>");
                    } 
                    ?>

                    <div class="container">
                        <h3 class="text-center m-2 p-2">Auto bylo upraveno</h3>

                        <h3 class="text-center m-2 p-2">Budete přesměrování zpět na hlavní stránku</h3>

                    </div>

                    <meta http-equiv="refresh" content="5;url=vozovypark.php">
                    <?php
                }
                elseif (($_GET["typ"] == "savezastavka" and $_SESSION["typ"] == "1") or ($_GET["typ"] == "savezastavka" and $_SESSION["typ"] == "4") or ($_GET["typ"] == "savezastavka" and $_SESSION["typ"] == "5"))
                {
                    //uloz_zastavku($_POST["zastavka"],$_POST["smena"],$_POST["cas"],$_POST["auto"]);     
                    //global $conn;

                    //$sql = "select spz,oznaceni from auta where id='" . $id . "';";
                
                    $sql= "insert into zastavky (auto,zastavka,cas1,cas2,cas3,cas4,cas5,cas6,cas7) values ('" . $_POST["auto"] . "','" . $_POST["zastavka"] . "','" . $_POST["cas1"] . "','" . $_POST["cas2"] . "','" . $_POST["cas3"] . "','" . $_POST["cas4"] . "','" . $_POST["cas5"] . "','" . $_POST["cas6"] . "','" . $_POST["cas7"] . "')"; 
                
                    if (!($vysledek = mysqli_query($conn, $sql)))
                    {
                    die("Nelze provést dotaz.</body></html>");
                    }

                    //vlozim zaznam do logu
                    $now = new DateTime('now', new DateTimeZone('Europe/Prague'));

                    if (!($vysledek = mysqli_query($conn, 
                    "insert into logs (kdo,typ,infotext,datumcas) values ('" . $_SESSION["log_id"] . "','Nová zastávka','Nová zastávka " . $_POST["zastavka"] . "','" . $now->format('Y-m-d H:i:s') . "')")))
                    {
                    die("Nelze provést dotaz.</body></html>");
                    } 
                    ?>

                    <div class="container">
                        <h3 class="text-center m-2 p-2">Zastávka přidána</h3>

                        <h3 class="text-center m-2 p-2">Budete přesměrování zpět na přehled zastávek</h3>

                    </div>

                    <meta http-equiv="refresh" content="5;url=vozovypark.php?typ=zastavky&id=<?php echo $_POST["auto"];?>">

                    <?php
                }

            }
            else
            {   ?>

                <h3 class="text-center m-2 p-2">Přehled vozového parku</h3>

                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                    <button type="button" class="btn btn-outline-primary text-center m-2" data-bs-toggle='modal' data-bs-target='#auto_new'>Nové auto</button>
                </div>
    
                <div class="container">
                    <div class="row justify-content-md-center">
                    <div class="col col-md-12">
    
                    <br>
                    <div class='table-responsive-lg text-center'>
                    <table class='table table-hover'>
                    <thead>
                        <tr class='table-active'>
                            <th scope='col'>ID</th>
                            <th scope='col'>SPZ</th>
                            <th scope='col'>Označení prostředku</th>
                            <th scope='col'>Zastávky</th>
    
                            <?php
                            if (($_SESSION["typ"] == '1') or ($_SESSION["typ"] == '4') or ($_SESSION["typ"] == '5'))
                            {   ?>
                                    <th scope='col'>Editace</th>
                                <?php
                            }
                            ?>
                            
                        </tr>
                    </thead>
                    <tbody>
    
                    <?php
                        $cislo = 1;
                        $sql = "select id,spz,oznaceni from auta order by id";
    
                        if (!($vysledek = mysqli_query($conn, $sql)))
                        {
                        die("Nelze provést dotaz</body></html>");
                        }            
    
                        while ($radek = mysqli_fetch_array($vysledek))
                        {   ?>
                            
                            <tr>
                            <td class='text-center fw-bold'><?php echo $cislo;?></td>
                            <td class='text-center'><?php echo $radek["spz"];?></td>
                            <td><?php echo $radek["oznaceni"];?></td>
                            <td><a type="button" class="btn btn-outline-primary btn" href="vozovypark.php?typ=zastavky&id=<?php echo $radek["id"];?>">Zastávky</button></a></td>
                            
                            <?php
                            if (($_SESSION["typ"] == '1') or ($_SESSION["typ"] == '4') or ($_SESSION["typ"] == '5'))
                            {   ?>
                                    <td class='text-start' width="50"><button type="button" class="btn btn-outline-primary btn" data-bs-toggle='modal' data-bs-target='#ModalAutoInfo<?php echo $radek['id'];?>'>Editace</button></td>
                                <?php
                            }
                            ?>
                            
                            </tr>
                            
                            <?php    
                            nove_auto($radek['id']);

                            $cislo ++;     
                        }
                        
                        mysqli_free_result($vysledek);
    
                    ?>
                    </tbody>
                    </table>
                    </div>                   
    
                    </div>
                    </div>
    
                </div>

            <?php

            nove_auto();

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