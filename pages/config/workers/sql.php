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
          if(($data->estado)==1) { $whr = "and w.estado=1 "; }
          if(($data->buscar)!="") { $whr = $whr."and  (w.worker like'%".$data->buscar."%' or w.dni like'%".$data->buscar."%') "; }
          if(($data->agenciaID) > 0) {
            $whr = $whr." and w.id_agencia=".($data->agenciaID);
            $rx = $db->fetch_array($db->select("select abrev from dbo.tb_agencias where ID=".$data->agenciaID));
            $abrev = ($rx["abrev"]);
          }

          $sql = "select u.login,s.codusuario,w.* from dbo.vw_workers w left outer join dbo.tb_usuarios u on w.ID=u.id_persona left outer join CoopSUD.dbo.COOP_DB_usuarios as s on s.codusuario = w.codigo where 1=1 ".$whr." order by w.orden";
          $qry = $db->select(($sql));
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $user = 0;
              $SUD = 0;
              if(!is_null($rs["login"])) { $user = 1; }
              if(!is_null($rs["codusuario"])) { $SUD = 1; }
              $tabla[] = array(
                "ID" => ($rs["ID"]),
                "user" => $user,
                "SUD" => $SUD,
                "codigo" => ($rs["codigo"]),
                "url" => ($rs["urlfoto"]),
                "estado" => ($rs["estado"]),
                "dni"=> (str_replace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', $rs["DNI"])),
                "worker" => (str_replace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', ($rs["worker"]))),
                "nombrecorto" => ($rs["nombrecorto"]),
                "cargo" => ($rs["cargo"]),
                "agencia" => ($rs["agencia"])
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
            "nombrecorto" => ($rs["nombrecorto"]),
            "correo" => $rs["correo"],
            "codigo" => $rs["codigo"],
            "agencia" => ($rs["agencia"]),
            "id_agencia" => $rs["id_agencia"],
            "id_cargo" => $rs["id_cargo"],
            "cargo" => ($rs["cargo"]),
            "fecha_ing" => ($rs["fecha_ing"]),
            "fecha_renov" => ($rs["fecha_renov"]),
            "fecha_vacac" => ($rs["fecha_vacac"]),
            "asigna_fam" => $rs["asigna_fam"],
            "estado" => $rs["estado"],
            "observWork" => ($rs["observWork"]),
            "sysuserWork" => ($rs["sysuserWork"]),
            "sysfechaWork" => ($rs["sysfechaWork"])
          );

          //datos de usuario
          $usrActivo = 0;
          $usrLogin ="";
          $usrNivelAcceso = 0;
          $qryUsr = $db->select("select * from dbo.tb_usuarios where id_persona=".$data->personaID);
          if ($db->has_rows($qryUsr)) {
            $rsUsr = $db->fetch_array($qryUsr);
            $usrActivo = 1;
            $usrLogin = ($rsUsr["login"]);
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
        case "insWorker":
          $params = array();
          $usuario = 0;

          //datos de worker
          $datWorker = $data->datosWorker;
          $sqlWrk = "exec dbo.sp_workers 'INS',".
            $datWorker->personaID.",'".
            ($datWorker->workCodigo)."','".
            ($datWorker->workNombrecorto)."','".
            ($datWorker->workCorreo)."','".
            ($datWorker->workFechaIngre)."','".
            ($datWorker->workFechaIngre)."','".
            ($datWorker->workFechaIngre)."',".
            ($datWorker->workAgenciaID).",".
            ($datWorker->workCargoID).",'".
            ($datWorker->workObservac)."',0,1,'".
            get_client_ip()."',".
            $_SESSION['usr_ID'];

          $qry = $db->insert($sqlWrk, $params);

          //datos de usuario
          $datUsuario = $data->datosUsuario;
          if($datUsuario->userEsUsuario==1){
            $sqlUsr = "exec sp_usuarios 'INS',".
              $datUsuario->personaID.",'".
              ($datUsuario->userLogin)."','".
              ($datUsuario->userPasswd)."','".
              ($datUsuario->userPasswdtxt)."',".
              ($datUsuario->userUsernivelID).",'".
              get_client_ip()."',".
              $_SESSION['usr_ID'];
            $qry = $db->insert($sqlUsr, $params);
            $usuario = 1;
          }

          $rpta = array("error"=>0,"InsertWorker"=>1,"InsertUsuario"=>$usuario);
          echo json_encode($rpta);
          break;
        case "updWorker": //cambiar los datos empleado,usuario
          $params = array();
          $updWorker = 0;
          $updUsuario = 0;

          //modificar datos de WORKER
          $datWorker = $data->datosWorker;
          if($datWorker->chkWorker==1){
            $sql = "exec sp_workers 'UPD',".$datWorker->personaID.",'".
              ($datWorker->workCodigo)."','".
              ($datWorker->workNombrecorto)."','".
              ($datWorker->workCorreo)."','".
              ($datWorker->workFechaIngre)."','".
              ($datWorker->workFechaRenov)."','".
              ($datWorker->workFechaVacac)."',".
              ($datWorker->workAgenciaID).",".
              ($datWorker->workCargoID).",'".
              ($datWorker->workObservac)."',".
              ($datWorker->workAsignaFam).",".
              ($datWorker->workEstado).",'".
              get_client_ip()."',".
              $_SESSION['usr_ID'];

            $qry = $db->update($sql, $params);
            $updWorker = 1;
          }

          //modificar datos de USUARIO
          $datUsuario = $data->datosUsuario;
          if($datUsuario->chkUsuario==1){
            if($datUsuario->userEsUsuario==0){ //borrar usuario
              $sql = "delete from dbo.tb_usuarios where id_persona=".$datUsuario->personaID;
              $qry = $db->delete($sql, $params);
            } else { //actualizar datos de usuario
              if ($db->has_rows($db->select("select * from dbo.tb_usuarios where id_persona=".$datUsuario->personaID))) {
                $sql = "exec sp_usuarios 'UPD',".
                  $datUsuario->personaID.",'".
                  ($datUsuario->userLogin)."','','',".
                  ($datUsuario->userUsernivelID).",'".
                  get_client_ip()."',".
                  $_SESSION['usr_ID'];

                $qry = $db->update($sql, $params);
              } else {
                $sql = "exec sp_usuarios 'INS',".$datUsuario->personaID.",'".
                  ($datUsuario->userLogin)."','".
                  ($datUsuario->userPasswd)."','".
                  ($datUsuario->userPasswdtxt)."',".
                  ($datUsuario->userUsernivelID).",'".
                  get_client_ip()."',".
                  $_SESSION['usr_ID'];

                $qry = $db->insert($sql, $params);
              }
            }
            $updUsuario = 1;
          }
          $rpta = array("error"=>$sql,"UpdateWorker"=>$updWorker,"UpdateUsuario"=>$updUsuario);
          echo json_encode($rpta);
          break;
        case "delWorker":
          $params = array();
          for($xx = 0; $xx<count($data->IDs); $xx++){
            $sql = "exec sp_delete 'tb_workers',".$data->IDs[$xx].",'".$data->fechaBaja."','".get_client_ip()."',".$_SESSION['usr_ID'];
            $qry = $db->delete($sql, $params);
          }
          $rpta = array("error" => false,"Delete" => 1);
          echo json_encode($rpta);
          break;
        case "downWorkers": //descargar los datos de los usuarios
          $whr = "";
          $agencia = array("codigo"=>"00", "abrev"=>"all");
          $tabla[] = array(
            array("text" => "Apellidos y Nombres"),
            array("text" => "DNI"),
            array("text" => "Fecha Nacimiento"),
            array("text" => "Agencia"),
            array("text" => "Cargo"),
            array("text" => "Fecha Ingreso"),
            array("text" => "Celular"),
            array("text" => "Correo"),
            array("text" => "Fecha Renovacion")
          );

          if(($data->agenciaID) > 0) {
            $whr = "and id_agencia=".($data->agenciaID);
            //agencia
            $rs = $db->fetch_array($db->select("select codigo,abrev from dbo.tb_agencias where ID=".$data->agenciaID));
            $agencia["codigo"] = ($rs["codigo"]);
            $agencia["abrev"] = ($rs["abrev"]);
          }
          
          $sql = "select v.*,REPLACE(CONVERT(NVARCHAR, p.fecha_nac, 103), ' ', '/') AS fecha_nac,p.celular,p.email from vw_workers v,tb_personas p where p.ID=v.ID and 0=0 and estado=1 ".$whr." order by id_agencia,worker";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);

              $tabla[] = array(
                array("text" => ($rs["worker"])),
                array("text" => ($rs["DNI"])),
                array("text" => ($rs["fecha_nac"])),
                array("text" => ($rs["agencia"])),
                array("text" => ($rs["cargo"])),
                array("text" => ($rs["fecha_ing"])),
                array("text" => ($rs["celular"])),
                array("text" => ($rs["correo"])),
                array("text" => ($rs["fecha_renov"]))
              );
            }
          }

          //respuesta
          $options = array("fileName"=>"empleados".$agencia["codigo"]."_".$agencia["abrev"]);
          $tableData[] = array("sheetName"=>"empleados","data"=>$tabla);
          $rpta = array("options"=>$options,"tableData"=>$tableData);
          echo json_encode($rpta);
          break;
        case "selDatosPassw":
          $sql = "select * from dbo.vw_workers where ID=".$data->personaID;
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) { $rs = $db->fetch_array($qry); }
          $worker = array(
            "nombrecorto" => ($rs["nombrecorto"])
          );
          $rpta = $worker;
          echo json_encode($rpta);
          break;

        case "coopSUDselect":
          $sql = "select * from CoopSUD.dbo.COOP_DB_usuarios where codusuario='".$data->codusuario."'";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            $rs = $db->fetch_array($qry);

            $rpta = array(
              "usuario" => ($rs["nombres"])." ".($rs["ap_pater"])." ".($rs["ap_mater"]),
              "agencia" => $rs["agencia"],
              "ventanilla" => $rs["ventanilla"],
              "nivel" => $rs["nivel"],
              "modifi" => $rs["modif_int"],
              "elimina" => $rs["elimina"]);
          } else {
            $rpta = array(
              "error" => true,
              "mensaje" => "problemas"
            );
          }
          echo json_encode($rpta);
          break;
        case "coopSUDCambiarDatos": //cambiar permisos en coopSUD
          $params = array();
          $sql = "update CoopSUD.dbo.COOP_DB_usuarios set nivel='".$data->nivel."',modif_int='".$data->modifi."',elimina='".$data->elimina."' where codusuario='".$data->codusuario."'";
          $qry = $db->update($sql, $params);
          if($qry) {
            $rpta = array(
              "error" => false,
              "resp" => "Se actualizo datos de usuario coopSUD"
            );
          } else{
            $rpta = array(
              "error" => true,
              "resp" => "Fallo actualizacion"
            );
          }
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
