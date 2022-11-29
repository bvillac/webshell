<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('PHPMailerAutoload.php');
class mailSystem {
    /*private $domEmpresa='Utimpor.com';
    //private $mailSMTP='mail.utimpor.com';
    private $mailSMTP='marquis.websitewelcome.com';
    private $noResponder='no-responder@utimpor.com';
    private $adminMail='ecastro@utimpor.com';
    private $noResponderPass='MP1TQyb=PkcZ';//'ect{UZCJ6hvR';
    public $Subject='Ha Recibido un(a)  Nuevo(a)!!! ';
    public $file_to_attachXML='';
    public $file_to_attachPDF='';
    public $fileXML='';
    public $filePDF='';*/

    public $domEmpresa='';
    public $mailSMTP='';
    public $mailPort='';
    public $mailSMTPSecure='';
    public $mailCharSet='';
    public $noResponder='';
    public $adminMail='';
    public $noResponderPass='';//'ect{UZCJ6hvR';
    public $Subject='Ha Recibido un(a)  Nuevo(a)!!! ';
    public $file_to_attachXML='';
    public $file_to_attachPDF='';
    public $fileXML='';
    public $filePDF='';


    public function parametroServer($dataMail,$objEmpData){
        //Configuracion de ServerCorreo
        //Mail,NombreMostrar,Asunto,Clave,SMTPServidor,SMTPPuerto,CCO,
        //DominioEmpresa,CorreoAdmin,SmtpSSL,MailCharSet,Estad
        $SerMail=$objEmpData->buscarServerMail();
        $dataMail->domEmpresa= $SerMail["DominioEmpresa"];
        $dataMail->mailSMTP= $SerMail["SMTPServidor"];
        $dataMail->noResponder= $SerMail["Mail"];
        $dataMail->noResponderPass= $SerMail["Clave"];
        $dataMail->Subject= $SerMail["Asunto"];
        $dataMail->adminMail= $SerMail["CorreoAdmin"];
        $dataMail->mailPort= $SerMail["SMTPPuerto"];
        $dataMail->mailSMTPSecure= $SerMail["SmtpSSL"];
        $dataMail->mailCharSet= $SerMail["MailCharSet"]; 
    }
    
