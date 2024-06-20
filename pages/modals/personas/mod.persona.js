//=========================funciones para crear el modal de personas============================
(function (window,document){
  const Persona = {
    rutaSQL : "pages/modals/personas/mod.sql.php",
    rutaHTML : "pages/modals/personas/mod.persona.htm",
    personaID : 0,
    commandSQL : "",
    queryBuscar : "",
    queryURL : "",
    tipoPersona : 0, //1: natual; 2: juridica
    addNewPers : false, //permite añadir nuevos en personas
    addNewLista : false, //permite añadir nuevos en la lista
    addRepLista : false, //permite añadir repetidos en lista
    tablaPers : 0,
    addModalToParentForm(contenedor) { $("#"+contenedor).load(Persona.rutaHTML); },
    close() { $("#modalPers").modal("hide"); },
    keyBuscar(e) { if(e.keyCode === 13) { this.buscar(); } },
    calcularEdad() {
      let hoy = moment(); //fecha hoy
      let nac = moment(appConvertToFecha($("#date_modPersFechanac").val(),"-")); //fecha nac
      $("#lbl_modPersEdad").html(hoy.diff(nac,"years"));
    },
    openBuscar(query, url, addNewPers, addNewLista, addRepLista){
      Object.assign(this, { queryBuscar: query, queryURL: url, addNewPers, addNewLista, addRepLista });
      $("#modPersTitulo").html("Verificar Doc. Identidad");
      $("#modPersFormEdit, #modPersGridDatosTabla").hide();
      $("#modPersFormGrid").show();
      $("#lbl_modPersWait").html("");
      $("#txt_modPersBuscar").val("");
      $("#modalPers").modal({keyboard:true}).on('shown.bs.modal', function() { $("#txt_modPersBuscar").focus(); });
    },
    sinErrores(){
      let rpta = true;
      $('.form-group').removeClass('has-error');

      switch ($("#cbo_modPersTipoPers").val()) {
        case "1":
          if($("#txt_modPersNombres").val().trim()=="") { $("#div_modPersNombres").addClass("has-error"); rpta = false; }
          if($("#txt_modPersDocumento").val().trim()=="") { $("#div_modPersDocumento").addClass("has-error"); rpta = false; }
          if($("#txt_modPersApePaterno").val().trim()=="") { $("#div_modPersApePaterno").addClass("has-error"); rpta = false; }
          if($("#txt_modPersApeMaterno").val().trim()=="") { $("#div_modPersApeMaterno").addClass("has-error"); rpta = false; }
          break;
        case "2":
          if($("#txt_modPersNombres").val().trim()=="") { $("#div_modPersNombres").addClass("has-error"); rpta = false; }
          if($("#txt_modPersDocumento").val().trim()=="") { $("#div_modPersDocumento").addClass("has-error"); rpta = false; }
          break;
      }
      return rpta;
    },
    datosToDatabase(){
      let datosPers = {
        TipoQuery : ((this.personaID==0)?("insPersona"):("updPersona")),
        commandSQL : this.commandSQL,
        ID : this.personaID,
        persPermisoID : $('#hid_modPersPermisoID').val(),
        persTipoPersona : $("#cbo_modPersTipoPers").val(),
        persNombres : $("#txt_modPersNombres").val().trim().toUpperCase(),
        persApePaterno : $("#txt_modPersApePaterno").val().trim().toUpperCase(),
        persApeMaterno : $("#txt_modPersApeMaterno").val().trim().toUpperCase(),
        persNroDUI : $("#txt_modPersDocumento").val().trim(),
        persId_DUI : $("#cbo_modPersDocumento").val(),
        persId_sexo : $("#cbo_modPersSexo").val(),
        persId_Ginstruc : $("#cbo_modPersGinstruc").val(),
        persId_Ecivil : $("#cbo_modPersEcivil").val(),
        persId_Ubigeo : $("#cbo_modPersDistrito").val(),
        persId_TipoVivienda : $("#cbo_modPersTipoVivienda").val(),
        persId_Paisnac : $("#cbo_modPersPaisnac").val(),
        persFechaNac : appConvertToFecha($("#date_modPersFechanac").val().trim(),""),
        persLugarNac : $("#txt_modPersLugarnac").val().trim().toUpperCase(),
        persTelefijo : $("#txt_modPersFijo").val().trim(),
        persCelular : $("#txt_modPersCelular").val().trim(),
        persEmail : $("#txt_modPersEmail").val().trim().toLowerCase(),
        persProfesion : $("#txt_modPersProfesion").val().trim().toUpperCase(),
        persOcupacion : $("#txt_modPersOcupacion").val().trim().toUpperCase(),
        persDireccion : $("#txt_modPersDireccion").val().trim().toUpperCase(),
        persReferencia : $("#txt_modPersReferencia").val().trim().toUpperCase(),
        persMedidorluz : $("#txt_modPersMedidorLuz").val().trim(),
        persMedidorAgua : $("#txt_modPersMedidorAgua").val().trim(),
        persUrlFoto : $('#hid_modPersUrlFoto').val().trim(),
        persObservac : $("#txt_modPersObserv").val().trim().toUpperCase()
      };
      return datosPers;
    },
    datosToForm(data){
      this.commandSQL = "UPD";
      this.personaID = data.ID;
      $('#hid_modPersUrlFoto').val(data.urlfoto);
      $('#cbo_modPersTipoPers').val(data.tipoPersona);
      $("#txt_modPersNombres").val(data.nombres);
      $("#txt_modPersApePaterno").val(data.ap_paterno);
      $("#txt_modPersApeMaterno").val(data.ap_materno);
      $("#txt_modPersDocumento").val(data.nroDUI);
      $("#txt_modPersCelular").val(data.celular);
      $("#txt_modPersFijo").val(data.telefijo);
      $("#txt_modPersEmail").val(data.correo);
      $("#txt_modPersProfesion").val(data.profesion);
      $("#txt_modPersOcupacion").val(data.ocupacion);
      $("#txt_modPersLugarnac").val(data.lugarnac);
      $('#date_modPersFechanac').datepicker("setDate",moment(data.fechanac).format("DD/MM/YYYY"));
      appLlenarDataEnComboBox(data.comboPais,"#cbo_modPersPaisnac",data.id_paisnac);
      appLlenarDataEnComboBox(data.comboDUI,"#cbo_modPersDocumento",data.id_dui);
      appLlenarDataEnComboBox(data.comboSexo,"#cbo_modPersSexo",data.id_sexo);
      appLlenarDataEnComboBox(data.comboECivil,"#cbo_modPersEcivil",data.id_ecivil);
      appLlenarDataEnComboBox(data.comboGInstruc,"#cbo_modPersGinstruc",data.id_ginstruc);
      appLlenarDataEnComboBox(data.comboTipoViv,"#cbo_modPersTipoVivienda",data.id_tipovivienda);
      appLlenarDataEnComboBox(data.comboRegiones,"#cbo_modPersRegion",data.id_region);
      appLlenarDataEnComboBox(data.comboProvincias,"#cbo_modPersProvincia",data.id_provincia);
      appLlenarDataEnComboBox(data.comboDistritos,"#cbo_modPersDistrito",data.id_distrito);
      $("#txt_modPersDireccion").val(data.direccion);
      $("#txt_modPersReferencia").val(data.referencia);
      $("#txt_modPersMedidorLuz").val(data.medidorluz);
      $("#txt_modPersMedidorAgua").val(data.medidoragua);
      $("#txt_modPersObserv").val(data.observPers);
      $('#file_modPersFoto').val(null);
      Persona.calcularEdad();
      if(data.tipoPersona==2){ //persona juridica
        $("#div_modPersApePaterno, #div_modPersApeMaterno, #div_modPersGinstruc, #div_modPersSexoEcivil").hide();
        $("#cbo_modPersDocumento").prop('disabled',false);
        $("#txt_modPersNombres").attr("placeholder", "RAZON SOCIAL");
      } else {
        $("#div_modPersApePaterno, #div_modPersApeMaterno, #div_modPersGinstruc, #div_modPersSexoEcivil").show();
        $("#cbo_modPersDocumento").prop('disabled', false);
        $("#txt_modPersNombres").attr("placeholder", "NOMBRES");
      }
    },
    handleBuscarResponse(resp, nroDUI) {
      $('#lbl_modPersWait').html("");
      $('#lbl_modPersDUI').html(nroDUI);
      $("#modPersGridDatosTabla").show();

      if(resp.persona){ //existe en Personas
        this.personaID = resp.tablaPers.ID;
        this.tablaPers = resp.tablaPers;
        $('#btn_modPersAddToPersonas').hide();

        switch(resp.activo){
          case true: // YA existe en la lista
            $('#btn_modPersAddToForm').toggle(this.addRepLista);
            $('#lbl_modPersPersona').html((this.addRepLista)
              ?(resp.tablaPers.persona+" &raquo; "+resp.tablaPers.direccion)
              :("La "+((resp.tablaPers.tipoPersona==1)?('persona '):('entidad '))+"<b>"+(resp.tablaPers.persona)+"</b> identificada con <b>"+(resp.tablaPers.tipoDUI)+'-'+(resp.tablaPers.nroDUI)+"</b> "+(resp.mensajeNOadd)));
            break;
          case false: // NO existe en la lista
            $('#btn_modPersAddToForm').toggle(Persona.addNewLista);
            $('#lbl_modPersPersona').html(resp.tablaPers.persona+" &raquo; "+resp.tablaPers.direccion+' '+((Persona.addNewLista)?(''):('No puede ser agregado a esta lista')));
            break;
          case 2: //no debe ser añadido
            $('#btn_modPersAddToForm').hide();
            $('#lbl_modPersPersona').html(resp.tablaPers.persona+' &raquo; '+resp.tablaPers.direccion+' No puede ser agregado a esta lista');
            break;
        }
      }else{ //NO existe en Personas
        $('#btn_modPersAddToForm').hide();
        $('#btn_modPersAddToPersonas').toggle(Persona.addNewPers); //permite añadir nuevas personas segun config
        $('#lbl_modPersPersona').html('No existe la persona identificada con nro <b>'+nroDUI+'</b> y deseo Agregarla');
      }
    },
    handler_modPersChange(e){ //handler del combo TipoPersona
      const isJuridica = $(this).val() == 2; // persona jurídica
      const displayStyle = isJuridica ? 'none' : 'block';
    
      $("#div_modPersApePaterno, #div_modPersApeMaterno, #div_modPersGinstruc, #div_modPersSexoEcivil").css('display', displayStyle);
      $("#cbo_modPersDocumento").prop('disabled', isJuridica);
      $("#txt_modPersNombres").attr('placeholder', isJuridica ? 'RAZON SOCIAL' : 'NOMBRES');
      
      if (isJuridica) {
        $("#txt_modPersApePaterno, #txt_modPersApeMaterno").val('');
        $("#cbo_modPersGinstruc, #cbo_modPersEcivil, #cbo_modPersSexo").prop('selectedIndex', 0);
        $("#cbo_modPersDocumento").val(502);
      } else {
        $("#cbo_modPersDocumento").val(501);
      }
    },
    async buscar(){
      const nroDUI = $("#txt_modPersBuscar").val().trim();
      $('#lbl_modPersWait').html('<div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></div>');
      
      if(nroDUI.length >= 8){
        try{
          const resp = await appAsynFetch({ TipoQuery:this.queryBuscar, nroDNI:nroDUI }, this.queryURL);
          this.handleBuscarResponse(resp, nroDUI);
        } catch (err){
          console.error('Error al cargar datos:', err);
        }
      } else{ 
        alert("!!!El Nro de DNI debe ser de 08 digitos y el RUC de 11 digitos!!!");
      }
    },
    async nuevo(){
      $('#lbl_modPersWait').html('<div class="progress progress-sm active"><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div>');
      const nroDNI = $("#txt_modPersBuscar").val().trim();
      try{
        const resp = await appAsynFetch( {TipoQuery:'newPersona'}, this.rutaSQL);
        $('#lbl_modPersWait').html("");

        //pestaña de datos personales
        this.commandSQL = "INS";
        this.personaID = 0;
        this.tablaPers = 0;

        $('#cbo_modPersTipoPers').val(1);
        $('#hid_modPersPermisoID').val(0);
        $("#txt_modPersDocumento").val(nroDNI);
        $("#lbl_modPersEdad").html("0");
        $('#hid_modPersUrlFoto, #txt_modPersNombres, #txt_modPersApePaterno, #txt_modPersApeMaterno, #txt_modPersCelular, #txt_modPersFijo, #txt_modPersEmail, #txt_modPersProfesion, #txt_modPersOcupacion, #txt_modPersLugarnac, #txt_modPersDireccion, #txt_modPersReferencia, #txt_modPersMedidorLuz, #txt_modPersMedidorAgua, #txt_modPersObserv').val("");
        appLlenarDataEnComboBox(resp.comboPais,"#cbo_modPersPaisnac",101);
        appLlenarDataEnComboBox(resp.comboDUI,"#cbo_modPersDocumento",0);
        appLlenarDataEnComboBox(resp.comboSexo,"#cbo_modPersSexo",0);
        appLlenarDataEnComboBox(resp.comboECivil,"#cbo_modPersEcivil",0);
        appLlenarDataEnComboBox(resp.comboGInstruc,"#cbo_modPersGinstruc",0);
        appLlenarDataEnComboBox(resp.comboTipoViv,"#cbo_modPersTipoVivienda",0);
        appLlenarDataEnComboBox(resp.comboRegiones,"#cbo_modPersRegion",1014); //region arequipa
        appLlenarDataEnComboBox(resp.comboProvincias,"#cbo_modPersProvincia",1401); //provincia arequipa
        appLlenarDataEnComboBox(resp.comboDistritos,"#cbo_modPersDistrito",140101); //distrito arequipa
        $('#date_modPersFechanac').datepicker("setDate",moment(resp.fecha).format("DD/MM/YYYY"));

        //config inicial
        $("#txt_modPersNombres").attr("placeholder","NOMBRES");
        $("#cbo_modPersDocumento").prop("disabled", false);
        $("#div_modPersApePaterno, #div_modPersApeMaterno, #div_modPersGinstruc, #div_modPersSexoEcivil").show();

        $("#modPersFormGrid, #btn_modPersUpdate").hide();
        $("#modPersFormEdit, #btn_modPersInsert").show();
        //$("#modalPers").modal();
      } catch(err){
        console.error('Error al cargar datos:', err);
      }
    },
    async editar(personaID,tipoPers){
      Persona.tipoPersona = tipoPers;
      try{
        const resp = await appAsynFetch({ TipoQuery:'selPersona', personaID },Persona.rutaSQL);
        //respuesta
        this.datosToForm(resp);
        $("#modPersTitulo").html("Datos Personales &raquo; "+personaID);
        $("#modPersFormGrid, #btn_modPersInsert").hide();
        $("#modPersFormEdit, #btn_modPersUpdate").show();
        $("#modalPers").modal({keyboard:true}).on('shown.bs.modal', function() { $('#txt_modPersNombres').trigger('focus') });
      } catch(err){
        console.error('Error al cargar datos:', err);
      }
    },
    async comboProvincia(){
      try{
        const resp = await appAsynFetch({
          TipoQuery : "comboUbigeo",
          tipoID  : 3, //provincias
          padreID : $("#cbo_modPersRegion").val()
        },Persona.rutaSQL);
        //respuesta
        appLlenarDataEnComboBox(resp.provincias,"#cbo_modPersProvincia",0); //provincia
        appLlenarDataEnComboBox(resp.distritos,"#cbo_modPersDistrito",0); //distrito
      } catch(err){
        console.error('Error al cargar datos:', err);
      }
    },
    async comboDistrito(){
      try{
        const resp = await appAsynFetch({
          TipoQuery : "comboUbigeo",
          tipoID  : 4, //distritos
          padreID : $("#cbo_modPersProvincia").val()
        },Persona.rutaSQL);
        //respuesta
        appLlenarDataEnComboBox(resp.distritos,"#cbo_modPersDistrito",0); //distrito
      } catch(err){
        console.error('Error al cargar datos:', err);
      }
    },
    async apidni(){
      try{
        const nroDoc = $("#txt_modPersDocumento").val();
        const resp = await fetch("https://dniruc.apisperu.com/api/v1/"+(((nroDoc.length==8)?("dni/"):((nroDoc.length==11)?("ruc/"):("")))+(nroDoc))+"?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6ImRhbm55YmVuaXF1ZUBtc24uY29tIn0.ts3qFRsLtLxqnoOMvwYEeOu470tyTUGWQbsuH4ZTC7I");
        const rpta = await resp.json();

        if(rpta.success){
          $("#txt_modPersNombres").val(rpta.nombres);
          $("#txt_modPersApePaterno").val(rpta.apellidoPaterno);
          $("#txt_modPersApeMaterno").val(rpta.apellidoMaterno);  
        } else {
          alert("NO hay datos desde la API");
        }
      } catch(err) {
        console.log(err);
        alert("Hubo un error con la API");
      }
    },
    async ejecutaSQL(){
      let foto = $('#file_modPersFoto')[0].files[0];
      let exec = new FormData();
      exec.append('imgFoto', foto);
      exec.append("appSQL",JSON.stringify(Persona.datosToDatabase()));
      
      try{
        const resp = await fetch(Persona.rutaSQL, { method:'POST', body:exec });
        const rpta = await resp.json();
        return rpta;
      } catch(err){
        console.log(err);
      }
    }
  };
  if(typeof window.Persona === 'undefined'){ window.Persona = Persona; }
})(window,document);
