const rutaSQL = "pages/mtto/socios/sql.php";
var menu = "";

//=========================funciones para Personas============================
async function appSociosGrid(){
  document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="8"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  document.querySelector("#chk_All").disabled = (menu.mtto.submenu.socios.cmdDelete===1) ? false : true;
  const disabledDelete = (menu.mtto.submenu.socios.cmdDelete===1) ? "" : "disabled";
  const txtBuscar = document.querySelector("#txtBuscar").value.toUpperCase();
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
      document.querySelector('#grdDatos').innerHTML = (fila);
    } else {
      let res = (txtBuscar==="") ? ("") : ("para "+txtBuscar);
      document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="8" style="text-align:center;color:red;">Sin Resultados '+(res)+'</td></tr>');
    }
    document.querySelector('#grdCount').innerHTML = (resp.tabla.length+"/"+resp.cuenta);
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appSociosReset(){
  document.querySelector("#txtBuscar").value = ("");
  document.querySelector("#grdDatos").innerHTML = ("");
  try{
    const resp = await appAsynFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php");
    menu = JSON.parse(resp.menu);
    document.querySelector("#btn_DEL").style.display = (menu.mtto.submenu.socios.cmdDelete==1)?('inline'):('none');
    document.querySelector("#btn_NEW").style.display = (menu.mtto.submenu.socios.cmdInsert==1)?('inline'):('none');
    document.querySelector("#div_PersAuditoria").style.display = ((resp.rolID==101)?('block'):('none'));
    appSociosGrid();
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appSociosBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { appSociosGrid(); }
}

function appSociosBotonCancel(){
  appSociosGrid();
  document.querySelector("#grid").style.display = 'block';
  document.querySelector("#edit").style.display = 'none';
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
  $('#btn_modPersInsert').on('click',async function(e) {
    if(Persona.sinErrores()){ //sin errores
      document.querySelector('#grid').style.display = 'none';
      document.querySelector('#edit').style.display = 'block';
      try{
        const resp = await Persona.ejecutaSQL();
        appPersonaSetData(resp.tablaPers);
        appLaboralClear();
        appConyugeClear();
        appSocioClear();
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
  $('#btn_modPersAddToForm').on('click',async function(e) {
    try{
      const resp = await appAsynFetch({
        TipoQuery : 'viewPersona',
        personaID : Persona.tablaPers.ID,
        fullQuery : 2
      },'pages/mtto/personas/sql.php');

      document.querySelector('#grid').style.display = 'none';
      document.querySelector('#edit').style.display = 'block';
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
  let arr = Array.from(document.querySelectorAll('[name="chk_Borrar"]:checked')).map(function(obj){return obj.attributes[2].nodeValue});
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
  document.querySelector("#div_SocAuditoria").style.display = 'block';
  document.querySelector("#btnUpdate").style.display = (menu.mtto.submenu.socios.cmdUpdate==1)?('inline'):('none');
  document.querySelector("#btnInsert").style.display = 'none';
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
    document.querySelector('#grid').style.display = 'none';
    document.querySelector('#edit').style.display = 'block';
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appSocioSetData(data){
  //info corta
  document.querySelector("#lbl_Codigo").innerHTML = (data.codigo);
  document.querySelector("#lbl_Agencia").innerHTML = (data.agencia);
  //pestaña de socio
  appLlenarDataEnComboBox(data.comboAgencias,"#cbo_SocAgencia",data.agenciaID);
  document.querySelector('#txt_SocFechaIng').value = (moment(data.fecha).format("DD/MM/YYYY"));
  document.querySelector("#txt_SocCodigo").value = (data.codigo);
  document.querySelector("#txt_SocObserv").value = (data.observac);
  document.querySelector("#lbl_SocSysFecha").innerHTML = (moment(data.sys_fecha).format("DD/MM/YYYY"));
  document.querySelector("#lbl_SocSysUser").innerHTML = (data.usermod);
}

async function appSocioClear(){
  //tabs default en primer tab
  $('.nav-tabs li').removeClass('active');
  $('.tab-content .tab-pane').removeClass('active');
  $('a[href="#datosSocio"]').closest('li').addClass('active');
  $('#datosSocio').addClass('active');

  //todos los inputs sin error y panel error deshabilitado
  $('.form-group').removeClass('has-error');
  document.querySelector("#div_SocAuditoria").style.display = 'none';
  document.querySelector("#btnInsert").style.display = (menu.mtto.submenu.socios.cmdInsert==1)?('inline'):('none');
  document.querySelector("#btnUpdate").style.display = 'none';
  document.querySelector("#txt_SocCodigo").placeholder = ("00-000000");
  document.querySelector("#txt_SocCodigo").value = ("");
  document.querySelector("#txt_SocObserv").value = ("");

  try{
    const resp = await appAsynFetch({
      TipoQuery : 'startSocio'
    }, rutaSQL);
    //pestaña de socio
    appLlenarDataEnComboBox(resp.comboAgencias,"#cbo_SocAgencia",0);
    document.querySelector('#txt_SocFechaIng').value = (moment(resp.fecha).format("DD/MM/YYYY"));
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
      socioID : document.querySelector("#lbl_ID").innerHTML,
      socCodigo : "",
      socFecha : appConvertToFecha(document.querySelector("#txt_SocFechaIng").value,""),
      socAgenciaID : document.querySelector("#cbo_SocAgencia").value,
      socObservac : document.querySelector("#txt_SocObserv").value
    }
  }
  return datos;
}

function appPersonaSetData(data){
  //permisos
  document.querySelector("#btn_PersUpdate").style.display = 'block';
  document.querySelector("#btn_PersPermiso").style.display = 'none';

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
}

function appPersonaEditar(){
  Persona.editar(document.querySelector('#lbl_ID').innerHTML,'S');
  $('#btn_modPersUpdate').on('click',async function(e) {
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
  document.querySelector("#grdLaboDatosBody").innerHTML = '';
  document.querySelector("#div_LaboEdit").style.display = 'none';
  document.querySelector("#btn_LaboDelete").style.display = 'none';
  document.querySelector("#btn_LaboUpdate").style.display = 'none';
  document.querySelector("#btn_LaboPermiso").style.display = 'none';
  document.querySelector("#btn_LaboInsert").style.display = 'inline';
}

function appLaboralNuevo(){
  Laboral.nuevo(document.querySelector('#lbl_ID').innerHTML);
  $('#btn_modLaboInsert').on('click',async function(e) {
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
  $('#btn_modLaboUpdate').on('click',async function(e) {
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
    const personaID = document.querySelector("#lbl_ID").innerHTML;
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
    document.querySelector("#lbl_ConyNombres").innerHTML = (conyuge.nombres);
    document.querySelector("#lbl_ConyApellidos").innerHTML = (conyuge.ap_paterno+' '+conyuge.ap_materno);
    document.querySelector("#lbl_ConyTipoDNI").innerHTML = (conyuge.tipoDUI);
    document.querySelector("#lbl_ConyNroDNI").innerHTML = (conyuge.nroDUI);
    document.querySelector("#lbl_ConyFechaNac").innerHTML = (moment(conyuge.fechanac).format("DD/MM/YYYY"));
    document.querySelector("#lbl_ConyEcivil").innerHTML = (conyuge.ecivil);
    document.querySelector("#lbl_ConyCelular").innerHTML = (conyuge.celular);
    document.querySelector("#lbl_ConyTelefijo").innerHTML = (conyuge.telefijo);
    document.querySelector("#lbl_ConyEmail").innerHTML = (conyuge.correo);
    document.querySelector("#lbl_ConyGInstruccion").innerHTML = (conyuge.ginstruc);
    document.querySelector("#lbl_ConyProfesion").innerHTML = (conyuge.profesion);
    document.querySelector("#lbl_ConyOcupacion").innerHTML = (conyuge.ocupacion);
    document.querySelector("#lbl_ConyUbicacion").innerHTML = (conyuge.region+" - "+conyuge.provincia+" - "+conyuge.distrito);
    document.querySelector("#lbl_ConyDireccion").innerHTML = (conyuge.direccion);
    document.querySelector("#lbl_ConyReferencia").innerHTML = (conyuge.referencia);
    document.querySelector("#lbl_ConyMedidorluz").innerHTML = (conyuge.medidorluz);
    document.querySelector("#lbl_ConyMedidoragua").innerHTML = (conyuge.medidoragua);
    document.querySelector("#lbl_ConyTipovivienda").innerHTML = (conyuge.tipovivienda);
    document.querySelector("#div_Conyuge").style.display = 'block';
    document.querySelector("#lbl_ConyTiempoRel").innerHTML = (data.tiempoRelacion+(" (años)"));
    //permisos
    document.querySelector("#btn_ConyInsert").style.display = 'none';
    document.querySelector("#btn_ConyDelete").style.display = 'inline';
    document.querySelector("#btn_ConyUpdate").style.display = 'inline';
    document.querySelector("#btn_ConyPermiso").style.display = 'none';
  } else {
    appConyugeClear();
  }
}

function appConyugeClear(){
  $('.form-group').removeClass('has-error');
  document.querySelector("#lbl_ConyTiempoRel").value = ("0");

  document.querySelector("#div_Conyuge").style.display = 'none';
  document.querySelector("#btn_ConyDelete").style.display = 'none';
  document.querySelector("#btn_ConyUpdate").style.display = 'none';
  document.querySelector("#btn_ConyPermiso").style.display = 'none';
  document.querySelector("#btn_ConyInsert").style.display = 'inline';
}

function appConyugeNuevo(){
  Conyuge.nuevo(document.querySelector('#lbl_ID').innerHTML,rutaSQL);
  $('#btn_modConyInsert').on('click',async function(e) {
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
  Conyuge.editar(document.querySelector('#lbl_ID').innerHTML);
  $('#btn_modConyUpdate').on('click',async function(e) {
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
      const resp = await Conyuge.borrar(document.querySelector('#lbl_ID').innerHTML);
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
