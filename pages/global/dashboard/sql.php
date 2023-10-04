<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/web_config.php');

      $data = json_decode($_REQUEST['appSQL']);
      switch ($data->TipoQuery) {
        case "dashboard":
          $qry = $db->query_all("select count(*) as cuenta from bn_bancos where id_padre=:coopac", [':coopac'=>$web->coopacID]);
          $agencias = reset($qry)['cuenta'];
          $qry = $db->query_all("select count(*) as cuenta from bn_socios where id_coopac=:coopac", [':coopac'=>$web->coopacID]);
          $socios = reset($qry)['cuenta'];
          $qry = $db->query_all("select count(*) as cuenta from bn_saldos where id_tipo_oper=124 and saldo>0 and id_coopac=:coopac", [':coopac'=>$web->coopacID]);
          $creditos = reset($qry)['cuenta'];

          //respuesta
          $rpta = array(
            "agencias" => $agencias,
            "socios" => $socios,
            "creditos" => $creditos
          );
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
