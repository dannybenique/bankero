<?php
  include_once('../../../includes/sess_verifica.php');
  if(isset($_SESSION['usr_ID'])) {
?>
<div class="modal-dialog modal-lg">
  <div class="modal-content">
    <div class="modal-header" style="background:#f9f9f9;padding:8px;">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 id="modPersTitulo" class="modal-title fontFlexoRegular">Datos Personales</h4>
    </div>
    <div class="modal-body">
      <div class="row" id="modPersFormGrid">
        <div class="col-md-12">
            <div>
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon no-border">Nro Documento</span>
                  <input type="number" id="txt_modPersBuscar" class="form-control" placeholder="DNI, RUC..." onkeypress="javascript:Persona.keyBuscar(event);">
                  <div class="input-group-btn">
                    <button type="button" class="btn btn-primary" onclick="javacript:Persona.buscar();"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
              <div class="box-body table-responsive no-padding">
                <span id="lbl_modPersWait"></span>
                <div id="modPersGridDatosTabla">
                  <ul class="todo-list">
                    <li style="height:60px;">
                      <div class="pull-left"><img class="img-circle" id="img_PersGridDatosTabla" src="data/personas/images/0noFotoUser.jpg" style="height:40px;width:40px;"/></div>
                      <div class="pull-left" style="margin-left:10px;">
                        <a href="#" class="product-title">
                          <div id="lbl_modPersDNI"></div>
                        </a>
                        <span id="lbl_modPersPersona" class="product-description"></span>
                      </div>
                      <div class="pull-right">
                        <button id="btn_modPersAddToForm" type="button" class="btn btn-success btn-sm" style="margin-top:5px;display:none;"><i class="fa fa-flash"></i> Agregar</button>
                        <button id="btn_modPersNuevo" type="button" class="btn btn-primary btn-sm" style="margin-top:5px;display:none;" onclick="Persona.nuevo();"><i class="fa fa-plus"></i> Agregar</button>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
        </div>
      </div>
      <div class="row" id="modPersFormEdit">
        <div class="box-body" style="padding-top:0;padding-bottom:0;">
          <form class="form-horizontal" autocomplete="off">
            <div class="col-md-6">
              <div class="box-body">
                <div class="form-group" style="margin-bottom:15px;">
                  <div class="input-group">
                    <span class="input-group-addon" style="background:#aaa;color:#555;border:1px solid #999;">PERSONA</span>
                    <select id="cbo_modPersTipoPers" class="form-control selectpicker">
                      <option value="1">NATURAL</option>
                      <option value="2">JURIDICA</option>
                    </select>
                    <script type="text/javascript">
                      $("#cbo_modPersTipoPers").change(function(){
                        if(this.options[this.selectedIndex].value==2){ //persona juridica
                          $("#div_modPersApePaterno").hide();
                          $("#div_modPersApeMaterno").hide();
                          $("#div_modPersGinstruc").hide();
                          $("#div_modPersEcivil").hide();
                          $("#div_modPersSexoEcivil").hide();
                          $("#cbo_modPersDocumento").prop('disabled','disabled');
                          $("#txt_modPersNombres").prop('placeholder','RAZON SOCIAL');
                          $("#txt_modPersApePaterno").val("");
                          $("#txt_modPersApeMaterno").val("");
                          $("#cbo_modPersGinstruc").get(0).selectedIndex=0;
                          $("#cbo_modPersEcivil").get(0).selectedIndex=0;
                          $("#cbo_modPersSexo").get(0).selectedIndex=0;
                          $("#cbo_modPersDocumento").val(502);
                        } else {
                          $("#txt_modPersNombres").prop('placeholder','NOMBRES');
                          $("#div_modPersApePaterno").show();
                          $("#div_modPersApeMaterno").show();
                          $("#div_modPersGinstruc").show();
                          $("#div_modPersEcivil").show();
                          $("#div_modPersSexoEcivil").show();
                          $("#cbo_modPersDocumento").removeAttr('disabled');
                          $("#cbo_modPersDocumento").val(501);
                        }
                      });
                    </script>
                  </div>
                </div>
                <div id="div_modPersNombres" class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" title="Nombres"><i class="fa fa-user" style="width:15px;"></i></span>
                    <input id="txt_modPersNombres" type="text" class="form-control" placeholder="Nombres..." style="text-transform:uppercase;"/>
                  </div>
                </div>
                <div id="div_modPersApePaterno" class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" title="Apellido Paterno"><i class="fa fa-users" style="width:15px;"></i></span>
                    <input id="txt_modPersApePaterno" type="text" class="form-control" placeholder="Apellido Paterno..." style="text-transform:uppercase;"/>
                  </div>
                </div>
                <div id="div_modPersApeMaterno" class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" title="Apellido Materno"><i class="fa fa-users" style="width:15px;"></i></span>
                    <input id="txt_modPersApeMaterno" type="text" class="form-control" placeholder="Apellido Materno..." style="text-transform:uppercase;"/>
                  </div>
                </div>
                <div id="div_modPersDocumento" class="form-group" style="margin-bottom:5px;">
                  <div class="col-sm-7" style="padding-left:0;padding-right:0;">
                    <div class="input-group">
                      <span class="input-group-addon" title="Documento de Identidad"><i class="fa fa-credit-card" style="width:15px;"></i></span>
                      <select id="cbo_modPersDocumento" class="form-control selectpicker"></select>
                    </div>
                  </div>
                  <div class="col-sm-5" style="padding-left:0;padding-right:0;">
                    <input id="txt_modPersDocumento" type="number" class="form-control" placeholder="nro Doc. Identidad..." disabled="disabled" onblur="appSetTexto('#lbl_DNI','#txt_PersDocumento',false);">
                  </div>
                </div>
                <div id="div_PersCelular" class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" title="Celular"><i class="fa fa-phone" style="width:15px;"></i></span>
                    <input id="txt_modPersCelular" type="text" class="form-control" placeholder="Nro. Celular" onblur="appSetTexto('#lbl_celular','#txt_PersCelular',false);"/>
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" title="Telefono Fijo"><i class="fa fa-tty" style="width:15px;"></i></span>
                    <input id="txt_modPersFijo" type="text" class="form-control" placeholder="Nro. Telefono Fijo" />
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" title="Correo Electronico"><i class="fa fa-envelope" style="width:15px;"></i></span>
                    <input id="txt_modPersEmail" type="text" class="form-control" placeholder="correo@..." style="text-transform:lowercase;"/>
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" title="Profesion/Rubro"><i class="fa fa-briefcase" style="width:15px;"></i></span>
                    <input id="txt_modPersProfesion" type="text" class="form-control" placeholder="profesion / rubro..." style="text-transform:uppercase;"/>
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" title="Ocupacion"><i class="fa fa-wrench" style="width:15px;"></i></span>
                    <input id="txt_modPersOcupacion" type="text" class="form-control" placeholder="ocupacion..." style="text-transform:uppercase;"/>
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" title="Lugar de Nacimiento"><i class="fa fa-map-marker" style="width:15px;"></i></span>
                    <input id="txt_modPersLugarnac" type="text" class="form-control" placeholder="lugar de nacimiento..." style="text-transform:uppercase;"/>
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="col-sm-5" style="padding-left:0;padding-right:0;">
                    <div class="input-group">
                      <span class="input-group-addon" title="Fecha de Nacimiento"><i class="fa fa-birthday-cake" style="width:15px;"></i></span>
                      <input id="date_modPersFechanac" type="text" class="form-control" style="width:105px;"/>
                    </div>
                  </div>
                  <div class="col-sm-7" style="padding-left:0;padding-right:0;">
                    <div class="input-group">
                      <span class="input-group-addon" title="Foto"><i class="fa fa-picture-o" style="width:15px;"></i></span>
                      <input type="file" id="file_modPersFoto" class="form-control" accept="image/*" />
                      <input type="hidden" id="hid_modPersUrlFoto" value="">
                    </div>
                  </div>
                </div>
                <div id="div_modPersGinstruc" class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" title="Grado de Instruccion"><i class="fa fa-graduation-cap" style="width:15px;"></i></span>
                    <select id="cbo_modPersGinstruc" class="form-control selectpicker"></select>
                  </div>
                </div>
                <div id="div_modPersSexoEcivil" class="form-group" style="margin-bottom:5px;">
                  <div class="col-sm-6" style="padding-left:0;padding-right:0;">
                    <div class="input-group">
                      <span class="input-group-addon" title="Sexo"><i class="fa fa-venus-mars" style="width:15px;"></i></span>
                      <select id="cbo_modPersSexo" class="form-control selectpicker" style="width:150px;"></select>
                    </div>
                  </div>
                  <div class="col-sm-6" style="padding-left:0;padding-right:0;">
                    <div class="input-group">
                      <span class="input-group-addon" title="Estado Civil"><i class="fa fa-heart" style="width:15px;"></i></span>
                      <select id="cbo_modPersEcivil" class="form-control selectpicker"></select>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="box-body">
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" title="Region"><i class="fa fa-map-o" style="width:15px;"></i></span>
                    <select id="cbo_modPersRegion" class="form-control selectpicker" onchange="javascript:Persona.comboProvincia();"></select>
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" title="Provincia"><i class="fa fa-map-o" style="width:15px;"></i></span>
                    <select id="cbo_modPersProvincia" class="form-control selectpicker" onchange="javascript:Persona.comboDistrito();"></select>
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" title="Distrito"><i class="fa fa-map-o" style="width:15px;"></i></span>
                    <select id="cbo_modPersDistrito" class="form-control selectpicker"></select>
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" title="Direccion"><i class="fa fa-map-marker" style="width:15px;"></i></span>
                    <input id="txt_modPersDireccion" type="text" class="form-control" placeholder="Direccion..." style="text-transform:uppercase;"/>
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" title="Referencia de la dirección"><i class="fa fa-map-marker" style="width:15px;"></i></span>
                    <input id="txt_modPersReferencia" type="text" class="form-control" placeholder="Referencia..." style="text-transform:uppercase;"/>
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="col-sm-6" style="padding-left:0;padding-right:0;">
                    <div class="input-group">
                      <span class="input-group-addon" title="Tipo de Vivienda"><i class="fa fa-home" style="width:15px;"></i></span>
                      <select id="cbo_modPersTipoVivienda" class="form-control selectpicker" style="width:150px;"></select>
                    </div>
                  </div>
                  <div class="col-sm-6" style="padding-left:0;padding-right:0;">
                    <div class="input-group">
                      <span class="input-group-addon" title="Codigo del Medidor de Luz"><i class="fa fa-lightbulb-o" style="width:15px;"></i></span>
                      <input id="txt_modPersMedidor" type="text" class="form-control" placeholder="Cod Medidor Luz..."/>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="input-group">
                    <textarea id="txt_modPersObserv" type="text" placeholder="Observaciones..." cols="100" rows="7" style="width:100%;"></textarea>
                  </div>
                </div>
                <div class="box-body pull-right">
                  <input type="hidden" id="hid_modPersPermisoID" value="">
                  <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
                  <button id="btn_modPersInsert" type="button" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> Guardar</button>
                  <button id="btn_modPersUpdate" type="button" class="btn btn-info btn-sm"><i class="fa fa-save"></i> Actualizar</button>
                  <?php if($_SESSION['usr_usernivelID']==701 || $_SESSION['usr_usernivelID']==705) { //menu solo para super ?>
                  <button id="btn_modPersApidni" type="button" class="btn btn-success btn-sm" onclick="Persona.apidni();"><i class="fa fa-cloud-download"></i></button>
                  <?php } ?>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
  } ?>
