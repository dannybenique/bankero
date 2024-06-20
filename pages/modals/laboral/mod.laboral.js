//=========================funciones para crear el modal de laboral============================
(function (window,document){
  var iniLaboral = function(){
      var Laboral = {
        rutaSQL : "pages/modals/laboral/mod.sql.php",
        rutaHTML : "pages/modals/laboral/mod.laboral.htm",
        ID : 0,
        personaID : 0,
        commandSQL : "",
        tablaLabo : 0,
        addModalToParentForm : function(contenedor) { $("#"+contenedor).load(Laboral.rutaHTML); },
        close : function(){ $("#modalLabo").modal("hide"); },
        nuevo : async function(personaID){
          try{
            const resp = await appAsynFetch({TipoQuery:'newLaboral'},Laboral.rutaSQL);
            Laboral.commandSQL = "INS";
            Laboral.ID = 0;
            Laboral.personaID = personaID;
            $("#cbo_LaboCondicion").val(0);
            $("#txt_LaboEmprIngreso").val(appFormatMoney(0,2));
            $('#hid_modLaboPermisoID, #txt_LaboEmpresa, #txt_LaboEmprRUC, #txt_LaboEmprFono, #txt_LaboEmprRubro, #txt_LaboEmprDireccion, #txt_LaboEmprCargo, #txt_LaboObservac').val("");
            appLlenarDataEnComboBox(resp.comboRegiones,"#cbo_LaboEmprRegion",1014);
            appLlenarDataEnComboBox(resp.comboProvincias,"#cbo_LaboEmprProvincia",1401);
            appLlenarDataEnComboBox(resp.comboDistritos,"#cbo_LaboEmprDistrito",140101);
            $('#date_LaboInicio').datepicker("setDate",moment(resp.fecha).format("DD/MM/YYYY"));

            $("#modLaboTitulo").html("Datos Laborales");
            $("#modLaboFormEdit, #btn_modLaboInsert").show();
            $("#btn_modLaboUpdate").hide();
            $("#modalLabo").modal({keyboard:true}).on('shown.bs.modal', function() { $("#txt_LaboEmpresa").focus(); });
          } catch(err){
            console.error('Error al cargar datos:', err);
          }
        },
        editar : async function(laboralID){
          try{
            const resp = await appAsynFetch({ TipoQuery:'selLaboral', ID:laboralID },Laboral.rutaSQL);

            Laboral.datosToForm(resp);
            $("#modLaboTitulo").html("Editar Datos Laborales");
            $("#modLaboFormEdit, #btn_modLaboUpdate").show();
            $("#btn_modLaboInsert").hide();
            $("#modalLabo").modal({keyboard:true}).on('shown.bs.modal', function() { $("#txt_LaboEmpresa").focus(); });
          } catch(err){
            console.error('Error al cargar datos:', err);
          }
        },
        borrar : async function(personaID,laboralID){
          try{
            const resp = await appAsynFetch({
              TipoQuery : "ersLaboral",
              commandSQL: 'ERS',
              laboralID : laboralID,
              personaID : personaID
            }, Laboral.rutaSQL);
            return resp;
          } catch(err) {
            console.error('Error al cargar datos:', err);
          }
        },
        comboProvincia : async function(){
          try {
            const resp = await appAsynFetch({
              TipoQuery : "comboUbigeo",
              tipoID  : 3,
              padreID : $("#cbo_LaboEmprRegion").val()
            }, Laboral.rutaSQL);

            appLlenarDataEnComboBox(resp.provincias,"#cbo_LaboEmprProvincia",0); //provincia
            appLlenarDataEnComboBox(resp.distritos,"#cbo_LaboEmprDistrito",0); //distrito
          } catch(err) {
            console.error('Error al cargar datos:', err);
          }
        },
        comboDistrito : async function(){
          try {
            const resp = await appAsynFetch({
              TipoQuery : "comboUbigeo",
              tipoID  : 4,
              padreID : $("#cbo_LaboEmprProvincia").val()
            }, Laboral.rutaSQL);

            appLlenarDataEnComboBox(resp.distritos,"#cbo_LaboEmprDistrito",0); //distrito
          } catch(err) {
            console.error('Error al cargar datos:', err);
          }
        },
        sinErrores : function(){
          let Error = true;
          $('.form-group').removeClass('has-error');

          if($("#txt_LaboEmpresa").val().trim()=="") { $("#div_LaboEmpresa").addClass("has-error"); Error = false; }
          if($("#txt_LaboEmprRubro").val().trim()=="") { $("#div_LaboEmprRubro").addClass("has-error"); Error = false; }
          if($("#date_LaboInicio").val().trim()=="") { $("#div_LaboEmprInicio").addClass("has-error"); Error = false; }
          if($("#txt_LaboEmprIngreso").val().trim()=="") { $("#div_LaboEmprIngreso").addClass("has-error"); Error = false; }
          if($("#txt_LaboEmprDireccion").val().trim()=="") { $("#div_LaboEmprDireccion").addClass("has-error"); Error = false; }

          return Error;
        },
        datosToDatabase : function(){
          let data = {
            TipoQuery : "execLaboral",
            commandSQL : Laboral.commandSQL,
            ID : Laboral.ID,
            personaID : Laboral.personaID,
            condicion : $("#cbo_LaboCondicion").val(),
            empresa : $("#txt_LaboEmpresa").val().trim().toUpperCase(),
            ruc : $("#txt_LaboEmprRUC").val(),
            telefono : $("#txt_LaboEmprFono").val(),
            rubro : $("#txt_LaboEmprRubro").val().trim().toUpperCase(),
            distritoID : $("#cbo_LaboEmprDistrito").val(),
            direccion : $("#txt_LaboEmprDireccion").val().trim().toUpperCase(),
            cargo : $("#txt_LaboEmprCargo").val().trim().toUpperCase(),
            ingreso : appConvertToNumero($("#txt_LaboEmprIngreso").val()),
            fechaini : appConvertToFecha($("#date_LaboInicio").val(),""),
            estado: 1,
            observac : $("#txt_LaboObservac").val().trim().toUpperCase()
          };
          return data;
        },
        datosToForm : function(data){
          Laboral.commandSQL = "UPD";
          Laboral.ID = data.ID;
          Laboral.personaID = data.id_persona;
          $("#cbo_LaboCondicion").val(data.condicion);
          $("#txt_LaboEmpresa").val(data.empresa);
          $("#txt_LaboEmprRUC").val(data.ruc);
          $("#txt_LaboEmprFono").val(data.telefono);
          $("#txt_LaboEmprRubro").val(data.rubro);
          appLlenarDataEnComboBox(data.comboRegiones,"#cbo_LaboEmprRegion",data.id_region);
          appLlenarDataEnComboBox(data.comboProvincias,"#cbo_LaboEmprProvincia",data.id_provincia);
          appLlenarDataEnComboBox(data.comboDistritos,"#cbo_LaboEmprDistrito",data.id_distrito);
          $("#txt_LaboEmprDireccion").val(data.direccion);
          $('#date_LaboInicio').datepicker("setDate",moment(data.fechaIni).format("DD/MM/YYYY"));
          $("#txt_LaboEmprCargo").val(data.cargo);
          $("#txt_LaboEmprIngreso").val(appFormatMoney(data.ingreso,2));
          $("#txt_LaboObservac").val(data.observLabo);
        },
        ejecutaSQL : async function(){
          try{
            const resp = await appAsynFetch(Laboral.datosToDatabase(), Laboral.rutaSQL);
            return resp;
          } catch(err) {
            console.error('Error al cargar datos:', err);
          }
        },
      };
    return Laboral;
  }
  if(typeof window.Laboral === 'undefined'){ window.Laboral = iniLaboral(); }
})(window,document);
