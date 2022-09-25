<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- ChartJS -->
<script src="libs/chart.js/chart.min.js"></script>

<section class="content-header">
  <h1><i class="fa fa-tasks"></i> Estadistica</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Estadistica</li>
  </ol>
</section>
<section class="content">
  <div class="row" id="grid">
    <div class="col-md-12">
      <div class="box mailbox-controls">
        <div class="btn-group">
          <button type="button" class="btn btn-default btn-sm" onclick="javascript:appBotonCreditos();" title="Estadistica de Creditos"><i class="fa fa-flash"></i> Creditos</button>
          <select id="cboAgencias" class="btn btn-default btn-sm" style="height:30px;text-align:left;"></select>
        </div>
        <span id="grdDatosCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;">&nbsp;</span>
        <div class="btn-group">
          <button type="button" class="btn btn-default btn-sm" onclick="javascript:appBotonAhorros();" title="Estadistica de Ahorros"><i class="fa fa-flash"></i> Ahorros</button>
        </div>
      </div>
      <div class="box box-primary">
        <div class="box-header no-padding">
          <div class="mailbox-controls">
            <span id="titulo" style="display:inline-block;margin-left:5px;font-size:20px;">Datos</span>
            <span id="grdDatosCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div id="div_Grafico" class="box-body">
            <p class="text-center" id="lbl_Grafico" style="font-weight:bold;"></p>
            <div class="chart" id="michart">

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="pages/oper/estadistica/estadistica.js"></script>
<script>
  $(document).ready(function(){ appGridReset(); });
</script>
