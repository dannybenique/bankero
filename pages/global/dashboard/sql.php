<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/web_config.php');

      $data = json_decode($_REQUEST['appSQL']);
      switch ($data->TipoQuery) {
        case "dashboard":
          foreach($db->query_all("select count(*) as cuenta from bn_bancos where id_padre=".$web->coopacID) as $rs){ $agencias = $rs['cuenta']; }
          foreach($db->query_all("select count(*) as cuenta from bn_socios where id_coopac=".$web->coopacID) as $rs){ $socios = $rs['cuenta']; }

          //respuesta
          $rpta = array(
            "agencias" => $agencias,
            "socios" => $socios
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
