const rutaSQL = "pages/repo/extractobanca/sql.php";
var menu = "";

//=========================funciones para Personas============================
function appMovimGrid(){
  document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="7"><div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></td></tr>');
  let txtBuscar = document.querySelector("#txtBuscar").value;
  let datos = { TipoQuery: 'selMovim', buscar:txtBuscar };

  appFetch(datos,rutaSQL).then(resp => {
    let disabledDelete = (menu.caja.submenu.movim.cmdDelete===1) ? "" : "disabled";
    document.querySelector("#chk_All").disabled = (menu.caja.submenu.movim.cmdDelete===1) ? false : true;
    if(resp.pagos.length>0){
      let fila = "";
      resp.pagos.forEach((valor,key)=>{
        fila += '<tr>';
        fila += '<td><input type="checkbox" name="chk_Borrar" value="'+(valor.ID)+'" '+(disabledDelete)+'/></td>';
        fila += '<td style="text-align:center;">'+(valor.fecha)+' <span style="font-size:10px;color:#888;">'+(valor.hora)+'</span></td>';
        fila += '<td><a href="javascript:appMovimView('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.codigo)+'</a></td>';
        fila += '<td>'+(valor.agencia)+'</td>';
        fila += '<td>'+(valor.cajera)+'</td>';
        fila += '<td>'+(valor.socio)+'</td>';
        fila += '<td style="text-align:right;">'+(appFormatMoney(valor.importe,2))+'</td>';
        fila += '</tr>';
      });
      document.querySelector('#grdDatos').innerHTML = (fila);
    }else{
      document.querySelector('#grdDatos').innerHTML = ('<tr><td colspan="7" style="text-align:center;color:red;">Sin Resultados para '+txtBuscar+'</td></tr>');
    }
    document.querySelector('#grdCount').innerHTML = (resp.pagos.length);
  });
}

function appMovimReset(){
  appFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php").then(resp => {
    menu = JSON.parse(resp.menu);
    document.querySelector("#btn_DEL").style.display = (menu.caja.submenu.movim.cmdDelete==1)?('inline'):('none');
    document.querySelector("#btn_NEW").style.display = (menu.caja.submenu.movim.cmdInsert==1)?('inline'):('none');
    
    document.querySelector("#txtBuscar").value = ("");
    appMovimGrid();
  });
}

function appMovimBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { appMovimGrid(); }
}

function appProductoNuevo(){
  document.querySelector("#btnInsert").style.display = (menu.mtto.productos.cmdInsert==1)?('inline'):('none');
  document.querySelector("#btnUpdate").style.display = 'none';
  appFetch({ TipoQuery:'startProducto' },rutaSQL).then(resp => {
    try{
      $(".form-group").removeClass("has-error");
      document.querySelector("#hid_productoID").value = ("0");
      document.querySelector("#txt_Codigo").value = ("");
      document.querySelector("#txt_Abrev").value = ("");
      document.querySelector("#txt_Nombre").value = ("");
      appLlenarDataEnComboBox(resp.comboTipoProd,"#cbo_Tipo",0); //tipos de producto
      document.querySelector("#grid").style.display = 'none';
      document.querySelector("#edit").style.display = 'block';
    } catch (err){
      console.log(err);
    }
  });
}

