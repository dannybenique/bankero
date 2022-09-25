var rutaSQL = "pages/config/personas/sql.php";

//=========================funciones para Personas============================
function appPersonasGrid(){
  let txtBuscar = $("#txtBuscar").val();
  let datos = { TipoQuery:'selPersonas', buscar:txtBuscar }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    let disabledDelete = (resp.usernivel==resp.admin) ? ("") : ("disabled");

    if(disabledDelete.length>0) { $('#chk_All').attr("disabled",disabledDelete);}
    if(resp.tabla.length>0){
      let appData = "";
      $.each(resp.tabla,function(key, valor){
        appData += '<tr>';
        appData += '<td><input type="checkbox" id="chk_Borrar" name="chk_Borrar[]" value="'+(valor.ID)+'" '+disabledDelete+'/></td>';
        appData += '<td><a href="javascript:appPersonasBotonAuditoria('+(valor.ID)+');"><i class="fa fa-eye" title="Auditoria"></i></a></td>';
        appData += '<td><a href="javascript:appPersonasBotonFormatos('+(valor.ID)+');"><i class="fa fa-files-o" title="Formatos..."></i></a></td>';
        appData += '<td>'+(valor.DNI)+'</td>';
        appData += '<td>'+((valor.url=="") ? (''):('<i class="fa fa-paperclip"></i>'))+'</td>';
        appData += '<td><a href="javascript:appPersonasView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.persona)+'</a></td>';
        appData += '<td>'+(valor.direccion)+'</td>';
        appData += '</tr>';
      });
      $('#grdDatosBody').html(appData);
    }else{
      $('#grdDatosBody').html('<tr><td colspan="7" style="text-align:center;color:red;">Sin Resultados para '+txtBuscar+'</td></tr>');
    }
    $('#grdDatosCount').html(resp.tabla.length+"/"+resp.cuenta);
  });
}

function appPersonasReset(){
  $("#txtBuscar").val("");
  appPersonasGrid();
}

function appPersonasBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { appPersonasGrid(); }
}

