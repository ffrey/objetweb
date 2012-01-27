<?php
//pour que la r�ponse s'affiche comme du texte brut
header('Content-Type: text/plain');
 
/*partie � modifier*/
$name = 'www.siteduzero.com';//nom du site
 
//pour ne pas devoir calculer � la main la longueur du corps, on le stocke dans une variable et la fonction strlen() nous la donne.
$data = 'variable=valeur&variable2=valeur2';
 
//la requ�te
$envoi  = "POST / HTTP/1.1\r\n";
$envoi .= "Host: ".$name."\r\n";
$envoi .= "Connection: Close\r\n";
$envoi .= "Content-type: application/x-www-form-urlencoded\r\n";
$envoi .= "Content-Length: ".strlen($data)."\r\n\r\n";
$envoi .= $data."\r\n";
/*/partie � modifier*/
 
/*ouverture socket*/
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if($socket < 0){
        die('FATAL ERROR: socket_create() : " '.socket_strerror($socket).' "');
}
 
if (socket_connect($socket,gethostbyname($name),80) < 0){
        die('FATAL ERROR: socket_connect()');
}
/*/ouverture socket*/
 
/*envoi demande*/
if(($int = socket_write($socket, $envoi, strlen($envoi))) === false){
        die('FATAL ERROR: socket_write() failed, '.$int.' characters written');
}
/*/envoi demande*/
 
/*lecture r�ponse*/
$reception = '';
while($buff = socket_read($socket, 2000)){
   $reception.=$buff;
}
echo $reception;
/*/lecture r�ponse*/
 
socket_close($socket);
?>