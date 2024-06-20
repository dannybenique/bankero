var rutaSQL = "pages/global/profile/sql.php";

//=========================funciones para profile============================
async function appProfile(userID){
  try{
    const resp = await appAsynFetch({ TipoQuery:'viewPerfil', userID:userID }, rutaSQL);

    let data = resp.tablaPers;
    let user = resp.user;
    $("#div_PersAuditoria").toggle(user.rolID === user.rolROOT);
    
    //info corta
    $("#perfil_imagen").attr("src", user.urlfoto);
    $("#perfil_nombrecorto").html(user.nombrecorto);
    $("#perfil_cargo").html(user.cargo);
    $("#perfil_DNI").html(data.nroDUI);
    $("#perfil_Celular").html(data.celular);
    $("#perfil_Agencia").html(resp.agencia);
    $("#perfil_Correo").html(data.correo);
    $("#perfil_Direccion").html(data.direccion);

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
  } catch(err){
    console.error('Error al cargar datos:'+err);
  }
}

async function appProfileCambiarPassw(userID,pass,repass){
  let miPass = $(pass).val();
  let miRepass = $(repass).val();

  if (miPass==miRepass){
    try{
      const resp = await appAsynFetch({
        TipoQuery : 'updPassword',
        pass : SHA1(miPass).toString().toUpperCase(),
        userID : userID
      }, rutaSQL);

      //respuesta
      if(!resp.error) { //sin errores
        $(pass).val("");
        $(repass).val("");
        alert("Cambio Hecho!!!");
      }
    } catch(err){
      console.error('Error al cargar datos:'+err);
    }
  } else {
    alert("La clave no coincide");
  }
}
