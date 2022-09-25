var rutaSQL = "pages/caja/ahorros/sql.php";

//=========================funciones para Personas============================
function appAhorrosGetAll(){
  let agenciaID = $("#cboAgencias").val();
  let txtBuscar = $("#txtBuscar").val();
  let datos = {
    TipoQuery : 'selAhorros',
    agenciaID : agenciaID,
    buscar : txtBuscar
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    let disabledDelete = (resp.usernivel==resp.admin) ? ("") : ("disabled");

    if(disabledDelete.length>0) { $('#chk_All').attr("disabled",disabledDelete);}
    if(resp.tabla.length>0){
      let appData = "";
      $.each(resp.tabla,function(key, valor){
        if(valor.url===""){ persona_url = ''; } else { persona_url = '<i class="fa fa-paperclip"></i>'; }
        appData += '<tr>';
        appData += '<td><input type="checkbox" id="chk_Borrar" name="chk_Borrar[]" value="'+(valor.id_socio)+'" '+disabledDelete+'/></td>';
        appData += '<td>'+(valor.codigo)+'</td>';
        appData += '<td>'+(valor.DNI)+'</td>';
        appData += '<td><a href="javascript:appAhorrosEdit('+(valor.id_socio)+');" title="'+(valor.id_socio)+'">'+(valor.socio)+'</a></td>';
        appData += '<td>'+(valor.agencia)+'</td>';
        appData += '</tr>';
      });
      $('#grdDatosBody').html(appData);
    }else{
      let mensaje = "";
      if (txtBuscar!=""){ mensaje = "para "+txtBuscar; }
      $('#grdDatosBody').html('<tr><td colspan="7" style="text-align:center;color:red;">Sin Resultados '+mensaje+'</td></tr>');
    }
    $('#grdDatosCount').html(resp.tabla.length+"/"+resp.cuenta);
  });
}

function appAhorrosReset(agenciaID){
  $("#txtBuscar").val("");
  let datos = {
    TipoQuery : 'ComboBox',
    miSubSelect : 'Agencias'
  }
  appAjaxSelect(datos).done(function(resp){
    appLlenarComboAgencias("#cboAgencias",resp.agenciaID,resp.tabla);
    appAhorrosGetAll();
  });
}

function appAhorrosBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { appAhorrosGetAll(); }
}

function appAhorrosEdit(socioID){
  let datos = {
    TipoQuery : 'ahorrosSocio',
    personaID : socioID,
    qryPers : 1,
    qryUser : 1
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    appAhorrosEditLlenarInfoCorta(resp.tablaPers);
    appAhorrosEditLlenarSaldoAhorros(resp.tablaSaldos,resp.tablaUser);

    $('#grid').hide();
    $('#edit').show();
  });
}

function appAhorrosEditLlenarInfoCorta(data){
  $('#img_PersFoto').prop("src",data.urlfoto=="" ? "data/personas/images/0noFotoUser.jpg" : data.urlfoto);
  $("#lbl_SocioNombres").html(data.nombres);
  $("#lbl_SocioApellidos").html(data.apellidos);
  $("#lbl_SocioID").html(data.ID);
  $("#lbl_SocioDNI").html(data.nroDNI);
  $("#lbl_SocioCelular").html(data.celular);
  $("#lbl_SocioAgencia").html(data.agencia);
  $("#hid_SocioAgenciaID").val(data.agenciaID);
}

