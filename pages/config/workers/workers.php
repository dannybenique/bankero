<script src="plugins/excel-export/xlsx.core.min.js"></script>
<script src="plugins/excel-export/FileSaver.js"></script>
<script src="plugins/excel-export/jhxlsx.js"></script>

<!-- SHA1 -->
<script type="text/javascript" src="libs/webtoolkit/webtoolkit.sha1.js"></script>

<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- modal de Personas -->
<script src="pages/modals/Personas/mod.persona.js"></script>

<section class="content-header">
  <h1><i class="fa fa-user"></i> Empleados</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Empleados</li>
  </ol>
</section>
<section class="content">
  <div class="row" id="grid">
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header no-padding">
          <div class="mailbox-controls">
            <div class="btn-group">
              <button type="button" class="btn btn-default btn-sm" onclick="javascript:appWorkerBotonBorrar();"><i class="fa fa-trash-o"></i></button>
              <button type="button" class="btn btn-default btn-sm" onclick="javascript:appWorkerBotonNuevo();"><i class="fa fa-plus"></i></button>
              <button type="button" class="btn btn-default btn-sm" onclick="javascript:appWorkerBotonDownload();" title="Descargar todos los empleados de esta agencia"><i class="fa fa-download"></i></button>
            </div>
            <div class="btn-group">
              <button id="btnEstado" type="button" class="btn btn-default btn-sm" onclick="javascript:appWorkerBotonEstado();" title="Solo activos"><i id="icoEstado" class="fa fa-toggle-on"></i><input type="hidden" id="hidEstado" value="1"></button>
              <button type="button" class="btn btn-default btn-sm" onclick="javascript:appWorkerBotonReset();"><i class="fa fa-refresh"></i></button>
            </div>
            <div class="btn-group">
              <select id="cboAgencias" class="btn btn-default btn-sm" style="height:30px;text-align:left;" onchange="javascript:appWorkerGridAll();"></select>
              <input type="hidden" id="agenciaAbrev" value="">
            </div>
            <div class="btn-group">
              <input type="text" id="txtBuscar" class="form-control input-sm pull-right" placeholder="dni, empleado..." onkeypress="javascript:appWorkerBotonBuscar(event);" style="text-transform:uppercase;" autocomplete="off">
              <span class="fa fa-search form-control-feedback"></span>
            </div>
            <span id="grdDatosCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
              <table class="table table-hover" id="grdDatos">
                <thead>
                  <tr>
                    <th style="width:30px;"><input type="checkbox" onclick="SelectAll(this,'chk_Borrar','grdDatos');"/></th>
                    <th style="width:30px;">Pw</th>
                    <th style="width:30px;">SUD</th>
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
  <div class="modal fade" id="modalChangePassw" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" style="background:#f9f9f9;padding:8px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title fontFlexoRegular">Cambiar Password</h4>
        </div>
        <form class="form-horizontal" id="frmChangePassw" autocomplete="off">
          <div class="modal-body no-padding">
            <div class="box-body">
              <h4 class="timeline-header no-border fontFlexoRegular">
                <span class="appSpanPerfil" >Usuario</span>
                <span id="usrNombreCorto" style="color:#3c8dbc;font-weight:600;"></span>
              </h4>
            </div>
            <div class="box-body">
              <div class="col-md-12">
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon no-border">Nuevo Password</span>
                    <input type="password" class="form-control" id="txt_passwordNew" placeholder="password..." autocomplete="off">
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon no-border">Repetir Password</span>
                    <input type="password" class="form-control" id="txt_passwordRenew" placeholder="repetir password..." autocomplete="off">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
            <input type="hidden" id="usrIDpassw" value="">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
            <button type="button" class="btn btn-primary" onclick="javascript:modWorkerBotonUpdatePassw();"><i class="fa fa-flash"></i> Cambiar Password</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalChangeCoopSUD" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" style="background:#f9f9f9;padding:8px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title fontFlexoRegular">Cambiar Accesos CoopSUD</h4>
        </div>
        <form class="form-horizontal" id="frmChangeCoopSUD" autocomplete="off">
          <div class="modal-body no-padding">
            <div class="box-body">
              <h4 class="timeline-header no-border fontFlexoRegular">
                <span class="appSpanPerfil">Usuario</span>
                <span id="coopUsuario" style="color:black;font-weight:600;"></span>
              </h4>
                <span class="appSpanPerfil">Agencia</span>
                <span id="coopAgencia" style="color:black;font-weight:600;"></span>
                <br/>
                <span class="appSpanPerfil">Ventanilla</span>
                <span id="coopVentanilla" style="color:black;font-weight:600;"></span>
            </div>
            <div class="box-body">
              <table class="table no-border">
                <tr style="padding:10px;">
                  <td style="text-align:right;width:35%;">
                    <span style="padding-right:10px;">Nivel Acceso</span></td>
                  <td>
                    <select class="form-control" id="cbo_coopNivel" name="cbo_coopNivel" class="selectpicker">
                      <option value="E">Empleado</option>
                      <option value="J">Jefatura</option>
                      <option value="S">Supervisor</option>
                      <option value="G">Gerencia</option>
                    </select></td>
                </tr>
                <tr style="padding:10px;">
                  <td style="text-align:right;">
                    <span style="padding-right:10px;">Modificar Inter.</span></td>
                  <td>
                    <input type="checkbox" id="chk_coopModiInter" name="chk_coopModiInter"></td>
                </tr>
                <tr style="padding:10px;">
                  <td style="text-align:right;">
                    <span style="padding-right:10px;">Eliminar Mov.</span></td>
                  <td>
                    <input type="checkbox" id="chk_coopDeleMovi" name="chk_coopDeleMovi"></td>
                </tr>
              </table>
            </div>
          </div>
          <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
            <input type="hidden" id="coopCodUsuario" value="">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
            <button type="button" class="btn btn-primary" onclick="javascript:modWorkerBotonUpdateCoopSUD($('#coopCodUsuario').prop('value'),'#cbo_coopNivel','#chk_coopModiInter','#chk_coopDeleMovi');"><i class="fa fa-flash"></i> Cambiar Accesos</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalDeleteWorker" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" style="background:#f9f9f9;padding:8px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title fontFlexoRegular">Eliminar Empleado</h4>
        </div>
        <form class="form-horizontal" id="frmDeleteWorker" autocomplete="off">
          <div class="modal-body no-padding">
            <div class="box-body">
              <table class="table no-border">
                <tr>
                  <td style="width:30%"></td>
                  <td>
                    <div class="input-group">
                      <span class="input-group-addon" style="background:#f5f5f5;">Fecha de Baja</span>
                      <input id="date_fechabaja" name="date_fechabaja" type="text" class="form-control" style="width:105px;"/>
                    </div>
                  </td>
                  <td style="width:30%"></td>
                </tr>
              </table>
            </div>
          </div>
          <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
            <button type="button" class="btn btn-danger" onclick="javascript:modWorkerBotonDelete();"><i class="fa fa-trash-o"></i> Borrar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalPers" role="dialog"></div>
</section>

<script src="pages/config/workers/workers.js"></script>
<script>
  $(document).ready(function(){
    Persona.addModalToParentForm('modalPers');
    appWorkerBotonReset();
  });
</script>
