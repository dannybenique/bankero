const rutaSQL = "pages/repo/movim/sql.php";
var menu = "";

//=========================funciones para Personas============================
async function appMovimGrid(){
  $('#grdDatos').html('<tr><td colspan="7"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  try{
    const resp = await appAsynFetch({ 
      TipoQuery: 'selMovim',
      agenciaID: $('#cboAgencias').val(),
      usuarioID: $('#cboUsuarios').val(),
      monedaID: $('#cboMonedas').val(),
      fecha: appConvertToFecha($('#txtFecha').val(),'')
    }, rutaSQL);

    //respuesta
    if(resp.movim.length>0){
      let totIngresos = 0;
      let totSalidas = 0;
      let fila = "";
      let foot = "";
      resp.movim.forEach((valor,key)=>{
        totIngresos += valor.ingreso;
        totSalidas += valor.salida;
        fila += '<tr>'+
                '<td>'+(valor.hora)+'</td>'+
                '<td>'+(valor.voucher)+'</td>'+
                '<td>'+(valor.codsocio+' '+valor.socio)+'</td>'+
                '<td>'+(valor.codprod+' '+valor.producto)+'</td>'+
                '<td>'+(valor.codmov+' '+valor.movim)+'</td>'+
                '<td style="text-align:right;">'+((valor.ingreso>0)?(appFormatMoney(valor.ingreso,2)):('-'))+'</td>'+
                '<td style="text-align:right;">'+((valor.salida>0)?(appFormatMoney(valor.salida,2)):('-'))+'</td>'+
                '</tr>';
      });
      foot = '<tr>'+
              '<td colspan="5" style="text-align:right;"><b>TOTAL GENERAL</b></td>'+
              '<td style="text-align:right;border-bottom-style:double;"><b>'+(appFormatMoney(totIngresos,2))+'</b></td>'+
              '<td style="text-align:right;border-bottom-style:double;"><b>'+(appFormatMoney(totSalidas,2))+'</b></td>'+
              '</tr>'+
              '<tr><td colspan="7"></td></tr>';
      $('#grdDatos').html(fila);
      
    }else{
      $('#grdDatos').html('<tr><td colspan="7" style="text-align:center;color:red;">Sin Resultados</td></tr>');
    }
    $('#grdCount').html(resp.movim.length);
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appMovimReset(){
  try{
    const resp = await appAsynFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php");
    menu = JSON.parse(resp.menu);

    const rpta = await appAsynFetch({ TipoQuery:'StartMovim' }, rutaSQL);
    appLlenarDataEnComboBox(rpta.comboAgencias,"#cboAgencias",0);
    appLlenarDataEnComboBox(rpta.comboMonedas,"#cboMonedas",0);
    appLlenarDataEnComboBox(rpta.comboUsuarios,"#cboUsuarios",0);
    $('#txtFecha').datepicker("setDate",moment().format("DD/MM/YYYY"));
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appMovimBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { appMovimGrid(); }
}

async function appMovimView(voucherID){
  $(".form-group").removeClass("has-error");
  $('#grid').hide();
  $('#edit').show();

  try{
    const resp = await appAsynFetch({
      TipoQuery : 'viewMovim',
      voucherID : voucherID
    },rutaSQL);
    
    //cabecera
    $("#hid_movimID").val(resp.cab.ID);
    $("#lbl_pagoAgencia").html(resp.cab.agencia);
    $("#lbl_pagoTipoOper").html(resp.cab.tipo_oper+" / "+resp.cab.moneda);
    $("#lbl_pagoCodigo").html(resp.cab.codigo);
    $("#lbl_pagoFecha").html(resp.cab.fecha+" <small style='font-size:10px;'>"+resp.cab.hora+"</small>");
    $("#lbl_pagoSocio").html(resp.cab.socio);
    $("#lbl_tipodui").html(resp.cab.tipodui+":");
    $("#lbl_pagoNroDUI").html(resp.cab.nrodui);
    $("#lbl_pagoCajera").html(resp.cab.cajera);
    $("#lbl_pagoImporte").html("<small style='font-size:10px;'>"+resp.cab.mon_abrevia+"</small> "+appFormatMoney(resp.cab.importe,2));
      
    //detalle
    if(resp.deta.length>0){
      let fila = "";
      resp.deta.forEach((valor,key)=>{
        fila += '<tr>'+
                '<td style="text-align:center;">'+(valor.item)+'</td>'+
                '<td>'+(valor.tipo_mov)+'</td>'+
                '<td>'+(valor.producto)+'</td>'+
                '<td style="text-align:right;">'+(appFormatMoney(valor.importe,2))+'</td>'+
                '</tr>';
      });
      $('#grdDetalleDatos').html(fila);
    }else{
      $('#grdDetalleDatos').html('<tr><td colspan="4" style="text-align:center;color:red;">Sin DETALLE</td></tr>');
    }
    $('#grid').hide();
    $('#edit').show();
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appMovimRefresh(){
  const codigo = $("#hid_movimID").val();
  appMovimView(codigo);
}

function appMovimCancel(){
  appMovimGrid();
  $('#grid').show();
  $('#edit').hide();
}
