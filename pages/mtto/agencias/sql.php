<?php
  include_once('../../../includes/sess_verifica.php');
  include_once('../../../includes/db_database.php');
  include_once('../../../includes/web_config.php');
  include_once('../../../includes/funciones.php');

  if (!isset($_SESSION["usr_ID"])) { $db->enviarRespuesta(array("error" => true, "mensaje" => "Caducó la sesión.")); }
  if (!isset($_REQUEST["appSQL"])) { $db->enviarRespuesta(array("error" => true, "mensaje" => "Ninguna variable en POST")); }
  
  $data = json_decode($_REQUEST['appSQL']);
  $rpta = 0;

  switch ($data->TipoQuery) {
    case "selAgencias":
      $tabla = array();
      $buscar = strtoupper($data->buscar);
      $sql = "select b.*,x.region,x.provincia,x.distrito from bn_bancos b,vw_ubigeo x where b.estado=1 and b.id_ubigeo=x.id_distrito and b.id_padre=:coopacID and b.nombre LIKE :buscar order by codigo;";
      $params = [":coopacID"=>$web->coopacID,":buscar"=>'%'.$buscar.'%'];
      $qry = $db->query_all($sql,$params);
      if ($qry) {
        foreach($qry as $rs){
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

      //respuesta
      $rpta = array("agencias"=>$tabla);
      $db->enviarRespuesta($rpta);
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
      $db->enviarRespuesta($rpta);
      break;
    case "editAgencia":
      //cargar datos de la persona
      $qry = $db->query_all("select b.*,id_distrito,id_provincia,id_region from bn_bancos b,vw_ubigeo u where b.id_ubigeo=u.id_distrito and b.id=".$data->agenciaID);
      if ($qry) {
        $rs = reset($qry);
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
      $db->enviarRespuesta($rpta);
      break;
    case "insAgencia":
      //obteniendo nuevo ID
      $id = $fn->getValorCampo("select max(id)+1 as maxi from bn_bancos;", "maxi");

      //agregando a la tabla
      $sql = "insert into bn_bancos values (:id,:codigo,:nombre,:abrevia,null,:telefonos,:ciudad,:direccion,:ubigeoID,:coopacID,:estado,:sysIP,:userID,now(),:observac)";
      $params = [
        ":id"=>$id,
        ":codigo"=>$data->codigo,
        ":nombre"=>$data->nombre,
        ":abrevia"=>$data->abrev,
        ":telefonos"=>$data->telefonos,
        ":ciudad"=>$data->ciudad,
        ":direccion"=>$data->direccion,
        ":ubigeoID"=>$data->ubigeoID,
        ":coopacID"=>$web->coopacID,
        ":estado"=>1,
        ":sysIP"=>$fn->getClientIP(),
        ":userID"=>$_SESSION['usr_ID'],
        ":observac"=>$data->observac
      ];
      $qry = $db->query_all($sql,$params);
      $rs = ($qry) ? (reset($qry)) : (null);

      //respuesta
      $rpta = array("error" => false,"ingresados" => 1);
      $db->enviarRespuesta($rpta);
      break;
    case "updAgencia":
      $sql = "update bn_bancos set codigo=:codigo,nombre=:nombre,abrev=:abrev,telefonos=:telefonos,ciudad=:ciudad,direccion=:direccion,observac=:observac,id_ubigeo=:ubigeoID,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id=:id";
      $params = [
        ":id"=>$data->ID,
        ":codigo"=>$data->codigo,
        ":nombre"=>$data->nombre,
        ":abrev"=>$data->abrev,
        ":telefonos"=>$data->telefonos,
        ":ciudad"=>$data->ciudad,
        ":direccion"=>$data->direccion,
        ":observac"=>$data->observac,
        ":ubigeoID"=>$data->ubigeoID,
        ":sysIP"=>$fn->getClientIP(),
        ":userID"=>$_SESSION['usr_ID']
      ];
      $qry = $db->query_all($sql,$params);
      $rs = ($qry) ? (reset($qry)) : (null);

      //respuesta
      $rpta = array("error" => false,"actualizados" => 1);
      $db->enviarRespuesta($rpta);
      break;
    case "delAgencias":
      foreach($data->arr as $obj){
        $sql = "update bn_bancos set estado=0,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id=:id";
        $params = [":id"=>$obj,":sysIP"=>$fn->getClientIP(),":userID"=>$_SESSION['usr_ID']];
        $qry = $db->query_all($sql,$params);
        $rs = ($qry) ? (reset($qry)) : (null);
      }

      //respuesta
      $rpta = array("error" => false,"borrados" => count($data->arr));
      $db->enviarRespuesta($rpta);
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
      $db->enviarRespuesta($rpta);
      break;
  }
  $db->close();
?>
