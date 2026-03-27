<?php
// Zahrnutí souboru s údaji o připojení
include('db.php');

?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/> 
    

	<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

	<link rel="stylesheet" href="css/style.css">

    <title>PŘIHLÁŠENÍ DO RECRA SYSTÉMU</title>

    <style>
        h3 {font-family: 'Montserrat', 'Medium 500';}
    </style>

</head>

<body>

<body class="img js-fullheight" style="background-image: url(img/carousel2.jpg);">

<ul class="nav">
  <li class="nav-item">
    <a class="navbar-brand">
      <img src="img/logo.png" alt="Bootstrap" width="400" height="120" class="img-fluid img-thumbnail bg-transparent">
    </a>
  </li>
</ul>

<section class="ftco-section">
    <div class="container">
     
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="login-wrap p-0">
                
                    <h3 class="mb-4 text-center ">PŘIHLASTE SE</h3>
                    
                    <form method="POST" action="prihlasit.php" class="signin-form">
                        <div class="form-group">
                            <input type="text" class="form-control" name="name" id="name" placeholder="Vaše uživatelské jméno" required>
                        </div>
                        
                        <div class="form-group">
                        <input type="password" class="form-control" name="password" id="password" placeholder="Vaše nejtajnější heslo" required>
                        <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="form-control btn btn-primary submit px-3">Přihlásit se</button>
                        </div>
                        
                    </form>

                </div>
            </div>
        </div>
    </div>
</section>

</body>      
                                

<script src="js/bootstrap.js"></script>

</html>