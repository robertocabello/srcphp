<?php include('includes/config.php');
include("class.phpmailer.php");
include('includes/dompdf/dompdf_config.inc.php');
include('ws/nusoap/lib/nusoap.php');

$archivo_log = "ws/logs/OC".date('Y-m-d').".log";
$registro_log = fopen($archivo_log, "a+");

$archivo_log_2 = "ws/logs/NV".date('Y-m-d').".log";
$registro_log_2 = fopen($archivo_log_2, "a+");

$token = $campos['token'];

if(!empty($token)){
    
    unset($_SESSION["cart_id"]);

    $sql = "SELECT * FROM seo_pedido where token = '".$token."' ";
    $result = $conexion->query($sql);
    $data_sql = $result->fetch_array(MYSQLI_BOTH);
    $existe_pedido = $result->num_rows;

    if($existe_pedido > 0){
        $id = $data_sql['id'];
        $total = $data_sql['total'];
        $fecha_add = $data_sql['fecha_add'];
        $status_pago = $data_sql['status_pago'];
        $metodo_pago = $data_sql['metodo_pago'];

        $nombre = $data_sql['nombre'];
        $email = $data_sql['email'];
        $telefono = $data_sql['telefono'];
        $razon_social = $data_sql['razon_social'];
        $rut = $data_sql['rut'];
        $giro = $data_sql['giro'];
        $direccion = $data_sql['direccion'];
        $comuna = $data_sql['comuna'];
        $ciudad = $data_sql['ciudad'];

        $notificacion_email = $data_sql['notificacion_email'];
        $oc_ws = $data_sql['oc_ws'];

        $formato_fecha = strftime("%d/%m/%Y", strtotime($fecha_add));

		$sql2 = "SELECT * FROM ".$prefix."proveedor WHERE id = '".$data_sql['id_proveedor']."' LIMIT 1";
		$result2 = $conexion->query($sql2);
		$data_sql2 = $result2->fetch_array(MYSQLI_BOTH);
    }else{
    	//
    }

    if($data_sql['status_pago'] == '1'){ $estado_pago = '<span style="color:#444;">PENDIENTE</span>'; }
    if($data_sql['status_pago'] == '2'){ $estado_pago = '<span style="color:#444;">PAGADO</span>'; }

    if($data_sql['metodo_pago'] == 'webpay plus'){ $metodo_pago = '<span style="color:#444;">Webpay plus</span>'; }
    if($data_sql['metodo_pago'] == 'credito'){ $metodo_pago = '<span style="color:#444;">Crédito</span>'; }


    if($oc_ws != 'SI'){
        $contador = 0;
        $contador2 = 0;
        $contador_lineas = 1;
        $productos_no_informados = 0;
        $cadena_no_informados = '';
        $ultimoId = 0;

        $id_pedido = $id;
        $id_cliente = $data_sql['id_proveedor'];

        $sql_cliente = "SELECT * FROM ".$prefix."proveedor WHERE id = '".$id_cliente."' LIMIT 1 ";
        $result_cliente = $conexion->query($sql_cliente);
        $data_sql_cliente = $result_cliente->fetch_array(MYSQLI_BOTH);

        $nombreFantasia = $data_sql_cliente['razon_social'];
        $comentario_NV = $nombreFantasia.' - '.date('Y-m-d');

        fwrite($registro_log, 'ID: '.$id_pedido."\n");
        fwrite($registro_log_2, 'ID: '.$id_pedido."\n");

        $fecha_pedido = strftime("%Y-%m-%d", strtotime($fecha_add));

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

        fwrite($registro_log, 'cadena_no_informados: '.$cadena_no_informados."\n\n");

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

            $cliente = new nusoap_client($url_wsdl, 'wsdl');

            $json = array(
                'pvarOrdenCompra' => $cadena,
                'pvarEmpresa' => 'TAVELLI15'
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

    if($notificacion_email != 'SI'){
        $sql_masivo = "SELECT id_proveedor FROM ".$prefix."carro_detalle WHERE token = '".$data_sql['token']."' GROUP BY id_proveedor ";
        $result_masivo = $conexion->query($sql_masivo);
        while($data_sql_while_masivo = $result_masivo->fetch_array(MYSQLI_BOTH)){
            $sql_info = "SELECT id, email FROM ".$prefix."proveedor WHERE id = '".$data_sql_while_masivo['id_proveedor']."' LIMIT 1 ";
            $result_info = $conexion->query($sql_info);
            $data_sql_info = $result_info->fetch_array(MYSQLI_BOTH);

            if($data_sql_info['email'] != ''){
                $body_html = '<html>
                <head>
                <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700,800,600,300" rel="stylesheet" type="text/css">
                    <style>
                        body { font-family: "Open Sans", sans-serif; color:#333333;}
                        h2 { font-size:14pt; color:#333333;}
                        table#info_cliente { width:100%;}
                        table#info_cliente tr{padding:0px;}
                        table#info_cliente tr p{padding:0px;margin:0px;}
                        table#info_cliente tr td:first-of-type { width:66%;}
                        table#info_cliente tr td:last-of-type { width:44%;}
                        table#info_cliente tr td table tr td { font-size:10pt; padding:0px 10px 0px 0px;}
                        .texto_01 { color:#006C67;}
                        .texto_02 { color:#EB7920;}
                        table#detalle { width:100%;}
                        table#detalle tr td { border-bottom:1px solid #333333; padding:5px 10px 5px 5px; font-size:9pt;}
                        table#detalle tr.border-none td { border-bottom:none; font-size:11pt; padding-bottom:3px; color:#333333;}
                        table#detalle tr td:last-of-type { padding-right:0px;}
                        table#detalle2 { width:100%; margin-left:240px;}
                        table#detalle2 tr td { border-bottom:1px solid #333333; padding:5px 10px 5px 5px; font-size:9pt;}
                        table#detalle2 tr.border-none td { border-bottom:none; font-size:11pt; padding-bottom:3px; color:#333333;}
                        table#detalle2 tr td:last-of-type { padding-right:0px;}
                        /*
                        - Para el header hay que aumentar 30 pixeles sobre el alto de la imagen, para separar más la cabecera del contenido
                        (Ej: Aquí la imagen del header mide 820 x 217 pixeles, así que quedará de 247 pixeles en @page(margin), #header(top- y height)
                        - Para el footer hay que aumentar 40 pixeles sobre el alto de la imagen
                        (ej: Aquí la imagen del footer mide 820 x 205 px., el valor de la propiedad bottom será de -245px) 
                        */
                        @page { margin: 179px 50px;}
                        #header { position: absolute; left: -50px; top: -179px; right: 0px; width:820px; height:179px; background:url("includes/dompdf/header2.png") 0px 0px no-repeat;} 
                        #footer { position: absolute; left: -50px; bottom: -245px; right: 0px; width:820px; height:205px; background:url("includes/dompdf/footer.jpg") 0px 0px no-repeat;}
                    </style>
                </head>
                <body>';

                $body_html .= '<div id="header"><img src="includes/dompdf/header2.png"></div>';

                $body_html .= '<div id="content">
                            

                <table border="0" cellspadding="0" cellspacing="0" id="info_cliente">
                    <tr>
                        <td valign="top">
                            <table border="0" cellspadding="0" cellspacing="0">
                                <tr><td><h2>Orden de compra Nº '.$data_sql['id'].'</h2></td></tr>
                                <tr><td><p><strong>Dirección:</strong> '.$data_sql['direccion'].'</p></td></tr>
                                <tr><td><p><strong>Comuna:</strong> '.$data_sql['comuna'].'</p></td></tr>
                                <tr><td><p><strong>Ciudad:</strong> '.$data_sql['ciudad'].'</p><br><br><br></td></tr>
                            </table>
                        </td>
                        <td valign="top">
                            <table border="0" cellspadding="0" cellspacing="0">
                                <tr>
                                    <td><br><br><strong>Fecha:</strong></td>
                                    <td><br><br>'.$data_sql['fecha_add'].'</td>
                                </tr>
                                <tr>
                                    <td><strong>Estado Pago:</strong></td>
                                    <td>'.$estado_pago.'</td>
                                </tr>
                                <tr>
                                    <td><strong>Método Pago:</strong></td>
                                    <td>'.$metodo_pago.'</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <table border="0" cellspadding="0" cellspacing="0" id="detalle">
                    <tr>
                        <td style="background:#404044;color:#ffffff;"><strong>Proveedor</strong></td>
                        <td style="background:#404044;color:#ffffff;"><strong>Código</strong></td>
                        <td style="background:#404044;color:#ffffff;"><strong>Producto</strong></td>
                        <td style="background:#404044;color:#ffffff;"><strong>Cantidad</strong></td>
                        <td style="background:#404044;color:#ffffff;"><strong>Precio Unitario</strong></td>
                        <td style="background:#404044;color:#ffffff;"><strong>Subtotal</strong></td>
                    </tr>';
                    $precio_por_proveedor = 0;
                    $sql = "SELECT cd.producto as producto, cd.cantidad as cantidad, cd.precio as precio_unitario, pro.razon_social as razon_social, prod.codigo as codigo, prod.unidad_medida as unidad_medida FROM ".$prefix."carro_detalle as cd, ".$prefix."proveedor as pro, ".$prefix."producto as prod WHERE cd.token = '".$data_sql['token']."' AND cd.id_proveedor = pro.id AND cd.id_producto = prod.id ";
                    $sql .= " AND pro.id = '".$data_sql_while_masivo["id_proveedor"]."' ";
                    $result = $conexion->query($sql);
                    while($data_sql_while = $result->fetch_array(MYSQLI_BOTH)){

                        $precio_unitario_item = formato_int($data_sql_while['precio_unitario']);
                        $precio_total_item = ($precio_unitario_item*$data_sql_while['cantidad']);

                        $precio_por_proveedor = $precio_por_proveedor + $precio_total_item;
                        $iva_por_proveedor = $precio_por_proveedor * 0.19;

                        $body_html .= '<tr>';
                        $body_html .= '<td>'.$data_sql_while['razon_social'].'</td>';
                        $body_html .= '<td>'.$data_sql_while['codigo'].'</td>';
                        $body_html .= '<td>'.$data_sql_while['producto'].'</td>';
                        $body_html .= '<td>'.$data_sql_while['cantidad'].' '.$data_sql_while['unidad_medida'].'</td>';
                        $body_html .= '<td>'.formato_precio($data_sql_while['precio_unitario']).'</td>';
                        $body_html .= '<td>'.formato_precio($data_sql_while['cantidad'] * formato_int($data_sql_while['precio_unitario'])).'</td>';
                        $body_html .= '</tr>';
                    }

                    $body_html .= '<tr><td colspan="4"></td><td><strong>IVA (19%): </strong></td><td><strong>'.formato_precio($iva_por_proveedor).'</strong></td></tr>';
                    $body_html .= '<tr><td colspan="4"></td><td><strong>TOTAL: </strong></td><td><strong>'.formato_precio($precio_por_proveedor).'</strong></td></tr>';

                $body_html .= '</table>';
                $body_html .= ' 
                        
                      </div>

                      <div id="footer">
                      <img src="includes/dompdf/footer.jpg">
                      <p class="page"></p>
                      </div>

                    </body>
                </html>';

                $dompdf = new DOMPDF();
                $dompdf->load_html($body_html);
                $dompdf->render();
                $pdf = $dompdf->output();
                $pdf_proveedor = 'pedidos/pedido'.$data_sql['token'].'-'.$data_sql_info['id'].'.pdf';
                $pdf_proveedor_title = $data_sql['token'].'-'.$data_sql_info['id'].'.pdf';

                file_put_contents('pedidos/pedido'.$data_sql['token'].'-'.$data_sql_info['id'].'.pdf', $pdf);

                $mail = new PHPMailer();
                //$mail->Host = "localhost";

                $mail->isSMTP();
                //$mail->SMTPDebug = 2;
                //$mail->Debugoutput='html';
                $mail->Host = 'smtp.gmail.com';
                $mail->Port = 465;
                $mail->SMTPSecure = 'ssl';
                $mail->SMTPAuth = true;
                $mail->Username   = $usuario_smtp;
                $mail->Password = $pass_smtp;

                $mail->From = $noreply;
                $mail->FromName = $dominio;
                $mail->CharSet = 'utf8';
                $mail->AddEmbeddedImage($url_logo, 'logo');
                $mail->Subject = "Nuevo Pedido Registrado";
                $mail->addAttachment($pdf_proveedor, $pdf_proveedor_title);
                $mail->AddAddress($data_sql_info['email']);
                //$mail->AddAddress('mpastor@webseo.cl');

                $message  = "<html><body>";
                $message .= "<table width='100%' bgcolor='#e0e0e0' cellpadding='0' cellspacing='0' border='0'>";
                $message .= "<tr><td>";
                $message .= "<table align='center' width='100%' border='0' cellpadding='0' cellspacing='0' style='max-width:650px; background-color:#fff; font-family:Verdana, Geneva, sans-serif;'>";
                $message .= "<thead>
                  <tr height='80'>
                  <th colspan='4' style='background-color:#424242; border-bottom:solid 1px #bdbdbd; font-family:Verdana, Geneva, sans-serif; color:#333; font-size:28px;padding-top:10px;padding-bottom:10px;' ><img src=\"cid:logo\" style='margin:0 auto;'></th>
                  </tr></thead>";
                $message .= "<tbody>";
                $message .= "<tr>
                    <td colspan='4' style='padding:15px;'>
                    <p style='font-size:20px;'>Orden de compra Nº ".$data_sql['id']."</p>
                    <hr />
                    <p style='font-size:22px;'>DETALLE</p>
                    <p style='font-size:15px; font-family:Verdana, Geneva, sans-serif;'>Dirección: <span>".$data_sql['direccion']."</span></p>
                    <p style='font-size:15px; font-family:Verdana, Geneva, sans-serif;'>Comuna: <span>".$data_sql['comuna']."</span></p>
                    <p style='font-size:15px; font-family:Verdana, Geneva, sans-serif;'>Ciudad: <span>".$data_sql['ciudad']."</span></p>
                    <p style='font-size:15px; font-family:Verdana, Geneva, sans-serif;'>IVA (19%): <span>".formato_precio($precio_por_proveedor * 0.19)."</span></p>
                    <p style='font-size:15px; font-family:Verdana, Geneva, sans-serif;'>Monto: <span>".formato_precio($precio_por_proveedor)."</span></p>
                    <hr />
                    </td>
                    </tr>
                    <tr>
                    <td colspan='4' style='padding:15px;'>
                    <p style='font-size:11px;color:#777777;'>Este email fue generado de manera automática.</p>
                    <p style='font-size:14px;'>Atte. ".$sitio."</p>
                    </td>
                    </tr>
                </tbody>";
                $message .= "</table>";
                $message .= "</td></tr>";
                $message .= "</table>";
                $message .= "</body></html>";
                $mail->Body = utf8_decode($message);
                $mail->IsHTML(true);
                $mail->Send();
            }
        }
    }

    $body_html = '<html>
    <head>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700,800,600,300" rel="stylesheet" type="text/css">
        <style>
            body { font-family: "Open Sans", sans-serif; color:#333333;}
            h2 { font-size:14pt; color:#333333;}
            table#info_cliente { width:100%;}
            table#info_cliente tr{padding:0px;}
            table#info_cliente tr p{padding:0px;margin:0px;}
            table#info_cliente tr td:first-of-type { width:66%;}
            table#info_cliente tr td:last-of-type { width:44%;}
            table#info_cliente tr td table tr td { font-size:10pt; padding:0px 10px 0px 0px;}
            .texto_01 { color:#006C67;}
            .texto_02 { color:#EB7920;}
            table#detalle { width:100%;}
            table#detalle tr td { border-bottom:1px solid #333333; padding:5px 10px 5px 5px; font-size:9pt;}
            table#detalle tr.border-none td { border-bottom:none; font-size:11pt; padding-bottom:3px; color:#333333;}
            table#detalle tr td:last-of-type { padding-right:0px;}
            table#detalle2 { width:100%; margin-left:240px;}
            table#detalle2 tr td { border-bottom:1px solid #333333; padding:5px 10px 5px 5px; font-size:9pt;}
            table#detalle2 tr.border-none td { border-bottom:none; font-size:11pt; padding-bottom:3px; color:#333333;}
            table#detalle2 tr td:last-of-type { padding-right:0px;}
            /*
            - Para el header hay que aumentar 30 pixeles sobre el alto de la imagen, para separar más la cabecera del contenido
            (Ej: Aquí la imagen del header mide 820 x 217 pixeles, así que quedará de 247 pixeles en @page(margin), #header(top- y height)
            - Para el footer hay que aumentar 40 pixeles sobre el alto de la imagen
            (ej: Aquí la imagen del footer mide 820 x 205 px., el valor de la propiedad bottom será de -245px) 
            */
            @page { margin: 179px 50px;}
            #header { position: absolute; left: -50px; top: -179px; right: 0px; width:820px; height:179px; background:url("includes/dompdf/header2.png") 0px 0px no-repeat;} 
            #footer { position: absolute; left: -50px; bottom: -245px; right: 0px; width:820px; height:205px; background:url("includes/dompdf/footer.jpg") 0px 0px no-repeat;}
        </style>
    </head>
    <body>';

    $body_html .= '<div id="header"><img src="includes/dompdf/header2.png"></div>';

    $body_html .= '<div id="content">
                

    <table border="0" cellspadding="0" cellspacing="0" id="info_cliente">
        <tr>
            <td valign="top">
                <table border="0" cellspadding="0" cellspacing="0">
                    <tr><td><h2>Orden de compra Nº '.$data_sql['id'].'</h2></td></tr>
                    <tr><td><p><strong>Dirección:</strong> '.$data_sql['direccion'].'</p></td></tr>
                    <tr><td><p><strong>Comuna:</strong> '.$data_sql['comuna'].'</p></td></tr>
                    <tr><td><p><strong>Ciudad:</strong> '.$data_sql['ciudad'].'</p><br><br><br></td></tr>
                </table>
            </td>
            <td valign="top">
                <table border="0" cellspadding="0" cellspacing="0">
                    <tr>
                        <td><br><br><strong>Fecha:</strong></td>
                        <td><br><br>'.$data_sql['fecha_add'].'</td>
                    </tr>
                    <tr>
                        <td><strong>Estado Pago:</strong></td>
                        <td>'.$estado_pago.'</td>
                    </tr>
                    <tr>
                        <td><strong>Método Pago:</strong></td>
                        <td>'.$metodo_pago.'</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table border="0" cellspadding="0" cellspacing="0" id="detalle">
        <tr>
            <td style="background:#404044;color:#ffffff;"><strong>Proveedor</strong></td>
            <td style="background:#404044;color:#ffffff;"><strong>Código</strong></td>
            <td style="background:#404044;color:#ffffff;"><strong>Producto</strong></td>
            <td style="background:#404044;color:#ffffff;"><strong>Cantidad</strong></td>
            <td style="background:#404044;color:#ffffff;"><strong>Precio Unitario</strong></td>
            <td style="background:#404044;color:#ffffff;"><strong>Subtotal</strong></td>
        </tr>';

        $sql = "SELECT cd.producto as producto, cd.cantidad as cantidad, cd.precio as precio_unitario, pro.razon_social as razon_social, prod.codigo as codigo, prod.unidad_medida as unidad_medida, prod.stock as stock FROM ".$prefix."carro_detalle as cd, ".$prefix."proveedor as pro, ".$prefix."producto as prod WHERE cd.token = '".$data_sql['token']."' AND cd.id_proveedor = pro.id AND cd.id_producto = prod.id ";
        if($_SESSION["admin_privilegio"] == 'proveedor'){
            $sql .= " AND pro.id = '".$_SESSION["admin_id"]."' ";
        }
        $result = $conexion->query($sql);
        while($data_sql_while = $result->fetch_array(MYSQLI_BOTH)){

            if($data_sql_while['stock'] != '-1'){
                $sql_update1 = "UPDATE ".$prefix."producto SET stock = stock-".$data_sql_while['cantidad']." WHERE codigo='".$data_sql_while['codigo']."' ";
                $conexion->query($sql_update1);
            }

            $body_html .= '<tr>';
            $body_html .= '<td>'.$data_sql_while['razon_social'].'</td>';
            $body_html .= '<td>'.$data_sql_while['codigo'].'</td>';
            $body_html .= '<td>'.$data_sql_while['producto'].'</td>';
            $body_html .= '<td>'.$data_sql_while['cantidad'].' '.$data_sql_while['unidad_medida'].'</td>';
            $body_html .= '<td>'.formato_precio($data_sql_while['precio_unitario']).'</td>';
            $body_html .= '<td>'.formato_precio($data_sql_while['cantidad'] * formato_int($data_sql_while['precio_unitario'])).'</td>';
            $body_html .= '</tr>';
        }

        $body_html .= '<tr><td colspan="5"></td><td><strong>IVA (19%): '.formato_precio($data_sql['total']*0.19).'</strong></td></tr>';
        $body_html .= '<tr><td colspan="5"></td><td><strong>TOTAL: '.formato_precio($data_sql['total']).'</strong></td></tr>';

    $body_html .= '</table>';
    $body_html .= ' 
            
          </div>

          <div id="footer">
          <img src="includes/dompdf/footer.jpg">
          <p class="page"></p>
          </div>

        </body>
    </html>';

    $dompdf = new DOMPDF();
    $dompdf->load_html($body_html);
    $dompdf->render();
    $pdf = $dompdf->output();
    $pdf_general = 'pedidos/pedido'.$data_sql['token'].'.pdf';
    $pdf_general_title = $data_sql['token'].'.pdf';

    file_put_contents('pedidos/pedido'.$data_sql['token'].'.pdf', $pdf);

    if($notificacion_email != 'SI'){
        $mail = new PHPMailer();
        //$mail->Host = "localhost";

        $mail->isSMTP();
        //$mail->SMTPDebug = 2;
        //$mail->Debugoutput='html';
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 465;
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->Username   = $usuario_smtp;
        $mail->Password = $pass_smtp;

        $mail->From = $noreply;
        $mail->FromName = $dominio;
        $mail->CharSet = 'utf8';
        $mail->AddEmbeddedImage($url_logo, 'logo');
        $mail->Subject = utf8_decode("Nuevo Pedido Registrado - Crédito");
        $mail->addAttachment($pdf_general, $pdf_general_title);
        $mail->AddAddress($data_sql['email']);
        //$mail->AddAddress('mpastor@webseo.cl');
        $mail->addBCC($email_empresa);

        $message  = "<html><body>";
        $message .= "<table width='100%' bgcolor='#e0e0e0' cellpadding='0' cellspacing='0' border='0'>";
        $message .= "<tr><td>";
        $message .= "<table align='center' width='100%' border='0' cellpadding='0' cellspacing='0' style='max-width:650px; background-color:#fff; font-family:Verdana, Geneva, sans-serif;'>";
        $message .= "<thead>
          <tr height='80'>
          <th colspan='4' style='background-color:#424242; border-bottom:solid 1px #bdbdbd; font-family:Verdana, Geneva, sans-serif; color:#333; font-size:28px;padding-top:10px;padding-bottom:10px;' ><img src=\"cid:logo\" style='margin:0 auto;'></th>
          </tr></thead>";
        $message .= "<tbody>";
        $message .= "<tr>
            <td colspan='4' style='padding:15px;'>
            <p style='font-size:20px;'>Orden de compra Nº ".$data_sql['id']."</p>
            <hr />
            <p style='font-size:22px;'>DETALLE</p>
            <p style='font-size:15px; font-family:Verdana, Geneva, sans-serif;'>Dirección: <span>".$data_sql['direccion']."</span></p>
            <p style='font-size:15px; font-family:Verdana, Geneva, sans-serif;'>Comuna: <span>".$data_sql['comuna']."</span></p>
            <p style='font-size:15px; font-family:Verdana, Geneva, sans-serif;'>Ciudad: <span>".$data_sql['ciudad']."</span></p>
            <p style='font-size:15px; font-family:Verdana, Geneva, sans-serif;'>IVA (19%): <span>".formato_precio($data_sql['total']*0.19)."</span></p>
            <p style='font-size:15px; font-family:Verdana, Geneva, sans-serif;'>Monto: <span>".formato_precio($data_sql['total'])."</span></p>
            <hr />
            </td>
            </tr>
            <tr>
            <td colspan='4' style='padding:15px;'>
            <p style='font-size:11px;color:#777777;'>Este email fue generado de manera automática.</p>
            <p style='font-size:14px;'>Atte. ".$sitio."</p>
            </td>
            </tr>
        </tbody>";
        $message .= "</table>";
        $message .= "</td></tr>";
        $message .= "</table>";
        $message .= "</body></html>";
        $mail->Body = utf8_decode($message);
        $mail->IsHTML(true);
        if($mail->Send()){
            $sql_update = "UPDATE ".$prefix."pedido SET notificacion_email = 'SI' WHERE id='".$id."' ";
            $conexion->query($sql_update);
        }
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
    <meta name="author" content="www.mavericks.cl">
    <base href="<?php echo $baseurl; ?>">

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
                        <li><span>Método de pago por venta de producto:</span> Crédito</li>
                        <li><span>Fecha de la transacción:</span> <?php echo $formato_fecha; ?></li>
                        <li><span>Monto:</span> <?php echo formato_precio($total); ?></li>
                        <br>
                        <li><strong><span>Crédito actual disponible:</span> <?php echo $data_sql2['credito']; ?></strong></li>
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