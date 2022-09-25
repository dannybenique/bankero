<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<section class="content-header">
  <h1><i class="fa fa-tasks"></i> CoopSUD Servicios</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Servicios</li>
  </ol>
</section>
<section class="content">
  <div class="row" id="grid">
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header no-padding">
          <div class="mailbox-controls">
            <div class="btn-group">
              <button type="button" class="btn btn-default btn-sm" title="Volver a cargar los datos" onclick="javascript:appGridReset();"><i class="fa fa-refresh"></i></button>
            </div>
            <div class="btn-group">
              <select id="cboTipo" class="btn btn-default btn-sm" style="height:30px;" onchange="javascript:changeComboTipo();">
                <option value="02">Ahorros</option>
                <option value="04">Creditos</option>
              </select>
            </div>
            <div id="divBuscar" class="btn-group">
              <input type="text" id="txtBuscar" name="txtBuscar" class="form-control input-sm pull-right" placeholder="servicio..." onkeypress="javascript:appAhorrosBuscar(event);" style="text-transform:uppercase;" autocomplete="off">
              <span class="fa fa-search form-control-feedback"></span>
            </div>
            <span id="grdDatosCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
              <table class="table table-hover" id="grdDatos">
                <thead>
                  <tr>
                    <th style="width:80px;">Tipo</th>
                    <th style="width:80px;text-align:center;">Codigo</th>
                    <th style="width:280px;">Servicio</th>
                    <th style="width:100px;">interes_1</th>
                    <th style="width:100px;">interes_2</th>
                    <th style="width:100px;">interes_3</th>
                    <th style="width:30px;">apl_1</th>
                    <th style="width:30px;">apl_2</th>
                    <th style="width:30px;">apl_3</th>
                    <th></th>
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

<script src="pages/coopsud/tiposerv/tiposerv.js"></script>
<script>
  $(document).ready(function(){
    appGridReset();
  });
</script>