function appAhorrosEditLlenarSaldoAhorros(data,user){
  let refer = (user.usernivel==user.admin) ? ('href="javascript:appMtto') : ('');

  if(data.length>0){
    let appData = "";
    $.each(data,function(key, valor){
      let colorData = (valor.saldo<0) ? ("color:red;") : ((valor.saldo==0) ? ("color:#aaaaaa;") : ("color:black;"));
      let colorLink = (valor.saldo>0) ? ("") : (colorData);

      appData += '<tr style="'+colorData+'">';
      appData += '<td>'+(key+1)+'</td>';
      appData += '<td><a href="javascript:appExtractoBancario(\''+(valor.producto)+' '+appFormatMoney(valor.tasa,2)+'%; Cert.: '+(valor.certificado)+'\','+(valor.ID)+');" style="'+colorLink+'"><i class="fa fa-file-text-o" title="Extracto Bancario..."></i></a></td>';
      appData += '<td><a href="javascript:appSuplentes('+(valor.ID)+');" style="'+colorLink+'"><i class="fa fa-odnoklassniki" title="Suplentes..."></i></a></td>';
      appData += '<td><a href="javascript:appIntereses('+(valor.ID)+');" style="'+colorLink+'"><i class="fa fa-linkedin-square" title="Generar Intereses..."></i></a></td>';
      appData += '<td><a href="javascript:appRetiros('+(valor.ID)+');" style="'+colorLink+'"><i class="fa fa-send-o" title="Retirar..."></i></a></td>';
      appData += '<td><a '+refer+' ('+(valor.ID)+');" style="'+colorLink+'" title="'+(valor.ID)+'">'+(valor.producto)+' '+appFormatMoney(valor.tasa,2)+'% '+'</a></td>';
      appData += '<td>'+(valor.retiro)+'</td>';
      appData += '<td style="text-align:right;">'+(valor.certificado)+'</td>';
      appData += '<td style="text-align:right;">'+(valor.fechaini)+'</td>';
      appData += '<td style="text-align:right;">'+(valor.fechafin)+'</td>';
      appData += '<td style="text-align:right;">'+appFormatMoney(valor.saldo,2)+'</td>';
      appData += '</tr>';
    });
    $('#grdAhorrosBody').html(appData);
  }else{
    $('#grdAhorrosBody').html('<tr><td colspan="12" style="text-align:center;color:#aaaaaa;">No hay registros</td></tr>');
  }
}

function appAhorrosNew(){
  let appData = "";
  let datos = {
    TipoQuery : 'ahorrosADD',
    personaID : $("#lbl_SocioID").html(),
    agenciaID : $("#hid_SocioAgenciaID").val()
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    //limpiar controles
    $("#div_fechaFin").prop("class","form-group");
    $("#div_nrocert").prop("class","form-group");
    $("#div_tasa").prop("class","form-group");
    $("#div_plazo").prop("class","form-group");

    //datos generales
    $("#lbl_modAhorrosTitulo").html($("#lbl_SocioApellidos").html()+', '+$("#lbl_SocioNombres").html());
    $('#txt_fecha').datepicker("setDate",resp.fecha);
    $("#txt_fechaFin").val("");
    $("#txt_nrocert").val("");
    $("#txt_tasa").val("");
    $("#txt_plazo").val("");
    $("#txt_observac").val("");
    appLlenarDataEnComboBox(resp.promotores,"#cbo_promotor",0);
    appLlenarDataEnComboBox(resp.productos,"#cbo_productos",0);
    $("#divAhorros").show();

    //llenar tabla con movimientos
    if(resp.movs.length>0){
      $.each(resp.movs,function(key, valor){
        appData += '<tr style="font-size:18px;">';
        appData += '<td>1</td>';
        appData += '<td><input id="hid_AhorrosTipoMovID" name="hid_AhorrosTipoMovID[]" type="hidden" value="'+(valor.ID)+'">'+(valor.nombre)+'</td>';
        appData += '<td><input id="txt_AhorrosImporte" name="txt_AhorrosImporte[]" type="text" class="form-control" style="width:130px;text-align:right;" value="10.00" onkeypress="modalAhorrosCalcularKeyPress(event);" onblur="modalAhorrosCalcularTotal();" /></td>';
        appData += '</tr>';
      });

      $('#grdAhorrosNewBody').html(appData);
      modalAhorrosCalcularTotal();
      $("#divAhorrosPrint").hide();
      $("#divPagos").show();
      $("#btn_modalAhorrosInsert").show();
    }
    $('#modalAhorros').modal();
  });
}

function appAhorrosRegresar(){
  $('#grid').show();
  $('#edit').hide();
  appAhorrosGetAll();
}

function appAhorrosSaldosReset(){
  let datos = {
    TipoQuery : 'ahorrosSocio',
    personaID : $("#lbl_SocioID").html(),
    qryPers : 0,
    qryUser : 1
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    appAhorrosEditLlenarSaldoAhorros(resp.tablaSaldos,resp.tablaUser);
  });
}

