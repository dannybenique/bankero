var rutaSQL = "pages/oper/simula/sql.php";
var arrCuotas = 0;
var nroActual = 0;

//=========================funciones para Simulacion Ahorros============================
function appAhorrosFechaFin(){
  let tiempo = $("#txt_TiempoMeses").val();
  let fechaIni = appConvertToFecha($("#date_FechaIni").val(),"-");
  let fechaFin = moment(fechaIni).add(tiempo,'months');

  $("#date_FechaFin").html(fechaFin.format("DD/MM/YYYY"));
  $("#dias_FechaFin").html(fechaFin.diff(fechaIni,'days'));
}

function appAhorrosReset(){
  $('#date_FechaIni').datepicker("setDate",moment().format("DD/MM/YYYY"));
  appAhorrosFechaFin();
  appAjaxSelect({TipoQuery:"comboAhorros"},rutaSQL).done(function(resp){ appLlenarDataEnComboBox(resp,"#cbo_Productos",0); });
}

function appAhorrosGenerarIntereses(){
  let tiempo = $("#txt_TiempoMeses").val();
  let productoID = $("#cbo_Productos").val();
  let fecha = appConvertToFecha($("#date_FechaIni").val(),"-");
  let capital = appConvertToNumero($("#txt_Importe").val());
  let datos = {
    TipoQuery : 'simulaAhorro',
    fechaIni : moment(fecha).format("YYYYMMDD"),
    fechaFin : moment(fecha).add(tiempo,'months').format("YYYYMMDD"),
    productoID : productoID,
    importe : capital,
    segDesgr : 0.1,
    tasa : appConvertToNumero($("#txt_Tasa").val())
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    let fila = "";
    let total = 0;
    let interes = appConvertToNumero(resp.interes);

    switch(productoID){
      case "106":
      case "127": //ahorrosuperpension
        interes = interes/tiempo;
        total = capital+interes;
        for(x=1; x<=tiempo; x++){
          fila += '<tr>';
          fila += '<td>'+(x)+'</td>';
          fila += '<td>'+(moment(fecha).add(x,'months').format("DD/MM/YYYY"))+'</td>';
          fila += '<td style="text-align:right;">'+appFormatMoney((x==tiempo)?capital:0,2)+'</td>';
          fila += '<td style="text-align:right;">'+appFormatMoney(interes,2)+'</td>';
          fila += '<td style="text-align:right;">'+appFormatMoney((x==tiempo)?total:interes,2)+'</td>';
          fila += '<td></td>';
          fila += '</tr>';
        }
        fila += '<tr style="color:blue;">';
        fila += '<td colspan="2" style="text-align:center;">TOTAL</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(capital,2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(appConvertToNumero(resp.interes),2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(capital+appConvertToNumero(resp.interes),2)+'</td>';
        fila += '<td></td>';
        fila += '</tr>';
        break;
      default:
        fila += '<tr>';
        fila += '<td>'+(1)+'</td>';
        fila += '<td>'+(moment(fecha).add(tiempo,'months').format("DD/MM/YYYY"))+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(capital,2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(interes,2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(capital+interes,2)+'</td>';
        fila += '<td></td>';
        fila += '</tr>';
        break;
    }

    $('#grdDatosBody').html(fila);
  });
}

//=========================funciones para Simulacion Creditos============================
function appCreditosReset(){
  let fecha = moment().format("DD/MM/YYYY");
  $('#date_fechaIniSimula').datepicker("setDate",fecha);
  //$('#date_fechaPriCuotaSimula').datepicker("setDate",moment().add(1,'M').format("DD/MM/YYYY"))
  $("#cbo_producto").val(1);
  $("#txt_TasaMensual").val("1.0");
  $("#txt_NroCuotas").val("12");
  $("#txt_Importe").val("1100");
  $("#txt_SegDesgr").val("0.10");
  arrCuotas = 0;
}

function appCreditosGenerarPlanPagos(){
  let FechaIni = appConvertToFecha($("#date_fechaIniSimula").val(),"");
  let FechaPri = moment(appConvertToFecha($("#date_fechaIniSimula").val(),"-")).add(1,'M').format("YYYYMMDD");
  let Importe  = appConvertToNumero($("#txt_Importe").val());
  let SegDesgr = $("#txt_SegDesgr").val();
  let TasaMensual = $("#txt_TasaMensual").val();

  let datos = {
    TipoQuery   : 'simulaCredito',
    segDesgr    : SegDesgr,
    fechaIni    : FechaIni,
    fechaPri    : FechaPri,
    importe     : Importe,
    tasaMensual : TasaMensual,
    nroCuotas   : $("#txt_NroCuotas").val()
  }

  appAjaxSelect(datos,rutaSQL).done(function(resp){
    $("#lbl_TED").html(appFormatMoney(resp.TED,6)+"%");
    $("#lbl_TEM").html(appFormatMoney(TasaMensual,2)+"%");
    $("#lbl_TEA").html(appFormatMoney(resp.TEA,2)+"%");
    $("#lbl_TCEA").html(appFormatMoney(resp.TCEA,2)+"%");
    arrCuotas = resp.tabla;
    appCreditosLlenarCuotas(arrCuotas);
  });
}

function appCreditosLlenarCuotas(cuotas){
  if(cuotas.length>0){
    let fila = "";
    let tot_Total = 0;
    let tot_Capital = 0;
    let tot_Interes = 0;
    let tot_Desgrav = 0;

    $.each(cuotas,function(key, valor){
      tot_Total += Number(valor.total);
      tot_Capital += Number(valor.capital);
      tot_Interes += Number(valor.interes);
      tot_Desgrav += Number(valor.desgrav);

      fila += '<tr '+((valor.capital<=0 && valor.nro>0)?('style="color:red;"'):(''))+'>';
      fila += '<td style="color:#bbb;">'+(valor.dias)+'</td>';
      fila += '<td>'+(valor.nro)+'</td>';
      fila += '<td style="text-align:center;"><a href="javascript:appCreditosCambiarFecha('+(valor.nro)+');">'+(moment(valor.fecha.date).format("DD/MM/YYYY"))+'</a></td>';
      fila += '<td style="text-align:right;"><a href="javascript:appCreditosCambiarCuota('+(valor.nro)+');">'+appFormatMoney(valor.total,2)+'</a></td>';
      fila += '<td style="text-align:right;">'+appFormatMoney(valor.capital,2)+'</td>';
      fila += '<td style="text-align:right;">'+appFormatMoney(valor.interes,2)+'</td>';
      fila += '<td style="text-align:right;">'+appFormatMoney(valor.desgrav,2)+'</td>';
      fila += '<td style="text-align:right;">'+appFormatMoney(valor.saldo,2)+'</td>';
      fila += '<td></td>';
      fila += '</tr>';
    });
    //totales
    fila += '<tr>';
    fila += '<td style="text-align:center" colspan="3"><strong>Totales</strong></td>';
    fila += '<td style="text-align:right;"><strong>'+appFormatMoney(tot_Total,2)+'</strong></td>';
    fila += '<td style="text-align:right;"><strong>'+appFormatMoney(tot_Capital,2)+'</strong></td>';
    fila += '<td style="text-align:right;"><strong>'+appFormatMoney(tot_Interes,2)+'</strong></td>';
    fila += '<td style="text-align:right;"><strong>'+appFormatMoney(tot_Desgrav,2)+'</strong></td>';
    fila += '<td style="" colspan="2"></td>';
    fila += '</tr>';

    $('#grdDatosBody').html(fila);
  }else{
    $('#grdDatosBody').html("");
  }
}

function appCreditosCambiarFecha(nro){
  nroActual = nro;
  $("#mod_lblFechaCambio").html("&laquo; Cuota "+(nroActual)+" &raquo;");
  $("#mod_txtFechaCambio").datepicker("setDate",moment(arrCuotas[nroActual].fecha.date).format("DD/MM/YYYY"));
  $("#modalCambioFecha").modal("show");
}

function appCreditosCambiarCuota(nro){
  nroActual = nro;
  $("#mod_lblCuotaCambio").html("&laquo; Cuota "+(nroActual)+" &raquo;");
  $("#mod_txtCuotaCambio").val(appFormatMoney(arrCuotas[nroActual].total,2));
  $("#modalCambioCuota").modal({keyboard:true});
  $('#modalCambioCuota').on('shown.bs.modal', function() {
    $('#mod_txtCuotaCambio').trigger('focus');
    $('#mod_txtCuotaCambio').select();
  });
}

function modCambioFecha_Ejecutar(){
  let FechaIni = moment(arrCuotas[nroActual-1].fecha.date).format("YYYYMMDD");
  let FechaPri = appConvertToFecha($("#mod_txtFechaCambio").val(),"");
  let SaldoCap  = arrCuotas[nroActual-1].saldo;
  let TasaMensual = $("#txt_TasaMensual").val();
  let SegDesgr = $("#txt_SegDesgr").val();
  let Cuota = arrCuotas[nroActual-1].total;

  let datos = {
    TipoQuery : 'simulaCreditoCambio_Fecha',
    segDesgr  : SegDesgr,
    fechaIni  : FechaIni,
    fechaPri  : FechaPri,
    importe   : SaldoCap,
    tasaMensual : TasaMensual,
    nroCuotas : (arrCuotas.length) - nroActual,
    cuota     : Cuota
  }
  //console.log(datos);
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    for(var i=0;i<(resp.length-1);i++){
      arrCuotas[i + nroActual].dias = resp[i+1].dias;
      arrCuotas[i + nroActual].nro = (i + nroActual);
      arrCuotas[i + nroActual].fecha = resp[i+1].fecha;
      arrCuotas[i + nroActual].total = resp[i+1].total;
      arrCuotas[i + nroActual].aporte = resp[i+1].aporte;
      arrCuotas[i + nroActual].capital = resp[i+1].capital;
      arrCuotas[i + nroActual].interes = resp[i+1].interes;
      arrCuotas[i + nroActual].desgrav = resp[i+1].desgrav;
      arrCuotas[i + nroActual].saldo = resp[i+1].saldo;
    }
    appCreditosLlenarCuotas(arrCuotas);
    $("#modalCambioFecha").modal("hide");
  });
}

