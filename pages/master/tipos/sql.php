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
        case "selTipos":
          $tabla = array();
          $data->buscar = pg_escape_string($data->buscar);
          $sql = "select s.*,b.id_tipo,x.nombre as nivel from sis_tipos s join sis_tipos x on (s.id_padre=x.id) left join bn_tipos b on (s.id=b.id_tipo and b.id_coopac=".$web->coopacID.") where s.id_padre=".$data->tipo." and s.nombre ilike'%".$data->buscar."%' order by s.id;";
          $qry = $db->query($sql);
          if ($db->num_rows($qry)>0) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => $rs["id"],
                "tipoID" => $rs["id_tipo"], //este dato esta configurado en bn_tipos
                "nombre" => str_ireplace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', $rs["nombre"]),
                "codigo" => $rs["codigo"],
                "abrevia" => $rs["abrevia"],
                "tipo" => $rs["tipo"],
                "padreID" => $rs["id_padre"],
                "nivel" => $rs["nivel"]
              );
            }
          }
          $rpta = array("tipos"=>$tabla,"sql"=>$sql);
          echo json_encode($rpta);
          break;
        case "viewTipo":
          //cabecera
          $qry = $db->query_params("select * from sis_tipos where id=$1;",array($data->ID));
          if ($db->num_rows($qry)) {
            $rs = $db->fetch_array($qry);
            $tipo = array(
              "ID" => $rs["id"],
              "codigo" => $rs["codigo"],
              "nombre" => $rs["nombre"],
              "abrevia" => $rs["abrevia"],
              "tipo" => $rs["tipo"],
              "padreID" => $rs["id_padre"]
            );
          }
          
          //respuesta
          $rpta = array(
            'comboTipos' => $fn->getComboBox("select id,nombre from sis_tipos where id_padre is null order by id;"),
            'tipo'=> $tipo );
          echo json_encode($rpta);
          break;
        case "startTipos":
          //respuesta
          $rpta = array(
            'comboTipos' => $fn->getComboBox("select id,nombre from sis_tipos where id_padre is null order by id;"));
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
