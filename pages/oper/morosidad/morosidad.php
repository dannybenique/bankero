<!-- export Excel -->
<script src="plugins/excel-export/xlsx.core.min.js"></script>
<script src="plugins/excel-export/FileSaver.js"></script>
<script src="plugins/excel-export/jhxlsx.js"></script>

<!-- ChartJS -->
<script src="libs/chart.js/chart.min.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-pie-chart"></i>Reporte de Morosidad</h1>
  <ol class="breadcrumb">
    <li><i class="fa fa-dashboard"></i> Home</li>
    <li>Reportes</li>
    <li class="active">Morosidad</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <div class="row" id="grid">
    <div class="col-xs-12">
      <div class="box box-primary">
        <div class="box-header no-padding">
          <div class="mailbox-controls">
            <?php if($_SESSION['usr_usernivelID']==701){ //solo superadmin ?>
              <div class="btn-group">
                <button type="button" id="btnCierre" style="display:none;" class="btn btn-default btn-sm" title="Ejecutar cierre de cartera" onclick="javascript:appSetMorosidadCierre();"><i class="fa fa-heartbeat"></i></button>
              </div>
            <?php  } ?>
            <div class="btn-group">
                <input id='hid_analistaID' type='hidden' value="<?php echo $_SESSION['usr_ID'];?>" />
                <button type="button" class="btn btn-default btn-sm" title="Mensaje - Whatsapp Mora preventiva" onclick="javascript:appGetMorosidadDatosUsuario();"><i class="fa fa-whatsapp"></i></button>
              <?php if($_SESSION['usr_usernivelID']<=711){ //solo superadmin,jefaturas ?>
                <button type="button" class="btn btn-default btn-sm" title="Descargar Morosidad de esta agencia" onclick="javascript:appGetMorosidadSociosDownload();"><i class="fa fa-download"></i></button>
                <button type="button" class="btn btn-default btn-sm" title="Descargar Preventivo de esta agencia" onclick="javascript:appGetPreventivoSociosDownload();"><i class="fa fa-file-excel-o"></i></button>
              <?php  } ?>
              <button type="button" class="btn btn-default btn-sm" title="Volver a cargar los datos" onclick="javascript:appGetMorosidad();"><i class="fa fa-refresh"></i></button>
            </div>
            <div class="btn-group">
              <select id="cboAgencias" class="btn btn-default btn-sm" style="height:30px;text-align:left;" onchange="javascript:appGetMorosidad();"></select>
            </div>
            <span id="grdDatosCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
              <table class="table table-hover" id="grdDatos">
                <thead>
                  <tr>
                    <th style="width:60px;">Codigo</th>
                    <th style="width:30px;"><i class="fa fa-area-chart"></i></th>
                    <th>Empleado</th>
                    <th style="width:160px;">Cargo</th>
                    <th style="width:50px;text-align:right;">Ini</th>
                    <th style="width:50px;text-align:right;">Hoy</th>
                    <th style="width:120px;text-align:right;" title="Total Vencido al Inicio">Saldo Ini</th>
                    <th style="width:120px;text-align:right;" title="TOTAL saldo al dia de hoy">Saldo Hoy</th>
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
            <h3 class="box-title fontFlexoRegular">Datos Mora Preventiva</h3>
          </div>
          <div class="box-body">
            <strong><i class="fa fa-user margin-r-5"></i> Usuario</strong><br/>
            Usuario: <a id="lbl_usuario"></a><br>
            Codigo: <a id="lbl_codigo"></a><br>
            Agencia: <a id="lbl_agencia"></a><br><br>
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
            <button type="button" id="btn_Recargar" class="btn btn-default btn-sm" onclick="javascript:appRefresh();" title="recargar datos de ahorro"><i class="fa fa-refresh"></i></button>
            <table class="table table-hover" id="grdMoraPreven">
                <thead>
                  <tr>
                    <th style="width:70px;" title="Codigo del Socio">Codigo</th>
                    <th style="width:100px;">Telefono</th>
                    <th style="" title="Socio">Socio</th>
                    <th style="" title="Servicio">Servicio</th>
                    <th style="width:30px;text-align:center;" title="Cuota por Vencer">CV</th>
                    <th style="width:30px;text-align:center;" title="Total de Cuotas">CT</th>
                    <th style="width:100px;text-align:right;" title="Monto Total">TT</th>
                    <th style="width:120px;text-align:left;" title="Mensaje">Mensaje</th>
                  </tr>
                </thead>
                <tbody id="grdMoraPrevenBody">
                </tbody>
              </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalMorosidad" role="dialog">
    <div class="modal-dialog" style="width:90%;">
      <div class="modal-content">
        <div class="modal-header" style="background:#f9f9f9;padding:8px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><b>MOROSIDAD</b><span id="lbl_SaldoMora" class="label bg-red" style="display:inline-block;margin-left:5px;font-size:14px;font-weight:500;">Saldo Total: 0.00</span></h4>
        </div>
        <div class="modal-body">
          <div class="box-body table-responsive no-padding">
            <table class="table table-hover" id="grdMora">
              <thead>
                <tr>
                  <th style="width:80px;">Codigo</th>
                  <th>Socio</th>
                  <th>Direccion</th>
                  <th style="width:80px;">Telefono</th>
                  <th style="width:50px;" title="Numero de prestamo">pres</th>
                  <th style="width:50px;text-align:right;" title="Dias de Mora">Atraso</th>
                  <th style="width:80px;text-align:right;">Importe</th>
                  <th style="width:80px;text-align:right;">Saldo</th>
                  <th style="width:80px;text-align:right;">Cuota</th>
                  <th style="width:80px;text-align:right;">Interes</th>
                  <th style="width:80px;text-align:right;">Moratorio</th>
                  <th style="width:80px;text-align:right;" title="Seguro de Desgravamen">Desg</th>
                  <th style="width:80px;text-align:right;" title="Gastos Administrativos">Gastos</th>
                  <th style="width:80px;text-align:right;" title="Gastos Judiciales">Judicial</th>
                  <th style="width:80px;text-align:right;">Total</th>
                </tr>
              </thead>
              <tbody id="grdMoraBody">
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
  <div class="modal fade" id="modalGrafiMorosidad" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" style="background:#f9f9f9;padding:8px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="modal-title">GRAFICO DE MOROSIDAD</h4>
        </div>
        <div class="modal-body">
          <div class="box-body table-responsive no-padding">
            <div id="div_canvas"></div>
          </div>
        </div>
        <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
          <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="pages/oper/morosidad/morosidad.js"></script>
<script>
  $(document).ready(function(){
    appMoraReset();
  });
</script>
