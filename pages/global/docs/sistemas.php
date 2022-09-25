<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<section class="content-header">
  <h1><i class="fa fa-leanpub"></i> Docs Tecnologias de la informacion y Comunic</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">sistemas</li>
  </ol>
</section>
<section class="content">
  <div class="row" id="grid">
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header no-padding">
          <div class="mailbox-controls">
            <span id="grdDatosCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
              <table class="table table-hover" id="grdDatos">
                <thead>
                  <tr>
                    <th style="width:30px;"><input type="checkbox" onclick="SelectAll(this,'chk_Borrar','grdDatos');"/></th>
                    <th style="width:80px;">Codigo</th>
                    <th style="">Documento</th>
                    <th style="width:80px;">Fecha</th>
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
    <form class="form-horizontal" id="frmWorker" autocomplete="off">
    <div class="col-md-3">
      <div class="box box-widget widget-user-2">
        <div class="widget-user-header" style="background:#f9f9f9;">
          <div class="widget-user-image">
            <input type="hidden" id="hid_PersUrlFoto" value="">
            <img class="profile-user-img img-circle" src="" id="img_Foto" alt="persona"/>
          </div>
          <div style="min-height:70px;">
            <h5 class="widget-user-username fontFlexoRegular" id="lbl_NombreCorto"></h5>
            <h4 class="widget-user-desc fontFlexoRegular" id="lbl_cargo"></h4>
          </div>
        </div>
        <div class="no-padding">
          <ul class="list-group list-group-unbordered">
            <li class="list-group-item" style="padding:5px 10px 5px 10px;"><b>ID</b><a class="pull-right" id="lbl_ID"></a></li>
            <li class="list-group-item" style="padding:5px 10px 5px 10px;"><b>Agencia</b><a class="pull-right" id="lbl_agencia"></a></li>
            <li class="list-group-item" style="padding:5px 10px 5px 10px;"><b><span id="lbl_TipoDNI">DNI</span></b><a class="pull-right" id="lbl_DNI"></a></li>
            <li class="list-group-item" style="padding:5px 10px 5px 10px;"><b>Celular</b><a class="pull-right" id="lbl_celular"></a></li>
          </ul>
        </div>
        <div id="pn_EditChecks" class="box-body" style="display:none;">
          <div class="row">
              <div class="col-xs-6" title="Editar Datos de Empleado">
                <center><i class="fa fa-briefcase"></i> <input type="checkbox" id="chk_EditWorker"></center>
              </div>
              <div class="col-xs-6" title="Editar Datos de Usuario">
                <center><i class="fa fa-user"></i> <input type="checkbox" id="chk_EditUsuario"></center>
              </div>
          </div>
        </div>
        <div class="box-body">
          <button type="button" class="btn btn-default" onclick="javascript:appWorkerBotonCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
          <button id="btnInsert" type="button" class="btn btn-primary pull-right" onclick="javascript:appWorkerBotonInsert();"><i class="fa fa-save"></i> Guardar</button>
          <button id="btnUpdate" type="button" class="btn btn-info pull-right" onclick="javascript:appWorkerBotonUpdate();"><i class="fa fa-save"></i> Actualizar</button>
        </div>
      </div>
    </div>
    <div class="col-md-9">
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
          <li class="active"><a href="#datosPersonal" data-toggle="tab"><i class="fa fa-male"></i> Personal</a></li>
          <li class=""><a href="#datosWorker" data-toggle="tab"><i class="fa fa-briefcase"></i> Empleado</a></li>
          <li class=""><a href="#datosUsuario" data-toggle="tab"><i class="fa fa-user"></i> Usuario</a></li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane active" id="datosPersonal">
            <div class="box-body">
              <div class="col-md-5">
                <div class="box-body">
                  <strong><i class="fa fa-user margin-r-5"></i> Basicos</strong>
                  <p class="text-muted">
                    <input type="hidden" id="hid_PersID" value="">
                    Nombres: <a id="lbl_PersNombres"></a><br>
                    Apellidos: <a id="lbl_PersApellidos"></a><br><br>
                    <span id="lbl_PersTipoDNI"></span>: <a id="lbl_PersNroDNI"></a><br>
                    Fecha Nac: <a id="lbl_PersFechaNac"></a><br>
                    Lugar Nac: <a id="lbl_PersLugarNac"></a><br>
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
                    Modif. por: <span id="lbl_PersSysUser"></span>
                  </div><br>
                  <button id="btn_PersUpdate" type="button" class="btn btn-primary btn-xs" onclick="javascript:appPersonaBotonEditar();"><i class="fa fa-flash"></i> Modificar Datos</button>
                  <button id="btn_PersPermiso" type="button" class="btn btn-success btn-xs" style="display:none;" onclick="javascript:appPermisoPersonas();"><i class="fa fa-shield"></i> Solicitar Permiso</button>
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane" id="datosWorker">
            <input type="hidden" id="date_WorkRenov" value="">
            <input type="hidden" id="date_WorkVacac" value="">
            <input type="hidden" id="chk_WorkAsignaFam" value="">
            <input type="hidden" id="chk_WorkEstado" value="">
            <div class="box-body">
              <div class="col-md-6">
                <div class="box-body">
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon" title="Nombre Corto"><i class="fa fa-user" style="width:15px;"></i></span>
                      <input id="txt_WorkNombreCorto" name="txt_WorkNombreCorto" type="text" class="form-control" placeholder="Nombre Corto..." onblur="javascript:appSetTexto('#lbl_NombreCorto','#txt_WorkNombreCorto',false);"/>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon" title="Correo institucional"><i class="fa fa-envelope" style="width:15px;"></i></span>
                      <input id="txt_WorkCorreo" name="txt_WorkCorreo" type="text" class="form-control" placeholder="correo..."/>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon" title="Codigo"><i class="fa fa-code"></i></span>
                      <input id="txt_WorkCodigo" name="txt_WorkCodigo" type="text" class="form-control" placeholder="Codigo..."/>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon" title="Agencia"><i class="fa fa-users"></i></span>
                      <select id="cbo_WorkAgencia" name="cbo_WorkAgencia" class="form-control selectpicker" onchange="appSetTexto('#lbl_agencia','#cbo_WorkAgencia',true);"></select>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon" title="Cargo"><i class="fa fa-star"></i></span>
                      <select id="cbo_WorkCargo" name="cbo_WorkCargo" class="form-control selectpicker" onchange="appSetTexto('#lbl_cargo','#cbo_WorkCargo',true);"></select>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon" title="Fecha de Ingreso"><i class="fa fa-calendar"></i></span>
                      <input id="date_WorkIngreso" name="date_WorkIngreso" type="text" class="form-control" style="width:105px;" onblur="appSetTexto('#date_WorkRenov','#date_WorkIngreso',false);"/>
                    </div>
                  </div>
                  <div class="form-group" id="chk_estado">
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="box-body">
                  <div class="form-group">
                    <div class="input-group">
                      <textarea id="txt_WorkObserv" name="txt_WorkObserv" type="text" placeholder="Observaciones de empleado..." cols="80" rows="10" style="width:100%;"></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane" id="datosUsuario">
            <div class="box-body box-profile">
              <div class="col-md-6">
                <div class="box-body">
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon pull-left" style="border:1px solid #D2D6DD;width:100%;padding:9px 10px 9px 10px;">
                        <input id="chk_UsrEsUsuario" name="chk_UsrEsUsuario" type="checkbox" value="0" onchange="appUsuarioSetOne(this.checked);"> Si, es usuario del sistema
                      </span>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon" title="Login"><i class="fa fa-user" style="width:15px;"></i></span>
                      <input id="txt_UsrLogin" name="txt_UsrLogin" type="text" class="form-control" placeholder="login..."/>
                    </div>
                  </div>
                  <div class="form-group" id="appUsrPassw">
                    <div class="input-group">
                      <span class="input-group-addon" title="Password"><i class="fa fa-lock" style="width:15px;"></i></span>
                      <input id="txt_UsrPassw" name="txt_UsrPassw" type="password" class="form-control" placeholder="password...." autocomplete="off"/>
                    </div>
                  </div>
                  <div class="form-group" id="appUsrRepassw">
                    <div class="input-group">
                      <span class="input-group-addon" title="Repetir Password"><i class="fa fa-lock" style="width:15px;"></i></span>
                      <input id="txt_UsrRepassw" name="txt_UsrRepassw" type="password" class="form-control" placeholder=" repetir password..." autocomplete="off"/>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon" title="Nivel de acceso"><i class="fa fa-credit-card" style="width:15px;"></i></span>
                      <select id="cbo_UsrNivelAcceso" name="cbo_UsrNivelAcceso" class="form-control selectpicker"></select>
                    </div>
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
  <div class="modal fade" id="modalVisor" role="dialog">
    <div class="modal-dialog" style="width:98%;">
      <div class="modal-content">
        <div class="modal-header" style="background:#f9f9f9;padding:8px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><b>Visor de Documentos</b></h4>
        </div>
        <div class="modal-body">
          <div class="table-responsive no-padding" id="contenedorFrame">
          </div>
        </div>
        <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
          <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="pages/global/docs/sistemas.js"></script>
<script>
  $(document).ready(function(){
    appBotonReset();
  });
</script>
