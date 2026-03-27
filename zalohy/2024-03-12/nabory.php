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
    <title>NÁBORY</title>
</head>
<body>

<?php

global $conn;

menu();

if (kontrola_prihlaseni() == "OK")
{   
    if (isset($_GET["typ"]))
    {
        if (($_GET["typ"] == "savenabor" and $_SESSION["typ"] == "1") or ($_GET["typ"] == "savenabor" and $_SESSION["typ"] == "4") or ($_GET["typ"] == "savenabor" and $_SESSION["typ"] == "5") or ($_GET["typ"] == "savenabor" and $_SESSION["typ"] == "6"))
        {
            $date1=date_create($_POST["dat_narozeni"]); //narozen
            $date2=date_create($_POST["dat_evidence"]); //dat evidence
            
            if ($_POST["dat_nastup"] == '')
            {
                $date3=date_create('0000-00-00'); //nastup
            }
            else
            {
                $date3=date_create($_POST["dat_nastup"]); //nastup
            }

            if ($_POST["dat_vystup"] == '')
            {
                $date4=date_create('0000-00-00'); //vystup
            }
            else
            {
                $date4=date_create($_POST["dat_vystup"]); //vystup
            }

            $sql= "insert into nabory (prijmeni,jmeno,telefon,adresa,dat_narozeni,stat,dat_evidence,zdroj_inzerce,pozice,klient,klient2,souhlas,rekruter,vysledek,nastup,vystup,koordinator,duvod_ukonceni,poznamka) values ('" . $_POST["prijmeni"] . "','" . $_POST["jmeno"] . "','" . $_POST["telefon"] . "','" . $_POST["adresa"] . "','" . date_format($date1,"Y-m-d") . "','" . $_POST["stat"] . "','" . date_format($date2,"Y-m-d") . "','" . $_POST["zdroj"] . "','" . $_POST["pozice"] . "','" . $_POST["klient"] . "','" . $_POST["klient2"] . "','" . $_POST["souhlas"] . "','" . $_POST["rekruter"] . "','" . $_POST["vysledek"] . "','" . date_format($date3,"Y-m-d") . "','" . date_format($date4,"Y-m-d") . "','" . $_POST["koordinator"] . "','" . $_POST["duvod_ukonceni"] . "','" . $_POST["poznamka"] . "')"; 

            if (!($vysledek = mysqli_query($conn, $sql)))
            {
            die("Nelze provést dotaz.</body></html>");
            }           

            //zaznam do logu
            $now = new DateTime('now', new DateTimeZone('Europe/Prague'));
               
            if (!($vysledek = mysqli_query($conn, 
            "insert into logs (kdo,typ,infotext,datumcas) values ('" . $_SESSION["log_id"] . "','Nový nábor','Vytvořen nový nábor " . $_POST["prijmeni"] . " " . $_POST["jmeno"] . " - " . date_format($date2,"Y-m-d") . "','" . $now->format('Y-m-d H:i:s') . "')")))
            {
            die("Nelze provést dotaz.</body></html>");
            }
            ?>
       
            <meta http-equiv="refresh" content="0;url=nabory.php">

        <?php
        }
        elseif (($_GET["typ"] == "updatenabor" and $_SESSION["typ"] == "1") or ($_GET["typ"] == "updatenabor" and $_SESSION["typ"] == "4") or ($_GET["typ"] == "updatenabor" and $_SESSION["typ"] == "5") or ($_GET["typ"] == "updatenabor" and $_SESSION["typ"] == "6"))
        {
            $date1=date_create($_POST["dat_narozeni"]); //narozen
            $date2=date_create($_POST["dat_evidence"]); //dat evidence

            if ($_POST["dat_nastup"] == '')
            {
                $date3=date_create('0000-00-00'); //nastup
            }
            else
            {
                $date3=date_create($_POST["dat_nastup"]); //nastup
            }

            if ($_POST["dat_vystup"] == '')
            {
                $date4=date_create('0000-00-00'); //vystup
            }
            else
            {
                $date4=date_create($_POST["dat_vystup"]); //vystup
            }

            $sql= "update nabory set prijmeni = '" . $_POST["prijmeni"] . "',jmeno='" . $_POST["jmeno"] . "',telefon='" . $_POST["telefon"] . "',adresa='" . $_POST["adresa"] . "',dat_narozeni='" . date_format($date1,"Y-m-d") . "',stat='" . $_POST["stat"] . "',dat_evidence='" . date_format($date2,"Y-m-d") . "',zdroj_inzerce='" . $_POST["zdroj"] . "',pozice='" . $_POST["pozice"] . "',klient='" . $_POST["klient"] . "',klient2='" . $_POST["klient2"] . "',souhlas='" . $_POST["souhlas"] . "',rekruter='" . $_POST["rekruter"] . "',vysledek='" . $_POST["vysledek"] . "',nastup='" . date_format($date3,"Y-m-d") . "',vystup='" . date_format($date4,"Y-m-d") . "',koordinator='" . $_POST["koordinator"] . "',duvod_ukonceni='" . $_POST["duvod_ukonceni"] . "',poznamka='" . $_POST["poznamka"] . "' where id='" . $_POST["radek_v_db"] . "'"; 
            
            if (!($vysledek = mysqli_query($conn, $sql)))
            {
            die("Nelze provést dotaz.</body></html>");
            }        
            
            //zaznam do logu
            $now = new DateTime('now', new DateTimeZone('Europe/Prague'));
               
            if (!($vysledek = mysqli_query($conn, 
            "insert into logs (kdo,typ,infotext,datumcas) values ('" . $_SESSION["log_id"] . "','Aktualizován nábor','Nábor upraven u " . $_POST["prijmeni"] . " " . $_POST["jmeno"] . " - " . date_format($date2,"Y-m-d") . "','" . $now->format('Y-m-d H:i:s') . "')")))
            {
            die("Nelze provést dotaz.</body></html>");
            }
            ?>
       
            <meta http-equiv="refresh" content="0;url=nabory.php">

        <?php
        }
    }
    else
    {
        if (isset($_GET["sort"]))
        {

            if ($_GET["sort"] == "jmenoasc")
            {
                $sort = "prijmeni asc";
                $sort_link = "&sort=jmenoasc";
            }
            elseif ($_GET["sort"] == "jmenodesc")
            {
                $sort = "prijmeni desc";
                $sort_link = "&sort=jmenodesc";
            }
            elseif ($_GET["sort"] == "dateasc")
            {
                $sort = "dat_evidence asc";
                $sort_link = "&sort=dateasc";
            }
            elseif ($_GET["sort"] == "datedesc")
            {
                $sort = "dat_evidence desc";
                $sort_link = "&sort=datedesc";
            }
            else
            {
                $sort = "dat_evidence desc";
                $sort_link = "&sort=datedesc";
            }
        }
        else
        {
            $sort = "dat_evidence desc";
            $sort_link = "&sort=datedesc";
        }

        if (isset($_GET["select"]))
        {
            $odkaz = "nabory.php?select=" . $_GET['select'] . $sort_link;
        }
        else
        {
            $odkaz = "nabory.php?select=all&sort=dateasc";
        }

    ?>

        <h3 class="text-center mt-3">Nábory</h3>
 
        <div class="container-fluid">
            <div class="row justify-content-md-center">
        
                <div class="d-md-flex">                

                    <div class="col-md-2">
                        <a class="form-control btn btn-success" data-bs-toggle='modal' data-bs-target='#nabor_new'>Nový záznam</a>
                    </div>  

                    <div class="col-md-2">
                        <a class="form-control btn btn-primary" href="nabory.php?select=all<?php echo $sort_link;?>" role="button">Všichni</a>
                    </div>

                    <div class="col-md-2">
                        <a class="form-control btn btn-warning" href="nabory.php?select=wait<?php echo $sort_link;?>" role="button">Čekající</a>
                    </div>                 

                    <div class="col-md-2">
                        <a class="form-control btn btn-danger" href="nabory.php?select=reject<?php echo $sort_link;?>" role="button">Zamítnutí</a>
                    </div>

                    <div class="col-md-2">
                        <a class="form-control btn btn-success" href="nabory.php?select=done<?php echo $sort_link;?>" role="button">Přijatí</a>
                    </div>                   

                    <div class="col-md-2">                    

           
                        <a href="<?php echo $odkaz;?>&sort=jmenoasc"><button type="button" class="btn btn-sm align-middle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-sort-alpha-down" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M10.082 5.629 9.664 7H8.598l1.789-5.332h1.234L13.402 7h-1.12l-.419-1.371zm1.57-.785L11 2.687h-.047l-.652 2.157z"/>
                                <path d="M12.96 14H9.028v-.691l2.579-3.72v-.054H9.098v-.867h3.785v.691l-2.567 3.72v.054h2.645zM4.5 2.5a.5.5 0 0 0-1 0v9.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L4.5 12.293z"/>
                            </svg>
                        </button></a>

                        <a href="<?php echo $odkaz;?>&sort=jmenodesc"><button type="button" class="btn btn-sm align-middle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-sort-alpha-down-alt" viewBox="0 0 16 16">
                            <path d="M12.96 7H9.028v-.691l2.579-3.72v-.054H9.098v-.867h3.785v.691l-2.567 3.72v.054h2.645z"/>
                            <path fill-rule="evenodd" d="M10.082 12.629 9.664 14H8.598l1.789-5.332h1.234L13.402 14h-1.12l-.419-1.371zm1.57-.785L11 9.688h-.047l-.652 2.156z"/>
                            <path d="M4.5 2.5a.5.5 0 0 0-1 0v9.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L4.5 12.293z"/>
                            </svg>
                        </button></a>

                        <a href="<?php echo $odkaz;?>&sort=dateasc"><button type="button" class="btn btn-sm align-middle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-sort-numeric-down-alt" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M11.36 7.098c-1.137 0-1.708-.657-1.762-1.278h1.004c.058.223.343.45.773.45.824 0 1.164-.829 1.133-1.856h-.059c-.148.39-.57.742-1.261.742-.91 0-1.72-.613-1.72-1.758 0-1.148.848-1.836 1.973-1.836 1.09 0 2.063.637 2.063 2.688 0 1.867-.723 2.848-2.145 2.848zm.062-2.735c.504 0 .933-.336.933-.972 0-.633-.398-1.008-.94-1.008-.52 0-.927.375-.927 1 0 .64.418.98.934.98"/>
                            <path d="M12.438 8.668V14H11.39V9.684h-.051l-1.211.859v-.969l1.262-.906h1.046zM4.5 2.5a.5.5 0 0 0-1 0v9.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L4.5 12.293z"/>
                            </svg>
                        </button></a>

                        <a href="<?php echo $odkaz;?>&sort=datedesc"><button type="button" class="btn btn-sm align-middle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-sort-numeric-down" viewBox="0 0 16 16">
                            <path d="M12.438 1.668V7H11.39V2.684h-.051l-1.211.859v-.969l1.262-.906h1.046z"/>
                            <path fill-rule="evenodd" d="M11.36 14.098c-1.137 0-1.708-.657-1.762-1.278h1.004c.058.223.343.45.773.45.824 0 1.164-.829 1.133-1.856h-.059c-.148.39-.57.742-1.261.742-.91 0-1.72-.613-1.72-1.758 0-1.148.848-1.835 1.973-1.835 1.09 0 2.063.636 2.063 2.687 0 1.867-.723 2.848-2.145 2.848zm.062-2.735c.504 0 .933-.336.933-.972 0-.633-.398-1.008-.94-1.008-.52 0-.927.375-.927 1 0 .64.418.98.934.98"/>
                            <path d="M4.5 2.5a.5.5 0 0 0-1 0v9.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L4.5 12.293z"/>
                            </svg>
                        </button></a>

                    </div>

                </div> 
            </div> 
        </div> 


        <div class="container-fluid mt-2">
      
        <div class="row justify-content-md-center">
        <div class="col col-md-12">
      
        <div class='table-responsive-lg text-center'>
        <table class='table table-hover'>
        <thead>
            <tr class='table-active'>
                <th scope='col'>#</th>
                <th scope='col'></th>
                <th scope='col' class='text-start' width="10%">Příjmení</th>
                <th scope='col' class='text-start' width="10%">Jméno</th>
                <th scope='col'>Telefon</th>
                <th scope='col' width="100">Datum<br>evidence</th>
                <th scope='col' width="100">Vstup</th>
                <th scope='col' width="100">Výstup</th>
                <th scope='col'>Klient</th>
                <th scope='col'>Rekrutér</th>
                <th scope='col'>Výsledek</th>
                <th scope='col' width="10%">Poznámka</th>
                <th scope='col'></th>
            </tr>
        </thead>
        <tbody>

        <?php  
            $cislo = 1;               

            if (isset($_GET["select"]))
            {
                if ($_GET["select"] == "all")
                {
                    $sql = "select id,prijmeni,jmeno,telefon,adresa,dat_narozeni,stat,dat_evidence,zdroj_inzerce,pozice,klient,klient2,souhlas,rekruter,vysledek,nastup,vystup,koordinator,poznamka from nabory order by " . $sort;
                }
                elseif ($_GET["select"] == "wait")
                {
                    $sql = "select id,prijmeni,jmeno,telefon,adresa,dat_narozeni,stat,dat_evidence,zdroj_inzerce,pozice,klient,klient2,souhlas,rekruter,vysledek,nastup,vystup,koordinator,poznamka from nabory where vysledek='Čeká se' order by " . $sort;
                }
                elseif ($_GET["select"] == "reject")
                {
                    $sql = "select id,prijmeni,jmeno,telefon,adresa,dat_narozeni,stat,dat_evidence,zdroj_inzerce,pozice,klient,klient2,souhlas,rekruter,vysledek,nastup,vystup,koordinator,poznamka from nabory where vysledek='Zamítnut' order by " . $sort;
                }
                elseif ($_GET["select"] == "done")
                {
                    $sql = "select id,prijmeni,jmeno,telefon,adresa,dat_narozeni,stat,dat_evidence,zdroj_inzerce,pozice,klient,klient2,souhlas,rekruter,vysledek,nastup,vystup,koordinator,poznamka from nabory where (vysledek='Nastoupil' or vysledek='Přijat') order by " . $sort;
                }
            }
            else
            {
                $sql = "select id,prijmeni,jmeno,telefon,adresa,dat_narozeni,stat,dat_evidence,zdroj_inzerce,pozice,klient,klient2,souhlas,rekruter,vysledek,nastup,vystup,koordinator,poznamka from nabory order by " . $sort;
            }

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
                    <td class='text-start'><?php echo $radek["telefon"];?></td>
                    <td class='text-center'><?php echo prevod_data($radek["dat_evidence"],1);?></td>
                    <td class='text-center'><?php echo prevod_data($radek["nastup"],1);?></td>
                    <td class='text-center'><?php echo prevod_data($radek["vystup"],1);?></td>
                    <td class='text-center'><?php echo $radek["klient"];?><?php echo ($radek['klient2'] <> '') ? " / " . $radek['klient2'] : "";?></td>
                    <td class='text-center'><?php echo $radek["rekruter"];?></td>
                    <td class='text-center'><?php echo $radek["vysledek"];?></td>
                    <td class='text-center'><?php echo $radek["poznamka"];?></td>

                    <?php
                    if (($radek['vysledek'] == "Nastoupil") or ($radek['vysledek'] == "Zamítnut"))
                    {   ?>
                            <td></td>
                        <?php
                    }
                    else
                    {   ?>
                            <td class='text-start' width="50"><button type="button" class="form-control btn btn-sm btn-success mt-2" data-bs-toggle='modal' data-bs-target='#ModalNaborInfo<?php echo $radek['id'];?>'>EDIT</button></td>
                        <?php
                    }

                    ?>                  

                </tr>
                
                <?php
                $cislo ++;

                if (($radek['vysledek'] == "Nastoupil") or ($radek['vysledek'] == "Zamítnut"))
                {
                    //není třeba modal
                }
                else
                {
                    novy_nabor($radek['id']);
                }
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
        $('.common-datepicker1').datepicker({
            format: 'dd.mm.yyyy',
            autoclose: true,
            todayHighlight: true,
            language: 'cs',
            allowInputToggle: false,
            startDate: '01.01.1930', // Nastavte výchozí hodnotu dle potřeby
            endDate: '31.12.2020'    // Nastavte výchozí hodnotu dle potřeby
        });
    });
</script>

<script>
    $(document).ready(function(){
        $('.common-datepicker2').datepicker({
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
        $('.common-datepicker3').datepicker({
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
        $('.common-datepicker4').datepicker({
            format: 'dd.mm.yyyy',
            autoclose: true,
            todayHighlight: true,
            language: 'cs',
            allowInputToggle: false
        });
    });
</script>

<?php novy_nabor();?>