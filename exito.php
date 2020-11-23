<?php include('includes/config.php');
include('ws/nusoap/lib/nusoap.php');

$archivo_log = "ws/logs/OC".date('Y-m-d').".log";
$registro_log = fopen($archivo_log, "a+");

$archivo_log_2 = "ws/logs/NV".date('Y-m-d').".log";
$registro_log_2 = fopen($archivo_log_2, "a+");

$token_ws = $campos['token_ws'];

if(!empty($token_ws)){
    
    unset($_SESSION["cart_id"]);

    $sql = "SELECT * FROM seo_webpay where webpay_token = '".$token_ws."' ";
    $result = $conexion->query($sql);
    $data_sql_w = $result->fetch_array(MYSQLI_BOTH);
    $existe_transaccion = $result->num_rows;

    if($existe_transaccion > 0){
        $id_orden = $data_sql_w['id_webpay'];
        $code_webpay = $data_sql_w['code_webpay'];
        $token_carro = $data_sql_w['token_carro'];
        $total = $data_sql_w['total'];
        $date_add = $data_sql_w['date_add'];
        $status = $data_sql_w['status'];
        $numero_tarjeta = $data_sql_w['numero_tarjeta'];
        $expiracion_tarjeta = $data_sql_w['exp_tarjeta'];
        $codigo_autorizacion = $data_sql_w['codigo_autorizacion'];
        $codigo_tipo_pago = $data_sql_w['tipo_pago'];
        $numero_cuotas = $data_sql_w['numero_cuotas'];

        $nombre = $data_sql_w['nombre'];
        $email = $data_sql_w['email'];
        $telefono = $data_sql_w['telefono'];
        $razon_social = $data_sql_w['razon_social'];
        $rut = $data_sql_w['rut'];
        $giro = $data_sql_w['giro'];
        $direccion = $data_sql_w['direccion'];
        $comuna = $data_sql_w['comuna'];
        $ciudad = $data_sql_w['ciudad'];
                 
        if($codigo_tipo_pago == 'VN'){ $tipo_pago = "Crédito"; $tipo_cuotas="Sin Cuotas"; }
        if($codigo_tipo_pago == 'VC'){ $tipo_pago = "Crédito"; $tipo_cuotas="Cuotas normales"; }
        if($codigo_tipo_pago == 'SI'){ $tipo_pago = "Crédito"; $tipo_cuotas="Sin interés"; }
        if($codigo_tipo_pago == 'CI'){ $tipo_pago = "Crédito"; $tipo_cuotas="Cuotas Comercio"; }
        if($codigo_tipo_pago == 'VD'){ $tipo_pago = "Débito"; $tipo_cuotas="Venta Débito"; }

        $formato_fecha = strftime("%d/%m/%Y", strtotime($date_add));

        if($token_carro != ''){
            $sql_pedido = "SELECT * FROM seo_pedido where token = '".$token_carro."' ";
            $result_pedido = $conexion->query($sql_pedido);
            $data_sql = $result_pedido->fetch_array(MYSQLI_BOTH);

            if($data_sql['oc_ws'] != 'SI'){
                $contador = 0;
                $contador2 = 0;
                $contador_lineas = 1;
                $productos_no_informados = 0;
                $cadena_no_informados = '';
                $ultimoId = 0;

                $id_pedido = $data_sql['id'];
                $id_cliente = $data_sql['id_proveedor'];

                $sql_cliente = "SELECT * FROM ".$prefix."proveedor WHERE id = '".$id_cliente."' LIMIT 1 ";
                $result_cliente = $conexion->query($sql_cliente);
                $data_sql_cliente = $result_cliente->fetch_array(MYSQLI_BOTH);

                $nombreFantasia = $data_sql_cliente['razon_social'];
                $comentario_NV = $nombreFantasia.' - '.date('Y-m-d');

                fwrite($registro_log, 'ID: '.$id_pedido."\n");
                fwrite($registro_log_2, 'ID: '.$id_pedido."\n");

                $fecha_pedido = strftime("%Y-%m-%d", strtotime($data_sql['fecha_add']));

                $sql_2 = "SELECT * FROM ".$prefix."carro_detalle WHERE token = '".$data_sql['token']."' ";
                $result_2 = $conexion->query($sql_2);
                $qty_carro = $result_2->num_rows;
                while($data_sql_2 = $result_2->fetch_array(MYSQLI_BOTH)){

                    $id_producto = $data_sql_2['id_producto'];
                    $cantidad = $data_sql_2['cantidad'];
                    $precio_carro = $data_sql_2['precio'];

                    $sql_3 = "SELECT * FROM ".$prefix."producto WHERE id = '".$id_producto."' LIMIT 1 ";
                    $result_3 = $conexion->query($sql_3);
                    $data_sql_3 = $result_3->fetch_array(MYSQLI_BOTH);

                    if($data_sql_3['wsdl'] != 'SI'){
                        $sql_c = "SELECT * FROM ".$prefix."categorias WHERE id = '".$data_sql_3['categoria']."' LIMIT 1";
                        $result_c = $conexion->query($sql_c);
                        $data_categoria = $result_c->fetch_array(MYSQLI_BOTH);

                        $sql_s = "SELECT * FROM ".$prefix."subcategorias WHERE id = '".$data_sql_3['subcategoria']."' LIMIT 1";
                        $result_s = $conexion->query($sql_s);
                        $data_subcategoria = $result_s->fetch_array(MYSQLI_BOTH);

                        $cliente = new nusoap_client($url_wsdl, 'wsdl');

                        $json_producto = array(
                            'pvarCodProd' => $data_sql_3['codigo'],
                            'pvarNomProd' => $data_sql_3['producto'],
                            'pvarDesc2' => $data_sql_3['producto'],
                            'pvarCodGrupo' => $data_categoria['codigo'],
                            'pvarCodSubGrupo' => $data_subcategoria['codigo'],
                            'pvarUniMed' => $data_sql_3['unidad_medida'],
                            'pvarPrecio' => formato_int($data_sql_3['precio']),
                            'pvarMoneda' => '01',
                            'pvarInvent' => '0',
                            'pvarEmpresa' => $var_empresa_wsdl
                        );
                        $response_ws_producto['response'] = $cliente->call("fnProducto", $json_producto);
                        $cadena_respuesta_producto = $response_ws_producto['response']['fnProductoResult'];

                        if($cadena_respuesta_producto == 'CREADO' || $cadena_respuesta_producto == 'ACTUALIZADO'){
                            $update = "UPDATE ".$prefix."producto SET wsdl = 'SI', wsdl_respuesta = '".$cadena_respuesta_producto."', wsdl_fecha = '".date('Y-m-d G:i:s')."' WHERE id = '".$data_sql_3['id']."' ";
                            $conexion->query($update);
                        }else{
                            $productos_no_informados = $productos_no_informados + 1;

                            $update = "UPDATE ".$prefix."producto SET estado = 'inactivo', wsdl = 'NO', wsdl_respuesta = '".addslashes($cadena_respuesta_producto)."', wsdl_fecha = '".date('Y-m-d G:i:s')."' WHERE id = '".$data_sql_3['id']."' ";
                            $conexion->query($update);

                            $cadena_no_informados .= $data_sql_3['id'].' - '.$data_sql_3['producto'].'('.$data_sql_3['id_asociado'].');';
                        }
                    }

                    $titulo_producto = $data_sql_3['producto'];
                    $unidad_medida = $data_sql_3['unidad_medida'];
                    $precio_producto = $data_sql_3['precio'];

                    if(($precio_carro != $precio_producto) && ($precio_producto > $precio_carro)){
                        $precio_descuento = ($precio_producto - $precio_carro);
                        $calculo1 = round(($precio_carro * 100) / $precio_producto);
                        $porcentaje_descuento = (100 - $calculo1);
                    }else{
                        $precio_descuento = 0;
                        $porcentaje_descuento = 0;
                    }

                    if($qty_carro == 1){
                        ${"cadena".$contador_lineas} .= '{"NumWeb":"99'.$id_pedido.'","Cab_pvarCodAux":"'.$id_cliente.'","Cab_pvarFechaOC":"'.$fecha_pedido.'","Cab_pvarCodcc":"000","Cab_pvarContacto":"'.$id_cliente.'","Det_pvarcodProd":"'.$data_sql_3['codigo'].'","Det_pvarCantidad":"'.$cantidad.'","Det_pvarPrecioUnit":"'.$precio_carro.'","Det_pvarCodUmed":"'.$unidad_medida.'","Det_pvarPorcDesc01":"'.$porcentaje_descuento.'","Det_pvarValDescto01":"'.$precio_descuento.'","pvarUltimaLinea":"true"}';

                        ${"cadenanv".$contador_lineas} .= '{"NumWeb":"99'.$id_pedido.'","Cab_FechaVenta":"'.$fecha_pedido.'","Cab_nvObser":"'.$comentario_NV.'","Cab_CentroCosto":"001","Cab_pvarCodAux":"'.$id_cliente.'","Cab_NumOC":"replacenv","Cab_CondVta":"C03","Det_CodProd":"'.$data_sql_3['codigo'].'","Det_DetProd":"'.$titulo_producto.'","Det_nvCant":"'.$cantidad.'","Det_CodUMed":"'.$unidad_medida.'","Det_nvDesc01":"'.$precio_descuento.'","Det_nvDesc01P":"'.$porcentaje_descuento.'","Det_nvPrecio":"'.$precio_carro.'","Det_nvFecCompr":"'.$fecha_pedido.'","pvarUltimaLinea":"true"}';
                    
                        //[{"NumWeb":"999999","Cab_FechaVenta":"2019-11-19","Cab_nvObser":"Obs ecoomerce","Cab_CentroCosto":"001","Cab_pvarCodAux":"71","Cab_NumOC":"280","Cab_CondVta":"C03","Det_CodProd":"137","Det_DetProd":"MASA WRAPS","Det_nvCant":"1","Det_CodUMed":"UN","Det_nvDesc01":"0","Det_nvDesc01P":"0","Det_nvPrecio":"1404","Det_nvFecCompr":"2019-11-19","pvarUltimaLinea":"true"}]

                    }elseif($qty_carro > 1){
                        $contador = $contador + 1;
                        $contador2 = $contador2 + 1;

                        if($contador > 30){
                            $contador_lineas = $contador_lineas + 1;
                            $contador = 1;
                        }

                        if(($contador == 1)){
                            ${"cadena".$contador_lineas} .= '{"NumWeb":"99'.$id_pedido.'","Cab_pvarCodAux":"'.$id_cliente.'","Cab_pvarFechaOC":"'.$fecha_pedido.'","Cab_pvarCodcc":"000","Cab_pvarContacto":"'.$id_cliente.'","Det_pvarcodProd":"'.$data_sql_3['codigo'].'","Det_pvarCantidad":"'.$cantidad.'","Det_pvarPrecioUnit":"'.$precio_carro.'","Det_pvarCodUmed":"'.$unidad_medida.'","Det_pvarPorcDesc01":"'.$porcentaje_descuento.'","Det_pvarValDescto01":"'.$precio_descuento.'","pvarUltimaLinea":"false"}';
                            ${"cadenanv".$contador_lineas} .= '{"NumWeb":"99'.$id_pedido.'","Cab_FechaVenta":"'.$fecha_pedido.'","Cab_nvObser":"'.$comentario_NV.'","Cab_CentroCosto":"001","Cab_pvarCodAux":"'.$id_cliente.'","Cab_NumOC":"replacenv","Cab_CondVta":"C03","Det_CodProd":"'.$data_sql_3['codigo'].'","Det_DetProd":"'.$titulo_producto.'","Det_nvCant":"'.$cantidad.'","Det_CodUMed":"'.$unidad_medida.'","Det_nvDesc01":"'.$precio_descuento.'","Det_nvDesc01P":"'.$porcentaje_descuento.'","Det_nvPrecio":"'.$precio_carro.'","Det_nvFecCompr":"'.$fecha_pedido.'","pvarUltimaLinea":"false"}';
                            //echo 'CONTADOR: '.$contador.' CADENA: '.'"NumWeb":"'.$id_pedido.'","Cab_pvarCodAux":"'.$id_cliente.'","Cab_pvarFechaOC":"'.$fecha_pedido.'","Cab_pvarCodcc":"000","Cab_pvarContacto":"'.$id_cliente.'","Det_pvarcodProd":"'.$data_sql_3['codigo'].'","Det_pvarCantidad":"'.$cantidad.'","Det_pvarPrecioUnit":"'.$precio_carro.'","Det_pvarCodUmed":"'.$unidad_medida.'","Det_pvarPorcDesc01":"'.$porcentaje_descuento.'","Det_pvarValDescto01":"'.$precio_descuento.'","pvarUltimaLinea":"false"'.'<br>'.'<br>';
                        }elseif(($contador2 == $qty_carro) || ($contador == 30)){
                            ${"cadena".$contador_lineas} .= ',{"NumWeb":"99'.$id_pedido.'","Cab_pvarCodAux":"'.$id_cliente.'","Cab_pvarFechaOC":"'.$fecha_pedido.'","Cab_pvarCodcc":"000","Cab_pvarContacto":"'.$id_cliente.'","Det_pvarcodProd":"'.$data_sql_3['codigo'].'","Det_pvarCantidad":"'.$cantidad.'","Det_pvarPrecioUnit":"'.$precio_carro.'","Det_pvarCodUmed":"'.$unidad_medida.'","Det_pvarPorcDesc01":"'.$porcentaje_descuento.'","Det_pvarValDescto01":"'.$precio_descuento.'","pvarUltimaLinea":"true"}';
                            ${"cadenanv".$contador_lineas} .= ',{"NumWeb":"99'.$id_pedido.'","Cab_FechaVenta":"'.$fecha_pedido.'","Cab_nvObser":"'.$comentario_NV.'","Cab_CentroCosto":"001","Cab_pvarCodAux":"'.$id_cliente.'","Cab_NumOC":"replacenv","Cab_CondVta":"C03","Det_CodProd":"'.$data_sql_3['codigo'].'","Det_DetProd":"'.$titulo_producto.'","Det_nvCant":"'.$cantidad.'","Det_CodUMed":"'.$unidad_medida.'","Det_nvDesc01":"'.$precio_descuento.'","Det_nvDesc01P":"'.$porcentaje_descuento.'","Det_nvPrecio":"'.$precio_carro.'","Det_nvFecCompr":"'.$fecha_pedido.'","pvarUltimaLinea":"true"}';
                            //echo 'CONTADOR: '.$contador.' CADENA: '.',"NumWeb":"'.$id_pedido.'","Cab_pvarCodAux":"'.$id_cliente.'","Cab_pvarFechaOC":"'.$fecha_pedido.'","Cab_pvarCodcc":"000","Cab_pvarContacto":"'.$id_cliente.'","Det_pvarcodProd":"'.$data_sql_3['codigo'].'","Det_pvarCantidad":"'.$cantidad.'","Det_pvarPrecioUnit":"'.$precio_carro.'","Det_pvarCodUmed":"'.$unidad_medida.'","Det_pvarPorcDesc01":"'.$porcentaje_descuento.'","Det_pvarValDescto01":"'.$precio_descuento.'","pvarUltimaLinea":"true"'.'<br>'.'<br>';
                        }elseif(($contador < $qty_carro) && ($contador < 30)){
                            ${"cadena".$contador_lineas} .= ',{"NumWeb":"99'.$id_pedido.'","Cab_pvarCodAux":"'.$id_cliente.'","Cab_pvarFechaOC":"'.$fecha_pedido.'","Cab_pvarCodcc":"000","Cab_pvarContacto":"'.$id_cliente.'","Det_pvarcodProd":"'.$data_sql_3['codigo'].'","Det_pvarCantidad":"'.$cantidad.'","Det_pvarPrecioUnit":"'.$precio_carro.'","Det_pvarCodUmed":"'.$unidad_medida.'","Det_pvarPorcDesc01":"'.$porcentaje_descuento.'","Det_pvarValDescto01":"'.$precio_descuento.'","pvarUltimaLinea":"false"}';
                            ${"cadenanv".$contador_lineas} .= ',{"NumWeb":"99'.$id_pedido.'","Cab_FechaVenta":"'.$fecha_pedido.'","Cab_nvObser":"'.$comentario_NV.'","Cab_CentroCosto":"001","Cab_pvarCodAux":"'.$id_cliente.'","Cab_NumOC":"replacenv","Cab_CondVta":"C03","Det_CodProd":"'.$data_sql_3['codigo'].'","Det_DetProd":"'.$titulo_producto.'","Det_nvCant":"'.$cantidad.'","Det_CodUMed":"'.$unidad_medida.'","Det_nvDesc01":"'.$precio_descuento.'","Det_nvDesc01P":"'.$porcentaje_descuento.'","Det_nvPrecio":"'.$precio_carro.'","Det_nvFecCompr":"'.$fecha_pedido.'","pvarUltimaLinea":"false"}';
                            //echo 'CONTADOR: '.$contador.' CADENA: '.',"NumWeb":"'.$id_pedido.'","Cab_pvarCodAux":"'.$id_cliente.'","Cab_pvarFechaOC":"'.$fecha_pedido.'","Cab_pvarCodcc":"000","Cab_pvarContacto":"'.$id_cliente.'","Det_pvarcodProd":"'.$data_sql_3['codigo'].'","Det_pvarCantidad":"'.$cantidad.'","Det_pvarPrecioUnit":"'.$precio_carro.'","Det_pvarCodUmed":"'.$unidad_medida.'","Det_pvarPorcDesc01":"'.$porcentaje_descuento.'","Det_pvarValDescto01":"'.$precio_descuento.'","pvarUltimaLinea":"false"'.'<br>'.'<br>';
                        }
                    }

                }

                if($productos_no_informados > 0){
                    $sql_insert = array(
                        'id' => '',
                        'id_pedido' => $id_pedido,
                        'nvoc' => '0',
                        'respuesta_ws' => $cadena_no_informados,
                        'fecha' => date('Y-m-d G:i:s')
                    );
                    $sql_insert_ = ingresar_registros($prefix.'pedido_oc', $sql_insert);
                    $result_insert = $conexion->query($sql_insert_);
                }

                for($l = 1; $l <= $contador_lineas; $l++){
                    $cadena = '['.${"cadena".$l}.']';
                    $cadenanv = '['.${"cadenanv".$l}.']';

                    fwrite($registro_log, $cadena."\n\n");
                    //fwrite($registro_log_2, $cadenanv."\n\n");

                    $cliente = new nusoap_client($url_wsdl, 'wsdl');

                    $json = array(
                        'pvarOrdenCompra' => $cadena,
                        'pvarEmpresa' => $var_empresa_wsdl
                        );

                    $result_ws['response'] = $cliente->call("fnOrdenCompraMasiva", $json);
                    $cadena_respuesta_ = $result_ws['response']['fnOrdenCompraMasivaResult'];

                    fwrite($registro_log, 'Respuesta: '.$cadena_respuesta_."\n\n");
                    
                    $cadena_respuesta = str_replace("[", "", $cadena_respuesta_);
                    $cadena_respuesta = str_replace("]", "", $cadena_respuesta);
                    $cadena_respuesta = str_replace("'", "", $cadena_respuesta);
                    $cadena_respuesta = str_replace("{", "", $cadena_respuesta);
                    $cadena_respuesta = str_replace("}", "", $cadena_respuesta);

                    $expl_respuesta = explode(",", $cadena_respuesta);

                    for ($i = 0; $i < count($expl_respuesta); $i++) {
                        if($i == 0){ $valor_insert1 = $expl_respuesta[0]; }
                        if($i == 1){ $valor_insert2 = $expl_respuesta[1]; }
                    }

                    $oc_id = explode(":", $valor_insert2);

                    $sql_insert = array(
                        'id' => '',
                        'id_pedido' => $id_pedido,
                        'nvoc' => $valor_insert2,
                        'respuesta_ws' => $cadena_respuesta,
                        'fecha' => date('Y-m-d G:i:s'),
                        'cadena' => '',
                        'estatus' => 'generada'
                    );
                    $sql_insert_ = ingresar_registros($prefix.'pedido_oc', $sql_insert);
                    if($result_insert = $conexion->query($sql_insert_)){
                        $id_generado = $conexion->insert_id;
                        $ultimoId = $id_generado;

                        if($id_generado > 0 && $oc_id[1] > 0){
                            $sql_update = "UPDATE ".$prefix."pedido SET oc_ws = 'SI' WHERE id='".$id_pedido."' ";
                            $conexion->query($sql_update);
                        }
                    }
                }

            }

            if($ultimoId > 0){
                fwrite($registro_log_2, 'ultimoId: '.$ultimoId."\n");

                $sql_4 = "SELECT * FROM ".$prefix."pedido_oc WHERE id = '".$ultimoId."' LIMIT 1 ";
                $result_4 = $conexion->query($sql_4);
                $data_sql_4 = $result_4->fetch_array(MYSQLI_BOTH);
                if($data_sql_4['nvoc'] != ''){
                    $oc_id = explode(":", $data_sql_4['nvoc']);
                    if($oc_id[1] > 0){
                        fwrite($registro_log_2, 'OC: '.$oc_id[1]."\n\n");

                        for($l = 1; $l <= $contador_lineas; $l++){
                            $cadenanv = '['.${"cadenanv".$l}.']';
                            $cadenanv_ = str_replace('replacenv', $oc_id[1], $cadenanv);

                            fwrite($registro_log_2, $cadenanv_."\n\n");

                            $cliente = new nusoap_client($url_wsdl, 'wsdl');

                            $json = array(
                                'pvarNotaVenta' => $cadenanv_,
                                'pvarEmpresa' => $var_empresa_wsdl
                                );

                            $result_ws_2['response'] = $cliente->call("fnNotaVentaMasiva", $json);
                            $cadena_respuesta_ = $result_ws_2['response']['fnNotaVentaMasivaResult'];

                            fwrite($registro_log_2, 'Respuesta: '.$cadena_respuesta_."\n\n");
                            
                            $cadena_respuesta = str_replace("[", "", $cadena_respuesta_);
                            $cadena_respuesta = str_replace("]", "", $cadena_respuesta);
                            $cadena_respuesta = str_replace("'", "", $cadena_respuesta);
                            $cadena_respuesta = str_replace("{", "", $cadena_respuesta);
                            $cadena_respuesta = str_replace("}", "", $cadena_respuesta);

                            $expl_respuesta = explode(",", $cadena_respuesta);

                            for ($i = 0; $i < count($expl_respuesta); $i++) {
                                if($i == 0){ $valor_insert1 = $expl_respuesta[0]; }
                                if($i == 1){ $valor_insert2 = $expl_respuesta[1]; }
                            }

                            $nv_id = explode(":", $valor_insert2);

                            $sql_insert_2 = array(
                                'id' => '',
                                'id_pedido' => $id_pedido,
                                'nvnum' => $valor_insert2,
                                'respuesta_ws' => $cadena_respuesta,
                                'fecha' => date('Y-m-d G:i:s'),
                                'cadena' => '',
                                'estatus' => 'generada'
                            );
                            $sql_insert_2_ = ingresar_registros($prefix.'pedido_nv', $sql_insert_2);
                            if($result_insert = $conexion->query($sql_insert_2_)){
                                $id_generado_2 = $conexion->insert_id;

                                if($id_generado_2 > 0 && $nv_id[1] > 0){
                                    $sql_update = "UPDATE ".$prefix."pedido SET nv_ws = 'SI' WHERE id='".$id_pedido."' ";
                                    $conexion->query($sql_update);
                                }
                            }
                        }

                    }
                }
            }
            /*here*/
        }
        
    }else{

    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1">
    <meta name="description" content="">

    <title>Transacción Exitosa | <?php echo $sitio; ?></title>

    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico">
    <!-- Vendor CSS -->
    <link href="js/vendor/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="js/vendor/slick/slick.min.css" rel="stylesheet">
    <link href="js/vendor/fancybox/jquery.fancybox.min.css" rel="stylesheet">
    <link href="js/vendor/animate/animate.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="css/style-light.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/style-responsive.css" rel="stylesheet">
    <!--icon font-->
    <link href="fonts/icomoon/icomoon.css" rel="stylesheet">
    <!--custom font-->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i" rel="stylesheet">
</head>

<body class="home-page is-dropdn-click has-slider">
    
<?php include('header.php'); ?>

<div class="page-content">
    <div class="holder mt-0 holder-gris">
        <div class="container">
            <div class="row justify-content-around informacion-pago box-cart">
                <div class="col-sm-12 col-md-12">
                    
                    <h1>Transacción Exitosa</h1>
                    <h4>Su pago ha sido confirmado con éxito</h4>
                
                    <div class="clearfix">
                      <ul class="simple-list margin-top-20">
                        <li><span>Método de pago por venta de producto:</span> Webpay</li>
                        <li><span>Tipo de pago realizado:</span> <?php echo $tipo_pago; ?></li>
                        <li><span>4 Últimos dígitos de la tarjeta:</span> <?php echo $numero_tarjeta; ?></li>
                        <li><span>Fecha de la transacción:</span> <?php echo $formato_fecha; ?></li>
                        <li><span>Código de autorización:</span> <?php echo $codigo_autorizacion; ?></li>
                        <li><span>Tipo de transacción:</span> Venta</li>
                        <li><span>Monto:</span> <?php echo formato_precio($total); ?></li>
                        <li><span>Número de cuotas:</span> <?php echo $numero_cuotas; ?></li>
                        <li><span>Tipo de cuotas:</span> <?php echo $tipo_cuotas; ?></li>
                      </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>

<?php include('includes/scripts.php'); ?>

</body>
</html>