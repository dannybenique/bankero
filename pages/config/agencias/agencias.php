<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-gears"></i> Agencias</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Agencias</li>
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
                <button type="button" class="btn btn-default btn-sm" onclick="javascript:appAgenciaDelete();"><i class="fa fa-trash-o"></i></button>
                <button type="button" class="btn btn-default btn-sm" onclick="javascript:appAgenciaNuevo();"><i class="fa fa-plus"></i></button>
              <?php }?>
            </div>
            <button type="button" class="btn btn-default btn-sm" onclick="javascript:appAgenciasReset();"><i class="fa fa-refresh"></i></button>
            <div class="btn-group">
              <input type="text" id="txtBuscar" name="txtBuscar" class="form-control input-sm pull-right" placeholder="nombre..." onkeypress="javascript:appAgenciasBuscar(event);">
              <span class="fa fa-search form-control-feedback"></span>
            </div>
            <span id="grdDatosCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
              <table class="table table-hover" id="grdDatos">
                <thead>
                  <tr>
                    <th style="width:30px;"><input type="checkbox" id="chk_All" onclick="SelectAll(this,'chk_Borrar','grdDatos');"/></th>
                    <th style="width:60px;">Codigo</th>
                    <th style="width:150px;">Nombre</th>
                    <th style="width:150px;">Telefonos</th>
                    <th style="width:150px;">Ciudad</th>
                    <th>Direccion</th>
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
                <input type="hidden" id="hid_agenciaID" value="">
                <div class="row">
                  <div class="col-xs-6">
                    <div id="pn_Codigo" class="form-group">
                      <div class="input-group">
                        <span class="input-group-addon"><b>Codigo</b></span>
                        <input id="txt_Codigo" name="txt_Codigo" type="text" maxlength="4" class="form-control" placeholder="codigo..."/>
                      </div>
                    </div>
                  </div>
                  <div class="col-xs-6">
                    <div id="pn_Abrev" class="form-group">
                      <div class="input-group">
                        <span class="input-group-addon"><b>Abrev.</b></span>
                        <input id="txt_Abrev" name="txt_Abrev" type="text" maxlength="5" class="form-control" placeholder="abrev..."/>
                      </div>
                    </div>
                  </div>
                </div>
                <div id="pn_Nombre" class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon"><b>Nombre</b></span>
                    <input id="txt_Nombre" name="txt_Nombre" type="text" maxlength="50" class="form-control" placeholder="nombre..."/>
                  </div>
                </div>
                <div id="pn_Ciudad" class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon"><b>Ciudad</b></span>
                    <input id="txt_Ciudad" name="txt_Ciudad" type="text" maxlength="50" class="form-control" placeholder="ciudad..."/>
                  </div>
                </div>
                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon"><b>Direccion</b></span>
                    <input id="txt_Direccion" name="txt_Direccion" type="text" maxlength="150" class="form-control" placeholder="direccion..."/>
                  </div>
                </div>
                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon"><b>Telefonos</b></span>
                    <input id="txt_Telefonos" name="txt_Telefonos" type="text" maxlength="30" class="form-control" placeholder="telefonos..."/>
                  </div>
                </div>
                <div class="form-group">
                  <div class="input-group">
                    <textarea id="txt_Observac" name="txt_Observac" type="text" placeholder="Observaciones..." cols="100" rows="6" style="width:100%;"></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="box-body">
            <button type="button" class="btn btn-default" onclick="javascript:appAgenciaCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
            <?php
              if($_SESSION['usr_usernivelID']==701){ //solo superadmin
            ?>
            <button id="btnInsert" type="button" class="btn btn-primary pull-right" onclick="javascript:appAgenciaInsert();"><i class="fa fa-save"></i> Guardar</button>
            <button id="btnUpdate" type="button" class="btn btn-info pull-right" onclick="javascript:appAgenciaUpdate();"><i class="fa fa-save"></i> Actualizar</button>
            <?php } ?>
          </div>
        </div>
      </form>
    </div>
  </div>
</section>

<script src="pages/config/agencias/agencias.js"></script>
<script>
  $(document).ready(function(){
    appAgenciasGetAll();
  });
</script>
