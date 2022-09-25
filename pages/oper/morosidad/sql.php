<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');
      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      switch ($data->TipoQuery) {
        //****************morosidad****************
        case "controlCierreMorosidad":
          $qry = $db->select("select CONVERT(char(8),DATEADD(ms,-3,DATEADD(mm,0,DATEADD(mm,DATEDIFF(mm,0,GETDATE())+1,0))),112) as ultimodia,CONVERT(char(8),GETDATE(),112) as actualdia,count(*) as cuenta from tb_cartera where id_tipo_oper=403 and yyyy=YEAR(GETDATE()) and mes=MONTH(GETDATE())");
          $rs = $db->fetch_array($qry);
          if($rs["ultimodia"]==$rs["actualdia"]){ $dato = 1; } else { $dato = 0; }
          $cuenta = $rs["cuenta"];

          $rpta = array("ultimodia" => $dato, "cuenta" => $cuenta);
          echo json_encode($rpta);

          break;
        case "rptMorosidad":
          $tabla = array();

          //verificamos alerta de morosidad
          $rsAlertaMora = $db->fetch_array($db->select("select count(*) as cuenta from CoopSUD.dbo.COOP_DB_Historial where num_pres='07744';"));

          $whr = (($data->agenciaID)>0)?("and c.id_agencia=".$data->agenciaID):("");
          $sql = "select c.id_agencia,c.agencia,c.respons2,c.id_analista,c.analista,c.cargo,c.estado,count(*) as hoy,sum(c.saldo) as saldo from xx_Morosidad_Cab m, CoopSUD.dbo.COOP_DB_saldos s right join xx_CarteraPrest c on(c.codsocio=s.codagenc+'-'+s.codsocio and s.tipo_oper='07') where c.agencia=m.agencia and c.respons2=m.respons2 and c.codsocio=m.codsocio and c.tipo_serv=m.tipo_serv and c.num_pres=m.num_pres and c.atraso<0 ".$whr." group by c.id_agencia,c.agencia,c.respons2,c.id_analista,c.analista,c.cargo,c.estado";
          /*$sql = "select c.id_agencia,c.agencia,c.respons2,c.id_analista,c.analista,c.cargo,count(*) as hoy,sum(m.capital) as capital,sum(m.interes)as interes,sum(moratorio) as moratorio,sum(seg_desgr) as seg_desgr,sum(gasadmin) as gasadmin from xx_CarteraPrest c,xx_Morosidad_Cab m where c.agencia=m.agencia and c.respons2=m.respons2 and c.codsocio=m.codsocio and c.tipo_serv=m.tipo_serv and c.num_pres=m.num_pres and c.atraso<0 ".$whr." group by c.id_agencia,c.agencia,c.respons2,c.id_analista,c.analista,c.cargo";*/
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);

              //verificamos data anterior
              $cantIni = 0;
              $saldIni = 0;
              $qryIni = $db->select("select cantidad,saldo from dbo.tb_cartera where id_agencia=".$rs["id_agencia"]." and id_responsable=".$rs["id_analista"]." and id_tipo_oper=403 and mes=MONTH(DATEADD(mm,-1,GETDATE())) and yyyy=year(DATEADD(mm,-1,GETDATE()))");
              if($db->has_rows($qryIni)) {
                $rsIni = $db->fetch_array($qryIni);
                $cantIni = $rsIni["cantidad"];
                $saldIni = $rsIni["saldo"];
              }

              $tabla[] = array(
                "agencia" => $rs["agencia"],
                "agenciaID" => $rs["id_agencia"],
                "codigo" => $rs["respons2"],
                "id_analista" => $rs["id_analista"],
                "worker" => ($rs["analista"]),
                "cargo" => ($rs["cargo"]),
                "estado" => $rs["estado"],
                "ini" => $cantIni,
                "hoy" => $rs["hoy"],
                "saldo_ini" => $saldIni,
                "saldo_hoy" => $rs["saldo"],
                "alertaMora" => ($rsAlertaMora["cuenta"]-2)
              );
            }

            //los datos no registrado en la primera consulta
            $xqry = $db->select("select a.nombre,c.id_agencia,w.codigo,c.id_responsable,w.nombrecorto,w.cargo,c.cantidad,c.saldo,w.estado from tb_cartera c,vw_workers w,tb_agencias a where a.ID=c.id_agencia and c.id_responsable=w.ID and c.mes=(MONTH(DATEADD(mm,-1,GETDATE()))) and c.yyyy=year(DATEADD(mm,-1,GETDATE())) and c.id_tipo_oper=403 and c.id_agencia=".$data->agenciaID." and c.id_responsable not in(select distinct c.id_analista from xx_CarteraPrest c where c.atraso<0 and id_agencia=".$data->agenciaID.")");
            if ($db->has_rows($xqry)) {
              for($xx = 0; $xx<$db->num_rows($xqry); $xx++){
                $rs = $db->fetch_array($xqry);
                $tabla[] = array(
                  "agencia" => $rs["nombre"],
                  "agenciaID" => $rs["id_agencia"],
                  "codigo" => $rs["codigo"],
                  "id_analista" => $rs["id_responsable"],
                  "worker" => ($rs["nombrecorto"]),
                  "cargo" => ($rs["cargo"]),
                  "estado" => $rs["estado"],
                  "ini" => $rs["cantidad"],
                  "hoy" => 0,
                  "saldo_ini" => $rs["saldo"],
                  "saldo_hoy" => 0,
                  "alertaMora" => 0
                );
              }
            }
          }

          $rpta = array("tabla"=>$tabla);
          echo json_encode($rpta);
          break;
        case "rptMorosidadAnalista":
          $whr = "";
          $tabla = array();
          if(($data->agenciaID) > 0) { //agencia
            $whr = "and id_agencia=".($data->agenciaID);
            if(($data->analistaID)>0) { $whr = " and id_analista=".$data->analistaID;}
            $rs = $db->fetch_array($db->select("select codigo,abrev from dbo.tb_agencias where ID=".$data->agenciaID));
            $agencia["codigo"] = ($rs["codigo"]);
            $agencia["abrev"] = ($rs["abrev"]);
          }

          $sql = "select c.codsocio,c.socio,c.telefono,c.direccion,c.fec_otorg,c.num_cuot as cuotas,c.num_pres,c.servicio,c.atraso,m.capital,m.interes,m.moratorio,m.seg_desgr,m.gasadmin,isnull(sum(s.saldo),0) as gasjudi,c.respons2,c.id_analista,c.analista,c.importe,c.saldo from xx_Morosidad_Cab m, CoopSUD.dbo.COOP_DB_saldos s right join xx_CarteraPrest c on(c.codsocio=s.codagenc+'-'+s.codsocio and s.tipo_oper='07') where c.agencia=m.agencia and c.respons2=m.respons2 and c.codsocio=m.codsocio and c.tipo_serv=m.tipo_serv and c.num_pres=m.num_pres and c.atraso<0 ".$whr." group by c.codsocio,c.socio,c.telefono,c.direccion,c.fec_otorg,c.num_cuot,c.num_pres,c.servicio,c.atraso,m.capital,m.interes,m.moratorio,m.seg_desgr,m.gasadmin,c.respons2,c.id_analista,c.analista,c.importe,c.saldo order by c.respons2,c.socio";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);

              $tabla[] = array(
                "codsocio" => ($rs["codsocio"]),
                "dias_mora" => ($rs["atraso"])*(-1),
                "socio" => ($rs["socio"]),
                "telefono" => ($rs["telefono"]),
                "direccion" => ($rs["direccion"]),
                "num_pres" => ($rs["num_pres"]),
                "importe" => ($rs["importe"]*1),
                "saldo" => ($rs["saldo"]*1),
                "capital" => ($rs["capital"]*1),
                "interes" => ($rs["interes"]*1),
                "moratorio" => ($rs["moratorio"]*1),
                "seg_desgr" => ($rs["seg_desgr"]*1),
                "gas_admin" => ($rs["gasadmin"]*1),
                "gas_judi" => ($rs["gasjudi"]*1),
                "total" => ($rs["capital"]+$rs["interes"]+$rs["moratorio"]+$rs["seg_desgr"]+$rs["gasadmin"]+$rs["gasjudi"])
              );
            }
          }
          //respuesta
          $rpta = $tabla;
          echo json_encode($rpta);
          break;
        case "rptMorosidadDatosUsuario":
          //usuario
          $usuario = "";
          $codigo = "";
          $qry = $db->select("select * from dbo.vw_workers where ID=".$data->analistaID);
          if ($db->has_rows($qry)) {
            $rs = $db->fetch_array($qry);

            $usuario = array(
              "nombrecorto" => ($rs["nombrecorto"]),
              "codigo" => ($codigo = $rs["codigo"]),
              "agenciaID" => ($rs["id_agencia"]),
              "agencia" => ($rs["agencia"])
            );
          }

          //lista de morosos
          $morosos = array();
          $sql = "select concat(so.codagenc, '-', so.codsocio) as codsocio,substring((rtrim(so.ap_pater)+' '+rtrim(so.ap_mater)+', '+rtrim(so.nombres)+rtrim(so.raz_social)),1,45) AS socio,case when so.tel_movil <> '' and so.tel_fijo <> '' then CONCAT(so.tel_movil,' - ',so.tel_fijo) when so.tel_movil <> '' and so.tel_fijo = '' then so.tel_movil when so.tel_movil = '' and so.tel_fijo <> '' then so.tel_fijo ELSE 'xxxx' END as telefono, ts.detalle as servicio, dp.fec_vencim as fecha_vencimiento, dp.numero as cuota_x_vencer, ph.num_cuot as t_cuotas, ((amortizacion - pago_amor) + (int_comp - pago_int_c)+ dp.seg_desgr) as total, concat( 'https://api.whatsapp.com/send?phone=51',so.tel_movil,'&text=','Hola, Grupo Inversión Sudamericano te recuerda pagar puntualmente tu crédito hasta el ', convert(varchar,dp.fec_vencim, 103), ' el monto de tu cuota por vencer es de S/ ', ((amortizacion - pago_amor) + (int_comp - pago_int_c)+ dp.seg_desgr), ', te esperamos en todas nuestras agencia o canales autorizados.') as mensaje ";
          $sql .= "from coopsud.dbo.COOP_DB_prestamos_det dp inner join coopsud.dbo.COOP_DB_prestamos ph ON dp.codsocio = ph.codsocio and dp.codagenc = ph.codagenc and dp.num_pres = ph.num_pres inner join coopsud.dbo.COOP_DB_socios_gen so ON dp.codagenc=so.codagenc and dp.codsocio=so.codsocio INNER JOIN coopsud.dbo.COOP_DB_tipo_serv ts ON ph.tipo_serv=ts.tipo_serv left join (select concat(c.departamento, c.provincia, c.distrito) as cod, a.detalle as dep, b.detalle as prov, c.detalle as dis from coopsud.dbo.COOP_DB_departamento a inner join coopsud.dbo.COOP_DB_provincia b on a.departamento = b.departamento inner join coopsud.dbo.COOP_DB_distrito c ON c.departamento = a.departamento and c.provincia = b.provincia) ub ON so.ubigeo = ub.cod ";
          $sql .= "where amortizacion - pago_amor <> 0 and ph.respons2='".$codigo."' and datediff(d,fec_vencim, format(getdate(),'yyyyMMdd')) between (7 * -1) and -1 order by dp.fec_vencim";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx < $db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $morosos[] = array(
                "codsocio" => ($rs["codsocio"]),
                "socio" => ($rs["socio"]),
                "telefono" => ($rs["telefono"]),
                "servicio" => ($rs["servicio"]),
                "cuota_vence" => ($rs["cuota_x_vencer"]),
                "total_cuotas" => ($rs["t_cuotas"]),
                "total" => ($rs["total"]),
                "link" => ($rs["mensaje"])
              );
            }
          }

          //respuesta
          $rpta = array("usuario"=>$usuario,"morosos"=>$morosos);
          echo json_encode($rpta);
          break;
        case "rptMorosidadSociosDownload":
          $whr = "";
          $agencia = array("codigo"=>"00", "abrev"=>"all");
          $tabla[] = array(
            array("text" => "codsocio"),
            array("text" => "dias mora"),
            array("text" => "calificacion"),
            array("text" => "socio"),
            array("text" => "DNI"),
            array("text" => "telefono"),
            array("text" => "direccion casa"),
            array("text" => "distrito casa"),
            array("text" => "direccion laboral"),
            array("text" => "distrito laboral"),
            array("text" => "fec_otorg"),
            array("text" => "cuotas"),
            array("text" => "num_pres"),
            array("text" => "servicio"),
            array("text" => "nro cuota"),
            array("text" => "cuota fecha pago"),
            array("text" => "fecha ulti_pago"),
            array("text" => "cuota normal"),
            array("text" => "importe"),
            array("text" => "saldo"),
            array("text" => "capital"),
            array("text" => "interes"),
            array("text" => "moratorio"),
            array("text" => "seg_desgr"),
            array("text" => "gas_admin"),
            array("text" => "gas_judi"),
            array("text" => "total"),
            array("text" => "analista")
          );

          if(($data->agenciaID) > 0) {
            $whr = "and id_agencia=".($data->agenciaID);
            if(($data->analistaID)>0) { $whr = " and id_analista=".$data->analistaID;}
            //agencia
            $rs = $db->fetch_array($db->select("select codigo,abrev from dbo.tb_agencias where ID=".$data->agenciaID));
            $agencia["codigo"] = ($rs["codigo"]);
            $agencia["abrev"] = ($rs["abrev"]);
          }

          $sql =  "select c.codsocio,c.socio,c.doc,c.nrodoc,c.telefono,c.direccion as direccion1,coopSUD.dbo.fc_ubigeo(c.ubigeo,'D') as distrito1,replace(convert(NVARCHAR, c.fec_otorg, 103), ' ', '/') as fec_otorg,c.num_cuot as cuotas,c.num_pres,c.servicio,c.atraso,m.capital,m.interes,m.moratorio,m.seg_desgr,m.gasadmin,isnull(sum(s.saldo),0) as gasjudi,c.respons2,c.id_analista,c.analista,c.importe,c.saldo,c.nrocuota,c.importecuota,replace(convert(NVARCHAR, c.fec_vencim, 103), ' ', '/') as fec_vencim,replace(convert(NVARCHAR, c.fec_pago, 103), ' ', '/') as fec_pago ";
          $sql .= "from xx_Morosidad_Cab m, CoopSUD.dbo.COOP_DB_saldos s right join xx_CarteraPrest c on(c.codsocio=s.codagenc+'-'+s.codsocio and s.tipo_oper='07') ";
          $sql .= "where c.agencia=m.agencia and c.respons2=m.respons2 and c.codsocio=m.codsocio and c.tipo_serv=m.tipo_serv and c.num_pres=m.num_pres and c.atraso<0 ".$whr." ";
          $sql .= "group by c.codsocio,c.socio,c.doc,c.nrodoc,c.telefono,c.direccion,coopSUD.dbo.fc_ubigeo(c.ubigeo,'D'),c.fec_otorg,c.num_cuot,c.num_pres,c.servicio,c.atraso,m.capital,m.interes,m.moratorio,m.seg_desgr,m.gasadmin,c.respons2,c.id_analista,c.analista,c.importe,c.saldo,c.nrocuota,c.importecuota,c.fec_vencim,c.fec_pago ";
          $sql .= "order by c.respons2,c.socio";

          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $miAtraso = ($rs["atraso"])*(-1);
              $miCalifi = ($miAtraso<=8) ? ("normal") : (($miAtraso<=30)?("CPP"):(($miAtraso<=60)?("Deficiente"):(($miAtraso<=120)?("Dudoso"):("Perdida"))));

              $tabla[] = array(
                array("text" => $rs["codsocio"]),
                array("text" => $miAtraso),
                array("text" => $miCalifi),
                array("text" => $rs["socio"]),
                array("text" => $rs["nrodoc"]),
                array("text" => ($rs["telefono"])),
                array("text" => ($rs["direccion1"])),
                array("text" => ($rs["distrito1"])),
                array("text" => ("")),
                array("text" => ("")),
                array("text" => $rs["fec_otorg"]),
                array("text" => $rs["cuotas"]),
                array("text" => $rs["num_pres"]),
                array("text" => ($rs["servicio"])),
                array("text" => ($rs["nrocuota"])),
                array("text" => $rs["fec_vencim"]),
                array("text" => $rs["fec_pago"]),
                array("text" => $rs["importecuota"]*1),
                array("text" => $rs["importe"]*1),
                array("text" => $rs["saldo"]*1),
                array("text" => $rs["capital"]*1),
                array("text" => $rs["interes"]*1),
                array("text" => $rs["moratorio"]*1),
                array("text" => $rs["seg_desgr"]*1),
                array("text" => $rs["gasadmin"]*1),
                array("text" => $rs["gasjudi"]*1),
                array("text" => ($rs["capital"]+$rs["interes"]+$rs["moratorio"]+$rs["seg_desgr"]+$rs["gasadmin"]+$rs["gasjudi"])),
                array("text" => ($rs["analista"]))
              );
            }
          }
          //respuesta
          $options = array("fileName"=>"morosidad_".$agencia["codigo"]."_".$agencia["abrev"]);
          $tableData[] = array("sheetName"=>"morosidad","data"=>$tabla);
          $rpta = array("options"=>$options,"tableData"=>$tableData);
          echo json_encode($rpta);
          break;
        case "rptPreventivoSociosDownload":
          $whr = "";
          $agencia = array("codigo"=>"00", "abrev"=>"all");
          $tabla[] = array(
            array("text" => "codsocio"),
            array("text" => "dias mora"),
            array("text" => "calificacion"),
            array("text" => "socio"),
            array("text" => "DNI"),
            array("text" => "telefono"),
            array("text" => "direccion"),
            array("text" => "distrito"),
            array("text" => "fec_vencim"),
            array("text" => "cuotas"),
            array("text" => "num_pres"),
            array("text" => "servicio"),
            array("text" => "importe"),
            array("text" => "saldo"),
            array("text" => "capital"),
            array("text" => "interes"),
            array("text" => "moratorio"),
            array("text" => "seg_desgr"),
            array("text" => "gas_admin"),
            array("text" => "gas_judi"),
            array("text" => "total"),
            array("text" => "analista")
          );

          if(($data->agenciaID) > 0) {
            $whr = "and c.id_agencia=".($data->agenciaID);
            if(($data->analistaID)>0) { $whr = " and c.id_analista=".$data->analistaID;}
            //agencia
            $rs = $db->fetch_array($db->select("select codigo,abrev from dbo.tb_agencias where ID=".$data->agenciaID));
            $agencia["codigo"] = ($rs["codigo"]);
            $agencia["abrev"] = ($rs["abrev"]);
          }
          $sql = "select c.*,REPLACE(CONVERT(NVARCHAR, c.fec_vencim, 103), ' ', '/') AS fecha_vencim,p.capital,p.interes,p.seg_desgr from xx_CarteraPrest c,xx_Preventivo_Det p where c.codsocio=p.codsocio and c.tipo_serv=p.tipo_serv and c.num_pres=p.num_pres and c.nrocuota=p.numero and c.atraso>0 ".$whr." order by c.respons2,c.socio";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);

              if(strlen($rs["socio"])<=4){$miSocio = $rs["raz_social"]; $miDNI = $rs["ruc"];} else { $miSocio = $rs["socio"]; $miDNI = $rs["dni"];}
              $miAtraso = ($rs["atraso"])*(-1);
              $miCalifi = ($miAtraso<=1) ? ("normal") : (($miAtraso<=30)?("CPP"):(($miAtraso<=60)?("Deficiente"):(($miAtraso<=120)?("Dudoso"):("Perdida"))));

              $tabla[] = array(
                array("text" => $rs["codsocio"]),
                array("text" => $miAtraso),
                array("text" => $miCalifi),
                array("text" => ($miSocio)),
                array("text" => ($miDNI)),
                array("text" => ($rs["telefono"])),
                array("text" => ($rs["direccion"])),
                array("text" => ($rs["distrito"])),
                array("text" => $rs["fecha_vencim"]),
                array("text" => $rs["num_cuot"]),
                array("text" => $rs["num_pres"]),
                array("text" => ($rs["servicio"])),
                array("text" => $rs["importe"]*1),
                array("text" => $rs["saldo"]*1),
                array("text" => $rs["capital"]*1),
                array("text" => $rs["interes"]*1),
                array("text" => 0),
                array("text" => $rs["seg_desgr"]*1),
                array("text" => 0),
                array("text" => 0),
                array("text" => ($rs["capital"]+$rs["interes"]+$rs["seg_desgr"])),
                array("text" => ($rs["analista"]))
              );
            }
          }
          //respuesta
          $options = array("fileName"=>"preventivo_".$agencia["codigo"]."_".$agencia["abrev"]);
          $tableData[] = array("sheetName"=>"preventivo","data"=>$tabla);
          $rpta = array("options"=>$options,"tableData"=>$tableData);
          echo json_encode($rpta);
          break;
        case "cierreMorosidad": //403
          $rpta = array();
          $params = array();

          //el maximo ID de tb_cartera
          $rsMax = $db->fetch_array($db->select("select isnull(max(ID),0) as maxi from dbo.tb_cartera;"));
          $maxi = $rsMax["maxi"];

          //consulta para ingresar los cierres
          $sql ="insert into dbo.tb_cartera select (".$maxi.") + ROW_NUMBER()over(order by id_agencia,id_analista) as nro, getdate() as fecha,year(getdate()) as yyyy,month(getdate()) as mes, c.id_agencia,c.id_analista,count(*) as hoy,sum(c.saldo) as saldo_hoy,1 as id_tipo_mone,403 as id_tipo_oper,'".get_client_ip()."' as sys_IP,".$_SESSION['usr_agenciaID']." as sys_agencia,".$_SESSION['usr_ID']." as sys_user,getdate() as sys_fecha,dbo.fn_GetTime() as sys_hora from dbo.xx_Morosidad_Cab m, CoopSUD.dbo.COOP_DB_saldos s right join dbo.xx_CarteraPrest c on(c.codsocio=s.codagenc+'-'+s.codsocio and s.tipo_oper='07') where c.agencia=m.agencia and c.respons2=m.respons2 and c.codsocio=m.codsocio and c.tipo_serv=m.tipo_serv and c.num_pres=m.num_pres and c.atraso<0 group by c.id_agencia,c.id_analista";
          $qry = $db->insert($sql, $params);

          //resultado
          $rpta = array(
            "error" => 0,
            "mensaje" => "se ejecuto el cierre de la morosidad con exito"
          );
          echo json_encode($rpta);
          break;
        case "graficoMorosidad":
          $analista = array();
          $qry = $db->select("select * from dbo.vw_workers where ID=".$data->analistaID);
          if ($db->has_rows($qry)) {
            $rs = $db->fetch_array($qry);
            $analista[0] = array(
              "worker" => $rs["worker"],
              "nombrecorto" => $rs["nombrecorto"],
              "agencia" => $rs["agencia"],
              "cargo" => $rs["cargo"]
            );
          }

          $cartera = array();
          $qry = $db->select("select * from dbo.fn_HistoCrediMorosidad(".$data->agenciaID.",".$data->analistaID.")");
          for($xx = 0; $xx < $db->num_rows($qry); $xx++){
            $rs = $db->fetch_array($qry);
            $cartera[] = array(
              "meses" => $rs["smes"],
              "moraSaldo" => $rs["monto"],
              "moraCantidad" => $rs["cantidad"]
            );
          }
          $rpta = array("analista"=>$analista,"grafiMorosidad" => $cartera);
          echo json_encode($rpta);
          break;
      }
      $db->close();
    } else{
      $resp = array("error"=>true,"mensaje"=>"ninguna variable en POST");
      echo json_encode($resp);
    }
  } else {
    $resp = array("error"=>true,"mensaje"=>"Caducó la sesion.");
    echo json_encode($resp);
  }
?>
