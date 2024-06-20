const rutaSQL = "pages/master/tipos/sql.php";
var menu = "";

//=========================funciones para Personas============================
function appTiposBuscar(e){ if(e.keyCode === 13) { appTiposGrid(); } }

async function appTiposGrid(){
  $('#grdDatos').html('<tr><td colspan="9"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  const txtBuscar = $("#txtBuscar").val().toUpperCase();
  const cboTipo = $("#cbo_Tipos").val();
  try{
    const resp = await appAsynFetch({ TipoQuery:'selTipos', tipo:cboTipo, buscar:txtBuscar },rutaSQL);
    //respuesta
    if(resp.tipos.length>0){
      let fila = "";
      resp.tipos.forEach((valor,key)=>{
        fila += '<tr>'+
                '<td>'+(valor.ID)+'</td>'+
                '<td style="text-align:center;">'+((valor.tipoID!=null)?('<i class="fa fa-info-circle" title="Este ID esta habilitado para esta coopac" style="color:#0097BC;"></i>'):(''))+'</td>'+
                '<td>'+(valor.nombre)+'</td>'+
                '<td>'+(valor.codigo)+'</td>'+
                '<td>'+(valor.abrevia)+'</td>'+
                '<td style="text-align:center;">'+(valor.tipo)+'</td>'+
                '<td style="text-align:center;">'+(valor.padreID)+'</td>'+
                '<td>'+(valor.nivel)+'</td>'+
                '<td></td>'+
                '</tr>';
      });
      $('#grdDatos').html(fila);
    }else{
      $('#grdDatos').html('<tr><td colspan="9" style="text-align:center;color:red;">Sin Resultados '+((txtBuscar=="")?(""):("para "+txtBuscar))+'</td></tr>');
    }
    $('#grdCount').html(resp.tipos.length);
    $('#div_info').toggle(cboTipo == 5);
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appTiposReset(){
  $("#txtBuscar").val("");
  try{
    const resp = await appAsynFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php");
    menu = JSON.parse(resp.menu);
    
    const rpta = await appAsynFetch({TipoQuery:'startTipos'},rutaSQL);
    appLlenarDataEnComboBox(rpta.comboTipos,"#cbo_Tipos",0); //tipos de pago
    appTiposGrid();
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appTiposCancel(){
  $('#grid').show();
  $('#edit').hide();
  appTiposGrid();
}

async function appTipoView(tipoID){
  $('#grid, #btnInsert').hide();
  $('#edit, #btnUpdate').show();
  $(".form-group").removeClass("has-error");
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
    console.error('Error al cargar datos:', err);
  }
}
