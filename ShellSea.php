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
        $dtActFecha = '2019-01';//date("Y-m", strtotime(date()));
        $dtAntFecha = '2018-12';//date("Y-m", strtotime(date()));//restarle 1 mes
        try {
            $obj_con = new cls_Base();
            $obj_var = new cls_Global();
            $con = $obj_con->conexionServidor();
            //$rawDataANT = array();
            $rawDataACT = array();
            //CONSULTA CONSTO GUARDADO ANT           
            /*$sql = "SELECT * FROM " . $obj_con->BdServidor . ".IG0007
                        WHERE COD_LIN='$codLin' AND TIP_CON='$tipConsult' AND ANO_MES='$dtFecha';";
            $sentencia = $con->query($sql);
            if ($sentencia->num_rows > 0) {
                while ($fila = $sentencia->fetch_assoc()) {//Array Asociativo
                    $rawDataANT[] = $fila;
                }
            }*/
            
            $sql = "SELECT A.COD_LIN, A.COD_TIP, A.COD_MAR, D.NOM_LIN, E.NOM_TIP, F.NOM_MAR,"
                    . "SUM(A.P_PROME*B.EXI_TOT)  AS COS_ACT, ";
            $sql .= "IFNULL((SELECT X.COSTO_T FROM utimpor2019.IG0007 X "
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
                cls_Global::putMessageLogFile($sql);
                $command = $con->prepare($sql);                
                $command->execute();
                
                
            }

            /*$sql = "DELETE FROM " . $obj_con->BdServidor . ".IG0007 WHERE COD_LIN='$codLin' AND TIP_CON='$tipConsult' 
                        AND DATE_FORMAT(FEC_SIS,'%Y-%m')='$dtFecha' ";
            $command = $con->prepare($sql);
            $command->execute();*/
            
            

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

    /*'GUARDAR DATOS DE IMPORTACINES POR LINEA DE PRODUCTOS
    Public Function guardarImportacionLinea(ByRef Cod_Lin As String, ByRef f_inicio As String, ByRef tipConsult As String) As Boolean
        Try
            'Dim dtFehca As String = Date.Parse(f_inicio).ToString("yyyy-MM-dd")
            Dim dtFecha As String = Date.Parse(f_inicio).ToString("yyyy-MM") 'Filtra solo por a\F1o y mes
            'Dim dtFehca As New Date
            'dtFehca = Date.Parse(f_inicio)
            'dtFehca.Month
            'dtFehca.Year

            trSql = cn.BeginTransaction
            dsObj = New DataSet
            cmSql = New MySqlCommand
            'Eliminamos Datos de la Linea Actualizar.
            With cmSql
                .Connection = cn
                .CommandType = CommandType.Text
                .CommandText = "DELETE FROM IG0007 WHERE COD_LIN='" & Cod_Lin & "' AND TIP_CON='" & tipConsult & "' " & _
                                " AND DATE_FORMAT(FEC_SIS,'%Y-%m')='" & dtFecha & "' "
                .Transaction = trSql
                .ExecuteNonQuery()
            End With

            Dim sql As New StringBuilder
            sql.Append("SELECT A.COD_LIN, A.COD_TIP, A.COD_MAR, D.NOM_LIN, E.NOM_TIP, F.NOM_MAR,SUM(A.P_PROME*B.EXI_TOT)  AS COSTO ")
            sql.Append("    FROM IG0020 A ")
            sql.Append("        INNER JOIN IG0022 B ON A.COD_ART=B.COD_ART ")
            sql.Append("        INNER JOIN IG0001 D ON A.COD_LIN=D.COD_LIN ")
            sql.Append("        INNER JOIN IG0002 E ON A.COD_TIP=E.COD_TIP ")
            sql.Append("        INNER JOIN IG0003 F ON A.COD_MAR=F.COD_MAR ")
            sql.Append("    WHERE  B.EXI_TOT <> 0  ")
            If Cod_Lin <> "" Then sql.Append(" AND A.COD_LIN='" & Cod_Lin & "' ")
            If tipConsult <> "TD" Then sql.Append(" AND A.COD_TIP='" & tipConsult & "' ")
            sql.Append("    GROUP BY A.COD_LIN,A.COD_MAR ORDER BY COD_LIN,COD_MAR ")
            If cmSql IsNot Nothing Then cmSql.Dispose()
            cmSql = New MySqlCommand
            With cmSql
                .CommandText = sql.ToString
                .CommandType = CommandType.Text
                .Connection = cn
            End With
            daSql = New MySqlDataAdapter(cmSql)
            daSql.Fill(dsObj, "IG0020")
            'INSERTAR DATOS CONSULTADOS
            If dsObj.Tables("IG0020").Rows.Count > 0 Then
                For fil As Integer = 0 To dsObj.Tables("IG0020").Rows.Count - 1
                    If cmSql IsNot Nothing Then cmSql.Dispose()
                    cmSql = New MySqlCommand
                    With cmSql
                        .Connection = cn
                        .CommandType = CommandType.Text
                        .CommandText = "INSERT INTO IG0007 (COD_LIN,COD_TIP,COD_MAR,NOM_MAR,COSTO_T,FEC_SIS,TIP_CON,ANO_MES)VALUES " _
                                        & "(?COD_LIN,?COD_TIP,?COD_MAR,?NOM_MAR,?COSTO_T,?FEC_SIS,?TIP_CON,?ANO_MES) "

                        .Parameters.Add(New MySqlParameter("?COD_LIN", MySqlDbType.VarChar)).Value = Cod_Lin
                        .Parameters.Add(New MySqlParameter("?COD_TIP", MySqlDbType.VarChar)).Value = dsObj.Tables("IG0020").Rows(fil).Item("COD_TIP")
                        .Parameters.Add(New MySqlParameter("?COD_MAR", MySqlDbType.VarChar)).Value = dsObj.Tables("IG0020").Rows(fil).Item("COD_MAR")
                        .Parameters.Add(New MySqlParameter("?NOM_MAR", MySqlDbType.VarChar)).Value = dsObj.Tables("IG0020").Rows(fil).Item("NOM_MAR")
                        .Parameters.Add(New MySqlParameter("?COSTO_T", MySqlDbType.Decimal)).Value = dsObj.Tables("IG0020").Rows(fil).Item("COSTO")
                        .Parameters.Add(New MySqlParameter("?FEC_SIS", MySqlDbType.Date)).Value = Date.Parse(f_inicio).ToString("yyyy-MM-dd") 'Date.Parse(Today).ToString("yyyy-MM-dd")
                        .Parameters.Add(New MySqlParameter("?TIP_CON", MySqlDbType.VarChar)).Value = tipConsult
                        .Parameters.Add(New MySqlParameter("?ANO_MES", MySqlDbType.VarChar)).Value = dtFecha
                        .Transaction = trSql
                        .ExecuteNonQuery()
                        '.Dispose()
                    End With
                Next
            End If
            trSql.Commit()
            cmSql.Dispose()
            Return True
        Catch ex As MySqlException
            ' cancelar la trnsacci\F3n en caso de error en el    
            ' segundo comando con el m\E9todo Rollback   
            If Not trSql Is Nothing Then
                trSql.Rollback() 'deshacer   
                cmSql.Dispose()
            End If
            Select Case ex.Number
                Case 1062
                    MsgBox("Codigo ya Existe", MsgBoxStyle.Information, "Mensaje...")
                Case Else
                    MsgBox(ex.Message & " Error Numero:" & ex.Number)
            End Select
            Return False
        End Try
    End Function*/




    
    
}
