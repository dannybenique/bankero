<script src="plugins/excel-export/xlsx.core.min.js"></script>
<script src="plugins/excel-export/FileSaver.js"></script>
<script src="plugins/excel-export/jhxlsx.js"></script>

<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-pie-chart"></i>Reporte de Colocaciones</h1>
  <ol class="breadcrumb">
    <li><i class="fa fa-dashboard"></i> Home</li>
    <li>Reportes</li>
    <li class="active">Colocaciones</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-4">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title fontFlexoRegular">Config. del Reporte</h3>
        </div>
        <div class="box-body">
          <ul class="todo-list">
            <li>
              <table>
                <tr>
                  <td style="width:60px;text-align:right;padding-right:5px;">
                    <span class="text">Desde</span></td>
                  <td>
                    <div class="input-group date">
                      <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                      <input type="text" class="form-control pull-left" style="width:105px;" id="datepickerIni">
                    </div></td>
                </tr>
              </table>
            </li>
            <li>
              <table>
                <tr>
                  <td style="width:60px;text-align:right;padding-right:5px;">
                    <span class="text">Hasta</span></td>
                  <td>
                    <div class="input-group date">
                      <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                      <input type="text" class="form-control pull-left" style="width:105px;" id="datepickerFin">
                    </div></td>
                </tr>
              </table>
            </li>
          </ul>
        </div>
        <div class="box-footer clearfix no-border">
          <div id="divCierre" class="btn-group"></div>
          <a href="javascript:appColocGetAgencias(<?php echo $_SESSION['usr_ID'];?>);" class="btn btn-primary pull-right btn-sm"><i class="fa fa-flash"></i> Consultar</a>
        </div>
      </div>

      <div class="box box-solid">
        <div class="box-header with-border">
          <h3 class="box-title fontFlexoRegular">AGENCIAS</h3>
        </div>
        <div class="box-body no-padding">
          <table class="table table-hover" id="grdAgencias">
            <thead>
              <tr>
                <th style="">Agencia</th>
                <th style="width:25px;text-align:right;"></th>
                <th style="width:50px;text-align:right;">Nro</th>
                <th style="width:100px;text-align:right;">Importe</th>
              </tr>
            </thead>
            <tbody id="grdAgenciasBody" style="border-top:none;">
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <div class="box box-primary">
        <div class="box-header no-padding">
          <div class="mailbox-controls">
            <span style="font-size:20px;">Promotores para</span>
            <span id="grdPromotoresCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
              <table class="table table-hover" id="grdPromotores">
                <thead>
                  <tr>
                    <th style="width:60px;">Codigo</th>
                    <th>Empleado</th>
                    <th style="width:130px;">Agencia</th>
                    <th style="width:50px;text-align:right;">Nro</th>
                    <th style="width:100px;text-align:right;">Importe</th>
                    <th style="width:100px;text-align:right;">Saldo</th>
                  </tr>
                <thead>
                <tbody id="grdPromotoresBody">
                </tbody>
              </table>
            </div>
        </div>
      </div>
      <div class="box box-solid">
        <div class="box-header no-padding">
          <div class="mailbox-controls">
            <span style="font-size:20px;">Socios para el Promotor</span>
            <span id="grdSociosCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
              <table class="table table-hover" id="grdSocios">
                <thead>
                  <tr>
                    <th style="width:80px;">Codigo</th>
                    <th>Socio</th>
                    <th style="width:80px;text-align:right;">Fecha</th>
                    <th style="width:100px;text-align:right;">Importe</th>
                    <th style="width:100px;text-align:right;">Saldo</th>
                  </tr>
                </thead>
                <tbody id="grdSociosBody">
                </tbody>
              </table>
            </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="pages/oper/colocaciones/colocaciones.js"></script>
<script>
  appColocacionesReset();
</script>
