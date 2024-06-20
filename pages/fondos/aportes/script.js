const rutaSQL = "pages/fondos/aportes/sql.php";
var menu = "";

//=========================funciones para Personas============================
function appAportesBuscar(e){ if(e.keyCode === 13) { appAportesGrid(); } }

async function appAportesGrid(){
  $('#grdDatos').html('<tr><td colspan="5"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  $("#chk_All").prop("disabled", !(menu.fondos.submenu.aportes.cmdDelete === 1));
  const disabledDelete = (menu.fondos.submenu.aportes.cmdDelete===1) ? "" : "disabled";
  const txtBuscar = $("#txtBuscar").val();
  
  try{
    const resp = await appAsynFetch({ TipoQuery:'aportes_sel', buscar:txtBuscar },rutaSQL);
    if(resp.tabla.length>0){
      let fila = "";
      resp.tabla.forEach((valor,key)=>{
        fila += '<tr style="color:'+((resp.obliga)?((valor.saldo>0)?("black"):("red")):("black"))+'">'+
                '<td>'+((valor.num_movim==0)?('<input type="checkbox" name="chk_Borrar" value="'+(valor.ID)+'" '+disabledDelete+'/>'):(''))+'</td>'+
                '<td>'+(valor.nro_dui)+'</td>'+
                '<td>'+(valor.socio)+'</td>'+
                '<td><a href="javascript:appAportesView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.codigo+' &raquo; '+valor.producto+'; '+valor.m_abrevia+';')+'</a></td>'+
                '<td style="text-align:right;">'+appFormatMoney(valor.saldo,2)+'</td>'+
                '</tr>';
      });
      $('#grdDatos').html(fila);
    }else{
      let res = (txtBuscar==="") ? ("") : ("para "+txtBuscar);
      $('#grdDatos').html('<tr><td colspan="5" style="text-align:center;color:red;">Sin Resultados '+(res)+'</td></tr>');
    }
    $('#grdCount').html(resp.tabla.length+"/"+resp.cuenta);
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}

async function appAportesReset(){
  $("#txtBuscar").val("");
  try{
    const resp = await appAsynFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php");
    menu = JSON.parse(resp.menu);
    $("#btn_DEL").toggle(menu.fondos.submenu.aportes.cmdDelete == 1);
    $("#btn_NEW").toggle(menu.fondos.submenu.aportes.cmdInsert == 1);
    appAportesGrid();
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}

function appAportesRefresh(){
  const aporteID = $('#hid_aporteID').val();
  appAportesView(aporteID);
}

function appAportesBotonCancel(){
  appAportesGrid();
  $('#grid').show();
  $('#edit').hide();
}

async function appAportesBotonNuevo(){
  Persona.openBuscar('VerifyAportes',rutaSQL,false,true,false);
  $('#btn_modPersAddToForm').off('click').on('click',async function(e) {
    if(confirm('Confirme que realmente desea agregar APORTES a este socio')){
      try{
        const resp = await appAsynFetch({ TipoQuery:'insAportes', socioID:Persona.tablaPers.ID }, rutaSQL);
        
        appAportesGrid();
        Persona.close();
        e.stopImmediatePropagation();
        $('#btn_modPersAddToForm').off('click');
      } catch(err){
        console.error('Error al cargar datos:'+err);
      }
    }
  });
}

async function appAportesBotonBorrar(){
  const arr = $('[name="chk_Borrar"]:checked').map(function() { return this.value}).get();
  if(arr.length>0){
    if(confirm("¿Esta seguro de continuar?")) {
      try{
        const resp = await appAsynFetch({ TipoQuery:'delAportes', arr:arr },rutaSQL);
        if(!resp.error) { appAportesBotonCancel(); }
      } catch(err){
        console.error('Error al cargar datos:'+err);
      }
    }
  } else {
    alert("NO eligio borrar ninguno");
  }
}

async function appAportesView(aporteID){
  try{
    const resp = await appAsynFetch({ TipoQuery:'viewAporte', aporteID:aporteID }, rutaSQL);
    
    //respuesta
    appAportesSetData(resp.aporte);
    appMovimSetData(resp.movim);
    $('#grid').hide();
    $('#edit').show();
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}

function appAportesSetData(data){
  $('#hid_aporteID').val(data.ID);
  $('#lbl_aporteSocio').html(data.socio);
  $('#lbl_aporteTipoDUI').html(data.dui);
  $('#lbl_aporteNroDUI').html(data.nro_dui);
  $('#lbl_aporteCodigo').html(data.cod_prod);
  $('#lbl_aporteSaldo').html(appFormatMoney(data.saldo,2));
}

function appMovimSetData(data){
  // console.log(data);
  let totIngresos = 0;
  let totSalidas = 0;
  let totOtros = 0;
  let fila = "";
  data.forEach((valor,key)=>{
    totIngresos += valor.ingresos;
    totSalidas += valor.salidas;
    totOtros += valor.otros;
    fila += '<tr>'+
            '<td>'+(valor.ag)+'</td>'+
            '<td>'+(valor.us)+'</td>'+
            '<td style="text-align:center;">'+(moment(valor.fecha).format("DD/MM/YYYY"))+'</td>'+
            '<td style="text-align:center;">'+(valor.codigo)+'</td>'+
            '<td>'+(valor.codmov+' '+valor.movim)+'</td>'+
            '<td style="text-align:right;">'+((valor.ingresos>0)?(appFormatMoney(valor.ingresos,2)):(''))+'</td>'+
            '<td style="text-align:right;">'+((valor.salidas>0)?(appFormatMoney(valor.salidas,2)):(''))+'</td>'+
            '<td style="text-align:right;">'+((valor.otros>0)?appFormatMoney(valor.otros,2):(''))+'</td>'+
            '</tr>';
  });
  fila += '<tr>'+
          '<td colspan="5" style="text-align:center;"><b>Total</b></td>'+
          '<td style="text-align:right;"><b>'+appFormatMoney(totIngresos,2)+'</b></td>'+
          '<td style="text-align:right;"><b>'+appFormatMoney(totSalidas,2)+'</b></td>'+
          '<td style="text-align:right;"><b>'+appFormatMoney(totOtros,2)+'</b></td>'+
          '</tr>';
  $('#grdDetalleDatos').html(fila);
  $('#lbl_movimSaldo').html(appFormatMoney(totIngresos-totSalidas,2));
}
