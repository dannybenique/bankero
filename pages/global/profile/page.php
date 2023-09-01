<script src="libs/moment/min/moment.min.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-user"></i> <b>Perfil de Usuario</b></h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Perfil</li>
  </ol>
</section>
<section class="content">
  <div class="row">
    <div class="col-md-3">
      <div class="box box-primary">
        <div class="box-body box-profile">
          <img id="perfil_imagen" class="profile-user-img img-responsive img-circle" src="data/personas/fotouser.jpg" alt="Foto de Usuario">
          <h3 id="perfil_nombrecorto" class="profile-username text-center" style="font-family:flexoregular;font-weight:bold;"></h3>
          <p id="perfil_cargo" class="text-muted text-center"></p>
          <ul class="list-group list-group-unbordered">
            <li class="list-group-item">
              <span>DUI</span> <a id="perfil_DNI" class="pull-right"></a></li>
            <li class="list-group-item">
              <span>Celular</span> <a id="perfil_Celular" class="pull-right"></a></li>
            <li class="list-group-item">
              <span>Agencia</span> <a id="perfil_Agencia" class="pull-right"></a></li>
          </ul>
        </div>
      </div>
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title" style="font-family:flexoregular;font-weight:bold;">Acerca de mi</h3>
        </div>
        <div class="box-body">
          <span style="font-family:flexoregular;font-weight:bold;"><i class="fa fa-envelope margin-r-5"></i> Correo</span>
          <p id="perfil_Correo" class="text-muted"></p>
          <hr>
          <span style="font-family:flexoregular;font-weight:bold;"><i class="fa fa-map-marker margin-r-5"></i> Direccion</span>
          <p id="perfil_Direccion" class="text-muted"></p>
        </div>
      </div>
    </div>
    <div class="col-md-9">
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
          <li class="active"><a href="#settings" data-toggle="tab">Personal</a></li>
          <li><a href="#password" data-toggle="tab">Password</a></li>
          <?php //<li><a href="#timeline" data-toggle="tab">Timeline</a></li> ?>
        </ul>
        <div class="tab-content">
          <div class="tab-pane active" id="settings">
            <ul class="appDatosPers appDatosPers-inverse">
              <li><div class="timeline-item"><h3 class="timeline-header no-border" style="font-family:flexoregular;"><span class="appSpanPerfil">Nombres</span><span id="perfilDatos_Nombres"></span></h3></div></li>
              <li><div class="timeline-item"><h3 class="timeline-header no-border" style="font-family:flexoregular;"><span class="appSpanPerfil">Apellidos</span><span id="perfilDatos_Apellidos"></span></h3></div></li>
              <li><div class="timeline-item"><h3 class="timeline-header no-border" style="font-family:flexoregular;"><span class="appSpanPerfil">Nacim.</span><span id="perfilDatos_FechaNac"></span></h3></div></li>
              <li><div class="timeline-item"><h3 class="timeline-header no-border" style="font-family:flexoregular;"><span class="appSpanPerfil">Sexo</span><span id="perfilDatos_Sexo"></span></h3></div></li>
              <li><div class="timeline-item"><h3 class="timeline-header no-border" style="font-family:flexoregular;"><span class="appSpanPerfil">Estudios</span><span id="perfilDatos_GInstruccion"></span></h3></div></li>
              <li><div class="timeline-item"><h3 class="timeline-header no-border" style="font-family:flexoregular;"><span class="appSpanPerfil">E. Civil</span><span id="perfilDatos_ECivil"></span></h3></div></li>
              <li><div class="timeline-item"><h3 class="timeline-header no-border" style="font-family:flexoregular;"><span class="appSpanPerfil">Ocupacion</span><span id="perfilDatos_Ocupacion"></span></h3></div></li>
              <li><div class="timeline-item"><h3 class="timeline-header no-border" style="font-family:flexoregular;"><span class="appSpanPerfil">Observac.</span><span id="perfilDatos_Observac"></span></h3></div></li>
            </ul>
          </div>
          <div class="tab-pane" id="password">
            <form class="form-horizontal" autocomplete="off">
              <div class="box-body">
                <div class="col-md-12">
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon no-border">Nuevo Password</span>
                      <input type="password" class="form-control" id="txt_passwordNew" placeholder="password..." autocomplete="new-password">
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon no-border">Repetir Password</span>
                      <input type="password" class="form-control" id="txt_passwordRenew" placeholder="repetir password..." autocomplete="new-password">
                    </div>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-10">
                  <button type="button" class="btn btn-danger" onclick="javascript:appProfileCambiarPassw(<?php echo($_SESSION['usr_ID']); ?>,'#txt_passwordNew','#txt_passwordRenew');">Cambiar Password</button>
                </div>
              </div>
            </form>
          </div>
          <div class="tab-pane" id="timeline">
            <!-- The timeline -->
            <ul class="timeline timeline-inverse">
              <!-- timeline time label -->
              <li class="time-label">
                <span class="bg-blue" style="font-family:flexobold;">10 Feb. 2014</span>
              </li>
              <li>
                <i class="fa fa-plus bg-aqua"></i>
                <div class="timeline-item">
                  <span class="time"><i class="fa fa-clock-o"></i> 5 mins ago</span>
                  <h3 class="timeline-header no-border"><a href="#">Sarah Young</a> accepted your friend request</h3>
                </div>
              </li>
              <li>
                <i class="fa fa-pencil bg-yellow"></i>
                <div class="timeline-item">
                  <span class="time"><i class="fa fa-clock-o"></i> 27 mins ago</span>
                  <h3 class="timeline-header"><a href="#">Jay White</a> commented on your post</h3>
                </div>
              </li>
              <li>
                <i class="fa fa-minus bg-red"></i>
                <div class="timeline-item">
                  <span class="time"><i class="fa fa-clock-o"></i> 27 mins ago</span>
                  <h3 class="timeline-header"><a href="#">Jay White</a> commented on your post</h3>
                </div>
              </li>
              <li class="time-label">
                <span class="bg-green" style="font-family:flexobold;">3 Jan. 2014</span>
              </li>
              <li>
                <i class="fa fa-user bg-aqua"></i>
                <div class="timeline-item">
                  <span class="time"><i class="fa fa-clock-o"></i> 5 mins ago</span>
                  <h3 class="timeline-header no-border"><a href="#">Sarah Young</a> accepted your friend request</h3>
                </div>
              </li>
              <li>
                <i class="fa fa-comments bg-yellow"></i>
                <div class="timeline-item">
                  <span class="time"><i class="fa fa-clock-o"></i> 27 mins ago</span>
                  <h3 class="timeline-header"><a href="#">Jay White</a> commented on your post</h3>
                </div>
              </li>
              <li>
                <i class="fa fa-clock-o bg-gray"></i>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script type="text/javascript" src="libs/webtoolkit/webtoolkit.sha1.js"></script>
<script src="pages/global/profile/script.js"></script>
<script>
  $(document).ready(function(){
    appProfile(<?php echo($_SESSION['usr_ID']); ?>);
  });
</script>
