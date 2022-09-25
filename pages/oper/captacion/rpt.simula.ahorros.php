<?php
  include_once("../../../includes/sess_verifica.php");
  include_once("../../../includes/db_database.php");
  $personaID = $_GET["personaID"];
  $productoID = $_GET["productoID"];
  $tasa = $_GET["tasa"];
  $capital = $_GET["importe"];
  $interes = $_GET["interes"];
  $fechaIni = $_GET["fechaini"];
  $plazo = $_GET["plazo"];

  $qry = $db->select("select * from dbo.vw_workers where ID=".($_SESSION["usr_ID"]));
  $rs = $db->fetch_array($qry);
  $worker = utf8_encode($rs["worker"]);
  $cargo = utf8_encode($rs["cargo"]);
  $agencia = utf8_encode($rs["agencia"]);

  $qry = $db->select("select * from dbo.vw_personas where ID=".($personaID));
  $rs = $db->fetch_array($qry);
  $persona = utf8_encode($rs["persona"]);
  $tipoDNI = ($rs["doc"]);
  $DNI = ($rs["DNI"]);

  $qry = $db->select("select p.*,m.detalle as moneda from dbo.tb_productos p,tb_tipo_mone m where m.ID=p.id_tipo_mone and p.ID=".($productoID));
  $rs = $db->fetch_array($qry);
  $producto = utf8_encode($rs["nombre"]);
  $moneda = utf8_encode($rs["moneda"]);
  $fila = "";
  //date("Y,m,d",strtotime("+".$x." month",$fechaIni))
  switch($productoID){
    case 106: case 127: //ahorro superpension MN
      $interes = $interes/$plazo;
      $total = $capital + $interes;
      $fila .= '<table><tr>';
      $fila .= '<th class="planpagosTH" style="width:50px;text-align:right;">Nro</th>';
      $fila .= '<th class="planpagosTH" style="width:80px;">Fecha</th>';
      $fila .= '<th class="planpagosTH" style="width:110px;text-align:right;">Total</th>';
      $fila .= '<th class="planpagosTH" style="width:110px;text-align:right;">Capital</th>';
      $fila .= '<th class="planpagosTH" style="width:110px;text-align:right;">Interes</th>';
      $fila .= '</tr>';
      for($x=1; $x<=$plazo; $x++){
        $fila .= '<tr>';
        $fila .= '<td class="planpagosTD" style="text-align:right;">'.$x.'</td>';
        $fila .= '<td class="planpagosTD" style="text-align:right;">'.date("d/m/Y",strtotime($fechaIni." +".$x." month")).'</td>';
        $fila .= '<td class="planpagosTD" style="text-align:right;">'.number_format(($x==$plazo?$total:$interes),2).'</td>';
        $fila .= '<td class="planpagosTD" style="text-align:right;">'.number_format(($x==$plazo?$capital:0),2).'</td>';
        $fila .= '<td class="planpagosTD" style="text-align:right;">'.number_format($interes,2).'</td>';
        $fila .= '</tr>';
      }
      $fila .= '</table>';
      break;
    case 107: case 128: //ahorro plazo fijo MN
      $fila .= '<div style="font-size:18px;margin-bottom:150px;">';
      $fila .= '<span>Fecha Inicial: <b style="color:black;">'.date("d/m/Y",strtotime($fechaIni))."</b></span><br>";
      $fila .= '<span>Fecha Final: <b style="color:black;">'.date("d/m/Y",strtotime($fechaIni." +".$plazo." month"))."</b></span><br>";
      $fila .= '<span>Capital: <b style="color:black;">'.number_format($capital,2)."</b></span><br>";
      $fila .= '<span>Interes: <b style="color:black;">'.number_format($interes,2)."</b></span><br>";
      $fila .= '<span>Total: <b style="color:black;">'.number_format($capital+$interes,2)."</b></span><br>";
      $fila .= '</div>';
      break;
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
        table {border-collapse: collapse;border-spacing: 0;margin-bottom: 20px; }
        .planpagosTH{border-bottom:2px solid #ccc;}
        .planpagosTD{border-bottom:1px solid #ccc;}
      </style>
    </head>
    <body>
      <main>
        <div style="position:relative;">
          <div style="float:left;width:100px;"><img src="../../../includes/pdf/plantilla/img/logo.jpg" style="width:100px;"/></div>
        </div>
        <hr/>
        <div style="margin-top:20px;margin-bottom:20px;">
          <table style="width:100%;">
            <tr>
              <td style="border-right:5px solid #999;width:300px;vertical-align:top;">
                <div><span>CLIENTE: </span> <span style="color:black;font-weight:bold;">'.$persona.'</span></div>
                <div><span>'.$tipoDNI.': </span> <span style="color:black;font-weight:bold;">'.$DNI.'</span></div>
                <div><span>AGENCIA: </span> <span style="color:black;font-weight:bold;">'.$agencia.'</span></div>
              </td>
              <td style="width:5px;">
              </td>
              <td>
                <div><span>PRODUCTO: </span> <span style="color:black;font-weight:bold;">'.$producto.'</span></div>
                <div><span>TASA: </span> <span style="color:black;font-weight:bold;">'.number_format($tasa,2).'%</span></div>
                <div><span>MONEDA: </span> <span style="color:black;font-weight:bold;">'.$moneda.'</span></div>
                <div><span>MONTO: </span> <span style="color:black;font-weight:bold;">'.number_format($capital,2).'</span></div>
                <div><span>PLAZO: </span> <span style="color:black;font-weight:bold;">'.$plazo.' meses</span></div>
              </td>
            </tr>
          </table>
        </div>
        '.$fila.'
        <div style="margin-top:50px;">
          <h1>Formas de Deposito</h1>
          <ul>
            <li>Cuenta de Ahorros MN (soles) - CAJA AREQUIPA: <b>002 047 901 021 0000 200 3</b></li>
            <li>Codigo Interbancario MN (soles) (CCI) - CAJA AREQUIPA: <b>803 - 011 - 002047901002 - 65</b></li>
            <li>Codigo Interbancario ME (dolares) (CCI) - CAJA AREQUIPA: <b>803 - 011 - 002047901004 - 61</b></li>
            <li>Cheque de Gerencia: <b>coopac Grupo Inversion Sudamericano - RUC 20601390419</b></li>
            <li>En Efectivo: <b>en ventanilla de nuestra agencias</b></li>
          </ul>
        </div>
        <div style="text-align:center;width:100%;margin-top:90px;">
          <div style="position:absolute;width:300px;margin-left:28%;">
            <div style="width:100%;border-bottom:1px solid #555555;"></div>
            <b style="font-size:9px;color:#555;"><i>GRUPO INVERSION SUDAMERICANO</i></b><br>
            <b style="font-size:9px;color:#555;text-transform:uppercase">'.$cargo.'</b>
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
