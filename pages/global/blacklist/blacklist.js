let rutaSQL = "pages/global/blacklist/sql.php";
//=========================funciones para Blacklist============================
function appBlacklistGetAll(){
  let agenciaID = $("#cboAgencias").val();
  let txtBuscar = $("#txtBuscar").val().toUpperCase();
  let datos = {
    TipoQuery : "selBlacklist",
    verTodos  : $("#hidViewAll").val(),
    agenciaID : agenciaID,
    miBuscar  : txtBuscar
  };

  $('#grdDatosBody').html('<tr><td colspan="7" style="text-align:center;"><br><div class="progress progress-sm active" style=""><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div><br>Un momento, por favor...</td></tr>');
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    if(resp.tabla.length>0){
      let filas = "";
      let userTrue = false;
      switch(resp.usernivelID){
        case 701: //superadmin
        case 703: //gerencia
        case 706: //RRHH
        case 712: //caja
          userTrue = true; break;
        default : userTrue = false;
      }

      $.each(resp.tabla,function(key, valor){
        filas += '<tr>';
        filas += '<td><input type="checkbox" name="chk_Borrar" value="'+(valor.ID)+'"/></td>';
        filas += '<td>'+(valor.fecha)+'</td>';
        filas += '<td><a href="javascript:appBlacklistView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.persona)+'</a></td>';
        filas += '<td>'+(valor.DNI)+'</td>';
        filas += '<td>'+(valor.agencia)+'</td>';
        filas += '<td>'+(valor.tipoObservac)+'</td>';
        filas += '<td>'+((userTrue)?(valor.observac):('<span style="color:#aaa;font-style:italic;">***clasificado***</span>'))+'</td>';
        filas += '</tr>';
      });
      $('#grdDatosBody').html(filas);
    }else{
      let mensaje = ((txtBuscar=="")?(""):("para "+txtBuscar));
      $('#grdDatosBody').html('<tr><td colspan="7" style="text-align:center;"><a href="javascript:appBlacklistView(\'D'+txtBuscar+'\');" style="color:red;">Sin Resultados '+mensaje+'</a></td></tr>');
    }
    $('#grdDatosCount').html(resp.tabla.length+"/"+resp.cuenta);
  });
}

function appBlacklistBotonReset(){
  $("#txtBuscar").val("");
  let datos = { TipoQuery:'agencias_tipos' };
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    appLlenarComboAgencias("#cboAgencias",0,resp.agencias);
    appBlacklistGetAll();
  });
}

function appBlacklistBotonViewAll(){
  if($("#hidViewAll").val()=="1") {
    $("#hidViewAll").val("0");
    $("#icoViewAll").removeClass("fa-toggle-on");
    $("#icoViewAll").addClass("fa-toggle-off");
  } else {
    $("#hidViewAll").val("1");
    $("#icoViewAll").removeClass("fa-toggle-off");
    $("#icoViewAll").addClass("fa-toggle-on");
  }
  appBlacklistGetAll();
}

function appBlacklistBotonBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { appBlacklistGetAll(); }
}

function appBlacklistBotonPrint(userID){
  let urlServer = appUrlServer()+"pages/global/blacklist/rpt_blacklist.php?docDNI="+$("#lbl_DNI").html()+"&usrID="+userID;
  $("#contenedorFrame").html('<object id="objPDF" type="text/html" data="'+urlServer+'" width="100%" height="450px"></object>');
}

function appBlacklistBotonNuevo(){
  Persona.openBuscar('VerifyBlacklist',1,0);

  //modal de blacklist
  $("#btn_modEdicionInsert").show();
  $("#btn_modEdicionUpdate").hide();

  //botones del modal de personas
  $('#btn_modPersInsert').on('click',function(e) {
    if(Persona.sinErrores()){ //sin errores
      Persona.ejecutaSQL().done(function(resp){
        let data = JSON.parse(resp);
        appPersonaSetData(data.tablaPers);
        appBlacklistBlancos();
        Persona.close();
        $("#modalEdicion").modal();
      });
    } else {
      alert("!!!Faltan llenar Datos!!!");
    }
    e.stopImmediatePropagation();
    $('#btn_modPersInsert').off('click');
  });
  $('#btn_modPersAddToForm').on('click',function(e) {
    appPersonaSetData(Persona.tablaPers);
    appBlacklistBlancos();
    Persona.close();
    $("#modalEdicion").modal();
    e.stopImmediatePropagation();
    $('#btn_modPersAddToForm').off('click');
  });
}

