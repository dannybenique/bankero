<!-- exportar a excel -->
<script src="plugins/excel-export/xlsx.core.min.js"></script>
<script src="plugins/excel-export/FileSaver.js"></script>
<script src="plugins/excel-export/jhxlsx.js"></script>

<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<section class="content-header">
  <h1><i class="fa fa-tasks"></i> CoopSUD SUNAT</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">SUNAT</li>
  </ol>
</section>
<section class="content">
  <div class="row" id="grid">
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header no-padding">
          <div class="mailbox-controls">
            <span style="display:inline-block;margin-left:5px;">Desde</span>
            <div class="btn-group">
              <input type="text" id="txtFechaIni" class="form-control input-sm" style="width:105px;">
            </div>
            <span style="display:inline-block;margin-left:5px;">Hasta</span>
            <div class="btn-group">
              <input type="text" id="txtFechaFin" class="form-control input-sm" style="width:105px;">
            </div>
            <div class="btn-group">
              <button type="button" class="btn btn-default btn-sm" title="Cartera de aportes" onclick="javascript:appDownloadFacturas();"><i class="fa fa-download"></i> Facturacion</button>
            </div>
            <span id="grdDatosCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
              <table class="table table-hover" id="grdDatos">
                <thead>
                  <tr>
                    <th style="width:80px;">num_trans</th>
                    <th style="width:400px;">Socio</th>
                    <th style="">Servicio</th>
                    <th style="width:150px;text-align:right;">Total</th>
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
    <div class="col-md-4">
      <form class="form-horizontal" autocomplete="off">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title fontFlexoRegular">Datos Prestamo</h3>
          </div>
          <div class="box-body">
            <strong><i class="fa fa-user margin-r-5"></i> Socio</strong>
            <p class="text-muted">
              Socio: <a id="lbl_socio"></a><br>
              DNI: <a id="lbl_DNI"></a><br>
              Codigo: <a id="lbl_codigo"></a><br>
              Prestamo: <a id="lbl_numpres"></a><input type="hidden" id="hid_tipo_serv" value=""/><br>
              <a id="lbl_servicio"></a>
            </p>
            <button type="button" class="btn btn-default" onclick="javascript:appBotonCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
            <hr>
          </div>
        </div>
      </form>
    </div>
    <div class="col-md-8">
      <div class="box box-primary">
        <div class="box-body">
          <div class="box-body table-responsive no-padding">
            <button type="button" id="btn_Recargar" class="btn btn-default btn-sm" onclick="javascript:appRefresh();" title="recargar datos de prestamo"><i class="fa fa-refresh"></i></button>
            <?php if($_SESSION['usr_usernivelID']!=707){ //riesgos?>
            <button type="button" id="btn_CambiarFechaUnMesMas" class="btn btn-default btn-sm" onclick="javascript:appCambiarFechaUnMesMas();" title="cambiar fecha al siguiente mes"><i class="fa fa-database"></i></button>
            <button type="button" id="btn_PatearInteresAlFinal" style="display:none;" class="btn btn-default btn-sm" onclick="javascript:appPatearInteres_Final();" title="Patear el interes excedente a la ultima cuota"><i class="fa fa-fire-extinguisher"></i></button>
            <button type="button" id="btn_RedistribuirInteres" class="btn btn-default btn-sm" onclick="javascript:appRedistribuirInteres();" title="Redistribuir el interes entre las cuotas restantes NO pagadas"><i class="fa fa-share-alt"></i></button>
            <?php } ?>
            <table class="table table-hover" id="grdPrestamos">
                <thead>
                  <tr>
                    <th style="width:30px;"><input type="checkbox" id="allCheck" onclick="toggleAll(this,'chk_Borrar');"/></th>
                    <th style="width:30px;">Nro</th>
                    <th style="width:80px;">Fecha</th>
                    <th style="width:95px;text-align:right;">Total</th>
                    <th style="width:95px;text-align:right;">Capital</th>
                    <th style="width:95px;text-align:right;">Interes</th>
                    <th style="width:80px;text-align:right;">Desgr</th>
                    <th style="width:100px;text-align:right;">Saldo</th>
                    <th style="width:100px;text-align:center;">Atraso</th>
                    <th style="width:80px;">Pago</th>
                    <th></th>
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
</section>

<script src="pages/coopsud/sunat/sunat.js"></script>
<script>

  appReset();
</script>
