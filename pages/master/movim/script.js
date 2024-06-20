const rutaSQL = "pages/master/movim/sql.php";
var menu = "";

//=========================funciones para Personas============================
function appMovimBuscar(e){ if(e.keyCode === 13) { appMovimGrid(); } }

async function appMovimGrid(){
  $('#grdDatos').html('<tr><td colspan="8"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  const txtBuscar = $("#txtBuscar").val().toUpperCase();
  try{
    const resp = await appAsynFetch({TipoQuery:'selMovims', buscar:txtBuscar},rutaSQL);
    if(resp.movs.length>0){
      let fila = "";
      resp.movs.forEach((valor,key)=>{
        fila += '<tr>'+
                '<td>'+(valor.ID)+'</td>'+
                '<td>'+(valor.nombre)+'</td>'+
                '<td>'+(valor.codigo)+'</td>'+
                '<td>'+(valor.abrevia)+'</td>'+
                '<td>'+(valor.tipo_operID)+'</td>'+
                '<td>'+(valor.in_out)+'</td>'+
                '<td>'+(valor.afec_prod)+'</td>'+
                '</tr>';
      });
      $('#grdDatos').html(fila);
    }else{
      $('#grdDatos').html('<tr><td colspan="8" style="text-align:center;color:red;">Sin Resultados '+((txtBuscar=="")?(""):("para "+txtBuscar))+'</td></tr>');
    }
    $('#grdCount').html(resp.movs.length);
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}

async function appMovimReset(){
  $("#txtBuscar").val("");
  try{
    const resp = await appAsynFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php");
    menu = JSON.parse(resp.menu);
    appMovimGrid();
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}

function appMovimCancel(){
  $('#grid').show();
  $('#edit').hide();
  appMovimGrid();
}

async function appMovimView(tipoID){
  $(".form-group").removeClass("has-error");
  $('#grid, #btnInsert').hide();
  $('#edit, #btnUpdate').show();
  try{
    const resp = await appAsynFetch({ TipoQuery:'viewTipo', ID:tipoID }, rutaSQL);

    //respuesta
    $("#hid_tipoID").val(resp.tipo.ID);
    $("#txt_Codigo").val(resp.tipo.codigo);
    $("#txt_Abrev").val(resp.tipo.abrevia);
    $("#txt_Nombre").val(resp.tipo.nombre);
    $("#txt_Tipo").val(resp.tipo.tipo);
    appLlenarDataEnComboBox(resp.comboTipos,"#cbo_Padre",resp.padreID);
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}
