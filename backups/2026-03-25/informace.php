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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
    <title>INFORMACE K NÁSTUPŮM</title>
</head>
<body>

<?php

global $conn;

menu();

if (kontrola_prihlaseni() == "OK")
{   
    if (($_SESSION["typ"] == "5") or ($_SESSION["typ"] == "1") or ($_SESSION["typ"] == "6"))
    {
        if (isset($_GET["typ"]))
        {
            if ($_GET["typ"] == "updatedata")
            {
                $date1=date_create($_POST["dat_nastup"]);
    
                $dotaz="update nabory set boty='" . $_POST['boty'] . "',obleceni='" . $_POST['obleceni'] . "',telinfo='" . $_POST['telinfo'] . "',nastup='" . date_format($date1,"Y-m-d") . "',smena='" . $_POST['smena'] . "',nastupmisto='" . $_POST['nastupmisto'] . "',firma='" . $_POST['firma'] . "',cilova='" . $_POST['cilova'] . "',oscislo='" . $_POST['oscislo'] . "' where id='" . $_POST['idnabor'] . "'"; 
    
                if (!($vysledek = mysqli_query($conn, $dotaz)))
                {
                die("Nelze provést dotaz.</body></html>");
                } 
    
                //echo $dotaz;
                ?>

                <h1 class="text-center m-2 p-2">ZÁZNAM UPRAVEN</h1>
                <meta http-equiv="refresh" content="5;url=informace.php">
                
                <?php
            }
        }
        else
        {
        ?>
    
            <h3 class="text-center m-2 p-2">Informace k nástupům</h3>
    
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
                    <th scope='col'>Os. č.</th>
                    <th scope='col' class='text-start'>Příjmení</th>
                    <th scope='col' class='text-start'>Jméno</th>
                    <th scope='col'>Telefon</th>
                    <th scope='col'>Vstup</th>
                    <th scope='col'>Firma</th>
                    <th scope='col'>Zastávka</th>
                    <th scope='col'>Výsledek</th>
                    <th scope='col'></th>
                    <th scope='col'></th>
                </tr>
            </thead>
            <tbody>
    
            <?php  
                $cislo = 1;          
            
                $sql = "select nabory.id,prijmeni,jmeno,telefon,adresa,dat_narozeni,stat,dat_evidence,zdroj_inzerce,pozice,klient,klient2,souhlas,rekruter,vysledek,nastup,vystup,koordinator,poznamka,boty,obleceni,telinfo,nastupmisto,zastavka,smena,firmy.firma,oscislo from nabory left join nastupy on nabory.nastupmisto = nastupy.id left join firmy on nabory.firma = firmy.id where vysledek='Přijat' order by dat_evidence desc";
               
    
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
                        <td class='text-center'><?php echo $radek["oscislo"];?></td>
                        <td class='text-start'><?php echo $radek["prijmeni"];?></td>
                        <td class='text-start'><?php echo $radek["jmeno"];?></td>
                        <td class='text-center'><?php echo $radek["telefon"];?></td>
                        <td class='text-center'><?php echo prevod_data($radek["nastup"],1);?></td>  
                        <td class='text-center'><?php echo $radek["firma"];?></td>
                        <td class='text-center'><?php echo $radek["zastavka"];?></td>
                        <td>
                            <button type="button" class="btn btn-sm align-middle <?php echo ($radek['telinfo'] == 'proběhl') ? "text-success" : "text-danger";?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-telephone-fill" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/>
                                </svg>
                            </button>
    
                            <?php
                            if ($radek['boty'] <> '')
                            {   ?>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle='modal'>
                                        <b>B: </b> <span class="badge text-bg-warning"><?php echo $radek['boty'];?></span>
                                    </button>
                                <?php
                            }
    
                            if ($radek['obleceni'] <> '')
                            {   ?>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle='modal'>
                                        <b>O: </b> <span class="badge text-bg-warning"><?php echo $radek['obleceni'];?></span>
                                    </button>
                                <?php
                            }

                            if ($radek['smena'] <> '')
                            {   ?>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle='modal'>
                                        <b><?php echo $radek['smena'];?></b>
                                    </button>
                                <?php
                            }
                            ?>
          
                        </td>
    
                        <td class='text-start' width="50">
                            <?php

                            if (($_SESSION["typ"] == "5") or ($_SESSION["typ"] == "1"))
                            {   ?>
                                    <a type="button" class="btn btn-sm btn-primary" href="zamestnanci.php?typ=nastoupil&id=<?php echo $radek['id'];?>">Nástup</button></a>
                                <?php
                            }
                            ?>
                        </td>

                        <td class='text-start' width="50"><button type="button" class="form-control btn btn-sm btn-success" data-bs-toggle='modal' data-bs-target='#ModalDoplnNabor<?php echo $radek['id'];?>'>EDIT</button></td>
    
                    </tr>
                    
                    <?php
                    $cislo ++;
    
                    dopln_nabor($radek['id']);
            
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