    //Valida si es un Email Correcto Devuelve True
    private function valid_email($val) {
        if (!filter_var($val, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return true;
    }

    //put your code here
    public function enviarMail($body,$CabPed,$obj_var,$usuData,$fil) {
        $objEmpData= new EMPRESA();
        $mail = new PHPMailer();
        //$body = "Hola como estas";
        
        $mail->IsSMTP();
        //Para tls
        //$mail->SMTPSecure = 'tls';
        //$mail->Port = 587;
        //Para ssl
        $mail->SMTPSecure = $this->mailSMTPSecure;
        $mail->Port = $this->mailPort;;
        // la dirección del servidor, p. ej.: smtp.servidor.com
        $mail->Host = $this->mailSMTP;//"mail.utimpor.com";
   
        
        
        //$mail->SMTPKeepAlive = true;   
        //$mail->Mailer = "smtp";

        // dirección remitente, p. ej.: no-responder@miempresa.com
        // nombre remitente, p. ej.: "Servicio de envío automático"
        $mail->setFrom($this->noResponder, 'Servicio de envío automático '.$this->domEmpresa);

        // asunto y cuerpo alternativo del mensaje
        $mail->Subject = $this->Subject;
        $mail->AltBody = "Data alternativao";

        // si el cuerpo del mensaje es HTML
        $mail->MsgHTML($body);
        
        //##############################################
        //Separa en Array los Correos Ingresados para enviar
        $DataCorreos = (trim($CabPed[$fil]["CorreoPer"])!='')?explode(";",$CabPed[$fil]["CorreoPer"]):0;
        for ($icor = 0; $icor < count($DataCorreos); $icor++) {
            if ($this->valid_email(trim($DataCorreos[$icor]))) {//Verifica Email Correcto
                $mail->AddAddress(trim($DataCorreos[$icor]), trim($CabPed[$fil]["RazonSoc"]));
            }else{
                //Correos Alternativos de admin  $adminMail
                $mail->addBCC("byron_villacresesf@hotmail.com", "Byron Villa");
                $mail->addBCC($usuData["CorreoUser"], $usuData["NombreUser"]);//Enviar Correos del Vendedor
            }
        }
        //if($DataCorreos==0){
            //Correos Alternativos de admin  $adminMail
            //$mail->addBCC(trim($this->adminMail), trim("Gerencia"));
            //$mail->addBCC("bvillacreses@utimpor.com", "Byron Villa");
            $mail->addBCC($usuData["CorreoUser"], $usuData["NombreUser"]);//Enviar Correos del Vendedor
        //}
        
        //##############################################
        // podemos hacer varios AddAdress 
        //$mail->AddAddress($CabPed[0]["CorreoUser"], $CabPed[0]["NombreUser"]);//Usuario Autoriza Pedido
        //$mail->AddAddress($CabPed[0]["CorreoPersona"], $CabPed[0]["NombrePersona"]);//Usuario Genera Pedido CorreoUser
        //$mail->AddAddress("byron_villacresesf@hotmail.com", "Byron Villa");        
        //$mail->AddAddress("byronvillacreses@gmail.com", "Byron Villa");
        
        /******** COPIA OCULTA PARA VENTAS  ***************/
        //$mail->addBCC('byronvillacreses@gmail.com', 'Byron Villa'); //Para con copia
        //$mail->addCC("bvillacreses@utimpor.com", "Byron Villa");
        //$mail->addReplyTo('byronvillacreses@gmail.com', 'First Last');
        
        //$mail->AddAttachment("archivo.zip");//adjuntos un archivo al mensaje
        $mail->AddAttachment($this->file_to_attachXML.$CabPed[$fil]["Ruc"].'/'.$this->fileXML,$this->fileXML);
        $mail->AddAttachment($this->file_to_attachPDF.$CabPed[$fil]["Ruc"].'/'.$this->filePDF,$this->filePDF);
        // si el SMTP necesita autenticación
        $mail->SMTPAuth = true;

        // credenciales usuario
        $mail->Username = $this->noResponder;
        $mail->Password = $this->noResponderPass;
        $mail->CharSet =  $this->mailCharSet;
        //$mail->SMTPDebug = 4;//Muestra el Error
        return $obj_var->messageSystem('OK', "¡¡Enviado!!", null, null, null);
        if (!$mail->Send()) {
            //echo "Error enviando: " . $mail->ErrorInfo;
            return $obj_var->messageSystem('NO_OK', "Error enviando: " . $mail->ErrorInfo, null, null, null);
        } else {
            //echo "¡¡Enviado!!";
            return $obj_var->messageSystem('OK', "¡¡Enviado!!", null, null, null);
        }
    }
    
    public function enviarMailError($DocData) {
        $mail = new PHPMailer();
        $body = 'Error en Documento '.$DocData["tipo"].'-'.$DocData["NumDoc"].'<BR>';
        $body .= 'Error '.$DocData["Error"];
        
        $mail->IsSMTP();
        $mail->SMTPSecure = "ssl";
        $mail->Port = 465;
        $mail->Host = $this->mailSMTP;//"mail.utimpor.com";
        $mail->setFrom($this->noResponder, 'Error Servicio de envío automático '.$this->domEmpresa);

        // asunto y cuerpo alternativo del mensaje
        $mail->Subject = $this->Subject;
        $mail->AltBody = "Data alternativao";

        // si el cuerpo del mensaje es HTML
        $mail->MsgHTML($body);
        $mail->AddAddress("bvillacreses@utimpor.com", "Ing.Byron Villa");

        $mail->SMTPAuth = true;

        // credenciales usuario
        $mail->Username = $this->noResponder;
        $mail->Password = $this->noResponderPass;
        $mail->CharSet = 'UTF-8';
        //$mail->SMTPDebug = 1;//Muestra el Error
        
        $mail->Send();

        /*if (!$mail->Send()) {
            //echo "Error enviando: " . $mail->ErrorInfo;
            return $obj_var->messageSystem('NO_OK', "Error enviando: " . $mail->ErrorInfo, null, null, null);
        } else {
            //echo "¡¡Enviado!!";
            return $obj_var->messageSystem('OK', "¡¡Enviado!!", null, null, null);
        }*/
    }

}
