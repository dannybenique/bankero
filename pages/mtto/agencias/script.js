const rutaSQL = "pages/mtto/agencias/sql.php";
var menu = "";

//=========================funciones para Personas============================
async function appAgenciasGrid(){
  document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="4"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  const disabledDelete = (menu.mtto.submenu.agencias.cmdDelete===1) ? "" : "disabled";
  const txtBuscar = document.querySelector("#txtBuscar").value;
  try{
    const resp = await appAsynFetch({ TipoQuery:'selAgencias', buscar:txtBuscar },rutaSQL);
    document.querySelector("#chk_All").disabled = (menu.mtto.submenu.agencias.cmdDelete===1) ? false : true;
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
      document.querySelector('#grdDatos').innerHTML = (fila);
    }else{
      document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="4" style="text-align:center;color:red;">Sin Resultados para '+txtBuscar+'</td></tr>');
    }
    document.querySelector('#grdCount').innerHTML = (resp.agencias.length);
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appAgenciasReset(){
  document.querySelector("#txtBuscar").value = ("");
  try{
    const resp = await appAsynFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php");
    menu = JSON.parse(resp.menu);
    document.querySelector("#btn_DEL").style.display = (menu.mtto.submenu.agencias.cmdDelete==1)?('inline'):('none');
    document.querySelector("#btn_NEW").style.display = (menu.mtto.submenu.agencias.cmdInsert==1)?('inline'):('none');
    appAgenciasGrid();
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

function appAgenciasBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { appAgenciasGrid(); }
}

async function appAgenciaNuevo(){
  document.querySelector("#btnInsert").style.display = (menu.mtto.submenu.agencias.cmdInsert==1)?('inline'):('none');
  document.querySelector("#btnUpdate").style.display = 'none';
  try{
    const resp = await appAsynFetch({ TipoQuery:'newAgencia' },rutaSQL);
    $(".form-group").removeClass("has-error");
    document.querySelector("#hid_agenciaID").value = ("0");
    document.querySelector("#txt_Codigo").value = ("");
    document.querySelector("#txt_Abrev").value = ("");
    document.querySelector("#txt_Nombre").value = ("");
    document.querySelector("#txt_Ciudad").value = ("");
    document.querySelector("#txt_Direccion").value = ("");
    document.querySelector("#txt_Telefonos").value = ("");
    document.querySelector("#txt_Observac").value = ("");
    appLlenarDataEnComboBox(resp.comboRegiones,"#cbo_Region",1014); //region arequipa
    appLlenarDataEnComboBox(resp.comboProvincias,"#cbo_Provincia",1401); //provincia arequipa
    appLlenarDataEnComboBox(resp.comboDistritos,"#cbo_Distrito",140101); //distrito arequipa
    document.querySelector("#grid").style.display = 'none';
    document.querySelector("#edit").style.display = 'block';
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appAgenciaView(agenciaID){
  document.querySelector("#btnUpdate").style.display = (menu.mtto.submenu.agencias.cmdUpdate==1)?('inline'):('none');
  document.querySelector("#btnInsert").style.display = 'none';
  try{
    const resp = await appAsynFetch({
      TipoQuery : 'editAgencia',
      agenciaID : agenciaID
    },rutaSQL);
    //respuesta
    $(".form-group").removeClass("has-error");
    document.querySelector("#hid_agenciaID").value = resp.ID;
    document.querySelector("#txt_Codigo").value = (resp.codigo);
    document.querySelector("#txt_Abrev").value = (resp.abrev);
    document.querySelector("#txt_Nombre").value = (resp.nombre);
    document.querySelector("#txt_Ciudad").value = (resp.ciudad);
    document.querySelector("#txt_Direccion").value = (resp.direccion);
    document.querySelector("#txt_Telefonos").value = (resp.telefonos);
    document.querySelector("#txt_Observac").value = (resp.observac);
    appLlenarDataEnComboBox(resp.comboRegiones,"#cbo_Region",resp.id_region); //region arequipa
    appLlenarDataEnComboBox(resp.comboProvincias,"#cbo_Provincia",resp.id_provincia); //provincia arequipa
    appLlenarDataEnComboBox(resp.comboDistritos,"#cbo_Distrito",resp.id_distrito); //distrito arequipa
    document.querySelector('#grid').style.display = 'none';
    document.querySelector('#edit').style.display = 'block';
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
  //let arr = $('[name="chk_Borrar"]:checked').map(function(){return this.value}).get();
  let arr = Array.from(document.querySelectorAll('[name="chk_Borrar"]:checked')).map(function(obj){return obj.attributes[2].nodeValue});
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
  if(document.querySelector("#txt_Codigo").value=="") { document.querySelector("#pn_Codigo").className = "form-group has-error"; esError = true; }
  if(document.querySelector("#txt_Abrev").value=="")  { document.querySelector("#pn_Abrev").className = "form-group has-error"; esError = true; }
  if(document.querySelector("#txt_Nombre").value=="") { document.querySelector("#pn_Nombre").className = "form-group has-error"; esError = true; }
  if(document.querySelector("#txt_Ciudad").value=="") { document.querySelector("#pn_Ciudad").className = "form-group has-error"; esError = true; }

  if(!esError){
    rpta = {
      ID : document.querySelector("#hid_agenciaID").value,
      codigo : document.querySelector("#txt_Codigo").value,
      abrev : document.querySelector("#txt_Abrev").value,
      nombre : document.querySelector("#txt_Nombre").value,
      ciudad : document.querySelector("#txt_Ciudad").value,
      direccion : document.querySelector("#txt_Direccion").value,
      ubigeoID : document.querySelector("#cbo_Distrito").value,
      telefonos : document.querySelector("#txt_Telefonos").value,
      observac : document.querySelector("#txt_Observac").value
    }
  }
  return rpta;
}

function appAgenciaCancel(){
  document.querySelector('#grid').style.display = 'block';
  document.querySelector('#edit').style.display = 'none';
}

async function comboProvincias(){
  try{
    const resp = ({ 
      TipoQuery : "comboUbigeo",
      tipoID  : 3,
      padreID : document.querySelector("#cbo_Region").value
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
      padreID : document.querySelector("#cbo_Provincia").value
    },rutaSQL);
    //respuesta
    appLlenarDataEnComboBox(resp.distritos,"#cbo_Distrito",0); //distrito
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}