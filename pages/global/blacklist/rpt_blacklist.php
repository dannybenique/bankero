<?php

  include_once("../../../includes/db_database.php");
  $sysusuario = $_GET['usrID'];
  $docDNI = $_GET["docDNI"];

  $qryusr = $db->select("select * from dbo.vw_workers where ID=".($sysusuario));
  $rsUser = $db->fetch_array($qryusr);

  $qry = $db->select("select * from dbo.vw_blacklist where DNI='".($docDNI)."'");
  if ($db->has_rows($qry)) {
    $rs = $db->fetch_array($qry);
    $persona = utf8_encode($rs["persona"]);
    $DNI = utf8_encode($rs["DNI"]);
    $agencia = utf8_encode($rs["agencia"]);
    $direccion = utf8_encode($rs["direccion"]);
    $referencia = utf8_encode($rs["referencia"]);
    $medidorluz = utf8_encode($rs["medidorluz"]);
    $observac = utf8_encode($rs["observac"]);
    $result = '<div><b style="text-decoration:underline;font-size:28px;">SI</b>, figura en la LISTA NEGRA por el siguiente motivo:</div>';
  } else {
    $persona = "";
    $DNI = $docDNI;
    $agencia = "";
    $direccion = "";
    $referencia = "";
    $medidorluz = "";
    $observac = "";
    $result = '<div><b style="text-decoration:underline;font-size:28px;">NO</b>, figura en la LISTA NEGRA</div>';
  }


  $html ='
  <!DOCTYPE html>
  <html lang="en">
    <head>
      <meta charset="utf-8">
      <title>Reporte de Lista Negra</title>
      <style>
        .clearfix:after {content: "";display: table;clear: both;}
        body { position: relative; width: 21cm; height: 29.7cm; margin:0 auto; color:#444; background:#FFFFFF; font-size:12px; font-family:Arial;}
        table {width: 100%;border-collapse: collapse;border-spacing: 0;margin-bottom: 20px; }
      </style>
    </head>
    <body>
      <main>
        <div style="position:relative;">
          <div style="float:left;width:100px;"><img src="../../../includes/pdf/plantilla/img/logo.jpg" style="width:100px;"/></div>
          <div style="width:280px;float:right;">
            <h3 style="width:280px;background:#000;color:white;font-size:18px;border-radius:0.3em;margin:0;padding:0.3em;text-align:center;">REPORTE DE LISTA NEGRA</h3>
          </div>
        </div>
        <hr/>
        <div style="margin-top:20px;margin-bottom:20px;">
          <div style="margin-bottom:20px;"><span>EL SOCIO CON LOS SIGUIENTES DATOS:</span></div>
          <div><span>SOCIO: </span> <span style="color:black;font-weight:bold;">'.$persona.'</span></div>
          <div><span>DNI: </span> <span style="color:black;font-weight:bold;">'.$DNI.'</span></div>
          <div><span>AGENCIA: </span> <span style="color:black;font-weight:bold;">'.$agencia.'</span></div>
          <div><span>DIRECCION: </span> <span style="color:black;font-weight:bold;">'.$direccion.'</span></div>
          <div><span>REFERENCIA: </span> <span style="color:black;font-weight:bold;">'.$referencia.'</span></div>
          <div><span>MEDIDOR LUZ: </span> <span style="color:black;font-weight:bold;">'.$medidorluz.'</span></div>
        </div>
        <div>
          '.$result.'
          <div style="margin-top:20px;font-size:16px;font-style:italic;">'.$observac.'</div>
        </div>
        <p style="height:200px;"></p>
        <div style="text-align:center;">
          <span style="border-top:1px solid;">'.utf8_encode($rsUser["worker"]).'</span><br>
          <span>DNI '.utf8_encode($rsUser["DNI"]).' </span>
        </div>
      </main>
    </body>
  </html>';
  $footer = '<table width="100%"><tr><td width="33%"></td><td width="33%" align="center"></td><td width="33%" style="text-align: right;">Grupo Inversion Sudamericano</td></tr></table>';

  require_once "../../../libs/pdf.php/vendor/autoload.php";
  $mpdf = new \Mpdf\Mpdf([]);
  $mpdf->WriteHTML($html);
  $mpdf->SetHTMLFooter($footer);
  $mpdf->Output();
  exit;
?>
