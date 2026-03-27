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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <title>HLAVNÍ STRÁNKA</title>
</head>
<body>

<script>
    function aktualizujCas() {
        $.ajax({
            url: 'cas.php', // PHP skript, který vrátí aktuální čas
            success: function(data) {
                $('#cas').text(data);
            }
        });
    }

    $(document).ready(function() {
        aktualizujCas(); // Zavoláme funkci hned při načtení stránky
        setInterval(aktualizujCas, 1000); // Aktualizujeme čas každou vteřinu
    });


</script>

<?php

global $conn;

menu();

if (kontrola_prihlaseni() == "OK")
{   
    
    if (isset($_GET["typ"]))
    {
        
        if (($_GET["typ"] == "updatefirma" and $_SESSION["typ"] == "1") or ($_GET["typ"] == "updatefirma" and $_SESSION["typ"] == "4") or ($_GET["typ"] == "updatefirma" and $_SESSION["typ"] == "5"))
        {

            if ($_POST["zmenasmen"] == "30.11.-0001")
            {   
                $date=date_create($_POST["zmenasmen"]);                     
                $sql = "update firmy set firma='" . $_POST["nazevfirmy"] . "',aktivni='" . $_POST["aktivni"] . "',objednavka='" . $_POST["objednavka"] . "',zmenasmen=now(),zmenastatus='0' where id='" . $_POST["id_firmy"] . "'";    
            }
            else
            {
                $date=date_create($_POST["zmenasmen"]);         
                $sql = "update firmy set firma='" . $_POST["nazevfirmy"] . "',aktivni='" . $_POST["aktivni"] . "',objednavka='" . $_POST["objednavka"] . "',zmenasmen='" . date_format($date,"Y-m-d") . " " . $_POST["caszmeny"] . "',zmenastatus='1' where id='" . $_POST["id_firmy"] . "'";
            }
                               
            if (!($vysledek = mysqli_query($conn, $sql)))
            {
            die("Nelze provést dotaz.</body></html>");
            }

            //vlozim zaznam do logu
            $now = new DateTime('now', new DateTimeZone('Europe/Prague'));

            if (!($vysledek = mysqli_query($conn, 
            "insert into logs (kdo,typ,infotext,datumcas) values ('" . $_SESSION["log_id"] . "','Editace firmy','Editace firmy " . $_POST["nazevfirmy"] . "','" . $now->format('Y-m-d H:i:s') . "')")))
            {
            die("Nelze provést dotaz.</body></html>");
            }  
            ?>

            <div class="container">
                <h3 class="text-center m-2 p-2">Firma byla upravena</h3>

                <h3 class="text-center m-2 p-2">Budete přesměrování zpět na firmy</h3>

            </div>
            
            <meta http-equiv="refresh" content="5;url=firmy.php">
            <?php
        }
        elseif (($_GET["typ"] == "vytvorfirmu" and $_SESSION["typ"] == "1") or ($_GET["typ"] == "vytvorfirmu" and $_SESSION["typ"] == "4") or ($_GET["typ"] == "vytvorfirmu" and $_SESSION["typ"] == "5"))
        {
    
            $sql= "insert into firmy (firma,aktivni,objednavka) values ('" . $_POST["nazevfirmy"] . "','" . $_POST["aktivni"] . "','" . $_POST["objednavka"] . "')"; 
        
            if (!($vysledek = mysqli_query($conn, $sql)))
            {
            die("Nelze provést dotaz.</body></html>");
            }

            //vlozim zaznam do logu
            $now = new DateTime('now', new DateTimeZone('Europe/Prague'));

            if (!($vysledek = mysqli_query($conn, 
            "insert into logs (kdo,typ,infotext,datumcas) values ('" . $_SESSION["log_id"] . "','Nová firma','Vytvořena nová firma " . $_POST["nazevfirmy"] . "','" . $now->format('Y-m-d H:i:s') . "')")))
            {
            die("Nelze provést dotaz.</body></html>");
            }  
            ?>

            <div class="container">
                <h3 class="text-center m-2 p-2">Firma byla založena</h3>

                <h3 class="text-center m-2 p-2">Budete přesměrování zpět na firmy</h3>

            </div>

            <meta http-equiv="refresh" content="5;url=firmy.php">

            <?php
        }
        else
        {   ?>
            <div class="container">
            <h3 class="text-center m-2 p-2">NEPOVOLENÁ OPERACE</h3>

            <h3 class="text-center m-2 p-2">Budete přesměrování zpět HLAVNÍ OBRAZOVKU</h3>

            </div>

            <meta http-equiv="refresh" content="5;url=main.php">

            <?php
        }
    }
    else
    {   ?>
        
        <h1 class="text-center m-2 p-2">HLAVNÍ STRÁNKA RECRA SYSTÉMU</h1>

        <h3 class="text-center m-2 p-2">Přehled firem</h3>
        
        <?php

            if (isset($_POST["radioGroup"])) 
            {
                $selectedOption = $_POST["radioGroup"];

                if ($selectedOption == 1)
                {
                    $vyber1 = "checked";
                    $vyber2 = "";
                }
                else
                {
                    $vyber1 = "";
                    $vyber2 = "checked";
                }
            } 
            else 
            {
                // Radio button nebyl vybrán
                $selectedOption = 1;
              
                $vyber1 = "checked";
                $vyber2 = "";
                
            }

            if (($_SESSION["typ"] == '1') or ($_SESSION["typ"] == '4') or ($_SESSION["typ"] == '5'))
            {   ?>

                <form action="firmy.php" method="post">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">                       

                        <button type="button" class="btn btn-outline-primary text-center m-2" data-bs-toggle='modal' data-bs-target='#firma_new'>Nová firma</button>
                        
                        <div class="form-check m-2">
                            <input class="form-check-input m-2" type="radio" name="radioGroup" id="firmaaktiv" value="1" <?php echo $vyber1;?>>
                            <label class="form-check-label m-2" for="firmaaktiv">
                                Aktivní firmy
                            </label>
                        </div>

                        <div class="form-check m-2">
                            <input class="form-check-input m-2" type="radio" name="radioGroup" id="firmaall" value="2" <?php echo $vyber2;?>>
                            <label class="form-check-label m-2" for="firmaall">
                                Všechny firmy
                            </label>
                        </div>

                        <button type="submit" class="btn btn-outline-primary">Vyber</button>
                
                    </div>

                </form>

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
                    <th scope='col'>ID</th>
                    <th scope='col'>Firma</th>
                    <th scope='col'>Aktivní<br>Neaktivní</th>
                    <th scope='col'>Zaměstnanců<br>Objednávka</th>
                    <th scope='col'>Docházky</th>

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
                if ($_SESSION["typ"] == '5')
                {
                    if ($selectedOption == 1)
                    {
                        $sql = "select id,firma,aktivni,zmenasmen,zmenastatus,zmenaprovedena from firmy where aktivni='1' order by id";
                    }
                    else
                    {
                        $sql = "select id,firma,aktivni,zmenasmen,zmenastatus,zmenaprovedena from firmy order by id";
                    }
                    
                }
                else
                {
                    if ($selectedOption == 1)
                    {
                        $sql = "select id,firma,aktivni,zmenasmen,zmenastatus,zmenaprovedena from firmy where id in (" . $_SESSION["firma"] . ") and aktivni='1' order by id";
                    }
                    else
                    {
                        $sql = "select id,firma,aktivni,zmenasmen,zmenastatus,zmenaprovedena from firmy where id in (" . $_SESSION["firma"] . ") order by id";
                    }
                  
                }                

                if (!($vysledek = mysqli_query($conn, $sql)))
                {
                die("Nelze provést dotaz</body></html>");
                }            

                while ($radek = mysqli_fetch_array($vysledek))
                {   ?>
                    
                    <tr>
                    <td class='text-center fw-bold'><?php echo $cislo;?></td>
                    <td class='text-start'><b><?php echo $radek["firma"];?></b><br><?php echo zjisti_preklopeni_smen($radek["id"]);?></td>
                    <td>
                        <?php
                        if ($radek['aktivni'] == 1)
                        {   ?>
                                <h4><span class="badge bg-primary">Aktivní</span></h4>
                            <?php
                        }
                        elseif ($radek['aktivni'] == 0)
                        {   ?>
                                <h4><span class="badge bg-warning">Neaktivní</span></h4>
                            <?php
                        }
                        ?>                      
                        
                    </td>
                    <td><?php echo zjisti_pocet_zamestnancu_ve_firme($radek["id"]);?> / <?php echo zjisti_pocet_zamestnancu_ve_firme_objednavka($radek["id"]);?> </td>
                    <td>
                    
                    <?php 
                        if ($_SESSION["typ"] == '2')
                        {
                            vytvor_tlacitka_pro_smeny($radek["id"],$_SESSION["log_id"],$_SESSION["autobus"]);
                        }                        
                    ?>
                    
                    </td>
                        
                    <?php
                    if (($_SESSION["typ"] == '1') or ($_SESSION["typ"] == '4') or ($_SESSION["typ"] == '5'))
                    {   ?>
                    
                        <td class='text-start' width="50"><button type="button" class="btn btn-outline-primary" data-bs-toggle='modal' data-bs-target='#ModalFirmaInfo<?php echo $radek['id'];?>'>Editace</button></td>

                        <?php

                        nova_firma($radek['id']);

                    }
                    ?>
                    
                    </tr>
                    
                    <?php                    

                    $cislo ++;         
                }
                
                mysqli_free_result($vysledek);

            //zapati tabulky ?>

                    <tr>
                    <td class='text-center'></td>
                    <td class='text-start'>Nepřiřazení zaměstnanci</td>
                    <td></td>
                    <td><?php echo zjisti_pocet_zamestnancu_ve_firme(0);?></td>
                    <td></td>
                    <?php
                    if (($_SESSION["typ"] == '1') or ($_SESSION["typ"] == '4') or ($_SESSION["typ"] == '5'))
                    {   ?>
                        <td></td>
                        <?php
                    }
                    ?>
                    
                    </tr>

            </tbody>
            </table>
            </div>

            </div>
            </div>

        </div>

       <?php
    }

    nova_firma();
}
else
{
    ?>

    <h1 class="text-center m-2 p-2">NEAUTORIZOVANÝ PŘÍSTUP</h1>
    <meta http-equiv="refresh" content="5;url=login.php">

    <?php
}
?>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.cs.min.js"></script>

<script>
    $(document).ready(function(){
        $('#datepicker').datepicker({
            format: 'dd.mm.yyyy',
            autoclose: true,
            todayHighlight: true,
            language: 'cs',
            allowInputToggle: false
        });
    });
</script>

<script>
    $(document).ready(function(){
        $('.common-datepicker').datepicker({
            format: 'dd.mm.yyyy',
            autoclose: true,
            todayHighlight: true,
            language: 'cs',
            allowInputToggle: false,
            startDate: '01.01.1930', // Nastavte výchozí hodnotu dle potřeby
            endDate: '31.12.2099'    // Nastavte výchozí hodnotu dle potřeby
        });
    });
</script>