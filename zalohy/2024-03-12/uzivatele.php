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
    <title>UŽIVATELÉ</title>
</head>
<body>

<?php

global $conn;

menu();

if (kontrola_prihlaseni() == "OK")
{   
    if (isset($_GET["typ"]))
    {
        if (($_GET["typ"] == "novyuzivatel") and ($_SESSION["typ"] == "5"))
        {   ?>
            
            <h3 class="text-center m-2 p-2">Přidání nového uživatele</h3>

            <div class="container">
                            
            <form class="row g-3" action="uzivatele.php?typ=ulozuzivatele" method="post">
         
            <div class="col-md-2">
                <div class="form-floating">
                <input type="text" class="form-control" id="uzivatel" name="uzivatel" placeholder="zadej login name" required>
                <label for="floatingInputGrid">Uživatel (login name)</label>
                </div>
            </div>     
            
            <div class="col-md-3">
                <div class="form-floating">
                <input type="password" class="form-control" id="heslo" name="heslo" placeholder="" required>
                <label for="floatingInputGrid">Heslo</label>
                </div>
            </div>    

            <div class="col-md-2">
                <div class="form-floating">
                <select class="form-select" id="aktivni" name="aktivni" aria-label="Floating label select example">
                    <option value="0">Nektivní</option>
                    <option value="1" selected>Aktivní</option>
                </select>
                <label for="floatingSelect">Aktivní / neaktivní</label>
                </div>
            </div>

            <div class="col-md-5">
                <div class="form-floating">
                <select class="form-select" id="firma" name="firma" aria-label="Floating label select example">
                    <option value="0" selected>Zatím nepřiřazen</option>

                    <?php
                    $sql = "select firma,id,aktivni from firmy where aktivni='1' order by firma";

                    if (!($vysledek = mysqli_query($conn, $sql)))
                    {
                    die("Nelze provést dotaz</body></html>");
                    }            

                    while ($radek = mysqli_fetch_array($vysledek))
                    {   
                        if ($firma == $radek["id"])
                        {   ?>
                                <option value="<?php echo $radek["id"];?>" selected><?php echo $radek["firma"];?></option>    
                            <?php
                        }
                        else
                        {   ?>
                                <option value="<?php echo $radek["id"];?>"><?php echo $radek["firma"];?></option>
                            <?php
                        }          
                    }

                    mysqli_free_result($vysledek);
                    ?>
                    
                </select>
                <label for="floatingSelect">Firma</label>
                </div>
            </div>

            <div class="col-md-5">
                <div class="form-floating">
                <select class="form-select" id="autobus" name="autobus" aria-label="Floating label select example">
                    <option value="0" selected>Nepřiřazen</option>

                    <?php
                    $sql = "select id,spz,oznaceni from auta order by spz";

                    if (!($vysledek = mysqli_query($conn, $sql)))
                    {
                    die("Nelze provést dotaz</body></html>");
                    }            

                    while ($radek = mysqli_fetch_array($vysledek))
                    {   ?>
                        <option value="<?php echo $radek["id"];?>"><?php echo $radek["spz"] . " - " . $radek["oznaceni"];?></option>
                        <?php                                                    
                    }

                    mysqli_free_result($vysledek);
                    ?>
                    
                </select>
                <label for="floatingSelect">Autobus</label>
                </div>
            </div>  

            <div class="col-md-3">
                <div class="form-floating">
                <input type="email" class="form-control" id="email" name="email" placeholder="vas.email@domena.cz">
                <label for="floatingInputGrid">Email</label>
                </div>
            </div>     
            
            <div class="col-md-4">
                <div class="form-floating">
                <select class="form-select" id="typ" name="typ" aria-label="Floating label select example">
                    <option value="1">Koordinátor</option>
                    <option value="2">Řidič</option>
                    <option value="3" selected>Teamleader</option>        
                    <option value="4">Manažer dopravy</option>
                    <option value="6">Náborář</option>
                </select>
                <label for="floatingSelect">Typ účtu</label>
                </div>
            </div>
    
            <div class="row g-2 mt-2">
                <button type="submit" class="btn btn-primary btn-block">Ulož změny do databáze</button>
            </div>

            </form>

            </div>

            <?php
        }
        elseif (($_GET["typ"] == "ulozuzivatele") and ($_SESSION["typ"] == "5"))
        {
            $sql= "insert into uzivatele (uzivatel,heslo,autobus,firma,typ,aktivni,email) values ('" . $_POST["uzivatel"] . "','" . hash('md5',$_POST["heslo"]) . "','" . $_POST["autobus"] . "','" . $_POST["firma"] . "','" . $_POST["typ"] . "','" . $_POST["aktivni"] . "','" . $_POST["email"] . "')";            

            if (!($vysledek = mysqli_query($conn, $sql)))
            {
            die("Nelze provést dotaz.</body></html>");
            }

            //vlozim zaznam do logu
            $now = new DateTime('now', new DateTimeZone('Europe/Prague'));

            if (!($vysledek = mysqli_query($conn, 
            "insert into logs (kdo,typ,infotext,datumcas) values ('" . $_SESSION["log_id"] . "','Nový uživatel','Vytvořen nový uživatel systému " . $_POST["uzivatel"] . " typ: " . $_POST["typ"] . "','" . $now->format('Y-m-d H:i:s') . "')")))
            {
            die("Nelze provést dotaz.</body></html>");
            }  
            ?>

            <div class="container">
                <h3 class="text-center m-2 p-2">Zaměstnanec přidán</h3>

                <h3 class="text-center m-2 p-2">Budete přesměrování zpět na hlavní stránku</h3>

            </div>

            <meta http-equiv="refresh" content="5;url=uzivatele.php">
            <?php
        }
        elseif ((($_GET["typ"] == "edituser") and ($_SESSION["typ"] == "5")) or (($_GET["typ"] == "edituser") and ($_SESSION["typ"] == "1")))
        {   
            global $conn;

            $sql = "select id,uzivatel,autobus,firma,typ,aktivni,email from uzivatele where id='" . $_GET["id"] . "'";
        
            if (!($vysledek = mysqli_query($conn, $sql)))
            {
            die("Nelze provést dotaz</body></html>");
            }            
        
            while ($radek = mysqli_fetch_array($vysledek))
            {  
                $id = $radek["id"];
                $uzivatel = $radek["uzivatel"];
                $autobus = $radek["autobus"];
                $firma = $radek["firma"];
                $typ = $radek["typ"];
                $aktivni = $radek["aktivni"];
                $email = $radek["email"];
            }
            
            mysqli_free_result($vysledek);
                          
            ?>
            
            <h3 class="text-center m-2 p-2">Editace uživatele</h3>

            <div class="container">
                            
                <form class="row g-3" action="uzivatele.php?typ=updateuzivatele" method="post">

                <input type="hidden" class="form-control" id="id_user" name="id_user" placeholder="" value=<?php echo $_GET["id"];?>>

                <div class="col-md-6">
                    <div class="form-floating">
                    <input type="text" class="form-control" id="uzivatel" name="uzivatel" placeholder="" value="<?php echo $uzivatel;?>" required>
                    <label for="floatingInputGrid">Uživatel (login name)</label>
                    </div>
                </div>             

                <div class="col-md-6">
                    <div class="form-floating">
                    <select class="form-select" id="aktivni" name="aktivni" aria-label="Floating label select example">
                        
                    <?php
                    if ($aktivni == "0")
                    {   ?>
                            <option value="0" selected>Nektivní</option>
                            <option value="1">Aktivní</option>
                        <?php
                    }
                    elseif ($aktivni == "1")
                    {   ?>
                            <option value="0">Nektivní</option>
                            <option value="1" selected>Aktivní</option>
                        <?php
                    }
                    ?>   
           
                    </select>
                    <label for="floatingSelect">Aktivní / neaktivní</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating">
                    <select class="form-select" id="autobus" name="autobus" aria-label="Floating label select example">
                        <option value="0" selected>Nepřiřazen</option>

                        <?php
                        $sql = "select id,spz,oznaceni from auta order by spz";

                        if (!($vysledek = mysqli_query($conn, $sql)))
                        {
                        die("Nelze provést dotaz</body></html>");
                        }            

                        while ($radek = mysqli_fetch_array($vysledek))
                        {   
                            if ($autobus == $radek["id"])
                            {   ?>
                                    <option value="<?php echo $radek["id"];?>" selected><?php echo $radek["spz"] . " - " . $radek["oznaceni"];?></option>
                                <?php
                            }
                            else
                            {   ?>
                                    <option value="<?php echo $radek["id"];?>"><?php echo $radek["spz"] . " - " . $radek["oznaceni"];?></option>
                                <?php
                            }     
                                  
                        }

                        mysqli_free_result($vysledek);
                        ?>
                        
                    </select>
                    <label for="floatingSelect">Autobus</label>
                    </div>
                </div>  
                
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="email" class="form-control" id="email" name="email" placeholder="vas.email@domena.cz" value=<?php echo $email;?>>
                        <label for="floatingInputGrid">Email</label>
                    </div>
                </div> 

                <div class="col-md-3">
                    <div class="form-floating">
                    <select class="form-select" id="typ" name="typ" aria-label="Floating label select example">
                        
                    <?php
                    echo (isset($typ) && $typ == '1') ? "<option value='1' selected>Koordinátor</option>" : "<option value='1'>Koordinátor</option>";
                    echo (isset($typ) && $typ == '2') ? "<option value='2' selected>Řidič</option>" : "<option value='2'>Řidič</option>";
                    echo (isset($typ) && $typ == '3') ? "<option value='3' selected>Teamleader</option>" : "<option value='3'>Teamleader</option>";
                    echo (isset($typ) && $typ == '4') ? "<option value='4' selected>Manažer dopravy</option>" : "<option value='4'>Manažer dopravy</option>";
                    echo (isset($typ) && $typ == '6') ? "<option value='6' selected>Náborář</option>" : "<option value='6'>Náborář</option>";
                    ?>
           
                    </select>
                    <label for="floatingSelect">Typ účtu</label>
                    </div>
                </div>


                <?php 
                
                $firmy_pole = explode(",", $firma);
                $pocet = count($firmy_pole);
                ?>
      
                <div class="col-md-12">
                    <label for="floatingSelect">Firma</label>
                    <select class="form-select" size="10" multiple aria-label="Multiple select example" id="firma" name="firma[]">

                        <?php
                        $sql = "select firma,id,aktivni from firmy where aktivni='1' order by firma";

                        if (!($vysledek = mysqli_query($conn, $sql)))
                        {
                        die("Nelze provést dotaz</body></html>");
                        }            

                        while ($radek = mysqli_fetch_array($vysledek))
                        {   
                            $check = 0;

                            for ($index = 0; $index <= $pocet-1; $index++)
                            {
                                if ($firmy_pole[$index] == $radek["id"])
                                {   ?>
                                        <option value="<?php echo $radek["id"];?>" selected><?php echo $radek["firma"];?></option>    
                                    <?php
                                    $check = 1;
                                }
                            }

                            if ($check == 0)
                            {
                                ?>
                                    <option value="<?php echo $radek["id"];?>"><?php echo $radek["firma"];?></option>
                                <?php
                            }
                        }

                        mysqli_free_result($vysledek);
                        ?>
             
                    </select> 

                </div>     

                <div class="row g-2 mt-2">
                    <button type="submit" class="btn btn-primary btn-block">Ulož změny do databáze</button>
                </div>

                </form>

            </div>

            <?php
        }
        elseif ((($_GET["typ"] == "updateuzivatele") and ($_SESSION["typ"] == "5")) or (($_GET["typ"] == "updateuzivatele") and ($_SESSION["typ"] == "1")))
        {
         
            $selectedOptions = $_POST['firma'];
            
            $vybrane_firmy = "";

            if (!empty($selectedOptions)) 
            {
                foreach ($selectedOptions as $option) 
                {
                    $vybrane_firmy = $vybrane_firmy . $option . ",";
                }
            } 
            else 
            {
                $vybrane_firmy = "";
            }

            if (strlen($vybrane_firmy) > 0) {
                $vybrane_firmy = substr($vybrane_firmy, 0, -1);
            } else {
                $vybrane_firmy = 0;
            }

            $sql = "update uzivatele set uzivatel='" . $_POST["uzivatel"] . "',autobus='" . $_POST["autobus"] . "',firma='" . $vybrane_firmy . "',typ='" . $_POST["typ"] . "',aktivni='" . $_POST["aktivni"] . "',email='" . $_POST["email"] . "' where id='" . $_POST["id_user"] . "'";
            
            if (!($vysledek = mysqli_query($conn, $sql)))
            {
            die("Nelze provést dotaz.</body></html>");
            }

            //vlozim zaznam do logu
            $now = new DateTime('now', new DateTimeZone('Europe/Prague'));

            if (!($vysledek = mysqli_query($conn, 
            "insert into logs (kdo,typ,infotext,datumcas) values ('" . $_SESSION["log_id"] . "','Editace uživatele','Editace uživatele systému " . $_POST["uzivatel"] . " typ: " . $_POST["typ"] . "','" . $now->format('Y-m-d H:i:s') . "')")))
            {
            die("Nelze provést dotaz.</body></html>");
            }  
            ?>

            <div class="container">
                <h3 class="text-center m-2 p-2">Uživatel byl upraven</h3>

                <h3 class="text-center m-2 p-2">Budete přesměrování zpět na hlavní stránku</h3>

            </div>

            <meta http-equiv="refresh" content="5;url=uzivatele.php"
            
            <?php
        }
        elseif ((($_GET["typ"] == "zmenahesla") and ($_SESSION["typ"] == "5")) or (($_GET["typ"] == "zmenahesla") and ($_SESSION["typ"] == "1")))
        {                        
            ?>           

            <div class="container mt-5 pt-5">

                <form class="row g-3" action="uzivatele.php?typ=updatehesla" method="post">

                    <div class="d-flex justify-content-center h-100">
                        <div class="card">
                            <div class="card-header">
                                <h3>Změna hesla</h3>
                                <div class="d-flex justify-content-end social_icon">
                                    
                                </div>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="prihlasit.php">
                                
                                    <input type="hidden" class="form-control" id="id" name="id" placeholder="" value=<?php echo $_GET["id"];?>>

                                    <div class="input-group form-group mt-2">
                                        <input type="password" class="form-control" placeholder="Zadej nové heslo" name="heslo1" id="heslo2">
                                    </div>

                                    <div class="input-group form-group mt-2">
                                        <input type="password" class="form-control" placeholder="Znovu heslo" name="heslo2" id="heslo2">
                                    </div>

                                    <div class="row align-items-center">
                                    </div>
                                    
                                    <div class="row g-2 mt-2">
                                        <button type="submit" class="btn btn-primary btn-block">Změn heslo</button>
                                    </div>
                                </form>
                            </div>
                        
                        </div>
                    </div>
                    
                </form>

            </div>

            <?php
        }
        elseif ((($_GET["typ"] == "updatehesla") and ($_SESSION["typ"] == "5")) or (($_GET["typ"] == "updatehesla") and ($_SESSION["typ"] == "1")))
        {
            if (($_POST['heslo1']) == ($_POST['heslo2']))
            {

                $sql = "update uzivatele set heslo = '" . hash('md5',$_POST['heslo1']) . "' where id='" . $_POST['id'] . "'";
            
                //echo $sql;
    
                if (!($vysledek = mysqli_query($conn, $sql)))
                {
                die("Nelze provést dotaz.</body></html>");
                }
                ?>
    
                <div class="container">
                    <h3 class="text-center m-2 p-2">Heslo úspěšně změněno</h3>
                    <h3 class="text-center m-2 p-2">Budete přesměrování zpět na přehled uživatelů</h3>
                </div>

                <?php
            }
            else
            {   ?>

                <div class="container">
                    <h3 class="text-center m-2 p-2">Nesouhlasí heslo 1 a heslo 2, prosím proveďte změnu znova</h3>
                    <h3 class="text-center m-2 p-2">Budete přesměrování zpět na přehled uživatelů</h3>
                </div>

                <?php
            }
            
            ?>

            <meta http-equiv="refresh" content="5;url=uzivatele.php">

            <?php
        }
        else
        {   ?>

                <h1 class="text-center m-2 p-2">NEAUTORIZOVANÝ PŘÍSTUP</h1>
                <meta http-equiv="refresh" content="5;url=uzivatele.php">
            <?php

        }
    }
    else
    {   ?>

        <h3 class="text-center m-2 p-2">Přehled uživatelů</h3>

        <div class="container-fluid">
        <div class="row justify-content-md-center">
        <div class="col col-md-12">

        <?php
            if ($_SESSION["typ"] == '5')
            {   ?>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a class="btn btn-outline-primary text-center m-2" href="uzivatele.php?typ=novyuzivatel" role="button">Nový uživatel</a>
                    </div>
                <?php
            }
        ?>
    
        <br>
        <div class='table-responsive-lg text-center'>
        <table class='table table-hover'>
        <thead>
            <tr class='table-active'>
                <th scope='col'>ID</th>
                <th scope='col'>Uživatel</th>
                <th scope='col'>Autobus</th>
                <th scope='col'>Typ uživatele</th>
                <th scope='col'>Firma</th>

                <?php
                    if (($_SESSION["typ"] == '5') or ($_SESSION["typ"] == '1'))
                    {   ?>
                            <th scope='col'>Editace / Změna hesla</th>
                        <?php
                    }
                ?>
              
            </tr>
        </thead>
        <tbody>

        <?php

            $cislo = 1;
            $sql = "select uzivatele.id,uzivatel,auta.spz,typ,uzivatele.aktivni,firma from uzivatele left join auta on uzivatele.autobus = auta.id order by uzivatel";

            if (!($vysledek = mysqli_query($conn, $sql)))
            {
            die("Nelze provést dotaz</body></html>");
            }            

            while ($radek = mysqli_fetch_array($vysledek))
            {   
                
                if ($radek["aktivni"] == "1")
                {
                    $barva = "table-success opacity-10";
                }
                else
                {
                    $barva = "table-danger";
                }               
                
                ?>
                
                <tr class='<?php echo $barva;?>'>
                    <td class='text-center fw-bold'><?php echo $cislo;?></td>
                    <td class='text-start'><?php echo $radek["uzivatel"];?></td>
                    <td class='text-center'><?php echo $radek["spz"];?></td>
                    <td class='text-center'>
                    <?php 
                        if ($radek["typ"] == 1)
                        {
                            echo "Koordinátor";
                        }
                        elseif ($radek["typ"] == 2)
                        {
                            echo "Řidič";
                        }
                        elseif ($radek["typ"] == 3)
                        {
                            echo "Teamleader";
                        }      
                        elseif ($radek["typ"] == 4)
                        {
                            echo "Manažer dopravy";
                        }               
                        elseif ($radek["typ"] == 5)
                        {
                            echo "Administrátor";
                        } 
                        elseif ($radek["typ"] == 6)
                        {
                            echo "Náborář";
                        }  
                        ?>     
                    </td>

                    <?php
                    if ($radek["typ"] == "5")
                    {
                        echo "<td><span class='text-center fw-bold text-success'>všechny firmy</span></td>";
                    }
                    else
                    {
                        echo "<td>" . get_firmy_from_id_IN($radek["firma"]) . "</td>";
                    }
                    ?>
                    
                    <?php
                        if ($_SESSION["typ"] == '5')
                        {  
                            if ($radek["typ"] <> '5')
                            {
                                ?>
                                    <td>
                                        <a type="button" class="btn btn-outline-primary" href="uzivatele.php?typ=edituser&id=<?php echo $radek["id"];?>">Edit</button></a>

                                        <a button class="btn btn-outline-primary" href="uzivatele.php?typ=zmenahesla&id=<?php echo $radek["id"];?>">
                                            <img src="img/key.svg" alt="změna hesla" style="width: 20px; height: 20px; margin-right: 5px;">
                                        </button></a>

                                    </td>
                                <?php
                            }
                            else
                            {
                                ?>
                                    <td>

                                        <a button class="btn btn-outline-primary" href="uzivatele.php?typ=zmenahesla&id=<?php echo $radek["id"];?>">
                                                <img src="img/key.svg" alt="změna hesla" style="width: 20px; height: 20px; margin-right: 5px;">
                                        </button></a>
                                    
                                    </td>

                                <?php
                            }
                        }
                        elseif ($_SESSION["typ"] == '1') //koordinator
                        {   
                            if ($radek["typ"] == '2')
                            {   ?>
                                    <td>
                                        <a type="button" class="btn btn-outline-primary" href="uzivatele.php?typ=edituser&id=<?php echo $radek["id"];?>">Edit</button></a>

                                        <a button class="btn btn-outline-primary" href="uzivatele.php?typ=zmenahesla&id=<?php echo $radek["id"];?>">
                                            <img src="img/key.svg" alt="změna hesla" style="width: 20px; height: 20px; margin-right: 5px;">
                                        </button></a>

                                    </td>
                                <?php
                            }
                            else
                            {   ?>
                                    <td></td>
                                <?php
                            }
                        }
                    ?> 
                    
                </tr>
                
                <?php
                $cislo = $cislo + 1;
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