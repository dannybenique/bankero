<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-gears"></i> Aportes</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Aportes</li>
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
              <button type="button" class="btn btn-default btn-sm" onclick="javascript:appAportesReset();"><i class="fa fa-refresh"></i></button>
            </div>
            <div class="btn-group">
              <select id="cboAgencias" class="btn btn-default btn-sm" style="height:30px;" onchange="javascript:appAportesGetAll();"></select>
              <input type="hidden" id="agenciaAbrev" value="">
            </div>
            <div class="btn-group">
              <input type="text" id="txtBuscar" name="txtBuscar" class="form-control input-sm pull-right" placeholder="DNI, persona..." onkeypress="javascript:appAportesBuscar(event);" style="text-transform:uppercase;">
              <span class="fa fa-search form-control-feedback"></span>
            </div>
            <span id="grdDatosCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
            <table class="table table-hover" id="grdDatos">
              <thead>
                <tr>
                  <th style="width:80px;">Codigo</th>
                  <th style="width:100px;">DNI</th>
                  <th style="width:300px;">Socio</th>
                  <th style="width:100px;text-align:right;">Saldo</th>
                  <th style="width:120px;">Agencia</th>
                  <th style="">Observaciones</th>
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
      <div class="box box-widget widget-user-2">
        <div class="widget-user-header bg-aqua-active">
          <div class="widget-user-image">
            <input type="hidden" id="hid_PersUrlFoto" value="">
            <img class="profile-user-img img-circle" src="" id="img_PersFoto" alt="persona"/>
          </div>
          <div style="min-height:70px;">
            <h5 class="widget-user-username fontFlexoRegular" id="lbl_SocioApellidos"></h5>
            <h4 class="widget-user-desc fontFlexoRegular" id="lbl_SocioNombres"></h4>
          </div>
        </div>
        <div class="no-padding">
          <ul class="list-group list-group-unbordered">
            <li class="list-group-item" style="padding:5px 10px 5px 10px;"><b>ID</b><a class="pull-right" id="lbl_SocioID"></a></li>
            <li class="list-group-item" style="padding:5px 10px 5px 10px;"><b>DNI</b><a class="pull-right" id="lbl_SocioDNI"></a></li>
            <li class="list-group-item" style="padding:5px 10px 5px 10px;"><b>Celular</b><a class="pull-right" id="lbl_SocioCelular"></a></li>
            <li class="list-group-item" style="padding:5px 10px 5px 10px;"><b>Agencia</b><a class="pull-right" id="lbl_SocioAgencia"></a></li>
          </ul>
        </div>
        <div class="box-body">
          <button type="button" class="btn btn-default" onclick="javascript:appAportesRegresar();"><i class="fa fa-angle-double-left"></i> Regresar</button>
        </div>
      </div>
    </div>
    <div class="col-md-9">
      <form class="form-horizontal" id="frmPersona" name="frmPersona" autocomplete="off">
        <div class="box box-primary">
          <div class="box-body">
            <div class="box-header no-padding">
              <div class="mailbox-controls">
                <div class="btn-group">
                  <input type="hidden" id="hid_AportesUrlServer" value="<?php echo ("https://".$webconfig->getURL());?>"/>
                  <button type="button" class="btn btn-info btn-sm" onclick="javascript:appExtractoBancario();" title="Extracto Bancario" ><i class="fa fa-file-text"></i></button>
                  <button type="button" class="btn btn-default btn-sm" onclick="javascript:appAportesNewAporte();" title="Añadir Aporte"><i class="fa fa-plus"></i></button>
                  <button type="button" class="btn btn-default btn-sm" onclick="javascript:appAportesRetiro();" title="Retirar Aporte"><i class="fa fa-minus"></i></button>
                </div>
                <div class="btn-group">
                  <button type="button" class="btn btn-default btn-sm" onclick="javascript:appAportesSaldosReset();"><i class="fa fa-refresh"></i></button>
                </div>
              </div>
              <div class="box-body table-responsive no-padding">
                <table class="table table-hover" id="grdAportes">
                  <thead>
                    <tr>
                      <th style="width:30px;">Nº</th>
                      <th style="">Concepto</th>
                      <th style="width:100px;text-align:right;">Importe</th>
                      <th style="width:100px;text-align:right;">Accion</th>
                    </tr>
                  </thead>
                  <tbody id="grdAportesBody">
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="modal fade" id="modalPagos" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form class="form-horizontal" id="frm_modalPagos" autocomplete="off">
          <div class="modal-header" style="background:#f9f9f9;padding:8px;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title fontFlexoRegular">Añadir aportes para... <span id="lbl_modPagosTitulo"></span></h4>
          </div>
          <div class="modal-body" id="divPagos">
            <div class="box-body">
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon bg-green">Fecha de Pago</span>
                  <input id="date_fechaing" name="date_fechaing" type="text" class="form-control" style="width:105px;"/>
                </div>
              </div>
            </div>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover" id="grdPagos">
                <thead>
                  <tr>
                    <th style="width:30px;">Nº</th>
                    <th style="">Concepto</th>
                    <th style="width:80px;">Importe</th>
                  </tr>
                </thead>
                <tbody id="grdPagosBody">
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="2" style="text-align:right;font-size:18px;"><b>TOTAL</b></td>
                    <td><input id="txt_PagoTotal" name="txt_PagoTotal" type="number" class="form-control" style="width:120px;text-align:right;" disabled="disabled" value="0.00" /></td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
          <div class="modal-body" id="divPrint" style="display:none;">
            <object id="objPDF" type="text/html" data="" width="100%" height="300px"></object>
          </div>
          <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
            <button type="button" id="btn_modalCerrar" class="btn btn-default btn-sm pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
            <button type="button" id="btn_modalPagosInsert" class="btn btn-primary btn-sm" onclick="javascript:modalPagosInsertPago();"><i class="fa fa-flash"></i> Pagar e Imprimir</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalRetiros" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form class="form-horizontal" id="frm_modalRetiros" autocomplete="off">
          <div class="modal-header" style="background:#f9f9f9;padding:8px;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title fontFlexoRegular">Retiro de aportes para... <span id="lbl_modRetirosTitulo"></span></h4>
          </div>
          <div class="modal-body" id="divRetiros">
            <div class="box-body">
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon bg-red">Fecha de Retiro</span>
                  <input id="date_fechadel" name="date_fechadel" type="text" class="form-control" style="width:105px;"/>
                </div>
              </div>
            </div>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover" id="grdRetiros">
                <thead>
                  <tr>
                    <th style="width:30px;">Nº</th>
                    <th style="">Concepto</th>
                    <th style="width:80px;">Importe</th>
                  </tr>
                </thead>
                <tbody id="grdRetirosBody">
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="2" style="text-align:right;font-size:18px;"><b>TOTAL</b></td>
                    <td><input id="txt_RetiroTotal" name="txt_RetiroTotal" type="text" class="form-control" style="width:120px;text-align:right;" disabled="disabled" value="0.00" /></td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
          <div class="modal-body" id="divRetiroPrint" style="display:none;">
            <object id="objRetiroPDF" type="text/html" data="" width="100%" height="300px"></object>
          </div>
          <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
            <button type="button" id="btn_modalRetiroCerrar" class="btn btn-default btn-sm pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
            <button type="button" id="btn_modalRetiroDelete" class="btn btn-primary btn-sm" onclick="javascript:modalRetirosInsertRetiro(<?php echo ("'https://".$webconfig->getURL()."'");?>);"><i class="fa fa-credit-card"></i> Retirar e Imprimir</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalExtracto" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form class="form-horizontal" id="frm_modalExtracto" autocomplete="off">
          <div class="modal-header" style="background:#f9f9f9;padding:8px;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title fontFlexoRegular">Extracto Bancario de Aportes para... <span id="lbl_modExtractoTitulo"></span></h4>
          </div>
          <div class="modal-body">
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover" id="grdExtracto">
                <thead>
                  <tr>
                    <th style="width:40px;">AG</th>
                    <th style="width:40px;">USR</th>
                    <th style="width:40px;">USR</th>
                    <th style="width:85px;text-align:right;">Fecha</th>
                    <th style="width:85px;text-align:right;">Nro Oper.</th>
                    <th style="">Detalle</th>
                    <th style="width:100px;text-align:right;">Depositos</th>
                    <th style="width:100px;text-align:right;">Retiros</th>
                    <th style="width:100px;text-align:right;">Otros</th>
                  </tr>
                </thead>
                <tbody id="grdExtractoBody">
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
            <button type="button" class="btn btn-default btn-sm pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<script src="pages/caja/aportes/aportes.js"></script>
<script>
  $(document).ready(function(){
    appAportesReset(<?php echo $_SESSION['usr_agenciaID'];?>);
    $('#date_fechanac').datepicker("setDate",new Date());
    appComboBox("#cbo_ciudades","CiudadesAg",0);
  });
</script>
