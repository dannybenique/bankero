const rutaSQL = "pages/caja/extornos/sql.php";
var menu = "";
var agenciaID = 0;

//=========================funciones para Personas============================
async function appPagosReset(){
  $("#lbl_crediAtraso").css("color", "#777");
  $('#lbl_crediTipoDUI').html("DUI");
  $('#lbl_crediTasaCred, #lbl_crediTasaMora').html("%");
  $('#lbl_crediAtraso, #lbl_crediSocio, #lbl_crediNroDUI, #lbl_crediFecha, #lbl_crediMoneda, #lbl_crediProducto, #lbl_crediCodigo, #lbl_crediAgencia, #lbl_crediPromotor, #lbl_crediAnalista, #lbl_crediImporte, #lbl_crediSaldo, #cbo_DeudaMedioPago, #cbo_DeudaMonedas').html("");
  $('#hid_crediID, #hid_crediProductoID, #hid_crediTasaMora, #hid_crediSocioID, #txt_DeudaCapital, #txt_DeudaInteres, #txt_DeudaMora, #txt_DeudaOtros, #txt_DeudaFecha, #txt_DeudaTotalNeto, #txt_DeudaImporte').val("");

  try{
    const resp = await appAsynFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php");
    menu = JSON.parse(resp.menu);
    agenciaID = resp.agenciaID;
    $("#btn_NEW").toggle(menu.caja.submenu.pagos.cmdInsert==1);
    $("#btn_PAGAR").prop("disabled", true);
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}

function appPagosBotonNuevo(){
  $("#modalCredi_Titulo").html("Verificar Creditos por Doc. Identidad");
  $("#modalCredi_Grid").hide();
  $("#modalCredi_Wait").html("");
  $("#modalCredi_Buscar").val("");
  $('#modalCredi').modal({keyboard:true}).on('shown.bs.modal', ()=> { $("#modalCredi_Buscar").focus(); });
}

async function appPagosBotonPagar(){
  let importe = appConvertToNumero($("#txt_DeudaImporte").val());
  if(!isNaN(importe)){
    if(importe>0){
      if(confirm("¿Esta seguro de continuar con el PAGO?")){
        try{
          const resp = await appAsynFetch({
            TipoQuery : 'insPago',
            agenciaID : agenciaID*1,
            codprod : $("#lbl_crediCodigo").html(),
            prestamoID : $("#hid_crediID").val()*1,
            medioPagoID : $("#cbo_DeudaMedioPago").val()*1,
            productoID : $("#hid_crediProductoID").val()*1,
            tasaMora : $('#hid_crediTasaMora').val()*1,
            socioID : $("#hid_crediSocioID").val()*1,
            monedaID : $("#cbo_DeudaMonedas").val()*1,
            importe : importe*1
          }, rutaSQL);
          //respuesta
          if (!resp.error) { 
            if(confirm("¿Desea Imprimir el desembolso?")){
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
    }
  } else {
    alert("el IMPORTE debe ser una cantidad valida");
  }
}

function modalCredi_keyBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) {
    $("#modalCredi_Grid").hide();
    if($("#"+e.srcElement.id).val().length>=3){ 
      modalCrediGrid();
    } else { 
      $('#modalCredi_Wait').html('<div class="callout callout-warning"><h4>Demasiado Corto</h4><p>El NRO de documento de Identidad debe tener como minimo <b>4 numeros</b></p></div>'); 
    }
  }
}

async function modalCrediGrid(){
  $('#modalCredi_Wait').html('<div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></div>');
  const txtBuscar = $("#modalCredi_Buscar").val();
  try{
    const resp = await appAsynFetch({ TipoQuery:'selCreditos', buscar:txtBuscar },rutaSQL);
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
    const resp = await appAsynFetch({
      TipoQuery : 'viewCredito',
      prestamoID : prestamoID
    }, rutaSQL);

    //respuesta
    //console.log(resp);
    appCredi_Cabecera_SetData(resp.cabecera);
    appCredi_Detalle_SetData(resp.detalle);
    appLlenarDataEnComboBox(resp.comboTipoPago,"#cbo_DeudaMedioPago",0); //medios de pago
    appLlenarDataEnComboBox(resp.comboMonedas,"#cbo_DeudaMonedas",0); //monedas
    $('#txt_DeudaFecha').val(moment(resp.fecha).format("DD/MM/YYYY"));
    $("#btn_PAGAR").prop("disabled", false);
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}

function appCredi_Cabecera_SetData(data){
  $("#lbl_crediAtraso").css("color", (data.atraso>0) ? ("#D00") : ("#777"));
  $('#lbl_crediAtraso').html(data.atraso);

  $('#hid_crediID').val(data.ID);
  $('#hid_crediSocioID').val(data.socioID);
  $('#hid_crediProductoID').val(data.productoID);
  $('#hid_crediTasaMora').val(data.mora),
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
