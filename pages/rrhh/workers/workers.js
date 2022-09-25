var rutaSQL = "pages/rrhh/workers/sql.php";

//=========================funciones para Recursos Humanos RRHH============================
function rrhhWorkerGetAll(){
  let agenciaID = $("#cboAgencias").val();
  let txtBuscar = $("#txtBuscar").val();
  let datos = {
    TipoQuery : 'selWorkers',
    agenciaID : agenciaID,
    buscar  : txtBuscar.toUpperCase(),
    estado    : 1
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    if(resp.tabla.length>0){
      let workerData = "";

      $.each(resp.tabla,function(key, valor){
        workerData += '<tr>';
        workerData += '<td>'+(valor.codigo)+'</td>';
        workerData += '<td>'+((valor.url==="")?(''):('<i class="fa fa-paperclip"></i>'))+'</td>';
        workerData += '<td>'+(valor.dni)+'</td>';
        workerData += '<td><a href="javascript:rrhhWorkerView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.worker)+'</a></td>';
        workerData += '<td>'+(valor.cargo)+'</td>';
        workerData += '<td>'+(valor.agencia)+'</td>';
        workerData += '</tr>';
      });
      $('#grdDatosBody').html(workerData);
    }else{
      let mensaje = ((txtBuscar=="")?(""):("para "+txtBuscar));
      $('#grdDatosBody').html('<tr><td colspan="6" style="text-align:center;color:red;">Sin Resultados '+mensaje+'</td></tr>');
    }
    $('#grdDatosCount').html(resp.tabla.length);
    $('#agenciaAbrev').val(resp.agenciaAbrev);
  });
}

function rrhhWorkerReset(){
  $("#txtBuscar").val("");
  appAjaxSelect({TipoQuery:'ComboBox',miSubSelect:'Agencias'}).done(function(resp){
    appLlenarComboAgencias("#cboAgencias",resp.agenciaID,resp.tabla);
    rrhhWorkerGetAll();
  });
}

function rrhhWorkerBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { rrhhWorkerGetAll(); }
}

function rrhhWorkerView(workerID){
  let datos = {
    TipoQuery : 'editWorker',
    personaID : workerID
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    rrhhPersonaSetData(resp.tablaPers);
    rrhhWorkerSetData(resp.tablaWork);

    //seteo de tabs y otros div ocultos
    $('.nav-tabs li').removeClass('active');
    $('.tab-content .tab-pane').removeClass('active');
    $('a[href="#datosWorker"]').closest('li').addClass('active');
    $('#datosWorker').addClass('active');
    $("#btnUpdate").show();
    $("#pn_Error").hide();

    $('#grid').hide();
    $('#edit').show();
  });
}

function rrhhPersonaBotonEditar(){
  Persona.editar($('#lbl_ID').html(),'W');
  $('#btn_modPersUpdate').on('click',function(e) {
    if(Persona.sinErrores()){ //sin errores
      Persona.ejecutaSQL().done(function(resp){
        let data = JSON.parse(resp);
        rrhhPersonaSetData(data.tablaPers);
        Persona.close();
      });
    } else {
      alert("!!!Faltan llenar Datos!!!");
    }
    e.stopImmediatePropagation();
    $('#btn_modPersUpdate').off('click');
  });
}

function rrhhPersonaSetData(data){
  //datos info corta
  $('#img_Foto').prop("src",data.urlfoto=="" ? "data/personas/images/0noFotoUser.jpg" : data.urlfoto);
  $("#lbl_ID").html(data.ID);
  $("#lbl_DNI").html(data.nroDNI);
  $("#lbl_Celular").html(data.celular);

  //datos personales
  $("#lbl_PersNombres").html(data.nombres);
  $("#lbl_PersApellidos").html(data.ap_paterno+' '+data.ap_materno);
  $("#lbl_PersTipoDNI").html(data.tipoDNI);
  $("#lbl_PersNroDNI").html(data.nroDNI);
  $("#lbl_PersFechaNac").html(data.fechanac);
  $("#lbl_PersLugarnac").html(data.lugarnac);
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

function rrhhWorkerSetData(data){
  //datos info corta
  $("#lbl_NombreCorto").html(data.nombrecorto);
  $("#lbl_Cargo").html(data.cargo);
  $("#lbl_Agencia").html(data.agencia);

  //datos worker
  $("#txt_WorkNombreCorto").val(data.nombrecorto);
  $("#txt_WorkCodigo").val(data.codigo);
  appComboBox("#cbo_WorkAgencia","Agencias",data.id_agencia);//agencia
  appComboBox("#cbo_WorkCargo","Cargo",data.id_cargo);//cargo
  $('#date_WorkIngreso').datepicker("setDate",data.fecha_ing);
  $('#date_WorkRenov').datepicker("setDate",data.fecha_renov);
  $('#date_WorkVacac').datepicker("setDate",data.fecha_vacac);
  if(data.asigna_fam==1) { $('#chk_WorkAsignaFam').prop("checked",true); } else { $('#chk_WorkAsignaFam').prop("checked",false); }
  $("#txt_WorkObserv").val(data.observWork);
  $("#lbl_WorkSysFecha").html(data.sysfechaWork);
  $("#lbl_WorkSysUser").html(data.sysuserWork);
}

function rrhhWorkerUpdate(){
  let esError = false;

  $('.box-body .form-group').removeClass('has-error');
  if($("#txt_PersNombres").val()=="") { $("#pn_PersNombres").prop("class","form-group has-error"); esError = true; }
  if($("#txt_PersApePaterno").val()=="") { $("#pn_PersApePaterno").prop("class","form-group has-error"); esError = true; }
  if($("#txt_PersApeMaterno").val()=="") { $("#pn_PersApeMaterno").prop("class","form-group has-error"); esError = true; }
  if($("#txt_PersDocumento").val()=="") { $("#pn_PersDocumento").prop("class","form-group has-error"); esError = true; }
  if($("#txt_PersCelular").val()=="") { $("#pn_PersCelular").prop("class","form-group has-error"); esError = true; }

  if(esError){
    alert("!!!Faltan llenar datos!!!");
  } else { //guardamos datos de persona, empleado y usuario
    let datos = {
      TipoQuery : 'rrhhWorker',
      ID : $("#lbl_ID").html(),
      workCodigo : $("#txt_WorkCodigo").val(),
      workNombrecorto : $("#txt_WorkNombreCorto").val(),
      workFechaIngre : appConvertToFecha($("#date_WorkIngreso").val(),""),
      workFechaRenov : appConvertToFecha($("#date_WorkRenov").val(),""),
      workFechaVacac : appConvertToFecha($("#date_WorkVacac").val(),""),
      workAgenciaID : $("#cbo_WorkAgencia").val(),
      workCargoID : $("#cbo_WorkCargo").val(),
      workObservac : $("#txt_WorkObserv").val(),
      workAsignaFam : $("#chk_WorkAsignaFam").is(":checked")?1:0
    }
    appAjaxUpdate(datos,rutaSQL).done(function(resp){
      rrhhWorkerGetAll();
      rrhhWorkerCancel();
    });
  }
}

function rrhhWorkerCancel(){
    $('#grid').show();
    $('#edit').hide();
}

function appPermisoPersonas(){
  let datos = { TipoQuery:'OneNotificacion', tabla:'tb_personas', personaID:$("#lbl_ID").html() }
  appAjaxInsert(datos).done(function(resp){
    if(resp.error==false){ $("#btn_PersPermiso").hide(); }
    else { alert("!!!Hubo un error... "+(resp.mensaje)+"!!!"); }
  });
}
