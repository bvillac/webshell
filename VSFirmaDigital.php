<?php
//Yii::import('system.vendors.nusoap.lib.*');
//require_once('nusoap.php');

include("nusoap/lib/nusoap.php");
//include('cls_Base.php');//para HTTP

class VSFirmaDigital {
    private $seaFirma = '/opt/SEAF/';
    private $seaFirext = 'p12'; //Extension de Firma Electronica


    public function recuperarFirmaDigital($id) {
        $obj_con = new cls_Base();
        $conApp = $obj_con->conexionAppWeb();
        $rawData = array();
        $sql = "SELECT Clave,RutaFile,Wdsl_local,SeaDocXml FROM " . $obj_con->BdAppweb . ".VSFirmaDigital WHERE idCompania=$id AND Estado=1";
        $sentencia = $conApp->query($sql);
        //$conApp->active = false;//Verificar
        return $sentencia->fetch_assoc();
    }

    
    
    public function firmaXAdES_BES($Documento,$DirDocFirmado) {
        //$obj = new VSFirmaDigital;
        $Dataf = $this->recuperarFirmaDigital('1');
        $fileCertificado = $this->seaFirma . base64_decode($Dataf['RutaFile']);
        $pass = base64_decode($Dataf['Clave']);
        $filexml = $Dataf['SeaDocXml'].$Documento;//$obj_var->seaDocXml. $Documento;
        $wdsl = $Dataf['Wdsl_local'];//'http://127.0.0.1:8080/FIRMARSRI/FirmaElectronicaSRI?wsdl';
        $param = array(
            'pathOrigen' => $filexml,
            'pathFirmado' => $DirDocFirmado,
            'pathCertificado' => $fileCertificado,
            'clave' => $pass,
            'nombreFirmado' => $Documento
        );
        //VSValidador::putMessageLogFile($param);
        $metodo = 'firmar';
        return $this->webServiceNuSoap($wdsl, $param, $metodo);
    }
    
    public function autorizacionComprobanteWS($ClaveAcceso) {
        $wdsl = Yii::app()->getSession()->get('Autorizacion', FALSE);//wsdl dependiendo del ambiente Configurado
        //$wdsl = 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantes?wsdl'; //Ruta del Web service SRI AutorizacionComprobantes
        $param = array(
            'claveAccesoComprobante' => $ClaveAcceso
        );
        $metodo = 'autorizacionComprobante';
        return $this->webServiceNuSoap($wdsl, $param, $metodo);
    }

    public function validarComprobanteWS($Documento,$DirDocFirmado) {
        $filexml = $DirDocFirmado . $Documento;
        $filebyte = $this->StrToByteArray(file_get_contents($filexml));
        $file64base = base64_encode(file_get_contents($filexml));
        $wdsl = Yii::app()->getSession()->get('Recepcion', FALSE);//wsdl dependiendo del ambiente Configurado
        //$wdsl = 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantes?wsdl'; //Ruta del Web service SRI RecepcionComprobantes
        $param = array(
            'xml' => $file64base
        );
        $metodo = 'validarComprobante';
        return $this->webServiceNuSoap($wdsl, $param, $metodo);
    }

    private function webServiceNuSoap($wdsl, $param, $metodo) {
        $client = new nusoap_client($wdsl, 'wsdl');
        $err = $client->getError();
        if ($err) {
            //echo 'Error en Constructor' . $err;
            $arroout["status"] = "NO";
            $arroout["error"] = $err;
            $arroout["message"] = 'Error en Constructor';
            $arroout["data"] = null;
            return $arroout;
        }
        
        $response = $client->call($metodo, $param);
        if ($client->fault) {
            //echo 'Existe un Problemas en el Envio';
            //print_r($response);
            $arroout["status"] = "NO";
            $arroout["error"] = $response;
            $arroout["message"] = 'Existe un Problemas en el Envio';
            $arroout["data"] = null;
            return $arroout;
        } else { // Chequea errores
            $err = $client->getError();
            if ($err) {  // Muestra el error
                //echo 'Error' . $err;
                $arroout["status"] = "NO";
                $arroout["error"] = $err;
                $arroout["message"] = 'Error en la Respuesta del CLiente';
                $arroout["data"] = null;
                return $arroout;
            } else {  // Muestra el resultado
                //print_r($response);
                $arroout["status"] = "OK";
                $arroout["error"] = $err;
                $arroout["message"] = 'Respuesta Ok WebService: '.$metodo;
                $arroout["data"] = $response;
                return $arroout;
            }
        }
    }
    
    
}