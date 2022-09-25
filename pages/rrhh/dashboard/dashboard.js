//=========================funciones para Recursos Humanos RRHH============================
function rrhhDashBoard(){
  let datos = { TipoQuery:'dashboardRRHH' }
  appAjaxSelect(datos,"pages/rrhh/dashboard/sql.php").done(function(resp){
    $("#appEmpleados").html(resp.nroEmpleados);
    $("#appVacaciones").html(resp.nroVacaciones);
    $("#appRenovacion").html(resp.nroRenovacion);
    $("#appColocaciones").html(resp.nroColocaciones);
    $("#appCumple").html(resp.nroCumple);
  });
}
