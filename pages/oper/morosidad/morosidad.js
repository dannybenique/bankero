var rutaSQL = "pages/oper/morosidad/sql.php";

//=========================funciones para rpt Morosidad============================
function appMoraReset(){
  let datos = {
    TipoQuery : 'ComboBox',
    miSubSelect : 'Agencias'
  }
  appAjaxSelect(datos).done(function(resp){
    appLlenarComboAgencias("#cboAgencias",resp.agenciaID,resp.tabla);
    appGetMorosidad();
    appGetMorosidadControlCierre();
  });
}

function appGetMorosidad(){
  $('#grdDatosBody').html('<tr><td colspan="8" style="text-align:center;"><br><div class="progress progress-sm active" style=""><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div><br>Un momento, por favor...</td></tr>');
  let agenciaID = $('#cboAgencias').val();
  let datos = {
    TipoQuery : 'rptMorosidad',
    agenciaID : agenciaID
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    let alertaHist = 0;

    if(resp.tabla.length>0){
      let fila = "";
      let tot_ini = 0;
      let tot_hoy = 0;
      let tot_saldo_ini = 0;
      let tot_saldo_hoy = 0;
      let miColor = "";
      let miColorHoy = "";
      let miColorRes = "";
      let miColorLnk = "";
      const colorDis = "color:#bfbfbf;"; //color deshabilitada
      const colorEna = "color:blue;"; //color habilitado
      const colorAlerta = "color:red;"; //color alerta

      $.each(resp.tabla,function(key, valor){
        tot_ini += Number(valor.ini);
        tot_hoy += Number(valor.hoy);
        tot_saldo_ini += Number(valor.saldo_ini);
        tot_saldo_hoy += Number(valor.saldo_hoy);
        alertaHist = valor.alertaMora;

        if(valor.estado==0){ //fila deshabilitada
          miColorLnk = colorDis;
          miColor = colorDis; miColorHoy = colorDis;
        }else{
          miColorLnk = "";
          miColor=""; miColorHoy = colorEna;
        }

        fila += '<tr>';
        fila += '<td style="'+miColor+'">'+(valor.codigo)+'</td>';
        fila += '<td style="'+miColor+'"><a style="'+miColorLnk+'" data-toggle="modal" data-target="#modalGrafiMorosidad" data-source="'+(valor.id_analista+','+valor.agenciaID)+'" href="#"><i class="fa fa-area-chart"></i></a></td>';
        fila += '<td style="'+miColor+'"><a style="'+miColorLnk+'" href="javascript:appGetMoraAnalista('+(valor.id_analista)+','+valor.agenciaID+');">'+(valor.worker)+'</a></td>';
        fila += '<td style="'+miColor+'">'+(valor.cargo)+'</td>';
        fila += '<td style="text-align:right;'+colorDis+'">'+(valor.ini)+'</td>';
        fila += '<td style="text-align:right;'+miColorHoy+'">'+(valor.hoy)+'</td>';
        fila += '<td style="text-align:right;'+colorDis+'">'+appFormatMoney(valor.saldo_ini,2)+'</td>';
        fila += '<td style="text-align:right;'+miColorHoy+'">'+appFormatMoney(valor.saldo_hoy,2)+'</td>';
        fila += '</tr>';
      });
      //totales
      fila += '<tr>';
      fila += '<td style="" colspan="4"><strong>Totales</strong></td>';
      fila += '<td style="text-align:right;'+colorDis+'"><strong>'+(tot_ini)+'</strong></td>';
      fila += '<td style="text-align:right;color:#0000ff;"><strong>'+(tot_hoy)+'</strong></td>';
      fila += '<td style="text-align:right;'+colorDis+'"><strong>'+appFormatMoney(tot_saldo_ini,2)+'</strong></td>';
      fila += '<td style="text-align:right;color:#0000ff;"><strong>'+appFormatMoney(tot_saldo_hoy,2)+'</strong></td>';
      fila += '</tr>';

      $('#grdDatosBody').html(fila);
    }else{
      $('#grdDatosBody').html("");
    }
    let miTextoAlertaMora = "";
    if(alertaHist>0){ miTextoAlertaMora = "... <span style='font-size:12px; color:red;'>hay "+alertaHist+" problemas con los gastos judiciales, verificar la tabla coop_db_historial</span>";}
    $('#grdDatosCount').html(resp.tabla.length+miTextoAlertaMora);
  });
}

