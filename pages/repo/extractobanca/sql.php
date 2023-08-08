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
        case "selMovim":
          $tabla = array();
          $data->buscar = pg_escape_string($data->buscar);
          $sql = "select mv.*, b.nombre as agencia,mo.nombre as moneda,mo.abrevia as mo_abrevia,to_char(mv.fecha,'DD/MM/YYYY') as fechamov,to_char(mv.fecha,'HH24:MI:SS') as horamov, CASE pe.tipo_persona WHEN 1 THEN concat(pe.ap_paterno, ' ', pe.ap_materno, ', ', pe.nombres) WHEN 2 THEN pe.nombres::text END AS socio,em.nombrecorto as cajera from bn_movim mv join bn_bancos b on mv.id_agencia=b.id join sis_tipos mo on mv.id_moneda=mo.id join personas pe on mv.id_socio=pe.id join bn_empleados em on mv.id_cajera=em.id_empleado where mv.id_coopac=$1;";
          $qry = $db->query_params($sql,array($web->coopacID));
          if ($db->num_rows($qry)>0) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => $rs["id"],
                "codigo" => str_ireplace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', $rs["codigo"]),
                "agencia" => $rs["agencia"],
                "cajera" => strtoupper($rs["cajera"]),
                "importe" => $rs["importe"],
                "socio" => $rs["socio"],
                "mo_abrevia" => $rs["mo_abrevia"],
                "id_tipo_oper" => $rs["id_tipo_oper"],
                "fecha" => $rs["fechamov"],
                "hora" => $rs["horamov"],
                "estado" => $rs["estado"]
              );
            }
          }
          $rpta = array("pagos"=>$tabla);
          echo json_encode($rpta);
          break;
        case "viewMovim":
          //cabecera
          $cabecera = 0;
          $tipo_oper = 0;
          $sql = "select m.*,b.nombre as agencia,t.nombre as tipo_oper,o.nombre as moneda,o.abrevia as mon_abrevia,to_char(fecha,'DD/MM/YYYY') as fechamov,to_char(fecha,'HH24:MI:SS') as horamov,em.nombrecorto,CASE p.tipo_persona WHEN 1 THEN concat(p.ap_paterno, ' ', p.ap_materno, ', ', p.nombres) WHEN 2 THEN p.nombres::text END AS socio,ax.nombre as tipo_dui,p.nro_dui from bn_movim m join bn_bancos b on m.id_agencia=b.id join sis_tipos t on m.id_tipo_oper=t.id join personas p on m.id_socio=p.id join sis_tipos o on m.id_moneda=o.id join personas_tipos_aux ax on p.id_dui=ax.id  join bn_empleados em on m.id_cajera=em.id_empleado where m.id=$1;";
          $params = array($data->voucherID);
          $qry = $db->query_params($sql,$params);
          if ($db->num_rows($qry)) {
            $rs = $db->fetch_array($qry);
            $tipo_oper = $rs["id_tipo_oper"]; 
            $cabecera = array(
              "ID" => $rs["id"],
              "codigo" => $rs["codigo"],
              "fecha" => $rs["fechamov"],
              "hora" => $rs["horamov"],
              "socio" => $rs["socio"],
              "tipodui" => $rs["tipo_dui"],
              "nrodui" => $rs["nro_dui"],
              "cajera" => $rs["nombrecorto"],
              "agencia" => $rs["agencia"],
              "moneda" => $rs["moneda"],
              "mon_abrevia" => $rs["mon_abrevia"],
              "importe" => $rs["importe"],
              "tipo_oper" => $rs["tipo_oper"]
            );
          }
          
          //detalle
          $detalle = array();
          $sql = "select d.*,x.nombre as tipo_mov,concat(pr.nombre,' :: ',pt.codigo) as producto,pt.tasa_cred from bn_movim_det d join sis_mov x on d.id_tipo_mov=x.id join bn_productos pr on d.id_producto=pr.id join bn_prestamos pt on d.id_tabla=pt.id where d.id_movim=$1 order by item;";
          $params = array($data->voucherID);
          $qry = $db->query_params($sql,$params);
          if ($db->num_rows($qry)) {
            $rs = $db->fetch_array($qry);
            $detalle[] = array(
              "ID" => $rs["id"],
              "item" => $rs["item"],
              "tipo_mov" => $rs["tipo_mov"],
              "producto" => $rs["producto"],
              "importe" => $rs["importe"]
            );
          }

          //respuesta
          $rpta = array('cab'=> $cabecera, 'deta'=> $detalle);
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
          $sql = "insert into bn_productos values ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,now())";
          $params = array($id, $codigo, pg_escape_string($data->nombre), pg_escape_string($data->abrevia), $id_tipo_oper, $web->coopacID, $data->tipoID, 1, $fn->getClientIP(), $_SESSION['usr_ID']);

          $qry = $db->query_params($sql,$params);
          $rpta = array("error" => false,"ingresados" => 1);
          echo json_encode($rpta);
          break;
        case "updProducto":
          $id_tipo_oper = $db->fetch_array($db->query_params("select id_tipo_oper from bn_productos where id=$1",array($data->tipoID)))["id_tipo_oper"];
          $sql = "update bn_productos set nombre=$2,abrevia=$3,id_tipo_oper=$4,id_padre=$5,sys_ip=$6,sys_user=$7,sys_fecha=now() where id=$1";
          $params = array(
            $data->ID,
            pg_escape_string($data->nombre),
            pg_escape_string($data->abrevia),
            $id_tipo_oper,
            $data->tipoID,
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
