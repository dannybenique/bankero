<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');
      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      switch ($data->TipoQuery) {
        //****************captacion****************
        case "selPreventa":
          $whr = "";
          $tabla = array();

          //verificar usuario
          $qryusr = $db->select(utf8_decode("select id_usernivel from dbo.tb_usuarios where id_persona=".$_SESSION['usr_ID'].";"));
          $rsusr = $db->fetch_array($qryusr);

          //cargar datos de Socios
          if(($data->buscar)!="") { $whr = " and (DNI like'%".$data->buscar."%') " ;}
          if(($data->agenciaID) > 0) { $whr = $whr." and sysagencia=".($data->agenciaID); }
          $qryCount = $db->select(utf8_decode("select count(*) as cuenta from dbo.vw_oper_captaciones where estado=1 ".$whr.";"));
          $rsCount = $db->fetch_array($qryCount);

          $qry = $db->select(utf8_decode("select top(15)* from dbo.vw_oper_captaciones where estado=1 ".$whr." order by fecha desc;"));
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => $rs["ID"],
                "agencia"=> utf8_encode($rs["agencia"]),
                "nombrecorto"=> utf8_encode($rs["nombrecorto"]),
                "personaID"=> ($rs["id_persona"]),
                "persona"=> utf8_encode($rs["persona"]),
                "producto"=> utf8_encode($rs["producto"]),
                "nroDNI" => str_replace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', utf8_encode($rs["DNI"])),
                "tipo_oper" => utf8_encode($rs["tipo_oper"]),
                "plazo" => ($rs["plazo"]),
                "monto" => ($rs["monto"]),
                "tasa" => ($rs["tasa"]),
                "fecha" => ($rs["fechaing"]),
                "transac" => ($rs["transac"]) //1 = aun no ha sido captado 0 = ya fue captado
              );
            }
          }

          //respuesta
          $rpta = array("cuenta"=>$rsCount["cuenta"],"tabla"=>$tabla,"usernivel"=>$rsusr["id_usernivel"],"admin"=>701);
          echo json_encode($rpta);
          break;
        case "editPreventa":
          //verificar usuario
          $qryusr = $db->select(utf8_decode("select id_usernivel from dbo.tb_usuarios where id_persona=".$_SESSION['usr_ID'].";"));
          $rsusr = $db->fetch_array($qryusr);
          $usuario = array(
            "id_usuario" => $_SESSION['usr_ID'],
            "usernivel" => $rsusr["id_usernivel"],
            "admin" => 701,
          );

          //verificar preventa
          $qry = $db->select("select * from dbo.vw_oper_captaciones where id_persona=".$data->personaID);
          if ($db->has_rows($qry)) { $rs = $db->fetch_array($qry); }

          $persona = getOnePersona($data->personaID);
          $preventa = array(
            "ID" => ($rs["ID"]),
            "productoID" => ($rs["id_producto"]),
            "plazo" => ($rs["plazo"]),
            "monto" => ($rs["monto"]),
            "tasa" => ($rs["tasa"]),
            "fecha" => ($rs["fechaing"]),
            "observac" => utf8_encode($rs["observac"]),
            "sysfecha" => ($rs["sysfecha1"]),
            "sysuserID" => ($rs["sysuser"]),
            "sysuser" => utf8_encode($rs["sysusuario"]));

          $rpta = array("persona"=>$persona,"preventa"=>$preventa,"usuario"=>$usuario);
          echo json_encode($rpta);
          break;
        case "delPreventa":
          $params = array();
          for($xx = 0; $xx<count($data->IDs); $xx++){
            $sql = "exec dbo.sp_delete 'tb_oper_captaciones',".$data->IDs[$xx].",'','".get_client_ip()."',".$_SESSION['usr_ID'];
            $qry = $db->delete($sql, $params);
          }
          $rpta = array("error" => false,"delete" => 1);
          echo json_encode($rpta);
          break;
        case "execPreventa":
          $rpta = array();
          $params = array();
          $sql = "exec dbo.sp_oper_captaciones '".$data->commandSQL."',".
            ($data->preventaID).",".
            ($_SESSION['usr_ID']).",".
            ($data->personaID).",".
            ($data->negociotipoID).",".
            ($data->productoID).",".
            ($data->plazo).",".
            ($data->monto).",".
            ($data->tasa).",'".
            ($data->fecha)."','".
            utf8_decode($data->observac)."','".
            get_client_ip()."',".
            $_SESSION['usr_ID'];

          $qry = $db->update($sql, $params);
          $rs = $db->fetch_array($qry);
          $rpta = array("error" => false,"ejecutados" => 1);
          echo json_encode($rpta);
          break;

        case "captaPreventa":
          $params = array();
          for($xx = 0; $xx<count($data->IDs); $xx++){
            $sql = "exec dbo.sp_delete 'tb_oper_capta_transac',".$data->IDs[$xx].",'','".get_client_ip()."',".$_SESSION['usr_ID'];
            $qry = $db->delete($sql, $params);
          }
          $rpta = array("error" => false,"delete" => 1);
          echo json_encode($rpta);
          break;
        case "selProductos":
          $tabla = array();

          $qry = $db->select(utf8_decode("select * from dbo.tb_productos where id_padre=2 and id_tipo_prod=201 order by nombre;"));
          if ($db->has_rows($qry)) {
            for($xx=0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => $rs["ID"],
                "nombre"=> utf8_encode($rs["nombre"])
              );
            }
          }

          //respuesta
          $rpta = $tabla;
          echo json_encode($rpta);
          break;
        case "simulaAhorro":
          //productos
          $rs = $db->fetch_array($db->select("select * from dbo.tb_productos where ID=".$data->productoID));
          $producto = $rs["nombre"];

          //simulacion de ahorros
          $sql = "select dbo.fn_GetAhorrosTotalInteresImporte('".$data->fechaIni."','".$data->fechaFin."',".$data->importe.",".$data->tasa.") as interes";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
              $rs = $db->fetch_array($qry);
              $tabla = array(
                "productoID" => $data->productoID,
                "producto" => $producto,
                "interes" => $rs["interes"]
              );
          }
          echo json_encode($tabla);
          break;
      }
      $db->close();
    } else{
      $resp = array("error" => true,"mensaje" => "ninguna variable en POST");
      echo json_encode($resp);
    }
  } else {
    $resp = array("error" => true,"mensaje" => "Caducó la sesion.");
    echo json_encode($resp);
  }
?>
