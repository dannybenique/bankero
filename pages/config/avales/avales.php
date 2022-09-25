<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<!-- modal de Personas -->
<script src="app/js/modalPersonas/mod.persona.js"></script>

<section class="content-header">
  <h1><i class="fa fa-user"></i> Avales</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Avales</li>
  </ol>
</section>
<section class="content">
  <div class="row" id="grid">
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header no-padding">
          <div class="mailbox-controls">
            <div class="btn-group">
              <button type="button" class="btn btn-default btn-sm" onclick="javascript:appSociosSqlDelete();"><i class="fa fa-trash-o"></i></button>
              <button type="button" class="btn btn-default btn-sm" onclick="javascript:Persona.openBuscar('VerifySocios',1,0);"><i class="fa fa-plus"></i></button>
            </div>
            <div class="btn-group">
              <button type="button" class="btn btn-default btn-sm" onclick="javascript:appSociosGridReset();" title="Volver a cargar todos los avales"><i class="fa fa-refresh"></i></button>
            </div>
            <div class="btn-group">
              <select id="cboAgencias" class="btn btn-default btn-sm" style="height:30px;" onchange="javascript:appSociosGridAll();"></select>
              <input type="hidden" id="agenciaAbrev" value="">
            </div>
            <div class="btn-group">
              <input type="text" id="txtBuscar" class="form-control input-sm pull-right" placeholder="buscar DNI, socio..." onkeypress="javascript:appSociosGridBuscar(event);">
              <span class="fa fa-search form-control-feedback"></span>
            </div>
            <span id="grdDatosCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
              <table class="table table-hover" id="grdDatos">
                <thead>
                  <tr>
                    <th style="width:25px;"><input type="checkbox" onclick="SelectAll(this,'chk_Borrar','grdDatos');"/></th>
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
    <form class="form-horizontal" autocomplete="off">
      <div class="col-md-3">
        <div class="box box-primary">
          <div class="box-body box-profile">
            <img class="profile-user-img img-responsive img-circle" src="" id="img_Foto" alt="Foto de Socio">
            <h3 class="profile-username text-center" style="font-family:flexobold" id="lbl_Nombres">...</h3>
            <p class="text-muted text-center" id="lbl_Apellidos">...</p>
            <ul class="list-group list-group-unbordered">
              <li class="list-group-item">
                <b>ID</b> <a class="pull-right" id="lbl_ID"></a></li>
              <li class="list-group-item">
                <b>Agencia</b> <a class="pull-right" id="lbl_agencia"></a></li>
              <li class="list-group-item">
                <b>DNI</b> <a class="pull-right" id="lbl_DNI"></a></li>
              <li class="list-group-item">
                <b>Celular</b> <a class="pull-right" id="lbl_celular"></a></li>
            </ul>
          </div>
          <div id="divSocioError" class="box-body box-profile" style="display:none;">
            <div class="callout callout-danger">
              <h4>Faltan Datos!</h4>
              <p id="lbl_Error">faltan llenar datos importantes</p>
            </div>
          </div>
          <div id="pn_PersBotones" class="box-body box-profile">
            <button type="button" class="btn btn-default" onclick="javascript:appSociosEditCancel();">Cancelar</button>
            <button id="btnInsert" type="button" class="btn btn-primary pull-right" onclick="javascript:appSociosEditInsert();">Guardar</button>
            <button id="btnUpdate" type="button" class="btn btn-info pull-right" onclick="javascript:appSociosEditUpdate();">Actualizar</button>
          </div>
        </div>
      </div>
      <div class="col-md-9">
        <div class="nav-tabs-custom">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#datosPersonal" data-toggle="tab"><i class="fa fa-male"></i> Datos Personales</a></li>
            <li><a href="#datosSocio" data-toggle="tab"><i class="fa fa-briefcase"></i> Datos Socio</a></li>
            <li><a href="#datosConyuge" data-toggle="tab"><i class="fa fa-heart"></i> Conyuge</a></li>
            <li><a href="#datosLaboral" data-toggle="tab"><i class="fa fa-cogs"></i> Laboral</a></li>
          </ul>
          <div class="tab-content">
            <div id="datosPersonal" class="tab-pane active">
              <div class="box-body">
                <div class="col-md-6">
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
                      <span id="lbl_PersObservac"></span><br><br>
                      Fecha: <span id="lbl_PersSysFecha"></span><br>
                      Modif. por: <span id="lbl_PersSysUser"></span><br><br>
                      <button type="button" class="btn btn-primary btn-xs" onclick="javascript:Persona.editar($('#lbl_ID').html(),'S');">Modificar Datos</button>
                    </p>
                  </div>
                </div>
              </div>
            </div>
            <div id="datosSocio" class="tab-pane">
              <div class="box-body">
                <div class="col-md-6">
                  <div class="box-body">
                    <div class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:85px;">Codigo</span>
                        <input id="txt_SocCodigo" name="txt_SocCodigo" type="text" class="form-control" placeholder="Codigo..." maxlength="7" style="width:80px;"/>
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
                        <span class="input-group-addon" style="width:85px;">Fecha Ing.</span>
                        <input id="date_SocIngreso" name="date_SocIngreso" type="text" class="form-control" style="width:105px;" />
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
                        <span class="input-group-addon" style="width:85px;" title="Nro de Dependientes">Nro Dep.</i></span>
                        <input id="txt_SocGasNrodep" name="txt_SocGasNrodep" type="text" class="form-control" style="width:50px; text-align:right;" value="0" />
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
            <div id="datosConyuge" class="tab-pane">
              <div class="box-body box-profile">
                <button id="btn_ConyInsert" type="button" class="btn btn-primary btn-sm" onclick="javascript:Persona.openBuscar('VerifyConyuge',1,0);">Agregar Conyuge</button>
                <button id="btn_ConyDelete" type="button" class="btn btn-danger btn-sm" onclick="javascript:appConyugeEditQuitarCony();">Quitar Conyuge</button>
              </div>
              <div id="div_SocConyuge" class="box-body">
                <div class="col-md-6">
                  <div class="box-body">
                    <strong><i class="fa fa-user margin-r-5"></i> Basicos</strong>
                    <p class="text-muted">
                      <input type="hidden" id="hid_ConyID" value="">
                      Nombres: <a id="lbl_ConyNombres"></a><br>
                      Apellidos: <a id="lbl_ConyApellidos"></a><br>
                      <span id="lbl_ConyTipoDNI"></span>: <a id="lbl_ConyNroDNI"></a><br>
                      Fecha Nac: <a id="lbl_ConyFechaNac"></a><br>
                      Estado Civil: <a id="lbl_ConyEcivil"></a>
                    </p>
                    <hr>

                    <strong><i class="fa fa-phone margin-r-5"></i> Contacto</strong>
                    <p class="text-muted">
                      Celular: <a id="lbl_ConyCelular"></a><br>
                      Telefono Fijo: <a id="lbl_ConyTelefijo"></a><br>
                      Correo: <a id="lbl_ConyEmail"></a><br>
                    </p>
                    <hr>

                    <strong><i class="fa fa-graduation-cap margin-r-5"></i> Profesionales</strong>
                    <p class="text-muted">
                      Grado Instruccion: <a id="lbl_ConyGInstruccion"></a><br>
                      Ocupacion: <a id="lbl_ConyOcupacion"></a>
                    </p>
                    <hr>

                    <strong><i class="fa fa-map-marker margin-r-5"></i> Ubicacion</strong>
                    <p class="text-muted">
                      <a id="lbl_ConyUbicacion"></a><br>
                      Direccion: <a id="lbl_ConyDireccion"></a><br>
                      Referencia: <a id="lbl_ConyReferencia"></a><br>
                      Medidor de Luz: <a id="lbl_ConyMedidorluz"></a><br>
                      Tipo de Vivienda: <a id="lbl_ConyTipovivienda"></a><br><br>
                      <button type="button" class="btn btn-primary btn-xs" onclick="javascript:Persona.editar($('#hid_ConyID').val(),'C');">Modificar Datos</button>
                    </p>
                    <hr>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="box-body">
                    <strong><i class="fa fa-truck margin-r-5"></i> Laborales</strong>
                    <div id="div_ConyCondicion" class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:85px;">Condicion</span>
                        <select id="cbo_ConyCondicion" name="cbo_ConyCondicion" class="form-control selectpicker">
                          <option value="1">Dependiente</option>
                          <option value="0" selected>Independiente</option>
                        </select>
                      </div>
                    </div>
                    <div id="div_ConyEmpresa" class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon">Empresa</span>
                        <input id="txt_ConyEmpresa" name="txt_ConyEmpresa" type="text" class="form-control"/>
                      </div>
                    </div>
                    <div id="div_ConyEmprRUC" class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:81px;">RUC</span>
                        <input id="txt_ConyEmprRUC" name="txt_ConyEmprRUC" type="text" class="form-control"/>
                      </div>
                    </div>
                    <div id="div_ConyEmprFono" class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:81px;">Telefono</span>
                        <input id="txt_ConyEmprFono" name="txt_ConyEmprFono" type="text" class="form-control"/>
                      </div>
                    </div>
                    <div id="div_ConyEmprRubro" class="form-group" style="margin-bottom:25px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:81px;">Rubro</span>
                        <input id="txt_ConyEmprRubro" name="txt_ConyEmprRubro" type="text" class="form-control"/>
                      </div>
                    </div>
                    <div id="div_ConyEmprRegion" class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon" title="Region"><i class="fa fa-map-o"></i></span>
                        <select id="cbo_ConyEmprRegion" name="cbo_ConyEmprRegion" class="form-control selectpicker" onchange="javascript:appComboUbiGeo('#cbo_ConyEmprProvincia',this.value,0);"></select>
                      </div>
                    </div>
                    <div id="div_ConyEmprProvincia" class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon" title="Provincia"><i class="fa fa-map-o"></i></span>
                        <select id="cbo_ConyEmprProvincia" name="cbo_ConyEmprProvincia" class="form-control selectpicker" onchange="javascript:appComboUbiGeo('#cbo_ConyEmprDistrito',this.value,0);"></select>
                      </div>
                    </div>
                    <div id="div_ConyEmprDistrito" class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon" title="Distrito"><i class="fa fa-map-o"></i></span>
                        <select id="cbo_ConyEmprDistrito" name="cbo_ConyEmprDistrito" class="form-control selectpicker"></select>
                      </div>
                    </div>
                    <div id="div_ConyEmprDireccion" class="form-group" style="margin-bottom:25px;">
                      <div class="input-group">
                        <span class="input-group-addon" title="Direccion"><i class="fa fa-map-marker"></i></span>
                        <input id="txt_ConyEmpreDireccion" name="txt_ConyEmpreDireccion" type="text" class="form-control" placeholder="Direccion..."/>
                      </div>
                    </div>
                    <div id="div_ConyEmprCargo" class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:85px;">Cargo</span>
                        <input id="txt_ConyEmpreCargo" name="txt_ConyEmpreCargo" type="text" class="form-control" placeholder="Cargo..."/>
                      </div>
                    </div>
                    <div id="div_ConyEmprIngreso" class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:85px;">Ingreso</span>
                        <input id="txt_ConyEmprIngreso" name="txt_ConyEmprIngreso" type="text" class="form-control" placeholder="S/. 0.00" value="0.00" style="width:125px;"/>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div id="datosLaboral" class="tab-pane">

            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
  <div class="modal fade" id="modalPers" role="dialog"></div>
</section>

<script src="app/js/avales.js"></script>
<script>
  $(document).ready(function(){
    Persona.addModalToParentForm('modalPers');
    appSociosGridReset();
  });
</script>
