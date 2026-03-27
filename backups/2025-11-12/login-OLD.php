<?php
// Zahrnutí souboru s údaji o připojení
include('db.php');

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
    <title>LOGIN</title>
</head>


<style>
    #carouselExampleInterval {
  display: absolute;
  flex-direction: column;
  align-items: flex-start;
  justify-content: flex-start;
  height: 100vh; /* zajistí, že karusel bude vyplňovat celou výšku okna prohlížeče */
}

.carousel-inner {
  text-align: left; /* Zarovná text na levý okraj */
}

#carouselExampleInterval .carousel-caption {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    text-align: left;
    background-color: rgba(0, 0, 0, 0.5); /* Případné pozadí pro lepší čitelnost textu */
    color: white; /* Barva textu */
    padding: 10px; /* Volitelný odsazovací prostor kolem textu */
  }
</style>

<body>

<section id="karusel">

<div class="container-fluid text-center align-items-start">

    <div class="row">

      <div class="col-12">
      <div id="carouselExampleInterval" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner">

              <div class="carousel-item active" data-bs-interval="5000">
                  
                  <img src="img/carousel2.jpg" alt="" class="d-block w-100 opacity-50 img-fluid" data-interval="5000">

                  <div class="carousel-caption d-md-block">

                    <section id="showcase" class="d-flex justify-content-left align-items-center mt-2 mb-2">
                      <img src="img/RECRA.png" width="200" height="200" alt="LOGO RECRA" loading="lazy" class="img-fluid">
                    </section>
                    
                    <div class="container bg-transparent">
                    <table class="table table-bordered border-primary">
                      <tr>
                        <td>
                          
                        <h1 class="text-center m-2">PŘIHLÁSIT SE</h1>

                        <form method="POST" action="prihlasit.php">
                          <div class="form-group col-lg-12">
                            <label for="formGroupExampleInput">Uživatel</label>
                            <input type="text" class="form-control mt-2" name="name" id="formGroupExampleInput" placeholder="Vaše uživatelské jméno" required>
                          </div>
                          <div class="form-group col-lg-12 mt-2">
                            <label for="formGroupExampleInput2">Heslo</label>
                            <input type="password" class="form-control mt-2" name="password" id="formGroupExampleInput2" placeholder="Vaše nejtajnější heslo" required>
                          </div>
                          <div class="form-group col-lg-12 mt-2">
                            <button type="submit" class="btn btn-primary mt-2 w-100">Přihlásit se</button>
                          </div>

                        </form>
                                                
                        </td>

                      </tr>

                    </table>
                    </div>
                   

                </div>

              </div>       
         
          </div>
    
          </div>
      </div> 

    </div>

  </div>

    </section>

<script src="js/bootstrap.js"></script>


</body>
</html>