function appIntereses(ahorroID){
  let datos = {
    TipoQuery : 'ahorrosOne',
    ahorroID : ahorroID
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    $('#lbl_modInteresesTitulo').html("Generar Intereses para "+$("#lbl_SocioApellidos").html()+', '+$("#lbl_SocioNombres").html());
    $('#txt_modInteresesProducto').val(resp.producto+" "+appFormatMoney(resp.tasa,2)+"%    certificado Nº "+resp.certificado);
    $('#hid_modInteresesAhorroID').val(resp.ID);
    if(resp.meses<=0){
      $('#lbl_modInteresesWait').html('¡¡¡Los intereses fueron actualizados!!!, ver detalle en el SALDO');
      $('#btn_modInteresesGenerar').hide();
    } else{
      $('#lbl_modInteresesWait').html("");
      $('#btn_modInteresesGenerar').show();
    }
    $('#modalIntereses').modal();
  });
}

function appMtto(ahorroID){
  let datos = {
    TipoQuery : 'ahorrosRET',
    ahorroID : ahorroID
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    $("#lbl_modMttoTitulo").html($("#lbl_SocioApellidos").html()+', '+$("#lbl_SocioNombres").html());
    $('#lbl_modMttoServicio').html(resp.ahorro.producto+" "+appFormatMoney(resp.ahorro.tasa,2)+"%; Certificado Nº: "+resp.ahorro.certificado+";");
    $('#lbl_modMttoPromotor').html(resp.ahorro.promotor);
    $('#lbl_modMttoIntereses').html(resp.ahorro.retirointeres);
    $('#lbl_modMttoFecContrato').html(resp.ahorro.fechacont);
    $('#lbl_modMttoImporte').html(appFormatMoney(resp.ahorro.importe,2));
    $('#lbl_modMttoSaldo').html(appFormatMoney(resp.ahorro.saldo,2));
    $('#hid_modMttoAhorroID').val(resp.ahorro.ID);
    $('#cbo_modMttoRetiro').val(resp.ahorro.retanticipado);
    $('#cbo_modMttoBloqueo').val(resp.ahorro.retbloqueo);
    $('#txt_modMttoDesde').datepicker("setDate",resp.ahorro.fechaini);
    $('#txt_modMttoHasta').datepicker("setDate",resp.ahorro.fechafin);
    $('#txt_modMttoObservac').val(resp.ahorro.observac);
    $('#modalMtto').modal();
  });
}

function appRetiros(ahorroID){
  let datos = {
    TipoQuery : 'ahorrosRET',
    ahorroID : ahorroID
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    //datos generales
    $("#lbl_modRetirosTitulo").html($("#lbl_SocioApellidos").html()+', '+$("#lbl_SocioNombres").html());
    $('#lbl_modRetirosServicio').html(resp.ahorro.producto+" "+appFormatMoney(resp.ahorro.tasa,2)+"%; Certificado Nº: "+resp.ahorro.certificado+"; Vigencia: "+resp.ahorro.fechaini+" - "+resp.ahorro.fechafin);
    $('#lbl_modRetirosPromotor').html(resp.ahorro.promotor);
    $('#lbl_modRetirosInteresTipo').html(resp.ahorro.retirointeres);
    $('#lbl_modRetirosImporte').html(appFormatMoney((resp.ahorro.importe),2));
    $('#lbl_modRetirosIntereses').html(appFormatMoney(resp.ahorro.intereses,2));
    $('#lbl_modRetirosSaldo').html(appFormatMoney(resp.ahorro.saldo,2));
    $("#hid_modRetirosAnti").val(resp.ahorro.retanticipado);
    $('#hid_modRetirosDisponible').val(resp.ahorro.disponible);
    $('#hid_modRetirosTipo').val(resp.ahorro.retinteres);
    $('#hid_modRetirosAhorroID').val(resp.ahorro.ID);
    $('#txt_modRetirosFecha').val(resp.fecha);
    $("#txt_modRetirosObservac").val("disponible: "+appFormatMoney(resp.ahorro.disponible,2)+" plazo: "+resp.ahorro.plazo+" intereses: "+resp.ahorro.intereses);
    //$("#txt_modRetirosObservac").val(resp.ahorro.observac);
    $("#divRetiros").show();
    $("#divRetirosPrint").hide();

    //verificar bloqueo / retiro anticipado
    let mensaje = (resp.ahorro.retbloqueo==1) ? ("¡¡¡ESTE AHORRO ESTA BLOQUEADO!!!") : ((resp.ahorro.retanticipado==1) ? ("¡¡¡SE AUTORIZO EL RETIRO ANTICIPADO!!!") : (""));
    $("#lbl_modRetirosMensajes").html(mensaje);

    //llenar tabla con movimientos
    let appData = "";
    if(resp.movs.length>0){
      $.each(resp.movs,function(key, valor){
        appData += '<tr style="font-size:18px;">';
        appData += '<td>1</td>';
        appData += '<td><input id="hid_RetirosTipoMovID" type="hidden" value="'+(valor.ID)+'">'+(valor.nombre)+'</td>';
        appData += '<td><input id="txt_RetirosImporte" type="text" class="form-control" style="width:130px;text-align:right;" value="'+(appFormatMoney($('#hid_modRetirosDisponible').val(),2))+'" onkeypress="modalRetirosCalcularKeyPress(event);" onblur="modalRetirosCalcularTotal();" /></td>';
        appData += '</tr>';
      });
      $('#grdRetirosBody').html(appData);
      modalRetirosCalcularTotal();
    }
    $('#modalRetiros').modal();
  });
}

