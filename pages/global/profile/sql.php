<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');

      $data = json_decode($_REQUEST['appSQL']);

      //****************personas****************
      switch ($data->TipoQuery) {
        case "selPerfil":
          $sql = "select p.id,p.nombres,p.ap_paterno,p.ap_materno,dc.nombre AS doc,p.nro_dui,sx.nombre AS sexo,gi.nombre AS ginstruc,ec.nombre AS ecivil,p.direccion,p.referencia,p.fecha_nac,p.telefijo,p.celular,p.email,p.urlfoto,p.ocupacion, cg.nombre AS cargo,e.nombrecorto, e.codigo, ag.nombre AS agencia, ub.region, ub.provincia, ub.distrito,e.estado,e.observac ".
                 "from bn_empleados e ".
                 "join personas p on (e.id_empleado=p.id) ".
                 "join vw_ubigeo ub on (p.id_ubigeo=ub.id_distrito) ".
                 "join bn_bancos ag on (e.id_agencia=ag.id) ".
                 "join sis_tipos cg on (e.id_cargo=cg.id) ".
                 "join personas_tipos_aux sx on (p.id_sexo=sx.id) ".
                 "join personas_tipos_aux dc on (p.id_dui=dc.id) ".
                 "join personas_tipos_aux gi on (p.id_ginstruccion=gi.id) ".
                 "join personas_tipos_aux ec on (p.id_ecivil=ec.id) ".
                 "where e.id_empleado=".$data->userID;
          $qry = $db->query_all($sql);
          if ($qry) { $rs = reset($qry); }
          $perfil = array(
            "fecha_nac" => $rs["fecha_nac"],
            "doc_dui" => $rs["doc"]." - ".$rs["nro_dui"],
            "celular" => $rs["celular"],
            "agencia" => ($rs["agencia"]),
            "correo" => $rs["email"],
            "direccion" => ($rs["direccion"])."<br/>".($rs["distrito"]).", ".($rs["provincia"]).", ".($rs["region"]),
            "observac" => ($rs["observac"]),
            "nombres" => ($rs["nombres"]),
            "apellidos" => ($rs["ap_paterno"])." ".($rs["ap_materno"]),
            "instruccion" => $rs["ginstruc"],
            "ecivil" => $rs["ecivil"],
            "sexo" => $rs["sexo"],
            "ocupacion" => $rs["ocupacion"]);

          //respuesta
          $rpta = array("perfil"=>$perfil,"user"=>$_SESSION['usr_data']);
          echo json_encode($rpta);
          break;
        case "updPassword": //cambiar password de usuario
          //verificamos nivel de usuario
          $params = [":passw"=>$data->pass,":id"=>$data->userID];
          $sql = "update bn_usuarios set passw=:passw where id=:id;";
          $qry = $db->query_all($sql, $params);
          $user = ($qry) ? (array("error" => false,"resp" => "Se actualizo el passw")) : (array("error" => true,"resp" => "Fallo actualizacion"));
          
          //respuesta
          $rpta  = $user;
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
