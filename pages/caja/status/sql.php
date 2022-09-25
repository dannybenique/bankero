<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');

      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      switch ($data->TipoQuery) {
        //****************movimientos****************
        case "coopSUDmovim":
          //cargar datos de movimientos coopsud
          $movim = array();
          $sql = "select agencia,ventanilla,moneda,count(*) as totaloper from coopSUD.dbo.COOP_DB_movimientos ";
          $sql.= "where fecha>='".$data->fecha." 00:00:00' AND fecha<='".$data->fecha." 23:58:59' group by agencia,ventanilla,moneda order by agencia,ventanilla,moneda";

          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $movim[] = array(
                "agencia"=> ($rs["agencia"]),
                "ventanilla" => ($rs["ventanilla"]),
                "moneda"=> ($rs["moneda"]),
                "totaloper"=> ($rs["totaloper"])
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
            array("text" => "agencia"),
            array("text" => "ventanilla"),
            array("text" => "moneda"),
            array("text" => "total")
          );

          $sql =  "select agencia,ventanilla,moneda,count(*) as totaloper from coopSUD.dbo.COOP_DB_movimientos ";
          $sql.= "where fecha>='".$data->fecha." 00:00:00' AND fecha<='".$data->fecha." 23:58:59' group by agencia,ventanilla,moneda order by agencia,ventanilla,moneda";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx=0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                array("text" => $rs["agencia"]),
                array("text" => $rs["ventanilla"]),
                array("text" => $rs["moneda"]),
                array("text" => $rs["totaloper"])
              );
            }
          }

          //respuesta
          $options = array("fileName"=>"status");
          $tableData[] = array("sheetName"=>"status","data"=>$tabla);
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
