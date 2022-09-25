var rutaSQL = "pages/config/socios/sql.php";

//=========================funciones para workers============================
function appSociosGridAll(){
  let agenciaID = $("#cboAgencias").val();
  let txtBuscar = $("#txtBuscar").val();
  let datos = {
    TipoQuery : 'selSocios',
    agenciaID : agenciaID,
    miBuscar:txtBuscar.toUpperCase() };
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    if(resp.tabla.length>0){
      let workerData = "";
      let miColor = "";

      $.each(resp.tabla,function(key, valor){
        miColor = (valor.estado==0)?("color:#bfbfbf;"):(""); //fila deshabilitada

        workerData += '<tr>';
        workerData += '<td><input type="checkbox" id="chk_Borrar" name="chk_Borrar[]" value="'+(valor.ID)+'"/></td>';
        workerData += '<td><a style="'+miColor+'" href="javascript:appSociosExtrBancario('+(valor.ID)+');" title="Extracto Bancario"><i class="fa fa-file-text-o"></i></a></td>';
        workerData += '<td><a style="'+miColor+'" href="javascript:appSociosFormatos('+(valor.ID)+');" title="Formatos..."><i class="fa fa-files-o"></i></a></td>';
        workerData += '<td style="'+miColor+'">'+(valor.codigo)+'</td>';
        workerData += '<td style="'+miColor+'">'+(valor.DNI)+'</td>';
        workerData += '<td><a style="'+miColor+'" href="javascript:appSocioView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.socio)+'</a></td>';
        workerData += '<td style="'+miColor+'">'+(valor.agencia)+'</td>';
        workerData += '</tr>';
      });
      $('#grdDatosBody').html(workerData);
    }else{
      $('#grdDatosBody').html('<tr><td colspan="9" style="text-align:center;color:red;">Sin Resultados para '+txtBuscar+'</td></tr>');
    }
    $('#grdDatosCount').html(resp.tabla.length+"/"+resp.cuenta);
    $('#agenciaAbrev').val(resp.agenciaAbrev);
  });
}

function appSociosReset(){
  $("#txtBuscar").val("");
  let datos = {
    TipoQuery : 'ComboBox',
    miSubSelect : 'Agencias' }
  appAjaxSelect(datos).done(function(resp){
    $("#hid_agenciaText").val(resp.tabla[0].nombre);
    appLlenarComboAgencias("#cboAgencias",0,resp.tabla);
    appSociosGridAll();
  });
}

function appSociosBotonBuscar(e){
  var code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { appSociosGridAll(); }
}

function appSociosBotonDelete(){
  alert("en construccion");
  /*
  let arr = $('[name="chk_Borrar[]"]:checked').map(function(){ return this.value; }).get();
  let datos = {
    miDelete : 'OneWorker',
    miUserIDs : arr,
    fechaBaja : appConvertToFecha($("#date_fechabaja").val(),"")
  };

  $.ajax({
    url:'includes/sql_delete.php',
    type:'POST',
    dataType:'json',
    data:{"appDelete":JSON.stringify(datos)}
  })
  .done(function(resp){
    console.log(resp);
    if (resp.error == false) { //sin errores
      $('#modalDeleteWorker').modal('hide');
      appWorkerGetAll();
    }
  })
  .fail(function(resp){
    console.log("fail:.... "+resp.responseText);
  });
  */
}

function appSociosBotonCancel(){
    $('#grid').show();
    $('#edit').hide();
}

function appSociosBotonInsert(){
  let datosSocio = appSocioGetDatosToDatabase();
  datosSocio.commandSQL = "INS";
  if(datosSocio!=""){
    let datos = {
      TipoQuery : "execSocio",
      datosSocio : datosSocio }
    appAjaxInsert(datos,rutaSQL).done(function(resp){
      appSociosGridAll();
      appSociosBotonCancel();
    });
  }
}

