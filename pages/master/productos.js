var rutaSQL = "pages/master/sql.php";

//=========================funciones para productos============================
function appProductosGetAll(){
  let txtBuscar = $("#txtBuscar").val();
  let datos = {
    TipoQuery : 'selProductos',
    miBuscar : txtBuscar
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    let disabledDelete = (resp.usernivel==resp.admin) ? ("") : ("disabled");

    if(disabledDelete.length>0) { $('#chk_All').attr("disabled",disabledDelete);}
    if(resp.tabla.length>0){
      let appData = "";
      $.each(resp.tabla,function(key, valor){
        appData += '<tr>';
        appData += '<td><input type="checkbox" id="chk_Borrar" name="chk_Borrar" value="'+(valor.ID)+'" '+disabledDelete+'/></td>';
        appData += '<td>'+(valor.ID)+'</td>';
        appData += '<td><a href="javascript:appProductosEdit('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.nombre)+'</a></td>';
        appData += '<td>'+appFormatMoney(valor.tasamin,2)+'</td>';
        appData += '<td>'+appFormatMoney(valor.tasamax,2)+'</td>';
        appData += '<td>'+appFormatMoney(valor.tasamora,2)+'</td>';
        appData += '<td>'+(valor.tipo)+'</td>';
        appData += '</tr>';
      });
      $('#grdDatosBody').html(appData);
    }else{
      $('#grdDatosBody').html("");
    }
    $('#grdDatosCount').html(resp.tabla.length);
  })
}

function appProductosBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { appProductosGetAll(); }
}

function appProductosReset(){
  $("#txtBuscar").val("");
  appProductosGetAll();
}

function appProductosNuevo(){
  $("#txt_ID").val("0");
  $("#txt_Nombre").val("");
  $("#txt_tasaMin").val(appFormatMoney(0,2));
  $("#txt_tasaMax").val(appFormatMoney(0,2));
  $("#txt_tasaMora").val(appFormatMoney(0,2));
  $("#txt_segDesgr").val(appFormatMoney(0.1,2));
  appComboBox("#cbo_tipoProd","tipoProducto",0);
  appComboBox("#cbo_tipoOper","tipoOperacion",0);
  appComboBox("#cbo_tipoMone","tipoMoneda",0);

  $('.box-body .form-group').removeClass('has-error');
  $("#btnInsert").show();
  $("#btnUpdate").hide();

  $('#grid').hide();
  $('#edit').show();
}

function appProductosEdit(productoID){
  let datos = {
    TipoQuery : 'editProducto',
    productoID : productoID
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    $("#txt_ID").val(resp.ID);
    $("#txt_Nombre").val(resp.nombre);
    $("#txt_tasaMin").val(appFormatMoney(resp.tasamin,2));
    $("#txt_tasaMax").val(appFormatMoney(resp.tasamax,2));
    $("#txt_tasaMora").val(appFormatMoney(resp.tasamora,2));
    $("#txt_segDesgr").val(appFormatMoney(resp.segdesgr,2));
    appComboBox("#cbo_tipoProd","tipoProducto",resp.id_tipo_prod);
    appComboBox("#cbo_tipoOper","tipoOperacion",resp.id_tipo_oper);
    appComboBox("#cbo_tipoMone","tipoMoneda",resp.id_tipo_mone);

    $("#btnInsert").hide();
    $("#btnUpdate").show();

    $('#grid').hide();
    $('#edit').show();
  })
}

function appProductosDelete(){
  let arr = $('[name="chk_Borrar"]:checked').map(function(){ return this.value; }).get();
  if(arr.length>0){
    if(confirm("¿Desea borrar estos "+arr.length+" productos?")){
      let datos = { TipoQuery:'delProductos', IDs:arr };
      appAjaxDelete(datos,rutaSQL).done(function(resp){
        if (!resp.error) { //sin errores
          appProductosGetAll();
          appProductoCancel();
        }
      });
    }
  } else {
    alert("NO eligio borrar ninguno");
  }
}

function appProductoCancel(){
    $('#grid').show();
    $('#edit').hide();
}

function appProductoInsert(){
  let datos = appGetDataToDataBase();
  if(datos!=1){
    datos.commandSQL = "INS";
    appAjaxInsert(datos,rutaSQL).done(function(resp){
      appProductosGetAll();
      appProductoCancel();
    });
  } else {
    alert("!!!Faltan Datos!!!");
  }
}

function appProductoUpdate(){
  let datos = appGetDataToDataBase();
  if(datos!=1){
    datos.commandSQL = "UPD";
    appAjaxUpdate(datos,rutaSQL).done(function(resp){
      appProductosGetAll();
      appProductoCancel();
    });
  } else {
    alert("!!!Faltan Datos!!!");
  }
}

function appGetDataToDataBase(){
  let EsError = 0;
  let rpta = 0;

  $('.box-body .form-group').removeClass('has-error');
  if($("#txt_Nombre").val()=="") { $("#pn_Nombre").prop("class","form-group has-error"); EsError = true; }
  if($("#txt_tasaMin").val()=="") { $("#div_tasaMin").prop("class","form-group has-error"); EsError = true; }
  if($("#txt_tasaMax").val()=="") { $("#div_tasaMax").prop("class","form-group has-error"); EsError = true; }
  if($("#txt_tasaMora").val()=="") { $("#div_tasaMora").prop("class","form-group has-error"); EsError = true; }
  if($("#txt_segDesgr").val()=="") { $("#div_segDesgr").prop("class","form-group has-error"); EsError = true; }

  if(EsError){
    rpta = 1;
  } else {
    rpta = {
      TipoQuery : 'execProducto',
      ID : $("#txt_ID").val(),
      nombre : $("#txt_Nombre").val(),
      tasamin : $("#txt_tasaMin").val(),
      tasamax : $("#txt_tasaMax").val(),
      tasamora : $("#txt_tasaMora").val(),
      segDesgr : $("#txt_segDesgr").val(),
      id_tipo_prod : $("#cbo_tipoProd").val(),
      id_tipo_oper : $("#cbo_tipoOper").val(),
      id_tipo_mone : $("#cbo_tipoMone").val()
    }
  }
  return rpta;
}
