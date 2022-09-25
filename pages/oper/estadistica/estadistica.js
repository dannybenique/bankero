let rutaSQL = "pages/oper/estadistica/sql.php";

//=========================funciones para coopSUD Prestamos============================
function appGridReset(){
  //$('#div_Grafico').html("");
  let datos = {
    TipoQuery : 'ComboBox',
    miSubSelect : 'Agencias'
  }
  appAjaxSelect(datos).done(function(resp){
    console.log(resp);
    appLlenarComboAgencias("#cboAgencias",resp.agenciaID,resp.tabla);
  });
}

function appBotonCreditos(){
  let datos = {
    TipoQuery : 'dashboard_Creditos',
    agenciaID : $('#cboAgencias').val()
  }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    $('#titulo').html('Creditos');
    $('#lbl_Grafico').html(resp.titulo);
    $('#michart').html('<canvas id="creditosChart" style="height:400px;"></canvas>');
    appGraphCreditos(resp.grafico);
  });
}

function appBotonAhorros(){
  let datos = { TipoQuery : 'dashboard_Ahorros' }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    $('#titulo').html('Ahorros');
    $('#lbl_Grafico').html(resp.titulo);
    $('#michart').html('<canvas id="ahorrosChart" style="height:400px;"></canvas>');
    appGraphAhorros(resp.grafico);
  });
}

function appGraphCreditos(data){
  //console.log(data);
  let graf = {
    labels:[],
    carteraSaldo:[],
    carteraCanti:[],
    colocSaldo:[],
    colocCanti:[],
    moraSaldo:[],
    moraCanti:[]
  };
  $.each(data,function(key, valor){
    graf.labels.push(valor.meses);
    graf.carteraSaldo.push(valor.carteraSaldo);
    graf.carteraCanti.push(valor.carteraCantidad);
    graf.colocSaldo.push(valor.colocSaldo);
    graf.colocCanti.push(valor.colocCantidad);
    graf.moraSaldo.push(valor.moraSaldo);
    graf.moraCanti.push(valor.moraCantidad);
  });
  let areaChartCanvas = $('#creditosChart').get(0).getContext('2d');
  let areaChartData = {
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
        labels              : graf.colocCanti
      },
      { label               : 'Mora',
        backgroundColor     : 'rgba(221, 0, 0,0.7)',
        borderColor         : 'rgba(150, 0, 0,1)',
        pointColor          : 'rgb(150, 0, 0)',
        pointStrokeColor    : 'rgb(150, 0, 0)',
        pointHighlightFill  : 'rgb(150, 0, 0)',
        pointHighlightStroke: 'rgb(150, 0, 0)',
        data                : graf.moraSaldo,
        labels              : graf.moraCanti
      },
      { label               : 'Cartera',
        backgroundColor     : 'rgba(45, 192, 236,0.5)',
        borderColor         : 'rgba(38, 164, 201,1)',
        pointColor          : 'rgb(45, 192, 236)',
        pointStrokeColor    : 'rgb(45, 192, 236)',
        pointHighlightFill  : 'rgb(52, 123, 161)',
        pointHighlightStroke: 'rgb(52, 123, 161)',
        data                : graf.carteraSaldo,
        labels              : graf.carteraCanti
      }
    ]
  };
  let areaChartOptions = {
    showScale               : true,   // Boolean - If we should show the scale at all
    scaleShowGridLines      : true,  // Boolean - Whether grid lines are shown across the chart
    scaleGridLineColor      : 'rgba(0,0,0,.05)',  // String - Colour of the grid lines
    scaleGridLineWidth      : 1,      // Number - Width of the grid lines
    scaleShowHorizontalLines: true,   // Boolean - Whether to show horizontal lines (except X axis)
    scaleShowVerticalLines  : true,   // Boolean - Whether to show vertical lines (except Y axis)
    bezierCurve             : true,   // Boolean - Whether the line is curved between points
    bezierCurveTension      : 0.3,    // Number - Tension of the bezier curve between points
    pointDot                : true,  // Boolean - Whether to show a dot for each point
    pointDotRadius          : 4,      // Number - Radius of each point dot in pixels
    pointDotStrokeWidth     : 1,      // Number - Pixel width of point dot stroke
    pointHitDetectionRadius : 20,     // Number - amount extra to add to the radius to cater for hit detection outside the drawn point
    datasetStroke           : true,   // Boolean - Whether to show a stroke for datasets
    datasetStrokeWidth      : 2,      // Number - Pixel width of dataset stroke
    datasetFill             : true,   // Boolean - Whether to fill the dataset with a color
    maintainAspectRatio     : true,   // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    responsive              : true,    // Boolean - whether to make the chart responsive to window resizing
    tooltips : {
      callbacks : {
        title : function (tooltipItem,data){ return data.labels[tooltipItem[0].index]+": "+data.datasets[tooltipItem[0].datasetIndex].labels[tooltipItem[0].index]; },
        label : function (tooltipItem,data){ return tooltipItem.yLabel.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'); }
      }
    }
  };

  let areaChart = new Chart(areaChartCanvas,{
    type : 'line',
    data : areaChartData,
    options : areaChartOptions
  });
}

