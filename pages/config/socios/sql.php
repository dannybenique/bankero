<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');

      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      //****************personas****************
      switch ($data->TipoQuery) {
        case "selSocios":
          $whr = "";
          $tabla = array();

          //cargar datos de Socios
          if(($data->miBuscar)!="") { $whr = " and (socio like'%".$data->miBuscar."%' or dni like'%".$data->miBuscar."%') " ;}
          if(($data->agenciaID) > 0) { $whr = $whr." and id_agencia=".($data->agenciaID); }
          $qryCount = $db->select(utf8_decode("select count(*) as cuenta from dbo.vw_socios where 1=1 ".$whr.";"));
          $rsCount = $db->fetch_array($qryCount);

          $qry = $db->select(utf8_decode("select top(15)* from dbo.vw_socios where 1=1 ".$whr." order by socio;"));
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              if($rs["id_doc"]==502) { $socio = utf8_encode($rs["nombres"]);} else { $socio = utf8_encode($rs["socio"]); }
              $tabla[] = array(
                "ID" => $rs["id_persona"],
                "codigo"=> $rs["codigo"],
                "DNI"=> str_replace($data->miBuscar, '<span style="background:yellow;">'.$data->miBuscar.'</span>', $rs["DNI"]),
                "socio" => str_replace($data->miBuscar, '<span style="background:yellow;">'.$data->miBuscar.'</span>', $socio),
                "estado" => ($rs["estado"]),
                "agencia" => utf8_encode($rs["agencia"])
              );
            }
          }

          //respuesta
          $rpta = array("cuenta"=>$rsCount["cuenta"],"tabla"=>$tabla);
          echo json_encode($rpta);
          break;
        case "editSocio":
          //verificar usuario
          $qry = $db->select(utf8_decode("select id_usernivel from dbo.tb_usuarios where id_persona=".$_SESSION['usr_ID'].";"));
          $rs = $db->fetch_array($qry);
          $tablaUser = array("usernivel"=>$rs["id_usernivel"],"admin"=>701);

          //cargar datos de persona
          $tablaPers = getOnePersona($data->personaID);
          $qry =  $db->select("select codigo,id_agencia,agencia from dbo.vw_socios where id_persona=".$data->personaID);
          $rs = $db->fetch_array($qry);
          $tablaPers["codigo"] = $rs["codigo"];
          $tablaPers["agencia"] = $rs["agencia"];
          $tablaPers["agenciaID"] = $rs["id_agencia"];

          if($data->fullQuery==1){
            $tablaSoc = getOneSocio($data->personaID); //cargar datos de tb_socios
            $tablaLabo = getOneLaboral($data->personaID); //cargar datos Laborales
            $tablaCony = getOneConyuge($data->personaID); //cargar datos de conyuge
            $rpta = array('tablaSoc'=>$tablaSoc,'tablaPers'=>$tablaPers,'tablaCony'=>$tablaCony,'tablaLabo'=>$tablaLabo,'tablaUser'=>$tablaUser);
          } else { $rpta = $tablaPers; }
          echo json_encode($rpta);
          break;
        case "execSocio":
          $params = array();

          //datos socio DB
          $datSocio = $data->datosSocio;
          $sql = "exec dbo.sp_socios '".$datSocio->commandSQL."',".
            ($datSocio->personaID).",'".
            utf8_decode($datSocio->socCodigo)."','".
            ($datSocio->socFechaIng)."',".
            ($datSocio->socAgenciaID).",".
            ($datSocio->socG_nrodep).",".
            ($datSocio->socG_alim).",".
            ($datSocio->socG_educ).",".
            ($datSocio->socG_trans).",".
            ($datSocio->socG_alqui).",".
            ($datSocio->socG_fono).",".
            ($datSocio->socG_agua).",".
            ($datSocio->socG_luz).",".
            ($datSocio->socG_otros).",".
            ($datSocio->socG_prest).",'".
            utf8_decode($datSocio->socObservac)."','".
            get_client_ip()."',".
            $_SESSION['usr_ID'];
          $qry = $db->insert($sql, $params);

          $rpta = array("error"=>false,"InsertSocio"=>1);
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
