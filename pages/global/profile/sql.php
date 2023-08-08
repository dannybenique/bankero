<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');

      $data = json_decode($_REQUEST['appSQL']);

      //****************personas****************
      switch ($data->TipoQuery) {
        case "selProfile":
          $qry = $db->select("select pr.ID, pr.nombres, pr.ap_paterno, pr.ap_materno, dc.nombre AS doc, pr.DNI,sx.nombre AS sexo, gi.nombre AS gInstruc, ec.nombre AS ecivil, pr.direccion, pr.referencia,REPLACE(CONVERT(NVARCHAR, pr.fecha_nac, 103), ' ', '/') AS fecha_nac, pr.telefijo, pr.celular, pr.email, pr.urlfoto, pr.ocupacion, cg.nombre AS cargo,wr.nombrecorto, wr.codigo, ag.nombre AS agencia, ub.region, ub.provincia, ub.distrito, wr.estado, pr.observac FROM dbo.tb_workers wr,dbo.tb_personas pr,dbo.vw_ubigeo ub,dbo.tb_agencias ag,dbo.tb_mastertipos cg,dbo.tb_mastertipos sx,dbo.tb_mastertipos dc,dbo.tb_mastertipos gi,dbo.tb_mastertipos ec where wr.id_persona=pr.ID and pr.id_ubigeo=ub.id_distrito and wr.id_agencia=ag.ID and wr.id_cargo=cg.ID and pr.id_sexo=sx.ID and pr.id_doc=dc.ID and pr.id_ginstruc=gi.ID and pr.id_ecivil=ec.ID and pr.ID=".$data->miID);
          if ($db->has_rows($qry)) { $rs = $db->fetch_array($qry); }
          $rpta = array(
            "nacimiento" => $rs["fecha_nac"],
            "documento" => $rs["doc"]." - ".$rs["DNI"],
            "celular" => $rs["celular"],
            "agencia" => utf8_encode($rs["agencia"]),

            "correo" => $rs["email"],
            "direccion" => utf8_encode($rs["direccion"])."<br/>".utf8_encode($rs["distrito"]).", ".utf8_encode($rs["provincia"]).", ".utf8_encode($rs["region"]),
            "observa" => utf8_encode($rs["observac"]),

            "nombres" => utf8_encode($rs["nombres"]),
            "apellidos" => utf8_encode($rs["ap_paterno"])." ".utf8_encode($rs["ap_materno"]),
            "instruccion" => $rs["gInstruc"],
            "ecivil" => $rs["ecivil"],
            "sexo" => $rs["sexo"],
            "ocupacion" => $rs["ocupacion"]);
          echo json_encode($rpta);
          break;
        case "updPassword": //cambiar password de usuario
          //verificamos nivel de usuario
          $params = array();
          $sql = "update dbo.tb_usuarios set passw='".utf8_decode($data->pass)."',passtxt='".utf8_decode($data->passtxt)."' where id_persona=".$data->userID.";";
          $qry = $db->update($sql, $params);
          if($qry) {
            $rpta = array(
              "error" => false,
              "resp" => "Se actualizo el passw"
            );
          } else{
            $rpta = array(
              "error" => true,
              "resp" => "Fallo actualizacion"
            );
          }
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
