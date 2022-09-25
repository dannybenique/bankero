<!-- fullCalendar -->
<link rel="stylesheet" href="libs/fullcalendar/5.11.0/main.min.css">
<script src="libs/fullcalendar/5.11.0/main.min.js"></script>
<script src="libs/fullcalendar/5.11.0/locales/es.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-pie-chart"></i> Recordatorios</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Recordatorios</li>
  </ol>
</section>

<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-body no-padding">
          <!-- THE CALENDAR -->
          <div id="calendar"></div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalEvento" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" style="background:#f9f9f9;padding:8px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title fontFlexoRegular" id="modalTitle">Cambiar Accesos CoopSUD</h4>
        </div>
        <form class="form-horizontal" id="frmChangeCoopSUD" autocomplete="off">
          <div class="modal-body no-padding">
            <div class="box-body">
              <div class="col-md-12">
                <div class="box-body">
                  <div class="form-group" style="margin-bottom:5px;">
                    <div class="input-group">
                      <span class="input-group-addon" style="background:#EEEEEE;font-weight:bold;">Usuario</span>
                      <input id="mod_txtUsuario" type="text" class="form-control" readonly style="background:#fff;"/>
                    </div>
                  </div>
                  <div class="form-group" style="margin-bottom:5px;">
                    <div class="input-group">
                      <span class="input-group-addon" style="background:#EEEEEE;font-weight:bold;">Fecha</span>
                      <input id="mod_txtFecha" type="text" class="form-control" style="width:120px;"/>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon" style="background:#EEEEEE;font-weight:bold;">Todo el Dia</span>
                      <span class="input-group-addon" id="spanDocum_Fecha"><input id="mod_chkFullDay" type="checkbox"/></span>
                      <input id="mod_txtFecha" type="text" class="form-control" readonly style="width:10px;"/>
                    </div>
                  </div>
                </div>
              </div>


            </div>
            <div class="box-body">
              <table class="table no-border">
                <tr style="padding:10px;">
                  <td style="text-align:right;width:35%;">
                    <span style="padding-right:10px;">Nivel Acceso</span></td>
                  <td>
                    <select class="form-control" id="cbo_coopNivel" name="cbo_coopNivel" class="selectpicker">
                      <option value="E">Empleado</option>
                      <option value="J">Jefatura</option>
                      <option value="S">Supervisor</option>
                      <option value="G">Gerencia</option>
                    </select></td>
                </tr>
                <tr style="padding:10px;">
                  <td style="text-align:right;">
                    <span style="padding-right:10px;">Modificar Inter.</span></td>
                  <td>
                    <input type="checkbox" id="chk_coopModiInter" name="chk_coopModiInter"></td>
                </tr>
                <tr style="padding:10px;">
                  <td style="text-align:right;">
                    <span style="padding-right:10px;">Eliminar Mov.</span></td>
                  <td>
                    <input type="checkbox" id="chk_coopDeleMovi" name="chk_coopDeleMovi"></td>
                </tr>
              </table>
            </div>
          </div>
          <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
            <input type="hidden" id="coopCodUsuario" value="">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
            <button type="button" class="btn btn-primary" onclick="javascript:modWorkerBotonUpdateCoopSUD($('#coopCodUsuario').prop('value'),'#cbo_coopNivel','#chk_coopModiInter','#chk_coopDeleMovi');"><i class="fa fa-flash"></i> Cambiar Accesos</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<script>
  $(function () {
    /* ==================initialize the calendar================== */
    var calendar = new FullCalendar.Calendar(document.getElementById('calendar'),{
      headerToolbar : { left : 'prev,next,miAdd', center : 'title', right : 'today,timeGridWeek,listWeek' },
      buttonText : { today: 'hoy',week:'SMN',list:'LST' },
      initialDate : moment().format('YYYY-MM-DD'),
      initialView : 'timeGridWeek',
      businessHours : false,
      droppable : false,
      editable : false,
      locale : 'es',
      firstDay : 0,
      customButtons:{
        miAdd:{
          text:"nuevo",
          click:function(){
            $("#modalTitle").html("Nuevo Recordatorio"),
            $("#modalEvento").modal()
          }
        }
      },
      eventClick: function(info) {
        alert('Event: ' + info);
      },
      events : {
        url: 'pages/global/recordatorio/sql.php',
        failure: function (err) { console.log(err); },
        success: function(resp) { console.log(resp); }
      }
    });
    calendar.render();
  })
</script>
