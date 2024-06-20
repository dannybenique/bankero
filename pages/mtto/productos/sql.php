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
    case "selProductos":
      $tabla = array();
      $buscar = strtoupper($data->buscar);
      $sql = "select * from vw_productos where estado=1 and id_coopac=:coopacID and producto LIKE :buscar order by id_padre,producto;";
      $params = [":coopacID"=>$web->coopacID,":buscar"=>'%'.$buscar.'%'];
      $qry = $db->query_all($sql,$params);
      if ($qry) {
        foreach($qry as $rs){
          $tabla[] = array(
            "ID" => $rs["id"],
            "codigo" => $rs["codigo"],
            "producto" => str_ireplace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["producto"]),
            "abrevia" => $rs["abrevia"],
            "tipo_oper" => $rs["tipo_oper"],
            "obliga" => $rs["obliga"],
            "estado" => $rs["estado"]
          );
        }
      }
      $rpta = array("productos"=>$tabla);
      $db->enviarRespuesta($rpta);
      break;
    case "editProducto":
      //cargar datos de la persona
      $qry = $db->query_all("select * from bn_productos where id=".$data->productoID);
      if ($qry) {
        $rs = reset($qry);
        $rpta = array(
          "ID" => $rs["id"],
          "codigo" => $rs["codigo"],
          "abrev" => ($rs["abrevia"]),
          "nombre" => ($rs["nombre"]),
          "id_padre" => ($rs["id_padre"]),
          "obliga" => ($rs["obliga"]*1),
          "comboTipoProd" => $fn->getComboBox("select id,nombre from bn_productos where id_padre is null;"),
        );
      }

      //respuesta
      $db->enviarRespuesta($rpta);
      break;
    case "insProducto":
      //obteniendo nuevo ID y otros
      $id = $fn->getValorCampo("select COALESCE(max(id)+1,1) as maxi from bn_productos;", "maxi");
      $codigo = $fn->getValorCampo("select right('0000'||cast(max(codigo::integer+1) as text),4) as code from bn_productos where id_coopac=".$web->coopacID, "code");
      $id_tipo_oper = $fn->getValorCampo("select id_tipo_oper from bn_productos where id=".$data->tipoID, "id_tipo_oper");

      //agregando a la tabla
      $sql = "insert into bn_productos values (:id,:codigo,:nombre,:abrevia,:operID,:coopacID,:tipoID,:obliga,:estado,:sysIP,:userID,now())";
      $params = [
        ":id"=>$id,
        ":codigo"=>$codigo,
        ":nombre"=>$data->nombre,
        ":abrevia"=>$data->abrevia,
        ":operID"=>$id_tipo_oper,
        ":coopacID"=>$web->coopacID,
        ":tipoID"=>$data->tipoID,
        ":obliga"=>$data->obliga,
        ":estado"=>1,
        ":sysIP"=>$fn->getClientIP(),
        ":userID"=>$_SESSION['usr_ID']
      ];
      $qry = $db->query_all($sql,$params);
      $rs = ($qry) ? (reset($qry)) : (null);

      //respuesta
      $rpta = array("error" => false,"ingresados" => 1);
      $db->enviarRespuesta($rpta);
      break;
    case "updProducto":
      $id_tipo_oper = $fn->getValorCampo("select id_tipo_oper from bn_productos where id=".$data->tipoID, "id_tipo_oper");
      
      $sql = "update bn_productos set nombre=:nombre,abrevia=:abrevia,id_tipo_oper=:operID,id_padre=:tipoID,obliga=:obliga,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id=:id";
      $params = [
        ":id"=>$data->ID,
        ":nombre"=>$data->nombre,
        ":abrevia"=>$data->abrevia,
        ":operID"=>$id_tipo_oper,
        ":tipoID"=>$data->tipoID,
        ":obliga"=>$data->obliga,
        ":sysIP"=>$fn->getClientIP(),
        ":userID"=>$_SESSION['usr_ID']
      ];
      $qry = $db->query_all($sql,$params);
      $rs = ($qry) ? (reset($qry)) : (null);

      //respuesta
      $rpta = array("error" => false,"actualizados" => 1, "sql" => $sql);
      $db->enviarRespuesta($rpta);
      break;
    case "delProductos":
      foreach($data->arr as $obj){
        $sql = "update bn_productos set estado=0,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id=:id";
        $params = [
          ":id"=>$obj,
          ":sysIP"=>$fn->getClientIP(),
          ":userID"=>$_SESSION['usr_ID']
        ];
        $qry = $db->query_all($sql,$params);
        $rs = ($qry) ? (reset($qry)) : (null);
      }

      //respuesta
      $rpta = array("error" => false,"borrados" => count($data->arr));
      $db->enviarRespuesta($rpta);
      break;
    case "startProducto":
      //respuesta
      $rpta = array(
        "comboTipoProd" => $fn->getComboBox("select id,nombre from bn_productos where id_padre is null;"),
        "fecha" => $fn->getFechaActualDB(),
        "coopac" => $web->coopacID
      );
      $db->enviarRespuesta($rpta);
      break;
  }
  $db->close();
?>
