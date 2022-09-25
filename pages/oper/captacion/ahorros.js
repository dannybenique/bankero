var rutaSQL = "pages/oper/captacion/sql.php";

//=========================funciones para workers============================
function appGridAll(){
  let agenciaID = $("#cboAgencias").val();
  let txtBuscar = $("#txtBuscar").val();
  let datos = {
    TipoQuery : 'selPreventa',
    agenciaID : agenciaID,
    buscar : txtBuscar
  };
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    console.log(resp);
    if(resp.tabla.length>0){
      let fila = "";
      let disabledDelete = (resp.usernivel==resp.admin) ? ("") : ("disabled");

      $.each(resp.tabla,function(key, valor){

        fila += '<tr style="'+((valor.transac==0)?("color:#bfbfbf;"):(""))+'>';
        fila += '<td><input type="checkbox" name="chk_Borrar" value="'+(valor.ID)+'" '+disabledDelete+'/></td>';
        fila += '<td>'+(valor.fecha)+'</td>';
        fila += '<td>'+(valor.agencia)+'</td>';
        fila += '<td>'+(valor.nombrecorto)+'</td>';
        fila += '<td><a href="javascript:appPreahorroView('+(valor.personaID)+');" title="'+(valor.personaID)+'">'+(valor.persona)+' - '+(valor.nroDNI)+'</a></td>';
        fila += '<td>'+(valor.producto)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.monto,2)+'</td>';
        fila += '<td style="text-align:right;">'+(valor.plazo)+' m</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.tasa,2)+'%</td>';
        fila += '</tr>';
        //if(valor.transac==0 && resp.usernivel<=703) { appData += fila; }
      });
      $('#grdDatosBody').html(fila);
    }else{
      let mensaje = (txtBuscar=="") ? ("") : ("para "+txtBuscar);
      $('#grdDatosBody').html('<tr><td colspan="9" style="text-align:center;color:red;">Sin Resultados '+mensaje+'</td></tr>');
    }
    $('#grdDatosCount').html(resp.tabla.length+"/"+resp.cuenta);
    $('#agenciaAbrev').val(resp.agenciaAbrev);
  });
}

function appBotonReset(){
  $("#txtBuscar").val("");
  let datos = { TipoQuery:'ComboBox', miSubSelect:'Agencias' }
  appAjaxSelect(datos).done(function(resp){
    appLlenarComboAgencias("#cboAgencias",0,resp.tabla);
    appGridAll();
  });
}

function appBotonBuscar(e){
  var code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { appGridAll(); }
}

