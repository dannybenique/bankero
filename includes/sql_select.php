<?php
  include_once('sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('db_database.php');
      include_once('funciones.php');
      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      switch ($data->TipoQuery) {
        case "ComboBox":
          $tabla = array();
          $sql = "";
          switch($data->miSubSelect){
            case "Agencias" : $sql = "select * from dbo.tb_agencias where estado=1 order by ID;"; break; //agencias
            case "CiudadesAg" : $sql = "select distinct ciudad as nombre,ciudad as ID from dbo.tb_agencias order by ciudad;"; break; //ciudades de agencia
            case "Sexo" : $sql = "select * from dbo.tb_mastertipos where id_padre=1 order by orden;"; break; //sexo
            case "EstadoCivil" : $sql = "select * from dbo.tb_mastertipos where id_padre=2 order by orden;"; break; //estado civil
            case "GradoInstruccion" : $sql = "select * from dbo.tb_mastertipos where id_padre=3 order by orden;"; break; //grado de instruccion
            case "TipoVivienda" : $sql = "select * from dbo.tb_mastertipos where id_padre=4 order by orden;"; break; //Tipo Vivienda
            case "DNI" : $sql = "select * from dbo.tb_mastertipos where id_padre=5 order by orden;"; break; //tipos DNI
            case "Cargo" : $sql = "select * from dbo.tb_mastertipos where id_padre=6 order by orden;"; break; //tipo Cargos
            case "NivelAcceso" : $sql = "select * from dbo.tb_mastertipos where id_padre=7 order by orden;"; break; //tipo Nivel de Acceso
            case "Prod.Aportes" : $sql = "select * from dbo.tb_productos where id_tipo_oper=1 and ID=2"; break; //solo aportes
            case "Prod.Ahorros" : $sql = "select * from dbo.tb_productos where id_tipo_oper=2 and estado=1 order by nombre"; break; //solo ahorros
            case "Prod.Creditos" : $sql = "select * from dbo.tb_productos where id_tipo_oper=4 and estado=1 order by nombre"; break; //solo creditos
            case "UbiGeo" : $sql = "select * from dbo.sis_ubigeo where id_padre=".$data->miPadreID." order by nombre"; break; //ubigeo: region, pronvicia, distrito
            case "tipoProducto" : $sql = "select ID,detalle as nombre from dbo.tb_tipo_prod where id_padre is not null order by ID"; break;
            case "tipoOperacion" : $sql = "select ID,detalle as nombre from dbo.tb_tipo_oper where id_padre is null order by ID"; break;
            case "tipoMoneda" : $sql = "select ID,detalle as nombre from dbo.tb_tipo_mone order by ID"; break;
            case "cajaDocs" : $sql = "select ID,nombre from dbo.tb_tipo_cajadocs where ID>1 order by ID"; break;
            case "ExtrOperaciones": $sql = "select distinct op.ID,op.detalle as nombre from tb_tipo_oper op, tb_oper_movimientos mv where op.ID=mv.id_tipo_oper and mv.id_socio=".$data->miSocioID; break;//estracto bancario - operaciones
            case "ExtrProducto": $sql = "select distinct p.ID,p.nombre from tb_productos p, tb_oper_movimientos m where p.ID=m.id_producto and p.ID>1 and m.id_tipo_oper=".$data->miPadreID." and m.id_socio=".$data->miSocioID; break;//estracto bancario - productos
          }
          $qry = $db->select($sql);
          if ($db->has_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => $rs["ID"],
                "nombre" => utf8_encode($rs["nombre"])
              );
            }
          }

          if($data->miSubSelect=="Agencias") {
            echo json_encode(array(
              "tabla"=>$tabla,
              "agenciaID"=>$_SESSION['usr_agenciaID'],
              "usernivelID"=>$_SESSION['usr_usernivelID'],
              "admin"=>701
            ));
          } else { echo json_encode($tabla); }
          break;
        case "fechaHoy":
          //obtener fecha actual de operacion
          $rs = $db->fetch_array($db->select("select REPLACE(CONVERT(NVARCHAR, getdate(), 103), ' ', '/') as fecha"));
          $fechaHoy = utf8_encode($rs["fecha"]);

          $rpta = array("fecha"=>$fechaHoy);
          echo json_encode($rpta);
          break;
        case "ExtrBancario":
          $tabla = array();
          $whr = ($data->prestahorroID>0) ? (" and id_prestahorro=".$data->prestahorroID) : ("");

          $sql = "select * from dbo.vw_oper_movimientos where id_tipo_oper=".$data->operacionID." and id_socio=".$data->socioID.$whr." order by fecha2";
          $qry = $db->select(utf8_decode($sql));

          if($db->has_rows($qry)){
            for($xx=0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $depositos = 0; $retiros = 0; $otros = 0;
              switch($rs["IO"]){
                case 0: $otros = $rs["importe"]; break;
                case 1: $depositos = $rs["importe"]; break;
                case 2: $retiros = $rs["importe"]; break;
              }
              $tabla[] = array(
                "agenciaID" => ($rs["id_agencia"]),
                "usuarioID" => ($rs["id_usuario"]),
                "tipomovID" => ($rs["id_tipo_mov"]),
                "fecha" => utf8_encode($rs["fecha"]),
                "numtrans" => utf8_encode($rs["num_trans"]),
                "detalle" => utf8_encode($rs["detalle"]),
                "depositos" => ($depositos),
                "retiros" => ($retiros),
                "otros" => ($otros)
              );
            }
          }
          echo json_encode($tabla);
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
