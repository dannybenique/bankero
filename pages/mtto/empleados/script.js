const rutaSQL = "pages/mtto/empleados/sql.php";
var menu = "";
var zTreeObj = null;
var zMnuEmpleado = null;
var zSetting = { 
  check : {
    enable : true
  },
  view : {
    addDiyDom : null,
    showIcon : showIconForTree
  },
  callback: {
    beforeDrag : beforeDrag
  },
  edit: {
    enable : true,
    showRemoveBtn : true,
    showRenameBtn : true
  }
};

//=========================funciones============================
function appWorkersBuscar(e){ if(e.keyCode === 13) { appWorkersGrid(); } }

async function appWorkersGrid(){
  $('#grdDatos').html('<tr><td colspan="9"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  const txtBuscar = $("#txtBuscar").val().toUpperCase();
  const disabledDelete = (menu.mtto.submenu.empleados.cmdDelete===1) ? "" : "disabled";
  try{
    $("#chk_All").prop("disabled", !(menu.mtto.submenu.empleados.cmdDelete === 1));
    const resp = await appAsynFetch({ TipoQuery:'selWorkers', buscar:txtBuscar },rutaSQL);
    if(resp.tabla.length>0){
      let fila = "";
      resp.tabla.forEach((valor,key)=>{
        fila += '<tr>'+
                '<td><input type="checkbox" name="chk_Borrar" value="'+(valor.ID)+'" '+disabledDelete+'/></td>'+
                '<td><i class="fa fa-paperclip"></i></td>'+
                '<td>'+((valor.login!=null)?('<a href="javascript:appUserCambioPassw('+(valor.ID)+')"><i class="fa fa-lock"></i></a>'):(''))+'</td>'+
                '<td>'+(valor.codigo)+'</td>'+
                '<td>'+(valor.nro_dui)+'</td>'+
                '<td><a href="javascript:appWorkerView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.empleado)+'</a></td>'+
                '<td>'+(valor.nombrecorto)+'</td>'+
                '<td>'+(valor.cargo)+'</td>'+
                '<td>'+(valor.agencia)+'</td>'+
                '</tr>';
      });
      $('#grdDatos').html(fila);
    } else {
      let res = (txtBuscar==="") ? ("") : ("para "+txtBuscar);
      $('#grdDatos').html('<tr><td colspan="9" style="text-align:center;color:red;">Sin Resultados '+(res)+'</td></tr>');
    }
    $('#grdCount').html(resp.tabla.length+"/"+resp.cuenta);
  } catch(err) {
    console.error('Error al cargar datos:', err);
  }
}

async function appWorkersReset(){
  zTreeObj = $.fn.zTree.init($("#appTreeView"), zSetting, null); //configurar treeview
  $("#txtBuscar").val("");
  $("#grdDatos").html("");

  try{
    const resp = await appAsynFetch({TipoQuery:'selDataUser'},"includes/sess_interfaz.php");
    
    menu = JSON.parse(resp.menu);
    $("#btn_DEL").toggle(menu.mtto.submenu.empleados.cmdDelete == 1);
    $("#btn_NEW").toggle(menu.mtto.submenu.empleados.cmdInsert == 1);
    $("#div_PersAuditoria").toggle(resp.rolID == 101);
    appWorkersGrid();
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appWorkersBotonCancel(){
  appWorkersGrid();
  $("#grid").show();
  $("#edit").hide();
}

async function appWorkersBotonInsert(){
  let datos = appWorkerGetDatosToDatabase();

  if(datos!=null){
    datos.TipoQuery = "insWorker";
    datos.usuario = appUserGetDatosToDatabase();
    try{
      const resp = await appAsynFetch(datos,rutaSQL);
      if(!resp.error) { appWorkersBotonCancel(); }
    } catch(err) {
      console.error('Error al cargar datos:', err);
    }
  }
}

async function appWorkersBotonUpdate(){
  let datos = appWorkerGetDatosToDatabase();
  
  if(datos!=null){
    datos.TipoQuery = "updWorker";
    datos.usuario = appUserGetDatosToDatabase();
    try{
      const resp = await appAsynFetch(datos,rutaSQL);
      if(!resp.error) { appWorkersBotonCancel(); }
    } catch(err) {
      console.error('Error al cargar datos:', err);
    }
  }
}

function appWorkerBotonNuevo(){
  Persona.openBuscar('VerifyWorker',rutaSQL,true,true,false);
  $('#btn_modPersInsert').off('click').on('click',handlerWorkersInsert_Click);
  $('#btn_modPersAddToForm').off('click').on('click',handlerWorkersAddToForm_Click);
}

async function handlerWorkersInsert_Click(e){
  if(Persona.sinErrores()){ 
    try{
      const resp = await Persona.ejecutaSQL();
      appPersonaSetData(resp.tablaPers);

      const rpta = await appAsynFetch({TipoQuery:'startWorker'},rutaSQL);
      appWorkerClear(rpta);
      appUserClear(rpta);

      $('#grid').hide();
      $('#edit').show();
      Persona.close();
      e.stopImmediatePropagation();
      $('#btn_modPersInsert').off('click');
    } catch(err){
      console.error('Error al cargar datos:', err);
    }
  } else {
    alert("!!!Faltan llenar Datos!!!");
  }
}

async function handlerWorkersAddToForm_Click(e){
  try{
    const resp = await appAsynFetch({TipoQuery:'viewPersona', personaID:Persona.tablaPers.ID, fullQuery:2 }, 'pages/master/personas/sql.php');
    appPersonaSetData(Persona.tablaPers); //pestaña Personales

    const rpta = await appAsynFetch({TipoQuery : 'startWorker'},rutaSQL);
    appWorkerClear(rpta);
    appUserClear(rpta);
    $('#grid').hide();
    $('#edit').show();
    Persona.close();
    e.stopImmediatePropagation();
    $('#btn_modPersAddToForm').off('click');
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appWorkersBotonBorrar(){
  const arr = $('[name="chk_Borrar"]:checked').map(function() { return this.value}).get();
  if(arr.length>0){
    if(confirm("¿Esta seguro de continuar?")) {
      try{
        const resp = await appAsynFetch({ TipoQuery:'delWorkers', arr:arr },rutaSQL);
        if(!resp.error) { appWorkersBotonCancel(); }
      } catch(err){
        console.error('Error al cargar datos:', err);
      }
    }
  } else {
    alert("NO eligio borrar ninguno");
  }
}

async function appWorkerView(personaID){
  //tabs default en primer tab
  $('.nav-tabs li').removeClass('active');
  $('.tab-content .tab-pane').removeClass('active');
  $('a[href="#datosWorker"]').closest('li').addClass('active');
  $('#datosWorker').addClass('active');

  try{
    const resp = await appAsynFetch({
      TipoQuery : 'viewWorker',
      personaID : personaID,
      fullQuery : 2
    },rutaSQL);
    //respuesta
    appPersonaSetData(resp.tablaPers); //pestaña Personales
    appWorkerSetData(resp.tablaWorker);  //pestaña Empleado
    appUserSetData(resp.tablaUser); //pestaña usuario
      
    $("#div_WorkerAuditoria").show();
    $("#btnUpdate").toggle(menu.mtto.submenu.empleados.cmdUpdate == 1);
    $("#btnInsert").hide();

    $('#grid').hide();
    $('#edit').show();
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appWorkerSetData(data){
  //info corta
  $("#lbl_Codigo").html(data.codigo);
  $("#lbl_Agencia").html(data.agencia);
  //pestaña de Empleado
  appLlenarDataEnComboBox(data.comboAgencias,"#cbo_WorkerAgencia",data.agenciaID);
  appLlenarDataEnComboBox(data.comboCargos,"#cbo_WorkerCargo",data.cargoID);
  $('#txt_WorkerFechaIng').val(moment(data.fecha_ing).format("DD/MM/YYYY"));
  $("#txt_WorkerCodigo").val(data.codigo);
  $("#txt_WorkerNombreCorto").val(data.nombrecorto);
  $("#txt_WorkerCorreo").val(data.correo);
  $("#txt_WorkerObserv").val(data.observac);
  $("#lbl_WorkerSysFecha").html(moment(data.sys_fecha).format("DD/MM/YYYY"));
  $("#lbl_WorkerSysUser").html(data.usermod);
}

function appWorkerClear(data){
  //todos los inputs sin error y panel error deshabilitado
  $('.form-group').removeClass('has-error');
  $("#div_WorkerAuditoria").hide();
  $("#btnInsert").toggle(menu.mtto.submenu.empleados.cmdInsert==1);
  $("#btnUpdate").hide();

  //tabs default en primer tab
  $('.nav-tabs li').removeClass('active');
  $('.tab-content .tab-pane').removeClass('active');
  $('a[href="#datosWorker"]').closest('li').addClass('active');
  $('#datosWorker').addClass('active');

  //pestaña de Empleado
  appLlenarDataEnComboBox(data.comboAgencias,"#cbo_WorkerAgencia",0);
  appLlenarDataEnComboBox(data.comboCargos,"#cbo_WorkerCargo",0);
  $('#txt_WorkerFechaIng').val(moment(data.fecha).format("DD/MM/YYYY"));
  $("#txt_WorkerCodigo").val("").attr("placeholder", "00-000000");
  $("#txt_WorkerNombreCorto, #txt_WorkerObserv").val("");
}

function appWorkerGetDatosToDatabase(){
  let rpta = null;
  let esError = false;

  $('.form-group').removeClass('has-error');
  if($("#txt_WorkerNombreCorto").val()==="") { 
    $("#div_WorkerNombreCorto").addClass("has-error"); 
    esError = true; 
    alert("!!!Falta Nombre Corto en el Empleado!!!");
  }

  if(!esError){
    rpta = {
      workerID : $("#lbl_ID").html(),
      agenciaID : $("#cbo_WorkerAgencia").val(),
      cargoID : $("#cbo_WorkerCargo").val(),
      nombrecorto : $("#txt_WorkerNombreCorto").val(),
      correo : $("#txt_WorkerCorreo").val(),
      fecha : appConvertToFecha($("#txt_WorkerFechaIng").val(),""),
      observac : $("#txt_WorkerObserv").val(),
      usuario : null
    }
  }
  return rpta;
}

function appPersonaSetData(data){
  //info corta
  $('#img_Foto').attr('src', data.urlfoto === "" ? "data/personas/fotouser.jpg" : data.urlfoto);
  $('#lbl_Nombres').text(data.nombres);
  $('#lbl_Apellidos').text(data.ap_paterno + " " + data.ap_materno);
  $('#lbl_ID').text(data.ID);
  $('#lbl_TipoDNI').text(data.tipoDUI);
  $('#lbl_DNI').text(data.nroDUI);
  $('#lbl_Celular').text(data.celular);

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
  $("#btn_PersUpdate").show();
  $("#btn_PersPermiso").hide();
}

function appPersonaEditar(){
  Persona.editar($('#lbl_ID').html(),'S');
  $('#btn_modPersUpdate').on('click',async function(e) {
    if(Persona.sinErrores()){ //sin errores
      try{
        const resp = await Persona.ejecutaSQL();
        appPersonaSetData(resp.tablaPers);
        Persona.close();
        e.stopImmediatePropagation();
        $('#btn_modPersUpdate').off('click');
      } catch(err){
        console.error('Error al cargar datos:', err);
      }
    } else {
      alert("!!!Faltan llenar Datos!!!");
    }
  });
}

function appUserSetData(data){
  if(data.ID!=null){
    $("#chk_UserEsUsuario").prop("checked", true);
    $("#txt_UserLogin").val(data.login);
    $("#txt_UserPassword, #txt_UserRePassword").val('demo');
    $("#div_UserPassword, #div_UserRePassword").hide();
    appLlenarDataEnComboBox(data.comboRoles,"#cbo_UserRol",data.rolID);

    zMnuEmpleado = data.menu = JSON.parse(data.menu);
    appUserEsUsuario();
  } else { //no tiene usuario
    zMnuEmpleado = null;
    appUserClear(data);
  }
}

function appUserClear(data){
  appLlenarDataEnComboBox(data.comboRoles,"#cbo_UserRol",0);
  $("#chk_UserEsUsuario").prop("checked", false);
  $('#txt_UserLogin, #txt_UserPassword, #txt_UserRePassword').val("");
  $("#div_UserPassword, #div_UserRePassword").show();
  appUserEsUsuario();
}

async function appUserCambioPassw(userID){
  $("#modalChangePassw").modal("show");
  $("#txt_PassPassNew, #txt_PassPassRe").value("");
  try{
    const resp = await appAsynFetch({ TipoQuery:"selUserPass",  userID:userID }, rutaSQL);

    //respuesta
    $("#hid_PassID").val(resp.ID);
    $("#lbl_PassNombrecorto").html(resp.nombrecorto);
    $("#lbl_PassLogin").html(resp.login);
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function modUserBotonUpdatePassw(){
  if($("#txt_PassPassNew").val()!=""){
    if($("#txt_PassPassNew").val()===$("#txt_PassPassRe").val()){
      try{
        const resp = await appAsynFetch({
          TipoQuery:"changeUserPass",
          userID : $("#hid_PassID").val(),
          passw : SHA1($("#txt_PassPassNew").val()).toString().toUpperCase()
        }, rutaSQL);
        
        //respuesta
        if (!resp.error) { 
          alert("El PASSWORD se modifico correctamente");
          $("#modalChangePassw").modal("hide");
        }
      } catch(err){
        console.error('Error al cargar datos:', err);
      }
    } else {
      alert("!!!El PASSWORD es distintos en ambos campos!!!");
    }
  } else {
    alert("!!!NO pueden quedar vacios los campos!!!");
  }
}

function appUserEsUsuario(){
  const estado = $("#chk_UserEsUsuario").is(":checked");
  $("#txt_UserLogin, #txt_UserPassword, #txt_UserRePassword, #cbo_UserRol, #btn_UserPerfilRoot, #btn_UserPerfilCaja").prop("disabled", !estado);
  
  //menu
  zTreeObj = $.fn.zTree.init($("#appTreeView"), zSetting, (estado==false)?(null):(transformData(zMnuEmpleado)));
}

function appUserGetDatosToDatabase(){
  let rpta = null;
  let esError = false;
  let esUsuario = $("#chk_UserEsUsuario").is(":checked");

  $('.form-group').removeClass('has-error');
  if(esUsuario){
    if($("#txt_UserLogin").val()==="") { $("#div_UserLogin").addClass("has-error"); esError = true; }
    if($("#txt_UserPassword").val()==="") { $("#div_UserPassword").addClass("has-error"); esError = true; }
    if($("#txt_UserRePassword").val() != $("#txt_UserPassword").val()) { 
      $("#div_UserPassword").addClass("has-error");
      $("#div_UserRePassword").addClass("has-error");
      alert("el Password NO coincide");
      esError = true;
    }
    if(zTreeObj.getNodes().length==0) { alert("Debe configurar un PERFIL de usuario"); esError = true; }
    
  }

  if(esError==false && esUsuario==true){
    rpta = {
      login : $("#txt_UserLogin").val(),
      passw : SHA1($("#txt_UserPassword").val()).toString().toUpperCase(),
      rolID : $("#cbo_UserRol").val(),
      menu : JSON.stringify(getTreeJSON(zTreeObj))
    }
  }
  return rpta;
}

async function appUserPerfilMenu(perfilID){
  try{
    const resp = await appAsynFetch({
      TipoQuery : "selSisMenu",
      perfilID : perfilID
    }, rutaSQL);
    
    const mnu = JSON.parse(resp.menu);
    zTreeObj = $.fn.zTree.init($("#appTreeView"), zSetting, transformData(mnu));
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}


//menu
function beforeDrag(treeId, treeNodes) { return false; }

function transformData(data) {
  var nodes = [];
  
  for (var key in data) {
    if (typeof data[key] === 'object') {
      let node = {
        name: key,
        nocheck : true,
        children: transformData(data[key])
      };
      nodes.push(node);
    } else {
      let node ={
        name : ($.isNumeric(data[key]))?key:key+" : "+data[key],
        nocheck : ($.isNumeric(data[key]))?false:true,
        checked : data[key]
      }
      nodes.push(node);
    }
  }
  return nodes;
}

function showIconForTree(treeId, treeNode) {
  return treeNode.nocheck;
};

function getTreeJSON(treeOBJ){
  var arr = {};
  for(let node of treeOBJ.getNodes()){
    if(node.children){ arr[node.name] = getTreeNodes(node.children) }
  }
  return arr;
}

function getTreeNodes(nodes) {
  let arr = {};
  for (let node of nodes) {
    let cad = node.name.split(":");
    if(node.children) { 
      arr[$.trim(cad[0])] = getTreeNodes(node.children); 
    } else {
      if(node.nocheck){
        arr[$.trim(cad[0])] = $.trim(cad[1]);
      } else {
        arr[$.trim(cad[0])] = (node.checked)?1:0;
      }
    }
  }
  return arr;
}
