<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');
      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      switch ($data->TipoQuery) {
        case "postulantes":
          $whr = "";
          $tabla = array();
          $combo = array();

          //cargar el combo de agencias en caso de ser carga inicial
          if(isset($data->reset)){
            $qry = $db->select("select * from dbo.tb_agencias order by ID");
            if ($db->has_rows($qry)) {
              for($xx = 0; $xx<$db->num_rows($qry); $xx++){
                $rs = $db->fetch_array($qry);
                $combo[] = array(
                  "ID" => $rs["ID"],
                  "codigo" => $rs["codigo"],
                  "abrev" => $rs["abrev"],
                  "nombre" => $rs["nombre"],
                );
              }
            }
          }

          //cargar datos de postulantes RRHH
          if(($data->miBuscar)!="") { $whr = " and (postulante like'%".$data->miBuscar."%' or dni like'%".$data->miBuscar."%') " ;}
          if(($data->agenciaID) > 0) { $whr = $whr." and id_agencia=".($data->agenciaID); }
          $qryCount = $db->select(utf8_decode("select count(*) as cuenta from dbo.vw_postulantes where 0=0 ".$whr.";"));
          $rsCount = $db->fetch_array($qryCount);

          $sql = "select top(15)* from dbo.vw_postulantes where 0=0 ".$whr." order by postulante;";
          $qry = $db->select(utf8_decode($sql));
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => $rs["id_persona"],
                "DNI"=> str_replace($data->miBuscar, '<span style="background:yellow;">'.$data->miBuscar.'</span>', $rs["DNI"]),
                "postulante" => str_replace($data->miBuscar, '<span style="background:yellow;">'.$data->miBuscar.'</span>', utf8_encode($rs["postulante"])),
                "celular" => utf8_encode($rs["celular"]),
                "cargo" => utf8_encode($rs["cargo"]),
                "condicion" => utf8_encode($rs["condicion"]),
                "agencia" => utf8_encode($rs["agencia"]),
                "fecha" => utf8_encode($rs["fecha"])
              );
            }
          }

          //respuesta
          if(isset($data->reset)){
            $rpta = array("cuenta"=>$rsCount["cuenta"],"tabla"=>$tabla,"combo"=>$combo);
          }else{
            $rpta = array("cuenta"=>$rsCount["cuenta"],"tabla"=>$tabla);
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
