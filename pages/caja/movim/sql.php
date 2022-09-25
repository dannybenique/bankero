<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');

      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      switch ($data->TipoQuery) {
        //****************movimientos****************
        case "ComboBoxAgencias":
          //verificar usuario
          $rsusr = $db->fetch_array($db->select("select u.id_usernivel,a.codigo as agencia from dbo.tb_usuarios u,tb_workers w,tb_agencias a where w.id_agencia=a.id and w.id_persona=u.id_persona and u.id_persona=".$_SESSION['usr_ID'].";"));

          //combo agencias
          $agencias = array();
          $qry = $db->select("select * from coopSUD.dbo.COOP_DB_agencia order by agencia;");
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $agencias[] = array(
                "ID" => $rs["agencia"],
                "nombre" => ($rs["agencia"]." - ".$rs["detalle"])
              );
            }
          }

          //combo ventanillas
          $ventanillas = array();
          $qry = $db->select("select * from coopSUD.dbo.COOP_DB_ventanilla where agencia='".$rsusr["agencia"]."' order by ventanilla;");
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $ventanillas[] = array(
                "ID" => $rs["ventanilla"],
                "nombre" => ($rs["ventanilla"]." - ".$rs["detalle"])
              );
            }
          }

          //respuesta
          $rpta = array("agencias"=>$agencias,"ventanillas"=>$ventanillas,"agencia"=>$rsusr["agencia"],"agenciaID"=>$_SESSION['usr_agenciaID'],"usernivel"=>$rsusr["id_usernivel"],"admin"=>701);
          echo json_encode($rpta);
          break;
        case "coopSUDmovim":
          //cargar datos de movimientos coopsud
          $movim = array();
          $sql =  "select distinct m.agencia, m.ventanilla, m.tipo_mov, m.tipo_serv, m.tipo_oper, m.num_trans, m.item, m.fecha, m.moneda, m.codagenc+'-'+m.codsocio as codsocio, m.referen, m.num_pres, m.importe, m.codusuario, m.fecha_dig, m.dias_int, m.condicion, coopSUD.dbo.fc_socio(m.codagenc + '-' + m.codsocio, 'S') AS socio, x.observac, o.es, o.detalle AS movim, s.detalle AS servicio ";
          $sql .= "from coopSUD.dbo.COOP_DB_movimientos AS m INNER JOIN coopSUD.dbo.COOP_DB_tipo_mov AS o ON m.tipo_mov = o.tipo_mov INNER JOIN coopSUD.dbo.COOP_DB_tipo_serv AS s ON m.tipo_serv = s.tipo_serv LEFT OUTER JOIN coopSUD.dbo.COOP_DB_AUX_CAJA AS x ON m.agencia = x.agencia AND m.ventanilla = x.ventanilla AND m.num_trans = x.num_trans ";
          $sql .= "where (m.agencia = '".$data->agencia."') AND (m.ventanilla = '".$data->ventanilla."') AND m.moneda='".$data->moneda."' AND (m.fecha >= '".$data->fecha." 00:00:00') AND (m.fecha <= '".$data->fecha." 23:58:59') ";
          $sql .= "order by m.num_trans";

          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $movim[] = array(
                "agencia"=> ($rs["agencia"]),
                "ventanilla" => ($rs["ventanilla"]),
                "tipo_mov"=> ($rs["tipo_mov"]),
                "movim"=> ($rs["movim"]),
                "tipo_serv"=> ($rs["tipo_serv"]),
                "servicio"=> ($rs["servicio"]),
                "codsocio"=> ($rs["codsocio"]),
                "num_trans"=> ($rs["num_trans"]),
                "fecha"=> ($rs["fecha"]),
                "socio" => ($rs["socio"]==null)?(($rs["observac"]==null)?(""):($rs["observac"])):($rs["socio"]),
                "ingreso" => (($rs["es"]=="E")?($rs["importe"]):(0))*1,
                "egreso" => (($rs["es"]=="S")?($rs["importe"]):(0))*1
              );
            }
          }

          //respuesta
          $rpta = array("movim"=>$movim,"sql"=>$sql);
          echo json_encode($rpta);
          break;
        case "coopSUDmovimDownload":
          //cargar datos de movimientos para coopSUD
          $tabla[] = array(
            array("text" => "Voucher"),
            array("text" => "Fecha"),
            array("text" => "Codigo"),
            array("text" => "Socio"),
            array("text" => "Servicio"),
            array("text" => "Movimiento"),
            array("text" => "Ingresos"),
            array("text" => "Egresos")
          );

          $sql =  "select distinct m.agencia, m.ventanilla, m.tipo_mov, m.tipo_serv, m.tipo_oper, m.num_trans, m.item, m.fecha, m.moneda, m.codagenc+'-'+m.codsocio as codsocio, m.referen, m.num_pres, m.importe, m.codusuario, m.fecha_dig, m.dias_int, m.condicion, coopSUD.dbo.fc_socio(m.codagenc + '-' + m.codsocio, 'S') AS socio, x.observac, o.es, o.detalle AS movim, s.detalle AS servicio ";
          $sql .= "from coopSUD.dbo.COOP_DB_movimientos AS m INNER JOIN coopSUD.dbo.COOP_DB_tipo_mov AS o ON m.tipo_mov = o.tipo_mov INNER JOIN coopSUD.dbo.COOP_DB_tipo_serv AS s ON m.tipo_serv = s.tipo_serv LEFT OUTER JOIN coopSUD.dbo.COOP_DB_AUX_CAJA AS x ON m.agencia = x.agencia AND m.ventanilla = x.ventanilla AND m.num_trans = x.num_trans ";
          $sql .= "where (m.agencia = '".$data->agencia."') AND (m.ventanilla = '".$data->ventanilla."') AND m.moneda='".$data->moneda."' AND (m.fecha >= '".$data->fecha." 00:00:00') AND (m.fecha <= '".$data->fecha." 23:58:59') ";
          $sql .= "order by m.num_trans";

          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx=0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                array("text" => ($rs["agencia"].".".$rs["ventanilla"].".".$rs["num_trans"])),
                array("text" => ($data->fecha)),
                array("text" => ($rs["codsocio"])),
                array("text" => ($rs["socio"]==null)?($rs["observac"]):($rs["socio"])),
                array("text" => ($rs["tipo_serv"]." - ".$rs["servicio"])),
                array("text" => ($rs["tipo_mov"]." - ".$rs["movim"])),
                array("text" => ((($rs["es"]=="E")?($rs["importe"]):(0))*1)),
                array("text" => ((($rs["es"]=="S")?($rs["importe"]):(0))*1)),
              );
            }
          }

          //respuesta
          $options = array("fileName"=>"movim");
          $tableData[] = array("sheetName"=>"movim","data"=>$tabla);
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
