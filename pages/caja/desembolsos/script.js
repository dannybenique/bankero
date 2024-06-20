const rutaSQL = "pages/caja/desembolsos/sql.php";
var menu = "";
var desemb = null;

//=========================funciones para Personas============================
function appDesembBuscar(e){ if(e.keyCode === 13) { appDesembGrid(); } }

async function appDesembGrid(){
  $('#grdDatos').html('<tr><td colspan="9"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  $("#chk_All").prop("disabled", !(menu.caja.submenu.desemb.cmdDelete === 1));
  const disabledDelete = (menu.caja.submenu.desemb.cmdDelete===1) ? "" : "disabled";
  const txtBuscar = $("#txtBuscar").val();
  try{
    const resp = await appAsynFetch({ TipoQuery:'selDesembolsos', buscar:txtBuscar }, rutaSQL);

    //respuesta
    if(resp.tabla.length>0){
      let fila = "";
      resp.tabla.forEach((valor,key)=>{
        fila += '<tr>'+
                '<td><input type="checkbox" name="chk_Borrar" value="'+(valor.ID)+'" '+disabledDelete+'/></td>'+
                '<td>'+(moment(valor.fecha).format("DD/MM/YYYY"))+'</td>'+
                '<td>'+(valor.socio+' &raquo; '+valor.nro_dui)+'</td>'+
                '<td><a href="javascript:appDesembView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.codigo+' &raquo; '+valor.producto+'; '+valor.mon_abrevia+'; '+appFormatMoney(valor.tasa,2))+'%</a></td>'+
                '<td>'+(valor.tiposbs)+'</td>'+
                '<td>'+(moment(valor.inicio).format("DD/MM/YYYY"))+'</td>'+
                '<td style="text-align:right;">'+appFormatMoney(valor.importe,2)+'</td>'+
                '<td style="text-align:center;">'+(valor.nro_cuotas)+'</td>'+
                '</tr>';
      });
      $('#grdDatos').html(fila);
    }else{
      $('#grdDatos').html('<tr><td colspan="9" style="text-align:center;color:red;">Sin Resultados '+((txtBuscar==="")?(""):("para "+txtBuscar))+'</td></tr>');
    }
    $('#grdCount').html(resp.tabla.length+"/"+resp.cuenta);
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}

async function appDesembReset(){
  desemb = null;
  $("#txtBuscar").val("");
  $("#grdDatos").html("");
  try{
    const resp = await appAsynFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php");
    menu = JSON.parse(resp.menu);
    $("#btn_DEL").toggle(menu.caja.submenu.desemb.cmdDelete==1);
    $("#div_PersAuditoria").toggle(resp.rolID == 101);
    appDesembGrid();
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}

function appDesembBotonCancel(){
  appDesembGrid();
  $('#grid').show();
  $('#edit').hide();
}

async function appDesembBotonDesembolsar(){
  if(confirm("¿Esta seguro de continuar?")) {
    try{
      const resp = await appAsynFetch({
        TipoQuery : 'ejecutarDesembolso',
        ID : desemb.id,
        socioID : desemb.socioID,
        monedaID : desemb.monedaID,
        agenciaID : desemb.agenciaID,
        tipopagoID : desemb.tipopagoID,
        tipocredID : desemb.tipocredID,
        productoID : desemb.productoID,
        cod_prod : $("#lbl_DesembCodigo").html(),
        fecha_desemb : appConvertToFecha($("#txt_DesembFecha").val(),""),
        fecha_otorga : appConvertToFecha($("#lbl_DesembFechaOtorga").html()),
        importe : appConvertToNumero($("#lbl_DesembImporte").html()),
        tasa_cred : appConvertToNumero($("#lbl_DesembTasaCred").html()),
        tasa_desgr : appConvertToNumero($("#lbl_DesembTasaDesgr").html()),
        nrocuotas : $("#lbl_DesembNrocuotas").html(),
        pivot : (desemb.tipocredID==1)?(appConvertToFecha($("#lbl_DesembFechaPriCuota").html())):($("#lbl_DesembFrecuencia").html()),
        observac: $("#lbl_DesembObservac").html()
      }, rutaSQL);
      
      //respuesta
      if (!resp.error) { 
        if(confirm("¿Desea Imprimir el desembolso?")){
          $("#modalPrint").modal("show");
          let urlServer = appUrlServer()+"pages/caja/desembolsos/rpt.voucher.php?movimID="+resp.movimID;
          $("#contenedorFrame").html('<object id="objPDF" type="text/html" data="'+urlServer+'" width="100%" height="500px"></object>');
        }
        appDesembBotonCancel(); 
      }
    } catch(err){
      console.error('Error al cargar datos:'+err);
    }
  }
}

async function appDesembBotonBorrar(){
  const arr = $('[name="chk_Borrar"]:checked').map(function() { return this.value}).get();
  if(arr.length>0){
    if(confirm("¿Esta seguro de continuar?")) {
      try{
        const resp = await appAsynFetch({ TipoQuery:'delDesembolsos', arr:arr },rutaSQL);
        if (!resp.error) { appDesembGrid(); }
      } catch(err){
        console.error('Error al cargar datos:'+err);
      }
    }
  } else {
    alert("NO eligio borrar ninguno");
  }
}

async function appDesembView(solicredID){
  //tabs default en primer tab
  $('.nav-tabs li').removeClass('active');
  $('.tab-content .tab-pane').removeClass('active');
  $('a[href="#datosSoliCred"]').closest('li').addClass('active');
  $('#datosSoliCred').addClass('active');
  $("#btnInsert").toggle(menu.caja.submenu.desemb.cmdUpdate==1);

  try{
    const resp = await appAsynFetch({
      TipoQuery : 'viewDesembolso',
      SoliCredID : solicredID
    }, rutaSQL);

    //respuesta
    $('#grid').hide();
    $('#edit').show();

    appDesembSetData(resp.tablaDesembolso);  //pestaña Solicitud de credito
    appPersonaSetData(resp.tablaPers);
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}

function appDesembSetData(data){
  //pestaña de desembolso
  desemb = {
    id : data.ID,
    tipopagoID : 164,
    tipocredID : data.tipocredID,
    productoID : data.productoID,
    monedaID : data.monedaID,
    socioID : data.socioID,
    agenciaID : data.agenciaID
  }
  $("#lbl_FormAprueba").css("color", data.aprueba === "" ? "#D00" : "#777");
  $("#txt_DesembFecha").prop("disabled", data.rolUser != data.rolROOT);
  $('#txt_DesembFecha').datepicker("setDate",moment(data.fecha_desemb).format("DD/MM/YYYY"));

  $('#lbl_DesembSocio').html(data.socio);
  $('#lbl_DesembFechaSoliCred').html(moment(data.fecha_solicred).format("DD/MM/YYYY"));
  $("#lbl_DesembCodigo").html(data.codigo);
  $("#lbl_DesembMoneda").html(data.moneda);
  $("#lbl_DesembClasifi").html(data.clasifica);
  $("#lbl_DesembCondicion").html(data.condicion);
  $("#lbl_DesembAgencia").html(data.agencia);
  $("#lbl_DesembPromotor").html(data.promotor);
  $("#lbl_DesembAnalista").html(data.analista);
  $("#lbl_DesembAprueba").html(data.aprueba);
  $("#lbl_DesembTipoSBS").html(data.tiposbs);
  $("#lbl_DesembDestSBS").html(data.destsbs);
  $("#lbl_DesembPrestamoID").html(data.ID);
  $("#lbl_DesembTipoCred").html(data.tipocred);
  $("#lbl_DesembProducto").html(data.producto);
  $("#lbl_DesembImporte").html(appFormatMoney(data.importe,2));
  $("#lbl_DesembNrocuotas").html(data.nrocuotas);
  $("#lbl_DesembTasaCred").html(appFormatMoney(data.tasa_cred,2));
  $("#lbl_DesembTasaMora").html(appFormatMoney(data.tasa_mora,2));
  $("#lbl_DesembTasaDesgr").html(appFormatMoney(data.tasa_desgr,2));
  $("#lbl_DesembFechaOtorga").html(moment(data.fecha_otorga).format("DD/MM/YYYY"));
  $("#lbl_DesembFechaPriCuota").html(moment(data.fecha_pricuota).format("DD/MM/YYYY"));
  $("#lbl_DesembEtqFrecuencia").toggle(data.tipocredID=="1");
  $("#lbl_DesembFrecuencia").html(data.frecuencia);
  $("#lbl_DesembCuota").html("&nbsp;<small style='font-size:10px;'>"+data.mon_abrevia+"</small>&nbsp;&nbsp;"+data.cuota+"&nbsp;");
  $("#lbl_DesembObservac").html(data.observac);
}

function appPersonaSetData(data){
  //pestaña datos personales
  if(data.tipoPersona==2){ //persona juridica
    $("#lbl_PersTipoNombres").html("Razon Social");
    $("#lbl_PersTipoProfesion").html("Rubro");
    $("#lbl_PersTipoApellidos, #lbl_PersTipoSexo, #lbl_PersTipoECivil, #lbl_PersTipoGIntruc").hide();
  }else{
    $("#lbl_PersTipoNombres").html("Nombres");
    $("#lbl_PersTipoProfesion").html("Profesion");
    $("#lbl_PersTipoApellidos, #lbl_PersTipoSexo, #lbl_PersTipoECivil, #lbl_PersTipoGIntruc").show();
  }
  $("#hid_PersID").val(data.ID);
  $("#lbl_PersNombres").html(data.nombres);
  $("#lbl_PersApellidos").html(data.ap_paterno+" "+data.ap_materno);
  $("#lbl_PersTipoDNI").html(data.tipoDUI);
  $("#lbl_PersNroDNI").html(data.nroDUI);
  $("#lbl_PersFechaNac").html(moment(data.fechanac).format("DD/MM/YYYY"));
  $("#lbl_PersEdad").html(moment().diff(moment(data.fechanac),"years")+" años");
  $("#lbl_PersPaisNac").html(data.paisnac);
  $("#lbl_PersLugarNac").html(data.lugarnac);
  $("#lbl_PersSexo").html(data.sexo);
  $("#lbl_PersEcivil").html(data.ecivil);
  $("#lbl_PersCelular").html(data.celular);
  $("#lbl_PersTelefijo").html(data.telefijo);
  $("#lbl_PersEmail").html(data.correo);
  $("#lbl_PersGInstruccion").html(data.ginstruc);
  $("#lbl_PersProfesion").html(data.profesion);
  $("#lbl_PersOcupacion").html(data.ocupacion);
  $("#lbl_PersUbicacion").html(data.region+" - "+data.provincia+" - "+data.distrito);
  $("#lbl_PersDireccion").html(data.direccion);
  $("#lbl_PersReferencia").html(data.referencia);
  $("#lbl_PersMedidorluz").html(data.medidorluz);
  $("#lbl_PersMedidorAgua").html(data.medidoragua);
  $("#lbl_PersTipovivienda").html(data.tipovivienda);
  $("#lbl_PersObservac").html(data.observPers);
  $("#lbl_PersSysFecha").html(moment(data.sysfechaPers).format("DD/MM/YYYY HH:mm:ss"));
  $("#lbl_PersSysUser").html(data.sysuserPers);
}