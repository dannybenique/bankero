<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- modal de Personas -->
<script src="pages/modals/Personas/mod.persona.js"></script>

<section class="content-header">
  <h1><i class="fa fa-user"></i> Postulantes RR.HH.</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Postulantes</li>
  </ol>
</section>
<section class="content">
  <div class="row" id="grid">
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header no-padding">
          <div class="mailbox-controls">
            <div class="btn-group">
              <?php if($_SESSION['usr_usernivelID']==701) {?>
              <button type="button" class="btn btn-default btn-sm" onclick="javascript:rrhhPostulantesBorrar();"><i class="fa fa-trash-o"></i></button>
              <button type="button" class="btn btn-default btn-sm" onclick="javascript:rrhhPostulantesNuevo();"><i class="fa fa-plus"></i></button>
              <?php }?>
            </div>
            <button type="button" class="btn btn-default btn-sm" onclick="javascript:rrhhPostulantesReset();"><i class="fa fa-refresh"></i></button>
            <div class="btn-group">
              <select id="cboAgencias" class="btn btn-default btn-sm" style="height:30px;" onchange="rrhhPostulantesGetAll(this.value,$('#txtBuscar').val());"></select>
            </div>
            <div class="btn-group">
              <input type="text" id="txtBuscar" name="txtBuscar" class="form-control input-sm pull-right" onkeypress="javascript:rrhhPostulantesBuscar(event);" placeholder="DNI, persona..." style="text-transform:uppercase;" autocomplete="off"/>
              <span class="fa fa-search form-control-feedback"></span>
            </div>
            <span id="grdDatosCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
            <table class="table table-hover" id="grdDatos">
              <thead>
                <tr>
                  <th style="width:30px;"><input type="checkbox" onclick="toggleAll(this,'chk_Borrar');"/></th>
                  <th style="width:80px;">DNI</th>
                  <th>Postulante</th>
                  <th style="width:100px;">Celular</th>
                  <th style="width:180px;">Cargo</th>
                  <th style="width:100px;">Status</th>
                  <th style="width:130px;">Agencia</th>
                  <th style="width:80px;" title="Ultima fecha de calificacion">Calif.</th>
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
  <div class="row" id="edit" style="display:none;">
    <form class="form-horizontal" autocomplete="off">
    <div class="col-md-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title fontFlexoRegular">Datos de Blacklist</h3>
        </div>
        <div class="box-body">
          <strong><i class="fa fa-user margin-r-5"></i> Persona</strong>
          <p class="text-muted">
            <input type="hidden" id="hid_personaID" value="">
            <span id="lbl_persona"></span><br>
            DNI: <span id="lbl_DNI"></span>
          </p>
          <hr>

          <strong><i class="fa fa-map-marker margin-r-5"></i> Ubicacion</strong>
          <p class="text-muted">
            <span id="lbl_ubicacion"></span><br>
            Dir.: <span id="lbl_direccion"></span><br>
            Ref.: <span id="lbl_referencia"></span><br>
            Medidor de Luz: <span id="lbl_medidorluz"></span><br>
            Tipo de Vivienda: <span id="lbl_tipovivienda"></span>
          </p>
          <hr>

          <strong><i class="fa fa-file-text-o margin-r-5"></i> Observaciones</strong>
          <p class="text-muted">
            Agencia: <span id="lbl_agencia"></span><br>
            Fecha: <span id="lbl_fecha"></span><br>
            Modif. por: <span id="lbl_modif"></span><br><br>
            <span id="lbl_observac"></span><br>
          </p>
          <hr>
          <div class="btn-group">
            <button type="button" class="btn btn-default" id="btn_cancel" onclick="javascript:appBlacklistCancel();">cancelar</button>
            <?php if($_SESSION['usr_usernivelID']==701) {?>
            <button type="button" class="btn btn-default" id="btn_save" onclick="javascript:appBlacklistSave();">guardar</button>
            <?php }?>
            <button type="button" class="btn btn-default" id="btn_print" onclick="javascript:appBlacklistPrint(<?php echo ($_SESSION["usr_ID"].","."'https://".$_SERVER['HTTP_HOST']."'");?>);">imprimir</button>
          </div>
          <button type="button" class="btn btn-info" id="btn_personas" onclick="javascript:appBlacklistPersonas();"><i class="fa fa-user"></i></button>
        </div>

      </div>
    </div>
    <div class="col-md-9">
      <div class="box box-primary">
        <div class="box-body box-profile">
          <div class="col-md-6">
            <div class="row">
              <div class="col-xs-6">
                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon" title="Agencia"><i class="fa fa-users"></i></span>
                    <select class="form-control" id="cbo_BlkAgencia" name="cbo_BlkAgencia" class="selectpicker" style="width:90%;" onchange="appSetTexto('#lbl_agencia','#cbo_WorkAgencia',true);"></select>
                  </div>
                </div>
              </div>
              <div class="col-xs-6">
                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon" title="Fecha de Ingreso"><i class="fa fa-calendar"></i></span>
                    <input id="date_fechaing" name="date_fechaing" type="text" class="form-control" style="width:105px;"/>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="input-group">
                <textarea id="txt_Observac" name="txt_Observac" type="text" placeholder="Observaciones..." cols="100" rows="20" style="width:100%;"></textarea>
              </div>
            </div>
          </div>
          <div class="col-md-6" id="contenedorFrame">
            <object id="objPDF" type="text/html" data="" width="100%" height="450px"></object>
          </div>
        </div>
      </div>
    </div>
    </form>
  </div>
</section>

<script src="pages/rrhh/postulantes/postulantes.js"></script>
<script>
  $(document).ready(function(){
    Persona.addModalToParentForm('modalPers');
    rrhhPostulantesReset();
  });
</script>
