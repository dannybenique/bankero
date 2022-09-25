<?php
  //Obtiene la IP del cliente
  function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP')) $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR')) $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED')) $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR')) $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED')) $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR')) $ipaddress = getenv('REMOTE_ADDR');
    else $ipaddress = 'UNKNOWN';
    return $ipaddress;
  }

  //Obtener datos
  function getOnePersona($personaID){
    $db = $GLOBALS["db"];
    //verificar usuario
    $qry = $db->select(utf8_decode("select id_usernivel from dbo.tb_usuarios where id_persona=".$_SESSION['usr_ID'].";"));
    $rs = $db->fetch_array($qry);
    $tablaUser = array("usernivel"=>$rs["id_usernivel"],"admin"=>701);

    //verificar permisos
    $sql = "select * from dbo.tb_usuarios_permisos where tabla='tb_personas' and id_usuario_solic=".$_SESSION['usr_ID']." and id_persona=".$personaID;
    $qry = $db->select($sql);
    if($db->has_rows($qry)){ $rs = $db->fetch_array($qry); $permisoPersona = array("ID"=>$rs["ID"],"estado"=>(1 + $rs["estado"])); }
    else { $permisoPersona = array("ID"=>0,"estado"=>0); }

    //obtener datos personales
    $qry = $db->select("select * from dbo.vw_personas where ID=".$personaID);
    if ($db->has_rows($qry)) {
        $rs = $db->fetch_array($qry);
        $tabla = array(
          "ID" => ($rs["ID"]),
          "tipoPersona" => ($rs["tipoPersona"]),
          "urlfoto" => utf8_encode($rs["urlfoto"]),
          "persona" => utf8_encode($rs["persona"]),
          "nombres" => utf8_encode($rs["nombres"]),
          "ap_paterno" => utf8_encode($rs["ap_paterno"]),
          "ap_materno" => utf8_encode($rs["ap_materno"]),
          "id_doc" => $rs["id_doc"],
          "tipoDNI" => $rs["doc"],
          "nroDNI" => utf8_encode($rs["DNI"]),
          "celular" => utf8_encode($rs["celular"]),
          "fijo" => utf8_encode($rs["telefijo"]),
          "correo" => utf8_encode($rs["email"]),
          "profesion" => utf8_encode($rs["profesion"]),
          "ocupacion" => utf8_encode($rs["ocupacion"]),
          "fechanac" => ($rs["fecha_nac"]),
          "lugarnac" => utf8_encode($rs["lugar_nac"]),
          "id_ginstruc" => ($rs["id_ginstruc"]),
          "ginstruc" => ($rs["ginstruc"]),
          "id_ecivil" => ($rs["id_ecivil"]),
          "ecivil" => ($rs["ecivil"]),
          "id_sexo" => ($rs["id_sexo"]),
          "sexo" => ($rs["sexo"]),
          "id_region" => ($rs["id_region"]),
          "region" => utf8_encode($rs["region"]),
          "id_provincia" => ($rs["id_provincia"]),
          "provincia" => utf8_encode($rs["provincia"]),
          "id_distrito" => ($rs["id_distrito"]),
          "distrito" => utf8_encode($rs["distrito"]),
          "direccion" => utf8_encode($rs["direccion"]),
          "referencia" => utf8_encode($rs["referencia"]),
          "medidorluz" => utf8_encode($rs["medidorluz"]),
          "id_tipovivienda" => $rs["id_tipovivienda"],
          "tipovivienda" => $rs["tipovivienda"],
          "observPers" => utf8_encode($rs["observPers"]),
          "sysuserPers" => utf8_encode($rs["sysuserPers"]),
          "sysfechaPers" => utf8_encode($rs["sysfechaPers"]),
          "permisoPersona"=>$permisoPersona,
          "tablaUser" => $tablaUser
        );
    }
    return $tabla;
  }
  function getOneLaboral($personaID){
    $db = $GLOBALS["db"];

    //verificar usuario
    $qry = $db->select(utf8_decode("select id_usernivel from dbo.tb_usuarios where id_persona=".$_SESSION['usr_ID'].";"));
    $rs = $db->fetch_array($qry);
    $tablaUser = array("usernivel"=>$rs["id_usernivel"],"admin"=>701);

    //verificar permisos
    $qry = $db->select("select * from dbo.tb_usuarios_permisos where tabla='tb_personas_labo' and id_usuario_solic=".$_SESSION['usr_ID']." and id_persona=".$personaID);
    if($db->has_rows($qry)){ $rs = $db->fetch_array($qry); $permisoLaboral = array("ID"=>$rs["ID"],"estado"=>(1 + $rs["estado"])); }
    else { $permisoLaboral = array("ID"=>0,"estado"=>0); }

    //cargar datos de conyuge
    $qry = $db->select("select * from dbo.vw_personas_labo where id_persona=".$personaID);
    if ($db->has_rows($qry)) {
      $rs = $db->fetch_array($qry);
      $tabla = array(
        "id_persona" => ($rs["id_persona"]),
        "condicion" => ($rs["condicion"]),
        "ruc" => utf8_encode($rs["RUC"]),
        "empresa" => utf8_encode($rs["empresa"]),
        "telefono" => utf8_encode($rs["telefono"]),
        "rubro" => utf8_encode($rs["rubro"]),
        "id_region" => ($rs["id_region"]),
        "region" => utf8_encode($rs["region"]),
        "id_provincia" => ($rs["id_provincia"]),
        "provincia" => utf8_encode($rs["provincia"]),
        "id_distrito" => ($rs["id_distrito"]),
        "distrito" => utf8_encode($rs["distrito"]),
        "direccion" => utf8_encode($rs["direccion"]),
        "cargo" => utf8_encode($rs["cargo"]),
        "ingreso" => ($rs["ingreso"]),
        "fechaIni" => ($rs["inicio"]),
        "observLabo" => utf8_encode($rs["observLabo"]),
        "sysuserLabo" => utf8_encode($rs["sysuserLabo"]),
        "sysfechaLabo" => utf8_encode($rs["sysfechaLabo"]),
        "permisoLaboral" => $permisoLaboral,
        "tablaUser" => $tablaUser
      );
    } else { $tabla = array("id_persona" => 0); }
    return $tabla;
  }
  function getOneConyuge($personaID){
    $db = $GLOBALS["db"];

    //verificar usuario
    $qry = $db->select(utf8_decode("select id_usernivel from dbo.tb_usuarios where id_persona=".$_SESSION['usr_ID'].";"));
    $rs = $db->fetch_array($qry);
    $tablaUser = array("usernivel"=>$rs["id_usernivel"],"admin"=>701);

    //verificar permisos
    $qry = $db->select("select * from dbo.tb_usuarios_permisos where tabla='tb_personas_cony' and id_usuario_solic=".$_SESSION['usr_ID']." and id_persona=".$personaID);
    if($db->has_rows($qry)){ $rs = $db->fetch_array($qry); $permisoConyuge = array("ID"=>$rs["ID"],"estado"=>(1 + $rs["estado"])); }
    else { $permisoConyuge = array("ID"=>0,"estado"=>0); }

    //verificar si la persona tiene conyuge
    $qry1 =  $db->select("select * from dbo.tb_personas_cony where id_conyuge1=".$personaID);
    $qry2 =  $db->select("select * from dbo.tb_personas_cony where id_conyuge2=".$personaID);

    if ($db->has_rows($qry1) || $db->has_rows($qry2)) {
      if ($db->has_rows($qry1)) { $rs = $db->fetch_array($qry1); $conyugeID = $rs["id_conyuge2"]; }
      else { $rs = $db->fetch_array($qry2); $conyugeID = $rs["id_conyuge1"]; }
      $tabla = array(
        "id_conyuge" => ($conyugeID),
        "persona" => getOnePersona($conyugeID),
        "laboral" => getOneLaboral($conyugeID),
        "tiempoRelacion" => ($rs["tiempoRelacion"]),
        "permisoConyuge" => $permisoConyuge,
        "tablaUser" => $tablaUser
      );
    } else { $tabla = array("id_conyuge"=>0); }
    return $tabla;
  }
  function getOneSocio($socioID){
    $db = $GLOBALS["db"];

    //cargar datos de socio
    $qry =  $db->select("select * from dbo.vw_socios where id_persona=".$socioID);
    if ($db->has_rows($qry)) {
      $rs = $db->fetch_array($qry);
      $tabla = array(
        "codigo" => ($rs["codigo"]),
        "fecha_ing" => ($rs["fecha_ing"]),
        "id_agencia" => ($rs["id_agencia"]),
        "agencia" => utf8_encode($rs["agencia"]),
        "g_nrodep" => ($rs["g_nrodep"]),
        "g_alim" => ($rs["g_alim"]),
        "g_educ" => ($rs["g_educ"]),
        "g_trans" => ($rs["g_trans"]),
        "g_alqui" => ($rs["g_alqui"]),
        "g_fono" => ($rs["g_fono"]),
        "g_agua" => ($rs["g_agua"]),
        "g_luz" => ($rs["g_luz"]),
        "g_otros" => ($rs["g_otros"]),
        "g_prest" => ($rs["g_prest"]),
        "observac" => utf8_encode($rs["observac"]),
        "sysuserSoc" => utf8_encode($rs["sysuserSoc"]),
        "sysfechaSoc" => utf8_encode($rs["sysfechaSoc"])
      );
    }
    return $tabla;
  }
  function getOneUsuario($usuarioID){
    $db = $GLOBALS["db"];

    //obtener datos usuario
    $qry = $db->select("select * from dbo.vw_usuarios where ID=".$usuarioID);
    if ($db->has_rows($qry)) {
        $rs = $db->fetch_array($qry);
        $tabla = array(
          "ID" => ($rs["ID"]),
          "codigo" => ($rs["codigo"]),
          "usuario" => utf8_encode($rs["usuario"]),
          "nombrecorto" => utf8_encode($rs["nombrecorto"]),
          "id_usernivel" => ($rs["id_usernivel"]),
          "id_agencia" => ($rs["id_agencia"]),
          "nroDNI" => ($rs["DNI"])
        );
    }
    return $tabla;
  }
  function getComboBox($cadSQL){
    $db = $GLOBALS["db"];
    $tabla = array();
    $qry = $db->select($cadSQL);
    if ($db->num_rows($qry)>0) {
      for($xx=0; $xx<$db->num_rows($qry); $xx++){
        $rs = $db->fetch_array($qry);
        $tabla[] = array(
          "ID" => $rs["id"],
          "nombre" => utf8_encode($rs["nombre"])
        );
      }
    }
    return $tabla;
  }
?>
