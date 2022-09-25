//=========================funciones para crear el modal de personas============================
(function (window,document){
  var inicio = function(){
      var Persona = {
        rutaSQL : "pages/modals/Personas/mod.sql.php",
        rutaHTML : "pages/modals/Personas/mod.persona.php",
        personaID : 0,
        commandSQL : "",
        queryBuscar : "",
        tipoPersona : "",
        permiteAdd : "",
        foreignKey : "",
        tablaPers : 0,
        addModalToParentForm : function(contenedor) { $("#"+contenedor).load(Persona.rutaHTML); },
        close : function(){ $("#modalPers").modal("hide"); },
        keyBuscar: function(e){
          let code = (e.keyCode ? e.keyCode : e.which);
          if(code == 13) { Persona.buscar(); }
        },
        openBuscar : function(query,permiteAdd,foreignKey){
          Persona.queryBuscar = query;
          Persona.permiteAdd = permiteAdd;
          Persona.foreignKey = foreignKey;
          $("#modPersTitulo").html("Verificar Doc. Identidad");
          $("#modPersFormEdit").hide();
          $("#modPersGridDatosTabla").hide();
          $("#modPersFormGrid").show();
          $("#lbl_modPersWait").html("");
          $("#txt_modPersBuscar").val("");
          $("#modalPers").modal({keyboard:true});
          $('#modalPers').on('shown.bs.modal', function() { $('#txt_modPersBuscar').trigger('focus') });
        },
        buscar : function(){
          let nroDNI = $("#txt_modPersBuscar").val().trim();
          if(nroDNI.length>=8){
            $('#lbl_modPersWait').html('<div class="progress progress-sm active" style=""><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div>');
            let datos = {
              TipoQuery : Persona.queryBuscar,
              foreignKey : Persona.foreignKey,
              nroDNI : nroDNI
            }
            appAjaxSelect(datos,Persona.rutaSQL).done(function(resp){
              $('#lbl_modPersDNI').html(nroDNI);
              $('#lbl_modPersWait').html("");
              $('#btn_modPersNuevo').hide();
              $("#modPersGridDatosTabla").show();

              if(resp.persona!=0){ //existe en la BD
                Persona.tablaPers = resp.persona;
                let entidad = (Persona.tablaPers.tipoPersona==2) ? (Persona.tablaPers.nombres) : (Persona.tablaPers.persona);// persona natural o juridica
                switch(resp.activo){
                  case 0: //la persona esta apta para ser ingresada
                    $('#btn_modPersAddToForm').show();
                    $('#lbl_modPersPersona').html(entidad+" &raquo; "+Persona.tablaPers.direccion);
                    break;
                  case 1: //la persona ya existe en la lista
                    $('#btn_modPersAddToForm').hide();
                    $('#lbl_modPersPersona').html("La "+((Persona.tablaPers.tipoPersona==1)?('persona '):('razon social '))+entidad+" identificada con "+(Persona.tablaPers.tipoDNI)+'-'+(nroDNI)+" ya fue ingresada a esta lista...");
                    break;
                  case 2://***solo ahorros*** la persona no tiene aportes
                    $('#btn_modPersAddToForm').hide();
                    $('#lbl_modPersPersona').html('<span style="color:red;">El SOCIO '+entidad+' con '+(Persona.tablaPers.tipoDNI)+'-'+(nroDNI)+' AUN NO TIENE APORTES...</span>');
                    break;
                }
              }else{
                $('#btn_modPersAddToForm').hide();
                if(Persona.permiteAdd==1){ //permite añadir nuevas personas
                  $('#btn_modPersNuevo').show();
                  $('#lbl_modPersPersona').html('No existe la persona identificada con <b>'+nroDNI+'</b>, deseo Agregarla');
                } else{
                  $("#lbl_modPersPersona").html('No existe SOCIO identificado con <b>'+nroDNI+'</b>');
                }
              }
            });
          } else{ alert("!!!El Nro de DNI debe ser de 8 digitos y el RUC de 11 digitos!!!");}
        },
        nuevo : function(){
          $('#lbl_modPersWait').html('<div class="progress progress-sm active" style=""><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div>');

          let nroDNI = $("#txt_modPersBuscar").val().trim();

          appAjaxSelect({TipoQuery:'fechaHoy'}).done(function(resp){
            $('#lbl_modPersWait').html("");

            //pestaña de datos personales
            Persona.commandSQL = "INS";
            Persona.personaID = 0;
            Persona.tablaPers = 0;
            $('#cbo_modPersTipoPers').val(1);
            $('#hid_modPersPermisoID').val(0);
            $('#hid_modPersUrlFoto').val("");
            $("#txt_modPersNombres").val("");
            $("#txt_modPersApePaterno").val("");
            $("#txt_modPersApeMaterno").val("");
            appComboBox("#cbo_modPersDocumento","DNI",0);
            $("#txt_modPersDocumento").val(nroDNI);
            $("#txt_modPersCelular").val("");
            $("#txt_modPersFijo").val("");
            $("#txt_modPersEmail").val("");
            $("#txt_modPersProfesion").val("");
            $("#txt_modPersOcupacion").val("");
            $('#date_modPersFechanac').datepicker("setDate",resp.fecha);
            $("#txt_modPersLugarnac").val("");
            appComboBox("#cbo_modPersGinstruc","GradoInstruccion",0);
            appComboBox("#cbo_modPersEcivil","EstadoCivil",0);
            appComboBox("#cbo_modPersSexo","Sexo",0);
            appComboUbiGeo("#cbo_modPersRegion",0,14); //region arequipa
            appComboUbiGeo("#cbo_modPersProvincia",14,1401);//provincia arequipa
            appComboUbiGeo("#cbo_modPersDistrito",1401,140101);//distrito arequipa
            $("#txt_modPersDireccion").val("");
            $("#txt_modPersReferencia").val("");
            $("#txt_modPersMedidor").val("");
            appComboBox("#cbo_modPersTipoVivienda","TipoVivienda",0);
            $("#txt_modPersObserv").val("");

            //config inicial
            $("#txt_modPersNombres").prop('placeholder','NOMBRES');
            $("#div_modPersApePaterno").show();
            $("#div_modPersApeMaterno").show();
            $("#div_modPersGinstruc").show();
            $("#div_modPersEcivil").show();
            $("#div_modPersSexo").show();
            $("#cbo_modPersDocumento").removeAttr('disabled');

            $("#modPersFormGrid").hide();
            $("#modPersFormEdit").show();
            $("#btn_modPersUpdate").hide();
            $("#btn_modPersInsert").show();
            //$("#modalPers").modal();
          });
        },
        editar : function(personaID,tipoPers){
          Persona.tipoPersona = tipoPers;
          let datos = {
            TipoQuery : 'selPersona',
            personaID : personaID
          }
          appAjaxSelect(datos,Persona.rutaSQL).done(function(resp){
            Persona.datosToForm(resp);
            $("#modPersTitulo").html("Datos Personales");
            $("#modPersFormGrid").hide();
            $("#modPersFormEdit").show();
            $("#btn_modPersInsert").hide();
            $("#btn_modPersUpdate").show();
            $("#modalPers").modal({keyboard:true});
            $('#modalPers').on('shown.bs.modal', function() { $('#txt_modPersNombres').trigger('focus') });
          });
        },
        comboProvincia : function(){
          let datos = {
            TipoQuery : "comboUbigeo",
            padreID : $("#cbo_modPersRegion").val()
          }
          appAjaxSelect(datos,Persona.rutaSQL).done(function(resp){
            appLlenarDataEnComboBox(resp.provincias,"#cbo_modPersProvincia",0); //provincia
            appLlenarDataEnComboBox(resp.distritos,"#cbo_modPersDistrito",0); //distrito
          });
        },
        comboDistrito : function(){
          let datos = {
            TipoQuery : "comboUbigeo",
            padreID : $("#cbo_modPersProvincia").val()
          }
          appAjaxSelect(datos,Persona.rutaSQL).done(function(resp){
            appLlenarDataEnComboBox(resp.distritos,"#cbo_modPersDistrito",0); //distrito
          });
        },
        apidni : function(){
          let url = (($("#txt_modPersDocumento").val().length==8)?("dni/"):(($("#txt_modPersDocumento").val().length==11)?("ruc/"):("")))+($("#txt_modPersDocumento").val());
          fetch("https://dniruc.apisperu.com/api/v1/"+url+"?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6ImRhbm55YmVuaXF1ZUBtc24uY29tIn0.ts3qFRsLtLxqnoOMvwYEeOu470tyTUGWQbsuH4ZTC7I")
          .then(response=>response.json())
          .then((resp)=>{
            console.log(resp);
            if(resp.nombres==null){
              alert("NO hay datos desde la API");
            } else {
              $("#txt_modPersNombres").val(resp.nombres);
              $("#txt_modPersApePaterno").val(resp.apellidoPaterno);
              $("#txt_modPersApeMaterno").val(resp.apellidoMaterno);
            }
          })
          .catch((error)=>{
            console.log(error);
            alert("Hubo un error con la API");
          });

          /*
          let rpta = $.ajax({
            url : Persona.rutaSQL,
            type : 'POST',
            dataType : 'json',
            data : {"appSQL":JSON.stringify({
              TipoQuery : 'apiPeru',
              nroDNI : $("#txt_modPersBuscar").val().trim()
            })}
          })
          .fail(function(resp){
            let error = JSON.stringify(resp);
            let rpta = JSON.parse(error);
            console.log(resp);
            console.log(error);
            console.log(rpta);
            alert("Hubo un error con la API");
          })
          .done(function(rpta){
            console.log(JSON.parse(rpta.api));
            let resp = JSON.parse(rpta.api);
            $("#txt_modPersNombres").val(resp.nombres);
            $("#txt_modPersApePaterno").val(resp.apellidoPaterno);
            $("#txt_modPersApeMaterno").val(resp.apellidoMaterno);
          })*/
        },
        sinErrores : function(){
          let rpta = true;
          $('.box-body .form-group').removeClass('has-error');

          switch ($("#cbo_modPersTipoPers").val()) {
            case "1":
              if($("#txt_modPersNombres").val().trim()=="") { $("#div_modPersNombres").prop("class","form-group has-error"); rpta = false; }
              if($("#txt_modPersDocumento").val().trim()=="") { $("#div_modPersDocumento").prop("class","form-group has-error"); rpta = false; }
              if($("#txt_modPersApePaterno").val().trim()=="") { $("#div_modPersApePaterno").prop("class","form-group has-error"); rpta = false; }
              if($("#txt_modPersApeMaterno").val().trim()=="") { $("#div_modPersApeMaterno").prop("class","form-group has-error"); rpta = false; }
              break;
            case "2":
              if($("#txt_modPersNombres").val().trim()=="") { $("#div_modPersNombres").prop("class","form-group has-error"); rpta = false; }
              if($("#txt_modPersDocumento").val().trim()=="") { $("#div_modPersDocumento").prop("class","form-group has-error"); rpta = false; }
              break;
          }
          return rpta;
        },
        datosToDatabase : function(){
          let datosPers = {
            TipoQuery : ((Persona.personaID==0)?("insPersona"):("updPersona")),
            commandSQL : Persona.commandSQL,
            ID : Persona.personaID,
            persPermisoID : $('#hid_modPersPermisoID').val(),
            persTipoPersona : $("#cbo_modPersTipoPers").val(),
            persNombres : $("#txt_modPersNombres").val().trim().toUpperCase(),
            persApePaterno : $("#txt_modPersApePaterno").val().trim().toUpperCase(),
            persApeMaterno : $("#txt_modPersApeMaterno").val().trim().toUpperCase(),
            persDNI : $("#txt_modPersDocumento").val().trim(),
            persId_Doc : $("#cbo_modPersDocumento").val(),
            persId_sexo : $("#cbo_modPersSexo").val(),
            persId_Ginstruc : $("#cbo_modPersGinstruc").val(),
            persId_Ecivil : $("#cbo_modPersEcivil").val(),
            persId_Ubigeo : $("#cbo_modPersDistrito").val(),
            persId_TipoVivienda : $("#cbo_modPersTipoVivienda").val(),
            persFechaNac : appConvertToFecha($("#date_modPersFechanac").val().trim(),""),
            persLugarnac : $("#txt_modPersLugarnac").val().trim().toUpperCase(),
            persTelefijo : $("#txt_modPersFijo").val().trim(),
            persCelular : $("#txt_modPersCelular").val().trim(),
            persEmail : $("#txt_modPersEmail").val().trim(),
            persProfesion : $("#txt_modPersProfesion").val().trim().toUpperCase(),
            persOcupacion : $("#txt_modPersOcupacion").val().trim().toUpperCase(),
            persDireccion : $("#txt_modPersDireccion").val().trim().toUpperCase(),
            persReferencia : $("#txt_modPersReferencia").val().trim().toUpperCase(),
            persMedidorluz : $("#txt_modPersMedidor").val().trim(),
            persUrlFoto : $('#hid_modPersUrlFoto').val().trim(),
            persObservac : $("#txt_modPersObserv").val().trim().toUpperCase()
          };
          return datosPers;
        },
        datosToForm : function(data){
          Persona.commandSQL = "UPD";
          Persona.personaID = data.ID;
          $('#hid_modPersPermisoID').val(data.permisoPersona.ID);
          $('#hid_modPersUrlFoto').val(data.urlfoto);
          $('#cbo_modPersTipoPers').val(data.tipoPersona);
          $("#txt_modPersNombres").val(data.nombres);
          $("#txt_modPersApePaterno").val(data.ap_paterno);
          $("#txt_modPersApeMaterno").val(data.ap_materno);
          appComboBox("#cbo_modPersDocumento","DNI",data.id_doc);
          $("#txt_modPersDocumento").val(data.nroDNI);
          $("#txt_modPersCelular").val(data.celular);
          $("#txt_modPersFijo").val(data.fijo);
          $("#txt_modPersEmail").val(data.correo);
          $("#txt_modPersProfesion").val(data.profesion);
          $("#txt_modPersOcupacion").val(data.ocupacion);
          $('#date_modPersFechanac').datepicker("setDate",data.fechanac);
          $("#txt_modPersLugarnac").val(data.lugarnac);
          appComboBox("#cbo_modPersGinstruc","GradoInstruccion",data.id_ginstruc);
          appComboBox("#cbo_modPersEcivil","EstadoCivil",data.id_ecivil);
          appComboBox("#cbo_modPersSexo","Sexo",data.id_sexo);
          appComboUbiGeo("#cbo_modPersRegion",0,data.id_region);
          appComboUbiGeo("#cbo_modPersProvincia",data.id_region,data.id_provincia);
          appComboUbiGeo("#cbo_modPersDistrito",data.id_provincia,data.id_distrito);
          $("#txt_modPersDireccion").val(data.direccion);
          $("#txt_modPersReferencia").val(data.referencia);
          $("#txt_modPersMedidor").val(data.medidorluz);
          appComboBox("#cbo_modPersTipoVivienda","TipoVivienda",data.id_tipovivienda);
          $("#txt_modPersObserv").val(data.observPers);
          $('#file_modPersFoto').val(null);

          if(data.tipoPersona==2){ //persona juridica
            $("#div_modPersApePaterno").hide();
            $("#div_modPersApeMaterno").hide();
            $("#div_modPersGinstruc").hide();
            $("#div_modPersEcivil").hide();
            $("#div_modPersSexo").hide();
            $("#cbo_modPersDocumento").prop('disabled','disabled');
            $("#txt_modPersNombres").prop('placeholder','RAZON SOCIAL');
          } else {
            $("#txt_modPersNombres").prop('placeholder','NOMBRES');
            $("#div_modPersApePaterno").show();
            $("#div_modPersApeMaterno").show();
            $("#div_modPersGinstruc").show();
            $("#div_modPersEcivil").show();
            $("#div_modPersSexo").show();
            $("#cbo_modPersDocumento").removeAttr('disabled');
          }
        },
        ejecutaSQL : function(){
          let exec = new FormData();
          let datos = Persona.datosToDatabase();
          let foto = $('#file_modPersFoto')[0].files[0];

          exec.append('imgFoto', foto);
          exec.append("appSQL",JSON.stringify(datos));
          let rpta = $.ajax({
            url  : Persona.rutaSQL,
            type : 'POST',
            processData : false,
            contentType : false,
            data : exec
          })
          .fail(function(resp){
            console.log("fail:.... "+resp.responseText);
          });
          return rpta;
        }
      };
    return Persona;
  }
  if(typeof window.Persona === 'undefined'){ window.Persona = inicio(); }
})(window,document);
