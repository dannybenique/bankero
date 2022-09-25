let rutaSQL = "pages/coopsud/ahorros/sql.php";

//=========================funciones para coopSUD Ahorros============================
function appGridAll(){
  $('#grdDatosCount').html("");
  $('#grdDatosBody').html('<tr><td colspan="11" style="text-align:center;"><br><div class="progress progress-sm active"><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div><br>Un momento, por favor...</td></tr>');
    if($('#txtBuscar').val().trim()==""){
      $('#grdDatosBody').html('<tr><td colspan="11" style="text-align:center;color:red;">Sin Resultados</td></tr>');
    } else {
      let txtBuscar = $('#txtBuscar').val().trim();
      if($("#cboTipo").val()=="1"){ //verificamos que es por codigo
        txtBuscar = txtBuscar.split('-');
        txtBuscar = zfill(Math.abs(txtBuscar[0]),2)+"-"+zfill(Math.abs(txtBuscar[1]),4);
      }
      $('#txtBuscar').val(txtBuscar);
      let datos = {
        TipoQuery:'coopSUDcartera',
        tipo:$('#cboTipo').val(),
        buscar:$('#txtBuscar').val()
      };
      appAjaxSelect(datos,rutaSQL).done(function(resp){
        if(resp.length>0){
          let fila = "";
          $.each(resp,function(key, valor){
            fila += '<tr style="'+((valor.saldo<=0)?("color:#bbb;"):(""))+'">';
            fila += '<td><a href="javascript:appFormatos(\''+(valor.codsocio)+'\',\''+(valor.tipo_serv)+'\',\''+(valor.numcert)+'\');"><i class="fa fa-files-o" title="Formatos..."></i></a></td>';
            fila += '<td>'+(valor.codsocio)+'</td>';
            fila += '<td>'+(valor.nrodoc)+'</td>';
            fila += '<td>'+(valor.socio)+'</td>';
            fila += '<td><a style="'+((valor.saldo<=0)?("color:#bbb;"):(""))+'" href="javascript:appAhorrosView(\''+(valor.codsocio)+'\',\''+(valor.tipo_serv)+'\',\''+(valor.numcert)+'\');">'+(valor.tipo_serv)+' - '+(valor.servicio)+'</a></td>';
            fila += '<td style="text-align:center;">'+((valor.fec_ini=="")?(""):(moment(valor.fec_ini).format('DD/MM/YYYY')))+'</td>';
            fila += '<td style="text-align:center;">'+((valor.fec_fin=="")?(""):(moment(valor.fec_fin).format('DD/MM/YYYY')))+'</td>';
            fila += '<td style="text-align:center;">'+(valor.plazo)+'</td>';
            fila += '<td style="text-align:center;">'+(valor.numcert)+'</td>';
            fila += '<td style="text-align:right;">'+appFormatMoney(valor.importe,2)+'</td>';
            fila += '<td style="text-align:right;">'+appFormatMoney(valor.saldo,2)+'</td>';
            fila += '</tr>';
          });
          $('#grdDatosBody').html(fila);
        }else{
          $('#grdDatosBody').html('<tr><td colspan="11" style="text-align:center;color:red;">Sin Resultados</td></tr>');
        }
        $('#grdDatosCount').html(resp.length);
      });
    }
}

function appGridReset(){
  $("#txtBuscar").val('');
  $('#grdDatosBody').html("");
}

function appComboTipo(){
  //console.log($("#cboTipo").val());
  switch($("#cboTipo").val()){
    case "1": $("#txtBuscar").prop("placeholder","COD-SOCIO..."); break;
    case "2": $("#txtBuscar").prop("placeholder","DNI..."); break;
  }
}

function appAhorrosBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { appGridAll(); }
}

function appBotonCancel(){
  $('#formatos').hide();
  $("#edit").hide();
  $("#grid").show();
  appGridAll();
}

