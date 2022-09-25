<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Perfil de Usuario</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Perfil de Usuario</li>
  </ol>
</section>
<section class="content">
  <div class="row">
    <div class="col-md-3">
      <div class="box box-primary">
        <div class="box-body box-profile">
          <img class="profile-user-img img-responsive img-circle" src="<?php echo $_SESSION['usr_urlfoto'];?>" alt="Foto de Usuario">
          <h3 class="profile-username text-center" style="font-family:flexobold"> <?php echo $_SESSION['usr_nombrecorto'];?> </h3>
          <p class="text-muted text-center"> <?php echo $_SESSION['usr_cargo'];?> </p>
          <ul class="list-group list-group-unbordered">
            <li class="list-group-item">
              <span>Documento</span> <a id="proDNI" class="pull-right"></a></li>
            <li class="list-group-item">
              <span>Celular</span> <a id="proCelular" class="pull-right"></a></li>
            <li class="list-group-item">
              <span>Agencia</span> <a id="proAgencia" class="pull-right"></a></li>
          </ul>
        </div>
      </div>

      <!-- About Me Box -->
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title" style="font-family:flexobold">Acerca de mi</h3>
        </div>
        <div class="box-body">
          <span style="font-family:flexobold"><i class="fa fa-envelope margin-r-5"></i> Correo</span>
          <p id="proCorreo" class="text-muted"> </p>
          <hr>
          <span style="font-family:flexobold"><i class="fa fa-map-marker margin-r-5"></i> Direccion</span>
          <p id="proDireccion" class="text-muted">Malibu, California</p>
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
              <li><div class="timeline-item"><h3 class="timeline-header no-border fontFlexoRegular"><span class="appSpanPerfil">Nombres</span><span id="proDatNombres"></span></h3></div></li>
              <li><div class="timeline-item"><h3 class="timeline-header no-border fontFlexoRegular"><span class="appSpanPerfil">Apellidos</span><span id="proDatApellidos"></span></h3></div></li>
              <li><div class="timeline-item"><h3 class="timeline-header no-border fontFlexoRegular"><span class="appSpanPerfil">Nacim.</span><span id="proDatFechaNac"></span></h3></div></li>
              <li><div class="timeline-item"><h3 class="timeline-header no-border fontFlexoRegular"><span class="appSpanPerfil">Sexo</span><span id="proDatSexo"></span></h3></div></li>
              <li><div class="timeline-item"><h3 class="timeline-header no-border fontFlexoRegular"><span class="appSpanPerfil">Estudios</span><span id="proDatGInstruccion"></span></h3></div></li>
              <li><div class="timeline-item"><h3 class="timeline-header no-border fontFlexoRegular"><span class="appSpanPerfil">E. Civil</span><span id="proDatECivil"></span></h3></div></li>
              <li><div class="timeline-item"><h3 class="timeline-header no-border fontFlexoRegular"><span class="appSpanPerfil">Ocupacion</span><span id="proDatOcupacion"></span></h3></div></li>
              <li><div class="timeline-item"><h3 class="timeline-header no-border fontFlexoRegular"><span class="appSpanPerfil">Observac.</span><span id="proDatObservac"></span></h3></div></li>
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
    <!-- /.col -->
  </div>
  <!-- /.row -->
</section>

<!-- SHA1 -->
<script type="text/javascript" src="libs/webtoolkit/webtoolkit.sha1.js"></script>
<script src="pages/global/profile/profile.js"></script>
<script>
  $(document).ready(function(){
    appProfileGetOne(<?php echo($_SESSION['usr_ID']); ?>);
  });
</script>
