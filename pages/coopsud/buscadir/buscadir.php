<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<section class="content-header">
  <h1><i class="fa fa-tasks"></i> CoopSUD Busca Direcciones</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">BuscaDir</li>
  </ol>
</section>
<section class="content">
  <div class="row" id="grid">
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header no-padding">
          <div class="mailbox-controls">
            <div class="btn-group">
              <input type="text" id="txtBuscar" name="txtBuscar" class="form-control input-sm pull-right" placeholder="recibo luz..." onkeypress="javascript:appBuscaDirBuscar(event);" style="text-transform:uppercase;" autocomplete="off">
              <span class="fa fa-search form-control-feedback"></span>
            </div>
            <span id="grdDatosCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
              <table class="table table-hover" id="grdDatos">
                <thead>
                  <tr>
                    <th style="width:80px;">Codigo</th>
                    <th style="width:100px;" title="Recibo de Luz">Rec. Luz</th>
                    <th style="">Socio</th>
                    <th style="">Direccion</th>
                    <th style="width:40px;text-align:center;" title="Prestamos Inactivos">PI</th>
                    <th style="width:40px;text-align:center;" title="Prestamos Activos">PA</th>
                    <th style="width:40px;text-align:center;" title="Total Prestamos">TP</th>
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

<script src="pages/coopsud/buscadir/buscadir.js"></script>
<script>
  $(document).ready(function(){
    appGridReset();
  });
</script>
