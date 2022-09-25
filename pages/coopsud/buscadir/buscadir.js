let rutaSQL = "pages/coopsud/buscadir/sql.php";

//=========================funciones para coopSUD Prestamos============================
function appGridAll(){
  $('#grdDatosCount').html("");
  $('#grdDatosBody').html('<tr><td colspan="10" style="text-align:center;"><br><div class="progress progress-sm active"><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div><br>Un momento, por favor...</td></tr>');

  if($('#txtBuscar').val().trim()==""){
    $('#grdDatosBody').html('<tr><td colspan="10" style="text-align:center;color:red;">Sin Resultados</td></tr>');
  } else {
    let datos = {
      TipoQuery:'coopSUDbuscadireccion',
      buscar:$('#txtBuscar').val().trim()
    };
    appAjaxSelect(datos,rutaSQL).done(function(resp){
      appLlenarTabla(resp.tabla);
    });
  }
}

function appLlenarTabla(tabla){
  if(tabla.length>0){
    let fila = "";
    $.each(tabla,function(key, valor){
      fila += '<tr>';
      fila += '<td>'+(valor.codsocio)+'</td>';
      fila += '<td>'+(valor.reciboluz)+'</td>';
      fila += '<td><span style="font-size:12px;color:#999;">'+(valor.duitxt)+': '+(valor.duinro)+'</span><br>'+(valor.socio)+'</td>';
      fila += '<td><span style="font-size:12px;color:#999;">'+(valor.ubigeo)+'</span><br>'+(valor.direccion)+'</td>';
      fila += '<td style="text-align:right;">'+(valor.crediPI)+'</td>';
      fila += '<td style="text-align:right;">'+(valor.crediPA)+'</td>';
      fila += '<td style="text-align:right;">'+(valor.crediPI+valor.crediPA)+'</td>';
      fila += '</tr>';
    });
    $('#grdDatosBody').html(fila);
  }else{
    $('#grdDatosBody').html('<tr><td colspan="10" style="text-align:center;color:red;">Sin Resultados</td></tr>');
  }
  $('#grdDatosCount').html(tabla.length);
}

function appGridReset(){
  appGridAll();
}

function appBuscaDirBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { appGridAll(); }
}
