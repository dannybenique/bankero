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
        $sql = "select s.*,b.nombre as agencia,e.nombrecorto as usermod from bn_empleados s join bn_bancos b on s.id_agencia=b.id join bn_empleados e on e.id_empleado=s.sys_user where s.estado=1 and s.id_empleado=$1 and s.id_coopac=$2;";
        $params = array($personaID,$web->coopacID);
        $qry = $db->query_params($sql,$params);
        
        if ($db->num_rows($qry)) {
            $rs = $db->fetch_array($qry);
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
        $tabla = null;
        $sql = "select * from bn_usuarios where id_usuario=$1 and id_coopac=$2;";
        $params = array($personaID,$web->coopacID);
        $qry = $db->query_params($sql,$params);
        if ($db->num_rows($qry)) {
          $rs = $db->fetch_array($qry);
          $tabla = array(
            "ID" => ($rs["id_usuario"]*1),
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
          $whr = "";
          $tabla = array();
          $data->buscar = pg_escape_string(strtoupper($data->buscar));
          $whr = " and id_coopac=".$web->coopacID." and (empleado like '%".$data->buscar."%' or nro_dui like '%".$data->buscar."%') ";
          $rsCount = $db->fetch_array($db->query("select count(*) as cuenta from vw_empleados where estado=1 ".$whr.";"));

          $qry = $db->query("select * from vw_empleados where estado=1 ".$whr." order by empleado limit 25 offset 0;");
          if ($db->num_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => $rs["id_empleado"],
                "codigo" => $rs["codigo"],
                "nro_dui"=> str_replace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', $rs["nro_dui"]),
                "empleado" => str_ireplace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', $rs["empleado"]),
                "nombrecorto" => $rs["nombrecorto"],
                "agencia" => ($rs["agencia"]),
                "cargo" => ($rs["cargo"]),
                "login" => ($rs["login"])
              );
            }
          }
          $rpta = array("tabla"=>$tabla,"cuenta"=>$rsCount["cuenta"],"rolID" => (int)$_SESSION["usr_data"]["rolID"]);
          echo json_encode($rpta);
          break;
        case "insWorker":
          $codworker = $db->fetch_array($db->query("select right('000000'||cast(coalesce(max(right(codigo,4)::integer)+1,1) as text),4) as code from bn_empleados where id_coopac=".$web->coopacID.";"))["code"];
          $sql = "insert into bn_empleados values($1,$2,$3,$4,$5,$6,$7,$8,null,null,null,$9,$10,$11,now(),$12);";
          $params = array(
            $data->workerID, 
            $web->coopacID, 
            $data->agenciaID, 
            $data->cargoID, 
            $codworker, 
            pg_escape_string($data->nombrecorto),
            pg_escape_string($data->correo),
            $data->fecha, 1,
            $fn->getClientIP(), 
            $_SESSION['usr_ID'],
            pg_escape_string($data->observac)
          );
          $qry = $db->query_params($sql,$params);
          $xx = $db->fetch_array($qry);
          //respuesta
          $rpta = array("error"=>false, "insert"=>1);
          echo json_encode($rpta);
          break;
        case "updWorker":
          $sql = "update bn_empleados set id_agencia=$3,id_cargo=$4,nombrecorto=$5,correo=$6,observac=$7,sys_ip=$8,sys_user=$9,sys_fecha=now() where id_empleado=$1 and id_coopac=$2;";
          $params = array(
            $data->workerID, 
            $web->coopacID, 
            $data->agenciaID, 
            $data->cargoID, 
            pg_escape_string($data->nombrecorto), 
            $data->correo,
            pg_escape_string($data->observac), 
            $fn->getClientIP(), 
            $_SESSION['usr_ID']
          );
          $qry = $db->query_params($sql,$params);
          $xx = $db->fetch_array($qry);
          //respuesta
          $rpta = array("error"=>false, "update"=>1);
          echo json_encode($rpta);
          break;
        case "delWorkers":
          $params = array();
          for($i=0; $i<count($data->arr); $i++){
            $sql = "update bn_empleados set estado=0,sys_ip=$2,sys_user=$3,sys_fecha=now() where id_empleado=$1";
            $params = array($data->arr[$i],$fn->getClientIP(),$_SESSION['usr_ID']);
            $qry = $db->query_params($sql,$params);
          }
          //respuesta
          $rpta = array("error"=>false, "delete"=>$data->arr);
          echo json_encode($rpta);
          break;
        case "viewWorker":
          $rpta = array('tablaPers'=>$fn->getViewPersona($data->personaID),'tablaWorker'=> getViewWorker($data->personaID),'tablaUser'=>getViewUser($data->personaID));
          echo json_encode($rpta);
          break;
        case "VerifyWorker":
          $tablaPers = ""; //almacena los datos de la persona
          $persona = false; //indica que existe en personas
          $activo = false; //indica que encontro en tabla de empleados

          //verificar en Personas
          $qry = $db->query_params("select id from personas where (nro_dui=$1);",array($data->nroDNI));
          if($db->num_rows($qry)){
            $rs = $db->fetch_array($qry);
            $tablaPers = $fn->getViewPersona($rs["id"]);
            $persona = true;
            //verificar en Empleados
            $qryEmpleado = $db->query_params("select id_empleado from bn_empleados where id_coopac=$1 and id_empleado=$2;",array($web->coopacID,$rs["id"]));
            $activo = ($db->num_rows($qryEmpleado)) ? true : false;
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
            "fecha" => $db->fetch_array($db->query("select now() as fecha;"))["fecha"],
            "coopac" => $web->coopacID);
          echo json_encode($rpta);
          break;
        case "selMenu":
          //cargar Ubigeo
          $pID = (array_key_exists('id',$_REQUEST)) ? ($pID = $_REQUEST['id']) : (0);
          $qry = $db->query("select * from sis_ubigeo where id_padre=".$pID." order by id;");
          if ($db->num_rows($qry)>0) {
            $tabla = array();
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
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
          $tabla = "";
          $qry = $db->query("select u.id_usuario,u.login,e.nombrecorto from bn_usuarios u join bn_empleados e on u.id_usuario=e.id_empleado where u.id_usuario=".$data->userID);
          if ($db->num_rows($qry)==1) {
            $rs = $db->fetch_array($qry);
            $tabla = array(
              "ID" => $rs["id_usuario"],
              "login" => $rs["login"],
              "nombrecorto" => $rs["nombrecorto"]
            );
          }
          //respuesta
          $rpta = $tabla;
          echo json_encode($rpta);
          break;
        case "changeUserPass":
          $sql = "update bn_usuarios set passw=$3,sys_ip=$4,sys_user=$5,sys_fecha=now() where id_usuario=$1 and id_coopac=$2;";
          $params = array($data->userID, $web->coopacID, $data->passw, $fn->getClientIP(), $_SESSION['usr_ID']);
          $qry = $db->query_params($sql,$params);
          $xx = $db->fetch_array($qry);
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
