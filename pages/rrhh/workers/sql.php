<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');
      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      switch ($data->TipoQuery) {
        //****************workers****************
        case "selWorkers":
          $whr = "";
          $abrev = "";
          $tabla = array();

          //cargar datos de agencias
          if(($data->estado)==1) { $whr = "and estado=1 "; }
          if(($data->buscar)!="") { $whr = $whr."and  (worker like'%".$data->buscar."%' or DNI like'%".$data->buscar."%') "; }
          if(($data->agenciaID) > 0) {
            $whr = $whr." and id_agencia=".($data->agenciaID);
            $rx = $db->fetch_array($db->select("select abrev from dbo.tb_agencias where ID=".$data->agenciaID));
            $abrev = utf8_encode($rx["abrev"]);
          }

          $sql = "select * from dbo.vw_workers where 1=1 ".$whr." order by orden";
          $qry = $db->select(utf8_decode($sql));
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $user = 0;
              $SUD = 0;
              $tabla[] = array(
                "ID" => ($rs["ID"]),
                "codigo" => ($rs["codigo"]),
                "url" => ($rs["urlfoto"]),
                "estado" => ($rs["estado"]),
                "dni"=> str_replace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', $rs["DNI"]),
                "worker" => str_replace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', utf8_encode($rs["worker"])),
                "nombrecorto" => utf8_encode($rs["nombrecorto"]),
                "cargo" => utf8_encode($rs["cargo"]),
                "agencia" => utf8_encode($rs["agencia"])
              );
            }
          }

          //respuesta
          $rpta = array("agenciaAbrev"=>$abrev,"tabla"=>$tabla);
          echo json_encode($rpta);
          break;
        case "editWorker":
          //datos de persona
          $tablaPers = getOnePersona($data->personaID);

          //datos de trabajador
          $qry = $db->select("select * from dbo.vw_workers where ID=".$data->personaID);
          if ($db->has_rows($qry)) { $rs = $db->fetch_array($qry); }
          $tablaWork = array(
            "nombrecorto" => utf8_encode($rs["nombrecorto"]),
            "codigo" => $rs["codigo"],
            "agencia" => utf8_encode($rs["agencia"]),
            "id_agencia" => $rs["id_agencia"],
            "id_cargo" => $rs["id_cargo"],
            "cargo" => utf8_encode($rs["cargo"]),
            "fecha_ing" => utf8_encode($rs["fecha_ing"]),
            "fecha_renov" => utf8_encode($rs["fecha_renov"]),
            "fecha_vacac" => utf8_encode($rs["fecha_vacac"]),
            "asigna_fam" => $rs["asigna_fam"],
            "observWork" => utf8_encode($rs["observWork"]),
            "sysuserWork" => utf8_encode($rs["sysuserWork"]),
            "sysfechaWork" => utf8_encode($rs["sysfechaWork"])
          );

          //datos de usuario
          $usrActivo = 0;
          $usrLogin ="";
          $usrNivelAcceso = 0;
          $qryUsr = $db->select("select * from dbo.tb_usuarios where id_persona=".$data->personaID);
          if ($db->has_rows($qryUsr)) {
            $rsUsr = $db->fetch_array($qryUsr);
            $usrActivo = 1;
            $usrLogin = utf8_encode($rsUsr["login"]);
            $usrNivelAcceso = $rsUsr["id_usernivel"];
          }
          $tablaUser = array(
            "usrActivo" => $usrActivo,
            "login" => $usrLogin,
            "id_usernivel" => $usrNivelAcceso
          );


          $rpta = array('tablaPers'=>$tablaPers,'tablaWork'=>$tablaWork,'tablaUser'=>$tablaUser);
          echo json_encode($rpta);
          break;
        case "rrhhWorker": //cambiar los datos de persona,empleado,usuario
          $params = array();

          //modificar datos de WORKER
          $sql = "exec sp_workers 'UPD',".$data->ID.",'".
            utf8_decode($data->workCodigo)."','".
            utf8_decode($data->workNombrecorto)."','".
            utf8_decode($data->workFechaIngre)."','".
            utf8_decode($data->workFechaRenov)."','".
            utf8_decode($data->workFechaVacac)."',".
            ($data->workAgenciaID).",".
            ($data->workCargoID).",'".
            utf8_decode($data->workObservac)."',".
            ($data->workAsignaFam).",'".
            get_client_ip()."',".
            $_SESSION['usr_ID'];
          $qry = $db->update($sql, $params);

          $rpta = array("error"=>false,"UpdateWorker"=>1);
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
