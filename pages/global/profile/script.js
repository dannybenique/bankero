var rutaSQL = "pages/global/profile/sql.php";

//=========================funciones para profile============================
function appProfile(userID){
  let datos = {
    TipoQuery : 'selPerfil',
    userID : userID
  }
  appFetch(datos,rutaSQL).then(resp => {
    // console.log(resp);
    document.querySelector("#perfil_imagen").src = (resp.user.urlfoto);
    document.querySelector("#perfil_nombrecorto").innerHTML = (resp.user.nombrecorto);
    document.querySelector("#perfil_cargo").innerHTML = (resp.user.cargo);

    document.querySelector("#perfil_DNI").innerHTML = (resp.perfil.doc_dui);
    document.querySelector("#perfil_Celular").innerHTML = (resp.perfil.celular);
    document.querySelector("#perfil_Agencia").innerHTML = (resp.perfil.agencia);
    document.querySelector("#perfil_Correo").innerHTML = (resp.perfil.correo);
    document.querySelector("#perfil_Direccion").innerHTML = (resp.perfil.direccion);

    document.querySelector("#perfilDatos_Nombres").innerHTML = (resp.perfil.nombres);
    document.querySelector("#perfilDatos_Apellidos").innerHTML = (resp.perfil.apellidos);
    document.querySelector("#perfilDatos_FechaNac").innerHTML = (moment(resp.perfil.fecha_nac).format("DD/MM/YYYY"));
    document.querySelector("#perfilDatos_Sexo").innerHTML = (resp.perfil.sexo);
    document.querySelector("#perfilDatos_GInstruccion").innerHTML = (resp.perfil.instruccion);
    document.querySelector("#perfilDatos_ECivil").innerHTML = (resp.perfil.ecivil);
    document.querySelector("#perfilDatos_Ocupacion").innerHTML = (resp.perfil.ocupacion);
    document.querySelector("#perfilDatos_Observac").innerHTML = (resp.perfil.observac);
  });
}

function appProfileCambiarPassw(userID,pass,repass){
  let miPass = document.querySelector(pass).value;
  let miRepass = document.querySelector(repass).value;

  if (miPass==miRepass){
    let datos = {
      TipoQuery : 'updPassword',
      pass : SHA1(miPass).toString().toUpperCase(),
      userID : userID
    }
    appFetch(datos,rutaSQL).then(resp => {
      console.log("done:.... "+resp);
      if(!resp.error) { //sin errores
        document.querySelector(pass).value = "";
        document.querySelector(repass).value = "";
        alert("Cambio Hecho!!!");
      }
    });
  } else {
    alert("La clave no coincide");
  }
}
