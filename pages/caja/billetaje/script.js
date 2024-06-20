const rutaSQL = "pages/caja/billetaje/sql.php";
var menu = "";

//=========================funciones para Personas============================
async function appBillGrid(){
  $('#grdDatos').html('<tr><td colspan="9"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  try{
    const resp = await appAsynFetch({
      TipoQuery: 'selBilletaje',
      usuarioID: $("#cboUsuarios").val(),
      monedaID: $("#cboMonedas").val()
    }, rutaSQL);

    //respuesta
    let disabledDelete = (menu.caja.submenu.billetaje.cmdDelete===1) ? "" : "disabled";
    $("#chk_All").prop("disabled", !(menu.caja.submenu.billetaje.cmdDelete === 1));
    if(resp.tabla.length>0){
      let fila = "";
      resp.tabla.forEach((valor,key)=>{
        fila += '<tr>'+
                '<td><input type="checkbox" name="chk_Borrar" value="'+(valor.ID)+'" '+disabledDelete+'/></td>'+
                '<td><a href="javascript:appBillView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(moment(valor.fecha).format("DD/MM/YYYY"))+'</a></td>'+
                '<td>'+(valor.agencia)+'</td>'+
                '<td>'+(valor.empleado)+'</td>'+
                '<td>'+(valor.moneda)+'</td>'+
                '<td style="text-align:right;">'+appFormatMoney(valor.total,2)+'</td>'+
                '</tr>';
      });
      $('#grdDatos').html(fila);
    }else{
      $('#grdDatos').html('<tr><td colspan="5" style="text-align:center;color:red;">Sin Resultados</td></tr>');
    }
    $('#grdCount').html(resp.tabla.length+"/"+resp.cuenta);
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}

async function appBillReset(){
  try{
    const resp = await appAsynFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php");
    menu = JSON.parse(resp.menu);
    $("#btn_DEL").toggle(menu.caja.submenu.billetaje.cmdDelete == 1);
    $("#btn_NEW").toggle(menu.caja.submenu.billetaje.cmdInsert == 1);
    
    const rpta = await appAsynFetch({ TipoQuery:'StartBilletaje' }, rutaSQL);
    appLlenarDataEnComboBox(rpta.comboMonedas,"#cboMonedas",0);
    appLlenarDataEnComboBox(rpta.comboUsuarios,"#cboUsuarios",((rpta.rolID==rpta.root)?(0):(rpta.userID)));
    $("#cboUsuarios").prop("disabled", rpta.rolID !== rpta.root);
    appBillGrid();
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}

function appBillBotonCancel(){
  appBillGrid();
  $('#grid').show();
  $('#edit').hide();
}

async function appBillBotonBorrar(){
  const arr = $('[name="chk_Borrar"]:checked').map(function() { return this.value}).get();
  if(arr.length>0){
    if(confirm("¿Esta seguro de continuar?")) {
      try{
        const resp = await appAsynFetch({ TipoQuery:'delBilletaje', arr:arr },rutaSQL);
        if(!resp.error) { appBillGrid(); }
      } catch(err){
        console.error('Error al cargar datos:'+err);
      }
    }
  } else {
    alert("NO eligio borrar ninguno");
  }
}

async function appBillBotonNuevo(){
  $("#txt_Mx200, #txt_Mx100, #txt_Mx50, #txt_Mx20, #txt_Mx10, #txt_Mx5, #txt_Mx2, #txt_Mx1, #txt_Mx05, #txt_Mx02, #txt_Mx01").val("");
  $("#txt_MxTotal").html("0.00");
  $("#btnInsert").toggle(menu.caja.submenu.billetaje.cmdInsert == 1);
  $("#btnUpdate").hide();
  try{
    const resp = await appAsynFetch({
      TipoQuery:'newBilletaje',
      monedaID : $("#cboMonedas").val(),
      usuarioID : $("#cboUsuarios").val() 
    }, rutaSQL);

    //respuesta
    $(".form-group").removeClass("has-error");
    $(".billetaje_mon").html(resp.mon_abrevia);
    $('#txt_Fecha').datepicker("setDate",moment(resp.fecha).format("DD/MM/YYYY"));
    appLlenarDataEnComboBox(resp.comboMonedas,"#cbo_MonedasEdit",$("#cboMonedas").val());
    appLlenarDataEnComboBox(resp.comboAgencias,"#cbo_AgenciasEdit",0);
    $("#hid_billID").val(0);
    $("#hid_usuarioID").val($("#cboUsuarios").val());
    $("#txt_UsuarioEdit").val(resp.usuario);
    $("#grid").hide();
    $("#edit").show();
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}

async function appBillView(billID){
  $(".form-group").removeClass("has-error");
  try{
    const resp = await appAsynFetch({ TipoQuery:'viewBilletaje', billID }, rutaSQL);
    
    //respuesta
    document.querySelector("#btnUpdate").style.display = (resp.rolUSR===resp.rolROOT)?('inline'):((resp.fecha==resp.tabla.fecha && menu.caja.submenu.billetaje.cmdUpdate==1)?('inline'):('none'));
    $("#grid, #btnInsert").hide();
    $('#edit').show();
    
    $(".billetaje_mon").html(resp.tabla.mon_abrevia);
    $('#txt_Fecha').datepicker("setDate",moment(resp.tabla.fecha).format("DD/MM/YYYY"));
    appLlenarDataEnComboBox(resp.comboMonedas,"#cbo_MonedasEdit",resp.tabla.monedaID);
    appLlenarDataEnComboBox(resp.comboAgencias,"#cbo_AgenciasEdit",resp.tabla.agenciaID);
  
    $("#hid_billID").val(resp.tabla.ID);
    $("#hid_usuarioID").val(resp.tabla.usuarioID);
    $("#txt_UsuarioEdit").val(resp.tabla.usuario);
    $("#txt_Mx200").val(resp.tabla.mx_200);
    $("#txt_Mx100").val(resp.tabla.mx_100);
    $("#txt_Mx50").val(resp.tabla.mx_50);
    $("#txt_Mx20").val(resp.tabla.mx_20);
    $("#txt_Mx10").val(resp.tabla.mx_10);
    $("#txt_Mx5").val(resp.tabla.mx_5);
    $("#txt_Mx2").val(resp.tabla.mx_2);
    $("#txt_Mx1").val(resp.tabla.mx_1);
    $("#txt_Mx05").val(resp.tabla.mx_05);
    $("#txt_Mx02").val(resp.tabla.mx_02);
    $("#txt_Mx01").val(resp.tabla.mx_01);
    $("#txt_MxTotal").html(appFormatMoney(resp.tabla.mx_total,2));
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}

function appBillCalcular(){
  let mx200 = $.isNumeric($("#txt_Mx200").val()) ? ($("#txt_Mx200").val()*200) : (0);
  let mx100 = $.isNumeric($("#txt_Mx100").val()) ? ($("#txt_Mx100").val()*100) : (0);
  let mx50 = $.isNumeric($("#txt_Mx50").val()) ? ($("#txt_Mx50").val()*50) : (0);
  let mx20 = $.isNumeric($("#txt_Mx20").val()) ? ($("#txt_Mx20").val()*20) : (0);
  let mx10 = $.isNumeric($("#txt_Mx10").val()) ? ($("#txt_Mx10").val()*10) : (0);
  let mx5 = $.isNumeric($("#txt_Mx5").val()) ? ($("#txt_Mx5").val()*5) : (0);
  let mx2 = $.isNumeric($("#txt_Mx2").val()) ? ($("#txt_Mx2").val()*2) : (0);
  let mx1 = $.isNumeric($("#txt_Mx1").val()) ? ($("#txt_Mx1").val()*1) : (0);
  let mx05 = $.isNumeric($("#txt_Mx05").val()) ? ($("#txt_Mx05").val()*0.5) : (0);
  let mx02 = $.isNumeric($("#txt_Mx02").val()) ? ($("#txt_Mx02").val()*0.2) : (0);
  let mx01 = $.isNumeric($("#txt_Mx01").val()) ? ($("#txt_Mx01").val()*0.1) : (0);
  let total = mx200 + mx100 + mx50 + mx20 + mx10 + mx5 + mx2 + mx1 + mx05 + mx02 + mx01;

  $("#txt_MxTotal").html(appFormatMoney(total,2));
}

async function appBillInsert(){
  let datos = appGetDataToDataBase();
  if(datos!=null){
    datos.TipoQuery = 'insBilletaje';
    try{
      const resp = await appAsynFetch(datos,rutaSQL);
      appBillGrid();
      appBillBotonCancel();
    } catch(err){
      console.error('Error al cargar datos:'+err);
    }
  } else {
    alert("!!!Faltan Datos!!!");
  }
}

async function appBillUpdate(){
  let datos = appGetDataToDataBase();
  if(datos!=null){
    datos.TipoQuery = 'updBilletaje';
    try{
      const resp = await appAsynFetch(datos,rutaSQL);
      appBillGrid();
      appBillBotonCancel();
    } catch(err){
      console.error('Error al cargar datos:'+err);
    }
  } else {
    alert("!!!Faltan Datos!!!");
  }
}

function appGetDataToDataBase(){
  let rpta = null;
  let esError = false;

  $(".form-group").removeClass("has-error");
  if($("#txt_Fecha").val()=="") { $("#pn_Fecha").addClass("has-error"); esError = true; }
  
  if(!esError){
    rpta = {
      ID : $("#hid_billID").val(),
      usuarioID: $("#hid_usuarioID").val(),
      monedaID : $("#cbo_MonedasEdit").val(),
      agenciaID : $("#cbo_AgenciasEdit").val(),
      fecha : appConvertToFecha($("#txt_Fecha").val()),
      mx200 : ($.isNumeric($("#txt_Mx200").val())?($("#txt_Mx200").val()):(0)),
      mx100 : ($.isNumeric($("#txt_Mx100").val())?($("#txt_Mx100").val()):(0)),
      mx50 : ($.isNumeric($("#txt_Mx50").val())?($("#txt_Mx50").val()):(0)),
      mx20 : ($.isNumeric($("#txt_Mx20").val())?($("#txt_Mx20").val()):(0)),
      mx10 : ($.isNumeric($("#txt_Mx10").val())?($("#txt_Mx10").val()):(0)),
      mx5 : ($.isNumeric($("#txt_Mx5").val())?($("#txt_Mx5").val()):(0)),
      mx2 : ($.isNumeric($("#txt_Mx2").val())?($("#txt_Mx2").val()):(0)),
      mx1 : ($.isNumeric($("#txt_Mx1").val())?($("#txt_Mx1").val()):(0)),
      mx05 : ($.isNumeric($("#txt_Mx05").val())?($("#txt_Mx05").val()):(0)),
      mx02 : ($.isNumeric($("#txt_Mx02").val())?($("#txt_Mx02").val()):(0)),
      mx01 : ($.isNumeric($("#txt_Mx01").val())?($("#txt_Mx01").val()):(0)),
      mxtotal : appConvertToNumero($("#txt_MxTotal").html())
    }
  }
  return rpta;
}