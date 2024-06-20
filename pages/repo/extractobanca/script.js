const rutaSQL = "pages/repo/extractobanca/sql.php";
var menu = "";

function modalSocio_keyBuscar(e){ if(e.keyCode === 13) { modalSocioBuscar(); } }

async function appReset(){
  $("#div_InfoCorta").html('');
  try{
    const resp = await appAsynFetch({TipoQuery:'selDataUser'},"includes/sess_interfaz.php");
    menu = JSON.parse(resp.menu);
  } catch (err) {
    console.error('Error al cargar datos:', err);
  }
}

function appBotonBuscar(){
  $("#modalSocio_Titulo").html("Verificar Aportes por Doc. Identidad");
  $("#modalSocio_Grid").hide();
  $("#modalSocio_Wait").html("");
  $("#modalSocio_TxtBuscar").val("");
  $('#modalSocio').modal({keyboard:true}).on('shown.bs.modal', ()=> { $("#modalSocio_TxtBuscar").focus(); });
}

function modalSocioBuscar(){
  $("#modalSocio_Grid").hide();
  if($("#modalSocio_TxtBuscar").val().length>3){ 
    modalSocioGrid();
  } else { 
    $('#modalSocio_Wait').html('<div class="callout callout-warning"><h4>Demasiado Corto</h4><p>El NRO de documento de Identidad debe tener como minimo <b>4 numeros</b></p></div>'); 
  }
}

async function modalSocioGrid(){
  $('#modalSocio_Wait').html('<div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></div>');
  $("#modalSocio_Grid").show();
  $('#modalSocio_Wait').html("");
  const txtBuscar = $("#modalSocio_TxtBuscar").val();
  try{
    const resp = await appAsynFetch({ TipoQuery: 'selSocios', buscar:txtBuscar },rutaSQL);
    if(resp.socios.length>0){
      let fila = "";
      resp.socios.forEach((valor,key)=>{
        fila += '<tr>'+
                '<td>'+(valor.DUI+' - '+valor.nro_DUI)+'</td>'+
                '<td><a href="javascript:appSociosOperView('+(valor.ID)+');">'+(valor.socio)+'</a></td>'+
                '<td style="text-align:right;">'+(valor.prods)+'</td>'+
                '</tr>';
      });
      $('#modalSocio_GridBody').html(fila);
    } else {
      $('#modalSocio_GridBody').html('<tr><td colspan="5" style="text-align:center;color:red;">Sin Resultados para '+txtBuscar+'</td></tr>');
    }
  } catch (err) {
    console.error('Error al cargar datos:', err);
  }
}

async function appSociosOperView(socioID){
  $('#modalSocio').modal('hide');
  try{
    const resp = await appAsynFetch({
      TipoQuery: 'viewSocio',
      socioID:socioID
    }, rutaSQL);

    //respuesta
    let fila = '';
    resp.prods.forEach((valor,key)=>{
      fila += '<li class="list-group-item"><a href="javascript:appSociosViewMovimProd('+(valor.saldoID)+','+(valor.operID)+');"><span>'+(valor.producto)+'&raquo; '+(valor.cod_prod)+'</span></a> <a class="pull-right">'+appFormatMoney(valor.saldo,2)+'</a></li>';
    });
    $("#div_InfoCorta").html('<div class="box-body">'+
      'Socio: <a>'+(resp.socio.persona)+'</a><br/>'+
      (resp.socio.tipoDUI)+': <a>'+(resp.socio.nroDUI)+'</a><br/>'+
      'Codigo: <a>'+(resp.socio.codigo)+'</a>'+
      '<br/><br/>'+
      '<ul class="list-group list-group-unbordered">'+(fila)+'</ul></div>');
    $("#div_TablaMovim").html('');
  } catch (err) {
    console.error('Error al cargar datos:', err);
  }
}