function appSociosBotonUpdate(){
  let datosSocio = appSocioGetDatosToDatabase();
  datosSocio.commandSQL = "UPD";
  if(datosSocio!=""){
    let datos = {
      TipoQuery : "execSocio",
      datosSocio : datosSocio }
    appAjaxUpdate(datos,rutaSQL).done(function(resp){
      appSociosGridAll();
      appSociosBotonCancel();
    });
  }
}

function appSociosExtrBancario(socioID){
  let datos = {
    TipoQuery : 'ExtrBancario',
    personaID : socioID,
    fullQuery : 0 }
  appAjaxSelect(datos,"includes/sql_select.php").done(function(resp){
    $("#lbl_ExtrID").html(resp.ID);
    $("#lbl_ExtrCodigo").html(resp.codigo);
    $("#lbl_ExtrNombres").html(resp.nombres);
    $("#lbl_ExtrApellidos").html(resp.apellidos);
    $("#lbl_ExtrTipoDNI").html(resp.tipoDNI);
    $("#lbl_ExtrNroDNI").html(resp.nroDNI);
    $("#lbl_ExtrAgencia").html(resp.agencia);

    let cbodatos = {
      TipoQuery : "ComboBox",
      miSubSelect : "ExtrOperaciones",
      miSocioID : socioID,
    }
    appAjaxSelect(cbodatos,"includes/sql_select.php").done(function(rpta){
      let miData = "";
      $.each(rpta,function(key, valor){ miData += '<option value="'+(valor.ID)+'">'+(valor.nombre)+'</option>'; });
      $("#cbo_ExtrOperaciones").html(miData);
      appComboExtrBancario("#cbo_ExtrProducto","ExtrProducto",socioID,$("#cbo_ExtrOperaciones").val(),0);
    });

    $('#grdExtrBancarioBody').html("");

    $('#grid').hide();
    $('#formatos').hide();
    $('#ExtrBancario').show();
  });
}

function appSociosExtrBancReporte(){
  let datos = {
    TipoQuery: 'ExtrBancario',
    socioID: $("#lbl_ExtrID").html(),
    operacionID: $('#cbo_ExtrOperaciones').val(),
    productoID: $('#cbo_ExtrProducto').val(),
    prestahorroID: 0
  };

  appAjaxSelect(datos).done(function(resp){
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
      rptData += '<td style="text-align:right;" colspan="6"><span style="font-weight:bold;font-size:14px;color:blue;">Saldo Final</span></td>';
      rptData += '<td style="text-align:right;"><span style="font-weight:bold;font-size:14px;color:blue;">'+appFormatMoney(totDepo-totReti,2)+'</span></td>';
      rptData += '<td style="text-align:right;" colspan="2"></td>';
      rptData += '</tr>';
      $('#grdExtrBancarioBody').html(rptData);
    }else{
      $('#grdExtrBancarioBody').html('<tr><td colspan="8" style="text-align:center;color:red;">****** Sin Resultados ******</td></tr>');
    }
  });
}

function appSociosFormatos(socioID){
  let datos = {
    TipoQuery : 'OneSocio',
    personaID : socioID,
    fullQuery : 0
  }
  appAjaxSelect(datos).done(function(resp){
    $("#lbl_FormNombres").html(resp.nombres);
    $("#lbl_FormApellidos").html(resp.apellidos);
    $("#lbl_FormTipoDNI").html(resp.tipoDNI);
    $("#lbl_FormNroDNI").html(resp.nroDNI);
    $("#lbl_FormFechaNac").html(resp.fechanac);
    $("#lbl_FormLugarNac").html(resp.lugarnac);
    $("#lbl_FormSexo").html(resp.sexo);
    $("#lbl_FormEcivil").html(resp.ecivil);

    $('#objPDF').prop("data","");

    $('#grid').hide();
    $('#formatos').show();
  });
}

