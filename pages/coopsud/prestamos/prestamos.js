let rutaSQL = "pages/coopsud/prestamos/sql.php";
let globNumCuota = 0; //obtiene el numero de cuota para patear el interes al final
let globFinCuota = 0; //obtiene el numero de la ultima cuota
let globInteresCuota = 0;
let globInteresFinal = 0;
let globRedisNumCuotas = 0; //numero de cuotas restantes NO pagadas

//=========================funciones para coopSUD Prestamos============================
function appGridAll(){
  let agenciaID = $("#cboAgencias").val();
  $('#grdDatosCount').html("");
  $('#grdDatosBody').html('<tr><td colspan="10" style="text-align:center;"><br><div class="progress progress-sm active"><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div><br>Un momento, por favor...</td></tr>');
  if(agenciaID>0){
    $('#divBuscar').hide();
    let datos = { TipoQuery:'coopSUDcartera', agenciaID:agenciaID, tipo:$('#cboTipo').val(), buscar:"" };
    appAjaxSelect(datos,rutaSQL).done(function(resp){
      appLlenarTabla(resp);
    });
  } else {
    $('#divBuscar').show();
    if($('#txtBuscar').val().trim()==""){
      $('#grdDatosBody').html('<tr><td colspan="10" style="text-align:center;color:red;">Sin Resultados</td></tr>');
    } else {
      let txtBuscar = $('#txtBuscar').val().trim();
      if($("#cboTipo").val()=="1"){ //verificamos que es por codigo
        txtBuscar = txtBuscar.split('-');
        txtBuscar = zfill(Math.abs(txtBuscar[0]),2)+"-"+zfill(Math.abs(txtBuscar[1]),4);
      }
      $('#txtBuscar').val(txtBuscar);
      let datos = {
        TipoQuery:'coopSUDcartera',
        agenciaID:agenciaID,
        tipo:$('#cboTipo').val(),
        buscar:$('#txtBuscar').val()
      };
      appAjaxSelect(datos,rutaSQL).done(function(resp){
        appLlenarTabla(resp);
      });
    }
  }
}

function appLlenarTabla(tabla){
  if(tabla.length>0){
    let fila = "";
    $.each(tabla,function(key, valor){
      fila += '<tr style="'+((valor.saldo<=0)?("color:#bbb;"):(""))+'">';
      fila += '<td><a href="javascript:appFormatos(\''+(valor.codsocio)+'\',\''+(valor.tiposerv)+'\',\''+(valor.numpres)+'\');"><i class="fa fa-files-o" title="Formatos..."></i></a></td>';
      fila += '<td>'+(valor.agencia)+'</td>';
      fila += '<td>'+(valor.codsocio)+'</td>';
      fila += '<td>'+(valor.nrodoc)+'</td>';
      fila += '<td>'+(valor.socio)+'</td>';
      fila += '<td><a style="'+((valor.saldo<=0)?("color:#bbb;"):(""))+'" title="'+(valor.analista)+'" href="javascript:appPrestamoView(\''+(valor.codsocio)+'\',\''+(valor.tiposerv)+'\',\''+(valor.numpres)+'\');">'+(valor.tiposerv)+' - '+(valor.servicio)+' - '+(valor.numpres)+'</a></td>';
      fila += '<td style="text-align:center;">'+(moment(valor.fec_otorg.date).format('DD/MM/YYYY'))+'</td>';
      fila += '<td style="text-align:center;">'+(valor.cuotas)+'</td>';
      fila += '<td style="text-align:right;">'+appFormatMoney(valor.importe,2)+'</td>';
      fila += '<td style="text-align:right;">'+appFormatMoney(valor.saldo,2)+'</td>';
      fila += '</tr>';
    });
    $('#grdDatosBody').html(fila);
  }else{
    $('#grdDatosBody').html('<tr><td colspan="9" style="text-align:center;color:red;">Sin Resultados</td></tr>');
  }
  $('#grdDatosCount').html(tabla.length);
}

