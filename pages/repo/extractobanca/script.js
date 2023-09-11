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
    let fila = '';
    resp.prods.forEach((valor,key)=>{
      fila += '<li class="list-group-item"><a href="javascript:appSociosViewMovimProd('+(valor.saldoID)+','+(valor.operID)+');"><span>'+(valor.producto)+'</span></a> <a class="pull-right">'+appFormatMoney(valor.saldo,2)+'</a></li>';
    });
    let prods = '<ul class="list-group list-group-unbordered">'+(fila)+'</ul>';
    document.querySelector("#div_InfoCorta").innerHTML = '<div class="box-body">'+
      'Socio: <a>'+(resp.socio.persona)+'</a><br/>'+
      (resp.socio.tipoDUI)+': <a>'+(resp.socio.nroDUI)+'</a><br/>'+
      'Codigo: <a>'+(resp.socio.codigo)+'</a><br/><br/>'+ prods + '</div>';
  });
}

function appSociosViewMovimProd(saldoID,tipoOperID){
  let result = "";
  switch(tipoOperID){
    case 121: //aportes
      let datos = {
        TipoQuery : 'viewProdMovim',
        saldoID : saldoID
      }
      appFetch(datos,rutaSQL).then(resp => {
        let totIngresos = 0;
        let totSalidas = 0;
        let totOtros = 0;
        let fila = "";
        resp.movim.forEach((valor,key)=>{
          totIngresos += valor.ingresos;
          totSalidas += valor.salidas;
          totOtros += valor.otros;
          fila += '<tr>';
          fila += '<td>'+(valor.ag)+'</td>';
          fila += '<td>'+(valor.us)+'</td>';
          fila += '<td style="text-align:center;">'+(moment(valor.fecha).format("DD/MM/YYYY"))+'</td>';
          fila += '<td style="text-align:center;">'+(valor.codigo)+'</td>';
          fila += '<td>'+(valor.codmov+' '+valor.movim)+'</td>';
          fila += '<td style="text-align:right;">'+((valor.ingresos>0)?(appFormatMoney(valor.ingresos,2)):(''))+'</td>';
          fila += '<td style="text-align:right;">'+((valor.salidas>0)?(appFormatMoney(valor.salidas,2)):(''))+'</td>';
          fila += '<td style="text-align:right;">'+((valor.otros>0)?appFormatMoney(valor.otros,2):(''))+'</td>';
          fila += '</tr>';
        });
        fila += '<tr>';
        fila += '<td colspan="5" style="text-align:center;"><b>Total</b></td>';
        fila += '<td style="text-align:right;"><b>'+appFormatMoney(totIngresos,2)+'</b></td>';
        fila += '<td style="text-align:right;"><b>'+appFormatMoney(totSalidas,2)+'</b></td>';
        fila += '<td style="text-align:right;"><b>'+appFormatMoney(totOtros,2)+'</b></td>';
        fila += '</tr>';
        
        //resultado
        result = '<table class="table table-hover" style="font-family:helveticaneue_light;">'+
            '<thead><tr>'+
                '<th style="width:25px;" title="Agencia">AG</th>'+
                '<th style="width:25px;" title="Usuario">US</th>'+
                '<th style="width:80px;text-align:center;">Fecha</th>'+
                '<th style="width:120px;text-align:center;">num_trans</th>'+
                '<th style="">Detalle</th>'+
                '<th style="width:95px;text-align:right;">Depositos</th>'+
                '<th style="width:80px;text-align:right;">Retiros</th>'+
                '<th style="width:80px;text-align:right;">Otros</th>'+
              '</tr></thead>'+
            '<tbody>'+fila+'</tbody>'+
          '</table>';
        document.querySelector("#title_prod_movim").innerHTML = 'APORTES';
        document.querySelector("#div_TablaMovim").innerHTML = result;
      });
      break;
    case 124: //creditos
      break;
  }
}