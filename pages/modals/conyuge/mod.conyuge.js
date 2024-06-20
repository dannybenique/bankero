//=========================funciones para crear el modal de laboral============================
(function (window,document){
  var iniConyuge = function(){
      var Conyuge = {
        rutaSQL : "pages/modals/conyuge/mod.sql.php",
        rutaHTML : "pages/modals/conyuge/mod.conyuge.htm",
        personaID : 0,
        conyugeID : 0,
        laboralID : 0,
        commandSQL : "",
        addModalToParentForm : function(contenedor) { $("#"+contenedor).load(Conyuge.rutaHTML); },
        close : function(){ $("#modalCony").modal("hide"); },
        lnkConyuge : function(){
          Persona.openBuscar('VerifyConyuge',Conyuge.rutaSQL,true,true,false);

          $('#btn_modPersInsert').off('click').on('click',async function(e) {
            if(Persona.sinErrores()){
              try{
                const resp = await Persona.ejecutaSQL();
                Persona.close();
                Conyuge.datosToForm({
                  id_conyuge : data.tablaPers.ID,
                  tiempoRelacion : 1,
                  persona : data.tablaPers
                });
                $("#modalCony").modal();
                $('#btn_modConyLaboral').show();
              } catch(err){
                console.error('Error al cargar datos:', err);
              }
            } else {
              alert("!!!Faltan llenar Datos!!!");
            }
            e.stopImmediatePropagation();
            $('#btn_modPersInsert').off('click');
          });
          $('#btn_modPersUpdate').off('click').on('click',function(e) {
            console.log("ingreso por update aun no esta definido");
            e.stopImmediatePropagation();
            $('#btn_modPersUpdate').off('click');
          });
          $('#btn_modPersAddToForm').off('click').on('click',function(e) {
            //console.log(otro);
            Persona.close();
            Conyuge.datosToForm({
              id_conyuge : Persona.tablaPers.ID,
              tiempoRelacion : 1,
              persona : Persona.tablaPers
            });
            $("#modalCony").modal();
            $('#btn_modConyLaboral').show();
            e.stopImmediatePropagation();
            $('#btn_modPersAddToForm').off('click');
          });
        },
        nuevo : function(personaID){
          let datos = {
            id_conyuge : 0,
            tiempoRelacion : 1
          }
          Conyuge.commandSQL = "INS";
          Conyuge.personaID = personaID;
          Conyuge.datosToForm(datos);
          $("#modConyTitulo").html("Nuevo Datos Conyugales");
          $("#btn_modConyInsert").show();
          $("#btn_modConyUpdate").hide();
          Conyuge.lnkConyuge();
        },
        editar : async function(personaID){
          try{
            const resp = await appAsynFetch({
              TipoQuery : 'selConyuge',
              personaID : personaID
            },Conyuge.rutaSQL);
            //respuesta
            Conyuge.commandSQL = "UPD";
            Conyuge.personaID = personaID;
            Conyuge.datosToForm(resp);
            $("#modConyTitulo").html("Editar Datos Conyugales");
            $("#btn_modConyUpdate").show();
            $("#btn_modConyInsert").hide();
            $("#modalCony").modal();
          } catch(err) {
            console.error('Error al cargar datos:', err);
          }
        },
        borrar : async function(personaID){
          try{
            const resp = await appAsynFetch({
              TipoQuery : "delConyuge",
              commandSQL: "DEL",
              personaID : personaID
            }, Conyuge.rutaSQL);
            return rpta;
          } catch(err){
            console.error('Error al cargar datos:', err);
          }
        },
        sinErrores : function(){
          let Error = true;
          $('.form-group').removeClass('has-error');
          if($("#txt_modConyTiempoRela").val().trim()=="") { $("#div_modConyTiempoRela").addClass("has-error"); Error = false; }
          if(Conyuge.conyugeID==0) { Error = false; }
          return Error;
        },
        datosToDatabase : function(){
          let data = {
            TipoQuery : "execConyuge",
            commandSQL : Conyuge.commandSQL,
            personaID : Conyuge.personaID,
            conyugeID : Conyuge.conyugeID,
            permisoID : $('#hid_modConyPermisoID').val(),
            tiempoRelacion : $("#txt_modConyTiempoRela").val()
          };
          return data;
        },
        datosToForm : function(data){
          //datos personales
          Conyuge.conyugeID = data.id_conyuge;
          if(data.id_conyuge>0){
            $('#lbl_modConyNombres').html(data.persona.nombres);
            $('#lbl_modConyApellidos').html(data.persona.ap_paterno+" "+data.persona.ap_materno);
            $('#lbl_modConyNroDNI').html(data.persona.nroDUI);
            $('#lbl_modConyFechaNac').html(moment(data.persona.fechanac).format("DD/MM/YYYY"));
            $('#lbl_modConyEcivil').html(data.persona.ecivil);
          } else {
            $('#lbl_modConyNombres, #lbl_modConyApellidos, #lbl_modConyNroDNI, #lbl_modConyFechaNac, #lbl_modConyEcivil').html("");
          }

          //datos relacion
          $("#txt_modConyTiempoRela").val(data.tiempoRelacion);
        },
        ejecutaSQL : async function(){
          try{
            const resp = await appAsynFetch(Conyuge.datosToDatabase(), Conyuge.rutaSQL);
            return resp;
          }catch(err){
            console.error('Error al cargar datos:', err);
          }
        },
      };
    return Conyuge;
  }
  if(typeof window.Conyuge === 'undefined'){ window.Conyuge = iniConyuge(); }
})(window,document);
