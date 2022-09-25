//=========================funciones para workers============================
var ruta = "pages/coopsud/cancelados/sql.php";

function appGridAll(){
  $('#grdDatosBody').html('<tr><td colspan="9" style="text-align:center;"><br><div class="progress progress-sm active" style=""><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div><br>Un momento, por favor...</td></tr>');
  let datos = {
    TipoQuery : 'coopSUDcancelados',
    fechaIni  : appConvertToFecha($("#txt_fechaIni").val(),'-'),
    fechaFin  : appConvertToFecha($("#txt_fechaFin").val(),'-')
  }

  appAjaxSelect(datos,ruta).done(function(resp){
    console.log(resp);
    if(resp.tabla.length>0){
      let appData = "";
      $.each(resp.tabla,function(key, valor){
        appData += '<tr>';
        appData += '<td>'+(valor.agencia)+'</td>';
        appData += '<td>'+(valor.codigo)+'</td>';
        appData += '<td>'+(valor.socio)+' &raquo; '+(valor.doc)+'-'+(valor.nrodoc)+'</td>';
        appData += '<td>'+(valor.celular)+'</td>';
        appData += '<td>'+(valor.nrocuenta)+'</td>';
        appData += '<td>'+(valor.analista)+'</td>';
        appData += '<td>'+(moment(valor.fecha_pago.date).format("DD/MM/YYYY"))+'</td>';
        appData += '<td>'+(valor.p_num_cuot)+'</td>';
        appData += '<td>'+appFormatMoney(valor.importe,2)+'</td>';
        appData += '</tr>';
      });
      $('#grdDatosBody').html(appData);
    }else{
      $('#grdDatosBody').html('<tr><td colspan="9" style="text-align:center;color:red;">Sin Resultados</td></tr>');
    }
    $('#grdDatosCount').html(resp.tabla.length);
  });
}

function appGridReset(){
  let datos = { TipoQuery : 'inicio' }
  appAjaxSelect(datos,ruta).done(function(resp){
    $('#txt_fechaIni').datepicker("setDate",moment().format("DD/MM/YYYY"));
    $('#txt_fechaFin').datepicker("setDate",moment().format("DD/MM/YYYY"));
    if(resp.usernivel==resp.admin || resp.usernivel==resp.jefe) {
      $('#div_descargar').html('<button type="button" class="btn btn-default btn-sm" title="Descargar cancelados de esta agencia" onclick="javascript:rptGetCanceladosSociosDownload();"><i class="fa fa-download"></i></button>');
    } else {$('#div_descargar').html('');}
  });
}

function rptGetCanceladosSociosDownload(){
  let datos = {
    TipoQuery : 'coopSUDcanceladosDownload',
    fechaIni  : appConvertToFecha($("#txt_fechaIni").val(),'-'),
    fechaFin  : appConvertToFecha($("#txt_fechaFin").val(),'-')
  }
  console.log(datos);
  appAjaxSelect(datos,ruta).done(function(resp){
    Jhxlsx.export(resp.tableData, resp.options);
    //JSONToCSVConvertor(resp, "Cancelados_"+agenciaID, true);
  });
}
