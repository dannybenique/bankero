var rutaSQL = "pages/conta/cuentas/sql.php";
//=========================funciones para Agencias============================
function appCuentasRootGetAll(){
  let setting = {
    async: {
      enable : true,
      url    : rutaSQL,
      type   : "post",
      autoParam  : ["id", "name", "level"],
      otherParam : {"appSQL":JSON.stringify({"TipoQuery":'selContaCuentas'})},
      dataFilter : appCuentasFiltro
    }
  };
  $.fn.zTree.init($("#appTreeView"), setting);
}

function appCuentasResetNodo(){
  let zTree = $.fn.zTree.getZTreeObj("appTreeView");
  let nodes = zTree.getSelectedNodes();
  if (nodes.length == 0) {
    appCuentasRootGetAll();
  } else {
    let nodo = nodes[0];
    zTree.reAsyncChildNodes(nodo, "refresh");
    zTree.selectNode(nodo);
  }
}

function appCuentasFiltro(treeId, parentNode, childNodes) {
  return childNodes;
}

function appCuentasNew(nodo){
  $("#txt_ID").val("0");
  $("#txt_Codigo").val("");
  $("#txt_Nombre").val("");
  $("#txt_Parent").val(nodo.id+" - "+nodo.name);
  $("#hid_ParentID").val(nodo.id);
  $("#btn_modalCuentasInsert").show();
  $("#btn_modalCuentasUpdate").hide();

  $("#div_Codigo").prop("class","form-group");
  $("#div_Nombre").prop("class","form-group");

  $("#modalCuentas").modal();
}

function appCuentasEdit(nodo){
  let datos = {
    TipoQuery : 'editContaCuenta',
    ID : nodo.id
  };
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    $("#txt_ID").val(resp.tabla.ID);
    $("#txt_Codigo").val(resp.tabla.codigo);
    $("#txt_Nombre").val(resp.tabla.nombre);
    $("#txt_Parent").val(resp.tabla.nombrePadre);

    $("#btn_modalCuentasUpdate").show();
    $("#btn_modalCuentasInsert").hide();

    $("#div_Codigo").prop("class","form-group");
    $("#div_Nombre").prop("class","form-group");

    $("#modalCuentas").modal();
  });
}

function modalCuentasInsert(){
  let datos = appGetDataToDataBase();
  if(datos!=""){
    datos.commandSQL = "INS";
    appAjaxInsert(datos,rutaSQL).done(function(resp){
      let zTree = $.fn.zTree.getZTreeObj("appTreeView");
      let nodes = zTree.getSelectedNodes();
      let nodo = nodes[0];
      if(nodo.isParent==false) {nodo.isParent=true;}
      zTree.reAsyncChildNodes(nodo, "refresh");
      zTree.selectNode(nodo);
      $("#modalCuentas").modal("hide");
    });
  }
}

function modalCuentasUpdate(){
  let datos = appGetDataToDataBase();
  if(datos!=""){
    datos.commandSQL = "UPD";
    appAjaxUpdate(datos,rutaSQL).done(function(resp){
      let zTree = $.fn.zTree.getZTreeObj("appTreeView");
      let nodes = zTree.getSelectedNodes();
      let nodo = nodes[0];
      nodo.name = $("#txt_Codigo").val()+" - "+$("#txt_Nombre").val();
      zTree.updateNode(nodo);
      zTree.selectNode(nodo);
      $("#modalCuentas").modal("hide");
    });
  }
}

function refreshNode(e) {
  var zTree = $.fn.zTree.getZTreeObj("appTreeView");
  var nodes = zTree.getSelectedNodes();
  if (nodes.length == 0) {
    alert("!!!Debe seleccionar una CUENTA!!!");
  } else {
    switch(e.data.type){
      case "add": appCuentasNew(nodes[0]); break;
      case "edt": appCuentasEdit(nodes[0]); break;
    }
  }
}

function appGetDataToDataBase(){
  let EsError = false;
  let rpta = "";

  $('.box-body .form-group').removeClass('has-error');
  if($("#txt_Codigo").val()=="") { $("#div_Codigo").prop("class","form-group has-error"); EsError = true; }
  if($("#txt_Nombre").val()=="") { $("#div_Nombre").prop("class","form-group has-error"); EsError = true; }

  if(!EsError){
    rpta = {
      TipoQuery : 'execContaCuenta',
      ID : $("#txt_ID").val(),
      codigo : $("#txt_Codigo").val(),
      nombre : $("#txt_Nombre").val(),
      padreID : $("#hid_ParentID").val()
    }
  }
  return rpta;
}
