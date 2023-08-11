<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');
      include_once('../../../includes/web_config.php');

      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      //****************personas****************
      function getViewWorker($personaID){
        $db = $GLOBALS["db"]; //base de datos
        $fn = $GLOBALS["fn"]; //funciones
        $web = $GLOBALS["web"]; //web-config
        
        //obtener datos personales
        $sql = "select s.*,b.nombre as agencia,e.nombrecorto as usermod from bn_empleados s join bn_bancos b on s.id_agencia=b.id join bn_empleados e on e.id_empleado=s.sys_user where s.estado=1 and s.id_empleado=:personaID and s.id_coopac=:coopacID;";
        $params = [":personaID"=>$personaID,":coopacID"=>$web->coopacID];
        $qry = $db->query_all($sql,$params);
        
        if ($qry) {
            $rs = reset($qry);
            $tabla = array(
              "ID" => ($rs["id_empleado"]),
              "coopacID" => ($rs["id_coopac"]),
              "agenciaID" => ($rs["id_agencia"]),
              "cargoID" => ($rs["id_cargo"]),
              "comboAgencias" => ($fn->getComboBox("select id,nombre from bn_bancos where estado=1 and id_padre=".$web->coopacID." order by codigo;")),
              "comboCargos" => ($fn->getComboBox("select id,nombre from sis_tipos where id_padre=7 order by id;")),
              "codigo" => $rs["codigo"],
              "agencia" => $rs["agencia"],
              "estado" => $rs["estado"],
              "nombrecorto" => $rs["nombrecorto"],
              "correo" => $rs["correo"],
              "fecha_ing" => $rs["fecha_ing"],
              "fecha_vacac" => $rs["fecha_vacac"],
              "fecha_renov" => $rs["fecha_renov"],
              "fecha_baja" => $rs["fecha_baja"],
              "observac" => ($rs["observac"]),
              "usermod" => ($rs["usermod"]),
              "sysuser" => ($rs["sys_user"]),
              "sysfecha" => ($rs["sys_fecha"])
            );
        }
        return $tabla; 
      }
      function getViewUser($personaID){
        $db = $GLOBALS["db"]; //base de datos
        $fn = $GLOBALS["fn"]; //funciones
        $web = $GLOBALS["web"]; //web-config

        //obtener datos usuario
        $sql = "select * from bn_usuarios where id=:id and id_coopac=:coopacID;";
        $params = [":id"=>$personaID,":coopacID"=>$web->coopacID];
        $qry = $db->query_all($sql,$params);
        if ($qry) {
          $rs = reset($qry);
          $tabla = array(
            "ID" => ($rs["id"]*1),
            "coopacID" => ($rs["id_coopac"]*1),
            "rolID" => ($rs["id_rol"]*1),
            "login" => ($rs["login"]),
            "comboRoles" => ($fn->getComboBox("select id,nombre from sis_tipos where id_padre=2 order by id;")),
            "menu" => ($rs["menu"]),
            "sysuser" => ($rs["sys_user"]),
            "sysfecha" => ($rs["sys_fecha"])
          );
        } else {
          $tabla = array(
            "ID" => null,
            "comboRoles" => ($fn->getComboBox("select id,nombre from sis_tipos where id_padre=2 order by id;"))
          );
        }
        return $tabla; 
      }
      switch ($data->TipoQuery) {
        case "selWorkers":
          $tabla = array();
          $buscar = strtoupper($data->buscar);
          $whr = " and id_coopac=:coopacID and (empleado LIKE :buscar or nro_dui LIKE :buscar) ";
          $params = [":coopacID"=>$web->coopacID,":buscar"=>'%'.$buscar.'%'];
          $qryCount = $db->query_all("select count(*) as cuenta from vw_empleados where estado=1 ".$whr.";",$params);
          $rsCount = ($qryCount) ? reset($qryCount)["cuenta"] : (0);

          $qry = $db->query_all("select * from vw_empleados where estado=1 ".$whr." order by empleado limit 25 offset 0;",$params);
          if ($qry) {
            foreach($qry as $rs){
              $tabla[] = array(
                "ID" => $rs["id_empleado"],
                "codigo" => $rs["codigo"],
                "nro_dui"=> str_replace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["nro_dui"]),
                "empleado" => str_ireplace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["empleado"]),
                "nombrecorto" => $rs["nombrecorto"],
                "agencia" => ($rs["agencia"]),
                "cargo" => ($rs["cargo"]),
                "login" => ($rs["login"])
              );
            }
          }

          //respuesta
          $rpta = array("tabla"=>$tabla,"cuenta"=>$rsCount,"rolID" => (int)$_SESSION["usr_data"]["rolID"]);
          echo json_encode($rpta);
          break;
        case "insWorker":
          $qry = $db->query_all("select right('000000'||cast(coalesce(max(right(codigo,4)::integer)+1,1) as text),4) as code from bn_empleados where id_coopac=".$web->coopacID.";");
          $codworker = reset($qry)["code"];
          $sql = "insert into bn_empleados values(:id,:coopacID,:agenciaID,:cargoID,:codworker,:nombrecorto,:correo,:fecha,null,null,null,:estado,:sysIP,:userID,now(),:observac);";
          $params = [
            ":id"=>$data->workerID,
            ":coopacID"=>$web->coopacID, 
            ":agenciaID"=>$data->agenciaID, 
            ":cargoID"=>$data->cargoID, 
            ":codworker"=>$codworker, 
            ":nombrecorto"=>$data->nombrecorto,
            ":correo"=>$data->correo,
            ":fecha"=>$data->fecha,
            ":estado"=>1,
            ":sysIP"=>$fn->getClientIP(), 
            ":userID"=>$_SESSION['usr_ID'],
            ":observac"=>$data->observac
          ];
          $qry = $db->query_all($sql,$params);
          $rs = ($qry) ? (reset($qry)) : (null);

          //respuesta
          $rpta = array("error"=>false, "insert"=>1);
          echo json_encode($rpta);
          break;
        case "updWorker":
          $sql = "update bn_empleados set id_agencia=:agenciaID,id_cargo=:cargoID,nombrecorto=:nombrecorto,correo=:correo,observac=:observac,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id_empleado=:id and id_coopac=:coopacID;";
          $params = [
            ":id"=>$data->workerID,
            ":coopacID"=>$web->coopacID,
            ":agenciaID"=>$data->agenciaID,
            ":cargoID"=>$data->cargoID,
            ":nombrecorto"=>$data->nombrecorto,
            ":correo"=>$data->correo,
            ":observac"=>$data->observac,
            ":sysIP"=>$fn->getClientIP(), 
            ":userID"=>$_SESSION['usr_ID']
          ];
          $qry = $db->query_all($sql,$params);
          $rs = ($qry) ? (reset($qry)) : (null);

          //respuesta
          $rpta = array("error"=>false, "update"=>1);
          echo json_encode($rpta);
          break;
        case "delWorkers":
          $params = array();
          for($i=0; $i<count($data->arr); $i++){
            $sql = "update bn_empleados set estado=0,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id_empleado=:id";
            $params = [
              ":id"=>$data->arr[$i],
              ":sysIP"=>$fn->getClientIP(),
              ":userID"=>$_SESSION['usr_ID']
            ];
            $qry = $db->query_all($sql,$params);
            $rs = ($qry) ? (reset($qry)) : (null);
          }

          //respuesta
          $rpta = array("error"=>false, "delete"=>$data->arr);
          echo json_encode($rpta);
          break;
        case "viewWorker":
          //respuesta
          $rpta = array(
            'tablaPers'=>$fn->getViewPersona($data->personaID),
            'tablaWorker'=> getViewWorker($data->personaID),
            'tablaUser'=>getViewUser($data->personaID)
          );
          echo json_encode($rpta);
          break;
        case "VerifyWorker":
          $tablaPers = ""; //almacena los datos de la persona
          $persona = false; //indica que existe en personas
          $activo = false; //indica que encontro en tabla de empleados

          //verificar en Personas
          $sql = "select id from personas where (nro_dui=:nrodni);";
          $params = [":nrodni"=>$data->nroDNI];
          $qry = $db->query_all($sql,$params);
          if($qry){
            $rs = reset($qry);
            $tablaPers = $fn->getViewPersona($rs["id"]);
            $persona = true;
            //verificar en Empleados
            $sql = "select id_empleado from bn_empleados where id_coopac=:coopacID and id_empleado=:id;";
            $params = [":coopacID"=>$web->coopacID,":id"=>$rs["id"]];
            $qryEmpleado = $db->query_all($sql,$params);
            $activo = ($qryEmpleado) ? true : false;
          }

          //respuesta
          $rpta = array(
            "tablaPers" => $tablaPers,
            "persona" => $persona,
            "activo" => $activo,
            "mensajeNOadd" => "ya es EMPLEADO ACTIVO...");
          echo json_encode($rpta);
          break;
        case "startWorker":
          //respuesta
          $rpta = array(
            "comboAgencias" => $fn->getComboBox("select id,nombre from bn_bancos where estado=1 and id_padre=".$web->coopacID),
            "comboCargos" => $fn->getComboBox("select id,nombre from sis_tipos where id_padre=7 order by id;"),
            "comboRoles" => $fn->getComboBox("select id,nombre from sis_tipos where id_padre=2 order by id;"),
            "fecha" => $fn->getFechaActualDB(),
            "coopac" => $web->coopacID);
          echo json_encode($rpta);
          break;
        case "selMenu":
          //cargar Ubigeo
          $tabla = array();
          $pID = (array_key_exists('id',$_REQUEST)) ? ($_REQUEST['id']) : (0);
          $qry = $db->query_all("select * from sis_ubigeo where id_padre=".$pID." order by id;");
          if ($qry) {
            foreach($qry as $rs){
              //$rscount = $db->fetch_array($db->query("select count(*) as cuenta from sis_ubigeo where id_padre=".$rs["id"]));
              $tabla[] = array(
                "id" => ($rs["id"]),
                "pId" => ($pID),
                "name" => ($rs["codigo"])." - ".($rs["nombre"]),
                "isParent"=>(strlen($rs["id"])<6 ? true : false)
              );
            }
          }

          //respuesta
          echo json_encode($tabla);
          break;
        case "viewUserPass":
          $qry = $db->query_all("select u.id,u.login,e.nombrecorto from bn_usuarios u join bn_empleados e on u.id=e.id_empleado where u.id=".$data->userID);
          if ($qry) {
            $rs = reset($qry);
            $tabla = array(
              "ID" => $rs["id"],
              "login" => $rs["login"],
              "nombrecorto" => $rs["nombrecorto"]
            );
          }

          //respuesta
          $rpta = $tabla;
          echo json_encode($rpta);
          break;
        case "changeUserPass":
          $sql = "update bn_usuarios set passw=:passw,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id=:id and id_coopac=:coopacID;";
          $params = [
            ":id"=>$data->userID, 
            ":coopacID"=>$web->coopacID, 
            ":passw"=>$data->passw, 
            ":sysIP"=>$fn->getClientIP(), 
            ":userID"=>$_SESSION['usr_ID']
          ];
          $qry = $db->query_all($sql,$params);
          $rs = ($qry) ? (reset($qry)) : (null);

          //respuesta
          $rpta = array("error"=>false, "update"=>1);
          echo json_encode($rpta);
      }
      $db->close();
    } else{
      $resp = array("error"=>true,"data"=>$tabla,"mensaje"=>"ninguna variable en POST");
      echo json_encode($resp);
    }
  } else {
    $resp = array("error"=>true,"mensaje"=>"Caducó la sesion.");
    echo json_encode($resp);
  }
?>
