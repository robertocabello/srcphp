<?php

include("class.phpmailer.php");

$mail = new PHPMailer();

$mail->isSMTP();
$mail->SMTPDebug = 2;
$mail->Debugoutput='html';
$mail->Host = 'smtp.gmail.com';
$mail->Port = 465;
$mail->SMTPSecure = 'ssl';
$mail->SMTPAuth = true;
$mail->Username   = "soporte@supplyme.cl";
$mail->Password = "soporte2020";

//$mail->Host = "localhost"; 
$mail->From = $noreply;
$mail->FromName = $dominio;
$mail->Subject = "Formulario Test";
$mail->AddAddress('mpastor@webseo.cl');

$body = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>";
$body.= "<html xmlns='http://www.w3.org/1999/xhtml'>
<head>
  <title>Formulario de Contacto ".$nombre_fantasia."</title><meta name='viewport' content='width=device-width, initial-scale=1.0'/>
</head>
<body style='width:100%;background-color:#f5f5f5;'>
  <div style='max-width:600px;background-color:#fff;border-top: 4px solid#8fc84c;margin:0 auto;'>
    <p style='text-align:center;'><img src='".$baseurl."img/logo.png' style='margin:0 auto;'></p>";
    $body.= "<p style='text-align:center;font-size:19px;color:#8fc84c;font-family:Arial;'>Formulario Test</p>";
  $body.= "</div>
</body>
</html>";
$mail->Body = utf8_decode($body);
$mail->IsHTML(true);
if(!$mail->Send()){
	
}else{
	
}

//var_dump($_POST);
?>