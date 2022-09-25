<?php
  include_once("../../../includes/sess_verifica.php");

  if(isset($_SESSION["usr_ID"])){
    include_once("../../../includes/db_database.php");
    $codsocio = $_GET["codsocio"];
    $tiposerv = $_GET["tiposerv"];
    $numpres = $_GET["numpres"];
    $codigo = explode("-",$codsocio);

    //fecha y lugar
    $sql = "select FORMAT(getdate(),'dd') as dia,m.nombre as mes,year(getdate()) as yyyy, a.ciudad from tb_agencias a,tb_workers e,sis_meses m where a.ID=e.id_agencia and m.ID=month(getdate()) and e.id_persona=".$_SESSION["usr_ID"];
    $qry = $db->select($sql);
    $rs = $db->fetch_array($qry);
    $fecha = $rs["dia"]." de ".$rs["mes"]." de ".$rs["yyyy"];
    $ciudad = $rs["ciudad"];

    //prestamo cabecera
    $sql = "select case when s.dni<>'0' and s.ruc='0' and carnet='0' then s.ap_pater+' '+s.ap_mater+', '+s.nombres when s.dni='0' and s.ruc<>'0' and carnet='0' then s.raz_social when s.dni='0' and s.ruc='0' and carnet<>'0' then s.ap_pater+' '+s.ap_mater+', '+s.nombres ELSE 'ERROR' END as socio,case when s.dni<>'0' and s.ruc='0' and carnet='0' then 'DNI' when s.dni='0' and  s.ruc<>'0' and carnet='0' then 'RUC' when s.dni='0' and  s.ruc='0' and carnet<> '0' then 'CARNET' ELSE 'ERROR' END as doc,case when s.dni<>'0' and s.ruc='0' and carnet='0' then s.dni when s.dni='0' and s.ruc<>'0' and carnet='0' then s.ruc when s.dni='0' and s.ruc='0' and carnet<>'0' then s.carnet ELSE 'ERROR' END as nrodoc,x.detalle as servicio,x.moneda,x.interes_2,format(p.fec_otorg,'dd') as dia_otorg,coopSUD.dbo.fc_IntAnual(x.interes_1) as TEA,coopSUD.dbo.fc_IntAnualC(x.interes_1) as TCEA,coopSUD.dbo.fc_IntAnual(x.interes_2) as TMEA,p.num_pres,p.num_cuot,p.importe,p.saldo,p.tipo_cred,d.interes ";
    $sql.= "from (select sum(int_comp) as interes from coopSUD.dbo.COOP_DB_prestamos_det where codagenc='".$codigo[0]."' and codsocio='".$codigo[1]."' and num_pres='".$numpres."' ) as d,coopSUD.dbo.COOP_DB_prestamos p,coopSUD.dbo.COOP_DB_socios_gen s,coopSUD.dbo.COOP_DB_tipo_serv x ";
    $sql.= "where x.tipo_serv=p.tipo_serv and s.codagenc=p.codagenc and s.codsocio=p.codsocio and p.codagenc='".$codigo[0]."' and p.codsocio='".$codigo[1]."' and p.tipo_serv='".$tiposerv."' and p.num_pres='".$numpres."'";
    $qry = $db->select($sql);
    $rs = $db->fetch_array($qry);

    $socio = utf8_encode($rs["socio"]);
    $tipoDNI = $rs["doc"];
    $nroDNI = $rs["nrodoc"];
    $servicio = strtoupper($rs["servicio"])." - ".$rs["num_pres"];
    $moneda = ($rs["moneda"]=="S")?("Soles"):("Dolares");
    $importe = (($rs["moneda"]=="S")?("S/. "):("US$ ")).number_format($rs["importe"],2,".",",");
    $interes = (($rs["moneda"]=="S")?("S/. "):("US$ ")).number_format($rs["interes"],2,".",",");
    $saldo = (($rs["moneda"]=="S")?("S/. "):("US$ ")).number_format($rs["saldo"],2,".",",");
    $TEA = number_format($rs["TEA"],2); //tasa efectiva anual
    $TCEA = number_format($rs["TCEA"],2); //tasa costo efectiva anual
    $TMEA = number_format($rs["TMEA"],2); //tasa moratoria efectiva anual

    //tipo y destino de credito
    $sqlx = "select o.tipo_cred,o.destino,c.detalle as tp_credito,d.detalle as dt_credito from coopSUD.dbo.COOP_DB_sol_prestamos o,coopSUD.dbo.COOP_DB_tipo_cred c,coopSUD.dbo.COOP_DB_dest_credito d where o.tipo_cred=c.tipo_cred and o.tipo_cred=d.tipo_cred and o.destino=d.destino and o.codagencia='".$codigo[0]."' and o.codsocio='".$codigo[1]."' and o.tipo_serv='".$tiposerv."' and o.num_prest='".$numpres."'";
    $qryX = $db->select($sqlx);
    if($db->has_rows($qryX)) {
      $rx = $db->fetch_array($qryX);
      $tipocred = strtoupper($rx["tp_credito"]);
      $destcred = strtoupper($rx["dt_credito"]);
    } else {
      $tipocred = strtoupper("*****error*****");
      $destcred = strtoupper("*****error*****");
    }

    //documento html
    $html ='
    <!DOCTYPE html>
    <html lang="en">
      <head>
        <meta charset="utf-8">
        <title>Hoja Resumen de Credito</title>
        <style>
          .clearfix:after {content: "";display: table;clear: both;}
          body { position: relative; width: 21cm; height: 29.7cm; margin:0 auto; color:#555; background:#FFFFFF; font-size:10px; font-family:Arial;}
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
            <div style="width:340px;float:right;">
              <h3 style="width:340px;background:#white;color:#222;font-size:16px;border-radius:0.3em;margin:0;padding:0.3em;text-align:center;">HOJA RESUMEN DE CREDITO (SOCIO)</h3>
            </div>
          </div>
          <hr/>
          <div style="font-size:10px;margin-bottom:10px;">
            El presente documento forma parte integrante del contrato de crédito como acto cooperativo, suscrito por las partes, y tiene por finalidad establecer las
            condiciones del crédito (socio), y los aspectos más relevantes, tasa de interés compensatorio, tasa de interés moratorio, comisiones, gastos y el resumen
            de las condiciones contractuales para las partes.
          </div>
          <table border="0" cellspacing="0" cellpadding="0" class="tablacredito">
            <tbody>
              <tr style="background:orange;">
                <th colspan="4" style="text-align:center;font-size:14px;color:#444;">DATOS DEL CREDITO</th>
              </tr>
              <tr>
                <td class="mitr" style="width:200px;">1. Nombre del Socio</td>
                <td class="mitr" colspan="3" style="font-size:12px;font-weight:bold;color:black;">'.$socio.'</td>
              </tr>
              <tr>
                <td class="mitr" style="">2. Codigo del Socio</td>
                <td class="mitr" colspan="3" style="font-size:12px;font-weight:bold;color:black;">'.$codsocio.'</td>
              </tr>
              <tr>
                <td class="mitr" style="">3. Documento de Identidad</td>
                <td class="mitr" colspan="3" style="font-size:12px;font-weight:bold;color:black;">'.$rs["doc"]." ".$rs["nrodoc"].'</td>
              </tr>
              <tr>
                <td class="mitr" style="">4. Producto</td>
                <td class="mitr" colspan="3" style="font-size:12px;font-weight:bold;color:black;">'.$servicio.'</td>
              </tr>
              <tr>
                <td class="mitr" style="">5. Moneda y Monto del Credito aprobado.</td>
                <td class="mitr" style="font-size:12px;width:100px;color:black;">'.$moneda.'</td>
                <td class="mitr" colspan="2" style="font-size:12px;font-weight:bold;color:black;">'.$importe.'</td>
              </tr>
              <tr>
                <td class="mitr" style="">6. Monto desembolsado</td>
                <td class="mitr" colspan="3" style="font-size:12px;font-weight:bold;color:black;">'.$importe.'</td>
              </tr>
              <tr>
                <td class="mitr" style="">7. Tipo de Crédito</td>
                <td class="mitr" colspan="3" style="font-size:12px;font-weight:bold;color:black;">'.$tipocred.'</td>
              </tr>
              <tr>
                <td class="mitr" style="">8. Destino del Crédito</td>
                <td class="mitr" colspan="3" style="font-size:12px;font-weight:bold;color:black;">'.$destcred.'</td>
              </tr>
              <tr>
                <td class="mitr" style="">9. Plazo del Crédito</td>
                <td class="mitr" style="font-size:12px;width:100px;color:black;">Dias calendario</td>
                <td class="mitr" style="font-size:12px;width:100px;color:black;">360 días</td>
                <td class="mitr" style="font-size:12px;font-weight:bold;color:black;">A: '.$rs["num_cuot"].' cuotas</td>
              </tr>
              <tr>
                <td class="mitr" style="width:200px;">10. Fecha de Vencimiento</td>
                <td class="mitr" colspan="3" style="font-size:12px;font-weight:bold;color:black;">'.($rs["dia_otorg"]).' DE CADA MES</td>
              </tr>
              <tr>
                <td class="mitr" style="">11. Frecuencia de pago</td>
                <td class="mitr" colspan="3" style="font-size:12px;font-weight:bold;color:black;">Mensual</td>
              </tr>
              <tr>
                <td class="mitr" style="">12. Tasa de Interes compensatoria efectiva anual (TEA 360 dias)</td>
                <td class="mitr" colspan="3" style="font-size:12px;font-weight:bold;color:black;">'.$TEA.'%</td>
              </tr>
              <tr>
                <td class="mitr" style="">13. Tasa de costo Efectiva Anual (TCEA)</td>
                <td class="mitr" colspan="3" style="font-size:12px;font-weight:bold;color:black;">'.$TCEA.'%</td>
              </tr>
              <tr>
                <td class="mitr" style="">14.Monto total de interés Compensatorio</td>
                <td class="mitr" colspan="3" style="font-size:12px;font-weight:bold;color:black;">'.$interes.'</td>
              </tr>
              <tr>
                <td class="mitr" style="">15. Saldo Capital</td>
                <td class="mitr" colspan="3" style="font-size:12px;font-weight:bold;color:black;">'.$saldo.'</td>
              </tr>
              <tr>
                <td class="mitr" style="">16. Tasa Efectiva Anual Moratoria (TEA 360 días)</td>
                <td class="mitr" colspan="3" style="font-size:12px;font-weight:bold;color:black;">'.$TMEA.'%</td>
              </tr>
            </tbody>
          </table>
          <table border="0" cellspacing="0" cellpadding="0" class="tablacredito">
            <tbody>
              <tr style="background:orange;">
                <th colspan="4" style="text-align:center;font-size:14px;color:#444;">COMISIONES Y GASTOS APLICABLES AL CRÉDITO (socio)</th>
              </tr>
              <tr style="background:#dfdfdf;">
                <td class="mitr" style="">SERVICIOS VARIOS</td>
                <td class="mitr" colspan="2" style="font-size:12px;">IMPORTE</td>
                <td class="mitr" style="font-size:12px;">OPORTUNIDAD DE COBRO</td>
              </tr>
              <tr>
                <td class="mitr" style="">
                  <ul>
                    <li>Comisión por envío mensual de extractos de cuenta o saldo deudor</li>
                    <li>Constancia de cancelación de crédito (no adeudo)</li>
                    <li>Cronograma de plan de pagos</li>
                    <li>Reporte de Búsqueda(de inmuebles) SUNARP</li>
                  </ul>
                </td>
                <td class="mitr" colspan="2">Previa solicitud (S/. 0.00)
                  Por primera vez S/. 0.00 posterior S/. 10.00 por cada solicitud.<br><br>
                  Por primera vez S/. 0.00 posterior S/. 10.00 por cada solicitud.<br><br>
                  Según tupa Sunarp.</td>
                <td class="mitr" style="text-align:center;">Mensual</td>
              </tr>
              <tr style="background:orange;">
                <td colspan="4" style="color:#444;font-size:8px;">Nota: Las comisiones o gastos se basará en un costo real y demostrable que serán trasladados al socio, por las gestiones relacionadas a su cobro, dichas condiciones de comisiones y gastos señalados en el contrato cooperativo y  documento.</td>
              </tr>
            </tbody>
          </table>
          <div style="font-size:8px;margin-bottom:5px;"> <sup style="color:black;">(1)</sup>El monto total de interés compensatorios equivalente en moneda nacional ha sido calculada en base al tipo de cambio venta vigente para las operaciones del BCR al momento de la emisión del presente documento.</div>

          <table border="0" cellspacing="0" cellpadding="0" class="tablacredito" style="margin-bottom:10px;">
            <tbody>
              <tr style="background:orange;">
                <th colspan="4" style="text-align:center;font-size:14px;color:#444;">PENALIDADES</th>
              </tr>
              <tr style="background:#dfdfdf;">
                <td class="mitr" colspan="2" style="">TARIFARIO EN SOLES POR DIAS DE ATRASO</td>
                <td class="mitr" colspan="2" style="font-size:12px;">MONTO</td>
              </tr>
              <tr>
                <td class="mitr" colspan="2" style="">DE 9 A 30 DIAS</td>
                <td class="mitr" colspan="2">S/. 10.00</td>
              </tr>
              <tr>
                <td class="mitr" colspan="2" style="">DE 31 A 60 DIAS</td>
                <td class="mitr" colspan="2">S/. 25.00</td>
              </tr>
              <tr>
                <td class="mitr" colspan="2" style="">DE 61 A 90 DIAS</td>
                <td class="mitr" colspan="2">S/. 50.00</td>
              </tr>
              <tr>
                <td class="mitr" colspan="2" style="">DE 90 A MAS</td>
                <td class="mitr" colspan="2">S/. 80.00</td>
              </tr>
              <tr>
                <td class="mitr" colspan="4" style="font-size:8px;">
                  <span style="color:red;">No incluye costos o gastos notariales o judiciales que estarán a cargo del socio en caso incurra en cobranza judicial o prejudicial.</span>
                  En caso de incumplimiento en el pago en las fechas pactadas, LOS PRESTATARIOS serán Reportados, con la calificación que corresponda, a la Central de Riesgo.
                </td>
              </tr>
            </tbody>
          </table>
          <table border="0" cellspacing="0" cellpadding="0" class="tablacredito" style="margin-bottom:10px;">
            <tbody>
              <tr style="background:orange;">
                <th colspan="3" style="text-align:center;font-size:14px;color:#444;">DATOS DEL SEGURO</th>
              </tr>
              <tr style="background:orange;">
                <td class="mitr" style="width:38%;"><b>Informacion sobre Seguros</b></td>
                <td class="mitr" style="width:38%;font-size:12px;"><b>Desgravamen</b></td>
                <td class="mitr" style="width:24%;font-size:12px;"><b>Oportunidad de Cobro</b></td>
              </tr>
              <tr>
                <td class="mitr">Monto o tasa de la prima</td>
                <td class="mitr">Prima minima S/. 1.00   0.10% (sobre el saldo deudor)</td>
                <td class="mitr" rowspan="3" style="text-align:center;">Mensual</td>
              </tr>
              <tr>
                <td class="mitr">Nombre de la compañía de Seguros</td>
                <td class="mitr">SERVIPERU</td>
              </tr>
              <tr>
                <td class="mitr">N° de la Poliza</td>
                <td class="mitr">082/P</td>
              </tr>
              <tr>
                <td class="mitr">Nombre del Asegurado</td>
                <td class="mitr" colspan="2">'.$socio.'</td>
              </tr>
            </tbody>
          </table>
          <table border="0" cellspacing="0" cellpadding="0" class="tablacredito" style="margin-bottom:10px;">
            <tbody>
              <tr style="background:orange;">
                <td class="mitr" style="width:38%;"><b>Informacion sobre Seguros</b></td>
                <td class="mitr" style="width:38%;font-size:12px;"><b>MULTIRIESGO</b></td>
                <td class="mitr" style="width:24%;font-size:12px;"><b>Oportunidad de Cobro</b></td>
              </tr>
              <tr>
                <td class="mitr">Monto o tasa de la prima (anual)</td>
                <td class="mitr"></td>
                <td class="mitr" rowspan="3" style="text-align:center;">Anual</td>
              </tr>
              <tr>
                <td class="mitr">Nombre de la compañía de Seguros</td>
                <td class="mitr">MAPFRE</td>
              </tr>
              <tr>
                <td class="mitr">N° de la Poliza</td>
                <td class="mitr"></td>
              </tr>
              <tr>
                <td class="mitr">Nombre del Asegurado</td>
                <td class="mitr" colspan="2">'.$socio.'</td>
              </tr>
            </tbody>
          </table>
          <table border="0" cellspacing="0" cellpadding="0" class="tablacredito">
            <tbody>
              <tr style="background:orange;">
                <td class="mitr" style="width:38%;"><b>Informacion sobre Seguros</b></td>
                <td class="mitr" style="width:38%;font-size:12px;"><b>VEHICULAR</b></td>
                <td class="mitr" style="width:24%;font-size:12px;"><b>Oportunidad de Cobro</b></td>
              </tr>
              <tr>
                <td class="mitr">Monto o tasa de la prima</td>
                <td class="mitr"></td>
                <td class="mitr" rowspan="3" style="text-align:center;">Anual</td>
              </tr>
              <tr>
                <td class="mitr">Nombre de la compañía de Seguros</td>
                <td class="mitr">MAPFRE</td>
              </tr>
              <tr>
                <td class="mitr">N° de la Poliza</td>
                <td class="mitr"></td>
              </tr>
              <tr>
                <td class="mitr">Monto de tasa GPS/Vehicular</td>
                <td class="mitr">S/. 35.00</td>
                <td class="mitr" rowspan="2" style="text-align:center;">Mensual</td>
              </tr>
              <tr>
                <td class="mitr">Nombre de la Compañía GPS</td>
                <td class="mitr">TELCOM</td>
              </tr>
              <tr>
                <td class="mitr">Nombre del Asegurado</td>
                <td class="mitr" colspan="2">'.$socio.'</td>
              </tr>
              <tr style="background:white;">
                <td colspan="3" style="color:#444;font-size:8px;">Nota: Los riesgos objeto de la cobertura  y demas condiciones de su poliza podran ser consultados a travez de  las agencias de La Cooperativa como los canales de atencion autorizados por la misma.</td>
              </tr>
            </tbody>
          </table>

          <div style="font-size:10px;margin:3px 0 3px 0;">18. EL SOCIO(a) podrá solicitar una constancia de cancelación del crédito detallado en el presente documento solo por única vez, a partir de la segunda constancia se pagará una comisión, cuyo importe aparece en el tarifario de comisiones.</div>
          <div style="font-size:10px;margin:3px 0 3px 0;">19. En caso EL SOCIO(a) que efectúe un pago en exceso que no sea una amortización o un pago anticipado del crédito, dicho exceso les será devuelto por la COOPAC GRUPO INVERESION SUDAMERICANO.</div>
          <div style="font-size:10px;margin:3px 0 3px 0;">20. Las garantías reales otorgadas por el cliente a favor de COOPAC GRUPO INVERSION SUDAMERICANO, respaldan a todas las obligaciones que tiene el socio con la Obligación asumida.</div>
          <p style="font-size:10px;font-weight:bold;">DECLARACION FINAL</p>
          <p style="font-size:10px;">EL SOCIO(a) y fiadores solidarios, declaramos haber leído previamente a su suscripción, el contrato de crédito cooperativo, cronograma de pago,
            hoja resumen, seguro de desgravamen, seguro mobiliario inmobiliario (garantía real) y cláusula adicional de fianza; asimismo, que hemos sido instruidos a cerca de los
            alcances y significado de los términos y condiciones establecidos en dichos documentos habiendo sido absueltas y aclaradas nuestras consultas y/o dudas, por lo que,
            declaramos tener pleno y exacto conocimiento de las condiciones establecidas en dichos documentos. Se adjunta:
          </p>
          <ul>
            <li>Hoja Resumen de Créditos</li>
            <li>Cronograma de Pagos</li>
            <li>Contrato de Crédito Cooperativo</li>
          </ul>
          <div style="text-align:right;"> En la ciudad de '.$ciudad.', '.$fecha.' </div>

          <div style="text-align:center;width:100%;margin-top:50px;">
            <div style="position:absolute;width:260px;float:left;">
              <div style="margin-left:190px;width:70px;text-align:center;">
                <div style="border:1px solid black;width:100%;height:80px;">&nbsp;</div>
                <i style="font-size:8px;">Huella</i>
              </div>
              <div style="position:absolute;margin-top:-30px;width:190px;">
                <div style="width:100%;border-bottom:1px solid #555555;"></div>
                <span style="font-size:12px;color:#555;">EL SOCIO(a) (Titular)<br>'.$tipoDNI.": ".$nroDNI.'</span><br>
              </div>
            </div>
            <div style="position:absolute;width:260px;float:right;">
              <div style="margin-left:190px;width:70px;text-align:center;">
                <div style="border:1px solid black;width:100%;height:80px;">&nbsp;</div>
                <i style="font-size:8px;">Huella</i>
              </div>
              <div style="position:absolute;margin-top:-30px;width:190px;">
                <div style="width:100%;border-bottom:1px solid #555555;"></div>
                <span style="font-size:12px;color:#555;">COOPAC GRUPO<br>INVERSION SUDAMERICANO</span><br>
              </div>
            </div>
            <div style="position:absolute;width:260px;margin-top:70px;float:left;">
              <div style="margin-left:190px;width:70px;text-align:center;">
                <div style="border:1px solid black;width:100%;height:80px;">&nbsp;</div>
                <i style="font-size:8px;">Huella</i>
              </div>
              <div style="position:absolute;margin-top:-30px;width:190px;">
                <div style="width:100%;border-bottom:1px solid #555555;"></div>
                <span style="font-size:12px;color:#555;">EL SOCIO(a) (Conyuge)<br>DNI:<span style="color:white;">00000000</span></span>
              </div>
            </div>
            <div style="position:absolute;width:260px;float:right;">
              <div style="margin-left:190px;width:70px;text-align:center;">
                <div style="border:1px solid black;width:100%;height:80px;">&nbsp;</div>
                <i style="font-size:8px;">Huella</i>
              </div>
              <div style="position:absolute;margin-top:-30px;width:190px;">
                <div style="width:100%;border-bottom:1px solid #555555;"></div>
                <span style="font-size:12px;color:#555;">EL AVAL y/o Fiador Solidario<br>DNI:<span style="color:white;">00000000</span></span><br>
              </div>
            </div>
            <div style="position:absolute;width:260px;margin-top:70px;float:left;">
              <div style="margin-left:190px;width:70px;text-align:center;">
                <div style="border:1px solid black;width:100%;height:80px;">&nbsp;</div>
                <i style="font-size:8px;">Huella</i>
              </div>
              <div style="position:absolute;margin-top:-30px;width:190px;">
                <div style="width:100%;border-bottom:1px solid #555555;"></div>
                <span style="font-size:12px;color:#555;">EL AVAL y/o Fiador Solidario<br>DNI:<span style="color:white;">00000000</span></span>
              </div>
            </div>
            <div style="position:absolute;width:260px;float:right;">
              <div style="margin-left:190px;width:70px;text-align:center;">
                <div style="border:1px solid black;width:100%;height:80px;">&nbsp;</div>
                <i style="font-size:8px;">Huella</i>
              </div>
              <div style="position:absolute;margin-top:-30px;width:190px;">
                <div style="width:100%;border-bottom:1px solid #555555;"></div>
                <span style="font-size:12px;color:#555;">EL AVAL y/o Fiador Solidario<br>DNI:<span style="color:white;">00000000</span></span><br>
              </div>
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
  }
?>