function appGridReset(){
  let datos = { TipoQuery : 'ComboBoxAgencias' }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    resp.combo.push({"ID":0,"nombre":"Todas las AG."});
    appLlenarDataEnComboBox(resp.combo,"#cboAgencias",0);
    if(resp.usernivel==resp.admin){ $("#cboAgencias").removeAttr("disabled"); } else { $("#cboAgencias").attr("disabled","disabled"); }
    appGridAll();
  });
}

function appComboTipo(){
  switch($("#cboTipo").val()){
    case "1": $("#txtBuscar").prop("placeholder","COD-SOCIO..."); break;
    case "2": $("#txtBuscar").prop("placeholder","DNI..."); break;
  }
}

function rptGetCanceladosSociosDownload(){
  let agenciaID = $('#cboAgencias').val();
  let datos = { TipoQuery:'coopSUDcancelados', agenciaID:agenciaID }
  appAjaxSelect(datos).done(function(resp){
    JSONToCSVConvertor(resp, "Cancelados_"+agenciaID, true);
  });
}

function appPrestamosBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { appGridAll(); }
}

function appBotonCancel(){
  globNumCuota = 0;
  globFinCuota = 0;
  globInteresCuota = 0;
  globInteresFinal = 0;
  globRedisNumCuotas = 0;
  $("#formatos").hide();
  $("#edit").hide();
  $("#grid").show();
  appGridAll();
}

function appBotonGarantes(){
  let datos = {
    TipoQuery : 'coopSUDgarantes',
    codsocio : $("#lbl_codigo").html()
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    //console.log(resp);
    if(resp.length>0){
      let fila = "";
      $.each(resp,function(key, valor){
        fila += '<tr style="">';
        fila += '<td>'+(valor.dni)+'</td>';
        fila += '<td>'+(valor.garante)+'</td>';
        fila += '<td>'+(valor.telefonos)+'</td>';
        fila += '<td>'+(valor.direccion)+'</td>';
        fila += '</tr>';
      });
      $('#grdGarantesBody').html(fila);
    }else{
      $('#grdGarantesBody').html('<tr><td colspan="4" style="text-align:center;color:red;">Sin Resultados</td></tr>');
    }
    $('#modalGarantes').modal("show");
  });
}

function appBotonConfigCred(){
  let datos = {
    TipoQuery : 'coopSUDprestamo',
    codsocio  : $("#lbl_codigo").html(),
    tiposerv  : $("#lbl_tiposerv").html(),
    numpres   : $("#lbl_numpres").html()
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    let valor = resp.prestaCabe;

    switch(valor.condicion){
      case "J": $('#modConfigComboEstado').html('<option value="1">Medida Cautelar</option><option value="2">Admisorio</option><option value="3">Anotacion / Inscripcion en RRPP</option><option value="4">Demanda</option><option value="5">Admisorio Demanda</option><option value="6">Contradiccion</option><option value="7">Sentencia</option>');break;
      default : $('#modConfigComboEstado').html('<option value="P">Pendiente de Pago</option><option value="C">Cancelado</option>');break;
    }
    $('#modConfigCodPrestamo').html(valor.codsocio+"."+valor.tipo_serv+"."+valor.prestamo);
    $('#modConfigComboCondicion').val(valor.condicion);
    $('#modConfigComboEstado').val(valor.estado);
    $('#modalConfigCred').modal("show");
  });
}

function appFormatos(codsocio,tiposerv,numpres){
  let datos = {
    TipoQuery : "coopSUDformatos",
    codsocio  : codsocio,
    tiposerv  : tiposerv,
    numpres   : numpres
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    $("#lbl_FormaSocio").html(resp.socio.socio);
    $("#lbl_FormaDNI").html(resp.socio.nrodoc);
    $("#lbl_FormaCodSocio").html(resp.socio.codsocio);
    $("#lbl_FormaNumpres").html(resp.socio.prestamo);
    $("#lbl_FormaTiposerv").html(resp.socio.tipo_serv);
    $("#lbl_FormaServicio").html(resp.socio.servicio);
    $("#lbl_FormaFecha").html(moment(resp.socio.fecha.date).format('DD/MM/YYYY'));
    $("#lbl_FormaCuotas").html(resp.socio.cuotas+" cuotas");
    $("#lbl_FormaImporte").html(appFormatMoney(resp.socio.importe,2));
    $("#lbl_FormaSaldo").html(appFormatMoney(resp.socio.saldo,2));
    $("#lbl_FormaPromotor").html(resp.socio.promotor);
    $("#lbl_FormaAnalista").html(resp.socio.analista);

    $("#contenedorFrame").hide();
    $("#objPDF").prop("data","");

    $('#grid').hide();
    $('#formatos').show();
  });
}

