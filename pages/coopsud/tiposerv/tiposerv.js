let rutaSQL = "pages/coopsud/tiposerv/sql.php";
let globNumCuota = 0; //obtiene el numero de cuota para patear el interes al final
let globFinCuota = 0; //obtiene el numero de la ultima cuota
let globInteresCuota = 0;
let globInteresFinal = 0;
let globRedisNumCuotas = 0; //numero de cuotas restantes NO pagadas

//=========================funciones para coopSUD Prestamos============================
function appGridAll(){
  $('#grdDatosBody').html('<tr><td colspan="10" style="text-align:center;"><br><div class="progress progress-sm active" style=""><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div><br>Un momento, por favor...</td></tr>');
  let datos = {
    TipoQuery:'coopSUDservicio',
    tipo:$('#cboTipo').val(),
    buscar:$('#txtBuscar').val().trim()
  };
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    //console.log(resp);
    if(resp.length>0){
      let fila = "";
      $.each(resp,function(key, valor){
        fila += '<tr style="">';
        fila += '<td>'+(($('#cboTipo').val()=='02')?("Ahorros"):("Creditos"))+'</td>';
        fila += '<td style="text-align:center;">'+(valor.tipo_serv)+'</td>';
        fila += '<td>'+(valor.servicio)+'</td>';
        fila += '<td>'+(valor.interes_1)+'</td>';
        fila += '<td>'+(valor.interes_2)+'</td>';
        fila += '<td>'+(valor.interes_3)+'</td>';
        fila += '<td style="text-align:center;">'+(valor.apl_1)+'</td>';
        fila += '<td style="text-align:center;">'+(valor.apl_2)+'</td>';
        fila += '<td style="text-align:center;">'+(valor.apl_3)+'</td>';
        fila += '<td></td>';
        fila += '</tr>';
      });
      $('#grdDatosBody').html(fila);
    }else{
      $('#grdDatosBody').html('<tr><td colspan="10" style="text-align:center;color:red;">Sin Resultados</td></tr>');
    }
    $('#grdDatosCount').html(resp.length);
  });
}

function appGridReset(){
  $("#cboTipo").val('02');
  $("#txtBuscar").val('');
  $('#grdDatosBody').html('');
}

function appAhorrosBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { appGridAll(); }
}

function changeComboTipo(){
  $("#txtBuscar").val('');
  $("#txtBuscar").focus();
}
