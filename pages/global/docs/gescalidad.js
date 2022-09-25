var rutaSQL = "pages/global/docs/sql.php";

//=========================funciones para workers============================
function appBotonReset(){
  appDocsGridAll();
}

function appBotonBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { appDocsGridAll(); }
}

function appBotonCancel(){
    $('#grid').show();
    $('#edit').hide();
}

function appDocsGridAll(){
  $('#grdDatosBody').html('<tr><td colspan="4"><br><div class="progress progress-sm active"><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div></td></tr>');
  var datos = {
    TipoQuery : 'selDocsGesCal'
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    if(resp.tabla.length>0){
      let fila = "";

      $.each(resp.tabla,function(key, valor){
        fila += '<tr>';
        fila += '<td></td>';
        fila += '<td>'+(valor.codigo)+'</td>';
        fila += '<td><a href=javascript:appDocsVisor("'+(valor.url)+'") title="'+(valor.ID)+'">'+(valor.nombre)+'</a></td>';
        fila += '<td>'+(moment(valor.fecha.date).format('DD/MM/YYYY'))+'</td>';
        fila += '</tr>';
      });
      $('#grdDatosBody').html(fila);
    }else{
      $('#grdDatosBody').html('<tr><td colspan="4" style="text-align:center;color:red;">Sin Resultados</td></tr>');
    }
    $('#grdDatosCount').html(resp.tabla.length);
  });
}

function appDocsVisor(url){
  let urlServer = appUrlServer()+url;
  $("#contenedorFrame").html('<object id="objPDF" type="text/html" data="'+urlServer+'" width="100%" height="'+(window.innerHeight*0.8)+'px"></object>');
  $('#modalVisor').modal();
}
