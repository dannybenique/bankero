var rutaSQL = "pages/coopsud/carlos/sql.php";

//=========================funcion iniciales RESET============================
function appReset(){
  let datos = { TipoQuery  : 'comboAgencias' }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    appLlenarDataEnComboBox(resp,"#cboAgencias","01");
  });
}

//=========================funciones para coopSUD migracion============================
function appDownloadAportes(){
  $('#grdDatosBody').html('<tr><td colspan="7" style="text-align:center;"><br><div class="progress progress-sm active" style=""><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div></td></tr>');

  let datos = { TipoQuery  : 'DownloadAportes' }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    Jhxlsx.export(resp.tableData, resp.options);
    $('#grdDatosBody').html('');
  });
}

function appDownloadAhorros(){
  $('#grdDatosBody').html('<tr><td colspan="7" style="text-align:center;"><br><div class="progress progress-sm active" style=""><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div></td></tr>');

  let datos = { TipoQuery  : 'DownloadAhorros' }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    Jhxlsx.export(resp.tableData, resp.options);
    $('#grdDatosBody').html('');
  });
}

function appDownloadCreditos(){
  $('#grdDatosBody').html('<tr><td colspan="7" style="text-align:center;"><br><div class="progress progress-sm active" style=""><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div></td></tr>');

  let datos = { TipoQuery  : 'DownloadCreditos' }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    Jhxlsx.export(resp.tableData, resp.options);
    $('#grdDatosBody').html('');
  });
}

function appDownloadOperaciones(){
  $('#grdDatosBody').html('<tr><td colspan="7" style="text-align:center;"><br><div class="progress progress-sm active" style=""><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div></td></tr>');

  let datos = { TipoQuery  : 'DownloadOperaciones' }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    console.log(resp.tableData);
    Jhxlsx.export(resp.tableData, resp.options);
    $('#grdDatosBody').html('');
  });
}

function appDownloadSaldos(){
  $('#grdDatosBody').html('<tr><td colspan="7" style="text-align:center;"><br><div class="progress progress-sm active" style=""><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div></td></tr>');

  let datos = { TipoQuery  : 'DownloadSaldos',agenciaID : $("#cboAgencias").val() }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    Jhxlsx.export(resp.tableData, resp.options);
    $('#grdDatosBody').html('');
  });
}

function appArregloAportes(arreglar){
  $('#grdDatosBody').html('<tr><td colspan="7" style="text-align:center;"><br><div class="progress progress-sm active" style=""><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div></td></tr>');

  //let datos = { TipoQuery  : 'ArregloAportes',arreglar : arreglar }
  let datos = { TipoQuery  : 'ArregloCreditos' }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    let fila = "";
    if(resp.length>0){
      $.each(resp,function(key, valor){
        fila += '<tr>';
        fila += '<td>'+(valor.codsocio)+'</td>';
        fila += '<td>-</td>';
        fila += '<td>'+(valor.tiposerv)+'.'+(valor.numpres)+'</td>';
        fila += '<td>-</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.importe,2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.saldo,2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.sumamovim,2)+'</td>';
        fila += '</tr>';
      });
      $('#grdDatosBody').html(fila);
    } else {
      $('#grdDatosBody').html('<tr><td colspan="7" style="text-align:center;color:red;">Sin Resultados</td></tr>');
    }
    $('#grdDatosCount').html(resp.length);
  });
}