function appExtractoBancario(producto,ahorroID){
  let datos = {
    TipoQuery: 'ExtrBancario',
    socioID: $("#lbl_SocioID").html(),
    operacionID: 2,
    prestahorroID: ahorroID
  };

  appAjaxSelect(datos).done(function(resp){
    $("#lbl_modExtractoTitulo").html(producto);
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
        rptData += '</tr>';
      });
      rptData += '<tr>';
      rptData += '<td style="text-align:right;" colspan="6"><b>Totales</b></td>';
      rptData += '<td style="text-align:right;"><b>'+appFormatMoney(totDepo,2)+'</b></td>';
      rptData += '<td style="text-align:right;"><b>'+appFormatMoney(totReti,2)+'</b></td>';
      rptData += '</tr>';
      rptData += '<tr>';
      rptData += '<td style="text-align:right;" colspan="6"><span style="font-weight:bold;font-size:14px;color:blue;">Saldo Final</span></td>';
      rptData += '<td style="text-align:right;"><span class="" style="font-weight:bold;font-size:14px;color:blue;">'+appFormatMoney(totDepo-totReti,2)+'</span></td>';
      rptData += '<td style="text-align:right;"></td>';
      rptData += '</tr>';
      $('#grdExtractoBody').html(rptData);
    }else{
      $('#grdExtractoBody').html('<tr><td colspan="7" style="text-align:center;color:red;">****** Sin Resultados ******</td></tr>');
    }
    $('#modalExtracto').modal();
  });
}

function appSuplentes(ahorroID){
  $('#modalSuplentes').modal();
  modalSuplenteCancel();

  let data = {
    TipoQuery : 'ahorrosOne',
    ahorroID : ahorroID
  }
  appAjaxSelect(data,rutaSQL).done(function(resp){
    $('#lbl_modSuplentesTitulo').html(resp.producto+" "+appFormatMoney(resp.tasa,2)+"%   »  certificado Nº "+resp.certificado);
    $('#hid_modSuplentesAhorroID').val(ahorroID);

    let datos = {
      TipoQuery : 'ahorrosOneSuplentes',
      ahorroID : ahorroID
    }
    appAjaxSelect(datos,rutaSQL).done(function(resp){
      if(resp.length>0){
        let appData = "";
        $.each(resp,function(key, valor){
          let fila = "";
          if(valor.tipo>0){ fila = 'background:#ffffbd;' };
          appData += '<tr style="font-size:13px;'+fila+'">';
          appData += '<td><input type="checkbox" id="chk_BorrarSuplente" name="chk_BorrarSuplente[]" value="'+(valor.ID)+'"/></td>';
          appData += '<td>'+(key+1)+'</td>';
          appData += '<td style="text-align:right;">'+(valor.DNI)+'</td>';
          appData += '<td>'+(valor.suplente)+'</td>';
          appData += '<td>'+(valor.tipotexto)+'</td>';
          appData += '<td>'+(valor.fechanac)+'</td>';
          appData += '<td>'+(valor.parentesco)+'</td>';
          appData += '<td>'+(valor.celular)+'</td>';
          appData += '</tr>';
        });
        $('#grdSuplentesBody').html(appData);
      }else{
        $('#grdSuplentesBody').html('<tr><td colspan="8" style="text-align:center;color:#aaaaaa;">Sin SUPLENTES</td></tr>');
      }
    });
  });
}

