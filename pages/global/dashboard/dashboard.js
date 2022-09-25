var rutaSQL = "pages/global/dashboard/sql.php";

//=========================funciones para Dashboard============================
function appDashBoard(){
  let datos = { TipoQuery : 'dashboard' }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    $("#appColocaciones").html(resp.colocaciones);
    $("#appEmpleados").html(resp.empleados);
    $("#appMorosos").html(resp.morosos);
    $("#appCartera").html(resp.cartera);
    if(resp.numcumple==0){
      $("#boxcumple").hide();
    } else{
      $("#boxcumple").show();
      $("#boxcumple_usu").html(resp.nombres_cumple);
    }
  });
}
