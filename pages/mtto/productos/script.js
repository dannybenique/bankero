const rutaSQL = "pages/mtto/productos/sql.php";
var menu = "";

//=========================funciones para Personas============================
function appProductosBuscar(e){ if(e.keyCode === 13) { appProductosGrid(); } }

async function appProductosGrid(){
  $('#grdDatos').html('<tr><td colspan="7"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  const disabledDelete = (menu.mtto.submenu.productos.cmdDelete===1) ? "" : "disabled";
  const txtBuscar = $("#txtBuscar").val();
  try{
    const resp = await appAsynFetch({TipoQuery:'selProductos', buscar:txtBuscar},rutaSQL);
    $("#chk_All").prop("disabled", !(menu.mtto.submenu.productos.cmdDelete === 1));
      
    if(resp.productos.length>0){
      let fila = "";
      resp.productos.forEach((valor,key)=>{
        fila += '<tr>'+
                '<td><input type="checkbox" name="chk_Borrar" value="'+(valor.ID)+'" '+(disabledDelete)+'/></td>'+
                '<td style="text-align:center;">'+(valor.codigo)+'</td>'+
                '<td style="text-align:center;">'+((valor.obliga==1)?('<i class="fa fa-info-circle" style="color:#AF2031;" title="Obligatorio"></i>'):(''))+'</td>'+
                '<td><a href="javascript:appProductoView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.producto)+'</a></td>'+
                '<td>'+(valor.abrevia)+'</td>'+
                '<td>'+(valor.tipo_oper)+'</td>'+
                '<td></td>'+
                '</tr>';
      });
      $('#grdDatos').html(fila);
    }else{
      $('#grdDatos').html('<tr><td colspan="7" style="text-align:center;color:red;">Sin Resultados para '+txtBuscar+'</td></tr>');
    }
    $('#grdCount').html(resp.productos.length);
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appProductosReset(){
  $("#txtBuscar").val("");
  try{
    const resp = await appAsynFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php");
    menu = JSON.parse(resp.menu);
    $("#btn_DEL").toggle(menu.mtto.submenu.productos.cmdDelete == 1);
    $("#btn_NEW").toggle(menu.mtto.submenu.productos.cmdInsert == 1);
    appProductosGrid();
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appProductoNuevo(){
  $(".form-group").removeClass("has-error");
  $("#btnInsert").toggle(menu.mtto.submenu.productos.cmdInsert == 1);
  $("#btnUpdate").hide();
  $("#hid_productoID, #cbo_Obliga").val("0");
  $("#txt_Codigo, #txt_Abrev, #txt_Nombre").val("");
  try{
    const resp = await appAsynFetch({ TipoQuery:'startProducto' },rutaSQL);
    appLlenarDataEnComboBox(resp.comboTipoProd,"#cbo_Tipo",0); //tipos de producto
    $("#grid").hide();
    $("#edit").show();
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appProductoView(productoID){
  $(".form-group").removeClass("has-error");
  $("#btnUpdate").toggle(menu.mtto.submenu.productos.cmdUpdate == 1);
  $("#btnInsert").hide();

  try{
    const resp = await appAsynFetch({
      TipoQuery : 'editProducto',
      productoID : productoID
    }, rutaSQL);
    //respuesta
    $("#hid_productoID").val(resp.ID);
    $("#txt_Codigo").val(resp.codigo);
    $("#txt_Abrev").val(resp.abrev);
    $("#txt_Nombre").val(resp.nombre);
    $("#cbo_Obliga").val((resp.obliga)?1:0);
    appLlenarDataEnComboBox(resp.comboTipoProd,"#cbo_Tipo",resp.id_padre); //tipo producto
    $('#grid').hide();
    $('#edit').show();
  } catch(err){
    console.error('Error al cargar datos:', err);
  }
}

async function appProductoInsert(){
  let datos = modGetDataToDataBase();
  if(datos!=""){
    datos.TipoQuery = 'insProducto';
    try{
      const resp = await appAsynFetch(datos, rutaSQL);
      if(!resp.error) { appProductoCancel(); }
    } catch(err){
      console.error('Error al cargar datos:', err);
    }
  } else {
    alert("!!!Faltan Datos!!!");
  }
}

async function appProductoUpdate(){
  let datos = modGetDataToDataBase();
  if(datos!=""){
    datos.TipoQuery = 'updProducto';
    try{
      const resp = await appAsynFetch(datos, rutaSQL);
      if(!resp.error) { appProductoCancel(); }
    } catch(err){
      console.error('Error al cargar datos:', err);
    }
  } else {
    alert("!!!Faltan Datos!!!");
  }
}

async function appProductosBorrar(){
  const arr = $('[name="chk_Borrar"]:checked').map(function(){return this.value}).get();
  if(arr.length>0){
    if(confirm("¿Esta seguro de continuar?")) {
      try{
        const resp = await appAsynFetch({TipoQuery:'delProductos', arr:arr},rutaSQL);
        if (!resp.error) { appProductoCancel(); }
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
  if($("#txt_Abrev").val()==="")  { $("#div_Abrev").addClass("has-error"); esError = true; }
  if($("#txt_Nombre").val()==="") { $("#div_Nombre").addClass("has-error"); esError = true; }

  if(!esError){
    rpta = {
      ID : $("#hid_productoID").val(),
      abrevia : $("#txt_Abrev").val(),
      nombre : $("#txt_Nombre").val(),
      obliga : $("#cbo_Obliga").val(),
      tipoID : $("#cbo_Tipo").val()
    }
  }
  return rpta;
}

function appProductoCancel(){
  appProductosGrid();
  $('#grid').show();
  $('#edit').hide();
}
