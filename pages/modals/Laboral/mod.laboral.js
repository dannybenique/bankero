//=========================funciones para crear el modal de laboral============================
(function (window,document){
  var iniLaboral = function(){
      var Laboral = {
        rutaSQL : "pages/modals/Laboral/mod.sql.php",
        rutaHTML : "pages/modals/Laboral/mod.laboral.htm",
        personaID : "",
        commandSQL : "",
        tablaLabo : 0,
        addModalToParentForm : function(contenedor) { $("#"+contenedor).load(Laboral.rutaHTML); },
        close : function(){ $("#modalLabo").modal("hide"); },
        nuevo : function(personaID){
          appAjaxSelect({TipoQuery:'fechaHoy'}).done(function(resp){
            Laboral.commandSQL = "INS";
            Laboral.personaID = personaID;
            $('#hid_modLaboPermisoID').val("");
            $("#cbo_LaboCondicion").val(0);
            $("#txt_LaboEmpresa").val("");
            $("#txt_LaboEmprRUC").val("");
            $("#txt_LaboEmprFono").val("");
            $("#txt_LaboEmprRubro").val("");
            appComboUbiGeo("#cbo_LaboEmprRegion",0,14);
            appComboUbiGeo("#cbo_LaboEmprProvincia",14,1401);
            appComboUbiGeo("#cbo_LaboEmprDistrito",1401,140101);
            $("#txt_LaboEmprDireccion").val("");
            $('#date_LaboInicio').datepicker("setDate",resp.fecha);
            $("#txt_LaboEmprCargo").val("");
            $("#txt_LaboEmprIngreso").val(appFormatMoney(0,2));
            $("#txt_LaboObservac").val("");

            $("#modLaboTitulo").html("Datos Laborales");
            $("#modLaboFormEdit").show();
            $("#btn_modLaboInsert").show();
            $("#btn_modLaboUpdate").hide();
            $("#modalLabo").modal({keyboard:true});
            $('#modalLabo').on('shown.bs.modal', function() { $('#txt_LaboEmpresa').trigger('focus') });
          });
        },
        editar : function(personaID){
          let datos = {
            TipoQuery : 'selLaboral',
            personaID : personaID
          }
          appAjaxSelect(datos,Laboral.rutaSQL).done(function(resp){
            Laboral.datosToForm(resp);
            $("#modLaboTitulo").html("Editar Datos Laborales");
            $("#modLaboFormEdit").show();
            $("#btn_modLaboUpdate").show();
            $("#btn_modLaboInsert").hide();
            $("#modalLabo").modal({keyboard:true});
            $('#modalLabo').on('shown.bs.modal', function() { $('#txt_LaboEmpresa').trigger('focus') });
          });
        },
        borrar : function(personaID){
          let datos = {
            TipoQuery : "delLaboral",
            personaID : personaID
          }
          let formData = new FormData();
          formData.append("appSQL",JSON.stringify(datos));
          let rpta = $.ajax({
            url  : Laboral.rutaSQL,
            type : 'POST',
            processData : false,
            contentType : false,
            data : formData
          })
          .fail(function(resp){
            console.log("fail:.... "+resp.responseText);
          });
          return rpta;
        },
        sinErrores : function(){
          let Error = true;
          $('.box-body .form-labo .form-group').removeClass('has-error');

          if($("#txt_LaboEmpresa").val().trim()=="") { $("#div_LaboEmpresa").prop("class","form-group has-error"); Error = false; }
          if($("#txt_LaboEmprRubro").val().trim()=="") { $("#div_LaboEmprRubro").prop("class","form-group has-error"); Error = false; }
          if($("#date_LaboInicio").val().trim()=="") { $("#div_LaboEmprInicio").prop("class","form-group has-error"); Error = false; }
          if($("#txt_LaboEmprIngreso").val().trim()=="") { $("#div_LaboEmprIngreso").prop("class","form-group has-error"); Error = false; }
          if($("#txt_LaboEmprDireccion").val().trim()=="") { $("#div_LaboEmprDireccion").prop("class","form-group has-error"); Error = false; }

          return Error;
        },
        datosToDatabase : function(){
          let data = {
            TipoQuery : "updLaboral",
            commandSQL : Laboral.commandSQL,
            permisoID : $('#hid_LaboPermisoID').val(),
            personaID : Laboral.personaID,
            condicion : $("#cbo_LaboCondicion").val(),
            ruc : $("#txt_LaboEmprRUC").val(),
            empresa : $("#txt_LaboEmpresa").val().trim().toUpperCase(),
            telefono : $("#txt_LaboEmprFono").val(),
            rubro : $("#txt_LaboEmprRubro").val().trim().toUpperCase(),
            distritoID : $("#cbo_LaboEmprDistrito").val(),
            direccion : $("#txt_LaboEmprDireccion").val().trim().toUpperCase(),
            cargo : $("#txt_LaboEmprCargo").val().trim().toUpperCase(),
            ingreso : appConvertToNumero($("#txt_LaboEmprIngreso").val()),
            inicio : appConvertToFecha($("#date_LaboInicio").val(),""),
            observac : $("#txt_LaboObservac").val().trim().toUpperCase()
          };
          return data;
        },
        datosToForm : function(data){
          Laboral.commandSQL = "UPD";
          Laboral.personaID = data.id_persona;
          $('#hid_modLaboPermisoID').val(data.permisoLaboral.ID);
          $("#cbo_LaboCondicion").val(data.condicion);
          $("#txt_LaboEmpresa").val(data.empresa);
          $("#txt_LaboEmprRUC").val(data.ruc);
          $("#txt_LaboEmprFono").val(data.telefono);
          $("#txt_LaboEmprRubro").val(data.rubro);
          appComboUbiGeo("#cbo_LaboEmprRegion",0,data.id_region);
          appComboUbiGeo("#cbo_LaboEmprProvincia",data.id_region,data.id_provincia);
          appComboUbiGeo("#cbo_LaboEmprDistrito",data.id_provincia,data.id_distrito);
          $("#txt_LaboEmprDireccion").val(data.direccion);
          $('#date_LaboInicio').datepicker("setDate",data.fechaIni);
          $("#txt_LaboEmprCargo").val(data.cargo);
          $("#txt_LaboEmprIngreso").val(appFormatMoney(data.ingreso,2));
          $("#txt_LaboObservac").val(data.observLabo);
        },
        ejecutaSQL : function(){
          let exec = new FormData();
          let datos = Laboral.datosToDatabase();
          exec.append("appSQL",JSON.stringify(datos));
          let rpta = $.ajax({
            url  : Laboral.rutaSQL,
            type : 'POST',
            processData : false,
            contentType : false,
            data : exec
          })
          .fail(function(resp){
            console.log("fail:.... "+resp.responseText);
          });
          return rpta;
        },
      };
    return Laboral;
  }
  if(typeof window.Laboral === 'undefined'){ window.Laboral = iniLaboral(); }
})(window,document);
