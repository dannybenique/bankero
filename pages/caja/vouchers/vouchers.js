var rutaSQL = "pages/caja/vouchers/sql.php";

//=========================funciones para Personas============================
function appVouchersGetAll(){
  let txtBuscar = $("#txtBuscar").val();
  let tipoOperac = $("#cboTipoOperaciones").val();
  let datos = {
    TipoQuery : 'selVouchers',
    tipo_oper : tipoOperac,
    buscar : txtBuscar
  };
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    if(resp.tabla.length>0){
      let appData = "";
      $.each(resp.tabla,function(key, valor){
        appData += '<tr>';
        appData += '<td style="text-align:right;"><a href="javascript:appVoucherEdit('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.numtrans)+'</a></td>';
        appData += '<td style="text-align:right;">'+(valor.fecha)+'</td>';
        appData += '<td>'+(valor.tipo_oper)+'</td>';
        appData += '<td>'+(valor.responsable)+'</td>';
        appData += '<td>'+(valor.socio)+'</td>';
        appData += '</tr>';
      });
      $('#grdDatosBody').html(appData);
    }else{
      $('#grdDatosBody').html('<tr><td colspan="5" style="text-align:center;color:red;">Sin Resultados para '+txtBuscar+'</td></tr>');
    }
    $('#grdDatosCount').html(resp.tabla.length+"/"+resp.cuenta);
  });
}

function appVouchersReset(){
  $("#txtBuscar").val("");
  let datos = { TipoQuery:'ComboBox', miSubSelect:"tipoOperacion" }
  appAjaxSelect(datos).done(function(resp){
    resp.push({"ID":0,"nombre":"Todos"});
    appLlenarDataEnComboBox(resp,"#cboTipoOperaciones",0);
    appVouchersGetAll();
  });
}

function appVouchersBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { appVouchersGetAll(); }
}

function appVoucherEdit(voucherID){
  let datos = {
    TipoQuery : 'editVoucher',
    voucherID : voucherID
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    //cabecera
    $("#hid_ID").val(resp.cabecera.ID);
    $("#hid_id_tipo_oper").val(resp.cabecera.id_tipo_oper);
    $("#txt_numtrans").val(resp.cabecera.numtrans);
    $("#txt_fecha").val(resp.cabecera.fecha);
    $("#txt_responsable").val(resp.cabecera.responsable);
    $("#txt_socio").val(resp.cabecera.socio);
    $("#txt_operacion").val(resp.cabecera.tipo_oper);

    //detalle
    if(resp.detalle.length>0){
      let total = 0;
      let appData = "";
      $.each(resp.detalle,function(key, valor){
        appData += '<tr>';
        appData += '<td>'+(valor.item)+'</td>';
        appData += '<td>'+(valor.detalle)+'</td>';
        appData += '<td style="text-align:right;">'+appFormatMoney(valor.importe,2)+'</td>';
        appData += '</tr>';
        total += appConvertToNumero(valor.importe);
      });
      $('#grdVouchersBody').html(appData);
      $('#lbl_total').html(appFormatMoney(total,2));
    }else{
      $('#grdVouchersBody').html('<tr><td colspan="3" style="text-align:center;color:red;">Sin Datos</td></tr>');
    }
    $("#contenedorFrame").hide();

    $('#grid').hide();
    $('#edit').show();
  });
}

function appVoucherPrint(urlServer){
  let voucherID = $("#hid_ID").val();
  $("#contenedorFrame").show();
  $('#objPDF').prop("data",urlServer+"/includes/pdf/plantilla/rpt.caja.voucher.php?voucherID="+(voucherID));
}

function appVoucherDelete(){
  if(confirm("¿Realmente desea eliminar este voucher?")){
    let datos = {
      TipoQuery : 'delVoucher',
      voucherID : $("#hid_ID").val()
    }
    appAjaxDelete(datos,rutaSQL).done(function(resp){
      appVouchersGetAll();
      appVoucherCancel();
    });
  }
}

function appVoucherCancel(){
  $('#grid').show();
  $('#edit').hide();
}