function appFormatosGenerarPDF(tipo){
  let urlServer = $("#hid_FormUrlServer").val();
  let urlReporte = "";

  $("#contenedorFrame").show();
  switch(tipo){
    case "ResumenCredito": urlReporte = appUrlServer()+"pages/coopsud/prestamos/rpt.coopsud.resuprest.php?codsocio="+$("#lbl_FormaCodSocio").html()+"&tiposerv="+$("#lbl_FormaTiposerv").html()+"&numpres="+$("#lbl_FormaNumpres").html(); break;
    case "CartaAutorizaGarLiquida": urlReporte = appUrlServer()+"pages/coopsud/prestamos/rpt.coopsud.carta.php?codsocio="+$("#lbl_FormaCodSocio").html(); break;
    case "DeclJuraConviv": urlReporte = ""; break;
  }
  $("#contenedorFrame").html('<object id="objPDF" type="text/html" data="'+urlReporte+'" width="100%" height="450px"></object>');
}

function appPrestamoView(codsocio,tiposerv,numpres){
  let datos = {
    TipoQuery : "coopSUDprestamo",
    codsocio  : codsocio,
    tiposerv  : tiposerv,
    numpres   : numpres
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    $("#grid").hide();
    $("#edit").show();

    //llenar cabecera de prestamo
    if(resp.usernivel==resp.admin) { $("#secc_admin").html('<button type="button" id="btn_ConfigCred" class="btn btn-default btn-xs" onclick="javascript:appBotonConfigCred();">Config</button>'); }
    appLlenarPrestamoDatos(resp.prestaCabe);

    //llenar saldos
    $('#grdSaldosBody').html('<tr><td colspan="2" style="text-align:center;"><br><div class="progress progress-sm active"><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div><br>Un momento, por favor...</td></tr>');
    appAjaxSelect({TipoQuery:"coopSUDprestamo_saldos",codsocio:codsocio},rutaSQL).done(function(resp){
      if(resp.saldos.length>0){ $('#grdSaldosBody').html(appLlenarSaldos(resp.saldos)); }
    });

    //llenar detalle de prestamo
    $('#grdPrestamosBody').html('<tr><td colspan="12" style="text-align:center;"><br><div class="progress progress-sm active"><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div><br>Un momento, por favor...</td></tr>');
    appAjaxSelect({TipoQuery:"coopSUDprestamo_det",codsocio:codsocio,numpres:numpres},rutaSQL).done(function(resp){
      console.log(resp);
      if(resp.prestaDeta.length>0){ $('#grdPrestamosBody').html(appLlenarCuotas(resp.prestaDeta)); }
    });
  });
}

