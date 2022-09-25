<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- export EXCEL -->
<script src="plugins/excel-export/xlsx.core.min.js"></script>
<script src="plugins/excel-export/FileSaver.js"></script>
<script src="plugins/excel-export/jhxlsx.js"></script>

<section class="content-header">
  <h1><i class="fa fa-tasks"></i> CoopSUD Movimientos</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Movimientos</li>
  </ol>
</section>
<section class="content">
  <div class="row" id="grid">
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header no-padding">
          <div class="mailbox-controls">
            <div class="btn-group">
              <select id="cboAgencias" class="btn btn-default btn-sm" style="height:30px;text-align:left;" onchange="javascript:appGridAll();"></select>
              <select id="cboVentanillas" class="btn btn-default btn-sm" style="height:30px;text-align:left;"></select>
              <select id="cboMonedas" class="btn btn-default btn-sm" style="height:30px;text-align:left;">
                <option value='S'>Soles</option>
                <option value='D'>Dolares</option>
                <option value='E'>Euros</option>
              </select>
            </div>
            <span style="display:inline-block;margin-left:5px;">Fecha</span>
            <div class="btn-group">
              <input type="text" id="txt_fechaIni" class="form-control input-sm pull-left" style="width:105px;">
            </div>
            <div class="btn-group">
              <button type="button" class="btn btn-success btn-sm" onclick="javascript:appGridAll();" title="Volver a cargar toda la lista"><i class="fa fa-flash"></i> Ejecutar</button>
              <button type="button" class="btn btn-success btn-sm" title="Descargar cancelados de esta agencia" onclick="javascript:rptGetMovimDownload();"><i class="fa fa-download"></i></button>
            </div>
            <span id="grdDatosCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
              <table class="table table-hover" id="grdDatos">
                <thead>
                  <tr>
                    <th style="width:100px;" title="Voucher">Voucher</th>
                    <th style="width:80px;">Codigo</th>
                    <th style="">Socio</th>
                    <th style="">Servicio</th>
                    <th style="">Movimiento</th>
                    <th style="width:100px;text-align:right;">Ingresos</th>
                    <th style="width:100px;text-align:right;">Salidas</th>
                  </tr>
                </thead>
                <tbody id="grdDatosBody">
                </tbody>
              </table>
            </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="pages/caja/movim/movim.js"></script>
<script>
  $(document).ready(function(){
    appGridReset();
  });
</script>
