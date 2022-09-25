var rutaSQL ="pages/oper/cartera/sql.php";

//=========================funciones para rpt Cartera============================
function appCarteraReset(){
  let datos = {
    TipoQuery : 'ComboBox',
    miSubSelect : 'Agencias'
  }
  appAjaxSelect(datos).done(function(resp){
    appLlenarComboAgencias("#cboAgencias",resp.agenciaID,resp.tabla);
    appGetCartera();
    appGetCarteraControlCierre();
  });
}

function appGetCartera(){
  $('#grdDatosBody').html('<tr><td colspan="11" style="text-align:center;"><br><div class="progress progress-sm active" style=""><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div><br>Un momento, por favor...</td></tr>');
  let agenciaID = $('#cboAgencias').val();
  let datos = {
    TipoQuery : 'rptCartera',
    agenciaID : agenciaID
  };
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    console.log(resp);
    if(resp.tabla.length>0){
      let rptData = "";
      let tot_ini = 0;
      let tot_hoy = 0;
      let tot_saldoini = 0;
      let tot_saldohoy = 0;
      let tot_saldos = 0;
      let saldocrec = 0;
      let crec = 0;
      let miColor = "";
      let miColorHoy = "";
      let miColorRes = "";
      let miColorTot = "";
      let miColorLnk = "";
      const colorDis = "color:#BFBFBF;"; //color deshabilitada
      const colorEna = "color:blue;"; //color habilitado
      const colorAlerta = "color:red;"; //color alerta
      const colorDisAlerta = "color:#FF5500;"; //color alerta deshabilitado

      //console.log(resp.tabla);
      $.each(resp.tabla,function(key, valor){
        tot_ini += Number(valor.ini);
        tot_hoy += Number(valor.hoy);
        tot_saldoini += Number(valor.saldo_ini);
        tot_saldohoy += Number(valor.saldo_hoy);
        saldocrec = Number(valor.saldo_hoy)-Number(valor.saldo_ini);
        crec = Number(valor.hoy)-Number(valor.ini);

        if(valor.estado==0){ //fila deshabilitada
          miColor = colorDis;
          miColorLnk = colorDis;
          miColorHoy = colorDis;
          miColorRes = (saldocrec<0)?(colorDisAlerta):(colorEna);
        }else{
          miColor = "";
          miColorLnk = "";
          miColorHoy = colorEna;
          miColorRes = (saldocrec<0)?(colorAlerta):(colorEna);
        }

        rptData += '<tr>';
        rptData += '<td style="'+miColor+'">'+(valor.agencia)+'</td>';
        rptData += '<td style="text-align:right; '+miColor+'">'+(valor.codigo)+'</td>';
        rptData += '<td style="'+miColor+'"><a style="'+miColorLnk+'" data-toggle="modal" data-target="#modalGrafiCartera" data-source="'+(valor.id_analista+','+valor.id_agencia)+'" href="#"><i class="fa fa-area-chart"></i></a></td>';
        rptData += '<td style="'+miColor+'" title="'+(valor.id_analista)+'"><a style="'+miColorLnk+'" href="javascript:appGetCarteraAnalista('+(valor.id_analista)+','+(valor.id_agencia)+');">'+(valor.worker)+'</a></td>';
        rptData += '<td style="'+miColor+'" title="'+(valor.cargo)+'">'+(valor.abrevia)+'</td>';
        rptData += '<td style="text-align:right;'+colorDis+'">'+(valor.ini)+'</td>';
        rptData += '<td style="text-align:right;'+miColorHoy+'">'+(valor.hoy)+'</td>';
        rptData += '<td style="text-align:right;'+miColor+'">'+(crec)+'</td>';
        rptData += '<td style="text-align:right;'+colorDis+'">'+appFormatMoney(valor.saldo_ini,2)+'</td>';
        rptData += '<td style="text-align:right;'+miColorHoy+'">'+appFormatMoney(valor.saldo_hoy,2)+'</td>';
        rptData += '<td style="text-align:right;'+miColorRes+'">'+appFormatMoney(saldocrec,2)+'</td>';
        rptData += '</tr>';
      });

      //totales
      tot_saldos = tot_saldohoy - tot_saldoini;
      miColorTot = (tot_saldos<0)?(colorAlerta):(colorEna);

      rptData += '<tr>';
      rptData += '<td style="" colspan="5"><strong>Totales</strong></td>';
      rptData += '<td style="text-align:right;'+colorDis+'"><strong>'+(tot_ini)+'</strong></td>';
      rptData += '<td style="text-align:right;'+colorEna+'"><strong>'+(tot_hoy)+'</strong></td>';
      rptData += '<td style="text-align:right;"></td>';
      rptData += '<td style="text-align:right;'+colorDis+'"><b>'+appFormatMoney(tot_saldoini,2)+'</b></td>';
      rptData += '<td style="text-align:right;'+colorEna+'"><b>'+appFormatMoney(tot_saldohoy,2)+'</b></td>';
      rptData += '<td style="text-align:right;'+miColorTot+'"><b>'+appFormatMoney(tot_saldos,2)+'</b></td>';
      rptData += '</tr>';

      $('#grdDatosBody').html(rptData);
    }else{
      $('#grdDatosBody').html("");
    }
    $('#grdDatosCount').html(resp.tabla.length);
  });
}

