const rutaSQL = "pages/caja/aportes/sql.php";
const cIngreso = 1;
const cRetiro = 0;
var menu = "";
var agenciaID = null;
var tipoOperAporte = null;
var aporte = null;

//=========================funciones para Personas============================
function modalAporte_keyBuscar(e){ if(e.keyCode === 13) { modalAporteBuscar(); } }

async function appPagosReset(){
  $('#lbl_aporteTipoDUI').text("DUI");
  $('#lbl_aporteSocio, #lbl_aporteNroDUI, #lbl_aporteSaldo').text("");
  $('#txt_aporteFecha, #txt_aporteImporte').val("");
  $('#cbo_aporteMedioPago, #cbo_aporteMonedas').empty("");
  
  try{
    const resp = await appAsynFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php");
    menu = JSON.parse(resp.menu);
    agenciaID = resp.agenciaID;
    tipoOperAporte = null;
    aporte = {
      id : null,
      saldo : null,
      socioID : null,
      productoID : null,
      esObligatorio : null
    }
    $("#btn_NEW").toggle(menu.caja.submenu.aportes.cmdInsert==1);
    $("#btn_RET").toggle(menu.caja.submenu.aportes.cmdInsert==1);
    $("#btn_EXEC").prop('disabled', true);
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}

function appAportesBotonIngreso(){
  tipoOperAporte = cIngreso;
  appPreparaModalPersonas();
}

function appAportesBotonRetiro(){
  tipoOperAporte = cRetiro;
  appPreparaModalPersonas();
}

async function appAportesBotonExec(){
  let importe = appConvertToNumero($("#txt_aporteImporte").val());
  if(isNaN(importe)){
    alert("el IMPORTE debe ser una cantidad valida");
  } else {
    if(importe<=0){
      alert("el IMPORTE debe ser mayor a cero 0.00");
    } else {
      if(tipoOperAporte==cRetiro && importe>aporte.saldo){ //controlamos el saldo mayor a cero
        alert("!!!NO se puede retirar un monto mayor al SALDO!!!");
      } else {
        if(tipoOperAporte==cRetiro && aporte.esObligatorio==1 && importe==aporte.saldo){ //controlamos si es obligatorio que tenga saldo
          alert("!!!El saldo NO puede quedar en CERO 0.00!!!");
        } else {
          if(confirm("¿Esta seguro de continuar con la operacion?")){
            try{
              const resp = await appAsynFetch({
                TipoQuery : 'insOperacion',
                agenciaID : agenciaID,
                saldoID : aporte.id,
                socioID : aporte.socioID,
                productoID : aporte.productoID,
                medioPagoID : $("#cbo_aporteMedioPago").val()*1,
                monedaID : $("#cbo_aporteMonedas").val()*1,
                tipoOperAporte : tipoOperAporte,
                importe : importe
              }, rutaSQL);

              //respuesta
              if (!resp.error) { 
                if(confirm("¿Desea Imprimir el pago?")){
                  $("#modalPrint").modal("show");
                  let urlServer = appUrlServer()+"pages/caja/aportes/rpt.voucher.php?movimID="+resp.movimID;
                  $("#contenedorFrame").html('<object id="objPDF" type="text/html" data="'+urlServer+'" width="100%" height="500px"></object>');
                }
                appPagosReset();
              }
            } catch(err){
              console.error('Error al cargar datos:'+err);
            }
          }
        }
      }
    }
  }
}

function modalAporteBuscar(){
  $("#modalAporte_Grid").hide();
  if($("#modalAporte_TxtBuscar").val().length>=3){ 
    modalAporteGrid();
  } else { 
    $('#modalAporte_Wait').html('<div class="callout callout-warning"><h4>Demasiado Corto</h4><p>El NRO de documento de Identidad debe tener como minimo <b>4 numeros</b></p></div>'); 
  }
}

async function modalAporteGrid(){
  $('#modalAporte_Wait').html('<div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></div>');
  const txtBuscar = $("#modalAporte_TxtBuscar").val();
  try{
    const resp = await appAsynFetch({ TipoQuery:'selAportes', buscar:txtBuscar }, rutaSQL);
    $('#modalAporte_Wait').html("");
    $("#modalAporte_Grid").show();
    if(resp.aportes.length>0){
      let fila = "";
      resp.aportes.forEach((valor,key)=>{
        fila += '<tr>'+
                '<td>'+(valor.nro_DUI)+'</td>'+
                '<td>'+(valor.socio)+'</td>'+
                '<td><a href="javascript:appAportesOperView('+(valor.ID)+');">'+(valor.codigo+' &raquo; '+valor.producto+'; '+valor.mon_abrevia+';')+'</a></td>'+
                '<td style="text-align:right;">'+(appFormatMoney(valor.saldo,2))+'</td>'+
                '</tr>';
      });
      $('#modalAporte_GridBody').html(fila);
    }else{
      $('#modalAporte_GridBody').html('<tr><td colspan="5" style="text-align:center;color:red;">Sin Resultados para '+txtBuscar+'</td></tr>');
    }
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}

async function appAportesOperView(saldoID){
  $('#modalAporte').modal('hide');
  try{
    const resp = await appAsynFetch({ TipoQuery:'viewAporte', saldoID }, rutaSQL);
    
    //respuesta
    aporte.id = (saldoID);
    aporte.saldo = (resp.aporte.saldo);
    aporte.socioID = (resp.aporte.socioID);
    aporte.productoID = (resp.aporte.productoID);
    aporte.esObligatorio = (resp.aporte.obliga);

    $('#lbl_aporteSocio').text(resp.aporte.socio);
    $('#lbl_aporteTipoDUI').text(resp.aporte.DUI);
    $('#lbl_aporteNroDUI').text(resp.aporte.nro_dui);
    $('#lbl_aporteSaldo').html(appFormatMoney(resp.aporte.saldo,2));
    appLlenarDataEnComboBox(resp.comboTipoPago,"#cbo_aporteMedioPago",0); //medios de pago
    appLlenarDataEnComboBox(resp.comboMonedas,"#cbo_aporteMonedas",0); //monedas
    $('#txt_aporteFecha').val(moment(resp.fecha).format("DD/MM/YYYY"));
    $("#btn_EXEC").prop('disabled',false).html((tipoOperAporte==cIngreso) ? '<i class="fa fa-plus"></i> Aplicar Ingreso' : '<i class="fa fa-minus"></i> Aplicar Retiro');
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}

function appPreparaModalPersonas(){
  $("#modalAporte_Grid").hide();
  $("#modalAporte_Titulo").html("Verificar Aportes por Doc. Identidad");
  $("#modalAporte_Wait").html("");
  $("#modalAporte_TxtBuscar").val("");
  $('#modalAporte').modal({keyboard:true}).on('shown.bs.modal', ()=> { $("#modalAporte_TxtBuscar").focus(); });
}