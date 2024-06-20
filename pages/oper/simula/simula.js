const rutaSQL = "pages/oper/simula/sql.php";

//=========================funciones para Simulacion Ahorros============================
function appAhorrosFechaFin(){
  let tiempo = $("#txt_TiempoMeses").val();
  let fechaIni = appConvertToFecha($("#date_FechaIni").val(),"-");
  let fechaFin = moment(fechaIni).add(tiempo,'months');

  $("#date_FechaFin").html(fechaFin.format("DD/MM/YYYY"));
  $("#dias_FechaFin").html(fechaFin.diff(fechaIni,'days'));
}

async function appAhorrosReset(){
  $('#date_FechaIni').datepicker("setDate",moment().format("DD/MM/YYYY"));
  appAhorrosFechaFin();
  try{
    const resp = await appAsynFetch({ TipoQuery:"selProductos" },rutaSQL);
    appLlenarDataEnComboBox(resp,"#cbo_Productos",0); 
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}

async function appAhorrosGenerarIntereses(){
  let tiempo = $("#txt_TiempoMeses").val();
  let productoID = $("#cbo_Productos").val();
  let fecha = appConvertToFecha($("#date_FechaIni").val(),"-");
  let capital = appConvertToNumero($("#txt_Importe").val());
  try{
    const resp = await appAsynFetch({
      TipoQuery : 'simulaAhorro',
      fechaIni : moment(fecha).format("YYYYMMDD"),
      fechaFin : moment(fecha).add(tiempo,'months').format("YYYYMMDD"),
      productoID : productoID,
      importe : capital,
      segDesgr : 0.1,
      tasa : appConvertToNumero($("#txt_Tasa").val())
    }, rutaSQL);

    //respuesta
    let fila = "";
    let total = 0;
    let interes = appConvertToNumero(resp.interes);
    switch(productoID){
      case "106": //ahorrosuperpension
        interes = interes/tiempo;
        total = capital+interes;
        for(x=1; x<=tiempo; x++){
          fila += '<tr>'+
                   '<td>'+(x)+'</td>'+
                   '<td>'+(moment(fecha).add(x,'months').format("DD/MM/YYYY"))+'</td>'+
                   '<td style="text-align:right;">'+appFormatMoney((x==tiempo)?capital:0,2)+'</td>'+
                   '<td style="text-align:right;">'+appFormatMoney(interes,2)+'</td>'+
                   '<td style="text-align:right;">'+appFormatMoney((x==tiempo)?total:interes,2)+'</td>'+
                   '<td></td>'+
                   '</tr>';
        }
        fila += '<tr style="color:blue;">'+
                '<td colspan="2" style="text-align:center;">TOTAL</td>'+
                '<td style="text-align:right;">'+appFormatMoney(capital,2)+'</td>'+
                '<td style="text-align:right;">'+appFormatMoney(appConvertToNumero(resp.interes),2)+'</td>'+
                '<td style="text-align:right;">'+appFormatMoney(capital+appConvertToNumero(resp.interes),2)+'</td>'+
                '<td></td>'+
                '</tr>';
        break;
      default:
        fila += '<tr>'+
                '<td>'+(1)+'</td>'+
                '<td>'+(moment(fecha).add(tiempo,'months').format("DD/MM/YYYY"))+'</td>'+
                '<td style="text-align:right;">'+appFormatMoney(capital,2)+'</td>'+
                '<td style="text-align:right;">'+appFormatMoney(interes,2)+'</td>'+
                '<td style="text-align:right;">'+appFormatMoney(capital+interes,2)+'</td>'+
                '<td></td>'+
                '</tr>';
        break;
    }
    $('#grdDatos').html(fila);
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}


//=========================funciones para Simulacion Creditos============================
function appCreditosReset(){
  $('#txt_FechaSimula').datepicker("setDate",moment().format("DD/MM/YYYY"));
  $('#txt_FechaPriCuota').datepicker("setDate",moment().add(1,'M').format("DD/MM/YYYY"));
  $('#txt_TEA').val(30);
  $('#txt_NroCuotas').val(12);
  $('#txt_Importe').val(1000);
  $('#txt_SegDesgr').val(0.1);
  $('#txt_Frecuencia').val(14);
  $('#grdDatos').html("");
  $('#lbl_TEA').html("0.00 %");
  $('#lbl_TEM').html("0.00 %");
  $('#lbl_TED').html("0.00 %");
}

function appCreditosCambiarTipoCredito(){
  switch($("#cbo_TipoCredito").val()){
    case "1":
      $("#div_FechaPriCuota").show();
      $("#div_Frecuencia").hide();
      break;
    case "2":
      $("#div_Frecuencia").show();
      $("#div_FechaPriCuota").hide();
      break;
  }
}

async function appCreditosGenerarPlanPagos(){
  $('#grdDatos').html('<tr><td colspan="9"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  try{
    const resp = await appAsynFetch({
      TipoQuery : 'simulaCredito',
      TipoCredito : $("#cbo_TipoCredito").val(),
      importe : appConvertToNumero($("#txt_Importe").val()),
      TEA : $("#txt_TEA").val(),
      segDesgr : $("#txt_SegDesgr").val(),
      nroCuotas: $("#txt_NroCuotas").val(),
      fecha : appConvertToFecha($("#txt_FechaSimula").val(),""),
      pricuota : appConvertToFecha($("#txt_FechaPriCuota").val(),""),
      frecuencia : $("#txt_Frecuencia").val()
    }, rutaSQL);

    //respuesta
    if(resp.tabla.length>0){
      let fila = "";
      let tot_Cuota = 0;
      let tot_Capital = 0;
      let tot_Interes = 0;
      let tot_Desgrav = 0;

      resp.tabla.forEach((valor,key)=>{
        tot_Cuota += Number(valor.cuota);
        tot_Capital += Number(valor.capital);
        tot_Interes += Number(valor.interes);
        tot_Desgrav += Number(valor.desgr);

        fila += '<tr>'+
                '<td>'+(valor.nro)+'</td>'+
                '<td style="color:#aaa;">'+(valor.dias)+'</td>'+
                '<td>'+(moment(valor.fecha).format("DD/MM/YYYY"))+'</td>'+
                '<td style="text-align:right;">'+appFormatMoney(valor.cuota,2)+'</td>'+
                '<td style="text-align:right;">'+appFormatMoney(valor.capital,2)+'</td>'+
                '<td style="text-align:right;">'+appFormatMoney(valor.interes,2)+'</td>'+
                '<td style="text-align:right;">'+appFormatMoney(valor.desgr,2)+'</td>'+
                '<td style="text-align:right;">'+appFormatMoney(valor.saldo,2)+'</td>'+
                '<td></td>'+
                '</tr>';
      });
      //totales
      fila += '<tr>'+
              '<td style="text-align:center;" colspan="3"><b>Totales</b></td>'+
              '<td style="text-align:right;"><b>'+appFormatMoney(tot_Cuota,2)+'</b></td>'+
              '<td style="text-align:right;"><b>'+appFormatMoney(tot_Capital,2)+'</b></td>'+
              '<td style="text-align:right;"><b>'+appFormatMoney(tot_Interes,2)+'</b></td>'+
              '<td style="text-align:right;"><b>'+appFormatMoney(tot_Desgrav,2)+'</b></td>'+
              '<td style="" colspan="2"></td>'+
              '</tr>';

      $('#grdDatos').html(fila);
      $('#lbl_TEA').html(appFormatMoney(resp.tea,2)+" %");
      $('#lbl_TEM').html(resp.tem+" %");
      $('#lbl_TED').html(resp.ted+" %");
    }else{
      $('#grdDatos').html("");
    }
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}