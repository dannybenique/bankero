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
        case "selAgencias":
          $tabla = array();
          $data->buscar = pg_escape_string(strtoupper($data->buscar));
          $sql = "select b.*,x.region,x.provincia,x.distrito from bn_bancos b,vw_ubigeo x where b.estado=1 and b.id_ubigeo=x.id_distrito and b.id_padre=".$web->coopacID." and b.nombre like'%".$data->buscar."%' order by codigo;";
          $qry = $db->query($sql);
          if ($db->num_rows($qry)>0) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => $rs["id"],
                "codigo" => $rs["codigo"],
                "nombre" => str_ireplace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', $rs["nombre"]),
                "telefonos" => $rs["telefonos"],
                "direccion" => $rs["direccion"],
                "region" => $rs["region"],
                "provincia" => $rs["provincia"],
                "distrito" => $rs["distrito"]
              );
            }
          }
          $rpta = array("agencias"=>$tabla);
          echo json_encode($rpta);
          break;
        case "newAgencia":
          //comboBox inicial
          $rpta = array(
            "comboRegiones" => ($fn->getComboBox("select id,nombre from sis_ubigeo where id_padre=101 order by nombre;")),
            "comboProvincias" => ($fn->getComboBox("select id,nombre from sis_ubigeo where id_padre=1014 order by nombre;")),
            "comboDistritos" => ($fn->getComboBox("select id,nombre from sis_ubigeo where id_padre=1401 order by nombre;")),
            "rolID" => (int)$_SESSION["usr_data"]["rolID"],
            "rootID" => 101
          );
          echo json_encode($rpta);
          break;
        case "editAgencia":
          //cargar datos de la persona
          $qry = $db->query("select b.*,id_distrito,id_provincia,id_region from bn_bancos b,vw_ubigeo u where b.id_ubigeo=u.id_distrito and b.id=".$data->agenciaID);
          if ($db->num_rows($qry)>0) {
            $rs = $db->fetch_array($qry);
            $rpta = array(
              "ID" => $rs["id"],
              "codigo" => $rs["codigo"],
              "abrev" => ($rs["abrev"]),
              "nombre" => ($rs["nombre"]),
              "ciudad" => ($rs["ciudad"]),
              "direccion" => ($rs["direccion"]),
              "comboRegiones" => ($fn->getComboBox("select id,nombre from sis_ubigeo where id_padre=101 order by nombre;")),
              "comboProvincias" => ($fn->getComboBox("select id,nombre from sis_ubigeo where id_padre=".$rs["id_region"]." order by nombre;")),
              "comboDistritos" => ($fn->getComboBox("select id,nombre from sis_ubigeo where id_padre=".$rs["id_provincia"]." order by nombre;")),
              "telefonos" => ($rs["telefonos"]),
              "observac" => ($rs["observac"]),
              "id_distrito" => ($rs["id_distrito"]),
              "id_provincia" => ($rs["id_provincia"]),
              "id_region" => ($rs["id_region"]),
              "rolID" => (int)$_SESSION["usr_data"]["rolID"],
              "rootID" => 101
            );
          }
          //respuesta
          echo json_encode($rpta);
          break;
        case "insAgencia":
          //obteniendo nuevo ID
          $id = $db->fetch_array($db->query("select max(id)+1 as maxi from bn_bancos where id_padre=".$web->coopacID))["maxi"];

          //agregando a la tabla
          $sql = "insert into bn_bancos values ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,now())";
          $params = array(
            $id,
            pg_escape_string($data->codigo),
            pg_escape_string($data->nombre),
            pg_escape_string($data->abrev),
            null,
            pg_escape_string($data->telefonos),
            pg_escape_string($data->ciudad),
            pg_escape_string($data->direccion),
            $data->ubigeoID,
            $web->coopacID,
            pg_escape_string($data->observac),
            1,
            $fn->getClientIP(),
            $_SESSION['usr_ID']
          );

          $qry = $db->query_params($sql,$params);
          $rpta = array("error" => false,"ingresados" => 1);
          echo json_encode($rpta);
          break;
        case "updAgencia":
          $sql = "update bn_bancos set codigo=$2,nombre=$3,abrev=$4,telefonos=$5,ciudad=$6,direccion=$7,observac=$8,id_ubigeo=$9,sys_ip=$10,sys_user=$11,sys_fecha=now() where id=$1";
          $params = array(
            $data->ID,
            pg_escape_string($data->codigo),
            pg_escape_string($data->nombre),
            pg_escape_string($data->abrev),
            pg_escape_string($data->telefonos),
            pg_escape_string($data->ciudad),
            pg_escape_string($data->direccion),
            pg_escape_string($data->observac),
            $data->ubigeoID,
            $fn->getClientIP(),
            $_SESSION['usr_ID']
          );

          $qry = $db->query_params($sql,$params);
          $rpta = array("error" => false,"actualizados" => 1);
          echo json_encode($rpta);
          break;
        case "delAgencias":
          $params = array();
          for($i=0; $i<count($data->arr); $i++){
            $sql = "update bn_bancos set estado=0,sys_ip=$2,sys_user=$3,sys_fecha=now() where id=$1";
            $params = array($data->arr[$i],$fn->getClientIP(),$_SESSION['usr_ID']);
            $qry = $db->query_params($sql,$params);
          }
          //respuesta
          $rpta = array("error" => false,"borrados" => count($data->arr));
          echo json_encode($rpta);
          break;
        case "comboUbigeo":
          switch($data->tipoID){
            case 3: //actualiza provincia
              $provincias = $fn->getComboBox("select id,nombre from sis_ubigeo where id_padre=".$data->padreID." order by nombre;");
              $distritos = $fn->getComboBox("select id,nombre from sis_ubigeo where id_padre=".$provincias[0]["ID"]." order by nombre;");
              $rpta = array( "provincias" => $provincias, "distritos" => $distritos );
              break;
            case 4: //actualiza distrito
              $distritos = $fn->getComboBox("select id,nombre from sis_ubigeo where id_padre=".$data->padreID." order by nombre;");
              $rpta = array( "distritos" => $distritos );
              break;
          }
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
