<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');

      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      switch ($data->TipoQuery) {
        //****************agencias****************
        case "selAgencias":
          $whr = "";
          $tabla = array();

          //verificar usuario
          $qryusr = $db->select(utf8_decode("select id_usernivel from dbo.tb_usuarios where id_persona=".$_SESSION['usr_ID'].";"));
          $rsusr = $db->fetch_array($qryusr);

          //cargar agencias
          $sql = "select * from tb_agencias where estado=1 and nombre like'%".($data->miBuscar)."%' order by ID;";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => $rs["ID"],
                "codigo" => $rs["codigo"],
                "nombre" => utf8_encode($rs["nombre"]),
                "ciudad" => utf8_encode($rs["ciudad"]),
                "direccion" => utf8_encode($rs["direccion"]),
                "telefonos" => utf8_encode($rs["telefonos"])
              );
            }
          }
          //respuesta
          $rpta = array("tabla"=>$tabla,"usernivel"=>$rsusr["id_usernivel"],"admin"=>701);
          echo json_encode($rpta);
          break;
        case "editAgencia":
          //cargar datos de la persona
          $qry = $db->select("select * from dbo.tb_agencias where ID=".$data->agenciaID);
          if ($db->has_rows($qry)) {
            $rs = $db->fetch_array($qry);
            $rpta = array(
              "ID" => $rs["ID"],
              "codigo" => $rs["codigo"],
              "abrev" => utf8_encode($rs["abrev"]),
              "nombre" => utf8_encode($rs["nombre"]),
              "ciudad" => utf8_encode($rs["ciudad"]),
              "direccion" => utf8_encode($rs["direccion"]),
              "telefonos" => utf8_encode($rs["telefonos"]),
              "observac" => utf8_encode($rs["observac"])
            );
          }

          //respuesta
          echo json_encode($rpta);
          break;
        case "execAgencia":
          $sql = "exec dbo.sp_agencias '".$data->commandSQL."',".
            ($data->ID).",'".
            utf8_decode($data->codigo)."','".
            utf8_decode($data->abrev)."','".
            utf8_decode($data->nombre)."','".
            utf8_decode($data->ciudad)."','".
            utf8_decode($data->direccion)."','".
            utf8_decode($data->telefonos)."','".
            utf8_decode($data->observac)."','".
            get_client_ip()."',".
            $_SESSION['usr_ID'];

          $qry = $db->update($sql, array());
          $rpta = array("error" => false,"afectados" => 1);
          echo json_encode($rpta);
          break;
        case "delAgencias":
          $params = array();
          for($xx = 0; $xx<count($data->IDs); $xx++){
            $sql = "exec dbo.sp_delete 'tb_agencias',".$data->IDs[$xx].",'','".get_client_ip()."',".$_SESSION['usr_ID'];
            $qry = $db->delete($sql, $params);
          }
          $rpta = array("error" => false,"delete" => 1);
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
