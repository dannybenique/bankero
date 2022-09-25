var rutaSQL = "pages/oper/confirmapagos/sql.php";

//=========================funciones para workers============================
function appGridAll(){
  $('#grdDatosCount').html("");
  $('#grdDatosBody').html('<tr><td colspan="11" style="text-align:center;"><br><div class="progress progress-sm active"><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div><br>Un momento, por favor...</td></tr>');

  let agenciaID = $("#cboAgencias").val();
  let txtBuscar = $("#txtBuscar").val().trim();
  let datos = {
    TipoQuery : 'sel_Confirmaciones',
    agenciaID : agenciaID,
    buscar : txtBuscar
  };
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    if(resp.tabla.length>0){
      let fila = "";
      let disabledDelete = (resp.usernivel==resp.admin) ? ("") : ("disabled");

      $.each(resp.tabla,function(key, valor){
        fila += '<tr style="'+((valor.statusconta==1 && valor.statuscaja==1)?("color:#bbb;"):(""))+'">';
        fila += '<td><input type="checkbox" name="chk_Borrar" value="'+(valor.ID)+'" '+disabledDelete+'/></td>';
        fila += '<td><a href="javascript:appConfirmaPago('+(valor.ID)+')" style="'+((valor.statusconta==1 && valor.statuscaja==1)?("color:#bbb;"):(""))+'" title="confirmacion de Contabilidad"><i class="fa fa-database"></i></a></td>';
        fila += '<td>'+(valor.tipo_oper)+'</td>';
        fila += '<td>'+(valor.codsocio)+'<br>'+(valor.socio)+'</td>';
        fila += '<td>'+(moment(valor.fecha.date).format("DD/MM/YYYY"))+'</td>';
        fila += '<td><a href="javascript:appEditarPago('+(valor.ID)+');" style="'+((valor.statusconta==1 && valor.statuscaja==1)?("color:#bbb;"):(""))+'" title="'+(valor.ID)+'">'+(valor.banco)+' - '+(valor.voucher)+'</a></td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.importe,2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.comision,2)+'</td>';
        fila += '<td>'+(valor.solicitante)+'</td>';
        fila += '<td><i class="fa fa-check"></i> conta: '+(valor.confirmaconta)+'<br><i class="fa fa-check"></i> caja: '+(valor.confirmacaja)+'</td>';
        fila += '<td style="text-align:center;"><span style="font-weight:normal" class="label '+((valor.statusconta==0)?('bg-orange">standby conta'):('bg-blue">OK conta'))+'</span><br><span style="font-weight:normal" class="label '+((valor.statuscaja==0)?('bg-orange">standby caja'):('bg-blue">OK caja'))+'</span></td>';
        fila += '</tr>';
      });
      $('#grdDatosBody').html(fila);
    }else{
      let mensaje = (txtBuscar=="") ? ("") : ("para "+txtBuscar);
      $('#grdDatosBody').html('<tr><td colspan="11" style="text-align:center;color:red;">Sin Resultados '+mensaje+'</td></tr>');
    }
    $('#grdDatosCount').html(resp.tabla.length+"/"+resp.cuenta);
    $('#agenciaAbrev').val(resp.agenciaAbrev);
  });
}

function appBotonReset(){
  $("#txtBuscar").val("");
  let datos = { TipoQuery:'agencias'}
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    resp.agencias.push({"ID":0,"nombre":"Todas las AG."});
    //appLlenarDataEnComboBox(resp.agencias,"#cboAgencias",resp.agenciaID);
    appLlenarDataEnComboBox(resp.agencias,"#cboAgencias",0);
    appGridAll();
  });
}

function appBotonNuevo(){
  let datos = { TipoQuery:'bancos_monedas'}

  $('.box-body .form-group').removeClass('has-error');
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    appLlenarDataEnComboBox(resp.bancos,"#cbo_bancos",0);
    appLlenarDataEnComboBox(resp.monedas,"#cbo_tipo_mone",0);
    $('#hid_codsocio').val('');
    $('#hid_socio').val('');
    $('#hid_ID').val('0');
    $('#txt_fecha').datepicker("setDate",moment().format("DD/MM/YYYY"));
    $('#txt_voucher').val('');
    $('#txt_importe').val('');
    $('#btn_Insert').show();
    $('#btn_Update').hide();
    $('#edit').show();
    $('#grid').hide();
  });
}

