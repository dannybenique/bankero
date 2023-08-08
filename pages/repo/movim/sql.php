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
          $sql = "select *,to_char(fecha,'HH24:MI:SS') as horamov from vw_movim where id_coopac=$1 and id_agencia=$2 and id_moneda=$3 and id_cajera=$4 and to_char(fecha,'YYYYMMDD')=$5;";
          $qry = $db->query_params($sql,array($web->coopacID,$data->agenciaID,$data->monedaID,$data->usuarioID,$data->fecha));
          if ($db->num_rows($qry)>0) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => $rs["id"],
                "voucher" => $rs["codigo"],
                "codsocio" => $rs["codsocio"],
                "socio" => $rs["socio"],
                "codprod" => $rs["codprod"],
                "producto" => $rs["producto"],
                "codmov" => $rs["codmov"],
                "movim" => $rs["movim"],
                "ingreso" => ($rs["in_out"]==1)?($rs["importe_det"]*1):(0),
                "salida" => ($rs["in_out"]==0)?($rs["importe_det"]*1):(0),
                "hora" => $rs["horamov"]
              );
            }
          }
          $rpta = array("movim"=>$tabla);
          echo json_encode($rpta);
          break;
        case "viewMovim":
          //cabecera
          $cabecera = 0;
          $tipo_oper = 0;
          $sql = "select m.*,b.nombre as agencia,t.nombre as tipo_oper,o.nombre as moneda,o.abrevia as mon_abrevia,to_char(fecha,'DD/MM/YYYY') as fechamov,to_char(fecha,'HH24:MI:SS') as horamov,em.nombrecorto,fn_get_persona(p.tipo_persona,p.ap_paterno,p.ap_materno,p.nombres) AS socio,ax.nombre as tipo_dui,p.nro_dui from bn_movim m join bn_bancos b on m.id_agencia=b.id join sis_tipos t on m.id_tipo_oper=t.id join personas p on m.id_socio=p.id join sis_tipos o on m.id_moneda=o.id join personas_tipos_aux ax on p.id_dui=ax.id  join bn_empleados em on m.id_cajera=em.id_empleado where m.id=$1;";
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
          $sql = "select d.*,x.nombre as tipo_mov,concat(pr.nombre,' :: ',pt.codigo) as producto,pt.tasa_cred from bn_movim_det d join sis_mov x on d.id_tipo_mov=x.id join bn_productos pr on d.id_producto=pr.id left join bn_prestamos pt on d.id_tabla=pt.id where d.id_movim=$1 order by item;";
          $params = array($data->voucherID);
          $qry = $db->query_params($sql,$params);
          if ($db->num_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $detalle[] = array(
                "ID" => $rs["id"],
                "item" => $rs["item"],
                "tipo_mov" => $rs["tipo_mov"],
                "producto" => $rs["producto"],
                "importe" => $rs["importe"]
              );
            }
          }

          //respuesta
          $rpta = array('cab'=> $cabecera, 'deta'=> $detalle);
          echo json_encode($rpta);
          break;
        case "StartMovim":
          $qry = $db->query_params("select id_rol from bn_usuarios where id_coopac=$1 and id_usuario=$2 and estado=1;",array($web->coopacID,$_SESSION['usr_ID']));
          if ($db->num_rows($qry)>0) { //usuario de una coopac
            $rolID = $db->fetch_array($qry)["id_rol"]; 
          } else {//root
            $rolID = $db->fetch_array($db->query_params("select id_rol from bn_usuarios where estado=1 and id_usuario=$1;",array($_SESSION['usr_ID'])))["id_rol"];
          }
          
          //respuesta
          $rpta = array(
            "rolID" => $rolID,
            "comboUsuarios" => $fn->getComboBox("select id_empleado as id,nombrecorto as nombre from bn_empleados where estado=1 and id_coopac=".$web->coopacID.(($rolID>102)?(" and id_empleado=".$_SESSION['usr_ID']):(""))),
            "comboAgencias" => $fn->getComboBox("select id,nombre from bn_bancos where estado=1 and id_padre=".$web->coopacID),
            "comboMonedas" => $fn->getComboBox("select id,nombre from sis_tipos where id_padre=1"),
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
