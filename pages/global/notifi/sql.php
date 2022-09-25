<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');

      $data = json_decode($_REQUEST['appSQL']);

      //****************personas****************
      switch ($data->TipoQuery) {
        case "selNotifi":
          $tabla = array();
          //cuenta
          $rs = $db->fetch_array($db->select("select count(*) as cuenta from vw_usuarios_permisos;"));
          $cuenta = $rs["cuenta"];

          //datos
          $qry = $db->select("select * from vw_usuarios_permisos;");
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => ($rs["ID"]),
                "estado" => ($rs["estado"]),
                "tabla" => ($rs["tabla"]),
                "id_usuario_solic" => ($rs["id_usuario_solic"]),
                "usr_solic" => ($rs["usr_solic"]),
                "id_persona" => ($rs["id_persona"]),
                "persona" => utf8_encode(($rs["tipoPersona"]==2)?(substr($rs["persona"],3)):($rs["persona"])),
                "id_doc" => ($rs["id_doc"]),
                "tipoDNI" => utf8_encode($rs["doc"]),
                "nroDNI" => ($rs["DNI"])
              );
            }
          }
          $rpta = array("tabla"=>$tabla,"cuenta"=>$cuenta);
          echo json_encode($rpta);
          break;
        case "insNotifi":
          $params = array();

          $qry = $db->insert("exec dbo.sp_usuarios_permisos 'INS',0,".$_SESSION['usr_ID'].",0,".$data->personaID.",0,'".$data->tabla."','".get_client_ip()."'", $params);
          $xx = $db->fetch_array($qry);
          $rpta = array("error" => false,"insert" => 1);
          echo json_encode($rpta);
          break;
        case "updNotifi":
          $params = array();
          $qry = $db->update("exec dbo.sp_usuarios_permisos 'PMS',".$data->notificacionID.",".$_SESSION['usr_ID'].",0,0,1,'','".get_client_ip()."'", $params);
          $rpta = array("error"=>false,"Update"=>1);
          echo json_encode($rpta);
          break;
        case "delNotifi":
          $params = array();
          $sql = "delete from dbo.tb_usuarios_permisos where ID=".$data->notificacionID;
          $qry = $db->delete($sql, $params);
          $rpta = array("error" => false, "Delete" => 1);
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
