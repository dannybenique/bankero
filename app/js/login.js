$(document).on('submit','#frmLogin',function(event){
  event.preventDefault();

  let datos = {
    login : $("#txt_UserName").val(),
    passw : SHA1($("#txt_UserPass").val()).toString().toUpperCase()
  }

  $.ajax({
    url:'includes/sess_login.php',
    type:'POST',
    dataType:'json',
    data:{"frmLogin":JSON.stringify(datos)},
    beforeSend:function() {
      $('#botonOK').val('Validando....');
    }
  })
  .done(function(resp){
    console.log(resp);
    if (resp.error==0) { //sin errores
      location.href = 'interfaz.php';
    } else {
      console.log("error:... "+resp.error);
      $('.login_WarningText').fadeIn('fast');
      setTimeout(function() {
        $('.login_WarningText').fadeOut('fast');
        $('#txt_UserName').val('');
        $('#txt_UserPass').val('');
        $('#txt_UserName').focus();
        $('#botonOK').val('ACCESAR');
        //location.href='index.php';
      },2000);
    }
  })
  .fail(function(resp){
    console.log("fail:.... "+resp.responseText);
    $('#pn_Warning').slideDown('fast');
    setTimeout(function() { $('#pn_Warning').slideUp('fast'); },2000);
    $('#botonOK').val('ACCESAR');
  })
  .always(function(){
    console.log("always:.... complete");
  });
});