function appSuplentesCommand(command){
  switch(command){
    case "new":
      $('#hid_modSuplenteID').val("");
      $('#txt_modSuplentesPersona').val("");
      $('#txt_modSuplentesParentesco').val("");
      $('#modSuplentesGrid').hide();
      $('#modSuplentesEdit').show();
      break;
    case "del":
      let arr = $('[name="chk_BorrarSuplente[]"]:checked').map(function(){ return this.value; }).get();
      if(arr.length>0){
        if(confirm("¿Desea eliminar realmente estos suplentes?")==true){
          let datos = {
            TipoQuery : 'delSuplentes',
            IDs : arr
          }
          appAjaxDelete(datos,rutaSQL).done(function(resp){
            if (resp.error == false) { //sin errores
              appSuplentes($('#hid_modSuplentesAhorroID').val());
            }
          });
        }
      } else {
        alert("NO eligio borrar ninguno");
      }
      break;
  }
}

function modalAhorrosCalcularKeyPress(e) {
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { modalAhorrosCalcularTotal(); }
}

function modalAhorrosCalcularTotal(){
  let monto = 0;
  let formu = document.forms["frm_modalAhorros"].elements;

  for(xx=1;xx<formu.length;xx++){
    if((formu[xx].id!="hid_PagoID") && (formu[xx].id!="hid_PagoTipo")) {
      if(formu[xx].id=="txt_AhorrosImporte") {
        monto += appConvertToNumero(formu[xx].value);
        formu[xx].value = appFormatMoney(appConvertToNumero(formu[xx].value),2);
      }
      if(formu[xx].id=="txt_PagoTotal"){
        formu[xx].value = appFormatMoney(monto,2);
      }
    }
  }
}

function modalAhorrosFechaFin(){
  if(!isNaN($("#txt_plazo").val())){
    let txtFecha = appConvertToFecha($("#txt_fecha").val(),"-");
    let fechaIni = new Date(txtFecha);
    let numMeses = Number($("#txt_plazo").val());

    fechaIni.setTime(fechaIni.getTime() + fechaIni.getTimezoneOffset() * 60 * 1000);
    fechaIni.setMonth(fechaIni.getMonth()+numMeses)

    let res = ("0"+fechaIni.getDate()).slice(-2)+"/"+("0"+(fechaIni.getMonth()+1)).slice(-2)+"/"+fechaIni.getFullYear();
    $("#div_plazo").prop("class","form-group");
    $("#div_fechaFin").prop("class","form-group");
    $("#txt_fechaFin").val(res);
  } else {
    $("#div_plazo").prop("class","form-group has-error");
    $("#div_fechaFin").prop("class","form-group has-error");
  }
}

function modalAhorrosInsert(urlServer){
  let datosAhorros = modalAhorrosGetDatosToDatabase();

  if(datosAhorros!=""){
    let datos = {
      TipoQuery : "insAhorros",
      datosAhorro : datosAhorros
    }
    appAjaxInsert(datos,rutaSQL).done(function(resp){
      $("#divAhorros").hide();
      $("#divAhorrosPrint").show();
      $('#obj_modalAhorroPDF').prop("data",urlServer+"/includes/pdf/plantilla/rpt_solicIngrSoc.php?nroDNI=29723728&ciudad=arequipa");
      $("#btn_modalAhorrosInsert").hide();
      appAhorrosSaldosReset();
    });
  } else {
    alert("Falta llenar un dato de manera correcta");
  }
}