function modCambioCuota_Ejecutar(){
  let FechaIni = moment(arrCuotas[nroActual-1].fecha.date).format("YYYYMMDD");
  let FechaPri = moment(arrCuotas[nroActual].fecha.date).format("YYYYMMDD");
  let SaldoCap = arrCuotas[nroActual-1].saldo;
  let TasaMensual = $("#txt_TasaMensual").val();
  let SegDesgr = $("#txt_SegDesgr").val();
  let Cuota    = $("#mod_txtCuotaCambio").val();

  let datos = {
    TipoQuery : 'simulaCreditoCambio_Cuota',
    segDesgr  : SegDesgr,
    fechaIni  : FechaIni,
    fechaPri  : FechaPri,
    importe   : SaldoCap,
    tasaMensual : TasaMensual,
    nroCuotas : (arrCuotas.length) - nroActual,
    cuota     : Cuota
  }
  //console.log(datos);
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    for(var i=0;i<(resp.length-1);i++){
      arrCuotas[i + nroActual].dias = resp[i+1].dias;
      arrCuotas[i + nroActual].nro = (i + nroActual);
      arrCuotas[i + nroActual].fecha = resp[i+1].fecha;
      arrCuotas[i + nroActual].total = resp[i+1].total;
      arrCuotas[i + nroActual].aporte = resp[i+1].aporte;
      arrCuotas[i + nroActual].capital = resp[i+1].capital;
      arrCuotas[i + nroActual].interes = resp[i+1].interes;
      arrCuotas[i + nroActual].desgrav = resp[i+1].desgrav;
      arrCuotas[i + nroActual].saldo = resp[i+1].saldo;
    }
    appCreditosLlenarCuotas(arrCuotas);
    $("#modalCambioCuota").modal("hide");
  });
}

function modCambioCuota_OnKeyPress(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { modCambioCuota_Ejecutar(); }
}
