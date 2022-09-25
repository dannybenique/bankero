var rutaSQL = "pages/config/workers/sql.php";

//=========================funciones para workers============================
function appWorkerBotonReset(){
  $("#txtBuscar").val("");
  let datos = { TipoQuery:'ComboBox', miSubSelect:'Agencias' }
  appAjaxSelect(datos).done(function(resp){
    $("#hid_agenciaText").val(resp.tabla[0].nombre);
    appLlenarComboAgencias("#cboAgencias",resp.agenciaID,resp.tabla);
    appWorkerGridAll();
  });
}

function appWorkerBotonBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { appWorkerGridAll(); }
}

function appWorkerBotonBorrar(){
  var arr = $('[name="chk_Borrar[]"]:checked').map(function(){ return this.value; }).get();
  if((arr.length)==0){
    alert("Debe elegir por lo menos UN empleado para borrar");
  } else {
    $('#date_fechabaja').datepicker("setDate",moment().format("DD/MM/YYYY"));
    $('#modalDeleteWorker').modal();
  }
}

function appWorkerBotonDownload(){
  let datos = {
    TipoQuery : 'downWorkers',
    agenciaID : $('#cboAgencias').val()
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    Jhxlsx.export(resp.tableData, resp.options);
  });
}

function appWorkerBotonNuevo(){
  Persona.openBuscar('VerifyWorker',1,0);
  $('#btn_modPersInsert').on('click',function(e) {
    if(Persona.sinErrores()){ //sin errores
      Persona.ejecutaSQL().done(function(resp){
        let data = JSON.parse(resp);
        appWorkerClear();
        appPersonaSetData(data.tablaPers);
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
    appWorkerClear();
    appPersonaSetData(Persona.tablaPers); //pestaña Personales
    $('#grid').hide();
    $('#edit').show();
    Persona.close();
    e.stopImmediatePropagation();
    $('#btn_modPersAddToForm').off('click');
  });
}

function appPersonaBotonEditar(){
  Persona.editar($('#lbl_ID').html(),'W');
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

function appWorkerBotonEstado(){
  if($("#hidEstado").val()=="1") {
    $("#hidEstado").val("0");
    $("#icoEstado").removeClass("fa-toggle-on");
    $("#icoEstado").addClass("fa-toggle-off");
  } else {
    $("#hidEstado").val("1");
    $("#icoEstado").removeClass("fa-toggle-off");
    $("#icoEstado").addClass("fa-toggle-on");
  }
  appWorkerGridAll();
}

function appWorkerBotonCancel(){
    $('#grid').show();
    $('#edit').hide();
}

function appWorkerBotonInsert(){
  let datosWorker = appWorkerGetDatosToDatabase();
  let datosUsuario = appUsuarioGetDatosToDatabase();

  if(datosWorker!="" && datosUsuario!=""){
    let datos = {
      TipoQuery : "insWorker",
      datosWorker : datosWorker,
      datosUsuario : datosUsuario
    }
    appAjaxInsert(datos,rutaSQL).done(function(resp){
      appWorkerGridAll();
      appWorkerBotonCancel();
    });
  }
}

function appWorkerBotonUpdate(){
  let datosWorker = appWorkerGetDatosToDatabase();
  let datosUsuario = appUsuarioGetDatosToDatabase();
  console.log(datosWorker);

  if(datosWorker!="" && datosUsuario!=""){
    let datos = {
      TipoQuery : "updWorker",
      datosWorker : datosWorker,
      datosUsuario : datosUsuario
    }
    appAjaxUpdate(datos,rutaSQL).done(function(resp){
      console.log(resp);
      appWorkerGridAll();
      appWorkerBotonCancel();
    });
  }
}

function appWorkerLinkPassw(userID){
  var datos = {
    TipoQuery : 'selDatosPassw',
    personaID : userID
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    $('#usrIDpassw').prop("value",userID);
    $('#txt_passwordNew').val('');
    $('#txt_passwordRenew').val('');
    $('#usrNombreCorto').html(resp.nombrecorto);
    $('#modalChangePassw').modal();
  });
}

function appWorkerLinkCoopSUD(codUsuario){
  var datos = {
    TipoQuery : 'coopSUDselect',
    codusuario : codUsuario
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    $('#coopCodUsuario').prop("value",codUsuario);
    $("#coopUsuario").html(resp.usuario);
    $("#coopAgencia").html(resp.agencia);
    $("#coopVentanilla").html(resp.ventanilla);
    $("#cbo_coopNivel option[selected]").removeAttr("selected");
    $("#cbo_coopNivel option[value='"+ resp.nivel +"']").attr("selected",true);
    if(resp.modifi=='S') {$("#chk_coopModiInter").prop("checked",true);} else {$("#chk_coopModiInter").prop("checked",false);}
    if(resp.elimina=='S') {$("#chk_coopDeleMovi").prop("checked",true);} else {$("#chk_coopDeleMovi").prop("checked",false);}
    $('#modalChangeCoopSUD').modal();
  });
}

function appWorkerGridAll(){
  $('#grdDatosBody').html('<tr><td colspan="9" style="text-align:center;"><br><div class="progress progress-sm active" style=""><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div><br>Un momento, por favor...</td></tr>');
  var agenciaID = $("#cboAgencias").val();
  var txtBuscar = $("#txtBuscar").val().toUpperCase();
  var datos = {
    TipoQuery : 'selWorkers',
    agenciaID : agenciaID,
    buscar  : txtBuscar,
    estado    : $("#hidEstado").val()
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    if(resp.tabla.length>0){
      let workerData = "";

      $.each(resp.tabla,function(key, valor){
        let trEstado = (valor.estado==0)?("color:#bfbfbf;"):("");

        workerData += '<tr style="'+(trEstado)+'">';
        workerData += '<td><input type="checkbox" id="chk_Borrar" name="chk_Borrar[]" value="'+(valor.ID)+'"/></td>';
        workerData += '<td>'+((valor.user==1)?('<a href="javascript:appWorkerLinkPassw('+(valor.ID)+');"><i class="fa fa-lock"></i></a>'):(''))+'</td>';
        workerData += '<td>'+((valor.SUD==1)?('<a href="javascript:appWorkerLinkCoopSUD(\''+(valor.codigo)+'\');" style="'+(trEstado=="" ? "color:orange;" : trEstado)+'"><i class="fa fa-toggle-on"></i></a>'):(''))+'</td>';
        workerData += '<td>'+(valor.codigo)+'</td>';
        workerData += '<td>'+((valor.url=="")?(''):('<i class="fa fa-paperclip"></i>'))+'</td>';
        workerData += '<td>'+(valor.dni)+'</td>';
        workerData += '<td><a style="'+(trEstado)+'" href="javascript:appWorkerView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.worker)+'</a></td>';
        workerData += '<td>'+(valor.cargo)+'</td>';
        workerData += '<td>'+(valor.agencia)+'</td>';
        workerData += '</tr>';
      });
      $('#grdDatosBody').html(workerData);
    }else{
      let mensaje = (txtBuscar!="")?("para "+txtBuscar):("");
      $('#grdDatosBody').html('<tr><td colspan="9" style="text-align:center;color:red;">Sin Resultados '+mensaje+'</td></tr>');
    }
    $('#grdDatosCount').html(resp.tabla.length);
    $('#agenciaAbrev').val(resp.agenciaAbrev);
  });
}

function appWorkerView(workerID){
  var datos = {
    TipoQuery : 'editWorker',
    personaID : workerID
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    console.log(resp);
    appPersonaSetData(resp.tablaPers);
    appWorkerSetData(resp.tablaWork);
    appUsuarioSetData(resp.tablaUser);

    //tabs default en primer tab
    $('.nav-tabs li').removeClass('active');
    $('.tab-content .tab-pane').removeClass('active');
    $('a[href="#datosPersonal"]').closest('li').addClass('active');
    $('#datosPersonal').addClass('active');

    $("#btnInsert").hide();
    $("#btnUpdate").show();
    $("#pn_EditChecks").show();
    $("#chk_EditWorker").prop("checked",false);
    $("#chk_EditUsuario").prop("checked",false);

    $('#grid').hide();
    $('#edit').show();
  });
}