function appSocioView(socioID){
  let datos = {
    TipoQuery : 'editSocio',
    personaID : socioID,
    fullQuery : 1 }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    appSocioSetData(resp.tablaSoc);
    appPersonaSetData(resp.tablaPers); //pestaña de datos personales
    appLaboralSetData(resp.tablaLabo); //pestaña laborales
    appConyugeSetData(resp.tablaCony); //pestaña de Conyuge

    //siempre primer tab por default
    $('.nav-tabs li').removeClass('active');
    $('.tab-content .tab-pane').removeClass('active');
    $('a[href="#datosSocio"]').closest('li').addClass('active');
    $('#datosSocio').addClass('active');

    $("#btnInsert").hide();
    $("#btnUpdate").show();
    $('#grid').hide();
    $('#edit').show();
  });
}

function appSocioClear(){
  //tab socio
  appComboBox("#cbo_SocAgencia","Agencias",0);//agencia
  $("#txt_SocCodigo").val("");
  $("#txt_SocCodigo").removeAttr('disabled');
  $('#txt_SocFechaIng').datepicker("setDate",moment().format("DD/MM/YYYY"));
  $("#txt_SocFechaIng").removeAttr('disabled');
  $("#cbo_SocAgencia").removeAttr('disabled');
  $("#txt_SocGasNrodep").val("0");
  $("#txt_SocGasAlim").val("0.00");
  $("#txt_SocGasEduc").val("0.00");
  $("#txt_SocGasTrans").val("0.00");
  $("#txt_SocGasAlqui").val("0.00");
  $("#txt_SocGasFono").val("0.00");
  $("#txt_SocGasAgua").val("0.00");
  $("#txt_SocGasLuz").val("0.00");
  $("#txt_SocGasOtros").val("0.00");
  $("#txt_SocObserv").val("");

  //tabs default en primer tab
  $('.nav-tabs li').removeClass('active');
  $('.tab-content .tab-pane').removeClass('active');
  $('a[href="#datosSocio"]').closest('li').addClass('active');
  $('#datosSocio').addClass('active');

  //todos los inputs sin error y panel error deshabilitado
  $('.box-body .form-group').removeClass('has-error');

  //botones
  $('#btnInsert').show();
  $('#btnUpdate').hide();
}

function appSocioNuevo(){
  Persona.openBuscar('VerifySocios',1,0);
  $('#btn_modPersInsert').on('click',function(e) {
    if(Persona.sinErrores()){ //sin errores
      Persona.ejecutaSQL().done(function(resp){
        let data = JSON.parse(resp);
        appPersonaSetData(data.tablaPers);
        appSocioClear();
        appLaboralClear();
        appConyugeClear();
        $('#grid').hide();
        $('#edit').show();
        Persona.close();
      });
    } else {
      alert("!!!Faltan llenar Datos!!!");
    }
    e.stopImmediatePropagation();
    $('#btn_modPersInsert').off('click');
  });
  $('#btn_modPersAddToForm').on('click',function(e) {
    let datos = {
      TipoQuery : 'editPersona',
      personaID : Persona.tablaPers.ID,
      fullQuery : 2
    }
    appAjaxSelect(datos,"pages/config/personas/sql.php").done(function(resp){
      appPersonaSetData(Persona.tablaPers); //pestaña Personales
      appLaboralSetData(resp.tablaLabo); //pestaña laborales
      appConyugeSetData(resp.tablaCony); //pestaña de Conyuge
      appSocioClear();
      $('#grid').hide();
      $('#edit').show();
      Persona.close();
    });
    e.stopImmediatePropagation();
    $('#btn_modPersAddToForm').off('click');
  });
}