function appGraphAhorros(data){
  //console.log(data);
  let graf = {
    labels:[],
    amovilSaldoPE:[],
    amovilCantiPE:[],
    amovilSaldoUS:[],
    amovilCantiUS:[],
    dpfSaldoPE:[],
    dpfCantiPE:[],
    dpfSaldoUS:[],
    dpfCantiUS:[]
  };
  $.each(data,function(key, valor){
    graf.labels.push(valor.meses);
    graf.amovilSaldoPE.push(valor.amovilSaldoPE);
    graf.amovilCantiPE.push(valor.amovilCantiPE);
    graf.amovilSaldoUS.push(valor.amovilSaldoUS);
    graf.amovilCantiUS.push(valor.amovilCantiUS);
    graf.dpfSaldoPE.push(valor.dpfSaldoPE);
    graf.dpfCantiPE.push(valor.dpfCantiPE);
    graf.dpfSaldoUS.push(valor.dpfSaldoUS);
    graf.dpfCantiUS.push(valor.dpfCantiUS);
  });
  let areaChartCanvas = $('#ahorrosChart').get(0).getContext('2d');
  let areaChartData = {
    labels  : graf.labels,
    datasets: [
      { label               : 'Movil PEN',
        backgroundColor     : 'rgba(12, 166, 90,0.6)',
        borderColor         : 'rgba(0, 113, 0,1)',
        pointColor          : 'rgb(0, 113, 0)',
        pointStrokeColor    : 'rgb(0, 113, 0)',
        pointHighlightFill  : 'rgb(0, 113, 0)',
        pointHighlightStroke: 'rgb(0, 113, 0)',
        data                : graf.amovilSaldoPE,
        labels              : graf.amovilCantiPE,
      },
      { label               : 'Movil US$',
        backgroundColor     : 'rgba(112, 48, 160,0.5)',
        borderColor         : 'rgba(74, 32, 106,1)',
        pointColor          : 'rgb(112, 48, 160)',
        pointStrokeColor    : 'rgb(112, 48, 160)',
        pointHighlightFill  : 'rgb(112, 48, 160)',
        pointHighlightStroke: 'rgb(112, 48, 160)',
        data                : graf.amovilSaldoUS,
        labels              : graf.amovilCantiUS,
      },
      { label               : 'DPF PEN',
        backgroundColor     : 'rgba(45, 192, 236,0.5)',
        borderColor         : 'rgba(38, 164, 201,1)',
        pointColor          : 'rgb(45, 192, 236)',
        pointStrokeColor    : 'rgb(45, 192, 236)',
        pointHighlightFill  : 'rgb(52, 123, 161)',
        pointHighlightStroke: 'rgb(52, 123, 161)',
        data                : graf.dpfSaldoPE,
        labels              : graf.dpfCantiPE,
      },
      { label               : 'DPF US$',
        backgroundColor     : 'rgba(100, 100, 100,0.5)',
        borderColor         : 'rgba(0, 0, 0,1)',
        pointColor          : 'rgb(100, 100, 100)',
        pointStrokeColor    : 'rgb(100, 100, 100)',
        pointHighlightFill  : 'rgb(100, 100, 100)',
        pointHighlightStroke: 'rgb(100, 100, 100)',
        data                : graf.dpfSaldoUS,
        labels              : graf.dpfCantiUS,
      }
    ]
  };
  let areaChartOptions = {
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

  let areaChart = new Chart(areaChartCanvas,{
    type : 'line',
    data : areaChartData,
    options : areaChartOptions
  });
}
