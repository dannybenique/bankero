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
        $sql = "select s.*,b.nombre as agencia,e.nombrecorto as usermod from bn_socios s,bn_bancos b,bn_empleados e where s.estado=1 and e.id_empleado=s.sys_user and s.id_agencia=b.id and s.id_socio=:socioID and s.id_coopac=:coopacID";
        $params = [":socioID"=>$personaID,"coopacID"=>$web->coopacID];
        $qry = $db->query_all($sql,$params);
        
        if ($qry) {
            $rs = reset($qry);
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
          $tabla = array();
          $buscar = strtoupper($data->buscar);
          $whr = " and id_coopac=".$web->coopacID." and (persona LIKE :buscar or nro_dui LIKE :buscar) ";
          $params = [":buscar"=>'%'.$buscar.'%'];
          $sql = "select count(*) as cuenta from vw_socios where estado in(1,2) ".$whr;
          $qryCount = $db->query_all($sql,$params);
          $rsCount = ($qryCount) ? reset($qryCount)["cuenta"] : (0);

          $sql = "select * from vw_socios where estado in(1,2) ".$whr." order by persona limit 25 offset 0;";
          $qry = $db->query_all($sql,$params);
          if($qry) {
            foreach($qry as $rs){
              $tabla[] = array(
                "ID" => $rs["id_socio"],
                "codigo" => $rs["codigo"],
                "fecha" => $rs["fecha"],
                "DUI" => $rs["dui"],
                "nro_dui"=> str_replace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["nro_dui"]),
                "socio" => str_ireplace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["persona"]),
                "url" => $rs["urlfoto"],
                "direccion" => ($rs["direccion"])
              );
            }
          }

          //respuesta
          $rpta = array("tabla"=>$tabla,"cuenta"=>$rsCount,"rolID" => (int)$_SESSION["usr_data"]["rolID"]);
          echo json_encode($rpta);
          break;
        case "insSocio":
          //ingresar datos del socio
          $qry = $db->query_all("select codigo from bn_bancos where id=".$data->socAgenciaID.";");
          $codagenc = ($qry) ? (reset($qry)["codigo"]) : (null);
          $qry = $db->query_all("select right('000000'||cast(coalesce(max(right(codigo,6)::integer)+1,1) as text),6) as code from bn_socios where id_agencia=".$data->socAgenciaID.";");
          $codsocio = ($qry) ? (reset($qry)["code"]) : (null);
          $sql = "insert into bn_socios values(:socioID,:coopacID,:agenciaID,null,null,:codsocio,:fecha,:estado,:sysIP,:userID,now(),:observac);";
          $params = [
            ":socioID"=>$data->socioID,
            ":coopacID"=>$web->coopacID,
            ":agenciaID"=>$data->socAgenciaID,
            ":codsocio"=>$codagenc."-".$codsocio,
            ":fecha"=>$data->socFecha,
            ":estado"=>1, 
            ":sysIP"=>$fn->getClientIP(),
            ":userID"=>$_SESSION['usr_ID'], 
            ":observac"=>$data->socObservac
          ];
          $qry = $db->query_all($sql,$params);
          $rs = ($qry) ? (reset($qry)) : (null);

          //verificar e ingresar en SALDOS los productos obligatorios
          $qry = $db->query_all("select * from bn_productos where id_tipo_oper=121 and obliga=1 and id_coopac=".$web->coopacID);
          if ($qry) {
            foreach($qry as $rs){
              $xry = $db->query_all("select coalesce(max(id)+1,1) as code from bn_saldos;");
              $id = reset($xry)["code"];
              $xry = $db->query_all("select concat(to_char(now(),'YYYYMMDD'),'-',right('000000'||cast(coalesce(max(right(cod_prod,4)::integer)+1,1) as text),4)) as code from bn_saldos where left(cod_prod,8)=to_char(now(),'YYYYMMDD') and id_coopac=".$web->coopacID);
              $cod_prod = reset($xry)["code"];
              
              $sql = "insert into bn_saldos values(:id,:coopacID,:socioID,:operID,:productoID,:monedaID,:codprod,:saldo,:estado,:sysIP,:userID,now());";
              $params = [
                ":id"=>$id,
                ":coopacID"=>$web->coopacID,
                ":socioID"=>$data->socioID,
                ":operID"=>$rs["id_tipo_oper"],
                ":productoID"=>$rs["id"],
                ":monedaID"=>111,
                ":codprod"=>$cod_prod,
                ":saldo"=>0,
                ":estado"=>1,
                ":sysIP"=>$fn->getClientIP(),
                ":userID"=>$_SESSION['usr_ID']
              ];
              $xry = $db->query_all($sql,$params);
              $rs = ($xry) ? (reset($xry)) : (null);
            }
          }

          //respuesta
          $rpta = array("error"=>false, "insert"=>1);
          echo json_encode($rpta);
          break;
        case "updSocio":
          $sql = "update bn_socios set id_agencia=:agenciaID,observac=:observac,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id_socio=:socioID and id_coopac=:coopacID;";
          $params = [
            ":agenciaID"=>$data->socAgenciaID,
            ":observac"=>$data->socObservac,
            ":sysIP"=>$fn->getClientIP(),
            ":userID"=>$_SESSION['usr_ID'],
            "socioID"=>$data->socioID,
            "coopacID"=>$web->coopacID];
          $qry = $db->query_all($sql,$params);
          $rs = ($qry) ? (reset($qry)) : (null);
          
          //respuesta
          $rpta = array("error"=>false, "update"=>1);
          echo json_encode($rpta);
          break;
        case "delSocios":
          $params = array();
          for($i=0; $i<count($data->arr); $i++){
            $sql = "update bn_socios set estado=0,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id_socio=:socioID";
            $params = [
              ":socioID"=>$data->arr[$i],
              ":sysIP"=>$fn->getClientIP(),
              "userID"=>$_SESSION['usr_ID']
            ];
            $qry = $db->query_all($sql,$params);
            $rs = ($qry) ? (reset($qry)) : (null);
          }
          //respuesta
          $rpta = array("error"=>false, "delete"=>$data->arr);
          echo json_encode($rpta);
          break;
        case "viewSocio":
          switch($data->fullQuery){
            case 0: //datos personales
              $rpta = array(
                'tablaSocio'=> getViewSocio($data->personaID),
                'tablaPers'=>$fc->getViewPersona($data->personaID));
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
          $params = [":nrodni"=>$data->nroDNI];
          $qry = $db->query_all("select id from personas where (nro_dui=:nrodni);",$params);
          if($qry){
            $rs = reset($qry);
            $tablaPers = $fn->getViewPersona($rs["id"]);
            $persona = true;
            
            //verificar en Socios
            $sql = "select id_socio from bn_socios where id_coopac=:coopacID and id_socio=:socioID;";
            $paramSocio = [":coopacID"=>$web->coopacID,":socioID"=>$rs["id"]];
            $qrySocio = $db->query_all($sql,$paramSocio);
            $activo = ($qrySocio) ? true : false;
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
          //obtener fecha actual de operacion
          $qry = $db->query_all("select cast(now() as date) as fecha");
          if($qry){ $rs = reset($qry); }
          $fechaHoy = $rs["fecha"];
          
          //respuesta
          $rpta = array(
            "comboAgencias" => $fn->getComboBox("select id,nombre from bn_bancos where estado=1 and id_padre=".$web->coopacID),
            "fecha" => $fn->getFechaActualDB(),
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
