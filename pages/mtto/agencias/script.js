const rutaSQL = "pages/mtto/agencias/sql.php";
var menu = "";

//=========================funciones para Personas============================
function appAgenciasBuscar(e){ if(e.keyCode === 13) { appAgenciasGrid(); } }

async function appAgenciasGrid(){
  $('#grdDatos').html('<tr><td colspan="4"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  const disabledDelete = (menu.mtto.submenu.agencias.cmdDelete===1) ? "" : "disabled";
  const txtBuscar = $("#txtBuscar").val();
  try{
    const resp = await appAsynFetch({ TipoQuery:'selAgencias', buscar:txtBuscar },rutaSQL);
    $("#chk_All").prop("disabled", !(menu.mtto.submenu.agencias.cmdDelete === 1));
    if(resp.agencias.length>0){
      let fila = "";
      resp.agencias.forEach((valor,key)=>{
        fila += '<tr>'+
                '<td><input type="checkbox" name="chk_Borrar" value="'+(valor.ID)+'" '+(disabledDelete)+'/></td>'+
                '<td style="font-size:18px;font-weight:bold;text-align:center;">'+(valor.codigo)+'</td>'+
                '<td><a href="javascript:appAgenciaView('+(valor.ID)+');" title="'+(valor.ID)+'"><span style="font-size:12px;">'+(valor.telefonos)+'</span><br>'+(valor.nombre)+'</a></td>'+
                '<td><span style="font-size:12px;color:#999;">'+(valor.region+' - '+valor.provincia+' - '+valor.distrito)+'</span><br>'+(valor.direccion)+'</td>'+
                '</tr>';
      });
      $('#grdDatos').html(fila);
    }else{
      $('#grdDatos').html('<tr><td colspan="4" style="text-align:center;color:red;">Sin Resultados para '+txtBuscar+'</td></tr>');
    }
    $('#grdCount').html(resp.agencias.length);
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appAgenciasReset(){
  $("#txtBuscar").val("");
  try{
    const resp = await appAsynFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php");
    
    menu = JSON.parse(resp.menu);
    $("#btn_DEL").toggle(menu.mtto.submenu.agencias.cmdDelete == 1);
    $("#btn_NEW").toggle(menu.mtto.submenu.agencias.cmdInsert == 1);
    appAgenciasGrid();
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appAgenciaNuevo(){
  $("#btnInsert").toggle(menu.mtto.submenu.agencias.cmdInsert == 1);
  $("#btnUpdate").hide();
  try{
    const resp = await appAsynFetch({ TipoQuery:'newAgencia' },rutaSQL);
    $(".form-group").removeClass("has-error");
    $("#hid_agenciaID").val("0");
    $("#txt_Codigo, #txt_Abrev, #txt_Nombre, #txt_Ciudad, #txt_Direccion, #txt_Telefonos, #txt_Observac").val("");
    appLlenarDataEnComboBox(resp.comboRegiones,"#cbo_Region",1014); //region arequipa
    appLlenarDataEnComboBox(resp.comboProvincias,"#cbo_Provincia",1401); //provincia arequipa
    appLlenarDataEnComboBox(resp.comboDistritos,"#cbo_Distrito",140101); //distrito arequipa
    $("#grid").hide();
    $("#edit").show();
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appAgenciaView(agenciaID){
  $("#btnUpdate").toggle(menu.mtto.submenu.agencias.cmdUpdate == 1);
  $("#btnInsert").hide();
  try{
    const resp = await appAsynFetch({ TipoQuery:'editAgencia', agenciaID:agenciaID },rutaSQL);
    //respuesta
    $(".form-group").removeClass("has-error");
    $("#hid_agenciaID").val(resp.ID);
    $("#txt_Codigo").val(resp.codigo);
    $("#txt_Abrev").val(resp.abrev);
    $("#txt_Nombre").val(resp.nombre);
    $("#txt_Ciudad").val(resp.ciudad);
    $("#txt_Direccion").val(resp.direccion);
    $("#txt_Telefonos").val(resp.telefonos);
    $("#txt_Observac").val(resp.observac);
    appLlenarDataEnComboBox(resp.comboRegiones,"#cbo_Region",resp.id_region); //region arequipa
    appLlenarDataEnComboBox(resp.comboProvincias,"#cbo_Provincia",resp.id_provincia); //provincia arequipa
    appLlenarDataEnComboBox(resp.comboDistritos,"#cbo_Distrito",resp.id_distrito); //distrito arequipa
    $('#grid').hide();
    $('#edit').show();
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appAgenciaInsert(){
  let datos = modGetDataToDataBase();
  if(datos!=""){
    datos.TipoQuery = 'insAgencia';
    try{
      const resp = await appAsynFetch(datos,rutaSQL);
      if(!resp.error) {
        appAgenciasGrid();
        appAgenciaCancel();
      }
    } catch(err){
      console.error('Error al cargar datos:', err);
    }
  } else {
    alert("!!!Faltan Datos!!!");
  }
}

async function appAgenciaUpdate(){
  let datos = modGetDataToDataBase();
  if(datos!=""){
    datos.TipoQuery = 'updAgencia';
    try{
      const resp = await appAsynFetch(datos,rutaSQL);
      if(!resp.error){ 
        appAgenciasGrid();
        appAgenciaCancel();
      }
    } catch(err){
      console.error('Error al cargar datos:', err);
    }
  } else {
    alert("!!!Faltan Datos!!!");
  }
}

async function appAgenciasDelete(){
  const arr = $('[name="chk_Borrar"]:checked').map(function() { return this.value}).get();
  if(arr.length>0){
    if(confirm("¿Esta seguro de continuar?")) {
      try{
        const resp = await appAsynFetch({ TipoQuery:'delAgencias', arr:arr },rutaSQL);
        if (!resp.error) { //sin errores
          appAgenciasGrid();
          appAgenciaCancel();
        }
      } catch(err){
        console.error('Error al cargar datos:', err);
      }
    }
  } else {
    alert("NO eligio borrar ninguno");
  }
}

function modGetDataToDataBase(){
  let rpta = "";
  let esError = false;

  $(".form-group").removeClass("has-error");
  if($("#txt_Codigo").val()==="") { $("#pn_Codigo").addClass("has-error"); esError = true; }
  if($("#txt_Abrev").val()==="")  { $("#pn_Abrev").addClass("has-error"); esError = true; }
  if($("#txt_Nombre").val()==="") { $("#pn_Nombre").addClass("has-error"); esError = true; }
  if($("#txt_Ciudad").val()==="") { $("#pn_Ciudad").addClass("has-error"); esError = true; }

  if(!esError){
    rpta = {
      ID : $("#hid_agenciaID").val(),
      codigo : $("#txt_Codigo").val(),
      abrev : $("#txt_Abrev").val(),
      nombre : $("#txt_Nombre").val(),
      ciudad : $("#txt_Ciudad").val(),
      direccion : $("#txt_Direccion").val(),
      ubigeoID : $("#cbo_Distrito").val(),
      telefonos : $("#txt_Telefonos").val(),
      observac : $("#txt_Observac").val()
    }
  }
  return rpta;
}

function appAgenciaCancel(){
  $('#grid').show();
  $('#edit').hide();
}

async function comboProvincias(){
  try{
    const resp = ({ 
      TipoQuery : "comboUbigeo",
      tipoID  : 3,
      padreID : $("#cbo_Region").val()
    },rutaSQL);
    //respuesta
    appLlenarDataEnComboBox(resp.provincias,"#cbo_Provincia",0); //provincia
    appLlenarDataEnComboBox(resp.distritos,"#cbo_Distrito",0); //distrito
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function comboDistritos(){
  try{
    const resp = await appAsynFetch({ 
      TipoQuery : "comboUbigeo",
      tipoID  : 4,
      padreID : $("#cbo_Provincia").val()
    },rutaSQL);
    //respuesta
    appLlenarDataEnComboBox(resp.distritos,"#cbo_Distrito",0); //distrito
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}