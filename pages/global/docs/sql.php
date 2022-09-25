<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');

      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      switch ($data->TipoQuery) {
        //****************workers****************
        case "selDocsDirPlan":
          $whr = "";
          $abrev = "";
          $tabla = array();

          $sql = "select * from tb_docs where id_tipo=1 order by fecha,nombre;";
          $qry = $db->select(($sql));
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => ($rs["ID"]),
                "codigo" => ($rs["codigo"]),
                "nombre" => ($rs["nombre"]),
                "url" => ($rs["url"]),
                "fecha" => ($rs["fecha"]),
                "estado" => ($rs["estado"])
              );
            }
          }

          //respuesta
          $rpta = array("tabla"=>$tabla);
          echo json_encode($rpta);
          break;
        case "selDocsAdmFin":
          $whr = "";
          $abrev = "";
          $tabla = array();

          $sql = "select * from tb_docs where id_tipo=2 order by fecha,nombre;";
          $qry = $db->select(($sql));
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => ($rs["ID"]),
                "codigo" => ($rs["codigo"]),
                "nombre" => ($rs["nombre"]),
                "url" => ($rs["url"]),
                "fecha" => ($rs["fecha"]),
                "estado" => ($rs["estado"])
              );
            }
          }

          //respuesta
          $rpta = array("tabla"=>$tabla);
          echo json_encode($rpta);
          break;
        case "selDocsRRHH":
          $whr = "";
          $abrev = "";
          $tabla = array();

          $sql = "select * from tb_docs where id_tipo=3 order by fecha,nombre;";
          $qry = $db->select(($sql));
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => ($rs["ID"]),
                "codigo" => ($rs["codigo"]),
                "nombre" => ($rs["nombre"]),
                "url" => ($rs["url"]),
                "fecha" => ($rs["fecha"]),
                "estado" => ($rs["estado"])
              );
            }
          }

          //respuesta
          $rpta = array("tabla"=>$tabla);
          echo json_encode($rpta);
          break;
        case "selDocsLogis":
          $whr = "";
          $abrev = "";
          $tabla = array();

          $sql = "select * from tb_docs where id_tipo=4 order by fecha,nombre;";
          $qry = $db->select(($sql));
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => ($rs["ID"]),
                "codigo" => ($rs["codigo"]),
                "nombre" => ($rs["nombre"]),
                "url" => ($rs["url"]),
                "fecha" => ($rs["fecha"]),
                "estado" => ($rs["estado"])
              );
            }
          }

          //respuesta
          $rpta = array("tabla"=>$tabla);
          echo json_encode($rpta);
          break;
        case "selDocsCred":
          $whr = "";
          $abrev = "";
          $tabla = array();

          $sql = "select * from tb_docs where id_tipo=5 order by fecha,nombre;";
          $qry = $db->select(($sql));
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => ($rs["ID"]),
                "codigo" => ($rs["codigo"]),
                "nombre" => ($rs["nombre"]),
                "url" => ($rs["url"]),
                "fecha" => ($rs["fecha"]),
                "estado" => ($rs["estado"])
              );
            }
          }

          //respuesta
          $rpta = array("tabla"=>$tabla);
          echo json_encode($rpta);
          break;
        case "selDocsCredInfo":
          $whr = "";
          $abrev = "";
          $tabla = array();

          $sql = "select * from tb_docs where id_tipo=51 order by fecha,nombre;";
          $qry = $db->select(($sql));
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => ($rs["ID"]),
                "codigo" => ($rs["codigo"]),
                "nombre" => ($rs["nombre"]),
                "url" => ($rs["url"]),
                "fecha" => ($rs["fecha"]),
                "estado" => ($rs["estado"])
              );
            }
          }

          //respuesta
          $rpta = array("tabla"=>$tabla);
          echo json_encode($rpta);
          break;
        case "selDocsOper":
          $whr = "";
          $abrev = "";
          $tabla = array();

          $sql = "select * from tb_docs where id_tipo=6 order by fecha,nombre;";
          $qry = $db->select(($sql));
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => ($rs["ID"]),
                "codigo" => ($rs["codigo"]),
                "nombre" => ($rs["nombre"]),
                "url" => ($rs["url"]),
                "fecha" => ($rs["fecha"]),
                "estado" => ($rs["estado"])
              );
            }
          }

          //respuesta
          $rpta = array("tabla"=>$tabla);
          echo json_encode($rpta);
          break;
        case "selDocsRecup":
          $whr = "";
          $abrev = "";
          $tabla = array();

          $sql = "select * from tb_docs where id_tipo=8 order by fecha,nombre;";
          $qry = $db->select(($sql));
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => ($rs["ID"]),
                "codigo" => ($rs["codigo"]),
                "nombre" => ($rs["nombre"]),
                "url" => ($rs["url"]),
                "fecha" => ($rs["fecha"]),
                "estado" => ($rs["estado"])
              );
            }
          }

          //respuesta
          $rpta = array("tabla"=>$tabla);
          echo json_encode($rpta);
          break;
        case "selDocsTIC":
          $whr = "";
          $abrev = "";
          $tabla = array();

          $sql = "select * from tb_docs where id_tipo=9 order by fecha,nombre;";
          $qry = $db->select(($sql));
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => ($rs["ID"]),
                "codigo" => ($rs["codigo"]),
                "nombre" => ($rs["nombre"]),
                "url" => ($rs["url"]),
                "fecha" => ($rs["fecha"]),
                "estado" => ($rs["estado"])
              );
            }
          }

          //respuesta
          $rpta = array("tabla"=>$tabla);
          echo json_encode($rpta);
          break;
        case "selDocsAhorros":
          $whr = "";
          $abrev = "";
          $tabla = array();

          $sql = "select * from tb_docs where id_tipo=10 order by fecha,nombre;";
          $qry = $db->select(($sql));
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => ($rs["ID"]),
                "codigo" => ($rs["codigo"]),
                "nombre" => ($rs["nombre"]),
                "url" => ($rs["url"]),
                "fecha" => ($rs["fecha"]),
                "estado" => ($rs["estado"])
              );
            }
          }

          //respuesta
          $rpta = array("tabla"=>$tabla);
          echo json_encode($rpta);
          break;
        case "selDocsLegal":
          $whr = "";
          $abrev = "";
          $tabla = array();

          $sql = "select * from tb_docs where id_tipo=12 order by fecha,nombre;";
          $qry = $db->select(($sql));
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => ($rs["ID"]),
                "codigo" => ($rs["codigo"]),
                "nombre" => ($rs["nombre"]),
                "url" => ($rs["url"]),
                "fecha" => ($rs["fecha"]),
                "estado" => ($rs["estado"])
              );
            }
          }

          //respuesta
          $rpta = array("tabla"=>$tabla);
          echo json_encode($rpta);
          break;
        case "selDocsGesCal":
          $whr = "";
          $abrev = "";
          $tabla = array();

          $sql = "select * from tb_docs where id_tipo=13 order by fecha,nombre;";
          $qry = $db->select(($sql));
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => ($rs["ID"]),
                "codigo" => ($rs["codigo"]),
                "nombre" => ($rs["nombre"]),
                "url" => ($rs["url"]),
                "fecha" => ($rs["fecha"]),
                "estado" => ($rs["estado"])
              );
            }
          }

          //respuesta
          $rpta = array("tabla"=>$tabla);
          echo json_encode($rpta);
          break;
        case "selDocsGesRie":
          $whr = "";
          $abrev = "";
          $tabla = array();

          $sql = "select * from tb_docs where id_tipo=14 order by fecha,nombre;";
          $qry = $db->select(($sql));
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => ($rs["ID"]),
                "codigo" => ($rs["codigo"]),
                "nombre" => ($rs["nombre"]),
                "url" => ($rs["url"]),
                "fecha" => ($rs["fecha"]),
                "estado" => ($rs["estado"])
              );
            }
          }

          //respuesta
          $rpta = array("tabla"=>$tabla);
          echo json_encode($rpta);
          break;
      }
      $db->close();
    } else {
      $resp = array("error"=>true,"mensaje"=>"ninguna variable en POST");
      echo json_encode($resp);
    }
  } else {
    $resp = array("error"=>true,"mensaje"=>"Caducó la sesion.");
    echo json_encode($resp);
  }
?>
