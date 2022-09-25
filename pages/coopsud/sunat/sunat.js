var rutaSQL = "pages/coopsud/sunat/sql.php";

//=========================funcion iniciales RESET============================
function appReset(){
  $('#txtFechaIni').val(moment().format("01/MM/YYYY"));
  $('#txtFechaFin').val(moment().endOf('month').format('DD/MM/YYYY'));
  $('#txtFechaIni').datepicker({ autoclose: true })
  $('#txtFechaFin').datepicker({ autoclose: true })
}

//=========================funciones para coopSUD SUNAT============================
function appDownloadFacturas(){
  $('#grdDatosBody').html('<tr><td colspan="4" style="text-align:center;"><br><div class="progress progress-sm active" style=""><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div></td></tr>');

  let datos = {
    TipoQuery  : 'DownloadFacturas',
    fechaIni : appConvertToFecha($('#txtFechaIni').val(),"-"),
    fechaFin : appConvertToFecha($('#txtFechaFin').val(),"-")
  }
  console.log(datos);
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    //console.log(resp);
    Jhxlsx.export(resp.tableData, resp.options);
    $('#grdDatosBody').html('');
  });
}


function appArregloAportes(arreglar){
  $('#grdDatosBody').html('<tr><td colspan="6" style="text-align:center;"><br><div class="progress progress-sm active" style=""><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div></td></tr>');

  let datos = { TipoQuery  : 'ArregloAportes',arreglar : arreglar }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    let fila = "";
    if(resp.length>0){
      $.each(resp,function(key, valor){
        fila += '<tr>';
        fila += '<td>'+(valor.codsocio)+'</td>';
        fila += '<td>-</td>';
        fila += '<td>'+(valor.fecha1)+' - '+(valor.fecha2)+'</td>';
        fila += '<td>'+(valor.tipomov)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.importe,2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.saldo,2)+'</td>';
        fila += '</tr>';
      });
      $('#grdDatosBody').html(fila);
    } else {
      $('#grdDatosBody').html('<tr><td colspan="5" style="text-align:center;color:red;">Sin Resultados</td></tr>');
    }
    $('#grdDatosCount').html(resp.length);
  });
}
