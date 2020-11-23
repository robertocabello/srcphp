<?php include('includes/config.php');

$existe_precio_especial = 0;
$total_carro_actual = 0;
$cuenta_no_disponible = '0';

if(!empty($_SESSION["admin_privilegio"]) && $_SESSION["admin_privilegio"] != 'administrador'){

	$sql = "SELECT prov.direccion as direccion, prov.estado_comprador as estado_comprador, prov.comuna as codigo_comuna, com.comuna as comuna, ciu.ciudad as ciudad, prov.credito as credito FROM ".$prefix."proveedor as prov, ".$prefix."comunas as com, ".$prefix."ciudades as ciu WHERE prov.id = '".$_SESSION["admin_id"]."' AND prov.comuna = com.codigo AND prov.ciudad = ciu.codigo LIMIT 1";
	$result = $conexion->query($sql);
	$data_cuenta_usuario = $result->fetch_array(MYSQLI_BOTH);

	if($data_cuenta_usuario['estado_comprador'] != 'activo'){
		$cuenta_no_disponible = '1';
	}

}elseif(isset($_SESSION["admin_privilegio"]) && $_SESSION["admin_privilegio"] == 'administrador'){
	$cuenta_no_disponible = '1';
}elseif(isset($_SESSION["admin_privilegio"]) && $_SESSION["admin_privilegio"] == 'proveedor'){
    $cuenta_no_disponible = '1';
}

if(!empty($_SESSION["cart_id"])){
    $sql = "SELECT * FROM ".$prefix."carro_detalle WHERE token = '".$_SESSION["cart_id"]."' ";
    $result = $conexion->query($sql);
    while($carro_actual = $result->fetch_array(MYSQLI_BOTH)){
        $sql2 = "SELECT * FROM ".$prefix."producto WHERE id = '".$carro_actual['id_producto']."' AND estado = 'activo' LIMIT 1";
        $result2 = $conexion->query($sql2);
        $data_producto = $result2->fetch_array(MYSQLI_BOTH);

        if(!empty($_SESSION["admin_id"])){
            $sql3 = "SELECT * FROM ".$prefix."producto_precio WHERE id_producto = '".$carro_actual['id_producto']."' AND id_comprador = '".$_SESSION["admin_id"]."' LIMIT 1 ";
            $result3 = $conexion->query($sql3);
            $existe_precio_especial = $result3->num_rows;
            $data_precio_especial = $result3->fetch_array(MYSQLI_BOTH);
        }

        if($existe_precio_especial > 0){
            $precio_publicado = ((formato_int($data_precio_especial['precio']) * $margen) + formato_int($data_precio_especial['precio']));
        }else{
            $precio_publicado = ((formato_int($data_producto['precio']) * $margen) + formato_int($data_producto['precio']));
        }

        $total_carro_actual = $total_carro_actual + ($precio_publicado * $carro_actual['cantidad']);
        $total_iva_actual = ($total_carro_actual*0.19);
        $total_bruto_actual = ($total_carro_actual + $total_iva_actual);
    }
}