async function appSociosViewMovimProd(saldoID,tipoOperID){
  $("#div_title_movim").html('<div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></div>');
  
  try{
    let fila = "";
    let result = "";
    let resp = null;
    let totOtros = 0;
    switch(tipoOperID){
      case 121: //aportes
        resp = await appAsynFetch({ TipoQuery:'viewMovimAportes', saldoID:saldoID }, rutaSQL);

        //respuesta
        let totIngresos = 0;
        let totSalidas = 0;
        resp.movim.forEach((valor,key)=>{
          totIngresos += valor.ingresos;
          totSalidas += valor.salidas;
          totOtros += valor.otros;
          fila += '<tr>'+
                  '<td>'+(valor.codagenc)+'</td>'+
                  '<td>'+(valor.coduser)+'</td>'+
                  '<td style="text-align:center;">'+(moment(valor.fecha).format("DD/MM/YYYY"))+'</td>'+
                  '<td style="text-align:center;">'+(valor.codigo)+'</td>'+
                  '<td>'+(valor.codmov+' '+valor.movim)+'</td>'+
                  '<td style="text-align:right;">'+((valor.ingresos>0)?(appFormatMoney(valor.ingresos,2)):(''))+'</td>'+
                  '<td style="text-align:right;">'+((valor.salidas>0)?(appFormatMoney(valor.salidas,2)):(''))+'</td>'+
                  '<td style="text-align:right;">'+((valor.otros>0)?appFormatMoney(valor.otros,2):(''))+'</td>'+
                  '</tr>';
        });
        fila += '<tr>'+
                '<td colspan="5" style="text-align:center;"><b>Total</b></td>'+
                '<td style="text-align:right;"><b>'+appFormatMoney(totIngresos,2)+'</b></td>'+
                '<td style="text-align:right;"><b>'+appFormatMoney(totSalidas,2)+'</b></td>'+
                '<td style="text-align:right;"><b>'+appFormatMoney(totOtros,2)+'</b></td>'+
                '</tr>';
        
        //resultado
        result = '<table class="table table-hover" style="font-family:helveticaneue_light;">'+
            '<thead><tr>'+
                '<th style="width:25px;" title="Agencia">AG</th>'+
                '<th style="width:25px;" title="Usuario">US</th>'+
                '<th style="width:80px;text-align:center;">Fecha</th>'+
                '<th style="width:120px;text-align:center;">num_trans</th>'+
                '<th>Detalle</th>'+
                '<th style="width:95px;text-align:right;">Depositos</th>'+
                '<th style="width:80px;text-align:right;">Retiros</th>'+
                '<th style="width:80px;text-align:right;">Otros</th>'+
              '</tr></thead>'+
            '<tbody>'+fila+'</tbody>'+
          '</table>';
        $("#div_title_movim").html('<h3 class="box-title" style="font-family:flexoregular;font-weight:bold;">'+resp.producto+'</h3>');
        $("#div_TablaMovim").html(result);
        break;
      case 124: //creditos
        resp = await appAsynFetch({ TipoQuery:'viewMovimCreditos', saldoID:saldoID },rutaSQL);

        //respuesta
        let totCapital = 0;
        let totInteres = 0;
        let totMora = 0;
        resp.movim.forEach((valor,key)=>{
          totCapital += valor.capital;
          totInteres += valor.interes;
          totMora += valor.mora;
          totOtros += valor.otros;
          fila += '<tr>'+
                  '<td style="text-align:center;">'+(valor.codigo)+'</td>'+
                  '<td>'+(valor.codagenc)+'</td>'+
                  '<td>'+(valor.coduser)+'</td>'+
                  '<td style="text-align:center;">'+(moment(valor.fecha).format("DD/MM/YYYY"))+'</td>'+
                  '<td style="text-align:right;">'+((valor.capital!=0)?appFormatMoney(valor.capital,2):'')+'</td>'+
                  '<td style="text-align:right;">'+((valor.interes>0)?appFormatMoney(valor.interes,2):'')+'</td>'+
                  '<td style="text-align:right;">'+((valor.mora>0)?appFormatMoney(valor.mora,2):'')+'</td>'+
                  '<td style="text-align:right;">'+((valor.otros>0)?appFormatMoney(valor.otros,2):'')+'</td>'+
                  '<td style="text-align:right;">'+(appFormatMoney((valor.capital+valor.interes+valor.mora+valor.otros),2))+'</td>'+
                  '</tr>';
        });
        fila += '<tr>'+
                '<td colspan="4" style="text-align:center;"><b>TOTAL GENERAL</b></td>'+
                '<td style="text-align:right;"><b>'+appFormatMoney(totCapital,2)+'</b></td>'+
                '<td style="text-align:right;"><b>'+appFormatMoney(totInteres,2)+'</b></td>'+
                '<td style="text-align:right;"><b>'+appFormatMoney(totMora,2)+'</b></td>'+
                '<td style="text-align:right;"><b>'+appFormatMoney(totOtros,2)+'</b></td>'+
                '<td></td></tr>';
        
        //resultado
        result = '<table class="table table-hover" style="font-family:helveticaneue_light;">'+
            '<thead><tr>'+
                '<th style="width:120px;text-align:center;">num_trans</th>'+
                '<th style="width:25px;" title="Agencia">AG</th>'+
                '<th style="width:25px;" title="Usuario">US</th>'+
                '<th style="width:80px;text-align:center;">Fecha</th>'+
                '<th style="width:80px;text-align:right;">Capital</th>'+
                '<th style="width:80px;text-align:right;">Interes</th>'+
                '<th style="width:80px;text-align:right;">Mora</th>'+
                '<th style="width:80px;text-align:right;">Otros</th>'+
                '<th style="width:80px;text-align:right;">Total</th>'+
              '</tr></thead>'+
            '<tbody>'+fila+'</tbody>'+
            '<tfoot>'+
              '<tr style="height:60px;">'+
                '<td colspan="3"></td>'+
                '<td style="text-align:center;vertical-align:bottom;border-bottom-style:double;"><b>Saldo Final</b></td>'+
                '<td style="text-align:right;vertical-align:bottom;border-bottom-style:double;"><b>'+appFormatMoney(totCapital,2)+'</b></td>'+
                '<td colspan="4"></td></tr>'+
            '</tfoot>'+
          '</table>';
        $("#div_title_movim").html('<h3 class="box-title" style="font-family:flexoregular;font-weight:bold;">'+resp.producto.producto+'</h3>');
        $("#div_TablaMovim").html(result);
        break;
    }
  } catch (err) {
    console.error('Error al cargar datos:', err);
  }
}