function appSocioSetData(data){
  //info corta
  $("#lbl_agencia").html(data.agencia);
  //pestaña de socio
  appComboBox("#cbo_SocAgencia","Agencias",data.id_agencia);//agencia
  $("#cbo_SocAgencia").attr("disabled","disabled");
  $("#txt_SocCodigo").val(data.codigo);
  $("#txt_SocCodigo").attr("disabled","disabled");
  $('#txt_SocFechaIng').datepicker("setDate",data.fecha_ing);
  $("#txt_SocFechaIng").attr("disabled","disabled");
  $("#txt_SocGasNrodep").val(data.g_nrodep);
  $("#txt_SocGasAlim").val(appFormatMoney(data.g_alim,2));
  $("#txt_SocGasEduc").val(appFormatMoney(data.g_educ,2));
  $("#txt_SocGasTrans").val(appFormatMoney(data.g_trans,2));
  $("#txt_SocGasAlqui").val(appFormatMoney(data.g_alqui,2));
  $("#txt_SocGasFono").val(appFormatMoney(data.g_fono,2));
  $("#txt_SocGasAgua").val(appFormatMoney(data.g_agua,2));
  $("#txt_SocGasLuz").val(appFormatMoney(data.g_luz,2));
  $("#txt_SocGasOtros").val(appFormatMoney(data.g_otros,2));
  $("#txt_SocGasPrest").val(appFormatMoney(data.g_prest,2));
  $("#txt_SocObserv").val(data.observac);
}

function appSocioGetDatosToDatabase(){
  let EsError = false;
  let datos = "";
  $('.box-body .form-group').removeClass('has-error');
  if(isNaN($("#txt_SocGasNrodep").val())) { $("#div_SocGasNrodep").prop("class","form-group has-error"); EsError = true; }
  if(isNaN($("#txt_SocGasAlim").val())) { $("#div_SocGasAlim").prop("class","form-group has-error"); EsError = true; }
  if(isNaN($("#txt_SocGasEduc").val())) { $("#div_SocGasEduc").prop("class","form-group has-error"); EsError = true; }
  if(isNaN($("#txt_SocGasTrans").val())) { $("#div_SocGasTrans").prop("class","form-group has-error"); EsError = true; }
  if(isNaN($("#txt_SocGasAlqui").val())) { $("#div_SocGasAlqui").prop("class","form-group has-error"); EsError = true; }
  if(isNaN($("#txt_SocGasFono").val())) { $("#div_SocGasFono").prop("class","form-group has-error"); EsError = true; }
  if(isNaN($("#txt_SocGasAgua").val())) { $("#div_SocGasAgua").prop("class","form-group has-error"); EsError = true; }
  if(isNaN($("#txt_SocGasLuz").val())) { $("#div_SocGasLuz").prop("class","form-group has-error"); EsError = true; }
  if(isNaN($("#txt_SocGasOtros").val())) { $("#div_SocGasOtros").prop("class","form-group has-error"); EsError = true; }
  if(isNaN($("#txt_SocGasPrest").val())) { $("#div_SocGasPrest").prop("class","form-group has-error"); EsError = true; }

  if(!EsError){
    datos = {
      personaID : $("#lbl_ID").html(),
      socCodigo : $("#txt_SocCodigo").val(),
      socFechaIng : appConvertToFecha($("#txt_SocFechaIng").val(),""),
      socAgenciaID : $("#cbo_SocAgencia").val(),
      socG_nrodep : $("#txt_SocGasNrodep").val(),
      socG_alim : $("#txt_SocGasAlim").val(),
      socG_educ : $("#txt_SocGasEduc").val(),
      socG_trans : $("#txt_SocGasTrans").val(),
      socG_alqui : $("#txt_SocGasAlqui").val(),
      socG_fono : $("#txt_SocGasFono").val(),
      socG_agua : $("#txt_SocGasAgua").val(),
      socG_luz : $("#txt_SocGasLuz").val(),
      socG_otros : $("#txt_SocGasOtros").val(),
      socG_prest : $("#txt_SocGasPrest").val(),
      socObservac : $("#txt_SocObserv").val()
    }
  }
  return datos;
}

