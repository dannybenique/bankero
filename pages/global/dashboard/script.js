const rutaSQL = "pages/global/dashboard/sql.php";

//=========================funciones para Dashboard============================
async function appDashBoard(){
  try{
    const resp = await appAsynFetch({ TipoQuery:'dashboard' },rutaSQL);
    // console.log(resp);
    $("#appTotalAgencias").html(resp.agencias);
    $("#appTotalSocios").html(resp.socios);
    $("#appTotalCreditos").html(resp.creditos);
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}

function appGraph(data){
  let graf = {labels:[], cartera:[], morosidad:[]};
  $.each(data,function(key, valor){
    graf.labels.push(valor.meses);
    graf.cartera.push(valor.cartera);
    graf.morosidad.push(valor.morosidad);
  });
  let salesChartCanvas = $('#salesChart').get(0).getContext('2d');
  let salesChart = new Chart(salesChartCanvas);
  let salesChartData = {
    labels  : graf.labels,
    datasets: [
      {
        label               : 'Cartera',
        fillColor           : 'rgba(45, 192, 236,0.7)',
        strokeColor         : 'rgba(38, 164, 201,1)',
        pointColor          : 'rgb(45, 192, 236)',
        pointStrokeColor    : 'rgb(45, 192, 236)',
        pointHighlightFill  : 'rgb(52, 123, 161)',
        pointHighlightStroke: 'rgb(52, 123, 161)',
        data                : graf.cartera
      },
      {
        label               : 'Morosidad',
        fillColor           : 'rgba(100, 100, 100,0.5)',
        strokeColor         : 'rgba(100, 100, 100,0.7)',
        pointColor          : 'rgba(100, 100, 100,1)',
        pointStrokeColor    : 'rgba(100, 100, 100,1)',
        pointHighlightFill  : 'rgba(100, 100, 100,1)',
        pointHighlightStroke: 'rgba(100, 100, 100,1)',
        data                : graf.morosidad
      }
    ]
  };
  let salesChartOptions = {
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
    // String - A legend template
    //legendTemplate          : '<ul class=\'<%=name.toLowerCase()%>-legend\'><% for (var i=0; i<datasets.length; i++){%><li> <span style=\'background-color:<%=datasets[i].lineColor%>\'></span><%=datasets[i].label%></li><%}%></ul>',
    maintainAspectRatio     : true,   // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    responsive              : true    // Boolean - whether to make the chart responsive to window resizing
  };

  salesChart.Line(salesChartData, salesChartOptions);
}
