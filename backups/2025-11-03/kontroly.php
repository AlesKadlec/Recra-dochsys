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
    <title>KONTROLY PRACOVNÍCH NESCHOPNOSTÍ</title>
</head>
<body>

<?php

global $conn;

menu();

if (kontrola_prihlaseni() == "OK")
{   
    if (($_SESSION["typ"] == "5") or ($_SESSION["typ"] == "1"))
    {
        if (isset($_GET["typ"]))
        {
            if ($_GET["typ"] == "pridejkontrolu")
            {
                $date1=date_create($_POST["dpn_od"]);
                $date2=date_create($_POST["dpn_do"]);
    
                $dotaz="insert into kontroly (id_zam,dpn_od,dpn_do,zadal,provedl,vysledek,adresa) values ('" . $_POST['id_zam'] . "','" . date_format($date1,'Y-m-d') . "','" . date_format($date2,'Y-m-d') . "','" . $_SESSION["log_id"] . "','" . $_POST['ridic'] . "','čeká na kontrolu','" . $_POST['adresa'] . "')"; 
    
                if (!($vysledek = mysqli_query($conn, $dotaz)))
                {
                die("Nelze provést dotaz.</body></html>");
                } 
    
                //echo $dotaz;
                ?>

                <h1 class="text-center m-2 p-2">ZÁZNAM O KONTROLE DPN VLOŽEN DO DATABÁZE</h1>
                <meta http-equiv="refresh" content="5;url=kontroly.php">
                
                <?php
            }
        }
        else
        {
        ?>
    
            <h3 class="text-center m-2 p-2">Kontroly pracovních neschopností</h3>
    
            <div class="container-fluid">
          
            <div class="row justify-content-md-center">
            <div class="col col-md-12">
     
            <br>
            <div class='table-responsive-lg text-center'>
            <table class='table table-hover'>
            <thead>
                <tr class='table-active'>
                    <th scope='col'>#</th>
                    <th scope='col' class='text-start'>Příjmení</th>
                    <th scope='col' class='text-start'>Jméno</th>
                    <th scope='col' class='text-center'>Zakázka</th>
                    <th scope='col'>Adresa</th>
                    <th scope='col' class='text-center'>DPN Od</th>
                    <th scope='col' class='text-center'>Kontrola Do</th>
                    <th scope='col'></th>
                </tr>
            </thead>
            <tbody>
    
            <?php  
                $cislo = 1;          
            
                $sql = "select id,prijmeni,jmeno,adresa,cilova,nepritomnost,dpn_od from zamestnanci where aktivni='1' and nepritomnost='DPN'";
    
                if (!($vysledek = mysqli_query($conn, $sql)))
                {
                die("Nelze provést dotaz</body></html>");
                }            
    
                while ($radek = mysqli_fetch_array($vysledek))
                {       
                    if ($radek["dpn_od"] == '0000-00-00')
                    {
                        $nemoc_doba = "";
                        $ok = 0;
                    }
                    else
                    {
                        $startDate = $radek["dpn_od"];
                        $do_kdy = addDaysToDate($startDate, 13);
                        $nemoc_doba = prevod_data(addDaysToDate($startDate, 13),1);

                        $dateObject = date_create($do_kdy);
                   
                        $today = new DateTime();
                        $today = date_create($today->format('Y-m-d'));

                        if ($dateObject > $today) 
                        {                            
                            $ok = 1;
                        } 
                        elseif ($dateObject < $today) 
                        {                           
                            $ok = 0;
                        } 
                        else 
                        {
                            $ok = 1;
                        }              
                    }
                    
                    ?>
                    
                    <tr>
                        <td class='text-center fw-bold'><?php echo $cislo;?></td>
                        <td class='text-start'><?php echo $radek["prijmeni"];?></td>
                        <td class='text-start'><?php echo $radek["jmeno"];?></td>
                        <td class='text-center'><?php echo $radek["cilova"];?></td>
                        <td class='text-start'><?php echo $radek["adresa"];?></td>
                        <td class='text-center'><?php echo prevod_data($radek["dpn_od"],1);?></td>
                        <td class='text-center'><?php echo $nemoc_doba;?></td>

                        <?php
                        if (over_kontrolu($radek['id']) == "čeká na kontrolu")
                        {   ?>
                                <td class='text-start' width="50"><button type="button" class="form-control btn btn-sm btn-danger">ČEKÁ NA KONTROLU</button></td>
                            <?php
                        }
                        else
                        {   
                            if ($ok == 1)
                            {
                                ?>
                                <td class='text-start' width="50"><button type="button" class="form-control btn btn-sm btn-success" data-bs-toggle='modal' data-bs-target='#ModalKontrola<?php  echo $radek['id'];?>'>ZKONTROLOVAT</button></td>
                                <?php
                            }
                            else
                            {
                                ?>
                                <td class='text-start' width="50"><button type="button" class="form-control btn btn-sm btn-warning">NEJDE</button></td>
                                <?php
                            }                          
                         
                        }
                        ?>

                        
                    </tr>
                    
                    <?php

                    kontrola_dpn($radek["id"]);

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