function appAhorrosView(codsocio,tipo_serv,num_cert){
  let datos = {
    TipoQuery : "coopSUDahorro",
    codsocio : codsocio,
    tipo_serv : tipo_serv,
    num_cert : num_cert
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    $("#lbl_socio").html(resp.socio.socio);
    $("#lbl_DNI").html(resp.socio.nrodoc);
    $("#lbl_codigo").html(resp.socio.codsocio);

    $("#lbl_tiposerv").html(resp.socio.tipo_serv);
    $("#lbl_servicio").html(resp.socio.servicio);
    $("#lbl_numpres").html(resp.socio.numcert);
    $("#lbl_importe").html(appFormatMoney(resp.socio.importe,2));
    $("#lbl_saldo").html(appFormatMoney(resp.socio.saldo,2));
    $("#lbl_promotor").html(resp.socio.promotor);

    $('#grdAhorrosBody').html('<tr><td colspan="10" style="text-align:center;"><br><div class="progress progress-sm active"><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div><br>Un momento, por favor...</td></tr>');
    $('#grdAhorrosBody').html(appLlenarMovimientos(resp.movim));
    /*if(resp.movim.length>0) { $('#grdAhorrosBody').html(appLlenarMovimientos(resp.movim)); }
    else { $('#grdAhorrosBody').html(""); }
    */
    $("#grid").hide();
    $("#edit").show();
  });
}

function appLlenarMovimientos(tabla){
  let fila = "";
  let totDepositos = 0;
  let totRetiros = 0;
  let totSaldo = 0;

  $("#allCheck").prop('checked',false);
  $.each(tabla,function(key, valor){
    totDepositos += (valor.deposito);
    totRetiros += (valor.retiro);
    fila += '<tr style="color:#000">';
    fila += '<td>'+(valor.agencia)+'</td>';
    fila += '<td>'+(valor.ventanilla)+'</td>';
    fila += '<td style="text-align:center;">'+(valor.num_trans)+'</td>';
    fila += '<td>'+(moment(valor.fecha1).format('DD/MM/YYYY'))+'</td>';
    fila += '<td style="text-align:center;">'+(valor.tipo_mov)+'</td>';
    fila += '<td>'+(valor.detalle)+'</td>';
    fila += '<td style="text-align:right;">'+((valor.deposito>0)?(appFormatMoney(valor.deposito,2)):("-"))+'</td>';
    fila += '<td style="text-align:right;">'+((valor.retiro>0)?(appFormatMoney(valor.retiro,2)):("-"))+'</td>';
    fila += '<td style="text-align:right;">'+((valor.otro>0)?(appFormatMoney(valor.otro,2)):("-"))+'</td>';
    fila += '</tr>';
  });
  totSaldo = totDepositos - totRetiros;
  fila += '<tr>';
  fila += '<td colspan="6" style="text-align:right;font-weight:bold;">Total</td>';
  fila += '<td style="text-align:right;"><b>'+appFormatMoney(totDepositos,2)+'</b></td>';
  fila += '<td style="text-align:right;"><b>'+appFormatMoney(totRetiros,2)+'</b></td>';
  fila += '<td style="text-align:right;"></td>';
  fila += '</tr>';
  fila += '<tr>';
  fila += '<td colspan="6" style="text-align:right;font-weight:bold;">Saldo movim</td>';
  fila += '<td colspan="2" style="text-align:center;background:#eee;"><b>'+appFormatMoney(totSaldo,2)+'</b></td>';
  fila += '<td style="text-align:right;"></td>';
  fila += '</tr>';
  $("#hid_SaldoMovim").val(totSaldo);
  $("#lbl_SaldoMovim").html("Saldo movim: &nbsp;&nbsp;&nbsp;"+appFormatMoney(totSaldo,2));
  return fila;
}

function appBorrarMovimiento(){
  let arr = $('[name="chk_Borrar"]:checked').map(function(){ return this.value; }).get();
  if((arr.length)==0){
    alert("Debe elegir por lo menos UN movimiento para eliminarlo");
  } else {
    if(confirm("desea eliminar este registro?")){
      let datos = {
        TipoQuery : 'coopSUDdelMovim',
        IDs       : arr,
        codsocio  : $("#lbl_codigo").html(),
        tiposerv  :$("#lbl_tiposerv").html()
      };
      appAjaxDelete(datos,rutaSQL).done(function(resp){
        if (resp.error == false) { //sin errores
          appAhorrosView($("#lbl_codigo").html(),$("#lbl_tiposerv").html(),$("#lbl_numpres").html());
        }
      });
    }
  }
}

function appCorregirSaldo(){
  if(confirm("¿Desea corregir el saldo de este servicio?")){
    let datos = {
      TipoQuery : 'coopSUDcorregirSaldo',
      codsocio  : $("#lbl_codigo").html(),
      tiposerv  : $("#lbl_tiposerv").html(),
      saldo     : $("#hid_SaldoMovim").val()
    };
    appAjaxSelect(datos,rutaSQL).done(function(resp){
      console.log(resp);
      if (resp.error == false) { //sin errores
        appAhorrosView($("#lbl_codigo").html(),$("#lbl_tiposerv").html(),$("#lbl_numpres").html());
      }
    });
  }
}

