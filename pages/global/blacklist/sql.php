<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');

      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      switch ($data->TipoQuery) {
        //blacklist
        case "agencias_tipos":
          //respuesta
          $rpta = array(
            "agencias" => getComboBox("select id,nombre from dbo.tb_agencias order by id"),
            "agenciaID" => $_SESSION['usr_agenciaID'],
            "tipos" => getComboBox("select id,nombre from dbo.tb_mastertipos where id_padre=9 order by orden"));
          echo json_encode($rpta);
          break;
        case "selBlacklist":
          $whr = "";
          $tabla = array();

          //cargar datos de Blacklist
          if(($data->miBuscar)!="") { $whr = " and (persona like'%".$data->miBuscar."%' or dni like'%".$data->miBuscar."%') " ;}
          if(($data->agenciaID) > 0) { $whr = $whr." and id_agencia=".($data->agenciaID); }
          $sql = "select count(*) as cuenta from dbo.vw_blacklist where 0=0 ".$whr;
          $rsCount = $db->fetch_array($db->select($sql));

          $qry = $db->select("select ".(($data->verTodos==0)?("top(15)"):(""))."* from dbo.vw_blacklist where 0=0 ".$whr." order by persona;");
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => $rs["ID"],
                "fecha"=> $rs["fecha"],
                "DNI"=> str_replace($data->miBuscar, '<span style="background:yellow;">'.$data->miBuscar.'</span>', $rs["DNI"]),
                "persona" => str_replace($data->miBuscar, '<span style="background:yellow;">'.$data->miBuscar.'</span>', ($rs["persona"])),
                "agencia" => ($rs["agencia"]),
                "direccion" => ($rs["direccion"]),
                "tipoObservac" => ($rs["tipoObservac"]),
                "observac" => ($rs["observac"])
              );
            }
          }
          //respuesta
          $rpta = array("cuenta"=>$rsCount["cuenta"],"tabla"=>$tabla,"usernivelID"=>$_SESSION['usr_usernivelID']);
          echo json_encode($rpta);
          break;
        case "editBlacklist":
          if ($data->personaID>0){
            $qry = $db->select("select * from dbo.vw_blacklist where ID=".$data->personaID);
            if ($db->has_rows($qry)) { $rs = $db->fetch_array($qry); }

            $tablaPers = getOnePersona($data->personaID);
            $tablaBlack = array(
              "id_agencia" => $rs["id_agencia"],
              "id_tipo"    => $rs["id_tipoblacklist"],
              "agencia"    => ($rs["agencia"]),
              "fecha"      => $rs["fecha"],
              "observac"   => ($rs["observac"]),
              "sysusuario" => ($rs["sysusuario"])
            );
          } else {
            $tablaPers = array(
              "ID"=>0,
              "persona"=>"",
              "nroDNI"=>substr($data->personaID,1),
              "region"=>"",
              "provincia"=>"",
              "distrito"=>"",
              "direccion"=>"",
              "referencia"=>"",
              "medidorluz"=>"",
              "tipovivienda"=>""
              );
            $tablaBlack = array(
              "id_agencia" => "",
              "id_tipo"    => "",
              "agencia"    => "",
              "fecha"      => "",
              "observac"   => "",
              "sysusuario" => "");
          }
          $rpta = array(
            "tablaPers"  => $tablaPers,
            "tablaBlack" => $tablaBlack,
            "agenciaID"  => $_SESSION['usr_agenciaID'],
            "agencias"   => getComboBox("select id,nombre from dbo.tb_agencias order by id"),
            "tipos"      => getComboBox("select id,nombre from dbo.tb_mastertipos where id_padre=9 order by orden;"));
          echo json_encode($rpta);
          break;
        case "execBlacklist":
          $rpta = array();
          $params = array();
          $sql = "exec dbo.sp_blacklist '".$data->commandSQL."',".
            ($data->personaID).",".
            ($data->agenciaID).",".
            ($data->tipoblkID).",'".
            ($data->fecha)."','".
            ($data->observac)."','".
            get_client_ip()."',".
            $_SESSION['usr_ID'];

          $qqy = $db->update($sql, $params);
          $rs = $db->fetch_array($qqy);

          $qry = $db->select("select * from dbo.vw_blacklist where ID=".$data->personaID);
          if ($db->has_rows($qry)) { $rs = $db->fetch_array($qry); }
          $tablaBlack = array(
            "id_agencia" => $rs["id_agencia"],
            "agencia" => ($rs["agencia"]),
            "fecha" => $rs["fecha"],
            "observac" => ($rs["observac"]),
            "sysusuario" => ($rs["sysusuario"])
          );
          $rpta = array("error"=>0, "tablaBlack"=>$tablaBlack);
          echo json_encode($rpta);
          break;
        case "delBlacklist":
          for($xx=0; $xx<count($data->IDs); $xx++){
            $sql = "exec dbo.sp_delete 'tb_blacklist',".$data->IDs[$xx].",'','".get_client_ip()."',".$_SESSION['usr_ID'];
            $qry = $db->delete($sql, array());
          }
          $rpta = array("error" => false,"delete" => count($data->IDs));
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
