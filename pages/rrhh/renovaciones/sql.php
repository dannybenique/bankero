<?php
  if (isset($_GET["start"])){
    include_once('../../../includes/db_database.php');
    $ini = $_GET["start"];
    $dia = (int) substr($ini,8,2);
    $mes = (int) substr($ini,5,2);
    $yyy = (int) substr($ini,0,4);

    if($dia>20) {$mes++;}
    if($mes>12) {$mes=1;$yyy++;}
    $sql = "select p.nombres+' '+p.ap_paterno+' '+p.ap_materno as worker,w.nombrecorto,format(w.fecha_renov,'yyyy-MM-dd') as fecha from dbo.tb_workers w,dbo.tb_personas p where p.ID=w.id_persona and w.estado=1 and MONTH(w.fecha_renov)=".$mes." ";
    $qry = $db->select($sql);
    if ($db->has_rows($qry)) {
      $eventos = array();
      for($xx = 0; $xx<$db->num_rows($qry); $xx++){
        $rs = $db->fetch_array($qry);
        $eventos[] = array(
          "title"=>utf8_encode($rs["nombrecorto"]),
          "start" => $rs["fecha"],
          "end" => $rs["fecha"]
        );
      }
    }
    //print_r(json_encode($eventos,JSON_UNESCAPED_UNICODE));
    echo json_encode($eventos);
    //var_dump($sql);

    $db->close();
  } else{
    $resp = array("error"=>true,"resp"=>"ninguna variable en GET");
    echo json_encode($resp);
  }
?>
