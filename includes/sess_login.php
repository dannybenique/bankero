<?php
  if (isset($_POST["frmLogin"])){
    include_once('db_database.php');
    $data = json_decode($_POST['frmLogin']);

    if ($db->conn){
      $qry = $db->select("select * from financiero.dbo.vw_usuarios where login='".$data->login."' and passw='".$data->passw."'");

      if ($db->has_rows($qry)){
        $rs = $db->fetch_array($qry);
        // En este punto, el usuario ya esta validado. Grabamos los datos del usuario en una sesion.
        session_cache_limiter('nocache,private');
        session_name("BANKero");
        session_start();

        $urlfoto = (strlen($rs['urlfoto'])>3) ? ($rs['urlfoto']) : ('data/personas/images/0noFotoUser.jpg');

        // Asignamos variables de sesion con datos del Usuario para el uso en el resto de paginas autentificadas.
        $_SESSION['usr_ID']    = $rs['ID']; // definimos el ID del usuario en nuestra BD de usuarios
        $_SESSION['usr_login']  = $rs['login']; // definimos el login del usuario
        $_SESSION['usr_nombrecorto'] = utf8_encode($rs['nombrecorto']); //definimos nombre corto del usuario
        $_SESSION['usr_cargo'] = utf8_encode($rs['cargo']); //definimos cargo del usuario
        $_SESSION['usr_urlfoto'] = $urlfoto; //definimos foto del usuario
        $_SESSION['usr_usernivelID'] = $rs['id_usernivel']; //definimos el nivel de acceso del usuario
        $_SESSION['usr_agenciaID'] = $rs['id_agencia']; //definimos el nivel de acceso del usuario

        echo json_encode(array("error"=>0,"usernivel"=>$rs['id_usernivel']));
      } else{
        echo json_encode(array("error" =>1));
      }
      $db->close();
    }
    else{
      echo "fallo";
      die(print_r(sqlsrv_errors(),true));
    }
  } else{
    $resp = array("error"=>true,"resp"=>"ninguna variable en POST");
    echo json_encode($resp);
  }
?>