function appPersonaNuevo(){
  Persona.openBuscar('VerifyPersona',1,0);
  $('#btn_modPersInsert').on('click',function(e) {
    if(Persona.sinErrores()){ //sin errores
      Persona.ejecutaSQL().done(function(resp){
        let data = JSON.parse(resp);
        appPersonaSetData(data.tablaPers);
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
}

function appPersonaEditar(){
  Persona.editar($('#lbl_ID').html(),'P');
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

function appPersonasView(personaID){
  let datos = {
    TipoQuery : 'editPersona',
    personaID : personaID,
    fullQuery : 2
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    appPersonaSetData(resp.tablaPers); //pestaña Personales
    appLaboralSetData(resp.tablaLabo); //pestaña laborales
    appConyugeSetData(resp.tablaCony); //pestaña de Conyuge

    //tabs default en primer tab
    $('.nav-tabs li').removeClass('active');
    $('.tab-content .tab-pane').removeClass('active');
    $('a[href="#datosPersonal"]').closest('li').addClass('active');
    $('#datosPersonal').addClass('active');

    $('#grid').hide();
    $('#edit').show();
  });
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

function appPersonasBotonCancel(){
  appPersonasGrid();
  $('#grid').show();
  $('#edit').hide();
}

function appPersonasBotonFormatos(personaID){
  let datos = {
    TipoQuery : 'editPersona',
    personaID : personaID,
    fullQuery : 0
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    appComboBox("#cbo_ciudades","CiudadesAg",0);
    if(resp.tipoPersona==2){ //persona juridica
      $("#lbl_FormTipoNombres").html("Razon Social");
      $("#lbl_FormTipoApellidos").hide();
      $("#lbl_FormTipoSexo").hide();
      $("#lbl_FormTipoECivil").hide();
    }else{
      $("#lbl_FormTipoNombres").html("Nombres");
      $("#lbl_FormTipoApellidos").show();
      $("#lbl_FormTipoSexo").show();
      $("#lbl_FormTipoECivil").show();
    }
    $("#hid_FormPersonaID").val(personaID);
    $("#lbl_FormNombres").html(resp.nombres);
    $("#lbl_FormApellidos").html(resp.apellidos);
    $("#lbl_FormTipoDNI").html(resp.tipoDNI);
    $("#lbl_FormNroDNI").html(resp.nroDNI);
    $("#lbl_FormFechaNac").html(resp.fechanac);
    $("#lbl_FormLugarNac").html(resp.lugarnac);
    $("#lbl_FormSexo").html(resp.sexo);
    $("#lbl_FormECivil").html(resp.ecivil);

    $("#contenedorFrame").hide();
    $("#objPDF").prop("data","");

    $('#grid').hide();
    $('#formatos').show();
  });
}

function appPersonasBotonAuditoria(personaID){
  let datos = {
    TipoQuery: 'audiPersona',
    personaID: personaID
  }

  appAjaxSelect(datos,rutaSQL).done(function(resp){
    $("#lbl_modAuditoriaTitulo").html((resp.tablaPers.tipoPersona==2) ? (resp.tablaPers.nombres) : (resp.tablaPers.persona));
    if(resp.tablaLog.length>0){
      let rptData = "";
      $.each(resp.tablaLog,function(key, valor){
        rptData += '<tr>';
        rptData += '<td>'+(valor.codigo)+'</td>';
        rptData += '<td>'+(valor.tabla)+'</td>';
        rptData += '<td>'+(valor.accion)+'</td>';
        rptData += '<td>'+(valor.campo)+'</td>';
        rptData += '<td>'+(valor.observac)+'</td>';
        rptData += '<td>'+(valor.usuario)+'</td>';
        rptData += '<td>'+(valor.sysIP)+'</td>';
        rptData += '<td style="text-align:center;">'+(valor.sysagencia)+'</td>';
        rptData += '<td style="text-align:right;">'+(valor.sysfecha)+'</td>';
        rptData += '<td style="text-align:right;">'+(valor.syshora)+'</td>';
        rptData += '</tr>';
      });
      $('#grdAuditoriaBody').html(rptData);
    }else{
      $('#grdAuditoriaBody').html('<tr><td colspan="10" style="text-align:center;color:red;">****** Sin Resultados ******</td></tr>');
    }
    $('#modalAuditoria').modal();
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
    if(Laboral.sinErrores()){
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
  let urlServer = $("#hid_FormUrlServer").val();
  $("#contenedorFrame").show();
  switch(tipo){
    case "SolicitudIngrSocio": $("#objPDF").prop("data",urlServer+"/includes/pdf/plantilla/rpt.solicIngrSoc.php?nroDNI="+$("#lbl_FormNroDNI").html()+"&ciudad="+$("#cbo_ciudades").val()); break;
    case "FichaInscripcion": $("#objPDF").prop("data",urlServer+"/includes/pdf/plantilla/rpt.fichaInscrip.php?personaID="+$("#hid_FormPersonaID").val()); break;
    case "DeclJuraConviv": $("#objPDF").prop("data",urlServer+"/includes/pdf/plantilla/rpt.declaracion.jurada.convivencia.php?personaID="+$("#hid_FormPersonaID").val()); break;
  }
}

function appFormatosCancel(){
  $('#grid').show();
  $("#objPDF").prop("data","");
  $("#contenedorFrame").hide();
  $('#formatos').hide();
}

function appPermisoPersonas(){
  let datos = { TipoQuery:'insNotifi', tabla:'tb_personas', personaID:$("#lbl_ID").html() }
  appAjaxInsert(datos,"pages/global/notifi/sql.php").done(function(resp){
    if(!resp.error){ $("#btn_PersPermiso").hide(); }
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
