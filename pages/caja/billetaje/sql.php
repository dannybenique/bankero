<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');
      include_once('../../../includes/web_config.php');

      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      switch ($data->TipoQuery) {
        case "selBilletaje":
          $tabla = array();
          $whr = " and bi.id_coopac=".$web->coopacID." and bi.id_empleado=".$data->usuarioID." and id_moneda=".$data->monedaID;
          $rsCount = $db->fetch_array($db->query("select count(bi.*) as cuenta from bn_billetaje bi where bi.estado=1 ".$whr.";"));

          $sql = "select em.nombrecorto as empleado,bn.nombre as agencia,mo.nombre as moneda,bi.* from bn_billetaje bi join bn_empleados em on (bi.id_empleado=em.id_empleado) join bn_bancos bn on (bi.id_agencia=bn.id) join sis_tipos mo on(bi.id_moneda=mo.id) where bi.estado=1 ".$whr." order by bi.fecha desc limit 25 offset 0;";
          $qry = $db->query($sql);
          if ($db->num_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => $rs["id"],
                "empleado" => $rs["empleado"],
                "agencia" => $rs["agencia"],
                "fecha" => $rs["fecha"],
                "moneda" => $rs["moneda"],
                "total" => $rs["mx_total"]*1
              );
            }
          }
          $rpta = array("tabla"=>$tabla,"cuenta"=>$rsCount["cuenta"]);
          echo json_encode($rpta);
          break;
        case "viewBilletaje":
          $tabla = 0;
          $qry = $db->query("select e.nombrecorto as usuario,s.abrevia as mon_abrevia,b.* from bn_billetaje b join bn_empleados e on (b.id_empleado=e.id_empleado) join sis_tipos s on(b.id_moneda=s.id) where b.id=".$data->billID);
          if ($db->num_rows($qry)) {
            $rs = $db->fetch_array($qry);
            $tabla = array(
              "ID" => $rs["id"],
              "usuarioID" => $rs["id_empleado"],
              "coopacID" => $rs["id_coopac"],
              "agenciaID" => $rs["id_agencia"],
              "monedaID" => $rs["id_moneda"],
              "mon_abrevia" => $rs["mon_abrevia"],
              "usuario" => $rs["usuario"],
              "fecha" => $rs["fecha"],
              "mx_200" => $rs["mx_200"]*1,
              "mx_100" => $rs["mx_100"]*1,
              "mx_50" => $rs["mx_50"]*1,
              "mx_20" => $rs["mx_20"]*1,
              "mx_10" => $rs["mx_10"]*1,
              "mx_5" => $rs["mx_5"]*1,
              "mx_2" => $rs["mx_2"]*1,
              "mx_1" => $rs["mx_1"]*1,
              "mx_05" => $rs["mx_05"]*1,
              "mx_02" => $rs["mx_02"]*1,
              "mx_01" => $rs["mx_01"]*1,
              "mx_total" => $rs["mx_total"]*1,
              "estado" => ($rs["estado"]*1)
            );
          }
          
            //respuesta
            $rpta = array(
            "comboMonedas" => $fn->getComboBox("select id,nombre from sis_tipos where id_padre=1"),
            "comboAgencias" => $fn->getComboBox("select id,nombre from bn_bancos where estado=1 and id_padre=".$web->coopacID),
            "tabla" => $tabla
          );
          echo json_encode($rpta);
          break;
        case "newBilletaje":
          //respuesta
          $rpta = array(
            "comboMonedas" => $fn->getComboBox("select id,nombre from sis_tipos where id_padre=1"),
            "comboAgencias" => $fn->getComboBox("select id,nombre from bn_bancos where estado=1 and id_padre=".$web->coopacID),
            "mon_abrevia" => $db->fetch_array($db->query("select abrevia from sis_tipos where id=".$data->monedaID))["abrevia"],
            "usuario" => $db->fetch_array($db->query("select nombrecorto from bn_empleados where id_empleado=".$data->usuarioID))["nombrecorto"],
            "fecha" => $db->fetch_array($db->query("select now() as fecha"))["fecha"]
          );
          echo json_encode($rpta);
          break;
        case "delBilletaje":
          $params = array();
          for($i=0; $i<count($data->arr); $i++){
            $sql = "update bn_billetaje set estado=0,sys_ip=$2,sys_user=$3,sys_fecha=now() where id=$1";
            $params = array($data->arr[$i],$fn->getClientIP(),$_SESSION['usr_ID']);
            $qry = $db->query_params($sql,$params);
          }
          //respuesta
          $rpta = array("error"=>false, "delete"=>$data->arr);
          echo json_encode($rpta);
          break;
        case "insBilletaje":
          $estado = 1;
          $clientIP = $fn->getClientIP();

          /******agregamos bn_billetaje*******/
          $billID = $db->fetch_array($db->query("select coalesce(max(id)+1,1) as code from bn_billetaje;"))["code"];
          $sql = "insert into bn_billetaje values($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18,$19,$20,$21,now())";
          $params = array(
            $billID,
            $data->usuarioID,
            $web->coopacID,
            $data->agenciaID,
            $data->monedaID,
            $data->fecha,
            $data->mx200,
            $data->mx100,
            $data->mx50,
            $data->mx20,
            $data->mx10,
            $data->mx5,
            $data->mx2,
            $data->mx1,
            $data->mx05,
            $data->mx02,
            $data->mx01,
            $data->mxtotal,
            $estado,
            $clientIP,
            $_SESSION['usr_ID']);
          $rs = $db->fetch_array($db->query_params($sql,$params));
          
          //respuesta
          $rpta = array("error"=>false, "billetajeID"=>$billID);
          echo json_encode($rpta);
          break;
        case "updBilletaje":
          /******actualizamos bn_billetaje*******/
          $billID = $db->fetch_array($db->query("select coalesce(max(id)+1,1) as code from bn_billetaje;"))["code"];
          $sql = "update bn_billetaje set id_agencia=$2,fecha=$3,mx_200=$4,mx_100=$5,mx_50=$6,mx_20=$7,mx_10=$8,mx_5=$9,mx_2=$10,mx_1=$11,mx_05=$12,mx_02=$13,mx_01=$14,mx_total=$15,sys_ip=$16,sys_user=$17,sys_fecha=now() where id=$1;";
          $params = array(
            $data->ID,
            $data->agenciaID,
            $data->fecha,
            $data->mx200,
            $data->mx100,
            $data->mx50,
            $data->mx20,
            $data->mx10,
            $data->mx5,
            $data->mx2,
            $data->mx1,
            $data->mx05,
            $data->mx02,
            $data->mx01,
            $data->mxtotal,
            $fn->getClientIP(),
            $_SESSION['usr_ID']);
          $rs = $db->fetch_array($db->query_params($sql,$params));
          
          //respuesta
          $rpta = array("error"=>false, "billetajeID"=>$billID);
          echo json_encode($rpta);
          break;
        case "StartBilletaje":
          $qry = $db->query_params("select id_rol from bn_usuarios where id_coopac=$1 and id_usuario=$2 and estado=1;",array($web->coopacID,$_SESSION['usr_ID']));
          if ($db->num_rows($qry)>0) { //usuario de una coopac
            $rolID = $db->fetch_array($qry)["id_rol"]; 
          } else {//root
            $rolID = $db->fetch_array($db->query_params("select id_rol from bn_usuarios where estado=1 and id_usuario=$1;",array($_SESSION['usr_ID'])))["id_rol"];
          }
          
          //respuesta
          $rpta = array(
            "root" => "101",
            "rolID" => $rolID,
            "userID" => $_SESSION['usr_ID'],
            "comboUsuarios" => $fn->getComboBox("select id_empleado as id,nombrecorto as nombre from bn_empleados where estado=1 and id_coopac=".$web->coopacID.(($rolID>102)?(" and id_empleado=".$_SESSION['usr_ID']):(""))),
            "comboAgencias" => $fn->getComboBox("select id,nombre from bn_bancos where estado=1 and id_padre=".$web->coopacID),
            "comboMonedas" => $fn->getComboBox("select id,nombre from sis_tipos where id_padre=1")
          );
          echo json_encode($rpta);
          break;
      }
      $db->close();
    } else{
      $resp = array("error"=>true,"data"=>$tabla,"mensaje"=>"ninguna variable en POST");
      echo json_encode($resp);
    }
  } else {
    $resp = array("error"=>true,"mensaje"=>"Caducó la sesion.");
    echo json_encode($resp);
  }
?>