function appLlenarPrestamoDatos(data){
  $("#hid_FactorMora").val(data.factor_mora);
  $("#lbl_socio").html(data.socio);
  $("#lbl_DNI").html(data.nrodoc);
  $("#lbl_codigo").html(data.codsocio);
  $("#lbl_direccion").html(data.direccion);
  $("#lbl_numpres").html(data.prestamo);
  $("#lbl_tiposerv").html(data.tipo_serv);
  $("#lbl_servicio").html(data.servicio);
  $("#lbl_fecha").html(moment(data.fecha.date).format('DD/MM/YYYY'));
  $("#lbl_cuotas").html(data.cuotas+" cuotas");
  $("#lbl_importe").html(appFormatMoney(data.importe,2));
  $("#lbl_saldo").html(appFormatMoney(data.saldo,2));

  $("#lbl_agencia").html(data.agencia);
  $("#lbl_promotor").html(data.promotor);
  $("#lbl_analista").html(data.analista);

  let estado = "";
  let condicion = "";

  switch(data.estado){
    case "P": estado = ' - Pendiente de pago'; break;
    case "C": estado = ' - Cancelado'; break;
    case "1": estado = ' - Medida Cautelar'; break;
    case "2": estado = ' - Admisorio'; break;
    case "3": estado = ' - Anotacion / inscripcion RR.PP.'; break;
    case "4": estado = ' - Demanda'; break;
    case "5": estado = ' - Admisorio demanda'; break;
    case "6": estado = ' - Contradiccion'; break;
    case "7": estado = ' - Sentencia'; break;
  }

  switch(data.condicion){
    case "N": condicion = '<span class="label bg-silver" style="font-weight:normal;background:#ddd;color:black;">Condicion: Normal'+estado+'</span>';break;
    case "C": condicion = '<span class="label bg-blue" style="font-weight:normal;">Condicion: reprogramado COVID19'+estado+'</span>';break;
    case "R": condicion = '<span class="label bg-blue" style="font-weight:normal;">Condicion: reprogramado'+estado+'</span>';break;
    case "P": condicion = '<span class="label bg-green" style="font-weight:normal;">Condicion: paralelo'+estado;'</span>';break;
    case "D": condicion = '<span class="label bg-silver" style="font-weight:normal;background:#orange;color:black;">Condicion: prejudicial'+estado+'</span>';break;
    case "O": condicion = '<span class="label bg-silver" style="font-weight:normal;background:#ddd;color:black;">Condicion: condonado'+estado+'</span>';break;
    case "S": condicion = '<span class="label bg-silver" style="font-weight:normal;background:#ddd;color:black;">Condicion: castigado'+estado+'</span>';break;
    case "A": condicion = '<span class="label bg-silver" style="font-weight:normal;background:#ddd;color:black;">Condicion: ampliado'+estado+'</span>';break;
    case "F": condicion = '<span class="label bg-silver" style="font-weight:normal;background:#ddd;color:black;">Condicion: refinanciado'+estado+'</span>';break;
    case "J": condicion = '<span class="label bg-red" style="font-weight:normal;">Condicion: judicial'+(estado)+'</span>';break;
  }
  $("#tipoCreditoSpan").html(condicion);
}

