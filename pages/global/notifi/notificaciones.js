var rutaSQL = "pages/global/notifi/sql.php";

//=========================funciones para Personas============================
function appNotificacionesGetAll(){
  let datos = { TipoQuery : 'selNotifi' }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    if(resp.tabla.length>0){
      let appData = "";
      $.each(resp.tabla,function(key, valor){
        let permitir = (valor.estado==0) ? ('<button type="button" class="btn btn-primary btn-xs" onclick="javascript:appNotificacionesPermitir('+(valor.ID)+');"><i class="fa fa-flash" ></i> Permitir</button>') : ('');
        appData += '<tr>';
        appData += '<td style="width:80px;">'+(permitir)+'</td>';
        appData += '<td style="width:90px;"><button type="button" class="btn btn-danger btn-xs" onclick="javascript:appNotificacionesDenegar('+(valor.ID)+');"><i class="fa fa-close" ></i> Denegar</button></td>';
        appData += '<td>'+(valor.tabla)+'</td>';
        appData += '<td>'+(valor.usr_solic)+'</td>';
        appData += '<td>'+(valor.persona)+'... '+(valor.tipoDNI)+" "+(valor.nroDNI)+'</td>';
        appData += '</tr>';
      });
      $('#grdDatosBody').html(appData);
    }else{
      $('#grdDatosBody').html('<tr><td colspan="5" style="text-align:center;color:red;">No hay NINGUNA notificacion</td></tr>');
    }
    $('#grdDatosCount').html(resp.cuenta+" solicitudes");
  });
}

function appNotificacionesPermitir(notificacionID){
  let datos = {
    TipoQuery : 'updNotifi',
    notificacionID : notificacionID
  }
  appAjaxUpdate(datos,rutaSQL).done(function(resp){
    if(!resp.error){ appNotificacionesGetAll(); }
    else { alert("!!!Hubo un error... "+(resp.mensaje)+"!!!"); }
  });
}

function appNotificacionesDenegar(notificacionID){
  let datos = {
    TipoQuery : 'delNotifi',
    notificacionID : notificacionID
  }
  appAjaxDelete(datos,rutaSQL).done(function(resp){
    if(!resp.error){ appNotificacionesGetAll(); appNotificaciones();}
    else { alert("!!!Hubo un error... "+(resp.mensaje)+"!!!"); }
  });
}