function appBlacklistBotonCancel(){
  $('#edit').hide();
  $('#grid').show();
}

function appBlacklistBotonEdit(){
  let datos = {
    TipoQuery:'editBlacklist',
    personaID:$("#hid_personaID").val() };
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    $('#date_fechaing').datepicker("setDate",resp.tablaBlack.fecha);
    appLlenarDataEnComboBox(resp.agencias,"#cbo_BlkAgencia",resp.tablaBlack.id_agencia);
    appLlenarDataEnComboBox(resp.tipos,"#cbo_BlkTipo",resp.tablaBlack.id_tipo);
    $("#txt_Observac").val(resp.tablaBlack.observac);

    $("#btn_modEdicionInsert").hide();
    $("#btn_modEdicionUpdate").show();
    $("#modalEdicion").modal();
  });
}

function appBlacklistBotonDelete(){
  let arr = $('[name="chk_Borrar"]:checked').map(function(){ return this.value; }).get();
  if(confirm("¿Esta seguro de continuar borrando estos "+arr.length+" registros?")){
    let datos = { TipoQuery : 'delBlacklist', IDs : arr };
    appAjaxDelete(datos,rutaSQL).done(function(resp){
      if (!resp.error) { appBlacklistGetAll(); }
    });
  }
}

function appBlacklistBlancos(){
  let datos = { TipoQuery:'agencias_tipos' };
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    appLlenarDataEnComboBox(resp.agencias,"#cbo_BlkAgencia",0);
    appLlenarDataEnComboBox(resp.tipos,"#cbo_BlkTipo",0);
    $('#date_fechaing').datepicker("setDate",moment().format("DD/MM/YYYY"));
    $("#txt_Observac").val("");
  });
}

function appBlacklistView(personaID){
  let datos = {
    TipoQuery : 'editBlacklist',
    personaID : personaID
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    appPersonaSetData(resp.tablaPers);
    appBlacklistSetData(resp.tablaBlack);

    //otros datos para mostrar info
    $("#contenedorFrame").html("");
    $('#grid').hide();
    $('#edit').show();
  });
}

function appPersonaSetData(data){
  $("#hid_personaID").val(data.ID);
  $("#lbl_persona").html(data.persona);
  $("#lbl_DNI").html(data.nroDNI);
  $("#lbl_ID").html(data.ID);
  $("#lbl_ubicacion").html(data.region+" - "+data.provincia+" - "+data.distrito);
  $("#lbl_direccion").html(data.direccion);
  $("#lbl_referencia").html(data.referencia);
  $("#lbl_medidorluz").html(data.medidorluz);
  $("#lbl_tipovivienda").html(data.tipovivienda);
}

function appBlacklistSetData(data){
  $("#lbl_agencia").html(data.agencia);
  $("#lbl_fecha").html(data.fecha);
  $("#lbl_modif").html(data.sysusuario);
  $("#lbl_observac").html(data.observac);
}

function modalEdicionGetDatosToDatabase(){
  let datos = {
    TipoQuery : 'execBlacklist',
    personaID : $("#hid_personaID").val(),
    agenciaID : $("#cbo_BlkAgencia").val(),
    tipoblkID : $("#cbo_BlkTipo").val(),
    fecha     : appConvertToFecha($("#date_fechaing").val(),""),
    observac  : $("#txt_Observac").val()
  }
  return datos;
}

function modalEdicionBotonInsert(){
  let datos = modalEdicionGetDatosToDatabase();
  datos.commandSQL = "INS";
  appAjaxInsert(datos,rutaSQL).done(function(resp){
    appBlacklistGetAll();
    appBlacklistSetData(resp.tablaBlack);
    $('#grid').hide();
    $('#edit').show();
    $("#btn_print").show();
    $("#modalEdicion").modal('hide');
  });
}

function modalEdicionBotonUpdate(){
  let datos = modalEdicionGetDatosToDatabase();
  datos.commandSQL = "UPD";

  appAjaxUpdate(datos,rutaSQL).done(function(resp){
    appBlacklistGetAll();
    appBlacklistSetData(resp.tablaBlack);
    $("#modalEdicion").modal('hide');
  });
}
