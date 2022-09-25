<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- modal de Personas -->
<script src="pages/modals/Personas/mod.persona.js"></script>

<section class="content-header">
  <h1><i class="fa fa-tasks"></i> Captacion de Ahorros</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Ahorros</li>
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
              <?php if($_SESSION['usr_usernivelID']==701 || $_SESSION['usr_usernivelID']==703) {?>
              <button type="button" class="btn btn-default btn-sm" onclick="javascript:appBotonCaptado();"><i class="fa fa-bell-slash-o"></i></button>
              <?php }?>
              <button type="button" class="btn btn-default btn-sm" onclick="javascript:appBotonNuevo();"><i class="fa fa-plus"></i></button>
            </div>
            <div class="btn-group">
              <button type="button" class="btn btn-default btn-sm" onclick="javascript:appBotonReset();" title="Volver a cargar toda la lista de potenciales ahorristas"><i class="fa fa-refresh"></i></button>
            </div>
            <div class="btn-group">
              <select id="cboAgencias" class="btn btn-default btn-sm" style="height:30px;text-align:left;" onchange="javascript:appGridAll();"></select>
              <input type="hidden" id="agenciaAbrev" value="">
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
                    <th style="width:80px;">Fecha</th>
                    <th style="width:140px;">Agencia</th>
                    <th style="width:160px;">Asesor de Ahorros</th>
                    <th style="">Cliente</th>
                    <th style="">Producto</th>
                    <th style="width:100px;text-align:right;">Monto</th>
                    <th style="width:65px;text-align:right;">Plazo</th>
                    <th style="width:65px;text-align:center;">Tasa</th>
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
      <div class="col-md-3">
        <div class="box box-widget widget-user-2">
          <div class="widget-user-header bg-aqua-active">
            <div class="widget-user-image">
              <input type="hidden" id="hid_PersUrlFoto" value="">
              <img class="img-circle" src="" id="img_Foto" alt="Foto de Socio"/>
            </div>
            <div style="min-height:70px;">
              <h5 class="widget-user-username fontFlexoRegular" id="lbl_Apellidos"></h5>
              <h4 class="widget-user-desc fontFlexoRegular" id="lbl_Nombres"></h4>
            </div>
          </div>
          <div class="no-padding">
            <ul class="list-group list-group-unbordered">
              <li class="list-group-item" style="padding:5px 10px 5px 10px;"><b>ID</b><a class="pull-right" id="lbl_ID"></a></li>
              <li class="list-group-item" style="padding:5px 10px 5px 10px;"><b><span id="lbl_TipoDNI">DNI</span></b><a class="pull-right" id="lbl_DNI"></a></li>
              <li class="list-group-item" style="padding:5px 10px 5px 10px;"><b>Celular</b><a class="pull-right" id="lbl_celular"></a></li>
            </ul>
          </div>
          <div class="box-body">
            <button type="button" class="btn btn-default" onclick="javascript:appBotonCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
            <button id="btnInsert" type="button" class="btn btn-primary pull-right" style="display:none;" onclick="javascript:appBotonInsert();"><i class="fa fa-save"></i> Guardar</button>
            <button id="btnUpdate" type="button" class="btn btn-info pull-right" style="display:none;" onclick="javascript:appBotonUpdate();"><i class="fa fa-save"></i> Actualizar</button>
          </div>
        </div>
      </div>
      <div class="col-md-9">
        <div class="nav-tabs-custom">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#datosPersonal" data-toggle="tab"><i class="fa fa-male"></i> Personal</a></li>
            <li><a href="#datosPreahorro" data-toggle="tab"><i class="fa fa-briefcase"></i> Preventa</a></li>
            <li><a href="#datosSimulacion" data-toggle="tab"><i class="fa fa-table"></i> Simulacion</a></li>
          </ul>
          <div class="tab-content">
            <div id="datosPersonal" class="tab-pane active">
              <div class="box-body">
                <div class="col-md-5">
                  <div class="box-body">
                    <strong><i class="fa fa-user margin-r-5"></i> Basicos</strong>
                    <p class="text-muted">
                      <span id="lbl_PersTipoNombres">Nombres</span>: <a id="lbl_PersNombres"></a><br>
                      <span id="lbl_PersTipoApellidos">Apellidos: <a id="lbl_PersApellidos"></a><br></span><br>
                      <span id="lbl_PersTipoDNI"></span>: <a id="lbl_PersNroDNI"></a><br>
                      Fecha Nac: <a id="lbl_PersFechaNac"></a><br>
                      Lugar Nac: <a id="lbl_PersLugarNac"></a><br>
                      <span id="lbl_PersTipoSexo">Sexo: <a id="lbl_PersSexo"></a><br></span>
                      <span id="lbl_PersTipoECivil">Estado Civil: <a id="lbl_PersEcivil"></a></span>
                    </p>
                    <hr>

                    <strong><i class="fa fa-phone margin-r-5"></i> Contacto</strong>
                    <p class="text-muted">
                      Celular: <a id="lbl_PersCelular"></a><br>
                      Telefono Fijo: <a id="lbl_PersTelefijo"></a><br>
                      Correo: <a id="lbl_PersEmail"></a><br>
                    </p>
                    <hr>

                    <strong><i class="fa fa-graduation-cap margin-r-5"></i> Profesionales</strong>
                    <p class="text-muted">
                      <span id="lbl_PersTipoGIntruc">Grado Instruccion: <a id="lbl_PersGInstruccion"></a><br></span>
                      <span id="lbl_PersTipoProfesion">Profesion</span>: <a id="lbl_PersProfesion"></a><br>
                      Ocupacion: <a id="lbl_PersOcupacion"></a>
                    </p>
                  </div>
                </div>
                <div class="col-md-7">
                  <div class="box-body">
                    <strong><i class="fa fa-map-marker margin-r-5"></i> Ubicacion</strong>
                    <p class="text-muted">
                      <a id="lbl_PersUbicacion"></a><br>
                      Direccion: <a id="lbl_PersDireccion"></a><br>
                      Referencia: <a id="lbl_PersReferencia"></a><br>
                      Medidor de Luz: <a id="lbl_PersMedidorluz"></a><br>
                      Tipo de Vivienda: <a id="lbl_PersTipovivienda"></a>
                    </p>
                    <hr>

                    <strong><i class="fa fa-file-text-o margin-r-5"></i> Observaciones</strong>
                    <p class="text-muted">
                      <span id="lbl_PersObservac"></span>
                    </p><br><br>
                    <strong><i class="fa fa-eye margin-r-5"></i> Auditoria</strong>
                    <div style="font-style:italic;font-size:11px;color:gray;">
                      Fecha: <span id="lbl_PersSysFecha"></span><br>
                      Modif. por: <span id="lbl_PersSysUser"></span><br>
                    </div><br>
                    <button id="btn_PersUpdate" type="button" class="btn btn-primary btn-xs" onclick="javascript:appPersonaEditar();"><i class="fa fa-flash"></i> Modificar Datos</button>
                    <button id="btn_PersPermiso" type="button" class="btn btn-success btn-xs" style="display:none;" onclick="javascript:appPermisoPersonas();"><i class="fa fa-shield"></i> Solicitar Permiso</button>
                  </div>
                </div>
              </div>
            </div>
            <div id="datosPreahorro" class="tab-pane">
              <div class="box-body">
                <div class="col-md-5">
                  <div class="box-body">
                    <input type="hidden" id="hid_PrevID" value="">
                    <div id="div_PrevProducto" class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:90px;">Producto</span>
                        <select id="cbo_Productos" name="cbo_Productos" class="form-control selectpicker" onchange="javascript:appResetSimulacion();"></select>
                      </div>
                    </div>
                    <div id="div_PrevPlazo" class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:90px;">Plazo</span>
                        <input id="txt_PrevTiempoMeses" name="txt_PrevTiempoMeses" type="number" class="form-control" value="3" placeholder="..." style="width:100px;"/>
                        <span class="input-group-addon" style="width:30px;border:0">meses</span>
                      </div>
                    </div>
                    <div id="div_PrevMonto" class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:90px;">Monto</span>
                        <input id="txt_PrevMonto" name="txt_PrevMonto" type="text" class="form-control" placeholder="monto..." style="width:100px;"/>
                      </div>
                    </div>
                    <div id="div_PrevTasa" class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:90px;">Tasa</span>
                        <input id="txt_PrevTasa" name="txt_PrevTasa" type="text" class="form-control" placeholder="tasa..." style="width:70px;"/>
                        <span class="input-group-addon" style="width:10px;border:0">%</span>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:90px;">Fecha Ing.</span>
                        <input id="date_PrevFecha" name="date_Fecha" type="text" class="form-control" style="width:105px;" />
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-7">
                  <strong><i class="fa fa-file-text-o margin-r-5"></i> Negociacion de Ejecutiva de Ahorros</strong>
                  <div class="form-group">
                    <div class="input-group">
                      <textarea id="txt_PrevObserv" name="txt_PrevObserv" type="text" placeholder="Observaciones de la preventa..." cols="80" rows="7" style="width:100%;"></textarea>
                    </div>
                  </div>
                  <strong><i class="fa fa-eye margin-r-5"></i> Auditoria</strong>
                  <div style="font-style:italic;font-size:11px;color:gray;">
                    Fecha: <span id="lbl_SysFecha"></span><br>
                    Modif. por: <span id="lbl_SysUser"></span>
                  </div>
                </div>
              </div>
            </div>
            <div id="datosSimulacion" class="tab-pane">
              <div class="box-body">
                <input type="hidden" id="hid_interes" value="0"/>
                <button id="btn_Simular" type="button" class="btn btn-primary btn-xs" onclick="javascript:appBotonSimulacion();"><i class="fa fa-flash"></i> mostrar Simulacion</button>
                <button id="btn_Print" type="button" class="btn btn-success btn-xs" style="display:none;" onclick="javascript:appBotonPrintSimulacion();"><i class="fa fa-file-pdf-o"></i> Imprimir</button>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <span id="lbl_SimuProducto" style="color:blue;"></span>
                  <div class="box-body table-responsive no-padding">
                    <table class="table table-hover" id="grdSimulacion">
                      <thead>
                        <tr>
                          <th style="width:30px;">Nro</th>
                          <th style="width:90px;">Fecha</th>
                          <th style="width:100px;text-align:right;">Total</th>
                          <th style="width:100px;text-align:right;">Capital</th>
                          <th style="width:100px;text-align:right;">Interes</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody id="grdSimulacionBody">
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="table-responsive no-padding" id="contenedorFrame"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
  <div class="modal fade" id="modalPers" role="dialog"></div>
</section>

<script src="pages/oper/captacion/ahorros.js"></script>
<script>
  $(document).ready(function(){
    Persona.addModalToParentForm('modalPers');
    appBotonReset();
  });
</script>
