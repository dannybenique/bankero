<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css"/>
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- modal de Personas -->
<script src="pages/modals/Personas/mod.persona.js"></script>
<script src="pages/modals/Laboral/mod.laboral.js"></script>
<script src="pages/modals/Conyuge/mod.conyuge.js"></script>

<section class="content-header">
  <h1><i class="fa fa-user"></i> Socios</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Socios</li>
  </ol>
</section>
<section class="content">
  <div class="row" id="grid">
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header no-padding">
          <div class="mailbox-controls">
            <div class="btn-group">
              <button type="button" class="btn btn-default btn-sm" onclick="javascript:appSociosDelete();"><i class="fa fa-trash-o"></i></button>
              <button type="button" class="btn btn-default btn-sm" onclick="javascript:appSocioNuevo();"><i class="fa fa-plus"></i></button>
            </div>
            <div class="btn-group">
              <button type="button" class="btn btn-default btn-sm" onclick="javascript:appSociosReset();" title="Volver a cargar todos los socios"><i class="fa fa-refresh"></i></button>
            </div>
            <div class="btn-group">
              <select id="cboAgencias" class="btn btn-default btn-sm" style="height:30px;" onchange="javascript:appSociosGridAll();"></select>
              <input type="hidden" id="agenciaAbrev" value="">
            </div>
            <div class="btn-group">
              <input type="text" id="txtBuscar" class="form-control input-sm pull-right" placeholder="DNI, socio..." onkeypress="javascript:appSociosBotonBuscar(event);" style="text-transform:uppercase;" autocomplete="off">
              <span class="fa fa-search form-control-feedback"></span>
            </div>
            <span id="grdDatosCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
              <table class="table table-hover" id="grdDatos">
                <thead>
                  <tr>
                    <th style="width:25px;"><input type="checkbox" onclick="SelectAll(this,'chk_Borrar','grdDatos');"/></th>
                    <th style="width:30px;"><i class="fa fa-file-text" title="Extracto Bancario..."></i></th>
                    <th style="width:30px;"><i class="fa fa-files-o" title="Formatos..."></i></th>
                    <th style="width:70px;">Codigo</th>
                    <th style="width:110px;">DNI</th>
                    <th>Socio</th>
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
    <div class="col-md-3">
      <div class="box box-widget widget-user-2">
        <div class="widget-user-header bg-aqua-active">
          <div class="widget-user-image">
            <input type="hidden" id="hid_PersUrlFoto" value=""/>
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
            <li class="list-group-item" style="padding:5px 10px 5px 10px;"><b>Agencia</b><a class="pull-right" id="lbl_agencia"></a><input id="hid_agenciaText" type="hidden" value="" /></li>
            <li class="list-group-item" style="padding:5px 10px 5px 10px;"><b><span id="lbl_TipoDNI">DNI</span></b><a class="pull-right" id="lbl_DNI"></a></li>
            <li class="list-group-item" style="padding:5px 10px 5px 10px;"><b>Celular</b><a class="pull-right" id="lbl_Celular"></a></li>
          </ul>
        </div>
        <div class="box-body">
          <button type="button" class="btn btn-default" onclick="javascript:appSociosBotonCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
          <button id="btnInsert" type="button" class="btn btn-primary pull-right" style="display:none;" onclick="javascript:appSociosBotonInsert();"><i class="fa fa-save"></i> Guardar</button>
          <button id="btnUpdate" type="button" class="btn btn-info pull-right" style="display:none;" onclick="javascript:appSociosBotonUpdate();"><i class="fa fa-save"></i> Actualizar</button>
        </div>
      </div>
    </div>
    <div class="col-md-9">
        <form class="form-horizontal" autocomplete="off">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li  class="active"><a href="#datosSocio" data-toggle="tab"><i class="fa fa-briefcase"></i> Socio</a></li>
              <li><a href="#datosPersonal" data-toggle="tab"><i class="fa fa-male"></i> Personal</a></li>
              <li><a href="#datosLaboral" data-toggle="tab"><i class="fa fa-cogs"></i> Laboral</a></li>
              <li><a href="#datosConyuge" data-toggle="tab"><i class="fa fa-heart"></i> Conyuge</a></li>
            </ul>
            <div class="tab-content">
              <div id="datosSocio" class="tab-pane active">
                <div class="box-body">
                  <div class="col-md-6">
                    <div class="box-body">
                      <div class="form-group" style="margin-bottom:5px;">
                        <div class="input-group">
                          <span class="input-group-addon" style="width:85px;">Ingreso</span>
                          <input id="txt_SocFechaIng" name="txt_SocFechaIng" type="text" class="form-control" style="width:105px;" />
                        </div>
                      </div>
                      <div class="form-group" style="margin-bottom:5px;">
                        <div class="input-group">
                          <span class="input-group-addon" style="width:85px;">Codigo</span>
                          <input id="txt_SocCodigo" name="txt_SocCodigo" type="text" class="form-control" placeholder="Codigo..." maxlength="7" style="width:85px;"/>
                        </div>
                      </div>
                      <div class="form-group" style="margin-bottom:5px;">
                        <div class="input-group">
                          <span class="input-group-addon" style="width:85px;">Agencia</span>
                          <select id="cbo_SocAgencia" name="cbo_SocAgencia" class="form-control selectpicker" onchange="appSetTexto('#lbl_agencia','#cbo_SocAgencia',true);"></select>
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="input-group">
                          <textarea id="txt_SocObserv" name="txt_SocObserv" type="text" placeholder="Observaciones de socio..." cols="80" rows="10" style="width:100%;"></textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="box-body">
                      <div id="div_SocGasNrodep" class="form-group" style="margin-bottom:5px;">
                        <div class="input-group">
                          <span class="input-group-addon" style="width:85px;" title="Nro de Dependientes">Nro Dep.</span>
                          <input id="txt_SocGasNrodep" name="txt_SocGasNrodep" type="number" class="form-control" style="width:50px; text-align:right;" value="0" />
                        </div>
                      </div>
                      <div id="div_SocGasAlim" class="form-group" style="margin-bottom:5px;">
                        <div class="input-group">
                          <span class="input-group-addon" style="width:85px;" title="Gastos de Alimentacion">G. Alim.</span>
                          <input id="txt_SocGasAlim" name="txt_SocGasAlim" type="text" class="form-control" style="width:105px; text-align:right;" value="0.00" />
                        </div>
                      </div>
                      <div id="div_SocGasEduc" class="form-group" style="margin-bottom:5px;">
                        <div class="input-group">
                          <span class="input-group-addon" style="width:85px;" title="Gastos de Educacion">G. Educ.</span>
                          <input id="txt_SocGasEduc" name="txt_SocGasEduc" type="text" class="form-control" style="width:105px;text-align:right;" value="0.00" />
                        </div>
                      </div>
                      <div id="div_SocGasTrans" class="form-group" style="margin-bottom:5px;">
                        <div class="input-group">
                          <span class="input-group-addon" style="width:85px;" title="Gastos de Transporte">G. Trans.</span>
                          <input id="txt_SocGasTrans" name="txt_SocGasTrans" type="text" class="form-control" style="width:105px;text-align:right;" value="0.00" />
                        </div>
                      </div>
                      <div id="div_SocGasAlqui" class="form-group" style="margin-bottom:5px;">
                        <div class="input-group">
                          <span class="input-group-addon" style="width:85px;" title="Gastos de Alquiler">G. Alqu.</span>
                          <input id="txt_SocGasAlqui" name="txt_SocGasAlqui" type="text" class="form-control" style="width:105px;text-align:right;" value="0.00" />
                        </div>
                      </div>
                      <div id="div_SocGasFono" class="form-group" style="margin-bottom:5px;">
                        <div class="input-group">
                          <span class="input-group-addon" style="width:85px;" title="Gastos de Telefono">G. Telef.</span>
                          <input id="txt_SocGasFono" name="txt_SocGasFono" type="text" class="form-control" style="width:105px;text-align:right;" value="0.00" />
                        </div>
                      </div>
                      <div id="div_SocGasAgua" class="form-group" style="margin-bottom:5px;">
                        <div class="input-group">
                          <span class="input-group-addon" style="width:85px;" title="Gastos de Agua">G. Agua</span>
                          <input id="txt_SocGasAgua" name="txt_SocGasAgua" type="text" class="form-control" style="width:105px;text-align:right;" value="0.00" />
                        </div>
                      </div>
                      <div id="div_SocGasLuz" class="form-group" style="margin-bottom:5px;">
                        <div class="input-group">
                          <span class="input-group-addon" style="width:85px;" title="Gastos de Luz">G. Luz</span>
                          <input id="txt_SocGasLuz" name="txt_SocGasLuz" type="text" class="form-control" style="width:105px;text-align:right;" value="0.00" />
                        </div>
                      </div>
                      <div id="div_SocGasOtros" class="form-group" style="margin-bottom:5px;">
                        <div class="input-group">
                          <span class="input-group-addon" style="width:85px;" title="Gastos Otros">G. Otros</span>
                          <input id="txt_SocGasOtros" name="txt_SocGasOtros" type="text" class="form-control" style="width:105px;text-align:right;" value="0.00" />
                        </div>
                      </div>
                      <div id="div_SocGasPrest" class="form-group" style="margin-bottom:5px;">
                        <div class="input-group">
                          <span class="input-group-addon" style="width:85px;" title="Gastos Otros">G. Prest</span>
                          <input id="txt_SocGasPrest" name="txt_SocGasPrest" type="text" class="form-control" style="width:105px;text-align:right;" value="0.00" />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div id="datosPersonal" class="tab-pane">
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
        </form>
      </div>
  </div>
  <div class="row" id="formatos" style="display:none;">
    <div class="col-md-4">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title fontFlexoRegular">Datos Socio</h3>
        </div>
        <div class="box-body">
          <strong><i class="fa fa-user margin-r-5"></i> Basicos</strong>
          <p class="text-muted">
            <input type="hidden" id="hid_PersID" value="">
            Nombres: <a id="lbl_FormNombres"></a><br>
            Apellidos: <a id="lbl_FormApellidos"></a><br><br>
            <span id="lbl_FormTipoDNI"></span>: <a id="lbl_FormNroDNI"></a><br>
            Fecha Nac: <a id="lbl_FormFechaNac"></a><br>
            Lugar Nac: <a id="lbl_FormLugarNac"></a><br>
            Sexo: <a id="lbl_FormSexo"></a><br>
            Estado Civil: <a id="lbl_FormEcivil"></a>
            <hr>
          </p>
          <div class="row">
            <div class="col-md-6">
              <button type="button" class="btn btn-default btn-sm" onclick="javascript:appFormatosCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
            </div>
            <div class="col-md-6">
              <input type="hidden" id="hid_FormUrlServer" value="<?php echo ("https://".$webconfig->getURL());?>"/>
              <button type="button" class="btn btn-primary btn-sm pull-right" onclick="javascript:appFormatosGenerarPDF('ahorrosContrato');"><i class="fa fa-file-pdf-o"></i> Contrato Ahorros</button>
              <button type="button" class="btn btn-primary btn-sm pull-right" style="margin-top:5px;" onclick="javascript:appFormatosGenerarPDF('ahorrosAnexo01');"><i class="fa fa-file-pdf-o"></i> Anexo 01 Ahorros</button>
              <button type="button" class="btn btn-primary btn-sm pull-right" style="margin-top:5px;" onclick="javascript:appFormatosGenerarPDF('creditosResumen');"><i class="fa fa-file-pdf-o"></i> Carta Garantia Liquida</button>
              <button type="button" class="btn btn-primary btn-sm pull-right" style="margin-top:5px;" onclick="javascript:appFormatosGenerarPDF('cartaGarLiqui');"><i class="fa fa-file-pdf-o"></i> Hoja Resumen Credito</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <div class="box box-primary">
        <div class="box-body">
          <div class="box-body table-responsive no-padding" id="contenedorFrame">
            <object id="objPDF" type="text/html" data="" width="100%" height="450px"></object>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row" id="ExtrBancario" style="display:none;">
    <div class="col-md-4">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title fontFlexoRegular">Datos Socio</h3>
        </div>
        <div class="box-body">
          <strong><i class="fa fa-user margin-r-5"></i> Persona</strong>
          <p class="text-muted">
            Nombres: <a id="lbl_ExtrNombres"></a><br>
            Apellidos: <a id="lbl_ExtrApellidos"></a><br><br>
            ID: <a id="lbl_ExtrID"></a><br>
            Codigo: <a id="lbl_ExtrCodigo"></a><br>
            <span id="lbl_ExtrTipoDNI"></span>: <a id="lbl_ExtrNroDNI"></a><br>
            Agencia: <a id="lbl_ExtrAgencia"></a>
            <hr>
          </p>

          <strong><i class="fa fa-file-text-o margin-r-5"></i> Otros Datos</strong>
          <p class="text-muted">
            Operacion: <select id="cbo_ExtrOperaciones" class="btn btn-default btn-sm" style="height:30px;"></select><br>
            Producto: <select id="cbo_ExtrProducto" class="btn btn-default btn-sm" style="height:30px;"></select><br>
            <hr>
          </p>

          <div class="box-body">
            <button id="btnCancel" type="button" class="btn btn-default" onclick="javascript:appFormatosBotonCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
            <button type="button" class="btn btn-primary pull-right" onclick="javascript:appSociosExtrBancReporte();"><i class="fa fa-file-text-o"></i> Extracto Bancario</button>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <div class="box box-primary">
        <div class="box-body box-profile">
          <div style="">

          </div>
          <div class="box-body table-responsive no-padding">
              <table class="table table-hover" id="grdExtrBancario">
                <thead>
                  <tr>
                    <th style="width:40px;">AG</th>
                    <th style="width:40px;">USR</th>
                    <th style="width:40px;">MOV</th>
                    <th style="width:85px;text-align:right;">Fecha</th>
                    <th style="width:85px;text-align:right;">Nro Oper.</th>
                    <th style="">Detalle</th>
                    <th style="width:100px;text-align:right;">Depositos</th>
                    <th style="width:100px;text-align:right;">Retiros</th>
                    <th style="width:100px;text-align:right;">Otros</th>
                  </tr>
                </thead>
                <tbody id="grdExtrBancarioBody">
                </tbody>
              </table>
            </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalCony" role="dialog"></div>
  <div class="modal fade" id="modalLabo" role="dialog"></div>
  <div class="modal fade" id="modalPers" role="dialog"></div>
</section>

<script src="pages/config/socios/socios.js"></script>
<script>
  $(document).ready(function(){
    Persona.addModalToParentForm('modalPers');
    Laboral.addModalToParentForm('modalLabo');
    Conyuge.addModalToParentForm('modalCony');
    appSociosReset();
  });
</script>
