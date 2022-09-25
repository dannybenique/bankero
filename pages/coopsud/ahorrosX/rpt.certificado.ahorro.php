<?php
  include_once("../../../includes/db_database.php");
  $codsocio = $_GET["codsocio"];
  $tiposerv = $_GET["tiposerv"];
  $numcert  = $_GET["numcert"];
  $codigo = explode("-",$codsocio);

  //suplente
  $suplente = "";
  if($_GET["suplente"]=="1"){
    $sql = "select ap_pater+' '+ap_mater+', '+nombres as suplente,condicion from coopSUD.dbo.COOP_DB_Suplentes where codagenc+'-'+codsocio='".$codsocio."'";
    $qry = $db->select($sql);
    $rs = $db->fetch_array($qry);
    $suplente = $rs["condicion"]." ".$rs["suplente"];
  }

  //socio
  $sql = "select case when s.dni<>'0' and s.ruc='0' and carnet='0' then s.ap_pater+' '+s.ap_mater+', '+s.nombres when s.dni='0' and s.ruc<>'0' and carnet='0' then s.raz_social when s.dni='0' and s.ruc='0' and carnet<>'0' then s.ap_pater+' '+s.ap_mater+', '+s.nombres ELSE 'ERROR' END as socio,case when s.dni<>'0' and s.ruc='0' and carnet='0' then 'DNI' when s.dni='0' and  s.ruc<>'0' and carnet='0' then 'RUC' when s.dni='0' and  s.ruc='0' and carnet<> '0' then 'CARNET' ELSE 'ERROR' END as doc,case when s.dni<>'0' and s.ruc='0' and carnet='0' then s.dni when s.dni='0' and  s.ruc<>'0' and carnet='0' then s.ruc when s.dni='0' and s.ruc='0' and carnet<> '0' then s.carnet ELSE 'ERROR' END as nrodoc,sa.codagenc+'-'+sa.codsocio as codigo,s.direccion,x.detalle as servicio,x.moneda,x.interes_2,x.aplica_2,sa.*,ah.numero as numcert,REPLACE(CONVERT(NVARCHAR, ah.fec_inicio, 103), ' ', '/') AS fec_ini,REPLACE(CONVERT(NVARCHAR, ah.fec_fin, 103), ' ', '/') AS fec_fin,ah.importe,ah.plazo,ah.tiporet,dbo.fn_GetImporteEnTexto(ah.importe,x.moneda) as importeTexto ";
  $sql .= "from coopSUD.dbo.coop_db_saldos as sa inner join coopSUD.dbo.coop_db_tipo_serv as x on sa.tipo_serv=x.tipo_serv inner join coopSUD.dbo.COOP_DB_socios_gen as s ON sa.codagenc=s.codagenc and sa.codsocio=s.codsocio LEFT OUTER JOIN coopSUD.dbo.COOP_DB_ahorros_plazo as ah ON sa.tipo_oper = ah.tipo_oper AND sa.codagenc = ah.codagenc AND sa.codsocio = ah.codsocio AND sa.tipo_serv = ah.tipo_serv ";
  $sql .= "where (sa.codagenc+'-'+sa.codsocio='".$codsocio."') and (sa.tipo_serv='".$tiposerv."') and (ah.numero='".$numcert."')";
  $qry = $db->select($sql);
  $rs = $db->fetch_array($qry);

  //documento html
  $html ='
  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Certificado de Ahorro</title>
    <style>
      .clearfix:after { content:""; display:table; clear:both; }
      body { position:relative; width:21cm; height:29.7cm; margin:0 auto; color:#000; background:#FFFFFF; font-family:Arial; }
      table { width:100%; border-collapse:collapse; border-spacing:0; font-size:20px; }
    </style>
  </head>
  <body>
    <main>
      <div style="width:100%;height:195px;"></div>
      <table style="width:100%;margin-bottom:53px;">
        <tr>
          <td style="width:180px;"></td>
          <td style="width:200px;"><b>'.$codigo[0].'</b></td>
          <td style="width:180px;"><b>'.$codigo[1].'</b></td>
          <td style=""><b>'.($rs["nrodoc"]).'</b></td>
        </tr>
      </table>
      <table style="width:100%;margin-bottom:9px;">
        <tr>
          <td style="width:210px;" rowspan="2"></td>
          <td style=""><b>'.($rs["socio"]).' '.$suplente.'</b></td>
          <td rowspan="2"></td>
        </tr>
        <tr>
          <td style="font-size:18px;height:35px;">'.($rs["direccion"]).'</td>
        </tr>
      </table>
      <table style="width:100%;margin-bottom:8px;">
        <tr>
          <td style="width:300px;"></td>
          <td style="width:440px;"><b>'.(($rs["moneda"]=="S")?("SOLES"):("DOLARES")).'</b></td>
          <td style=""><b>'.number_format($rs["importe"],2,".",",").'</b></td>
        </tr>
      </table>
      <table style="width:100%;margin-bottom:50px;">
        <tr>
          <td style="width:170px;"></td>
          <td style="font-size:18px;"><b>'.($rs["importeTexto"]).'</b></td>
        </tr>
      </table>
      <table style="width:100%;">
        <tr>
          <td style="width:300px;height:35px;" rowspan="3"></td>
          <td style="width:410px;height:35px;"><b>'.($rs["plazo"]).' días</b></td>
          <td style="height:35px;"><b>'.number_format($rs["interes_2"],2).'%</b></td>
          <td rowspan="3"></td>
        </tr>
        <tr>
          <td style="height:35px;"></td>
          <td style="height:35px;"><b>'.(($rs["tiporet"]=="V")?("AL VENCIMIENTO"):("MENSUAL")).'</b></td>
        </tr>
        <tr>
          <td style="height:35px;"><b>'.($rs["fec_ini"]).'</b></td>
          <td style="height:35px;"><b>'.($rs["fec_fin"]).'</b></td>
        </tr>
      </table>
    </main>
  </body>
  </html>';

  require_once "../../../libs/pdf.php/vendor/autoload.php";
  $mpdf = new \Mpdf\Mpdf(['orientation'=>'L']);
  $mpdf->WriteHTML($html);
  $mpdf->Output();
  exit;
?>
