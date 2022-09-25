<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css"/>
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- modal de Personas -->
<script src="pages/modals/Personas/mod.persona.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-gg"></i> Caja</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Caja</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <div class="row" id="grid">
    <div class="col-xs-12">
      <div class="box box-primary">
        <div class="box-header no-padding">
          <div class="mailbox-controls">
            <button type="button" class="btn btn-default btn-sm" onclick="javascript:appCajaNuevo();"><i class="fa fa-plus"></i></button>
            <button type="button" class="btn btn-default btn-sm" onclick="javascript:appCajaReset();"><i class="fa fa-refresh"></i></button>
            <div class="btn-group">
              <input type="text" id="txtBuscar" name="txtBuscar" class="form-control input-sm pull-right" placeholder="nombre..." onkeypress="javascript:appCajaBuscar(event);">
              <span class="fa fa-search form-control-feedback"></span>
            </div>
            <span id="grdDatosCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
              <table class="table table-hover" id="grdDatos">
                <thead>
                  <tr>
                    <th style="width:100px;">Nº Voucher</th>
                    <th style="width:100px;">Fecha</th>
                    <th >Detalle</th>
                    <th style="width:130px;">Servicio</th>
                    <th style="width:250px;">Movimiento</th>
                    <th style="width:90px;text-align:right;">Cargos</th>
                    <th style="width:90px;text-align:right;">Abonos</th>
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
    <div class="col-md-6">
      <form class="form-horizontal" autocomplete="off">
        <div class="box box-primary">
          <div class="box-body">
            <div class="col-md-12">
              <div class="box-body">
                <div class="row">
                  <div class="col-xs-4">
                    <div class="form-group">
                      <div class="input-group">
                        <span class="input-group-addon" title="Numero de Transaccion"><b>Nro Transac</b></span>
                        <input id="txt_NroTransacc" name="txt_NroTransacc" type="text" class="form-control" disabled="disabled" style="width:120px;"/>
                        <input id="hid_ID" type="hidden" value=""/>
                      </div>
                    </div>
                  </div>
                  <div class="col-xs-4">
                  </div>
                  <div class="col-xs-4">
                    <div class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon"><b>Fecha</b></span>
                        <input id="txt_FechaIng" name="txt_FechaIng" type="text" class="form-control" style="width:120px;"/>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon"><b>Servicio</b></span>
                    <select id="cbo_tipoProd" name="cbo_tipoProd" class="form-control selectpicker"></select>
                  </div>
                </div>
                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon"><b>Movimientos</b></span>
                    <select id="cbo_tipoMovs" name="cbo_tipoMovs" class="form-control selectpicker"></select>
                  </div>
                </div>
                <div id="div_Importe" class="form-group" style="margin-bottom:25px;">
                  <div class="input-group">
                    <span class="input-group-addon"><b>Importe</b></span>
                    <input id="txt_Importe" name="txt_Importe" type="text" class="form-control" placeholder="0.00" style="width:120px;"/>
                  </div>
                </div>
                <div id="div_Observac" class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <textarea id="txt_Observac" name="txt_Observac" type="text" placeholder="Observaciones..." cols="100" rows="5" style="width:100%;"></textarea>
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <div class="input-group-btn">
                      <button type="button" class="btn btn-info" onclick="javascript:Persona.openBuscar('VerifyCajaProveedor',1,0);">RUC</button>
                    </div>
                    <input id="hid_proveedorID" type="hidden" value="">
                    <input id="txt_Proveedor" name="txt_Proveedor" type="text" class="form-control" disabled="disabled"/>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-4">
                    <div class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon"><b>Fecha Doc.</b></span>
                        <input id="txt_FechaDoc" name="txt_FechaDoc" type="text" class="form-control" style="width:120px;"/>
                      </div>
                    </div>
                  </div>
                  <div class="col-xs-4">
                    <div class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon"><b>Documento</b></span>
                        <select id="cbo_cajaDocs" name="cbo_cajaDocs" class="form-control selectpicker"></select>
                      </div>
                    </div>
                  </div>
                  <div class="col-xs-4">
                    <div id="div_tasaMax" class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon"><b>Nro Doc.</b></span>
                        <input id="txt_NroCajaDoc" name="txt_NroCajaDoc" type="text" class="form-control" placeholder="000-000000"/>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <div class="input-group-btn">
                      <button type="button" class="btn btn-info" onclick="javascript:Persona.openBuscar('VerifySuplentes',1,$('#hid_modSuplentesAhorroID').val());">Cta. Contable</button>
                    </div>
                    <input id="hid_ctacontableID" type="hidden" value="">
                    <input id="txt_CtaContable" name="txt_CtaContable" type="text" class="form-control" disabled="disabled"/>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="box-body box-profile">
            <button type="button" class="btn btn-default" onclick="javascript:appProductoCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
            <?php
              if($_SESSION['usr_usernivelID']==701){ //solo superadmin
            ?>
            <button id="btnInsert" type="button" class="btn btn-primary pull-right" onclick="javascript:appProductoInsert();"><i class="fa fa-save"></i> Guardar</button>
            <button id="btnUpdate" type="button" class="btn btn-info pull-right" onclick="javascript:appProductoUpdate();"><i class="fa fa-save"></i> Actualizar</button>
            <?php } ?>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="modal fade" id="modalPers" role="dialog"></div>
</section>

<script src="pages/caja/caja/caja.js"></script>
<script>
  $(document).ready(function(){
    Persona.addModalToParentForm('modalPers');
    appCajaGetAll();
  });
</script>
