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
    public function messageSystem($status, $error, $op, $message, $data) {
        $arroout["status"] = $status;
        $arroout["error"] = $error;
        $arroout["message"] = $this->messageError($op, $message);
        $arroout["data"] = $data;
        return $arroout;
    }
    
    //Mensajes Web Services Sri
    public function messageWSSRI($status, $error, $op, $message, $data) {
        $arroout["status"] = $status;
        $arroout["error"] = $error;
        $arroout["message"] = $this->messageWSSRIError($op, $message);//$message ." Error Nº ".$op;//
        $arroout["data"] = $data;
        return $arroout;
    }

    public function messageFileXML($status, $nomDocfile, $ClaveAcceso, $op, $message, $data) {
        $arroout = array(
            'status' => $status,
            'nomDoc' => $nomDocfile,
            'ClaveAcceso' => $ClaveAcceso,
            'message' => $this->messageError($op, $message),
            'data' => $data
        );
        return $arroout;
    }

    public function messagePedidos($status, $numDoc, $nomDoc, $error, $op, $message, $data) {
        $arroout = array(
            'status' => $status,
            'documento' => ' Pedido Nº: ' . $nomDoc . '-' . $numDoc,
            'error' => $error,
            'message' => $this->messageError($op, $message),
            'data' => $data
        );
        return $arroout;
    }

    private function messageError($op, $message) {
        $messageError = '';
        switch ($op) {
            case 1:
                //Documento no se Encontro.
                $messageError = Yii::t('EXCEPTION', 'Error document was not found.');
                break;
            case 2:
                $messageError = Yii::t('EXCEPTION', 'Gender xml file correctly.');
                break;
            case 3:
                $messageError = Yii::t('EXCEPTION', 'Failed to perform the signed document.');
                break;
            case 4:
                $messageError = Yii::t('EXCEPTION', 'Failed to perform validation of the document.');
                break;
            case 6://Petion invalida volver a intentar
                //$messageError=Yii::t('EXCEPTION', 'Invalid request. Please do not repeatt this request again.');
                break;
            case 10://Petion invalida volver a intentar
                $messageError = Yii::t('EXCEPTION', '<strong>Well done!</strong> your information was successfully saved.');
                break;
            case 11://Petion invalida no volver a intentar
                $messageError = Yii::t('EXCEPTION', 'Invalid request. Please do not repeatt this request again.');
                break;
            case 12://Datos eliminados Correctamente
                $messageError = Yii::t('EXCEPTION', '<strong>Well done!</strong> your information was successfully delete.');
                break;
            case 13://Datos eliminados Correctamente
                $messageError = Yii::t('EXCEPTION', '<strong>Well done!</strong> your information was successfully cancel.');
                break;
            case 15://Datos eliminados Correctamente
                $messageError = Yii::t('EXCEPTION', '<strong>Well done!</strong> Your document was properly authorized.');
                break;
            case 16://Datos eliminados Correctamente
                $messageError = Yii::t('EXCEPTION', 'His paper was rejected or denied.');
                break;
            case 17://Su documento fue Devuelto por errores en el documento
                $messageError = Yii::t('EXCEPTION', 'Your document was returned for errors in the voucher.');
                break;
            case 20://La solicitud fÚe realizada correctamente.
                $messageError = Yii::t('EXCEPTION', 'The request was completed successfully.');
                break;
            case 21://No podemos encontrar los datos que está solicitando.
                $messageError = Yii::t('EXCEPTION', 'We can not find the information you are requesting.');
                break;
            case 22://Por favor vuelva a intentar despues de unos minutos
                $messageError = Yii::t('EXCEPTION', 'Please come back in a while.');
                break;
            case 30://Su Orden fue guardada correctamente.
                $messageError = Yii::t('EXCEPTION', '<strong>Well done!</strong> your order was successfully saved.');
                break;
            case 40://Su Orden fue envia correctamente.
                $messageError = Yii::t('EXCEPTION', '<strong>Well done!</strong> your information was successfully send.');
                break;
            case 41://Su Orden fue guardada correctamente.
                $messageError = Yii::t('EXCEPTION', 'Failed to send the document, check with your Web Manager.');
                break;
            case 42://Este documento ya fue autorizado por SRI.
                $messageError = Yii::t('EXCEPTION', 'This document was already authorized by SRI.');
                break;
            case 43://Este documento ya fue autorizado por SRI.
                $messageError = Yii::t('EXCEPTION', 'Access key registered, retry in a few minutes.');
                break;
            case 44://Este documento ya fue autorizado por SRI.
                $messageError = Yii::t('EXCEPTION', 'At a time when your mail will be sent.');
                break;

            default:
                $messageError = $message;
        }
        return $messageError;
    }
    private function messageWSSRIError($op, $message) {
        $messageError = '';
        switch ($op) {
            case 43:
                //Clave de Acceso Registrada
                $messageError = Yii::t('EXCEPTION', 'Access key registered, retry in a few minutes.');
                break;
            default:
                $messageError = $message ." Error Nº ".$op;//
        }
        return $messageError;
    }

}
