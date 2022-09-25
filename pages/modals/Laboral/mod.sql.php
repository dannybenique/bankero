<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');
      $data = json_decode($_REQUEST['appSQL']);
      
      switch ($data->TipoQuery) {
        case "selLaboral":
          echo json_encode(getOneLaboral($data->personaID));
          break;
        case "updLaboral":
          $params = array();
          if($data->commandSQL=="INS"){ $data->permisoID = 1; }
          else { if($_SESSION['usr_usernivelID']==701){ $data->permisoID = 1; } }//darle permiso al superusuario

          if($data->permisoID>0){ //tiene permiso para actualizar
            $sql = "exec dbo.sp_personas_labo '".($data->commandSQL)."',".
              ($data->permisoID).",".
              ($data->personaID).",".
              ($data->condicion).",'".
              utf8_decode($data->ruc)."','".
              utf8_decode($data->empresa)."','".
              utf8_decode($data->telefono)."','".
              utf8_decode($data->rubro)."',".
              ($data->distritoID).",'".
              utf8_decode($data->direccion)."','".
              utf8_decode($data->cargo)."',".
              ($data->ingreso).",'".
              utf8_decode($data->inicio)."','".
              utf8_decode($data->observac)."','".
              get_client_ip()."',".
              $_SESSION['usr_ID'];

            $qry = $db->insert($sql, $params);
            $rpta = array("error"=>0, "update"=>1, "tablaLabo"=>getOneLaboral($data->personaID));
            echo json_encode($rpta);
          }
          break;
        case "delLaboral":
          $params = array();
          $xx = $db->delete("exec dbo.sp_delete 'tb_personas_labo',".$data->personaID.",'','".get_client_ip()."',".$_SESSION['usr_ID'],$params);
          $rpta = array("error"=>false);
          echo json_encode($rpta);
          break;
      }
      $db->close();
    } else {
      $resp = array("error"=>true,"mensaje"=>"ninguna variable en POST");
      echo json_encode($resp);
    }
  } else {
    $resp = array("error"=>true,"mensaje"=>"Caducó la sesion.");
    echo json_encode($resp);
  }
?>
