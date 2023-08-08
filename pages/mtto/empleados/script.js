const rutaSQL = "pages/mtto/empleados/sql.php";
var menu = "";

//=========================funciones============================
function appWorkersGrid(){
  document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="9"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  let txtBuscar = document.querySelector("#txtBuscar").value.toUpperCase();
  let datos = { TipoQuery: 'selWorkers', buscar: txtBuscar };

  appFetch(datos,rutaSQL).then(resp => {
    let disabledDelete = (menu.mtto.submenu.empleados.cmdDelete===1) ? "" : "disabled";
    document.querySelector("#chk_All").disabled = (menu.mtto.submenu.empleados.cmdDelete===1) ? false : true;
    if(resp.tabla.length>0){
      let fila = "";
      resp.tabla.forEach((valor,key)=>{
        fila += '<tr>';
        fila += '<td><input type="checkbox" name="chk_Borrar" value="'+(valor.ID)+'" '+disabledDelete+'/></td>';
        fila += '<td><i class="fa fa-paperclip"></i></td>';
        fila += '<td>'+((valor.login!=null)?('<a href="javascript:appUserCambioPassw('+(valor.ID)+')"><i class="fa fa-lock"></i></a>'):(''))+'</td>';
        fila += '<td>'+(valor.codigo)+'</td>';
        fila += '<td>'+(valor.nro_dui)+'</td>';
        fila += '<td><a href="javascript:appWorkerView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.empleado)+'</a></td>';
        fila += '<td>'+(valor.nombrecorto)+'</td>';
        fila += '<td>'+(valor.cargo)+'</td>';
        fila += '<td>'+(valor.agencia)+'</td>';
        fila += '</tr>';
      });
      document.querySelector('#grdDatos').innerHTML = (fila);
    } else {
      let res = (txtBuscar==="") ? ("") : ("para "+txtBuscar);
      document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="9" style="text-align:center;color:red;">Sin Resultados '+(res)+'</td></tr>');
    }
    document.querySelector('#grdCount').innerHTML = (resp.tabla.length+"/"+resp.cuenta);
  });
}

function appWorkersReset(){
  appFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php").then(resp => {
    menu = JSON.parse(resp.menu);
    //console.log(menu);
    mnuTree = "";
    /*
    for(let prop in menu){
      console.log(prop+':'+menu[prop]);
    };
    */

    document.querySelector("#btn_DEL").style.display = (menu.mtto.submenu.empleados.cmdDelete==1)?('inline'):('none');
    document.querySelector("#btn_NEW").style.display = (menu.mtto.submenu.empleados.cmdInsert==1)?('inline'):('none');

    document.querySelector("#txtBuscar").value = ("");
    document.querySelector("#grdDatos").innerHTML = ("");
    document.querySelector("#div_PersAuditoria").style.display = ((resp.rolID==101)?('block'):('none'));
    appWorkersGrid();
  });
}

function appWorkersBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { appWorkersGrid(); }
}

function appWorkersBotonCancel(){
  appWorkersGrid();
  document.querySelector("#grid").style.display = 'block';
  document.querySelector("#edit").style.display = 'none';
}

function appWorkersBotonInsert(){
  let datos = appWorkerGetDatosToDatabase();
  
  if(datos!=""){
    datos.TipoQuery = "insWorker";
    appFetch(datos,rutaSQL).then(resp => {
      appWorkersBotonCancel();
    });
  }
}

function appWorkersBotonUpdate(){
  // let datos = appWorkerGetDatosToDatabase();

  // if(datos!=""){
  //   datos.TipoQuery = "updWorker";
  //   datos.workerID = document.querySelector("#lbl_ID").innerHTML;
  //   appFetch(datos,rutaSQL).then(resp => {
  //     appWorkersBotonCancel();
  //   });
  // }
  let xx = getTreeJSON($.fn.zTree.getZTreeObj("appTreeView"));
  console.log(JSON.stringify(xx));
}

