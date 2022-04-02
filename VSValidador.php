<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class VSValidador {
    
    public function tipoIdent($cedula){
        $obj_var = new cls_Global();//Agregar globales
        $valor='07';//Consumidor Final por Defecto
        IF(strlen($cedula)>10) {
            IF(strlen($cedula)==13){
                $valor='04';//VENTA CON RUC
            }ELSEIF(strlen($cedula)>13){
                $valor='06';//VENTA CON PASAPORTE
            }
        }ELSE{
            //IF($cedula==Yii::app()->params['consumidorfinal']){//Esta vericacion depende de la empresa
            IF($cedula==$obj_var->consumidorfinal){//Esta vericacion depende de la empresa
                $valor='07';//VENTA A CONSUMIDOR FINAL*  SON 13 DIGITOS
            }ELSE{
                $valor='05';//VENTA CON CEDULA
            }
        }
        return $valor;

    }
    public function ajusteNumDoc($numDoc,$num){
        $result='';
        IF(strlen($numDoc)<$num){
            $result=$this->add_ceros($numDoc,$num);//Ajusta los 9
        }ELSE{
            $result=substr($numDoc, -($num));//Extrae Solo 9
        }
        return $result;
    }
    public function add_ceros($numero, $ceros) {
        /* Ejemplos para usar.
          $numero="123";
          echo add_ceros($numero,8) */
        $order_diez = explode(".", $numero);
        $dif_diez = $ceros - strlen($order_diez[0]);
        for ($m = 0; $m < $dif_diez; $m++) {
            @$insertar_ceros .= 0;
        }
        return $insertar_ceros .= $numero;
    }
    //*****/^[\w-\.]{3,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}$/
    
}
