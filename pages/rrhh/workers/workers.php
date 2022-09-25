<!-- bootstrap datepicker -->
<link href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" />
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- modal de Personas -->
<script src="pages/modals/Personas/mod.persona.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-user"></i> Colaboradores</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Colaboradores</li>
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
              <button type="button" class="btn btn-default btn-sm" onclick="javascript:rrhhWorkerReset();"><i class="fa fa-refresh"></i></button>
            </div>
            <div class="btn-group">
              <select id="cboAgencias" class="btn btn-default btn-sm" style="height:30px;" onchange="javascript:rrhhWorkerGetAll();"></select>
              <input type="hidden" id="agenciaAbrev" value="">
            </div>
            <div class="btn-group">
              <input type="text" id="txtBuscar" class="form-control input-sm pull-right" placeholder="buscar empleado..." onkeypress="javascript:rrhhWorkerBuscar(event);">
              <span class="fa fa-search form-control-feedback"></span>
            </div>
            <span id="grdDatosCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
              <table class="table table-hover" id="grdDatos">
                <thead>
                  <tr>
                    <th style="width:60px;">Codigo</th>
                    <th style="width:30px;"><i class="fa fa-paperclip"></i></th>
                    <th style="width:110px;">DNI</th>
                    <th>Empleado</th>
                    <th style="width:200px;">Cargo</th>
                    <th style="width:150px;">Agencia</th>
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
    <form class="form-horizontal" autocomplete="off">
    <div class="col-md-3">
      <div class="box box-widget widget-user-2">
        <div class="widget-user-header" style="background:#f9f9f9;">
          <div class="widget-user-image">
            <input type="hidden" id="hid_PersUrlFoto" value="">
            <img class="profile-user-img img-circle" src="" id="img_Foto" alt="persona"/>
          </div>
          <div style="min-height:70px;">
            <h5 class="widget-user-username fontFlexoRegular" id="lbl_NombreCorto"></h5>
            <h4 class="widget-user-desc fontFlexoRegular" id="lbl_Cargo"></h4>
          </div>
        </div>
        <div class="box-footer no-padding">
          <ul class="nav nav-stacked">
            <li style="padding:10px"><b>ID</b> <span class="pull-right badge" id="lbl_ID" style="font:normal 13px flexoregular;color:#0091C0;background:#f1f1f1;"></span></li>
            <li style="padding:10px"><b>Agencia</b> <span class="pull-right badge" id="lbl_Agencia" style="font:normal 13px flexoregular;color:#0091C0;background:#f1f1f1;"></span></li>
            <li style="padding:10px"><b>DNI</b> <span class="pull-right badge" id="lbl_DNI" style="font:normal 13px flexoregular;color:#0091C0;background:#f1f1f1;"></span></li>
            <li style="padding:10px"><b>Celular</b><span class="pull-right badge" id="lbl_Celular" style="font:normal 13px flexoregular;color:#0091C0;background:#f1f1f1;"></span></li>
          </ul>
        </div>
        <div class="box-body">
          <button type="button" class="btn btn-default" onclick="javascript:rrhhWorkerCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
          <?php if($_SESSION['usr_usernivelID']==706){ /*solo RRHH ve esta parte*/ ?>
            <button id="btnUpdate" type="button" class="btn btn-info pull-right" onclick="javascript:rrhhWorkerUpdate();"><i class="fa fa-save"></i> Actualizar</button>
          <?php }?>
        </div>
      </div>
    </div>
    <div class="col-md-9">
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
          <li class="active"><a href="#datosPersonal" data-toggle="tab"><i class="fa fa-male"></i> Datos Personales</a></li>
          <li class=""><a href="#datosWorker" data-toggle="tab"><i class="fa fa-briefcase"></i> Datos Empleado</a></li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane active" id="datosPersonal">
            <div class="box-body" id="pn_PersShow">
              <div class="col-md-6">
                <div class="box-body">
                  <strong><i class="fa fa-user margin-r-5"></i> Basicos</strong>
                  <p class="text-muted">
                    <input type="hidden" id="hid_PersID" value="">
                    Nombres: <a id="lbl_PersNombres"></a><br>
                    Apellidos: <a id="lbl_PersApellidos"></a><br><br>
                    <span id="lbl_PersTipoDNI"></span>: <a id="lbl_PersNroDNI"></a><br>
                    Fecha Nac: <a id="lbl_PersFechaNac"></a><br>
                    Lugar Nac: <a id="lbl_PersLugarnac"></a><br>
                    Sexo: <a id="lbl_PersSexo"></a><br>
                    Estado Civil: <a id="lbl_PersEcivil"></a>
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
                    Grado Instruccion: <a id="lbl_PersGInstruccion"></a><br>
                    Profesion: <a id="lbl_PersProfesion"></a><br>
                    Ocupacion: <a id="lbl_PersOcupacion"></a>
                  </p>
                </div>
              </div>
              <div class="col-md-6">
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
                    Modif. por: <span id="lbl_PersSysUser"></span>
                  </div><br>
                  <button id="btn_PersUpdate" type="button" class="btn btn-primary btn-xs" onclick="javascript:rrhhPersonaBotonEditar();"><i class="fa fa-flash"></i> Modificar Datos</button>
                  <button id="btn_PersPermiso" type="button" class="btn btn-success btn-xs" style="display:none;" onclick="javascript:appPermisoPersonas();"><i class="fa fa-shield"></i> Solicitar Permiso</button>
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane" id="datosWorker">
            <div class="box-body">
              <div class="col-md-6">
                <div class="box-body">
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon" title="Nombre Corto"><i class="fa fa-user"></i></span>
                      <input id="txt_WorkNombreCorto" name="txt_WorkNombreCorto" type="text" class="form-control" placeholder="Nombre Corto..." disabled/>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon" title="Codigo"><i class="fa fa-code"></i></span>
                      <input id="txt_WorkCodigo" name="txt_WorkCodigo" type="text" class="form-control" placeholder="Codigo..." disabled/>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon" title="Agencia"><i class="fa fa-users"></i></span>
                      <select class="form-control" id="cbo_WorkAgencia" name="cbo_WorkAgencia" class="selectpicker"></select>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon" title="Cargo"><i class="fa fa-star"></i></span>
                      <select class="form-control" id="cbo_WorkCargo" name="cbo_WorkCargo" class="selectpicker"></select>
                    </div>
                  </div>
                  <?php if($_SESSION['usr_usernivelID']==706){ /*solo RRHH ve esta parte*/ ?>
                    <div class="form-group">
                      <div class="input-group">
                        <span class="input-group-addon" title="Fecha de Ingreso"><i class="fa fa-calendar"></i></span>
                        <input id="date_WorkIngreso" name="date_WorkIngreso" type="text" class="form-control" style="width:105px;" disabled/>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="input-group">
                        <span class="input-group-addon" title="Fecha de Renovacion"><i class="fa fa-calendar"></i></span>
                        <input id="date_WorkRenov" name="date_WorkRenov" type="text" class="form-control" style="width:105px;"/>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="input-group">
                        <span class="input-group-addon" title="Fecha de Vacaciones"><i class="fa fa-calendar"></i></span>
                        <input id="date_WorkVacac" name="date_WorkVacac" type="text" class="form-control" style="width:105px;"/>
                      </div>
                    </div>
                  <?php }?>
                </div>
              </div>
              <div class="col-md-6">
                <div class="box-body">
                  <?php if($_SESSION['usr_usernivelID']==706){ /*solo RRHH ve esta parte*/ ?>
                    <div class="form-group">
                      <div class="input-group">
                        <span class="input-group-addon pull-left" style="border-left:1px solid #D2D6DD;border-right:1px solid #D2D6DD;width:160px;">
                          <input type="checkbox" id="chk_WorkAsignaFam" name="chk_WorkAsignaFam">Asignacion Familiar
                        </span>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="input-group">
                        <textarea id="txt_WorkObserv" name="txt_WorkObserv" type="text" placeholder="Observaciones de empleado..." cols="80" rows="10" style="width:100%;"></textarea>
                      </div>
                    </div>
                  <?php }?>
                  <strong><i class="fa fa-eye margin-r-5"></i> Auditoria</strong>
                  <div style="font-style:italic;font-size:11px;color:gray;">
                    Fecha: <span id="lbl_WorkSysFecha"></span><br>
                    Modif. por: <span id="lbl_WorkSysUser"></span>
                  </div>
                </div>
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

<script src="pages/rrhh/workers/workers.js"></script>
<script>
  $(document).ready(function(){
    Persona.addModalToParentForm('modalPers');
    rrhhWorkerReset();
  });
</script>
