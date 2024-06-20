const rutaSQL = "pages/mtto/socios/sql.php";
var menu = "";

function appSociosBuscar(e){ if(e.keyCode === 13) { appSociosGrid(); } }

async function appSociosGrid(){
  $('#grdDatos').html('<tr><td colspan="8"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  $("#chk_All").prop("disabled", !(menu.mtto.submenu.socios.cmdDelete === 1));
  const disabledDelete = (menu.mtto.submenu.socios.cmdDelete===1) ? "" : "disabled";
  const txtBuscar = $("#txtBuscar").val().toUpperCase();
  try{
    const resp = await appAsynFetch({ TipoQuery:'selSocios', buscar:txtBuscar },rutaSQL);
    if(resp.tabla.length>0){
      let fila = "";
      resp.tabla.forEach((valor,key)=>{
        fila += '<tr>'+
                '<td><input type="checkbox" name="chk_Borrar" value="'+(valor.ID)+'" '+disabledDelete+'/></td>'+
                '<td>'+(valor.codigo)+'</td>'+
                '<td>'+(moment(valor.fecha).format("DD/MM/YYYY"))+'</td>'+
                '<td>'+(valor.nro_dui)+'</td>'+
                '<td><a href="javascript:appSocioView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.socio)+'</a></td>'+
                '<td>'+(valor.direccion)+'</td>'+
                '</tr>';
      });
      $('#grdDatos').html(fila);
    } else {
      let res = (txtBuscar==="") ? ("") : ("para "+txtBuscar);
      $('#grdDatos').html('<tr><td colspan="8" style="text-align:center;color:red;">Sin Resultados '+(res)+'</td></tr>');
    }
    $('#grdCount').html(resp.tabla.length+"/"+resp.cuenta);
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appSociosReset(){
  $("#txtBuscar").val("");
  $("#grdDatos").html("");
  try{
    const resp = await appAsynFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php");
    
    menu = JSON.parse(resp.menu);
    $("#btn_DEL").toggle(menu.mtto.submenu.socios.cmdDelete == 1);
    $("#btn_NEW").toggle(menu.mtto.submenu.socios.cmdInsert == 1);
    $("#div_PersAuditoria").toggle(resp.rolID == 101);
    appSociosGrid();
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appSociosBotonCancel(){
  appSociosGrid();
  $("#grid").show();
  $("#edit").hide();
}

async function appSociosBotonInsert(){
  let datos = appSocioGetDatosToDatabase();
  
  if(datos!=""){
    datos.TipoQuery = "insSocio";
    try{
      const resp = await appAsynFetch(datos,rutaSQL);
      if(!resp.error){ appSociosBotonCancel(); }
    } catch(err){
      console.error('Error al cargar datos:', err);
    }
  }
}

async function appSociosBotonUpdate(){
  let datos = appSocioGetDatosToDatabase();

  if(datos!=""){
    datos.TipoQuery = "updSocio";
    try{
      const resp = await appAsynFetch(datos,rutaSQL);
      if(!resp.error){ appSociosBotonCancel(); }
    } catch(err){
      console.error('Error al cargar datos:', err);
    }
  }
}

function appSocioBotonNuevo(){
  Persona.openBuscar('VerifySocio',rutaSQL,true,true,false);
  $('#btn_modPersInsert').off('click').on('click',async function(e) {
    if(Persona.sinErrores()){ //sin errores
      $('#grid').hide();
      $('#edit').show();
      try{
        const resp = await Persona.ejecutaSQL();
        appPersonaSetData(resp.tablaPers);
        appLaboralClear();
        appConyugeClear();
        appSocioClear();
        Persona.close();
        e.stopImmediatePropagation();
    $('#btn_modPersInsert').off('click');
      } catch(err){
        console.error('Error al cargar datos:', err);
      }
    } else {
      alert("!!!Faltan llenar Datos!!!");
    }
  });
  $('#btn_modPersAddToForm').off('click').on('click',async function(e) {
    try{
      const resp = await appAsynFetch({
        TipoQuery : 'viewPersona',
        personaID : Persona.tablaPers.ID,
        fullQuery : 2
      },'pages/master/personas/sql.php');

      $('#grid').hide();
      $('#edit').show();
      appPersonaSetData(Persona.tablaPers); //pestaña Personales
      appLaboralSetData(resp.tablaLabo); //pestaña laborales
      appConyugeSetData(resp.tablaCony); //pestaña de Conyuge
      appSocioClear();
      Persona.close();
      e.stopImmediatePropagation();
      $('#btn_modPersAddToForm').off('click');
    } catch(err){
      console.error('Error al cargar datos:', err);
    }
  });
}

async function appSociosBotonBorrar(){
  const arr = $('[name="chk_Borrar"]:checked').map(function() { return this.value}).get();
  if(arr.length>0){
    if(confirm("¿Esta seguro de continuar?")) {
      try{
        const resp = await appAsynFetch({ TipoQuery:'delSocios', arr:arr },rutaSQL);
        if(!resp.error) { appSociosBotonCancel(); }
      } catch(err){
        console.error('Error al cargar datos:', err);
      }
    }
  } else {
    alert("NO eligio borrar ninguno");
  }
}

async function appSocioView(personaID){
  //tabs default en primer tab
  $('.nav-tabs li').removeClass('active');
  $('.tab-content .tab-pane').removeClass('active');
  $('a[href="#datosSocio"]').closest('li').addClass('active');
  $('#datosSocio').addClass('active');
  $("#div_SocAuditoria").show();
  $("#btnUpdate").toggle(menu.mtto.submenu.socios.cmdUpdate == 1);
  $("#btnInsert").hide();
  try{
    const resp = await appAsynFetch({
      TipoQuery : 'viewSocio',
      personaID : personaID,
      fullQuery : 2
    }, rutaSQL);

    //respuesta
    appSocioSetData(resp.tablaSocio);  //pestaña Socio
    appPersonaSetData(resp.tablaPers); //pestaña Personales
    appLaboralSetData(resp.tablaLabo); //pestaña laborales
    appConyugeSetData(resp.tablaCony); //pestaña de Conyuge
    $('#grid').hide();
    $('#edit').show();
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appSocioSetData(data){
  //info corta
  $("#lbl_Codigo").html(data.codigo);
  $("#lbl_Agencia").html(data.agencia);
  //pestaña de socio
  appLlenarDataEnComboBox(data.comboAgencias,"#cbo_SocAgencia",data.agenciaID);
  $('#txt_SocFechaIng').val(moment(data.fecha).format("DD/MM/YYYY"));
  $("#txt_SocCodigo").val(data.codigo);
  $("#txt_SocObserv").val(data.observac);
  $("#lbl_SocSysFecha").html(moment(data.sys_fecha).format("DD/MM/YYYY"));
  $("#lbl_SocSysUser").html(data.usermod);
}

async function appSocioClear(){
  //tabs default en primer tab
  $('.nav-tabs li').removeClass('active');
  $('.tab-content .tab-pane').removeClass('active');
  $('a[href="#datosSocio"]').closest('li').addClass('active');
  $('#datosSocio').addClass('active');

  //todos los inputs sin error y panel error deshabilitado
  $('.form-group').removeClass('has-error');
  $("#div_SocAuditoria").hide();
  $("#btnInsert").toggle(menu.mtto.submenu.socios.cmdInsert==1);
  $("#btnUpdate").hide();
  $("#txt_SocCodigo").val("").attr("placeholder", "00-000000");
  $("#txt_SocObserv").val("");

  try{
    const resp = await appAsynFetch({
      TipoQuery : 'startSocio'
    }, rutaSQL);
    //pestaña de socio
    appLlenarDataEnComboBox(resp.comboAgencias,"#cbo_SocAgencia",0);
    $('#txt_SocFechaIng').val(moment(resp.fecha).format("DD/MM/YYYY"));
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appSocioGetDatosToDatabase(){
  let datos = "";
  let EsError = false;
  $('.form-group').removeClass('has-error');
  
  if(!EsError){
    datos = {
      socioID : $("#lbl_ID").html(),
      socCodigo : "",
      socFecha : appConvertToFecha($("#txt_SocFechaIng").val(),""),
      socAgenciaID : $("#cbo_SocAgencia").val(),
      socObservac : $("#txt_SocObserv").val()
    }
  }
  return datos;
}

function appPersonaSetData(data){
  //permisos
  $("#btn_PersUpdate").show();
  $("#btn_PersPermiso").hide();

  //info corta
  $('#img_Foto').attr("src", (data.urlfoto=="")?("data/personas/fotouser.jpg"):(data.urlfoto));
  $("#lbl_Nombres").html(data.nombres);
  $("#lbl_Apellidos").html(data.ap_paterno+" "+data.ap_materno);
  $("#lbl_ID").html(data.ID);
  $("#lbl_TipoDNI").html(data.tipoDUI);
  $("#lbl_DNI").html(data.nroDUI);
  $("#lbl_Celular").html(data.celular);

  //pestaña datos personales
  if(data.tipoPersona==2){ //persona juridica
    $("#lbl_PersTipoNombres").html("Razon Social");
    $("#lbl_PersTipoProfesion").html("Rubro");
    $("#lbl_PersTipoApellidos, #lbl_PersTipoSexo, #lbl_PersTipoECivil, #lbl_PersTipoGIntruc").hide();
  }else{
    $("#lbl_PersTipoNombres").html("Nombres");
    $("#lbl_PersTipoProfesion").html("Profesion");
    $("#lbl_PersTipoApellidos, #lbl_PersTipoSexo, #lbl_PersTipoECivil, #lbl_PersTipoGIntruc").show();
  }
  $("#lbl_PersNombres").html(data.nombres);
  $("#lbl_PersApellidos").html(data.ap_paterno+" "+data.ap_materno);
  $("#lbl_PersTipoDNI").html(data.tipoDUI);
  $("#lbl_PersNroDNI").html(data.nroDUI);
  $("#lbl_PersFechaNac").html(moment(data.fechanac).format("DD/MM/YYYY"));
  $("#lbl_PersEdad").html(moment().diff(moment(data.fechanac),"years")+" años");
  $("#lbl_PersPaisNac").html(data.paisnac);
  $("#lbl_PersLugarNac").html(data.lugarnac);
  $("#lbl_PersSexo").html(data.sexo);
  $("#lbl_PersEcivil").html(data.ecivil);
  $("#lbl_PersCelular").html(data.celular);
  $("#lbl_PersTelefijo").html(data.telefijo);
  $("#lbl_PersEmail").html(data.correo);
  $("#lbl_PersGInstruccion").html(data.ginstruc);
  $("#lbl_PersProfesion").html(data.profesion);
  $("#lbl_PersOcupacion").html(data.ocupacion);
  $("#lbl_PersUbicacion").html(data.region+" - "+data.provincia+" - "+data.distrito);
  $("#lbl_PersDireccion").html(data.direccion);
  $("#lbl_PersReferencia").html(data.referencia);
  $("#lbl_PersMedidorluz").html(data.medidorluz);
  $("#lbl_PersMedidorAgua").html(data.medidoragua);
  $("#lbl_PersTipovivienda").html(data.tipovivienda);
  $("#lbl_PersObservac").html(data.observPers);
  $("#lbl_PersSysFecha").html(moment(data.sysfechaPers).format("DD/MM/YYYY HH:mm:ss"));
  $("#lbl_PersSysUser").html(data.sysuserPers);
}

function appPersonaEditar(){
  Persona.editar($('#lbl_ID').html(),'S');
  $('#btn_modPersUpdate').off('click').on('click',async function(e) {
    if(Persona.sinErrores()){ //sin errores
      try{
        const resp = await Persona.ejecutaSQL();
        appPersonaSetData(resp.tablaPers);
        Persona.close();
      } catch(err){
        console.error('Error al cargar datos:', err);
      }
    } else {
      alert("!!!Faltan llenar Datos!!!");
    }
    e.stopImmediatePropagation();
    $('#btn_modPersUpdate').off('click');
  });
}

function appLaboralSetData(data){
  if(data.length>0){
    let fila = "";
    data.forEach((valor,key)=>{
      fila += '<tr>'+
              '<td><a href="javascript:appLaboralDelete('+(valor.ID)+');"><i class="fa fa-trash" style="color:#D73925;"></i></a></td>'+
              '<td>'+((valor.condicion==1)?("Dependiente"):("Independiente"))+'</td>'+
              '<td>'+(valor.ruc)+'</td>'+
              '<td><a href="javascript:appLaboralEditar('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.empresa)+'</a></td>'+
              '<td>'+(valor.cargo)+'</td>'+
              '<td style="text-align:right;">'+(appFormatMoney(valor.ingreso,2))+'</td>'+
              '</tr>';
    });
    $('#grdLaboDatosBody').html(fila);
  } else {
    $('#grdLaboDatosBody').html('');
  }
}

function appLaboralClear(){
  $("#grdLaboDatosBody").html('');
  $("#div_LaboEdit, #btn_LaboDelete, #btn_LaboUpdate, #btn_LaboPermiso").hide();
  $("#btn_LaboInsert").show();
}

function appLaboralNuevo(){
  Laboral.nuevo($('#lbl_ID').html());
  $('#btn_modLaboInsert').off('click').on('click',async function(e) {
    if(Laboral.sinErrores()){
      try{
        const resp = await Laboral.ejecutaSQL();
        appLaboralSetData(resp.tablaLabo);
        Laboral.close();
      } catch(err){
        console.error('Error al cargar datos:', err);
      }
    } else {
      alert("!!!Faltan llenar Datos!!!");
    }
    e.stopImmediatePropagation();
    $('#btn_modLaboInsert').off('click');
  });
}

function appLaboralEditar(laboralID){
  Laboral.editar(laboralID);
  $('#btn_modLaboUpdate').off('click').on('click',async function(e) {
    if(Laboral.sinErrores()){ 
      try{
        const resp = await Laboral.ejecutaSQL();
        appLaboralSetData(resp.tablaLabo);
        Laboral.close();
      } catch(err){
        console.error('Error al cargar datos:', err);
      }
    } else {
      alert("!!!Faltan llenar Datos!!!");
    }
    e.stopImmediatePropagation();
    $('#btn_modLaboUpdate').off('click');
  });
}

async function appLaboralDelete(laboralID){
  if(confirm("¿Realmente desea eliminar los datos laborales?")) {
    const personaID = $("#lbl_ID").html();
    try{
      const resp = await Laboral.borrar(personaID,laboralID);
      if(!resp.error){
        appLaboralSetData(resp.tablaLabo);
        Laboral.close();
      } else {
        alert("!!!Hubo un error al momento de eliminar estos datos!!!");
      }
    } catch(err){
      console.error('Error al cargar datos:', err);
    }
  }
}

function appConyugeSetData(data){
  if(data.id_conyuge>0){
    let conyuge = data.persona;
    $('.form-group').removeClass("has-error");
    $("#lbl_ConyNombres").html(conyuge.nombres);
    $("#lbl_ConyApellidos").html(conyuge.ap_paterno+' '+conyuge.ap_materno);
    $("#lbl_ConyTipoDNI").html(conyuge.tipoDUI);
    $("#lbl_ConyNroDNI").html(conyuge.nroDUI);
    $("#lbl_ConyFechaNac").html(moment(conyuge.fechanac).format("DD/MM/YYYY"));
    $("#lbl_ConyEcivil").html(conyuge.ecivil);
    $("#lbl_ConyCelular").html(conyuge.celular);
    $("#lbl_ConyTelefijo").html(conyuge.telefijo);
    $("#lbl_ConyEmail").html(conyuge.correo);
    $("#lbl_ConyGInstruccion").html(conyuge.ginstruc);
    $("#lbl_ConyProfesion").html(conyuge.profesion);
    $("#lbl_ConyOcupacion").html(conyuge.ocupacion);
    $("#lbl_ConyUbicacion").html(conyuge.region+" - "+conyuge.provincia+" - "+conyuge.distrito);
    $("#lbl_ConyDireccion").html(conyuge.direccion);
    $("#lbl_ConyReferencia").html(conyuge.referencia);
    $("#lbl_ConyMedidorluz").html(conyuge.medidorluz);
    $("#lbl_ConyMedidoragua").html(conyuge.medidoragua);
    $("#lbl_ConyTipovivienda").html(conyuge.tipovivienda);
    $("#div_Conyuge").style.display = 'block';
    $("#lbl_ConyTiempoRel").innerHTML = (data.tiempoRelacion+(" (años)"));
    //permisos
    $("#btn_ConyInsert, #btn_ConyPermiso").hide();
    $("#btn_ConyDelete, #btn_ConyUpdate").show();
  } else {
    appConyugeClear();
  }
}

function appConyugeClear(){
  $('.form-group').removeClass('has-error');
  $("#lbl_ConyTiempoRel").val("0");
  $("#div_Conyuge, #btn_ConyDelete, #btn_ConyUpdate, #btn_ConyPermiso").hide();
  $("#btn_ConyInsert").show();
}

function appConyugeNuevo(){
  Conyuge.nuevo($('#lbl_ID').html(),rutaSQL);
  $('#btn_modConyInsert').off('click').on('click',async function(e) {
    if(Conyuge.sinErrores()){ //guardamos datos del conyuge
      try{
        const resp = await Conyuge.ejecutaSQL();
        appConyugeSetData(resp.tablaCony);
        Conyuge.close();
      } catch(err){
        console.error('Error al cargar datos:', err);
      }
    } else {
      alert("!!!Faltan llenar Datos!!!");
    }
    e.stopImmediatePropagation();
    $('#btn_modConyInsert').off('click');
  });
}

function appConyugeEditar(){
  Conyuge.editar($('#lbl_ID').html());
  $('#btn_modConyUpdate').off('click').on('click',async function(e) {
    if(Conyuge.sinErrores()){
      try{
        const resp = await Conyuge.ejecutaSQL();
        appConyugeSetData(resp.tablaCony);
        Conyuge.close();
      } catch(err){
        console.error('Error al cargar datos:', err);
      }
    } else {
      alert("!!!Faltan llenar Datos!!!");
    }
    e.stopImmediatePropagation();
    $('#btn_modConyUpdate').off('click');
  });
}

async function appConyugeDelete(){
  if(confirm("¿Realmente desea eliminar los datos de Conyuge?")) {
    try{
      const resp = await Conyuge.borrar($('#lbl_ID').html());
      if(!resp.error){
        appConyugeClear();
        Conyuge.close();
      } else {
        alert("!!!Hubo un error al momento de eliminar estos datos!!!");
      }
    } catch(err){
      console.error('Error al cargar datos:', err);
    }
  }
}
