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
    <title>Kalendář pro 4 směnný provoz</title>
</head>
<body>

<?php

global $conn;

menu();

if (kontrola_prihlaseni() == "OK")
{
    if (isset($_SESSION["typ"]))
    {
        if (($_SESSION["typ"] == "5") or ($_SESSION["typ"] == "1") or ($_SESSION["typ"] == "4"))
        {
            if(isset($_POST['mesic']))
            {
                $str_arr = explode ("-", $_POST["mesic"]); 
                $rok = $str_arr[0];
                $mesic = $str_arr[1];
            }
            else
            {
                if(isset($_GET['date']))
                {
                    $str_arr = explode ("-", $_GET["date"]); 
                    $rok = $str_arr[0];
                    $mesic = $str_arr[1];
                }
                else
                {
                    $mesic = date('m');
                    $rok = date('Y');
                }
            }      

            if(isset($_GET['shift']))
            {
                vloz_data_do_smenneho_kalendare($_GET['date'],$_GET['shift'],$_GET['smena']);
            }
            ?>

            <h2 class='text-center m-2 p-2 d-print-none'>Plánovací kalendář pro 4 směnný provoz</h2>

            <div class="d-grid gap-2 d-md-flex justify-content-md-center d-print-none mb-3">
                <form class="row g-3" action="report5.php" method="post">    
              
                <div class="col-auto">
                    <label for="datepicker">Výběr měsíce</label>

                    <select class="form-select mt-2" id="mesic" name="mesic">
                        <?php
                        if (($rok . "-" . $mesic) === (getNextMonth()))
                        {   ?>
                                <option value="<?php echo getNextMonth();?>" selected><?php echo getNextMonth();?></option>
                            <?php
                        }
                        else
                        {   ?>
                                <option value="<?php echo getNextMonth();?>"><?php echo getNextMonth();?></option>
                            <?php
                        }                    
                        
                        global $conn;

                        $sql = "SELECT left(datum,7) as datumek FROM dochazka group by left(datum,7) order by datumek desc";

                        if (!($vysledek = mysqli_query($conn, $sql)))
                        {
                        die("Nelze provést dotaz</body></html>");
                        }            
 
                        while ($radek = mysqli_fetch_array($vysledek))
                        {   
                            if (($rok . "-" . $mesic) == ($radek["datumek"]))
                            {
                                ?>
                                    <option value="<?php echo $radek["datumek"];?>" selected><?php echo $radek["datumek"];?></option>
                                <?php
                            }
                            else
                            {
                                ?>
                                    <option value="<?php echo $radek["datumek"];?>"><?php echo $radek["datumek"];?></option>
                                <?php
                            }           
                        }

                        mysqli_free_result($vysledek);                       
                        ?>

                    </select>
                </div>
            
                <div class="col-auto">
                    <label for="vyber"> </label>
                    <button type="submit" class="form-control btn btn-primary mt-2">Proveď výběr</button>
                </div>

                </form>
            </div>

            <div class="container-fluid ">
                <div class="row row-cols-1 row-cols-xxl-2">
                    <div class="col border border-primary"><?php vyrob_kalendar_4sm($rok, $mesic,'A');?></div>
                    <div class="col border border-primary"><?php vyrob_kalendar_4sm($rok, $mesic,'B');?></div>
                    <div class="col border border-primary"><?php vyrob_kalendar_4sm($rok, $mesic,'C');?></div>
                    <div class="col border border-primary"><?php vyrob_kalendar_4sm($rok, $mesic,'D');?></div>
                </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>