function appPersonaSetData(data){
  //info corta
  $('#img_Foto').prop("src",(data.urlfoto=="") ? ("data/personas/images/0noFotoUser.jpg") : (data.urlfoto));
  $("#lbl_Nombres").html(data.nombres);
  $("#lbl_Apellidos").html(data.ap_paterno+" "+data.ap_materno);
  $("#lbl_ID").html(data.ID);
  $("#lbl_TipoDNI").html(data.tipoDNI);
  $("#lbl_DNI").html(data.nroDNI);
  $("#lbl_Celular").html(data.celular);

  //pestaña datos personales
  if(data.tipoPersona==2){ //persona juridica
    $("#lbl_PersTipoNombres").html("Razon Social");
    $("#lbl_PersTipoProfesion").html("Rubro");
    $("#lbl_PersTipoApellidos").hide();
    $("#lbl_PersTipoSexo").hide();
    $("#lbl_PersTipoECivil").hide();
    $("#lbl_PersTipoGIntruc").hide();
  }else{
    $("#lbl_PersTipoNombres").html("Nombres");
    $("#lbl_PersTipoProfesion").html("Profesion");
    $("#lbl_PersTipoApellidos").show();
    $("#lbl_PersTipoSexo").show();
    $("#lbl_PersTipoECivil").show();
    $("#lbl_PersTipoGIntruc").show();
  }
  $("#lbl_PersNombres").html(data.nombres);
  $("#lbl_PersApellidos").html(data.ap_paterno+" "+data.ap_materno);
  $("#lbl_PersTipoDNI").html(data.tipoDNI);
  $("#lbl_PersNroDNI").html(data.nroDNI);
  $("#lbl_PersFechaNac").html(data.fechanac);
  $("#lbl_PersLugarNac").html(data.lugarnac);
  $("#lbl_PersSexo").html(data.sexo);
  $("#lbl_PersEcivil").html(data.ecivil);
  $("#lbl_PersCelular").html(data.celular);
  $("#lbl_PersTelefijo").html(data.fijo);
  $("#lbl_PersEmail").html(data.correo);
  $("#lbl_PersTipoVivienda").html(data.tipovivienda);
  $("#lbl_PersGInstruccion").html(data.ginstruc);
  $("#lbl_PersProfesion").html(data.profesion);
  $("#lbl_PersOcupacion").html(data.ocupacion);
  $("#lbl_PersUbicacion").html(data.region+" - "+data.provincia+" - "+data.distrito);
  $("#lbl_PersDireccion").html(data.direccion);
  $("#lbl_PersReferencia").html(data.referencia);
  $("#lbl_PersMedidorluz").html(data.medidorluz);
  $("#lbl_PersTipovivienda").html(data.tipovivienda);
  $("#lbl_PersObservac").html(data.observPers);
  $("#lbl_PersSysFecha").html(data.sysfechaPers);
  $("#lbl_PersSysUser").html(data.sysuserPers);

  //permisos
  if(data.tablaUser.usernivel==data.tablaUser.admin) {
    $("#btn_PersUpdate").show();
    $("#btn_PersPermiso").hide();
  } else {
    switch(data.permisoPersona.estado){
      case 0: $("#btn_PersPermiso").show(); $("#btn_PersUpdate").hide(); break; //sin permisos
      case 1: $("#btn_PersPermiso").hide(); $("#btn_PersUpdate").hide(); break; //pendiente de confirmar
      case 2: $("#btn_PersPermiso").hide(); $("#btn_PersUpdate").show(); break; //permiso concedido
    }
  }
}

function appPersonaEditar(){
  Persona.editar($('#lbl_ID').html(),'S');
  $('#btn_modPersUpdate').on('click',function(e) {
    if(Persona.sinErrores()){ //sin errores
      Persona.ejecutaSQL().done(function(resp){
        let data = JSON.parse(resp);
        appPersonaSetData(data.tablaPers);
        Persona.close();
      });
    } else {
      alert("!!!Faltan llenar Datos!!!");
    }
    e.stopImmediatePropagation();
    $('#btn_modPersUpdate').off('click');
  });
}

