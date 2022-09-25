//coloca "0" a numeros menores de 10
function pad(s) {
  return (s < 10) ? "0" + s : s;
}

//rellena "0" a la izquierda depenediendo del width
function zfill(number,width){
  let numberOutput = Math.abs(number); /* Valor absoluto del número */
  let length = number.toString().length; /* Largo del número */
  let zero = "0"; /* String de cero */

  if (width <= length) {
      if (number < 0) { return ("-" + numberOutput.toString()); }
      else { return numberOutput.toString(); }
  } else {
      if (number < 0) { return ("-" + (zero.repeat(width - length)) + numberOutput.toString()); }
      else { return ((zero.repeat(width - length)) + numberOutput.toString()); }
  }
}

//formatea un numero a 2 decimales y con separador de miles
function appFormatMoney(num, c) {
  // c = cantidad decimales
  var d = "."; //decimales
  var t = ","; //miles
  var s = num < 0 ? "-" : "";
  var i = String(parseInt(num = Math.abs(Number(num) || 0).toFixed(c)));
  var j = (j = i.length) > 3 ? j % 3 : 0;

  return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(num - i).toFixed(c).slice(2) : "");
};

//selecciona todas las filas en una Grid
function SelectAll(CheckBox, chkChild, appGridName) {
  var TargetBaseControl = document.getElementById(appGridName);
  var TargetChildControl = chkChild;
  var Inputs = TargetBaseControl.getElementsByTagName("input");
  for(var iCount = 0; iCount < Inputs.length; ++iCount)  {
    if(Inputs[iCount].type == 'checkbox' && Inputs[iCount].id.indexOf(TargetChildControl,0) >= 0)
    Inputs[iCount].checked = CheckBox.checked;
  }
}

//selecciona todas las filas en una Grid
function toggleAll(source,name){
  let checkboxes = document.getElementsByName(name);
  for(let x=0; x<checkboxes.length; x++) {
    checkboxes[x].checked = source.checked;
  }
}

//devuleve la URL absoluta del servidor
function appUrlServer(){
  let loc = window.location;
  let pathName = loc.pathname.substring(0, loc.pathname.lastIndexOf('/') + 1);
  return loc.href.substring(0, loc.href.length - ((loc.pathname + loc.search + loc.hash).length - pathName.length));
}

//convierte una fecha dd/mm/yyyy a yyyymmdd o yyyy-mm-dd (dependiendo del simbolo= "" "-")
function appConvertToFecha(miFecha,simbolo){
  var fecha = miFecha.split("/");
  var rpta = fecha.reverse().join(simbolo);
  return rpta;
}

//convierte un numero formateado con comas a numero
function appConvertToNumero(numFormateado){
  var preNumero = numFormateado.split(",");
  var rpta = preNumero.join("");
  return Number(rpta);
}

//establecer un texto de un textbox o combobox a un label
function appSetTexto(miTarget,miSource,esCombo){
  if(esCombo){
    $(miTarget).html($(miSource+" option:selected").text());
  } else{
    $(miTarget).html($(miSource).val());
  }
}

//rellena un ComboBox con la data de la DB
function appComboBox(miCombo,miSQL,miValor){
  let datos = {TipoQuery:'ComboBox', miSubSelect:miSQL};
  appAjaxSelect(datos).done(function(resp){
    if(miSQL=="Agencias"){ resp = resp.tabla; }
    appLlenarDataEnComboBox(resp,miCombo,miValor);
  });
}

