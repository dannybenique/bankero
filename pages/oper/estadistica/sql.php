<?php
  include_once('../../../includes/sess_verifica.php');

    if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');
      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      switch ($data->TipoQuery) {
        case "dashboard_Creditos":
          //verificamos nivel de usuario
          $sql = "select id_usernivel,codagenc,id_agencia from vw_usuarios where ID=".$_SESSION['usr_ID'];
          $qryUser = $db->select($sql);
          if ($db->has_rows($qryUser)) { $rsUser = $db->fetch_array($qryUser); }

          //llenar grafico de cartera - morosidad
          $whr = "";
          $grafico = array();
          switch($_SESSION['usr_usernivelID']){
            case 701: //super
            case 703: //gerencia
            case 704: //contabilidad
            case 710: //practica sistemas
            case 705: $whr = $data->agenciaID.",0"; break; //jefatura
            case 711: $whr = $_SESSION['usr_agenciaID'].",0"; break; //administracion
            case 712: $whr = $_SESSION['usr_agenciaID'].",0"; break; //caja
            case 713: $whr = $_SESSION['usr_agenciaID'].",".$_SESSION['usr_ID']; break; //analista
          }
          $qryCart = $db->select("select * from dbo.fn_HistoCrediCartera(".$whr.")");
          $qryColc = $db->select("select * from dbo.fn_HistoCrediColocacion(".$whr.")");
          $qryMora = $db->select("select * from dbo.fn_HistoCrediMorosidad(".$whr.")");
          $final = $db->num_rows($qryCart);
          for($xx = 0; $xx < $final; $xx++){
            $rsCart = $db->fetch_array($qryCart);
            $rsColc = $db->fetch_array($qryColc);
            $rsMora = $db->fetch_array($qryMora);

            if($xx==0){ $mesini = $rsCart["smes"]." ".$rsCart["nyear"]; }
            if($xx==($final-1)){ $mesfin = $rsCart["smes"]." ".$rsCart["nyear"]; }
            $grafico[] = array(
              "meses" => $rsCart["smes"],
              "carteraSaldo" => $rsCart["monto"]*1,
              "carteraCantidad" => $rsCart["cantidad"]*1,
              "colocSaldo" => $rsColc["monto"]*1,
              "colocCantidad" => $rsColc["cantidad"]*1,
              "moraSaldo" => $rsMora["monto"]*1,
              "moraCantidad" => $rsMora["cantidad"]*1
            );
          }

          //respuesta
          $rpta = array("titulo"=>"Cartera, Colocacion y Morosidad ".$mesini." - ".$mesfin,"grafico"=>$grafico);
          echo json_encode($rpta);
          break;
        case "dashboard_Ahorros":
          //verificamos nivel de usuario
          $sql = "select id_usernivel,codagenc,id_agencia from vw_usuarios where ID=".$_SESSION['usr_ID'];
          $qryUser = $db->select($sql);
          if ($db->has_rows($qryUser)) { $rsUser = $db->fetch_array($qryUser); }

          //llenar grafico de cartera - morosidad
          $whr = "";
          $grafico = array();
          switch($_SESSION['usr_usernivelID']){
            case 701: //super
            case 703: //gerencia
            case 704: $whr = "0,0"; break;//contabilidad
          }
          $qrymovilPE = $db->select("select * from dbo.fn_HistoAhorroCartera(".$whr.",1,212)");//ahorro movil soles
          $qrymovilUS = $db->select("select * from dbo.fn_HistoAhorroCartera(".$whr.",2,212)");//ahorro movil dolares
          $qrydpfPE = $db->select("select * from dbo.fn_HistoAhorroCartera(".$whr.",1,222)");//DPF soles
          $qrydpfUS = $db->select("select * from dbo.fn_HistoAhorroCartera(".$whr.",2,222)");//DPF dolares

          $final = $db->num_rows($qrydpfPE);
          for($xx = 0; $xx < $final; $xx++){
            $rsmovilPE = $db->fetch_array($qrymovilPE);
            $rsmovilUS = $db->fetch_array($qrymovilUS);
            $rsDPFpe = $db->fetch_array($qrydpfPE);
            $rsDPFus = $db->fetch_array($qrydpfUS);

            if($xx==0){ $mesini = $rsDPFpe["smes"]." ".$rsDPFpe["nyear"]; }
            if($xx==($final-1)){ $mesfin = $rsDPFpe["smes"]." ".$rsDPFpe["nyear"]; }
            $grafico[] = array(
              "meses" => $rsDPFpe["smes"],
              "amovilSaldoPE" => $rsmovilPE["monto"]*1,
              "amovilCantiPE" => $rsmovilPE["cantidad"]*1,
              "amovilSaldoUS" => $rsmovilUS["monto"]*1,
              "amovilCantiUS" => $rsmovilUS["cantidad"]*1,
              "dpfSaldoPE" => $rsDPFpe["monto"]*1,
              "dpfCantiPE" => $rsDPFpe["cantidad"]*1,
              "dpfSaldoUS" => $rsDPFus["monto"]*1,
              "dpfCantiUS" => $rsDPFus["cantidad"]*1
            );
          }

          //respuesta
          $rpta = array("titulo"=>"Cartera Ahorros ".$mesini." - ".$mesfin,"grafico"=>$grafico);
          echo json_encode($rpta);
          break;
        case "dashboard_Aportes":
          //verificamos nivel de usuario
          $sql = "select id_usernivel,codagenc,id_agencia from vw_usuarios where ID=".$_SESSION['usr_ID'];
          $qryUser = $db->select($sql);
          if ($db->has_rows($qryUser)) { $rsUser = $db->fetch_array($qryUser); }

          //llenar grafico de cartera - morosidad
          $whr = "";
          $grafico = array();
          switch($_SESSION['usr_usernivelID']){
            case 701: //super
            case 703: //gerencia
            case 704: $whr = "0,0"; break;//contabilidad
          }
          $qryAportes = $db->select("select * from dbo.fn_HistoAporteCartera(".$whr.")"); //aportes

          $final = $db->num_rows($qryAportes);
          for($xx = 0; $xx < $final; $xx++){
            $rsAportes = $db->fetch_array($qryAportes);

            if($xx==0){ $mesini = $rsAportes["smes"]." ".$rsAportes["nyear"]; }
            if($xx==($final-1)){ $mesfin = $rsAportes["smes"]." ".$rsAportes["nyear"]; }
            $grafico[] = array(
              "meses" => $rsAportes["smes"],
              "aportesSaldo" => $rsAportes["monto"]*1,
              "aportesCanti" => $rsAportes["cantidad"]*1
            );
          }

          //respuesta
          $rpta = array("titulo"=>"Cartera Aportes ".$mesini." - ".$mesfin,"grafico"=>$grafico);
          echo json_encode($rpta);
          break;
      }
      $db->close();
    } else{
      $resp = array("error"=>true,"mensaje"=>"ninguna variable en POST");
      echo json_encode($resp);
    }
  } else {
    $resp = array("error"=>true,"mensaje"=>"Caducó la sesion.");
    echo json_encode($resp);
  }
?>