function appLaboralSetData(data){
  if(data.id_persona>0){
    $('#hid_LaboPermisoID').val(data.permisoLaboral.ID);
    $("#lbl_LaboCondicion").html((data.condicion==0)?("Independiente"):("Dependiente"));
    $("#lbl_LaboEmprRazon").html(data.empresa);
    $("#lbl_LaboEmprRUC").html(data.ruc);
    $("#lbl_LaboEmprFono").html(data.telefono);
    $("#lbl_LaboEmprRubro").html(data.rubro);
    $('#lbl_LaboEmprFechaIng').html(data.fechaIni);
    $("#lbl_LaboEmprCargo").html(data.cargo);
    $("#lbl_LaboEmprIngreso").html(appFormatMoney(data.ingreso,2));

    $("#lbl_LaboEmprDireccion").html(data.direccion);
    $("#lbl_LaboEmprUbicacion").html(data.region+" - "+data.provincia+" - "+data.distrito);
    $("#lbl_LaboEmprObservac").html(data.observLabo);
    $("#lbl_LaboSysFecha").html(data.sysfechaLabo);
    $("#lbl_LaboSysUser").html(data.sysuserLabo);
    $("#div_Laboral").show();

    //permisos
    $("#btn_LaboInsert").hide();
    if(data.tablaUser.usernivel==data.tablaUser.admin) {
      $("#btn_LaboDelete").show();
      $("#btn_LaboUpdate").show();
      $("#btn_LaboInsert").hide();
      $("#btn_LaboPermiso").hide();
    } else {
      switch(data.permisoLaboral.estado) {
        case 0: $("#btn_LaboPermiso").show(); $("#btn_LaboUpdate").hide(); break;
        case 1: $("#btn_LaboPermiso").hide(); $("#btn_LaboUpdate").hide(); break;
        case 2: $("#btn_LaboPermiso").hide(); $("#btn_LaboUpdate").show(); break;
      }
    }
  } else {
    appLaboralClear();
  }
}

function appLaboralClear(){
  $("#div_Laboral").hide();
  $("#btn_LaboDelete").hide();
  $("#btn_LaboUpdate").hide();
  $("#btn_LaboPermiso").hide();
  $("#btn_LaboInsert").show();
}

function appLaboralNuevo(){
  Laboral.nuevo($('#lbl_ID').html());
  $('#btn_modLaboInsert').on('click',function(e) {
    if(Laboral.sinErrores()){ //guardamos datos de persona
      Laboral.ejecutaSQL().done(function(resp){
        let data = JSON.parse(resp);
        appLaboralSetData(data.tablaLabo);
        Laboral.close();
      });
    } else {
      alert("!!!Faltan llenar Datos!!!");
    }
    e.stopImmediatePropagation();
    $('#btn_modLaboInsert').off('click');
  });
}

function appLaboralEditar(){
  Laboral.editar($('#lbl_ID').html());
  $('#btn_modLaboUpdate').on('click',function(e) {
    if(Laboral.sinErrores()){ //guardamos datos de persona
      Laboral.ejecutaSQL().done(function(resp){
        let data = JSON.parse(resp);
        appLaboralSetData(data.tablaLabo);
        Laboral.close();
      });
    } else {
      alert("!!!Faltan llenar Datos!!!");
    }
    e.stopImmediatePropagation();
    $('#btn_modLaboUpdate').off('click');
  });
}

function appLaboralDelete(){
  if(confirm("¿Realmente desea eliminar los datos laborales?")) {
    Laboral.borrar($('#lbl_ID').html()).done(function(resp){
      let data = JSON.parse(resp);
      if(!data.error){
        appLaboralClear();
        Laboral.close();
      } else {
        alert("!!!Hubo un error al momento de eliminar estos datos!!!");
      }
    });
  }
}

