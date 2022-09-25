<?php
  include_once('../../../includes/sess_verifica.php');

    if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      switch ($data->TipoQuery) {
        //****************congelados****************
        case "coopSUDbuscadireccion": //busca la direccion en base al recibo de luz
          $tabla = array();
          $sql = "select s.ap_pater+' '+s.ap_mater+', '+s.nombres as socio,dp.detalle as depa,pr.detalle as prov,dt.detalle as dist,s.* from CoopSUD.dbo.coop_db_socios_gen s, CoopSUD.dbo.COOP_DB_departamento dp, CoopSUD.dbo.COOP_DB_provincia pr, CoopSUD.dbo.COOP_DB_distrito dt where dp.departamento=pr.departamento and dt.departamento=pr.departamento and dt.provincia=pr.provincia and dt.departamento+dt.provincia+dt.distrito=s.ubigeo and s.tiempo_res like'%".$data->buscar."%' order by s.codagenc,s.codsocio";

          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);

              $miSocio  = (strlen($rs["raz_social"])<=4) ? ($rs["socio"]) : ($rs["raz_social"]);
              $miDUInro = (strlen($rs["raz_social"])<=4) ? ($rs["dni"]) : ($rs["ruc"]);
              $miDUItxt = (strlen($rs["raz_social"])<=4) ? ("DNI") : ("RUC");
              $rsPI = $db->fetch_array($db->select("select count(*) as cuenta from CoopSUD.dbo.COOP_DB_prestamos where codagenc='".$rs["codagenc"]."' and codsocio='".$rs["codsocio"]."' and saldo<=0"));
              $rsPA = $db->fetch_array($db->select("select count(*) as cuenta from CoopSUD.dbo.COOP_DB_prestamos where codagenc='".$rs["codagenc"]."' and codsocio='".$rs["codsocio"]."' and saldo>0"));

              $tabla[] = array(
                "codsocio" => ($rs["codagenc"]."-".$rs["codsocio"]),
                "duitxt" => $miDUItxt,
                "duinro" => $miDUInro,
                "socio" => ($miSocio),
                "reciboluz" => str_replace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', $rs["tiempo_res"]),
                "ubigeo"=> ($rs["depa"]." &raquo; ".$rs["prov"]." &raquo; ".$rs["dist"]),
                "direccion" => $rs["direccion"],
                "crediPI" => $rsPI["cuenta"]*1,//creditos inactivos
                "crediPA" => $rsPA["cuenta"]*1//creditos activos
              );
            }
          }

          $rpta = array("tabla"=>$tabla);
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
