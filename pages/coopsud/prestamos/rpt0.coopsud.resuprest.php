<?php
  include_once("../../../includes/db_database.php");
  $codsocio = $_GET["codsocio"];
  $tiposerv = $_GET["tiposerv"];
  $numpres = $_GET["numpres"];
  $codigo = explode("-",$codsocio);

  //prestamo cabecera
  $sql = "select case when s.dni<>'0' and s.ruc='0' and carnet='0' then s.ap_pater+' '+s.ap_mater+', '+s.nombres when s.dni='0' and s.ruc<>'0' and carnet='0' then s.raz_social when s.dni='0' and s.ruc='0' and carnet<>'0' then s.ap_pater+' '+s.ap_mater+', '+s.nombres ELSE 'ERROR' END as socio,case when s.dni<>'0' and s.ruc='0' and carnet='0' then 'DNI' when s.dni='0' and  s.ruc<>'0' and carnet='0' then 'RUC' when s.dni='0' and  s.ruc='0' and carnet<> '0' then 'CARNET' ELSE 'ERROR' END as doc,case when s.dni<>'0' and s.ruc='0' and carnet='0' then s.dni when s.dni='0' and s.ruc<>'0' and carnet='0' then s.ruc when s.dni='0' and s.ruc='0' and carnet<>'0' then s.carnet ELSE 'ERROR' END as nrodoc,x.detalle as servicio,x.moneda,x.interes_2,format(p.fec_otorg,'dd') as dia_otorg,((power(1+x.interes_1,360.00)-1)*100) as TEA,((power(1+x.interes_2,360.00)-1)*100) as TEAM,p.num_cuot,p.importe,p.saldo,p.tipo_cred,d.interes ";
  $sql.= "from (select sum(int_comp) as interes from coopSUD.dbo.COOP_DB_prestamos_det where codagenc='".$codigo[0]."' and codsocio='".$codigo[1]."' and num_pres='".$numpres."' ) as d,coopSUD.dbo.COOP_DB_prestamos p,coopSUD.dbo.COOP_DB_socios_gen s,coopSUD.dbo.COOP_DB_tipo_serv x ";
  $sql.= "where x.tipo_serv=p.tipo_serv and s.codagenc=p.codagenc and s.codsocio=p.codsocio and p.codagenc='".$codigo[0]."' and p.codsocio='".$codigo[1]."' and p.tipo_serv='".$tiposerv."' and p.num_pres='".$numpres."'";
  $qry = $db->select($sql);
  $rs = $db->fetch_array($qry);

  $socio = utf8_encode($rs["socio"]);
  $tipoDNI = $rs["doc"];
  $nroDNI = $rs["nrodoc"];
  $servicio = strtoupper($rs["servicio"]);
  $moneda = ($rs["moneda"]=="S")?("Soles"):("Dolares");
  $importe = (($rs["moneda"]=="S")?("S/. "):("US$ ")).number_format($rs["importe"],2,".",",");
  $interes = (($rs["moneda"]=="S")?("S/. "):("US$ ")).number_format($rs["interes"],2,".",",");
  $saldo = (($rs["moneda"]=="S")?("S/. "):("US$ ")).number_format($rs["saldo"],2,".",",");
  $TEA = number_format($rs["TEA"],2); //tasa efectiva anual
  $TEAM = number_format($rs["TEAM"],2); //tasa efectiva moratoria anual

  //tipo y destino de credito
  $sql = "select o.tipo_cred,o.destino,c.detalle as tp_credito,d.detalle as dt_credito from coopSUD.dbo.COOP_DB_sol_prestamos o,coopSUD.dbo.COOP_DB_tipo_cred c,coopSUD.dbo.COOP_DB_dest_credito d where o.tipo_cred=c.tipo_cred and o.tipo_cred=d.tipo_cred and o.destino=d.destino and o.codagencia='".$codigo[0]."' and o.codsocio='".$codigo[1]."' and o.tipo_serv='".$tiposerv."' and o.num_prest='".$numpres."'";
  $qryX = $db->select($sql);
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
            <h3 style="width:340px;background:#white;color:#222;font-size:16px;border-radius:0.3em;margin:0;padding:0.3em;text-align:center;">HOJA DE RESUMEN DE CREDITO (SOCIO)</h3>
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
              <th colspan="4" style="height:25px;text-align:center;font-size:14px;color:#444;">DATOS DEL CREDITO</th>
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
              <td class="mitr" style="width:200px;">11. Frecuencia de pago</td>
              <td class="mitr" colspan="3" style="font-size:12px;font-weight:bold;color:black;">Mensual</td>
            </tr>
            <tr>
              <td class="mitr" style="width:200px;">12. Tasa de Interes compensatoria efectiva anual (TEA 360 dias)</td>
              <td class="mitr" colspan="3" style="font-size:12px;font-weight:bold;color:black;">'.$TEA.'%</td>
            </tr>
            <tr>
              <td class="mitr" style="width:200px;">13.Monto total de interés Compensatorio</td>
              <td class="mitr" colspan="3" style="font-size:12px;font-weight:bold;color:black;">'.$interes.'</td>
            </tr>
            <tr>
              <td class="mitr" style="width:200px;">14. Saldo Capital</td>
              <td class="mitr" colspan="3" style="font-size:12px;font-weight:bold;color:black;">'.$saldo.'</td>
            </tr>
            <tr>
              <td class="mitr" style="width:200px;">15. Tasa Efectiva Anual Moratoria (TEA 360 días)</td>
              <td class="mitr" colspan="3" style="font-size:12px;font-weight:bold;color:black;">'.$TEAM.'%</td>
            </tr>
            <tr>
              <td class="mitr" style="width:200px;">16. Tasa de costo Efectiva Anual (TCEA)</td>
              <td class="mitr" colspan="3" style="font-size:12px;font-weight:bold;color:black;"></td>
            </tr>
          </tbody>
        </table>
        <table border="0" cellspacing="0" cellpadding="0" class="tablacredito">
          <tbody>
            <tr style="background:orange;">
              <th colspan="4" style="height:15px;text-align:center;font-size:14px;"></th>
            </tr>
            <tr>
              <td class="mitr" style="width:200px;">17. Información sobre seguros</td>
              <td class="mitr" colspan="2" style="font-size:12px;">Desgravamen</td>
              <td class="mitr" style="text-align:center;">Contra todo riesgo</td>
            </tr>
            <tr style="background:#dfdfdf;">
              <td class="mitr" style="">Tipo de crédito</td>
              <td class="mitr" colspan="3" style="font-size:12px;">Suma asegurada / monto</td>
            </tr>
            <tr>
              <td class="mitr" style="">Pyme<br>Consumo<br>Hipotecario<br>Custodia<br>GPS</td>
              <td class="mitr" style="width:150px;"></td>
              <td class="mitr" style="width:150px;">Porcentaje (*)<br><br>0.1%</td>
              <td class="mitr" style="text-align:center;">En cada cuota</td>
            </tr>
            <tr style="background:orange;">
              <th colspan="4" style="height:25px;text-align:center;font-size:14px;color:#444;">COMISIONES Y GASTOS APLICABLES AL CRÉDITO (socio)</th>
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
        <p style="font-size:8px;"> <sup style="color:black;">(1)</sup>El monto total de interés compensatorios equivalente en moneda nacional ha sido calculada en base al tipo de cambio venta vigente para las operaciones del BCR al momento de la emisión del presente documento.</p>

        <table border="0" cellspacing="0" cellpadding="0" class="tablacredito">
          <tbody>
            <tr style="background:orange;">
              <th colspan="4" style="height:25px;text-align:center;font-size:14px;color:#444;">PENALIDADES</th>
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
              <td class="mitr" colspan="4" style="">
                <span style="color:red;">No incluye costos o gastos notariales o judiciales que estarán a cargo del socio en caso incurra en cobranza judicial o prejudicial.</span>
                En caso de incumplimiento en el pago en las fechas pactadas, LOS PRESTATARIOS serán Reportados, con la calificación que corresponda, a la Central de Riesgo.
              </td>
            </tr>
          </tbody>
        </table>
        <br>
        <p style="font-size:10px;">18. En caso de incumplimiento en el pago de la fecha prevista (frecuencia de pago), EL SOCIO(a) será reportado a las Centrales de Riesgo de la SBS y a aquellas constituidas de acuerdo a ley, con la que COOPAC GRUPO INVERSION SUDAMERICANO, tenga suscrito un convenio o contrato con dicho objeto, con calificación que corresponda, de conformidad con el reglamento para la evaluación y clasificación del deudor y la exigencia de provisiones vigente.</p>
        <p style="font-size:10px;">19. LOS SOCIO (a) podrá solicitar una constancia de cancelación del crédito detallado en el presente documento solo por única vez, a partir de la segunda constancia se pagará una comisión, cuyo importe aparece en el tarifario de comisiones.</p>
        <p style="font-size:10px;">20. En caso EL SOCIO(a) que efectúe un pago en exceso que no sea una amortización o un pago anticipado del crédito, dicho exceso les será devuelto por la COOPAC GRUPO INVERESION SUDAMERICANO.</p>
        <p style="font-size:10px;">21. La COOPAC GRUPO INVERSION SUDAMERICANO podrá optar por resolver el contrato, sin previo aviso a EL SOCIO (a) como consecuencia de las referidas a la administración del riesgo de sobre endeudamiento de deudores minoristas, por perfil de cliente vinculado al sistema de prevención de lavado de activos o del financiamiento del terrorismo o por falta de transparencia del SOCIO(a), (proporcionar información inexacta incompleta o falsa o inconsistente con la información previamente declarada o entregada).</p>
        <p style="font-size:10px;">22. Los FIADORES SOLIDARIOS, se constituyen en fiador(es) de EL SOCIO(a), en forma solidaria, irrevocable, incondicionada, ilimitada e indefinida renunciando expresamente al beneficio de exclusión y de división así como la facultad establecida en el artículo 1899° del código civil, obligándose a pagar todas las obligaciones derivadas de o los crédito(s) que contraiga el SOCIO(a) en ejecución del contrato del crédito presentes o futuros, directos o indirectos, aceptando desde ya cualquier modificación a las condiciones del crédito(s) que se le otorgue, o reprogramaciones, sin necesidad de comunicación alguna, aceptando todas las prórrogas de los plazos de COOPAC GRUPO INVERSION SUDAMERICANO tenga a bien concederles.</p>
        <p style="font-size:10px;">23. Las garantías reales otorgadas por el cliente a favor de COOPAC GRUPO INVERSION SUDAMERICANO, respaldan a todas las obligaciones que tiene el socio con la Obligación asumida.</p>
        <p style="font-size:10px;">24. EL SOCIO(a) tiene derecho a solicitar una copia de la tasación realizada al bien otorgado en garantía, si lo hubiese y cuando corresponda, según el crédito.</p>

        <p style="font-size:10px;font-weight:bold;">RESUMEN DE ALGUNAS CONDICIONES CONTRACTUALES RELEVANTES PARA LAS PARTES</p>
        <p style="font-size:10px;font-weight:bold;">MODIFICACIONES CONTRACTUAL</p>
        <p style="font-size:10px;"><b>CLAUSULA CUARTA. – </b>La COOPAC GRUPO INVERSION SUDAMERICANO tiene la facultad de modificar unilateralmente las condiciones del contrato de crédito cooperativo suscrito y de las establecidas en la hoja resumen, cronograma de pagos y otros, así como establecer nuevas condiciones, las cuales serán puestas de conocimiento de EL SOCIO(a).</p>
        <p style="font-size:10px;font-weight:bold;">FACULTAD DE COMPENSACIÓN</p>
        <p style="font-size:10px;"><b>CLAUSULA OCTAVA. – </b>La COOPAC GRUPO INVERSION SUDAMERICANO tiene la facultad de compensar el importe de sus obligaciones vencidas, así como todos los gastos y suma de deuda que se originen en otorgamiento y recuperación del préstamo cooperativo otorgado, con los saldo existente de cualquiera de las cuentas que tuviera o pudieran tener con la COOPAC GRUPO INVERSION SUDAMERICANO Individual o conjuntamente con terceros; así como retener cualquier fondo, valor, crédito y/o bien, que estuviesen destinados a favor de EL SOCIO(a) o de los FIADORES SOLIDARIOS, y aplicar el monto de los mismos a la amortización y/o cancelación de las obligaciones.</p>
        <p style="font-size:10px;font-weight:bold;">EMISION DE TITULO VALOR INCOMPLETO</p>
        <p style="font-size:10px;"><b>CLAUSULA DECIMA. – </b>EL SOCIO(a) y los FIADORES SOLIDARIOS declaran expresamente que la COOPAC GRUPO INVERSION SUDAMERICANO ha hecho de su conocimiento los mecanismos de protección que la ley permite para la emisión o aceptación de títulos valores, incompletos habiendo sido informados de los alcances del Art. 10 de la ley N° 27287, de inciso d) del artículo 56 de la ley Nro. 29571 y del contenido de la circular SBS N° G-0090-2001, cuyos textos declaran haber leído y conocer aceptando las mismas.</p>
        <p style="font-size:10px;font-weight:bold;">FACULTAD DE EFECTUR PAGOS ANTICIPADOS Y ADELANTO DE CUOTAS </p>
        <p style="font-size:10px;"><b>CLAUSULA DECIMA PRIMERA. – </b>El SOCIO(a) de la COOPAC GRUPO INVERSION SUDAMERICANO en cualquiera de nuestras agencias tiene la facultad de efectuar el pago anticipado de cuotas (parcial) o del saldo de su crédito(total), con la consiguiente reducción de intereses, comisiones y gastos al día de pago, así como el pago adelantado de las cuotas establecidas en el cronograma de pago.</p>
        <p style="font-size:10px;">El pago anticipado del total de la obligación importa la cancelación del crédito. El pago anticipado parcial importa la cancelación de un monto mayor a las cuotas exigible en el periodo, trae como consecuencia la aplicación del monto al capital del crédito, con la consiguiente reducción de intereses, las comisiones y los gastos derivados pactados al día de pago. El pago anticipado parcial del crédito podrá aplicarse a solicitud de El SOCIO(a), a la reducción del número de cuotas con la consecuente reducción del plazo o, a la reducción de las cuotas, manteniendo el plazo original. En aquellos casos en los que EL SOCIO(a), no pueda realizar dicha elección, se procederá a la reducción del número de cuotas. En todos los casos, realizado el pago, inmediatamente se emitirá un nuevo cronograma de pagos, el cual sustituirá al anterior. Las partes están de acuerdo en dicha operación no constituye una novación de la obligación, el procedimiento para efectuar el pago anticipado total o parcial de las obligaciones, estará disponible en nuestra agencia y Pagina web. Sin perjuicio de lo antes referido, EL SOCIO(a), podrán manifestar expresamente su voluntad para adelantar el pago de sus cuotas, procediendo a la COOPAC GRUPO INVERSION SUDAMERICANO, a aplicar el monto pagado en exceso de sobre la cuota del periodo a las cuotas inmediatas siguientes.</p>
        <p style="font-size:10px;">El adelanto de cuotas a solicitud de EL SOCIO(a), trae como consecuencia la aplicación del monto pagado a las cuotas inmediatamente posteriores a la exigible en el periodo, sin que produzca una reducción del interés, comisiones y los gastos pactados, sin perjuicio de ello, EL SOCIO(a), podrán solicitar antes o al momento de efectuar el pago, deberá procederse a la aplicación del pago como anticipado, resultado aplicable lo pactado respecto al pago anticipado, en lo que corresponda.</p>
        <br>
        <p style="font-size:10px;font-weight:bold;">SEGURO CONTRA TODO RIESGO</p>
        <p style="font-size:10px;"><b>CLAUSULA DECIMO SEGUNDA. – </b>En caso la COOPAC GRUPO INVERSION SUDAMERICANO lo requiera, EL SOCIO(a) se obliga a contratar directamente una póliza de seguro contra todo riesgo, en las condiciones requeridas por la COOPAC GRUPO INVERSION SUDAMERICANO, que cubra el bien otorgado en Garantía, obligándose a mantenerla vigente hasta la cancelación del (los) crédito(s) que se otorguen, las condiciones requeridas por la COOPAC GRUPO INVERSION SUDAMERICANO, serán informados a EL SOCIO(a) al momento de la solicitud del crédito cooperativo, ello sin perjuicio de su publicación en la página web.</p>
        <p style="font-size:10px;font-weight:bold;">SEGURO DE DESGRAVAMEN</p>
        <p style="font-size:10px;"><b>CLAUSULA DECIMO TERCERA. – </b>Por cada crédito contratado como acto cooperativo y durante toda su vigencia, EL SOCIO(a), (solo personas naturales o el titular de la empresa individual o responsabilidad limitada) se obligan a contratar y mantener vigente un seguro de desgravamen, según los lineamientos establecidos por la COOPAC GRUPO INVERSION SUDAMERICANO. Las condiciones requeridas por la COOPAC GRUPO INVERSION SUDAMERICANO serán informados a EL SOCIO(a) al momento de la solicitud del crédito, ello sin perjuicio de su publicación en la página web.</p>
        <p style="font-size:10px;font-weight:bold;">DECLARACION FINAL</p>
        <p style="font-size:10px;">EL SOCIO(a) y fiadores solidarios, declaramos haber leído previamente a su suscripción el contrato de crédito cooperativo, cronograma de pago, hoja resumen, contrato mutuo, contrato de crédito cooperativo, seguro de desgravamen, cláusula adicional de fianza; que hemos sido instruidos a cerca de los alcances y significado de los términos y condiciones establecidos en dichos documentos, habiendo sido absueltas y aclaradas a nuestra satisfacción todas nuestras consultas y/o dudas, por lo que declaramos tener pleno y exacto conocimiento de las condiciones establecidas en dichos documentos. Se adjunta:</p>
          <ul>
            <li>Hoja Resumen de Créditos</li>
            <li>Cronograma de Pagos</li>
            <li>Contrato de Crédito Cooperativo</li>
            <li>Seguro de Desgravamen</li>
            <li>Cláusula Adicional de Fianza.</li>
          </ul>
        <p style="font-size:10px;">Así mismo EL SOCIO(a) y fiadores Solidarios, Declaran que son conscientes que otorgan poder al Gerente de la COOPAC GRUPO INVERSION SUDAMERICANO, para que sus aportes al capital Social, en caso de incumplimiento de la obligación asumida, puedan ser usados para cubrir las obligaciones de la cooperativa de ser necesario de acuerdo a los reglamentos de la cooperativa.</p>

        <div style="text-align:center;width:100%;margin-top:100px;">
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
              <span style="font-size:12px;color:#555;">EL AVAL(Fiador Solidario)<br>DNI:<span style="color:white;">00000000</span></span><br>
            </div>
          </div>
        </div>
        <div style="margin-top:50px;font-size:8px;text-align:justify;"><b>NOTA:</b> El resumen de las condiciones detalladas en el presente anexo tiene únicamente una finalidad didáctica por lo que no sustituye a las condiciones establecidas en el contrato.</div>
      </main>
    </body>
  </html>';

  require_once "../../../libs/pdf.php/vendor/autoload.php";
  $mpdf = new \Mpdf\Mpdf();
  $mpdf->WriteHTML($html);
  $mpdf->Output();
  exit;
?>
