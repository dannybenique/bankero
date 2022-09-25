var rutaSQL = "pages/caja/aportes/sql.php";
var gridDetalle = new Array();

//=========================funciones para Personas============================
function appAportesGetAll(){
  let agenciaID = $("#cboAgencias").val();
  let txtBuscar = $("#txtBuscar").val();
  let datos = {
    TipoQuery : 'selAportes',
    agenciaID : agenciaID,
    buscar : txtBuscar
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    if(resp.tabla.length>0){
      let appData = "";
      $.each(resp.tabla,function(key, valor){
        appData += '<tr>';
        appData += '<td>'+(valor.codigo)+'</td>';
        appData += '<td>'+(valor.DNI)+'</td>';
        appData += '<td><a href="javascript:appAportesEdit('+(valor.id_socio)+');" title="'+(valor.id_socio)+'">'+(valor.socio)+'</a></td>';
        appData += '<td style="text-align:right;">'+appFormatMoney(valor.saldo,2)+'</td>';
        appData += '<td>'+(valor.agencia)+'</td>';
        appData += '<td>'+(valor.observac)+'</td>';
        appData += '</tr>';
      });
      $('#grdDatosBody').html(appData);
    }else{
      let mensaje = (txtBuscar=="") ? ("") : ("para "+txtBuscar);
      $('#grdDatosBody').html('<tr><td colspan="7" style="text-align:center;color:red;">Sin Resultados '+mensaje+'</td></tr>');
    }
    $('#grdDatosCount').html(resp.tabla.length+"/"+resp.cuenta);
  });
}

function appAportesReset(agenciaID){
  $("#txtBuscar").val("");
  let datos = {
    TipoQuery : 'ComboBox',
    miSubSelect : 'Agencias'
  }
  appAjaxSelect(datos).done(function(resp){
    appLlenarComboAgencias("#cboAgencias",resp.agenciaID,resp.tabla);
    appAportesGetAll();
  });
}

function appAportesBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { appAportesGetAll(); }
}

function appAportesEdit(socioID){
  let datos = {
    TipoQuery : 'editAporte',
    personaID : socioID
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    appAportesEditLlenarInfoCorta(resp.tablaPers);
    appAportesEditLlenarSaldoAportes(resp.tablaSaldos);

    $("#btnInsert").hide();
    $("#btnUpdate").show();
    $("#pn_Error").hide();

    $('#grid').hide();
    $('#edit').show();
  });
}

function appAportesEditLlenarInfoCorta(data){
  $('#img_PersFoto').prop("src",data.urlfoto=="" ? "data/personas/images/0noFotoUser.jpg" : data.urlfoto);
  $("#lbl_SocioNombres").html(data.nombres);
  $("#lbl_SocioApellidos").html(data.ap_paterno+' '+data.ap_materno);
  $("#lbl_SocioID").html(data.ID);
  $("#lbl_SocioDNI").html(data.nroDNI);
  $("#lbl_SocioCelular").html(data.celular);
  $("#lbl_SocioAgencia").html(data.agencia);
}

function appAportesEditLlenarSaldoAportes(data){
  if(data.length>0){
    let appData = "";
    let colorData = "";
    $.each(data,function(key, valor){
      if(valor.saldo<0){ colorData = "color:red;";} else{ colorData = "color:black;";}
      appData += '<tr style="'+colorData+'">';
      appData += '<td>'+(key+1)+'</td>';
      appData += '<td>'+(valor.producto)+'</td>';
      appData += '<td style="text-align:right;">'+appFormatMoney(valor.saldo,2)+'</td>';
      appData += '<td></td>';
      appData += '</tr>';
    });
    $('#grdAportesBody').html(appData);
  }else{
    $('#grdAportesBody').html('<tr><td colspan="4" style="text-align:center;color:red;">No hay registros</td></tr>');
  }
}

function appAportesNewAporte(){
  let datos = {
    TipoQuery : 'addAporte',
    personaID : $("#lbl_SocioID").html()
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    //datos generales
    $("#lbl_modPagosTitulo").html((resp.socio.tipoPersona==1)?(resp.socio.persona):(resp.socio.nombres));
    $('#date_fechaing').datepicker("setDate",resp.fecha);

    //llenar tabla con producto
    if(resp.tablaSaldos.length>0){
      let fila = "";
      gridDetalle = resp.tablaSaldos;
      $.each(gridDetalle,function(key, valor){
        fila += '<tr style="font-size:18px;">';
        fila += '<td>'+(key+1)+'</td>';
        fila += '<td>'+(valor.producto)+'</td>';
        fila += '<td><input name="txt_PagosImporte" type="number" class="form-control" style="width:120px;text-align:right;" '+((valor.prioridad==1)?('disabled="disabled"'):(''))+' value="'+appFormatMoney(valor.saldo,2)+'" onkeypress="modalPagosCalcularKeyPress(event);" onblur="modalPagosCalcularTotal();" /></td>';
        fila += '</tr>';
      });
      $('#grdPagosBody').html(fila);
      modalPagosCalcularTotal();
      $("#divPrint").hide();
      $("#divPagos").show();
      $("#btn_modalPagosInsert").show();
      $('#modalPagos').modal();
    }
  });
}

