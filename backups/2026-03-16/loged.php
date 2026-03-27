<?php
//session_start();
include "funkce.php";
include "funkcenew.php";

global $conn;

?>  
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="css5/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="favicon.ico" type="image/x-icon"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/> 
    <title>ADMIN SEKCE</title>

</head>
<body>

<?php 

if ($_SESSION["log_name"] == "")
{
  menu();
  neautorizovany();

}
else
{
  menu(); ?>

<h1 class='text-center m-2 p-2'>ČLENSKÁ SEKCE UŽIVATELE - <?php echo $_SESSION["log_full_name"];?></h1>

<div class="container-fluid">
  
  <div class="row">

    <?php logged_box1($_SESSION["id_user"]);?>

    <?php logged_box2($_SESSION["id_user"]);?>

  </div>

  <div class="row mt-3">
    <div class="col-sm-12 mb-3 mb-sm-0">
      
      <ul class="nav nav-pills nav-fill border">
        <li class="nav-item">
          <a class="nav-link active" data-bs-toggle="tab" href="#tab1">Přihlášen</a>
        </li>

        <li class="nav-item">
          <a class="nav-link" data-bs-toggle="tab" href="#tab2">Nepřihlášen</a>
        </li>

      </ul>

      <div class="tab-content border mt-2">

        <div class="tab-pane container-fluid active" id="tab1">     

          <h3 class='text-center m-2 p-2'>Seznam akcí na které jste momentálně přihlášen (-a)</h3>

          <table class="table table-hover">
      
            <thead>
              <tr class='table-active'>
              <th scope='col' style='width:50px' class='text-center'>Datum</th>
              <th scope='col' style='width:110px' class='text-center'>Akce</th>
              <th scope='col' style='width:50px' class='text-center'>Kategorie</th>
              <th scope='col' style='width:100px' class='text-center'>Nástup</th>
              <th scope='col' style='width:70px' class='text-center'>VS</th>
              <th scope='col' style='width:70px' class='text-center'>Cena</th>
              <th scope='col' style='width:70px' class='text-center'>Zaplaceno</th>
              <th scope='col' style='width:70px' class='text-center'>QR</th>
              </tr>
            </thead>

            <tbody>
            
            <?php
            $sql = "select akce.id as akce_id,eprihlaska_ucast.id as id_ucast,akce.nazev,akce.datum,akce.datum2,variabilni_symbol,eprihlaska_ceny.kategorie,eprihlaska_ceny.cena,prihlasen,eprihlaska_nastupy.cas,active,poznamka,nastupy_akce.nastupni_misto,kapacita from eprihlaska_ucast left join akce on eprihlaska_ucast.akce_id = akce.id left join eprihlaska_ceny on eprihlaska_ucast.cena_id = eprihlaska_ceny.id left join eprihlaska_nastupy on eprihlaska_ucast.nastup_id = eprihlaska_nastupy.id left join nastupy_akce on eprihlaska_nastupy.nastup_id = nastupy_akce.id where user_id='" . $_SESSION["id_user"] . "' and active='1'";

            //$sql = "select user_id,akce_id,nastup_id,cena_id,variabilni_symbol,prihlasen,active,poznamka from eprihlaska_ucast where user_id='" . $_SESSION["id_user"] . "'";

            //echo $sql;

            if (!($vysledek = mysqli_query($conn, $sql)))
            {
            die("Nelze provést dotaz</body></html>");
            }            

            while ($radek = mysqli_fetch_array($vysledek))
            { ?>  
                <tr>
                  <td class='text-center'><?php echo $radek['datum'];?></td>
                  <td class='text-start'><?php echo $radek['nazev'];?></td>
                  <td class='text-start'><?php echo $radek['kategorie'];?></td>
                  <td class='text-center'><?php echo $radek['nastupni_misto'];?></td>
                  <td class='text-center'><?php echo $radek['variabilni_symbol'];?></td>
                  <td class='text-center'><?php echo $radek['cena'];?> Kč</td>
                  <td class='text-center'><?php echo zaplaceno_ucast_id($radek['id_ucast']);?> Kč</td>
                  <td class='text-center'><a><button type="button" class="btn btn-sm" data-bs-toggle='modal' data-bs-target='#ModalPlatba<?php echo $radek['id_ucast'];?>'>
                        <?php echo generateQRCodeURL($radek['variabilni_symbol'], $radek['cena'],50,'platba');?></button></a>
                    </td>
                  <?php platba_modal($radek['id_ucast']);?>
                <tr>

                <tr>
                  <td class='text-start bg-primary-subtle' colspan='2'><b>Jste: <?php echo ($radek['active'] == '1') ? "<span class='text-success'>Účastník</span>" : "<span class='text-danger'>Náhradník</span>";?></b></td>
                  <td class='text-start bg-success-subtle' colspan='4'><b>Poznámka: </b><?php echo $radek['poznamka'];?></td>
                  <td class='text-start bg-primary-subtle' colspan='2'><b>Obsazenost: </b><?php echo pocet_ucastniku_na_akci($radek['akce_id']);?> z <?php echo $radek['kapacita'];?></td>
                <tr>
                
              <?php

            }
            
            mysqli_free_result($vysledek);
            ?>

            </tbody>

          </table>
        
        </div>

        <div class="tab-pane container-fluid" id="tab2">

          <h3 class='text-center m-2 p-2'>Seznam akcí na které je možné se přihlásit</h3>

          <table class="table table-hover">
    
          <thead>
            <tr class='table-active'>
            <th scope='col' style='width:50px' class='text-center'>Datum</th>
            <th scope='col' style='width:110px' class='text-center'>Akce</th>
            <th scope='col' style='width:50px' class='text-center'>Typ akce</th>
            <th scope='col' style='width:100px' class='text-center'>Kapacita</th>
            <th scope='col' style='width:70px' class='text-center'>Obsazenost</th>
            <th scope='col' style='width:70px' class='text-center'>Vedoucí</th>
            <th scope='col' style='width:70px' class='text-center'>Přihlášení</th>
            </tr>
          </thead>

          <tbody>
        
          <?php
          $sql = "select akce.id,nazev,typ_akce.typ_akce_nazev,datum,datum2,kapacita,vedouci from akce left join typ_akce on akce.typ_akce = typ_akce.id where eprihlaska='1' and datum >= curdate()";

          //$sql = "select user_id,akce_id,nastup_id,cena_id,variabilni_symbol,prihlasen,active,poznamka from eprihlaska_ucast where user_id='" . $_SESSION["id_user"] . "'";

          //echo $sql;
          $pole_pro_modal = array();
          $i = 0;


          if (!($vysledek = mysqli_query($conn, $sql)))
          {
          die("Nelze provést dotaz</body></html>");
          }            

          while ($radek = mysqli_fetch_array($vysledek))
          { ?>  
              <tr>
                <td class='text-center'><?php echo $radek['datum'];?></td>
                <td class='text-start'><?php echo $radek['nazev'];?></td>
                <td class='text-center'><?php echo $radek['typ_akce_nazev'];?></td>
                <td class='text-center'><?php echo $radek['kapacita'];?></td>

                <?php
                if (pocet_ucastniku_na_akci($radek['id']) > 0)
                { ?>
                    <td class='text-center' width="50"><button type="button" class="form-control btn btn-sm btn-success mt-2" data-bs-toggle='modal' data-bs-target='#ModalTest<?php echo $radek['id'];?>'><?php echo pocet_ucastniku_na_akci($radek['id']);?></button></td>
                  <?php

                  array_push($pole_pro_modal, $radek['id']);
                  $i++;

                  //test_modal($radek['id']);
                  //ucastnici_na_akci_modal($radek['id']);

                }
                else
                { ?>

                    <td></td>

                  <?php
                }

                ?>
                               
                <td class='text-center'><?php echo $radek['vedouci'];?></td>

                <?php
                
                if (zjisti_prihlaseni($radek['id'],$_SESSION["id_user"]) == 'ANO')
                { ?>
                    <td class='text-center' width="50">Přihlášen</td>
                  <?php
                }
                else
                { 
                  if ($_SESSION["active"] == 1)
                  { ?>
                      <td class='text-center' width="50"><button type="button" class="form-control btn btn-sm btn-success mt-2" data-bs-toggle='modal' data-bs-target='#ModalPrihlasit<?php echo $radek['id'];?>'>Přihlásit</button></td>

                    <?php
                    prihlasit_modal($radek['id']);                   
                    
                  }
                  else
                  { ?>
                      <td class='text-center' width="50"><button type="button" class="form-control btn btn-sm btn-warning mt-2">Aktivuj email</button></td>
                    <?php
                  }
                  
                }   

                ?>            
              </tr>
            <?php
           
            
          }
          
          mysqli_free_result($vysledek);
          ?>

          </tbody>

          </table>
    
        </div>


      </div>   

    </div>

  </div>
  
</div>

<?php
  // Výsledná URL pro QR kód s variabilním symbolem, číslem účtu a částkou
//$variableSymbol = '12345678'; // Zde můžete zadat váš proměnný symbol
//$accountNumber = 'CZ8162106701002201428408'; // Zde můžete zadat číslo účtu
//$amount = '500.00'; // Zde můžete zadat částku
//$note = "platba za zajezd";

//$qrCodeURL = generateQRCodeURL($variableSymbol, $accountNumber, $amount, $note);


// Výstup pro zobrazení v prohlížeči
//echo '<img src="' . $qrCodeURL . '" alt="QR Code">';

for ($j = 0; $j < count($pole_pro_modal); $j++)
{
  ucastnici_na_akci_modal($pole_pro_modal[$j]);
}

} ?>

<script src="js5/bootstrap.bundle.min.js"></script>

</body>
</html>