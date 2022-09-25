<!-- exportar a excel -->
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
              <button type="button" class="btn btn-default btn-sm" title="Cartera de aportes" onclick="javascript:appDownloadAportes();"><i class="fa fa-download"></i> Aportes</button>
              <button type="button" class="btn btn-default btn-sm" title="Cartera de ahorros" onclick="javascript:appDownloadAhorros();"><i class="fa fa-download"></i> Ahorros</button>
              <button type="button" class="btn btn-default btn-sm" title="Cartera de creditos" onclick="javascript:appDownloadCreditos();"><i class="fa fa-download"></i> Creditos</button>
              <button type="button" class="btn btn-default btn-sm" title="Cartera de creditos" onclick="javascript:appDownloadOperaciones();"><i class="fa fa-download"></i> Operaciones</button>
            </div>
            <div class="btn-group">
              <button type="button" class="btn btn-success btn-sm" title="Descargar Saldos aportes y ahorros vs. movimientos" onclick="javascript:appDownloadSaldos();"><i class="fa fa-download"></i> Saldos</button>
              <select id="cboAgencias" class="btn btn-success btn-sm" style="height:30px;"></select>
            </div>
            <div class="btn-group">
              <button type="button" class="btn btn-default btn-sm" title="arreglar saldos" onclick="javascript:appArregloAportes(0);"><i class="fa fa-eye"></i> Saldos con problemas</button>
              <button type="button" class="btn btn-default btn-sm" title="arreglar saldos" onclick="javascript:appArregloAportes(1);"><i class="fa fa-bomb"></i> Arreglar Saldos</button>
            </div>
            <span id="grdDatosCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
              <table class="table table-hover" id="grdDatos">
                <thead>
                  <tr>
                    <th style="width:80px;">Codigo</th>
                    <th style="width:400px;">Socio</th>
                    <th style="">Servicio</th>
                    <th style="width:50px;" title="Cuotas">Cuo</th>
                    <th style="width:150px;text-align:right;">Importe</th>
                    <th style="width:150px;text-align:right;">Saldo</th>
                    <th style="width:150px;text-align:right;">Sumatoria</th>
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

<script src="pages/coopsud/carlos/carlos.js"></script>
<script>
  appReset();
</script>