function modalAhorrosGetDatosToDatabase(){
  let EsError = false;
  let datos = "";
  if($("#txt_fechaFin").val()=="") { $("#div_fechaFin").prop("class","form-group has-error"); EsError = true; } else { $("#div_fechaFin").prop("class","form-group"); }
  if($("#txt_nrocert").val()=="") { $("#div_nrocert").prop("class","form-group has-error"); EsError = true; } else { $("#div_nrocert").prop("class","form-group"); }
  if($("#txt_tasa").val()=="" || isNaN($("#txt_tasa").val())) { $("#div_tasa").prop("class","form-group has-error"); EsError = true; } else { $("#div_tasa").prop("class","form-group"); }
  if($("#txt_plazo").val()=="" || isNaN($("#txt_plazo").val())) { $("#div_plazo").prop("class","form-group has-error"); EsError = true; } else { $("#div_plazo").prop("class","form-group"); }
  if(isNaN(appConvertToNumero($("#txt_AhorrosImporte").val()))) { EsError = true; }

  if(!EsError){
    datos = {
      numero : $("#txt_nrocert").val(),
      agenciaID : $("#hid_SocioAgenciaID").val(),
      promotorID : $("#cbo_promotor").val(),
      productoID : $("#cbo_productos").val(),
      socioID : $("#lbl_SocioID").html(),
      fechaIni : appConvertToFecha($("#txt_fecha").val(),""),
      fechaFin : appConvertToFecha($("#txt_fechaFin").val(),""),
      plazo : $("#txt_plazo").val(),
      tasa : $("#txt_tasa").val(),
      tasamin : 2.16,
      tiporetiro : $("#cbo_intereses").val(),
      importe : appConvertToNumero($("#txt_AhorrosImporte").val()),
      observac : $("#txt_observac").val()
    }
  }
  return datos;
}

function modalRetirosCalcularKeyPress(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { modalRetirosCalcularTotal(); }
}

function modalRetirosCalcularTotal(){
  let total = 0;
  let monto = appConvertToNumero($("#txt_RetirosImporte").val());
  let disponible = appConvertToNumero($("#hid_modRetirosDisponible").val());
  let saldo = appConvertToNumero($('#lbl_modRetirosImporte').html())+appConvertToNumero($('#lbl_modRetirosIntereses').html());

  //verificacion del monto
  if(monto>disponible){ total = disponible; saldo = saldo - disponible; }
  else { total = monto; saldo = saldo - monto; }

  //verificacion de retiro anticipado
  if($('#hid_modRetirosAnti').val()==0){ if(total>0){ $("#btn_modRetirosInsert").show(); } else { $("#btn_modRetirosInsert").hide(); } }
  else { $("#btn_modRetirosInsert").show(); }

  $("#txt_RetirosImporte").val(appFormatMoney(total,2));
  $("#txt_RetirosTotal").val(appFormatMoney(total,2));
  $("#lbl_modRetirosSaldo").html(appFormatMoney(saldo,2));
}

function modalRetirosInsert(urlServer){
  let montoRetiro = appConvertToNumero($("#txt_RetirosTotal").val());
  if(montoRetiro>0) {
    let datos = {
      TipoQuery:"updAhorrosRetiro",
      ahorroID : $("#hid_modRetirosAhorroID").val(),
      fecha : appConvertToFecha($("#txt_modRetirosFecha").val(),""),
      retinteres : $("#hid_modRetirosTipo").val(),
      retanticipado : $("#hid_modRetirosAnti").val(),
      importe : montoRetiro
    }

    appAjaxUpdate(datos,rutaSQL).done(function(resp){
      $("#divRetiros").hide();
      $("#divRetirosPrint").show();
      $('#obj_modalRetirosPDF').prop("data",urlServer+"/includes/pdf/plantilla/rpt_solicIngrSoc.php?nroDNI=29723728&ciudad=arequipa");
      $("#btn_modRetirosInsert").hide();
      appAhorrosSaldosReset();
    });
  } else {
    alert("El retiro de AHORRO debe ser mayor a 0.00");
  }
}

function modalMttoUpdate(){
  let datos = {
    TipoQuery:"updAhorrosMtto",
    ahorroID : $("#hid_modMttoAhorroID").val(),
    fechaini : appConvertToFecha($("#txt_modMttoDesde").val(),""),
    fechafin : appConvertToFecha($("#txt_modMttoHasta").val(),""),
    retanticipado : $("#cbo_modMttoRetiro").val(),
    retbloqueo : $("#cbo_modMttoBloqueo").val(),
    observac : $("#txt_modMttoObservac").val()
  }

  appAjaxUpdate(datos,rutaSQL).done(function(resp){
    appAhorrosSaldosReset();
    $('#modalMtto').modal("hide");
  });
}

