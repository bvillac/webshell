<?php

/*
 * Control de AUTORIZACION de Documentos 
 *  */
//include('VSFirmaDigital.php');
//include('VSexception.php');
class VSAutoDocumento {

    public function AutorizaDocumento($result,$ids,$i,$DirDocAutorizado,$DirDocFirmado,$DBTabDoc,$DocErr,$CampoID) {
        $firmaDig = new VSFirmaDigital();
        $firma = $firmaDig->firmaXAdES_BES($result['nomDoc'],$DirDocFirmado);
        //Verifica Errores del Firmado
        if ($firma['status'] == 'OK') {
            //Validad COmprobante
            $valComp = $firmaDig->validarComprobanteWS($result['nomDoc'],$DirDocFirmado); //Envio NOmbre Documento
            if ($valComp['status'] == 'OK') {//Retorna Datos del Comprobacion
                //Verifica si el Doc Fue Recibido Correctamente...
                $Rac = $valComp['data']['RespuestaRecepcionComprobante'];
                $estadoRac = $Rac['estado'];
                if ($estadoRac == 'RECIBIDA') {
                    //Continua con el Proceso
                    //Autorizacion de Comprobantes 
                    return $this->autorizaComprobante($result, $ids, $i, $DirDocAutorizado, $DirDocFirmado, $DBTabDoc, $DocErr, $CampoID);
                } else {
                    //Verifica si la Clave esta en Proceso de Validacion
                    if ($estadoRac == 'DEVUELTA') {
                        //Actualiza Errores de Documento Devuelto...
                        $AdrSri = $this->recibeDocSriDevuelto($Rac, $ids[$i], $result['nomDoc'], $DirDocFirmado,$DBTabDoc,$CampoID);
                        if (count($ids) == 1) {//Sale directamente si solo tiene un Domento para validadr
                            return $AdrSri; //Si la autorizacion es uno a uno.
                        }
                    }
                }
            } else {
                //Si Existe un error al realizar la peticion al Web Servicies
                //return $errAuto->messageSystem('NO_OK', $valComp["error"], 4, null, null);
                return VSexception::messageSystem('NO_OK', $valComp["error"], 4, null, null);
            }
        } else {
            //Sin No hay firma Automaticamente Hay que Parar el Envio
            //break;
            //return $errAuto->messageSystem('NO_OK', $firma["error"], 3, null, null);
            return VSexception::messageSystem('NO_OK', $firma["error"], 3, null, null);
        }
        //return $errAuto->messageSystem('OK', null,40,null, null);//Si nunka tuvo un Error Devuelve OK
        return VSexception::messageSystem('OK', null,40,null, null);//Si nunka tuvo un Error Devuelve OK
    }
    
    public function autorizaComprobante($result, $ids, $i, $DirDocAutorizado, $DirDocFirmado, $DBTabDoc, $DocErr, $CampoID) {
        $firmaDig = new VSFirmaDigital();
        //Continua con el Proceso
        //Autorizacion de Comprobantes
        $autComp = $firmaDig->autorizacionComprobanteWS($result['ClaveAcceso']); //Envio CLave de Acceso
        if ($autComp['status'] == 'OK') {
            //Validamos el Numero de Autorizacin que debe ser Mayor a 0
            $numeroAutorizacion = (int) $autComp['data']['RespuestaAutorizacionComprobante']['numeroComprobantes'];
            /*             * ****************************************************** */
            //Operacion de Stop, si no hay ningun Documento AUtorizado sale automaticamente de la funcion Autoriza
            //Su finalidad es que no siga realizado el resto de las operaciones y continuar con la siguiente.
            if ($numeroAutorizacion == 0) {
                $mError="No podemos encontrar la información que está solicitando.";
                return VSexception::messageSystem('NO_OK', '', 22, $mError, null);
            }//Por favor volver a Intentar en unos minutos
            /*             * ****************************************************** */
            $autorizacion = $autComp['data']['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion'];
            if ($numeroAutorizacion == 1) {//Verifica si Autorizo algun Comprobante Apesar de recibirlo Autorizo Comprobante
                $AdrSri = $this->actualizaDocRecibidoSri($autorizacion, $ids[$i], $result['nomDoc'], $DirDocAutorizado, $DirDocFirmado, $DBTabDoc, $DocErr, $CampoID);
                $this->newXMLDocRecibidoSri($autorizacion, $result['nomDoc'], $DirDocAutorizado);
                if (count($ids) == 1) {//Sale directamente si solo tiene un Domento para validadr
                    return $AdrSri; //Si la autorizacion es uno a uno.
                }
            } else {
                //Ingresa si el Documento a intentado Varias AUTORIZACIONES
                if ($numeroAutorizacion > 1) {
                    for ($c = 0; $c < sizeof($autorizacion); $c++) {
                        $this->actualizaDocRecibidoSri($autorizacion[$c], $ids[$i], $result['nomDoc'], $DirDocAutorizado, $DirDocFirmado, $DBTabDoc, $DocErr, $CampoID);
                        if ($autorizacion[$c]['estado'] == 'AUTORIZADO') {
                            $this->newXMLDocRecibidoSri($autorizacion[$c], $result['nomDoc'], $DirDocAutorizado);
                            break; //Si de todo el Recorrido Existe un Autorizado 
                        }
                    }
                }
            }
        } else {
            $mError="(Error al Realizar la Autorizacion del Documento)";
            return VSexception::messageSystem('NO_OK', $autComp["error"], 22, $mError, null);
        }
    }

