//=========================funciones para workers============================
var ruta = "pages/caja/movim/sql.php";

function appGridAll(){
  $('#grdDatosBody').html('<tr><td colspan="9" style="text-align:center;"><br><div class="progress progress-sm active" style=""><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div><br>Un momento, por favor...</td></tr>');
  let datos = {
    TipoQuery  : 'coopSUDmovim',
    fecha      : appConvertToFecha($("#txt_fechaIni").val(),'-'),
    agencia    : $("#cboAgencias").val(),
    ventanilla : $("#cboVentanillas").val(),
    moneda     : $("#cboMonedas").val()
  }

  appAjaxSelect(datos,ruta).done(function(resp){
    if(resp.movim.length>0){
      let tot_IN = 0;
      let tot_OUT = 0;
      let fila = "";
      $.each(resp.movim,function(key, valor){
        tot_IN += valor.ingreso;
        tot_OUT += valor.egreso;
        fila += '<tr>';
        fila += '<td>'+(valor.agencia+'.'+valor.ventanilla+'.'+valor.num_trans)+'</td>';
        fila += '<td>'+(valor.codsocio)+'</td>';
        fila += '<td title="'+(valor.socio)+'">'+((valor.socio.length>35)?(valor.socio.substr(0,35)+"..."):(valor.socio))+'</td>';
        fila += '<td>'+(valor.tipo_serv+' - '+valor.servicio)+'</td>';
        fila += '<td>'+(valor.tipo_mov+' - '+valor.movim)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.ingreso,2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.egreso,2)+'</td>';
        fila += '</tr>';
      });
      fila += '<td colspan="5" style="text-align:right;font-weight:bold;">TOTAL</td>';
      fila += '<td style="text-align:right;">'+appFormatMoney(tot_IN,2)+'</td>';
      fila += '<td style="text-align:right;">'+appFormatMoney(tot_OUT,2)+'</td>';
      $('#grdDatosBody').html(fila);
    }else{
      $('#grdDatosBody').html('<tr><td colspan="9" style="text-align:center;color:red;">Sin Resultados</td></tr>');
    }
    $('#grdDatosCount').html(resp.movim.length);
  });
}

function appGridReset(){
  $('#txt_fechaIni').datepicker("setDate",moment().format("DD/MM/YYYY"));
  let datos = { TipoQuery : 'ComboBoxAgencias' }
  appAjaxSelect(datos,ruta).done(function(resp){
    appLlenarDataEnComboBox(resp.agencias,"#cboAgencias",resp.agencia);
    appLlenarDataEnComboBox(resp.ventanillas,"#cboVentanillas",0);
  });
}

function rptGetMovimDownload(){
  let datos = {
    TipoQuery  : 'coopSUDmovimDownload',
    fecha      : appConvertToFecha($("#txt_fechaIni").val(),'-'),
    agencia    : $("#cboAgencias").val(),
    ventanilla : $("#cboVentanillas").val(),
    moneda     : $("#cboMonedas").val()
  }
  appAjaxSelect(datos,ruta).done(function(resp){
    Jhxlsx.export(resp.tableData, resp.options);
    //JSONToCSVConvertor(resp, "Cancelados_"+agenciaID, true);
  });
}
