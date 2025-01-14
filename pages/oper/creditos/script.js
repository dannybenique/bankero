const rutaSQL = "pages/oper/creditos/sql.php";
var viewTotalPagado = false;
var viewTotalPorVencer = false;
var menu = "";

//=========================funciones para Personas============================
function appCreditosBuscar(e){ if(e.keyCode === 13) { load_flag = 0; $('#grdDatosBody').html(""); appCreditosGrid(); } }

async function appCreditosGrid(){
  $('#grdDatos').html('<tr><td colspan="10"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  const txtBuscar = $("#txtBuscar").val();
  try{
    const resp = await appAsynFetch({
      TipoQuery: 'selCreditos',
      buscar: txtBuscar
    }, rutaSQL);

    //respuesta
    if(resp.tabla.length>0){
      let fila = "";
      resp.tabla.forEach((valor,key)=>{
        fila += '<tr>'+
                '<td>'+(moment(valor.fecha).format("DD/MM/YYYY"))+'</td>'+
                '<td>'+(valor.nro_dui)+'</td>'+
                '<td>'+(valor.socio)+'</td>'+
                '<td><a href="javascript:appCreditosView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.codigo+' &raquo; '+valor.producto+'; '+valor.mon_abrevia+'; '+appFormatMoney(valor.tasa,2))+'%</a></td>'+
                '<td>'+(valor.tiposbs)+'</td>'+
                '<td style="text-align:right;">'+appFormatMoney(valor.importe,2)+'</td>'+
                '<td style="text-align:right;">'+appFormatMoney(valor.saldo,2)+'</td>'+
                '<td style="text-align:center;">'+(valor.nro_cuotas)+'</td>'+
                '</tr>';
      });
      $('#grdDatos').html(fila);
    }else{
      let res = (txtBuscar==="") ? ("") : ("para "+txtBuscar);
      $('#grdDatos').html('<tr><td colspan="10" style="text-align:center;color:red;">Sin Resultados '+(res)+'</td></tr>');
    }
    $('#grdCount').html(resp.tabla.length+"/"+resp.cuenta);
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}

async function appCreditosReset(){
  $("#txtBuscar").val("");
  try{
    const resp = await appAsynFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php");
    menu = JSON.parse(resp.menu);
    appCreditosGrid();
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}

function appCreditosRefresh(){
  const prestamoID = $('#hid_crediID').val();
  appCreditosView(prestamoID);
}

function appCreditosBotonCancel(){
  appCreditosGrid();
  $('#grid').show();
  $('#edit').hide();
}

async function appCreditosView(prestamoID){
  try{
    const resp = await appAsynFetch({
      TipoQuery : 'viewCredito',
      prestamoID : prestamoID
    }, rutaSQL);
    
    // console.log(resp);
    appCabeceraSetData(resp.prestamo);
    appDetalleSetData(resp.detalle);
    $('#grid').hide();
    $('#edit').show();
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}

function appCabeceraSetData(data){
  $('#hid_crediID').val(data.ID);
  $('#lbl_crediSocio').html(data.socio);
  $('#lbl_crediTipoDUI').html(data.dui);
  $('#lbl_crediNroDUI').html(data.nro_dui);
  $('#lbl_crediID').html(data.ID);
  $('#lbl_crediFecha').html(moment(data.fecha_otorga).format("DD/MM/YYYY"));
  $('#lbl_crediProducto').html(data.producto);
  $('#lbl_crediCodigo').html(data.codigo);
  $('#lbl_crediCodigo').title = (data.ID);
  $('#lbl_crediTasaCred').html(appFormatMoney(data.tasa,2)+'% <span style="font-size:10px;">(TEA)</span>');
  $('#lbl_crediTasaMora').html(appFormatMoney(data.mora,2)+'% <span style="font-size:10px;">(TEA)</span>');
  $('#lbl_crediMoneda').html(data.moneda+' <span style="font-size:10px;">('+data.mon_abrevia+')</span>');
  $('#lbl_crediAgencia').html(data.agencia);
  $('#lbl_crediPromotor').html(data.promotor);
  $('#lbl_crediAnalista').html(data.analista);
  $('#lbl_crediImporte').html(appFormatMoney(data.importe,2));
  $('#lbl_crediSaldo').html(appFormatMoney(data.saldo,2));
}

function appDetalleSetData(data){
  let cuoTotal = 0;
  let cuoCapital = 0;
  let cuoInteres = 0;
  let cuoMora = 0;
  let cuoOtros = 0;
  let totGrayTotal = 0;
  let totGrayCapital = 0;
  let totGrayInteres = 0;
  let totGrayMora = 0;
  let totGrayOtros = 0;
  let totRedTotal = 0;
  let totRedCapital = 0;
  let totRedInteres = 0;
  let totRedMora = 0;
  let totRedOtros = 0;
  let totBlackTotal = 0;
  let totBlackCapital = 0;
  let totBlackInteres = 0;
  let totBlackMora = 0;
  let totBlackOtros = 0;
  let fecha = "";
  let fila = "";

  data.forEach((valor,key)=>{
    if(valor.capital==valor.pg_capital){ //cuota pagada
      totGrayTotal += valor.total+valor.pg_mora;
      totGrayCapital += valor.pg_capital;
      totGrayInteres += valor.pg_interes;
      totGrayMora += valor.pg_mora;
      totGrayOtros += valor.pg_otros;
    } else {
      if(valor.atraso>=0){ //cuota en deuda
        totRedTotal += valor.total+valor.mora;
        totRedCapital += valor.capital-valor.pg_capital;
        totRedInteres += valor.interes-valor.pg_interes;
        totRedMora += valor.mora-valor.pg_mora;
        totRedOtros += valor.otros-valor.pg_otros;
      } else { //cuota por vencer
        totBlackTotal += valor.total;
        totBlackCapital += valor.capital;
        totBlackInteres += valor.interes;
        totBlackMora += valor.mora;
        totBlackOtros += valor.otros;
      }
    }
    cuoOtros = (valor.capital==valor.pg_capital)?(valor.pg_otros):(valor.otros);
    cuoMora = (valor.capital==valor.pg_capital)?(valor.pg_mora):(valor.mora);
    cuoInteres = (valor.capital==valor.pg_capital)?(valor.pg_interes):(valor.interes-valor.pg_interes);
    cuoCapital = (valor.capital==valor.pg_capital)?(valor.pg_capital):(valor.capital-valor.pg_capital);
    cuoTotal = cuoCapital + cuoInteres + cuoMora + cuoOtros;
    fila += '<tr style="'+((valor.numero==0)?('color:#bbb;'):((valor.capital==valor.pg_capital)?('color:#bbb;'):((valor.atraso>=0)?("color:#f00;"):(""))))+'">'+
            '<td>'+(valor.numero)+'</td>'+
            '<td>'+((valor.numero>0)?(moment(valor.fecha).diff(fecha,"days")):(0))+'</td>'+
            '<td>'+(moment(valor.fecha).format("DD/MM/YYYY"))+'</td>'+
            '<td style="text-align:right;">'+appFormatMoney(cuoTotal,2)+'</td>'+
            '<td style="text-align:right;" title="Inicial:&nbsp;'+appFormatMoney(valor.capital,2)+'\nA Cta:&nbsp;'+appFormatMoney(valor.pg_capital,2)+'\nActual:&nbsp;'+appFormatMoney(valor.capital-valor.pg_capital,2)+'">'+appFormatMoney((cuoCapital),2)+'</td>'+
            '<td style="text-align:right;" title="Inicial:&nbsp;'+appFormatMoney(valor.interes,2)+'\nA Cta:&nbsp;'+appFormatMoney(valor.pg_interes,2)+'\nActual:&nbsp;'+appFormatMoney(valor.interes-valor.pg_interes,2)+'">'+appFormatMoney((cuoInteres),2)+'</td>'+
            '<td style="text-align:right;">'+appFormatMoney((cuoMora),2)+'</td>'+
            '<td style="text-align:right;">'+appFormatMoney((cuoOtros),2)+'</td>'+
            '<td style="text-align:right;">'+appFormatMoney(valor.saldo,2)+'</td>'+
            '<td style="text-align:center;">'+((valor.numero==0)?(0):((valor.atraso<0)?(0):(valor.atraso)))+'</td>'+
            '<td></td></tr>';
    fecha = valor.fecha;
  });
  
  if(viewTotalPagado){ //totales GRAY
    fila += '<tr style="color:#bbb;">'+
            '<td colspan="3" style="text-align:center;"><b>Total Pagado</b></td>'+
            '<td style="text-align:right;"><b>'+appFormatMoney(totGrayTotal,2)+'</b></td>'+
            '<td style="text-align:right;"><b>'+appFormatMoney(totGrayCapital,2)+'</b></td>'+
            '<td style="text-align:right;"><b>'+appFormatMoney(totGrayInteres,2)+'</b></td>'+
            '<td style="text-align:right;"><b>'+appFormatMoney(totGrayMora,2)+'</b></td>'+
            '<td style="text-align:right;"><b>'+appFormatMoney(totGrayOtros,2)+'</b></td>'+
            '<td colspan="3"></td>'+
            '</tr>';
  }
  //totales RED
  fila += '<tr style="color:red;">'+
          '<td colspan="3" style="text-align:center;"><b>Total Vencido</b></td>'+
          '<td style="text-align:right;"><b>'+appFormatMoney(totRedTotal,2)+'</b></td>'+
          '<td style="text-align:right;"><b>'+appFormatMoney(totRedCapital,2)+'</b></td>'+
          '<td style="text-align:right;"><b>'+appFormatMoney(totRedInteres,2)+'</b></td>'+
          '<td style="text-align:right;"><b>'+appFormatMoney(totRedMora,2)+'</b></td>'+
          '<td style="text-align:right;"><b>'+appFormatMoney(totRedOtros,2)+'</b></td>'+
          '<td colspan="3"></td>'+
          '</tr>';
  if(viewTotalPorVencer){ //totales BLACK
    fila += '<tr id="trTotalPorVencer">'+
            '<td colspan="3" style="text-align:center;"><b>Total por Vencer</b></td>'+
            '<td style="text-align:right;"><b>'+appFormatMoney(totBlackTotal,2)+'</b></td>'+
            '<td style="text-align:right;"><b>'+appFormatMoney(totBlackCapital,2)+'</b></td>'+
            '<td style="text-align:right;"><b>'+appFormatMoney(totBlackInteres,2)+'</b></td>'+
            '<td style="text-align:right;"><b>'+appFormatMoney(totBlackMora,2)+'</b></td>'+
            '<td style="text-align:right;"><b>'+appFormatMoney(totBlackOtros,2)+'</b></td>'+
            '<td colspan="3"></td>'+
            '</tr>';
  }
  
  $('#grdDetalleDatos').html(fila);
}

function appCreditosViewTotalPagado(){
  $("#iconTotalPagado").html(viewTotalPagado==true)?('<i class="fa fa-toggle-off"></i>'):('<i class="fa fa-toggle-on"></i>');
  viewTotalPagado = !viewTotalPagado;
  appCreditosRefresh();
}

function appCreditosViewTotalPorVencer(){
  $("#iconTotalPorVencer").html(viewTotalPorVencer==true)?('<i class="fa fa-toggle-off"></i>'):('<i class="fa fa-toggle-on"></i>');
  viewTotalPorVencer = !viewTotalPorVencer;
  appCreditosRefresh();
}