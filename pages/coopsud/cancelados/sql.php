<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');

      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      switch ($data->TipoQuery) {
        //****************cancelados****************
        case "inicio":
          //verificar usuario
          $qryusr = $db->select("select id_usernivel from dbo.tb_usuarios where id_persona=".$_SESSION['usr_ID'].";");
          $rsusr = $db->fetch_array($qryusr);

          //respuesta
          $rpta = array("usernivel"=>$rsusr["id_usernivel"],"admin"=>701,"jefe"=>705);
          echo json_encode($rpta);
          break;
        case "coopSUDcancelados":
          //verificar usuario
          $qryusr = $db->select("select u.id_usernivel,a.codigo from dbo.tb_usuarios u, dbo.tb_workers w,dbo.tb_agencias a where u.id_persona=w.id_persona and a.ID=w.id_agencia and u.id_persona=".$_SESSION['usr_ID'].";");
          $rsusr = $db->fetch_array($qryusr);

          //cargar datos de cancelados para coopSUD
          $whr = ($rsusr["id_usernivel"]<711)?(""):("and m.agencia='".$rsusr["codigo"]."' ");
          $tabla = array();
          $sql = "select m.agencia,s.codagenc+'-'+s.codsocio as codsocio,case when s.dni<>'0' and s.ruc='0' and carnet='0' then s.ap_pater+' '+s.ap_mater+', '+s.nombres when s.dni='0' and s.ruc<>'0' and carnet='0' then s.raz_social when s.dni='0' and s.ruc='0' and carnet<>'0' then s.ap_pater+' '+s.ap_mater+', '+s.nombres ELSE 'ERROR' END as socio,case when s.dni<>'0' and s.ruc='0' and carnet='0' then 'DNI' when s.dni='0' and  s.ruc<>'0' and carnet='0' then 'RUC' when s.dni='0' and  s.ruc='0' and carnet<> '0' then 'CARNET' ELSE 'ERROR' END as doc,case when s.dni<>'0' and s.ruc='0' and carnet='0' then s.dni when s.dni='0' and  s.ruc<>'0' and carnet='0' then s.ruc when s.dni='0' and s.ruc='0' and carnet<> '0' then s.carnet ELSE 'ERROR' END as nrodoc,s.tel_movil+' - '+s.tel_fijo as celular,w.nombrecorto as analista,p.codagenc+'-'+p.codsocio+'.'+p.tipo_serv+'.'+p.num_pres as 'cuenta',p.importe,d.numero as 'd_numero',p.num_cuot as 'p_num_cuot',d.fec_pago as 'd_fec_pago' ";
          $sql .= "from coopSUD.dbo.coop_db_prestamos p, coopSUD.dbo.COOP_DB_prestamos_det d, coopSUD.dbo.COOP_DB_socios_gen s,coopSUD.dbo.COOP_DB_movimientos m,financiero.dbo.tb_workers w ";
          $sql .= "where s.codagenc=p.codagenc and s.codsocio=p.codsocio and p.codagenc=d.codagenc and p.codsocio=d.codsocio and p.codagenc=m.codagenc and p.codsocio=m.codsocio and p.num_pres=d.num_pres and p.num_pres=m.num_pres and m.tipo_mov='19' and w.codigo=p.respons2 and p.saldo=0 and d.numero=(select max(numero) from coopSUD.dbo.coop_db_prestamos_det where codagenc=d.codagenc and codsocio=d.codsocio and num_pres=d.num_pres) and d.fec_pago>='".$data->fechaIni." 00:00:00' and d.fec_pago<='".$data->fechaFin." 23:58:59' ".$whr;
          $sql .= "order by year(d.fec_pago),month(d.fec_pago),day(d.fec_pago),p.codagenc,p.codsocio,p.tipo_serv,p.num_pres";

          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "agencia"=> ($rs["agencia"]),
                "codigo" => ($rs["codsocio"]),
                "socio"=> ($rs["socio"]),
                "doc"=> ($rs["doc"]),
                "nrodoc"=> ($rs["nrodoc"]),
                "celular"=> ($rs["celular"]),
                "analista"=> ($rs["analista"]),
                "nrocuenta" => ($rs["cuenta"]),
                "importe" => ($rs["importe"]),
                "d_num_cuot" => ($rs["d_numero"]),
                "p_num_cuot" => ($rs["p_num_cuot"]),
                "fecha_pago" => ($rs["d_fec_pago"])
              );
            }
          }

          //respuesta
          $rpta = array("tabla"=>$tabla,"sql"=>$sql);
          echo json_encode($rpta);
          break;
        case "coopSUDcanceladosDownload":
          //verificar usuario
          $qryusr = $db->select("select u.id_usernivel,a.codigo from dbo.tb_usuarios u, dbo.tb_workers w,dbo.tb_agencias a where u.id_persona=w.id_persona and a.ID=w.id_agencia and u.id_persona=".$_SESSION['usr_ID'].";");
          $rsusr = $db->fetch_array($qryusr);

          //cargar datos de cancelados para coopSUD
          $whr = ($rsusr["id_usernivel"]<711)?(""):("and m.agencia='".$rsusr["codigo"]."' ");
          $tabla[] = array(
            array("text" => "agencia"),
            array("text" => "codsocio"),
            array("text" => "Socio"),
            array("text" => "DUI"),
            array("text" => "Celular"),
            array("text" => "Nº Cuenta"),
            array("text" => "Analista"),
            array("text" => "fec_Cancel"),
            array("text" => "Cuotas"),
            array("text" => "Importe")
          );

          $sql = "select m.agencia,s.codagenc+'-'+s.codsocio as codsocio,case when s.dni<>'0' and s.ruc='0' and carnet='0' then s.ap_pater+' '+s.ap_mater+', '+s.nombres when s.dni='0' and s.ruc<>'0' and carnet='0' then s.raz_social when s.dni='0' and s.ruc='0' and carnet<>'0' then s.ap_pater+' '+s.ap_mater+', '+s.nombres ELSE 'ERROR' END as socio,case when s.dni<>'0' and s.ruc='0' and carnet='0' then 'DNI' when s.dni='0' and  s.ruc<>'0' and carnet='0' then 'RUC' when s.dni='0' and  s.ruc='0' and carnet<> '0' then 'CARNET' ELSE 'ERROR' END as doc,case when s.dni<>'0' and s.ruc='0' and carnet='0' then s.dni when s.dni='0' and  s.ruc<>'0' and carnet='0' then s.ruc when s.dni='0' and s.ruc='0' and carnet<> '0' then s.carnet ELSE 'ERROR' END as nrodoc,s.tel_movil+' - '+s.tel_fijo as celular,w.nombrecorto as analista,p.codagenc+'-'+p.codsocio+'.'+p.tipo_serv+'.'+p.num_pres as 'cuenta',p.importe,d.numero as 'd_numero',p.num_cuot as 'p_num_cuot',convert(varchar,d.fec_pago,21) as 'd_fec_pago' ";
          $sql .= "from coopSUD.dbo.coop_db_prestamos p, coopSUD.dbo.COOP_DB_prestamos_det d, coopSUD.dbo.COOP_DB_socios_gen s,coopSUD.dbo.COOP_DB_movimientos m,financiero.dbo.tb_workers w ";
          $sql .= "where s.codagenc=p.codagenc and s.codsocio=p.codsocio and p.codagenc=d.codagenc and p.codsocio=d.codsocio and p.codagenc=m.codagenc and p.codsocio=m.codsocio and p.num_pres=d.num_pres and p.num_pres=m.num_pres and m.tipo_mov='19' and w.codigo=p.respons2 and p.saldo=0 and d.numero=(select max(numero) from coopSUD.dbo.coop_db_prestamos_det where codagenc=d.codagenc and codsocio=d.codsocio and num_pres=d.num_pres) and d.fec_pago>='".$data->fechaIni." 00:00:00' and d.fec_pago<='".$data->fechaFin." 23:58:59' ".$whr;
          $sql .= "order by year(d.fec_pago),month(d.fec_pago),day(d.fec_pago),p.codagenc,p.codsocio,p.tipo_serv,p.num_pres";

          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx=0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                array("text" => ($rs["agencia"])),
                array("text" => ($rs["codsocio"])),
                array("text" => ($rs["socio"])),
                array("text" => ($rs["doc"]."-".$rs["nrodoc"])),
                array("text" => ($rs["celular"])),
                array("text" => ($rs["cuenta"])),
                array("text" => ($rs["analista"])),
                array("text" => ($rs["d_fec_pago"])),
                array("text" => ($rs["p_num_cuot"])),
                array("text" => ($rs["importe"]*1))
              );
            }
          }

          //respuesta
          $options = array("fileName"=>"cancelados");
          $tableData[] = array("sheetName"=>"cancelados","data"=>$tabla);
          $rpta = array("options"=>$options,"tableData"=>$tableData);
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
