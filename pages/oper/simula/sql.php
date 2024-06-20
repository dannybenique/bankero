<?php
  include_once('../../../includes/sess_verifica.php');
  include_once('../../../includes/db_database.php');
  include_once('../../../includes/funciones.php');
  
  if (!isset($_SESSION["usr_ID"])) { $db->enviarRespuesta(array("error" => true, "mensaje" => "Caducó la sesión.")); }
  if (!isset($_REQUEST["appSQL"])) { $db->enviarRespuesta(array("error" => true, "mensaje" => "Ninguna variable en POST")); }
  
  $data = json_decode($_REQUEST['appSQL']);
  $rpta = 0;

  switch ($data->TipoQuery) {
    //****************simulacion****************
    case "simulaCredito":
      //obtenemos la simulacion
      $pivot = ($data->TipoCredito=="1")?($data->pricuota):($data->frecuencia);
      $tabla = $fn->getSimulacionCredito(
        $data->TipoCredito,
        $data->importe,
        $data->TEA,
        $data->segDesgr,
        $data->nroCuotas,
        $data->fecha,
        $pivot
      );

      //tasas
      $qry = $db->query_all("select fn_get_tem(".$data->TEA.") as tem,fn_get_ted(".$data->TEA.") as  ted;");
      $rs = reset($qry);
      $TEM = $rs["tem"];
      $TED = $rs["ted"];

      //respuesta
      $rpta = array("tabla"=>$tabla, "tea"=>$data->TEA, "tem"=>$TEM, "ted"=>$TED);
      $db->enviarRespuesta($rpta);
      break;
    case "simulaAhorro":
      //respuesta
      $rpta = array( "interes" => $fn->getValorCampo("select dbo.fn_GetAhorrosTotalInteresImporte('".$data->fechaIni."','".$data->fechaFin."',".$data->importe.",".$data->tasa.") as interes", "interes") );
      $db->enviarRespuesta($rpta);
      break;
    case "selProductos":
      $tabla = array();

      $qry = $db->query_all("select * from bn_productos where id_padre=2 and id_tipo_prod=201 order by nombre;");
      if ($qry) {
        foreach($qry as $rs){
          $tabla[] = array(
            "ID" => $rs["ID"],
            "nombre"=> ($rs["nombre"])
          );
        }
      }

      //respuesta
      $rpta = $tabla;
      $db->enviarRespuesta($rpta);
      break;
  }
  $db->close();
?>
