var rutaSQL = "pages/config/agencias/sql.php";

//=========================funciones para Agencias============================
function appAgenciasGetAll(){
  let txtBuscar = $("#txtBuscar").val();
  let datos = {
    TipoQuery : 'selAgencias',
    miBuscar : txtBuscar
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    let disabledDelete = resp.usernivel==resp.admin ? "" : "disabled";

    if(disabledDelete.length>0) { $('#chk_All').attr("disabled",disabledDelete);}
    if(resp.tabla.length>0){
      let appData = "";
      $.each(resp.tabla,function(key, valor){
        appData += '<tr>';
        appData += '<td><input type="checkbox" id="chk_Borrar" name="chk_Borrar[]" value="'+(valor.ID)+'" '+disabledDelete+'/></td>';
        appData += '<td>'+(valor.codigo)+'</td>';
        appData += '<td><a href="javascript:appAgenciaView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.nombre)+'</a></td>';
        appData += '<td>'+(valor.telefonos)+'</td>';
        appData += '<td>'+(valor.ciudad)+'</td>';
        appData += '<td>'+(valor.direccion)+'</td>';
        appData += '</tr>';
      });
      $('#grdDatosBody').html(appData);
    }else{
      $('#grdDatosBody').html("");
    }
    $('#grdDatosCount').html(resp.tabla.length);
  })
}

function appAgenciasBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) {
    appAgenciasGetAll();
  }
}

function appAgenciasReset(){
  $("#txtBuscar").val("");
  appAgenciasGetAll();
}

function appAgenciaNuevo(){
    $('#hid_agenciaID').prop("value","0");
    $("#txt_Codigo").val("");
    $("#txt_Abrev").val("");
    $("#txt_Nombre").val("");
    $("#txt_Ciudad").val("");
    $("#txt_Direccion").val("");
    $("#txt_Telefonos").val("");
    $("#txt_Observac").val("");

    $('.box-body .form-group').removeClass('has-error');
    $("#btnInsert").show();
    $("#btnUpdate").hide();

    $('#grid').hide();
    $('#edit').show();
}

function appAgenciaView(agenciaID){
  let datos = {
    TipoQuery : 'editAgencia',
    agenciaID : agenciaID
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    $('#hid_agenciaID').prop("value",resp.ID);
    $("#txt_Codigo").val(resp.codigo);
    $("#txt_Abrev").val(resp.abrev);
    $("#txt_Nombre").val(resp.nombre);
    $("#txt_Ciudad").val(resp.ciudad);
    $("#txt_Direccion").val(resp.direccion);
    $("#txt_Telefonos").val(resp.telefonos);
    $("#txt_Observac").val(resp.observac);

    $("#btnInsert").hide();
    $("#btnUpdate").show();

    $('#grid').hide();
    $('#edit').show();
  })
}

function appAgenciaCancel(){
    $('#grid').show();
    $('#edit').hide();
}

function appAgenciaInsert(){
  let datos = modPersGetDataToDataBase();
  if(datos!=1){
    datos.commandSQL = "INS";
    appAjaxInsert(datos,rutaSQL).done(function(resp){
      appAgenciasGetAll();
      appAgenciaCancel();
    });
  } else {
    alert("!!!Faltan Datos!!!");
  }
}

function appAgenciaUpdate(){
  let datos = modPersGetDataToDataBase();
  if(datos!=1){
    datos.commandSQL = "UPD";
    appAjaxUpdate(datos,rutaSQL).done(function(resp){
      appAgenciasGetAll();
      appAgenciaCancel();
    });
  } else {
    alert("!!!Faltan Datos!!!");
  }
}

function appAgenciaDelete(){
  let arr = $('[name="chk_Borrar[]"]:checked').map(function(){ return this.value; }).get();
  if(arr.length>0){
    let datos = { TipoQuery : 'delAgencias', IDs : arr };
    appAjaxDelete(datos,rutaSQL).done(function(resp){
      if (resp.error == false) { //sin errores
        appAgenciasGetAll();
        appAgenciaCancel();
      }
    });
  } else {
    alert("NO eligio borrar ninguno");
  }
}

function modPersGetDataToDataBase(){
  let esError = false;
  let rpta = 0;

  $('.box-body .form-group').removeClass('has-error');
  if($("#txt_Codigo").val()=="") { $("#pn_Codigo").prop("class","form-group has-error"); esError = true; }
  if($("#txt_Abrev").val()=="") { $("#pn_Abrev").prop("class","form-group has-error"); esError = true; }
  if($("#txt_Nombre").val()=="") { $("#pn_Nombre").prop("class","form-group has-error"); esError = true; }
  if($("#txt_Ciudad").val()=="") { $("#pn_Ciudad").prop("class","form-group has-error"); esError = true; }

  if(esError){
    rpta = 1;
  }else{
    rpta = {
      TipoQuery : 'execAgencia',
      ID : $("#hid_agenciaID").val(),
      codigo : $("#txt_Codigo").val(),
      abrev : $("#txt_Abrev").val(),
      nombre : $("#txt_Nombre").val(),
      ciudad : $("#txt_Ciudad").val(),
      direccion : $("#txt_Direccion").val(),
      telefonos : $("#txt_Telefonos").val(),
      observac : $("#txt_Observac").val()
    }
  }
  return rpta;
}
