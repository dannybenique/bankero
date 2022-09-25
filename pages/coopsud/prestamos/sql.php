<?php
  include_once('../../../includes/sess_verifica.php');

    if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      function getCabePrestamo($codsocio,$tiposerv,$numpres){
        $db = $GLOBALS["db"];
        $socio = 0;
        $codigo = explode("-",$codsocio);
        //datos socio
        $sql = "select coopSUD.dbo.fc_socio('".$codsocio."','S') as socio,coopSUD.dbo.fc_socio('".$codsocio."','D') as doc,coopSUD.dbo.fc_socio('".$codsocio."','N') as nrodoc,s.direccion,m.agencia,x.detalle as servicio,x.interes_2,w.nombrecorto as analista,z.nombrecorto as promotor,p.* ";
        $sql.= "from coopSUD.dbo.COOP_DB_prestamos p,coopSUD.dbo.COOP_DB_socios_gen s,coopSUD.dbo.COOP_DB_tipo_serv x,coopSUD.dbo.COOP_DB_movimientos m,tb_workers w,tb_workers z ";
        $sql.= "where x.tipo_serv=p.tipo_serv and s.codagenc=p.codagenc and s.codsocio=p.codsocio and m.codagenc=p.codagenc and m.codsocio=p.codsocio and m.num_pres=p.num_pres and m.tipo_serv=p.tipo_serv and m.tipo_mov='19' and w.codigo=p.respons2 and z.codigo=p.respons and p.codagenc+'-'+p.codsocio='".$codsocio."' and p.tipo_serv='".$tiposerv."' and p.num_pres='".$numpres."'";
        $qry = $db->select($sql);
        if($db->has_rows($qry)) {
          $rs = $db->fetch_array($qry);
          $socio = array(
            "codsocio" => ($codsocio),
            "agencia" => ($rs["agencia"]),
            "socio" => ($rs["socio"]),
            "doc" => ($rs["doc"]),
            "nrodoc" => ($rs["nrodoc"]),
            "direccion" => ($rs["direccion"]),
            "prestamo" => ($rs["num_pres"]),
            "tipo_serv" => ($rs["tipo_serv"]),
            "factor_mora" => ($rs["interes_2"]*1),
            "servicio" => ($rs["servicio"]),
            "analista" => ($rs["analista"]),
            "promotor" => ($rs["promotor"]),
            "fecha" => ($rs["fec_otorg"]),
            "cuotas" => ($rs["num_cuot"]),
            "importe" => ($rs["importe"]),
            "saldo" => ($rs["saldo"]),
            "estado" => ($rs["estado"]),
            "condicion" => ($rs["condicion"])
          );
        }
        return $socio;
      }
      function getDetaPrestamo($codsocio,$numpres){
        $db = $GLOBALS["db"];
        $prestamo = array();
        $sql = "select *, DATEDIFF(dd, fec_vencim, GETDATE()) AS atraso1,DATEDIFF(dd, fec_vencim, fec_pago) AS atraso2 from coopSUD.dbo.COOP_DB_prestamos_det where codagenc+'-'+codsocio='".$codsocio."' and num_pres='".$numpres."';";
        $qry = $db->select($sql);
        if ($db->has_rows($qry)) {
          for($xx = 0; $xx<$db->num_rows($qry); $xx++){
            $rs = $db->fetch_array($qry);
            $prestamo[] = array(
              "numero" => ($rs["numero"]),
              "fec_vencim" => ($rs["fec_vencim"]),
              "capital" => ($rs["amortizacion"]*1),
              "interes" => ($rs["int_comp"]*1),
              "desgr" => ($rs["seg_desgr"]*1),
              "saldo" => ($rs["saldo_pre"]*1),
              "total" => ($rs["amortizacion"]+$rs["int_comp"]+$rs["seg_desgr"]),
              "atraso1" => ($rs["atraso1"]),
              "atraso2" => (($xx>0)?($rs["atraso2"]):("")),
              "pag_capital" => ($rs["pago_amor"]*1),
              "pag_interes" => ($rs["pago_int_c"]*1),
              "pag_moratorio" => ($rs["pago_int_m"]*1),
              "fec_pago" => ($rs["fec_pago"]),
              "doc_pago" => trim($rs["agencia"].".".$rs["ventanilla"].".".$rs["num_trans"])
            );
          }
        }
        return $prestamo;
      }
      function getServicio($codsocio,$tipo_oper){
        $db = $GLOBALS["db"];
        $servicio = 0;
        $codigo = explode("-",$codsocio);
        $sql = "select * from coopSUD.dbo.COOP_DB_saldos where codagenc='".$codigo[0]."' and codsocio='".$codigo[1]."' and tipo_oper='".$tipo_oper."';";
        $qry = $db->select($sql);
        if ($db->has_rows($qry)) { $rs = $db->fetch_array($qry); $servicio = $rs["saldo"]; }
        return $servicio;
      }
      function getSaldos($codsocio){
        $db = $GLOBALS["db"];
        $saldos = array();
        $codigo = explode("-",$codsocio);
        $sql = "select t.detalle,s.* from coopSUD.dbo.COOP_DB_saldos s,coopSUD.dbo.COOP_DB_tipo_serv t where s.tipo_serv=t.tipo_serv and s.codagenc='".$codigo[0]."' and s.codsocio='".$codigo[1]."' order by tipo_oper,detalle;";
        $qry = $db->select($sql);
        if ($db->has_rows($qry)) {
          for($xx=0; $xx<$db->num_rows($qry); $xx++){
            $rs = $db->fetch_array($qry);
            $saldos[] = array(
              "tipo_serv" => $rs["tipo_serv"],
              "detalle" => $rs["detalle"],
              "activo" => $rs["activo"],
              "saldo" => $rs["saldo"]*1
            );
          }
        }
        return $saldos;
      }

      include_once('../../../includes/db_database.php');
      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      switch ($data->TipoQuery) {
        //****************congelados****************
        case "ComboBoxAgencias":
          //verificar usuario
          $rsusr = $db->fetch_array($db->select(utf8_decode("select id_usernivel from financiero.dbo.tb_usuarios where id_persona=".$_SESSION['usr_ID'].";")));

          //verificar tipo de combo
          $combobox = array();
          $qry = $db->select("select * from financiero.dbo.tb_agencias where estado=1 order by ID;");
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $combobox[] = array(
                "ID" => $rs["ID"],
                "nombre" => utf8_encode($rs["nombre"])
              );
            }
          }
          echo json_encode(array("combo"=>$combobox,"agenciaID"=>$_SESSION['usr_agenciaID'],"usernivel"=>$rsusr["id_usernivel"],"admin"=>701));
          break;
        case "coopSUDcartera":
          $tabla = array();
          $busca = ($data->tipo=="1")?(" and c.codsocio='".$data->buscar."'"):(" and c.dni='".$data->buscar."'");
          $whr = (($data->agenciaID)>0)?(" and saldo>0 and id_agencia=".($data->agenciaID)):($busca);
          $sql = "select c.agencia,c.codsocio,coopSUD.dbo.fc_socio(c.codsocio,'N') as nrodoc,coopSUD.dbo.fc_socio(c.codsocio,'S') as socio,c.tipo_serv,c.servicio,c.num_pres,c.fec_otorg,c.num_cuot,c.importe,c.saldo,c.respons2,u.ap_pater+' '+u.ap_mater+'/'+u.nombres as usuario from xx_coopSUDcartera c, CoopSUD.dbo.COOP_DB_usuarios u where c.respons2=u.codusuario ".$whr." order by codsocio,fec_otorg desc";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);

              $tabla[] = array(
                "agencia" => $rs["agencia"],
                "codsocio" => $rs["codsocio"],
                "nrodoc" => $rs["nrodoc"],
                "socio" => $rs["socio"],
                "servicio" => ($rs["servicio"]),
                "tiposerv" => $rs["tipo_serv"],
                "numpres" => $rs["num_pres"],
                "fec_otorg" => $rs["fec_otorg"],
                "cuotas" => $rs["num_cuot"],
                "importe" => $rs["importe"],
                "saldo" => $rs["saldo"],
                "analista" => ($rs["usuario"])
              );
            }
          }
          echo json_encode($tabla);
          break;
        case "coopSUDprestamo":
          //verificamos nivel de usuario
          $qryUser = $db->select("select id_usernivel,codagenc,id_agencia from financiero.dbo.vw_usuarios where ID=".$_SESSION['usr_ID']);
          if ($db->has_rows($qryUser)) { $rsusr = $db->fetch_array($qryUser); }

          //datos cabecera prestamo
          $prestaCabe = getCabePrestamo($data->codsocio,$data->tiposerv,$data->numpres);

          //respuesta
          $rpta = array("prestaCabe"=>$prestaCabe,"usernivel"=>$rsusr["id_usernivel"],"admin"=>701);
          echo json_encode($rpta);
          break;
        case "coopSUDprestamo_det"://detalle de las cuotas del prestamo
          $prestaDeta = getDetaPrestamo($data->codsocio,$data->numpres); //datos detalle prestamo
          //respuesta
          $rpta = array("prestaDeta"=>$prestaDeta);
          echo json_encode($rpta);
          break;
        case "coopSUD_cronograma_download"://detalle de las cuotas del prestamo
          $tabla[] = array(
            array("text" => "cuenta"),
            array("text" => "COD_FINANSOFT"),
            array("text" => "fecha"),
            array("text" => "capital"),
            array("text" => "capital_pagado"),
            array("text" => "interes"),
            array("text" => "interes_pagado"),
            array("text" => "mora"),
            array("text" => "mora_pagada"),
            array("text" => "gastos"),
            array("text" => "gastos_pagados"),
            array("text" => "otros"),
            array("text" => "otros_pagados"),
            array("text" => "total"),
            array("text" => "total_pagado"),
            array("text" => "tipo"),
            array("text" => "estado"),
            array("text" => "fecha_pago")
          );
          $codigo = explode("-",$data->codsocio);
          $sql = "select *,convert(NVARCHAR, fec_vencim, 23) as fvencim,convert(NVARCHAR,fec_pago,23) as fpago,convert(NVARCHAR,fec_pago,20) as fpagoX from coopSUD.dbo.COOP_DB_prestamos_det where codagenc='".$codigo[0]."' and codsocio='".$codigo[1]."' and num_pres='".$data->numpres."';";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $xql = "select * from coopSUD.dbo.COOP_DB_movimientos where codagenc='".$codigo[0]."' and codsocio='".$codigo[1]."' and num_pres='".$data->numpres."' and tipo_mov='05' and fecha='".$rs["fpagoX"]."'";
              $qxx = $db->select($xql);
              if ($db->has_rows($qxx)) { $rx = $db->fetch_array($qxx); $gastos = $rx["importe"]*1; }
              else { $gastos = 0; }
              $capital = ($xx==0)?($rs["saldo_pre"]*1):($rs["amortizacion"]*1);
              $pago_cap = $rs["pago_amor"]*1;
              $tipo = ($xx==0)?("DESEMBOLSO"):("PAGADO");
              $estado = ($xx==0)?("DESEMBOLSO"):(($capital==$pago_cap)?("PAGADO"):("PENDIENTE"));
              $fpago = ($xx==0)?(""):(($capital==$pago_cap)?($rs["fpago"]):(""));
              $tabla[] = array(
                array("text" => $data->codsocio.".".$data->tiposerv.".".$data->numpres),
                array("text" => ""),
                array("text" => $rs["fvencim"]),
                array("text" => $capital),
                array("text" => $pago_cap),
                array("text" => $rs["int_comp"]*1),
                array("text" => $rs["pago_int_c"]*1),
                array("text" => $rs["pago_int_m"]*1),
                array("text" => $rs["pago_int_m"]*1),
                array("text" => $gastos),
                array("text" => $gastos),
                array("text" => $rs["seg_desgr"]*1),
                array("text" => (($pago_cap==0)?(0):($rs["seg_desgr"]*1))),
                array("text" => ($rs["amortizacion"]*1)+($rs["int_comp"]*1)+($rs["pago_int_m"]*1)+($rs["seg_desgr"]*1)+($gastos)),
                array("text" => (($pago_cap==0)?(0):($rs["pago_amor"]*1)+($rs["pago_int_c"]*1)+($rs["pago_int_m"]*1)+($rs["seg_desgr"]*1)+($gastos))),
                array("text" => $tipo),
                array("text" => $estado),
                array("text" => $fpago)
              );
            }
          }
          //respuesta
          $options = array("fileName"=>"crono_".$data->codsocio);
          $tableData[] = array("sheetName"=>"cronograma","data"=>$tabla);
          $rpta = array("options"=>$options,"tableData"=>$tableData);
          echo json_encode($rpta);
          break;
        case "coopSUDprestamo_saldos":
          $saldos = getSaldos($data->codsocio); //saldos
          //respuesta
          $rpta = array("saldos"=>$saldos);
          echo json_encode($rpta);
          break;
        case "coopSUDformatos":
          $codigo = explode("-",$data->codsocio);
          $prestaCabe = getCabePrestamo($data->codsocio,$data->tiposerv,$data->numpres); //datos cabecera prestamo

          //respuesta
          $rpta = array("socio"=>$prestaCabe);
          echo json_encode($rpta);
          break;
        case "coopSUDgarantes":
          $codigo = explode("-",$data->codsocio);
          //datos garantes
          $garantes = array();
          $sql = "select * from coopSUD.dbo.COOP_DB_garantes where codagenc='".$codigo[0]."' and codsocio='".$codigo[1]."' order by codgarante;";
          $qry = $db->select($sql);
          if($db->has_rows($qry)) {
            $rs = $db->fetch_array($qry);
            $garantes[] = array(
              "dni" => ($rs["dni"]),
              "garante" => utf8_encode($rs["ap_pater"].' '.$rs["ap_mater"].', '.$rs["nombres"]),
              "telefonos" => ($rs["tel_fijo"].' - '.$rs["tel_movil"]),
              "direccion" => utf8_encode($rs["direccion"])
            );
          }

          //respuesta
          $rpta = $garantes;
          echo json_encode($rpta);
          break;
        case "coopSUDmovimiento":
          $movim = array();
          $codigo = explode(".",$data->movID);
          $agencia = $codigo[0];
          $ventanilla = $codigo[1];
          $num_trans = $codigo[2];
          $sql = "select x.detalle,m.* from coopSUD.dbo.COOP_DB_movimientos m, coopSUD.dbo.coop_db_tipo_mov x where x.tipo_mov=m.tipo_mov and m.agencia='".$agencia."' and m.ventanilla='".$ventanilla."' and m.num_trans='".$num_trans."' order by m.item";
          $qry = $db->select($sql);
          if($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $fecha = $rs["fecha"];
              $movim[] = array(
                "item" => $rs["item"],
                "tipo_mov" => $rs["tipo_mov"],
                "detalle" => $rs["detalle"],
                "importe" => $rs["importe"]*1,
              );
            }
          }

          //respuesta
          $rpta = array("movim"=>$movim,"fecha"=>$fecha,"agencia"=>$agencia,"ventanilla"=>$ventanilla,"num_trans"=>$num_trans);
          echo json_encode($rpta);
          break;
        case "cambiarFechaUnMesMas":
          //cambiar los datos
          $codigo = explode("-",$data->codsocio);
          for($xx=0; $xx<count($data->IDs); $xx++){
            $sql = "update coopSUD.dbo.coop_db_prestamos_det set fec_vencim=dateadd(m,1,fec_vencim) where codagenc='".$codigo[0]."' and codsocio='".$codigo[1]."' and num_pres='".$data->numpres."' and numero=".$data->IDs[$xx];
            $qry = $db->update($sql,array());
          }

          //respuesta
          $rpta = array("prestamo"=>getDetaPrestamo($data->codsocio,$data->numpres),"verifi"=>true);
          echo json_encode($rpta);
          break;
        case "patearInteresFinal":
          $codigo = explode("-",$data->codsocio);
          $sql = "update coopSUD.dbo.coop_db_prestamos_det set int_comp=".$data->int_actual." where codagenc='".$codigo[0]."' and codsocio='".$codigo[1]."' and num_pres='".$data->numpres."' and numero=".$data->num_cuota;
          $qry = $db->update($sql,array());
          $sql = "update coopSUD.dbo.coop_db_prestamos_det set int_comp=int_comp+".$data->int_final." where codagenc='".$codigo[0]."' and codsocio='".$codigo[1]."' and num_pres='".$data->numpres."' and numero=".$data->fin_cuota;
          $qry = $db->update($sql,array());

          //respuesta
          $rpta = array("prestamo"=>getDetaPrestamo($data->codsocio,$data->numpres));
          echo json_encode($rpta);
          break;
        case "redistribuirInteres": //redistribuye el interes entre todas las cuotas restantes
          $codigo = explode("-",$data->codsocio);

          //cuota con interes alto
          $sql = "update coopSUD.dbo.coop_db_prestamos_det set int_comp=".(($data->int_actual)+($data->redis_interes))." where codagenc='".($codigo[0])."' and codsocio='".($codigo[1])."' and num_pres='".($data->numpres)."' and numero=".($data->num_cuota);
          $qry = $db->update($sql,array());
          //resto de cuotas
          for($xx=(($data->num_cuota)+1); $xx<=($data->fin_cuota); $xx++){
            $sql = "update coopSUD.dbo.coop_db_prestamos_det set int_comp=int_comp+".($data->redis_interes)." where codagenc='".($codigo[0])."' and codsocio='".($codigo[1])."' and num_pres='".($data->numpres)."' and numero=".$xx;
            $qry = $db->update($sql,array());
          }

          //respuesta
          $rpta = array("prestamo"=>getDetaPrestamo($data->codsocio,$data->numpres));
          echo json_encode($rpta);
          break;
        case "coopSUD_Update_Prestamo":
          //cambiar los datos
          $codigo = explode("-",$data->codsocio);
          $sql = "update coopSUD.dbo.coop_db_prestamos set condicion='".$data->condicion."',estado='".$data->estado."' where codagenc='".$codigo[0]."' and codsocio='".$codigo[1]."' and tipo_serv='".$data->tiposerv."' and num_pres='".$data->numpres."'";
          $qry = $db->update($sql,array());

          //respuesta
          $rpta = array("prestaCabe"=>getCabePrestamo($data->codsocio,$data->tiposerv,$data->numpres));
          echo json_encode($rpta);
          break;
        case "updateSoftia":
          //cambiar los datos
          $codigo = explode("-",$data->codsocio);
          //$sql = "update d set d.amortizacion=x.capital,d.int_comp=x.interes,d.seg_desgr=x.desgr from coop_db_prestamos_det d inner join danny_prestamos_det x on d.codagenc=x.codagenc and d.codsocio=x.codsocio and d.num_pres=x.num_pres and d.numero=x.numero where x.codagenc='".$codigo[0]."' and x.codsocio='".$codigo[1]."' and x.num_pres='".$data->numpres."'";
          //$qry = $db->update($sql,array());
          $qry = $db->select("select *,convert(varchar, fecha, 20) as fecha1 from coopsud.dbo.danny_prestamos_det where codagenc='".$codigo[0]."' and codsocio='".$codigo[1]."' and num_pres='".$data->numpres."' order by numero;");
          if ($db->has_rows($qry)) {
            $saldo = $data->importe;
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $saldo = $saldo - $rs["capital"];
              //$xxx = $db->update("update coopsud.dbo.coop_db_prestamos_det set amortizacion=".$rs["capital"].",int_comp=".$rs["interes"].",seg_desgr=".$rs["desgr"].",saldo_pre=".$saldo." where codagenc='".$codigo[0]."' and codsocio='".$codigo[1]."' and num_pres='".$data->numpres."' and numero=".$rs["numero"],array());
              $xxx = $db->update("update coopsud.dbo.coop_db_prestamos_det set amortizacion=".$rs["capital"].",int_comp=".$rs["interes"].",fec_vencim='".$rs["fecha1"]."',seg_desgr=".$rs["desgr"].",saldo_pre=".$saldo." where codagenc='".$codigo[0]."' and codsocio='".$codigo[1]."' and num_pres='".$data->numpres."' and numero=".$rs["numero"],array());
            }
          }

          //respuesta
          $rpta = array("prestaDeta"=>getdetaPrestamo($data->codsocio,$data->numpres));
          echo json_encode($rpta);
          break;
        /*case "cambiarVencimiento":
          //cambiar los datos
          $codigo = explode("-",$data->codsocio);
          for($xx=0; $xx<count($data->IDs); $xx++){
            $sql = "update coopSUD.dbo.coop_db_prestamos_det set fec_vencim='".$data->fecha."' where codagenc='".$codigo[0]."' and codsocio='".$codigo[1]."' and num_pres='".$data->numpres."' and numero=".$data->IDs[$xx];
            $qry = $db->update($sql,array());
          }
          $sql = "insert into coopSUD_prest_cong (codsocio,tipo_serv,num_pres) values('".$data->codsocio."','".$data->tipo_serv."','".$data->numpres."');";
          $qry = $db->insert($sql,array());

          //respuesta
          $rpta = array("prestamo"=>getDetaPrestamo($data->codsocio,$data->numpres),"verifi"=>true);
          echo json_encode($rpta);
          break;*/
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
