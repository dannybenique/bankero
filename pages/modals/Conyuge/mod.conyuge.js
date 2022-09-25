//=========================funciones para crear el modal de laboral============================
(function (window,document){
  var iniConyuge = function(){
      var Conyuge = {
        rutaSQL : "pages/modals/Conyuge/mod.sql.php",
        rutaHTML : "pages/modals/Conyuge/mod.conyuge.htm",
        personaID : 0,
        conyugeID : 0,
        laboralID : 0,
        commandSQL : "",
        addModalToParentForm : function(contenedor) { $("#"+contenedor).load(Conyuge.rutaHTML); },
        close : function(){ $("#modalCony").modal("hide"); },
        lnkConyuge : function(){
          Persona.openBuscar('VerifyConyuge',1,0);

          $('#btn_modPersInsert').on('click',function(e) {
            if(Persona.sinErrores()){
              Persona.ejecutaSQL().done(function(rpta){
                let data = JSON.parse(rpta);
                let datos = {
                  TipoQuery : 'selLaboral',
                  conyugeID : data.tablaPers.ID
                }
                appAjaxSelect(datos,Conyuge.rutaSQL).done(function(resp){
                  let otro = {
                    id_conyuge : data.tablaPers.ID,
                    tiempoRelacion : 1,
                    persona : data.tablaPers,
                    laboral : resp
                  }
                  Conyuge.datosToForm(otro);
                  $('#btn_modConyLaboral').show();
                  Persona.close();
                });
              });
            } else {
              alert("!!!Faltan llenar Datos!!!");
            }
            e.stopImmediatePropagation();
            $('#btn_modPersInsert').off('click');
          });
          $('#btn_modPersUpdate').on('click',function(e) {
            console.log("ingreso por update aun no esta definido");
            e.stopImmediatePropagation();
            $('#btn_modPersUpdate').off('click');
          });
          $('#btn_modPersAddToForm').on('click',function(e) {
            let datos = {
              TipoQuery : "selLaboral",
              conyugeID : Persona.tablaPers.ID
            }

            appAjaxSelect(datos,Conyuge.rutaSQL).done(function(resp){
              let otro = {
                id_conyuge : Persona.tablaPers.ID,
                tiempoRelacion : 1,
                persona : Persona.tablaPers,
                laboral : resp
              }
              console.log(otro);
              Conyuge.datosToForm(otro);
              $('#btn_modConyLaboral').show();
              Persona.close();
            });
            e.stopImmediatePropagation();
            $('#btn_modPersAddToForm').off('click');
          });
        },
        lnkLaboral : function(){
          let datos = {
            TipoQuery : "selLaboral",
            conyugeID : Conyuge.conyugeID
          }

          appAjaxSelect(datos,Conyuge.rutaSQL).done(function(resp){
            if(resp.id_persona==0){ //sin datos laborales
              Laboral.nuevo(Conyuge.conyugeID);
              $('#btn_modLaboInsert').click(function(e) {
                if(Laboral.sinErrores()){ //guardamos datos de persona
                  Laboral.ejecutaSQL().done(function(resp){
                    let data = JSON.parse(resp);
                    $('#lbl_modConyEmprCondicion').html((data.tablaLabo.condicion==1)?("Dependiente"):("Independiente"));
                    $('#lbl_modConyEmprRazonSocial').html(data.tablaLabo.empresa);
                    $('#lbl_modConyEmprRUC').html(data.tablaLabo.ruc);
                    $('#lbl_modConyEmprRubro').html(data.tablaLabo.rubro);
                    $('#lbl_modConyEmprFono').html(data.tablaLabo.telefono);
                    Laboral.close();
                  });
                } else {
                  alert("!!!Faltan llenar Datos!!!");
                }
                e.stopImmediatePropagation();
              });
            } else{
              Laboral.editar(Conyuge.conyugeID);
              $('#btn_modLaboUpdate').click(function(e) {
                if(Laboral.sinErrores()){ //guardamos datos de persona
                  Laboral.ejecutaSQL().done(function(resp){
                    let data = JSON.parse(resp);
                    $('#lbl_modConyEmprCondicion').html((data.tablaLabo.condicion==1)?("Dependiente"):("Independiente"));
                    $('#lbl_modConyEmprRazonSocial').html(data.tablaLabo.empresa);
                    $('#lbl_modConyEmprRUC').html(data.tablaLabo.ruc);
                    $('#lbl_modConyEmprRubro').html(data.tablaLabo.rubro);
                    $('#lbl_modConyEmprFono').html(data.tablaLabo.telefono);
                    Laboral.close();
                  });
                } else {
                  alert("!!!Faltan llenar Datos!!!");
                }
                e.stopImmediatePropagation();
              });
            }
          });
        },
        nuevo : function(personaID){
          let datos = {
            id_conyuge : 0,
            tiempoRelacion : 1,
            laboral : { id_persona : 0 }
          }
          Conyuge.commandSQL = "INS";
          Conyuge.personaID = personaID;
          Conyuge.datosToForm(datos);
          $("#modConyTitulo").html("Nuevo Datos Conyugales");
          $("#modConyFormEdit").show();
          $("#btn_modConyInsert").show();
          $('#btn_modConyLaboral').hide();
          $("#btn_modConyUpdate").hide();
          $("#modalCony").modal();
        },
        editar : function(personaID){
          let datos = {
            TipoQuery : 'selConyuge',
            personaID : personaID
          }
          appAjaxSelect(datos,Conyuge.rutaSQL).done(function(resp){
            Conyuge.commandSQL = "UPD";
            Conyuge.personaID = personaID;
            Conyuge.datosToForm(resp);
            $("#modConyTitulo").html("Editar Datos Coyugales");
            $("#btn_modConyUpdate").show();
            $("#btn_modConyInsert").hide();
            $("#modalCony").modal();
          });
        },
        borrar : function(personaID){
          let datos = {
            TipoQuery : "delConyuge",
            personaID : personaID
          }
          let formData = new FormData();
          formData.append("appSQL",JSON.stringify(datos));
          let rpta = $.ajax({
            url  : Conyuge.rutaSQL,
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
          $('.box-body .form-Cony .form-group').removeClass('has-error');
          if($("#txt_modConyTiempoRel").val().trim()=="") { $("#div_modConyTiempoRel").prop("class","form-group has-error"); Error = false; }
          if(Conyuge.conyugeID==0) { Error = false; }
          return Error;
        },
        datosToDatabase : function(){
          let data = {
            TipoQuery : "updConyuge",
            commandSQL : Conyuge.commandSQL,
            permisoID : $('#hid_modConyPermisoID').val(),
            personaID : Conyuge.personaID,
            conyugeID : Conyuge.conyugeID,
            tiempoRelacion : $("#txt_modConyTiempoRel").val()
          };
          return data;
        },
        datosToForm : function(data){
          //datos personales
          Conyuge.conyugeID = data.id_conyuge;
          if(data.id_conyuge>0){
            $('#lbl_modConyNombres').html(data.persona.nombres);
            $('#lbl_modConyApellidos').html(data.persona.ap_paterno+" "+data.persona.ap_materno);
            $('#lbl_modConyNroDNI').html(data.persona.nroDNI);
            $('#lbl_modConyFechaNac').html(data.persona.fechanac);
            $('#lbl_modConyEcivil').html(data.persona.ecivil);
          } else {
            $('#lbl_modConyNombres').html("");
            $('#lbl_modConyApellidos').html("");
            $('#lbl_modConyNroDNI').html("");
            $('#lbl_modConyFechaNac').html("");
            $('#lbl_modConyEcivil').html("");
          }

          //datos laboral
          if(data.laboral.id_persona>0){
            $('#lbl_modConyEmprCondicion').html((data.laboral.condicion==1)?("Dependiente"):("Independiente"));
            $('#lbl_modConyEmprRazonSocial').html(data.laboral.empresa);
            $('#lbl_modConyEmprRUC').html(data.laboral.ruc);
            $('#lbl_modConyEmprRubro').html(data.laboral.rubro);
            $('#lbl_modConyEmprFono').html(data.laboral.telefono);
          } else {
            $('#lbl_modConyEmprCondicion').html("");
            $('#lbl_modConyEmprRazonSocial').html("");
            $('#lbl_modConyEmprRUC').html("");
            $('#lbl_modConyEmprRubro').html("");
            $('#lbl_modConyEmprFono').html("");
          }
          //datos relacion
          $("#txt_modConyTiempoRel").val(data.tiempoRelacion);
        },
        ejecutaSQL : function(){
          let exec = new FormData();
          let datos = Conyuge.datosToDatabase();
          exec.append("appSQL",JSON.stringify(datos));
          let rpta = $.ajax({
            url  : Conyuge.rutaSQL,
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
    return Conyuge;
  }
  if(typeof window.Conyuge === 'undefined'){ window.Conyuge = iniConyuge(); }
})(window,document);
