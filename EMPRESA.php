<?php
//include('cls_Base.php');
class EMPRESA {
    //put your code here
    public function buscarDataEmpresa($emp_id,$est_id,$pemi_id) {
        //$conApp = yii::app()->db;
        $obj_con = new cls_Base();
        $conApp = $obj_con->conexionAppWeb();
        //$rawData = array();
        $sql = "SELECT A.EMP_ID,A.EMP_RUC Ruc,A.EMP_RAZONSOCIAL RazonSocial,A.EMP_NOM_COMERCIAL NombreComercial,
                    A.EMP_AMBIENTE Ambiente,A.EMP_TIPO_EMISION TipoEmision,EMP_DIRECCION_MATRIZ DireccionMatriz,EST_DIRECCION DireccionSucursal,
                    A.EMP_OBLIGA_CONTABILIDAD ObligadoContabilidad,EMP_CONTRI_ESPECIAL ContribuyenteEspecial,EMP_EMAIL_DIGITAL,
                    B.EST_NUMERO Establecimiento,C.PEMI_NUMERO PuntoEmision,A.EMP_MONEDA Moneda
                    FROM " . $obj_con->BdAppweb . ".EMPRESA A
                            INNER JOIN (" . $obj_con->BdAppweb . ".ESTABLECIMIENTO B
                                            INNER JOIN " . $obj_con->BdAppweb . ".PUNTO_EMISION C
                                                    ON B.EST_ID=C.EST_ID AND C.EST_LOG='1')
                                    ON A.EMP_ID=B.EMP_ID AND B.EST_LOG='1'
            WHERE A.EMP_ID='$emp_id' AND A.EMP_EST_LOG='1' 
                     AND B.EST_ID='$est_id' AND C.PEMI_ID='$pemi_id'";
        //echo $sql;
        //$rawData = $conApp->createCommand($sql)->queryAll(); //Varios registros =>  $rawData[0]['RazonSocial']
        //$rawData = $conApp->createCommand($sql)->queryRow();  //Un solo Registro => $rawData['RazonSocial']
        //$conCont->active = false;
        $sentencia = $conApp->query($sql);
        return $sentencia->fetch_assoc();
    }
}
