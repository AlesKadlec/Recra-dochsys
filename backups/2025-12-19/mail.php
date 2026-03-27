<?php

$to = "ales.kadlec@godsc.cz,ota.vitous@hotmail.com";
$subject = "Předmět zprávy +ěščřžýáíé";
$message = "Toto je text zprávy poslán z recra serveru";
//$message = utf8_encode($message);
//$headers = "From: testmail@recra.cz";
//$headers .= "Content-Type: text/plain";

$headers = "From: testmail@recra.cz\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

if (mail($to, $subject, $message, $headers)) 
{
   echo "Email byl úspěšně odeslán.";
} 
else 
{
   echo "Email se nepodařilo odeslat.";
}

?>