    private function actualizaDocRecibidoSri($response,$ids,$NombreDocumento,$DirDocAutorizado,$DirDocFirmado,$DBTabDoc,$DocErr,$CampoID) {
        $obj_con = new cls_Base();
        $con = $obj_con->conexionIntermedio();
        $msg= new VSexception();
        try {
            $UsuId=1;//Usuario Admin //Yii::app()->getSession()->get('user_id', FALSE);
            $estado = $response['estado'];
            $fecha = date("Y-m-d H:i:s", strtotime($response['fechaAutorizacion']));//date(Yii::app()->params["datebytime"], strtotime($response['fechaAutorizacion']));
            $numeroAutorizacion='';
            $CodigoError='';
            $DescripcionError='';
            if($estado=='AUTORIZADO'){
                $numeroAutorizacion = ($response['numeroAutorizacion']!=null)?$response['numeroAutorizacion']:'';
                $codEstado='2';
                $DirectorioDocumento=$DirDocAutorizado;
                $op=15;//Su documento fue autorizado correctamente
            }else{
                $codEstado='3';
                $op=16;//Su documento fue rechazado o negado
                $mensaje=$response['mensajes']['mensaje'];//Array de Errores Sri
                $this->mensajeErrorDocumentos($con,$mensaje,$ids,$DocErr);
                $CodigoError=$mensaje[0]['identificador'];
                $InformacionAdicional=(!empty($mensaje[0]['informacionAdicional']))?$mensaje[0]['informacionAdicional']:'';
                $DescripcionError=utf8_encode("ID=>$CodigoError Error=> $InformacionAdicional");
                $DirectorioDocumento=$DirDocFirmado;
            }
            
            $sql = 'UPDATE ' . $obj_con->BdIntermedio . '.'.$DBTabDoc.' SET 
                                AutorizacionSRI="'.$numeroAutorizacion.'",FechaAutorizacion="'.$fecha.'",
                                DirectorioDocumento="'.$DirectorioDocumento.'",NombreDocumento="'.$NombreDocumento.'",
                                EstadoDocumento="'.$estado.'",Estado="'.$codEstado.'",
                                DescripcionError="'.$DescripcionError.'",CodigoError="'.$CodigoError.'",USU_ID="'.$UsuId.'"
                            WHERE '.$CampoID.'='.$ids ;
            //echo $sql;
            $command = $con->prepare($sql);
            $command->execute();

            $con->commit();
            $con->close();
            $mError="No podemos encontrar la información que está solicitando.";
            return VSexception::messageSystem('NO_OK', '', 22, $mError, null);
        } catch (Exception $e) {
            $con->rollback();
            $con->close();
            //throw $e;
            return VSexception::messageSystem('NO_OK', $e->getMessage(), 41, null, null);
        }
    }
    
    private function recibeDocSriDevuelto($response, $ids, $NombreDocumento, $DirDocFirmado,$DBTabDoc,$CampoID) {
        $ids="22863";
        $obj_con = new cls_Base();
        $con = $obj_con->conexionIntermedio();
        try {
            $UsuId=1;//Usuario Admin //Yii::app()->getSession()->get('user_id', FALSE);
            $estado = $response['estado'];
            $CodigoError = '';
            $DescripcionError = '';
            $comprobanteRac = $response['comprobantes']['comprobante'];
            $codEstado = '4';
            $mensaje = $comprobanteRac['mensajes']['mensaje']; //Array de Errores Sri
            //$this->mensajeErrorDocumentos($con, $mensaje, $ids, 'FACTURA');
            $CodigoError = $mensaje['identificador'];
            $MensajeSRI = $mensaje['mensaje'];
            $InformacionAdicional = (!empty($mensaje['informacionAdicional'])) ? $mensaje['informacionAdicional'] : '';
            $DescripcionError = utf8_encode("IdFact=>$ids ID=>$CodigoError MensSri=>($MensajeSRI) InfAdicional=>($InformacionAdicional)");
            $DirectorioDocumento = $DirDocFirmado;

            $sql = 'UPDATE ' . $obj_con->BdIntermedio . '.'.$DBTabDoc.' SET 
                                DirectorioDocumento="'.$DirectorioDocumento.'",NombreDocumento="'.$NombreDocumento.'",
                                EstadoDocumento="'.$estado.'",Estado="'.$codEstado.'",
                                DescripcionError="'.$DescripcionError.'",CodigoError="'.$CodigoError.'",USU_ID="'.$UsuId.'"
                            WHERE '.$CampoID.'='.$ids ;
            //echo $sql;
            $command = $con->prepare($sql);
            $command->execute();

            $con->commit();
            $con->close();
            //Su documento fue devuelto por errores en el comprobante
            //Dependiendo del Error arrojado por el SRI
            return VSexception::messageWSSRI('OK', null, $CodigoError, $MensajeSRI, null);//Web service Sri
        } catch (Exception $e) {
            $con->rollback();
            $con->close();
            //throw $e;
            return VSexception::messageSystem('NO_OK', $e->getMessage(), 41, null, null);
        }
    }
    
