var rutaSQL = "pages/rrhh/postulantes/sql.php";

//=========================funciones para Recursos Humanos RRHH============================
function rrhhPostulantesGetAll(){
  let agenciaID = $("#cboAgencias").val();
  let txtBuscar = $("#txtBuscar").val();
  let datos = {
    TipoQuery : 'postulantes',
    agenciaID : agenciaID,
    miBuscar : txtBuscar }
  appAjaxSelect(datos,rutaSQL).done(function(resp){
    if(resp.tabla.length>0){
      let workerData = "";
      $.each(resp.tabla,function(key, valor){
        workerData += '<tr>';
        workerData += '<td><input type="checkbox" id="chk_Borrar" name="chk_Borrar" value="'+(valor.ID)+'"/></td>';
        workerData += '<td>'+(valor.DNI)+'</td>';
        workerData += '<td><a href="javascript:rrhhPostulanteEdit('+(valor.ID)+');" title="'+(valor.ID)+'">'+(valor.postulante)+'</a></td>';
        workerData += '<td>'+(valor.celular)+'</td>';
        workerData += '<td>'+(valor.cargo)+'</td>';
        workerData += '<td>'+(valor.condicion)+'</td>';
        workerData += '<td>'+(valor.agencia)+'</td>';
        workerData += '<td>'+(valor.fecha)+'</td>';
        workerData += '</tr>';
      });
      $('#grdDatosBody').html(workerData);
    }else{
      $('#grdDatosBody').html('<tr><td colspan="8" style="text-align:center;color:red;">Sin Resultados para '+txtBuscar+'</td></tr>');
    }
    $('#grdDatosCount').html(resp.tabla.length+"/"+resp.cuenta);
  });
}

function rrhhPostulantesReset(){
  $("#txtBuscar").val("");
  let datos = { TipoQuery:'ComboBox', miSubSelect:'Agencias' };
  appAjaxSelect(datos).done(function(resp){
    appLlenarComboAgencias("#cboAgencias",0,resp.tabla);
    rrhhPostulantesGetAll();
  })
}

function rrhhPostulantesBuscar(e){
  let code = (e.keyCode ? e.keyCode : e.which);
  if(code == 13) { rrhhPostulantesGetAll(); }
}

function appPersonaSetData(data){
  //info corta
  $('#img_Foto').prop("src",(data.urlfoto=="") ? ("data/personas/images/0noFotoUser.jpg") : (data.urlfoto));
  $("#lbl_Nombres").html(data.nombres);
  $("#lbl_Apellidos").html(data.ap_paterno+" "+data.ap_materno);
  $("#lbl_ID").html(data.ID);
  $("#lbl_TipoDNI").html(data.tipoDNI);
  $("#lbl_DNI").html(data.nroDNI);
  $("#lbl_celular").html(data.celular);

  //pestaña datos personales
  if(data.tipoPersona==2){ //persona juridica
    $("#lbl_PersTipoNombres").html("Razon Social");
    $("#lbl_PersTipoProfesion").html("Rubro");
    $("#lbl_PersTipoApellidos").hide();
    $("#lbl_PersTipoSexo").hide();
    $("#lbl_PersTipoECivil").hide();
    $("#lbl_PersTipoGIntruc").hide();
  }else{
    $("#lbl_PersTipoNombres").html("Nombres");
    $("#lbl_PersTipoProfesion").html("Profesion");
    $("#lbl_PersTipoApellidos").show();
    $("#lbl_PersTipoSexo").show();
    $("#lbl_PersTipoECivil").show();
    $("#lbl_PersTipoGIntruc").show();
  }
  $("#lbl_PersNombres").html(data.nombres);
  $("#lbl_PersApellidos").html(data.ap_paterno+" "+data.ap_materno);
  $("#lbl_PersTipoDNI").html(data.tipoDNI);
  $("#lbl_PersNroDNI").html(data.nroDNI);
  $("#lbl_PersFechaNac").html(data.fechanac);
  $("#lbl_PersLugarNac").html(data.lugarnac);
  $("#lbl_PersSexo").html(data.sexo);
  $("#lbl_PersEcivil").html(data.ecivil);
  $("#lbl_PersCelular").html(data.celular);
  $("#lbl_PersTelefijo").html(data.fijo);
  $("#lbl_PersEmail").html(data.correo);
  $("#lbl_PersTipoVivienda").html(data.tipovivienda);
  $("#lbl_PersGInstruccion").html(data.ginstruc);
  $("#lbl_PersProfesion").html(data.profesion);
  $("#lbl_PersOcupacion").html(data.ocupacion);
  $("#lbl_PersUbicacion").html(data.region+" - "+data.provincia+" - "+data.distrito);
  $("#lbl_PersDireccion").html(data.direccion);
  $("#lbl_PersReferencia").html(data.referencia);
  $("#lbl_PersMedidorluz").html(data.medidorluz);
  $("#lbl_PersTipovivienda").html(data.tipovivienda);
  $("#lbl_PersObservac").html(data.observPers);
  $("#lbl_PersSysFecha").html(data.sysfechaPers);
  $("#lbl_PersSysUser").html(data.sysuserPers);

  //permisos
  if(data.tablaUser.usernivel==data.tablaUser.admin) {
    $("#btn_PersUpdate").show();
    $("#btn_PersPermiso").hide();
  } else {
    switch(data.permisoPersona.estado){
      case 0: $("#btn_PersPermiso").show(); $("#btn_PersUpdate").hide(); break; //sin permisos
      case 1: $("#btn_PersPermiso").hide(); $("#btn_PersUpdate").hide(); break; //pendiente de confirmar
      case 2: $("#btn_PersPermiso").hide(); $("#btn_PersUpdate").show(); break; //permiso concedido
    }
  }
}

function modPersUpdate(){
  if(Persona.verificarErrores()==0){ //guardamos datos de persona, empleado y usuario
    let datos = Persona.datosToDatabase();
    let foto = $('input[name="file_modPersFoto"]').get(0).files[0];
    let formData = new FormData();

    formData.append('imgFoto', foto);
    formData.append("appUpdate",JSON.stringify(datos));
    $.ajax({
      url:'includes/sql_update.php',
      type:'POST',
      processData:false,
      contentType: false,
      data:formData
    })
    .done(function(resp){
      var datos = { TipoQuery:'OnePersona', personaID:Persona.getPersonaID(), fullQuery:0 }
      appAjaxSelect(datos).done(function(resp){
        rrhhWorkerLlenarDatosPersonales(resp);
        Persona.close();
      });
    })
    .fail(function(resp){
      console.log("fail:.... "+resp.responseText);
    });
  } else {
    alert("!!!Faltan llenar Datos!!!");
  }
}
