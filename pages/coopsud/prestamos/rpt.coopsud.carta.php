<?php
  include_once("../../../includes/db_database.php");
  $codsocio = $_GET["codsocio"];
  $codigo = explode("-",$codsocio);

  //personas
  $qryPers = $db->select("select * from coopSUD.dbo.coop_db_socios_gen where codagenc='".($codigo[0])."' and codsocio='".($codigo[1])."'");
  $rs = $db->fetch_array($qryPers);
  $socio = (trim($rs["dni"])!="0")?utf8_encode($rs["nombres"]." ".$rs["ap_pater"]." ".$rs["ap_mater"]):($rs["raz_social"]);
  $tipoDNI = (trim($rs["dni"])!="0")?("DNI"):("RUC");
  $nroDNI = (trim($rs["dni"])!="0")?($rs["dni"]):($rs["ruc"]);

  //documento html
  $html ='
  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Carta de Autorizacion - Garantia Liquida</title>
    <style>
      .clearfix:after {content: "";display: table;clear: both;}
      body { position: relative; width: 21cm; height: 29.7cm; margin:0 auto; color:#555; background:#FFFFFF; font-size:16px; font-family:Arial;}
      table {width:100%; border-collapse:collapse; border-spacing:0;}
      .tablacredito th{border:1px solid #333;border-bottom:none;font-size:10px;}
      .tablacredito td{border:1px solid #333;font-size:10px;padding-left:5px;}
      .tablacredito .mitr{height:20px;}
    </style>
  </head>
  <body>
    <main>
      <div style="position:relative;">
        <div style="float:left;width:100px;"><img src="../../../includes/pdf/plantilla/img/logo.jpg" style="width:100px;"/></div>
        <div style="width:260px;float:right;">
          <h3 style="width:260px;background:black;color:white;font-size:18px;border-radius:0.3em;margin:0;padding:0.3em;text-align:center;">CARTA DE AUTORIZACION</h3>
        </div>
      </div>
      <hr/>
      <div style="margin-bottom:30px;">
        <b>Señor Presidente del Consejo de Administración<br>
          Cooperativa de Ahorro y Crédito Grupo Inversión Sudamericano<br><br><br>
          Presente.-<br><br><br>
          Asunto: Autorización de disposición
        </b>
      </div>
      <p style="text-align:justify;">
        Por medio de la presente, me dirijo a su representada el suscrito identificado con <br><b style="color:black;">'.$tipoDNI.'</b> Nº <b style="color:black;">'.$nroDNI.'</b>;
        es socio con código Nº <b style="color:black;">'.$codsocio.'</b>, de su entidad, por lo que <b style="color:black;"><u>Autorizo</u></b>, a la Cooperativa de Ahorro y Crédito
        Grupo Inversión Sudamericano, disponer de mis aportes voluntarios y ahorros, para el pago de mis obligaciones de crédito y otros que mantenga como socio en dicha cooperativa,
        ello con el fin de garantizar el pago de las deudas pendientes contraídas, hasta la cancelación en su totalidad de mis obligaciones.
      </p>
      <p style="text-align:justify;">
        Agradeciéndole se sirva disponer a quien corresponda, sin otro particular aprovecho la oportunidad para reiterarle las consideraciones de mi estima personal. Dicha carta de
        autorización tendrá efecto de declaración jurada según lo establecido en la Ley Nº 27444.
      </p>
      <p style="text-align:center;margin-top:100px;">
        Atentamente,
      </p>

      <div style="text-align:center;width:100%;margin-top:180px;">
        <div style="position:absolute;width:300px;margin-left:25%;">
          <div style="width:100%;border-bottom:1px solid black;"></div>
          <span style="font-size:16px;color:black;">'.$socio.'</span><br>
          <span style="font-size:16px;color:black;">'.$tipoDNI." Nº ".$nroDNI.'</span><br>
        </div>
      </div>
    </main>
  </body>
  </html>';

  require_once "../../../libs/pdf.php/vendor/autoload.php";
  $mpdf = new \Mpdf\Mpdf();
  $mpdf->WriteHTML($html);
  $mpdf->Output();
  exit;
?>
