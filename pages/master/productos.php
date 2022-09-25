<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-barcode"></i> Productos</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Productos</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <div class="row" id="grid">
    <div class="col-xs-12">
      <div class="box box-primary">
        <div class="box-header no-padding">
          <div class="mailbox-controls">
            <div class="btn-group">
              <?php if($_SESSION['usr_usernivelID']==701) {?>
                <button type="button" class="btn btn-default btn-sm" onclick="javascript:appProductosDelete();"><i class="fa fa-trash-o"></i></button>
                <button type="button" class="btn btn-default btn-sm" onclick="javascript:appProductosNuevo();"><i class="fa fa-plus"></i></button>
              <?php }?>
            </div>
            <button type="button" class="btn btn-default btn-sm" onclick="javascript:appProductosReset();"><i class="fa fa-refresh"></i></button>
            <div class="btn-group">
              <input type="text" id="txtBuscar" name="txtBuscar" class="form-control input-sm pull-right" placeholder="producto..." onkeypress="javascript:appProductosBuscar(event);">
              <span class="fa fa-search form-control-feedback"></span>
            </div>
            <span id="grdDatosCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
              <table class="table table-hover" id="grdDatos">
                <thead>
                  <tr>
                    <th style="width:30px;"><input type="checkbox" id="chk_All" onclick="toggleAll(this,'chk_Borrar');"/></th>
                    <th style="width:40px;">ID</th>
                    <th style="width:250px;">Producto</th>
                    <th style="width:90px;">Tasa Min.</th>
                    <th style="width:90px;">Tasa Max.</th>
                    <th style="width:90px;">Tasa Mora</th>
                    <th >Tipo</th>
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
                    <div id="pn_ID" class="form-group">
                      <div class="input-group">
                        <span class="input-group-addon"><b>ID</b></span>
                        <input id="txt_ID" name="txt_ID" type="text" class="form-control" disabled="disabled"/>
                      </div>
                    </div>
                  </div>
                </div>
                <div id="pn_Nombre" class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon"><b>Nombre</b></span>
                    <input id="txt_Nombre" name="txt_Nombre" type="text" maxlength="50" class="form-control" placeholder="nombre..."/>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-6">
                    <div id="div_tasaMin" class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon"><b>tasa Min.</b></span>
                        <input id="txt_tasaMin" name="txt_tasaMin" type="text" maxlength="10" class="form-control" placeholder="...%"/>
                      </div>
                    </div>
                  </div>
                  <div class="col-xs-6">
                    <div id="div_tasaMax" class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon"><b>tasa Max.</b></span>
                        <input id="txt_tasaMax" name="txt_tasaMax" type="text" maxlength="10" class="form-control" placeholder="...%"/>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-6">
                    <div id="div_tasaMora" class="form-group">
                      <div class="input-group">
                        <span class="input-group-addon"><b>tasa Mora</b></span>
                        <input id="txt_tasaMora" name="txt_tasaMora" type="text" maxlength="10" class="form-control" placeholder="...%"/>
                      </div>
                    </div>
                  </div>
                  <div class="col-xs-6">
                    <div id="div_segDesgr" class="form-group">
                      <div class="input-group">
                        <span class="input-group-addon"><b>Desgravamen</b></span>
                        <input id="txt_segDesgr" name="txt_segDesgr" type="text" maxlength="10" class="form-control" placeholder="...%"/>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-6">
                    <div class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon"><b>tipo producto</b></span>
                        <select id="cbo_tipoProd" name="cbo_tipoProd" class="form-control selectpicker"></select>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-6">
                    <div class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon"><b>tipo operacion</b></span>
                        <select id="cbo_tipoOper" name="cbo_tipoOper" class="form-control selectpicker"></select>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-6">
                    <div class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon"><b>tipo moneda</b></span>
                        <select id="cbo_tipoMone" name="cbo_tipoMone" class="form-control selectpicker"></select>
                      </div>
                    </div>
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
</section>

<script src="pages/master/productos.js"></script>
<script>
  $(document).ready(function(){
    appProductosGetAll();
  });
</script>