function appGetMoraAnalista(analistaID,agenciaID){
  $('#lbl_SaldoMora').html("Saldo Total: 0.00");
  $('#grdMoraBody').html('<tr><td colspan="15" style="text-align:center;"><br><div class="progress progress-sm active" style=""><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div><br>Un momento, por favor...</td></tr>');
  $('#modalMorosidad').modal();
  let datos = {
    TipoQuery : 'rptMorosidadAnalista',
    analistaID : analistaID,
    agenciaID : agenciaID
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    if(resp.length>0){
      let saldoTotal = 0;
      let fila = "";
      $.each(resp,function(key, valor){
        saldoTotal += valor.saldo;
        fila += '<tr>';
        fila += '<td style="">'+(valor.codsocio)+'</td>';
        fila += '<td>'+(valor.socio)+'</td>';
        fila += '<td>'+(valor.direccion)+'</td>';
        fila += '<td>'+(valor.telefono)+'</td>';
        fila += '<td>'+(valor.num_pres)+'</td>';
        fila += '<td>'+(valor.dias_mora)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.importe,2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.saldo,2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.capital,2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.interes,2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.moratorio,2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.seg_desgr,2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.gas_admin,2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.gas_judi,2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.total,2)+'</td>';
        fila += '</tr>';
      });
      $('#lbl_SaldoMora').html("Saldo Total: "+appFormatMoney(saldoTotal,2));
      $('#grdMoraBody').html(fila);
    }else{
      $('#grdMoraBody').html("");
    }
  });
}

function appGetMorosidadDatosUsuario(){
  let datos = {
    TipoQuery  : 'rptMorosidadDatosUsuario',
    analistaID : $('#hid_analistaID').val()
  }

  appAjaxSelect(datos,rutaSQL).done(function(resp){
    console.log(resp);
    $("#lbl_usuario").html(resp.usuario.nombrecorto);
    $("#lbl_codigo").html(resp.usuario.codigo);
    $("#lbl_agencia").html(resp.usuario.agencia);

    if(resp.morosos.length>0){
      let fila = "";
      $.each(resp.morosos,function(key, valor){
        fila += '<tr>';
        fila += '<td>'+(valor.codsocio)+'</td>';
        fila += '<td>'+(valor.telefono)+'</td>';
        fila += '<td>'+(valor.socio)+'</td>';
        fila += '<td>'+(valor.servicio)+'</td>';
        fila += '<td>'+(valor.cuota_vence)+'</td>';
        fila += '<td>'+(valor.total_cuotas)+'</td>';
        fila += '<td>'+appFormatMoney(valor.total,2)+'</td>';
        fila += '<td><a href="'+(valor.link)+'">mensaje</a></td>';
        fila += '</tr>';
      });
      $('#grdMoraPrevenBody').html(fila);
    } else {
      $('#grdMoraPrevenBody').html("");
    }

    $("#edit").show();
    $("#grid").hide();
  });
}

function appGetMorosidadSociosWhatsapp(){

}

function appGetMorosidadSociosDownload(){
  let datos = {
    TipoQuery  : 'rptMorosidadSociosDownload',
    agenciaID  : $('#cboAgencias').val(),
    analistaID : 0
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    //console.log(resp);
    Jhxlsx.export(resp.tableData, resp.options);
  });
}

function appGetPreventivoSociosDownload(){
  let datos = {
    TipoQuery  : 'rptPreventivoSociosDownload',
    agenciaID  : $('#cboAgencias').val(),
    analistaID : 0
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    console.log(resp);
    Jhxlsx.export(resp.tableData, resp.options);
  });
}

function appGetMorosidadControlCierre(){
  let datos = { TipoQuery : 'controlCierreMorosidad' }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    if(resp.ultimodia==1 && resp.cuenta==0) {
      $("#btnCierre").show();
    } else{
      $("#btnCierre").hide();
    }
  });
}

