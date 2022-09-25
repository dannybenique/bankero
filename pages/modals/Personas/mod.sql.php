<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      function cadSQL($objDatos){
        return "exec dbo.sp_personas '".$objDatos->commandSQL."',".
          ($objDatos->ID).",".
          ($objDatos->persPermisoID).",".
          ($objDatos->persTipoPersona).",'".
          ($objDatos->persNombres)."','".
          ($objDatos->persApePaterno)."','".
          ($objDatos->persApeMaterno)."','".
          (trim($objDatos->persDNI))."',".
          ($objDatos->persId_Doc).",".
          ($objDatos->persId_sexo).",".
          ($objDatos->persId_Ginstruc).",".
          ($objDatos->persId_Ecivil).",".
          ($objDatos->persId_Ubigeo).",".
          ($objDatos->persId_TipoVivienda).",'".
          ($objDatos->persFechaNac)."','".
          ($objDatos->persLugarnac)."','".
          ($objDatos->persTelefijo)."','".
          ($objDatos->persCelular)."','".
          ($objDatos->persEmail)."','".
          ($objDatos->persProfesion)."','".
          ($objDatos->persOcupacion)."','".
          ($objDatos->persDireccion)."','".
          ($objDatos->persReferencia)."','".
          ($objDatos->persMedidorluz)."','".
          ($objDatos->persUrlFoto)."','".
          ($objDatos->persObservac)."','".
          get_client_ip()."',".
          $_SESSION['usr_ID'];
      }
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');
      $data = json_decode($_REQUEST['appSQL']);

      switch ($data->TipoQuery) {
        case "selPersona":
          echo json_encode(getOnePersona($data->personaID));
          break;
        case "insPersona":
          try {
            $qry = $db->insert(cadSQL($data), array());
            $rs = $db->fetch_array($db->select("select ID from dbo.tb_personas where DNI like'%".trim($data->persDNI)."%'"));
            $rpta = array("error"=>0, "insert"=>1, "tablaPers"=>getOnePersona($rs["ID"]));
            echo json_encode($rpta);
          } catch(Exception $e) {
            echo json_encode($e->getMessage());
          }
          break;
        case "updPersona":
          try {
            //en caso de haber fotos
            if(isset($_FILES["imgFoto"])){
              $foto = $_FILES["imgFoto"];
              if(is_uploaded_file($foto['tmp_name'])){
                if($foto["type"]=="image/jpg" or $foto["type"]=="image/jpeg"){
                  $data->persUrlFoto = "data/personas/".$data->ID.".jpg";
                  $ruta = "../../../".$data->persUrlFoto;
                  move_uploaded_file($foto["tmp_name"],$ruta);
                }
              }
            }

            //datos para DB
            $qry = $db->update(cadSQL($data), array());
            $rpta = array("error"=>false,"UpdatePersona"=>1,"tablaPers"=>getOnePersona($data->ID));
            echo json_encode($rpta);
          } catch(Exception $e) {
            echo json_encode($e->getMessage());
          }
          break;

        case "comboUbigeo":
          switch(strlen(strval($data->padreID))){
            case 2: //actualiza provincia
              $provincias = getComboBox("select id,nombre from sis_ubigeo where id_padre=".$data->padreID." order by nombre;");
              $distritos = getComboBox("select id,nombre from sis_ubigeo where id_padre=".$provincias[0]["ID"]." order by nombre;");
              $rpta = array( "provincias" => $provincias, "distritos" => $distritos );
              break;
            case 4: //actualiza distrito
              $distritos = getComboBox("select id,nombre from sis_ubigeo where id_padre=".$data->padreID." order by nombre;");
              $rpta = array( "distritos" => $distritos );
              break;
          }
          echo json_encode($rpta);
          break;
        case "VerifyPersona":
          $activo=0;
          $persona = 0; //indica que encontro en personas
          $qry = $db->select(utf8_decode("select ID from dbo.tb_personas where (DNI='".$data->nroDNI."');"));
          if($db->has_rows($qry)){
            $rs = $db->fetch_array($qry);
            $persona = getOnePersona($rs["ID"]);
            $activo = 1;
          }

          //respuesta
          $rpta = array("persona"=>$persona,"activo"=>$activo);
          echo json_encode($rpta);
          break;
        case "VerifyBlacklist":
          $activo = 0; //indica que encontro en blacklist
          $persona = 0; //indica que encontro en personas
          //verificar en Personas
          $qryPers = $db->select(utf8_decode("select ID from dbo.tb_personas where (DNI='".$data->nroDNI."');"));
          if($db->has_rows($qryPers)){
            $rsPers = $db->fetch_array($qryPers);
            $persona = getOnePersona($rsPers["ID"]);
            //verificar en blacklist
            $qryBlacklist = $db->select(utf8_decode("select id_persona from dbo.tb_blacklist where (id_persona=".$rsPers["ID"].");"));
            if($db->has_rows($qryBlacklist)){
              $rsBlacklist = $db->fetch_array($qryBlacklist);
              $activo = 1;
            }
          }

          //respuesta
          $rpta = array("persona"=>$persona,"activo"=>$activo);
          echo json_encode($rpta);
          break;
        case "VerifyConyuge":
          $activo = 0; //indica que encontro en conyuges
          $persona = 0; //indica que encontro en personas

          //verificar en Personas
          $qryPers = $db->select(utf8_decode("select ID from dbo.tb_personas where (DNI='".$data->nroDNI."');"));
          if($db->has_rows($qryPers)){
            $rsPers = $db->fetch_array($qryPers);
            $persona = getOnePersona($rsPers["ID"]);
            //verificar en Conyuges
            $qryConyuge1 = $db->select("select id_conyuge1 from dbo.tb_personas_cony where (id_conyuge1=".$rsPers["ID"].");");
            $qryConyuge2 = $db->select("select id_conyuge2 from dbo.tb_personas_cony where (id_conyuge2=".$rsPers["ID"].");");
            if($db->has_rows($qryConyuge1) || $db->has_rows($qryConyuge2)) { $activo = 1; }
          }

          //respuesta
          $rpta = array("persona"=>$persona,"activo"=>$activo);
          echo json_encode($rpta);
          break;
        case "VerifyAhorros":
          $activo = 0; //indica que encontro en ahorros
          $persona = 0; //indica que encontro en personas
          //verificar en Personas
          $qryPers = $db->select(utf8_decode("select id_persona from dbo.vw_socios where (DNI='".$data->nroDNI."');"));
          if($db->has_rows($qryPers)){
            $rsPers = $db->fetch_array($qryPers);
            $persona = getOnePersona($rsPers["id_persona"]);
            //verificar en ahorros
            $qry = $db->select(utf8_decode("select id_socio from dbo.tb_oper_ahorros where (id_socio=".$rsPers["id_persona"].");"));
            if($db->has_rows($qry)){ $activo = 1; }
            //verificar en Aportes
            $qry = $db->select(utf8_decode("select id_socio from dbo.tb_oper_aportes where id_socio=".$rsPers["id_persona"]." and id_producto=1 and saldo<0;"));
            if($db->has_rows($qry)){ $activo = 2; }
          }

          //respuesta
          $rpta = array("persona"=>$persona,"activo"=>$activo);
          echo json_encode($rpta);
          break;
        case "VerifySuplentes":
          $activo = 0; //indica que encontro en ahorros
          $persona = 0; //indica que encontro en personas
          //verificar en Personas
          $qryPers = $db->select(utf8_decode("select ID from dbo.vw_personas where (DNI='".$data->nroDNI."');"));
          if($db->has_rows($qryPers)){
            $rsPers = $db->fetch_array($qryPers);
            $persona = getOnePersona($rsPers["ID"]);
            //verificar en suplentes
            $qryAhorros = $db->select(utf8_decode("select id_suplente from dbo.tb_oper_ahorros_suplentes where id_suplente=".$rsPers["ID"]." and id_ahorro=".$data->foreignKey.";"));
            if($db->has_rows($qryAhorros)){ $activo = 1; }
          }

          //respuesta
          $rpta = array("persona"=>$persona,"activo"=>$activo);
          echo json_encode($rpta);
          break;
        case "VerifyCajaProveedor":
          $activo = 0; //indica que encontro en ahorros
          $persona = 0; //indica que encontro en personas
          //verificar en Personas
          $qryPers = $db->select(utf8_decode("select ID from dbo.tb_personas where (DNI='".$data->nroDNI."');"));
          if($db->has_rows($qryPers)){
            $rsPers = $db->fetch_array($qryPers);
            $persona = getOnePersona($rsPers["ID"]);
            //verificar en caja
            //$qryAhorros = $db->select(utf8_decode("select id_suplente from dbo.tb_oper_ahorros_suplentes where id_suplente=".$rsPers["ID"]." and id_ahorro=".$data->foreignKey.";"));
            //if($db->has_rows($qryAhorros)){ $activo = 1; }
          }

          //respuesta
          $rpta = array("persona"=>$persona,"activo"=>$activo);
          echo json_encode($rpta);
          break;
        case "VerifyWorker":
          $activo = 0; //indica que encontro en workers
          $persona = 0; //indica que encontro en personas

          //verificar en Personas
          $qryPers = $db->select(utf8_decode("select ID from dbo.tb_personas where (DNI='".$data->nroDNI."');"));
          if($db->has_rows($qryPers)){
            $rsPers = $db->fetch_array($qryPers);
            $persona = getOnePersona($rsPers["ID"]);
            //verificar en workers
            $qryWorker = $db->select(utf8_decode("select id_persona,estado from dbo.tb_workers where (id_persona=".$rsPers["ID"].");"));
            if($db->has_rows($qryWorker)){
              $rsWorker = $db->fetch_array($qryWorker);
              //verificar estado
              //if($rsWorker["estado"]==1) { $estado = 1; }
              $activo = 1;
            }
          }

          //respuesta
          $rpta = array("persona"=>$persona,"activo"=>$activo);
          echo json_encode($rpta);
          break;
        case "VerifySocios":
          $activo = 0; //indica que encontro en socios
          $persona = 0; //indica que encontro en personas
          //verificar en Personas
          $qryPers = $db->select(utf8_decode("select ID from dbo.tb_personas where (DNI='".$data->nroDNI."');"));
          if($db->has_rows($qryPers)){
            $rsPers = $db->fetch_array($qryPers);
            $persona = getOnePersona($rsPers["ID"]);
            //verificar en Socios
            $qrySocio = $db->select(utf8_decode("select id_persona,estado from dbo.tb_socios where (id_persona=".$rsPers["ID"].");"));
            if($db->has_rows($qrySocio)){
              $rsSocio = $db->fetch_array($qrySocio);
              //verificar estado
              //if($rsSocio["estado"]==1) { $estado = 1; }
              $activo = 1;
            }
          }

          //respuesta
          $rpta = array("persona"=>$persona,"activo"=>$activo);
          echo json_encode($rpta);
          break;
        case "VerifyPreventa":
          $activo = 0;
          $persona = 0; //indica que encontro en personas
          //verificar en Personas
          $qryPers = $db->select(utf8_decode("select ID from dbo.tb_personas where (DNI='".$data->nroDNI."');"));
          if($db->has_rows($qryPers)){
            $rsPers = $db->fetch_array($qryPers);
            $persona = getOnePersona($rsPers["ID"]);
            //verificar en Socios
            $qry = $db->select(utf8_decode("select id_persona,estado from dbo.tb_oper_captaciones where (id_persona=".$rsPers["ID"].");"));
            if($db->has_rows($qry)){ $activo = 1; }
          }

          //respuesta
          $rpta = array("persona"=>$persona,"activo"=>$activo);
          echo json_encode($rpta);
          break;
        /*case "apiPeru"://consulta el ruc o DNI
          $url = (strlen($data->nroDNI)==8)?("dni/".$data->nroDNI):((strlen($data->nroDNI)==11)?("ruc/".$data->nroDNI):(""));
          $veri = @file_get_contents("https://dniruc.apisperu.com/api/v1/".$url."?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6ImRhbm55YmVuaXF1ZUBtc24uY29tIn0.ts3qFRsLtLxqnoOMvwYEeOu470tyTUGWQbsuH4ZTC7I")
                  or exit(12);

          if($veri==false) { $retu = array("error"=>true,"message"=>$veri); }
          else { $retu = array("error"=>false,"api"=>$veri); }

          //respuesta
          $rpta = $retu;
          echo json_encode($rpta);
          break;*/
      }
      $db->close();
    } else {
      $resp = array("error"=>true,"mensaje"=>"ninguna variable en POST");
      echo json_encode($resp);
    }
  } else {
    $resp = array("error"=>true,"mensaje"=>"Caducó la sesion.");
    echo json_encode($resp);
  }
?>
