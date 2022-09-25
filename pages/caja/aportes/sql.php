<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');

      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      switch ($data->TipoQuery) {
        case "selAportes":
          $whr = "";
          $tabla = array();

          //verificar usuario
          $qryusr = $db->select("select id_usernivel from dbo.tb_usuarios where id_persona=".$_SESSION['usr_ID'].";");
          $rsusr = $db->fetch_array($qryusr);

          //cargar datos de socios en aportes producto=102
          $data->buscar = strtoupper($data->buscar);
          if(($data->buscar)!="") { $whr = " and (socio like'%".$data->buscar."%' or dni like'%".$data->buscar."%') " ;}
          if(($data->agenciaID) > 0) { $whr = $whr."and id_agencia=".($data->agenciaID); }
          $qryCount = $db->select(utf8_decode("select count(*) as cuenta from vw_oper_aportes where id_producto=102 ".$whr.";"));
          $rsCount = $db->fetch_array($qryCount);

          $qry = $db->select(utf8_decode("select top(15)* from dbo.vw_oper_aportes where id_producto=102 ".$whr." order by socio,id_producto desc;"));
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $observac = ($rs["saldo"]<=0) ? ("<span style='color:red;'>* Hay un servicio que debe ser cancelado ahora mismo</span>") : ("");
              $socio = utf8_encode(($rs["tipoPersona"]==2)?(substr($rs["socio"],3)):($rs["socio"]));

              $tabla[] = array(
                "codigo" => $rs["codigo"],
                "id_socio" => $rs["id_socio"],
                "DNI"=> str_replace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', $rs["DNI"]),
                "socio" => str_replace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', $socio),
                "agencia" => utf8_encode($rs["agencia"]),
                "saldo" => ($rs["saldo"]),
                "observac" => utf8_encode($observac)
              );
            }
          }

          //respuesta
          $rpta = array("cuenta"=>$rsCount["cuenta"],"tabla"=>$tabla,"usernivel"=>$rsusr["id_usernivel"],"admin"=>701);
          echo json_encode($rpta);
          break;
        case "editAporte":
          //cargar datos de persona
          $tablaPers = getOnePersona($data->personaID);
          $qry =  $db->select("select a.nombre from dbo.tb_agencias a,dbo.tb_socios s where s.id_agencia=a.ID and s.id_persona=".$data->personaID);
          $rs = $db->fetch_array($qry);
          $tablaPers["agencia"] = $rs["nombre"];

          //cargar datos de saldos
          $tablaSaldos = array();
          $sql = "select * from dbo.vw_oper_aportes where id_socio=".$data->personaID;
          $qry =  $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tablaSaldos[] = array(
                "id_producto" => ($rs["id_producto"]),
                "producto" => utf8_encode($rs["producto"]),
                "saldo" => ($rs["saldo"])
              );
            }
          }

          //respuesta
          $rpta = array('tablaPers'=>$tablaPers,'tablaSaldos'=>$tablaSaldos);
          echo json_encode($rpta);
          break;
        case "addAporte":
          //obtener fecha actual de operacion
          $rs = $db->fetch_array($db->select("select REPLACE(CONVERT(NVARCHAR, getdate(), 103), ' ', '/') as fecha"));
          $fechaHoy = utf8_encode($rs["fecha"]);

          //obtener el socio
          $socio = getOnePersona($data->personaID);

          //cargar inscripcion y/o aportes
          $tablaSaldos = array();
          $rx = $db->fetch_array($db->select("select count(*) as cuenta from dbo.vw_oper_aportes where saldo<0 and id_socio=".$data->personaID));
          if($rx["cuenta"]>0)
          $sql = "select * from dbo.vw_oper_aportes where id_socio=".$data->personaID;
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tablaSaldos[] = array(
                "ID" => ($rs["ID"]),
                "id_producto" => ($rs["id_producto"]),
                "producto" => utf8_encode($rs["producto"]),
                "prioridad" => ($rs["prioridad"]),
                "tipo" => "upd",
                "saldo" => (($rs["prioridad"]==1)?($rs["saldo"]*(-1)):(10))
              );
            }
          }

          //respuesta
          $rpta = array("fecha"=>$fechaHoy, "socio"=>$socio, "tablaSaldos"=>$tablaSaldos);
          echo json_encode($rpta);
          break;
        case "aporRetiro":
          //obtener fecha actual de operacion
          $rs = $db->fetch_array($db->select("select REPLACE(CONVERT(NVARCHAR, getdate(), 103), ' ', '/') as fecha"));
          $fechaHoy = utf8_encode($rs["fecha"]);

          //cargar aportes
          $qry = $db->select("select * from dbo.vw_oper_aportes where id_producto=2 and id_socio=".$data->personaID);
          if ($db->has_rows($qry)) {
            $rs = $db->fetch_array($qry);
            $tablaSaldos[] = array(
              "ID" => ($rs["ID"]),
              "id_producto" => ($rs["id_producto"]),
              "producto" => utf8_encode($rs["producto"]),
              "tipo" => "ret",
              "maxmonto" => $rs["saldo"],
              "saldo" => (10)
            );
          }

          //respuesta
          $rpta = array("fecha"=>$fechaHoy,"tablaSaldos"=>$tablaSaldos);
          echo json_encode($rpta);
          break;
        case "insAporte": //añadir aporte
          $rpta = array();
          $params = array();
          $pagoCuota = $data->pagCuota;
          $pagoAporte = $data->pagAporte;
          $tarea = "";
          //agregamos cuota y aportes
          if($pagoCuota->monto>0) { $tarea = "CUO"; } else { $tarea = "APO"; }
          $sql = "exec dbo.sp_oper_aportes '".$tarea."',0,".
            ($data->socioID).",".
            ($data->pagTipoOperID).",".
            ($data->pagTipoMoneID).",".
            ($pagoCuota->ID).",".
            ($pagoCuota->productoID).",".
            ($pagoCuota->monto).",".
            ($pagoAporte->ID).",".
            ($pagoAporte->productoID).",".
            ($pagoAporte->monto).",'".
            ($data->pagFechaIng)."','".
            get_client_ip()."',".
            $_SESSION['usr_ID'];
          $qry = $db->update($sql, $params);

          $rpta = array("error" => 0,"insert" => 1);
          echo json_encode($rpta);
          break;
        case "insRetiro": //retirar aporte
          $rpta = array();
          $params = array();
          $pagoCuota = $data->pagCuota;
          $pagoAporte = $data->pagAporte;
          //agregamos cuota y aportes
          $sql = "exec dbo.sp_oper_aportes 'RET',0,".
            ($data->socioID).",".
            ($data->pagTipoOperID).",".
            ($data->pagTipoMoneID).",".
            ($pagoCuota->ID).",".
            ($pagoCuota->productoID).",".
            ($pagoCuota->monto).",".
            ($pagoAporte->ID).",".
            ($pagoAporte->productoID).",".
            ($pagoAporte->monto).",'".
            ($data->pagFechaIng)."','".
            get_client_ip()."',".
            $_SESSION['usr_ID'];
          $qry = $db->update($sql, $params);

          $rpta = array("error" => 0,"insert" => 1);
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
