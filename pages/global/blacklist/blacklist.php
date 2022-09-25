<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- modal de Personas -->
<script src="pages/modals/Personas/mod.persona.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1 style="color:darkred;"><i class="fa fa-user-secret"></i> Lista Negra</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Lista Negra</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <div class="row" id="grid">
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header no-padding">
          <div class="mailbox-controls">
            <div class="btn-group">
              <?php if($_SESSION['usr_usernivelID']==701 || $_SESSION['usr_usernivelID']==703) {//super,rrhh?>
              <button type="button" class="btn btn-default btn-sm" onclick="javascript:appBlacklistBotonBorrar();"><i class="fa fa-trash-o"></i></button>
              <button type="button" class="btn btn-default btn-sm" onclick="javascript:appBlacklistBotonNuevo();"><i class="fa fa-plus"></i></button>
              <?php }?>
            </div>
            <div class="btn-group">
              <button type="button" class="btn btn-default btn-sm" onclick="javascript:appBlacklistBotonReset();"><i class="fa fa-refresh"></i></button>
              <button type="button" class="btn btn-default btn-sm" onclick="javascript:appBlacklistBotonViewAll();" title="ver todos los registros"><i id="icoViewAll" class="fa fa-toggle-off"></i><input type="hidden" id="hidViewAll" value="0"></button>
            </div>
            <div class="btn-group">
              <select id="cboAgencias" class="btn btn-default btn-sm" style="height:30px;" onchange="appBlacklistGetAll();"></select>
            </div>
            <div class="btn-group">
              <input type="text" id="txtBuscar" name="txtBuscar" class="form-control input-sm pull-right" placeholder="DNI, persona..." onkeypress="javascript:appBlacklistBotonBuscar(event);" style="text-transform:uppercase;" autocomplete="off"/>
              <span class="fa fa-search form-control-feedback"></span>
            </div>
            <span id="grdDatosCount" style="color:darkred;display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
            <table class="table table-hover" id="grdDatos">
              <thead>
                <tr>
                  <th style="width:30px;"><input type="checkbox" onclick="toggleAll(this,'chk_Borrar');"/></th>
                  <th style="width:80px;" title="Fecha de Ingreso a la Lista Negra">Fecha</th>
                  <th style="width:300px;">Persona</th>
                  <th style="width:80px;">DNI</th>
                  <th style="width:150px;">Agencia</th>
                  <th style="">Observacion</th>
                  <th style="">Detalle</th>
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
    <div class="col-md-4">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title fontFlexoRegular">Datos de Blacklist</h3>
        </div>
        <div class="box-body">
          <strong><i class="fa fa-user margin-r-5"></i> Persona</strong>
          <p class="text-muted">
            <input type="hidden" id="hid_personaID" value="">
            <span id="lbl_persona"></span><br>
            ID: <span id="lbl_ID"></span><br>
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
            <span style="color:red;" id="lbl_observac"></span><br>
          </p>
          <hr>
          <div class="btn-group">
            <button type="button" class="btn btn-default" id="btn_cancel" onclick="javascript:appBlacklistBotonCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
          </div>
          <div class="btn-group pull-right">
            <button type="button" class="btn btn-default" id="btn_print" onclick="javascript:appBlacklistBotonPrint(<?php echo $_SESSION["usr_ID"];?>);"><i class="fa fa-file-pdf-o"></i> imprimir</button>
            <?php if($_SESSION['usr_usernivelID']==701 || $_SESSION['usr_usernivelID']==703) {?>
            <button type="button" class="btn btn-default" id="btn_edit" onclick="javascript:appBlacklistBotonEdit();"><i class="fa fa-pencil"></i> Editar</button>
            <?php }?>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <div class="box box-primary">
        <div class="box-body">
          <div class="table-responsive no-padding" id="contenedorFrame">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalEdicion" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form class="form-horizontal" id="frmBlacklist" autocomplete="off">
          <div class="modal-header" style="background:#f9f9f9;padding:8px;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title fontFlexoRegular">Informacion de BlackList</h4>
          </div>
          <div class="modal-body">
            <div class="box-body">
              <div class="row">
                <div class="col-md-7">
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon">Agencia</span>
                      <select class="form-control" id="cbo_BlkAgencia" class="selectpicker" style="width:90%;"></select>
                    </div>
                  </div>
                </div>
                <div class="col-md-5">
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon" title="Fecha de Ingreso"><i class="fa fa-calendar"></i></span>
                      <input id="date_fechaing" name="date_fechaing" type="text" class="form-control" style="width:105px;"/>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-7">
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon">Tipo de ingreso</span>
                      <select class="form-control" id="cbo_BlkTipo" class="selectpicker" style="width:90%;"></select>
                    </div>
                  </div>
                </div>
                <div class="col-md-5">
                </div>
              </div>
              <div class="form-group">
                <div class="input-group">
                  <textarea id="txt_Observac" name="txt_Observac" type="text" placeholder="Observaciones..." cols="120" rows="20" style="width:100%"></textarea>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
            <button type="button" id="btn_modEdicionInsert" class="btn btn-primary btn-sm" onclick="javascript:modalEdicionBotonInsert();"><i class="fa fa-save"></i> Guardar</button>
            <button type="button" id="btn_modEdicionUpdate" class="btn btn-info btn-sm" onclick="javascript:modalEdicionBotonUpdate();"><i class="fa fa-save"></i> Actualizar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalPers" role="dialog"></div>
</section>

<script src="pages/global/blacklist/blacklist.js"></script>
<script>
  $(document).ready(function(){
    Persona.addModalToParentForm('modalPers');
    appBlacklistBotonReset();
  });
</script>
