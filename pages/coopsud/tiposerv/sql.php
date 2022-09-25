<?php
  include_once('../../../includes/sess_verifica.php');

    if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      switch ($data->TipoQuery) {
        //****************congelados****************
        case "coopSUDservicio":
          $tabla = array();
          $sql = "select * from coopSUD.dbo.COOP_DB_tipo_serv where (tipo_oper='".$data->tipo."') and (detalle like'%".$data->buscar."%') order by detalle";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx=0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);

              $tabla[] = array(
                "tipo_serv" => $rs["tipo_serv"],
                "servicio" => ($rs["detalle"]),
                "interes_1" => ($rs["interes_1"]),
                "interes_2" => ($rs["interes_2"]),
                "interes_3" => ($rs["interes_3"]),
                "apl_1" => ($rs["aplica_1"]),
                "apl_2" => ($rs["aplica_2"]),
                "apl_3" => ($rs["aplica_3"])
              );
            }
          }
          echo json_encode($tabla);
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