function appLlenarCuotas(tabla){
  let fila = "";
  let valorMora = 0;
  let totCapital = 0;
  let totInteres = 0;
  let totDesgr = 0;
  let totMora = 0;

  let venCapital = 0; //total capital vencido
  let venInteres = 0; //total interes vencido
  let venDesgr = 0; // total desgravamen vencido
  let venMora = 0; // total mora vencida

  let factorMora = 135;//$("#hid_FactorMora").val();

  globNumCuota = 0;
  globFinCuota = tabla.length-1;
  globInteresCuota = 0;
  globInteresFinal = 0;
  globRedisNumCuotas = 0;
  $("#allCheck").prop('checked',false);
  $.each(tabla,function(key, valor){
    totCapital += (valor.capital);
    totInteres += (valor.interes);
    totDesgr += (valor.desgr);
    //vencido
    valorMora = (valor.atraso2!=null)?(valor.pag_moratorio):((valor.atraso1<0)?(0):(valor.capital * ((1+(factorMora/100))**(valor.atraso1/360)-1)));
    if(valor.pag_capital<valor.capital){
      venCapital += valor.capital;
      venInteres += valor.interes;
      venDesgr += valor.desgr;
      venMora += valorMora;
    }

    if((globNumCuota==0)&&(valor.capital!=valor.pag_capital)&&(key>0)){ globNumCuota=valor.numero; globInteresCuota = valor.interes;}
    if((valor.capital!=valor.pag_capital)&&(key>0)){ globRedisNumCuotas += 1; }
    fila += '<tr style="'+((valor.pag_capital==valor.capital)?("color:#bbb"):((valor.pag_capital>valor.capital)?("color:#E75ED0;"):((valor.atraso1<0)?("color:#000"):("color:red"))))+'">';
    fila += '<td>'+((valor.pag_capital>=valor.capital)?(''):('<input type="checkbox" name="chk_Borrar" value="'+(valor.numero)+'"/>'))+'</td>';
    fila += '<td>'+(valor.numero)+'</td>';
    fila += '<td>'+(moment(valor.fec_vencim.date).format('DD/MM/YYYY'))+'</td>';
    fila += '<td style="text-align:right;">'+appFormatMoney(valor.total+valorMora,2)+'</td>';
    fila += '<td style="text-align:right;" title="pago real: '+appFormatMoney(valor.pag_capital,2)+'">'+appFormatMoney(valor.capital,2)+'</td>';
    fila += '<td style="text-align:right;" title="pago real: '+appFormatMoney(valor.pag_interes,2)+'">'+appFormatMoney(valor.interes,2)+'</td>';
    fila += '<td style="text-align:right;">'+appFormatMoney(valorMora,2)+'</td>';
    fila += '<td style="text-align:right;">'+appFormatMoney(valor.desgr,2)+'</td>';
    fila += '<td style="text-align:right;">'+appFormatMoney(valor.saldo,2)+'</td>';
    fila += '<td style="text-align:center;">'+((valor.atraso2!=null)?(valor.atraso2):((valor.atraso1<0)?(""):(valor.atraso1)))+'</td>';
    fila += '<td>'+((valor.pag_capital>=valor.capital)?((valor.fec_pago==null)?(""):(moment(valor.fec_pago.date).format('DD/MM/YYYY'))):(""))+'</td>';
    fila += '<td>'+((valor.fec_pago==null)?(""):('<a href="javascript:openModalMovimientos(\''+(valor.doc_pago)+'\')">'+(valor.doc_pago)+'</a>'))+'</td>';
    fila += '</tr>';
  });
  fila += '<tr>';
  fila += '<td colspan="3" style="text-align:center;font-weight:bold;">Total</td>';
  fila += '<td style="text-align:right;">'+appFormatMoney(totCapital+totInteres+totDesgr,2)+'</td>';
  fila += '<td style="text-align:right;">'+appFormatMoney(totCapital,2)+'</td>';
  fila += '<td style="text-align:right;">'+appFormatMoney(totInteres,2)+'</td>';
  fila += '<td style="text-align:right;">'+appFormatMoney(0,2)+'</td>';
  fila += '<td style="text-align:right;">'+appFormatMoney(totDesgr,2)+'</td>';
  fila += '<td colspan="4"></td>';
  fila += '</tr>';

  fila += '<tr style="color:red;">';
  fila += '<td colspan="3" style="text-align:center;">Total</td>';
  fila += '<td style="text-align:right;">'+appFormatMoney(venCapital+venInteres+venDesgr,2)+'</td>';
  fila += '<td style="text-align:right;">'+appFormatMoney(venCapital,2)+'</td>';
  fila += '<td style="text-align:right;">'+appFormatMoney(venInteres,2)+'</td>';
  fila += '<td style="text-align:right;">'+appFormatMoney(venMora,2)+'</td>';
  fila += '<td style="text-align:right;">'+appFormatMoney(venDesgr,2)+'</td>';
  fila += '<td colspan="4"></td>';
  fila += '</tr>';
  if(globNumCuota<globFinCuota){
    if(appConvertToNumero(appFormatMoney(tabla[globNumCuota].total))>appConvertToNumero(appFormatMoney(tabla[globNumCuota+1].total))) {
      $("#btn_PatearInteresAlFinal").hide();
      globInteresCuota = tabla[globNumCuota].interes - (tabla[globNumCuota].total - tabla[globNumCuota+1].total);
      globInteresFinal = tabla[globNumCuota].interes - globInteresCuota;
    } else { $("#btn_PatearInteresAlFinal").hide(); }
  } else { $("#btn_PatearInteresAlFinal").hide(); }
  return fila;
}

function appLlenarSaldos(tabla){
  let fila = "";
  $.each(tabla,function(key, valor){
    fila += '<tr>';
    fila += '<td title="'+(valor.tipo_serv)+'">'+(valor.detalle)+'</td>';
    fila += '<td style="text-align:right;">'+appFormatMoney(valor.saldo,2)+'</td>';
    fila += '</tr>';
  });
  return fila;
}

