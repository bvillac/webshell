<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//Yii::import('system.vendors.PHPMailer.*'); //Usar de Forma nativa.
require_once('PHPMailerAutoload.php');
//require_once('../class.phpmailer.php');
//include('class.phpmailer.php');
class mailSystem {

    //put your code here
    public function enviarMail($body,$CabPed,$obj_var) {
        $mail = new PHPMailer();
        //$body = "Hola como estas";
        

        $mail->IsSMTP();
        //Para tls
        //$mail->SMTPSecure = 'tls';
        //$mail->Port = 587;
        //Para ssl
        $mail->SMTPSecure = "ssl";
        $mail->Port = 465;
        // la dirección del servidor, p. ej.: smtp.servidor.com
        $mail->Host = "mail.utimpor.com";

        // dirección remitente, p. ej.: no-responder@miempresa.com
        // nombre remitente, p. ej.: "Servicio de envío automático"
        $mail->setFrom('no-responder@utimpor.com', 'Servicio de envío automático Utimpor.com');
        //$mail->setFrom('bvillacreses@utimpor.com', 'Utimpor.com');

        // asunto y cuerpo alternativo del mensaje
        $mail->Subject = "Ha Recibido un(a) Orden Nuevo(a)!!!";
        $mail->AltBody = "Data alternativao";

        // si el cuerpo del mensaje es HTML
        $mail->MsgHTML($body);

        // podemos hacer varios AddAdress 
        //$mail->AddAddress($CabPed[0]["CorreoUser"], $CabPed[0]["NombreUser"]);//Usuario Autoriza Pedido
        //$mail->AddAddress($CabPed[0]["CorreoPersona"], $CabPed[0]["NombrePersona"]);//Usuario Genera Pedido
        $mail->AddAddress("byron_villacresesf@hotmail.com", "Byron Villa");
        //$mail->AddAddress("byronvillacreses@gmail.com", "Byron Villa");
        
        /******** COPIA OCULTA PARA VENTAS  ***************/
        //$mail->addBCC('ventas@utimpor.com', 'Ventas Utimpor'); //Para copia Oculta
        //$mail->addBCC('ventas2@utimpor.com', 'Ventas Utimpor'); //Para copia Oculta
        //$mail->addBCC('yalava@utimpor.com', 'Ventas Utimpor'); //Para copia Oculta
        
        $mail->addBCC('byronvillacreses@gmail.com', 'Byron Villa'); //Para con copia
        //$mail->addCC('byronvillacreses@gmail.com', 'ByronV'); //Para con copia
        //$mail->addReplyTo('byronvillacreses@gmail.com', 'First Last');
        //
        // si el SMTP necesita autenticación
        $mail->SMTPAuth = true;

        // credenciales usuario
        $mail->Username = "no-responder@utimpor.com";
        $mail->Password = "F0E4CwUyWy?h";
        $mail->CharSet = 'UTF-8';
        //$mail->SMTPDebug = 1;//Muestra el Error

        if (!$mail->Send()) {
            echo "Error enviando: " . $mail->ErrorInfo;
            //return $obj_var->messageSystem('NO_OK', "Error enviando: " . $mail->ErrorInfo, 11, null, null);
        } else {
            echo "¡¡Enviado!!";
            //return $obj_var->messageSystem('OK', "¡¡Enviado!!", 30, null, null);
        }
    }

}
