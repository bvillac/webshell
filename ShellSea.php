<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ShellSea
 *
 * @author root
 */
include('cls_Base.php');//para HTTP
include('cls_Global.php');//para HTTP
include('VSexception.php');
class ShellSea {
    //put your code here
    public function importacionLinea($tipo) {
        //echo "llego2";
        $codLin = '02';
        $tipConsult = $tipo;
        $dtActFecha = date("Y-m", strtotime(date()));//'2019-02';//
        //$dtAntFecha = date("Y-m", strtotime(date()));//restarle 1 mes//'2019-01';//
	$dtAntFecha = date("Y-m", strtotime('-1 month', strtotime(date())));//Se resta 1 mes.
        try {
            $obj_con = new cls_Base();
            $obj_var = new cls_Global();
            $con = $obj_con->conexionServidor();
            //$rawDataANT = array();
            $rawDataACT = array();
            
            $sql = "SELECT A.COD_LIN, A.COD_TIP, A.COD_MAR, D.NOM_LIN, E.NOM_TIP, F.NOM_MAR,"
                    . "SUM(A.P_PROME*B.EXI_TOT)  AS COS_ACT, ";
            $sql .= "IFNULL((SELECT X.COSTO_T FROM " . $obj_con->BdServidor . ".IG0007 X "
                    . " WHERE X.COD_LIN=A.COD_LIN AND X.COD_MAR=A.COD_MAR AND TIP_CON='$tipConsult' "
                    . " AND ANO_MES='$dtAntFecha'),0) COS_ANT ";            
            $sql .= "    FROM " . $obj_con->BdServidor . ".IG0020 A ";
            $sql .= "        INNER JOIN " . $obj_con->BdServidor . ".IG0022 B ON A.COD_ART=B.COD_ART ";
            $sql .= "        INNER JOIN " . $obj_con->BdServidor . ".IG0001 D ON A.COD_LIN=D.COD_LIN ";
            $sql .= "        INNER JOIN " . $obj_con->BdServidor . ".IG0002 E ON A.COD_TIP=E.COD_TIP ";
            $sql .= "        INNER JOIN " . $obj_con->BdServidor . ".IG0003 F ON A.COD_MAR=F.COD_MAR ";
            $sql .= "    WHERE  B.EXI_TOT <> 0  ";            
            $sql .=($codLin!='')?" AND A.COD_LIN='$codLin' ":"";
            $sql .=($tipConsult!='TD')?" AND A.COD_TIP='$tipConsult' ":"";
            $sql .=" GROUP BY A.COD_LIN,A.COD_MAR ORDER BY COD_LIN,COD_MAR ";
            $sentencia = $con->query($sql);
            if ($sentencia->num_rows > 0) {
                while ($fila = $sentencia->fetch_assoc()) {//Array Asociativo
                    $rawDataACT[] = $fila;
                }
            }
            //cls_Global::putMessageLogFile($sql);
            for ($i = 0; $i < sizeof($rawDataACT); $i++) {
                
                $sql="INSERT INTO " . $obj_con->BdServidor . ".IG0007 
                    (COD_LIN,COD_TIP,COD_MAR,NOM_MAR,COST_ANT,COST_ACT,COSTO_T,FEC_SIS,TIP_CON,ANO_MES)VALUES 
                        ('" . $rawDataACT[$i]['COD_LIN'] . "',
                        '" . $rawDataACT[$i]['COD_TIP'] . "',
                        '" . $rawDataACT[$i]['COD_MAR'] . "',
                        '" . $rawDataACT[$i]['NOM_MAR'] . "',
                        '" . $rawDataACT[$i]['COS_ANT'] . "',
                        '" . $rawDataACT[$i]['COS_ACT'] . "',
                        '" . $rawDataACT[$i]['COS_ACT'] . "',
                        '" . $dtActFecha . "-01',
                        '" . $tipConsult . "',
                        '" . $dtActFecha . "')";
                //cls_Global::putMessageLogFile($rawDataACT[$i]['COS_ACT']);
                //cls_Global::putMessageLogFile($sql);
                $command = $con->prepare($sql);                
                $command->execute();
                
                
            }            

            $con->commit();
            $con->close();
            return true;
        } catch (Exception $e) { // se arroja una excepción si una consulta falla
            //return VSexception::messageSystem('NO_OK', $e->getMessage(), 41, null, null);
            $con->rollback();
            $con->close();
            throw $e;
            return false;
        }
    }
    
    
}
