const rutaSQL = "pages/caja/pagos/sql.php";
var menu = "";
var pago = null;
var agenciaID = null;

//=========================funciones para Personas============================
function modalCredi_keyBuscar(e){ if(e.keyCode === 13) { modalCrediBuscar(); } }

async function appPagosReset(){
  $(".form-group").removeClass("has-error");
  $("#lbl_crediAtraso").css("color", "#777");
  $('#lbl_crediTipoDUI').html("DUI");
  $('#lbl_crediAtraso, #lbl_crediSocio, #lbl_crediNroDUI, #lbl_crediFecha, #lbl_crediMoneda, #lbl_crediProducto, #lbl_crediCodigo, #lbl_crediTasaCred, #lbl_crediTasaMora, #lbl_crediAgencia, #lbl_crediPromotor, #lbl_crediAnalista, #lbl_crediImporte, #lbl_crediSaldo').html("");
  $('#txt_DeudaCapital, #txt_DeudaInteres, #txt_DeudaMora, #txt_DeudaOtros, #txt_DeudaFecha, #txt_DeudaTotalNeto, #txt_DeudaImporte').val("");
  $('#cbo_DeudaMedioPago, #cbo_DeudaMonedas').html("");

  try{
    const resp = await appAsynFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php");
    pago = null;
    agenciaID = resp.agenciaID;
    menu = JSON.parse(resp.menu);
    
    $("#btn_PAGAR").prop("disabled", true);
    $("#btn_NEW").toggle(menu.caja.submenu.pagos.cmdInsert == 1);
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}

function appPagosBotonNuevo(){
  $("#modalCredi_Titulo").html("Verificar Creditos por Doc. Identidad");
  $("#modalCredi_Grid").hide();
  $("#modalCredi_Wait").html("");
  $("#modalCredi_TxtBuscar").val("");
  $('#modalCredi').modal({keyboard:true}).on('shown.bs.modal', ()=> { $("#modalCredi_TxtBuscar").focus(); });
}

async function appPagosBotonPagar(){
  let importe = appConvertToNumero($("#txt_DeudaImporte").val());
  $(".form-group").removeClass("has-error");
  if(!isNaN(importe)){
    if(importe>0){
      if(confirm("¿Esta seguro de continuar con el PAGO?")){
        try{
          const resp = await appAsynFetch({
            TipoQuery : 'insPago',
            agenciaID : agenciaID*1,
            socioID : pago.socioID,
            tasaMora : pago.tasaMora,
            prestamoID : pago.prestamoID,
            productoID : pago.productoID,
            codprod : $("#lbl_crediCodigo").html(),
            medioPagoID : $("#cbo_DeudaMedioPago").val()*1,
            monedaID : $("#cbo_DeudaMonedas").val()*1,
            importe : importe*1
          }, rutaSQL);
          
          //respuesta
          if (!resp.error) { 
            if(confirm("¿Desea Imprimir el pago?")){
              $("#modalPrint").modal("show");
              let urlServer = appUrlServer()+"pages/caja/pagos/rpt.voucher.php?movimID="+resp.movimID;
              $("#contenedorFrame").html('<object id="objPDF" type="text/html" data="'+urlServer+'" width="100%" height="500px"></object>');
            }
            appPagosReset();
          }
        } catch(err){
          console.error('Error al cargar datos:'+err);
        }
      }
    } else {
      alert("el IMPORTE debe ser mayor a cero 0.00");
      $("#div_DeudaImporte").addClass("has-error");
    }
  } else {
    alert("el IMPORTE debe ser una cantidad valida");
    $("#div_DeudaImporte").addClass("has-error");
  }
}

function modalCrediBuscar(){
  $("#modalCredi_Grid").hide();
  if($("#modalCredi_TxtBuscar").val().length>=3){ 
    modalCrediGrid();
  } else { 
    $('#modalCredi_Wait').html('<div class="callout callout-warning"><h4>Demasiado Corto</h4><p>El NRO de documento de Identidad debe tener como minimo <b>4 numeros</b></p></div>'); 
  }
}

async function modalCrediGrid(){
  $('#modalCredi_Wait').html('<div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></div>');
  const txtBuscar = $("#modalCredi_TxtBuscar").val();
  try{
    const resp = await appAsynFetch({ TipoQuery: 'selCreditos', buscar:txtBuscar }, rutaSQL);
    //respuesta
    $('#modalCredi_Wait').html("");
    $("#modalCredi_Grid").show();
    if(resp.prestamos.length>0){
      let fila = "";
      resp.prestamos.forEach((valor,key)=>{
        fila += '<tr>'+
                '<td>'+(valor.nro_DUI)+'</td>'+
                '<td>'+(valor.socio)+'</td>'+
                '<td><a href="javascript:appCreditoPagoView('+(valor.ID)+');">'+(valor.codigo+' &raquo; '+valor.producto+'; '+valor.mon_abrevia+'; '+appFormatMoney(valor.tasa,2)+'%')+'</a></td>'+
                '<td style="text-align:right;">'+(appFormatMoney(valor.importe,2))+'</td>'+
                '<td style="text-align:right;">'+(appFormatMoney(valor.saldo,2))+'</td>'+
                '</tr>';
      });
      $('#modalCredi_GridBody').html(fila);
    }else{
      $('#modalCredi_GridBody').html('<tr><td colspan="5" style="text-align:center;color:red;">Sin Resultados para '+txtBuscar+'</td></tr>');
    }
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}

async function appCreditoPagoView(prestamoID){
  $('#modalCredi').modal('hide');
  try{
    const resp = await appAsynFetch({ TipoQuery:'viewCredito', prestamoID }, rutaSQL);

    //respuesta
    appCredi_Cabecera_SetData(resp.cabecera);
    appCredi_Detalle_SetData(resp.detalle);
    appLlenarDataEnComboBox(resp.comboTipoPago,"#cbo_DeudaMedioPago",0); //medios de pago
    appLlenarDataEnComboBox(resp.comboMonedas,"#cbo_DeudaMonedas",0); //monedas
    $('#txt_DeudaFecha').datepicker("setDate",moment(resp.fecha).format("DD/MM/YYYY"));
    $("#btn_PAGAR").prop("disabled", false);
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}

function appCredi_Cabecera_SetData(data){
  $("#txt_DeudaFecha, #txt_DeudaMora").prop('disabled', (data.rolUser!==data.rolROOT));
  $("#lbl_crediAtraso").html(data.atraso).css('color', data.atraso > 0 ? '#D00' : '#777');

  pago = {
    tasaMora : data.mora,
    socioID : data.socioID,
    prestamoID : data.prestamoID,
    productoID : data.productoID
  }
  $('#lbl_crediSocio').html(data.socio);
  $('#lbl_crediTipoDUI').html(data.dui);
  $('#lbl_crediNroDUI').html(data.nro_dui);
  $('#lbl_crediFecha').html(moment(data.fecha_otorga).format("DD/MM/YYYY"));
  $('#lbl_crediMoneda').html(data.moneda+' <span style="font-size:10px;">('+data.mon_abrevia+')</span>');
  $('#lbl_crediProducto').html(data.producto);
  $('#lbl_crediCodigo').html(data.codigo);
  $('#lbl_crediTasaCred').html(appFormatMoney(data.tasa,2)+'% <span style="font-size:10px;">(TEA)</span>');
  $('#lbl_crediTasaMora').html(appFormatMoney(data.mora,2)+'% <span style="font-size:10px;">(TEA)</span>');
  $('#lbl_crediAgencia').html(data.agencia);
  $('#lbl_crediPromotor').html(data.promotor);
  $('#lbl_crediAnalista').html(data.analista);
  $('#lbl_crediImporte').html(appFormatMoney(data.importe,2));
  $('#lbl_crediSaldo').html(appFormatMoney(data.saldo,2));
}

function appCredi_Detalle_SetData(data){
  let total = data.capital+data.interes+data.mora+data.otros;
  $('#txt_DeudaCapital').val(appFormatMoney(data.capital,2));
  $('#txt_DeudaInteres').val(appFormatMoney(data.interes,2));
  $('#txt_DeudaMora').val(appFormatMoney(data.mora,2));
  $('#txt_DeudaOtros').val(appFormatMoney(data.otros,2));
  $('#txt_DeudaTotalNeto').val(appFormatMoney(total,2));
  $('#txt_DeudaImporte').val(appFormatMoney(total,2));
}