function appCorregirDPFsaldo(){
  if(confirm("¿Desea corregir el saldo en la tabla COOP_DB_ahorros_plazo de este servicio?")){
    let datos = {
      TipoQuery : 'coopSUDcorregirDPF',
      codsocio  : $("#lbl_codigo").html(),
      tiposerv  : $("#lbl_tiposerv").html(),
      numero    : $("#lbl_numpres").html(),
      saldo     : $("#hid_SaldoMovim").val()
    };

    appAjaxSelect(datos,rutaSQL).done(function(resp){
      if (resp.error == false) { //sin errores
        appAhorrosView($("#lbl_codigo").html(),$("#lbl_tiposerv").html(),$("#lbl_numpres").html());
      }
    });
  }
}

function appRefresh(){
  //console.log("saludos");
  appAhorrosView($("#lbl_codigo").html(),$("#lbl_tiposerv").html(),$("#lbl_numpres").html());
}

function appFormatos(codsocio,tiposerv,num_cert){
  let datos = {
    TipoQuery : "coopSUDformatos",
    codsocio  : codsocio,
    tiposerv  : tiposerv,
    num_cert  : num_cert
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    console.log(resp);
    $("#lbl_FormaSocio").html(resp.socio);
    $("#lbl_FormaDNI").html(resp.nrodoc);
    $("#lbl_FormaCodSocio").html(resp.codsocio);
    $("#lbl_FormaNumCertificado").html(resp.numcert);
    $("#lbl_FormaTiposerv").html(resp.tipo_serv);
    $("#lbl_FormaServicio").html(resp.servicio);
    $("#lbl_FormaTiporet").html((resp.tipo_ret=="V")?("Vencimiento"):(resp.tipo_ret=="M"?"Mensual":"Otro"));
    $("#lbl_FormaFechaIni").html((resp.fecha!=null)?(moment(resp.fecha.date).format('DD/MM/YYYY')):(""));
    $("#lbl_FormaImporte").html(appFormatMoney((resp.importe!=null?resp.importe:0),2));
    $("#lbl_FormaSaldo").html(appFormatMoney(resp.saldo,2));
    $("#lbl_FormaPromotor").html(resp.promotor);

    if(resp.suplentes>0) {
      $("#div_FormaBotones").html('<button type="button" class="btn btn-primary btn-sm pull-right" onclick="javascript:appFormatosGenerarPDF(\'printCertIndividual\');" title="Imprime Certificado de Ahorro Individual"><i class="fa fa-file-pdf-o"></i> Certificado Individual</button> <button type="button" class="btn btn-primary btn-sm pull-right" onclick="javascript:appFormatosGenerarPDF(\'printCertColectivo\');" title="Imprime Certificado de Ahorro Colectivo"><i class="fa fa-file-pdf-o"></i> Certificado Colectivo</button>');
    } else {
      $("#div_FormaBotones").html('<button type="button" class="btn btn-primary btn-sm pull-right" onclick="javascript:appFormatosGenerarPDF(\'printCertIndividual\');" title="Imprime Certificado de Ahorro Individual"><i class="fa fa-file-pdf-o"></i> Certificado Individual</button>');
    }

    $("#contenedorFrame").hide();
    $("#objPDF").prop("data","");

    $('#grid').hide();
    $('#formatos').show();
  });
}

function appFormatosGenerarPDF(tipo){
  let urlServer = $("#hid_FormUrlServer").val();
  let urlReporte = "";

  $("#contenedorFrame").show();
  switch(tipo){
    case "printCertIndividual": urlReporte = appUrlServer()+"pages/coopsud/ahorros/rpt.certificado.ahorro.php?codsocio="+$("#lbl_FormaCodSocio").html()+"&tiposerv="+$("#lbl_FormaTiposerv").html()+"&numcert="+$("#lbl_FormaNumCertificado").html()+"&suplente=0"; break;
    case "printCertColectivo": urlReporte = appUrlServer()+"pages/coopsud/ahorros/rpt.certificado.ahorro.php?codsocio="+$("#lbl_FormaCodSocio").html()+"&tiposerv="+$("#lbl_FormaTiposerv").html()+"&numcert="+$("#lbl_FormaNumCertificado").html()+"&suplente=1"; break;
  }
  $("#contenedorFrame").html('<object id="objPDF" type="text/html" data="'+urlReporte+'" width="100%" height="450px"></object>');
}
