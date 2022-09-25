<?php
  include_once('../../../includes/sess_verifica.php');

    if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      switch ($data->TipoQuery) {
        case "DownloadAportes":
          $whr = "";
          $tabla[] = array(
            array("text" => "CODIGO SOCIO"),
            array("text" => "TIPO DOC"),
            array("text" => "NUMERO DOC"),
            array("text" => "TIPO PERSONA"),
            array("text" => "APE PATERNO"),
            array("text" => "APE MATERNO"),
            array("text" => "NOMBRES"),
            array("text" => "NACIONALIDAD"),
            array("text" => "GENERO"),
            array("text" => "DOMICILIO"),
            array("text" => "UBIGEO"),
            array("text" => "APORTE"),
            array("text" => "SUSCRITO"),
            array("text" => "SALDO APO"),
            array("text" => "SALDO RET")
          );

          $sql = "select s.codagenc+'-'+s.codsocio as codigo,s.dni,s.ruc,s.carnet,s.ap_pater,s.ap_mater,s.nombres,s.raz_social,s.sexo,s.direccion,s.ubigeo,sa.saldo from COOP_DB_saldos sa,COOP_DB_socios_gen s where s.codagenc=sa.codagenc and s.codsocio=sa.codsocio and sa.tipo_serv='001' and sa.saldo>0 order by s.dni,s.ruc";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $qry1 = $db->select("select sum(importe) as aporte from coop_db_movimientos where tipo_mov='18' and codagenc+'-'+codsocio='".$rs["codigo"]."'");
              $qry2 = $db->select("select sum(importe) as retiro from coop_db_movimientos where tipo_mov='30' and codagenc+'-'+codsocio='".$rs["codigo"]."'");

              if($db->has_rows($qry1)) { $rx = $db->fetch_array($qry1); $apo = $rx["aporte"]; } else { $apo = 0; }
              if($db->has_rows($qry2)) { $rx = $db->fetch_array($qry2); $ret = $rx["retiro"]; } else { $ret = 0; }

              if(trim($rs["ruc"])=="0" && trim($rs["carnet"])=="0"){
                $tipoDoc = "DNI";
                $nroDoc = $rs["dni"];
                $tipoPer = "NATURAL";
                $nombres = $rs["nombres"];
                $nacionalidad = "PERUANO";
              }
              if(trim($rs["dni"])=="0" && trim($rs["carnet"])=="0"){
                $tipoDoc = "RUC";
                $nroDoc = $rs["ruc"];
                $tipoPer = "JURIDICA";
                $nombres = $rs["raz_social"];
                $nacionalidad = "PERUANO";
              }
              if(trim($rs["dni"])=="0" && trim($rs["ruc"])=="0"){
                $tipoDoc = "CARNET";
                $nroDoc = $rs["carnet"];
                $tipoPer = "NATURAL";
                $nombres = $rs["nombres"];
                $nacionalidad = "";
              }

              $tabla[] = array(
                array("text" => $rs["codigo"]),
                array("text" => $tipoDoc),
                array("text" => $nroDoc),
                array("text" => $tipoPer),
                array("text" => utf8_encode($rs["ap_pater"])),
                array("text" => utf8_encode($rs["ap_mater"])),
                array("text" => utf8_encode($nombres)),
                array("text" => $nacionalidad),
                array("text" => utf8_encode($rs["sexo"])),
                array("text" => utf8_encode($rs["direccion"])),
                array("text" => utf8_encode($rs["ubigeo"])),
                array("text" => $rs["saldo"]*1),
                array("text" => 0),
                array("text" => $apo*1),
                array("text" => $ret*1)
              );
            }
          }
          //respuesta
          $options = array("fileName"=>"cart_aportes");
          $tableData[] = array("sheetName"=>"aportes","data"=>$tabla);
          $rpta = array("options"=>$options,"tableData"=>$tableData);
          echo json_encode($rpta);
          break;
        case "DownloadAhorros":
          $tabla[] = array(
            array("text" => "CODIGO CUENTA"),
            array("text" => "CODIGO SOCIO"),
            array("text" => "TIPO DOC"),
            array("text" => "NRO DOC"),
            array("text" => "APE PATERNO"),
            array("text" => "APE MATERNO"),
            array("text" => "NOMBRES"),
            array("text" => "FEC NAC"),
            array("text" => "SEXO"),
            array("text" => "OFICINA"),
            array("text" => "TIPO PROD"),
            array("text" => "TIPO CUENTA"),
            array("text" => "MONTO APERT"),
            array("text" => "SALDO"),
            array("text" => "FEC APERT"),
            array("text" => "FEC CANCEL"),
            array("text" => "TEA"),
            array("text" => "PLAZO DB"),
            array("text" => "MONEDA"),
            array("text" => "TIPO PERSONA"),
            array("text" => "INTERES"),
            array("text" => "TIPO PAGO"),
            array("text" => "PROFESION"),
            array("text" => "BLOQUEO"),
            array("text" => "CTA CONTABLE"),
            array("text" => "CTA INTERES"),
            array("text" => "GARANTIA"),
            array("text" => "VENCIMIENTO"),
            array("text" => "SALDO DISP"),
            array("text" => "TIPO_SERV"),
            array("text" => "SERVICIO")
          );

          $sql =  "select so.codagenc+'-'+so.codsocio as codigo,so.codagenc,so.codsocio,so.dni,so.ruc,so.carnet,so.ap_pater,so.ap_mater,so.nombres,so.raz_social,replace(convert(VARCHAR, so.fec_nacim, 103), ' ', '/') as fec_nacim,so.sexo,sa.saldo,so.ocupacion,sv.moneda,sv.tipo_serv,sv.detalle,sv.interes_2,case when sv.tipo_serv in ('673','688','640','676','631','632','633','843') then 'SI' else 'NO' end as garantia from COOP_DB_saldos sa,COOP_DB_socios_gen so,COOP_DB_tipo_serv sv where sv.tipo_serv=sa.tipo_serv and so.codagenc=sa.codagenc and so.codsocio=sa.codsocio and sa.tipo_oper='02' and sa.saldo>0 order by dni,ruc";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);

              $tipoDoc = (trim($rs["dni"])!="0") ? ("DNI") : ((trim($rs["ruc"])!="0")?("RUC"):("CARNET"));
              $nroDoc = (trim($rs["dni"])!="0") ? ($rs["dni"]) : ((trim($rs["ruc"])!="0") ? ($rs["ruc"]) : ($rs["carnet"]));
              $nombres = (trim($rs["ruc"])!="0") ? ($rs["raz_social"]) : ($rs["nombres"]);

              //movimientos
              $qmm = $db->select("select top (1) *,replace(convert(VARCHAR,fecha,103),' ','/') as fechaX from COOP_DB_movimientos where codagenc='".$rs["codagenc"]."' and codsocio='".$rs["codsocio"]."' and tipo_mov='01' and tipo_serv='".$rs["tipo_serv"]."' order by fecha");
              if ($db->has_rows($qmm)) {
                $mm = $db->fetch_array($qmm);
                $agencia = $mm["agencia"];
                $montoAp = $mm["importe"];
                $fec_ini = $mm["fechaX"];
              } else {
                $agencia = "---";
                $montoAp = 0;
                $fec_ini = "";
              }

              //subconsulta para prestamos y garantia
              $qrr = $db->select("select top(1) d.numero,replace(convert(VARCHAR,d.fec_vencim,103),' ','/') as fec_vencim,p.* from COOP_DB_prestamos p, COOP_DB_prestamos_det d where p.codagenc=d.codagenc and p.codsocio=d.codsocio and p.num_pres=d.num_pres and p.codagenc='".$rs["codagenc"]."' and p.codsocio='".$rs["codsocio"]."' and p.saldo>0 and p.tipo_serv in ('673','688','640','676','631','632','633','843') order by numero desc");
              if($db->has_rows($qrr)){
                $rr = $db->fetch_array($qrr);
                $fec_vencim = $rr["fec_vencim"];
                $garantia = "SI";
              } else {
                $fec_vencim = "";
                $garantia = "NO";
              }

              //subconsulta DPF
              $qss = $db->select("select *,datediff(dd,fec_inicio,fec_fin)-plazo as plazoreal,replace(convert(VARCHAR,dateadd(day,plazo,fec_inicio),103),' ','/') as fec_app,replace(convert(VARCHAR,fec_inicio,103),' ','/') as fec_ini,replace(convert(VARCHAR, fec_fin, 103), ' ', '/') as fec_fin from COOP_DB_ahorros_plazo where codagenc='".$rs["codagenc"]."' and codsocio='".$rs["codsocio"]."' and tipo_serv='".$rs["tipo_serv"]."' and saldo>0;");
              if ($db->has_rows($qss)) { //DPF
                $ss = $db->fetch_array($qss);
                $codigoCta = $rs["codigo"].".".$rs["tipo_serv"].".".$ss["numero"];
                $montoAp = $ss["importe"];
                $tipoProducto = ($rs["interes_2"]==0)?("AHORROS"):("DPF");
                $tipoCtaProd = ($rs["interes_2"]==0)?("INDIVIDUAL"):("");
                $plazodb = ($ss["plazoreal"]>0)?($ss["plazoreal"]):($ss["plazo"]);
                $fec_ini = ($ss["plazoreal"]>0)?($ss["fec_app"]):($ss["fec_ini"]);
                $fec_fin = $ss["fec_fin"];
              } else { //ahorro movil
                $codigoCta = $rs["codigo"].".".$rs["tipo_serv"];
                $tipoProducto = "AHORROS";
                $tipoCtaProd = "INDIVIDUAL";
                $plazodb = 0;
                $fec_fin = "";
              }

              $tabla[] = array(
                array("text" => $codigoCta),
                array("text" => $rs["codigo"]),
                array("text" => $tipoDoc),
                array("text" => $nroDoc),
                array("text" => utf8_encode($rs["ap_pater"])),
                array("text" => utf8_encode($rs["ap_mater"])),
                array("text" => utf8_encode($nombres)),
                array("text" => $rs["fec_nacim"]),
                array("text" => utf8_encode($rs["sexo"])),
                array("text" => $agencia),
                array("text" => $tipoProducto),
                array("text" => $tipoCtaProd),
                array("text" => $montoAp*1),
                array("text" => $rs["saldo"]*1),
                array("text" => $fec_ini),
                array("text" => $fec_fin),
                array("text" => $rs["interes_2"]*1),
                array("text" => $plazodb),
                array("text" => utf8_encode($rs["moneda"])),
                array("text" => ""),
                array("text" => ""),
                array("text" => ""),
                array("text" => utf8_encode($rs["ocupacion"])),
                array("text" => $garantia),
                array("text" => ""),
                array("text" => ""),
                array("text" => $garantia),
                array("text" => $fec_vencim),
                array("text" => $rs["saldo"]*1),
                array("text" => $rs["tipo_serv"]),
                array("text" => utf8_encode($rs["detalle"]))
              );
            }
          }
          //respuesta
          $options = array("fileName"=>"cart_ahorros");
          $tableData[] = array("sheetName"=>"ahorros","data"=>$tabla);
          $rpta = array("options"=>$options,"tableData"=>$tableData);
          echo json_encode($rpta);
          break;
        case "DownloadCreditos":
          $tabla[] = array(
            array("text" => "APE PATERNO"),
            array("text" => "APE MATERNO"),
            array("text" => "NOMBRES"),
            array("text" => "FEC NAC"),
            array("text" => "GENERO"),
            array("text" => "ESTADO CIVIL"),
            array("text" => "SIGLA EMP"),
            array("text" => "COD SOCIO"),
            array("text" => "PART. REGIS"),
            array("text" => "TIPO DOC"),
            array("text" => "NRO DOC"),
            array("text" => "TIPO PERS"),
            array("text" => "TELEFONO"),
            array("text" => "DOMICILIO"),
            array("text" => "REL COOP"),
            array("text" => "CLASIF DEUDOR"),
            array("text" => "COD AGENCIA"),
            array("text" => "MONEDA"),
            array("text" => "NUM_PRES"),
            array("text" => "TIPO CRED"),
            array("text" => "SUB TIPO"),
            array("text" => "FEC DESEMB"),
            array("text" => "MONTO"),
            array("text" => "TASA INT. ANUAL"),
            array("text" => "SALDO COLOC"),
            array("text" => "CAPITAL VIGENTE"),
            array("text" => "CAPITAL RESTRUC"),
            array("text" => "CAPITAL REFIN"),
            array("text" => "CAPITAL VENC"),
            array("text" => "CAPITAL JUDIC"),
            array("text" => "CAPITAL CONTING"),
            array("text" => "DIAS MORA"),
            array("text" => "SALDO GAR PREF"),
            array("text" => "SALDO GAR AUTOLIQ"),
            array("text" => "PROV REQUER"),
            array("text" => "PROV CONST"),
            array("text" => "SALDO CRED CAST"),
            array("text" => "REND. DEVENGADO"),
            array("text" => "INTER. EN SUSPENSO"),
            array("text" => "INGR DIFER"),
            array("text" => "TIPO SERV"),
            array("text" => "TIPO PROD"),
            array("text" => "PER GRACIA")
          );

          $sql = "select s.ap_pater,s.ap_mater,s.nombres,s.raz_social,replace(convert(VARCHAR, s.fec_nacim, 103), ' ', '/') as fec_nacim,s.sexo,s.est_civil,s.codagenc+'-'+s.codsocio as codigo,s.dni,s.ruc,s.carnet,s.tel_movil,s.tel_fijo,s.direccion,m.agencia, ts.moneda,p.num_pres,replace(convert(VARCHAR, p.fec_otorg, 103), ' ', '/') as fec_otorg,p.importe,ts.interes_1 as TEM,p.saldo,p.tipo_serv,ts.detalle from COOP_DB_prestamos p, COOP_DB_socios_gen s,COOP_DB_tipo_serv ts,COOP_DB_movimientos m where p.codagenc=s.codagenc and p.codsocio=s.codsocio and p.tipo_serv=ts.tipo_serv and p.codagenc=m.codagenc and p.codsocio=m.codsocio and p.tipo_serv=m.tipo_serv and p.num_pres=m.num_pres and m.tipo_mov='19' and p.saldo>0 order by codigo";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);

              $tipoDoc = (trim($rs["dni"])!="0") ? ("DNI") : ((trim($rs["ruc"])!="0")?("RUC"):("CARNET"));
              $nroDoc = (trim($rs["dni"])!="0") ? ($rs["dni"]) : ((trim($rs["ruc"])!="0") ? ($rs["ruc"]) : ($rs["carnet"]));
              $nombres = (trim($rs["ruc"])!="0") ? ($rs["raz_social"]) : ($rs["nombres"]);
              $sigla = (trim($rs["ruc"])!="0") ? ($rs["raz_social"]) : ("");
              $estCivil = ($rs["est_civil"]=="S") ? ("SOLTERO") : (($rs["est_civil"]=="C")?("CASADO"):(($rs["est_civil"]=="D")?("DIVORCIADO"):(($rs["est_civil"]=="V")?("VIUDO"):("CONVIVIENTE"))));

              $tabla[] = array(
                array("text" => utf8_encode($rs["ap_pater"])),
                array("text" => utf8_encode($rs["ap_mater"])),
                array("text" => utf8_encode($nombres)),
                array("text" => $rs["fec_nacim"]),
                array("text" => utf8_encode($rs["sexo"])),
                array("text" => $estCivil),
                array("text" => utf8_encode($sigla)),
                array("text" => $rs["codigo"]),
                array("text" => ""), //partida registral
                array("text" => $tipoDoc),
                array("text" => $nroDoc),
                array("text" => ""), //tipo persona
                array("text" => utf8_encode($rs["tel_movil"]."-".$rs["tel_fijo"])),
                array("text" => utf8_encode($rs["direccion"])),
                array("text" => "-"), //relacion laboral con la empresa
                array("text" => ""), //clasificacion del deudor
                array("text" => $rs["agencia"]),
                array("text" => $rs["moneda"]),
                array("text" => $rs["num_pres"]),
                array("text" => ""), //tipo de credito
                array("text" => ""), //sub tipo de credito
                array("text" => $rs["fec_otorg"]),
                array("text" => $rs["importe"]*1),
                array("text" => $rs["TEM"]*1), //tasa de interes diaria
                array("text" => $rs["saldo"]*1), //saldo de colocaciones
                array("text" => ""), //capital vigente
                array("text" => ""), //capital restructurado
                array("text" => ""), //capital refinanciado
                array("text" => ""), //capital vencido
                array("text" => ""), //capital en cobranza judicial
                array("text" => ""), //capital contingente
                array("text" => ""), //dias de mora
                array("text" => ""), //saldo de garantias preferidas
                array("text" => ""), //saldo de garantias autoliquidables
                array("text" => ""), //provisiones requeridas
                array("text" => ""), //provisiones contituidas
                array("text" => ""), //saldo de intereses castigados
                array("text" => ""), //rendimiento devengado
                array("text" => ""), //intereses en suspenso
                array("text" => ""), //ingresos diferidos
                array("text" => $rs["tipo_serv"]), //tipo serv
                array("text" => $rs["detalle"]), //tipo de producto
                array("text" => 0) //periodo de gracia
              );
            }
          }
          //respuesta
          $options = array("fileName"=>"cart_creditos");
          $tableData[] = array("sheetName"=>"creditos","data"=>$tabla);
          $rpta = array("options"=>$options,"tableData"=>$tableData);
          echo json_encode($rpta);
          break;
        case "DownloadOperaciones":
          $tabla[] = array(
            array("text" => "documento"),
            array("text" => "cuenta"),
            array("text" => "fecha"),
            array("text" => "moneda"),
            array("text" => "capital"),
            array("text" => "interes"),
            array("text" => "mora"),
            array("text" => "gastos"),
            array("text" => "total")
          );

          //where
          $whr = "agencia='03' and fecha>='2021-09-28 00:00:00' ";
          $qry = $db->select("select codagenc,codsocio,tipo_serv,num_pres,agencia,ventanilla,num_trans,fecha,REPLACE(CONVERT(NVARCHAR,fecha,103),' ','/') AS fecha2 from coopSUD.dbo.COOP_DB_movimientos where tipo_oper='04' and tipo_mov<>'19' and ".$whr." group by codagenc,codsocio,tipo_serv,num_pres,agencia,ventanilla,num_trans,fecha order by fecha,codagenc,codsocio,tipo_serv,num_pres");
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $capital = 0;
              $interes = 0;
              $mora = 0;
              $gastos = 0;
              $tipo_mov = "";
              $operacion = "";
              //subconsulta
              $qxx = $db->select("select tipo_mov,importe from coopSUD.dbo.COOP_DB_movimientos where codagenc='".$rs["codagenc"]."' and codsocio='".$rs["codsocio"]."' and agencia='".$rs["agencia"]."' and ventanilla='".$rs["ventanilla"]."' and num_pres='".$rs["num_pres"]."' and num_trans='".$rs["num_trans"]."'");
              if ($db->has_rows($qxx)) {
                for($n=0; $n<$db->num_rows($qxx); $n++){
                  $rx = $db->fetch_array($qxx);
                  switch($rx["tipo_mov"]){
                    case "14": $capital += $rx["importe"]; $operacion = "PAGO"; break; //capital
                    case "15": $interes += $rx["importe"]; $operacion = "PAGO"; break; //interes
                    case "17": $mora    += $rx["importe"]; $operacion = "PAGO"; break; //moratorio
                    case "05": //gastos
                    case "35": $gastos += $rx["importe"]; $operacion = "PAGO"; break; //gastos
                    /*case "07":
                    case "08":
                    case "09":
                    case "10":
                    case "12":
                    case "13":
                    case "30":
                    case "34":
                    case "38":
                    case "39":
                    case "42":
                    case "58":
                    case "63":
                    case "68":
                    case "69":
                    case "86":
                    case "87": $otros += $rx["importe"]; $operacion = $rx["detalle"]; $tipo_mov = $rx["tipo_mov"]; break; //caja*/
                    default: $otros += $rx["importe"]; $operacion = $rx["detalle"]; $tipo_mov = $rx["tipo_mov"]; break; //default
                  }
                }
              }
              $total = $capital + $interes + $mora + $gastos;

              $tabla[] = array(
                array("text" => ($rs["agencia"].".".$rs["ventanilla"].".".$rs["num_trans"])),
                array("text" => ($rs["codagenc"]."-".$rs["codsocio"].".".$rs["tipo_serv"].".".$rs["num_pres"])),
                array("text" => ($rs["fecha2"])),
                array("text" => $rs["moneda"]),
                array("text" => $capital),
                array("text" => $interes),
                array("text" => $mora),
                array("text" => $gastos),
                array("text" => $total)
              );
            }
          }
          //respuesta
          $options = array("fileName"=>"cart_operaciones");
          $tableData[] = array("sheetName"=>"operaciones","data"=>$tabla);
          $rpta = array("options"=>$options,"tableData"=>$tableData);
          echo json_encode($rpta);
          break;
        case "comboAgencias":
          $tabla = array();
          $qry = $db->select("select * from COOP_DB_agencia order by agencia");
          if ($db->has_rows($qry)) {
            for($xx=0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => ($rs["agencia"]),
                "nombre" => ($rs["detalle"])
              );
            }
          }
          echo json_encode($tabla);
          break;
        case "ArregloAportes": //arreglar saldos de aportes
          $tabla = array();
          $whr1 = //"and m.codagenc='02' ";
          $whr2 = ""; //" and s.saldo<=0 and year(m.fecha)=2016 ";
          $qry = $db->select("select m.*,replace(convert(NVARCHAR, m.fecha, 103), ' ', '/') as fecha1, s.saldo from COOP_DB_movimientos m, COOP_DB_saldos s where m.codagenc=s.codagenc and m.codsocio=s.codsocio and m.tipo_serv=s.tipo_serv and m.tipo_oper=s.tipo_oper and m.item='2' and m.tipo_serv='001' ".$whr1.$whr2);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              //verificar cantidad de anchivos
              $rx = $db->fetch_array($db->select("select count(*) as cuenta from COOP_DB_movimientos where codagenc='".$rs["codagenc"]."' and codsocio='".$rs["codsocio"]."' and num_trans='".$rs["num_trans"]."' and tipo_serv='001'"));

              //ultimo movimiento
              $qyy = $db->select("select replace(convert(NVARCHAR, max(fecha), 103), ' ', '/') as fecha from COOP_DB_movimientos where codagenc='".$rs["codagenc"]."' and codsocio='".$rs["codsocio"]."' and tipo_serv='001'");
              if ($db->has_rows($qyy)) { $ry = $db->fetch_array($qyy); $fechalast = $ry["fecha"]; } else { $fechalast = "error"; }

              if($rx["cuenta"]==1){ //tiene problemas
                if($data->arreglar==1){
                  $qqq = $db->select("insert into COOP_DB_movimientos select agencia,ventanilla,'18',tipo_serv,tipo_oper,num_trans,'1',fecha,moneda,codagenc,codsocio,referen,num_pres,5,codusuario,fecha_dig,dias_int,condicion from COOP_DB_movimientos where codagenc='".$rs["codagenc"]."' and codsocio='".$rs["codsocio"]."' and num_trans='".$rs["num_trans"]."' and tipo_serv='001' and tipo_oper='01'");
                  $qqq = $db->select("update COOP_DB_movimientos set importe=importe-5 where codagenc='".$rs["codagenc"]."' and codsocio='".$rs["codsocio"]."' and num_trans='".$rs["num_trans"]."' and tipo_serv='001' and tipo_oper='01' and item='2'");

                  $r1 = $db->fetch_array($db->select("select sum(importe) as ing from COOP_DB_movimientos where codagenc='".$rs["codagenc"]."' and codsocio='".$rs["codsocio"]."' and tipo_serv='001' and tipo_mov='18'"));
                  $r2 = $db->fetch_array($db->select("select sum(importe) as sal from COOP_DB_movimientos where codagenc='".$rs["codagenc"]."' and codsocio='".$rs["codsocio"]."' and tipo_serv='001' and tipo_mov='30'"));
                  $qqq = $db->select("update COOP_DB_saldos set saldo=".($r1["ing"]-$r2["sal"])." where codagenc='".$rs["codagenc"]."' and codsocio='".$rs["codsocio"]."' and tipo_serv='001'");
                }

                $tabla[] = array(
                  "codsocio" => $rs["codagenc"]."-".$rs["codsocio"],
                  "fecha1" => $rs["fecha1"],
                  "fecha2" => $fechalast,
                  "tipomov" => $rs["tipo_mov"],
                  "importe" => $rs["importe"],
                  "saldo" => $rs["saldo"]
                );
              }
            }
          }
          echo json_encode($tabla);
          break;
        case "ArregloCreditos": //arreglar saldos de creditos
          $tabla = array();
          $qry = $db->select("select * from COOP_DB_prestamos order by codagenc,codsocio");
          for($xx = 0; $xx<$db->num_rows($qry); $xx++){
            $rs = $db->fetch_array($qry);
            //verificar cantidad de anchivos
            $rx = $db->fetch_array($db->select("select sum(importe) as sumamovim from COOP_DB_movimientos where codagenc='".$rs["codagenc"]."' and codsocio='".$rs["codsocio"]."' and tipo_serv='".$rs["tipo_serv"]."' and num_pres='".$rs["num_pres"]."' and tipo_mov='14'"));
            $sum = $rx["sumamovim"]+$rs["saldo"];

            if($rs["importe"]!=$sum){ //tiene problemas
              $tabla[] = array(
                "codsocio" => $rs["codagenc"]."-".$rs["codsocio"],
                "tiposerv" => $rs["tipo_serv"],
                "numpres" => $rs["num_pres"],
                "importe" => $rs["importe"],
                "saldo" => $rs["saldo"],
                "sumamovim" => $rx["sumamovim"],
              );
            }
          }
          echo json_encode($tabla);
          break;
        case "DownloadSaldos": //verificar saldos aportes y ahorros
          $tabla[] = array(
            array("text" => "COD_SOCIO"),
            array("text" => "TIPO_SERV"),
            array("text" => "TIPO_OPER"),
            array("text" => "SALDO"),
            array("text" => "INGRESOS"),
            array("text" => "EGRESOS"),
            array("text" => "TOTAL_MOV")
          );

          $qry = $db->select("select * from COOP_DB_saldos where codagenc='".$data->agenciaID."' order by codagenc,codsocio;");
          if ($db->has_rows($qry)) {
            for($xx=0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $ingreso = 0;
              $egreso = 0;
              switch($rs["tipo_oper"]){
                case "01": //aportes
                  $q1 = $db->select("select sum(importe) as ingreso from COOP_DB_movimientos where codagenc='".$rs["codagenc"]."' and codsocio='".$rs["codsocio"]."' and tipo_serv='".$rs["tipo_serv"]."' and tipo_mov='18' ");
                  $q2 = $db->select("select sum(importe) as retiro from COOP_DB_movimientos where codagenc='".$rs["codagenc"]."' and codsocio='".$rs["codsocio"]."' and tipo_serv='".$rs["tipo_serv"]."' and tipo_mov='30' ");
                  if($db->has_rows($q1)){ $rx = $db->fetch_array($q1); $ingreso = $rx["ingreso"]*1; }
                  if($db->has_rows($q2)){ $rx = $db->fetch_array($q2); $egreso = $rx["retiro"]*1; }
                  break;
                case "02": //ahorros
                  $q1 = $db->select("select sum(importe) as ingreso from COOP_DB_movimientos where codagenc='".$rs["codagenc"]."' and codsocio='".$rs["codsocio"]."' and tipo_serv='".$rs["tipo_serv"]."' and tipo_mov in('01','16') ");
                  $q2 = $db->select("select sum(importe) as retiro from COOP_DB_movimientos where codagenc='".$rs["codagenc"]."' and codsocio='".$rs["codsocio"]."' and tipo_serv='".$rs["tipo_serv"]."' and tipo_mov='02' ");
                  if($db->has_rows($q1)){ $rx = $db->fetch_array($q1); $ingreso = $rx["ingreso"]*1; }
                  if($db->has_rows($q2)){ $rx = $db->fetch_array($q2); $egreso = $rx["retiro"]*1; }
                  break;
                case "07": //gastos judiciales
                  $q1 = $db->select("select sum(importe) as ingreso from COOP_DB_historial where codagenc='".$rs["codagenc"]."' and codsocio='".$rs["codsocio"]."' and tipo_serv='".$rs["tipo_serv"]."' ");
                  $q2 = $db->select("select sum(importe) as retiro from COOP_DB_movimientos where codagenc='".$rs["codagenc"]."' and codsocio='".$rs["codsocio"]."' and tipo_serv='".$rs["tipo_serv"]."' and tipo_mov='79' ");
                  if($db->has_rows($q1)){ $rx = $db->fetch_array($q1); $ingreso = $rx["ingreso"]*1; }
                  if($db->has_rows($q2)){ $rx = $db->fetch_array($q2); $egreso = $rx["retiro"]*1; } //el movimiento 79 es pago de los gastos judiciales
              }
              $tabla[] = array(
                array("text" => $rs["codagenc"]."-".$rs["codsocio"]),
                array("text" => $rs["tipo_serv"]),
                array("text" => $rs["tipo_oper"]),
                array("text" => round($rs["saldo"]*1,2)),
                array("text" => $ingreso),
                array("text" => $egreso),
                array("text" => round($ingreso-$egreso,2))
              );
            }
          }
          $options = array("fileName"=>"saldos_apo_aho_".$data->agenciaID);
          $tableData[] = array("sheetName"=>"aportes","data"=>$tabla);
          $rpta = array("options"=>$options,"tableData"=>$tableData);
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
