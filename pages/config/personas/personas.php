<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- modal de Personas y Laborales y Conyuge -->
<script src="pages/modals/Personas/mod.persona.js"></script>
<script src="pages/modals/Laboral/mod.laboral.js"></script>
<script src="pages/modals/Conyuge/mod.conyuge.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-gears"></i> Personas</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Personas</li>
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
              <?php if($_SESSION['usr_usernivelID']==701) {?>
              <button type="button" class="btn btn-default btn-sm" onclick="javascript:appPersonasBorrar();"><i class="fa fa-trash-o"></i></button>
              <?php }?>
              <button type="button" class="btn btn-default btn-sm" onclick="javascript:appPersonaNuevo();"><i class="fa fa-plus"></i></button>
            </div>
            <button type="button" class="btn btn-default btn-sm" onclick="javascript:appPersonasReset();"><i class="fa fa-refresh"></i></button>
            <div class="btn-group">
              <input type="text" id="txtBuscar" name="txtBuscar" class="form-control input-sm pull-right" placeholder="DNI, persona..." onkeypress="javascript:appPersonasBuscar(event);" style="text-transform:uppercase;" autocomplete="off">
              <span class="fa fa-search form-control-feedback"></span>
            </div>
            <span id="grdDatosCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
            <table class="table table-hover" id="grdDatos">
              <thead>
                <tr>
                  <th style="width:30px;"><input type="checkbox" id="chk_All" onclick="SelectAll(this,'chk_Borrar','grdDatos');" /></th>
                  <th style="width:30px;"><i class="fa fa-eye" title="Auditoria"></i></th>
                  <th style="width:30px;"><i class="fa fa-files-o" title="Formatos..."></i></th>
                  <th style="width:80px;">DNI</th>
                  <th style="width:30px;"><i class="fa fa-paperclip"></i></th>
                  <th style="width:350px;">Persona</th>
                  <th style="">Direccion</th>
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
    <form class="form-horizontal" id="frmPersona" autocomplete="off">
      <div class="col-md-3">
        <div class="box box-widget widget-user-2">
          <div class="widget-user-header bg-aqua-active">
            <div class="widget-user-image">
              <input type="hidden" id="hid_PersUrlFoto" value=""/>
              <img class="img-circle" src="" id="img_Foto" alt="persona"/>
            </div>
            <div style="min-height:70px;">
              <h5 class="widget-user-username fontFlexoRegular" id="lbl_Apellidos"></h5>
              <h4 class="widget-user-desc fontFlexoRegular" id="lbl_Nombres"></h4>
            </div>
          </div>
          <div class="box-footer no-padding">
            <ul class="nav nav-stacked">
              <li style="padding:10px"><b>ID</b> <span class="pull-right badge bg-blue" id="lbl_ID" style="font:normal 13px flexoregular;"></span></li>
              <li style="padding:10px"><b><span id="lbl_TipoDNI">DNI</span></b> <span class="pull-right badge bg-blue" id="lbl_DNI" style="font:normal 13px flexoregular;"></span></li>
              <li style="padding:10px"><b>Celular</b><span class="pull-right badge bg-blue" id="lbl_Celular" style="font:normal 13px flexoregular;"></span></li>
            </ul>
          </div>
          <div class="box-body">
            <button type="button" class="btn btn-default" onclick="javascript:appPersonasBotonCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
          </div>
        </div>
      </div>
      <div class="col-md-9">
        <div class="nav-tabs-custom">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#datosPersonal" data-toggle="tab"><i class="fa fa-male"></i> Personal</a></li>
            <li><a href="#datosLaboral" data-toggle="tab"><i class="fa fa-cogs"></i> Laboral</a></li>
            <li><a href="#datosConyuge" data-toggle="tab"><i class="fa fa-heart"></i> Conyuge</a></li>
          </ul>
          <div class="tab-content">
            <div id="datosPersonal" class="tab-pane active">
              <div class="box-body">
                <div class="col-md-5">
                  <div class="box-body">
                    <strong><i class="fa fa-user margin-r-5"></i> Basicos</strong>
                    <p class="text-muted">
                      <input type="hidden" id="hid_PersID" value=""/>
                      <span id="lbl_PersTipoNombres">Nombres</span>: <a id="lbl_PersNombres"></a><br>
                      <span id="lbl_PersTipoApellidos">Apellidos: <a id="lbl_PersApellidos"></a><br></span><br>
                      <span id="lbl_PersTipoDNI"></span>: <a id="lbl_PersNroDNI"></a><br>
                      Fecha Nac: <a id="lbl_PersFechaNac"></a><br>
                      Lugar Nac: <a id="lbl_PersLugarNac"></a><br>
                      <span id="lbl_PersTipoSexo">Sexo: <a id="lbl_PersSexo"></a><br></span>
                      <span id="lbl_PersTipoECivil">Estado Civil: <a id="lbl_PersEcivil"></a></span>
                    </p>
                    <hr/>

                    <strong><i class="fa fa-phone margin-r-5"></i> Contacto</strong>
                    <p class="text-muted">
                      Celular: <a id="lbl_PersCelular"></a><br>
                      Telefono Fijo: <a id="lbl_PersTelefijo"></a><br>
                      Correo: <a id="lbl_PersEmail"></a><br>
                    </p>
                    <hr/>

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
                      Direccion: <a id="lbl_PersDireccion"></a> &raquo; (<a id="lbl_PersUbicacion"></a>)<br>
                      Referencia: <a id="lbl_PersReferencia"></a><br>
                      Medidor de Luz: <a id="lbl_PersMedidorluz"></a><br>
                      Tipo de Vivienda: <a id="lbl_PersTipovivienda"></a>
                    </p>
                    <hr/>

                    <strong><i class="fa fa-file-text-o margin-r-5"></i> Observaciones</strong>
                    <p class="text-muted">
                      <span id="lbl_PersObservac"></span>
                    </p><br><br>
                    <strong><i class="fa fa-eye margin-r-5"></i> Auditoria</strong>
                    <div style="font-style:italic;font-size:11px;color:gray;">
                      Fecha: <span id="lbl_PersSysFecha"></span><br>
                      Modif. por: <span id="lbl_PersSysUser"></span>
                    </div><br>
                    <button id="btn_PersUpdate" type="button" class="btn btn-primary btn-xs" onclick="javascript:appPersonaEditar();"><i class="fa fa-flash"></i> Modificar Datos</button>
                    <button id="btn_PersPermiso" type="button" class="btn btn-success btn-xs" style="display:none;" onclick="javascript:appPermisoPersonas();"><i class="fa fa-shield"></i> Solicitar Permiso</button>
                  </div>
                </div>
              </div>
            </div>
            <div id="datosLaboral" class="tab-pane">
              <div class="box-body">
                <button id="btn_LaboInsert" type="button" class="btn btn-primary btn-xs" style="display:none;" onclick="javascript:appLaboralNuevo();"><i class="fa fa-plus"></i> Agregar Datos Laborales</button>
                <button id="btn_LaboUpdate" type="button" class="btn btn-primary btn-xs" style="display:none;" onclick="javascript:appLaboralEditar();"><i class="fa fa-flash"></i> Modificar Datos</button>
                <button id="btn_LaboDelete" type="button" class="btn btn-danger btn-xs" style="display:none;" onclick="javascript:appLaboralDelete();"><i class="fa fa-trash-o"></i> Quitar Laboral</button>
                <button id="btn_LaboPermiso" type="button" class="btn btn-success btn-xs" style="display:none;" onclick="javascript:appPermisoLaboral();"><i class="fa fa-shield"></i> Solicitar Permiso</button>
              </div>
              <div id="div_Laboral" class="box-body">
                <input type="hidden" id="hid_LaboPermisoID" value="0"/>
                <div class="col-md-6">
                  <div class="box-body">
                    <strong><i class="fa fa-truck"></i> Laborales</strong>
                    <p class="text-muted">
                      Condicion: <a id="lbl_LaboCondicion"></a><br>
                      Empresa: <a id="lbl_LaboEmprRazon"></a><br>
                      RUC: <a id="lbl_LaboEmprRUC"></a><br>
                      Telefono: <a id="lbl_LaboEmprFono"></a><br>
                      Rubro: <a id="lbl_LaboEmprRubro"></a><br><br>

                      Fecha Ing.: <a id="lbl_LaboEmprFechaIng"></a><br>
                      Cargo: <a id="lbl_LaboEmprCargo"></a><br>
                      Ingreso (S/.): <a id="lbl_LaboEmprIngreso"></a>
                    </p>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="box-body">
                    <strong><i class="fa fa-map-marker"></i> Ubicacion</strong>
                    <p class="text-muted">
                      Direccion: <a id="lbl_LaboEmprDireccion"></a> &raquo; (<a id="lbl_LaboEmprUbicacion"></a>)<br>
                    </p>
                    <hr/>

                    <strong><i class="fa fa-file-text-o margin-r-5"></i> Observaciones</strong>
                    <p class="text-muted">
                      <span id="lbl_LaboEmprObservac"></span>
                    </p><br><br>
                    <strong><i class="fa fa-eye margin-r-5"></i> Auditoria</strong>
                    <div style="font-style:italic;font-size:11px;color:gray;">
                      Fecha: <span id="lbl_LaboSysFecha"></span><br>
                      Modif. por: <span id="lbl_LaboSysUser"></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div id="datosConyuge" class="tab-pane">
              <div class="box-body">
                <button id="btn_ConyInsert"  type="button" class="btn btn-primary btn-xs" style="display:none;" onclick="javascript:appConyugeNuevo();"><i class="fa fa-plus"></i> Agregar Conyuge</button>
                <button id="btn_ConyUpdate"  type="button" class="btn btn-primary btn-xs" style="display:none;" onclick="javascript:appConyugeEditar();"><i class="fa fa-flash"></i> Modificar Datos</button>
                <button id="btn_ConyDelete"  type="button" class="btn btn-danger btn-xs"  style="display:none;" onclick="javascript:appConyugeDelete();"><i class="fa fa-trash-o"></i> Quitar Conyuge</button>
                <button id="btn_ConyPermiso" type="button" class="btn btn-success btn-xs" style="display:none;" onclick="javascript:appPermisoConyuge();"><i class="fa fa-shield"></i> Solicitar Permiso</button>
              </div>
              <div id="div_Conyuge" class="box-body">
                <input type="hidden" id="hid_ConyPermisoID" value="0">
                <div class="col-md-6">
                  <div class="box-body">
                    <strong><i class="fa fa-user margin-r-5"></i> Basicos</strong>
                    <p class="text-muted">
                      Nombres: <a id="lbl_ConyNombres"></a><br>
                      Apellidos: <a id="lbl_ConyApellidos"></a><br>
                      <span id="lbl_ConyTipoDNI"></span>: <a id="lbl_ConyNroDNI"></a><br>
                      Fecha Nac: <a id="lbl_ConyFechaNac"></a><br>
                      Estado Civil: <a id="lbl_ConyEcivil"></a>
                      <hr style="margin-top:-5px;"/>
                    </p>

                    <strong><i class="fa fa-phone margin-r-5"></i> Contacto</strong>
                    <p class="text-muted">
                      Celular: <a id="lbl_ConyCelular"></a><br>
                      Telefono Fijo: <a id="lbl_ConyTelefijo"></a><br>
                      Correo: <a id="lbl_ConyEmail"></a>
                      <hr style="margin-top:-5px;"/>
                    </p>

                    <strong><i class="fa fa-graduation-cap margin-r-5"></i> Profesionales</strong>
                    <p class="text-muted">
                      Grado Instruccion: <a id="lbl_ConyGInstruccion"></a><br>
                      Profesion: <a id="lbl_ConyProfesion"></a><br>
                      Ocupacion: <a id="lbl_ConyOcupacion"></a>
                      <hr style="margin-top:-5px;"/>
                    </p>

                    <strong><i class="fa fa-map-marker margin-r-5"></i> Ubicacion</strong>
                    <p class="text-muted">
                      Direccion: <a id="lbl_ConyDireccion"></a> &raquo; (<a id="lbl_ConyUbicacion"></a>)<br>
                      Referencia: <a id="lbl_ConyReferencia"></a><br>
                      Medidor de Luz: <a id="lbl_ConyMedidorluz"></a><br>
                      Tipo de Vivienda: <a id="lbl_ConyTipovivienda"></a>
                    </p>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="box-body">
                    <div class="form-cony">
                      <strong><i class="fa fa-tags"></i> Conyugal</strong>
                      <p id="div_ConyTiempo" class="text-muted" style="margin-bottom:35px;">
                        Tiempo de Relacion (años): <a id="lbl_ConyTiempoRel"></a>
                      </p>
                      <strong><i class="fa fa-truck"></i> Laborales</strong>
                      <p id="div_ConyLaboral" class="text-muted">
                        Condicion: <a id="lbl_ConyEmprCondicion"></a><br>
                        Empresa: <a id="lbl_ConyEmprRazonSocial"></a><br>
                        RUC: <a id="lbl_ConyEmprRUC"></a><br>
                        Rubro: <a id="lbl_ConyEmprRubro"></a><br>
                        Telefono: <a id="lbl_ConyEmprFono"></a><br><br>
                        Fecha Ing.: <a id="lbl_ConyEmprFechaIng"></a><br>
                        Cargo: <a id="lbl_ConyEmprCargo"></a><br>
                        Ingreso: <a id="lbl_ConyEmprIngreso"></a><br><br>
                        Direccion: <a id="lbl_ConyEmprDireccion"></a> &raquo; (<a id="lbl_ConyEmprUbicacion"></a>)<br><br>
                        Observaciones: <a id="lbl_ConyEmprObservac"></a>
                      </p>
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
  <div class="row" id="formatos" style="display:none;">
    <div class="col-md-4">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title fontFlexoRegular">Datos Persona</h3>
        </div>
        <div class="box-body">
          <div class="box-body">
            <strong><i class="fa fa-user margin-r-5"></i> Basicos</strong>
            <p class="text-muted">
              <input type="hidden" id="hid_FormPersonaID" value=""/>
              <span id="lbl_FormTipoNombres">Nombres</span>: <a id="lbl_FormNombres"></a><br>
              <span id="lbl_FormTipoApellidos">Apellidos: <a id="lbl_FormApellidos"></a><br></span><br>
              <span id="lbl_FormTipoDNI"></span>: <a id="lbl_FormNroDNI"></a><br>
              Fecha Nac: <a id="lbl_FormFechaNac"></a><br>
              Lugar Nac: <a id="lbl_FormLugarNac"></a><br>
              <span id="lbl_FormTipoSexo">Sexo: <a id="lbl_FormSexo"></a><br></span>
              <span id="lbl_FormTipoECivil">Estado Civil: <a id="lbl_FormECivil"></a></span>
            </p>
            <hr>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><b>Ciudad</b></span>
                <select id="cbo_ciudades" name="cbo_ciudades" class="form-control selectpicker"></select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <button type="button" class="btn btn-default btn-sm" onclick="javascript:appFormatosCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
            </div>
            <div class="col-md-6">
              <input type="hidden" id="hid_FormUrlServer" value="<?php echo ($webconfig->getURL());?>"/>
              <button type="button" class="btn btn-primary btn-sm pull-right" onclick="javascript:appFormatosGenerarPDF('SolicitudIngrSocio');" title="Generar Solicitud de Ingreso de Socio"><i class="fa fa-file-pdf-o"></i> Solicitud de Socio</button>
              <button type="button" class="btn btn-primary btn-sm pull-right" style="margin-top:5px;" onclick="javascript:appFormatosGenerarPDF('FichaInscripcion');" title="Generar Ficha de Inscripcion"><i class="fa fa-file-pdf-o"></i> Ficha de Inscripcion</button>
              <button type="button" class="btn btn-primary btn-sm pull-right" style="margin-top:5px;" onclick="javascript:appFormatosGenerarPDF('DeclJuraConviv');" title="Generar Declaracion Jurada de Convivencia"><i class="fa fa-file-pdf-o"></i> Declaracion Jurada Convivencia</button>
            </div>
          </div>
        </div>
      </div>
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
  <div class="modal fade" id="modalAuditoria" role="dialog">
    <div class="modal-dialog" style="width:90%;">
      <div class="modal-content">
        <form class="form-horizontal" id="frm_modalAuditoria" autocomplete="off">
          <div class="modal-header" style="background:#f9f9f9;padding:8px;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title fontFlexoRegular">Auditoria de acciones para... <span id="lbl_modAuditoriaTitulo"></span></h4>
          </div>
          <div class="modal-body">
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover" id="grdAuditoria">
                <thead>
                  <tr>
                    <th style="width:60px;">codigo</th>
                    <th style="width:150px;">tabla</th>
                    <th style="width:40px;">accion</th>
                    <th style="width:85px;">campo</th>
                    <th style="">observac.</th>
                    <th style="width:130px;">usuario</th>
                    <th style="width:100px;">sysIP</th>
                    <th style="width:50px;">sysAg</th>
                    <th style="width:85px;text-align:center;">sysfecha</th>
                    <th style="width:70px;text-align:center;">syshora</th>
                  </tr>
                </thead>
                <tbody id="grdAuditoriaBody">
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
            <button type="button" class="btn btn-default btn-sm pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalCony" role="dialog"></div>
  <div class="modal fade" id="modalLabo" role="dialog"></div>
  <div class="modal fade" id="modalPers" role="dialog"></div>
</section>

<script src="pages/config/personas/personas.js"></script>
<script>
  $(document).ready(function(){
    Persona.addModalToParentForm('modalPers');
    Laboral.addModalToParentForm('modalLabo');
    Conyuge.addModalToParentForm('modalCony');
    appPersonasReset();
  });
</script>
