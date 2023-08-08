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
        case "selProductos":
          $tabla = array();
          $data->buscar = pg_escape_string($data->buscar);
          $sql = "select * from vw_productos where estado=1 and id_coopac=".$web->coopacID." and producto ilike'%".$data->buscar."%' order by id_padre,producto;";
          $qry = $db->query($sql);
          if ($db->num_rows($qry)>0) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => $rs["id"],
                "codigo" => $rs["codigo"],
                "producto" => str_ireplace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', $rs["producto"]),
                "abrevia" => $rs["abrevia"],
                "tipo_oper" => $rs["tipo_oper"],
                "obliga" => $rs["obliga"],
                "estado" => $rs["estado"]
              );
            }
          }
          $rpta = array("productos"=>$tabla);
          echo json_encode($rpta);
          break;
        case "editProducto":
          //cargar datos de la persona
          $qry = $db->query("select * from bn_productos where id=".$data->productoID);
          if ($db->num_rows($qry)>0) {
            $rs = $db->fetch_array($qry);
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
          echo json_encode($rpta);
          break;
        case "insProducto":
          //obteniendo nuevo ID y otros
          $id = $db->fetch_array($db->query("select COALESCE(max(id)+1,1) as maxi from bn_productos;"))["maxi"];
          $codigo = $db->fetch_array($db->query_params("select right('0000'||cast(max(codigo::integer+1) as text),4) as code from bn_productos where id_coopac=$1",array($web->coopacID)))["code"];
          $id_tipo_oper = $db->fetch_array($db->query_params("select id_tipo_oper from bn_productos where id=$1",array($data->tipoID)))["id_tipo_oper"];

          //agregando a la tabla
          $sql = "insert into bn_productos values ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,now())";
          $params = array(
            $id, 
            $codigo, 
            pg_escape_string($data->nombre), 
            pg_escape_string($data->abrevia), 
            $id_tipo_oper, 
            $web->coopacID, 
            $data->tipoID,
            $data->obliga,
            1, 
            $fn->getClientIP(), 
            $_SESSION['usr_ID']
          );

          $qry = $db->query_params($sql,$params);
          $rpta = array("error" => false,"ingresados" => 1);
          echo json_encode($rpta);
          break;
        case "updProducto":
          $id_tipo_oper = $db->fetch_array($db->query_params("select id_tipo_oper from bn_productos where id=$1",array($data->tipoID)))["id_tipo_oper"];
          $sql = "update bn_productos set nombre=$2,abrevia=$3,id_tipo_oper=$4,id_padre=$5,obliga=$6,sys_ip=$7,sys_user=$8,sys_fecha=now() where id=$1";
          $params = array(
            $data->ID,
            pg_escape_string($data->nombre),
            pg_escape_string($data->abrevia),
            $id_tipo_oper,
            $data->tipoID,
            $data->obliga,
            $fn->getClientIP(),
            $_SESSION['usr_ID']
          );

          $qry = $db->query_params($sql,$params);
          $rpta = array("error" => false,"actualizados" => 1);
          echo json_encode($rpta);
          break;
        case "delProductos":
          $params = array();
          for($i=0; $i<count($data->arr); $i++){
            $sql = "update bn_productos set estado=0,sys_ip=$2,sys_user=$3,sys_fecha=now() where id=$1";
            $params = array($data->arr[$i],$fn->getClientIP(),$_SESSION['usr_ID']);
            $qry = $db->query_params($sql,$params);
          }
          //respuesta
          $rpta = array("error" => false,"borrados" => count($data->arr));
          echo json_encode($rpta);
          break;
        case "startProducto":
          //respuesta
          $rpta = array(
            "comboTipoProd" => $fn->getComboBox("select id,nombre from bn_productos where id_padre is null;"),
            "fecha" => $db->fetch_array($db->query("select now() as fecha;"))["fecha"],
            "coopac" => $web->coopacID);
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
