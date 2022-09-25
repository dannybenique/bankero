let rutaSQL = "pages/coopsud/adminprest/sql.php";
let arrPrest = [];

//=========================funciones para coopSUD Prestamos============================
function appGridAll(){
  let datos = {
    TipoQuery:'coopSUDcartera',
    filtroID:$("#cboAnalista").val(),
    saldo: $("#cboSaldo").val()
  };
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    $('#grdDatosBody').html('<tr><td colspan="11" style="text-align:center;color:red;">Sin Resultados</td></tr>');

    if(resp.length>0){
      let fila = "";
      $.each(resp,function(key, valor){
        fila += '<tr style="'+((valor.saldo<=0)?("color:#bbb;"):(""))+'">';
        fila += '<td><input type="checkbox" id="chk_Borrar" name="chk_Borrar[]" value="'+(valor.ID)+'"/></td>';
        fila += '<td>'+(valor.agencia)+'</td>';
        fila += '<td>'+(valor.codsocio)+'</td>';
        fila += '<td>'+(valor.socio)+'</td>';
        fila += '<td><a style="'+((valor.saldo<=0)?("color:#bbb;"):(""))+'" href="javascript:appPrestamoView(\''+(valor.codsocio)+'-'+(valor.tipo_serv)+'-'+(valor.numpres)+'\');">'+(valor.servicio)+' - '+(valor.cuotas)+' cuo</a></td>';
        fila += '<td colspan="2"></td>';
        fila += '<td style="text-align:center;">'+(moment(valor.fec_otorg.date).format('DD/MM/YYYY'))+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.importe,2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.saldo,2)+'</td>';
        fila += '<td>Prm: <span title="'+(valor.codpromotor)+'">'+(valor.promotor)+'</span><br><span title="'+(valor.codanalista)+'">Anlt: '+(valor.analista)+'</span></td>';
        fila += '</tr>';
      });
      $('#grdDatosBody').html(fila);
    }else{
      $('#grdDatosBody').html('<tr><td colspan="11" style="text-align:center;color:red;">Sin Resultados</td></tr>');
    }
    $('#grdDatosCount').html(resp.length);
  });
}

function appGridReset(){
  $('#dateIni').val(moment().format("01/MM/YYYY"));
  $('#dateFin').val(moment().format("DD/MM/YYYY"));
  $('#dateIni').datepicker({ autoclose: true })
  $('#dateFin').datepicker({ autoclose: true })
  let datos = { TipoQuery : 'Analistas_Activos' }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    if(resp.minivel==resp.admin){
      $('#opt_solicitud').html('<strong><i class="fa fa-file-text-o"></i> por solicitud de prestamo</strong><br><div class="btn-group"><button type="button" class="btn btn-default btn-sm" onclick="javascript:appBotonVeriPrest();" title="Verificar Prestamos"><i class="fa fa-eye"></i> veri Prestamo / Solicitud</button></div>');
      $('#opt_analista').html('<strong><i class="fa fa-user"></i> por analista</strong><br><div class="input-group" style="margin-top:3px;"><span class="input-group-addon">Cartera</span><select id="cboAnalista" class="form-control selectpicker" style="height:30px;"></select></div><div class="btn-group" style="margin-top:3px;"><button type="button" class="btn btn-default btn-sm" onclick="javascript:appGridAll();" title="cambiar analista"><i class="fa fa-flash"></i> Verificar Cartera</button><select id="cboSaldo" class="btn btn-default btn-sm" style="height:30px;text-align:left;"><option value="1">Saldo Mayor a Cero</option><option value="0">Saldo Cero 0</option></select></div><br><br><div class="btn-group"><button type="button" class="btn btn-default btn-sm" onclick="javascript:appBotonChangeUser();" title="cambiar analista"><i class="fa fa-user"></i> cambiar a este Analista</button></div>');
      appLlenarDataEnComboBox(resp.combobox,"#cboAnalista",0);
    }
  });
}

