<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/web_config.php');
      include_once('../../../includes/funciones.php');

      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      //****************personas****************
      switch ($data->TipoQuery) {
        case "selMovims":
          $tabla = array();
          $data->buscar = pg_escape_string($data->buscar);
          $sql = "select m.* from sis_mov m where m.nombre ilike'%".$data->buscar."%' order by m.id;";
          $qry = $db->query($sql);
          if ($db->num_rows($qry)>0) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => $rs["id"],
                "nombre" => str_ireplace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', $rs["nombre"]),
                "codigo" => $rs["codigo"],
                "abrevia" => $rs["abrevia"],
                "tipo_operID" => $rs["id_tipo_oper"],
                "in_out" => $rs["in_out"],
                "afec_prod" => $rs["afec_prod"]
              );
            }
          }
          $rpta = array("movs"=>$tabla);
          echo json_encode($rpta);
          break;
        case "viewMovim":
          $qry = $db->query_params("select * from sis_mov where id=$1;",array($data->ID));
          if ($db->num_rows($qry)) {
            $rs = $db->fetch_array($qry);
            $tipo = array(
              "ID" => $rs["id"],
              "nombre" => $rs["nombre"],
              "codigo" => $rs["codigo"],
              "abrevia" => $rs["abrevia"],
              "tipo_operID" => $rs["id_tipo_oper"],
              "in_out" => $rs["in_out"],
              "afec_prod" => $rs["afec_prod"]
            );
          }
          
          //respuesta
          $rpta = array('mov'=> $tipo);
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
