$(document).on('submit','#frmLogin', btn_submit);

async function btn_submit(event){
  try {
    event.preventDefault();
    let data = new FormData();
    data.append('frmLogin', JSON.stringify({
      login : $('#txt_UserName').val(),
      passw : SHA1($("#txt_UserPass").val()).toString().toUpperCase()
    }));
    const ruta = await fetch('includes/sess_login.php', { method: 'POST', body: data });
    const resp = await ruta.json();
    if(!resp.error) { //sin errores
      location.href = 'interfaz.php';
    } else {
      $('.login_WarningText').fadeIn('fast');
      setTimeout(function() {
        $('.login_WarningText').fadeOut('fast');
        $('#txt_UserName').val("");
        $('#txt_UserPass').val("");
        $('#txt_UserName').focus();
        $('#botonOK').val("ACCESAR");
      }, 2000);
    }
  } catch(err) {
    console.log(err);
    $('#pn_Warning').slideDown('fast');
    setTimeout(function() { $('#pn_Warning').slideUp('fast'); }, 2000);
    $('#botonOK').val("ACCESAR");
  }
};
