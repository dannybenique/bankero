<!-- treeview -->
<link rel="stylesheet" href="libs/ztree/css/ztreestyle.css" />
<script src="libs/ztree/js/jquery.ztree.core.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-share-alt"></i> Cuentas Contables</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Ctas Contables</li>
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
              <button type="button" id="treeviewBtnADD" class="btn btn-default btn-sm" onclick="return false;"><i class="fa fa-plus"></i></button>
              <button type="button" id="treeviewBtnEDT" class="btn btn-default btn-sm" onclick="return false;"><i class="fa fa-pencil"></i></button>
            </div>
            <button type="button" id="treeviewBtnRLD" class="btn btn-default btn-sm" onclick="javascript:appCuentasResetNodo();"><i class="fa fa-refresh"></i></button>
            <span id="grdDatosCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
            <div class="box-body">
              <ul id="appTreeView" class="ztree"></ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalCuentas" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form class="form-horizontal" id="frm_modalCuentas" autocomplete="off">
          <div class="modal-header" style="background:#f9f9f9;padding:8px;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title fontFlexoRegular"><b>CUENTAS</b></h4>
          </div>
          <div class="modal-body" id="divCuentas">
            <div class="box-body">
              <div class="form-group" style="margin-bottom:5px;">
                <div class="input-group">
                  <span class="input-group-addon" style="background:#f5f5f5;"><b>ID</b></span>
                  <input id="txt_ID" name="txt_ID" type="text" class="form-control" style="width:105px;" disabled="disabled"/>
                </div>
              </div>
              <div id="div_Codigo" class="form-group" style="margin-bottom:5px;">
                <div class="input-group">
                  <span class="input-group-addon" style="background:#f5f5f5;"><b>Codigo</b></span>
                  <input id="txt_Codigo" name="txt_Codigo" type="text" class="form-control" maxlength="10" style="width:205px;"/>
                </div>
              </div>
              <div id="div_Nombre" class="form-group" style="margin-bottom:5px;">
                <div class="input-group">
                  <span class="input-group-addon" style="background:#f5f5f5;"><b>Nombre</b></span>
                  <input id="txt_Nombre" name="txt_Nombre" type="text" class="form-control"/>
                </div>
              </div>
              <div class="form-group" style="margin-bottom:5px;">
                <div class="input-group">
                  <span class="input-group-addon" style="background:#f5f5f5;"><b>Agregado a...</b></span>
                  <input id="txt_Parent" name="txt_Parent" type="text" class="form-control" disabled="disabled"/>
                  <input id="hid_ParentID" type="hidden" value=""/>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
            <button type="button" id="btn_modalCerrar" class="btn btn-default pull-left btn-sm" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
            <button type="button" id="btn_modalCuentasInsert" class="btn btn-primary btn-sm" onclick="javascript:modalCuentasInsert();"><i class="fa fa-save"></i> Guardar</button>
            <button type="button" id="btn_modalCuentasUpdate" class="btn btn-info btn-sm" onclick="javascript:modalCuentasUpdate();"><i class="fa fa-save"></i> Actualizar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<script src="pages/conta/cuentas/cuentas.js"></script>
<script>
  $(document).ready(function(){
    appCuentasRootGetAll();
    $("#treeviewBtnADD").bind("click", {type:"add", silent:false}, refreshNode);
    $("#treeviewBtnEDT").bind("click", {type:"edt", silent:false}, refreshNode);
  });
</script>