function appBotonNuevo(){
  Persona.openBuscar('VerifyPreventa',1,0);
  $('#btn_modPersInsert').on('click',function(e) {
    if(Persona.sinErrores()){ //sin errores
      Persona.ejecutaSQL().done(function(resp){
        let data = JSON.parse(resp);
        appPersonaSetData(data.tablaPers);
        appPreahorroSetClear();
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
    appPersonaSetData(Persona.tablaPers); //pestaña Personales
    appPreahorroSetClear();
    $('#grid').hide();
    $('#edit').show();
    Persona.close();
    e.stopImmediatePropagation();
    $('#btn_modPersAddToForm').off('click');
  });
}

function appBotonDelete(){
  let arr = $('[name="chk_Borrar"]:checked').map(function(){ return this.value; }).get();
  if(confirm("¿Esta seguro de continuar borrando estos "+arr.length+" registros?")){
    let datos = { TipoQuery : 'delPreventa', IDs : arr };
    appAjaxDelete(datos,rutaSQL).done(function(resp){
      if (!resp.error) { appGridAll(); }
    });
  }
}

function appBotonCaptado(){
  let arr = $('[name="chk_Borrar"]:checked').map(function(){ return this.value; }).get();
  if(confirm("¿Esta seguro de continuar bloqueando estos "+arr.length+" registros?")){
    let datos = { TipoQuery : 'delPreventa', IDs : arr };
    appAjaxDelete(datos,rutaSQL).done(function(resp){
      if (!resp.error) { appGridAll(); }
    });
  }
}

function appBotonCancel(){
    $("#contenedorFrame").hide();
    $("#btn_Print").hide();
    $('#edit').hide();
    $('#grid').show();
}

function appBotonInsert(){
  let datos = appGetDatosToDatabase();
  if(datos!=""){
    datos.commandSQL = "INS";
    appAjaxInsert(datos,rutaSQL).done(function(resp){
      appGridAll();
      appBotonCancel();
    });
  } else {
    alert("¡¡¡FALTAN LLENAR DATOS o LOS VALORES NO PUEDEN SER CERO!!!");
  }
}

function appBotonUpdate(){
  let datos = appGetDatosToDatabase();
  if(datos!=""){
    datos.commandSQL = "UPD";
    appAjaxUpdate(datos,rutaSQL).done(function(resp){
      appGridAll();
      appBotonCancel();
    });
  } else {
    alert("¡¡¡FALTAN LLENAR DATOS o LOS VALORES NO PUEDEN SER CERO!!!");
  }
}

function appResetSimulacion(){
  $("#lbl_SimuProducto").html("");
  $("#grdSimulacionBody").html("");
  $("#btn_Print").hide();
  $("#contenedorFrame").hide();
}

function appBotonSimulacion(){
  let fecha = appConvertToFecha($("#date_PrevFecha").val(),"-");
  let tiempo = $("#txt_PrevTiempoMeses").val();
  let productoID = $("#cbo_Productos").val();
  let datos = {
    TipoQuery : 'simulaAhorro',
    productoID : productoID,
    fechaIni : appConvertToFecha($("#date_PrevFecha").val(),""),
    fechaFin : moment(fecha).add(tiempo,'months').format("YYYYMMDD"),
    importe : appConvertToNumero($("#txt_PrevMonto").val()),
    tasa : appConvertToNumero($("#txt_PrevTasa").val())
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    let fila = "";
    let total = 0;
    let interes = appConvertToNumero(resp.interes);
    let capital = appConvertToNumero($("#txt_PrevMonto").val());
    $("#hid_interes").val(interes);
    $("#lbl_SimuProducto").html("Prod.: "+resp.producto+" tasa: "+$("#txt_PrevTasa").val()+"%");

    switch(resp.productoID){
      case "106":
      case "127": //ahorrosuperpension
        interes = interes/tiempo;
        total = capital+interes;
        for(x=1; x<=tiempo; x++){
          fila += '<tr>';
          fila += '<td>'+(x)+'</td>';
          fila += '<td>'+(moment(fecha).add(x,'months').format("DD/MM/YYYY"))+'</td>';
          fila += '<td style="text-align:right;">'+appFormatMoney((x==tiempo)?total:interes,2)+'</td>';
          fila += '<td style="text-align:right;">'+appFormatMoney((x==tiempo)?capital:0,2)+'</td>';
          fila += '<td style="text-align:right;">'+appFormatMoney(interes,2)+'</td>';
          fila += '<td></td>';
          fila += '</tr>';
        }
        fila += '<tr style="color:blue;">';
        fila += '<td colspan="2" style="text-align:center;">TOTAL</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(capital+appConvertToNumero(resp.interes),2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(capital,2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(appConvertToNumero(resp.interes),2)+'</td>';
        fila += '<td></td>';
        fila += '</tr>';
        break;
      default: //plazo fijo, libre disponibilidad
        total = capital+interes;
        fila += '<tr>';
        fila += '<td>'+(1)+'</td>';
        fila += '<td>'+(moment(fecha).add(tiempo,'months').format("DD/MM/YYYY"))+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(total,2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(capital,2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(interes,2)+'</td>';
        fila += '<td></td>';
        fila += '</tr>';
        break;
    }
    $("#grdSimulacionBody").html(fila);
    $("#btn_Print").show();
  });
}

function appBotonPrintSimulacion(){
  let personaID = $("#lbl_ID").html();
  let productoID = $("#cbo_Productos").val();
  let tasa = appConvertToNumero($("#txt_PrevTasa").val());
  let plazo = $("#txt_PrevTiempoMeses").val();
  let importe = appConvertToNumero($("#txt_PrevMonto").val());
  let interes = $("#hid_interes").val();
  let tiempo = $("#txt_PrevTiempoMeses").val();
  let fechaini = appConvertToFecha($("#date_PrevFecha").val(),"-");
  let ruta = appUrlServer()+"pages/oper/captacion/rpt.simula.ahorros.php?personaID="+personaID+"&productoID="+productoID+"&tasa="+tasa+"&importe="+importe+"&interes="+interes+"&fechaini="+fechaini+"&plazo="+plazo;

  //$("#contenedorFrame").show();
  $("#contenedorFrame").show().html('<object id="objPDF" type="text/html" data="'+ruta+'" width="100%" height="450px"></object>');
}

function appPreahorroSetClear(){
  //tabs default en primer tab
  $('.nav-tabs li').removeClass('active');
  $('.tab-content .tab-pane').removeClass('active');
  $('a[href="#datosPreahorro"]').closest('li').addClass('active');
  $('#datosPreahorro').addClass('active');

  //tab socio
  $("#hid_PrevID").val("0");
  $("#txt_PrevPlazo").val("0");
  $("#txt_PrevMonto").val("0.00");
  $("#txt_PrevTasa").val("1.00");
  $('#date_PrevFecha').datepicker("setDate",moment().format("DD/MM/YYYY"));
  $("#txt_PrevObserv").val("");
  $("#grdSimulacionBody").html("");
  appAjaxSelect({TipoQuery:"selProductos"},rutaSQL).done(function(resp){ appLlenarDataEnComboBox(resp,"#cbo_Productos",0); });

  //todos los inputs sin error y panel error deshabilitado
  $('.box-body .form-group').removeClass('has-error');

  //botones
  $('#btnInsert').show();
  $('#btnUpdate').hide();
}

function appPreahorroView(personaID){
  let datos = {
    TipoQuery : 'editPreventa',
    personaID : personaID
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    appPersonaSetData(resp.persona); //pestaña de datos personal
    appPreahorroSetData(resp.preventa); //pestaña de socio
    $("#grdSimulacionBody").html("");

    //tabs default en primer tab
    $('.nav-tabs li').removeClass('active');
    $('.tab-content .tab-pane').removeClass('active');
    $('a[href="#datosPreahorro"]').closest('li').addClass('active');
    $('#datosPreahorro').addClass('active');

    $("#btnInsert").hide();

    $('#grid').hide();
    $('#edit').show();
  });
}

function appPreahorroSetData(data){
  //pestaña de socio
  appAjaxSelect({TipoQuery:"selProductos"},rutaSQL).done(function(resp){
    appLlenarDataEnComboBox(resp,"#cbo_Productos",data.productoID);
    $("#hid_PrevID").val(data.ID);
    $("#txt_PrevTiempoMeses").val(data.plazo);
    $("#txt_PrevMonto").val(appFormatMoney(data.monto,2));
    $("#txt_PrevTasa").val(appFormatMoney(data.tasa,2));
    $('#date_PrevFecha').datepicker("setDate",data.fecha);
    $("#date_PrevFecha").attr("disabled","disabled");
    $("#txt_PrevObserv").val(data.observac);

    $("#lbl_SysFecha").html(data.sysfecha);
    $("#lbl_SysUser").html(data.sysuser);
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
  $("#lbl_celular").html(data.celular);

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

function appPermisoPersonas(){
  let datos = { TipoQuery:'OneNotificacion', personaID:$("#lbl_ID").html() }
  appAjaxInsert(datos).done(function(resp){
    if(resp.error==false){ $("#btn_PersPermiso").hide(); }
    else { alert("!!!Hubo un error... "+(resp.mensaje)+"!!!"); }
  });
}

function appGetDatosToDatabase(){
  let EsError = false;
  let rpta = "";
  let monto = appConvertToNumero($("#txt_PrevMonto").val());
  let tasa = appConvertToNumero($("#txt_PrevTasa").val());

  $('.box-body .form-group').removeClass('has-error');
  if(isNaN(monto) || (monto<=0)) { $("#div_PrevMonto").prop("class","form-group has-error"); EsError = true; }
  if(isNaN(tasa) || (tasa<=0))  { $("#div_PrevTasa").prop("class","form-group has-error");  EsError = true; }

  if(!EsError){
    rpta = {
      TipoQuery : "execPreventa",
      preventaID : $("#hid_PrevID").val(),
      personaID : $("#lbl_ID").html(),
      negociotipoID : 2, //ahorros
      productoID : $("#cbo_Productos").val(),
      plazo : $("#txt_PrevTiempoMeses").val(),
      monto : monto,
      tasa : tasa,
      fecha : appConvertToFecha($("#date_PrevFecha").val(),""),
      observac : $("#txt_PrevObserv").val()
    }
  }
  return rpta;
}
