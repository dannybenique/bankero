<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');
      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      switch ($data->TipoQuery) {
        //****************captacion****************
        case "agencias":
          //respuesta
          $rpta = array(
            "agencias"=>getComboBox("select id,nombre from dbo.tb_agencias order by id"),
            "agenciaID" => $_SESSION['usr_agenciaID']);
          echo json_encode($rpta);
          break;
        case "bancos_monedas":
          //respuesta
          $rpta = array(
            "bancos" => getComboBox("select id,nombre from dbo.tb_bancos order by id"),
            "monedas" => getComboBox("select id,detalle as nombre from dbo.tb_tipo_mone order by id")
          );
          echo json_encode($rpta);
          break;
        case "sel_Confirmaciones":
          //verificar usuario
          $qryusr = $db->select("select id_usernivel from dbo.tb_usuarios where id_persona=".$_SESSION['usr_ID'].";");
          $rsusr = $db->fetch_array($qryusr);

          //cargar datos de Socios
          $whr = ($rsusr["id_usernivel"]>=713)?(" and id_solicitante=".$_SESSION['usr_ID']):("");
          //if(srtlen($data->buscar)>3) { $whr .= " and (socio like'%".$data->buscar."%') " ;}
          $qryCount = $db->select("select count(*) as cuenta from dbo.vw_oper_pagosconfirma where estado=1 ".$whr.";");
          $rsCount = $db->fetch_array($qryCount);

          $tabla = array();
          $qry = $db->select("select top(20)* from dbo.vw_oper_pagosconfirma where estado=1 ".$whr." order by status_caja,fecha desc;");
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => $rs["ID"],
                "fecha"=> ($rs["fecha"]),
                "tipo_oper"=> ($rs["tipo_oper"]),
                "codsocio"=> ($rs["codsocio"]),
                "socio"=> ($rs["socio"]),
                "banco"=> ($rs["banco"]),
                "voucher" => (($rs["voucher"]!=null)?($rs["voucher"]):("")),
                "importe" => ($rs["importe"]),
                "comision" => ($rs["comision"]),
                "solicitante"=> ($rs["solicitante"]),
                "confirmaconta"=> (($rs["confirmaconta"]!=null)?($rs["confirmaconta"]):("")),
                "confirmacaja"=> (($rs["confirmacaja"]!=null)?($rs["confirmacaja"]):("")),
                "statusconta" => ($rs["status_conta"]),
                "statuscaja" => ($rs["status_caja"])
              );
            }
          }

          //respuesta
          $rpta = array("cuenta"=>$rsCount["cuenta"],"tabla"=>$tabla,"usernivel"=>$rsusr["id_usernivel"],"admin"=>701);
          echo json_encode($rpta);
          break;
        case "buscar_codsocio":
          $codigo = explode("-",$data->codsocio);
          $qry = $db->select("select case when s.dni<>'0' and s.ruc='0' and carnet='0' then s.ap_pater+' '+s.ap_mater+', '+s.nombres when s.dni='0' and s.ruc<>'0' and carnet='0' then s.raz_social when s.dni='0' and s.ruc='0' and carnet<>'0' then s.ap_pater+' '+s.ap_mater+', '+s.nombres ELSE 'ERROR, ALGO PASO' END as socio, case when s.dni<>'0' and s.ruc='0' and carnet='0' then 'DNI' when s.dni='0' and  s.ruc<>'0' and carnet='0' then 'RUC' when s.dni='0' and  s.ruc='0' and carnet<> '0' then 'CARNET' ELSE 'ERROR, ALGO PASO' END as doc, case when s.dni<>'0' and s.ruc='0' and carnet='0' then s.dni when s.dni='0' and  s.ruc<>'0' and carnet='0' then s.ruc when s.dni='0' and s.ruc='0' and carnet<> '0' then s.carnet ELSE 'ERROR' END as nrodoc from coopSUD.dbo.COOP_DB_socios_gen s where s.codagenc='".$codigo[0]."' and s.codsocio='".$codigo[1]."'");
          if ($db->has_rows($qry)) {
            $rs = $db->fetch_array($qry);
            $socio = array(
              "codsocio" => $data->codsocio,
              "socio" => $rs["socio"],
              "doc" => $rs["doc"],
              "nrodoc" => $rs["nrodoc"]
            );
          }
          //respuesta
          $rpta = $socio;
          echo json_encode($rpta);
          break;
        case "exec_ConfirmaPago_Solic":
          $error = false;
          switch($data->commandSQL) {
            case "INS": //ingresa una nueva solicitud
              $r1 = $db->fetch_array($db->select("select count(*) as cuenta from dbo.tb_oper_pagosconfirma where fecha='".$data->fecha."' and id_banco=".($data->bancoID)." and voucher='".$data->voucher."' and importe=".$data->importe));
              if($r1["cuenta"]==1){
                $error = true;
                $rpta = array("error" => $error,"mensaje" => "¡¡¡Ya existe un registro con estos datos!!! ");
              }
              break;
            case "UPD": //actualiza la solicitud
              $r2 = $db->fetch_array($db->select("select count(*) as cuenta from dbo.tb_oper_pagosconfirma where status_conta=1 and ID=".$data->confirmaID));
              if($r2["cuenta"]==1){
                $error = true;
                $rpta = array("error" => $error,"mensaje" => "¡¡¡No se pudo modificar el registro!!! porque fue bloqueado por contabilidad");
              }
              break;
          }

          if(!$error){
            $sql = "exec dbo.sp_oper_pagosconfirma '".$data->commandSQL."',".
              ($data->confirmaID).",'".
              ($data->codsocio)."','".
              ($data->socio)."','".
              ($data->fecha)."',".
              ($data->tipo_oper).",".
              ($_SESSION['usr_ID']).",".
              ($data->bancoID).",".
              ($data->monedaID).",'','".
              ($data->voucher)."',".
              ($data->importe).",0";

            $qry = $db->update($sql, array());
            $rs = $db->fetch_array($qry);

            //respuesta
            $rpta = array("error" => false,"ejecutados" => 1);
          }

          echo json_encode($rpta);
          break;
        case "edit_ConfirmaPago_Solic":
          $qry = $db->select("select * from dbo.tb_oper_pagosconfirma where ID=".$data->confirmaID);
          if ($db->has_rows($qry)) { $rs = $db->fetch_array($qry); }
          $confirma = array(
            "ID" => ($rs["ID"]),
            "codsocio" => ($rs["codsocio"]),
            "socio" => ($rs["socio"]),
            "fecha" => ($rs["fecha"]),
            "tipo_oper" => ($rs["id_tipo_oper"]),
            "soliciID" => ($rs["id_solicitante"]),
            "status_conta" => ($rs["status_conta"]),
            "bancoID" => ($rs["id_banco"]),
            "monedaID" => ($rs["id_tipo_mone"]),
            "voucher" => ($rs["voucher"]),
            "importe" => ($rs["importe"])
          );

          //respuesta
          $rpta = array(
            "confirma" => $confirma,
            "bancos"   => getComboBox("select id,nombre from dbo.tb_bancos order by id"),
            "monedas"  => getComboBox("select id,detalle as nombre from dbo.tb_tipo_mone order by id"));
          echo json_encode($rpta);
          break;
        case "get_Confirmacion":
          //verificar usuario
          $qryusr = $db->select("select id_usernivel from dbo.tb_usuarios where id_persona=".$_SESSION['usr_ID'].";");
          $rsusr = $db->fetch_array($qryusr);

          //verificar data
          $qry = $db->select("select * from dbo.vw_oper_pagosconfirma where ID=".$data->confirmaID);
          if ($db->has_rows($qry)) { $rs = $db->fetch_array($qry); }
          $confirma = array(
            "ID" => ($rs["ID"]),
            "codsocio" => ($rs["codsocio"]),
            "socio" => ($rs["socio"]),
            "fecha" => ($rs["fecha"]),
            "tipo_oper" => ($rs["tipo_oper"]),
            "banco" => ($rs["banco"]),
            "sede" => ($rs["sede"]),
            "voucher" => ($rs["voucher"]),
            "moneda" => ($rs["detalle"]),
            "importe" => ($rs["importe"]),
            "comision" => ($rs["comision"]),
            "status_conta" => ($rs["status_conta"]),
            "status_caja" => ($rs["status_caja"]),
          );

          //respuesta
          $rpta = $confirma;
          $rpta = array("confirma"=>$confirma,"usernivel"=>$rsusr["id_usernivel"],"admin"=>701,"conta"=>704,"caja"=>712);
          echo json_encode($rpta);
          break;
        case "exec_ConfirmaPago_Conta":
          $rpta = array();
          $params = array();
          $sql = "exec dbo.sp_oper_pagosconfirma 'CNT',".
            ($data->confirmaID).",'','','',0,".
            ($_SESSION['usr_ID']).",0,0,'".
            ($data->sede)."','',0,".
            ($data->comision);

          $qry = $db->update($sql, $params);
          $rs = $db->fetch_array($qry);
          $rpta = array("error" => false,"ejecutados" => 1);
          echo json_encode($rpta);
          break;
        case "exec_ConfirmaPago_CAJA":
          $rpta = array();
          $params = array();
          $sql = "exec dbo.sp_oper_pagosconfirma 'CAJ',".
            ($data->confirmaID).",'','','',0,".
            ($_SESSION['usr_ID']).",0,0,'','',0,0";

          $qry = $db->update($sql, $params);
          $rs = $db->fetch_array($qry);
          $rpta = array("error" => false,"ejecutados" => 1);
          echo json_encode($rpta);
          break;
      }
      $db->close();
    } else{
      $resp = array("error" => true,"mensaje" => "ninguna variable en POST");
      echo json_encode($resp);
    }
  } else {
    $resp = array("error" => true,"mensaje" => "Caducó la sesion.");
    echo json_encode($resp);
  }
?>
