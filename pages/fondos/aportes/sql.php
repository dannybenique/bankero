<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');
      include_once('../../../includes/web_config.php');

      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      //****************prestamos****************
      switch ($data->TipoQuery) {
        case "selAportes":
          //producto tipo aporte
          $rsapo = $db->fetch_array($db->query("select obliga from bn_productos where id_coopac=100 and id_tipo_oper=121"));
          //cuenta de saldos
          $whr = "";
          $tabla = array();
          $data->buscar = pg_escape_string(strtoupper($data->buscar));
          $whr = " and id_tipo_oper=121 and id_coopac=".$web->coopacID." and (socio like '%".$data->buscar."%' or nro_dui like '%".$data->buscar."%') ";
          $rsCount = $db->fetch_array($db->query("select count(*) as cuenta from vw_saldos where estado=1 ".$whr.";"));
          //tabla de saldos por aporte
          $qry = $db->query("select * from vw_saldos where estado=1 ".$whr." limit 25 offset 0;");
          if ($db->num_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $num_movim = $db->fetch_array($db->query_params("select count(*) as cuenta from bn_movim where id_coopac=$1 and id_tipo_oper=$2 and id_socio=$3;",array($web->coopacID,$rs["id_tipo_oper"],$rs["id_socio"])))["cuenta"];
              $tabla[] = array(
                "ID" => $rs["id"],
                "nro_dui"=> str_replace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', $rs["nro_dui"]),
                "socio" => str_ireplace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', $rs["socio"]),
                "producto" => $rs["producto"],
                "codigo" => $rs["cod_prod"],
                "moneda" => $rs["moneda"],
                "m_abrevia" => $rs["m_abrevia"],
                "saldo" => $rs["saldo"]*1,
                "num_movim" => $num_movim*1
              );
            }
          }

          //respuesta
          $rpta = array(
            "tabla" => $tabla,
            "cuenta" => $rsCount["cuenta"],
            "obliga" => $rsapo["obliga"]*1 //indica si el aporte esta config en obligatorio
          );
          echo json_encode($rpta);
          break;
        case "insAportes":
          $tipo_oper_ID = 121;
          $id = $db->fetch_array($db->query("select coalesce(max(id)+1,1) as code from bn_saldos;"))["code"];
          $productoID = $db->fetch_array($db->query_params("select id from bn_productos where id_coopac=$1 and id_tipo_oper=$2;",array($web->coopacID,$tipo_oper_ID)))["id"];
          $cod_prod = $db->fetch_array($db->query_params("select concat(to_char(now(),'YYYYMMDD'),'-',right('000000'||cast(coalesce(max(right(cod_prod,4)::integer)+1,1) as text),4)) as code from bn_saldos where left(cod_prod,8)=to_char(now(),'YYYYMMDD') and id_coopac=$1;",array($web->coopacID)))["code"];
          $sql = "insert into bn_saldos values($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,now());";
          $params = array(
            $id,
            $web->coopacID,
            $data->socioID,
            $tipo_oper_ID,
            $productoID,
            111,
            $cod_prod,
            0,1,
            $fn->getClientIP(), 
            $_SESSION['usr_ID']);
          $exec = $db->fetch_array($db->query_params($sql,$params));

          //respuesta
          $rpta = array("error"=>false, "insert"=>1);
          echo json_encode($rpta);
          break;
        case "viewAporte":
          //cabecera
          $aporte = 0;
          $socioID = 0;
          $qry = $db->query_params("select * from vw_saldos where id=$1",array($data->aporteID));
          if ($db->num_rows($qry)) {
            $rs = $db->fetch_array($qry);
            
            $socioID = $rs["id_socio"];
            $aporte = array(
              "ID" => $rs["id"],
              "socioID" => $rs["id_socio"],
              "socio" => $rs["socio"],
              "dui" => $rs["dui"],
              "nro_dui" => $rs["nro_dui"],
              "producto" => $rs["producto"],
              "cod_prod" => $rs["cod_prod"],
              "moneda" => $rs["moneda"],
              "saldo" => $rs["saldo"]*1,
              "estado" => ($rs["estado"]*1),
            );
          }

          //movimientos
          $movim = array();
          $qry = $db->query_params("select * from vw_movim where id_coopac=$1 and id_tipo_oper=$2 and id_socio=$3 order by item;",array($web->coopacID,121,$socioID));
          if ($db->num_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $movim[] = array(
                "ag" => $rs["codagenc"],
                "us" => $rs["coduser"],
                "fecha" => $rs["fecha"],
                "codigo" => $rs["codigo"],
                "codmov" => $rs["codmov"],
                "movim" => $rs["movim"],
                "ingresos" => ($rs["in_out"]==1 && $rs["afec_prod"]==1)?($rs["importe_det"]*1):(0),
                "salidas" => ($rs["in_out"]==0 && $rs["afec_prod"]==1)?($rs["importe_det"]*1):(0),
                "otros" => ($rs["afec_prod"]==0)?($rs["importe_det"]*1):(0)
              );
            }
          }
          //respuesta
          $rpta = array('aporte'=>$aporte, "movim"=>$movim);
          echo json_encode($rpta);
          break;
        case "VerifyAportes":
          $tablaPers = ""; //almacena los datos de la persona
          $persona = false; //indica que existe en personas
          $activo = false; //indica que encontro en tabla de socios
          
          //verificar en Personas
          $qry = $db->query_params("select id from personas where (nro_dui=$1);",array($data->nroDNI));
          if($db->num_rows($qry)){
            $rs = $db->fetch_array($qry);
            $tablaPers = $fn->getViewPersona($rs["id"]);
            $persona = true;
            //verificar en Aportes
            $qry = $db->query_params("select * from bn_socios where id_coopac=$1 and id_socio=$2;",array($web->coopacID,$rs["id"]));
            if($db->num_rows($qry)){
              $qryAportes = $db->query_params("select * from bn_saldos where id_tipo_oper=121 and id_coopac=$1 and id_socio=$2;",array($web->coopacID,$rs["id"]));
              $activo = ($db->num_rows($qryAportes)) ? true : false;
            } else {
              $activo = 2;
            }
          }

          //respuesta
          $rpta = array(
            "tablaPers" => $tablaPers,
            "persona" => $persona,
            "activo" => $activo,
            "mensajeNOadd" => "ya tiene APORTES...");
          echo json_encode($rpta);
          break;
      }
      $db->close();
    } else {
      $resp = array("error"=>true,"data"=>$tabla,"mensaje"=>"ninguna variable en POST");
      echo json_encode($resp);
    }
  } else {
    $resp = array("error"=>true,"mensaje"=>"Caducó la sesion.");
    echo json_encode($resp);
  }
?>