function appWorkerBotonNuevo(){
  Persona.openBuscar('VerifyWorker',rutaSQL,true,true,false);
  $('#btn_modPersInsert').on('click',function(e) {
    if(Persona.sinErrores()){ //sin errores
      Persona.ejecutaSQL().then(resp => {
        appPersonaSetData(resp.tablaPers);
        appWorkerClear();
        document.querySelector('#grid').style.display = 'none';
        document.querySelector('#edit').style.display = 'block';
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
      TipoQuery : 'viewPersona',
      personaID : Persona.tablaPers.ID,
      fullQuery : 2
    }
    appFetch(datos,'pages/mtto/personas/sql.php').then(resp => {
      appPersonaSetData(Persona.tablaPers); //pestaña Personales
      appWorkerClear();
      document.querySelector('#grid').style.display = 'none';
      document.querySelector('#edit').style.display = 'block';
      Persona.close();
    });
    e.stopImmediatePropagation();
    $('#btn_modPersAddToForm').off('click');
  });
}

function appWorkersBotonBorrar(){
  let arr = Array.from(document.querySelectorAll('[name="chk_Borrar"]:checked')).map(function(obj){return obj.attributes[2].nodeValue});
  if(arr.length>0){
    if(confirm("¿Esta seguro de continuar?")) {
      appFetch({ TipoQuery:'delWorkers', arr:arr },rutaSQL).then(resp => {
        if (resp.error == false) { //sin errores
          console.log(resp);
          appWorkersBotonCancel();
        }
      });
    }
  } else {
    alert("NO eligio borrar ninguno");
  }
}

function appWorkerView(personaID){
  let datos = {
    TipoQuery : 'viewWorker',
    personaID : personaID,
    fullQuery : 2
  };
  
  appFetch(datos,rutaSQL).then(resp => {
    //console.log(resp);
    appWorkerSetData(resp.tablaWorker);  //pestaña Empleado
    appPersonaSetData(resp.tablaPers); //pestaña Personales
    appUserSetData(resp.tablaUser); //pestaña usuario

    //tabs default en primer tab
    $('.nav-tabs li').removeClass('active');
    $('.tab-content .tab-pane').removeClass('active');
    $('a[href="#datosWorker"]').closest('li').addClass('active');
    $('#datosWorker').addClass('active');
    document.querySelector("#div_WorkerAuditoria").style.display = 'block';
    document.querySelector("#btnUpdate").style.display = (menu.mtto.submenu.empleados.cmdUpdate==1)?('inline'):('none');
    document.querySelector("#btnInsert").style.display = 'none';

    document.querySelector('#grid').style.display = 'none';
    document.querySelector('#edit').style.display = 'block';
  });
}

function appWorkerSetData(data){
  //info corta
  document.querySelector("#lbl_Codigo").innerHTML = (data.codigo);
  document.querySelector("#lbl_Agencia").innerHTML = (data.agencia);
  //pestaña de Empleado
  appLlenarDataEnComboBox(data.comboAgencias,"#cbo_WorkerAgencia",data.agenciaID);
  appLlenarDataEnComboBox(data.comboCargos,"#cbo_WorkerCargo",data.cargoID);
  document.querySelector('#txt_WorkerFechaIng').value = (moment(data.fecha_ing).format("DD/MM/YYYY"));
  document.querySelector("#txt_WorkerCodigo").value = (data.codigo);
  document.querySelector("#txt_WorkerNombreCorto").value = (data.nombrecorto);
  document.querySelector("#txt_WorkerCorreo").value = (data.correo);
  document.querySelector("#txt_WorkerObserv").value = (data.observac);
  document.querySelector("#lbl_WorkerSysFecha").innerHTML = (moment(data.sys_fecha).format("DD/MM/YYYY"));
  document.querySelector("#lbl_WorkerSysUser").innerHTML = (data.usermod);
}

function appWorkerClear(){
  let datos = {
    TipoQuery : 'startWorker'
  }
  //todos los inputs sin error y panel error deshabilitado
  $('.form-group').removeClass('has-error');
  document.querySelector("#div_WorkerAuditoria").style.display = 'none';
  document.querySelector("#btnInsert").style.display = (menu.mtto.submenu.empleados.cmdInsert==1)?('inline'):('none');
  document.querySelector("#btnUpdate").style.display = 'none';

  //tabs default en primer tab
  $('.nav-tabs li').removeClass('active');
  $('.tab-content .tab-pane').removeClass('active');
  $('a[href="#datosWorker"]').closest('li').addClass('active');
  $('#datosWorker').addClass('active');

  appFetch(datos,rutaSQL).then(resp => {
    //pestaña de Empleado
    appLlenarDataEnComboBox(resp.comboAgencias,"#cbo_WorkerAgencia",0);
    appLlenarDataEnComboBox(resp.comboCargos,"#cbo_WorkerCargo",0);
    document.querySelector('#txt_WorkerFechaIng').value = (moment(resp.fecha).format("DD/MM/YYYY"));
    document.querySelector("#txt_WorkerCodigo").placeholder = ("00-000000");
    document.querySelector("#txt_WorkerCodigo").value = ("");
    document.querySelector("#txt_WorkerNombreCorto").value = ("");
    document.querySelector("#txt_WorkerObserv").value = ("");
    appUserClear(resp.comboRoles);
  });
}

function appWorkerGetDatosToDatabase(){
  let rpta = "";
  let esError = false;
  $('.form-group').removeClass('has-error');
  if(document.querySelector("#txt_WorkerNombreCorto").value=="") { document.querySelector("#div_WorkerNombreCorto").className = "form-group has-error"; esError = true; }

  if(!esError){
    rpta = {
      workerID : document.querySelector("#lbl_ID").innerHTML,
      agenciaID : document.querySelector("#cbo_WorkerAgencia").value,
      cargoID : document.querySelector("#cbo_WorkerCargo").value,
      nombrecorto : document.querySelector("#txt_WorkerNombreCorto").value,
      correo : document.querySelector("#txt_WorkerCorreo").value,
      fecha : appConvertToFecha(document.querySelector("#txt_WorkerFechaIng").value,""),
      observac : document.querySelector("#txt_WorkerObserv").value
    }
  }
  return rpta;
}

function appPersonaSetData(data){
  //info corta
  document.querySelector('#img_Foto').src = (data.urlfoto=="")?("data/personas/fotouser.jpg"):(data.urlfoto);
  document.querySelector("#lbl_Nombres").innerHTML = (data.nombres);
  document.querySelector("#lbl_Apellidos").innerHTML = (data.ap_paterno+" "+data.ap_materno);
  document.querySelector("#lbl_ID").innerHTML = (data.ID);
  document.querySelector("#lbl_TipoDNI").innerHTML = (data.tipoDUI);
  document.querySelector("#lbl_DNI").innerHTML = (data.nroDUI);
  document.querySelector("#lbl_Celular").innerHTML = (data.celular);

  //pestaña datos personales
  if(data.tipoPersona==2){ //persona juridica
    document.querySelector("#lbl_PersTipoNombres").innerHTML = ("Razon Social");
    document.querySelector("#lbl_PersTipoProfesion").innerHTML = ("Rubro");
    document.querySelector("#lbl_PersTipoApellidos").style.display = 'none';
    document.querySelector("#lbl_PersTipoSexo").style.display = 'none';
    document.querySelector("#lbl_PersTipoECivil").style.display = 'none';
    document.querySelector("#lbl_PersTipoGIntruc").style.display = 'none';
  }else{
    document.querySelector("#lbl_PersTipoNombres").innerHTML = ("Nombres");
    document.querySelector("#lbl_PersTipoProfesion").innerHTML = ("Profesion");
    document.querySelector("#lbl_PersTipoApellidos").style.display = 'block';
    document.querySelector("#lbl_PersTipoSexo").style.display = 'block';
    document.querySelector("#lbl_PersTipoECivil").style.display = 'block';
    document.querySelector("#lbl_PersTipoGIntruc").style.display = 'block';
  }
  document.querySelector("#lbl_PersNombres").innerHTML = (data.nombres);
  document.querySelector("#lbl_PersApellidos").innerHTML = (data.ap_paterno+" "+data.ap_materno);
  document.querySelector("#lbl_PersTipoDNI").innerHTML = (data.tipoDUI);
  document.querySelector("#lbl_PersNroDNI").innerHTML = (data.nroDUI);
  document.querySelector("#lbl_PersFechaNac").innerHTML = (moment(data.fechanac).format("DD/MM/YYYY"));
  document.querySelector("#lbl_PersEdad").innerHTML = (moment().diff(moment(data.fechanac),"years")+" años");
  document.querySelector("#lbl_PersPaisNac").innerHTML = (data.paisnac);
  document.querySelector("#lbl_PersLugarNac").innerHTML = (data.lugarnac);
  document.querySelector("#lbl_PersSexo").innerHTML = (data.sexo);
  document.querySelector("#lbl_PersEcivil").innerHTML = (data.ecivil);
  document.querySelector("#lbl_PersCelular").innerHTML = (data.celular);
  document.querySelector("#lbl_PersTelefijo").innerHTML = (data.telefijo);
  document.querySelector("#lbl_PersEmail").innerHTML = (data.correo);
  document.querySelector("#lbl_PersGInstruccion").innerHTML = (data.ginstruc);
  document.querySelector("#lbl_PersProfesion").innerHTML = (data.profesion);
  document.querySelector("#lbl_PersOcupacion").innerHTML = (data.ocupacion);
  document.querySelector("#lbl_PersUbicacion").innerHTML = (data.region+" - "+data.provincia+" - "+data.distrito);
  document.querySelector("#lbl_PersDireccion").innerHTML = (data.direccion);
  document.querySelector("#lbl_PersReferencia").innerHTML = (data.referencia);
  document.querySelector("#lbl_PersMedidorluz").innerHTML = (data.medidorluz);
  document.querySelector("#lbl_PersMedidorAgua").innerHTML = (data.medidoragua);
  document.querySelector("#lbl_PersTipovivienda").innerHTML = (data.tipovivienda);
  document.querySelector("#lbl_PersObservac").innerHTML = (data.observPers);
  document.querySelector("#lbl_PersSysFecha").innerHTML = (moment(data.sysfechaPers).format("DD/MM/YYYY HH:mm:ss"));
  document.querySelector("#lbl_PersSysUser").innerHTML = (data.sysuserPers);

  //permisos
  document.querySelector("#btn_PersUpdate").style.display = 'block';
  document.querySelector("#btn_PersPermiso").style.display = 'none';
}

function appPersonaEditar(){
  Persona.editar(document.querySelector('#lbl_ID').innerHTML,'S');
  $('#btn_modPersUpdate').on('click',function(e) {
    if(Persona.sinErrores()){ //sin errores
      Persona.ejecutaSQL().then(resp => {
        appPersonaSetData(resp.tablaPers);
        Persona.close();
      });
    } else {
      alert("!!!Faltan llenar Datos!!!");
    }
    e.stopImmediatePropagation();
    $('#btn_modPersUpdate').off('click');
  });
}

function appUserSetData(data){
  if(data.ID!=null){
    document.querySelector("#chk_UserEsUsuario").checked = true;
    document.querySelector("#txt_UserLogin").value = (data.login);
    document.querySelector("#div_UserPassword").style.display = 'none';
    document.querySelector("#div_UserRePassword").style.display = 'none';
    appLlenarDataEnComboBox(data.comboRoles,"#cbo_UserRol",data.rolID);
    
    data.menu = JSON.parse(data.menu);
    let setting = { 
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

    $.fn.zTree.init($("#appTreeView"), setting, transformData(data.menu));

    // Obtener el arbol y convertirlo a JSON
    //var zTreeObj = $.fn.zTree.getZTreeObj("appTreeView");
    //getTreeJSON(zTreeObj);
  } else {
    appUserClear(data.comboRoles);
  }
}

function appUserClear(data){
  appLlenarDataEnComboBox(data,"#cbo_UserRol",0);
  document.querySelector("#chk_UserEsUsuario").checked = false;
  document.querySelector('#txt_UserLogin').value = ("");
  document.querySelector('#txt_UserPassword').value = ("");
  document.querySelector('#txt_UserRePassword').value = ("");
  document.querySelector("#div_UserPassword").style.display = 'block';
  document.querySelector("#div_UserRePassword").style.display = 'block';
}

function appUserCambioPassw(userID){
  $("#modalChangePassw").modal("show");
  let datos = {
    TipoQuery:"viewUserPass",
    userID:userID
  }
  appFetch(datos,rutaSQL).then(resp => {
    document.querySelector("#hid_PassID").value = resp.ID;
    document.querySelector("#lbl_PassNombrecorto").innerHTML = resp.nombrecorto;
    document.querySelector("#lbl_PassLogin").innerHTML = resp.login;
    document.querySelector("#txt_PassPassNew").value = "";
    document.querySelector("#txt_PassPassRe").value = "";
  });
}

function modUserBotonUpdatePassw(){
  if(document.querySelector("#txt_userPassNew").value===document.querySelector("#txt_userPassRe").value){
    let datos = {
      TipoQuery:"changeUserPass",
      userID : document.querySelector("#hid_userID").value,
      passw : SHA1(document.querySelector("#txt_userPassNew").value).toString().toUpperCase()
    }
    appFetch(datos,rutaSQL).then(resp => {
      if (!resp.error) { 
        alert("El PASSWORD se modifico correctamente");
        $("#modalChangePassw").modal("hide");
      }
    });
  } else {
    alert("!!!El PASSWORD es distintos en ambos campos!!!");
  }
}

function appUserEsUsuario(){
  
}

//menu
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

function beforeDrag(treeId, treeNodes) { return false; }





function appFiltro(treeId, parentNode, childNodes) {
  return childNodes;
}

function refreshNode(e) {
  var zTree = $.fn.zTree.getZTreeObj("appTreeView");
  var nodes = zTree.getSelectedNodes();
  if (nodes.length == 0) {
    alert("!!!Debe seleccionar una UBICACION!!!");
  } else {
    switch(e.data.type){
      case "add": appNew(nodes[0]); break;
      case "edt": appEdit(nodes[0]); break;
    }
  }
}


//permisos para personas
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
