<?php
  include_once('../../../includes/sess_verifica.php');

    if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      switch ($data->TipoQuery) {
        case "DownloadFacturas":
          $tabla[] = array(
            array("text" => "tipoDoc"),
            array("text" => "nroSerieDoc"),
            array("text" => "nroCorrDoc"),
            array("text" => "tipoMoneda"),
            array("text" => "tipoOperNoGrav"),
            array("text" => "tipoDNI"),
            array("text" => "nroDNI"),
            array("text" => "socio"),
            array("text" => "interes"),
            array("text" => "moratorio"),
            array("text" => "seguros"),
            array("text" => "otros"),
            array("text" => "tot inafecta"),
            array("text" => "tot exonerada"),
            array("text" => "total"),
            array("text" => "tipoModi"), //tipo de documento que se modifica
            array("text" => "nroSerieModi"), //serie del documento que se modifica
            array("text" => "nroCorrModi"), //correlativo del documento que se modifica
            array("text" => "fec_otorg"), //fecha de otorgamiento YYYY-MM-DD
            array("text" => "capital"),
            array("text" => "nroContrato"),
            array("text" => "nroPoliza"),
            array("text" => "fecIniVigencia"), //fecha de inicio de vigencia de cobertura (YYYY-MM-DD)
            array("text" => "fecFinVigencia"), //fecha de termino de vigencia de cobertura (YYYY-MM-DD)
            array("text" => "tipoSeguro"),
            array("text" => "sumaAsegurada")
          );

          $sql = "";
          $sql .= "select distinct '13' as tipoDoc,'F001' as nroSerieDoc,'PEN' as tipoMoneda,case when s.dni<>'0' and s.ruc='0' and carnet='0' then '1' when s.dni='0' and  s.ruc<>'0' and carnet='0' then '6' when s.dni='0' and  s.ruc='0' and carnet<> '0' then '4' ELSE 'ERROR' END as tipoDNI,case when s.dni<>'0' and s.ruc='0' and carnet='0' then s.dni when s.dni='0' and  s.ruc<>'0' and carnet='0' then s.ruc when s.dni='0' and s.ruc='0' and carnet<> '0' then s.carnet ELSE 'ERROR' END as nroDNI,case when s.dni<>'0' and s.ruc='0' and carnet='0' then s.ap_pater+' '+s.ap_mater+', '+s.nombres when s.dni='0' and s.ruc<>'0' and carnet='0' then s.raz_social when s.dni='0' and s.ruc='0' and carnet<>'0' then s.ap_pater+' '+s.ap_mater+', '+s.nombres ELSE 'ERROR' END as socio,convert(varchar,p.fec_otorg,102) as fec_otorg,p.importe as  capital,p.codagenc+'-'+p.codsocio+'.'+p.tipo_serv+'.'+p.num_pres as nroContrato,m.num_trans,m.codagenc,m.codsocio,m.tipo_serv,m.num_pres,convert(varchar,m.fecha,20) as fecha ";
          $sql .= "from coopsud.dbo.COOP_DB_movimientos m,coopsud.dbo.COOP_DB_socios_gen s,coopsud.dbo.COOP_DB_prestamos p ";
          $sql .= "where s.codagenc=m.codagenc and s.codsocio=m.codsocio and s.codagenc=p.codagenc and s.codsocio=p.codsocio and m.codagenc=p.codagenc and m.codsocio=p.codsocio and m.tipo_serv=p.tipo_serv and m.num_pres=p.num_pres and m.tipo_oper='04' and m.tipo_mov<>'19' and (m.fecha between '".$data->fechaIni." 00:00:00' and '".$data->fechaFin." 23:58:58') order by fecha";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $socio = ($rs["socio"]);
              $capital = 0;
              $interes = 0;
              $moratorio = 0;
              $seg_desgr = 0;
              $otros = 0;
              $tipoOperNoGrav = "";

              //tipo de prestamos en coop_db_sol_prestamos
              $qa = $db->select("select tipo_cred from coopsud.dbo.COOP_DB_sol_prestamos where codagencia='".$rs["codagenc"]."' and codsocio='".$rs["codsocio"]."' and tipo_serv='".$rs["tipo_serv"]."' and num_prest='".$rs["num_pres"]."'");
              switch($db->num_rows($qa)){
                case 0: $tipoOperNoGrav = "ERROR: no existe solicitud"; break;
                case 1:
                  $ra = $db->fetch_array($qa);
                  switch($ra["tipo_cred"]){
                    case "1": $tipoOperNoGrav = "2102"; break;
                    case "3":
                    case "6":
                    case "7": $tipoOperNoGrav = "2100"; break;
                    default : $tipoOperNoGrav = "ERROR: Tipo no valido"; break;
                  }
                  break;
                default: $tipoOperNoGrav = "ERROR: existe mas de 1 solicitud"; break;
              }

              //movimientos
              $qx = $db->select("select * from coopsud.dbo.COOP_DB_movimientos where codagenc='".$rs["codagenc"]."' and codsocio='".$rs["codsocio"]."' and tipo_serv='".$rs["tipo_serv"]."' and num_pres='".$rs["num_pres"]."' and num_trans='".$rs["num_trans"]."' and fecha='".$rs["fecha"]."'");
              if ($db->has_rows($qx)) {
                for($aa = 0; $aa<$db->num_rows($qx); $aa++){
                  $rx = $db->fetch_array($qx);
                  switch($rx["tipo_mov"]){
                    case "14": $capital = $rx["importe"]; break;
                    case "15": $interes = $rx["importe"]; break;
                    case "17": $moratorio = $rx["importe"]; break;
                    case "35": $seg_desgr = $rx["importe"]; break;
                    case "05": //gastos admin.
                    case "64": //seg. vehic
                    case "65": //guardiania vehic
                    case "66": $otros += $rx["importe"]; break; //GPS vehic
                  }
                }
              }
              $totInafec = $interes + $moratorio + $seg_desgr + $otros;
              $totExoner = 0;

              if($totInafec>0){
                $tabla[] = array(
                  array("text" => ($rs["tipoDoc"])),
                  array("text" => ($rs["nroSerieDoc"])),
                  array("text" => (0)),
                  array("text" => ($rs["tipoMoneda"])),
                  array("text" => ($tipoOperNoGrav)),
                  array("text" => ($rs["tipoDNI"])),
                  array("text" => ($rs["nroDNI"])),
                  array("text" => str_replace('Ñ','N',$socio)),
                  array("text" => ($interes * 1)),
                  array("text" => ($moratorio * 1)),
                  array("text" => ($seg_desgr * 1)),
                  array("text" => ($otros * 1)),
                  array("text" => ($totInafec * 1)),
                  array("text" => ($totExoner * 1)),
                  array("text" => ($totInafec+$totExoner)),
                  array("text" => ""),
                  array("text" => ""),
                  array("text" => ""),
                  array("text" => str_replace(".","-",$rs["fec_otorg"])),
                  array("text" => ($rs["capital"] * 1)),
                  array("text" => ($rs["nroContrato"])),
                  array("text" => ""),
                  array("text" => ""),
                  array("text" => ""),
                  array("text" => ""),
                  array("text" => 0)
                );
              }
            }
          }
          //respuesta
          $options = array("fileName"=>"20601390419-BN-".date("Ymd")."-1");
          $tableData[] = array("sheetName"=>"facturacion","data"=>$tabla);
          $rpta = array("options"=>$options,"tableData"=>$tableData);
          echo json_encode($rpta);
          break;
        case "comboAgencias":
          $tabla = array();
          $qry = $db->select("select * from COOP_DB_agencia order by agencia");
          if ($db->has_rows($qry)) {
            for($xx=0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => ($rs["agencia"]),
                "nombre" => ($rs["detalle"])
              );
            }
          }
          echo json_encode($tabla);
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
