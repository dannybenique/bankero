<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<section class="content-header">
  <h1><i class="fa fa-tasks"></i> CoopSUD Ahorros</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Ahorros</li>
  </ol>
</section>
<section class="content">
  <div class="row" id="grid">
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header no-padding">
          <div class="mailbox-controls">
            <div class="btn-group">
              <select id="cboTipo" class="btn btn-default btn-sm" style="height:30px;" onchange="javascript:appComboTipo();">
                <option value="1">Codigo</option>
                <option value="2">DNI</option>
              </select>
            </div>
            <div id="divBuscar" class="btn-group">
              <input type="text" id="txtBuscar" name="txtBuscar" class="form-control input-sm pull-right" placeholder="cod-socio..." onkeypress="javascript:appAhorrosBuscar(event);" style="text-transform:uppercase;" autocomplete="off">
              <span class="fa fa-search form-control-feedback"></span>
            </div>
            <span id="grdDatosCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
              <table class="table table-hover" id="grdDatos">
                <thead>
                  <tr>
                    <th style="width:30px;"><i class="fa fa-files-o" title="Formatos..."></i></th>
                    <th style="width:80px;">Codigo</th>
                    <th style="width:80px;">DNI</th>
                    <th style="">Socio</th>
                    <th style="">Servicio</th>
                    <th style="width:80px;text-align:center;" title="Fecha de Inicio">Inicio</th>
                    <th style="width:80px;text-align:center;" title="Fecha de Fin">Fin</th>
                    <th style="width:80px;text-align:center;" title="Plazo en Dias">Plazo</th>
                    <th style="width:120px;text-align:center;" title="Certificado de Ahorros">Certif.</th>
                    <th style="width:120px;text-align:right;">Importe</th>
                    <th style="width:120px;text-align:right;">Saldo</th>
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
    <div class="col-md-3">
      <form class="form-horizontal" autocomplete="off">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title fontFlexoRegular">Datos Ahorro</h3>
          </div>
          <div class="box-body">
            <strong><i class="fa fa-user margin-r-5"></i> Socio</strong><br/>
            Socio: <a id="lbl_socio"></a><br>
            DNI: <a id="lbl_DNI"></a><br>
            Codigo: <a id="lbl_codigo"></a><br><br>

            <strong><i class="fa fa-signal margin-r-5"></i> Ahorro</strong><br/>
            Tipo_Serv: <a id="lbl_tiposerv"></a><br>
            Servicio: <a id="lbl_servicio"></a><br>
            Nº Certificado: <a id="lbl_numpres"></a><br>
            Importe: <a id="lbl_importe"></a><br>
            Saldo: <a id="lbl_saldo"></a><br>
            Promotor: <a id="lbl_promotor"></a><br>
            <hr>
            <button type="button" class="btn btn-default" onclick="javascript:appBotonCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
          </div>
        </div>
      </form>
    </div>
    <div class="col-md-9">
      <div class="box box-primary">
        <div class="box-body">
          <div class="box-body table-responsive no-padding">
            <button type="button" id="btn_Recargar" class="btn btn-default btn-sm" onclick="javascript:appRefresh();" title="recargar datos de ahorro"><i class="fa fa-refresh"></i></button>
            <span id="lbl_SaldoMovim" class="label bg-blue" style="display:inline-block;margin-left:5px;font-size:14px;font-weight:500;"></span>
            <input type="hidden" id="hid_SaldoMovim" value="0"/>
            <table class="table table-hover" id="grdAhorros">
                <thead>
                  <tr>
                    <th style="width:30px;" title="Agencia">Ag.</th>
                    <th style="width:30px;" title="Ventanilla">Vnt</th>
                    <th style="width:80px;">num_trans</th>
                    <th style="width:80px;text-align:center;">Fecha</th>
                    <th style="width:30px;" title="Movimiento">Mov.</th>
                    <th style="">Detalle</th>
                    <th style="width:100px;text-align:right;">Depositos</th>
                    <th style="width:100px;text-align:right;">Retiros</th>
                    <th style="width:100px;text-align:right;">Otros</th>
                  </tr>
                </thead>
                <tbody id="grdAhorrosBody">
                </tbody>
              </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row" id="formatos" style="display:none;">
    <div class="col-md-4">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title fontFlexoRegular">Datos Socio</h3>
        </div>
        <div class="box-body">
          <div class="box-body">
            <strong><i class="fa fa-user margin-r-5"></i> Socio</strong><br/>
            Socio: <a id="lbl_FormaSocio"></a><br>
            DNI/RUC: <a id="lbl_FormaDNI"></a><br>
            Codigo: <a id="lbl_FormaCodSocio"></a><br><br>

            <strong><i class="fa fa-signal margin-r-5"></i> Ahorro</strong><br/>
            Nº Certificado: <a id="lbl_FormaNumCertificado"></a><br>
            Tipo_Serv: <a id="lbl_FormaTiposerv"></a><br>
            Servicio: <a id="lbl_FormaServicio"></a><br>
            Tipo de Retiro: <a id="lbl_FormaTiporet"></a><br>
            Fecha Inicio: <a id="lbl_FormaFechaIni"></a><br>
            Importe: <a id="lbl_FormaImporte"></a><br>
            Saldo: <a id="lbl_FormaSaldo"></a><br>
            Promotor: <a id="lbl_FormaPromotor"></a><br><br>
          </div>
          <div class="row">
            <div class="col-md-6">
              <input type="hidden" id="hid_FormUrlServer" value="<?php echo ($webconfig->getURL());?>"/>
              <button type="button" class="btn btn-default btn-sm" onclick="javascript:appBotonCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
            </div>
            <div id="div_FormaBotones" class="col-md-6">

            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <div class="box box-primary">
        <div class="box-body">
          <div class="table-responsive no-padding" id="contenedorFrame">
            <object id="objPDF" type="text/html" data="" width="100%" height="450px"></object>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalMovimientos" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" style="background:#f9f9f9;padding:8px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title fontFlexoRegular">Movimientos</h4>
        </div>
        <div class="modal-body no-padding" style="border-right:1px solid white;">
          <div class="box-body">
            <div class="pull-right">Fecha: <span id="modMovimFecha"></span></div>
            <div>Num_Trans: <span id="modMovimNumTrans"></span></div>
            <div>Agencia: <span id="modMovimAgencia"></span></div>
            <div>Ventanilla: <span id="modMovimVentanilla"></span></div>
            <br>
            <table class="table">
              <thead>
                <tr>
                  <th>Detalle</th>
                  <th style="width:80px;text-align:right;">Pagos</th>
                  <th style="width:80px;text-align:right;">Saldos</th>
                </tr>
              </thead>
              <tbody id="grdMovimientosBody">
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
          <button type="button" class="btn btn-default btn-sm pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="pages/coopsud/ahorros/ahorros.js"></script>
<script>
  $(document).ready(function(){
    appGridReset();
  });
</script>
