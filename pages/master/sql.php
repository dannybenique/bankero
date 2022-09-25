<?php
  include_once('../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../includes/db_database.php');
      include_once('../../includes/funciones.php');
      $data = json_decode($_REQUEST['appSQL']);

      switch ($data->TipoQuery) {
        //****************Maestro****************
        case "selProductos":
          $whr = "";
          $tabla = array();

          //verificar usuario
          $qryusr = $db->select(utf8_decode("select id_usernivel from dbo.tb_usuarios where id_persona=".$_SESSION['usr_ID'].";"));
          $rsusr = $db->fetch_array($qryusr);

          //cargar agencias
          $sql = "select * from dbo.vw_productos where estado=1 and nombre like'%".($data->miBuscar)."%' order by id_tipo_prod,ID;";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => ($rs["ID"]),
                "nombre" => utf8_encode($rs["nombre"]),
                "tasamin" => ($rs["rangoTasaBajo"]),
                "tasamax" => ($rs["rangoTasaAlto"]),
                "tasamora" => ($rs["tasaMora"]),
                "tipo" => utf8_encode($rs["tipo"]),
              );
            }
          }
          //respuesta
          $rpta = array("tabla"=>$tabla,"usernivel"=>$rsusr["id_usernivel"],"admin"=>701);
          echo json_encode($rpta);
          break;
        case "editProducto":
          //cargar datos de la persona
          $qry = $db->select("select * from dbo.vw_productos where ID=".$data->productoID);
          if ($db->has_rows($qry)) {
            $rs = $db->fetch_array($qry);
            $rpta = array(
              "ID" => $rs["ID"],
              "nombre" => utf8_encode($rs["nombre"]),
              "tasamin" => ($rs["rangoTasaBajo"]),
              "tasamax" => ($rs["rangoTasaAlto"]),
              "tasamora" => ($rs["tasaMora"]),
              "segdesgr" => ($rs["segDesgr"]),
              "id_tipo_prod" => ($rs["id_tipo_prod"]),
              "id_tipo_oper" => ($rs["id_tipo_oper"]),
              "id_tipo_mone" => ($rs["id_tipo_mone"]),
              "sysIP" => utf8_encode($rs["sysIP"]),
              "sysagencia" => utf8_encode($rs["sysagencia"]),
              "sysuser" => utf8_encode($rs["sysuser"]),
              "sysfecha" => utf8_encode($rs["sysfecha"]),
            );
          }

          //respuesta
          echo json_encode($rpta);
          break;
        case "execProducto":
          $sql = "exec dbo.sp_productos '".$data->commandSQL."',".
            ($data->ID).",'".
            utf8_decode($data->nombre)."',".
            ($data->tasamin).",".
            ($data->tasamax).",".
            ($data->tasamora).",".
            ($data->segDesgr).",".
            ($data->id_tipo_prod).",".
            ($data->id_tipo_oper).",".
            ($data->id_tipo_mone).",'".
            get_client_ip()."',".
            $_SESSION['usr_ID'];
          $qry = $db->update($sql, array());
          $rs = $db->fetch_array($qry);
          $rpta = array("error" => false,"afectados" => 1,"sql"=>$sql);
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
