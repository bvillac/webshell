<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of VSexception
 *
 * @author root
 */
class VSexception {
    //NOTA: no poner estas funciones como static

    //put your code here
    public static function messageSystem($status, $error, $op, $message, $data) {
        $arroout["status"] = $status;
        $arroout["error"] = $error;
        $arroout["message"] = VSexception::messageError($op, $message);
        $arroout["data"] = $data;
        if($status=="NO_OK"){
            cls_Global::putMessageLogFile($arroout);//Imprime el Error en Logs
            //cls_Global::putMessageLogFile($message);
        }
        return $arroout;
    }
    
    //Mensajes Web Services Sri
    public static function messageWSSRI($status, $error, $op, $message, $data) {
        $arroout["status"] = $status;
        $arroout["error"] = $error;
        $arroout["message"] = VSexception::messageWSSRIError($op, $message);  //$this->messageWSSRIError($op, $message);//$message ." Error Nº ".$op;//
        $arroout["data"] = $data;
        //if($status=="NO_OK"){
            cls_Global::putMessageLogFile($arroout);//Imprime el Error en Logs
        //}
        return $arroout;
    }

    public static function messageFileXML($status, $nomDocfile, $ClaveAcceso, $op, $message, $data) {
        $arroout = array(
            'status' => $status,
            'nomDoc' => $nomDocfile,
            'ClaveAcceso' => $ClaveAcceso,
            'message' => VSexception::messageError($op, $message),
            'data' => $data
        );
        if($status=="NO_OK"){
            cls_Global::putMessageLogFile($arroout);//Imprime el Error en Logs
            cls_Global::putMessageLogFile($message);
        }
        return $arroout;
    }


    private function messageError($op, $message) {
        $messageError = '';
        switch ($op) {
            case 1:
                //Documento no se Encontro.
                $messageError = 'Error document was not found.';
                break;
            case 2:
                $messageError = 'Gender xml file correctly.';
                break;
            case 3:
                $messageError = 'Failed to perform the signed document.';
                break;
            case 4:
                $messageError = 'Failed to perform validation of the document.';
                break;
            case 6://Petion invalida volver a intentar
                //$messageError=Yii::t('EXCEPTION', 'Invalid request. Please do not repeatt this request again.');
                break;
            case 10://Petion invalida volver a intentar
                $messageError = '<strong>Well done!</strong> your information was successfully saved.';
                break;
            case 11://Petion invalida no volver a intentar
                $messageError = 'Invalid request. Please do not repeatt this request again.';
                break;
            case 12://Datos eliminados Correctamente
                $messageError = '<strong>Well done!</strong> your information was successfully delete.';
                break;
            case 13://Datos eliminados Correctamente
                $messageError = '<strong>Well done!</strong> your information was successfully cancel.';
                break;
            case 15://Datos eliminados Correctamente
                $messageError = '<strong>Well done!</strong> Your document was properly authorized.';
                break;
            case 16://Datos eliminados Correctamente
                $messageError = 'His paper was rejected or denied.';
                break;
            case 17://Su documento fue Devuelto por errores en el documento
                $messageError = 'Su documento fue Devuelto por errores en el documento';
                break;
            case 20://La solicitud fÚe realizada correctamente.
                $messageError = 'The request was completed successfully.';
                break;
            case 21://No podemos encontrar los datos que está solicitando.
                $messageError = 'We can not find the information you are requesting.';
                break;
            case 22://Por favor vuelva a intentar despues de unos minutos
                $messageError = 'Please come back in a while.';
                break;
            case 30://Su Orden fue guardada correctamente.
                $messageError = '<strong>Well done!</strong> your order was successfully saved.';
                break;
            case 40://Su Orden fue envia correctamente.
                $messageError = '<strong>Well done!</strong> your information was successfully send.';
                break;
            case 41://Su Orden fue guardada correctamente.
                $messageError = 'Failed to send the document, check with your Web Manager.';
                break;
            case 42://Este documento ya fue autorizado por SRI.
                $messageError = 'This document was already authorized by SRI.';
                break;
            case 43://Este documento ya fue autorizado por SRI.
                $messageError = 'Access key registered, retry in a few minutes.';
                break;
            case 44://Este documento ya fue autorizado por SRI.
                $messageError = 'At a time when your mail will be sent.';
                break;

            default:
                $messageError = $message;
        }
        return $messageError;
    }
    private static function messageWSSRIError($op, $message) {
        $messageError = '';
        switch ($op) {
            case 43:
                //Clave de Acceso Registrada
                $messageError = "Clave de acceso registrada, vuelva a intentarlo en unos minutos. =>>> ".$message;
                break;
            default:
                $messageError = $message ." Error Nº ".$op;//
        }
        return $messageError;
    }

}
