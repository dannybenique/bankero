<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');
      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      switch ($data->TipoQuery) {
        case "selVouchers":
          $whr = "";
          $tabla = array();

          //verificar usuario
          $qryusr = $db->select(utf8_decode("select id_usernivel from dbo.tb_usuarios where id_persona=".$_SESSION['usr_ID'].";"));
          $rsusr = $db->fetch_array($qryusr);
          $usuario = array("id_usuario" => $_SESSION['usr_ID'],"usernivel" => $rsusr["id_usernivel"],"admin" => 701,);

          //cargar datos de Vouchers
          if(($data->tipo_oper)>0) { $whr = " and id_tipo_oper=".$data->tipo_oper; }
          if(($data->buscar)!="") { $whr = $whr." and (num_trans like'%".$data->buscar."%') "; }
          $qryCount = $db->select("select count(*) as cuenta from dbo.vw_oper_max_movim where 1=1 ".$whr.";");
          $rsCount = $db->fetch_array($qryCount);

          $sql = "select top(15)* from dbo.vw_oper_max_movim where 1=1 ".$whr." order by ID desc;";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $socio = ($rs["id_doc"]==501) ? ($rs["socio"]) : ($rs["razonsocial"]);
              $tabla[] = array(
                "ID" => $rs["ID"],
                "numtrans"=> str_replace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', $rs["num_trans"]),
                "socio"=> utf8_encode($socio),
                "responsable"=> utf8_encode($rs["responsable"]),
                "tipo_oper" => ($rs["tipo_oper"]),
                "fecha" => ($rs["fecha1"]),
                "observac" => utf8_encode($rs["observac"])
              );
            }
          }

          //respuesta
          $rpta = array("cuenta"=>$rsCount["cuenta"],"tabla"=>$tabla,"usuario"=>$usuario);
          echo json_encode($rpta);
          break;
        case "editVoucher":
          //verificar usuario
          $sql = utf8_decode("select id_usernivel from dbo.tb_usuarios where id_persona=".$_SESSION['usr_ID'].";");
          $rsusr = $db->fetch_array($db->select($sql));
          $usuario = array(
            "id_usuario" => $_SESSION['usr_ID'],
            "usernivel" => $rsusr["id_usernivel"],
            "admin" => 701,
          );

          //verificar cabecera de voucher
          $sql = "select * from dbo.vw_oper_max_movim where ID=".$data->voucherID;
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) { $rs = $db->fetch_array($qry); }
          $cabecera = array(
            "ID" => ($rs["ID"]),
            "numtrans" => ($rs["num_trans"]),
            "responsable"=> utf8_encode($rs["responsable"]),
            "socio"=> (($rs["id_doc"]==501) ? utf8_encode($rs["socio"]) : utf8_encode($rs["razonsocial"]))." - ".utf8_encode($rs["doc"]).": ".($rs["DNI"]),
            "id_tipo_oper" => ($rs["id_tipo_oper"]),
            "tipo_oper" => utf8_encode($rs["tipo_oper"]),
            "fecha" => ($rs["fecha1"]),
            "observac" => utf8_encode($rs["observac"])
          );
          //verificar detalle de voucher
          $detalle = array();
          $qry = $db->select("select * from dbo.vw_oper_movimientos where id_maxmovim=".$data->voucherID." order by item;");
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $detalle[] = array(
                "ID" => ($rs["ID"]),
                "item" => ($rs["item"]),
                "detalle" => ($rs["detalle"]),
                "importe" => ($rs["importe"])
              );
            }
          }

          $rpta = array("cabecera"=>$cabecera,"detalle"=>$detalle,"usuario"=>$usuario);
          echo json_encode($rpta);
          break;
        case "delVoucher":
          $params = array();
          $sql = "exec dbo.sp_oper_movimientos 'DEL',".$data->voucherID.",'".get_client_ip()."',".$_SESSION['usr_ID'];
          $qry = $db->delete($sql, $params);
          $rpta = array("error" => false, "Delete" => 1);
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
