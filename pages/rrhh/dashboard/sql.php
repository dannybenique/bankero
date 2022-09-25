<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      switch ($data->TipoQuery) {
        case "dashboardRRHH":
          //cantidad colocaciones de prestamos
          $qry1 = $db->select("select count(*) as cuenta from xx_ColocacionesPrest where mes=month(getdate()) and [year]=year(getdate());");
          if ($db->has_rows($qry1)) { $rs1 = $db->fetch_array($qry1); }

          //cantidad de Empleados
          $qry2 = $db->select("select count(*) as cuenta from dbo.tb_workers where estado=1");
          if ($db->has_rows($qry2)) { $rs2 = $db->fetch_array($qry2); }

          //cantidad de Empleados con Vacaciones
          $qry3 = $db->select("select count(*) as cuenta from dbo.tb_workers where estado=1 and month(fecha_vacac)=month(getdate());");
          if ($db->has_rows($qry3)) { $rs3 = $db->fetch_array($qry3); }

          //cantidad de Cumpleaños, dia actual
          $qry4 = $db->select("select count(*) as cuenta from dbo.vw_cumple;");
          if ($db->has_rows($qry4)) { $rs4 = $db->fetch_array($qry4); }

          //cantidad de Renovaciones para este mes
          $qry5 = $db->select("select count(*) as cuenta from dbo.tb_workers where estado=1 and month(fecha_renov)=month(getdate());");
          if ($db->has_rows($qry5)) { $rs5 = $db->fetch_array($qry5); }

          $rpta = array(
            "nroColocaciones" => $rs1["cuenta"],
            "nroEmpleados" => $rs2["cuenta"],
            "nroVacaciones" => $rs3["cuenta"],
            "nroCumple" => $rs4["cuenta"],
            "nroRenovacion" => $rs5["cuenta"]
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