function appConfirmaPago(confirmaID){
  let datos = {
    TipoQuery : 'get_Confirmacion',
    confirmaID : confirmaID
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    //console.log(resp);
    $('#hid_modConfirmaID').val(resp.confirma.ID);
    $('#hid_modConfirmaImporte').val(resp.confirma.importe);
    $('#lbl_modConfirmaFecha').html(moment(resp.confirma.fecha.date).format("DD/MM/YYYY"));
    $('#lbl_modConfirmaCodigo').html(resp.confirma.codsocio);
    $('#lbl_modConfirmaSocio').html(resp.confirma.socio);
    $('#lbl_modConfirmaTipo').html(resp.confirma.tipo_oper);
    $('#lbl_modConfirmaBanco').html(resp.confirma.banco);
    $('#lbl_modConfirmaSede').html(resp.confirma.sede);
    $('#lbl_modConfirmaVoucher').html(resp.confirma.voucher);
    $('#lbl_modConfirmaMoneda').html(resp.confirma.moneda);
    $('#lbl_modConfirmaImporte').html(appFormatMoney(resp.confirma.importe,2));
    $('#lbl_modConfirmaComision').html(appFormatMoney(resp.confirma.comision,2));

    $("#txt_modConfirma_Sede").val("");
    $("#txt_modConfirma_Comision").val("0.00");

    //contabilidad y caja
    if((resp.usernivel==resp.admin || resp.usernivel==resp.conta) && resp.confirma.status_conta==0){ $("#div_modDatosConta").show(); } else { $("#div_modDatosConta").hide(); }
    if((resp.usernivel==resp.admin || resp.usernivel==resp.caja) && resp.confirma.status_caja==0){ $("#div_modDatosCaja").show(); } else { $("#div_modDatosCaja").hide(); }

    $('#modalConfirmaPago').modal("show");
  });
}

function appEditarPago(confirmaID){
  let datos = {
    TipoQuery : 'edit_ConfirmaPago_Solic',
    confirmaID : confirmaID
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    console.log(resp);
    appLlenarDataEnComboBox(resp.bancos,"#cbo_bancos",resp.confirma.bancoID);
    appLlenarDataEnComboBox(resp.monedas,"#cbo_tipo_mone",resp.confirma.monedaID);
    $('#hid_ID').val(resp.confirma.ID);
    $('#hid_socio').val(resp.confirma.socio);
    $('#hid_codsocio').val(resp.confirma.codsocio);
    $('#txt_codsocio').val(resp.confirma.codsocio+" » "+resp.confirma.socio);
    $('#txt_fecha').datepicker("setDate",moment(resp.confirma.fecha.date).format("DD/MM/YYYY"));
    $('#txt_importe').val(appFormatMoney(resp.confirma.importe,2));
    $('#txt_voucher').val(resp.confirma.voucher);
    if(resp.confirma.status_conta==1) { $('#btn_Update').hide(); } else { $('#btn_Update').show(); }
    $('#btn_Insert').hide();
    $('#edit').show();
    $('#grid').hide();
  });
}

function appBotonCancel(){
    $('#edit').hide();
    $('#grid').show();
}

function appBotonModalSocios(){
  $("#modCoopSUDGridDatosTabla").hide();
  $("#lbl_modCoopSUDWait").html("");
  $("#txt_BuscarModCoopSUD").val("");
  $('#modalCoopSUDSocio').modal({keyboard:true});
  $('#modalCoopSUDSocio').on('shown.bs.modal',function(e){ $('#txt_BuscarModCoopSUD').trigger('focus'); });
}

function appBotonInsert(){
  let datos = appGetDatosToDatabase();
  if(datos!=""){
    datos.commandSQL = "INS";
    appAjaxInsert(datos,rutaSQL).done(function(resp){
      if(resp.error){
        alert(resp.mensaje);
      } else {
        appGridAll();
        appBotonCancel();
      }
    });
  } else {
    alert("¡¡¡FALTAN LLENAR DATOS o LOS VALORES NO PUEDEN SER CERO!!!");
  }
}

function appBotonUpdate(){
  let datos = appGetDatosToDatabase();
  if(datos!=""){
    datos.commandSQL = "UPD";
    appAjaxUpdate(datos,rutaSQL).done(function(resp){
      if(resp.error) { alert(resp.mensaje); }
      appGridAll();
      appBotonCancel();
    });
  } else {
    alert("¡¡¡FALTAN LLENAR DATOS o LOS VALORES NO PUEDEN SER CERO!!!");
  }
}