function appConyugeSetData(data){
  if(data.id_conyuge>0){
    let conyuge = data.persona;
    $("#hid_ConyID").val(conyuge.ID);
    $("#lbl_ConyNombres").html(conyuge.nombres);
    $("#lbl_ConyApellidos").html(conyuge.ap_paterno+' '+conyuge.ap_materno);
    $("#lbl_ConyTipoDNI").html(conyuge.tipoDNI);
    $("#lbl_ConyNroDNI").html(conyuge.nroDNI);
    $("#lbl_ConyFechaNac").html(conyuge.fechanac);
    $("#lbl_ConyEcivil").html(conyuge.ecivil);
    $("#lbl_ConyCelular").html(conyuge.celular);
    $("#lbl_ConyTelefijo").html(conyuge.fijo);
    $("#lbl_ConyEmail").html(conyuge.correo);
    $("#lbl_ConyGInstruccion").html(conyuge.ginstruc);
    $("#lbl_ConyProfesion").html(conyuge.profesion);
    $("#lbl_ConyOcupacion").html(conyuge.ocupacion);
    $("#lbl_ConyUbicacion").html(conyuge.region+" - "+conyuge.provincia+" - "+conyuge.distrito);
    $("#lbl_ConyDireccion").html(conyuge.direccion);
    $("#lbl_ConyReferencia").html(conyuge.referencia);
    $("#lbl_ConyMedidorluz").html(conyuge.medidorluz);
    $("#lbl_ConyTipovivienda").html(conyuge.tipovivienda);
    $('.box-body .form-cony .form-group').removeClass('has-error');
    $("#div_Conyuge").show();

    let laboral = data.laboral;
    if(laboral.id_persona>0){
      $("#lbl_ConyEmprCondicion").html((laboral.condicion==1)?("Dependiente"):("Independiente"));
      $("#lbl_ConyEmprRazonSocial").html(laboral.empresa);
      $("#lbl_ConyEmprRUC").html(laboral.ruc);
      $("#lbl_ConyEmprRubro").html(laboral.rubro);
      $("#lbl_ConyEmprFono").html(laboral.telefono);
      $("#lbl_ConyEmprFechaIng").html(laboral.fechaIni);
      $("#lbl_ConyEmprCargo").html(laboral.cargo);
      $("#lbl_ConyEmprIngreso").html(appFormatMoney(laboral.ingreso,2));
      $("#lbl_ConyEmprDireccion").html(laboral.direccion);
      $("#lbl_ConyEmprUbicacion").html(laboral.region+" - "+laboral.provincia+" - "+laboral.distrito);
      $("#lbl_ConyEmprObservac").html(laboral.observac);
      $("#div_ConyLaboral").show();
    } else {
      $("#div_ConyLaboral").hide();
    }

    //permisos
    $("#lbl_ConyTiempoRel").html(data.tiempoRelacion);
    $('#hid_ConyPermisoID').val(data.permisoConyuge.ID);
    $("#btn_ConyInsert").hide();
    if(data.tablaUser.usernivel==data.tablaUser.admin) {
      $("#btn_ConyDelete").show();
      $("#btn_ConyUpdate").show();
      $("#btn_ConyPermiso").hide();
    } else {
      switch(data.permisoConyuge.estado) {
        case 0: $("#btn_ConyPermiso").show(); $("#btn_ConyDelete").hide(); break;
        case 1: $("#btn_ConyPermiso").hide(); $("#btn_ConyDelete").hide(); break;
        case 2: $("#btn_ConyPermiso").hide(); $("#btn_ConyDelete").show(); break;
      }
    }
  } else {
    appConyugeClear();
  }
}

function appConyugeClear(){
  $("#hid_ConyID").val("0");
  $("#txt_ConyTiempoRel").val("0");
  $('.box-body .form-cony .form-group').removeClass('has-error');

  $("#div_Conyuge").hide();
  $("#btn_ConyDelete").hide();
  $("#btn_ConyUpdate").hide();
  $("#btn_ConyPermiso").hide();
  $("#btn_ConyInsert").show();
}

