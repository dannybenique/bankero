<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- modal de Personas -->
<script src="pages/modals/Personas/mod.persona.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-gears"></i> Ahorros</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Ahorros</li>
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
              <button type="button" class="btn btn-default btn-sm" onclick="javascript:Persona.openBuscar('VerifyAhorros',0,0);"><i class="fa fa-plus"></i></button>
            </div>
            <button type="button" class="btn btn-default btn-sm" onclick="javascript:appAhorrosReset();"><i class="fa fa-refresh"></i></button>
            <div class="btn-group">
              <select id="cboAgencias" class="btn btn-default btn-sm" style="height:30px;" onchange="javascript:appAhorrosGetAll();"></select>
            </div>
            <div class="btn-group">
              <input type="text" id="txtBuscar" name="txtBuscar" class="form-control input-sm pull-right" placeholder="DNI, persona..." onkeypress="javascript:appAportesBuscar(event);" style="text-transform:uppercase;" autocomplete="off">
              <span class="fa fa-search form-control-feedback"></span>
            </div>
            <span id="grdDatosCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
            <table class="table table-hover" id="grdDatos">
              <thead>
                <tr>
                  <th style="width:30px;"><input type="checkbox" id="chk_All" onclick="SelectAll(this,'chk_Borrar','grdDatos');" /></th>
                  <th style="width:80px;">Codigo</th>
                  <th style="width:100px;">DNI</th>
                  <th style="width:300px;">Socio</th>
                  <th style="">Agencia</th>
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
      <div class="box box-primary">
        <div class="box-body box-profile">
          <div class="col-md-5">
            <input type="hidden" id="hid_PersUrlFoto" value="">
            <img class="profile-user-img img-responsive img-circle" src="" id="img_PersFoto" alt="Foto de Usuario">
            <h3 class="profile-username text-center" style="font-family:flexobold" id="lbl_SocioApellidos"></h3>
            <p class="text-muted text-center" style="margin-top:-10px;" id="lbl_SocioNombres"></p>
          </div>
          <div class="col-md-7">
            <ul class="list-group list-group-unbordered">
              <li class="list-group-item"><b>ID</b>  <a class="pull-right" id="lbl_SocioID"></a></li>
              <li class="list-group-item"><b>DNI</b> <a class="pull-right" id="lbl_SocioDNI"></a></li>
              <li class="list-group-item"><b>Celular</b> <a class="pull-right" id="lbl_SocioCelular"></a></li>
              <li class="list-group-item"><b>Agencia</b> <a class="pull-right" id="lbl_SocioAgencia"></a>
                <input type="hidden" id="hid_SocioAgenciaID" value=""/></li>
            </ul>
            <center>

            </center>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-12">
      <div class="box box-primary">
        <form class="form-horizontal" id="frmPersona" name="frmPersona" autocomplete="off">
          <div class="box-body">
            <div class="box-header no-padding">
              <div class="mailbox-controls">
                <div class="btn-group">
                  <button type="button" class="btn btn-default btn-sm" onclick="javascript:appAhorrosRegresar();"><i class="fa fa-angle-double-left"></i> Regresar</button>
                  <button type="button" class="btn btn-default btn-sm" onclick="javascript:appAhorrosNew();" title="Nuevo Ahorro"><i class="fa fa-plus"></i></button>
                  <button type="button" class="btn btn-default btn-sm" onclick="javascript:appAhorrosSaldosReset();" title="Recargar ahorros"><i class="fa fa-refresh"></i></button>
                </div>
                <span style="font-size:16px;"><a>SERVICIOS</a></span>
              </div>
              <div class="box-body table-responsive no-padding">
                <table class="table table-hover" id="grdAhorros">
                  <thead>
                    <tr>
                      <th style="width:20px;">#</th>
                      <th style="width:20px;"><i class="fa fa-file-text-o" title="Extracto Bancario..."></i></th>
                      <th style="width:20px;"><i class="fa fa-odnoklassniki" title="Suplentes..."></i></th>
                      <th style="width:20px;"><i class="fa fa-linkedin-square" title="Generar Intereses..."></i></th>
                      <th style="width:20px;"><i class="fa fa-send-o" title="Retirar Ahorros..."></i></th>
                      <th style="">Concepto</th>
                      <th style="width:80px;">Intereses</th>
                      <th style="width:80px;text-align:right;">Certificado</th>
                      <th style="width:80px;text-align:right;">Fecha Ini</th>
                      <th style="width:80px;text-align:right;">Fecha Fin</th>
                      <th style="width:80px;text-align:right;">Saldo</th>
                    </tr>
                  </thead>
                  <tbody id="grdAhorrosBody">
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalAhorros" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form class="form-horizontal" id="frm_modalAhorros" autocomplete="off">
          <div class="modal-header" style="background:#f9f9f9;padding:8px;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title fontFlexoRegular">Ahorros para... <span id="lbl_modAhorrosTitulo"></span></h4>
          </div>
          <div class="modal-body" id="divAhorros">
            <div class="box-body">
              <div class="row">
                <div class="col-xs-3">
                  <div class="form-group" style="margin-bottom:5px;">
                    <div class="input-group">
                      <span class="input-group-addon bg-green" style="background:#f5f5f5;">Fecha</span>
                      <input id="txt_fecha" name="txt_fecha" type="text" class="form-control" style="width:105px;"/>
                    </div>
                  </div>
                </div>
                <div class="col-xs-9">
                  <div class="form-group" id="div_fechaFin" style="margin-bottom:5px;">
                    <div class="input-group">
                      <span class="input-group-addon" style="background:#ffffff;color:#999999;">El plazo de este producto vence el</span>
                      <input id="txt_fechaFin" name="txt_fechaFin" type="text" class="form-control" disabled="disabled" style="width:105px;color:#999999;"/>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-6">
                  <div class="form-group" style="margin-bottom:5px;">
                    <div class="input-group">
                      <span class="input-group-addon" style="background:#f5f5f5;"><b>Servicio</b></span>
                      <select id="cbo_productos" name="cbo_productos" class="form-control"></select>
                    </div>
                  </div>
                </div>
                <div class="col-xs-3">
                  <div class="form-group" id="div_tasa" style="margin-bottom:5px;">
                    <div class="input-group">
                      <span class="input-group-addon" style="background:#f5f5f5;"><b>Tasa %</b></span>
                      <input id="txt_tasa" name="txt_tasa" type="text" maxlength="8" class="form-control" placeholder="0.00"/>
                    </div>
                  </div>
                </div>
                <div class="col-xs-3">
                  <div class="form-group" id="div_plazo" style="margin-bottom:5px;">
                    <div class="input-group">
                      <span class="input-group-addon" style="background:#f5f5f5;"><b>Plazo (meses)</b></span>
                      <input id="txt_plazo" name="txt_plazo" type="text" class="form-control" placeholder="0" onblur="modalAhorrosFechaFin();"/>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-3">
                  <div class="form-group" id="div_nrocert" style="margin-bottom:5px;">
                    <div class="input-group">
                      <span class="input-group-addon" style="background:#f5f5f5;"><b>Nro Cert.</b></span>
                      <input id="txt_nrocert" name="txt_nrocert" type="text" class="form-control" style="width:105px;"/>
                    </div>
                  </div>
                </div>
                <div class="col-xs-9">
                  <div class="form-group" style="margin-bottom:5px;">
                    <div class="input-group">
                      <span class="input-group-addon" style="background:#f5f5f5;"><b>Promotor</b></span>
                      <select id="cbo_promotor" name="cbo_promotor" class="form-control"></select>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-6">
                  <div class="form-group" style="margin-bottom:5px;">
                    <div class="input-group">
                      <span class="input-group-addon" style="background:#f5f5f5;"><b>Intereses</b></span>
                      <select id="cbo_intereses" name="cbo_intereses" class="form-control">
                        <option value="1">Mensual</option>
                        <option value="0">Al vencimiento</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <br>
            <div class="table-responsive no-padding">
              <table class="table table-hover" id="grdAhorrosNew">
                <thead>
                  <tr>
                    <th style="width:30px;">Nº</th>
                    <th style="">Concepto</th>
                    <th style="width:80px;">Importe</th>
                  </tr>
                </thead>
                <tbody id="grdAhorrosNewBody">
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="2" style="text-align:right;font-size:18px;"><b>TOTAL</b></td>
                    <td><input id="txt_PagoTotal" name="txt_PagoTotal" type="text" class="form-control" style="width:130px;text-align:right;" disabled="disabled" value="0.00" /></td>
                  </tr>
                </tfoot>
              </table>
            </div>
            <div class="box-body">
              <div class="form-group" style="margin-top:15px;">
                <div class="input-group">
                  <textarea id="txt_observac" name="txt_observac" type="text" placeholder="Observaciones..." cols="120" rows="3" style="width:100%;"></textarea>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-body" id="divAhorrosPrint" style="display:none;">
            <object id="obj_modalAhorroPDF" type="text/html" data="" width="100%" height="300px"></object>
          </div>
          <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
            <button type="button" id="btn_modalCerrar" class="btn btn-default pull-left btn-sm" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
            <button type="button" id="btn_modalAhorrosInsert" class="btn btn-primary btn-sm" onclick="javascript:modalAhorrosInsert(<?php echo ("'https://".$webconfig->getURL()."'");?>);"><i class="fa fa-credit-card"></i> Aceptar e Imprimir</button>
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
            <h4 class="modal-title fontFlexoRegular">Retiros para... <span id="lbl_modRetirosTitulo"></span></h4>
          </div>
          <div class="modal-body" id="divRetiros">
            <div class="box-body">
              <input id="hid_modRetirosDisponible" type="hidden" value="" />
              <input id="hid_modRetirosTipo" type="hidden" value="" />
              <input id="hid_modRetirosAnti" type="hidden" value="" />
              <input id="hid_modRetirosAhorroID" type="hidden" value="" />
              <div class="row">
                <div class="col-xs-3">
                  <div class="form-group" style="margin-bottom:5px;">
                    <div class="input-group">
                      <span class="input-group-addon bg-red" style="background:#f5f5f5;">Fecha</span>
                      <input id="txt_modRetirosFecha" name="txt_modRetirosFecha" type="text" class="form-control" disabled="disabled" style="width:105px;"/>
                    </div>
                  </div>
                </div>
                <div class="col-xs-9" style="align-items:center;">
                  <span id="lbl_modRetirosMensajes" style="color:red;line-height:34px;"></span>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-2">
                  <div class="form-group" style="margin-bottom:5px;">
                    <div class="input-group">
                      <span class="input-group-addon" style="background:#f5f5f5;border-left:1px solid #D2D6DE;"><b>Servicio</b></span>
                    </div>
                  </div>
                </div>
                <div class="col-xs-10">
                  <div class="form-group" style="margin-bottom:5px;">
                    <div class="input-group">
                      <span id="lbl_modRetirosServicio" class="input-group-addon" style="border-right:1px solid #D2D6DE;text-align:left;"></span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-2">
                  <div class="form-group" style="margin-bottom:5px;">
                    <div class="input-group">
                      <span class="input-group-addon" style="background:#f5f5f5;border-left:1px solid #D2D6DE;"><b>Promotor</b></span>
                    </div>
                  </div>
                </div>
                <div class="col-xs-10">
                  <div class="form-group" style="margin-bottom:5px;">
                    <div class="input-group">
                      <span id="lbl_modRetirosPromotor" class="input-group-addon" style="border-right:1px solid #D2D6DE;text-align:left;"></span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-2">
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon" style="background:#f5f5f5;border-left:1px solid #D2D6DE;"><b>Retiro Intereses</b></span>
                    </div>
                  </div>
                </div>
                <div class="col-xs-10">
                  <div class="form-group">
                    <div class="input-group">
                      <span id="lbl_modRetirosInteresTipo" class="input-group-addon" style="border-right:1px solid #D2D6DE;text-align:left;"></span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-3">
                  <div class="row">
                    <div class="col-xs-6">
                      <div class="form-group" style="margin-bottom:5px;">
                        <div class="input-group">
                          <span class="input-group-addon" style="background:#f5f5f5;border-left:1px solid #D2D6DE;"><b>Importe</b></span>
                        </div>
                      </div>
                    </div>
                    <div class="col-xs-6">
                      <div class="form-group" style="margin-bottom:5px;">
                        <div class="input-group">
                          <span id="lbl_modRetirosImporte" class="input-group-addon" style="border-right:1px solid #D2D6DE;text-align:right;"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-xs-6">
                      <div class="form-group" style="margin-bottom:5px;">
                        <div class="input-group">
                          <span class="input-group-addon" style="background:#f5f5f5;border-left:1px solid #D2D6DE;"><b>Intereses</b></span>
                        </div>
                      </div>
                    </div>
                    <div class="col-xs-6">
                      <div class="form-group" style="margin-bottom:5px;">
                        <div class="input-group">
                          <span id="lbl_modRetirosIntereses" class="input-group-addon" style="border-right:1px solid #D2D6DE;text-align:right;"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-xs-6">
                      <div class="form-group">
                        <div class="input-group">
                          <span class="input-group-addon" style="background:#f5f5f5;border-left:1px solid #D2D6DE;"><b>Saldo</b></span>
                        </div>
                      </div>
                    </div>
                    <div class="col-xs-6">
                      <div class="form-group">
                        <div class="input-group">
                          <span id="lbl_modRetirosSaldo" class="input-group-addon" style="border-right:1px solid #D2D6DE;text-align:right;color:blue;"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-xs-9">
                  <div class="box-body">
                    <div class="form-group" style="margin-top:-10px;margin-left:20px;">
                      <div class="input-group">
                        <textarea id="txt_modRetirosObservac" name="txt_modRetirosObservac" type="text" placeholder="Observaciones..." cols="100" rows="4" style="width:100%;"></textarea>
                      </div>
                    </div>
                  </div>
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
                    <td><input id="txt_RetirosTotal" name="txt_RetirosTotal" type="text" class="form-control" style="width:130px;text-align:right;" disabled="disabled" value="0.00" /></td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
          <div class="modal-body" id="divRetirosPrint" style="display:none;">
            <object id="obj_modalRetirosPDF" type="text/html" data="" width="100%" height="300px"></object>
          </div>
          <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
            <button type="button" class="btn btn-default pull-left btn-sm" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
            <button type="button" id="btn_modRetirosInsert" class="btn btn-primary btn-sm" onclick="javascript:modalRetirosInsert(<?php echo ("'https://".$webconfig->getURL()."'");?>);"><i class="fa fa-print"></i> Aceptar e Imprimir</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalMtto" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <form class="form-horizontal" id="frm_modalMtto" autocomplete="off">
          <div class="modal-header" style="background:#f9f9f9;padding:8px;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title fontFlexoRegular">Mantenimiento de servicio para... <span id="lbl_modMttoTitulo"></span></h4>
          </div>
          <div class="modal-body">
            <div class="box-body">
              <input id="hid_modMttoAhorroID" type="hidden" value="" />
              <div class="row">
                <div class="col-xs-2">
                  <div class="form-group" style="margin-bottom:5px;">
                    <div class="input-group">
                      <span class="input-group-addon" style="background:#f5f5f5;border-left:1px solid #D2D6DE;"><b>Servicio</b></span>
                    </div>
                  </div>
                </div>
                <div class="col-xs-10">
                  <div class="form-group" style="margin-bottom:5px;">
                    <div class="input-group">
                      <span id="lbl_modMttoServicio" class="input-group-addon" style="border-right:1px solid #D2D6DE;text-align:left;"></span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-2">
                  <div class="form-group" style="margin-bottom:5px;">
                    <div class="input-group">
                      <span class="input-group-addon" style="background:#f5f5f5;border-left:1px solid #D2D6DE;"><b>Promotor</b></span>
                    </div>
                  </div>
                </div>
                <div class="col-xs-10">
                  <div class="form-group" style="margin-bottom:5px;">
                    <div class="input-group">
                      <span id="lbl_modMttoPromotor" class="input-group-addon" style="border-right:1px solid #D2D6DE;text-align:left;"></span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-3">
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon" style="background:#f5f5f5;border-left:1px solid #D2D6DE;"><b>Retiro Intereses</b></span>
                    </div>
                  </div>
                </div>
                <div class="col-xs-3">
                  <div class="form-group">
                    <div class="input-group">
                      <span id="lbl_modMttoIntereses" class="input-group-addon" style="border-right:1px solid #D2D6DE;text-align:left;"></span>
                    </div>
                  </div>
                </div>
                <div class="col-xs-3">
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon" style="background:#f5f5f5;border-left:1px solid #D2D6DE;" title="Fecha de Contrato"><b>Contrato</b></span>
                    </div>
                  </div>
                </div>
                <div class="col-xs-3">
                  <div class="form-group">
                    <div class="input-group">
                      <span id="lbl_modMttoFecContrato" class="input-group-addon" style="border-right:1px solid #D2D6DE;text-align:left;"></span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-3">
                  <div class="row">
                    <div class="col-xs-6">
                      <div class="form-group" style="margin-bottom:5px;">
                        <div class="input-group">
                          <span class="input-group-addon" style="background:#f5f5f5;border-left:1px solid #D2D6DE;"><b>Importe</b></span>
                        </div>
                      </div>
                    </div>
                    <div class="col-xs-6">
                      <div class="form-group" style="margin-bottom:5px;">
                        <div class="input-group">
                          <span id="lbl_modMttoImporte" class="input-group-addon" style="border-right:1px solid #D2D6DE;text-align:right;"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-xs-6">
                      <div class="form-group" style="margin-bottom:5px;">
                        <div class="input-group">
                          <span class="input-group-addon" style="background:#f5f5f5;border-left:1px solid #D2D6DE;"><b>Saldo</b></span>
                        </div>
                      </div>
                    </div>
                    <div class="col-xs-6">
                      <div class="form-group" style="margin-bottom:5px;">
                        <div class="input-group">
                          <span id="lbl_modMttoSaldo" class="input-group-addon" style="border-right:1px solid #D2D6DE;text-align:right;"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-xs-9">
                  <div class="box-body">
                    <div class="form-group" style="margin-top:-10px;margin-left:20px;">
                      <div class="input-group">
                        <textarea id="txt_modMttoObservac" name="txt_modMttoObservac" type="text" placeholder="Observaciones..." cols="100" rows="3" style="width:100%;"></textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-6">
                  <div class="form-group" style="margin-bottom:5px;">
                    <div class="input-group">
                      <span class="input-group-addon bg-red" style="border:1px solid #AB3A2C;">Desde</span>
                      <input id="txt_modMttoDesde" name="txt_modMttoDesde" type="text" class="form-control" style="width:105px;"/>
                    </div>
                  </div>
                </div>
                <div class="col-xs-6">
                  <div class="form-group" style="margin-bottom:5px;">
                    <div class="input-group">
                      <span class="input-group-addon bg-red" style="border:1px solid #AB3A2C;">Hasta</span>
                      <input id="txt_modMttoHasta" name="txt_modMttoHasta" type="text" class="form-control" style="width:105px;"/>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-6">
                  <div class="form-group" style="margin-bottom:5px;">
                    <div class="input-group">
                      <span class="input-group-addon bg-red" style="border:1px solid #AB3A2C;" title="Retiro Anticipado">Antic.</span>
                      <select id="cbo_modMttoRetiro" class="btn btn-default btn-sm" style="height:30px;">
                        <option value="0">NO</option>
                        <option value="1">SI</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="col-xs-6">
                  <div class="form-group" style="margin-bottom:5px;">
                    <div class="input-group">
                      <span class="input-group-addon bg-red" style="border:1px solid #AB3A2C;" title="Ahorro Bloqueado">Bloqu.</span>
                      <select id="cbo_modMttoBloqueo" class="btn btn-default btn-sm" style="height:30px;">
                        <option value="0">NO</option>
                        <option value="1">SI</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
            <button type="button" class="btn btn-default btn-sm pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
            <button type="button" id="btn_modMttoUpdate" class="btn btn-primary btn-sm" onclick="javascript:modalMttoUpdate();"><i class="fa fa-save"></i> Actualizar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalIntereses" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <form class="form-horizontal" id="frm_modalIntereses" autocomplete="off">
          <div class="modal-header" style="background:#f9f9f9;padding:8px;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title fontFlexoRegular"><span id="lbl_modInteresesTitulo">Generar Intereses para... </span></h4>
          </div>
          <div class="modal-body" id="divIntereses">
            <div class="box-body">
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon"><b>Servicio</b></span>
                  <input id="hid_modInteresesAhorroID" type="hidden" value="">
                  <input id="txt_modInteresesProducto" name="txt_InteresesProducto" type="text" class="form-control" disabled="disabled"/>
                </div>
              </div>
              <div class="box-body table-responsive no-padding">
                <span id="lbl_modInteresesWait"></span>
                <table class="table table-hover" id="modInteresesGridDatosTabla">
                  <tbody id="modInteresesGridDatosBody">
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
            <button type="button" class="btn btn-default pull-left btn-sm" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
            <button type="button" id="btn_modInteresesGenerar" class="btn btn-primary btn-sm" onclick="javascript:modalInteresesGenerar();"><i class="fa fa-flash"></i> Generar Intereses</button>
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
            <h4 class="modal-title fontFlexoRegular">Extr. Bancario... <span id="lbl_modExtractoTitulo"></span></h4>
          </div>
          <div class="modal-body">
              <div class="box-body table-responsive no-padding">
                <table class="table table-hover" id="grdExtracto">
                  <thead>
                    <tr>
                      <th style="width:40px;">AG</th>
                      <th style="width:40px;">USR</th>
                      <th style="width:40px;">MOV</th>
                      <th style="width:85px;text-align:right;">Fecha</th>
                      <th style="width:85px;text-align:right;">Nro Oper.</th>
                      <th style="">Detalle</th>
                      <th style="width:90px;text-align:right;">Depositos</th>
                      <th style="width:90px;text-align:right;">Retiros</th>
                    </tr>
                  </thead>
                  <tbody id="grdExtractoBody">
                  </tbody>
                </table>
              </div>
          </div>
          <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
            <button type="button" class="btn btn-default pull-left btn-sm" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalSuplentes" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form class="form-horizontal" id="frm_modalSuplentes" autocomplete="off">
          <div class="modal-header" style="background:#f9f9f9;padding:8px;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title fontFlexoRegular">Suplentes... <span id="lbl_modSuplentesTitulo"></span></h4>
          </div>
          <input id="hid_modSuplentesAhorroID" type="hidden" value="">
          <div class="modal-body" id="modSuplentesGrid">
            <div class="btn-group">
              <button type="button" class="btn btn-primary btn-sm" onclick="javascript:appSuplentesCommand('new');" title="Añadir Suplente"><i class="fa fa-plus"></i></button>
              <button type="button" class="btn btn-primary btn-sm" onclick="javascript:appSuplentesCommand('del');" title="Retirar Suplente"><i class="fa fa-minus"></i></button>
            </div>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover" id="grdSuplentes">
                <thead>
                  <tr>
                    <th style="width:30px;"><input type="checkbox" id="chk_AllSuplente" onclick="SelectAll(this,'chk_BorrarSuplente','grdSuplentes');" /></th>
                    <th style="width:30px;">#</th>
                    <th style="width:80px;text-align:right;">DNI</th>
                    <th style="">Persona</th>
                    <th style="width:130px;">Tipo</th>
                    <th style="width:100px;">Fecha Nac.</th>
                    <th style="width:100px;">Parentesco</th>
                    <th style="width:100px;">Telefono</th>
                  </tr>
                </thead>
                <tbody id="grdSuplentesBody">
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-body" id="modSuplentesEdit" style="display:none;">
            <div class="box-body">
              <div id="div_modSuplentesPersona" class="form-group">
                <div class="input-group">
                  <div class="input-group-btn">
                    <button type="button" class="btn btn-info" onclick="javascript:Persona.openBuscar('VerifySuplentes',1,$('#hid_modSuplentesAhorroID').val());">Persona</button>
                  </div>
                  <input id="hid_modSuplenteID" type="hidden" value="">
                  <input id="txt_modSuplentesPersona" name="txt_modSuplentesPersona" type="text" class="form-control" disabled="disabled"/>
                </div>
              </div>
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon"><b>Tipo</b></span>
                  <select id="cbo_modSuplentesTipo" name="cbo_modSuplentesTipo" class="form-control">
                    <option value="0">Beneficiario</option>
                    <option value="1">Mancomunado "Y"</option>
                    <option value="2">Mancomunado "O"</option>
                  </select>
                </div>
              </div>
              <div id="div_modSuplentesParentesco" class="form-group">
                <div class="input-group">
                  <span class="input-group-addon"><b>Parentesco</b></span>
                  <input id="txt_modSuplentesParentesco" name="txt_modSuplentesParentesco" type="text" class="form-control"/>
                </div>
              </div>
              <div class="no-padding">
                <button type="button" class="btn btn-default btn-sm" onclick="javascript:modalSuplenteCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
                <button type="button" class="btn btn-primary btn-sm pull-right" onclick="javascript:modalSuplenteInsert();">Agregar Suplente</button>
              </div>
            </div>
          </div>
          <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
            <div class="btn-group pull-left">
              <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalPers" role="dialog"></div>
</section>

<script src="pages/caja/ahorros/ahorros.js"></script>
<script>
  $(document).ready(function(){
    appAhorrosReset(<?php echo $_SESSION['usr_agenciaID'];?>);
    Persona.addModalToParentForm('modalPers');
  });
</script>
