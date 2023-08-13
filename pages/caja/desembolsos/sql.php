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
        $sql = "select s.*,b.nombre as agencia from bn_socios s join bn_bancos b on (s.id_agencia=b.id) where s.estado=1 and s.id_socio=:socioID and s.id_coopac=:coopacID";
        $params = [":socioID"=>$personaID,":coopacID"=>$web->coopacID];
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
          $buscar = strtoupper($data->buscar);
          $whr = " and id_coopac=:coopacID and (socio LIKE :buscar or nro_dui LIKE :buscar) ";
          $params = [":coopacID"=>$web->coopacID,":buscar"=>'%'.$buscar.'%'];
          $qry = $db->query_all("select count(*) as cuenta from vw_prestamos_min where estado=2 ".$whr.";",$params);
          $rsCount = reset($qry);

          $qry = $db->query_all("select * from vw_prestamos_min where estado=2 ".$whr." order by socio limit 25 offset 0;",$params);
          if ($qry) {
            foreach($qry as $rs){
              $tabla[] = array(
                "ID" => $rs["id"],
                "codigo" => $rs["codigo"],
                "fecha" => $rs["fecha_solicred"],
                "otorga" => $rs["fecha_otorga"],
                "nro_dui"=> str_replace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["nro_dui"]),
                "socio" => str_ireplace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["socio"]),
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

          //respuesta
          $rpta = array("tabla"=>$tabla,"cuenta"=>$rsCount["cuenta"]);
          echo json_encode($rpta);
          break;
        case "delDesembolsos":
          $params = array();
          for($i=0; $i<count($data->arr); $i++){
            $sql = "update bn_prestamos set estado=3,id_aprueba=null,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id=:id";
            $params = [":id"=>$data->arr[$i],":sysIP"=>$fn->getClientIP(),":userID"=>$_SESSION['usr_ID']];
            $qry = $db->query_all($sql,$params);
            $rs = reset($qry);
          }
          //respuesta
          $rpta = array("error"=>false, "delete"=>$data->arr);
          echo json_encode($rpta);
          break;
        case "viewDesembolso":
          $tabla = 0;
          $socioID = 0;
          $qry = $db->query_all("select *,now() as fecha_desemb from vw_prestamos_ext where id=".$data->SoliCredID);
          if ($qry) {
            $rs = reset($qry);
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
          $userID = $_SESSION['usr_ID'];
          $clientIP = $fn->getClientIP();

          /******agregamos bn_movim*******/
          /*******************************/
          $qry = $db->query_all("select coalesce(max(id)+1,1) as code from bn_movim;");
          $movimID = reset($qry)["code"];
          $qry = $db->query_all("select right('0000000'||cast(coalesce(max(right(codigo,7)::integer)+1,1) as text),7) as code from bn_movim where id_cajera=".$userID);
          $movimCode = reset($qry)["code"];
          $sql = "insert into bn_movim values(:id,:coopacID,:agenciaID,:operID,:pagoID,:monedaID,:socioID,:prestamoID,:productoID,:userID,now(),:movimCode,:importe,:estado,:sysIP,:userID,now(),:observac)";
          $params = [
            ":id"=>$movimID,
            ":coopacID"=>$web->coopacID,
            ":agenciaID"=>$data->agenciaID,
            ":operID"=>$tipo_operID,
            ":pagoID"=>$tipo_pagoID,
            ":monedaID"=>$data->monedaID,
            ":socioID"=>$data->socioID,
            ":prestamoID"=>$data->prestamoID,
            ":productoID"=>$data->productoID,
            ":userID"=>$userID,
            ":movimCode"=>$userID."-".$movimCode,
            ":importe"=>$data->importe,
            ":estado"=>$estado,
            ":sysIP"=>$clientIP,
            ":observac"=>$data->observac
          ];
          $qry = $db->query_all($sql,$params);
          $rs = reset($qry);
          //agregamos bn_movim_det
          $qry = $db->query_all("select coalesce(max(id)+1,1) as code from bn_movim_det;");
          $detalleID = reset($qry)["code"];
          $tipo_movID = 9; 
          $item = 1;
          $sql = "insert into bn_movim_det values(:id,:cabeceraID,:tipomovID,:item,:importe);";
          $params = [
            ":id"=>$detalleID,
            ":cabeceraID"=>$movimID,
            ":tipomovID"=>$tipo_movID,
            ":item"=>$item,
            ":importe"=>$data->importe
          ];
          $qry = $db->query_all($sql,$params);
          $rs = reset($qry);


          /******agregamos bn_saldos*******/
          /********************************/
          $qry = $db->query_all("select coalesce(max(id)+1,1) as code from bn_saldos;");
          $saldoID = reset($qry)["code"];
          $sql = "insert into bn_saldos values(:id,:coopacID,:socioID,:operID,:productoID,:monedaID,:codprod,:importe,:estado,:sysIP,:userID,now())";
          $params = [
            ":id"=>$saldoID,
            ":coopacID"=>$web->coopacID,
            ":socioID"=>$data->socioID,
            ":operID"=>$tipo_operID,
            ":productoID"=>$data->productoID,
            ":monedaID"=>$data->monedaID,
            ":codprod"=>$data->cod_prod,
            ":importe"=>$data->importe*(-1),
            ":estado"=>$estado, 
            ":sysIP"=>$clientIP, 
            ":userID"=>$userID
          ];
          $qry = $db->query_all($sql,$params);
          $rs = reset($qry);


          /******actualizar bn_prestamos******/
          /***********************************/
          //actualizar cabecera de prestamo
          $sql = "update bn_prestamos set estado=:estado,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id=:id;";
          $params = [":id"=>$data->prestamoID,":estado"=>1,":sysIP"=>$clientIP,":userID"=>$userID];
          $qry = $db->query_all($sql,$params);
          $rs = reset($qry);
          //agregar detalle de prestamo
          $genPlanPagos = ($data->tipocredID=="1")?("fn_get_planpagos_fechafija"):("fn_get_planpagos_plazofijo");
          $sql = "insert into bn_prestamos_det select ".$data->prestamoID.",num,fecha,capital,interes,otros,saldo,0,0,0,0,concat('[{\"id\":13,\"descri\":\"PAGO SEGURO DESGR\",\"monto\":',otros::float,'}]'),0,null,null from ".$genPlanPagos."(:importe,tasa,:desgr,:nrocuotas,fechaOtor,:pivot) as (num integer,fecha date,dias integer,tasa_efec float,cuotax numeric,cuota numeric,capital numeric,interes numeric,otros numeric,saldo numeric)";
          $params = [
            ":importe"=>$data->importe,
            ":tasa"=>$data->tasa_cred,
            ":desgr"=>$data->tasa_desgr,
            ":nrocuotas"=>$data->nro_cuotas,
            ":fechaOtor"=>$data->fecha_otorga,
            ":pivot"=>$data->pivot
          ];
          $qry = $db->query_all($sql,$params);
          $rs = reset($qry);

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
