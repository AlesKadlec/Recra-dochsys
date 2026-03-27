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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
    <title>EMERGENCY</title>
</head>
<body>

<?php

global $conn;

menu();

if (kontrola_prihlaseni() == "OK")
{   
    if (($_SESSION["typ"] == "5") or ($_SESSION["typ"] == "1") or ($_SESSION["typ"] == "6"))
    {

        ?>
    
            <h3 class="text-center m-2 p-2">Emergency kontakty</h3>
    
            <div class="container-fluid">
          
            <div class="row justify-content-md-center">
            <div class="col col-md-12">
     
            <br>
            <div class='table-responsive-lg text-center'>
            <table class='table table-hover'>
            <thead>
                <tr class='table-active'>
                    <th scope='col'>#</th>
                    <th scope='col'></th>
                    <th scope='col' class='text-start'>Příjmení</th>
                    <th scope='col' class='text-start'>Jméno</th>
                    <th scope='col'>Telefon</th>
                    <th scope='col'>Dat. evidence</th>
                    <th scope='col'>Adresa</th>
                    
                </tr>
            </thead>
            <tbody>
    
            <?php  
                $cislo = 1;          
            
                $sql = "select nabory.id,prijmeni,jmeno,telefon,adresa,dat_narozeni,stat,dat_evidence,zdroj_inzerce,pozice,klient,klient2,souhlas,rekruter,vysledek,nastup,vystup,koordinator,poznamka,boty,obleceni,telinfo,nastupmisto,zastavka,smena from nabory left join zastavky on nabory.nastupmisto = zastavky.id where vysledek='Emergency' order by dat_evidence desc";          
    
                if (!($vysledek = mysqli_query($conn, $sql)))
                {
                die("Nelze provést dotaz</body></html>");
                }            
    
                while ($radek = mysqli_fetch_array($vysledek))
                {                                        
                    ?>
                    
                    <tr>
                        <td class='text-center fw-bold'><?php echo $cislo;?></td>
                        <td class='text-start' width="50"><img src="vlajky/<?php echo strtolower($radek["stat"]);?>.png" class="img-thumbnail" width="50"></td>
                        <td class='text-start'><?php echo $radek["prijmeni"];?></td>
                        <td class='text-start'><?php echo $radek["jmeno"];?></td>
                        <td class='text-center'><?php echo $radek["telefon"];?></td>
                        <td class='text-center'><?php echo prevod_data($radek["dat_evidence"],1);?></td>  
                        <td class='text-center'><?php echo $radek["adresa"];?></td>
    
                    </tr>
                    
                    <?php
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
        
    }
    else
    {
        ?>

        <h1 class="text-center m-2 p-2">NEAUTORIZOVANÝ PŘÍSTUP</h1>
        <meta http-equiv="refresh" content="5;url=main.php">
    
        <?php
    }
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
        $('.common').datepicker({
            format: 'dd.mm.yyyy',
            autoclose: true,
            todayHighlight: true,
            language: 'cs',
            allowInputToggle: false,
            startDate: '01.01.2024', // Nastavte výchozí hodnotu dle potřeby
            endDate: '31.12.2099'    // Nastavte výchozí hodnotu dle potřeby
        });
    });
</script>