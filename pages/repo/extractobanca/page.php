<?php if(isset($menu->repo->submenu->extractobanca)){?>
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-gears"></i> <b>Extracto Bancario</b></h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">extrac.banca</li>
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
              <button type="button" id="btn_DEL" class="btn btn-default btn-sm" onclick="javascript:appMovimBorrar();"><i class="fa fa-trash-o"></i></button>
              <button type="button" id="btn_NEW" class="btn btn-default btn-sm" onclick="javascript:appMovimNuevo();"><i class="fa fa-plus"></i></button>
            </div>
            <button type="button" id="btn_RST" class="btn btn-default btn-sm" onclick="javascript:appMovimReset();"><i class="fa fa-refresh"></i></button>
            <div class="btn-group">
              <input type="text" id="txtBuscar" class="form-control input-sm pull-right" placeholder="voucher..." onkeypress="javascript:appMovimBuscar(event);" style="text-transform:uppercase;" autocomplete="off">
              <span class="fa fa-search form-control-feedback"></span>
            </div>
            <span id="grdCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
            <table class="table table-hover" id="grd">
              <thead>
                <tr>
                  <th style="width:120px;">Codigo</th>
                  <th style="width:90px;">Fecha</th>
                  <th style="width:110px;" title="Documento Unico de Identidad = DNI, RUC">DUI</th>
                  <th style="">Socio</th>
                  <th style="">Direccion</th>
                </tr>
              </thead>
              <tbody id="grdDatos">
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
          <h3 class="box-title" style="font-family:flexoregular;"><b>Cabecera</b></h3>
        </div>
        <div class="box-body">
          <div class="box-body">
            <input type="hidden" id="hid_movimID" value="">
            
            <span>Agencia:</span> <a id="lbl_pagoAgencia"></a><br/>
            <span title="Tipo de operacion">Tipo:</span> <a id="lbl_pagoTipoOper"></a><br/>
            <span>Codigo:</span> <a id="lbl_pagoCodigo"></a><br/>
            <span>Fecha:</span> <a id="lbl_pagoFecha"></a><br/>
            <span>Socio:</span> <a id="lbl_pagoSocio"></a><br/>
            <span id="lbl_tipodui"></span> <a id="lbl_pagoNroDUI"></a><br/><br/>
            <span>Cajera:</span> <a id="lbl_pagoCajera"></a><br/>
            <span>Importe:</span> <a id="lbl_pagoImporte"></a><br/>
          </div>
          <div class="btn-group pull-left">
            <button id="btnCancel" type="button" class="btn btn-default" onclick="javascript:appMovimCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
          </div>
          <div class="btn-group pull-right">
            <button type="button" class="btn btn-info" id="btn_print" onclick="javascript:appMovimRefresh();"><i class="fa fa-refresh"></i></button>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <div class="box box-primary">
        <div class="box-body box-profile">
          <div class="box-body table-responsive no-padding">
              <table class="table table-hover" id="grdDetalle" style="font-family:helveticaneue_light;">
                <thead>
                  <tr>
                    <th style="width:30px;">Item</th>
                    <th style="">Tipo de Movimiento</th>
                    <th style="">Producto</th>
                    <th style="width:100px;text-align:right;">Importe</th>
                  </tr>
                </thead>
                <tbody id="grdDetalleDatos">
                </tbody>
              </table>
            </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="pages/repo/extractobanca/script.js"></script>
<script>
  $(document).ready(function(){
    appMovimReset();
  });
</script>
<?php } ?>