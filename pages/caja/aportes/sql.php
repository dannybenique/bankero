<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/web_config.php');
      include_once('../../../includes/funciones.php');

      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      function pago_Item($item,$importe){
        $valor = (($item>0)
                  ?(($importe>=$item)?($item):($importe))
                  :(0));
        return round($valor,2);
      }
      function reg_movim_det($tipo_movID,$prestamoID,$movimID,$productoID,$importe){
        $db = $GLOBALS["db"];
        
        $qry = $db->query_all("select coalesce(max(id)+1,1) as code from bn_movim_det;");
        $detalleID = reset($qry)["code"];
        $qry = $db->query_all("select coalesce(max(item)+1,1) as item from bn_movim_det where id_movim=".$movimID);
        $item = reset($qry)["item"];
        $sql = "insert into bn_movim_det values(:id,:cabeceraID,:tipomovID,:item,:importe);";
        $params = [
          ":id"=>$detalleID,
          ":cabeceraID"=>$movimID,
          ":tipomovID"=>$tipo_movID,
          ":item"=>$item,
          ":importe"=>$importe
        ];
        $qry = $db->query_all($sql,$params);
        $rs = reset($qry);
      }
      //****************sql****************
      switch ($data->TipoQuery) {
        case "selAportes":
          $tabla = array();
          $buscar = strtoupper($data->buscar);
          $sql = "select * from vw_saldos where estado=1 and id_tipo_oper=121 and id_coopac=:coopacID and nro_dui LIKE :buscar";
          $params = [":coopacID"=>$web->coopacID,":buscar"=>'%'.$buscar.'%'];
          $qry = $db->query_all($sql,$params);
          if ($qry) {
            foreach($qry as $rs){
              $tabla[] = array(
                "ID" => $rs["id"],
                "nro_DUI" => str_ireplace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["nro_dui"]),
                "socio" => $rs["socio"],
                "codigo" => $rs["cod_prod"],
                "producto" => $rs["producto"],
                "mon_abrevia" => $rs["m_abrevia"],
                "saldo" => $rs["saldo"]*1
              );
            }
          }

          //respuesta
          $rpta = array("aportes"=>$tabla);
          echo json_encode($rpta);
          break;
        case "viewAporte":
          //saldos
          $qry = $db->query_all("select * from vw_saldos where id=".$data->saldoID);
          if($qry) {
            $rs = reset($qry);
            $aporte = array(
              "ID" => $rs["id"],
              "DUI" => $rs["dui"],
              "nro_dui" => $rs["nro_dui"],
              "socio" => $rs["socio"],
              "productoID" => $rs["id_producto"],
              "socioID" => $rs["id_socio"],
              "saldo" => $rs["saldo"]
            );
          }

          //respuesta
          $rpta = array(
            "aporte" => $aporte,
            "comboTipoPago" => $fn->getComboBox("select id,nombre from sis_tipos where id_padre=13 order by id;"),
            "comboMonedas" => $fn->getComboBox("select id,nombre from sis_tipos where id_padre=1 order by id;"),
            "fecha" => $fn->getFechaActualDB());
          echo json_encode($rpta);
          break;
        case "insPago":
          $estado = 1;
          $tipo_operID = 124;
          $coopacID = $web->coopacID;
          $clientIP = $fn->getClientIP();
          $userID = $_SESSION['usr_ID'];
          $importe = $data->importe;

          //actualizamos cantidades en bn_prestamos_det
          $pg_otros = 0;
          $pg_mora = 0;
          $pg_interes = 0;
          $pg_capital = 0;
          $pg_tot_otros = 0;
          $pg_tot_mora = 0;
          $pg_tot_interes = 0;
          $pg_tot_capital = 0;
          $sql = "select id_prestamo,numero,capital-pg_capital as capital,interes-pg_interes as interes,(extract(days from now()-fecha)::float*(:tasamora*0.01/360)*(capital-pg_capital))-pg_mora as mora,otros-pg_otros as otros from bn_prestamos_det where extract(days from now()-fecha)>=0 and numero>0 and id_prestamo=:id order by numero;";
          $params = [":tasamora"=>$data->tasaMora,":id"=>$data->prestamoID];
          $qry = $db->query_all($sql,$params);
          if ($qry) {
            $temp = "";
            foreach($qry as $rs){
              if($importe>0){
                $pg_otros = pago_Item(($rs["otros"]),$importe); $importe -= $pg_otros;
                $pg_mora = pago_Item(($rs["mora"]),$importe); $importe -= $pg_mora;
                $pg_interes = pago_Item(($rs["interes"]),$importe); $importe -= $pg_interes;
                $pg_capital = pago_Item(($rs["capital"]),$importe); $importe -= $pg_capital;
                $pg_tot_otros += $pg_otros;
                $pg_tot_mora += $pg_mora;
                $pg_tot_interes += $pg_interes;
                $pg_tot_capital += $pg_capital;
                $whr_otros = ($pg_otros>0)?(",pg_otros=pg_otros+".$pg_otros):("");
                $whr_mora = ($pg_mora>0)?(",pg_mora=pg_mora+".$pg_mora):("");
                $whr_interes = ($pg_interes>0)?(",pg_interes=pg_interes+".$pg_interes):("");
                $whr_capital = ($pg_capital>0)?(",pg_capital=pg_capital+".$pg_capital):("");
                $whr_atraso = ($rs["capital"]-$pg_capital<=0)?(",atraso=extract(days from now()-fecha)"):("");
                $qrx = $db->query_all("update bn_prestamos_det set numero=".$rs["numero"].$whr_otros.$whr_mora.$whr_interes.$whr_capital.$whr_atraso." where id_prestamo=".$data->prestamoID." and numero=".$rs["numero"].";");
                $aa = reset($qrx);
              }
            }
          }
          //actualizamos saldo de bn_prestamos
          if($pg_tot_capital>0) { 
            $qry = $db->query_all("update bn_prestamos set saldo=saldo-:capital where id=:id;",[":id"=>$data->prestamoID,":capital"=>$pg_tot_capital]);
            $rs = reset($qry);
          }


          /******agregamos bn_movim********/
          /********************************/
          $qry = $db->query_all("select coalesce(max(id)+1,1) as code from bn_movim;");
          $movimID = reset($qry)["code"];
          $qry = $db->query_all("select right('0000000'||cast(coalesce(max(right(codigo,7)::integer)+1,1) as text),7) as code from bn_movim where id_cajera=".$userID);
          $codigo = $userID."-".reset($qry)["code"];
          $sql = "insert into bn_movim values(:id,:coopacID,:agenciaID,:operID,:pagoID,:monedaID,:socioID,:tabla,:productoID,:cajeraID,now(),:codigo,:importe,:estado,:sysIP,:userID,now(),:observac)";
          $params = [
            ":id"=>$movimID,
            ":coopacID"=>$coopacID,
            ":agenciaID"=>$data->agenciaID,
            ":operID"=>$tipo_operID,
            ":pagoID"=>$data->medioPagoID,
            ":monedaID"=>$data->monedaID,
            ":socioID"=>$data->socioID,
            ":tabla"=>null,
            ":productoID"=>null,
            ":cajeraID"=>$userID,
            ":codigo"=>$codigo,
            ":importe"=>$importe,
            ":estado"=>$estado,
            ":sysIP"=>$clientIP,
            ":userID"=>$userID,
            ":observac"=>''
          ];
          $qry = $db->query_all($sql,$params);
          $rs = reset($qry);
          //agregamos bn_movim_det
          if($pg_tot_otros>0) {reg_movim_det(13,$data->prestamoID,$movimID,$data->productoID,$pg_tot_otros); }
          if($pg_tot_mora>0) {reg_movim_det(12,$data->prestamoID,$movimID,$data->productoID,$pg_tot_mora); }
          if($pg_tot_interes>0) {reg_movim_det(11,$data->prestamoID,$movimID,$data->productoID,$pg_tot_interes); }
          if($pg_tot_capital>0) {reg_movim_det(10,$data->prestamoID,$movimID,$data->productoID,$pg_tot_capital); }


          /******agregamos bn_saldos*******/
          /********************************/
          if($pg_tot_capital>0) {
            $sql = "update bn_saldos set saldo=(saldo + :capital),sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id_coopac=:coopacID and id_socio=:socioID and id_tipo_oper=:operID and id_producto=:productoID;";
            $params = [
              ":coopacID"=>$coopacID,
              ":socioID"=>$data->socioID,
              ":operID"=>$tipo_operID,
              ":productoID"=>$data->productoID,
              ":capital"=>$pg_tot_capital,
              ":sysIP"=>$clientIP,
              ":userID"=>$userID
            ];
            $qry = $db->query_all($sql,$params);
            $rs = reset($qry);
          }

          $rpta = array("error" => false,"movimID"=>$movimID,"ingresados" => 1);
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
