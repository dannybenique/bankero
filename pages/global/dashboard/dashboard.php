<!-- datepicker -->
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>


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
        <a href="javascript:appSubmitButton('operColocaciones');" class="small-box-footer">Mas info <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- Cartera -->
    <div class="col-lg-3 col-xs-6">
      <div class="small-box bg-aqua">
        <div class="inner">
          <h3 id="appCartera"><sup style="font-size:20px">00</sup></h3>
          <p>Cartera</p>
        </div>
        <div class="icon">
          <i class="ion ion-android-clipboard"></i>
        </div>
        <a href="javascript:appSubmitButton('operCartera');" class="small-box-footer">Mas info <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- Morosos -->
    <div class="col-lg-3 col-xs-6">
      <div class="small-box bg-red">
        <div class="inner">
          <h3 id="appMorosos"><sup style="font-size:20px">00</sup></h3>
          <p>Morosidad</p>
        </div>
        <div class="icon">
          <i class="ion ion-android-clipboard"></i>
        </div>
        <a href="javascript:appSubmitButton('operMorosidad');" class="small-box-footer">Mas info <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
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
        <a href="<?php echo(($_SESSION['usr_usernivelID']<=701) ? ("javascript:appSubmitButton('configWorkers');") : ("#"));?>" class="small-box-footer">Mas info <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- Cumpleaños -->
    <div class="col-lg-3 col-xs-6" id="boxcumple" style="display:none;">
      <div class="small-box bg-purple">
        <div class="inner">
          <h3>Cumple</h3>
          <p id="boxcumple_usu"></p>
        </div>
        <div class="icon">
          <i class="ion ion-icecream"></i>
        </div>
        <a href="javascript:appSubmitButton('miscCumple');" class="small-box-footer">Mas info <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
  </div>
  <?php if(1==701){ ?>
  <!-- Reporte Mensual Cartera-->
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title fontFlexoRegular">Reporte Mensual de Prestamos</h3>
        </div>
        <div class="box-body">
          <div class="row">
            <div class="col-md-8">

            </div>
            <div class="col-md-4">
              <p class="text-center">
                <strong>Goal Completion</strong>
              </p>
              <div class="progress-group">
                <span class="progress-text">Add Products to Cart</span>
                <span class="progress-number"><b>160</b>/200</span>

                <div class="progress sm">
                  <div class="progress-bar progress-bar-aqua" style="width: 80%"></div>
                </div>
              </div>
              <div class="progress-group">
                <span class="progress-text">Complete Purchase</span>
                <span class="progress-number"><b>310</b>/400</span>

                <div class="progress sm">
                  <div class="progress-bar progress-bar-red" style="width: 80%"></div>
                </div>
              </div>
              <div class="progress-group">
                <span class="progress-text">Visit Premium Page</span>
                <span class="progress-number"><b>480</b>/800</span>

                <div class="progress sm">
                  <div class="progress-bar progress-bar-green" style="width: 80%"></div>
                </div>
              </div>
              <div class="progress-group">
                <span class="progress-text">Send Inquiries</span>
                <span class="progress-number"><b>250</b>/500</span>

                <div class="progress sm">
                  <div class="progress-bar progress-bar-yellow" style="width: 80%"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- ./box-body -->
        <div class="box-footer">
          <div class="row">
            <div class="col-sm-3 col-xs-6">
              <div class="description-block border-right">
                <span class="description-percentage text-green"><i class="fa fa-caret-up"></i> 17%</span>
                <h5 class="description-header">$35,210.43</h5>
                <span class="description-text">TOTAL REVENUE</span>
              </div>
            </div>
            <div class="col-sm-3 col-xs-6">
              <div class="description-block border-right">
                <span class="description-percentage text-yellow"><i class="fa fa-caret-left"></i> 0%</span>
                <h5 class="description-header">$10,390.90</h5>
                <span class="description-text">TOTAL COST</span>
              </div>
            </div>
            <div class="col-sm-3 col-xs-6">
              <div class="description-block border-right">
                <span class="description-percentage text-green"><i class="fa fa-caret-up"></i> 20%</span>
                <h5 class="description-header">$24,813.53</h5>
                <span class="description-text">TOTAL PROFIT</span>
              </div>
            </div>
            <div class="col-sm-3 col-xs-6">
              <div class="description-block">
                <span class="description-percentage text-red"><i class="fa fa-caret-down"></i> 18%</span>
                <h5 class="description-header">1200</h5>
                <span class="description-text">GOAL COMPLETIONS</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <section class="col-lg-8 connectedSortable">
      <!-- TO DO List -->
      <div class="box box-primary">
        <div class="box-header">
          <i class="ion ion-clipboard"></i>
          <h3 class="box-title">Lista de Tareas</h3>
          <div class="box-tools pull-right">
            <ul class="pagination pagination-sm inline">
              <li><a href="#">&laquo;</a></li>
              <li><a href="#">1</a></li>
              <li><a href="#">2</a></li>
              <li><a href="#">3</a></li>
              <li><a href="#">&raquo;</a></li>
            </ul>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <!-- See dist/js/pages/dashboard.js to activate the todoList plugin -->
          <ul class="todo-list">
            <li>
              <!-- drag handle -->
              <span class="handle">
                <i class="fa fa-ellipsis-v"></i>
                <i class="fa fa-ellipsis-v"></i>
              </span>
              <!-- checkbox -->
              <input type="checkbox" value="">
              <!-- todo text -->
              <span>Design a nice theme</span>
              <!-- Emphasis label -->
              <small class="label label-danger"><i class="fa fa-clock-o"></i> 2 mins</small>
              <!-- General tools such as edit or delete-->
              <div class="tools">
                <i class="fa fa-edit"></i>
                <i class="fa fa-trash-o"></i>
              </div>
            </li>
            <li>
              <span class="handle">
                <i class="fa fa-ellipsis-v"></i>
                <i class="fa fa-ellipsis-v"></i>
              </span>
              <input type="checkbox" value="">
              <span>Make the theme responsive</span>
              <small class="label label-info"><i class="fa fa-clock-o"></i> 4 hours</small>
              <div class="tools">
                <i class="fa fa-edit"></i>
                <i class="fa fa-trash-o"></i>
              </div>
            </li>
            <li>
              <span class="handle">
                <i class="fa fa-ellipsis-v"></i>
                <i class="fa fa-ellipsis-v"></i>
              </span>
              <input type="checkbox" value="">
              <span>Let theme shine like a star</span>
              <small class="label label-warning"><i class="fa fa-clock-o"></i> 1 day</small>
              <div class="tools">
                <i class="fa fa-edit"></i>
                <i class="fa fa-trash-o"></i>
              </div>
            </li>
            <li>
              <span class="handle">
                <i class="fa fa-ellipsis-v"></i>
                <i class="fa fa-ellipsis-v"></i>
              </span>
              <input type="checkbox" value="">
              <span>Let theme shine like a star</span>
              <small class="label label-success"><i class="fa fa-clock-o"></i> 3 days</small>
              <div class="tools">
                <i class="fa fa-edit"></i>
                <i class="fa fa-trash-o"></i>
              </div>
            </li>
            <li>
              <span class="handle">
                <i class="fa fa-ellipsis-v"></i>
                <i class="fa fa-ellipsis-v"></i>
              </span>
              <input type="checkbox" value="">
              <span>Check your messages and notifications</span>
              <small class="label label-primary"><i class="fa fa-clock-o"></i> 1 week</small>
              <div class="tools">
                <i class="fa fa-edit"></i>
                <i class="fa fa-trash-o"></i>
              </div>
            </li>
            <li>
              <span class="handle">
                <i class="fa fa-ellipsis-v"></i>
                <i class="fa fa-ellipsis-v"></i>
              </span>
              <input type="checkbox" value="">
              <span>Let theme shine like a star</span>
              <small class="label label-default"><i class="fa fa-clock-o"></i> 1 month</small>
              <div class="tools">
                <i class="fa fa-edit"></i>
                <i class="fa fa-trash-o"></i>
              </div>
            </li>
          </ul>
        </div>
        <!-- /.box-body -->
        <div class="box-footer clearfix no-border">
          <button type="button" class="btn btn-default pull-right"><i class="fa fa-plus"></i> Add item</button>
        </div>
      </div>
    </section>
    <section class="col-lg-4 connectedSortable">
      <!-- Calendar -->
      <div class="box box-solid bg-green-gradient">
        <div class="box-header">
          <i class="fa fa-calendar"></i>
          <h3 class="box-title">Calendar</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body no-padding">
          <!--The calendar -->
          <div id="calendar" style="width: 100%"></div>
        </div>
        <!-- /.box-body -->
        <div class="box-footer text-black">
          <div class="row">
            <div class="col-sm-6">
              <!-- Progress bars -->
              <div class="clearfix">
                <span class="pull-left">Task #1</span>
                <small class="pull-right">90%</small>
              </div>
              <div class="progress xs">
                <div class="progress-bar progress-bar-green" style="width: 90%;"></div>
              </div>

              <div class="clearfix">
                <span class="pull-left">Task #2</span>
                <small class="pull-right">70%</small>
              </div>
              <div class="progress xs">
                <div class="progress-bar progress-bar-green" style="width: 70%;"></div>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-sm-6">
              <div class="clearfix">
                <span class="pull-left">Task #3</span>
                <small class="pull-right">60%</small>
              </div>
              <div class="progress xs">
                <div class="progress-bar progress-bar-green" style="width: 60%;"></div>
              </div>

              <div class="clearfix">
                <span class="pull-left">Task #4</span>
                <small class="pull-right">40%</small>
              </div>
              <div class="progress xs">
                <div class="progress-bar progress-bar-green" style="width: 40%;"></div>
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div>
      </div>
    </section>
  </div>
  <?php }?>
</section>

<!-- dashboard.js -->
<script src="pages/global/dashboard/dashboard.js"></script>
<script src="app/js/interfaz.js"></script>

<script>
  $(document).ready(function(){ appDashBoard(); });
  $.widget.bridge('uibutton', $.ui.button);
</script>


<?php if($_SESSION['usr_usernivelID']==701){?>
<script>
  // jQuery UI sortable for the todo list
  $('.todo-list').sortable({
    placeholder         : 'sort-highlight',
    handle              : '.handle',
    forcePlaceholderSize: true,
    zIndex              : 999999
  });

  // The todo list plugin
  /*
  $('.todo-list').todoList({
    onCheck  : function () {
      window.console.log($(this), 'The element has been checked');
    },
    onUnCheck: function () {
      window.console.log($(this), 'The element has been unchecked');
    }
  });
  */

  // The Calender
  $('#calendar').datepicker();
</script>
<?php }?>