function modalSuplenteCancel(){
  $('#modSuplentesGrid').show();
  $('#modSuplentesEdit').hide();
}

function modalSuplenteInsert(){
  let EsError = false;
  if($("#hid_modSuplenteID").val()=="") { $("#div_modSuplentesPersona").prop("class","form-group has-error"); EsError = true; } else { $("#div_modSuplentesPersona").prop("class","form-group"); }
  if($("#hid_modSuplentesAhorroID").val()=="") { $("#div_modSuplentesProducto").prop("class","form-group has-error"); EsError = true; } else { $("#div_modSuplentesProducto").prop("class","form-group"); }
  if($("#txt_modSuplentesParentesco").val()=="") { $("#div_modSuplentesParentesco").prop("class","form-group has-error"); EsError = true; } else { $("#div_modSuplentesParentesco").prop("class","form-group"); }

  if(!EsError){
    let datos = {
      TipoQuery : "insSuplente",
      ahorroID : $("#hid_modSuplentesAhorroID").val(),
      suplenteID : $("#hid_modSuplenteID").val(),
      tipo : $("#cbo_modSuplentesTipo").val(),
      parentesco : $("#txt_modSuplentesParentesco").val()
    }
    appAjaxInsert(datos,rutaSQL).done(function(resp){
      appSuplentes($("#hid_modSuplentesAhorroID").val());
      $("#modalAddSuplentes").modal("hide");
    });
  } else {
    alert("¡¡¡Faltan datos!!!");
  }
}

function modalInteresesGenerar(){
  $('#lbl_modInteresesWait').html('<div class="progress progress-sm active" style=""><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div>');
  let datos = {
    TipoQuery : "updAhorrosIntereses",
    ahorroID : $('#hid_modInteresesAhorroID').val(),
    socioID : $('#lbl_SocioID').html()
  }
  appAjaxUpdate(datos,rutaSQL).done(function(resp){
    appAhorrosEditLlenarSaldoAhorros(resp.tablaSaldos,resp.tablaUser);
    $('#lbl_modInteresesWait').html('¡¡¡Los intereses fueron actualizados!!!, ver detalle en el SALDO');
    $('#btn_modInteresesGenerar').hide();
  });
}

function modPersAddToParentForm(personaID){
  switch(Persona.getQueryBuscar()){
    case "VerifyAhorros":
      var datos = {
        TipoQuery : 'OneSocio',
        personaID : personaID,
        fullQuery : 0
      }
      appAjaxSelect(datos).done(function(resp){
        appAhorrosEditLlenarInfoCorta(resp);
        $('#grdAhorrosBody').html('<tr><td colspan="12" style="text-align:center;color:#aaaaaa;">No hay registros</td></tr>');
        $('#grdxxSuplentesBody').html('<tr><td colspan="8" style="text-align:center;color:#aaaaaa;">Sin SUPLENTES</td></tr>');
        $('#lblProductoSuplentes').html('');
        $('#grid').hide();
        $('#edit').show();
        Persona.close();
      });
      break;
    case "VerifySuplentes":
      var datos = {
        TipoQuery : 'OnePersona',
        personaID : personaID
      }
      appAjaxSelect(datos).done(function(resp){
        $('#hid_modSuplenteID').val(resp.ID);
        $('#txt_modSuplentesPersona').val(resp.apellidos+', '+resp.nombres);
        Persona.close();
      });
      break;
  }
}

function modPersInsert(){
  if(Persona.verificarErrores()==0){ //guardamos datos de persona
    let datos = Persona.datosToDatabase();
    let foto = $('input[name="file_modPersFoto"]').get(0).files[0];
    let formData = new FormData();

    formData.append('imgFoto', foto);
    formData.append("appInsert",JSON.stringify(datos));
    $.ajax({
      url:'includes/sql_insert.php',
      type:'POST',
      processData:false,
      contentType: false,
      data:formData
    })
    .done(function(resp){
      let data = JSON.parse(resp);
      modPersAddToParentForm(data.personaID);
    })
    .fail(function(resp){
      console.log("fail:.... "+resp.responseText);
    });
  }
}