function appGetCarteraAnalista(analistaID,agenciaID){
  $('#grdCarteraBody').html('<tr><td colspan="9"><br><div class="progress progress-sm active"><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div></td></tr>');
  $('#modCarteraTitulo').html("");
  $('#modalCartera').modal();

  let datos = {
    TipoQuery : 'rptCarteraAnalista',
    analistaID: analistaID,
    agenciaID : agenciaID
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    $('#modCarteraTitulo').html("("+resp.length+(" CREDITOS)"));
    if(resp.length>0){
      let fila = "";
      $.each(resp,function(key, valor){
        fila += '<tr>';
        fila += '<td style="">'+(valor.agencia)+'</td>';
        fila += '<td style="">'+(valor.codsocio)+'</td>';
        fila += '<td>'+(valor.socio)+'</td>';
        fila += '<td>'+(valor.numpres)+'-'+(valor.servicio)+'</td>';
        fila += '<td>'+(valor.fecha)+'</td>';
        fila += '<td style="text-align:center;">'+(valor.atraso)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.importe,2)+'</td>';
        fila += '<td style="text-align:right;">'+appFormatMoney(valor.saldo,2)+'</td>';
        fila += '</tr>';
      });
      $('#grdCarteraBody').html(fila);
    }else{
      $('#grdCarteraBody').html("");
    }
  });
}

$('#modalGrafiCartera').on('shown.bs.modal', function (event) {
  let link = $(event.relatedTarget);
  let source = link.attr('data-source').split(',');

  let datos = {
    TipoQuery : 'graficoCartera',
    analistaID : source[0],
    agenciaID : source[1]
  };

  appAjaxSelect(datos,rutaSQL).done(function(resp){
    //console.log(resp);
    $("#div_canvas").html('<canvas id="canvas" style="height:150px;"></canvas>');
    $('#modal-title').html("<b>"+(resp.analista[0].nombrecorto)+(" - ")+(resp.analista[0].cargo)+"</b>");

    if(resp.grafiCartera.length>0){
      let modal = $("#modalGrafiCartera");
      let canvas = modal.find('.modal-body canvas');
      let ctx = canvas[0].getContext("2d");
      let graf = {labels:[], carteraSaldo:[],carteraNumero:[],colocSaldo:[],colocNumero:[]};
      $.each(resp.grafiCartera,function(key, valor){
        graf.labels.push(valor.meses);
        graf.carteraSaldo.push(valor.carteraSaldo);
        graf.carteraNumero.push(valor.carteraCantidad);
        graf.colocSaldo.push(valor.colocSaldo);
        graf.colocNumero.push(valor.colocCantidad);
      });
      let grafiChartData = {
        labels  : graf.labels,
        datasets: [
          { label               : 'Colocacion',
            backgroundColor     : 'rgba(12, 142, 17,0.5)',
            borderColor         : 'rgba(0, 113, 0,8)',
            pointColor          : 'rgb(0, 113, 0)',
            pointStrokeColor    : 'rgb(0, 113, 0)',
            pointHighlightFill  : 'rgb(0, 113, 0)',
            pointHighlightStroke: 'rgb(0, 113, 0)',
            data                : graf.colocSaldo,
            labels              : graf.colocNumero
          },
          { label               : 'Cartera',
            backgroundColor     : 'rgba(45, 192, 236,0.7)',
            borderColor         : 'rgba(38, 164, 201,1)',
            pointColor          : 'rgb(45, 192, 236)',
            pointStrokeColor    : 'rgb(45, 192, 236)',
            pointHighlightFill  : 'rgb(52, 123, 161)',
            pointHighlightStroke: 'rgb(52, 123, 161)',
            data                : graf.carteraSaldo,
            labels              : graf.carteraNumero
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
            label : function (tooltipItem,data) { return tooltipItem.yLabel.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'); }
          }
        }
      };

      let grafiChart = new Chart(ctx,{
        type : 'line',
        data : grafiChartData,
        options : grafiChartOptions
      });
    } else {
      alert("¡¡¡NO hay datos de Cartera que mostrar!!!");
    }
  });
}).on('hidden.bs.modal',function(event){
  $("#div_canvas").html('');
  $("#modalGrafiCartera").data('bs.modal', null);
});

function rptGetCarteraSociosDownload(){
  let datos = {
    TipoQuery  : 'rptCarteraSociosDownload',
    agenciaID  : $('#cboAgencias').val(),
    analistaID : 0
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    Jhxlsx.export(resp.tableData, resp.options);
  });
}

function appGetCarteraControlCierre(){
  let datos = { TipoQuery : 'controlCierreCartera' }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    if(resp.ultimodia==1 && resp.cuenta==0) {
      $("#btnCierre").show();
    } else{
      $("#btnCierre").hide();
    }
  });
}

function appSetCarteraCierre(){
  let datos = { TipoQuery : 'cierreCartera' };
  appAjaxCierre(datos,rutaSQL).done(function(resp){ $("#btnCierre").hide(); });
}
