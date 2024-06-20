<?php
  include_once('../../../includes/sess_verifica.php');
  include_once('../../../includes/db_database.php');
  include_once('../../../includes/web_config.php');
  include_once('../../../includes/funciones.php');

  if (!isset($_SESSION["usr_ID"])) { $db->enviarRespuesta(array("error" => true, "mensaje" => "Caducó la sesión.")); }
  if (!isset($_REQUEST["appSQL"])) { $db->enviarRespuesta(array("error" => true, "mensaje" => "Ninguna variable en POST")); }
  
  $data = json_decode($_REQUEST['appSQL']);

  switch ($data->TipoQuery) {
    case "dashboard":
      //respuesta
      $rpta = array(
        "agencias" => $fn->getValorCampo("select count(*) as cuenta from bn_bancos where id_padre=".$web->coopacID, "cuenta"),
        "socios" => $fn->getValorCampo("select count(*) as cuenta from bn_socios where id_coopac=".$web->coopacID, "cuenta"),
        "creditos" => $fn->getValorCampo("select count(*) as cuenta from bn_saldos where id_tipo_oper=124 and saldo>0 and id_coopac=".$web->coopacID,"cuenta")
      );
      $db->enviarRespuesta($rpta);
      break;
  }
  $db->close();
?>
