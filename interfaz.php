<?php
  include_once('includes/sess_verifica.php');
  include_once('includes/web_config.php');
  if(isset($_SESSION['usr_ID'])) {
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="Expires" content="0">
  <meta http-equiv="Last-Modified" content="0">
  <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <title>BANKcoop [administracion Bancaria]</title>
  <link rel="shortcut icon" href="favicon.ico" />
  <link rel="icon" type="image/vnd.microsoft.icon" href="favicon.ico" />
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="libs/bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="libs/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="libs/ionicons/css/ionicons.min.css">
  <!-- fonts para el sistema -->
  <link rel="stylesheet" href="app/css/fonts.css" />
  <link rel="stylesheet" href="app/css/interfaz.css" />
  <link rel="stylesheet" href="app/css/skin-blue.min.css" />
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

  <!-- jQuery 3 -->
  <script src="libs/jquery/jquery-3.6.0.min.js"></script>
  <!-- jQuery UI 1.11.4 -->
  <script src="libs/jquery-ui/jquery-ui.min.js"></script>
  <!-- Bootstrap 3.3.7 -->
  <script src="libs/bootstrap/js/bootstrap.min.js"></script>
  <!-- app interfaz-->
  <script src="app/js/adminLTE.min.js"></script>
  <script src="app/js/funciones.js"></script>
  <script src="app/js/interfaz.js"></script>
</head>
<body class="hold-transition skin-blue sidebar-mini" <?php if(!($_SESSION['usr_usernivelID']==701 || $_SESSION['usr_usernivelID']==712)) echo('ondragstart="return false" onselectstart="return false" oncontextmenu="return false"');?>>
<div class="wrapper">
  <header class="main-header">
    <!-- Logo -->
    <a href="interfaz.php" class="logo" style="background:#1A2226;">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>B</b>nk</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>BANK</b>coop</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
      </a>
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <?php if($_SESSION['usr_usernivelID']==701){ //solo superadmin?>
          <!-- Notificaciones: style can be found in dropdown.less -->
          <li class="dropdown notifications-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-bell-o"></i>
              <span id="lblNotifiCount1" class="label label-warning NotifiCount"></span>
            </a>
            <ul class="dropdown-menu">
              <li class="header">Tienes <span id="lblNotifiCount2" class="NotifiCount"></span> notificaciones</li>
              <li>
                <ul class="menu" id="appInterfazNotificaciones">
                </ul>
              </li>
              <li class="footer"><a href="javascript:appSubmitButton('notificaciones');">Ver todas...</a></li>
            </ul>
          </li>
          <?php }?>
          <!-- Menu Usuario: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo $_SESSION['usr_urlfoto'];?>" class="user-image" alt="Usuario">
              <span class="hidden-xs">
                <?php echo $_SESSION['usr_nombrecorto'];?>
              </span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="<?php echo $_SESSION['usr_urlfoto'];?>" class="img-circle" alt="User Image">
                <p>
                  <?php echo $_SESSION['usr_nombrecorto'];?>
                  <small><?php echo $_SESSION['usr_cargo']; ?></small>
                </p>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="javascript:appSubmitButton('profile');" class="btn btn-default btn-flat">Perfil</a>
                </div>
                <div class="pull-right">
                  <a href="javascript:appSubmitButton('logout');" class="btn btn-default btn-flat">Salir</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  <!-- =========================== MENU ============================== -->
  <?php
  $menuDashboard = '';
  $menuBlacklist = '';
  $menuMaster = '';
  $menuMasterProductos = '';

  $menuCaja = '';
  $menuCajaVouchers = '';
  $menuCajaAportes = '';
  $menuCajaAhorros = '';
  $menuCajaCaja = '';
  $menuCajaExpedientes = '';
  $menuCajaMovimientos = '';
  $menuCajaStatus = '';

  $menuOperaciones = '';
  $menuOperaPreventa = '';
  $menuOperaConfirmaPagos = '';
  $menuOperaSimulaAhorros = '';
  $menuOperaSimulaCreditos = '';
  $menuOperaEstadistica = '';
  $menuOperaColocaciones = '';
  $menuOperaCartera = '';
  $menuOperaMorosidad = '';

  $menuCoopSUD = '';
  $menuCoopSUDCancelados = '';
  $menuCoopSUDAdminPrest = '';
  $menuCoopSUDPrestamos = '';
  $menuCoopSUDBuscaDir = '';
  $menuCoopSUDAhorros = '';
  $menuCoopSUDTipoServ = '';
  $menuCoopSUDCarlos = '';
  $menuCoopSUDSunat = '';

  $menuConfig = '';
  $menuConfigPersonas = '';
  $menuConfigAgencias = '';
  $menuConfigSocios = '';
  $menuConfigAvales = '';
  $menuConfigWorkers = '';

  $menuRRHH = '';
  $menurrhhWorkers = '';
  $menurrhhVacac = '';
  $menurrhhRenov = '';
  $menurrhhPostul = '';

  $menuConta = '';
  $menuContaCuentas = '';
  $menuContaWorkSheet = '';

  $menuMisc = '';
  $menuMiscCumple = '';
  $menuMiscRecorda = '';

  $menuDocs = '';
  $menuDocsDirPlan = '';
  $menuDocsAdmFin = '';
  $menuDocsRRHH = '';
  $menuDocsLogis = '';
  $menuDocsCred = '';
  $menuDocsCred_docs = '';
  $menuDocsCred_info = '';
  $menuDocsOper = '';
  $menuDocsRecup = '';
  $menuDocsTIC = '';
  $menuDocsAhorros = '';
  $menuDocsLegal = '';
  $menuDocsGesCal = '';
  $menuDocsGesRie = '';

  $appPage = (($_SESSION['usr_usernivelID']==706)?("pages/rrhh/dashboard/dashboard.php"):("pages/global/dashboard/dashboard.php"));
  if(isset($_GET["page"])){
    switch ($_GET["page"]) {
      case "profile": $appPage = "pages/global/profile/profile.php"; break;
      case "notificaciones": $appPage = "pages/global/notifi/notificaciones.php"; break;
      case "blacklist" : $menuBlacklist = 'class="active"'; $appPage = "pages/global/blacklist/blacklist.php"; break;
      case "masterProductos" : $menuMaster = 'active menu-open'; $menuMasterProductos = 'class="active"'; $appPage = "pages/master/productos.php"; break;
      case "rrhhVacac":   $menuRRHH = 'active menu-open'; $menurrhhVacac = 'class="active"'; $appPage = "pages/rrhh/vacaciones/vacaciones.php"; break;
      case "rrhhRenov":   $menuRRHH = 'active menu-open'; $menurrhhRenov = 'class="active"'; $appPage = "pages/rrhh/renovaciones/renovacion.php"; break;
      case "rrhhPostul":  $menuRRHH = 'active menu-open'; $menurrhhPostul = 'class="active"'; $appPage = "pages/rrhh/postulantes/postulantes.php"; break;
      case "cajaVouchers":    $menuCaja = 'active menu-open'; $menuCajaVouchers = 'class="active"'; $appPage = "pages/caja/vouchers/vouchers.php"; break;
      case "cajaAportes":     $menuCaja = 'active menu-open'; $menuCajaAportes = 'class="active"'; $appPage = "pages/caja/aportes/aportes.php"; break;
      case "cajaAhorros":     $menuCaja = 'active menu-open'; $menuCajaAhorros = 'class="active"'; $appPage = "pages/caja/ahorros/ahorros.php"; break;
      case "cajaCaja":        $menuCaja = 'active menu-open'; $menuCajaCaja = 'class="active"'; $appPage = "pages/caja/caja/caja.php"; break;
      case "cajaExpedientes": $menuCaja = 'active menu-open'; $menuCajaExpedientes = 'class="active"'; $appPage = "pages/caja/expedientes/expedientes.php"; break;
      case "cajaMovimientos": $menuCaja = 'active menu-open'; $menuCajaMovimientos = 'class="active"'; $appPage = "pages/caja/movim/movim.php"; break;
      case "cajaStatus":      $menuCaja = 'active menu-open'; $menuCajaStatus = 'class="active"'; $appPage = "pages/caja/status/page.php"; break;
      case "operPreventa":       $menuOperaciones = 'active menu-open'; $menuOperaPreventa = 'class="active"'; $appPage = "pages/oper/captacion/ahorros.php"; break;
      case "operConfimaPagos":   $menuOperaciones = 'active menu-open'; $menuOperaConfirmaPagos = 'class="active"'; $appPage = "pages/oper/confirmapagos/confirma.php"; break;
      case "operSimulaCreditos": $menuOperaciones = 'active menu-open'; $menuOperaSimulaCreditos = 'class="active"'; $appPage = "pages/oper/simula/creditos.php"; break;
      case "operSimulaAhorros":  $menuOperaciones = 'active menu-open'; $menuOperaSimulaAhorros = 'class="active"'; $appPage = "pages/oper/simula/ahorros.php"; break;
      case "operEstadistica":    $menuOperaciones = 'active menu-open'; $menuOperaEstadistica = 'class="active"'; $appPage = "pages/oper/estadistica/estadistica.php"; break;
      case "operCartera":        $menuOperaciones = 'active menu-open'; $menuOperaCartera = 'class="active"'; $appPage = "pages/oper/cartera/cartera.php"; break;
      case "operMorosidad":      $menuOperaciones = 'active menu-open'; $menuOperaMorosidad = 'class="active"'; $appPage = "pages/oper/morosidad/morosidad.php"; break;
      case "operColocaciones":   $menuOperaciones = 'active menu-open'; $menuOperaColocaciones = 'class="active"'; $appPage = "pages/oper/colocaciones/colocaciones.php"; break;
      case "coopCancelados": $menuCoopSUD = 'active menu-open'; $menuCoopSUDCancelados = 'class="active"'; $appPage = "pages/coopsud/cancelados/cancelados.php"; break;
      case "coopAdminPrest": $menuCoopSUD = 'active menu-open'; $menuCoopSUDAdminPrest = 'class="active"'; $appPage = "pages/coopsud/adminprest/prestamos.php"; break;
      case "coopPrestamos":  $menuCoopSUD = 'active menu-open'; $menuCoopSUDPrestamos = 'class="active"'; $appPage = "pages/coopsud/prestamos/prestamos.php"; break;
      case "coopBuscaDir":   $menuCoopSUD = 'active menu-open'; $menuCoopSUDBuscaDir = 'class="active"'; $appPage = "pages/coopsud/buscadir/buscadir.php"; break;
      case "coopAhorros":    $menuCoopSUD = 'active menu-open'; $menuCoopSUDAhorros = 'class="active"'; $appPage = "pages/coopsud/ahorros/ahorros.php"; break;
      case "coopAhorrosX":    $menuCoopSUD = 'active menu-open'; $menuCoopSUDAhorros = 'class="active"'; $appPage = "pages/coopsud/ahorrosX/ahorros.php"; break;
      case "coopTipoServ":   $menuCoopSUD = 'active menu-open'; $menuCoopSUDTipoServ = 'class="active"'; $appPage = "pages/coopsud/tiposerv/tiposerv.php"; break;
      case "coopCarlos":     $menuCoopSUD = 'active menu-open'; $menuCoopSUDCarlos = 'class="active"'; $appPage = "pages/coopsud/carlos/carlos.php"; break;
      case "coopSunat":      $menuCoopSUD = 'active menu-open'; $menuCoopSUDSunat = 'class="active"'; $appPage = "pages/coopsud/sunat/sunat.php"; break;
      case "configPersonas": $menuConfig = 'active menu-open'; $menuConfigPersonas = 'class="active"'; $appPage = "pages/config/personas/personas.php"; break;
      case "configSocios":   $menuConfig = 'active menu-open'; $menuConfigSocios = 'class="active"'; $appPage = "pages/config/socios/socios.php"; break;
      case "configAvales":   $menuConfig = 'active menu-open'; $menuConfigAvales = 'class="active"'; $appPage = "pages/config/avales.php"; break;
      case "rrhhWorkers":    $menuConfig = 'active menu-open'; $menuConfigWorkers = 'class="active"'; $appPage = "pages/rrhh/workers/workers.php"; break;
      case "configWorkers":  $menuConfig = 'active menu-open'; $menuConfigWorkers = 'class="active"'; $appPage = "pages/config/workers/workers.php"; break;
      case "configAgencias": $menuConfig = 'active menu-open'; $menuConfigAgencias = 'class="active"'; $appPage = "pages/config/agencias/agencias.php";  break;
      case "contaCuentas":   $menuConta = 'active menu-open'; $menuContaCuentas = 'class="active"'; $appPage = "pages/conta/cuentas/cuentas.php"; break;
      case "contaWorkSheet": $menuConta = 'active menu-open'; $menuContaWorkSheet = 'class="active"'; $appPage = "pages/conta/worksheet/worksheet.php"; break;
      case "miscCumple":     $menuMisc = 'active menu-open'; $menuMiscCumple = 'class="active"'; $appPage = "pages/global/cumple/miscCumple.php"; break;
      case "miscRecorda":    $menuMisc = 'active menu-open'; $menuMiscRecorda = 'class="active"'; $appPage = "pages/global/recordatorio/recordatorio.php"; break;
      case "docsDirPlan":  $menuDocs = 'active menu-open'; $menuDocsDirPlan = 'class="active"'; $appPage = "pages/global/docs/dirplan.php"; break;
      case "docsAdmFin":   $menuDocs = 'active menu-open'; $menuDocsAdmFin  = 'class="active"'; $appPage = "pages/global/docs/admfin.php"; break;
      case "docsRRHH":     $menuDocs = 'active menu-open'; $menuDocsRRHH    = 'class="active"'; $appPage = "pages/global/docs/rrhh.php"; break;
      case "docsLogis":    $menuDocs = 'active menu-open'; $menuDocsLogis   = 'class="active"'; $appPage = "pages/global/docs/logistica.php"; break;
      case "docsOper":     $menuDocs = 'active menu-open'; $menuDocsOper    = 'class="active"'; $appPage = "pages/global/docs/operaciones.php"; break;
      case "docsRecup":    $menuDocs = 'active menu-open'; $menuDocsRecup   = 'class="active"'; $appPage = "pages/global/docs/recuperaciones.php"; break;
      case "docsTIC":      $menuDocs = 'active menu-open'; $menuDocsTIC     = 'class="active"'; $appPage = "pages/global/docs/sistemas.php"; break;
      case "docsAhorros":  $menuDocs = 'active menu-open'; $menuDocsAhorros = 'class="active"'; $appPage = "pages/global/docs/ahorros.php"; break;
      case "docsLegal":    $menuDocs = 'active menu-open'; $menuDocsLegal   = 'class="active"'; $appPage = "pages/global/docs/legal.php"; break;
      case "docsGesCal":   $menuDocs = 'active menu-open'; $menuDocsGesCal  = 'class="active"'; $appPage = "pages/global/docs/gescalidad.php"; break;
      case "docsGesRie":   $menuDocs = 'active menu-open'; $menuDocsGesRie  = 'class="active"'; $appPage = "pages/global/docs/gesriesgos.php"; break;
      case "docsCred_docs":$menuDocs = 'active menu-open'; $menuDocsCred = 'active menu-open'; $menuDocsCred_docs = 'class="active"'; $appPage = "pages/global/docs/creditos.php"; break;
      case "docsCred_info":$menuDocs = 'active menu-open'; $menuDocsCred = 'active menu-open'; $menuDocsCred_info = 'class="active"'; $appPage = "pages/global/docs/creditos.php"; break;
    }
  } else{
    $menuDashboard = 'class="active"';
  }
  ?>

  <aside class="main-sidebar">
    <!-- MENU PRINCIPAL -->
    <section class="sidebar">
      <div class="user-panel" style="background:#1A2226;display:none;">
        <div class="pull-left image">
          <img src="<?php echo $_SESSION['usr_urlfoto'];?>" class="img-circle" alt="foto de usuario">
        </div>
        <div class="pull-left info">
          <p> <?php echo $_SESSION['usr_nombrecorto'];?> </p>
          <small style="color:#859E9E;position:relative;top:-5px;"><?php echo $_SESSION['usr_login'];?></small>
        </div>
      </div>
      <ul class="sidebar-menu" data-widget="tree">
        <li <?php echo($menuDashboard);?>>
          <a href="interfaz.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a>
        </li>
        <li <?php echo($menuBlacklist);?>>
          <a href="javascript:appSubmitButton('blacklist');"><i class="fa fa-user-secret"></i> <span>Lista Negra</span></a>
        </li>
        <?php
          if($_SESSION['usr_usernivelID']==701) { //menu solo para super
        ?>
        <li class="treeview <?php echo($menuMaster);?>">
          <a href="#">
            <i class="fa fa-gears"></i>
            <span>Maestro</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
            <li <?php echo($menuMasterProductos);?>><a href="javascript:appSubmitButton('masterProductos');"><i class="fa fa-wrench"></i> <span>Productos</span></a></li>
          </ul>
        </li>
        <?php }
          if($_SESSION['usr_usernivelID']==701 || $_SESSION['usr_usernivelID']==706) { //menu de RRHH solo para super,RRHH
        ?>
        <li class="treeview <?php echo($menuRRHH);?>">
          <a href="#">
            <i class="fa fa-gears"></i>
            <span>RRHH</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
            <li <?php echo($menurrhhVacac);?>><a href="javascript:appSubmitButton('rrhhVacac');"><i class="fa fa-circle-o"></i> <span>Vacaciones</span></a></li>
            <li <?php echo($menurrhhRenov);?>><a href="javascript:appSubmitButton('rrhhRenov');"><i class="fa fa-circle-o"></i> <span>Renovaciones</span></a></li>
            <li <?php echo($menurrhhPostul);?>><a href="javascript:appSubmitButton('rrhhPostul');"><i class="fa fa-circle-o"></i> <span>Postulantes</span></a></li>
          </ul>
        </li>
        <?php } ?>
        <?php if($_SESSION['usr_usernivelID']==701 || $_SESSION['usr_usernivelID']==704 || $_SESSION['usr_usernivelID']==712 || $_SESSION['usr_usernivelID']==705){ //super,conta,caja,jefaturas ?>
        <li class="treeview <?php echo($menuCaja);?>">
          <a href="#">
            <i class="fa fa-gears"></i>
            <span>Caja</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
            <?php /*
            <li <?php echo($menuCajaVouchers);?>><a href="javascript:appSubmitButton('cajaVouchers');"><i class="fa fa-circle-o"></i> <span>Vouchers</span></a></li>
            <li <?php echo($menuCajaAportes);?>><a href="javascript:appSubmitButton('cajaAportes');"><i class="fa fa-circle-o"></i> <span>Aportes</span></a></li>
            <li <?php echo($menuCajaAhorros);?>><a href="javascript:appSubmitButton('cajaAhorros');"><i class="fa fa-circle-o"></i> <span>Ahorros</span></a></li>
            <li <?php echo($menuCajaCaja);?>><a href="javascript:appSubmitButton('cajaCaja');"><i class="fa fa-circle-o"></i> <span>Caja</span></a></li>
            <li <?php echo($menuCajaExpedientes);?>><a href="javascript:appSubmitButton('cajaExpedientes');"><i class="fa fa-circle-o"></i> <span>Expedientes</span></a></li>
            */?>
            <li <?php echo($menuCajaMovimientos);?>><a href="javascript:appSubmitButton('cajaMovimientos');"><i class="fa fa-circle-o"></i> <span>Movimientos</span></a></li>
            <li <?php echo($menuCajaStatus);?>><a href="javascript:appSubmitButton('cajaStatus');"><i class="fa fa-circle-o"></i> <span>Status</span></a></li>
          </ul>
        </li>
        <?php } ?>
        <li class="treeview <?php echo($menuOperaciones);?>">
          <a href="#">
            <i class="fa fa-gears"></i>
            <span>Operaciones</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
            <?php if($_SESSION['usr_usernivelID']==701 || $_SESSION['usr_usernivelID']==703 || $_SESSION['usr_usernivelID']==711 || $_SESSION['usr_usernivelID']==714){ //super, gerencia, asesorahorros ?>
            <li <?php echo($menuOperaPreventa);?>><a href="javascript:appSubmitButton('operPreventa');"><i class="fa fa-circle-o"></i> <span>Captacion - Ahorros</span></a></li>
            <?php } ?>
            <li <?php echo($menuOperaConfirmaPagos);?>><a href="javascript:appSubmitButton('operConfimaPagos');"><i class="fa fa-circle-o"></i> <span>Confirmacion de Pagos</span></a></li>
            <li <?php echo($menuOperaSimulaAhorros);?>><a href="javascript:appSubmitButton('operSimulaAhorros');"><i class="fa fa-circle-o"></i> <span>Simulador de Ahorros</span></a></li>
            <li <?php echo($menuOperaSimulaCreditos);?>><a href="javascript:appSubmitButton('operSimulaCreditos');"><i class="fa fa-circle-o"></i> <span>Simulador de Creditos</span></a></li>
            <li <?php echo($menuOperaEstadistica);?>><a href="javascript:appSubmitButton('operEstadistica');"><i class="fa fa-circle-o"></i> <span>Estadistica</span></a></li>
            <li <?php echo($menuOperaCartera);?>><a href="javascript:appSubmitButton('operCartera');"><i class="fa fa-circle-o"></i> <span>Cartera</span></a></li>
            <li <?php echo($menuOperaMorosidad);?>><a href="javascript:appSubmitButton('operMorosidad');"><i class="fa fa-circle-o"></i> <span>Morosidad</span></a></li>
            <li <?php echo($menuOperaColocaciones);?>><a href="javascript:appSubmitButton('operColocaciones');"><i class="fa fa-circle-o"></i> <span>Colocaciones</span></a></li>
          </ul>
        </li>

        <li class="treeview <?php echo($menuCoopSUD);?>">
          <a href="#">
            <i class="fa fa-gears"></i>
            <span>CoopSUD</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
            <?php if($_SESSION['usr_usernivelID']==701 || $_SESSION['usr_usernivelID']==710){ //super, practicante ?>
              <li <?php echo($menuCoopSUDCarlos);?>><a href="javascript:appSubmitButton('coopCarlos');"><i class="fa fa-circle-o"></i> <span>Carlos</span></a></li>
              <li <?php echo($menuCoopSUDSunat);?>><a href="javascript:appSubmitButton('coopSunat');"><i class="fa fa-circle-o"></i> <span>SUNAT</span></a></li>
            <?php } ?>
            <?php if($_SESSION['usr_usernivelID']==701 || $_SESSION['usr_usernivelID']==707 || $_SESSION['usr_usernivelID']==710){ //super, riesgos, practicante ?>
              <li <?php echo($menuCoopSUDAdminPrest);?>><a href="javascript:appSubmitButton('coopAdminPrest');"><i class="fa fa-circle-o"></i> <span>Admin Prestamos</span></a></li>
            <?php } ?>
            <li <?php echo($menuCoopSUDAhorros);?>><a href="javascript:appSubmitButton('coopAhorros');"><i class="fa fa-circle-o"></i> <span>Ahorros</span></a></li>
            <li <?php echo($menuCoopSUDPrestamos);?>><a href="javascript:appSubmitButton('coopPrestamos');"><i class="fa fa-circle-o"></i> <span>Prestamos</span></a></li>
            <li <?php echo($menuCoopSUDCancelados);?>><a href="javascript:appSubmitButton('coopCancelados');"><i class="fa fa-circle-o"></i> <span>Cancelados</span></a></li>
            <li <?php echo($menuCoopSUDBuscaDir);?>><a href="javascript:appSubmitButton('coopBuscaDir');"><i class="fa fa-circle-o"></i> <span>BuscaDir</span></a></li>
            <li <?php echo($menuCoopSUDTipoServ);?>><a href="javascript:appSubmitButton('coopTipoServ');"><i class="fa fa-circle-o"></i> <span>Servicios</span></a></li>
          </ul>
        </li>
        <li class="treeview <?php echo($menuConfig);?>">
          <a href="#">
            <i class="fa fa-gears"></i>
            <span>Mantenimento</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
            <li <?php echo($menuConfigPersonas);?>><a href="javascript:appSubmitButton('configPersonas');"><i class="fa fa-user"></i> <span>Personas</span></a></li>
            <?php
              if($_SESSION['usr_usernivelID']==701 || $_SESSION['usr_usernivelID']==704 || $_SESSION['usr_usernivelID']==712){
                echo "<li ".($menuConfigSocios)."><a href=\"javascript:appSubmitButton('configSocios');\"><i class=\"fa fa-user\"></i> <span>Socios</span></a></li>";
              }
            ?>
            <?php
              if($_SESSION['usr_usernivelID']==701 || $_SESSION['usr_usernivelID']==711){
                echo "<li ".($menuConfigAvales)."><a href=\"javascript:appSubmitButton('configAvales');\"><i class=\"fa fa-user\"></i> <span>Avales</span></a></li>";
              }
              if($_SESSION['usr_usernivelID']<=711){ //superadmin, gerencia y jefaturas
            ?>
                <li <?php echo($menuConfigWorkers);?>><a href="javascript:appSubmitButton('<?php if($_SESSION["usr_usernivelID"]==701) { echo("configWorkers"); } else { echo("rrhhWorkers"); } ?>');"><i class="fa fa-user"></i> <span>Empleados</span></a></li>
            <?php
              }
            ?>
            <li <?php echo($menuConfigAgencias);?>><a href="javascript:appSubmitButton('configAgencias');"><i class="fa fa-circle-o"></i> <span>Agencias</span></a></li>
          </ul>
        </li>
        <?php if($_SESSION['usr_usernivelID']==701 || $_SESSION['usr_usernivelID']==704){ ?>
        <li class="treeview <?php echo($menuConta);?>">
          <a href="#">
            <i class="fa fa-share-alt"></i>
            <span>Contabilidad</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
            <li <?php echo($menuContaCuentas);?>><a href="javascript:appSubmitButton('contaCuentas');"><i class="fa fa-share-alt"></i> <span>Ctas Contables</span></a></li>
            <li <?php echo($menuContaWorkSheet);?>><a href="javascript:appSubmitButton('contaWorkSheet');"><i class="fa fa-share-alt"></i> <span>WorkSheet</span></a></li>
          </ul>
        </li>
        <?php } ?>
        <li class="treeview <?php echo($menuMisc);?>">
          <a href="#">
            <i class="fa fa-pie-chart"></i>
            <span>Miscelaneos</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
            <li <?php echo($menuMiscCumple);?>><a href="javascript:appSubmitButton('miscCumple');"><i class="fa fa-circle-o"></i> <span>Cumpleaños</span></a></li>
            <li <?php echo($menuMiscRecorda);?>><a href="javascript:appSubmitButton('miscRecorda');"><i class="fa fa-circle-o"></i> <span>Recordatorios</span></a></li>
          </ul>
        </li>
        <li class="treeview <?php echo($menuDocs);?>">
          <a href="#">
            <i class="fa fa-leanpub"></i>
            <span>Repositorio GIS</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
            <li <?php echo($menuDocsDirPlan);?>><a href="javascript:appSubmitButton('docsDirPlan');"><i class="fa fa-circle-o"></i> <span>Direccion y Plane.</span></a></li>
            <li <?php echo($menuDocsAdmFin);?>><a href="javascript:appSubmitButton('docsAdmFin');"><i class="fa fa-circle-o"></i> <span>Adm. Financiera</span></a></li>
            <li <?php echo($menuDocsRRHH);?>><a href="javascript:appSubmitButton('docsRRHH');"><i class="fa fa-circle-o"></i> <span>RR.HH.</span></a></li>
            <li <?php echo($menuDocsLogis);?>><a href="javascript:appSubmitButton('docsLogis');"><i class="fa fa-circle-o"></i> <span>Logistica</span></a></li>
            <li class="treeview <?php echo($menuDocsCred);?>">
              <a href="#">
                <i class="fa fa-circle-o"></i> <span>Creditos</span>
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
              </a>
              <ul class="treeview-menu">
                <li <?php echo($menuDocsCred_docs);?>><a href="javascript:appSubmitButton('docsCred_docs');"><i class="fa fa-circle-o"></i> <span>Documentos</span></a></li>
                <li <?php echo($menuDocsCred_info);?>><a href="javascript:appSubmitButton('docsCred_info');"><i class="fa fa-circle-o"></i> <span>Informes</span></a></li>
              </ul>
            </li>
            <li <?php echo($menuDocsOper);?>><a href="javascript:appSubmitButton('docsOper');"><i class="fa fa-circle-o"></i> <span>Operaciones</span></a></li>
            <li <?php echo($menuDocsRecup);?>><a href="javascript:appSubmitButton('docsRecup');"><i class="fa fa-circle-o"></i> <span>Recuperaciones</span></a></li>
            <li <?php echo($menuDocsTIC);?>><a href="javascript:appSubmitButton('docsTIC');"><i class="fa fa-circle-o"></i> <span>Sistemas TIC</span></a></li>
            <li <?php echo($menuDocsAhorros);?>><a href="javascript:appSubmitButton('docsAhorros');"><i class="fa fa-circle-o"></i> <span>Atencion Cliente</span></a></li>
            <li <?php echo($menuDocsLegal);?>><a href="javascript:appSubmitButton('docsLegal');"><i class="fa fa-circle-o"></i> <span>Legal</span></a></li>
            <li <?php echo($menuDocsGesCal);?>><a href="javascript:appSubmitButton('docsGesCal');"><i class="fa fa-circle-o"></i> <span>Gestion Calidad</span></a></li>
            <li <?php echo($menuDocsGesRie);?>><a href="javascript:appSubmitButton('docsGesRie');"><i class="fa fa-circle-o"></i> <span>Gestion Riesgos</span></a></li>
          </ul>
        </li>
      </ul>
    </section>
  </aside>
  <!-- ========================= CONTENIDO  ====================== -->
  <div class="content-wrapper">
    <?php include_once($appPage); ?>
  </div>
</div>
<?php if($_SESSION['usr_usernivelID']==701){ //solo superadmin?>
  <script type="text/javascript">
    //$(document).ready(function(){ setTimeout(appNotificacionesSetInterval,1); });
  </script>
<?php }?>
</body>
</html>
<?php
  } else {
    header('location:index.php');
  }
?>