function appRefresh(){
  globNumCuota = 0;
  globFinCuota = 0;
  globInteresCuota = 0;
  globInteresFinal = 0;
  globRedisNumCuotas = 0;
  appPrestamoView($("#lbl_codigo").html(),$("#lbl_tiposerv").html(),$("#lbl_numpres").html());
}

function appDownloadCronograma(){
  let datos = {
    TipoQuery : 'coopSUD_cronograma_download',
    codsocio  : $('#lbl_codigo').html(),
    tiposerv  : $('#lbl_tiposerv').html(),
    numpres   : $('#lbl_numpres').html()
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    //console.log(resp);
    Jhxlsx.export(resp.tableData, resp.options);
  });
}

function openModalConfigCredComboCondicion(){
  switch($('#modConfigComboCondicion').val()){
    case "J": $('#modConfigComboEstado').html('<option value="1">Medida Cautelar</option><option value="2">Admisorio</option><option value="3">Anotacion / Inscripcion en RRPP</option><option value="4">Demanda</option><option value="5">Admisorio Demanda</option><option value="6">Contradiccion</option><option value="7">Sentencia</option>');break;
    default : $('#modConfigComboEstado').html('<option value="P">Pendiente de Pago</option><option value="C">Cancelado</option>');break;
  }
}

function openModalConfigCredGuardarCambios(){
  let datos = {
    TipoQuery : 'coopSUD_Update_Prestamo',
    codsocio  : $("#lbl_codigo").html(),
    tiposerv  : $("#lbl_tiposerv").html(),
    numpres   : $("#lbl_numpres").html(),
    condicion : $('#modConfigComboCondicion').val(),
    estado    : $('#modConfigComboEstado').val()
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    appLlenarPrestamoDatos(resp.prestaCabe);
    $('#modalConfigCred').modal("hide");
  });
}

function openModalMovimientos(mov){
  let datos = {
    TipoQuery : 'coopSUDmovimiento',
    movID : mov
  };
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    //console.log(resp);
    let fila = "";
    let total = 0;
    $.each(resp.movim,function(key, valor){
      total += valor.importe;
      fila += '<tr>';
      fila += '<td>'+(valor.tipo_mov+' - '+valor.detalle)+'</td>';
      fila += '<td style="text-align:right;">'+appFormatMoney(valor.importe,2)+'</td>';
      fila += '<td style="text-align:right;">0.00</td>';
      fila += '</tr>';
    });
    fila += '<tr>';
    fila += '<td><b>TOTAL</b></td>';
    fila += '<td style="text-align:right;"><b>'+appFormatMoney(total,2)+'</b></td>';
    fila += '<td></td>';
    fila += '</tr>';
    $('#modMovimFecha').html(moment(resp.fecha.date).format("DD/MM/YYYY"));
    $('#modMovimNumTrans').html(resp.num_trans);
    $('#modMovimAgencia').html(resp.agencia);
    $("#modMovimVentanilla").html(resp.ventanilla);
    $('#grdMovimientosBody').html(fila);
    $('#modalMovimientos').modal("show");
  });
}

function openModalFechaCuotas(){
  let arr = $('[name="chk_Borrar"]:checked').map(function(){ return this.value; }).get();
  if(arr.length>0){
    $("#modalCambiarVariasFechas_Cuotas").modal("show");
    $("#txt_FechaCambio").datepicker("setDate",moment().format("DD/MM/YYYY"));
  } else {
    alert("debe elegir por lo menos una cuota para cambiar de fecha");
  }
}

function openModalPatearInteres(){
  $("#txt_MontoInteres").val(appFormatMoney(globInteresCuota,2));
  $("#modalPatearInteres_Final").modal("show");
}

function appCambiarVencimiento(){
  let arr = $('[name="chk_Borrar"]:checked').map(function(){ return this.value; }).get();
  let datos = {
    TipoQuery : "cambiarVencimiento",
    IDs : arr,
    fecha : appConvertToFecha($("#txt_FechaCambio").val(),""),
    tipo_serv : $("#lbl_tiposerv").html(),
    codsocio : $("#lbl_codigo").html(),
    numpres : $("#lbl_numpres").html()
  }
  appAjaxUpdate(datos,rutaSQL).done(function(resp){
    if(resp.prestamo.length>0) { $('#grdPrestamosBody').html(appLlenarCuotas(resp.prestamo)); }
    $("#btn_CambiarFecha").hide();
    $("#modalCambiarVariasFechas_Cuotas").modal("hide");
  });
}

