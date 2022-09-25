<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');

      $data = json_decode($_REQUEST['appSQL']);

      //****************personas****************
      switch ($data->TipoQuery) {
        case "dashboard":
          //verificamos nivel de usuario
          $sql = "select id_usernivel,codagenc,id_agencia from vw_usuarios where ID=".$_SESSION['usr_ID'];
          $qryUser = $db->select($sql);
          if ($db->has_rows($qryUser)) { $rsUser = $db->fetch_array($qryUser); }

          $whr1 = "";
          $whr2 = "";
          $whr3 = "";
          $whr4 = "";
          switch($rsUser['id_usernivel']){
            case 711: //administrador
            case 712:  //caja
              $whr1 = " and id_agencia=".$rsUser['id_agencia'];
              $whr2 = " and id_agencia=".$rsUser['id_agencia'];
              $whr3 = " and id_agencia=".$rsUser['id_agencia'];
              $whr4 = " and id_agencia=".$rsUser['id_agencia'];
              break;
            case 713: //analista, promotor
              $whr1 = " and id_agencia=".$rsUser['id_agencia']." and id_promotor=".$_SESSION['usr_ID'];
              $whr2 = " and id_agencia=".$rsUser['id_agencia'];
              $whr3 = " and id_agencia=".$rsUser['id_agencia']." and id_analista=".$_SESSION['usr_ID'];
              $whr4 = " and id_agencia=".$rsUser['id_agencia']." and id_analista=".$_SESSION['usr_ID'];
              break;
          }

          //cantidad colocaciones de prestamos
          $sql = "select count(*) as cuenta from xx_ColocacionesPrest where mes=month(getdate()) and [year]=year(getdate()) ".$whr1;
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) { $rs1 = $db->fetch_array($qry); }

          //cantidad de empleados
          $sql = "select count(*) as cuenta from dbo.tb_workers where estado=1".$whr2;
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) { $rs2 = $db->fetch_array($qry); }

          //cantidad de morosos
          $sql = "select count(*) as cuenta from dbo.xx_CarteraPrest where atraso<0".$whr3;
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) { $rs3 = $db->fetch_array($qry); }

          //cantidad en cartera prestamos
          $sql = "select count(*) as cuenta from dbo.xx_CarteraPrest where 0=0".$whr4;
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) { $rs4 = $db->fetch_array($qry); }

          //cantidad y nombres de usuarios, cumpleaños, dia actual
          $qry = $db->select("select * from dbo.vw_cumple order by dia");
          if ($db->has_rows($qry)) {
            $num_cumple = $db->num_rows($qry);
            $nombres_cumple = "";
            for($xx = 0; $xx<$num_cumple; $xx++){
              $rs5 = $db->fetch_array($qry);
              $nombres_cumple = $nombres_cumple.($rs5["nombrecorto"])."<br/>";
            }
          } else {
            $num_cumple = 0;
            $nombres_cumple = "";
          }

          //respuesta
          $rpta = array(
            "colocaciones" => $rs1["cuenta"],
            "empleados" => $rs2["cuenta"],
            "morosos" => $rs3["cuenta"],
            "cartera" => $rs4["cuenta"],
            "numcumple" => $num_cumple,
            "nombres_cumple" => $nombres_cumple
          );
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
