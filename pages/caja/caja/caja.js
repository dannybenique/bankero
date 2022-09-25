//=========================funciones para Agencias============================
function appCajaGetAll(){
  let datos = {
    TipoQuery : 'caja',
    buscar : $("#txtBuscar").val()
  }
  appAjaxSelect(datos,"pages/caja/caja/sql.php").done(function(resp){
    if(resp.tabla.length>0){
      let appData = "";
      $.each(resp.tabla,function(key, valor){
        appData += '<tr>';
        appData += '<td><a href="javascript:appProductosEdit('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.numtrans)+'</a></td>';
        appData += '<td>'+(valor.fecha)+'</td>';
        appData += '<td>'+(valor.observac)+'</td>';
        appData += '<td>'+(valor.producto)+'</td>';
        appData += '<td>'+(valor.tipo_mov)+'</td>';
        appData += '<td style="text-align:right;">'+appFormatMoney(valor.depositos,2)+'</td>';
        appData += '<td style="text-align:right;">'+appFormatMoney(valor.retiros,2)+'</td>';
        appData += '</tr>';
      });
      $('#grdDatosBody').html(appData);
    }else{
      $('#grdDatosBody').html("");
    }
    $('#grdDatosCount').html(resp.tabla.length);
  })
}

function appCajaBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { appCajaGetAll(); }
}

function appCajaReset(){
  $("#txtBuscar").val("");
  appCajaGetAll();
}

function appCajaNuevo(){
  appAjaxSelect({ TipoQuery:'cajaADD' },"pages/caja/caja/sql.php").done(function(resp){
    appLlenarDataEnComboBox(resp.productos,"#cbo_tipoProd",0);
    appLlenarDataEnComboBox(resp.movs,"#cbo_tipoMovs",0);
    appLlenarDataEnComboBox(resp.cajaDocs,"#cbo_cajaDocs",0);
    $('#txt_FechaIng').datepicker("setDate",resp.fecha);
    $('#txt_FechaDoc').datepicker("setDate",resp.fecha);
    $('#txt_NroTransacc').val("");
    $('#txt_Importe').val("");
    $('#txt_Observac').val("");
    $('#txt_NroCajaDoc').val("");
    $('#txt_Proveedor').val("");
    $('#txt_CtaContable').val("");
    $('#hid_ID').val("");
    $('#hid_proveedorID').val("");
    $('#hid_ctacontableID').val("");

    $("#btnInsert").show();
    $("#btnUpdate").hide();
    $("#pn_Error").hide();

    $('#grid').hide();
    $('#edit').show();
  });
}

function appProductosEdit(productoID){
  let datos = {
    TipoQuery : 'OneProducto',
    productoID : productoID
  }
  appAjaxSelect(datos,"pages/caja/caja/sql.php").done(function(resp){
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
    $("#pn_Error").hide();

    $('#grid').hide();
    $('#edit').show();
  })
}

function appProductoCancel(){
    $('#grid').show();
    $('#edit').hide();
}

function appProductoInsert(){
  let datos = appGetDataToDataBase();
  if(datos!=1){
    appAjaxInsert(datos).done(function(resp){
      appProductosGetAll();
      appProductoCancel();
    });
  }
}

function appProductoUpdate(){
  let datos = appGetDataToDataBase();
  if(datos!=1){
    appAjaxUpdate(datos).done(function(resp){
      appProductosGetAll();
      appProductoCancel();
    });
  }
}

function appProductoDelete(){
  let arr = $('[name="chk_Borrar[]"]:checked').map(function(){ return this.value; }).get();
  if(arr.length>0){
    let datos = { TipoQuery : 'Productos', IDs : arr };
    appAjaxDelete(datos).done(function(resp){
      if (resp.error == false) { //sin errores
        appProductosGetAll();
        appProductoCancel();
      }
    });
  } else {
    alert("NO eligio borrar ninguno");
  }
}

function appGetDataToDataBase(){
  let EsError = 0;
  let rpta = 0;

  $('.box-body .form-group').removeClass('has-error');
  if($("#txt_Nombre").val()=="") { $("#pn_Nombre").prop("class","form-group has-error"); EsError = true; }
  if(isNaN($("#txt_tasaMin").val())) { $("#txt_tasaMin").prop("class","form-group has-error"); EsError = true; }
  if(isNaN($("#txt_tasaMax").val())) { $("#txt_tasaMax").prop("class","form-group has-error"); EsError = true; }
  if(isNaN($("#txt_tasaMora").val())) { $("#txt_tasaMora").prop("class","form-group has-error"); EsError = true; }
  if(isNaN($("#txt_segDesgr").val())) { $("#div_segDesgr").prop("class","form-group has-error"); EsError = true; }

  if(EsError==true){
    $("#pn_Error").show();
    setTimeout(function() {
      $('#pn_Error').fadeOut('fast');
    },1000);
    rpta = 1;
  } else {
    rpta = {
      TipoQuery : 'OneProducto',
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

function modPersAddToParentForm(personaID){
  let datos = { TipoQuery : 'OnePersona', personaID : personaID}
  appAjaxSelect(datos).done(function(resp){
    $('#hid_proveedorID').val(resp.ID);
    $('#txt_Proveedor').val(resp.nroDNI+' - '+resp.nombres);
    Persona.close();
  });
}

function modPersInsert(){
  if(Persona.verificarErrores()==0){ //guardamos datos de persona
    let datos = Persona.datosToDatabase();
    let foto = $('input[name="file_modPersFoto"]').get(0).files[0];
    let formData = new FormData();

    formData.append('imgFoto', foto);
    formData.append("appInsert",JSON.stringify(datos));
    $.ajax({
      url:'includes/sql_insert.php',
      type:'POST',
      processData:false,
      contentType: false,
      data:formData
    })
    .done(function(resp){
      let data = JSON.parse(resp);
      modPersAddToParentForm(data.personaID);
    })
    .fail(function(resp){
      console.log("fail:.... "+resp.responseText);
    });
  }
}
