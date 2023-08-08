<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');
      $data = json_decode($_REQUEST['appSQL']);
      
      switch ($data->TipoQuery) {
        case "selConyuge":
          echo json_encode($fn->getViewConyuge($data->personaID));
          break;
        case "execConyuge":
          /*
          $params = array();
          if($data->commandSQL=="INS"){ $data->permisoID = 1; }
          else { if($_SESSION['usr_usernivelID']==701){ $data->permisoID = 1; } }//darle permiso al superusuario

          if($data->permisoID>0){ //tiene permiso para actualizar
            
          }
          */
          $sql = "select sp_personas_rela ('".$data->commandSQL."',".
            $data->personaID.",".
            $data->conyugeID.",601,".
            $data->tiempoRelacion.",'".
            $fn->getClientIP()."',".
            $_SESSION['usr_ID'].") as nro";

          $rs = $db->fetch_array($db->query($sql));
          $rpta = array("error"=>false,$data->commandSQL=>1,"sql"=>$sql,"tablaCony"=>$fn->getViewConyuge($data->personaID));
          echo json_encode($rpta);
          break;
        case "delConyuge":
          $sql = "select sp_personas_rela ('".$data->commandSQL."',".$data->personaID.",0,601,0,'".$fn->getClientIP()."',".$_SESSION['usr_ID'].") as nro;";
          $rs = $db->fetch_array($db->query($sql));
          $rpta = array("error"=>false,"sql"=>$sql);
          echo json_encode($rpta);
          break;
        case "VerifyConyuge":
          $tablaPers = ""; //almacena los datos de la persona
          $persona = false; //indica que encontro en personas
          $activo = false; //indica que encontro en conyuges

          //verificar en Personas
          $qryPers = $db->query("select id from personas where (nro_dui='".$data->nroDNI."');");
          if($db->num_rows($qryPers)>0){
            $rsPers = $db->fetch_array($qryPers);
            $tablaPers = $fn->getViewPersona($rsPers["id"]);
            $persona = true;
            //verificar en Conyuges
            $qryConyuge1 = $db->query("select id_persona1 from personas_rela where (id_persona1=".$rsPers["id"].");");
            $qryConyuge2 = $db->query("select id_persona2 from personas_rela where (id_persona2=".$rsPers["id"].");");
            $activo = (($db->num_rows($qryConyuge1)>0) || ($db->num_rows($qryConyuge2)>0))? (true):(false);
          }

          //respuesta
          $rpta = array(
            "tablaPers" => $tablaPers,
            "persona" => $persona,
            "activo" => $activo
          );
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
