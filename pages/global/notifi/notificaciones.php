<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- modal de Personas -->
<script src="pages/modals/Personas/mod.persona.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-gears"></i> Notificaciones</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Notificaciones</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <div class="row" id="grid">
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header no-padding">
          <div class="mailbox-controls">
            <button type="button" class="btn btn-default btn-sm" onclick="javascript:appNotificacionesGetAll();"><i class="fa fa-refresh"></i></button>
            <span id="grdDatosCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
            <table class="table table-hover" id="grdDatos">
              <thead>
                <tr>
                  <th colspan="2" style="text-align:center;">Acciones</th>
                  <th style="width:120px;" title="">Tabla</th>
                  <th style="width:150px;" title="Usuario que solicita...">Usuario</th>
                  <th style="">Persona</th>
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

<script src="pages/global/notifi/notificaciones.js"></script>
<script>
  $(document).ready(function(){
    appNotificacionesGetAll();
  });
</script>
