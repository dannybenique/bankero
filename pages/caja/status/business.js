//=========================funciones para workers============================
var ruta = "pages/caja/status/sql.php";

function appGridAll(){
  $('#grdDatosBody').html('<tr><td colspan="5" style="text-align:center;"><br><div class="progress progress-sm active" style=""><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div><br>Un momento, por favor...</td></tr>');
  let datos = {
    TipoQuery : "coopSUDmovim",
    fecha : appConvertToFecha($("#txt_fechaIni").val(),'-')
  }
  appAjaxSelect(datos,ruta).done(function(resp){
    //console.log(resp);
    if(resp.movim.length>0){
      let fila = "";
      $.each(resp.movim,function(key, valor){
        fila += '<tr>';
        fila += '<td>'+(valor.agencia)+'</td>';
        fila += '<td>'+(valor.ventanilla)+'</td>';
        fila += '<td>'+(valor.moneda)+'</td>';
        fila += '<td>'+(valor.totaloper)+'</td>';
        fila += '<td></td>';
        fila += '</tr>';
      });
      $('#grdDatosBody').html(fila);
    }else{
      $('#grdDatosBody').html('<tr><td colspan="5" style="text-align:center;color:red;">Sin Resultados</td></tr>');
    }
    $('#grdDatosCount').html(resp.movim.length);
  });
}

function appGridReset(){
  $('#txt_fechaIni').datepicker("setDate",moment().format("DD/MM/YYYY"));
}

function rptGetStatusDownload(){
  let datos = {
    TipoQuery  : 'coopSUDmovimDownload',
    fecha      : appConvertToFecha($("#txt_fechaIni").val(),'-')
  }
  appAjaxSelect(datos,ruta).done(function(resp){
    Jhxlsx.export(resp.tableData, resp.options);
  });
}