function appCambiarFechaUnMesMas(){
  let arr = $('[name="chk_Borrar"]:checked').map(function(){ return this.value; }).get();
  if(confirm("¿Estas seguro de continuar con el cambio de fecha para estas "+(arr.length)+" Cuotas?")){
    let datos = {
      TipoQuery : "cambiarFechaUnMesMas",
      IDs : arr,
      codsocio : $("#lbl_codigo").html(),
      tiposerv : $("#lbl_tiposerv").html(),
      numpres : $("#lbl_numpres").html()
    }
    appAjaxUpdate(datos,rutaSQL).done(function(resp){
      if(resp.prestamo.length>0) { $('#grdPrestamosBody').html(appLlenarCuotas(resp.prestamo)); }
    });
  }
}

function appPatearInteres_Final(){
  if(confirm("Esta seguro de cambiar el interes de la cuota Nº "+globNumCuota+" por este monto: S/. "+appFormatMoney(globInteresCuota,2)+"?")){
    let datos = {
      TipoQuery  : 'patearInteresFinal',
      codsocio   : $("#lbl_codigo").html(),
      tiposerv   : $("#lbl_tiposerv").html(),
      numpres    : $("#lbl_numpres").html(),
      num_cuota  : globNumCuota,
      fin_cuota  : globFinCuota,
      int_actual : appConvertToNumero(appFormatMoney(globInteresCuota,2)),
      int_final  : appConvertToNumero(appFormatMoney(globInteresFinal,2))
    }
    appAjaxUpdate(datos,rutaSQL).done(function(resp){
      if(resp.prestamo.length>0) { $('#grdPrestamosBody').html(appLlenarCuotas(resp.prestamo)); }
    });
  }
}

function appUpdateSoftia(){
  if(confirm("Esta seguro de actualizar el cronograma desde CoopSUD.dbo.danny_prestamos_det?")){
    $('#grdPrestamosBody').html('<tr><td colspan="12" style="text-align:center;"><br><div class="progress progress-sm active"><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div><br>Un momento, por favor...</td></tr>');
    let datos = {
      TipoQuery  : 'updateSoftia',
      codsocio   : $("#lbl_codigo").html(),
      tiposerv   : $("#lbl_tiposerv").html(),
      numpres    : $("#lbl_numpres").html(),
      importe    : appConvertToNumero($("#lbl_importe").html())
    }
    appAjaxUpdate(datos,rutaSQL).done(function(resp){
      if(resp.prestaDeta.length>0) { $('#grdPrestamosBody').html(appLlenarCuotas(resp.prestaDeta)); }
    });
  }
}

function appRedistribuirInteres(){ //redistribuye el interes excedente entre las cuotas restantes
  let datos = {
    TipoQuery  : 'redistribuirInteres',
    codsocio   : $("#lbl_codigo").html(),
    tiposerv   : $("#lbl_tiposerv").html(),
    numpres    : $("#lbl_numpres").html(),
    num_cuota  : globNumCuota,
    fin_cuota  : globFinCuota,
    redis_cuotas : globRedisNumCuotas,
    redis_interes : (globInteresFinal/globRedisNumCuotas),
    int_actual : appConvertToNumero(appFormatMoney(globInteresCuota,2)),
    int_final  : appConvertToNumero(appFormatMoney(globInteresFinal,2))
  }
  //console.log(datos);
  if(datos.redis_interes>0){
    if(confirm("Esta seguro de REDISTRIBUIR el interes en las "+(datos.redis_cuotas)+" cuotas restante por este monto: S/. "+appFormatMoney(datos.redis_interes,2)+"?")){
      appAjaxUpdate(datos,rutaSQL).done(function(resp){
        if(resp.prestamo.length>0) { $('#grdPrestamosBody').html(appLlenarCuotas(resp.prestamo)); }
      });
    }
  } else {
    alert("no es posible redistribuir ningun interes");
  }
}
