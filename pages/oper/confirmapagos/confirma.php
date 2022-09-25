<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<section class="content-header">
  <h1><i class="fa fa-tasks"></i> Confirmaciones de Pago</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Pagos</li>
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
              <button type="button" class="btn btn-default btn-sm" onclick="javascript:appBotonDelete();"><i class="fa fa-trash-o"></i></button>
              <?php }?>
              <button type="button" class="btn btn-default btn-sm" onclick="javascript:appBotonNuevo();"><i class="fa fa-plus"></i></button>
            </div>
            <div class="btn-group">
              <button type="button" class="btn btn-default btn-sm" onclick="javascript:appBotonReset();" title="Volver a cargar toda la lista de potenciales ahorristas"><i class="fa fa-refresh"></i></button>
            </div>
            <div class="btn-group">
              <select id="cboAgencias" class="btn btn-default btn-sm" style="height:30px;text-align:left;" disabled="disabled" onchange="javascript:appGridAll();"></select>
            </div>
            <div class="btn-group">
              <input type="text" id="txtBuscar" class="form-control input-sm pull-right" placeholder="buscar DNI..." onkeypress="javascript:appBotonBuscar(event);" autocomplete="off">
              <span class="fa fa-search form-control-feedback"></span>
            </div>
            <span id="grdDatosCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
            <table class="table table-hover" id="grdDatos">
              <thead>
                <tr>
                  <th style="width:25px;"><input type="checkbox" id="chk_BorrarAll" onclick="toggleAll(this,'chk_Borrar');" <?php if($_SESSION['usr_usernivelID']!=701){echo("disabled");}?>/></th>
                  <th style="width:25px;"><i class="fa fa-database"></i></th>
                  <th style="width:80px;">Tipo</th>
                  <th style="">Socio</th>
                  <th style="width:80px;">Fecha</th>
                  <th style="">Voucher</th>
                  <th style="width:100px;text-align:right;">Importe</th>
                  <th style="width:70px;text-align:right;">Comis.</th>
                  <th style="">Solicitud</th>
                  <th style="">Confirmacion</th>
                  <th style="width:65px;text-align:center;">Status</th>
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
  <div class="row" id="edit" style="display:none">
    <form class="form-horizontal" autocomplete="off">
      <div class="col-md-6">
        <div class="box box-primary">
          <div class="box-body">
            <div class="box-body">
              <div id="div_fecha" class="form-group">
                <div class="input-group">
                  <span class="input-group-addon" style="width:90px;">Fecha Ing.</span>
                  <input id="txt_fecha" type="text" class="form-control" style="width:105px;" />
                </div>
              </div>
              <div id="div_codsocio" class="form-group" style="margin-bottom:5px;">
                <div class="input-group">
                  <div class="input-group-btn">
                    <button type="button" class="btn btn-primary btn-flat" style="width:90px;" onclick="javascript:appBotonModalSocios();">CodSocio</button>
                  </div>
                  <input id="txt_codsocio" type="text" class="form-control" readonly />
                </div>
              </div>
              <div id="div_tipo_oper" class="form-group" style="margin-bottom:5px;">
                <div class="input-group">
                  <span class="input-group-addon" style="width:90px;">Tipo</span>
                  <select id="cbo_tipo_oper" class="form-control selectpicker">
                    <option value="4">Creditos</option>
                    <option value="2">Ahorros</option>
                  </select>
                </div>
              </div>
              <div id="div_bancos" class="form-group" style="margin-bottom:5px;">
                <div class="input-group">
                  <span class="input-group-addon" style="width:90px;">Banco</span>
                  <select id="cbo_bancos" class="form-control selectpicker"></select>
                </div>
              </div>
              <div id="div_voucher" class="form-group" style="margin-bottom:5px;">
                <div class="input-group">
                  <span class="input-group-addon" style="width:90px;">Voucher</span>
                  <input id="txt_voucher" type="text" class="form-control" placeholder="..." style="width:150px;"/>
                </div>
              </div>
              <div id="div_importe" class="form-group" style="margin-bottom:5px;">
                <div class="input-group">
                  <span class="input-group-addon" style="width:90px;">Importe</span>
                  <div class="input-group-btn" style="width:100px;">
                    <select id="cbo_tipo_mone" class="form-control selectpicker"></select>
                  </div>
                  <input id="txt_importe" type="text" class="form-control" placeholder="0.00" style="width:110px;" onblur="javascript:$('#txt_importe').val(appFormatMoney(this.value,2));"/>
                </div>
              </div>
              <div class="box-body pull-right">
                <input type="hidden" id="hid_codsocio" value="">
                <input type="hidden" id="hid_socio" value="">
                <input type="hidden" id="hid_ID" value="">
                <button type="button" class="btn btn-default btn-sm" onclick="javascript:appBotonCancel();"><i class="fa fa-close"></i> Cancelar</button>
                <button id="btn_Insert" type="button" class="btn btn-primary btn-sm" onclick="javascript:appBotonInsert();"><i class="fa fa-save"></i> Guardar</button>
                <button id="btn_Update" type="button" class="btn btn-info btn-sm" onclick="javascript:appBotonUpdate();"><i class="fa fa-save"></i> Actualizar</button>
              </div>
            </div>

            <div style="font-style:italic;font-size:11px;color:gray;">
              <b style="font-style:normal;font-size:14px;color:black"><i class="fa fa-eye margin-r-5"></i> Auditoria</b><br>
              Fecha: <span id="lbl_SysFecha"></span><br>
              Modif. por: <span id="lbl_SysUser"></span>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
  <div class="modal fade" id="modalCoopSUDSocio" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" style="background:#f9f9f9;padding:8px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title fontFlexoRegular">Buscar Socio</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <div>
                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon no-border">codigo</span>
                    <input type="text" id="txt_BuscarModCoopSUD" class="form-control" placeholder="codsocio..." onkeypress="javascript:modCoopSUD_keyBuscar(event);">
                    <div class="input-group-btn">
                      <button type="button" class="btn btn-primary" onclick="javacript:modCoopSUDbuscar();"><i class="fa fa-search"></i></button>
                    </div>
                  </div>
                </div>
                <div class="box-body table-responsive no-padding">
                  <span id="lbl_modCoopSUDWait"></span>
                  <div id="modCoopSUDGridDatosTabla">
                    <ul class="todo-list">
                      <li style="height:60px;">
                        <div class="pull-left" style="margin-left:10px;">
                          <a href="#" class="product-title"><div id="lbl_modCoopSUDcodigo"></div></a>
                          <span id="lbl_modCoopSUDsocio" class="product-description"></span>
                        </div>
                        <div class="pull-right">
                          <button id="btn_modCoopSUDAddToForm" type="button" class="btn btn-success btn-sm" style="margin-top:5px;" onclick="javascript:modCoopSUDagregar();"><i class="fa fa-flash"></i> Agregar</button>
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalConfirmaPago" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" style="background:#f9f9f9;padding:8px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title fontFlexoRegular">Confirmar Pago</h4>
        </div>
        <div class="modal-body">
          <div class="box-body" id="div_modDatosSocio">
            <strong><i class="fa fa-user margin-r-5"></i> Datos Socio</strong>
            <div>
              <input type="hidden" id="hid_modConfirmaID" value="">
              Fecha: <a  id="lbl_modConfirmaFecha"></a><br>
              Codigo: <a id="lbl_modConfirmaCodigo"></a><br>
              Socio: <a  id="lbl_modConfirmaSocio"></a>
            </div>
            <hr>
          </div>
          <div class="box-body" id="div_modDatosPago">
            <strong><i class="fa fa-bank margin-r-5"></i> Datos Pago</strong>
            <div>
              <input type="hidden" id="hid_modConfirmaImporte" value="">
              Tipo Operacion: <a id="lbl_modConfirmaTipo"></a><br>
              Banco: <a id="lbl_modConfirmaBanco"></a><br>
              Sede: <a id="lbl_modConfirmaSede"></a><br>
              Voucher: <a id="lbl_modConfirmaVoucher"></a><br>
              Moneda: <a id="lbl_modConfirmaMoneda"></a><br>
              Importe: <a id="lbl_modConfirmaImporte"></a><br>
              Comision: <a id="lbl_modConfirmaComision"></a>
            </div>
            <hr>
          </div>
          <div class="box-body" id="div_modDatosConta" style="display:none;">
            <strong><i class="fa fa-database margin-r-5"></i> Datos Contabilidad</strong>
            <div class="row">
              <div class="col-md-9">
                <div class="box-body">
                  <div class="form-group" style="margin-bottom:5px;">
                    <div class="input-group">
                      <span class="input-group-addon no-border" style="width:120px;">Sede de Banco</span>
                      <input type="text" id="txt_modConfirma_Sede" class="form-control" placeholder="sede..." autocomplete="off" onblur="javascript:txt_modConfirma_Sede_onblur();">
                    </div>
                  </div>
                  <div class="form-group" style="margin-bottom:5px;">
                    <div class="input-group">
                      <span class="input-group-addon no-border" style="width:120px;">Comision</span>
                      <input type="text" id="txt_modConfirma_Comision" class="form-control" style="width:150px;" placeholder="0.00" autocomplete="off"  onblur="javascript:txt_modConfirma_Comision_onblur();">
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="box-body">
                  <a class="btn btn-app" href="javascript:modConfirmaConta();">
                    <i class="fa fa-save"></i> Confirmar
                  </a>
                </div>
              </div>
            </div>
          </div>
          <div class="box-body" id="div_modDatosCaja" style="display:none;">
            <strong><i class="fa fa-database margin-r-5"></i> Datos CAJA</strong>
            <div class="box-body">
              <a class="btn btn-app" href="javascript:modConfirmaCAJA();">
                <i class="fa fa-save"></i> Confirmar transaccion hecha en CAJA
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="pages/oper/confirmapagos/confirma.js"></script>
<script>
  $(document).ready(function(){
    appBotonReset();
  });
</script>
