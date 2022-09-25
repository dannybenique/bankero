let rutaSQL = "pages/oper/colocaciones/sql.php";

//=========================funciones para rpt Colocaciones============================
function appColocacionesReset(){
  $('#datepickerIni').val(moment().format("01/MM/YYYY"));
  $('#datepickerFin').val(moment().endOf('month').format('DD/MM/YYYY'));
  $('#datepickerIni').datepicker({ autoclose: true })
  $('#datepickerFin').datepicker({ autoclose: true })

  appGetColocacionesControlCierre();
  appColocGetAgencias();
}

function appColocGetAgencias(){
  let datos = {
    TipoQuery : 'rptColocAgencias',
    miFechaIni : appConvertToFecha($('#datepickerIni').val(),""),
    miFechaFin : appConvertToFecha($('#datepickerFin').val(),"")
  };
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    let rptData = "";
    let txtDownload = "";
    let totCuenta = 0;
    let totImporte = 0;
    let totSaldo = 0;

    $.each(resp,function(key, valor){
      totCuenta += valor.cuenta;
      totImporte += Number(valor.importe);
      totSaldo += Number(valor.saldo);

      if((valor.id_usernivel)==701 || (valor.id_usernivel)==707){ txtDownload = '<a href="javascript:rptColocGetSociosDownload('+(valor.ID)+');"><small class="label label-info"><i class="fa fa-download"></i></small></a>';} else { txtDownload=""; }
      rptData += '<tr>';
      rptData += '<td title"'+(valor.ID)+'"><a href="javascript:appColocGetPromotores(\''+(valor.codigo)+'\');">' + (valor.nombre) + '</a></td>';
      rptData += '<td style="text-align:right">'+txtDownload+'</td>';
      rptData += '<td style="text-align:right">' + (valor.cuenta) + '</td>';
      rptData += '<td style="text-align:right">' + appFormatMoney(valor.importe,2) + '</td>';
      rptData += '</tr>';
    });
    rptData += '<tr>';
    rptData += '<td colspan="2" style="font-weight:bold;">TOTAL</td>';
    rptData += '<td style="text-align:right;font-weight:bold;">' + (totCuenta) + '</td>';
    rptData += '<td style="text-align:right;font-weight:bold;">' + appFormatMoney(totImporte,2) + '</td>';
    rptData += '</tr>';

    $('#grdAgenciasBody').html(rptData);
    $('#grdPromotoresCount').html("");
    $('#grdPromotoresBody').html("");
    $('#grdSociosCount').html("");
    $('#grdSociosBody').html("");
  });
}

function appColocGetPromotores(codagenc){
  let splitIni = $('#datepickerIni').val().split("/");
  let splitFin = $('#datepickerFin').val().split("/");
  let datos = {
    TipoQuery : 'rptColocPromotores',
    miCodagenc : codagenc,
    miFechaIni : splitIni[2] + splitIni[1] + splitIni[0],
    miFechaFin : splitFin[2] + splitFin[1] + splitFin[0]
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    let rptData = "";
    let txtAgencia = "";
    let totCuenta = 0;
    let totImporte = 0;
    let totSaldo = 0;

    $.each(resp,function(key, valor){
      totCuenta += valor.cuenta;
      totImporte += Number(valor.importe);
      totSaldo += Number(valor.saldo);
      txtAgencia = valor.agencia;

      rptData += '<tr>';
      rptData += '<td>' + (valor.codigo) + '</td>';
      rptData += '<td><a href="javascript:appColocGetSocios(\''+(valor.codigo)+'\');" title="'+(valor.id_promotor)+'">' + (valor.worker) + '</a></td>';
      rptData += '<td>' + (valor.agencia) + '</td>';
      rptData += '<td style="text-align:right">' + (valor.cuenta) + '</td>';
      rptData += '<td style="text-align:right">' + appFormatMoney(valor.importe,2) + '</td>';
      rptData += '<td style="text-align:right">' + appFormatMoney(valor.saldo,2) + '</td>';
      rptData += '</tr>';
    });
    rptData += '<tr>';
    rptData += '<td colspan="3" style="font-weight:bold;">TOTAL</td>';
    rptData += '<td style="text-align:right;font-weight:bold;">' + (totCuenta) + '</td>';
    rptData += '<td style="text-align:right;font-weight:bold;">' + appFormatMoney(totImporte,2) + '</td>';
    rptData += '<td style="text-align:right;font-weight:bold;">' + appFormatMoney(totSaldo,2) + '</td>';
    rptData += '</tr>';

    $('#grdPromotoresCount').html(txtAgencia);
    $('#grdPromotoresBody').html(rptData);
    $('#grdSociosCount').html("");
    $('#grdSociosBody').html("");
  });
}

function appColocGetSocios(codpromo){
  let splitIni = $('#datepickerIni').val().split("/");
  let splitFin = $('#datepickerFin').val().split("/");
  let datos = {
    TipoQuery : 'rptColocSocios',
    miPromotor : codpromo,
    miFechaIni : splitIni[2] + splitIni[1] + splitIni[0],
    miFechaFin : splitFin[2] + splitFin[1] + splitFin[0]
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    let rptData = "";
    let txtPromotor = "";
    $.each(resp,function(key, valor){
      txtPromotor = valor.worker;
      rptData += '<tr>';
      rptData += '<td>' + (valor.codigo) + '</td>';
      rptData += '<td>' + (valor.socio) + " - " + (valor.dni) + '</td>';
      rptData += '<td style="text-align:right">' + (valor.fec_otorg) + '</td>';
      rptData += '<td style="text-align:right">' + appFormatMoney(valor.importe,2) + '</td>';
      rptData += '<td style="text-align:right">' + appFormatMoney(valor.saldo,2) + '</td>';
      rptData += '</tr>';
    });
    $('#grdSociosCount').html(txtPromotor);
    $('#grdSociosBody').html(rptData);
  });
}

function rptColocGetSociosDownload(agenciaID){
  let splitIni = $('#datepickerIni').val().split("/");
  let splitFin = $('#datepickerFin').val().split("/");
  let datos = {
    TipoQuery : 'rptColocSociosDownload',
    agenciaID : agenciaID,
    miFechaIni : splitIni[2] + splitIni[1] + splitIni[0],
    miFechaFin : splitFin[2] + splitFin[1] + splitFin[0]
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    //JSONToCSVConvertor(resp, "Colocaciones_"+agenciaID, true);
    Jhxlsx.export(resp.tableData, resp.options);
  });
}

function appGetColocacionesControlCierre(){
  let datos = { TipoQuery : 'controlCierreColocaciones' }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    if(resp.usernivel==resp.admin && resp.ultimodia==1 && resp.cuenta==0) {
      $("#divCierre").html('<button type="button" class="btn btn-default btn-sm" title="Ejecutar cierre de colocaciones" onclick="javascript:appSetColocacionesCierre();"><i class="fa fa-heartbeat"></i></button>');
    } else{
      $("#divCierre").html('');
    }
  });
}

function appSetColocacionesCierre(){
  let datos = { TipoQuery : 'cierreColocaciones' };
  appAjaxCierre(datos,rutaSQL).done(function(resp){ $("#btnCierre").hide(); });
}