function appAportesRetiro(){
  let datos = {
    TipoQuery : 'aporRetiro',
    personaID : $("#lbl_SocioID").html()
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    //datos generales
    $("#lbl_modRetirosTitulo").html($("#lbl_SocioApellidos").html()+', '+$("#lbl_SocioNombres").html());
    $('#date_fechadel').datepicker("setDate",resp.fecha);

    //llenar tabla con producto
    if(resp.tablaSaldos.length>0){
      let appData = "";
      $.each(resp.tablaSaldos,function(key, valor){
        appData += '<tr style="font-size:18px;">';
        appData += '<td><input id="hid_RetiroProductoID" type="hidden" value="'+(valor.id_producto)+'"><input id="hid_RetiroID" type="hidden" value="'+(valor.ID)+'">'+(key+1)+'</td>';
        appData += '<td><input id="hid_RetiroTipo" type="hidden" value="'+(valor.tipo)+'"><input id="hid_RetiroMaxMonto" type="hidden" value="'+(valor.maxmonto)+'">'+(valor.producto)+'</td>';
        appData += '<td><input id="txt_RetiroImporte" type="text" class="form-control" style="width:120px;text-align:right;" value="'+appFormatMoney(valor.saldo,2)+'" onkeypress="modalRetirosCalcularKeyPress(event);" onblur="modalRetirosCalcularTotal();" /></td>';
        appData += '</tr>';
      });
      $('#grdRetirosBody').html(appData);
      modalRetirosCalcularTotal();
      $("#divRetiroPrint").hide();
      $("#divRetiros").show();
      $("#btn_modalRetiroDelete").show();
      $('#modalRetiros').modal();
    }
  });
}

function appAportesRegresar(){
  $('#grid').show();
  $('#edit').hide();
  appAportesGetAll();
}

function appAportesSaldosReset(){
  let datos = {
    TipoQuery : 'editAporte',
    personaID : $("#lbl_SocioID").html()
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    appAportesEditLlenarSaldoAportes(resp.tablaSaldos);
  });
}

function appExtractoBancario(){
  let datos = {
    TipoQuery: 'ExtrBancario',
    socioID: $("#lbl_SocioID").html(),
    operacionID: 1,
    prestahorroID: 0
  };

  appAjaxSelect(datos).done(function(resp){
    $("#lbl_modExtractoTitulo").html($("#lbl_SocioApellidos").html()+', '+$("#lbl_SocioNombres").html());
    if(resp.length>0){
      let rptData = "";
      let totDepo = 0;
      let totReti = 0;

      $.each(resp,function(key, valor){
        totDepo = totDepo + Number(valor.depositos);
        totReti = totReti + Number(valor.retiros);

        rptData += '<tr>';
        rptData += '<td>'+(valor.agenciaID)+'</td>';
        rptData += '<td>'+(valor.usuarioID)+'</td>';
        rptData += '<td>'+(valor.tipomovID)+'</td>';
        rptData += '<td style="text-align:right;">'+(valor.fecha)+'</td>';
        rptData += '<td style="text-align:right;">'+(valor.numtrans)+'</td>';
        rptData += '<td>'+(valor.detalle)+'</td>';
        rptData += '<td style="text-align:right;">'+appFormatMoney(valor.depositos,2)+'</td>';
        rptData += '<td style="text-align:right;">'+appFormatMoney(valor.retiros,2)+'</td>';
        rptData += '<td style="text-align:right;">'+appFormatMoney(valor.otros,2)+'</td>';
        rptData += '</tr>';
      });
      rptData += '<tr>';
      rptData += '<td style="text-align:right;" colspan="6">Totales</td>';
      rptData += '<td style="text-align:right;">'+appFormatMoney(totDepo,2)+'</td>';
      rptData += '<td style="text-align:right;">'+appFormatMoney(totReti,2)+'</td>';
      rptData += '<td style="text-align:right;"></td>';
      rptData += '</tr>';
      rptData += '<tr>';
      rptData += '<td style="text-align:right;" colspan="6"><span style="font-weight:bold;color:blue;">Saldo Final</span></td>';
      rptData += '<td style="text-align:right;"><span class="" style="color:blue;font-weight:bold;font-size:14px;">'+appFormatMoney(totDepo-totReti,2)+'</span></td>';
      rptData += '<td style="text-align:right;" colspan="2"></td>';
      rptData += '</tr>';
      $('#grdExtractoBody').html(rptData);
    }else{
      $('#grdExtractoBody').html('<tr><td colspan="8" style="text-align:center;color:red;">****** Sin Resultados ******</td></tr>');
    }
    $('#modalExtracto').modal();
  });
}

function modalPagosCalcularKeyPress(e) {
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { modalPagosCalcularTotal(); }
}

