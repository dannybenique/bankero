<?php
  include_once("../../../includes/db_database.php");
  include_once("../../../includes/web_config.php");
  $movimID = $_REQUEST["movimID"];
  //bancos
  $qry = $db->query_all("select * from bn_bancos where id=".$web->coopacID);
  $rs = reset($qry);
  $banco_nombre = strtoupper($rs["nombre"]);
  $banco_ruc = $rs["ruc"];

  $params = [":movimID"=>$movimID];
  //cabecera movim
  $sql = "select m.*,b.nombre as agencia,pr.nombre as producto,t.nombre as tipo_oper,o.nombre as moneda,to_char(fecha,'DD/MM/YYYY HH24:MI:SS') as fechamov,fn_get_persona(p.tipo_persona, p.ap_paterno, p.ap_materno, p.nombres) AS socio,p.nro_dui from bn_movim m join bn_bancos b on m.id_agencia=b.id join bn_productos pr on m.id_producto=pr.id join sis_tipos t on m.id_tipo_oper=t.id join personas p on m.id_socio=p.id join sis_tipos o on m.id_moneda=o.id where m.id=:movimID;";
  $qry = $db->query_all($sql,$params);
  if($qry) {
    $rs = reset($qry); 
    $mov_agencia = strtoupper($rs["agencia"]);
    $mov_tipo_oper = strtoupper($rs["tipo_oper"]);
    $mov_moneda = strtoupper($rs["moneda"]);
    $mov_producto =$rs["producto"];
    $mov_codigo = $rs["codigo"];
    $mov_fecha = $rs["fechamov"];
    $mov_socio = $rs["socio"];
    $mov_DNI = $rs["nro_dui"];
  }

  //detalle movim
  $detalle = "";
  $sql = "select d.*,mv.nombre as tipo_mov from bn_movim_det d join sis_mov mv on d.id_tipo_mov=mv.id where id_movim=:movimID order by item";
  $qry = $db->query_all($sql,$params);
  if($qry) {
    foreach($qry as $rs){
      $detalle .= '<tr><td style="text-align:left;">'.$rs["tipo_mov"].'</td><td style="text-align:right;">'.number_format($rs["importe"],2,".",",").'</td></tr>';
    }
  }
  
  //documento html
  $html ='
  <!DOCTYPE html>
  <html lang="en">
    <head>
      <meta charset="utf-8">
      <title>Voucher</title>
      <style>
        body { position: relative; margin: 0; color: #111; background: #FFFFFF; font-size: 10px; font-family: Arial; }
        .gridBordes th,.gridBordes td{border-bottom:1px solid #555555;border-left:1px solid #555555;}
        .clearfix:after { content: ""; display: table; clear: both; }
      </style>
    </head>
    <body>
    <main>
      <div class="clearfix">
        <div>
          <div style="font-size:10px;text-align:center;">
            <b><u>'.$banco_nombre.'</u></b><br>
            <span style="font-size:9px;">RUC: '.$banco_ruc.'</span>
          </div>
          <br><br>
          <table border="0" cellspacing="0" cellpadding="0" style="width:100%;font-size:7px;">
            <tbody>
              <tr>
                <td style="text-align:left;width:40px;">Agencia</td>
                <td style="text-align:left;">'.$mov_agencia.'</td>
              </tr>
              <tr>
                <td style="text-align:left;">Tipo Oper.</td>
                <td style="text-align:left;">'.$mov_tipo_oper.'&nbsp;&nbsp;/&nbsp;&nbsp;'.$mov_moneda.'</td>
              </tr>
              <tr>
                <td style="text-align:left;">Codigo</td>
                <td style="text-align:left;">'.$mov_codigo.'</td>
              </tr>
              <tr>
                <td style="text-align:left;">Fecha</td>
                <td style="text-align:left;">'.$mov_fecha.'</td>
              </tr>
              <tr>
                <td style="text-align:left;vertical-align:top;">Socio</td>
                <td style="text-align:left;">'.$mov_socio.' / '.$mov_DNI.'</td>
              </tr>
            </tbody>
          </table>
          <br>
          <table border="0" cellspacing="0" cellpadding="0" style="width:100%;font-size:8px;">
            <tbody>
              <tr style="">
                <th style="border-bottom:1px dotted black;font-weight:bold;text-align:left;">Detalle</th>
                <th style="border-bottom:1px dotted black;width:45px;font-weight:bold;text-align:right;">Importe</th>
              </tr>
              '.$detalle.'
            </tbody>
          </table>
        </div>
      </div>
    </main>
    </body>
  </html>';
  $footer = '<p style="text-align:justify;font-size:6px;">
              <b>NO OLVIDE,</b> pague a tiempo y evite cargos
            </p>';

  include_once("../../../libs/pdf.php/vendor/autoload.php");
  $mpdf = new \Mpdf\Mpdf([
    'format' => [55,100],
    'margin_left' => 1,
    'margin_right' => 1,
    'margin_top' => 2,
    'margin_bottom' => 1
  ]);
  $mpdf->WriteHTML($html);
  $mpdf->SetHTMLFooter($footer);
  $mpdf->Output('voucher.pdf','I');
  exit;
?>