//$alerta_activa = '0';

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

    <title>Carro de Compra - <?php echo $sitio; ?></title>

    <link rel="apple-touch-icon" sizes="57x57" href="favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png">
    <link rel="manifest" href="favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#403f44">
    
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
    <style type="text/css">
    .card-total .cart-total-iva {font-weight: bold;float: right;}
    .box-cart.cart-bottom .card-total:nth-child(2){color: #000000;}
    </style>
</head>

<body class="home-page is-dropdn-click has-slider">
    
    <?php include('header.php'); ?>

    <div class="page-content">
        <div class="holder mt-0 holder-gris">
            <div class="container">
                <div class="box-cart">
                    <div class="row">
                        <div class="col-md-6">
                            <h1 class="text-left h1-style-cart">Tu carro de compras</h1>
                        </div>
                        <div class="col-md-6">
                            <div class="supplyme-alert">
                                <!--<?php
                                $contador2 = 0;
                                $sql = "SELECT * FROM ".$prefix."carro_detalle WHERE token = '".$_SESSION["cart_id"]."' GROUP BY id_proveedor ";
                                $result = $conexion->query($sql);
                                $qty_proveedores = $result->num_rows;
                                if($qty_proveedores > 0){
                                  while($data_sql_while = $result->fetch_array(MYSQLI_BOTH)){ $contador2 = $contador2 + 1;
                                    $sql2 = "SELECT * FROM ".$prefix."proveedor WHERE id = '".$data_sql_while['id_proveedor']."' LIMIT 1 ";
                                    $result2 = $conexion->query($sql2);
                                    $data_proveedor = $result2->fetch_array(MYSQLI_BOTH);

                                    $pedido_minimo_por_proveedor = formato_int($data_proveedor['pedido_minimo']);
                                    echo 'pedido minimo: '.$pedido_minimo_por_proveedor;

                                    if($pedido_minimo_por_proveedor != ''){

                                    $precio_por_proveedor = 0;
                                    $sql3 = "SELECT id, token, id_producto, producto, SUM(cantidad) as suma_cantidad, precio, id_proveedor, fecha_add FROM ".$prefix."carro_detalle WHERE token = '".$_SESSION["cart_id"]."' AND id_proveedor = '".$data_sql_while['id_proveedor']."' GROUP BY id_producto ";
                                    $result3 = $conexion->query($sql3);
                                    while($data_productos = $result3->fetch_array(MYSQLI_BOTH)){
                                        $sql4 = "SELECT * FROM ".$prefix."producto WHERE id = '".$data_productos['id_producto']."' AND estado = 'activo' LIMIT 1";
                                        $result4 = $conexion->query($sql4);
                                        $data_producto = $result4->fetch_array(MYSQLI_BOTH);

                                        $precio_unitario_item = formato_int($data_producto['precio']);

                                        $precio_total_item = ($precio_unitario_item*$data_productos['suma_cantidad']);
                                        echo $precio_total_item;

                                        $precio_por_proveedor = $precio_por_proveedor + $precio_total_item;
                                    }

                                    if($pedido_minimo_por_proveedor > $precio_por_proveedor){
                                        $alerta_activa = '1';

                                        $diferencial = ($pedido_minimo_por_proveedor - $precio_por_proveedor);
                                        echo '<p>Para el proveedor <strong>'.$data_proveedor['razon_social'].' te faltan '.formato_precio($diferencial).'</strong> para completar el pedido mínimo</p>';
                                    }

                                    ?>

                                <?php } ?>

                                <?php } ?>

                                <?php } ?>-->
                                <!--<p>Para el proveedor <strong>San Jorge te faltan $12.900</strong> para solicitar el pedido mínimo</p>-->
                            </div>
                        </div>
                    </div>
                    
                    <?php
                    if($cuenta_no_disponible != '1'){
                        $contador = 0;
                    	$sql = "SELECT * FROM ".$prefix."carro_detalle WHERE token = '".$_SESSION["cart_id"]."' GROUP BY id_proveedor ";
			            $result = $conexion->query($sql);
			            $qty_proveedores = $result->num_rows;
			            if($qty_proveedores > 0){
                    ?>
                    <ul class="cart-supplier-list">
                        <?php while($data_sql = $result->fetch_array(MYSQLI_BOTH)){ $contador = $contador + 1;
                        	$sql2 = "SELECT * FROM ".$prefix."proveedor WHERE id = '".$data_sql['id_proveedor']."' AND estado = 'activo' ";
			            	$result2 = $conexion->query($sql2);
			            	$qty_proveedor = $result2->num_rows;
			            	$data_proveedor = $result2->fetch_array(MYSQLI_BOTH);
			            	if($qty_proveedor > 0){
                        ?>
                        	<li <?php if($contador == 1){ ?>class="active"<?php } ?> data-supplier="<?php echo $data_sql['id_proveedor']; ?>"><?php echo $data_proveedor['razon_social']; ?></li>
                        <?php } } ?>
                    </ul>
                    <?php } ?>

                    <?php if($total_carro_actual > 0){ ?>
                    <div class="cart-table">
                        <div class="cart-table-prd table-thead">
                            <div class="cart-table-prd-name">Producto</div>
                            <div class="cart-table-prd-qty">Cantidad</div>
                            <div class="cart-table-prd-price">Precio Unidad</div>
                            <div class="cart-table-prd-price">Total</div>
                            <div class="cart-table-prd-action">Acción</div>
                        </div>
                        <div class="contenido-html">
                        	<?php
                        		$contador2 = 0;
		                    	$sql = "SELECT * FROM ".$prefix."carro_detalle WHERE token = '".$_SESSION["cart_id"]."' GROUP BY id_proveedor ";
					            $result = $conexion->query($sql);
					            $qty_proveedores = $result->num_rows;
					            if($qty_proveedores > 0){
					            	while($data_sql = $result->fetch_array(MYSQLI_BOTH)){ $contador2 = $contador2 + 1;
					            		$sql2 = "SELECT * FROM ".$prefix."proveedor WHERE id = '".$data_sql['id_proveedor']."' LIMIT 1 ";
						            	$result2 = $conexion->query($sql2);
						            	$data_proveedor = $result2->fetch_array(MYSQLI_BOTH);
		                    ?>
                            <div class="contenido-supplier<?php echo $data_sql['id_proveedor']; ?> <?php if($contador2 > 1){ ?>opc-hidden<?php } ?>">
                            	<?php
			                    	$sql2 = "SELECT id, token, id_producto, producto, SUM(cantidad) as suma_cantidad, precio, id_proveedor, fecha_add FROM ".$prefix."carro_detalle WHERE token = '".$_SESSION["cart_id"]."' AND id_proveedor = '".$data_sql['id_proveedor']."' GROUP BY id_producto ";
						            $result2 = $conexion->query($sql2);
						            while($data_productos = $result2->fetch_array(MYSQLI_BOTH)){
						            	$sql3 = "SELECT * FROM ".$prefix."producto WHERE id = '".$data_productos['id_producto']."' AND estado = 'activo' LIMIT 1";
                                    	$result3 = $conexion->query($sql3);
                                    	$data_producto = $result3->fetch_array(MYSQLI_BOTH);

                                    	$sql4 = "SELECT * FROM ".$prefix."producto_imagen WHERE token = '".$data_producto['token']."' AND portada = 'SI' LIMIT 1 ";
                                        $result4 = $conexion->query($sql4);
                                        $data_imagen_portada = $result4->fetch_array(MYSQLI_BOTH);
                                        if($data_imagen_portada['archivo'] == ''){
                                            $sql5 = "SELECT * FROM ".$prefix."producto_imagen WHERE token = '".$data_producto['token']."' ORDER BY RAND() LIMIT 1 ";
                                            $result5 = $conexion->query($sql5);
                                            $data_imagen_portada = $result5->fetch_array(MYSQLI_BOTH);
                                        }

                                        if(!empty($_SESSION["admin_id"])){
                                            $sql6 = "SELECT * FROM ".$prefix."producto_precio WHERE id_producto = '".$data_productos['id_producto']."' AND id_comprador = '".$_SESSION["admin_id"]."' LIMIT 1 ";
                                            $result6 = $conexion->query($sql6);
                                            $existe_precio_especial = $result6->num_rows;
                                            $data_precio_especial = $result6->fetch_array(MYSQLI_BOTH);
                                        }

                                        $url = 'producto/'.$data_producto['token'].'/'.generateSeoURL($data_productos['producto']).'.html';

                                        $cadena = encrypt('registro-'.$data_productos['id_producto'], $sitio);

                                        //$data_precio = str_replace("$ ", "", $data_producto['precio']);
                						//$data_precio = str_replace(".", "", $data_precio);

                                        if($data_producto['stock'] == '-1'){
                                            $item_qty_actual = $data_productos['suma_cantidad'];
                                            $item_max = '10000000';
                                        }else{
                    						if($data_producto['stock'] < $data_productos['suma_cantidad']){
                    							$item_qty_actual = $data_producto['stock'];
                    						}else{
                    							$item_qty_actual = $data_productos['suma_cantidad'];
                    						}
                                            $item_max = $data_producto['stock'];
                                        }

                                        if($existe_precio_especial > 0){
                                            $precio_publicado = ((formato_int($data_precio_especial['precio']) * $margen) + formato_int($data_precio_especial['precio']));
                                        }else{
                                            $precio_publicado = ((formato_int($data_producto['precio']) * $margen) + formato_int($data_producto['precio']));
                                        }
			                    ?>
                                <div class="cart-table-prd">
                                    <div class="cart-table-prd-name">
                                        <a href="<?php echo $url; ?>">
                                        <?php if($data_imagen_portada['archivo'] != ''){ ?>
                                            <img src="<?php echo $baseurl; ?>/supplyme-images/productos/<?php echo $data_imagen_portada['archivo']; ?>" alt="<?php echo $data_productos['producto']; ?>" width="70">
                                        <?php }else{ ?>
                                            <img src="<?php echo $baseurl; ?>/supplyme-images/default_img.jpg" alt="<?php echo $data_productos['producto']; ?>" width="70">
                                        <?php } ?>    
                                        </a>
                                        <h2><a href="<?php echo $url; ?>"><?php echo $data_productos['producto']; ?></a></h2>
                                    </div>
                                    <div class="cart-table-prd-qty">
                                        <span class="label-mobile hide-desktop">Cantidad:</span>
                                        <div class="qty qty-changer">
                                            <fieldset>
                                                <input type="button" value="&#8210;" class="decrease">
                                                <input type="text" class="qty-input" value="<?php echo $item_qty_actual; ?>" data-item="<?php echo $cadena; ?>" data-min="1" data-max="<?php echo $item_max; ?>">
                                                <input type="button" value="+" class="increase">
                                            </fieldset>
                                        </div>

                                        <!--<span><?php echo $data_productos['suma_cantidad']; ?></span>
                                        <div class="qty-move">
                                            <i class="icon-angle-up"></i>
                                            <i class="icon-angle-down"></i>
                                        </div>-->
                                    </div>
                                    <div class="cart-table-prd-price"><span class="label-mobile hide-desktop">Precio Unitario:</span><span class="precio_unitario"><?php echo formato_precio($precio_publicado); ?></span></div>
                                    <div class="cart-table-prd-price"><span class="label-mobile hide-desktop">Total:</span><span class="precio_linea"><?php echo formato_precio($precio_publicado * $data_productos['suma_cantidad']); ?></span></div>
                                    <div class="cart-table-prd-action"><a data-id="<?php echo $cadena; ?>" data-accion="delete"><i class="icon-cross"></i> Eliminar</a></div>
                                </div>
                                <?php } ?>
                                <div class="cart-table-prd cart-table-days padding-top-20">
                                    <div>
                                    	<?php
                                    		$string_dias = '';
                                    		$dias = array(
							                    'LU'=>'lunes',
							                    'MA'=>'martes',
							                    'MIE'=>'miercoles',
							                    'JUE'=>'jueves',
							                    'VIE'=>'viernes',
							                    'SA'=>'sabado',
							                    'DO'=>'domingo',
							                );

							                $dias_entrega = explode(",", $data_proveedor['dias_entrega']);
                                    	?>
                                        <span>Días de reparto del proveedor</span>
                                        <span><?php
	                                        foreach($dias_entrega as $dia){
							                	$clave = array_search($dia, $dias);
							                	if($clave != ''){
							                		$string_dias .= $clave.' - ';
							                	}
							                }
							                $string_dias = trim($string_dias, ' - ');
                                        ?>
                                        <?php if($string_dias != ''){ echo $string_dias; }else{ echo '<span style="color:red;">Sin reparto disponible</span>'; } ?>
                                        </span>
                                    </div>
                                    <?php
                                        $sql7 = "SELECT * FROM ".$prefix."opciones WHERE id = '".$data_proveedor['horario_corte']."' LIMIT 1 ";
                                        $result7 = $conexion->query($sql7);
                                        $data_opciones = $result7->fetch_array(MYSQLI_BOTH);
                                    ?>
                                    <div class="data-horario-corte">
                                        <span>Horario de Corte Pedidos</span>
                                        <span><?php echo $data_opciones['valor']; ?></span>
                                    </div>
                                </div>
                            </div>
                            <?php } } ?>
                        </div>
                    </div>
                    <?php }else{ ?>
                    	<h4>Carro vacío</h4>
                    <?php } }else{ ?>
                    	<h4>Carro no disponible <?php if($_SESSION["admin_privilegio"] == 'administrador'){ ?>para Administrador de Sistema.<?php } ?></h4>
                    <?php } ?>
                </div>

                <?php if(empty($_SESSION["admin_privilegio"]) && $total_carro_actual > 0){ ?>
                <div class="mt-3 mt-lg-5 box-login">
                    <div id="loginForm" class="col-md-4" style="margin: 0 auto;">
                        <h2 class="text-center">Ingresa a tu cuenta</h2>
                        <div class="form-wrapper">
                            <!--<p>If you have an account with us, please log in.</p>-->
                            <form id="loginFormulario" method="post">
                                <input name="ruta_carro" value="1" type="hidden">
                                <div class="form-group">
                                    <span class="icon-group"><i class="icon-person-fill"></i></span>
                                    <input type="email" class="form-control" name="email" placeholder="E-mail">
                                </div>
                                <div class="form-group">
                                    <span class="icon-group"><i class="icon-lock"></i></span>
                                    <input type="password" class="form-control" name="password" placeholder="Contraseña">
                                </div>

                                <button type="submit" class="btn btn--full btn--lg margin-top-20 btn-submit-form">Ingresa</button>
                                <div class="margin-top-30">
                                    <p class="text-center"><a href="#" class="js-toggle-forms">¿Olvidaste tu contraseña?</a></p>
                                </div>
                                <div class="margin-top-30">
                                    <p class="text-center"><a href="registro.html" style="color:#403f44;">Crear una cuenta</a></p>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div id="recoverPasswordForm" class="d-none">
                        <h2 class="text-center">Recupera tu contraseña</h2>
                        <div class="form-wrapper">
                            <p class="text-center">Te enviaremos un email para restablecer tu acceso.</p>
                            <form action="#">
                                <div class="form-group">
                                    <span class="icon-group"><i class="icon-person-fill"></i></span>
                                    <input type="email" class="form-control" placeholder="E-mail">
                                </div>
                                <div class="btn-toolbar margin-top-30">
                                    <a href="#" class="btn btn--alt btn--full btn--lg js-toggle-forms">Volver</a>
                                    <button class="btn btn--full btn--lg">Enviar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php } ?>

                <?php if($total_carro_actual > 0 && $cuenta_no_disponible != '1' && !empty($_SESSION["admin_privilegio"])){ ?>
                <div class="mt-3 mt-lg-5">
                    <div class="box-cart cart-bottom">
                        <div class="row vert-margin">

                            <div class="col-xs-12 col-sm-6 col-md-8 col-lg-8">
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
                                        <div class="margin-bottom-40">
                                            <label>Agrega una nota a tu compra</label>
                                            <textarea class="form-control textarea--height-100" name="nota_pedido"></textarea>
                                        </div>
                                        <div class="address-delivery">
                                            <label>Dirección de entrega</label>
                                            <p>
                                                <?php echo $data_cuenta_usuario['direccion']; ?>
                                                <!--<a href="#"><i class="fa fa-plus"></i> Añadir dirección</a>-->
                                            </p>
                                            <span>* Fecha de reparto en días festivos están sujetos a confirmaciones</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                <div class="card-total margin-bottom-20">
                                    Subtotal <span class="card-total-price"><?php echo formato_precio($total_carro_actual); ?></span>
                                </div>
                                <div class="card-total margin-bottom-20">
                                    IVA 19% <span class="cart-total-iva"><?php echo formato_precio($total_iva_actual); ?></span>
                                </div>
                                <!--<div class="card-total margin-bottom-20">
                                    Dscto cliente<br>preferente <span class="card-total-price">$294.000</span>
                                </div>-->
                                <div class="topline margin-bottom-20 padding-top-30 padding-bottom-30">
                                    <span class="cart-total-price"><?php echo formato_precio($total_bruto_actual); ?></span>
                                </div>
                                <!--<div class="card-total">
                                    <div class="delivery-price margin-bottom-20">
                                        <span>Pagar costo despacho Guallarauco</span> <span class="card-total-price">$12.000</span>
                                    </div>
                                </div>-->
                                <!--<button class="btn btn--full btn--lg margin-bottom-20">Pago Crédito</button>-->
                                <!--<button class="btn btn--full btn--lg btn--transparent">Otro medio de pago</button>-->
                                <button class="btn btn--full btn--lg margin-bottom-20 btn--pago" data-tipo="1" data-payment="webpayplus">Pago Webpay Plus <br> <img src="https://supplyme.webecommerce.cl/supplyme-images/pago-webpay.png" width="60"></button>
                                <?php if($data_cuenta_usuario['credito'] != '0' && $data_cuenta_usuario['credito'] != '$ 0'){ ?>
                                    <button class="btn btn--full btn--lg margin-bottom-20 btn--pago" data-tipo="2" data-payment="credito">Pago Crédito <br> Disponible <?php echo $data_cuenta_usuario['credito']; ?></button>
                                <?php } ?>
                            </div>

                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="form-to-sent"></div>
    
    <?php include('footer.php'); ?>


    <?php include('includes/scripts.php'); ?>

    <script type="text/javascript">
        function validaciones(){
            $.ajax({
              url: 'enviar.php',
              type: 'post',
              data: {'accion':'validaciones'},
              dataType: 'html',
              success: function(response){
                var obj = $.parseJSON(response);
                if(obj.response == 'success'){
                    $('.supplyme-alert').show();
                    $('.supplyme-alert').html(obj.contenido);
                    $('button.btn.btn--full.btn--pago').removeClass('btn--pago-carro');
                    if(obj.total_carro != ''){
                        $('span.card-total-price').html(obj.total_carro);
                        $('span.cart-total-iva').html(obj.iva_carro);
                        $('span.cart-total-price').html(obj.total_bruto);
                    }
                    if(obj.credito != 'SI'){
                        $('.btn--pago[data-tipo="2"]').hide();
                    }else{
                        $('.btn--pago[data-tipo="2"]').show();
                    }
                }else{
                    $('.supplyme-alert').hide();
                    $('button.btn.btn--full.btn--pago').addClass('btn--pago-carro');
                    if(obj.total_carro != ''){
                        $('span.card-total-price').html(obj.total_carro);
                        $('span.cart-total-iva').html(obj.iva_carro);
                        $('span.cart-total-price').html(obj.total_bruto);
                    }
                    if(obj.credito != 'SI'){
                        $('.btn--pago[data-tipo="2"]').hide();
                    }else{
                        $('.btn--pago[data-tipo="2"]').show();
                    }
                }
              }
            });
        }

        $(window).load(function(){
            validaciones();
        });

        $('ul.cart-supplier-list').slick({
            slidesToShow: 5,
            slidesToScroll: 1,
            dots: false,
            infinite: false,
            autoplay: true,
            autoplaySpeed: 2000,
            responsive: [
                {
                  breakpoint: 1199,
                  settings: {
                    slidesToShow: 4
                  }
                },
                {
                  breakpoint: 991,
                  settings: {
                    slidesToShow: 3
                  }
                },
                {
                  breakpoint: 767,
                  settings: {
                    slidesToShow: 2,
                    autoplay: false
                  }
                },
                {
                  breakpoint: 480,
                  settings: {
                    slidesToShow: 1,
                    autoplay: false
                  }
                }
            ]
        });

        $('ul.cart-supplier-list li').click(function(){
            $('ul.cart-supplier-list li').removeClass('active');
            $(this).addClass('active');

            var supplier = $(this).data('supplier');

            /*if(supplier == '2'){
                $('.supplyme-alert').show();
            }else{
                $('.supplyme-alert').hide();
            }*/

            $('.contenido-html > div').addClass('opc-hidden');
            $('.contenido-html .contenido-supplier' + supplier).removeClass('opc-hidden');
        });

        $(document).on('click', '.decrease, .increase', function (e) {
            var $this = $(e.target),
                input = $this.parent().find('.qty-input'),
                tipo = '',
                v = $this.hasClass('decrease') ? input.val() - 1 : input.val() * 1 + 1,
                item = input.attr('data-item'),
                min = input.attr('data-min') ? input.attr('data-min') : 1,
                max = input.attr('data-max') ? input.attr('data-max') : false;

                if($this.hasClass('decrease')){
                	tipo = 'decrease';
                }else{
                	tipo = 'increase';
                }

            if (v >= min) {
                if (!max == false && v > max) {
                    return false
                } else {

                	$.ajax({
			          url: 'enviar.php',
			          type: 'post',
			          data: {'accion':tipo, 'qty':v, 'item':item},
			          dataType: 'html',
			          success: function(response){
			            console.log(response);

			            var obj = $.parseJSON(response);

			            if(obj.response == 'success'){
			            	input.val(v);

                            validaciones();
			            }

			            if(obj.reload == 'true'){
			              location.reload();
			            }else{
			              if(obj.precio_unitario != ''){
			              	$this.closest('.cart-table-prd').find('span.precio_unitario').text(obj.precio_unitario);
			              }
			              if(obj.total_item != ''){
			              	$this.closest('.cart-table-prd').find('span.precio_linea').text(obj.total_item);
			          	  }
                          /*if(obj.total_carro != ''){
                            $('span.card-total-price').text(obj.total_carro);
                            $('span.cart-total-price').text(obj.total_carro);
                          }*/
			            }

			          }
			        });
                	
                }
            }
            e.preventDefault();
        });
        $(document).on('change', '.qty-input', function (e) {
            var input = $(e.target),
            	qty = 0,
            	item = input.attr('data-item'),
                min = input.attr('data-min') ? input.attr('data-min') : 1,
                max = input.attr('data-max'),
                v = parseInt(input.val());

            if (v > max){
            	//input.val(max);
            	qty = max;
            }else if (v < min){
            	//input.val(min);
            	qty = min;
            }else{
            	qty = v;
            }

            $.ajax({
	          url: 'enviar.php',
	          type: 'post',
	          data: {'accion':'keyup', 'qty':qty, 'item':item},
	          dataType: 'html',
	          success: function(response){
	            console.log(response);

	            var obj = $.parseJSON(response);

	            if(obj.response == 'success'){
	            	input.val(qty);

                    validaciones();
	            }

	            if(obj.reload == 'true'){
	              location.reload();
	            }else{
	              if(obj.precio_unitario != ''){
	              	input.closest('.cart-table-prd').find('span.precio_unitario').text(obj.precio_unitario);
	              }
	              if(obj.total_item != ''){
	              	input.closest('.cart-table-prd').find('span.precio_linea').text(obj.total_item);
	          	  }
                  /*if(obj.total_carro != ''){
                    $('span.card-total-price').text(obj.total_carro);
                    $('span.cart-total-price').text(obj.total_carro);
                  }*/
	            }

	          }
	        });

        });
    </script>
</body>

</html>