const rutaSQL = "pages/master/personas/sql.php";

//=========================funciones para Personas============================
function appPersonasBuscar(e){ if(e.keyCode === 13) { appPersonasGrid();  } }

async function appPersonasGrid(){
  $('#grdDatos').html('<tr><td colspan="6"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  const txtBuscar = $("#txtBuscar").val().toUpperCase();
  try{
    const resp = await appAsynFetch({ TipoQuery:'selPersonas', buscar:txtBuscar },rutaSQL);
    //respuesta
    const disabledDelete = (resp.rolID===resp.rootID) ? (""):("disabled");
    $("#chk_All").prop("disabled", resp.rolID !== resp.rootID);
    if(resp.tabla.length>0){
      let fila = "";
      resp.tabla.forEach((valor,key)=>{
        fila += '<tr>'+
                '<td><input type="checkbox" name="chk_Borrar" value="'+(valor.ID)+'" '+(disabledDelete)+'/></td>'+
                '<td><a href="javascript:appPersonasBotonAuditoria('+(valor.ID)+');"><i class="fa fa-paperclip" title="Auditoria"></i></a></td>'+
                '<td>'+(valor.DNI)+'</td>'+
                '<td><a href="javascript:appPersonaView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.persona)+'</a></td>'+
                '<td>'+(valor.direccion)+'</td>'+
                '</tr>';
      });
      $('#grdDatos').html(fila);
    }else{
      $('#grdDatos').html('<tr><td colspan="6" style="text-align:center;color:red;">Sin Resultados para '+txtBuscar+'</td></tr>');
    }
    $('#grdCount').html(resp.tabla.length+"/"+resp.cuenta);
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appPersonasReset(){
  $('#txtBuscar').val("");
  appPersonasGrid();
}

async function appPersonaNuevo(){
  Persona.openBuscar('VerifyPersona',rutaSQL,true,false,false);
  $('#btn_modPersInsert').off('click').on('click',async function(e) {
    if(Persona.sinErrores()){ //sin errores
      try{
        const resp = await Persona.ejecutaSQL();
        appPersonaSetData(resp.tablaPers);
        appLaboralClear();
        appConyugeClear();
        $('#grid').hide();
        $('#edit').show();
        Persona.close();
      } catch(err){
        console.error('Error al cargar datos:', err);
      }
    } else {
      alert("!!!Faltan llenar Datos!!!");
    }
    e.stopImmediatePropagation();
    $('#btn_modPersInsert').off('click');
  });
}

async function appPersonaEditar(){
  Persona.editar($("#lbl_ID").html(),'P');
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

async function appPersonaView(personaID){
  //tabs default en primer tab
  $('.nav-tabs li').removeClass('active');
  $('.tab-content .tab-pane').removeClass('active');
  $('a[href="#datosPersonal"]').closest('li').addClass('active');
  $('#datosPersonal').addClass('active');
  
  try{
    const resp = await appAsynFetch({
      TipoQuery : 'viewPersona',
      personaID : personaID,
      fullQuery : 2
    }, rutaSQL);

    //respuesta
    appPersonaSetData(resp.tablaPers); //pestaña Personales
    appLaboralSetData(resp.tablaLabo); //pestaña laborales
    appConyugeSetData(resp.tablaCony); //pestaña de Conyuge

    $('#grid').hide();
    $('#edit').show();
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appPersonaSetData(data){
  //info corta
  $('#img_Foto').attr("src", ((data.urlfoto=="")?("data/personas/fotouser.jpg"):(data.urlfoto)));
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

  //permisos
  $("#btn_PersPermiso").hide();
  $("#btn_PersUpdate").show();
}

function appPersonasBotonCancel(){
  appPersonasGrid();
  $('#grid').show();
  $('#edit').hide();
}

async function appPersonasBotonAuditoria(personaID){
  try{
    const resp = await appAsynFetch({ TipoQuery:'audiPersona', personaID }, rutaSQL);

    //respuesta
    $("#lbl_modAuditoriaTitulo").html((resp.tablaPers.tipoPersona==2) ? (resp.tablaPers.nombres) : (resp.tablaPers.persona));
    if(resp.tablaLog.length>0){
      let fila = "";
      resp.tablaLog.forEach((valor,key)=>{
        fila += '<tr>'+
                '<td>'+(valor.codigo)+'</td>'+
                '<td>'+(valor.tabla)+'</td>'+
                '<td>'+(valor.accion)+'</td>'+
                '<td>'+(valor.campo)+'</td>'+
                '<td>'+(valor.observac)+'</td>'+
                '<td>'+(valor.usuario)+'</td>'+
                '<td>'+(valor.sysIP)+'</td>'+
                '<td style="text-align:center;">'+(valor.sysagencia)+'</td>'+
                '<td style="text-align:right;">'+(valor.sysfecha)+'</td>'+
                '<td style="text-align:right;">'+(valor.syshora)+'</td>'+
                '</tr>';
      });
      $('#grdAuditoriaBody').html(fila);
    }else{
      $('#grdAuditoriaBody').html('<tr><td colspan="10" style="text-align:center;color:red;">****** Sin Resultados ******</td></tr>');
    }
    $('#modalAuditoria').modal();
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
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

async function appLaboralNuevo(){
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

async function appLaboralEditar(laboralID){
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
    let personaID = $("#lbl_ID").html();
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
    $("#div_Conyuge").html= 'block';
    $("#lbl_ConyTiempoRel").html(data.tiempoRelacion+(" (años)"));
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

async function appConyugeNuevo(){
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

async function appConyugeEditar(){
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

function appConyugeDelete(){
  if(confirm("¿Realmente desea eliminar los datos de Conyuge?")) {
    Conyuge.borrar($('#lbl_ID').html()).then(resp => {
      if(!resp.error){
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
