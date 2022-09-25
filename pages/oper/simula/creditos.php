<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-flag"></i> Simulador de Creditos</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Simulador de Creditos</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <div class="row" id="edit">
    <form class="form-horizontal" autocomplete="off">
    <div class="col-md-4">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title fontFlexoRegular">Datos para Simulacion</h3>
        </div>
        <div class="box-body">
          <div class="box-body">
            <div class="box-body">
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon" style="width:95px;"><b>Tipo</b></span>
                  <select id="cbo_Productos" class="form-control selectpicker" style="width:130px;" disabled>
                    <option value="1">Fecha Fija</option>
                    <option value="2">Plazo Fijo</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon" style="width:95px;"><b title="Tasa Efectiva Mensual">TEM %</b></span>
                  <input id="txt_TasaMensual" type="text" class="form-control" style="width:130px;"/>
                </div>
              </div>
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon" style="width:95px;"><b>Nº Cuotas</b></span>
                  <input id="txt_NroCuotas" type="text" class="form-control" style="width:130px;" />
                </div>
              </div>
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon" style="width:95px;"><b>Importe S/.</b></span>
                  <input id="txt_Importe" type="text" class="form-control" style="width:130px;" />
                </div>
              </div>
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon" style="width:95px;"><b>Seg. Desgr. %</b></span>
                  <input id="txt_SegDesgr" type="text" class="form-control" style="width:115px;"/>
                </div>
              </div>
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon" style="width:95px;"><b>Inicio</b></span>
                  <input id="date_fechaIniSimula" type="text" class="form-control" style="width:105px;"/>
                </div>
              </div>
              <div class="form-group">
              <!--
              <div class="input-group">
                <span class="input-group-addon" style="width:95px;"><b>1ª Cuota</b></span>
                <input id="date_fechaPriCuotaSimula" type="text" class="form-control" style="width:105px;"/>
              </div>
              -->
            </div>
            </div>
          </div>
          <div class="btn-group">
            <button type="button" class="btn btn-info" id="btn_print" onclick="javascript:appCreditosGenerarPlanPagos();"><i class="fa fa-flash"></i> Generar Simulacion</button>
          </div>
        </div>
      </div>
    </div>
    </form>
    <div class="col-md-8">
      <div class="box box-primary">
        <div class="box-body box-profile">
          <div style="display:inherit;margin-bottom:15px;">
            <span style="margin-left:0px; ">TED: </span> <span id="lbl_TED"  class="label bg-green" style="font-weight:normal;font-size:14px;">0.0%</span>
            <span style="margin-left:10px;">TEM: </span> <span id="lbl_TEM"  class="label bg-green" style="font-weight:normal;font-size:14px;">0.0%</span>
            <span style="margin-left:10px;">TEA: </span> <span id="lbl_TEA"  class="label bg-green" style="font-weight:normal;font-size:14px;">0.00%</span>
            <span style="margin-left:10px;">TCEA: </span><span id="lbl_TCEA" class="label bg-green" style="font-weight:normal;font-size:14px;">0.00%</span>
          </div>
          <div class="box-body table-responsive no-padding">
              <table class="table table-hover" id="grdDatos" style="font-family:helveticaneue_light;">
                <thead>
                  <tr>
                    <th style="width:30px;color:#aaa;">Dias</th>
                    <th style="width:30px;">Nro</th>
                    <th style="width:80px;text-align:center;">Fecha</th>
                    <th style="width:95px;text-align:right;">Total</th>
                    <th style="width:95px;text-align:right;">Capital</th>
                    <th style="width:95px;text-align:right;">Interes</th>
                    <th style="width:80px;text-align:right;">Desgr</th>
                    <th style="width:100px;text-align:right;">Saldo</th>
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
  <div class="modal fade" id="modalCambioFecha" role="dialog">
    <div class="modal-dialog" style="width:400px;">
      <div class="modal-content">
        <div class="modal-header" style="background:#f9f9f9;padding:8px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title fontFlexoRegular">Cambiar Fecha</h4>
        </div>
        <form class="form-horizontal" id="frmCambioFecha" autocomplete="off">
          <div class="modal-body no-padding">
            <div class="box-body">
              <table class="table no-border">
                <tr>
                  <td style="width:30%"></td>
                  <td>
                    <div class="input-group input-group-sm">
                      <span id="mod_lblFechaCambio" class="input-group-addon" style="background:#f5f5f5;"></span>
                      <input id="mod_txtFechaCambio" type="text" class="form-control" style="width:105px;"/>
                      <span class="input-group-btn">
                        <button type="button" class="btn btn-info btn-flat" onclick="javascript:modCambioFecha_Ejecutar();"><i class="fa fa-flash"></i> Cambiar</button>
                      </span>
                    </div>
                  </td>
                  <td style="width:30%"></td>
                </tr>
              </table>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalCambioCuota" role="dialog">
    <div class="modal-dialog" style="width:400px;">
      <div class="modal-content">
        <div class="modal-header" style="background:#f9f9f9;padding:8px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title fontFlexoRegular">Cambiar Fecha</h4>
        </div>
        <div class="modal-body no-padding">
          <div class="box-body">
            <table class="table no-border">
              <tr>
                <td style="width:30%"></td>
                <td>
                  <div class="input-group input-group-sm">
                    <span id="mod_lblCuotaCambio" class="input-group-addon" style="background:#f5f5f5;"></span>
                    <input id="mod_txtCuotaCambio" type="text" class="form-control" style="width:105px;" onkeypress="javascript:modCambioCuota_OnKeyPress(event);"/>
                    <div class="input-group-btn">
                      <button type="button" class="btn btn-info btn-flat" onclick="javascript:modCambioCuota_Ejecutar();"><i class="fa fa-flash"></i> Cambiar</button>
                    </div>
                  </div>
                </td>
                <td style="width:30%"></td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="pages/oper/simula/simula.js"></script>
<script>
  $(document).ready(function(){ appCreditosReset(); });
</script>
