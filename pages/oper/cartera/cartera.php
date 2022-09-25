<!-- export Excel -->
<script src="plugins/excel-export/xlsx.core.min.js"></script>
<script src="plugins/excel-export/FileSaver.js"></script>
<script src="plugins/excel-export/jhxlsx.js"></script>

<!-- ChartJS -->
<script src="libs/chart.js/chart.min.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-pie-chart"></i>Reporte de Cartera</h1>
  <ol class="breadcrumb">
    <li><i class="fa fa-dashboard"></i> Home</li>
    <li>Reportes</li>
    <li class="active">Cartera</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <div class="row" id="grid">
    <div class="col-xs-12">
      <div class="box box-primary">
        <div class="box-header no-padding">
          <div class="mailbox-controls">
            <?php if($_SESSION['usr_usernivelID']==701){ //solo superadmin ?>
              <div class="btn-group">
                <button type="button" id="btnCierre" style="display:none;" class="btn btn-default btn-sm" title="Ejecutar cierre de cartera" onclick="javascript:appSetCarteraCierre();"><i class="fa fa-heartbeat"></i></button>
                <button type="button" class="btn btn-default btn-sm" title="Descargar toda la cartera de esta agencia" onclick="javascript:rptGetCarteraSociosDownload();"><i class="fa fa-download"></i></button>
              </div>
            <?php  } ?>
            <div class="btn-group">
              <button type="button" class="btn btn-default btn-sm" title="Volver a cargar los datos" onclick="javascript:appGetCartera();"><i class="fa fa-refresh"></i></button>
            </div>
            <div class="btn-group">
              <select id="cboAgencias" class="btn btn-default btn-sm" style="height:30px;text-align:left;" onchange="javascript:appGetCartera(this.value);"></select>
            </div>
            <span id="grdDatosCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
              <table class="table table-hover" id="grdDatos">
                <thead>
                  <tr>
                    <th style="width:30px;">Ag</th>
                    <th style="width:60px;text-align:right;">Codigo</th>
                    <th style="width:30px;"><i class="fa fa-area-chart"></i></th>
                    <th>Empleado</th>
                    <th style="width:60px;">Cargo</th>
                    <th style="width:50px;text-align:right;">Ini</th>
                    <th style="width:50px;text-align:right;">Hoy</th>
                    <th style="width:50px;text-align:right;" title="Crecimiento">Crec</th>
                    <th style="width:120px;text-align:right;">Saldo Ini</th>
                    <th style="width:120px;text-align:right;">Saldo Hoy</th>
                    <th style="width:120px;text-align:right;">Crec</th>
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
  <div class="modal fade" id="modalCartera" role="dialog">
    <div class="modal-dialog" style="width:90%;">
      <div class="modal-content">
        <div class="modal-header" style="background:#f9f9f9;padding:8px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><b>CARTERA </b><span id="modCarteraTitulo" style="font-size:14px;"></span></h4>
        </div>
        <div class="modal-body">
          <div class="box-body table-responsive no-padding">
            <table class="table table-hover" id="grdCartera">
              <thead>
                <tr>
                  <th style="width:30px;">Ag</th>
                  <th style="width:80px;">Codigo</th>
                  <th style="">Socio</th>
                  <th style="">Servicio</th>
                  <th style="width:80px;">Fecha</th>
                  <th style="width:50px;text-align:right;" title="Dias de Mora">atraso</th>
                  <th style="width:80px;text-align:right;">Importe</th>
                  <th style="width:80px;text-align:right;">Saldo</th>
                </tr>
              </thead>
              <tbody id="grdCarteraBody">
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
          <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalGrafiCartera" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" style="background:#f9f9f9;padding:8px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="modal-title">GRAFICO DE CARTERA</h4>
        </div>
        <div class="modal-body">
          <div class="box-body table-responsive no-padding">
            <div id="div_canvas"></div>
          </div>
        </div>
        <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
          <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</section>
<script src="pages/oper/cartera/cartera.js"></script>
<script>
  $(document).ready(function(){
    appCarteraReset();
  });
</script>
