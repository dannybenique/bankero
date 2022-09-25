<?php
  include_once('../../../includes/sess_verifica.php');

    if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      switch ($data->TipoQuery) {
        //****************congelados****************
        case "coopSUDcartera":
          $tabla = array();
          $whr = ($data->tipo=="1")?("sa.codagenc+'-'+sa.codsocio='".$data->buscar."'"):("s.dni='".$data->buscar."'");
          $sql = "select case when s.dni<>'0' and s.ruc='0' and carnet='0' then s.ap_pater+' '+s.ap_mater+', '+s.nombres when s.dni='0' and s.ruc<>'0' and carnet='0' then s.raz_social when s.dni='0' and s.ruc='0' and carnet<>'0' then s.ap_pater+' '+s.ap_mater+', '+s.nombres ELSE 'ERROR' END as socio,case when s.dni<>'0' and s.ruc='0' and carnet='0' then 'DNI' when s.dni='0' and  s.ruc<>'0' and carnet='0' then 'RUC' when s.dni='0' and  s.ruc='0' and carnet<> '0' then 'CARNET' ELSE 'ERROR' END as doc,case when s.dni<>'0' and s.ruc='0' and carnet='0' then s.dni when s.dni='0' and  s.ruc<>'0' and carnet='0' then s.ruc when s.dni='0' and s.ruc='0' and carnet<> '0' then s.carnet ELSE 'ERROR' END as nrodoc,sa.codagenc+'-'+sa.codsocio as codigo,x.detalle as servicio,x.interes_2,x.aplica_2,sa.*,ah.plazo,ah.numero as numcert,convert(nvarchar,ah.fec_inicio,20) as fec_ini,convert(nvarchar,ah.fec_fin,20) as fec_fin,ah.importe,ah.saldo as saldo2 ";
          $sql .= "from coopSUD.dbo.COOP_DB_saldos AS sa INNER JOIN coopSUD.dbo.COOP_DB_tipo_serv AS x ON sa.tipo_serv = x.tipo_serv INNER JOIN coopSUD.dbo.COOP_DB_socios_gen AS s ON sa.codagenc = s.codagenc AND sa.codsocio = s.codsocio LEFT OUTER JOIN coopSUD.dbo.COOP_DB_ahorros_plazo AS ah ON sa.tipo_oper = ah.tipo_oper AND sa.codagenc = ah.codagenc AND sa.codsocio = ah.codsocio AND sa.tipo_serv = ah.tipo_serv ";
          $sql .= "where (sa.tipo_oper IN ('01','02')) and (".$whr.") order by sa.tipo_oper,sa.saldo desc,x.aplica_2 desc";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $saldo = ($rs["aplica_2"]!="S")?($rs["saldo"]):(($rs["saldo2"]==0)?(0):($rs["saldo"]));

              $tabla[] = array(
                "codsocio" => $rs["codigo"],
                "doc" => $rs["doc"],
                "nrodoc" => $rs["nrodoc"],
                "socio" => $rs["socio"],
                "servicio" => ($rs["servicio"]),
                "tipo_serv" => $rs["tipo_serv"],
                "fec_ini" => (($rs["fec_ini"]==null)?(""):($rs["fec_ini"])),
                "fec_fin" => (($rs["fec_fin"]==null)?(""):($rs["fec_fin"])),
                "plazo" => (($rs["plazo"]==null)?(""):($rs["plazo"])),
                "numcert" => (($rs["numcert"]==null)?(""):($rs["numcert"])),
                "importe" => $rs["importe"],
                "saldo" => $saldo
              );
            }
          }
          echo json_encode($tabla);
          break;
        case "coopSUDformatos":
          $whr = ($data->num_cert!="")?(" and (ah.numero='".$data->num_cert."')"):("");
          $sql = "select  CASE WHEN s.dni <> '0' AND s.ruc = '0' AND carnet = '0' THEN s.ap_pater + ' ' + s.ap_mater + ', ' + s.nombres WHEN s.dni = '0' AND s.ruc <> '0' AND carnet = '0' THEN s.raz_social WHEN s.dni = '0' AND s.ruc = '0' AND carnet <> '0' THEN s.ap_pater + ' ' + s.ap_mater + ', ' + s.nombres ELSE 'ERROR' END AS socio, CASE WHEN s.dni <> '0' AND s.ruc = '0' AND carnet = '0' THEN 'DNI' WHEN s.dni = '0' AND s.ruc <> '0' AND carnet = '0' THEN 'RUC' WHEN s.dni = '0' AND s.ruc = '0' AND carnet <> '0' THEN 'CARNET' ELSE 'ERROR' END AS doc, CASE WHEN s.dni <> '0' AND s.ruc = '0' AND carnet = '0' THEN s.dni WHEN s.dni = '0' AND s.ruc <> '0' AND carnet = '0' THEN s.ruc WHEN s.dni = '0' AND s.ruc = '0' AND carnet <> '0' THEN s.carnet ELSE 'ERROR' END AS nrodoc, sa.codagenc + '-' + sa.codsocio AS codigo, x.detalle AS servicio, x.interes_2, x.aplica_2,sa.codagenc, sa.codsocio, sa.tipo_serv, sa.tipo_oper, sa.saldo, sa.activo, sa.cancela, sa.tempo, sa.sobregiro, sa.observac, ah.numero AS numcert, ah.fec_inicio, ah.importe, ah.tiporet, pf.codUsuario, w.nombrecorto as promotor ";
          $sql .= "from CoopSUD.dbo.COOP_DB_PFijoAnalista AS pf INNER JOIN CoopSUD.dbo.COOP_DB_ahorros_plazo AS ah ON pf.codagenc = ah.codagenc AND pf.codsocio = ah.codsocio AND pf.tipo_serv = ah.tipo_serv AND pf.numero = ah.numero INNER JOIN dbo.tb_workers AS w ON pf.codUsuario = w.codigo RIGHT OUTER JOIN CoopSUD.dbo.COOP_DB_saldos AS sa INNER JOIN CoopSUD.dbo.COOP_DB_tipo_serv AS x ON sa.tipo_serv = x.tipo_serv INNER JOIN CoopSUD.dbo.COOP_DB_socios_gen AS s ON sa.codagenc = s.codagenc AND sa.codsocio = s.codsocio ON ah.tipo_oper = sa.tipo_oper AND ah.codagenc = sa.codagenc AND ah.codsocio = sa.codsocio AND ah.tipo_serv = sa.tipo_serv ";
          $sql .= "where (sa.codagenc+'-'+sa.codsocio='".$data->codsocio."') AND (sa.tipo_serv='".$data->tiposerv."')".$whr;

          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            $rs = $db->fetch_array($qry);
            $rr = $db->fetch_array($db->select("select count(*) as cuenta from coopSUD.dbo.COOP_DB_Suplentes where codagenc+'-'+codsocio='".$data->codsocio."'"));
            $tabla = array(
              "codsocio" => $rs["codigo"],
              "doc" => $rs["doc"],
              "nrodoc" => $rs["nrodoc"],
              "socio" => $rs["socio"],
              "servicio" => ($rs["servicio"]),
              "tipo_serv" => $rs["tipo_serv"],
              "tipo_ret" => $rs["tiporet"],
              "numcert" => (($rs["numcert"]==null)?(""):($rs["numcert"])),
              "promotor" => ($rs["promotor"]),
              "fecha" => $rs["fec_inicio"],
              "importe" => $rs["importe"],
              "saldo" => $rs["saldo"],
              "suplentes" => $rr["cuenta"]*1
            );
          }
          //respuesta
          $rpta = $tabla;
          echo json_encode($rpta);
          break;
        case "coopSUDahorro":
          //verificamos nivel de usuario
          $qryUser = $db->select("select id_usernivel,codagenc,id_agencia from financiero.dbo.vw_usuarios where ID=".$_SESSION['usr_ID']);
          if ($db->has_rows($qryUser)) { $rsusr = $db->fetch_array($qryUser); }

          //datos socio
          $whr = ($data->num_cert=="")?(""):(" and ah.numero='".$data->num_cert."'");
          $sql = "select case when s.dni<>'0' and s.ruc='0' and carnet='0' then s.ap_pater+' '+s.ap_mater+', '+s.nombres when s.dni='0' and s.ruc<>'0' and carnet='0' then s.raz_social when s.dni='0' and s.ruc='0' and carnet<>'0' then s.ap_pater+' '+s.ap_mater+', '+s.nombres ELSE 'ERROR' END as socio,case when s.dni<>'0' and s.ruc='0' and carnet='0' then 'DNI' when s.dni='0' and  s.ruc<>'0' and carnet='0' then 'RUC' when s.dni='0' and  s.ruc='0' and carnet<> '0' then 'CARNET' ELSE 'ERROR' END as doc,case when s.dni<>'0' and s.ruc='0' and carnet='0' then s.dni when s.dni='0' and  s.ruc<>'0' and carnet='0' then s.ruc when s.dni='0' and s.ruc='0' and carnet<> '0' then s.carnet ELSE 'ERROR' END as nrodoc,sa.codagenc+'-'+sa.codsocio as codigo,x.detalle as servicio,x.interes_2,x.aplica_2,sa.*,ah.numero as numcert,convert(varchar,ah.fec_inicio,20) as fec_ini,convert(varchar,ah.fec_fin,20) as fec_fin,ah.importe,ah.saldo as saldo2 ";
          $sql .= "from coopSUD.dbo.COOP_DB_saldos AS sa INNER JOIN coopSUD.dbo.COOP_DB_tipo_serv AS x ON sa.tipo_serv = x.tipo_serv INNER JOIN coopSUD.dbo.COOP_DB_socios_gen AS s ON sa.codagenc = s.codagenc AND sa.codsocio = s.codsocio LEFT OUTER JOIN coopSUD.dbo.COOP_DB_ahorros_plazo AS ah ON sa.tipo_oper = ah.tipo_oper AND sa.codagenc = ah.codagenc AND sa.codsocio = ah.codsocio AND sa.tipo_serv = ah.tipo_serv ";
          $sql .= "where sa.codagenc+'-'+sa.codsocio='".$data->codsocio."' and sa.tipo_serv='".$data->tipo_serv."'".$whr;
          $qry = $db->select($sql);
          if($db->has_rows($qry)) {
            $rx = $db->fetch_array($qry);
            $socio = array(
              "codsocio" => ($data->codsocio),
              "socio" => ($rx["socio"]),
              "doc" => ($rx["doc"]),
              "nrodoc" => ($rx["nrodoc"]),
              "numcert" => ($rx["numcert"]),
              "tipo_serv" => ($rx["tipo_serv"]),
              "servicio" => ($rx["servicio"]),
              "promotor" => ("-"),
              "importe" => ($rx["importe"]*1),
              "saldo" => ($rx["saldo"]*1)//COOP_DB_saldos
            );
          }

          //datos Movimientos
          $movim = array();
          $whr = ($data->num_cert=="")?(""):(" and (m.num_pres='".$data->num_cert."')");
          $sql = "select s.detalle, c.observac, m.*,convert(varchar,m.fecha,20) as fecha1 ";
          $sql .= "from coopsud.dbo.COOP_DB_movimientos AS m INNER JOIN coopsud.dbo.COOP_DB_tipo_mov AS s ON m.tipo_mov = s.tipo_mov LEFT OUTER JOIN coopsud.dbo.COOP_DB_AUX_CAJA AS c ON m.agencia = c.agencia AND m.ventanilla = c.ventanilla AND m.num_trans = c.num_trans ";
          $sql .= "where (m.codagenc + '-' + m.codsocio = '".$data->codsocio."') AND (m.tipo_serv = '".$data->tipo_serv."') ".$whr." order by m.fecha,m.item";
          $qry = $db->select($sql);
          if($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $deposito = ($rs["tipo_mov"]=="01" || $rs["tipo_mov"]=="16" || $rs["tipo_mov"]=="18")?($rs["importe"]*1):(0);
              $retiro = ($rs["tipo_mov"]=="02" || $rs["tipo_mov"]=="30")?($rs["importe"]*1):(0);
              $otro = ($rs["tipo_mov"]=="21")?($rs["importe"]*1):(0);

              $movim[] = array(
                "agencia" => $rs["agencia"],
                "ventanilla" => $rs["ventanilla"],
                "fecha1" => $rs["fecha1"],
                "num_trans" => $rs["num_trans"],
                "tipo_mov" => $rs["tipo_mov"],
                "detalle" => $rs["detalle"],
                "deposito" => $deposito,
                "retiro" => $retiro,
                "otro" => $otro
              );
            }
          }

          //datos en ahorros plazo
          $dpf = 0;
          $whr = ($data->num_cert=="")?(""):(" and (numero='".$data->num_cert."')");
          $sql = "select * from coopSUD.dbo.COOP_DB_ahorros_plazo where (codagenc+'-'+codsocio='".$data->codsocio."') and (tipo_serv='".$data->tipo_serv."') ".$whr;
          $qry = $db->select($sql);
          if($db->has_rows($qry)) {
            $rs = $db->fetch_array($qry);
            $dpf = array(
              "esDPF" => true,
              "saldo" => $rs["saldo"]*1
            );
          }

          //respuesta
          $rpta = array("socio"=>$socio,"movim"=>$movim, "DPF"=>$dpf, "usernivel"=>$rsusr["id_usernivel"],"admin"=>701);
          echo json_encode($rpta);
          break;
        case "coopSUDdelMovim":
          for($xx=0; $xx<count($data->IDs); $xx++){
            $sql = "delete from coopSUD.dbo.COOP_DB_movimientos where codagenc+'-'+codsocio='".$data->codsocio."' and tipo_serv='".$data->tiposerv."' and num_trans='".$data->IDs[$xx]."'";
            $qry = $db->delete($sql, array());
          }
          //respuesta
          $rpta = array("error" => false,"Delete" => $xx);
          echo json_encode($rpta);
          break;
        case "coopSUDcorregirSaldo": //saldo en COOP_DB_saldos
          $sql = "update coopSUD.dbo.COOP_DB_saldos set saldo=".$data->saldo." where codagenc+'-'+codsocio='".$data->codsocio."' and tipo_serv='".$data->tiposerv."'";
          $qry = $db->update($sql, array());

          //respuesta
          $rpta = array("error" => false,"Update" => 1,"sql"=>$sql);
          echo json_encode($rpta);
          break;
        case "coopSUDcorregirDPF": //saldo en COOP_DB_ahorros_plazo
          $sql = "update coopSUD.dbo.COOP_DB_ahorros_plazo set saldo=".$data->saldo." where codagenc+'-'+codsocio='".$data->codsocio."' and tipo_serv='".$data->tiposerv."' and numero='".$data->numero."'";
          $qry = $db->update($sql, array());

          //respuesta
          $rpta = array("error" => false,"Update" => 1,"sql"=>$sql);
          echo json_encode($rpta);
          break;
        case "coopSUDcorregirNumCert":
          $qry = $db->update("update coopSUD.dbo.COOP_DB_ahorros_plazo set numero='".$data->numcert."' where codagenc+'-'+codsocio+'.'+tipo_serv+'.'+numero='".$data->cuenta."';", array());
          $qry = $db->update("update coopSUD.dbo.COOP_DB_PFijoAnalista set numero='".$data->numcert."' where codagenc+'-'+codsocio+'.'+tipo_serv+'.'+numero='".$data->cuenta."';", array());
          $qry = $db->update("update coopSUD.dbo.COOP_DB_movimientos set num_pres='".$data->numcert."' where codagenc+'-'+codsocio+'.'+tipo_serv+'.'+num_pres='".$data->cuenta."';", array());
          //respuesta
          $rpta = array("error" => false,"Update" => 1,"sql"=>"");
          echo json_encode($rpta);
          break;
        case "coopSUDgetImporteMovim":
          $qry = $db->select("select agencia,ventanilla,num_trans,importe,fecha,convert(varchar,fecha,8) as hora from coopSUD.dbo.coop_db_movimientos where agencia+'.'+ventanilla+'.'+num_trans='".$data->voucher."'");
          if ($db->has_rows($qry)) {
            $rs = $db->fetch_array($qry);
          }
          $rpta = array(
            "voucher"=>$data->voucher,
            "importe"=>$rs["importe"],
            "fecha"=>$rs["fecha"],
            "hora"=>$rs["hora"],
            "agencia"=>$rs["agencia"],
            "ventanilla"=>$rs["ventanilla"],
            "num_trans"=>$rs["num_trans"]
          );
          echo json_encode($rpta);
          break;
        case "coopSUDcambiarImporteMovim":
          $sql = "update coopSUD.dbo.coop_db_movimientos set importe=".$data->importe." where agencia+'.'+ventanilla+'.'+num_trans='".$data->voucher."'";
          $qry = $db->update($sql,array());

          //respuesta
          $rpta = array("error"=>0,"update"=>1,"sql"=>$sql);
          echo json_encode($rpta);
          break;
        case "coopSUDcambiarFechaMovim":
          $sql = "update coopSUD.dbo.coop_db_movimientos set fecha='".$data->fecha."' where agencia+'.'+ventanilla+'.'+num_trans='".$data->voucher."'";
          $qry = $db->update($sql,array());

          //respuesta
          $rpta = array("error"=>0,"update"=>1,"sql"=>$sql);
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
