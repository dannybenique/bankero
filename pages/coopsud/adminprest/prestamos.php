<!-- export excel -->
<script src="plugins/excel-export/xlsx.core.min.js"></script>
<script src="plugins/excel-export/FileSaver.js"></script>
<script src="plugins/excel-export/jhxlsx.js"></script>

<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<section class="content-header">
  <h1><i class="fa fa-tasks"></i> CoopSUD - Admin Prestamos</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Cancelados</li>
  </ol>
</section>
<section class="content">
  <div class="row" id="grid">
    <div class="col-md-3">
      <div class="box box-primary">
        <div id="opt_desembolso" class="box-header with-border">
          <strong><i class="fa fa-calendar"></i> Por fecha Desembolso</strong>
          <div class="input-group" style="margin-top:10px;">
            <span class="input-group-addon"><b>Desde</b></span>
            <input type="text" class="form-control pull-left" style="width:105px;" id="dateIni">
          </div>
          <div class="input-group" style="margin-top:3px;">
            <span class="input-group-addon"><b>Hasta</b></span>
            <input type="text" class="form-control pull-left" style="width:105px;" id="dateFin">
          </div>
          <div class="btn-group" style="text-align:center;margin-top:3px;margin-bottom:10px;">
            <button type="button" class="btn btn-default" onclick="javascript:appBotonVeriDesembolsos();" title="Verificar Desembolsos"><i class="fa fa-flash"></i> verificar desembolsos</button>
            <button type="button" class="btn btn-default" style="margin-left:10px;" onclick="javascript:appBotonVeriDesembolsosDownload();" title="Verificar Prestamos"><i class="fa fa-download"></i> descargar info</button>
          </div>
        </div>
        <div id="opt_solicitud" class="box-header with-border"></div>
        <div id="opt_analista" class="box-header with-border"></div>
      </div>
    </div>
    <div class="col-md-9">
      <div class="box box-primary">
        <div class="box-header no-padding">
          <div class="mailbox-controls">
            <span style="display:inline-block;margin-left:5px;font-size:20px;">Registros: </span>
            <span id="grdDatosCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
              <table class="table table-hover" id="grdDatos">
                <thead>
                  <tr>
                    <th style="width:30px;"><input type="checkbox" onclick="SelectAll(this,'chk_Borrar','grdDatos');"/></th>
                    <th style="width:30px;" title="Agencia de Desembolso">Ag.</th>
                    <th style="width:70px;">Codigo</th>
                    <th style="">Socio</th>
                    <th style="">Servicio</th>
                    <th style="" title="Tipo Credito SBS">Tipo</th>
                    <th style="" title="Destino Credito SBS">Destino</th>
                    <th style="width:80px;text-align:center;" title="Fecha de Otorgamiento">Fecha</th>
                    <th style="width:100px;text-align:right;">Importe</th>
                    <th style="width:100px;text-align:right;">Saldo</th>
                    <th style="width:180px;text-align:left;">Respons</th>
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
      <form class="form-horizontal" autocomplete="off">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title fontFlexoRegular">Datos Socio</h3>
          </div>
          <div class="box-body">
            <strong><i class="fa fa-user margin-r-5"></i> Socio</strong>
            <p class="text-muted">
              Socio: <a id="lbl_socio"></a><br>
              DNI: <a id="lbl_DNI"></a><br>
              Codigo: <a id="lbl_codigo"></a><br><br><br>
              Nº Solicitud: <span id="lbl_solicitud"></span><br>
              Nº Prestamo: <a id="lbl_numpres"></a><br>
              Tipo_Serv: <a id="lbl_tiposerv"></a><br>
              Servicio: <a id="lbl_servicio"></a><br>
              Fecha Otorg.: <a id="lbl_fecha"></a><br>
              Cuotas: <a id="lbl_cuotas"></a><br>
              Importe: <a id="lbl_importe"></a><br>
              Saldo: <a id="lbl_saldo"></a><br>
            </p>
            <hr/>
            <button type="button" class="btn btn-default" onclick="javascript:appBotonCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
          </div>
        </div>
      </form>
    </div>
    <div class="col-md-8">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title fontFlexoRegular">Datos Prestamo</h3>
        </div>
        <div class="box-body">
          <form class="form-horizontal" autocomplete="off">
            <div class="col-md-12">
              <div class="box-body">
                <div id="div_tipoCredito" class="form-group" style="margin-bottom:5px;">
                  <div class="col-sm-7" style="padding-left:0;padding-right:0;">
                    <div class="input-group">
                      <span class="input-group-addon"><b>Tipo</b></span>
                      <select id="cbo_TipoCred" class="form-control selectpicker"></select>
                    </div>
                  </div>
                  <div class="col-sm-5" style="padding-left:0;padding-right:0;">
                    <button id="btn_tipocredito" type="button" class="btn btn-primary" onclick="javascript:appBotonCambiarTipoCred();"><i class="fa fa-microphone"></i> Cambiar Tipo Credito</button>
                  </div>
                </div>
                <div id="div_Promotor" class="form-group" style="margin-bottom:5px;">
                  <div class="col-sm-7" style="padding-left:0;padding-right:0;">
                    <div class="input-group">
                      <span class="input-group-addon"><b>Promotor</b></span>
                      <select id="cbo_Promotor" class="form-control selectpicker"></select>
                    </div>
                  </div>
                  <div class="col-sm-5" style="padding-left:0;padding-right:0;">
                    <button id="btn_Promotor" type="button" class="btn btn-primary" onclick="javascript:appBotonCambiarPromotor();"><i class="fa fa-thumbs-up"></i> Cambiar Promotor</button>
                  </div>
                </div>
                <div id="div_Analista" class="form-group" style="margin-bottom:5px;">
                  <div class="col-sm-7" style="padding-left:0;padding-right:0;">
                    <div class="input-group">
                      <span class="input-group-addon"><b>Analista</b></span>
                      <select id="cbo_Analista" class="form-control selectpicker"></select>
                    </div>
                  </div>
                  <div class="col-sm-5" style="padding-left:0;padding-right:0;">
                    <button id="btn_Analista" type="button" class="btn btn-primary" onclick="javascript:appBotonCambiarAnalista();"><i class="fa fa-thumbs-up"></i> Cambiar Analista</button>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalChangeUser" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" style="background:#f9f9f9;padding:8px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title fontFlexoRegular" id="titleChangeUser">Cambiar...</h4>
        </div>
        <form class="form-horizontal" id="frmChangeUser" autocomplete="off">
          <div class="modal-body no-padding">
            <div class="box-body">
              <table class="table no-border">
                <tr>
                  <td style="width:20%"></td>
                  <td>
                    <div class="input-group">
                      <span class="input-group-addon" style="background:#f5f5f5;">Analista</span>
                      <select id="cboChangeUser" class="form-control selectpicker" style="height:30px;"></select>
                    </div>
                  </td>
                  <td style="width:20%"></td>
                </tr>
              </table>
            </div>
          </div>
          <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
            <button type="button" class="btn btn-primary" onclick="javascript:modChangeUser_Cambiar();"><i class="fa fa-flash"></i> Cambiar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<script src="pages/coopsud/adminprest/prestamos.js"></script>
<script>
  $(document).ready(function(){ appGridReset(); });
</script>
