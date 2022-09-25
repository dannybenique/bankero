<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');

      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      switch ($data->TipoQuery) {
        //****************contabilidad****************
        case "selContaCuentas":
          //verificar usuario
          $qryusr = $db->select(utf8_decode("select id_usernivel from dbo.tb_usuarios where id_persona=".$_SESSION['usr_ID'].";"));
          $rsusr = $db->fetch_array($qryusr);

          //cargar cuentas contables
          $pID = (array_key_exists('id',$_REQUEST)) ? ($pID = $_REQUEST['id']) : (0);
          $whr = ($pID==0) ? (" id_padre is null") : ("id_padre=".$pID);
          $sql = "select * from dbo.tb_conta_cuentas where ".$whr." order by ID;";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            $tabla = array();
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $rscount = $db->fetch_array($db->select("select count(*) as cuenta from dbo.tb_conta_cuentas where id_padre=".$rs["ID"]));
              $tabla[] = array(
                "id" => ($rs["ID"]),
                "pId" => ($pID),
                "name" => ($rs["codigo"])." - ".utf8_encode($rs["nombre"]),
                "isParent"=>($rscount["cuenta"]>0 ? true : false)
              );
            }
          }
          //respuesta
          echo json_encode($tabla);
          break;
        case "editContaCuenta":
          //verificar usuario
          $qryusr = $db->select(utf8_decode("select id_usernivel from dbo.tb_usuarios where id_persona=".$_SESSION['usr_ID'].";"));
          $rsusr = $db->fetch_array($qryusr);
          $usuario = array(
            "id_usuario" => $_SESSION['usr_ID'],
            "usernivel" => $rsusr["id_usernivel"],
            "admin" => 701,
          );

          //verificar Cuenta Contable
          $tabla = array();
          $qry = $db->select("select * from dbo.vw_conta_cuentas where ID=".$data->ID);
          if ($db->has_rows($qry)) { $rs = $db->fetch_array($qry); }
          $tabla = array(
            "ID" => ($rs["ID"]),
            "codigo" => ($rs["codigo"]),
            "nombre"=> utf8_encode($rs["nombre"]),
            "id_padre"=> ($rs["id_padre"]),
            "nombrePadre" => utf8_encode($rs["nombrePadre"])
          );

          $rpta = array("tabla"=>$tabla,"usuario"=>$usuario);
          echo json_encode($rpta);
          break;
        case "execContaCuenta":
          $rpta = array();
          $params = array();
          $sql = "exec dbo.sp_conta_cuentas '".$data->commandSQL."',".($data->ID).",'".utf8_decode($data->codigo)."','".utf8_encode($data->nombre)."',".($data->padreID).",'".get_client_ip()."',".$_SESSION['usr_ID'];
          $qry = $db->insert($sql, $params);
          $rs = $db->fetch_array($qry);
          $rpta = array("error" => 0,"afectados" => 1);
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