function appWorkerClear(){
  //info corta
  $('#img_Foto').prop("src","data/personas/images/0noFotoUser.jpg");
  $("#lbl_NombreCorto").html("...");
  $("#lbl_cargo").html("...");
  $("#lbl_ID").html("...");
  $("#lbl_agencia").html("...");
  $("#lbl_DNI").html("...");
  $("#lbl_celular").html("...");

  //pestaña de empleado
  $("#txt_WorkNombreCorto").val("");
  $("#txt_WorkCorreo").val("");
  $("#txt_WorkCodigo").val("");
  appComboBox("#cbo_WorkAgencia","Agencias",0);//agencia
  appComboBox("#cbo_WorkCargo","Cargo",0);//cargo
  $('#date_WorkIngreso').datepicker("setDate",moment().format("DD/MM/YYYY"));
  $('#date_WorkRenov').val($('#date_WorkIngreso').val());
  $('#date_WorkVacac').val($('#date_WorkIngreso').val());
  $('#chk_WorkAsignaFam').val('0');
  $("#chk_WorkEstado").val('1');
  $("#txt_WorkObserv").val('');

  //pestaña de usuario
  appUsuarioNewOne();

  //tabs default en primer tab
  $('.nav-tabs li').removeClass('active');
  $('.tab-content .tab-pane').removeClass('active');
  $('a[href="#datosPersonal"]').closest('li').addClass('active');
  $('#datosPersonal').addClass('active');

  //todos los inputs sin error y panel error deshabilitado
  $('.box-body .form-group').removeClass('has-error');
  $("#pn_EditChecks").hide();
  $("#btnInsert").show();
  $("#btnUpdate").hide();

  $('#grid').hide();
  $('#edit').show();
}

