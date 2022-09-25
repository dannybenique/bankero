<?php
  if (isset($_GET["start"])){
    include_once('../../../includes/sess_verifica.php');
    include_once('../../../includes/db_database.php');
    $ini = $_GET["start"];
    $dia = (int) substr($ini,8,2);
    $mes = (int) substr($ini,5,2);
    $yyy = (int) substr($ini,0,4);
    $rpta = 0;

    if($dia>20) {$mes++;}
    if($mes>12) {$mes=1;$yyy++;}
    $sql = "select *,CONVERT(varchar,fec_ini,126) as fecha_ini,CONVERT(varchar,fec_fin,126) as fecha_fin from dbo.tb_recordatorios where id_worker=".$_SESSION['usr_ID']." order by fec_ini";
    $qry = $db->select($sql);
    if ($db->has_rows($qry)) {
      $eventos = array();
      for($xx = 0; $xx<$db->num_rows($qry); $xx++){
        $rs = $db->fetch_array($qry);
        $eventos[] = array(
          "id"=>$rs["ID"],
          "allDay"=>($rs["fullday"]==1)?(true):(false),
          "title"=>$rs["observac"],
          "start" => $rs["fecha_ini"],
          "end" => $rs["fecha_fin"]
        );
      }
    }

    echo json_encode($eventos);
    $db->close();
  } else{
    $resp = array("error"=>true,"resp"=>"ninguna variable en GET");
    echo json_encode($resp);
  }
?>