function modalPagosCalcularTotal(){
  let total = 0;
  $('input[name^="txt_PagosImporte"]').each(function(index) {
    let saldo = parseFloat($(this).val());
    if(Number.isNaN(saldo)) { $(this).val("0.00"); }
    else { total += (gridDetalle[index].saldo = saldo); $(this).val(appFormatMoney(saldo,2)); }
  });
  $("#txt_PagoTotal").val(appFormatMoney(total,2));
}

function modalPagosInsertPago(){
  let pagCuotaID = 0;
  let pagCuotaProductoID = 0;
  let pagCuotaTipo = "";
  let pagCuotaMonto = 0;
  let pagAporteID = 0;
  let pagAporteProductoID = 0;
  let pagAporteTipo = "";
  let pagAporteMonto = 0;
  let formu = document.forms["frm_modalPagos"].elements;

  if(formu[2].value==1){ //producto... Cuota de Ingreso
    pagCuotaProductoID = formu[2].value;
    pagCuotaID = formu[3].value;
    pagCuotaTipo = formu[4].value;
    pagCuotaMonto = appConvertToNumero(formu[5].value);
    pagAporteProductoID = formu[6].value;
    pagAporteID = formu[7].value;
    pagAporteTipo = formu[8].value;
    pagAporteMonto = appConvertToNumero(formu[9].value);
  }
  if(formu[2].value==2){ //producto... Aportes
    pagCuotaProductoID = 0;
    pagCuotaID = 0;
    pagCuotaTipo = "";
    pagCuotaMonto = 0;
    pagAporteProductoID = formu[2].value;
    pagAporteID = formu[3].value;
    pagAporteTipo = formu[4].value;
    pagAporteMonto = appConvertToNumero(formu[5].value);
  }

  if(pagAporteMonto>0) {
    let datosPago = {
      TipoQuery:"insAporte",
      socioID : $("#lbl_SocioID").html(),
      pagFechaIng : appConvertToFecha(formu[1].value,""),
      pagCuota : { ID : pagCuotaID, productoID : pagCuotaProductoID, tipo : pagCuotaTipo, monto : pagCuotaMonto },
      pagAporte : { ID : pagAporteID, productoID : pagAporteProductoID, tipo : pagAporteTipo, monto : pagAporteMonto },
      pagTipoOperID : 1,
      pagTipoMoneID : 1
    }
    appAjaxInsert(datosPago,rutaSQL).done(function(resp){
      $("#divPagos").hide();
      $("#divPrint").show();
      $('#objPDF').prop("data",urlServer+"/includes/pdf/plantilla/rpt_solicIngrSoc.php?nroDNI=29723728&ciudad=arequipa");
      $("#btn_modalPagosInsert").hide();
      appAportesSaldosReset();
    });
  } else {
    alert("El aporte debe ser mayor a 0.00");
  }
}

function modalRetirosCalcularKeyPress(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { modalRetirosCalcularTotal(); }
}

function modalRetirosCalcularTotal(){
  let monto = appConvertToNumero($("#txt_RetiroImporte").val());
  let maxmonto = appConvertToNumero($("#hid_RetiroMaxMonto").val());

  if(monto>maxmonto){
    $("#txt_RetiroImporte").val(appFormatMoney(maxmonto,2));
    $("#txt_RetiroTotal").val(appFormatMoney(maxmonto,2));
  } else {
    $("#txt_RetiroImporte").val(appFormatMoney(monto,2));
    $("#txt_RetiroTotal").val(appFormatMoney(monto,2));
  }
}

function modalRetirosInsertRetiro(urlServer){
  let retAporteID = $("#hid_RetiroID").val();
  let retAporteProductoID = $("#hid_RetiroProductoID").val();
  let retAporteTipo = $("#hid_RetiroTipo").val();
  let retAporteMonto = appConvertToNumero($("#txt_RetiroImporte").val());

  if(retAporteMonto>0) {
    let datos = {
      TipoQuery:"insRetiro",
      socioID : $("#lbl_SocioID").html(),
      pagFechaIng : appConvertToFecha($("#date_fechadel").val(),""),
      pagCuota : { ID : 0, productoID : 0, tipo : "", monto : 0 },
      pagAporte : { ID : retAporteID, productoID : retAporteProductoID, tipo : retAporteTipo, monto : retAporteMonto },
      pagTipoOperID : 1,
      pagTipoMoneID : 1
    }

    appAjaxInsert(datos,rutaSQL).done(function(resp){
      $("#divRetiros").hide();
      $("#divRetiroPrint").show();
      $('#objRetiroPDF').prop("data",urlServer+"/includes/pdf/plantilla/rpt_solicIngrSoc.php?nroDNI=29723728&ciudad=arequipa");
      $("#btn_modalRetiroDelete").hide();
      appAportesSaldosReset();
    });
  } else {
    alert("El retiro de aporte debe ser mayor a 0.00");
  }
}
