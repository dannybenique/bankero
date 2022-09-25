<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');

      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      switch ($data->TipoQuery) {
        case "selAhorros":
          $whr = "";
          $tabla = array();

          //verificar usuario
          $qryusr = $db->select(utf8_decode("select id_usernivel from dbo.tb_usuarios where id_persona=".$_SESSION['usr_ID'].";"));
          $rsusr = $db->fetch_array($qryusr);

          //cargar datos de socios en ahorros
          $data->buscar = strtoupper($data->buscar);
          if(($data->buscar)!="") { $whr = " and (socio like'%".$data->buscar."%' or dni like'%".$data->buscar."%') " ;}
          if(($data->agenciaID) > 0) { $whr = $whr."and id_agencia=".($data->agenciaID); }
          $qryCount = $db->select(utf8_decode("select count(distinct id_socio) as cuenta from vw_oper_ahorros where estado=1 ".$whr.";"));
          $rsCount = $db->fetch_array($qryCount);

          $sql = "select distinct top(15) id_socio,id_agencia,agencia,codigo,id_doc,doc,DNI,nombres,apellidos,celular from dbo.vw_oper_ahorros where estado=1 ".$whr." order by apellidos;";
          $qry = $db->select(utf8_decode($sql));
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              if($rs["id_doc"]==502) { $socio = utf8_encode($rs["nombres"]);} else { $socio = utf8_encode($rs["apellidos"].", ".$rs["nombres"]); }

              $tabla[] = array(
                "codigo" => $rs["codigo"],
                "id_socio" => $rs["id_socio"],
                "DNI"=> str_replace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', $rs["DNI"]),
                "socio" => str_ireplace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', $socio),
                "agencia" => utf8_encode($rs["agencia"])
              );
            }
          }

          //respuesta
          $rpta = array("cuenta"=>$rsCount["cuenta"],"tabla"=>$tabla,"usernivel"=>$rsusr["id_usernivel"],"admin"=>701);
          echo json_encode($rpta);
          break;
        case "ahorrosADD":
          //obtener fecha actual de operacion
          $rs = $db->fetch_array($db->select("select REPLACE(CONVERT(NVARCHAR, getdate(), 103), ' ', '/') as fecha"));
          $fechaHoy = utf8_encode($rs["fecha"]);

          //obtenemos los promotores
          $comboPromo = array();
          $qry =  $db->select("select ID,worker from dbo.vw_workers where estado=1 and id_agencia=".$data->agenciaID." order by worker;");
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $comboPromo[] = array("ID" => ($rs["ID"]),"nombre" => utf8_encode($rs["worker"]));
            }
          }

          //obtenemos los productos
          $comboProduc = array();
          $qry =  $db->select("select p.* from dbo.tb_productos p where id_tipo_oper=2 and estado=1;");
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $comboProduc[] = array("ID" => ($rs["ID"]),"nombre" => utf8_encode($rs["nombre"]));
            }
          }

          //obtenemos el tipo de movimiento
          $tablaMovs = array();
          $qry =  $db->select("select * from tb_tipo_mov where ID=6");
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tablaMovs[] = array("ID" => ($rs["ID"]),"nombre" => utf8_encode($rs["detalle"]));
            }
          }

          //respuesta
          $rpta = array("fecha"=>$fechaHoy,"promotores"=>$comboPromo,"productos"=>$comboProduc,"movs"=>$tablaMovs);
          echo json_encode($rpta);
          break;
        case "ahorrosRET":
          //obtener fecha actual de operacion
          $rs = $db->fetch_array($db->select("select REPLACE(CONVERT(NVARCHAR, getdate(), 103), ' ', '/') as fecha"));
          $fechaHoy = utf8_encode($rs["fecha"]);

          //obtenemos datos del producto/servicio
          $tablaAhorro = array();
          $qry =  $db->select("select * from dbo.vw_oper_ahorros_intereses where ID=".$data->ahorroID);
          if ($db->has_rows($qry)) {
            $rs = $db->fetch_array($qry);
            $intereses = 0;
            $disponible = 0;

            if($rs["ret_interes"]==0) { //al vencimiento
              if($rs["plazo"]>=0) {
                $intereses = $rs["intereses"];
                $disponible = ($rs["ret_bloqueo"]==1) ? (0) : ($rs["saldo"]);
              } else {
                if($rs["ret_anticipado"]==0) {
                  $intereses = $rs["intereses"];
                  $disponible = ($rs["ret_bloqueo"]==1) ? (0) : ($intereses);
                } else {
                  $dd = $db->fetch_array($db->select("select (saldo) - (importe) + (importe * tasamin / 100 / DATEDIFF(dd, fecha_inte, fecha_fin) * (DATEDIFF(dd, fecha_inte, fecha_fin)".$rs["plazo"].")) as newinteres from dbo.tb_oper_ahorros where ID=".$data->ahorroID));
                  $intereses = $dd["newinteres"];
                  $disponible = ($rs["ret_bloqueo"]==1) ? (0) : ($rs["importe"] + $intereses);
                }
              }
            } else { //mensual
              if($rs["plazo"]>=0) {
                $intereses = $rs["intereses"];
                $disponible = ($rs["ret_bloqueo"]==1) ? (0) : ($rs["saldo"]);
              } else {
                if($rs["ret_anticipado"]==0) {
                  $intereses = $rs["intereses"];
                  $disponible = ($rs["ret_bloqueo"]==1) ? (0) : ($intereses);
                } else {
                  $qryretiro = $db->fetch_array($db->select("select isnull(sum(m.importe),0) as totretiro from tb_oper_movimientos m where m.id_tipo_mov=7 and id_prestahorro=".$data->ahorroID));
                  $qryinteres = $db->fetch_array($db->select("select (importe * tasamin / 100)/DATEDIFF(dd, fecha_ini, fecha_fin)*DATEDIFF(dd, fecha_ini, getdate())-".$qryretiro["totretiro"]." as newinteres from dbo.tb_oper_ahorros a where ID=".$data->ahorroID));
                  $intereses = $qryinteres["newinteres"];
                  $disponible = ($rs["ret_bloqueo"]==1) ? (0) : ($rs["importe"] + $intereses);
                }
              }
            }

            $tablaAhorro = array(
              "ID" => ($rs["ID"]),
              "producto" => utf8_encode($rs["producto"]),
              "tasa" => ($rs["tasa"]),
              "tasamin" => ($rs["tasamin"]),
              "certificado" => ($rs["certificado"]),
              "fechacont" => utf8_encode($rs["fechacont"]),
              "fechaini" => utf8_encode($rs["fechaini"]),
              "fechafin" => utf8_encode($rs["fechafin"]),
              "promotor" => utf8_encode($rs["promotor"]),
              "retirointeres" => utf8_encode($rs["retiro"]),
              "retinteres" => ($rs["ret_interes"]),
              "retanticipado" => ($rs["ret_anticipado"]),
              "retbloqueo" => ($rs["ret_bloqueo"]),
              "importe" => ($rs["importe"]),
              "disponible" => $disponible,
              "intereses" => $intereses,
              "saldo" => $rs["saldo"],
              "plazo" => $rs["plazo"],
              "observac" => utf8_encode($rs["observac"])
            );
          }

          //obtenemos el tipo de movimiento para retiros
          $tablaMovs = array();
          $qry =  $db->select("select * from tb_tipo_mov where ID=7");
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tablaMovs[] = array(
                "ID" => ($rs["ID"]),
                "nombre" => utf8_encode($rs["detalle"])
              );
            }
          }

          $rpta = array("fecha"=>$fechaHoy,"ahorro"=>$tablaAhorro,"movs"=>$tablaMovs);
          echo json_encode($rpta);
          break;
        case "ahorrosSocio":
          $tablaUser = "";
          $tablaPers = "";

          //verificar usuario
          if($data->qryUser==1){
            $qryusr = $db->select(utf8_decode("select id_usernivel from dbo.tb_usuarios where id_persona=".$_SESSION['usr_ID'].";"));
            $rsusr = $db->fetch_array($qryusr);
            $tablaUser = array("usernivel"=>$rsusr["id_usernivel"],"admin"=>701);
          }

          //cargar datos de persona
          if($data->qryPers==1){
            $tablaPers = getOnePersona($data->personaID);
            $qry =  $db->select("select codigo,id_agencia,agencia from dbo.vw_socios where id_persona=".$tablaPers["ID"]);
            $rs = $db->fetch_array($qry);
            $tablaPers["codigo"] = $rs["codigo"];
            $tablaPers["agencia"] = $rs["agencia"];
            $tablaPers["agenciaID"] = $rs["id_agencia"];
          }

          //cargar datos de saldos
          $tablaSaldos = array();
          $qry =  $db->select("select * from dbo.vw_oper_ahorros where estado=1 and id_socio=".$data->personaID);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tablaSaldos[] = array(
                "ID" => ($rs["ID"]),
                "id_producto" => ($rs["id_producto"]),
                "producto" => utf8_encode($rs["producto"]),
                "tasa" => ($rs["tasa"]),
                "retiro" => ($rs["retiro"]),
                "certificado" => ($rs["certificado"]),
                "fechaini" => ($rs["fechaini"]),
                "fechafin" => ($rs["fechafin"]),
                "importe" => ($rs["importe"]),
                "saldo" => ($rs["saldo"])
              );
            }
          }

          //respuesta
          $rpta = array('tablaPers'=>$tablaPers,'tablaSaldos'=>$tablaSaldos,'tablaUser'=>$tablaUser);
          echo json_encode($rpta);
          break;
        case "ahorrosOne":
          $sql = "select dbo.fn_GetAhorrosTotalInteresMeses(fecha_inte,fecha_fin) as diferencia,* from dbo.vw_oper_ahorros where estado=1 and ID=".$data->ahorroID;
          $qry =  $db->select($sql);
          if ($db->has_rows($qry)) {
            $rs = $db->fetch_array($qry);
            $tablaProductos = array(
              "ID" => ($rs["ID"]),
              "id_producto" => ($rs["id_producto"]),
              "producto" => utf8_encode($rs["producto"]),
              "tasa" => ($rs["tasa"]),
              "certificado" => ($rs["certificado"]),
              "meses" => ($rs["diferencia"])
            );
          }
          //respuesta
          $rpta = $tablaProductos;
          echo json_encode($rpta);
          break;
        case "ahorrosOneSuplentes":
          //cargar datos de saldos
          $tablaBenef = array();
          $qry =  $db->select("select * from dbo.vw_oper_ahorros_suplentes where id_ahorro=".$data->ahorroID." order by tipo desc,suplente asc;");
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tablaBenef[] = array(
                "ID" => ($rs["ID"]),
                "DNI" => utf8_encode($rs["DNI"]),
                "suplente" => utf8_encode($rs["suplente"]),
                "tipo" => ($rs["tipo"]),
                "tipotexto" => utf8_encode($rs["tipoTexto"]),
                "fechanac" => utf8_encode($rs["fecha_nac"]),
                "parentesco" => utf8_encode($rs["parentesco"]),
                "celular" => utf8_encode($rs["celular"])
              );
            }
          }

          //respuesta
          $rpta = $tablaBenef;
          echo json_encode($rpta);
          break;
        case "insSuplente":
          $rpta = array();
          $params = array();
          $sql = "exec dbo.sp_oper_ahorros_suplentes 'INS',0,".
            ($data->ahorroID).",".
            ($data->suplenteID).",".
            ($data->tipo).",'".
            utf8_decode($data->parentesco)."','".
            get_client_ip()."',".
            $_SESSION['usr_ID'];

          $qry = $db->insert($sql, $params);
          $rs = $db->fetch_array($qry);
          $rpta = array("error" => 0,"insert" => 1);
          echo json_encode($rpta);
          break;
        case "delSuplentes":
          $params = array();
          for($xx = 0; $xx<count($data->IDs); $xx++){
            $sql = "exec dbo.sp_delete 'tb_oper_ahorros_suplentes',".$data->IDs[$xx].",'','".get_client_ip()."',".$_SESSION['usr_ID'];
            $qry = $db->delete($sql, $params);
          }
          $rpta = array("error" => false,"delete" => 1);
          echo json_encode($rpta);
          break;
        case "insAhorros":
          $rpta = array();
          $params = array();
          $datAhorro = $data->datosAhorro;
          $sql = "exec dbo.sp_oper_ahorros 'INS',0,'".$datAhorro->numero."',".
            ($datAhorro->agenciaID).",".
            ($datAhorro->promotorID).",".
            ($datAhorro->productoID).",".
            ($datAhorro->socioID).",'".
            ($datAhorro->fechaIni)."','".
            ($datAhorro->fechaFin)."',".
            ($datAhorro->plazo).",".
            ($datAhorro->tasa).",".
            ($datAhorro->tasamin).",".
            ($datAhorro->tiporetiro).",0,0,".
            ($datAhorro->importe).",".
            ($datAhorro->importe).",'".
            utf8_decode($datAhorro->observac)."','".
            get_client_ip()."',".
            $_SESSION['usr_ID'];

          $qry = $db->insert($sql, $params);
          $rs = $db->fetch_array($qry);
          $rpta = array("error" => 0,"insert" => 1);
          echo json_encode($rpta);
          break;
        case "updAhorrosMtto": //mantenimiento de ahorros
          $rpta = array();
          $params = array();
          $sql = "exec dbo.sp_oper_ahorros 'MTTO',".$data->ahorroID.",'',".
            (0).",".
            (0).",".
            (0).",".
            (0).",'".
            ($data->fechaini)."','".
            ($data->fechafin)."',".
            (0).",".
            (0).",".
            (0).",".
            (0).",".
            ($data->retanticipado).",".
            ($data->retbloqueo).",".
            (0).",".
            (0).",'".
            ($data->observac)."','".
            get_client_ip()."',".
            $_SESSION['usr_ID'];

          $qry = $db->update($sql, $params);
          $rs = $db->fetch_array($qry);

          $rpta = array("error" => 0,"update" => 1);
          echo json_encode($rpta);
          break;
        case "updAhorrosIntereses": //generando intereses
          $rpta = array();
          $params = array();
          $sql = "exec dbo.sp_oper_ahorros 'INTE',".$data->ahorroID.",'',".
            (0).",".
            (0).",".
            (0).",".
            (0).",'','',".
            (0).",".
            (0).",".
            (0).",".
            (0).",".
            (0).",".
            (0).",".
            (0).",".
            (0).",'','".
            get_client_ip()."',".
            $_SESSION['usr_ID'];

          $qry = $db->update($sql, $params);
          $rs = $db->fetch_array($qry);

          //verificar usuario
          $qryusr = $db->select(utf8_decode("select id_usernivel from dbo.tb_usuarios where id_persona=".$_SESSION['usr_ID'].";"));
          $rsusr = $db->fetch_array($qryusr);
          $tablaUser = array("usernivel"=>$rsusr["id_usernivel"],"admin"=>701);

          //obteniendo data para mostrar
          $tablaSaldos = array();
          $sql = "select * from dbo.vw_oper_ahorros where estado=1 and id_socio=".$data->socioID;
          $qry =  $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tablaSaldos[] = array(
                "ID" => ($rs["ID"]),
                "id_producto" => ($rs["id_producto"]),
                "producto" => utf8_encode($rs["producto"]),
                "tasa" => ($rs["tasa"]),
                "retiro" => ($rs["retiro"]),
                "certificado" => ($rs["certificado"]),
                "fechaini" => ($rs["fechaini"]),
                "fechafin" => ($rs["fechafin"]),
                "importe" => ($rs["importe"]),
                "saldo" => ($rs["saldo"])
              );
            }
          }

          $rpta = array("error" => 0,"update" => 1,"tablaSaldos" => $tablaSaldos,"tablaUser"=>$tablaUser);
          echo json_encode($rpta);
          break;
        case "updAhorrosRetiro": //retiro de ahorros
          $rpta = array();
          $params = array();
          $sql = "exec dbo.sp_oper_ahorros 'RET',".$data->ahorroID.",'',".
            (0).",".
            (0).",".
            (0).",".
            (0).",'".
            ($data->fecha)."','',".
            (0).",".
            (0).",".
            (0).",".
            ($data->retinteres).",".
            ($data->retanticipado).",".
            (0).",".
            ($data->importe).",".
            (0).",'','".
            get_client_ip()."',".
            $_SESSION['usr_ID'];

          $qry = $db->update($sql, $params);
          $rs = $db->fetch_array($qry);

          $rpta = array("error" => 0,"update" => 1);
          echo json_encode($rpta);
          break;
      }
      $db->close();
    } else{
      $resp = array("error"=>true,"mensaje"=>"ninguna variable en POST");
      echo json_encode($resp);
    }
  } else {
    $resp = array("error"=>true,"mensaje"=>"Caducó la sesion.");
    echo json_encode($resp);
  }
?>
