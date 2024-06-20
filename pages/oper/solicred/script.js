const rutaSQL = "pages/oper/solicred/sql.php";
var menu = "";

//=========================funciones para Personas============================
function appSoliCredBuscar(e){ if(e.keyCode === 13) { load_flag = 0; $('#grdDatosBody').html(""); appSoliCredGrid(); } }

async function appSoliCredGrid(){
  $('#grdDatos').html('<tr><td colspan="10"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  $("#chk_All").prop("disabled", !(menu.oper.submenu.solicred.cmdDelete === 1));
  const disabledDelete = (menu.oper.submenu.solicred.cmdDelete===1) ? "" : "disabled";
  const txtBuscar = $("#txtBuscar").val();

  try{
    const resp = await appAsynFetch({
      TipoQuery: 'selSoliCred',
      buscar: txtBuscar
    }, rutaSQL);

    //respuesta
    if(resp.tabla.length>0){
      let fila = "";
      resp.tabla.forEach((valor,key)=>{
        fila += '<tr>'+
                '<td><input type="checkbox" name="chk_Borrar" value="'+(valor.ID)+'" '+disabledDelete+'/></td>'+
                '<td>'+((menu.oper.submenu.solicred.aprueba===1)?('<a href="javascript:appSoliCredAprueba('+(valor.ID)+');"><i class="fa fa-thumbs-up" style="color:#FF0084;"></i></a>'):(''))+'</td>'+
                '<td>'+(moment(valor.fecha).format("DD/MM/YYYY"))+'</td>'+
                '<td>'+(valor.nro_dui)+'</td>'+
                '<td>'+(valor.socio)+'</td>'+
                '<td><a href="javascript:appSoliCredView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.codigo)+(' &raquo; ')+(valor.producto)+'; '+(valor.mon_abrevia)+'; '+appFormatMoney(valor.tasa,2)+'%</a></td>'+
                '<td>'+(valor.tiposbs)+'</td>'+
                '<td>'+(moment(valor.otorga).format("DD/MM/YYYY"))+'</td>'+
                '<td style="text-align:right;">'+appFormatMoney(valor.importe,2)+'</td>'+
                '<td style="text-align:center;">'+(valor.nro_cuotas)+'</td>'+
                '</tr>';
      });
      $('#grdDatos').html(fila);
    }else{
      let res = (txtBuscar==="") ? ("") : ("para "+txtBuscar);
      $('#grdDatos').html('<tr><td colspan="10" style="text-align:center;color:red;">Sin Resultados '+(res)+'</td></tr>');
    }
    $('#grdCount').html(resp.tabla.length+"/"+resp.cuenta);
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}

async function appSoliCredReset(){
  $("#txtBuscar").val("");
  
  try {
    const resp = await appAsynFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php");
    menu = JSON.parse(resp.menu);
    
    $("#btn_DEL").toggle(menu.oper.submenu.solicred.cmdDelete == 1);
    $("#btn_NEW").toggle(menu.oper.submenu.solicred.cmdInsert == 1);
    $("#div_PersAuditoria").toggle(resp.rolID==resp.rolROOT);
    appSoliCredGrid();
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}

function appSoliCredBotonCancel(){
  appSoliCredGrid();
  $('#grid').show();
  $('#edit').hide();
}

async function appSoliCredBotonInsert(){
  if(appSoliCredValidarCampos()){
    alert("¡¡¡Faltan datos!!!");
  } else {
    let datos = appSoliCredGetDatosToDatabase();
    datos.TipoExec = "INS";
    try{
      const resp = await appAsynFetch(datos,rutaSQL);
      if(!resp.error) { appSoliCredBotonCancel(); }
    } catch(err){
      console.error('Error al cargar datos:'+err);
    }
  }
}

async function appSoliCredBotonUpdate(){
  if(appSoliCredValidarCampos()){
    alert("¡¡¡Faltan datos!!!");
  } else {
    let datos = appSoliCredGetDatosToDatabase();
    datos.TipoExec = "UPD";

    try{
      const resp = await appAsynFetch(datos,rutaSQL);
      if(!resp.error) { appSoliCredBotonCancel(); }
    } catch(err){
      console.error('Error al cargar datos:'+err);
    }
  }
}

async function appSoliCredBotonNuevo(){
  Persona.openBuscar('VerifySoliCred',rutaSQL,false,true,true);
  $('#btn_modPersAddToForm').off('click').on('click',async function(e) {
    try{
      const resp = await appAsynFetch({
        TipoQuery : 'viewPersona',
        personaID : Persona.tablaPers.ID,
        fullQuery : 0
      }, 'pages/master/personas/sql.php');

      //respuesta
      appSoliCredClear(Persona.tablaPers.persona);
      appPersonaSetData(Persona.tablaPers); //pestaña Personales
      $("#grid").hide();
      $("#edit").show();
      Persona.close();
      e.stopImmediatePropagation();
    $('#btn_modPersAddToForm').off('click');
    } catch(err){
      console.error('Error al cargar datos:'+err);
    }
  });
}

async function appSoliCredBotonBorrar(){
  const arr = $('[name="chk_Borrar"]:checked').map(function() { return this.value}).get();
  if(arr.length>0){
    if(confirm("¿Esta seguro de continuar?")) {
      try{
        const resp = await appAsynFetch({ TipoQuery:'delSoliCred', arr:arr },rutaSQL);
        if (!resp.error) { appSoliCredBotonCancel(); }
      } catch(err){
        console.error('Error al cargar datos:'+err);
      }
    }
  } else {
    alert("NO eligio borrar ninguno");
  }
}

async function appSoliCredAprueba(solicredID){
  $("#modalAprueba").modal("show");
  try{
    const resp = await appAsynFetch({ TipoQuery:'viewApruebaSoliCred', SoliCredID:solicredID }, rutaSQL);

    //respuesta
    $("#txt_modApruebaFechaAprueba").prop("disabled", (resp.rolUser==resp.rolROOT) ? (false):(true));
    $("#txt_modApruebaFechaAprueba").datepicker("setDate",moment(resp.fecha_aprueba).format("DD/MM/YYYY"));

    $("#hid_modApruebaID").val(resp.ID);
    $("#lbl_modApruebaSocio").html(resp.socio);
    $("#lbl_modApruebaFechaSoliCred").html(moment(resp.fecha_solicred).format("DD/MM/YYYY"));
    $("#lbl_modApruebaCodigo").html(resp.codigo);
    $("#lbl_modApruebaMoneda").html(resp.moneda);
    $("#lbl_modApruebaClasifica").html(resp.clasifica);
    $("#lbl_modApruebaCondicion").html(resp.condicion);
    $("#lbl_modApruebaAgencia").html(resp.agencia);
    $("#lbl_modApruebaPromotor").html(resp.promotor);
    $("#lbl_modApruebaAnalista").html(resp.analista);
    $("#lbl_modApruebaTipoSBS").html(resp.tiposbs);
    $("#lbl_modApruebaDestinoSBS").html(resp.destsbs);
    $("#lbl_modApruebaTipoCredito").html(resp.tipocred);
    $("#lbl_modApruebaProducto").html(resp.producto);
    $("#lbl_modApruebaImporte").html(appFormatMoney(resp.importe,2));
    $("#lbl_modApruebaNrocuotas").html(resp.nrocuotas);
    $("#lbl_modApruebaTasaCred").html(appFormatMoney(resp.tasa,2));
    $("#lbl_modApruebaTasaMora").html(appFormatMoney(resp.mora,2));
    $("#lbl_modApruebaTasaDesgr").html(appFormatMoney(resp.desgr,2));
    $("#lbl_modApruebaFechaOtorga").html(moment(resp.fecha_otorga).format("DD/MM/YYYY"));
    $("#lbl_modApruebaFechaPriCuota").html(moment(resp.fecha_pricuota).format("DD/MM/YYYY"));
    $("#lbl_modApruebaFrecuencia").html(resp.frecuencia+" dias");
    $("#lbl_modApruebaCuota").html("&nbsp;<small style='font-size:10px;'>"+resp.mon_abrevia+"</small>&nbsp;&nbsp;"+resp.cuota+"&nbsp;");
    $("#lbl_modApruebaObservac").html(resp.observac);
    $("#lbl_modEtiqFrecuencia").html((resp.tipocredID==1)?('none'):('inherit'));
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}

async function appSoliCredView(solicredID){
  //tabs default en primer tab
  $('.nav-tabs li').removeClass('active');
  $('.tab-content .tab-pane').removeClass('active');
  $('a[href="#datosSoliCred"]').closest('li').addClass('active');
  $('#datosSoliCred').addClass('active');

  $("#btnUpdate").toggle(menu.oper.submenu.solicred.cmdUpdate == 1);
  $("#btnInsert").hide();
  try{
    const resp = await appAsynFetch({
      TipoQuery : 'viewSoliCred',
      SoliCredID : solicredID
    }, rutaSQL);
    appSoliCredSetData(resp.tablaSoliCred,resp.tablaPers.persona);  //pestaña Solicitud de credito
    appPersonaSetData(resp.tablaPers); //pestaña Personales

    $('#grid').hide();
    $('#edit').show();
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}

function appSoliCredSetData(data,txtSocio){
  //pestaña de solicred
  appLlenarDataEnComboBox(data.comboAgencias,"#cbo_SoliCredAgencia",data.agenciaID);
  appLlenarDataEnComboBox(data.comboEmpleados,"#cbo_SoliCredPromotor",data.promotorID);
  appLlenarDataEnComboBox(data.comboEmpleados,"#cbo_SoliCredAnalista",data.analistaID);
  appLlenarDataEnComboBox(data.comboProductos,"#cbo_SoliCredProducto",data.productoID);
  appLlenarDataEnComboBox(data.comboTipoSBS,"#cbo_SoliCredTipoSBS",data.tiposbsID);
  appLlenarDataEnComboBox(data.comboDestSBS,"#cbo_SoliCredDestSBS",data.destsbsID);
  appLlenarDataEnComboBox(data.comboClasifica,"#cbo_SoliCredClasifica",data.clasificaID);
  appLlenarDataEnComboBox(data.comboCondicion,"#cbo_SoliCredCondicion",data.condicionID);
  appLlenarDataEnComboBox(data.comboMoneda,"#cbo_SoliCredMoneda",data.monedaID);
  $("#cbo_SoliCredTipo").val(data.tipocredID);
  $("#txt_SoliCredFechaSolici").datepicker("setDate",moment(data.fecha_solicred).format("DD/MM/YYYY"));
  $("#txt_SoliCredFechaPriCuota").datepicker("setDate",moment(data.fecha_pricuota).format("DD/MM/YYYY"));
  $('#txt_SoliCredFechaOtorga').datepicker("setDate",moment(data.fecha_otorga).format("DD/MM/YYYY")).on('changeDate', function(e) { appSoliCredUpdatePriCuotaByFechaOtorga(); });
  $('#txt_SoliCredFrecuencia').val(data.frecuencia).on('input', function(e) { appSoliCredUpdatePriCuotaByFrecuencia(); });
  appSoliCredCambiarTipoCredito();
  
  $('#hid_SoliCredID').val(data.ID);
  $('#txt_SoliCredSocio').val(txtSocio);
  $("#txt_SoliCredCodigo").val(data.codigo);
  $("#txt_SoliCredImporte").val(Number(data.importe).toFixed(2));
  $("#txt_SoliCredTasa").val(Number(data.tasa).toFixed(2));
  $("#txt_SoliCredMora").val(Number(data.mora).toFixed(2));
  $("#txt_SoliCredSegDesgr").val(Number(data.desgr).toFixed(2));
  $("#txt_SoliCredNroCuotas").val(data.nrocuotas);
  $("#txt_SoliCredCuota").val(data.cuota);
  $("#txt_SoliCredObserv").val(data.observac);
}

async function appSoliCredClear(txtSocio){
  $('.form-group').removeClass('has-error');
    
  //tabs default en primer tab
  $('.nav-tabs li').removeClass('active');
  $('.tab-content .tab-pane').removeClass('active');
  $('a[href="#datosSoliCred"]').closest('li').addClass('active');
  $('#datosSoliCred').addClass('active');
  $("#btnUpdate").hide();
  $("#btnInsert").show();
  
  try{
    const resp = await appAsynFetch({ TipoQuery:'newSoliCred' }, rutaSQL);
    
    appLlenarDataEnComboBox(resp.comboAgencias,"#cbo_SoliCredAgencia",0);
    appLlenarDataEnComboBox(resp.comboEmpleados,"#cbo_SoliCredPromotor",0);
    appLlenarDataEnComboBox(resp.comboEmpleados,"#cbo_SoliCredAnalista",0);
    appLlenarDataEnComboBox(resp.comboProductos,"#cbo_SoliCredProducto",0);
    appLlenarDataEnComboBox(resp.comboTipoSBS,"#cbo_SoliCredTipoSBS",0);
    appLlenarDataEnComboBox(resp.comboDestSBS,"#cbo_SoliCredDestSBS",0);
    appLlenarDataEnComboBox(resp.comboClasifica,"#cbo_SoliCredClasifica",131);
    appLlenarDataEnComboBox(resp.comboCondicion,"#cbo_SoliCredCondicion",141);
    appLlenarDataEnComboBox(resp.comboMoneda,"#cbo_SoliCredMoneda",0);
  
    $("#hid_SoliCredID").val(0);
    $("#txt_SoliCredSocio").val(txtSocio);
    $("#txt_SoliCredFechaSolici").prop("disabled", (resp.rolUser==resp.rolROOT) ? (false):(true));
    $("#txt_SoliCredCodigo").val("");
    $("#txt_SoliCredImporte").val("1000.00");
    $("#txt_SoliCredTasa, #txt_SoliCredMora").val("100.00");
    $("#txt_SoliCredSegDesgr").val("0.1");
    $("#txt_SoliCredNroCuotas").val("12");
    $("#txt_SoliCredObserv").val("");
    $('#txt_SoliCredFrecuencia').val("").on('input', function(e) { appSoliCredUpdatePriCuotaByFrecuencia(); });
    $("#txt_SoliCredFechaSolici").datepicker("setDate",moment(resp.fecha).format("DD/MM/YYYY"));
    $('#txt_SoliCredFechaOtorga').datepicker("setDate",moment(resp.fecha).format("DD/MM/YYYY")).on('changeDate', function(e) { appSoliCredUpdatePriCuotaByFechaOtorga(); });
    $('#txt_SoliCredFechaPriCuota').datepicker("setDate",moment(resp.fecha).add(1,'M').format("DD/MM/YYYY"));
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}

function appSoliCredCambiarTipoCredito(){
  switch($("#cbo_SoliCredTipo").val()){
    case "1":
      $("#txt_SoliCredFechaPriCuota").prop("disabled", false);
      $("#txt_SoliCredFrecuencia").prop("disabled", true);
      $("#txt_SoliCredFrecuencia").val("");
      appSoliCredUpdatePriCuotaByFechaOtorga();
      break;
    case "2":
      $("#txt_SoliCredFechaPriCuota").prop("disabled", true);
      $("#txt_SoliCredFrecuencia").prop("disabled", false);
      $("#txt_SoliCredFrecuencia").val(14);
      appSoliCredUpdatePriCuotaByFrecuencia();
      break;
  }
}

function appSoliCredUpdatePriCuotaByFechaOtorga(){
  if($("#cbo_SoliCredTipo").val()==1){ //fecha fija
    const fecha = appConvertToFecha($("#txt_SoliCredFechaOtorga").val(),'-');
    $('#txt_SoliCredFechaPriCuota').datepicker("setDate",moment(fecha).add(1,'M').format("DD/MM/YYYY"));
    $('#txt_SoliCredCuota').val("");
  } else {
    appSoliCredUpdatePriCuotaByFrecuencia();
  }
}

function appSoliCredUpdatePriCuotaByFrecuencia(){
  if($("#cbo_SoliCredTipo").val()==2){ //plazo fijo
    const fecha = appConvertToFecha($("#txt_SoliCredFechaOtorga").val(),'-');
    const frecuencia = $("#txt_SoliCredFrecuencia").val();
    $('#txt_SoliCredFechaPriCuota').datepicker("setDate",moment(fecha).add(frecuencia,'d').format("DD/MM/YYYY"));
    $('#txt_SoliCredCuota').val("");
  }
}

async function appSoliCredCambiarTipoSBS(){  //corregir
  try{
    const resp = await appAsynFetch({
      TipoQuery : "cambiarTipoSBS",
      padreID : $("#cbo_SoliCredTipoSBS").val()
    },rutaSQL);
      //appLlenarDataEnComboBox(resp,"#cbo_SoliCredDestSBS",0); //destino SBS
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}

async function appSoliCredGenerarPlanPagos(){
  if(appSoliCredValidarCampos()){
    alert("¡¡¡Faltan datos!!!");
    $("#txt_SoliCredCuota").val("");
  } else {
    try{
      const resp = await appAsynFetch({
        TipoQuery : "simulaCredito",
        TipoCredito : $("#cbo_SoliCredTipo").val(),
        importe : appConvertToNumero($("#txt_SoliCredImporte").val()),
        TEA : appConvertToNumero($("#txt_SoliCredTasa").val()),
        mora : appConvertToNumero($("#txt_SoliCredMora").val()),
        segDesgr : appConvertToNumero($("#txt_SoliCredSegDesgr").val()),
        nroCuotas: appConvertToNumero($("#txt_SoliCredNroCuotas").val()),
        fecha : appConvertToFecha($("#txt_SoliCredFechaOtorga").val(),""),
        pricuota : appConvertToFecha($("#txt_SoliCredFechaPriCuota").val(),""),
        frecuencia : appConvertToNumero($("#txt_SoliCredFrecuencia").val())
      }, rutaSQL);

      if(resp!=undefined){
        $("#txt_SoliCredCuota").val(resp.tabla.cuota);
      } else {
        alert("Sucedio un Error");
      }
    } catch(err){
      console.error('Error al cargar datos:'+err);
    }
  }
}

function appSoliCredValidarCampos(){
  let esError = false;
  $('.form-group').removeClass('has-error');
  if($("#txt_SoliCredImporte").val()=="") { $("#div_SoliCredImporte").addClass("has-error"); esError = true; }
  if($("#txt_SoliCredNroCuotas").val()=="")  { $("#div_SoliCredNroCuotas").addClass("has-error"); esError = true; }
  if($("#txt_SoliCredTasa").val()=="")  { $("#div_SoliCredTasa").addClass("has-error"); esError = true; }
  if($("#txt_SoliCredMora").val()=="")  { $("#div_SoliCredMora").addClass("has-error"); esError = true; }
  if($("#txt_SoliCredSegDesgr").val()=="")  { $("#div_SoliCredSegDesgr").addClass("has-error"); esError = true; }
  if($("#txt_SoliCredFechaOtorga").val()=="")  { $("#div_SoliCredFechaOtorga").addClass("has-error"); esError = true; }
  if($("#txt_SoliCredFechaPriCuota").val()=="")  { $("#div_SoliCredFechaPriCuota").addClass("has-error"); esError = true; }
  if($("#cbo_SoliCredTipo").val()==2 && $("#txt_SoliCredFrecuencia").val()=="")  { $("#div_SoliCredFrecuencia").addClass("has-error"); esError = true; }
  return esError;
}

function appSoliCredGetDatosToDatabase(){
  let rpta = {
    TipoQuery : "execSoliCred",
    TipoExec : null,
    ID : $('#hid_SoliCredID').val(),
    socioID : $("#hid_PersID").val(),
    agenciaID : $("#cbo_SoliCredAgencia").val(),
    promotorID : $("#cbo_SoliCredPromotor").val(),
    analistaID : $("#cbo_SoliCredAnalista").val(),
    productoID : $("#cbo_SoliCredProducto").val(),
    tiposbsID : $("#cbo_SoliCredTipoSBS").val(),
    destsbsID : $("#cbo_SoliCredDestSBS").val(),
    clasificaID : $("#cbo_SoliCredClasifica").val(),
    condicionID : $("#cbo_SoliCredCondicion").val(),
    monedaID : $("#cbo_SoliCredMoneda").val(),
    importe : appConvertToNumero($("#txt_SoliCredImporte").val()),
    saldo : appConvertToNumero($("#txt_SoliCredImporte").val()),
    tasa : appConvertToNumero($("#txt_SoliCredTasa").val()),
    mora : appConvertToNumero($("#txt_SoliCredMora").val()),
    desgr : appConvertToNumero($("#txt_SoliCredSegDesgr").val()),
    nrocuotas : appConvertToNumero($("#txt_SoliCredNroCuotas").val()),
    fecha_solicred : appConvertToFecha($("#txt_SoliCredFechaSolici").val()),
    fecha_otorga : appConvertToFecha($("#txt_SoliCredFechaOtorga").val()),
    fecha_pricuota : appConvertToFecha($("#txt_SoliCredFechaPriCuota").val()),
    frecuencia : appConvertToNumero($("#txt_SoliCredFrecuencia").val()),
    tipocredID : $("#cbo_SoliCredTipo").val(),
    observac : $("#txt_SoliCredObserv").val()
  }
  return rpta;
}

function appPersonaSetData(data){
  //pestaña datos personales
  if(data.tipoPersona==2){ //persona juridica
    $("#lbl_PersTipoNombres").html("Razon Social");
    $("#lbl_PersTipoProfesion").html("Rubro");
    $("#lbl_PersTipoApellidos, #lbl_PersTipoSexo, #lbl_PersTipoECivil, #lbl_PersTipoGIntruc").hide();
  } else {
    $("#lbl_PersTipoNombres").html("Nombres");
    $("#lbl_PersTipoProfesion").html("Profesion");
    $("#lbl_PersTipoApellidos, #lbl_PersTipoSexo, #lbl_PersTipoECivil, #lbl_PersTipoGIntruc").show();
  }
  $("#hid_PersID").val(data.ID);
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

async function modAprueba_BotonAprobar(){
  if(confirm("¿Esta seguro de continuar?")) {
    try{
      const resp = await appAsynFetch({
        ID : $("#hid_modApruebaID").val(),
        FechaAprueba : appConvertToFecha($("#txt_modApruebaFechaAprueba").val()),
        TipoQuery : "aprobarSoliCred",
        TipoExec : "APRU" //aprueba solicitud de credito
      }, rutaSQL);

      //respuesta
      if (!resp.error) { 
        appSoliCredBotonCancel();
        $("#modalAprueba").modal("hide");
      }
    } catch(err){
      console.error('Error al cargar datos:'+err);
    }
  }
}