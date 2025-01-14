<?php
  if (isset($_POST["frmLogin"])){
    $rpta = "";
    try{
      include_once('db_database.php');
      include_once('web_config.php');
      $data = json_decode($_POST['frmLogin']);
      $sql = "select * from vw_usuarios where login=:dblogin and passw=:dbpassw and id_coopac in(5,:dbcoopac)";
      $params = [':dblogin'=>$data->login,':dbpassw'=>$data->passw,':dbcoopac'=>$web->coopacID]; // el 100=representa a la coopac de este site
      $qry = $db->query_all($sql,$params);
      if($qry){
        foreach($qry as $rs){
          // En este punto, el usuario ya esta validado. Grabamos los datos del usuario en una sesion.
          session_cache_limiter('nocache,private');
          session_name("BANKero");
          session_start();
  
          // Asignamos variables de sesion con datos del Usuario para el uso en el resto de paginas autentificadas.
          $user = array(
            "ID" => $rs['id_usuario'],
            "login" => $rs['login'],
            "cargo" => $rs['cargo'],
            "rolID" => $rs['id_rol'],
            "rolROOT" => 101,
            "coopacID" => $rs['id_coopac'],
            "agenciaID" => $rs['id_agencia'],
            "nombrecorto" => $rs['nombrecorto'],
            "urlfoto" => (strlen($rs['urlfoto'])>3) ? ($rs['urlfoto']) : ('data/personas/fotouser.jpg'),
            "menu" => ($rs['menu'])
          );
          $_SESSION['usr_ID']   = $rs['id_usuario'];
          $_SESSION['usr_data'] = ($user);
          
          //respuesta  
          $rpta = array("error" => false);
        }
      } else {
        $rpta = array("error" => 1,"data" => "credenciales sin acceso");
      }
      echo json_encode($rpta);
    } catch(PDOException $e){
      die("error... ".$e->getMessage());
    }
    $db->close();
  } else{
    $resp = array("error"=>true,"resp"=>"ninguna variable en POST");
    echo json_encode($resp);
  }
?>