function appSetMorosidadCierre(){
  let datos = { TipoQuery : 'cierreMorosidad' };
  appAjaxCierre(datos,rutaSQL).done(function(resp){ $("#btnCierre").hide(); });
}

$('#modalGrafiMorosidad')
  .on('shown.bs.modal', function (event) {
    let link = $(event.relatedTarget);
    let source = link.attr('data-source').split(',');
    let datos = {
      TipoQuery : 'graficoMorosidad',
      analistaID : source[0],
      agenciaID : source[1]
    };

    appAjaxSelect(datos,rutaSQL).done(function(resp){
      $("#div_canvas").html('<canvas id="canvas" style="height:150px;"></canvas>');
      $('#modal-title').html("<b>"+(resp.analista[0].nombrecorto)+(" - ")+(resp.analista[0].cargo)+"</b>");

      if(resp.grafiMorosidad.length>0){
        let modal = $("#modalGrafiMorosidad");
        let canvas = modal.find('.modal-body canvas');
        let ctx = canvas[0].getContext("2d");
        let graf = {labels:[], moraSaldo:[],moraNumero:[]};
        $.each(resp.grafiMorosidad,function(key, valor){
          graf.labels.push(valor.meses);
          graf.moraSaldo.push(valor.moraSaldo);
          graf.moraNumero.push(valor.moraCantidad);
        });
        let grafiChartData = {
          labels  : graf.labels,
          datasets: [
            { label               : 'Morosidad',
              backgroundColor     : 'rgba(255, 0, 0,0.5)',
              borderColor         : 'rgba(169, 0, 0,0.7)',
              pointColor          : 'rgba(169, 0, 0,1)',
              pointStrokeColor    : 'rgba(169, 0, 0,1)',
              pointHighlightFill  : 'rgba(169, 0, 0,1)',
              pointHighlightStroke: 'rgba(169, 0, 0,1)',
              data                : graf.moraSaldo,
              labels              : graf.moraNumero
            }
          ]
        };
        let grafiChartOptions = {
          showScale               : true,   // Boolean - If we should show the scale at all
          scaleShowGridLines      : true,   // Boolean - Whether grid lines are shown across the chart
          scaleGridLineColor      : 'rgba(0,0,0,.05)',  // String - Colour of the grid lines
          scaleGridLineWidth      : 1,      // Number - Width of the grid lines
          scaleShowHorizontalLines: true,   // Boolean - Whether to show horizontal lines (except X axis)
          scaleShowVerticalLines  : true,   // Boolean - Whether to show vertical lines (except Y axis)
          bezierCurve             : true,   // Boolean - Whether the line is curved between points
          bezierCurveTension      : 0.3,    // Number - Tension of the bezier curve between points
          pointDot                : true,   // Boolean - Whether to show a dot for each point
          pointDotRadius          : 4,      // Number - Radius of each point dot in pixels
          pointDotStrokeWidth     : 1,      // Number - Pixel width of point dot stroke
          pointHitDetectionRadius : 20,     // Number - amount extra to add to the radius to cater for hit detection outside the drawn point
          datasetStroke           : true,   // Boolean - Whether to show a stroke for datasets
          datasetStrokeWidth      : 2,      // Number - Pixel width of dataset stroke
          datasetFill             : true,   // Boolean - Whether to fill the dataset with a color
          maintainAspectRatio     : true,   // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
          responsive              : true,   // Boolean - whether to make the chart responsive to window resizing
          tooltips : {
            callbacks : {
              title : function (tooltipItem,data){ return data.labels[tooltipItem[0].index]+": "+data.datasets[tooltipItem[0].datasetIndex].labels[tooltipItem[0].index]; },
              label : function (tooltipItem,data){ return tooltipItem.yLabel.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'); }
            }
          }
        };

        let grafiChart = new Chart(ctx,{
          type : 'line',
          data : grafiChartData,
          options : grafiChartOptions
        });
      } else {
        alert("¡¡¡NO hay datos de Morosidad que mostrar!!!");
      }
    });
  })
  .on('hidden.bs.modal',function(event){
    $("#div_canvas").html('');
    $("#modalGrafiMorosidad").data('bs.modal', null);
  });