    private function mensajeErrorDocumentos($con, $mensaje, $ids, $tipDoc) {
        $IdFactura='';$IdRetencion='';$IdNotaCredito='';$IdNotaDebito='';$IdGuiaRemision='';
        switch ($tipDoc) {
            case 'FACTURA':
                $IdFactura=$ids;
                break;
            case 'RETENCION':
                $IdRetencion=$ids;
                break;
            case 'NOTA_CREDITO':
                $IdNotaCredito=$ids;
                break;
            case 'NOTA_DEBITO':
                $IdNotaDebito=$ids;
                break;
            default:
                $IdGuiaRemision=$ids;
        }
        for ($i = 0; $i < sizeof($mensaje); $i++) {
            $Identificador=$mensaje[$i]['identificador'];
            $TipoMensaje=$mensaje[$i]['tipo'];
            $Mensaje=$mensaje[$i]['mensaje'];
            $InformacionAdicional=(!empty($mensaje[$i]['informacionAdicional']))?$mensaje[$i]['informacionAdicional']:'';
            $sql = "INSERT INTO " . $con->BdIntermedio . ".NubeMensajeError 
                 (IdFactura,IdRetencion,IdNotaCredito,IdNotaDebito,IdGuiaRemision,Identificador,TipoMensaje,Mensaje,InformacionAdicional)
                 VALUES
                 ('$IdFactura','$IdRetencion','$IdNotaCredito','$IdNotaDebito','$IdGuiaRemision','$Identificador','$TipoMensaje','$Mensaje','$InformacionAdicional')";

            $command = $con->prepare($sql);
            $command->execute();
        }
    }
    
    private function newXMLDocRecibidoSri($response,$NombreDocumento,$DirDocAutorizado) {
        $estado = $response['estado'];
        if ($estado == 'AUTORIZADO') {
            $xmldata=$this->xmlAutoSri($response);
            file_put_contents($DirDocAutorizado . $NombreDocumento, $xmldata); //Escribo el Archivo Xml
            $arroout["status"] = "OK";
            $arroout["error"] = null;
            $arroout["message"] = 'El Xml se recibio correctamente';
            $arroout["data"] = null;
        } else {
            $arroout["status"] = "NO";
            $arroout["error"] = null;
            $arroout["message"] = $NombreDocumento .'El Xml no se Genero';
            $arroout["data"] = null;
        }
        if($arroout["status"]=="NO"){
            cls_Global::putMessageLogFile($arroout);//Imprime el Error en Logs
        }
        return $arroout;
    }
    private function xmlAutoSri($response) {
        $xmldata = '<?xml version="1.0" encoding="UTF-8"?>';
        $xmldata .= '<autorizacion>';
            $xmldata .= '<estado>' . $response["estado"] . '</estado>';
            $xmldata .= '<numeroAutorizacion>' . $response["numeroAutorizacion"] . '</numeroAutorizacion>';
            $xmldata .= '<fechaAutorizacion class="fechaAutorizacion">' . $this->getFechaAuto($response["fechaAutorizacion"]) . '</fechaAutorizacion>';
            $xmldata .= '<comprobante><![CDATA[' . $response["comprobante"] . ']]></comprobante>';
        $xmldata .= '</autorizacion>';
        return $xmldata;
    }
    
    private function getFechaAuto($fecha) {
         //formato de Fecha Autorizacion=>2014-12-02T21:34:15.637-05:00 =>   02/10/2014 18:59:27 
        $aux = explode(".", trim($fecha));
        $aux = explode("T", trim($aux[0]));//Separamos por medio de la T
        $fecha=date('d/m/Y', strtotime($aux[0])).' '.$aux[1];
        return $fecha;
    }
    
    public function actualizaClaveAccesoDocumento($ids,$clave,$DBTabDoc,$CampoID) {
        $con = Yii::app()->dbvsseaint;
        $trans = $con->beginTransaction();
        try {
            $sql = "UPDATE " . $con->dbname . ".$DBTabDoc SET ClaveAcceso='$clave' WHERE $CampoID='$ids'";
            //echo $sql;
            $command = $con->createCommand($sql);
            $command->execute();
            $trans->commit();
            $con->active = false;
            return true;
        } catch (Exception $e) {
            $trans->rollback();
            $con->active = false;
            throw $e;
            return false;
        }
    }

}
