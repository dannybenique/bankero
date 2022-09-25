<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-gears"></i> Vouchers</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Vouchers</li>
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
              <button type="button" class="btn btn-default btn-sm" onclick="javascript:appVouchersReset();"><i class="fa fa-refresh"></i></button>
            </div>
            <div class="btn-group">
              <select id="cboTipoOperaciones" class="btn btn-default btn-sm" style="height:30px;" onchange="javascript:appVouchersGetAll();"></select>
            </div>
            <div class="btn-group">
              <input type="text" id="txtBuscar" name="txtBuscar" class="form-control input-sm pull-right" placeholder="Nº VOUCHER..." onkeypress="javascript:appVouchersBuscar(event);" style="text-transform:uppercase;">
              <span class="fa fa-search form-control-feedback"></span>
            </div>
            <span id="grdDatosCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
            <table class="table table-hover" id="grdDatos">
              <thead>
                <tr>
                  <th style="width:100px;text-align:right;">Nº Voucher</th>
                  <th style="width:100px;text-align:right;">Fecha</th>
                  <th style="width:130px;">Tipo</th>
                  <th style="width:180px;">Responsable</th>
                  <th style="">Socio</th>
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
        <input id="hid_ID" type="hidden" value=""/>
        <input id="hid_id_tipo_oper" type="hidden" value=""/>
        <div class="box box-primary">
          <div class="box-body">
              <div class="box-body">
                <div class="row">
                  <div class="col-xs-6">
                    <div class="form-group">
                      <div class="input-group">
                        <span class="input-group-addon"><b>ID</b></span>
                        <input id="txt_numtrans" name="txt_numtrans" type="text" class="form-control" disabled="disabled" style="width:105px;"/>
                      </div>
                    </div>
                  </div>
                  <div class="col-xs-6">
                    <div class="pull-right">
                      <div class="form-group">
                        <div class="input-group">
                          <span class="input-group-addon"><b>Fecha</b></span>
                          <input id="txt_fecha" name="txt_fecha" type="text" class="form-control" disabled="disabled" style="width:100px;"/>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon"><b>Respons.</b></span>
                    <input id="txt_responsable" name="txt_responsable" type="text" class="form-control" disabled="disabled"/>
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon"><b>Socio</b></span>
                    <input id="txt_socio" name="txt_socio" type="text" class="form-control" disabled="disabled"/>
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon"><b>Operacion</b></span>
                    <input id="txt_operacion" name="txt_operacion" type="text" class="form-control" disabled="disabled"/>
                  </div>
                </div>
                <div class="box-body table-responsive no-padding">
                  <table class="table table-hover" id="grdVouchers">
                    <thead>
                      <tr>
                        <th style="width:30px;">Nº</th>
                        <th style="">Detalle</th>
                        <th style="width:80px;text-align:right;">Importe</th>
                      </tr>
                    </thead>
                    <tbody id="grdVouchersBody">
                    </tbody>
                    <tfoot>
                      <tr>
                        <td colspan="2" style="text-align:right;font-size:14px;"><b>TOTAL</b></td>
                        <td style="text-align:right;"><span id="lbl_total"></span></td>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
          </div>

          <div class="mailbox-controls">
            <button type="button" class="btn btn-default" onclick="javascript:appVoucherCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
            <?php
              if($_SESSION['usr_usernivelID']==701){ //solo superadmin
            ?>
            <button id="btnPrint" type="button" class="btn btn-primary" onclick="javascript:appVoucherPrint(<?php echo ("'https://".$webconfig->getURL()."'");?>);"><i class="fa fa-file-pdf-o"></i> imprimir</button>
            <button id="btnDelete" type="button" class="btn btn-danger" onclick="javascript:appVoucherDelete();"><i class="fa fa-close"></i> Eliminar</button>
            <?php } ?>
          </div>

        </div>
      </form>
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
</section>

<script src="pages/caja/vouchers/vouchers.js"></script>
<script>
  $(document).ready(function(){
    appVouchersReset();
  });
</script>
