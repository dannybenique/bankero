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
        case "selDesembolsos":
          $whr = "";
          $tabla = array();
          $data->buscar = pg_escape_string(strtoupper($data->buscar));
          $whr = " and id_coopac=".$web->coopacID." and (socio like '%".$data->buscar."%' or nro_dui like '%".$data->buscar."%') ";
          $rsCount = $db->fetch_array($db->query("select count(*) as cuenta from vw_prestamos_min where estado=2 ".$whr.";"));

          $qry = $db->query("select * from vw_prestamos_min where estado=2 ".$whr." order by socio limit 25 offset 0;");
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
        case "delDesembolsos":
          $params = array();
          for($i=0; $i<count($data->arr); $i++){
            $sql = "update bn_prestamos set estado=3,id_aprueba=null,sys_ip=$2,sys_user=$3,sys_fecha=now() where id=$1";
            $params = array($data->arr[$i],$fn->getClientIP(),$_SESSION['usr_ID']);
            $qry = $db->query_params($sql,$params);
          }
          //respuesta
          $rpta = array("error"=>false, "delete"=>$data->arr);
          echo json_encode($rpta);
          break;
        case "viewDesembolso":
          $tabla = 0;
          $socioID = 0;
          $qry = $db->query("select *,now() as fecha_desemb from vw_prestamos_ext where id=".$data->SoliCredID);
          if ($db->num_rows($qry)) {
            $rs = $db->fetch_array($qry);
            $socioID = $rs["id_socio"];
            $date = new DateTime($rs["fecha_pricuota"]);
            $pivot = ($rs["id_tipocred"]==1)?($date->format('Ymd')):($rs["frecuencia"]);
            $cuota = $fn->getSimulacionCredito($rs["id_tipocred"],$rs["importe"],$rs["tasa_cred"],$rs["tasa_desgr"],$rs["nro_cuotas"],$rs["fecha_otorga"],$pivot);
            $tabla = array(
              "ID" => $rs["id"],
              "codigo" => $rs["codigo"],
              "socio" => $rs["socio"],
              "coopacID" => $rs["id_coopac"],
              "agenciaID" => $rs["id_agencia"],
              "tipocredID" => $rs["id_tipocred"],
              "productoID" => $rs["id_producto"],
              "socioID" => $rs["id_socio"],
              "monedaID" => $rs["id_moneda"],
              "agencia" => $rs["agencia"],
              "promotor" => $rs["promotor"],
              "analista" => $rs["analista"],
              "aprueba" => ($rs["id_aprueba"]==null)?(""):($rs["aprueba"]),
              "producto" => $rs["producto"],
              "tiposbs" => $rs["tiposbs"],
              "destsbs" => $rs["destsbs"],
              "clasifica" => $rs["clasifica"],
              "condicion" => $rs["condicion"],
              "moneda" => $rs["moneda"],
              "mon_abrevia" => $rs["mon_abrevia"],
              "importe" => $rs["importe"],
              "saldo" => $rs["saldo"],
              "tasa_cred" => $rs["tasa_cred"],
              "tasa_mora" => $rs["tasa_mora"],
              "tasa_desgr" => $rs["tasa_desgr"],
              "nrocuotas" => $rs["nro_cuotas"],
              "fecha_desemb" => $rs["fecha_desemb"],
              "fecha_solicred" => $rs["fecha_solicred"],
              "fecha_otorga" => $rs["fecha_otorga"],
              "fecha_pricuota" => $rs["fecha_pricuota"],
              "tipocred" => $rs["tipocred"],
              "frecuencia" => $rs["frecuencia"],
              "cuota" => $cuota[1]["cuota"],
              "observac" => $rs["observac"],
              "estado" => ($rs["estado"]*1)
            );
          }
          
          //respuesta
          $rpta = array('tablaDesembolso'=> $tabla,'tablaPers'=>$fn->getViewPersona($socioID));
          echo json_encode($rpta);
          break;
        case "ejecutarDesembolso":
          $tipo_operID = 124; //creditos
          $tipo_pagoID = 164; //en efectivo
          $estado = 1; //activo
          $clientIP = $fn->getClientIP();
          $userID = $_SESSION['usr_ID'];

          /******agregamos bn_movim*******/
          /*******************************/
          $movimID = $db->fetch_array($db->query("select coalesce(max(id)+1,1) as code from bn_movim;"))["code"];
          $movimCode = $userID."-".$db->fetch_array($db->query("select right('0000000'||cast(coalesce(max(right(codigo,7)::integer)+1,1) as text),7) as code from bn_movim where id_cajera=".$userID))["code"];
          $sql = "insert into bn_movim values($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,now(),$11,$12,$13,$14,$15,now(),$16)";
          $params = array(
            $movimID,
            $web->coopacID,
            $data->agenciaID,
            $tipo_operID,
            $tipo_pagoID,
            $data->monedaID,
            $data->socioID,
            $data->prestamoID,
            $data->productoID,
            $userID,
            $movimCode,
            $data->importe,
            $estado,
            $clientIP,
            $userID,
            $data->observac);
          $rs = $db->fetch_array($db->query_params($sql,$params));
          //agregamos bn_movim_det
          $detalleID = $db->fetch_array($db->query("select coalesce(max(id)+1,1) as code from bn_movim_det;"))["code"];
          $tipo_movID = 9; 
          $item = 1;
          $sql = "insert into bn_movim_det values($1,$2,$3,$4,$5);";
          $params = array($detalleID,$movimID,$tipo_movID,$item,$data->importe);
          $rs = $db->fetch_array($db->query_params($sql,$params));

          
          /******agregamos bn_saldos*******/
          /********************************/
          $saldoID = $db->fetch_array($db->query("select coalesce(max(id)+1,1) as code from bn_saldos;"))["code"];
          $sql = "insert into bn_saldos values($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,now())";
          $params = array(
            $saldoID,
            $web->coopacID,
            $data->socioID,
            $tipo_operID,
            $data->productoID,
            $data->monedaID,
            $data->cod_prod,
            $data->importe*(-1),
            $estado, $clientIP, $userID );
          $rs = $db->fetch_array($db->query_params($sql,$params));
          

          /******actualizar bn_prestamos******/
          /***********************************/
          //actualizar cabecera de prestamo
          $sql = "update bn_prestamos set estado=$2,sys_ip=$3,sys_user=$4,sys_fecha=now() where id=$1;";
          $params = array($data->prestamoID,1,$clientIP,$userID);
          $rs = $db->fetch_array($db->query_params($sql,$params));
          //agregar detalle de prestamo
          $genPlanPagos = ($data->tipocredID=="1")?("fn_get_planpagos_fechafija"):("fn_get_planpagos_plazofijo");
          $sql = "insert into bn_prestamos_det select ".$data->prestamoID.",num,fecha,capital,interes,otros,saldo,0,0,0,0,concat('[{\"id\":13,\"descri\":\"PAGO SEGURO DESGR\",\"monto\":',otros::float,'}]'),0,null,null from ".$genPlanPagos."($1,$2,$3,$4,$5,$6) as (num integer,fecha date,dias integer,tasa_efec float,cuotax numeric,cuota numeric,capital numeric,interes numeric,otros numeric,saldo numeric)";
          $params = array(
            $data->importe,
            $data->tasa_cred,
            $data->tasa_desgr,
            $data->nro_cuotas,
            $data->fecha_otorga,
            $data->pivot
          );
          $rs = $db->fetch_array($db->query_params($sql,$params));

          //respuesta
          $rpta = array("error"=>false, "movimID"=>$movimID);
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
