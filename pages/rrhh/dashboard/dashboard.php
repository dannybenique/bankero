<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Dashboard</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Dashboard</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <!-- Empleados -->
    <div class="col-lg-3 col-xs-6">
      <div class="small-box bg-yellow">
        <div class="inner">
          <h3 id="appEmpleados"><sup style="font-size:20px">00</sup></h3>
          <p>Colaboradores</p>
        </div>
        <div class="icon">
          <i class="ion ion-person"></i>
        </div>
        <a href="javascript:appSubmitButton('rrhhWorkers');" class="small-box-footer">Mas info <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- Vacaciones -->
    <div class="col-lg-3 col-xs-6">
      <div class="small-box bg-aqua">
        <div class="inner">
          <h3 id="appVacaciones"><sup style="font-size:20px">00</sup></h3>
          <p>Vacaciones</p>
        </div>
        <div class="icon">
          <i class="ion ion-android-clipboard"></i>
        </div>
        <a href="javascript:appSubmitButton('rrhhVacac');" class="small-box-footer">Mas info <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- Renovacion Contrato -->
    <div class="col-lg-3 col-xs-6">
      <div class="small-box bg-blue">
        <div class="inner">
          <h3 id="appRenovacion"><sup style="font-size:20px">00</sup></h3>
          <p>Renovaciones</p>
        </div>
        <div class="icon">
          <i class="ion ion-bonfire"></i>
        </div>
        <a href="javascript:appSubmitButton('rrhhRenov');" class="small-box-footer">Mas info <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- colocaciones -->
    <div class="col-lg-3 col-xs-6">
      <div class="small-box bg-green">
        <div class="inner">
          <h3 id="appColocaciones"><sup style="font-size:20px">00</sup></h3>
          <p>Colocaciones</p>
        </div>
        <div class="icon">
          <i class="ion ion-social-usd"></i>
        </div>
        <a href="javascript:appSubmitButton('operaColocaciones');" class="small-box-footer">Mas info <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- Cumpleaños -->
    <div class="col-lg-3 col-xs-6">
      <div class="small-box bg-purple">
        <div class="inner">
          <h3 id="appCumple"><sup style="font-size:20px">00</sup></h3>
          <p>Cumple</p>
        </div>
        <div class="icon">
          <i class="ion ion-bonfire"></i>
        </div>
        <a href="javascript:appSubmitButton('miscCumple');" class="small-box-footer">Mas info <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
  </div>
</section>

<script src="pages/rrhh/dashboard/dashboard.js"></script>
<script>
  $.widget.bridge('uibutton', $.ui.button);
  $(document).ready(function(){
    rrhhDashBoard();
  });
</script>