function appConyugeNuevo(){
  Conyuge.nuevo($('#lbl_ID').html());
  $('#btn_modConyInsert').on('click',function(e) {
    if(Conyuge.sinErrores()){ //guardamos datos de persona
      Conyuge.ejecutaSQL().done(function(resp){
        let data = JSON.parse(resp);
        appConyugeSetData(data.tablaCony);

        Conyuge.close();
      });
    } else {
      alert("!!!Faltan llenar Datos!!!");
    }
    e.stopImmediatePropagation();
    $('#btn_modConyInsert').off('click');
  });
}

function appConyugeEditar(){
  Conyuge.editar($('#lbl_ID').html());
  $('#btn_modConyUpdate').on('click',function(e) {
    if(Conyuge.sinErrores()){ //guardamos datos de persona
      Conyuge.ejecutaSQL().done(function(resp){
        let data = JSON.parse(resp);
        appConyugeSetData(data.tablaCony);
        Conyuge.close();
      });
    } else {
      alert("!!!Faltan llenar Datos!!!");
    }
    e.stopImmediatePropagation();
    $('#btn_modConyUpdate').off('click');
  });
}

function appConyugeDelete(){
  if(confirm("¿Realmente desea eliminar los datos de Conyuge?")) {
    Conyuge.borrar($('#lbl_ID').html()).done(function(resp){
      let data = JSON.parse(resp);
      if(!data.error){
        appConyugeClear();
        Conyuge.close();
      } else {
        alert("!!!Hubo un error al momento de eliminar estos datos!!!");
      }
    });
  }
}

function appFormatosGenerarPDF(tipo){
  $("#contenedorFrame").show();
  switch(tipo){
    case "ahorrosContrato": $('#objPDF').prop("data",appUrlServer()+"includes/pdf/plantilla/rpt.ahorros.contrato.php?nroDNI="+$("#lbl_FormNroDNI").html()); break;
    case "ahorrosAnexo01": $('#objPDF').prop("data",appUrlServer()+"includes/pdf/plantilla/rpt.ahorros.anexo01.php?nroDNI="+$("#lbl_FormNroDNI").html()); break;
    case "creditosResumen": $('#objPDF').prop("data",appUrlServer()+"includes/pdf/plantilla/rpt.creditos.resumen.php?nroDNI="+$("#lbl_FormNroDNI").html()); break;
    case "cartaGarLiqui": $('#objPDF').prop("data",appUrlServer()+"includes/pdf/plantilla/rpt.carta.garantia.liquida.php?nroDNI="+$("#lbl_FormNroDNI").html()); break;
  }
}

function appFormatosBotonCancel(){
  $('#grid').show();
  $('#formatos').hide();
  $('#ExtrBancario').hide();
}

function appPermisoPersonas(){
  let datos = { TipoQuery:'OneNotificacion', tabla:'tb_personas', personaID:$("#lbl_ID").html() }
  appAjaxInsert(datos).done(function(resp){
    if(resp.error==false){ $("#btn_PersPermiso").hide(); }
    else { alert("!!!Hubo un error... "+(resp.mensaje)+"!!!"); }
  });
}

function appPermisoLaboral(){
  let datos = { TipoQuery:'OneNotificacion', tabla:'tb_personas_labo', personaID:$("#lbl_ID").html() }
  appAjaxInsert(datos).done(function(resp){
    if(resp.error==false){ $("#btn_LaboPermiso").hide(); }
    else { alert("!!!Hubo un error... "+(resp.mensaje)+"!!!"); }
  });
}

function appPermisoConyuge(){
  let datos = { TipoQuery:'OneNotificacion', tabla:'tb_personas_cony', personaID:$("#lbl_ID").html() }
  appAjaxInsert(datos).done(function(resp){
    if(resp.error==false){ $("#btn_ConyPermiso").hide(); }
    else { alert("!!!Hubo un error... "+(resp.mensaje)+"!!!"); }
  });
}
