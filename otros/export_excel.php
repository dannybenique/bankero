<?php
  if (isset($_POST["appSelect"])){
    include_once('db_database.php');
    $data = json_decode($_POST['appSelect']);
    $rpta = 0;

    switch ($data->miSelect) {
      case "rptCarteraSocios":
        $whr = "";
        $eventos = array();
        if(($data->miIdAgencia) > 0) { $whr = "and a.ID=".($data->miIdAgencia); }
        $sql = "select m.agencia,p.respons2,w.ID,w.worker,w.cargo,c.cantidad as ini,c.saldo as saldo_ini,count(*) as hoy,sum(p.saldo) as saldo_hoy from coopSUD.dbo.COOP_DB_prestamos p, coopSUD.dbo.COOP_DB_movimientos m,vw_workers w,tb_agencias a,tb_cartera c where w.codigo=p.respons2 and m.codagenc=p.codagenc and m.codsocio=p.codsocio and p.num_pres=m.num_pres and m.tipo_mov='19' and p.saldo>0 and w.ID=c.id_responsable and m.agencia=a.codigo and a.ID=c.id_agencia and c.id_negociotipo=204 ".$whr." group by m.agencia,p.respons2,w.ID,w.worker,w.cargo,c.cantidad,c.saldo order by cargo,worker";
        $qry = $_database->db_select($sql);
        if ($_database->db_has_rows($qry)) {
          for($xx = 0; $xx<$_database->db_num_rows($qry); $xx++){
            $rs = $_database->db_fetch_array($qry);
            $crec = ($rs["hoy"]) - ($rs["ini"]);

            $eventos[] = array(
              "agencia" => $rs["agencia"],
              "codigo" => $rs["respons2"],
              "worker" => utf8_encode($rs["worker"]),
              "cargo" => utf8_encode($rs["cargo"]),
              "ini" => $rs["ini"],
              "saldo_ini" => $rs["saldo_ini"],
              "hoy" => $rs["hoy"],
              "saldo_hoy" => $rs["saldo_hoy"],
              "crec" => $crec
            );
          }
        }
        //guardar en export_excel
        if(!empty($libros)) {
          $filename = “libros.xls”;
          header(“Content-Type: application/vnd.ms-excel”);
          header(“Content-Disposition: attachment; filename=”.$filename);
          $mostrar_columnas = false;

          foreach($libros as $libro) {
            if(!$mostrar_columnas) {
              echo implode(“\t”, array_keys($libro)) . “\n”;
              $mostrar_columnas = true;
            }
            echo implode(“\t”, array_values($libro)) . “\n”;
          }
        }else{
          echo ‘No hay datos a exportar’;
        }
        exit;
        //echo json_encode($eventos);
      break;
    }
    $_database->db_close();
  } else{
    $resp = array("error"=>true,"resp"=>"ninguna variable en POST");
    echo json_encode($resp);
  }
?>
