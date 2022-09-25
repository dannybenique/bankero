<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');
      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      switch ($data->TipoQuery) {
        //****************cartera****************
        case "controlCierreCartera":
          $qry = $db->select("select CONVERT(char(8),DATEADD(ms,-3,DATEADD(mm,0,DATEADD(mm,DATEDIFF(mm,0,GETDATE())+1,0))),112) as ultimodia,CONVERT(char(8),GETDATE(),112) as actualdia,count(*) as cuenta from tb_cartera where id_tipo_oper=402 and yyyy=YEAR(GETDATE()) and mes=MONTH(GETDATE())");
          $rs = $db->fetch_array($qry);
          if($rs["ultimodia"]==$rs["actualdia"]){ $dato = 1; } else { $dato = 0; }
          $cuenta = $rs["cuenta"];

          $rpta = array("ultimodia" => $dato, "cuenta" => $cuenta);
          echo json_encode($rpta);

          break;
        case "rptCartera":
          $tabla = array();
          $whr = (($data->agenciaID)>0)?("where ID=".($data->agenciaID)):("");

          $qryag = $db->select("select * from dbo.tb_agencias ".$whr." order by ID");
          if ($db->has_rows($qryag)) {
            for($aa=0; $aa<$db->num_rows($qryag); $aa++){
              $ag = $db->fetch_array($qryag);

              $qry = $db->select("select id_agencia,agencia,respons2 as codusuario,id_analista,analista,cargo,abrevia,estado,count(*) as hoy,sum(saldo) as saldo_hoy from dbo.xx_CarteraPrest where id_agencia=".$ag["ID"]." group by id_agencia,agencia,respons2,id_analista,analista,cargo,abrevia,estado order by agencia,analista");
              if ($db->has_rows($qry)) {
                for($xx=0; $xx<$db->num_rows($qry); $xx++){
                  $rs = $db->fetch_array($qry);

                  //verificamos data anterior
                  $cantIni = 0;
                  $saldIni = 0;
                  $qryIni = $db->select("select cantidad,saldo from dbo.tb_cartera where id_agencia=".$rs["id_agencia"]." and id_responsable=".$rs["id_analista"]." and id_tipo_oper=402 and mes=MONTH(DATEADD(mm,-1,GETDATE())) and yyyy=year(DATEADD(mm,-1,GETDATE()))");
                  if($db->has_rows($qryIni)) {
                    $rsIni = $db->fetch_array($qryIni);
                    $cantIni = $rsIni["cantidad"];
                    $saldIni = $rsIni["saldo"];
                  }

                  $tabla[] = array(
                    "agencia" => $rs["agencia"],
                    "id_agencia" => $rs["id_agencia"],
                    "codigo" => $rs["codusuario"],
                    "id_analista" => $rs["id_analista"],
                    "worker" => ($rs["analista"]),
                    "cargo" => ($rs["cargo"]),
                    "abrevia" => ($rs["abrevia"]),
                    "estado" => $rs["estado"],
                    "ini" => $cantIni,
                    "saldo_ini" => $saldIni,
                    "hoy" => $rs["hoy"],
                    "saldo_hoy" => $rs["saldo_hoy"]
                  );
                }
              }

              //los datos no registrado en la primera consulta
              $sql = "select c.id_agencia,a.codigo as agencia,w.codigo as codusuario,c.id_responsable as id_analista,w.nombrecorto as analista,w.cargo,w.abrevia,c.cantidad,c.saldo,w.estado from tb_cartera c,vw_workers w,tb_agencias a where a.ID=c.id_agencia and c.id_responsable=w.ID and c.mes=(MONTH(DATEADD(mm,-1,GETDATE()))) and c.yyyy=YEAR(DATEADD(mm,-1,GETDATE())) and c.id_tipo_oper=402 and c.id_agencia=".$ag["ID"]." and c.id_responsable not in(select distinct c.id_analista from xx_CarteraPrest c where id_agencia=".$ag["ID"].")";
              $qry = $db->select($sql);
              if ($db->has_rows($qry)) {
                for($xx = 0; $xx<$db->num_rows($qry); $xx++){
                  $rs = $db->fetch_array($qry);

                  $tabla[] = array(
                    "agencia" => $rs["agencia"],
                    "id_agencia" => $rs["id_agencia"],
                    "codigo" => $rs["codusuario"],
                    "id_analista" => $rs["id_analista"],
                    "worker" => ($rs["analista"]),
                    "cargo" => ($rs["cargo"]),
                    "abrevia" => ($rs["abrevia"]),
                    "estado" => $rs["estado"],
                    "ini" => $rs["cantidad"],
                    "saldo_ini" => $rs["saldo"],
                    "hoy" => 0,
                    "saldo_hoy" => 0
                  );
                }
              }
            }
          }

          //respuesta
          $rpta = array("tabla"=>$tabla);
          echo json_encode($rpta);
          break;
        case "rptCarteraAnalista":
          $tabla = array();
          if(($data->agenciaID)>0) {
            $whr = " and id_agencia=".($data->agenciaID);
            if(($data->analistaID)>0) { $whr .= " and id_analista=".$data->analistaID; }
          }
          $sql = "select agencia,codsocio,coopSUD.dbo.fc_socio(codsocio,'S') as socio,direccion,coopSUD.dbo.fc_ubigeo(ubigeo,'D') as distrito,ocupacion,telefono,atraso,num_pres,servicio,replace(convert(NVARCHAR, fec_otorg, 103), ' ', '/') as fec_otorg,replace(convert(NVARCHAR, fec_vencim, 103), ' ', '/') as fec_vencim,nrocuota,num_cuot,importe,saldo,respons2,analista ";
          $sql .= "from xx_CarteraPrest where 0=0 ".$whr." order by socio";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);

              $miAtraso = ($rs["atraso"]>0) ? (0) : (($rs["atraso"])*(-1));
              $miCalifi = ($miAtraso<=8) ? ("normal") : (($miAtraso<=30)?("CPP"):(($miAtraso<=60)?("Deficiente"):(($miAtraso<=120)?("Dudoso"):("Perdida"))));

              $tabla[] = array(
                "agencia" => ($rs["agencia"]),
                "codsocio" => ($rs["codsocio"]),
                "socio" => ($rs["socio"]),
                "numpres" => ($rs["num_pres"]),
                "servicio" => ($rs["servicio"]),
                "fecha" => ($rs["fec_otorg"]),
                "atraso" => ($miAtraso),
                "importe" => ($rs["importe"]*1),
                "saldo" => ($rs["saldo"]*1)
              );
            }
          }
          //respuesta
          $rpta = $tabla;
          echo json_encode($rpta);
          break;
        case "rptCarteraSociosDownload":
          $whr = "";
          $agencia = array("codigo"=>"00", "abrev"=>"all");
          $tabla[] = array(
            array("text" => "agencia"),
            array("text" => "codsocio"),
            array("text" => "socio"),
            array("text" => "DNI"),
            array("text" => "direccion"),
            array("text" => "distrito"),
            array("text" => "telefono"),
            array("text" => "ocupacion"),
            array("text" => "cuenta"),
            array("text" => "servicio"),
            array("text" => "fecha_otorg"),
            array("text" => "fecha_vencim"),
            array("text" => "num_vencim"),
            array("text" => "atraso"),
            array("text" => "importe"),
            array("text" => "saldo"),
            array("text" => "condicion"),
            array("text" => "estado"),
            array("text" => "calificacion"),
            array("text" => "nro_garantes"),
            array("text" => "promotor"),
            array("text" => "respons2"),
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
          $sql = "select agencia,codsocio,socio,doc,nrodoc,direccion,coopSUD.dbo.fc_ubigeo(ubigeo,'D') as distrito,ocupacion,telefono,atraso,tipo_serv,num_pres,servicio,estadoprest,case condicion when 'N' then 'Normal' when 'C' then 'reprogramado COVID' when 'R' then 'Reprogramado' when 'J' then 'Judicial' when 'P' then 'Paralelo' when 'D' then 'prejudicial' when 'O' then 'Condonado' when 'S' then 'Castigado' when 'A' then 'Ampliado' when 'F' then 'refinanciado' end as condicion,replace(convert(NVARCHAR, fec_otorg, 103), ' ', '/') as fec_otorg,replace(convert(NVARCHAR, fec_vencim, 103), ' ', '/') as fec_vencim,nrocuota,num_cuot,importe,saldo,promotor,respons2,analista ";
          $sql .= "from xx_CarteraPrest where 0=0 ".$whr." order by socio";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $rx = $db->fetch_array($db->select("select count(*) as nro_garantes from coopSUD.dbo.COOP_DB_garantes where codagenc+'-'+codsocio='".$rs["codsocio"]."'"));

              $miAtraso = ($rs["atraso"]>0) ? (0) : (($rs["atraso"])*(-1));
              $miCalifi = ($miAtraso<=8) ? ("normal") : (($miAtraso<=30)?("CPP"):(($miAtraso<=60)?("Deficiente"):(($miAtraso<=120)?("Dudoso"):("Perdida"))));

              $tabla[] = array(
                array("text" => $rs["agencia"]),
                array("text" => $rs["codsocio"]),
                array("text" => $rs["socio"]),
                array("text" => $rs["nrodoc"]),
                array("text" => ($rs["direccion"])),
                array("text" => ($rs["distrito"])),
                array("text" => ($rs["telefono"])),
                array("text" => ($rs["ocupacion"])),
                array("text" => ($rs["codsocio"].".".$rs["tipo_serv"].".".$rs["num_pres"])),
                array("text" => ($rs["servicio"])),
                array("text" => $rs["fec_otorg"]),
                array("text" => $rs["fec_vencim"]),
                array("text" => "cuota ".$rs["nrocuota"]."/".$rs["num_cuot"]),
                array("text" => $miAtraso),
                array("text" => $rs["importe"]*1),
                array("text" => $rs["saldo"]*1),
                array("text" => $rs["condicion"]),
                array("text" => $rs["estadoprest"]),
                array("text" => $miCalifi),
                array("text" => $rx["nro_garantes"]),
                array("text" => $rs["promotor"]),
                array("text" => $rs["respons2"]),
                array("text" => ($rs["analista"]))
              );
            }
          }

          //respuesta
          $options = array("fileName"=>"cartera_".$agencia["codigo"]."_".$agencia["abrev"]);
          $tableData[] = array("sheetName"=>"cartera","data"=>$tabla);
          $rpta = array("options"=>$options,"tableData"=>$tableData);
          echo json_encode($rpta);
          break;
        case "rptCarteraEspecial":
          $tabla = array();
          $sql = "select agencia,codsocio,socio,raz_social,num_pres,servicio,replace(convert(NVARCHAR, fec_otorg, 103), ' ', '/') as fec_otorg,num_cuot,importe,saldo,atraso,numero,replace(convert(NVARCHAR, fec_vencim, 103), ' ', '/') as fec_vencim from xx_CarteraPrest order by codsocio";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);

              $miSocio  = (strlen($rs["raz_social"])<=4) ? ($rs["socio"]) : ($rs["raz_social"]);
              $codigo = explode("-",$rs["codsocio"]);
              $qdd = $db->select("select numero,replace(convert(NVARCHAR, fec_vencim, 103), ' ', '/') as fec_vencim,replace(convert(NVARCHAR, fec_pago, 103), ' ', '/') as fec_pago from coopSUD.dbo.COOP_DB_prestamos_det where codagenc='".$codigo[0]."' and codsocio='".$codigo[1]."' and num_pres='".$rs["num_pres"]."' and numero=".($rs["numero"]-1));
              $rr = $db->fetch_array($qdd);
              $qdd = $db->select("select count(numero)-1 as cuenta from coopSUD.dbo.COOP_DB_prestamos_det where codagenc='".$codigo[0]."' and codsocio='".$codigo[1]."' and num_pres='".$rs["num_pres"]."'");
              $cuota_actual = $db->fetch_array($qdd);

              $tabla[] = array(
                "agencia" => $rs["agencia"],
                "codsocio" => $rs["codsocio"],
                "socio" => ($miSocio),
                "numpres" => $rs["num_pres"],
                "servicio" => ($rs["servicio"]),
                "nro_cuotas_orig" => $rs["num_cuot"],
                "nro_cuotas_actual" => $cuota_actual["cuenta"],
                "fecha_otorg" => $rs["fec_otorg"],
                "cuot_proxima" => $rs["numero"],
                "fecha_proxima" => $rs["fec_vencim"],
                "cuot_ultimo" => $rr["numero"],
                "fecha_ultimo" => $rr["fec_vencim"],
                "pago_ultimo" => $rr["fec_pago"],
                "importe" => $rs["importe"],
                "saldo" => $rs["saldo"]
              );
            }
          }
          echo json_encode($tabla);
          break;
        case "cierreCartera": //402
          $rpta = array();
          $params = array();

          //el maximo ID de tb_cartera
          $rsMax = $db->fetch_array($db->select("select isnull(max(ID),0) as maxi from dbo.tb_cartera;"));
          $maxi = $rsMax["maxi"];

          //consulta para ingresar los cierres
          $sql = "insert into dbo.tb_cartera select (".$maxi.") + ROW_NUMBER()over(order by id_agencia,id_analista) as nro,getdate() as fecha,year(getdate()) as yyyy,month(getdate()) as mes,id_agencia,id_analista,count(*) as hoy, sum(saldo) as saldo_hoy,1 as id_tipo_mone,402 as id_tipo_oper,'".get_client_ip()."' as sys_IP,".$_SESSION['usr_agenciaID']." as sys_agencia,".$_SESSION['usr_ID']." as sys_user,getdate() as sys_fecha,dbo.fn_GetTime() as sys_hora from xx_CarteraPrest group by id_agencia,id_analista";
          $qry = $db->insert($sql, $params);

          //resultado
          $rpta = array(
            "error" => 0,
            "mensaje" => "se ejecuto el cierre de cartera con exito"
          );
          echo json_encode($rpta);
          break;
        case "graficoCartera":
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
          //$qry = $db->select("select * from dbo.fn_HistoCrediCartera(".$data->agenciaID.",".$data->analistaID.")");
          $qryCart = $db->select("select * from dbo.fn_HistoCrediCartera(".$data->agenciaID.",".$data->analistaID.")");
          $qryColc = $db->select("select * from dbo.fn_HistoCrediColocacion(".$data->agenciaID.",".$data->analistaID.")");
          for($xx = 0; $xx < $db->num_rows($qryCart); $xx++){
            $rsCart = $db->fetch_array($qryCart);
            $rsColc = $db->fetch_array($qryColc);
            $cartera[] = array(
              "meses" => $rsCart["smes"],
              "carteraSaldo" => $rsCart["monto"]*1,
              "carteraCantidad" => $rsCart["cantidad"]*1,
              "colocSaldo" => $rsColc["monto"]*1,
              "colocCantidad" => $rsColc["cantidad"]*1,
            );
          }
          $rpta = array("analista"=>$analista,"grafiCartera" => $cartera);
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
