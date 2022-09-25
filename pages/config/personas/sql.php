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
        case "selPersonas":
          $whr = "";
          $tabla = array();

          //verificar usuario
          $qryusr = $db->select(utf8_decode("select id_usernivel from dbo.tb_usuarios where id_persona=".$_SESSION['usr_ID'].";"));
          $rsusr = $db->fetch_array($qryusr);

          //cargar datos de Personas
          $data->buscar = strtoupper($data->buscar);
          if(($data->buscar)!="") { $whr = " and (persona like'%".$data->buscar."%' or dni like'%".$data->buscar."%') " ;}
          $qryCount = $db->select(utf8_decode("select count(*) as cuenta from dbo.vw_personas where ID>1 ".$whr.";"));
          $rsCount = $db->fetch_array($qryCount);

          $sql = "select top(15)* from dbo.vw_personas where ID>1 ".$whr." order by persona;";
          $qry = $db->select(utf8_decode($sql));
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $pers = ($rs["tipoPersona"]==2) ? utf8_encode($rs["nombres"]) : utf8_encode($rs["persona"]);
              $tabla[] = array(
                "ID" => $rs["ID"],
                "DNI"=> str_replace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', $rs["DNI"]),
                "url" => $rs["urlfoto"],
                "persona" => str_ireplace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', $pers),
                "direccion" => utf8_encode($rs["direccion"])
              );
            }
          }

          //respuesta
          $rpta = array("cuenta"=>$rsCount["cuenta"],"tabla"=>$tabla,"usernivel"=>$rsusr["id_usernivel"],"admin"=>701);
          echo json_encode($rpta);
          break;
        case "editPersona":
          //datos Persona
          $tablaPers = getOnePersona($data->personaID);

          switch($data->fullQuery){
            case 0:
              $rpta = $tablaPers;
              break;
            case 1: //datos personales + laborales
              $tablaLabo = getOneLaboral($data->personaID); //cargar datos Laborales
              $rpta = array('tablaPers'=>$tablaPers,'tablaLabo'=>$tablaLabo);
              break;
            case 2: //datos personales + laborales + conyuge
              $tablaLabo = getOneLaboral($data->personaID); //cargar datos Laborales
              $tablaCony = getOneConyuge($data->personaID); //cargar datos Personales de conyuge
              $rpta = array('tablaPers'=>$tablaPers,'tablaLabo'=>$tablaLabo,'tablaCony'=>$tablaCony);
              break;
          }
          echo json_encode($rpta);
          break;
        case "audiPersona": //auditoria de personas
          $tablaPers = getOnePersona($data->personaID);
          $tablaLog = array();
          $sql = "select * from dbo.vw_sislog where tabla like'tb_persona%' and ID=".$data->personaID." order by sysfecha desc,syshora desc;";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tablaLog[] = array(
                "codigo" => $rs["codigo"],
                "tabla" => utf8_encode($rs["tabla"]),
                "accion" => utf8_encode($rs["accion"]),
                "campo" => utf8_encode($rs["campo"]),
                "observac" => utf8_encode($rs["observ"]),
                "usuario" => utf8_encode($rs["usuario"]),
                "sysIP" => utf8_encode($rs["sysIP"]),
                "sysagencia" => utf8_encode($rs["sysagencia"]),
                "sysfecha" => utf8_encode($rs["sysfecha1"]),
                "syshora" => utf8_encode($rs["syshora1"])
              );
            }
          }
          $rpta = array("tablaPers"=>$tablaPers,"tablaLog"=>$tablaLog);
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
