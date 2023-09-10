const rutaSQL = "pages/repo/extractobanca/sql.php";
var menu = "";

//=========================funciones para Personas============================
function appReset(){
  appFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php").then(resp => {
    menu = JSON.parse(resp.menu);
    document.querySelector("#div_InfoCorta").innerHTML = '';
  });
}

function appBotonBuscar(){
  document.querySelector("#modalSocio_Titulo").innerHTML = ("Verificar Aportes por Doc. Identidad");
  document.querySelector("#modalSocio_Grid").style.display = 'none';
  document.querySelector("#modalSocio_Wait").innerHTML = ("");
  document.querySelector("#modalSocio_TxtBuscar").value = ("");
  $('#modalSocio').modal({keyboard:true});
  $('#modalSocio').on('shown.bs.modal', ()=> { document.querySelector("#modalSocio_TxtBuscar").focus(); });
}

function modalSocio_keyBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { modalSocioBuscar(); }
}

function modalSocioBuscar(){
  document.querySelector("#modalSocio_Grid").style.display = 'none';
  if(document.querySelector("#modalSocio_TxtBuscar").value.length>=3){ 
    modalSocioGrid();
  } else { 
    document.querySelector('#modalSocio_Wait').innerHTML = ('<div class="callout callout-warning"><h4>Demasiado Corto</h4><p>El NRO de documento de Identidad debe tener como minimo <b>4 numeros</b></p></div>'); 
  }
}

function modalSocioGrid(){
  document.querySelector('#modalSocio_Wait').innerHTML = ('<div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></div>');
  let txtBuscar = document.querySelector("#modalSocio_TxtBuscar").value;
  let datos = { TipoQuery: 'selSocios', buscar:txtBuscar };

  appFetch(datos,rutaSQL).then(resp => {
    document.querySelector('#modalSocio_Wait').innerHTML = "";
    document.querySelector("#modalSocio_Grid").style.display = 'block';
    if(resp.socios.length>0){
      let fila = "";
      resp.socios.forEach((valor,key)=>{
        fila += '<tr>'+
                '<td>'+(valor.DUI+' - '+valor.nro_DUI)+'</td>'+
                '<td><a href="javascript:appSociosOperView('+(valor.ID)+');">'+(valor.socio)+'</a></td>'+
                '<td style="text-align:right;">'+(valor.prods)+'</td>'+
                '</tr>';
      });
      document.querySelector('#modalSocio_GridBody').innerHTML = (fila);
    } else {
      document.querySelector('#modalSocio_GridBody').innerHTML = ('<tr><td colspan="5" style="text-align:center;color:red;">Sin Resultados para '+txtBuscar+'</td></tr>');
    }
  });
}

function appSociosOperView(socioID){
  $('#modalSocio').modal('hide');
  let datos = {
    TipoQuery: 'viewSocio',
    socioID:socioID
  }
  appFetch(datos,rutaSQL).then(resp => {
    let socio = resp.socio;
    let prods = resp.prods;
    console.log(prods);
    let fila = '<ul class="list-group list-group-unbordered">';
    prods.forEach((valor,key)=>{
      fila += '<li class="list-group-item"><a href="javascript:viewMovimProd('+(valor.saldoID)+','+(valor.operID)+');"><span>'+(valor.producto)+'</span></a> <a class="pull-right">'+(valor.saldo)+'</a></li>';
    });
    fila += '</ul>';
    document.querySelector("#div_InfoCorta").innerHTML = '<div class="box-body">'+
    'Socio: <a>'+(socio.persona)+'</a><br/>'+
    (socio.tipoDUI)+': <a>'+(socio.nroDUI)+'</a><br/>'+
    'Codigo: <a>'+(socio.codigo)+'</a><br/><br/>'+ fila + '</div>';
  });
}