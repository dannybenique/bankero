<?php
  include_once('../../../includes/sess_verifica.php');

    if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');
      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      function get_prestamosErrados($rec,$err){
        return array(
          "ID" => $rec["codagenc"]."-".$rec["codsocio"]."-".$rec["tipo_serv"]."-".$rec["num_pres"],
          "codsocio" => $rec["codagenc"]."-".$rec["codsocio"],
          "socio" => ($rec["socio"])." - ".$rec["documento"],
          "servicio" => $rec["tipo_serv"]." - ".($rec["detalle"])." - ".$rec["num_pres"],
          "numpres" => $rec["num_pres"],
          "fec_otorg" => $rec["fec_otorg"],
          "cuotas" => $rec["num_cuot"],
          "importe" => $rec["importe"]*1,
          "saldo" => $rec["saldo"]*1,
          "codpromotor" => $rec["respons"],
          "codanalista" => $rec["respons2"],
          "promotor" => ($rec["promotor"]),
          "analista" => ($rec["analista"]),
          "error" => ($err)
        );
      }

      switch ($data->TipoQuery) {
        case "coopsudDesembolsos":
          $tabla = array();
          $whr = " and p.fec_otorg>='".($data->fechaINI)." 00:00:00' and p.fec_otorg<='".($data->fechaFIN)." 23:59:59'";
          $sql = "select m.agencia,p.*,case when s.dni <> '0' and  s.ruc = '0' and carnet = '0' then s.ap_pater+' '+s.ap_mater+', '+s.nombres when s.dni = '0' and  s.ruc <> '0' and carnet = '0' then s.raz_social when s.dni = '0' and  s.ruc = '0' and carnet <> '0' then s.ap_pater+' '+s.ap_mater+', '+s.nombres ELSE 'ERROR' END as socio,case when s.dni<>'0' and s.ruc='0' and carnet='0' then s.dni when s.dni='0' and  s.ruc<>'0' and carnet='0' then s.ruc when s.dni='0' and  s.ruc='0' and carnet<> '0' then s.carnet ELSE 'ERROR' END as documento,u.nombrecorto as analista,pr.nombrecorto as promotor,t.detalle from coopSUD.dbo.COOP_DB_prestamos p,coopSUD.dbo.COOP_DB_socios_gen s,coopSUD.dbo.COOP_DB_movimientos m,coopSUD.dbo.COOP_DB_tipo_serv t,tb_workers u,tb_workers pr where p.codagenc=m.codagenc and p.codsocio=m.codsocio and p.tipo_serv=m.tipo_serv and p.num_pres=m.num_pres and m.tipo_mov='19' and p.codagenc=s.codagenc and p.codsocio=s.codsocio and p.respons2=u.codigo and p.respons=pr.codigo and p.tipo_serv=t.tipo_serv and p.saldo>0 ".$whr." order by codagenc,codsocio";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx=0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);

              $qqq = $db->select("select p.tipo_cred,p.destino, t.detalle as tipoSBS,d.detalle as destinoSBS from coopsud.dbo.COOP_DB_sol_prestamos p,coopsud.dbo.COOP_DB_tipo_cred t,coopsud.dbo.COOP_DB_dest_credito d where p.tipo_cred=t.tipo_cred and p.tipo_cred=d.tipo_cred and p.destino=d.destino and p.codAgencia='".$rs["codagenc"]."' and p.codSocio='".$rs["codsocio"]."' and p.tipo_serv='".$rs["tipo_serv"]."' and p.num_prest='".$rs["num_pres"]."'");
              if ($db->has_rows($qqq)) { $rr = $db->fetch_array($qqq); $tipoSBS = $rr["tipoSBS"]; $destinoSBS = $rr["destinoSBS"]; }
              else { $tipoSBS = "--"; $destinoSBS = "--"; }
              $tabla[] = array(
                "ID" => ($rs["codagenc"]."-".$rs["codsocio"]."-".$rs["tipo_serv"]."-".$rs["num_pres"]),
                "agencia" =>($rs["agencia"]),
                "codsocio" => ($rs["codagenc"]."-".$rs["codsocio"]),
                "tipo_serv" =>($rs["tipo_serv"]),
                "socio" => utf8_encode($rs["socio"])." - ".$rs["documento"],
                "servicio" => ($rs["tipo_serv"]." - ".utf8_encode($rs["detalle"])." - ".$rs["num_pres"]),
                "numpres" => ($rs["num_pres"]),
                "tipoSBS" => $tipoSBS,
                "destinoSBS" => $destinoSBS,
                "fec_otorg" => ($rs["fec_otorg"]),
                "cuotas" => ($rs["num_cuot"]),
                "importe" => ($rs["importe"]*1),
                "saldo" => ($rs["saldo"]*1),
                "codpromotor" => ($rs["respons"]),
                "codanalista" => ($rs["respons2"]),
                "promotor" => utf8_encode($rs["promotor"]),
                "analista" => utf8_encode($rs["analista"])
              );
            }
          }
          echo json_encode($tabla);
          break;
        case "coopsudDesembolsosDownload":
          $tabla[] = array(
            array("text" => "ID"),
            array("text" => "agencia"),
            array("text" => "codsocio"),
            array("text" => "socio"),
            array("text" => "servicio"),
            array("text" => "tipoSBS"),
            array("text" => "destinoSBS"),
            array("text" => "fecha"),
            array("text" => "importe"),
            array("text" => "saldo"),
            array("text" => "analista")
          );
          $whr = " and p.fec_otorg>='".($data->fechaINI)." 00:00:00' and p.fec_otorg<='".($data->fechaFIN)." 23:59:59'";
          $sql = "select m.agencia,REPLACE(CONVERT(NVARCHAR, p.fec_otorg, 103), ' ', '/') as fecha,p.*,case when s.dni <> '0' and  s.ruc = '0' and carnet = '0' then s.ap_pater+' '+s.ap_mater+', '+s.nombres when s.dni = '0' and  s.ruc <> '0' and carnet = '0' then s.raz_social when s.dni = '0' and  s.ruc = '0' and carnet <> '0' then s.ap_pater+' '+s.ap_mater+', '+s.nombres ELSE 'ERROR' END as socio,case when s.dni<>'0' and s.ruc='0' and carnet='0' then s.dni when s.dni='0' and  s.ruc<>'0' and carnet='0' then s.ruc when s.dni='0' and  s.ruc='0' and carnet<> '0' then s.carnet ELSE 'ERROR' END as documento,u.nombrecorto as analista,pr.nombrecorto as promotor,t.detalle from coopSUD.dbo.COOP_DB_prestamos p,coopSUD.dbo.COOP_DB_socios_gen s,coopSUD.dbo.COOP_DB_movimientos m,coopSUD.dbo.COOP_DB_tipo_serv t,tb_workers u,tb_workers pr where p.codagenc=m.codagenc and p.codsocio=m.codsocio and p.tipo_serv=m.tipo_serv and p.num_pres=m.num_pres and m.tipo_mov='19' and p.codagenc=s.codagenc and p.codsocio=s.codsocio and p.respons2=u.codigo and p.respons=pr.codigo and p.tipo_serv=t.tipo_serv and p.saldo>0 ".$whr." order by codagenc,codsocio";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx=0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);

              $qqq = $db->select("select p.tipo_cred,p.destino, t.detalle as tipoSBS,d.detalle as destinoSBS from coopsud.dbo.COOP_DB_sol_prestamos p,coopsud.dbo.COOP_DB_tipo_cred t,coopsud.dbo.COOP_DB_dest_credito d where p.tipo_cred=t.tipo_cred and p.tipo_cred=d.tipo_cred and p.destino=d.destino and p.codAgencia='".$rs["codagenc"]."' and p.codSocio='".$rs["codsocio"]."' and p.tipo_serv='".$rs["tipo_serv"]."' and p.num_prest='".$rs["num_pres"]."'");
              if ($db->has_rows($qqq)) { $rr = $db->fetch_array($qqq); $tipoSBS = $rr["tipoSBS"]; $destinoSBS = $rr["destinoSBS"]; }
              else { $tipoSBS = "--"; $destinoSBS = "--"; }
              $tabla[] = array(
                array("text" => ($rs["codagenc"]."-".$rs["codsocio"].".".$rs["tipo_serv"].".".$rs["num_pres"])),
                array("text" => ($rs["agencia"])), //agencia
                array("text" => ($rs["codagenc"]."-".$rs["codsocio"])), //codsocio
                array("text" => ($rs["socio"])." - ".$rs["documento"]), //socio
                array("text" => ($rs["tipo_serv"]." - ".($rs["detalle"])." - ".$rs["num_pres"])), //servicio
                array("text" => $tipoSBS), //tipo
                array("text" => $destinoSBS), //destino
                array("text" => ($rs["fecha"])), //fecha
                array("text" => ($rs["importe"]*1)), //importe
                array("text" => ($rs["saldo"]*1)), //saldo
                array("text" => utf8_encode($rs["analista"])) //analista
              );
            }
          }

          //respuesta
          $options = array("fileName"=>"desembolsados");
          $tableData[] = array("sheetName"=>"desembolsados","data"=>$tabla);
          $rpta = array("options"=>$options,"tableData"=>$tableData);
          echo json_encode($rpta);
          break;
        case "coopSUDcartera":
          $tabla = array();
          //$busca = ($data->tipo=="1")?(" and codsocio='".$data->buscar."'"):(" and dni='".$data->buscar."'");
          $whr = ($data->saldo==1)?(" and saldo>0 and p.respons2='".($data->filtroID)."' "):(" and p.respons2='".($data->filtroID)."' ");
          $sql = "select m.agencia,p.*,case when s.dni <> '0' and  s.ruc = '0' and carnet = '0' then s.ap_pater+' '+s.ap_mater+', '+s.nombres when s.dni = '0' and  s.ruc <> '0' and carnet = '0' then s.raz_social when s.dni = '0' and  s.ruc = '0' and carnet <> '0' then s.ap_pater+' '+s.ap_mater+', '+s.nombres ELSE 'ERROR' END as socio,case when s.dni<>'0' and s.ruc='0' and carnet='0' then s.dni when s.dni='0' and  s.ruc<>'0' and carnet='0' then s.ruc when s.dni='0' and  s.ruc='0' and carnet<> '0' then s.carnet ELSE 'ERROR' END as documento,u.nombrecorto as analista,pr.nombrecorto as promotor,t.detalle from coopSUD.dbo.COOP_DB_prestamos p,coopSUD.dbo.COOP_DB_socios_gen s,coopSUD.dbo.COOP_DB_movimientos m,coopSUD.dbo.COOP_DB_tipo_serv t,tb_workers u,tb_workers pr where p.codagenc=m.codagenc and p.codsocio=m.codsocio and p.tipo_serv=m.tipo_serv and p.num_pres=m.num_pres and m.tipo_mov='19' and p.codagenc=s.codagenc and p.codsocio=s.codsocio and p.respons2=u.codigo and p.respons=pr.codigo and p.tipo_serv=t.tipo_serv ".$whr." order by agencia,codagenc,codsocio";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx=0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);

              $tabla[] = array(
                "ID" => ($rs["codagenc"]."-".$rs["codsocio"]."-".$rs["tipo_serv"]."-".$rs["num_pres"]),
                "agencia" =>($rs["agencia"]),
                "codsocio" => ($rs["codagenc"]."-".$rs["codsocio"]),
                "tipo_serv" =>($rs["tipo_serv"]),
                "socio" => ($rs["socio"])." - ".$rs["documento"],
                "servicio" => ($rs["tipo_serv"]." - ".($rs["detalle"])." - ".$rs["num_pres"]),
                "numpres" => ($rs["num_pres"]),
                "fec_otorg" => ($rs["fec_otorg"]),
                "cuotas" => ($rs["num_cuot"]),
                "importe" => ($rs["importe"]*1),
                "saldo" => ($rs["saldo"]*1),
                "codpromotor" => ($rs["respons"]),
                "codanalista" => ($rs["respons2"]),
                "promotor" => ($rs["promotor"]),
                "analista" => ($rs["analista"])
              );
            }
          }
          echo json_encode($tabla);
          break;
        case "coopSUDprestamo":
          $socio = 0;
          $prestamo = array();
          $codigo = explode("-",$data->codcuenta);

          //solicitud de prestamo
          $num_sol = "";
          $sql_sol = "select * from coopSUD.dbo.coop_db_sol_prestamos where codagencia='".$codigo[0]."' and codsocio='".$codigo[1]."' and num_prest='".$codigo[3]."'";
          $qry_solicitud = $db->select($sql_sol);
          if($db->has_rows($qry_solicitud)) { $rs = $db->fetch_array($qry_solicitud); $num_sol = $rs["numSolicitud"]; }

          //datos prestamo socio
          $sql = "select case when s.dni <> '0' and  s.ruc = '0' and carnet = '0' then s.ap_pater+' '+s.ap_mater+', '+s.nombres when s.dni = '0' and  s.ruc <> '0' and carnet = '0' then s.raz_social when s.dni = '0' and  s.ruc = '0' and carnet <> '0' then s.ap_pater+' '+s.ap_mater+', '+s.nombres END as socio,case when s.dni<>'0' and s.ruc='0' and carnet='0' then s.dni when s.dni='0' and  s.ruc<>'0' and carnet='0' then s.ruc when s.dni='0' and  s.ruc='0' and carnet<> '0' then s.carnet END as doc,x.detalle as servicio,x.interes_2,w.nombrecorto as analista,z.nombrecorto as promotor,p.codagenc+'-'+p.codsocio as codigo,p.* ";
          $sql .= "from coopSUD.dbo.COOP_DB_prestamos p,coopSUD.dbo.COOP_DB_socios_gen s,coopSUD.dbo.COOP_DB_tipo_serv x,tb_workers w,tb_workers z ";
          $sql .= "where x.tipo_serv=p.tipo_serv and s.codagenc=p.codagenc and s.codsocio=p.codsocio and w.codigo=p.respons2 and z.codigo=p.respons and p.codagenc='".$codigo[0]."' and p.codsocio='".$codigo[1]."' and p.tipo_serv='".$codigo[2]."' and p.num_pres='".$codigo[3]."'";
          $qry = $db->select($sql);
          if($db->has_rows($qry)) {
            $rs = $db->fetch_array($qry);
            $socio = array(
              "codsocio" => ($rs["codigo"]),
              "txtsocio" => utf8_encode($rs["socio"]),
              "dni" => ($rs["doc"]),
              "num_sol" => ($num_sol),
              "num_pres" => ($rs["num_pres"]),
              "tipo_serv" => ($rs["tipo_serv"]),
              "factor_mora" => ($rs["interes_2"]*1),
              "servicio" => utf8_encode($rs["servicio"]),
              "analista" => utf8_encode($rs["analista"]),
              "promotor" => utf8_encode($rs["promotor"]),
              "tipo_cred" => ($rs["tipo_cred"]), //codigo del credito
              "respons" => ($rs["respons"]), //codigo del promotor
              "respons2" => ($rs["respons2"]), //codigo del analista
              "fecha" => ($rs["fec_otorg"]),
              "cuotas" => ($rs["num_cuot"]),
              "importe" => ($rs["importe"]),
              "saldo" => ($rs["saldo"]),
            );
          }

          $usuarios = getComboBox("select codusuario as id,ap_pater+' '+ap_mater+', '+nombres as nombre from coopSUD.dbo.COOP_DB_usuarios order by nombre");
          $tipo_cred = getCombobox("select tipo_cred as id,detalle as nombre from coopSUD.dbo.COOP_DB_tipo_cred order by nombre;");

          //respuesta
          $rpta = array("socio"=>$socio,"usuarios"=>$usuarios,"tipo_cred"=>$tipo_cred);
          echo json_encode($rpta);
          break;
        case "coopSUDarreglarSolic":
          $codigo = explode("-",$data->codsocio);
          $rr = $db->fetch_array($db->select("select count(*) as cuenta from coopSUD.dbo.COOP_DB_sol_prestamos where codAgencia='".$codigo[0]."' and codSocio='".$codigo[1]."' and tipo_serv='".$data->tiposerv."' and importe=".$data->importe));
          if($rr["cuenta"]==1) {
            $qryrr = $db->update("update coopSUD.dbo.coop_db_sol_prestamos set num_prest='".$data->numpres."' where codAgencia='".$codigo[0]."' and codSocio='".$codigo[1]."' and tipo_serv='".$data->tiposerv."' and importe=".$data->importe,array());
            $error = 0; //sin errores
          } else {
            $error = 1; //error: hay mas de un registro
          }

          $rpta = array("error" => $error,"cuenta"=>$rr["cuenta"]);
          echo json_encode($rpta);
          break;
        case "Analistas_Activos":
          $sql = "select distinct p.respons2,w.nombrecorto as nombre,u.estado from coopSUD.dbo.coop_db_prestamos p,coopSUD.dbo.coop_db_usuarios u,tb_workers w where p.respons2=w.codigo and p.respons2=u.codusuario and w.codigo=u.codusuario and saldo>0 order by estado,nombre";
          $combobox = array();
          $qry = $db->select($sql);
          if ($db->num_rows($qry)>0) {
            for($xx=0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $combobox[] = array(
                "ID" => $rs["respons2"],
                "nombre" => (($rs["estado"]=="I")?("== ".$rs["nombre"]):($rs["nombre"]))
              );
            }
          }
          $rpta = array("combobox"=>$combobox,"minivel"=>$_SESSION['usr_usernivelID'],"admin"=>701);
          echo json_encode($rpta);
          break;
        case "Analistas_Todos":
          $sql = "select w.codigo, w.nombrecorto,u.estado from coopSUD.dbo.coop_db_usuarios u,tb_workers w where w.codigo=u.codusuario and w.estado=1 order by nombrecorto";
          $combobox = array();
          $qry = $db->select($sql);
          if ($db->num_rows($qry)>0) {
            for($xx=0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $combobox[] = array(
                "ID" => $rs["codigo"],
                "nombre" => ($rs["nombrecorto"])
              );
            }
          }
          echo json_encode($combobox);
          break;
        case "Analistas_Cambio":
          $tabla = array();
          for($xx = 0; $xx<count($data->arrOBJ); $xx++){
            $codigo = explode("-",$data->arrOBJ[$xx]);
            $sql = "select count(*) as cuenta from coopSUD.dbo.COOP_DB_sol_prestamos where codAgencia='".$codigo[0]."' and codSocio='".$codigo[1]."' and tipo_serv='".$codigo[2]."' and num_prest='".$codigo[3]."'";
            $rs = $db->fetch_array($db->select($sql));
            if($rs["cuenta"]!=1) {
              $tabla[] = array("codigo"=>$data->arrOBJ[$xx]);
            } else {
              $qry= $db->update("update coopSUD.dbo.coop_db_prestamos set respons0=respons2,respons2='".$data->usuarioID."' where codagenc='".$codigo[0]."' and codsocio='".$codigo[1]."' and tipo_serv='".$codigo[2]."' and num_pres='".$codigo[3]."'",array());
              $qry= $db->update("update coopSUD.dbo.coop_db_sol_prestamos set cod_cred_soc='".$data->usuarioID."' where codagenc='".$codigo[0]."' and codsocio='".$codigo[1]."' and tipo_serv='".$codigo[2]."' and num_pres='".$codigo[3]."'",array());
            }
          }
          $rpta = array("tablaError" => $tabla);
          echo json_encode($rpta);
          break;
        case "verificar_SaldoNegativo":
          //verificar prestamos con saldo negativo
          $rsn = $db->fetch_array($db->select("select count(*) as cuenta from coopSUD.dbo.COOP_DB_prestamos where saldo<0"));
          if($rsn["cuenta"]>0){
            $saldoNegativo = array();
            $sql = "select p.*,case when s.dni <> '0' and  s.ruc = '0' and carnet = '0' then s.ap_pater+' '+s.ap_mater+', '+s.nombres when s.dni = '0' and  s.ruc <> '0' and carnet = '0' then s.raz_social when s.dni = '0' and  s.ruc = '0' and carnet <> '0' then s.ap_pater+' '+s.ap_mater+', '+s.nombres ELSE 'ERROR, ALGO PASO' END as socio,case when s.dni<>'0' and s.ruc='0' and carnet='0' then s.dni when s.dni='0' and  s.ruc<>'0' and carnet='0' then s.ruc when s.dni='0' and  s.ruc='0' and carnet<> '0' then s.carnet ELSE 'ERROR, ALGO PASO' END as documento,u.nombrecorto as analista,pr.nombrecorto as promotor,t.detalle from coopSUD.dbo.COOP_DB_prestamos p,coopSUD.dbo.COOP_DB_socios_gen s,coopSUD.dbo.COOP_DB_tipo_serv t,tb_workers u,tb_workers pr where p.codagenc=s.codagenc and p.codsocio=s.codsocio and p.respons2=u.codigo and p.respons=pr.codigo and p.tipo_serv=t.tipo_serv and p.saldo<0 ".$whr." order by codagenc,codsocio";
            $qry = $db->select($sql);
            if ($db->has_rows($qry)) {
              for($xx=0; $xx<$db->num_rows($qry); $xx++){
                $rs = $db->fetch_array($qry);
                $saldoNegativo[] = array(
                  "ID" => $rs["codagenc"]."-".$rs["codsocio"]."-".$rs["tipo_serv"]."-".$rs["num_pres"],
                  "codsocio" => $rs["codagenc"]."-".$rs["codsocio"],
                  "socio" => utf8_encode($rs["socio"])." - ".$rs["dni"],
                  "servicio" => $rs["tipo_serv"]." - ".utf8_encode($rs["detalle"])." - ".$rs["num_pres"],
                  "numpres" => $rs["num_pres"],
                  "fec_otorg" => $rs["fec_otorg"],
                  "cuotas" => $rs["num_cuot"],
                  "importe" => $rs["importe"]*1,
                  "saldo" => $rs["saldo"]*1,
                  "codpromotor" => $rs["respons"],
                  "codanalista" => $rs["respons2"],
                  "promotor" => utf8_encode($rs["promotor"]),
                  "analista" => utf8_encode($rs["analista"])
                );
              }
            }
          }

          $rpta = array("saldoNegativo" => $saldoNegativo);
          echo json_encode($rpta);
          break;
        case "verificar_Prestamos":
          $tablaError = array();
          //verificar prestamos con diferente solicitud
          $sql = "select p.*,year(p.fec_otorg) as yy_otorg,month(p.fec_otorg) as mm_otorg,case when s.dni<>'0' and  s.ruc='0' and carnet='0' then s.ap_pater+' '+s.ap_mater+', '+s.nombres when s.dni='0' and  s.ruc<>'0' and carnet='0' then s.raz_social when s.dni='0' and  s.ruc='0' and carnet<>'0' then s.ap_pater+' '+s.ap_mater+', '+s.nombres ELSE 'ERROR, ALGO PASO' END as socio,case when s.dni<>'0' and s.ruc='0' and carnet='0' then s.dni when s.dni='0' and  s.ruc<>'0' and carnet='0' then s.ruc when s.dni='0' and s.ruc='0' and carnet<>'0' then s.carnet ELSE 'ERROR, ALGO PASO' END as documento,u.nombrecorto as analista,pr.nombrecorto as promotor,t.detalle from coopSUD.dbo.COOP_DB_prestamos p,coopSUD.dbo.COOP_DB_socios_gen s,coopSUD.dbo.COOP_DB_tipo_serv t,tb_workers u,tb_workers pr where p.codagenc=s.codagenc and p.codsocio=s.codsocio and p.respons2=u.codigo and p.respons=pr.codigo and p.tipo_serv=t.tipo_serv and p.saldo>0 order by codagenc,codsocio";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx=0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);

              $rr = $db->fetch_array($db->select("select count(*) as cuenta from coopSUD.dbo.COOP_DB_sol_prestamos where codAgencia='".$rs["codagenc"]."' and codSocio='".$rs["codsocio"]."' and tipo_serv='".$rs["tipo_serv"]."' and num_cuotas='".$rs["num_cuot"]."' and importe=".$rs["importe"]));
              switch($rr["cuenta"]){
                case 0: //sin solicitud de prestamos
                  $tablaError[] = get_prestamosErrados($rs,"NO tiene NINGUNA solicitud");
                  break;
                case 1://tiene una solicitud y es adecuada
                  $aa = $db->fetch_array($db->select("select count(*) as cuenta from coopSUD.dbo.COOP_DB_sol_prestamos where codAgencia='".$rs["codagenc"]."' and codSocio='".$rs["codsocio"]."' and tipo_serv='".$rs["tipo_serv"]."' and num_cuotas='".$rs["num_cuot"]."' and num_prest='".$rs["num_pres"]."' and importe=".$rs["importe"]));
                  if($aa["cuenta"]==0) { $qryrr= $db->update("update coopSUD.dbo.coop_db_sol_prestamos set num_prest='".$rs["num_pres"]."' where codAgencia='".$rs["codagenc"]."' and codSocio='".$rs["codsocio"]."' and tipo_serv='".$rs["tipo_serv"]."' and num_cuotas='".$rs["num_cuot"]."' and importe=".$rs["importe"],array()); }
                  break;
                default:
                  $aa = $db->fetch_array($db->select("select count(*) as cuenta from coopSUD.dbo.COOP_DB_sol_prestamos where codAgencia='".$rs["codagenc"]."' and codSocio='".$rs["codsocio"]."' and tipo_serv='".$rs["tipo_serv"]."' and num_cuotas='".$rs["num_cuot"]."' and num_prest='".$rs["num_pres"]."' and importe=".$rs["importe"]));
                  if($aa["cuenta"]==0) { $tablaError[] = get_prestamosErrados($rs,"Tienes varias solicitudes"); }
                  break;
              }
            }
          }
          if(count($tablaError)==0){ $sinError= $db->update("update coopSUD.dbo.COOP_DB_sol_prestamos set cod_eval_sect=p.respons,cod_cred_soc=p.respons2 FROM coopSUD.dbo.COOP_DB_sol_prestamos AS s INNER JOIN coopSUD.dbo.COOP_DB_prestamos AS p ON s.codAgencia=p.codagenc AND s.codSocio=p.codsocio AND s.tipo_serv=p.tipo_serv AND s.num_prest=p.num_pres WHERE (p.saldo>0)",array()); }
          $rpta = array("tablaError" => $tablaError);
          echo json_encode($rpta);
          break;
        case "cambiarTipoCred":
          $error = 0;
          $codigo = explode("-",$data->codsocio);
          $rs = $db->fetch_array($db->select("select count(*) as cuenta from CoopSUD.dbo.COOP_DB_prestamos p,CoopSUD.dbo.COOP_DB_sol_prestamos s where s.codAgencia=p.codagenc and s.codSocio=p.codsocio and s.tipo_serv=p.tipo_serv and s.num_prest=p.num_pres and p.codagenc='".$codigo[0]."' and p.codsocio='".$codigo[1]."' and p.tipo_serv='".$data->tipo_serv."' and p.num_pres='".$data->num_pres."'"));
          if($rs["cuenta"]==1){
            $qry= $db->update("update coopSUD.dbo.coop_db_prestamos set tipo_cred='".$data->tipo_cred."' where codagenc='".$codigo[0]."' and codsocio='".$codigo[1]."' and tipo_serv='".$data->tipo_serv."' and num_pres='".$data->num_pres."'",array());
            $qry= $db->update("update coopSUD.dbo.coop_db_sol_prestamos set tipo_cred='".$data->tipo_cred."' where codAgencia='".$codigo[0]."' and codSocio='".$codigo[1]."' and tipo_serv='".$data->tipo_serv."' and num_prest='".$data->num_pres."'",array());
            $error = 0;
          } else {
            $error = $rs["cuenta"];
          }
          $rpta = array("error" => $error);
          echo json_encode($rpta);
          break;

        case "cambiarPromotor":
          $error = 0;
          $codigo = explode("-",$data->codsocio);
          $rs = $db->fetch_array($db->select("select count(*) as cuenta from CoopSUD.dbo.COOP_DB_prestamos p,CoopSUD.dbo.COOP_DB_sol_prestamos s where s.codAgencia=p.codagenc and s.codSocio=p.codsocio and s.tipo_serv=p.tipo_serv and s.num_prest=p.num_pres and p.codagenc='".$codigo[0]."' and p.codsocio='".$codigo[1]."' and p.tipo_serv='".$data->tipo_serv."' and p.num_pres='".$data->num_pres."'"));
          if($rs["cuenta"]==1){
            $qry= $db->update("update coopSUD.dbo.coop_db_prestamos set respons='".$data->promotor."' where codagenc='".$codigo[0]."' and codsocio='".$codigo[1]."' and tipo_serv='".$data->tipo_serv."' and num_pres='".$data->num_pres."'",array());
            $qry= $db->update("update coopSUD.dbo.coop_db_sol_prestamos set cod_eval_sect='".$data->promotor."' where codAgencia='".$codigo[0]."' and codSocio='".$codigo[1]."' and tipo_serv='".$data->tipo_serv."' and num_prest='".$data->num_pres."'",array());
            $error = 0;
          } else {
            $error = $rs["cuenta"];
          }
          $rpta = array("error" => $error);
          echo json_encode($rpta);
          break;
        case "cambiarAnalista":
          $error = 0;
          $codigo = explode("-",$data->codsocio);
          $rs = $db->fetch_array($db->select("select count(*) as cuenta from CoopSUD.dbo.COOP_DB_prestamos p,CoopSUD.dbo.COOP_DB_sol_prestamos s where s.codAgencia=p.codagenc and s.codSocio=p.codsocio and s.tipo_serv=p.tipo_serv and s.num_prest=p.num_pres and p.codagenc='".$codigo[0]."' and p.codsocio='".$codigo[1]."' and p.tipo_serv='".$data->tipo_serv."' and p.num_pres='".$data->num_pres."'"));
          if($rs["cuenta"]==1){
            $qry= $db->update("update coopSUD.dbo.coop_db_prestamos set respons2='".$data->analista."' where codagenc='".$codigo[0]."' and codsocio='".$codigo[1]."' and tipo_serv='".$data->tipo_serv."' and num_pres='".$data->num_pres."'",array());
            $qry= $db->update("update coopSUD.dbo.coop_db_sol_prestamos set cod_cred_soc='".$data->analista."' where codAgencia='".$codigo[0]."' and codSocio='".$codigo[1]."' and tipo_serv='".$data->tipo_serv."' and num_prest='".$data->num_pres."'",array());
            $error = 0;
          } else {
            $error = $rs["cuenta"];
          }
          $rpta = array("error" => $error);
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