function appPersonaSetData(data){
  //info corta
  $('#img_Foto').prop("src",data.urlfoto=="" ? "data/personas/images/0noFotoUser.jpg" : data.urlfoto);
  $("#lbl_ID").html(data.ID);
  $("#lbl_agencia").html($("#hid_agenciaText").val());
  $("#lbl_DNI").html(data.nroDNI);
  $("#lbl_celular").html(data.celular);

  //pestaña de datos personales
  $("#lbl_PersNombres").html(data.nombres);
  $("#lbl_PersApellidos").html(data.ap_paterno+' '+data.ap_materno);
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

function appWorkerSetData(data){
  //info corta
  $("#lbl_agencia").html(data.agencia);
  $("#lbl_NombreCorto").html(data.nombrecorto);
  $("#lbl_cargo").html(data.cargo);

  //pestaña de empleado
  $("#txt_WorkNombreCorto").val(data.nombrecorto);
  $("#txt_WorkCorreo").val(data.correo);
  $("#txt_WorkCodigo").val(data.codigo);
  appComboBox("#cbo_WorkAgencia","Agencias",data.id_agencia);//agencia
  appComboBox("#cbo_WorkCargo","Cargo",data.id_cargo);//cargo
  $('#date_WorkIngreso').datepicker("setDate",data.fecha_ing);
  $('#date_WorkRenov').prop("value",data.fecha_renov);
  $('#date_WorkVacac').prop("value",data.fecha_vacac);
  $('#chk_WorkAsignaFam').prop("value",data.asigna_fam);
  $("#chk_WorkEstado").prop("value",data.estado);
  $("#txt_WorkObserv").val(data.observWork);
  $("#chk_estado").html((data.estado==1)?(''):('<div class="input-group"><span class="input-group-addon pull-left" style="border:1px solid #D2D6DD;width:100%;padding:9px 10px 9px 10px;"><input type="checkbox" value="0" onchange="appWorkerEstado(this.checked);"> Empleado Activo</span></div>'));
}

function appWorkerEstado(ss){
  $("#chk_WorkEstado").val((ss)?1:0);
}

function appUsuarioSetData(data){
  //pestaña de usuario
  $('#chk_UsrEsUsuario').prop("checked",data.usrActivo);
  if(data.usrActivo==1){
    $("#txt_UsrLogin").val(data.login);
    $("#appUsrPassw").hide();
    $("#appUsrRepassw").hide();
    appComboBox("#cbo_UsrNivelAcceso","NivelAcceso",data.id_usernivel); //nivel de acceso para usuario
  } else{
    appUsuarioNewOne();
  }
}

function appUsuarioNewOne(){
  $('#chk_UsrEsUsuario').prop("checked",false);

  $("#txt_UsrLogin").prop("disabled",true);
  $("#txt_UsrPassw").prop("disabled",true);
  $("#txt_UsrPassw").prop("disabled",true);
  $("#txt_UsrRepassw").prop("disabled",true);
  $("#cbo_UsrNivelAcceso").prop("disabled",true);

  $("#appUsrPassw").show();
  $("#appUsrRepassw").show();
  $("#txt_UsrLogin").val("");
  $("#txt_UsrPassw").val("");
  $("#txt_UsrRepassw").val("");
  appComboBox("#cbo_UsrNivelAcceso","NivelAcceso",0);
}

function appUsuarioSetOne(ss){
  $("#txt_UsrLogin").prop("disabled",!ss);
  $("#txt_UsrPassw").prop("disabled",!ss);
  $("#txt_UsrPassw").prop("disabled",!ss);
  $("#txt_UsrRepassw").prop("disabled",!ss);
  $("#cbo_UsrNivelAcceso").prop("disabled",!ss);
}

function appWorkerGetDatosToDatabase(){
  let EsError = false;
  let datos = "";
  $('.box-body .form-group').removeClass('has-error');

  if(EsError==false){
    datos = {
      personaID : $("#lbl_ID").html(),
      workCodigo : $("#txt_WorkCodigo").val(),
      workCorreo : $("#txt_WorkCorreo").val(),
      workNombrecorto : $("#txt_WorkNombreCorto").val(),
      workFechaIngre : appConvertToFecha($("#date_WorkIngreso").val(),""),
      workFechaRenov : appConvertToFecha($("#date_WorkRenov").val(),""),
      workFechaVacac : appConvertToFecha($("#date_WorkVacac").val(),""),
      workAgenciaID : $("#cbo_WorkAgencia").val(),
      workCargoID : $("#cbo_WorkCargo").val(),
      workObservac : $("#txt_WorkObserv").val(),
      workAsignaFam : $("#chk_WorkAsignaFam").val(),
      workEstado : $("#chk_WorkEstado").val(),
      chkWorker : $("#chk_EditWorker").is(":checked")?1:0
    }
  }
  return datos;
}

function appUsuarioGetDatosToDatabase(){
  let EsError = false;
  let datos = "";
  $('.box-body .form-group').removeClass('has-error');

  if(EsError==false){
    datos = {
      personaID : $("#lbl_ID").html(),
      userEsUsuario : $("#chk_UsrEsUsuario").is(":checked")?1:0,
      userLogin : $("#txt_UsrLogin").val(),
      userPasswd : SHA1($("#txt_UsrPassw").val()).toString().toUpperCase(),
      userPasswdtxt : $("#txt_UsrPassw").val(),
      userUsernivelID : $("#cbo_UsrNivelAcceso").val(),
      chkUsuario : $("#chk_EditUsuario").is(":checked")?1:0
    }
  }
  return datos;
}

function modWorkerBotonDelete(){
  var arr = $('[name="chk_Borrar[]"]:checked').map(function(){ return this.value; }).get();
  var datos = {
    TipoQuery : 'delWorker',
    IDs : arr,
    fechaBaja : appConvertToFecha($("#date_fechabaja").val(),"")
  };
  appAjaxDelete(datos,rutaSQL).done(function(resp){
    if (resp.error == false) { //sin errores
      $('#modalDeleteWorker').modal('hide');
      appWorkerGridAll();
    }
  });
}

function modWorkerBotonUpdatePassw(){
  let userID = $('#usrIDpassw').prop('value');
  let miPass = $('#txt_passwordNew').val();
  let miRepass = $('#txt_passwordRenew').val();

  if (miPass==miRepass){
    let datos = {
      TipoQuery : 'updPassword',
      pass : SHA1(miPass).toString().toUpperCase(),
      passtxt : miPass,
      userID : userID
    }
    appAjaxUpdate(datos,"pages/global/profile/sql.php").done(function(resp){
      if (!resp.error) { //sin errores
        $('#txt_passwordNew').val('');
        $('#txt_passwordRenew').val('');
        $('#modalChangePassw').modal('hide');
      }
    });
  } else{
    alert("La clave no coincide");
  }
}

function modWorkerBotonUpdateCoopSUD(codUsuario,nivelUsuario,coopModiInte,coopDeleMovi){
  var datos = {
    TipoQuery : 'coopSUDCambiarDatos',
    codusuario : codUsuario,
    nivel : $(nivelUsuario).val(),
    modifi : $(coopModiInte).is(":checked")?"S":"N",
    elimina : $(coopDeleMovi).is(":checked")?"S":"N"
  }
  appAjaxUpdate(datos,rutaSQL).done(function(resp){
    if (resp.error == false) { //sin errores
      $('#modalChangeCoopSUD').modal('hide');
    }
  });
}
