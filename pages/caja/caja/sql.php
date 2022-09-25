<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');

      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      switch ($data->TipoQuery) {
        case "caja":
          $whr = "";
          $tabla = array();

          //verificar usuario
          $qryusr = $db->select(utf8_decode("select id_usernivel from dbo.tb_usuarios where id_persona=".$_SESSION['usr_ID'].";"));
          $rsusr = $db->fetch_array($qryusr);
          $usuario = array("id_usuario" => $_SESSION['usr_ID'],"usernivel" => $rsusr["id_usernivel"],"admin" => 701,);

          //cargar datos de Vouchers
          if(($data->buscar)!="") { $whr = $whr." and (num_trans like'%".$data->buscar."%') "; }
          $qryCount = $db->select("select count(*) as cuenta from dbo.vw_oper_max_movim where id_usuario=".$_SESSION['usr_ID'].";");
          $rsCount = $db->fetch_array($qryCount);

          $sql = "select top(15)* from dbo.vw_oper_caja where id_usuario=".$_SESSION['usr_ID']." order by ID desc;";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $depositos = 0; $retiros = 0; $otros = 0;
              switch($rs["IO"]){
                case 0: $otros = $rs["importe"]; break;
                case 1: $depositos = $rs["importe"]; break;
                case 2: $retiros = $rs["importe"]; break;
              }
              $tabla[] = array(
                "ID" => $rs["ID"],
                "numtrans"=> str_replace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', $rs["num_trans"]),
                "responsable"=> utf8_encode($rs["responsable"]),
                "producto" => utf8_encode($rs["producto"]),
                "tipo_oper" => utf8_encode($rs["tipo_oper"]),
                "tipo_mov" => utf8_encode($rs["tipo_mov"]),
                "fecha" => ($rs["fecha1"]),
                "depositos" => ($depositos),
                "retiros" => ($retiros),
                "otros" => ($otros),
                "observac" => utf8_encode($rs["observac"])
              );
            }
          }

          //respuesta
          $rpta = array("cuenta"=>$rsCount["cuenta"],"tabla"=>$tabla,"usuario"=>$usuario);
          echo json_encode($rpta);
          break;
        case "cajaADD":
          //obtener fecha actual de operacion
          $rs = $db->fetch_array($db->select("select REPLACE(CONVERT(NVARCHAR, getdate(), 103), ' ', '/') as fecha"));
          $fechaHoy = utf8_encode($rs["fecha"]);

          //obtenemos los productos
          $comboProduc = array();
          $qry =  $db->select("select p.* from dbo.tb_productos p where id_tipo_oper=3 and estado=1;");
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $comboProduc[] = array("ID" => ($rs["ID"]),"nombre" => utf8_encode($rs["nombre"]));
            }
          }

          //obtenemos el tipo de movimiento
          $tablaMovs = array();
          $qry =  $db->select("select * from dbo.tb_tipo_mov where id_padre is not null;");
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tablaMovs[] = array("ID" => ($rs["ID"]),"nombre" => utf8_encode($rs["detalle"]));
            }
          }

          //obtenemos los documentos de caja
          $tablaDocs = array();
          $qry =  $db->select("select * from dbo.tb_tipo_cajadocs where ID>1 order by ID;");
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tablaDocs[] = array("ID" => ($rs["ID"]),"nombre" => utf8_encode($rs["nombre"]));
            }
          }

          //respuesta
          $rpta = array("fecha"=>$fechaHoy,"productos"=>$comboProduc,"movs"=>$tablaMovs,"cajaDocs"=>$tablaDocs);
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
