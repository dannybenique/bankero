<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      switch ($data->TipoQuery) {
        //****************simulacion****************
        case "simulaCredito":
          $tabla = array();

          //averiguamos la TEA
          $rs = $db->fetch_array($db->select("select dbo.fn_GetCreditosTCEA(".$data->tasaMensual.",".$data->segDesgr.") as TCEA,dbo.fn_GetCreditosTEA(".$data->tasaMensual.") as TEA, dbo.fn_GetCreditosTED(".$data->tasaMensual.") as TED"));
          $TCEA = $rs["TCEA"];
          $TEA = $rs["TEA"];
          $TED = $rs["TED"];

          //obtenemos la simulacion
          $sql = "select * from dbo.fn_GenerarPlanPagos_TEM(".$data->segDesgr.",'".$data->fechaIni."','".$data->fechaPri."',".$data->importe.",".$data->tasaMensual.",30,".$data->nroCuotas.",0) order by nro";
          //$sql = "select * from dbo.fn_GenerarPlanPagos(".$data->segDesgr.",'".$data->fechaIni."',".$data->importe.",".$data->tasaMensual.",".$data->nroCuotas.") order by nro";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "dias" => $rs["dias"],
                "nro" => $rs["nro"],
                "fecha"=> $rs["fecha"],
                "total"=> $rs["total"],
                "aporte" => $rs["aport"],
                "capital" => $rs["capital"],
                "interes" => $rs["interes"],
                "desgrav" => $rs["desgrav"],
                "saldo" => $rs["saldo"]
              );
            }
          }
          $rpta = array("TCEA"=>$TCEA,"TED"=>$TED,"TEA"=>$TEA,"tabla"=>$tabla);
          echo json_encode($rpta);
          break;
        case "simulaCreditoCambio_Fecha":
          $tabla = array();

          //obtenemos la simulacion
          $sql = "select * from dbo.fn_GenerarPlanPagos_TEM(".$data->segDesgr.",'".$data->fechaIni."','".$data->fechaPri."',".$data->importe.",".$data->tasaMensual.",30,".$data->nroCuotas.",".$data->cuota.") order by nro";
          //$sql = "select * from dbo.fn_GenerarPlanPagos(".$data->segDesgr.",'".$data->fechaIni."',".$data->importe.",".$data->tasaMensual.",".$data->nroCuotas.") order by nro";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "dias" => $rs["dias"],
                "nro" => $rs["nro"],
                "fecha"=> $rs["fecha"],
                "total"=> $rs["total"],
                "aporte" => $rs["aport"],
                "capital" => $rs["capital"],
                "interes" => $rs["interes"],
                "desgrav" => $rs["desgrav"],
                "saldo" => $rs["saldo"]
              );
            }
          }
          $rpta = $tabla;
          echo json_encode($rpta);
          break;
        case "simulaCreditoCambio_Cuota":
          $tabla = array();

          //obtenemos la simulacion
          $sql = "select * from dbo.fn_GenerarPlanPagos_TEM(".$data->segDesgr.",'".$data->fechaIni."','".$data->fechaPri."',".$data->importe.",".$data->tasaMensual.",30,".$data->nroCuotas.",".$data->cuota.") order by nro";
          //$sql = "select * from dbo.fn_GenerarPlanPagos(".$data->segDesgr.",'".$data->fechaIni."',".$data->importe.",".$data->tasaMensual.",".$data->nroCuotas.") order by nro";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "dias" => $rs["dias"],
                "nro" => $rs["nro"],
                "fecha"=> $rs["fecha"],
                "total"=> $rs["total"],
                "aporte" => $rs["aport"],
                "capital" => $rs["capital"],
                "interes" => $rs["interes"],
                "desgrav" => $rs["desgrav"],
                "saldo" => $rs["saldo"]
              );
            }
          }
          $rpta = $tabla;
          echo json_encode($rpta);
          break;
        case "simulaAhorro":
          $sql = "select dbo.fn_GetAhorrosTotalInteresImporte('".$data->fechaIni."','".$data->fechaFin."',".$data->importe.",".$data->tasa.") as interes";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
              $rs = $db->fetch_array($qry);
              $tabla = array(
                "interes" => $rs["interes"]
              );
          }
          echo json_encode($tabla);
          break;
        case "comboAhorros":
          $tabla = array();

          $qry = $db->select(utf8_decode("select * from dbo.tb_productos where id_padre=2 and id_tipo_prod=201 order by nombre;"));
          if ($db->has_rows($qry)) {
            for($xx=0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => $rs["ID"],
                "nombre"=> utf8_encode($rs["nombre"])
              );
            }
          }

          //respuesta
          $rpta = $tabla;
          echo json_encode($rpta);
          break;
        case "comboCreditos":
          $combo = array();

          $qry = $db->select("select * from dbo.tb_productos where id_padre=4 and id_tipo_prod=401 order by nombre;");
          if ($db->has_rows($qry)) {
            for($xx=0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $combo[] = array(
                "ID" => $rs["ID"],
                "nombre"=> utf8_encode($rs["nombre"])
              );
            }
          }

          //respuesta
          echo json_encode($combo);
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
