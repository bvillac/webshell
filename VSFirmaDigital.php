<?php
include("nusoap/lib/nusoap.php");
class VSFirmaDigital {
    
    private $seaFirma = '/opt/SEAF/';
    private $seaFirext = 'p12'; //Extension de Firma Electronica
    private $Recepcion="";
    private $Autorizacion="";
            
    function __construct() {
       //cls_Global::putMessageLogFile("En el constructor BaseClass\n");
       $Resul=  EMPRESA::buscarAmbienteEmp(cls_Global::$emp_id, cls_Global::$ambt_id);
       $this->Recepcion=$Resul['Recepcion'];
       $this->Autorizacion=$Resul['Autorizacion'];
    }


    public function recuperarFirmaDigital($id) {
        $obj_con = new cls_Base();
        $conApp = $obj_con->conexionAppWeb();
        $sql = "SELECT Clave,RutaFile,Wdsl_local,SeaDocXml FROM " . $obj_con->BdAppweb . ".VSFirmaDigital WHERE EMP_ID=$id AND Estado=1";
        $sentencia = $conApp->query($sql);
        $conApp->close();
        return $sentencia->fetch_assoc();
    }

    
    
    public function firmaXAdES_BES($Documento,$DirDocFirmado) {
        $Dataf = $this->recuperarFirmaDigital(cls_Global::$emp_id);
        $fileCertificado = $this->seaFirma . base64_decode($Dataf['RutaFile']);
        $pass = base64_decode($Dataf['Clave']);
        $filexml = $Dataf['SeaDocXml'].$Documento;
        $wdsl = $Dataf['Wdsl_local'];//'http://127.0.0.1:8080/FIRMARSRI/FirmaElectronicaSRI?wsdl';
        $param = array(
            'pathOrigen' => $filexml,
            'pathFirmado' => $DirDocFirmado,
            'pathCertificado' => $fileCertificado,
            'clave' => $pass,
            'nombreFirmado' => $Documento
        );
        //cls_Global::putMessageLogFile($obj_var->emp_id);
        $metodo = 'firmar';
        return $this->webServiceNuSoap($wdsl, $param, $metodo);
    }
    
    public function autorizacionComprobanteWS($ClaveAcceso) {
        $wdsl = $this->Autorizacion;//Yii::app()->getSession()->get('Autorizacion', FALSE);//wsdl dependiendo del ambiente Configurado
        $param = array(
            'claveAccesoComprobante' => $ClaveAcceso
        );
        $metodo = 'autorizacionComprobante';
        return $this->webServiceNuSoap($wdsl, $param, $metodo);
    }

    public function validarComprobanteWS($Documento,$DirDocFirmado) {
        $filexml = $DirDocFirmado . $Documento;
        if (file_exists($filexml)) {//si la Ruta y el archivo Existe Continua
            //Archivo Existe
            $filebyte = $this->StrToByteArray(file_get_contents($filexml));
            $file64base = base64_encode(file_get_contents($filexml));			
            $wdsl = $this->Recepcion;//Yii::app()->getSession()->get('Recepcion', FALSE);//wsdl dependiendo del ambiente Configurado
            //$wdsl = 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantes?wsdl'; //Ruta del Web service SRI RecepcionComprobantes
            $param = array(
                'xml' => $file64base
            );
            $metodo = 'validarComprobante';
            return $this->webServiceNuSoap($wdsl, $param, $metodo);
        }else{
            //Archivo No existe
            $arroout["status"] = "NO";
            $arroout["error"] = 'Error No Existe el Archivo';
            $arroout["message"] = 'Error No Existe el Archivo';
            $arroout["data"] = null;
            return $arroout;            
        }
        
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
                $arroout["error"] = $err . ':=> Error en la Respuesta Cliente o No esta habilitado el Servicio > Uso de Caracteres Especiales';
                $arroout["message"] = 'Error en la Respuesta Cliente o No esta habilitado el Servicio > Uso de Caracteres Especiales';
                $arroout["data"] = null;
                return $arroout;
            } else {  // Muestra el resultado
                //print_r($response);//Descomentar para ver el error
                $arroout["status"] = "OK";
                $arroout["error"] = $err;
                $arroout["message"] = 'Respuesta Ok WebService: '.$metodo;
                $arroout["data"] = $response;
                return $arroout;
            }
        }
    }
    
    private function StrToByteArray($string) {
        $bytes = array();
        for ($i = 0; $i < strlen($string); $i++) {
            $bytes[] = ord($string[$i]);
        }
        return $bytes;
    }

    public function ByteArrayToStr($bytes) {
        $string = "";
        foreach ($bytes as $chr) {
            $string .= chr($chr);
        }
        return $string;
    }
    
    
}