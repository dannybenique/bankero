<!-- export Excel -->
<script src="plugins/excel-export/xlsx.core.min.js"></script>
<script src="plugins/excel-export/FileSaver.js"></script>
<script src="plugins/excel-export/jhxlsx.js"></script>

<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<section class="content-header">
  <h1><i class="fa fa-tasks"></i> CoopSUD Prestamos</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Cancelados</li>
  </ol>
</section>
<section class="content">
  <div class="row" id="grid">
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header no-padding">
          <div class="mailbox-controls">
            <div class="btn-group">
              <select id="cboAgencias" class="btn btn-default btn-sm" style="height:30px;" onchange="javascript:appGridAll();"></select>
            </div>
            <div class="btn-group">
              <select id="cboTipo" class="btn btn-default btn-sm" style="height:30px;" onchange="javascript:appComboTipo();">
                <option value="1">Codigo</option>
                <option value="2">DNI</option>
              </select>
            </div>
            <div id="divBuscar" class="btn-group" style="display:none;">
              <input type="text" id="txtBuscar" name="txtBuscar" class="form-control input-sm pull-right" placeholder="cod-socio..." onkeypress="javascript:appPrestamosBuscar(event);" style="text-transform:uppercase;" autocomplete="off">
              <span class="fa fa-search form-control-feedback"></span>
            </div>
            <span id="grdDatosCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
              <table class="table table-hover" id="grdDatos">
                <thead>
                  <tr>
                    <th style="width:30px;"><i class="fa fa-files-o" title="Formatos..."></i></th>
                    <th style="width:35px;" title="Agencia de Desembolso">Ag.</th>
                    <th style="width:80px;">Codigo</th>
                    <th style="width:80px;">DNI</th>
                    <th style="">Socio</th>
                    <th style="">Servicio</th>
                    <th style="width:80px;text-align:center;" title="Fecha de Otorgamiento">Fecha</th>
                    <th style="width:40px;text-align:center;" title="Cuotas">Cuo</th>
                    <th style="width:120px;text-align:right;">Importe</th>
                    <th style="width:120px;text-align:right;">Saldo</th>
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
      <form class="form-horizontal" autocomplete="off">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title fontFlexoRegular">Datos Prestamo</h3>
          </div>
          <div class="box-body">
            <strong><i class="fa fa-user margin-r-5"></i> Socio</strong><br/>
            <input id="hid_FactorMora" type="hidden" value=""/>
            Socio: <a id="lbl_socio"></a><br>
            DNI: <a id="lbl_DNI"></a><br>
            Codigo: <a id="lbl_codigo"></a><br>
            Direcc: <a id="lbl_direccion"></a><br>
            <button type="button" id="btn_Garantes" class="btn btn-default btn-xs" onclick="javascript:appBotonGarantes();">Garantes</button>
            <span id="secc_admin"></span> <br><br>

            <strong><i class="fa fa-signal margin-r-5"></i> Prestamo</strong><br/>
            Nº Prestamo: <a id="lbl_numpres"></a><br>
            Tipo_Serv: <a id="lbl_tiposerv"></a><br>
            Servicio: <a id="lbl_servicio"></a><br>
            Fecha Otorg.: <a id="lbl_fecha"></a><br>
            Cuotas: <a id="lbl_cuotas"></a><br>
            Importe: <a id="lbl_importe"></a><br>
            Saldo: <a id="lbl_saldo"></a><br>
            Agencia: <a id="lbl_agencia"></a><br>
            Promotor: <a id="lbl_promotor"></a><br>
            Analista: <a id="lbl_analista"></a><br><br>

            <strong><i class="fa fa-paypal margin-r-5"></i> Saldos</strong>
            <table class="table" id="grdSaldos">
              <thead>
                <tr>
                  <th style="">Detalle</th>
                  <th style="width:80px;text-align:right;">Saldo</th>
                </tr>
              </thead>
              <tbody id="grdSaldosBody">
              </tbody>
            </table>
            <br>
            <span style="color:red;">*OJO: Los datos SOLO son referenciales</span><br>
            <span style="color:red;">*Este cronograma NO aplica a los creditos con GPS, SEG. Vehicular o Guardiania</span>
            <hr>
            <button type="button" class="btn btn-default" onclick="javascript:appBotonCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
          </div>
        </div>
      </form>
    </div>
    <div class="col-md-9">
      <div class="box box-primary">
        <div class="box-body">
          <div class="box-body table-responsive no-padding">
            <button type="button" id="btn_Recargar" class="btn btn-default btn-sm" onclick="javascript:appRefresh();" title="recargar datos de prestamo"><i class="fa fa-refresh"></i></button>
            <?php if($_SESSION['usr_usernivelID']==701){ //super?>
            <button type="button" id="btn_CambiarFechaUnMesMas" class="btn btn-default btn-sm" onclick="javascript:appCambiarFechaUnMesMas();" title="cambiar fecha al siguiente mes"><i class="fa fa-database"></i></button>
            <button type="button" id="btn_RedistribuirInteres" class="btn btn-default btn-sm" onclick="javascript:appRedistribuirInteres();" title="Redistribuir el interes entre las cuotas restantes NO pagadas"><i class="fa fa-share-alt"></i></button>
            <button type="button" id="btn_UpdateSoftia" class="btn btn-default btn-sm" onclick="javascript:appUpdateSoftia();" title="Actualizar capital,interes,desgr desde CoopSUD.dbo.danny_prestamos_det"><i class="fa fa-rocket"></i></button>
            <?php } ?>
            <button type="button" id="btn_DownloadCronograma" class="btn btn-default btn-sm" onclick="javascript:appDownloadCronograma();" title="Descargar el cronograma"><i class="fa fa-download"></i></button>
            <span id="tipoCreditoSpan"></span>
            <table class="table table-hover" id="grdPrestamos">
                <thead>
                  <tr>
                    <th style="width:30px;"><input type="checkbox" id="allCheck" onclick="toggleAll(this,'chk_Borrar');"/></th>
                    <th style="width:30px;">Nro</th>
                    <th style="width:80px;">Vcmto</th>
                    <th style="width:95px;text-align:right;">Total</th>
                    <th style="width:95px;text-align:right;">Capital</th>
                    <th style="width:95px;text-align:right;">Interes</th>
                    <th style="width:80px;text-align:right;">Mora</th>
                    <th style="width:80px;text-align:right;">Desgr</th>
                    <th style="width:100px;text-align:right;">Saldo</th>
                    <th style="width:80px;text-align:center;">Atraso</th>
                    <th style="width:80px;">Pago</th>
                    <th style="" title="<agencia>.<ventanilla>.<num_trans>">Doc. Pago</th>
                  </tr>
                </thead>
                <tbody id="grdPrestamosBody">
                </tbody>
              </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row" id="formatos" style="display:none;">
    <div class="col-md-4">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title fontFlexoRegular">Datos Socio</h3>
        </div>
        <div class="box-body">
          <div class="box-body">
            <strong><i class="fa fa-user margin-r-5"></i> Socio</strong><br/>
            Socio: <a id="lbl_FormaSocio"></a><br>
            DNI/RUC: <a id="lbl_FormaDNI"></a><br>
            Codigo: <a id="lbl_FormaCodSocio"></a><br><br>

            <strong><i class="fa fa-signal margin-r-5"></i> Prestamo</strong><br/>
            Nº Prestamo: <a id="lbl_FormaNumpres"></a><br>
            Tipo_Serv: <a id="lbl_FormaTiposerv"></a><br>
            Servicio: <a id="lbl_FormaServicio"></a><br>
            Fecha Otorg.: <a id="lbl_FormaFecha"></a><br>
            Cuotas: <a id="lbl_FormaCuotas"></a><br>
            Importe: <a id="lbl_FormaImporte"></a><br>
            Saldo: <a id="lbl_FormaSaldo"></a><br>
            Promotor: <a id="lbl_FormaPromotor"></a><br>
            Analista: <a id="lbl_FormaAnalista"></a><br><br>
          </div>
          <div class="row">
            <div class="col-md-6">
              <button type="button" class="btn btn-default btn-sm" onclick="javascript:appBotonCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
            </div>
            <div class="col-md-6">
              <input type="hidden" id="hid_FormUrlServer" value="<?php echo ($webconfig->getURL());?>"/>
              <button type="button" class="btn btn-primary btn-sm pull-right" onclick="javascript:appFormatosGenerarPDF('ResumenCredito');" title="Generar el Resumen de Credito"><i class="fa fa-file-pdf-o"></i> Resumen de Credito</button>
              <button type="button" class="btn btn-primary btn-sm pull-right" style="margin-top:5px;" onclick="javascript:appFormatosGenerarPDF('CartaAutorizaGarLiquida');" title="Generar Carta de Autorizacion - Garantia Liquida"><i class="fa fa-file-pdf-o"></i> Carta de Autorizacion - Garatia Liquida</button>
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
  <div class="modal fade" id="modalCambiarVariasFechas_Cuotas" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" style="background:#f9f9f9;padding:8px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title fontFlexoRegular">Cambiar varias cuotas a la fecha (congelamiento de cuotas)</h4>
        </div>
        <form class="form-horizontal" id="frmCambiarFechaCuotas" autocomplete="off">
          <div class="modal-body no-padding">
            <div class="box-body">
              <table class="table no-border">
                <tr>
                  <td style="width:30%"></td>
                  <td>
                    <div class="input-group">
                      <span class="input-group-addon" style="background:#f5f5f5;">Fecha de Cambio</span>
                      <input id="txt_FechaCambio" name="txt_FechaCambio" type="text" class="form-control" style="width:105px;"/>
                    </div>
                  </td>
                  <td style="width:30%"></td>
                </tr>
              </table>
            </div>
          </div>
          <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
            <button type="button" class="btn btn-success" onclick="javascript:appCambiarVencimiento();"><i class="fa fa-calendar-check-o"></i> Cambiar Fecha</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalMovimientos" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" style="background:#f9f9f9;padding:8px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title fontFlexoRegular">Movimientos</h4>
        </div>
        <div class="modal-body no-padding" style="border-right:1px solid white;">
          <div class="box-body">
            <div class="pull-right">Fecha: <span id="modMovimFecha"></span></div>
            <div>Num_Trans: <span id="modMovimNumTrans"></span></div>
            <div>Agencia: <span id="modMovimAgencia"></span></div>
            <div>Ventanilla: <span id="modMovimVentanilla"></span></div>
            <br>
            <table class="table">
              <thead>
                <tr>
                  <th>Detalle</th>
                  <th style="width:80px;text-align:right;">Pagos</th>
                  <th style="width:80px;text-align:right;">Saldos</th>
                </tr>
              </thead>
              <tbody id="grdMovimientosBody">
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
          <button type="button" class="btn btn-default btn-sm pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalGarantes" role="dialog">
    <div class="modal-dialog" style="width:90%;">
      <div class="modal-content">
        <div class="modal-header" style="background:#f9f9f9;padding:8px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><b>Garantes</b></h4>
        </div>
        <div class="modal-body">
          <div class="box-body table-responsive no-padding">
            <table class="table table-hover" id="grdGarantes">
              <thead>
                <tr>
                  <th style="width:80px;">DNI</th>
                  <th style="">Garante</th>
                  <th style="width:120px;">Telefonos</th>
                  <th style="">Direccion</th>
                </tr>
              </thead>
              <tbody id="grdGarantesBody">
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
          <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalConfigCred" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" style="background:#f9f9f9;padding:8px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><b>Configuracion del Prestamo</b></h4>
        </div>
        <div class="modal-body">
          <div class="box-body table-responsive no-padding">
            <table class="table table-hover">
              <tr>
                <th style="width:130px;line-height:30px;">Codigo Prestamo</th>
                <td style="line-height:30px;"><span id="modConfigCodPrestamo"></span></td>
              </tr>
              <tr>
                <th>Condicion</th>
                <td><select id="modConfigComboCondicion" class="btn btn-default btn-sm" style="height:30px;" onchange="javascript:openModalConfigCredComboCondicion();">
                      <option value="N">Normal</option>
                      <option value="R">Reprogramado</option>
                      <option value="C">Reprogramado COVID</option>
                      <option value="P">Paralelo</option>
                      <option value="D">Prejudicial</option>
                      <option value="J">Judicial</option>
                      <option value="O">Condonado</option>
                      <option value="S">Castigado</option>
                      <option value="A">Ampliado</option>
                      <option value="F">Refinanciado</option>
                    </select></td>
              </tr>
              <tr>
                <th>Estado</th>
                <td><select id="modConfigComboEstado" class="btn btn-default btn-sm" style="height:30px;"></select></td>
              </tr>
            </table>
          </div>
        </div>
        <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
          <button type="button" class="btn btn-primary btn-sm" onclick="javascript:openModalConfigCredGuardarCambios();"><i class="fa fa-flash"></i> Actualizar</button>
          <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="pages/coopsud/prestamos/prestamos.js"></script>
<script>
  $(document).ready(function(){
    appGridReset();
  });
</script>