function appBotonVeriDesembolsos(){
  let datos = {
    TipoQuery:'coopsudDesembolsos',
    fechaINI:appConvertToFecha($("#dateIni").val(),"-"),
    fechaFIN:appConvertToFecha($("#dateFin").val(),"-")
  };
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    $('#grdDatosBody').html('<tr><td colspan="11" style="text-align:center;color:red;">Sin Resultados</td></tr>');

    if(resp.length>0){
      let fila = "";
      $.each(resp,function(key, valor){
        fila += '<tr style="">';
        fila += '<td></td>';
        fila += '<td>'+(valor.agencia)+'</td>';
        fila += '<td>'+(valor.codsocio)+'</td>';
        fila += '<td>'+(valor.socio)+'</td>';
        fila += '<td><a style="'+((valor.saldo<=0)?("color:#bbb;"):(""))+'" href="javascript:appPrestamoView(\''+(valor.codsocio)+'-'+(valor.tipo_serv)+'-'+(valor.numpres)+'\');">'+(valor.servicio)+' - '+(valor.cuotas)+' cuo</a></td>';
        fila += '<td>'+(valor.tipoSBS)+'</td>';
        fila += '<td>'+(valor.destinoSBS)+'</td>';
        fila += '<td style="text-align:center;">'+(moment(valor.fec_otorg.date).format('DD/MM/YYYY'))+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.importe,2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.saldo,2)+'</td>';
        fila += '<td>Prm: <span title="'+(valor.codpromotor)+'">'+(valor.promotor)+'</span><br><span title="'+(valor.codanalista)+'">Anlt: '+(valor.analista)+'</span></td>';
        fila += '</tr>';
      });
      $('#grdDatosBody').html(fila);
    }else{
      $('#grdDatosBody').html('<tr><td colspan="11" style="text-align:center;color:red;">Sin Resultados</td></tr>');
    }
    $('#grdDatosCount').html(resp.length);
  });
}

function appBotonVeriDesembolsosDownload(){
  let datos = {
    TipoQuery  : 'coopsudDesembolsosDownload',
    fechaINI:appConvertToFecha($("#dateIni").val(),"-"),
    fechaFIN:appConvertToFecha($("#dateFin").val(),"-")
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    Jhxlsx.export(resp.tableData, resp.options);
  });
}

function appBotonVeriPrest(){
  $('#grdDatosBody').html('<tr><td colspan="11" style="text-align:center;"><br><div class="progress progress-sm active" style=""><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div><br>Un momento, por favor...</td></tr>');
  let datos = { TipoQuery : 'verificar_Prestamos' }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    console.log(resp);

    if(resp.tablaError.length>0){
      alert("!!!tenemos "+(resp.tablaError.length)+" creditos con errores!!!");

      let fila = "";
      $.each(resp.tablaError,function(key, valor){
        fila += '<tr style="">';
        fila += '<td></td>';
        fila += '<td></td>';
        fila += '<td>'+(valor.codsocio)+'</td>';
        fila += '<td>'+(valor.socio)+'</td>';
        fila += '<td><a style="'+((valor.saldo<=0)?("color:#bbb;"):(""))+'" href="javascript:appCongeladosView(\''+(valor.codsocio)+'\',\''+(valor.numpres)+'\');">'+(valor.servicio)+'</a></td>';
        fila += '<td style="text-align:center;">'+(moment(valor.fec_otorg.date).format('DD/MM/YYYY'))+'</td>';
        fila += '<td style="text-align:center;">'+(valor.cuotas)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.importe,2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.saldo,2)+'</td>';
        fila += '<td colspan="2" title="error en solicitud de prestamo">'+(valor.error)+'</td>';
        fila += '</tr>';
      });
      $('#grdDatosBody').html(fila);
      $('#grdDatosCount').html(resp.tablaError.length+" errores");
    } else {
      $('#grdDatosBody').html('<tr><td colspan="10" style="text-align:center;color:#0000ff;">¡¡¡Todo Bien!!!</td></tr>');
    }
  });
}

function appBotonChangeUser(){
  arrPrest = $('[name="chk_Borrar[]"]:checked').map(function(){ return this.value; }).get();

  if((arrPrest.length)==0){
    alert("Debe elegir por lo menos UN SOCIO para cambiar de USUARIO");
  } else {
    let datos = { TipoQuery : 'Analistas_Todos' }
    appAjaxSelect(datos,rutaSQL).done(function(resp){
      appLlenarDataEnComboBox(resp,"#cboChangeUser",0);
      $('#titleChangeUser').html("Cambiar de Analista para estos <b>"+(arrPrest.length)+"</b> socios");
      $('#modalChangeUser').modal();
    });
  }
}

function appBotonCancel(){
  $("#edit").hide();
  $("#grid").show();
}