function appMovimView(voucherID){
  document.querySelector('#grid').style.display = 'none';
  document.querySelector('#edit').style.display = 'block';
  $(".form-group").removeClass("has-error");

  let datos = {
    TipoQuery : 'viewMovim',
    voucherID : voucherID
  }
  appFetch(datos,rutaSQL).then(resp => {
    //cabecera
    document.querySelector("#hid_movimID").value = resp.cab.ID;
    document.querySelector("#lbl_pagoAgencia").innerHTML = (resp.cab.agencia);
    document.querySelector("#lbl_pagoTipoOper").innerHTML = (resp.cab.tipo_oper+" / "+resp.cab.moneda);
    document.querySelector("#lbl_pagoCodigo").innerHTML = (resp.cab.codigo);
    document.querySelector("#lbl_pagoFecha").innerHTML = (resp.cab.fecha+" <small style='font-size:10px;'>"+resp.cab.hora+"</small>");
    document.querySelector("#lbl_pagoSocio").innerHTML = (resp.cab.socio);
    document.querySelector("#lbl_tipodui").innerHTML = (resp.cab.tipodui+":");
    document.querySelector("#lbl_pagoNroDUI").innerHTML = (resp.cab.nrodui);
    document.querySelector("#lbl_pagoCajera").innerHTML = (resp.cab.cajera);
    document.querySelector("#lbl_pagoImporte").innerHTML = "<small style='font-size:10px;'>"+resp.cab.mon_abrevia+"</small> "+appFormatMoney(resp.cab.importe,2);
    
    //detalle
    if(resp.deta.length>0){
      let fila = "";
      resp.deta.forEach((valor,key)=>{
        fila += '<tr>';
        fila += '<td style="text-align:center;">'+(valor.item)+'</td>';
        fila += '<td>'+(valor.tipo_mov)+'</td>';
        fila += '<td>'+(valor.producto)+'</td>';
        fila += '<td style="text-align:right;">'+(appFormatMoney(valor.importe,2))+'</td>';
        fila += '</tr>';
      });
      document.querySelector('#grdDetalleDatos').innerHTML = (fila);
    }else{
      document.querySelector('#grdDetalleDatos').innerHTML = ('<tr><td colspan="4" style="text-align:center;color:red;">Sin DETALLE</td></tr>');
    }

    document.querySelector('#grid').style.display = 'none';
    document.querySelector('#edit').style.display = 'block';
  });
}

function appMovimRefresh(){
  let codigo = document.querySelector("#hid_movimID").value;
  appMovimView(codigo);
}

function appProductoInsert(){
  let datos = modGetDataToDataBase();
  if(datos!=""){
    datos.TipoQuery = 'insProducto';
    appFetch(datos,rutaSQL).then(resp => {
      appProductoCancel();
    });
  } else {
    alert("!!!Faltan Datos!!!");
  }
}

function appProductoUpdate(){
  let datos = modGetDataToDataBase();
  if(datos!=""){
    datos.TipoQuery = 'updProducto';
    appFetch(datos,rutaSQL).then(resp => {
      appProductoCancel();
    });
  } else {
    alert("!!!Faltan Datos!!!");
  }
}

function appProductosBorrar(){
  //let arr = $('[name="chk_Borrar"]:checked').map(function(){return this.value}).get();
  let arr = Array.from(document.querySelectorAll('[name="chk_Borrar"]:checked')).map(function(obj){return obj.attributes[2].nodeValue});
  if(arr.length>0){
    if(confirm("¿Esta seguro de continuar?")) {
      appFetch({ TipoQuery:'delProductos', arr:arr },rutaSQL).then(resp => {
        if (resp.error == false) { //sin errores
          appProductoCancel();
        }
      });
    }
  } else {
    alert("NO eligio borrar ninguno");
  }
}

function modGetDataToDataBase(){
  let rpta = "";
  let esError = false;

  $(".form-group").removeClass("has-error");
  if(document.querySelector("#txt_Abrev").value=="")  { document.querySelector("#div_Abrev").className = "form-group has-error"; esError = true; }
  if(document.querySelector("#txt_Nombre").value=="") { document.querySelector("#div_Nombre").className = "form-group has-error"; esError = true; }

  if(!esError){
    rpta = {
      ID : document.querySelector("#hid_productoID").value,
      abrevia : document.querySelector("#txt_Abrev").value,
      nombre : document.querySelector("#txt_Nombre").value,
      tipoID : document.querySelector("#cbo_Tipo").value
    }
  }
  return rpta;
}

function appMovimCancel(){
  appMovimGrid();
  document.querySelector('#grid').style.display = 'block';
  document.querySelector('#edit').style.display = 'none';
}