//rellena un ComboBox para extracto Bancario
function appComboExtrBancario(miCombo,subSelect,socioID,padreID,miValor){
  let datos = {
    TipoQuery : 'ComboBox',
    miSubSelect : subSelect,
    miSocioID : socioID,
    miPadreID : padreID
  }
  let rpta = $.ajax({
    url:'includes/sql_select.php',
    type:'POST',
    dataType:'json',
    data:{"appSQL":JSON.stringify(datos)}
  })
  .done(function(resp){
    appLlenarDataEnComboBox(resp,miCombo,miValor);
    /*
    let miData = "";
    let miSelect = "";

    $.each(resp,function(key, valor){
      if(valor.ID==miValor) { miSelect = " selected"; } else { miSelect = "";}
      miData += '<option value="'+(valor.ID)+'" '+miSelect+'>'+(valor.nombre)+'</option>';
    });
    $(miCombo).html(miData);
    */
  })
  .fail(function(resp){
    $("#cbo_ExtrProducto").html("");;
  });
}

//obtiene el ubigeo de una determinada Ubicacion=region, provincia, distrito
function appComboUbiGeo(miCombo,padreID,miValor){
  let datos = {
    TipoQuery : 'ComboBox',
    miSubSelect : 'UbiGeo',
    miPadreID: padreID
  }
  appAjaxSelect(datos).done(function(resp){
    appLlenarDataEnComboBox(resp,miCombo,miValor);
  });
}

//llena todas las agencias incluyendo la opcion Todas
function appLlenarComboAgencias(miCombo,miSelected,data){
  data.push({"ID":0,"nombre":"Todas las AG."});
  appLlenarDataEnComboBox(data,miCombo,miSelected);
}

//llenar un combobox con la data YA extraida de la DB
function appLlenarDataEnComboBox(data,miComboBox,valorSelect){
  let miData = "";
  $.each(data,function(key, valor){ miData += '<option value="'+(valor.ID)+'" '+((valor.ID==valorSelect) ? ("selected") : (""))+'>'+(valor.nombre)+'</option>'; });
  $(miComboBox).html(miData);
}

//llama a la funcion AJAX para obtener los datos
function appAjaxSelect(datos,ruta){
  let uri = (ruta==undefined) ? ("includes/sql_select.php") : (ruta);
  let rpta = $.ajax({
    url : uri,
    type : 'POST',
    dataType : 'json',
    data : {"appSQL":JSON.stringify(datos)}
  })
  .fail(function(resp){
    console.log("fail:.... "+resp.responseText);
  });
  return rpta;
}

//llama a la funcion Ajax para INSERT
function appAjaxInsert(datos,ruta){
  let uri = (ruta==undefined) ? ("includes/sql_insert.php") : (ruta);
  let rpta = $.ajax({
    url:uri,
    type:'POST',
    dataType:'json',
    data:{"appSQL":JSON.stringify(datos)}
  })
  .fail(function(resp){
    console.log("fail:.... "+resp.responseText);
  });
  return rpta;
}

//llama a la funcion Ajax para UPDATE
function appAjaxUpdate(datos,ruta){
  let uri = (ruta==undefined) ? ("includes/sql_update.php") : (ruta);
  let rpta = $.ajax({
    url:uri,
    type:'POST',
    dataType:'json',
    data:{"appSQL":JSON.stringify(datos)}
  })
  .fail(function(resp){
    console.log("fail:.... "+resp.responseText);
  });
  return rpta;
}

//llama a la funcion Ajax para DELETE
function appAjaxDelete(datos,ruta){
  let uri = (ruta==undefined) ? ("includes/sql_delete.php") : (ruta);
  let rpta = $.ajax({
    url:uri,
    type:'POST',
    dataType:'json',
    data:{"appSQL":JSON.stringify(datos)}
  })
  .fail(function(resp){
    console.log("fail:.... "+resp.responseText);
  });
  return rpta;
}

//Llama a la funcion Ajax para ejecutar CIERRES
function appAjaxCierre(datos,ruta){
  let rpta = $.ajax({
    url:ruta,
    type:'POST',
    dataType:'json',
    data:{"appSQL":JSON.stringify(datos)}
  })
  .fail(function(resp){
    console.log("fail:.... "+resp.responseText);
  });
  return rpta;
}