function appGetDatosToDatabase(){
  let EsError = false;
  let rpta = "";
  let importe = appConvertToNumero($("#txt_importe").val());

  $('.box-body .form-group').removeClass('has-error');
  if(isNaN(importe) || (importe<=0)) { $("#div_importe").prop("class","form-group has-error"); EsError = true; }
  if($("#hid_codsocio").val()=="") { $("#div_codsocio").prop("class","form-group has-error"); EsError = true; }

  if(!EsError){
    rpta = {
      TipoQuery  : "exec_ConfirmaPago_Solic",
      confirmaID : $("#hid_ID").val(),
      codsocio   : $("#hid_codsocio").val(),
      socio      : $("#hid_socio").val(),
      fecha      : appConvertToFecha($("#txt_fecha").val(),""),
      tipo_oper  : $("#cbo_tipo_oper").val(),
      monedaID   : $("#cbo_tipo_mone").val(),
      bancoID    : $("#cbo_bancos").val(),
      voucher    : $("#txt_voucher").val().trim(),
      importe    : importe
    }
  }
  return rpta;
}

function modCoopSUD_keyBuscar(e){
  var code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { modCoopSUDbuscar(); }
}

function modCoopSUDbuscar(){
  let txtBuscar = $('#txt_BuscarModCoopSUD').val().trim().split('-');
  txtBuscar = zfill(Math.abs(txtBuscar[0]),2)+"-"+zfill(Math.abs(txtBuscar[1]),4);
  $('#txt_BuscarModCoopSUD').val(txtBuscar);
  $('#lbl_modCoopSUDWait').html('<div class="progress progress-sm active" style=""><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div>');
  let datos = {
    TipoQuery : "buscar_codsocio",
    codsocio : $('#txt_BuscarModCoopSUD').val()
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    $('#lbl_modCoopSUDWait').html("");
    if(resp!=null){
      $('#lbl_modCoopSUDcodigo').html(resp.codsocio);
      $('#lbl_modCoopSUDsocio').html(resp.socio+" &raquo; "+resp.doc+": "+resp.nrodoc);
      $("#modCoopSUDGridDatosTabla").show();
    }
  });
}

function modCoopSUDagregar(){
  $('#hid_codsocio').val($('#lbl_modCoopSUDcodigo').html());
  $('#hid_socio').val($('#lbl_modCoopSUDsocio').html());
  $('#txt_codsocio').val($('#lbl_modCoopSUDcodigo').html()+" » "+$('#lbl_modCoopSUDsocio').html());
  $('#modalCoopSUDSocio').modal("hide");
}

function modConfirmaConta(){
  let sede = $("#txt_modConfirma_Sede").val().trim();

  if((sede!="") && ($("#txt_modConfirma_Comision").val().trim()!="")){
    let importe = appConvertToNumero($("#hid_modConfirmaImporte").val());
    let comision = appConvertToNumero($("#txt_modConfirma_Comision").val().trim());
    let datos = {
      TipoQuery  : 'exec_ConfirmaPago_Conta',
      confirmaID : $('#hid_modConfirmaID').val(),
      sede       : sede,
      comision   : comision
    };
    appAjaxUpdate(datos,rutaSQL).done(function(resp){
      appGridAll();
      $('#modalConfirmaPago').modal("hide");
    });
  } else {
    alert("la Sede no puede esta en blanco, y la comision debe ser un valor numerico");
  }
}

function modConfirmaCAJA(){
  let sede = $("#txt_modConfirma_Sede").val().trim();

  if(confirm("¿Esta segur(o/a) de confirmar la transaccion hecha en el sistema COOPSUD?")){
    let datos = {
      TipoQuery  : 'exec_ConfirmaPago_CAJA',
      confirmaID : $('#hid_modConfirmaID').val()
    };
    appAjaxUpdate(datos,rutaSQL).done(function(resp){
      appGridAll();
      $('#modalConfirmaPago').modal("hide");
    });
  }
}

function txt_modConfirma_Sede_onblur(){
  $('#lbl_modConfirmaSede').html($("#txt_modConfirma_Sede").val());
}

function txt_modConfirma_Comision_onblur(){
  let importe = appConvertToNumero($("#hid_modConfirmaImporte").val());
  let comision = appConvertToNumero($("#txt_modConfirma_Comision").val());
  let actual = importe - comision;

  $('#lbl_modConfirmaComision').html(appFormatMoney(comision,2));
  $('#txt_modConfirma_Comision').val(appFormatMoney(comision,2));
  $("#lbl_modConfirmaImporte").html("<span style='color:#aaa;'>"+appFormatMoney((importe),2)+"</span> &raquo; "+appFormatMoney((actual),2));
}
