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
        $sql = "select s.*,b.nombre as agencia from bn_socios s,bn_bancos b where s.estado=1 and s.id_agencia=b.id and s.id_socio=$1 and s.id_coopac=$2";
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
              "sysuser" => ($rs["sys_user"]),
              "sysfecha" => ($rs["sys_fecha"])
            );
        }
        return $tabla; 
      }
      switch ($data->TipoQuery) {
        case "selSoliCred":
          $whr = "";
          $tabla = array();
          $data->buscar = pg_escape_string(strtoupper($data->buscar));
          $whr = " and id_coopac=".$web->coopacID." and (socio like '%".$data->buscar."%' or nro_dui like '%".$data->buscar."%') ";
          $rsCount = $db->fetch_array($db->query("select count(*) as cuenta from vw_prestamos_min where estado=3 ".$whr.";"));

          $qry = $db->query("select * from vw_prestamos_min where estado=3 ".$whr." order by socio limit 25 offset 0;");
          if ($db->num_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => $rs["id"],
                "codigo" => $rs["codigo"],
                "fecha" => $rs["fecha_solicred"],
                "otorga" => $rs["fecha_otorga"],
                "nro_dui"=> str_replace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', $rs["nro_dui"]),
                "socio" => str_ireplace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', $rs["socio"]),
                "tipo_oper" => $rs["tipo_oper"],
                "producto" => $rs["producto"],
                "mon_abrevia" => $rs["mon_abrevia"],
                "tiposbs" => $rs["tipo_sbs"],
                "destsbs" => $rs["dest_sbs"],
                "tasa" => $rs["tasa"]*1,
                "importe" => $rs["importe"]*1,
                "nro_cuotas" => $rs["nro_cuotas"]*1
              );
            }
          }
          $rpta = array("tabla"=>$tabla,"cuenta"=>$rsCount["cuenta"]);
          echo json_encode($rpta);
          break;
        case "insSoliCred":
          //inicialmente el estado debe ser 3 (en bn_prestamos)
          $id = $db->fetch_array($db->query("select coalesce(max(id)+1,1)as maxi from bn_prestamos"))["maxi"];
          $codigo = $data->fecha_solicred."-".$db->fetch_array($db->query("select right('0000'||cast(coalesce(max(right(codigo,4)::integer)+1,1) as text),4) as maxi from bn_prestamos where id_coopac=".$web->coopacID." and fecha_solicred='".$data->fecha_solicred."';"))["maxi"];
          $sql = "insert into bn_prestamos values($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18,$19,$20,$21,$22,$23,$24,$25,$26,$27,$28,$29,now(),$30);";
          $params = array(
            $id, 
            $codigo, 
            $data->socioID, 
            $web->coopacID, 
            $data->agenciaID, 
            $data->promotorID, 
            $data->analistaID, 
            null, 
            $data->productoID, 
            $data->tiposbsID, 
            $data->destsbsID, 
            $data->clasificaID, 
            $data->condicionID, 
            $data->monedaID, 
            $data->importe, 
            $data->saldo, 
            $data->tasa, 
            $data->mora, 
            $data->desgr, 
            $data->nrocuotas, 
            $data->fecha_solicred,
            null,
            $data->fecha_otorga, 
            $data->fecha_pricuota,
            $data->tipocredID,
            ($data->tipocredID==2)?$data->frecuencia:null, 
            3, 
            $fn->getClientIP(), 
            $_SESSION['usr_ID'], 
            $data->observac
          );
          
          $qry = $db->query_params($sql,$params);
          if($qry){
            $xx = $db->fetch_array($qry);
            $rpta = array("error"=>false, "insert"=>1);
          } else {
            $rpta = array("error"=>true, "insert"=>0);
          }
          
          //respuesta
          echo json_encode($rpta);
          break;
        case "updSoliCred":
          $sql = "update bn_prestamos set id_socio=$2,id_agencia=$3,id_promotor=$4,id_analista=$5,id_producto=$6,id_tiposbs=$7,id_destsbs=$8,id_clasifica=$9,id_condicion=$10,id_moneda=$11,importe=$12,saldo=$13,tasa_cred=$14,tasa_mora=$15,tasa_desgr=$16,nro_cuotas=$17,fecha_solicred=$18,fecha_otorga=$19,fecha_pricuota=$20,id_tipocred=$21,frecuencia=$22,sys_ip=$23,sys_user=$24,sys_fecha=now(),observac=$25 where id=$1;";
          $params = array(
            $data->ID, 
            $data->socioID, 
            $data->agenciaID, 
            $data->promotorID, 
            $data->analistaID,
            $data->productoID, 
            $data->tiposbsID, 
            $data->destsbsID, 
            $data->clasificaID, 
            $data->condicionID, 
            $data->monedaID, 
            $data->importe, 
            $data->saldo, 
            $data->tasa, 
            $data->mora, 
            $data->desgr, 
            $data->nrocuotas, 
            $data->fecha_solicred,
            $data->fecha_otorga, 
            $data->fecha_pricuota,
            $data->tipocredID, 
            ($data->tipocredID==2)?$data->frecuencia:null, 
            $fn->getClientIP(), 
            $_SESSION['usr_ID'], 
            $data->observac
          );
          $qry = $db->query_params($sql,$params);
          $xx = $db->fetch_array($qry);
          //respuesta
          $rpta = array("error"=>false, "update"=>1);
          echo json_encode($rpta);
          break;
        case "delSoliCred":
          $params = array();
          for($i=0; $i<count($data->arr); $i++){
            $sql = "update bn_prestamos set estado=0,sys_ip=$2,sys_user=$3,sys_fecha=now() where id=$1";
            $params = array($data->arr[$i],$fn->getClientIP(),$_SESSION['usr_ID']);
            $qry = $db->query_params($sql,$params);
          }
          //respuesta
          $rpta = array("error"=>false, "delete"=>$data->arr);
          echo json_encode($rpta);
          break;
        case "newSoliCred":
          //respuesta
          $rpta = array(
            "comboAgencias" => $fn->getComboBox("select id,nombre from bn_bancos where estado=1 and id_padre=".$web->coopacID),
            "comboEmpleados" => $fn->getComboBox("select id_empleado as id,empleado as nombre from vw_empleados where estado=1 and id_coopac=".$web->coopacID),
            "comboProductos" => $fn->getComboBox("select id,nombre from bn_productos where estado=1 and id_padre=4 and id_coopac=".$web->coopacID),
            "comboTipoSBS" => $fn->getComboBox("select s.id,s.nombre from sis_tipos s join bn_tipos b on(b.id_tipo=s.id) where s.id_padre=5 and b.id_coopac=".$web->coopacID), //tipos credito SBS
            "comboDestSBS" => $fn->getComboBox("select id,nombre from sis_tipos where id_padre=6 order by id;"), //destino credito SBS
            "comboClasifica" => $fn->getComboBox("select id,nombre from sis_tipos where id_padre=3 order by id;"), //clasificacion crediticia
            "comboCondicion" => $fn->getComboBox("select id,nombre from sis_tipos where id_padre=4 order by id;"), //condicion credito
            "comboMoneda" => $fn->getComboBox("select id,nombre from sis_tipos where id_padre=1 order by id;"), //tipos moneda
            "fecha" => $db->fetch_array($db->query("select now() as fecha;"))["fecha"],
            "coopac" => $web->coopacID);
          echo json_encode($rpta);
          break;
        case "viewSoliCred":
          $tabla = 0;
          $socioID = 0;
          $qry = $db->query("select * from bn_prestamos where id=".$data->SoliCredID);
          if ($db->num_rows($qry)) {
            $rs = $db->fetch_array($qry);
            $socioID = $rs["id_socio"];
            $date = new DateTime($rs["fecha_pricuota"]);
            $pivot = ($rs["id_tipocred"]==1)?($date->format('Ymd')):($rs["frecuencia"]);
            $cuota = $fn->getSimulacionCredito($rs["id_tipocred"],$rs["importe"],$rs["tasa_cred"],$rs["tasa_desgr"],$rs["nro_cuotas"],$rs["fecha_otorga"],$pivot);

            $tabla = array(
              "ID" => $rs["id"],
              "codigo" => $rs["codigo"],
              "socioID" => $rs["id_socio"],
              "coopacID" => $rs["id_coopac"],
              "agenciaID" => $rs["id_agencia"],
              "promotorID" => $rs["id_promotor"],
              "analistaID" => $rs["id_analista"],
              "productoID" => $rs["id_producto"],
              "tiposbsID" => $rs["id_tiposbs"],
              "destsbsID" => $rs["id_destsbs"],
              "clasificaID" => $rs["id_clasifica"],
              "condicionID" => $rs["id_condicion"],
              "monedaID" => $rs["id_moneda"],
              "importe" => $rs["importe"],
              "saldo" => $rs["saldo"],
              "tasa" => $rs["tasa_cred"],
              "mora" => $rs["tasa_mora"],
              "desgr" => $rs["tasa_desgr"],
              "nrocuotas" => $rs["nro_cuotas"],
              "fecha_solicred" => $rs["fecha_solicred"],
              "fecha_otorga" => $rs["fecha_otorga"],
              "fecha_pricuota" => $rs["fecha_pricuota"],
              "tipocredID" => $rs["id_tipocred"],
              "frecuencia" => $rs["frecuencia"],
              "cuota" => $cuota[1]["cuota"],
              "observac" => $rs["observac"],
              "estado" => ($rs["estado"]*1),
              "comboAgencias" => $fn->getComboBox("select id,nombre from bn_bancos where estado=1 and id_padre=".$web->coopacID), //agencias
              "comboEmpleados" => $fn->getComboBox("select id_empleado as id,empleado as nombre from vw_empleados where estado=1 and id_coopac=".$web->coopacID), //empleados
              "comboProductos" => $fn->getComboBox("select id,nombre from bn_productos where estado=1 and id_padre=4 and id_coopac=".$web->coopacID), //productos
              "comboTipoSBS" => $fn->getComboBox("select s.id,s.nombre from sis_tipos s join bn_tipos b on(b.id_tipo=s.id) where s.id_padre=5 and b.id_coopac=".$web->coopacID), //tipos credito SBS
              "comboDestSBS" => $fn->getComboBox("select id,nombre from sis_tipos where id_padre=6 order by id;"), //destino credito
              "comboClasifica" => $fn->getComboBox("select id,nombre from sis_tipos where id_padre=3 order by id;"), //clasificacion crediticia
              "comboCondicion" => $fn->getComboBox("select id,nombre from sis_tipos where id_padre=4 order by id;"), //condicion credito
              "comboMoneda" => $fn->getComboBox("select id,nombre from sis_tipos where id_padre=1 order by id;"), //tipos moneda
            );
          }
          
          //respuesta
          $rpta = array('tablaSoliCred'=> $tabla,'tablaPers'=>$fn->getViewPersona($socioID));
          echo json_encode($rpta);
          break;
        case "viewApruebaSoliCred":
          $qry = $db->query("select * from vw_prestamos_ext where id=".$data->SoliCredID);
          if ($db->num_rows($qry)) {
            $rs = $db->fetch_array($qry);
            $date = new DateTime($rs["fecha_pricuota"]);
            $pivot = ($rs["id_tipocred"]==1)?($date->format('Ymd')):($rs["frecuencia"]);
            $cuota = $fn->getSimulacionCredito($rs["id_tipocred"],$rs["importe"],$rs["tasa_cred"],$rs["tasa_desgr"],$rs["nro_cuotas"],$rs["fecha_otorga"],$pivot);
            $tabla = array(
              "ID" => $rs["id"],
              "codigo" => $rs["codigo"],
              "socio" => $rs["socio"],
              "coopacID" => $rs["id_coopac"],
              "agencia" => $rs["agencia"],
              "promotor" => $rs["promotor"],
              "analista" => $rs["analista"],
              "producto" => $rs["producto"],
              "tiposbs" => $rs["tiposbs"],
              "destsbs" => $rs["destsbs"],
              "clasifica" => $rs["clasifica"],
              "condicion" => $rs["condicion"],
              "moneda" => $rs["moneda"],
              "mon_abrevia" => $rs["mon_abrevia"],
              "importe" => $rs["importe"],
              "saldo" => $rs["saldo"],
              "tasa" => $rs["tasa_cred"],
              "mora" => $rs["tasa_mora"],
              "desgr" => $rs["tasa_desgr"],
              "nrocuotas" => $rs["nro_cuotas"],
              "fecha_solicred" => $rs["fecha_solicred"],
              "fecha_otorga" => $rs["fecha_otorga"],
              "fecha_pricuota" => $rs["fecha_pricuota"],
              "tipocred" => $rs["tipocred"],
              "tipocredID" => $rs["id_tipocred"],
              "fecha_solicred" => $rs["fecha_solicred"],
              "frecuencia" => $rs["frecuencia"],
              "cuota" => $cuota[1]["cuota"],
              "observac" => $rs["observac"],
              "estado" => ($rs["estado"]*1)
            );
          }
          //respuesta
          $rpta = $tabla;
          echo json_encode($rpta);
          break;
        case "VerifySoliCred":
          $tablaPers = ""; //almacena los datos de la persona
          $persona = false; //indica que existe en personas
          $activo = false; //indica que encontro en tabla de prestamos
          
          //verificar en Personas
          $qry = $db->query_params("select p.id from personas p, bn_socios s where p.id=s.id_socio and (p.nro_dui=$1) and (s.id_coopac=$2);",array($data->nroDNI,$web->coopacID));
          if($db->num_rows($qry)){
            $rs = $db->fetch_array($qry);
            $tablaPers = $fn->getViewPersona($rs["id"]);
            $persona = true;
            $activo = true;
          }

          //respuesta
          $rpta = array(
            "tablaPers" => $tablaPers,
            "persona"=>$persona,
            "activo"=>$activo,
            "mensajeNOadd" => "");
          echo json_encode($rpta);
          break;
        case "cambiarTipoSBS":
          //respuesta
          $rpta = $fn->getComboBox("select id,nombre from sis_tipos where id_padre=".$data->padreID." order by id;");
          echo json_encode($rpta);
          break;
        case "simulaCredito":
          //obtenemos la simulacion
          $pivot = ($data->TipoCredito=="1")?($data->pricuota):($data->frecuencia);
          $tabla = $fn->getSimulacionCredito(
            $data->TipoCredito,
            $data->importe,
            $data->TEA,
            $data->segDesgr,
            $data->nroCuotas,
            $data->fecha,
            $pivot
          );

          //tasas
          $ss = $db->fetch_array($db->query("select fn_get_tem(".$data->TEA.") as tem,fn_get_ted(".$data->TEA.") as  ted;"));
          $rpta = array("error"=>false,"tabla"=>$tabla[1], "tea"=>$data->TEA, "tem"=>$ss["tem"], "ted"=>$ss["ted"]);
          
          //respuesta
          echo json_encode($rpta);
          break;
        case "aprobarSoliCred":
          $sql = "update bn_prestamos set estado=$2,id_aprueba=$3,fecha_aprueba=now(),sys_ip=$4,sys_user=$3,sys_fecha=now() where id=$1";
          $params = array(
            $data->SoliCredID,
            2,
            $_SESSION['usr_ID'],
            $fn->getClientIP()
          );
          $qry = $db->query_params($sql,$params);
          $xx = $db->fetch_array($qry);
          //respuesta
          $rpta = array("error"=>false, "update"=>1);
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
