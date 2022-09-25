<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');
      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      switch ($data->TipoQuery) {
        //****************colocaciones****************
        case "controlCierreColocaciones":
          $qry = $db->select("select CONVERT(char(8),DATEADD(ms,-3,DATEADD(mm,0,DATEADD(mm,DATEDIFF(mm,0,GETDATE())+1,0))),112) as ultimodia,CONVERT(char(8),GETDATE(),112) as actualdia,count(*) as cuenta from tb_cartera where id_tipo_oper=401 and yyyy=YEAR(GETDATE()) and mes=MONTH(GETDATE())");
          $rs = $db->fetch_array($qry);
          $dato = ($rs["ultimodia"]==$rs["actualdia"]) ? (1) : (0);
          $cuenta = $rs["cuenta"];

          $rpta = array("ultimodia"=>$dato,"cuenta"=>$cuenta,"usernivel"=>$_SESSION["usr_usernivelID"],"admin"=>701);
          echo json_encode($rpta);

          break;
        case "rptColocAgencias":
          //verificamos nivel de usuario
          $qryUser = $db->select("select id_usernivel,codagenc,id_agencia from vw_usuarios where ID=".$_SESSION['usr_ID'].";");
          if ($db->has_rows($qryUser)) { $rsUser = $db->fetch_array($qryUser); }

          $whr = "";
          $eventos = array();
          //if($rsUser['id_usernivel']==713){ $whr = "and id_agencia=".$rsUser['id_agencia']; } //operacoines solo ve su agencia
          $sql = "select id_agencia,nombre,agencia,count(*) as cuenta,sum(importe) as importe,sum(saldo) as saldo from dbo.xx_ColocacionesPrest where (fec_otorg between '".$data->miFechaIni."' and '".$data->miFechaFin." 22:59:59') ".$whr." group by id_agencia,nombre,agencia";
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $eventos[] = array(
                "ID" => $rs["id_agencia"],
                "nombre" => utf8_encode($rs["nombre"]),
                "codigo"=> $rs["agencia"],
                "cuenta" => $rs["cuenta"],
                "importe" => $rs["importe"],
                "saldo" => $rs["saldo"],
                "id_usernivel" => $rsUser["id_usernivel"]
              );
            }
          }
          echo json_encode($eventos);
          break;
        case "rptColocPromotores":
          $qry = $db->select("select id_promotor,codpromotor,promotor,nombre,count(*) as cuenta,sum(importe) as importe,sum(saldo) as saldo from dbo.xx_ColocacionesPrest where agencia='".$data->miCodagenc."' and (fec_otorg between '".$data->miFechaIni."' and '".$data->miFechaFin." 22:59:59') group by id_promotor,codpromotor,promotor,nombre order by importe desc");
          if ($db->has_rows($qry)) {
            $eventos = array();
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $eventos[] = array(
                "id_promotor" => $rs["id_promotor"],
                "codigo" => $rs["codpromotor"],
                "worker" => utf8_encode($rs["promotor"]),
                "agencia" => utf8_encode($rs["nombre"]),
                "cuenta" => $rs["cuenta"],
                "importe" => $rs["importe"],
                "saldo" => $rs["saldo"]
              );
            }
          }
          echo json_encode($eventos);
          break;
        case "rptColocSocios":
          $qry = $db->select("select codpromotor,promotor,agencia,codsocio,socio,dni,raz_social,fecha,importe,saldo from dbo.xx_ColocacionesPrest where codpromotor='".$data->miPromotor."' and (fec_otorg between '".$data->miFechaIni."' and '".$data->miFechaFin." 22:59:59')  order by importe desc");
          if ($db->has_rows($qry)) {
            $eventos = array();
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $miSocio = $rs["socio"];
              if(strlen($miSocio)<=4){$miSocio = $rs["raz_social"];}
              $eventos[] = array(
                "worker" => ($rs["promotor"]),
                "codigo" => ($rs["codsocio"]),
                "socio" => ($miSocio),
                "dni" => ($rs["dni"]),
                "fec_otorg" => $rs["fecha"],
                "importe" => $rs["importe"],
                "saldo" => $rs["saldo"]
              );
            }
          }
          echo json_encode($eventos);
          //var_dump($eventos);
          break;
        case "rptColocSociosDownload":
          $whr = "";
          $agencia = array("codigo"=>"00", "abrev"=>"all");
          $tabla[] = array(
            array("text" => "agencia"),
            array("text" => "codsocio"),
            array("text" => "socio"),
            array("text" => "direccion"),
            array("text" => "telefono"),
            array("text" => "fecha_otorg"),
            array("text" => "importe"),
            array("text" => "saldo"),
            array("text" => "respons"),
            array("text" => "promotor")
          );
          if(($data->agenciaID) > 0) {
            $whr = "and id_agencia=".($data->agenciaID);
            //agencia
            $rs = $db->fetch_array($db->select("select codigo,abrev from dbo.tb_agencias where ID=".$data->agenciaID));
            $agencia["codigo"] = utf8_encode($rs["codigo"]);
            $agencia["abrev"] = utf8_encode($rs["abrev"]);
          }
          $qry = $db->select("select agencia,codsocio,socio,raz_social,direccion,telefonos,fecha,importe,saldo,codpromotor,promotor from dbo.xx_ColocacionesPrest where (fec_otorg between '".$data->miFechaIni."' and '".$data->miFechaFin." 23:59:59') ".$whr."  order by importe desc");
          if ($db->has_rows($qry)) {
            $eventos = array();
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $miSocio  = (strlen($rs["raz_social"])<=4) ? ($rs["socio"]) : ($rs["raz_social"]);

              $tabla[] = array(
                array("text" => $rs["agencia"]),
                array("text" => $rs["codsocio"]),
                array("text" => utf8_encode($miSocio)),
                array("text" => utf8_encode($rs["direccion"])),
                array("text" => ($rs["telefonos"])),
                array("text" => ($rs["fecha"])),
                array("text" => ($rs["importe"]*1)),
                array("text" => ($rs["saldo"]*1)),
                array("text" => ($rs["codpromotor"])),
                array("text" => utf8_encode($rs["promotor"]))
              );
              /*$eventos[] = array(
                "agencia" => $rs["agencia"],
                "codsocio" => $rs["codsocio"],
                "socio" => utf8_encode($miSocio),
                "direccion" => utf8_encode($rs["direccion"]),
                "telefonos" => $rs["telefonos"],
                "fec_otorg" => $rs["fecha"],
                "importe" => $rs["importe"],
                "saldo" => $rs["saldo"],
                "respons" => $rs["codpromotor"],
                "promotor" => utf8_encode($rs["promotor"])
              );*/
            }
          }

          //respuesta
          $options = array("fileName"=>"colocac_".$agencia["codigo"]."_".$agencia["abrev"]);
          $tableData[] = array("sheetName"=>"colocaciones","data"=>$tabla);
          $rpta = array("options"=>$options,"tableData"=>$tableData);
          echo json_encode($rpta);
          break;
        case "cierreColocaciones": //401
          $rpta = array();
          $params = array();

          //el maximo ID de tb_cartera
          $rsMax = $db->fetch_array($db->select("select isnull(max(ID),0) as maxi from dbo.tb_cartera;"));
          $maxi = $rsMax["maxi"];

          //consulta para ingresar los cierres
          $sql = "insert into dbo.tb_cartera select (".$maxi.") + ROW_NUMBER()over(order by id_agencia,id_promotor) as nro, getdate() as fecha,year(getdate()) as yyyy,month(getdate()) as mes, id_agencia,id_promotor,count(*) as cuenta,sum(saldo) as saldo,1 as id_tipo_mone,401 as id_tipo_oper,'".get_client_ip()."' as sys_ip,".$_SESSION['usr_agenciaID']." as sys_agencia,".$_SESSION['usr_ID']." as sys_user,getdate() as sys_fecha,dbo.fn_GetTime() as sys_hora from dbo.xx_ColocacionesPrest where (fec_otorg between DATEADD(mm,DATEDIFF(mm,0,GETDATE()),0) and DATEADD(ms,-3,DATEADD(mm,0,DATEADD(mm,DATEDIFF(mm,0,GETDATE())+1,0)))) group by id_agencia,id_promotor";
          $qry = $db->insert($sql, $params);

          //resultado
          $rpta = array(
            "error" => 0,
            "mensaje" => "se ejecuto el cierre de las colocaciones con exito"
          );
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
