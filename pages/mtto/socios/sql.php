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
      function getViewSocio($personaID){
        $db = $GLOBALS["db"]; //base de datos
        $fn = $GLOBALS["fn"]; //funciones
        $web = $GLOBALS["web"]; //web-config
        
        //obtener datos personales
        $sql = "select s.*,b.nombre as agencia,e.nombrecorto as usermod from bn_socios s,bn_bancos b,bn_empleados e where s.estado=1 and e.id_empleado=s.sys_user and s.id_agencia=b.id and s.id_socio=$1 and s.id_coopac=$2";
        $params = array($personaID,$web->coopacID);
        $qry = $db->query_params($sql,$params);
        
        if ($db->num_rows($qry)) {
            $rs = $db->fetch_array($qry);
            $tabla = array(
              "ID" => ($rs["id_socio"]),
              "coopacID" => ($rs["id_coopac"]),
              "agenciaID" => ($rs["id_agencia"]),
              "comboAgencias" => ($fn->getComboBox("select id,nombre from bn_bancos where estado=1 and id_padre=".$web->coopacID." order by codigo;")),
              "agencia" => $rs["agencia"],
              "fecha" => $rs["fecha"],
              "codigo" => $rs["codigo"],
              "observac" => ($rs["observac"]),
              "usermod" => ($rs["usermod"]),
              "sysuser" => ($rs["sys_user"]),
              "sysfecha" => ($rs["sys_fecha"])
            );
        }
        return $tabla; 
      }
      switch ($data->TipoQuery) {
        case "selSocios":
          $whr = "";
          $tabla = array();
          $data->buscar = pg_escape_string(strtoupper($data->buscar));
          $whr = " and id_coopac=".$web->coopacID." and (persona like '%".$data->buscar."%' or nro_dui like '%".$data->buscar."%') ";
          $rsCount = $db->fetch_array($db->query("select count(*) as cuenta from vw_socios where estado in(1,2) ".$whr.";"));

          $qry = $db->query("select * from vw_socios where estado in(1,2) ".$whr." order by persona limit 25 offset 0;");
          if ($db->num_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => $rs["id_socio"],
                "codigo" => $rs["codigo"],
                "fecha" => $rs["fecha"],
                "DUI" => $rs["dui"],
                "nro_dui"=> str_replace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', $rs["nro_dui"]),
                "socio" => str_ireplace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', $rs["persona"]),
                "url" => $rs["urlfoto"],
                "direccion" => ($rs["direccion"])
              );
            }
          }
          $rpta = array("tabla"=>$tabla,"cuenta"=>$rsCount["cuenta"],"rolID" => (int)$_SESSION["usr_data"]["rolID"]);
          echo json_encode($rpta);
          break;
        case "insSocio":
          //ingresar datos del socio
          $codagenc = $db->fetch_array($db->query("select codigo from bn_bancos where id=".$data->socAgenciaID.";"))["codigo"];
          $codsocio = $db->fetch_array($db->query("select right('000000'||cast(coalesce(max(right(codigo,6)::integer)+1,1) as text),6) as code from bn_socios where id_agencia=".$data->socAgenciaID.";"))["code"];
          $sql = "insert into bn_socios values($1,$2,$3,null,null,$4,$5,$6,$7,$8,now(),$9);";
          $params = array( $data->socioID, $web->coopacID, $data->socAgenciaID, $codagenc."-".$codsocio, $data->socFecha, 1, $fn->getClientIP(), $_SESSION['usr_ID'], $data->socObservac );
          $exec = $db->fetch_array($db->query_params($sql,$params));

          //verificar e ingresar en SALDOS los productos obligatorios
          $qry = $db->query_params("select * from bn_productos where id_tipo_oper=121 and obliga=1 and id_coopac=$1",array($web->coopacID));
          if ($db->num_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $id = $db->fetch_array($db->query("select coalesce(max(id)+1,1) as code from bn_saldos;"))["code"];
              $cod_prod = $db->fetch_array($db->query_params("select concat(to_char(now(),'YYYYMMDD'),'-',right('000000'||cast(coalesce(max(right(cod_prod,4)::integer)+1,1) as text),4)) as code from bn_saldos where left(cod_prod,8)=to_char(now(),'YYYYMMDD') and id_coopac=$1;",array($web->coopacID)))["code"];
              
              $sql = "insert into bn_saldos values($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,now())";
              $params = array($id,$web->coopacID,$data->socioID,$rs["id_tipo_oper"],$rs["id"],111,$cod_prod,0,1,$fn->getClientIP(),$_SESSION['usr_ID']);
              $exec = $db->fetch_array($db->query_params($sql,$params));
            }
          }

          //respuesta
          $rpta = array("error"=>false, "insert"=>1);
          echo json_encode($rpta);
          break;
        case "updSocio":
          $sql = "update bn_socios set id_agencia=$3,observac=$4,sys_ip=$5,sys_user=$6,sys_fecha=now() where id_socio=$1 and id_coopac=$2;";
          $params = array(
            $data->socioID, 
            $web->coopacID, 
            $data->socAgenciaID, 
            $data->socObservac, 
            $fn->getClientIP(), 
            $_SESSION['usr_ID']
          );
          $qry = $db->query_params($sql,$params);
          $xx = $db->fetch_array($qry);
          
          //respuesta
          $rpta = array("error"=>false, "update"=>1);
          echo json_encode($rpta);
          break;
        case "delSocios":
          $params = array();
          for($i=0; $i<count($data->arr); $i++){
            $sql = "update bn_socios set estado=0,sys_ip=$2,sys_user=$3,sys_fecha=now() where id_socio=$1";
            $params = array($data->arr[$i],$fn->getClientIP(),$_SESSION['usr_ID']);
            $qry = $db->query_params($sql,$params);
          }
          //respuesta
          $rpta = array("error"=>false, "delete"=>$data->arr);
          echo json_encode($rpta);
          break;
        case "viewSocio":
          switch($data->fullQuery){
            case 0: //datos personales
              $rpta = array('tablaSocio'=> getViewSocio($data->personaID),'tablaPers'=>$fc->getViewPersona($data->personaID));
              break;
            case 1: //datos personales + laborales
              $rpta = array(
                'tablaSocio'=> getViewSocio($data->personaID),
                'tablaPers'=>$fn->getViewPersona($data->personaID),
                'tablaLabo'=>$fn->getAllLaborales($data->personaID));
              break;
            case 2: //datos personales + laborales + conyuge
              $rpta = array(
                'tablaSocio'=> getViewSocio($data->personaID),
                'tablaPers'=>$fn->getViewPersona($data->personaID),
                'tablaLabo'=>$fn->getAllLaborales($data->personaID),
                'tablaCony'=>$fn->getViewConyuge($data->personaID));
              break;
          }
          echo json_encode($rpta);
          break;
        case "VerifySocio":
          $tablaPers = ""; //almacena los datos de la persona
          $persona = false; //indica que existe en personas
          $activo = false; //indica que encontro en tabla de socios
          
          //verificar en Personas
          $qry = $db->query_params("select id from personas where (nro_dui=$1);",array($data->nroDNI));
          if($db->num_rows($qry)){
            $rs = $db->fetch_array($qry);
            $tablaPers = $fn->getViewPersona($rs["id"]);
            $persona = true;
            //verificar en Socios
            $qrySocio = $db->query_params("select id_socio from bn_socios where id_coopac=$1 and id_socio=$2;",array($web->coopacID,$rs["id"]));
            $activo = ($db->num_rows($qrySocio)) ? true : false;
          }

          //respuesta
          $rpta = array(
            "tablaPers" => $tablaPers,
            "persona" => $persona,
            "activo" => $activo,
            "mensajeNOadd" => "ya es SOCIO ACTIVO...");
          echo json_encode($rpta);
          break;
        case "startSocio":
          //respuesta
          $rpta = array(
            "comboAgencias" => $fn->getComboBox("select id,nombre from bn_bancos where estado=1 and id_padre=".$web->coopacID),
            "fecha" => $db->fetch_array($db->query("select now() as fecha;"))["fecha"],
            "coopac" => $web->coopacID);
          echo json_encode($rpta);
          break;
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