function appPrestamoView(codcuenta){
  let datos = {
    TipoQuery : "coopSUDprestamo",
    codcuenta : codcuenta
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    if(resp.socio.num_sol=="") {
      $("#lbl_solicitud").html('<button type="button" class="btn btn-danger btn-xs" onclick="javascript:appBotonArreglarSolic();">intentar arreglar</button>');
      $("#btn_tipocredito").hide();
      $("#btn_Promotor").hide();
      $("#btn_Analista").hide();
    } else {
      $("#lbl_solicitud").html('<a id="lbl_sol">'+(resp.socio.num_sol)+'</a>');
      $("#btn_tipocredito").show();
      $("#btn_Promotor").show();
      $("#btn_Analista").show();
    }

    $("#lbl_socio").html(resp.socio.txtsocio);
    $("#lbl_DNI").html(resp.socio.dni);
    $("#lbl_codigo").html(resp.socio.codsocio);
    $("#lbl_numpres").html(resp.socio.num_pres);
    $("#lbl_tiposerv").html(resp.socio.tipo_serv);
    $("#lbl_servicio").html(resp.socio.servicio);
    $("#lbl_fecha").html(moment(resp.socio.fecha.date).format('DD/MM/YYYY'));
    $("#lbl_cuotas").html(resp.socio.cuotas+" cuotas");
    $("#lbl_importe").html(appFormatMoney(resp.socio.importe,2));
    $("#lbl_saldo").html(appFormatMoney(resp.socio.saldo,2));
    appLlenarDataEnComboBox(resp.tipo_cred,"#cbo_TipoCred",resp.socio.tipo_cred);
    appLlenarDataEnComboBox(resp.usuarios,"#cbo_Promotor",resp.socio.respons);
    appLlenarDataEnComboBox(resp.usuarios,"#cbo_Analista",resp.socio.respons2);

    $("#grid").hide();
    $("#edit").show();
  });
}

function appBotonArreglarSolic(){
  let datos = {
    TipoQuery : "coopSUDarreglarSolic",
    codsocio : $("#lbl_codigo").html(),
    numpres  : $("#lbl_numpres").html(),
    tiposerv : $("#lbl_tiposerv").html(),
    importe : appConvertToNumero($("#lbl_importe").html())
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    if(resp.error==0){
      alert("se hizo el cambio con EXITO!!!!");
    } else {
      alert("!!---NO se pudo arreglar---!!");
    }
  });
}

function appBotonCambiarTipoCred(){
  let datos = {
    TipoQuery : "cambiarTipoCred",
    tipo_cred : $("#cbo_TipoCred").val(),
    codsocio : $("#lbl_codigo").html(),
    tipo_serv : $("#lbl_tiposerv").html(),
    num_pres : $("#lbl_numpres").html()
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    if(resp.error>0){
      alert("!!!No se pudo actualizar este registro porque hubo errores en la solicitud!!! === ("+(resp.error)+" solicitudes)");
    } else {
      alert("!!!Se actualizo bien!!!");
    }
  });
}

function appBotonCambiarPromotor(){
  let datos = {
    TipoQuery : "cambiarPromotor",
    promotor : $("#cbo_Promotor").val(),
    codsocio : $("#lbl_codigo").html(),
    tipo_serv : $("#lbl_tiposerv").html(),
    num_pres : $("#lbl_numpres").html()
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    if(resp.error>0){
      alert("!!!No se pudo actualizar este registro porque hubo errores en la solicitud!!! === ("+(resp.error)+" solicitudes)");
    } else {
      alert("!!!Se actualizo bien!!!");
      appGridAll();
      $("#grid").show();
      $("#edit").hide();
    }
  });
}

function appBotonCambiarAnalista(){
  let datos = {
    TipoQuery : "cambiarAnalista",
    analista : $("#cbo_Analista").val(),
    codsocio : $("#lbl_codigo").html(),
    tipo_serv : $("#lbl_tiposerv").html(),
    num_pres : $("#lbl_numpres").html()
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    if(resp.error>0){
      alert("!!!No se pudo actualizar este registro porque hubo errores en la solicitud!!! === ("+(resp.error)+" solicitudes)");
    } else {
      alert("!!!Se actualizo bien!!!");
      appGridAll();
      $("#grid").show();
      $("#edit").hide();
    }
  });
}

function modChangeUser_Cambiar(){
  if(confirm("¿Esta seguro de continuar?")){
    let datos = {
      TipoQuery : 'Analistas_Cambio',
      usuarioID : $("#cboChangeUser").val(),
      arrOBJ : arrPrest
    }
    appAjaxSelect(datos,rutaSQL).done(function(resp){
      console.log(resp);
      appGridAll();
      if(resp.tablaError.length>0){ alert("!!!tenemos "+(resp.tablaError.length)+" socios con errores!!!"); }
      $('#modalChangeUser').modal("hide");
    });
  }
}
