<?php include('includes/config.php');
include("class.phpmailer.php");
//include("class.smtp.php");
include('ws/nusoap/lib/nusoap.php');

$accion = $campos['accion'];


if($accion == 'recoveryForm'){
	$email_recovery = $campos['email_recovery'];

	if(isset($_SESSION["admin_id"])){
		$respuesta = array( 'response' => 'error', 'message'=>'¡Ha ocurrido un error! No es posible realizar la acción solicitada' );
	    echo json_encode($respuesta);
	    die;
	}else{

		if(!is_valid_email($email_recovery) || empty($email_recovery)){
		    $respuesta = array( 'response' => 'error', 'message'=>'¡Ha ocurrido un error! Correo electrónico inválido' );
		    echo json_encode($respuesta);
		    die;
		}else{
			$sql = "SELECT * FROM ".$prefix."proveedor WHERE email = '".$email_recovery."' LIMIT 1";
		    $result = $conexion->query($sql);
		    $existe_proveedor = $result->num_rows;
		    $data_sql = $result->fetch_array(MYSQLI_BOTH);

		    if($existe_proveedor > 0){
		    	$nombre = explode(" ", $data_sql['nombre']);

		    	$new_pass = strtoupper(generarCodigo(8));

		    	$aleatorio = aleatoriedad();
				$valor = "06";
				$salt = '$2y$'.$valor.'$'.$aleatorio.'$';
				$clave_crypt = crypt($new_pass, $salt);

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
				$mail->Subject = "Nueva clave de acceso";
				$mail->AddAddress($data_sql['email']);
				//$mail->AddAddress($email_empresa);

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
					<p style='font-size:20px;'>Estimado/a ".$nombre[0].", hemos recibido una solicitud para restablecer su contraseña de acceso.</p>
					<p style='font-size:15px;'>Utilice las siguientes credenciales para <a href='".$baseurl."login.html'>iniciar sesión</a> y acceder a todos nuestros beneficios:</p>
					<hr />
					<p style='font-size:22px;'>DETALLE</p>
					<p style='font-size:15px; font-family:Verdana, Geneva, sans-serif;'>Usuario: <span>".$data_sql['email']."</span></p>
					<p style='font-size:15px; font-family:Verdana, Geneva, sans-serif;'>Contraseña: <span>".$new_pass."</span></p>
					<hr />
					</td>
					</tr>
					<tr>
					<td colspan='4' style='padding:15px;'>
					<p style='font-size:15px;color:#777777;'>Recuerde que puede gestionar sus datos e información personal en el acceso a su cuenta.</p><br>
					<p style='font-size:11px;color:#777777;'>Este email fue generado de manera automática.</p>
					<p style='font-size:11px;color:#777777;'>Si no ha realizado ninguna de las acciones mencionadas, por favor contáctenos a la brevedad posible.</p>
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

					if($data_sql['solicitud_proveedor'] == 'SI'){
						$update = "UPDATE ".$prefix."admin SET password='".$clave_crypt."' WHERE id_asociado = '".$data_sql['id']."' ";
						if($conexion->query($update)){
							$update2 = "UPDATE ".$prefix."proveedor SET password='".$clave_crypt."', fecha_update='".date('Y-m-d G:i:s')."' WHERE id = '".$data_sql['id']."' ";
							$conexion->query($update2);
						}
					}else{
						$update = "UPDATE ".$prefix."proveedor SET password='".$clave_crypt."', fecha_update='".date('Y-m-d G:i:s')."' WHERE id = '".$data_sql['id']."' ";
						$conexion->query($update);
					}

					$respuesta = array('response' => 'success', 'message' => 'Hemos enviado la notificación a '.$data_sql['email'].' de manera exitosa', 'url' => '', 'time_out' => 'false');
				}else{
					$respuesta = array( 'response' => 'error', 'empty'=>'', 'message'=>'Ha ocurrido un error, intente nuevamente por favor.' );
				}

		    }else{
		    	$respuesta = array( 'response' => 'error', 'message'=>'¡Ha ocurrido un error! El email ingresado no se encuentra en nuestros registros.' );
			    echo json_encode($respuesta);
			    die;
		    }
		}

	}

	echo json_encode($respuesta);
}

if($accion == 'load_catalogo'){
	$proveedor = $campos['proveedor'];
	$categoria = $campos['categoria'];
	$subcategoria = $campos['subcategoria'];
	$pagina = $campos['pagina'];
	$precio = $campos['precio'];
	$orden = $campos['orden'];
	$paginas = '';

	if(!empty($proveedor)){
	    $sql = "SELECT * FROM ".$prefix."proveedor WHERE seourl = '".$proveedor."' LIMIT 1";
	    $result = $conexion->query($sql);
	    $data_proveedor = $result->fetch_array(MYSQLI_BOTH);
	}

	if(!empty($categoria)){
	    $sql = "SELECT * FROM ".$prefix."categorias WHERE seourl = '".$categoria."' LIMIT 1";
	    $result = $conexion->query($sql);
	    $data_categoria = $result->fetch_array(MYSQLI_BOTH);
	}

	if(!empty($subcategoria) && $subcategoria != 'todas'){
        $sql = "SELECT * FROM ".$prefix."subcategorias WHERE seourl = '".$subcategoria."' LIMIT 1";
        $result = $conexion->query($sql);
        $data_subcategoria = $result->fetch_array(MYSQLI_BOTH);
    }

	$sql = "SELECT prod.id as id, prod.codigo as codigo, prod.producto as producto, prod.categoria as categoria, prod.subcategoria as subcategoria, prod.unidad_medida as unidad_medida, prod.empaque as empaque, prod.precio as precio, prod.moneda as moneda, prod.descripcion as descripcion, prod.stock as stock, prod.estado as estado, prod.fecha_add as fecha_add, prod.fecha_update as fecha_update, prod.id_asociado as id_asociado, prod.token as token, prod.ventas as ventas  ";
	$sql .= " FROM ".$prefix."producto as prod, ".$prefix."proveedor as prov ";
	$sql .= " WHERE prod.estado = 'activo' AND prov.estado = 'activo' AND prod.id_asociado = prov.id ";
	if(!empty($proveedor)){
	    $sql .= " AND prov.id = '".$data_proveedor['id']."' ";
	}
	if(!empty($categoria)){
	    $sql .= " AND prod.categoria = '".$data_categoria['codigo']."' ";
	}
	if(!empty($subcategoria) && $subcategoria != 'todas'){
	    $sql .= " AND prod.subcategoria = '".$data_subcategoria['codigo']."' ";
	}
	//echo $sql;
	$result = $conexion->query($sql);
	$qty_resultados = $result->num_rows;
	
	if($qty_resultados > 6){
		$get_paginas = ceil($qty_resultados / 6);
	}else{
		$get_paginas = 1;
	}

	for ($i = 1; $i <= $get_paginas; $i++) {
		if($i == 1){
			$paginas .= '<li class="active"><a href="#">'.$i.'</a></li>';
		}else{
			$paginas .= '<li><a href="#">'.$i.'</a></li>';
		}
	}

	echo $paginas;

	//echo json_encode($respuesta);
}

if($accion == 'validaciones'){

    $contador2 = 0;
    $contenido_alerta = '';
    $dias_entrega = '';
    $alerta_activa = '0';
    $total_carro = 0;
    $credito_disponible = 0;
    $existe_precio_especial = 0;

    $dias = array(
        'LU'=>'lunes',
        'MA'=>'martes',
        'MIE'=>'miercoles',
        'JUE'=>'jueves',
        'VIE'=>'viernes',
        'SA'=>'sabado',
        'DO'=>'domingo',
    );

    if(isset($_SESSION["admin_id"])){
    	$sql_cuenta_usuario = "SELECT prov.direccion as direccion, prov.estado_comprador as estado_comprador, prov.comuna as codigo_comuna, com.comuna as comuna, ciu.ciudad as ciudad, prov.credito as credito FROM ".$prefix."proveedor as prov, ".$prefix."comunas as com, ".$prefix."ciudades as ciu WHERE prov.id = '".$_SESSION["admin_id"]."' AND prov.comuna = com.codigo AND prov.ciudad = ciu.codigo LIMIT 1";
		$result_cuenta_usuario = $conexion->query($sql_cuenta_usuario);
		$data_cuenta_usuario = $result_cuenta_usuario->fetch_array(MYSQLI_BOTH);

		$credito_disponible = formato_int($data_cuenta_usuario['credito']);
    }
    

    $sql = "SELECT * FROM ".$prefix."carro_detalle WHERE token = '".$_SESSION["cart_id"]."' GROUP BY id_proveedor ";
    $result = $conexion->query($sql);
    $qty_proveedores = $result->num_rows;

    if((!isset($_SESSION["admin_privilegio"])) || ($qty_proveedores > 0 && isset($_SESSION["admin_privilegio"]) && $_SESSION["admin_privilegio"] != 'administrador')){

      	while($data_sql_while = $result->fetch_array(MYSQLI_BOTH)){

	      	$contador2 = $contador2 + 1;

	        $sql2 = "SELECT * FROM ".$prefix."proveedor WHERE id = '".$data_sql_while['id_proveedor']."' LIMIT 1 ";
	        $result2 = $conexion->query($sql2);
	        $data_proveedor = $result2->fetch_array(MYSQLI_BOTH);

	        $dias_entrega = explode(",", $data_proveedor['dias_entrega']);

			$sql2_1 = "SELECT * FROM ".$prefix."proveedor_comuna WHERE id_proveedor = '".$data_sql_while['id_proveedor']."' AND codigo_comuna = '".$data_cuenta_usuario['codigo_comuna']."' ";
			$result2_1 = $conexion->query($sql2_1);
			$qty_comuna = $result2_1->num_rows;

			if(isset($_SESSION["admin_id"])){
				$sql2_2 = "SELECT * FROM ".$prefix."proveedor_reglas WHERE id_proveedor = '".$data_sql_while['id_proveedor']."' AND id_cliente = '".$_SESSION["admin_id"]."' ";
				$result2_2 = $conexion->query($sql2_2);
				$qty_reglas = $result2_2->num_rows;
			}else{
				$qty_reglas = 0;
			}

	        $pedido_minimo_por_proveedor = formato_int($data_proveedor['pedido_minimo']);
	        //echo 'pedido minimo: '.$pedido_minimo_por_proveedor;

	        /*if($pedido_minimo_por_proveedor != ''){*/

		        $precio_por_proveedor = 0;

		        $sql3 = "SELECT id, token, id_producto, producto, SUM(cantidad) as suma_cantidad, precio, id_proveedor, fecha_add FROM ".$prefix."carro_detalle WHERE token = '".$_SESSION["cart_id"]."' AND id_proveedor = '".$data_sql_while['id_proveedor']."' GROUP BY id_producto ";
		        $result3 = $conexion->query($sql3);
		        while($data_productos = $result3->fetch_array(MYSQLI_BOTH)){

		            $sql4 = "SELECT * FROM ".$prefix."producto WHERE id = '".$data_productos['id_producto']."' AND estado = 'activo' LIMIT 1";
		            $result4 = $conexion->query($sql4);
		            $data_producto = $result4->fetch_array(MYSQLI_BOTH);

		            if(!empty($_SESSION["admin_id"])){
                        $sql5 = "SELECT * FROM ".$prefix."producto_precio WHERE id_producto = '".$data_productos['id_producto']."' AND id_comprador = '".$_SESSION["admin_id"]."' LIMIT 1 ";
                        $result5 = $conexion->query($sql5);
                        $existe_precio_especial = $result5->num_rows;
                        $data_precio_especial = $result5->fetch_array(MYSQLI_BOTH);
                    }

                    if($existe_precio_especial > 0){
                    	$precio_unitario_item = formato_int($data_precio_especial['precio']);
		            	$precio_total_item = ($precio_unitario_item*$data_productos['suma_cantidad']);
                    }else{
                    	$precio_unitario_item = formato_int($data_producto['precio']);
		            	$precio_total_item = ($precio_unitario_item*$data_productos['suma_cantidad']);
                    }

		            $precio_por_proveedor = $precio_por_proveedor + $precio_total_item;

		            $total_carro = $total_carro + $precio_total_item;
		        }

		        if(($pedido_minimo_por_proveedor > $precio_por_proveedor) && ($pedido_minimo_por_proveedor != '')){
		            $alerta_activa = '1';

		            $diferencial = ($pedido_minimo_por_proveedor - $precio_por_proveedor);
		            if($diferencial > 0){
		            	$contenido_alerta .= '<p>Para el proveedor <strong>'.$data_proveedor['razon_social'].' te faltan '.formato_precio($diferencial).'</strong> para completar el pedido mínimo</p>';
		        	}
		        }

		        if(count($dias_entrega) < 2){
		        	$alerta_activa = '1';

		        	$contenido_alerta .= '<p>El proveedor <strong>'.$data_proveedor['razon_social'].' no tiene días de reparto disponible</strong>.</p>';
		        }

		        if($qty_comuna == 0 && !empty($_SESSION["admin_id"])){
		        	$alerta_activa = '1';

		        	$contenido_alerta .= '<p>El proveedor <strong>'.$data_proveedor['razon_social'].'</strong> no tiene reparto disponible a tu comuna: <strong>'.$data_cuenta_usuario['comuna'].'</strong>.</p>';
		        }

		        if($qty_reglas > 0 && !empty($_SESSION["admin_id"])){
		        	$alerta_activa = '1';

		        	$contenido_alerta .= '<p>El proveedor <strong>'.$data_proveedor['razon_social'].'</strong> se encuentra deshabilitado para compra</strong>.</p>';
		        }

	    	/*}*/

		}

	}

	if($credito_disponible >= $total_carro){
		$credito = 'SI';
	}else{
		$credito = 'NO';
	}

	$iva_carro = ($total_carro * 0.19);
	$total_bruto = ($total_carro + $iva_carro);

	if($contenido_alerta != '' && $alerta_activa == '1'){
		$respuesta = array('response' => 'success', 'contenido' => $contenido_alerta, 'total_carro' => formato_precio($total_carro), 'iva_carro' => formato_precio($iva_carro), 'total_bruto' => formato_precio($total_bruto), 'credito' => $credito, 'url' => '', 'time_out' => 'false');
	}else{
		$respuesta = array('response' => 'error', 'message' => '', 'total_carro' => formato_precio($total_carro), 'iva_carro' => formato_precio($iva_carro), 'total_bruto' => formato_precio($total_bruto), 'credito' => $credito, 'url' => '', 'time_out' => 'false');
	}

	echo json_encode($respuesta);
}

if($accion == 'crear-orden'){
	$nota_pedido = $campos['nota_pedido'];
	$tipopago = $campos['tipopago'];
	$payment = $campos['payment'];

	if(!empty($_SESSION["cart_id"]) && !empty($_SESSION["admin_id"])){
		$sql = "SELECT prov.nombre as nombre, prov.razon_social as razon_social, prov.rut as rut, prov.direccion as direccion, prov.telefono as telefono, prov.email as email, prov.estado_comprador as estado_comprador, com.comuna as comuna, ciu.ciudad as ciudad, gir.giro as giro, prov.credito as credito FROM ".$prefix."proveedor as prov, ".$prefix."comunas as com, ".$prefix."ciudades as ciu, ".$prefix."giros as gir WHERE prov.id = '".$_SESSION["admin_id"]."' AND prov.estado_comprador = 'activo' AND prov.comuna = com.codigo AND prov.ciudad = ciu.codigo AND prov.giro = gir.codigo LIMIT 1";
		$result = $conexion->query($sql);
		$data_proveedor = $result->fetch_array(MYSQLI_BOTH);

		$direccion_sql = str_replace("'", "", $data_proveedor['direccion']);

		$credito_disponible = formato_int($data_proveedor['credito']);

		$sql = "SELECT * FROM ".$prefix."pedido WHERE token = '".$_SESSION["cart_id"]."' AND id_proveedor = '".$_SESSION["admin_id"]."' LIMIT 1";
		$result = $conexion->query($sql);
		$existe_pedido = $result->num_rows;
		$data_pedido = $result->fetch_array(MYSQLI_BOTH);

		$sql = "SELECT * FROM ".$prefix."carro_detalle WHERE token = '".$_SESSION["cart_id"]."' ";
		$result = $conexion->query($sql);
		while($carro_actual = $result->fetch_array(MYSQLI_BOTH)){
			$sql3 = "SELECT * FROM ".$prefix."producto WHERE id = '".$carro_actual['id_producto']."' AND estado = 'activo' LIMIT 1";
        	$result3 = $conexion->query($sql3);
        	$data_producto = $result3->fetch_array(MYSQLI_BOTH);

        	if(!empty($_SESSION["admin_id"])){
                $sql4 = "SELECT * FROM ".$prefix."producto_precio WHERE id_producto = '".$carro_actual['id_producto']."' AND id_comprador = '".$_SESSION["admin_id"]."' LIMIT 1 ";
                $result4 = $conexion->query($sql4);
                $existe_precio_especial = $result4->num_rows;
                $data_precio_especial = $result4->fetch_array(MYSQLI_BOTH);
            }

            if($existe_precio_especial > 0){
                $precio_publicado = ((formato_int($data_precio_especial['precio']) * $margen) + formato_int($data_precio_especial['precio']));
            }else{
                $precio_publicado = ((formato_int($data_producto['precio']) * $margen) + formato_int($data_producto['precio']));
            }

			$total_carro_actual = $total_carro_actual + ($precio_publicado * $carro_actual['cantidad']);
		}

		if($credito_disponible >= $total_carro_actual){
			$credito = 'SI';
		}else{
			$credito = 'NO';
		}

		$iva_carro = ($total_carro_actual * 0.19);
		$total_carro_actual = round($total_carro_actual + $iva_carro);

		if($existe_pedido > 0){
			$update = "UPDATE ".$prefix."pedido SET fecha_add = '".date('Y-m-d G:i:s')."', nota_pedido = '".$nota_pedido."', total = '".$total_carro_actual."', nombre = '".$data_proveedor['nombre']."', email = '".$data_proveedor['email']."', telefono = '".$data_proveedor['telefono']."', razon_social = '".$data_proveedor['razon_social']."', rut = '".$data_proveedor['rut']."', giro = '".$data_proveedor['giro']."', direccion = '".$direccion_sql."', comuna = '".$data_proveedor['comuna']."', ciudad = '".$data_proveedor['ciudad']."', status_pago = '1', metodo_pago = '".$payment."' WHERE token = '".$_SESSION["cart_id"]."' AND id_proveedor = '".$_SESSION["admin_id"]."' ";
			if($conexion->query($update)){

				if(($payment == 'webpayplus' AND $tipopago == '1') OR ($payment == 'credito' AND $tipopago == '2' AND $credito == 'NO')){

					$sql = "SELECT * FROM ".$prefix."webpay WHERE code_webpay = '".$data_pedido['id']."' AND token_carro = '".$_SESSION["cart_id"]."' AND status = '1' LIMIT 1";
					$result = $conexion->query($sql);
					$existe_webpay = $result->num_rows;
					$data_webpay = $result->fetch_array(MYSQLI_BOTH);

					if($existe_webpay > 0){
						$update = "UPDATE ".$prefix."webpay SET total = '".$total_carro_actual."', date_add = '".date('Y-m-d G:i:s')."', nombre = '".$data_proveedor['nombre']."', email = '".$data_proveedor['email']."', telefono = '".$data_proveedor['telefono']."', razon_social = '".$data_proveedor['razon_social']."',	rut = '".$data_proveedor['rut']."',	giro = '".$data_proveedor['giro']."', direccion = '".$direccion_sql."', comuna = '".$data_proveedor['comuna']."',	ciudad = '".$data_proveedor['ciudad']."' WHERE code_webpay = '".$data_pedido['id']."' AND token_carro = '".$_SESSION["cart_id"]."' AND status = '1' ";
						if($conexion->query($update)){
							$respuesta = array('response' => 'success', 'tipo_respuesta' => '1', 'message' => 'Redireccionando a Webpay..', 'id_pedido' => $data_webpay['id_webpay'], 'url' => $baseurl.'webpay_ws.php', 'time_out' => 'false');
						}else{
							$respuesta = array('response' => 'error', 'message' => 'Ha ocurrido un error al intentar generar el pedido');
						}
					}else{
						$sql_insert = array(
							'id_webpay' => '',
							'code_webpay' => $data_pedido['id'],
							'token_carro' => $_SESSION["cart_id"],
							'total' => $total_carro_actual,
							'date_add' => date('Y-m-d G:i:s'),
							'status' => '1',
							'nombre' => $data_proveedor['nombre'],
							'email' => $data_proveedor['email'],
							'telefono' => $data_proveedor['telefono'],
							'razon_social' => $data_proveedor['razon_social'],
							'rut' => $data_proveedor['rut'],
							'giro' => $data_proveedor['giro'],
							'direccion' => $direccion_sql,
							'comuna' => $data_proveedor['comuna'],
							'ciudad' => $data_proveedor['ciudad']
						);
						$sql_exec = ingresar_registros($prefix.'webpay', $sql_insert);
						if($result_ = $conexion->query($sql_exec)){
							$id_generado2 = $conexion->insert_id;

							$respuesta = array('response' => 'success', 'tipo_respuesta' => '1', 'message' => 'Redireccionando a Webpay..', 'id_pedido' => $id_generado2, 'url' => $baseurl.'webpay_ws.php', 'time_out' => 'false');
						}else{
							$respuesta = array('response' => 'error', 'message' => 'Ha ocurrido un error al intentar generar el pedido');
						}
					}

				}elseif($payment == 'credito' AND $tipopago == '2' AND $credito == 'SI'){

					$credito_actual = formato_precio($credito_disponible - $total_carro_actual);

					$sql_update = "UPDATE ".$prefix."proveedor SET credito = '".$credito_actual."' WHERE id = '".$_SESSION["admin_id"]."' ";
					if($conexion->query($sql_update)){

						$sql_update2 = "UPDATE ".$prefix."pedido SET status_pago = '2', metodo_pago='".$payment."' WHERE token = '".$_SESSION["cart_id"]."' AND id_proveedor = '".$_SESSION["admin_id"]."' ";
						if($conexion->query($sql_update2)){
							$respuesta = array('response' => 'success', 'tipo_respuesta' => '2', 'message' => 'Pedido completado con éxito', 'token' => $_SESSION["cart_id"], 'url' => $baseurl.'pedido-generado/'.$_SESSION["cart_id"].'.html', 'time_out' => 'false');
						}else{
							$respuesta = array('response' => 'error', 'message' => 'Ha ocurrido un error al intentar generar el pedido');
						}

					}else{
						$respuesta = array('response' => 'error', 'message' => 'Ha ocurrido un error al intentar generar el pedido');
					}
				}

			}else{
				$respuesta = array('response' => 'error', 'message' => 'Ha ocurrido un error al intentar generar el pedido');
			}
		}else{
			$sql_insert = array(
				'id' => '',
				'token' => $_SESSION["cart_id"],
				'id_proveedor' => $_SESSION["admin_id"],
				'fecha_add' => date('Y-m-d G:i:s'),
				'nota_pedido' => $nota_pedido,
				'total' => $total_carro_actual,
				'nombre' => $data_proveedor['nombre'],
				'email' => $data_proveedor['email'],
				'telefono' => $data_proveedor['telefono'],
				'razon_social' => $data_proveedor['razon_social'],
				'rut' => $data_proveedor['rut'],
				'giro' => $data_proveedor['giro'],
				'direccion' => $direccion_sql,
				'comuna' => $data_proveedor['comuna'],
				'ciudad' => $data_proveedor['ciudad'],
				'metodo_pago' => $payment
			);
			$sql_exec = ingresar_registros($prefix.'pedido', $sql_insert);
			if($result_ = $conexion->query($sql_exec)){
				$id_generado = $conexion->insert_id;

				if(($payment == 'webpayplus' AND $tipopago == '1') OR ($payment == 'credito' AND $tipopago == '2' AND $credito == 'NO')){

					$sql_insert = array(
						'id_webpay' => '',
						'code_webpay' => $id_generado,
						'token_carro' => $_SESSION["cart_id"],
						'total' => $total_carro_actual,
						'date_add' => date('Y-m-d G:i:s'),
						'status' => '1',
						'nombre' => $data_proveedor['nombre'],
						'email' => $data_proveedor['email'],
						'telefono' => $data_proveedor['telefono'],
						'razon_social' => $data_proveedor['razon_social'],
						'rut' => $data_proveedor['rut'],
						'giro' => $data_proveedor['giro'],
						'direccion' => $direccion_sql,
						'comuna' => $data_proveedor['comuna'],
						'ciudad' => $data_proveedor['ciudad']
					);
					$sql_exec = ingresar_registros($prefix.'webpay', $sql_insert);
					if($result_ = $conexion->query($sql_exec)){
						$id_generado2 = $conexion->insert_id;

						$respuesta = array('response' => 'success', 'tipo_respuesta' => '1', 'message' => 'Redireccionando a Webpay..', 'id_pedido' => $id_generado2, 'url' => $baseurl.'webpay_ws.php', 'time_out' => 'false');
					}else{
						$respuesta = array('response' => 'error', 'message' => 'Ha ocurrido un error al intentar generar el pedido');
					}

				}elseif($payment == 'credito' AND $tipopago == '2' AND $credito == 'SI'){

					$credito_actual = formato_precio($credito_disponible - $total_carro_actual);

					$sql_update = "UPDATE ".$prefix."proveedor SET credito = '".$credito_actual."' WHERE id = '".$_SESSION["admin_id"]."' ";
					if($conexion->query($sql_update)){

						$sql_update2 = "UPDATE ".$prefix."pedido SET status_pago = '2', metodo_pago='".$payment."' WHERE token = '".$_SESSION["cart_id"]."' AND id_proveedor = '".$_SESSION["admin_id"]."' ";
						if($conexion->query($sql_update2)){
							$respuesta = array('response' => 'success', 'tipo_respuesta' => '2', 'message' => 'Pedido completado con éxito', 'token' => $_SESSION["cart_id"], 'url' => $baseurl.'pedido-generado/'.$_SESSION["cart_id"].'.html', 'time_out' => 'false');
						}else{
							$respuesta = array('response' => 'error', 'message' => 'Ha ocurrido un error al intentar generar el pedido');
						}

					}else{
						$respuesta = array('response' => 'error', 'message' => 'Ha ocurrido un error al intentar generar el pedido');
					}
				}

			}else{
				$respuesta = array('response' => 'error', 'message' => 'Ha ocurrido un error al intentar generar el pedido');
			}
		}

		//$data_producto = $result->fetch_array(MYSQLI_BOTH);
	}

	echo json_encode($respuesta);
}

if($accion == 'delete'){
	$id = $campos['id'];

	$id_ = decrypt($id, $sitio);
	$id_get = explode("-", $id_);
	$id = $id_get[1];

	if($id > 0 && !empty($_SESSION["cart_id"])){

		$sql = "SELECT * FROM ".$prefix."carro_detalle WHERE token = '".$_SESSION["cart_id"]."' AND id_producto = '".$id."' ";
		$result = $conexion->query($sql);
		$qty_carro = $result->num_rows;
		$data_eliminar = $result->fetch_array(MYSQLI_BOTH);

		if($qty_carro > 0){
			$sql2 = "SELECT * FROM ".$prefix."carro_detalle WHERE token = '".$_SESSION["cart_id"]."' AND id_proveedor = '".$data_eliminar['id_proveedor']."' AND id_producto <> '".$id."' ";
			$result2 = $conexion->query($sql2);
			$qty_carro_2 = $result2->num_rows;

			$delete = "DELETE FROM ".$prefix."carro_detalle WHERE token = '".$_SESSION["cart_id"]."' AND id_producto = '".$id."' ";
			if($conexion->query($delete)){
				if($qty_carro_2 == 0){
					$respuesta = array('response' => 'success', 'reload' => 'true');
				}else{
					$respuesta = array('response' => 'success', 'reload' => 'false', 'tab' => '1');
				}
			}else{
				$respuesta = array('response' => 'error', 'reload' => 'false');
			}
		}
	}else{
		$respuesta = array('response' => 'error', 'reload' => 'false');
	}

	echo json_encode($respuesta);
}

if($accion == 'increase' || $accion == 'decrease' || $accion == 'keyup'){
	$qty = $campos['qty'];
	$item = $campos['item'];

	$total_carro_actual = 0;

	$item_ = decrypt($item, $sitio);
	$item_get = explode("-", $item_);
	$id = $item_get[1];

	if($item != '' && $qty > 0 && $id > 0 && !empty($_SESSION["cart_id"])){
		$sql = "SELECT * FROM ".$prefix."producto WHERE id = '".$id."' AND estado = 'activo' LIMIT 1";
		$result = $conexion->query($sql);
		$data_producto = $result->fetch_array(MYSQLI_BOTH);

		//$precio_add = str_replace("$ ", "", $data_producto['precio']);
        //$precio_add = str_replace(".", "", $precio_add);

        if(!empty($_SESSION["admin_id"])){
            $sql2 = "SELECT * FROM ".$prefix."producto_precio WHERE id_producto = '".$data_producto['id']."' AND id_comprador = '".$_SESSION["admin_id"]."' LIMIT 1 ";
            $result2 = $conexion->query($sql2);
            $existe_precio_especial = $result2->num_rows;
            $data_precio_especial = $result2->fetch_array(MYSQLI_BOTH);
        }

        if($data_producto['stock'] == '-1'){
        	$stock_actual = '1000000000';
        }else{
        	$stock_actual = $data_producto['stock'];
        }
		
		if($data_producto['estado'] != 'activo'){
			$delete = "DELETE FROM ".$prefix."carro_detalle WHERE token = '".$_SESSION["cart_id"]."' AND id_producto = '".$id."' ";
			if($conexion->query($delete)){
				$respuesta = array('response' => 'success', 'reload' => 'true');
			}else{
				$respuesta = array('response' => 'error', 'reload' => 'true');
			}
		}else{

			if($existe_precio_especial > 0){
                $precio_publicado = ((formato_int($data_precio_especial['precio']) * $margen) + formato_int($data_precio_especial['precio']));
            }else{
                $precio_publicado = ((formato_int($data_producto['precio']) * $margen) + formato_int($data_producto['precio']));
            }

            if($data_producto['stock'] == '-1'){
            	$stock_query = $qty;
            }else{
            	if($stock_actual < $qty){
	                $stock_query = $stock_actual;
	            }else{
	                $stock_query = $qty;
	            }
            }

            $total_item = ($precio_publicado * $stock_query);

			$update = "UPDATE ".$prefix."carro_detalle SET cantidad='".$stock_query."' WHERE id_producto = '".$id."' AND token = '".$_SESSION["cart_id"]."' ";
			if($conexion->query($update)){

				$sql2 = "SELECT * FROM ".$prefix."carro_detalle WHERE token = '".$_SESSION["cart_id"]."' AND id_producto = '".$id."' ";
				$result2 = $conexion->query($sql2);
				while($carro_actual = $result2->fetch_array(MYSQLI_BOTH)){
					$sql3 = "SELECT * FROM ".$prefix."producto WHERE id = '".$carro_actual['id_producto']."' AND estado = 'activo' LIMIT 1";
                	$result3 = $conexion->query($sql3);
                	$data_producto = $result3->fetch_array(MYSQLI_BOTH);

                	if(!empty($_SESSION["admin_id"])){
                        $sql4 = "SELECT * FROM ".$prefix."producto_precio WHERE id_producto = '".$data_producto['id']."' AND id_comprador = '".$_SESSION["admin_id"]."' LIMIT 1 ";
                        $result4 = $conexion->query($sql4);
                        $existe_precio_especial = $result4->num_rows;
                        $data_precio_especial = $result4->fetch_array(MYSQLI_BOTH);
                    }

                    if($existe_precio_especial > 0){
                        $precio_publicado = ((formato_int($data_precio_especial['precio']) * $margen) + formato_int($data_precio_especial['precio']));
                    }else{
                        $precio_publicado = ((formato_int($data_producto['precio']) * $margen) + formato_int($data_producto['precio']));
                    }

					$total_carro_actual = $total_carro_actual + ($precio_publicado * $carro_actual['cantidad']);
				}

				$sql20 = "SELECT SUM(cantidad) as cantidad FROM ".$prefix."carro_detalle WHERE token = '".$_SESSION["cart_id"]."' ";
		        $result20 = $conexion->query($sql20);
		        $carro_existente = $result20->fetch_array(MYSQLI_BOTH);

		        $sql22 = "SELECT * FROM ".$prefix."carro_detalle WHERE token = '".$_SESSION["cart_id"]."' ";
				$result22 = $conexion->query($sql22);
				while($carro_actual_actual = $result22->fetch_array(MYSQLI_BOTH)){

					$sql33 = "SELECT * FROM ".$prefix."producto WHERE id = '".$carro_actual_actual['id_producto']."' AND estado = 'activo' LIMIT 1";
                	$result33 = $conexion->query($sql33);
                	$data_producto3 = $result33->fetch_array(MYSQLI_BOTH);

                	if(!empty($_SESSION["admin_id"])){
                        $sql44 = "SELECT * FROM ".$prefix."producto_precio WHERE id_producto = '".$data_producto3['id']."' AND id_comprador = '".$_SESSION["admin_id"]."' LIMIT 1 ";
                        $result44 = $conexion->query($sql44);
                        $existe_precio_especial2 = $result44->num_rows;
                        $data_precio_especial2 = $result44->fetch_array(MYSQLI_BOTH);
                    }

                    if($existe_precio_especial2 > 0){
                        $precio_publicado2 = ((formato_int($data_precio_especial2['precio']) * $margen) + formato_int($data_precio_especial2['precio']));
                    }else{
                        $precio_publicado2 = ((formato_int($data_producto3['precio']) * $margen) + formato_int($data_producto3['precio']));
                    }

					$total_carro_actual_2 = $total_carro_actual_2 + ($precio_publicado2 * $carro_actual_actual['cantidad']);
				}

				$respuesta = array('response' => 'success', 'precio_unitario' => formato_precio($precio_publicado), 'total_item' => formato_precio($total_item), 'total_carro' => formato_precio($total_carro_actual), 'total_carro_actual' => formato_precio($total_carro_actual_2), 'carro_existente' => $carro_existente['cantidad'], 'reload' => 'false');
			
			}else{
				$respuesta = array('response' => 'error', 'reload' => 'true');
			}

		}
	}else{
		$respuesta = array('response' => 'error', 'reload' => 'true');
	}

	echo json_encode($respuesta);
}

if($accion == 'newsletterFormulario'){
	$emailnewsletter = $campos['emailnewsletter'];

	if(!is_valid_email($emailnewsletter) || empty($emailnewsletter)){
	    $respuesta = array( 'response' => 'error', 'message'=>'¡Ha ocurrido un error! Correo electrónico inválido' );
	    echo json_encode($respuesta);
	    die;
	}

	$sql = "SELECT * FROM ".$prefix."newsletter WHERE email = '".$emailnewsletter."' LIMIT 1 ";
	$result = $conexion->query($sql);
	$existe_registro = $result->num_rows;

	if($existe_registro > 0){
		$respuesta = array( 'response' => 'error', 'message'=>'¡Ha ocurrido un error! El email ingresado ya existe en nuestra base de datos' );
	}else{
		$sql = array(
			'id' => '',
			'email' => $emailnewsletter,
			'fecha_add' => date('Y-m-d G:i:s'),
			'estado' => 'activo'
		);
		$sql_ = ingresar_registros($prefix.'newsletter', $sql);
		if($result_ = $conexion->query($sql_)){
			$id_generado = $conexion->insert_id;

			$respuesta = array('response' => 'success', 'message' => 'Suscripción generada con éxito!', 'url' => '', 'time_out' => 'false');
		}else{
			$respuesta = array('response' => 'error', 'message' => 'Ha ocurrido un error al intentar ingresar el registro');
		}
	}

	echo json_encode($respuesta);
}

if($accion == 'loginFormulario'){
	$email = $campos['email'];
	$password = $campos['password'];
	$ruta_carro = $campos['ruta_carro'];

	if(empty($email) || empty($password)){
	    $result = array( 'response' => 'error', 'message'=>'¡Ha ocurrido un error! Debes ingresar correctamente tu email o contraseña' );
	    echo json_encode($result);
	    die;
	}

	if(!is_valid_email($email)){
	    $result = array( 'response' => 'error', 'message'=>'¡Ha ocurrido un error! Correo electrónico inválido' );
	    echo json_encode($result);
	    die;
	}

	$sql = "SELECT a.password as password, a.estado as estado, a.id_asociado as id_asociado, a.privilegio as privilegio FROM ".$prefix."admin as a, ".$prefix."proveedor as p WHERE a.email = '".$email."' AND a.privilegio = 'proveedor' AND a.id_asociado = p.id AND p.estado = 'activo' LIMIT 1";
	$result = $conexion->query($sql);
	$data_sql = $result->fetch_array(MYSQLI_BOTH);
	$existe_registro = $result->num_rows;

	if($existe_registro > 0){
		if($data_sql['estado'] == 'SI'){
			if(password_verify($password, $data_sql['password'])){
				$_SESSION["admin_id"] = $data_sql['id_asociado'];
				$_SESSION["admin_privilegio"] = $data_sql['privilegio'];

				if($ruta_carro == '1'){
					$url = $baseurl.'carro.html';
				}else{
					if($_SESSION["admin_privilegio"] == 'comprador'){
						$url = $baseurl;
					}elseif($_SESSION["admin_privilegio"] == 'proveedor'){
						$url = $baseurl.'administracion/dashboard.html';
					}else{
						$url = $baseurl.'administracion/dashboard.html';
					}
				}

				$respuesta = array('response' => 'success', 'message' => '', 'url' => $url);
			}else{
				$respuesta = array('response' => 'error', 'message' => 'Los datos ingresados no coinciden con nuestros registros.');
			}
    	}else{
    		$sql2 = "SELECT * FROM ".$prefix."proveedor WHERE id = '".$data_sql['id_asociado']."' LIMIT 1";
			$result2 = $conexion->query($sql2);
			$data_sql2 = $result2->fetch_array(MYSQLI_BOTH);

			if($data_sql2['estado_comprador'] == 'activo'){
				if(password_verify($password, $data_sql2['password'])){

					$_SESSION["admin_id"] = $data_sql2['id'];
					$_SESSION["admin_privilegio"] = 'comprador';

					if($ruta_carro == '1'){
						$url = $baseurl.'carro.html';
					}else{
						if($_SESSION["admin_privilegio"] == 'comprador' || $_SESSION["admin_privilegio"] == 'proveedor'){
							$url = $baseurl;
						}else{
							$url = $baseurl.'administracion/dashboard.html';
						}
					}

					$respuesta = array('response' => 'success', 'message' => '', 'url' => $url);
				}else{
					$respuesta = array('response' => 'error', 'message' => 'Los datos ingresados no coinciden con nuestros registros.');
				}
			}else{
				$respuesta = array('response' => 'error', 'message' => 'Su cuenta no se encuentra activa. Para más información contáctenos.');
			}
    	}
	}else{

		$sql_2 = "SELECT * FROM ".$prefix."proveedor WHERE email = '".$email."' LIMIT 1";
		$result_2 = $conexion->query($sql_2);
		$data_sql_2 = $result_2->fetch_array(MYSQLI_BOTH);
		$existe_registro_2 = $result_2->num_rows;

		if($existe_registro_2 > 0){
			if($data_sql_2['estado_comprador'] == 'activo'){
				if(password_verify($password, $data_sql_2['password'])){
					$_SESSION["admin_id"] = $data_sql_2['id'];
					$_SESSION["admin_privilegio"] = 'comprador';

					if($ruta_carro == '1'){
						$url = $baseurl.'carro.html';
					}else{
						if($_SESSION["admin_privilegio"] == 'comprador' || $_SESSION["admin_privilegio"] == 'proveedor'){
							$url = $baseurl;
						}else{
							$url = $baseurl.'administracion/dashboard.html';
						}
					}

					$respuesta = array('response' => 'success', 'message' => '', 'url' => $url);
				}else{
					$respuesta = array('response' => 'error', 'message' => 'Los datos ingresados no coinciden con nuestros registros.');
				}
			}else{
				$respuesta = array('response' => 'error', 'message' => 'Su cuenta no se encuentra activa. Para más información contáctenos.');
			}
		}else{
			$respuesta = array( 'response' => 'error', 'message'=>'¡Ha ocurrido un error! Correo electrónico ingresado no coincide con nuestros registros' );
		}
	}

	echo json_encode($respuesta);
}

if($accion == 'registerFormulario'){
	$nombre = $campos['nombre'];
	$apellido = $campos['apellido'];
	$email = $campos['email'];
	$telefono = $campos['telefono'];
	$rut = $campos['rut'];
	$razon_social = $campos['razon_social'];
	$giro = $campos['giro'];
	$direccion = $campos['direccion'];
	$latitud = $campos['latitud'];
	$longitud = $campos['longitud'];
	//$numero = $campos['numero'];
	$comuna = $campos['comuna'];
	//$ciudad = $campos['ciudad'];
	$nombre_contacto = $campos['nombre_contacto'];
	$apellido_contacto = $campos['apellido_contacto'];
	$email_contacto = $campos['email_contacto'];
	$telefono_contacto = $campos['telefono_contacto'];
	$password = $campos['password'];

	$checkbox0 = $campos['checkbox0'];
	$checkbox1 = $campos['checkbox1'];

	if(empty($nombre)){
	    $respuesta = array( 'response' => 'error', 'message'=>'¡Ha ocurrido un error! Por favor ingrese su nombre y apellido' );
	    echo json_encode($respuesta);
	    die;
	}

	if(!is_valid_email($email) || empty($email)){
	    $respuesta = array( 'response' => 'error', 'message'=>'¡Ha ocurrido un error! Correo electrónico inválido' );
	    echo json_encode($respuesta);
	    die;
	}

	if(empty($telefono)){
	    $respuesta = array( 'response' => 'error', 'message'=>'¡Ha ocurrido un error! Por favor ingrese un número de contacto' );
	    echo json_encode($respuesta);
	    die;
	}else{
		if(strlen($telefono) < 9){
			$respuesta = array( 'response' => 'error', 'message'=>'¡Ha ocurrido un error! Formato de teléfono no es válido' );
		    echo json_encode($respuesta);
		    die;
		}
	}

	if(empty($rut)){
	    $respuesta = array( 'response' => 'error', 'message'=>'¡Ha ocurrido un error! Por favor ingrese Rut de empresa' );
	    echo json_encode($respuesta);
	    die;
	}else{
		/*$sql = "SELECT * FROM ".$prefix."proveedor WHERE rut = '".$rut."' LIMIT 1";
		$result = $conexion->query($sql);
		$existe_registro = $result->num_rows;
		if($existe_registro > 0){
			$respuesta = array( 'response' => 'error', 'message'=>'¡Ha ocurrido un error! El rut ingresado ya se encuentra en nuestros registros.' );
		    echo json_encode($respuesta);
		    die;
		}*/
	}

	if(empty($razon_social)){
	    $respuesta = array( 'response' => 'error', 'message'=>'¡Ha ocurrido un error! Debe ingresar razón social' );
	    echo json_encode($respuesta);
	    die;
	}

	if(empty($giro)){
	    $respuesta = array( 'response' => 'error', 'message'=>'¡Ha ocurrido un error! Debe seleccionar su actividad económica' );
	    echo json_encode($respuesta);
	    die;
	}

	if(empty($direccion) || empty($comuna) || $comuna == '0'){
	    $respuesta = array( 'response' => 'error', 'message'=>'¡Ha ocurrido un error! Debe completar datos de dirección' );
	    echo json_encode($respuesta);
	    die;
	}else{
		$sql_ciudad = "SELECT ci.codigo as codigo_ciudad, ci.id_region as idregion FROM ".$prefix."comunas as co, ".$prefix."ciudades as ci WHERE co.codigo = '".$comuna."' AND co.codigo_ciudad = ci.id  ";
		$result_ciudad = $conexion->query($sql_ciudad);
		$data_sql_ciudad = $result_ciudad->fetch_array(MYSQLI_BOTH);

		$sql_region = "SELECT codigo FROM ".$prefix."regiones WHERE id = '".$data_sql_ciudad['idregion']."' LIMIT 1  ";
		$result_region = $conexion->query($sql_region);
		$data_sql_region = $result_region->fetch_array(MYSQLI_BOTH);
	}

	if($checkbox0 == 'on'){
		if(empty($nombre_contacto) || empty($apellido_contacto)){
		    $respuesta = array( 'response' => 'error', 'message'=>'¡Ha ocurrido un error! Por favor ingrese nombre y apellido de contacto' );
		    echo json_encode($respuesta);
		    die;
		}

		if(!is_valid_email($email_contacto) || empty($email_contacto)){
		    $respuesta = array( 'response' => 'error', 'message'=>'¡Ha ocurrido un error! Correo electrónico de contacto inválido' );
		    echo json_encode($respuesta);
		    die;
		}

		if(empty($telefono_contacto)){
		    $respuesta = array( 'response' => 'error', 'message'=>'¡Ha ocurrido un error! Por favor ingrese un número de contacto' );
		    echo json_encode($respuesta);
		    die;
		}else{
			if(strlen($telefono_contacto) < 9){
				$respuesta = array( 'response' => 'error', 'message'=>'¡Ha ocurrido un error! Formato de teléfono no es válido' );
			    echo json_encode($respuesta);
			    die;
			}
		}
	}

	if(empty($password)){
	    $respuesta = array( 'response' => 'error', 'message'=>'¡Ha ocurrido un error! Debe ingresar una contraseña de acceso' );
	    echo json_encode($respuesta);
	    die;
	}else{
		$password = str_replace(" ", "", $password);

		if(strlen($password) < 6){
			$respuesta = array( 'response' => 'error', 'message'=>'¡Ha ocurrido un error! La contraseña debe contener al menos 6 caracteres' );
		    echo json_encode($respuesta);
		    die;
		}
	}

	if($checkbox1 != 'on'){
		$respuesta = array( 'response' => 'error', 'message'=>'Debe aceptar términos y condiciones para completar su registro' );
	    echo json_encode($respuesta);
	    die;
	}

	$sql = "SELECT * FROM ".$prefix."admin WHERE email = '".$email."' AND privilegio = 'proveedor' LIMIT 1";
	$result = $conexion->query($sql);
	$existe_registro = $result->num_rows;

	if($existe_registro == 0){
		$aleatorio = aleatoriedad();
		$valor = "06";
		$salt = '$2y$'.$valor.'$'.$aleatorio.'$';
		$clave_crypt = crypt($password, $salt);

		if($checkbox0 == 'on'){
			$cuenta_proveedor = 'SI';
			$solicitud_proveedor = 'SI';
		}else{
			$cuenta_proveedor = 'NO';
			$solicitud_proveedor = 'NO';
		}

		$sql_insert = array(
			'id' => '',
			'nombre' => $nombre." ".$apellido,
			'apellido' => '',
			'razon_social' => $razon_social,
			'rut' => $rut,
			'giro' => $giro,
			'direccion' => $direccion,
			'numeracion' => '',
			'comuna' => $comuna,
			'ciudad' => $data_sql_ciudad['codigo_ciudad'],
			'region' => '0',
			'latitud' => $latitud,
			'longitud' => $longitud,
			'telefono' => $telefono,
			'email' => $email,
			'sitio_web' => '',
			'estado' => 'pendiente',
			'estado_comprador' => 'activo',
			'fecha_add' => date('Y-m-d G:i:s'),
			'fecha_update' => date('Y-m-d G:i:s'),
			'dias_entrega' => '',
			'horario_corte' => '',
			'dias_habiles' => '',
			'tiempo_cancelacion' => '',
			'pedido_minimo' => '',
			'password' => $clave_crypt,
			'cuenta_proveedor' => $cuenta_proveedor,
			'nombre_contacto' => $nombre_contacto,
			'apellido_contacto' => $apellido_contacto,
			'email_contacto' => $email_contacto,
			'telefono_contacto' => $telefono_contacto,
			'seourl' => generateSeoURL($razon_social),
			'archivo' => '',
			'credito' => '$ 0',
			'solicitud_proveedor' => $solicitud_proveedor,
			'wsdl' => 'NO'
		);
		$sql_insert_ = ingresar_registros($prefix.'proveedor', $sql_insert);
		if($result_ = $conexion->query($sql_insert_)){
			$id_generado = $conexion->insert_id;

			//$_SESSION["comprador_id"] = $id_generado;
			//$_SESSION["comprador_email"] = $email;

			$cliente = new nusoap_client($url_wsdl, 'wsdl');

			$json = array(
				'pvarCodAux' => $id_generado,
				'pvarRutAux' => $rut,
				'pvarGirAux' => $giro,
				'pvarNomAux' => $razon_social,
				'pvarRazonSocial' => $razon_social,
				'pvarComAux' => $comuna,
				'pvarCiuAux' => $data_sql_ciudad['codigo_ciudad'],
				'pvarRegion' => $data_sql_region['codigo'],
				'pvarPais' => 'CL',
				'pvarDirAux' => $direccion,
				'pvarNumDir' => '',
				'pvarfonaux1' => $telefono,
				'pvarEmail' => $email,
				'pvarEmpresa' => $var_empresa_wsdl
			);

			$response_ws['response'] = $cliente->call("fnClienteProveedor", $json);
			$cadena_respuesta = $response_ws['response']['fnClienteProveedorResult'];

			if($cadena_respuesta == 'CREADO' || $cadena_respuesta == 'ACTUALIZADO'){
				$update = "UPDATE ".$prefix."proveedor SET wsdl = 'SI', wsdl_respuesta = '".$cadena_respuesta."', wsdl_fecha = '".date('Y-m-d G:i:s')."' WHERE id = '".$id_generado."' ";
				$conexion->query($update);

				if($checkbox0 == 'on'){
					$json2 = array(
						'pvarCodAuxi' => $id_generado,
						'pvarNombre' => $nombre_contacto,
						'pvarNombreCamb' => $nombre_contacto,
						'pvarEmail' => $email_contacto,
						'pvarTelefono' => $telefono_contacto,
						'pvarEmpresa' => $var_empresa_wsdl
					);

					$response_ws2['response'] = $cliente->call("fnContacto", $json2);
					$cadena_respuesta2 = $response_ws2['response']['fnContactoResult'];

					if($cadena_respuesta2 == 'CREADO' || $cadena_respuesta2 == 'ACTUALIZADO'){
						$update2 = "UPDATE ".$prefix."proveedor SET wsdl_contacto = 'SI', wsdl_respuesta_contacto = '".$cadena_respuesta."', wsdl_fecha_contacto = '".date('Y-m-d G:i:s')."' WHERE id = '".$id_generado."' ";
						$conexion->query($update2);
					}else{
						$update2 = "UPDATE ".$prefix."proveedor SET wsdl_contacto = 'NO', wsdl_respuesta_contacto = '".addslashes($cadena_respuesta)."', wsdl_fecha_contacto = '".date('Y-m-d G:i:s')."' WHERE id = '".$id_generado."' ";
						$conexion->query($update2);
					}
				}
			}else{
				$update = "UPDATE ".$prefix."proveedor SET wsdl = 'NO', wsdl_respuesta = '".addslashes($cadena_respuesta)."', wsdl_fecha = '".date('Y-m-d G:i:s')."' WHERE id = '".$id_generado."' ";
				$conexion->query($update);
			}

			$_SESSION["admin_id"] = $id_generado;
			$_SESSION["admin_privilegio"] = 'comprador';

			if($checkbox0 == 'on'){
				$url_logo = $ruta_absoluta.'/supplyme-images/logo_email.png';

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
				$mail->Subject = "Notificación Nueva Cuenta Proveedor";
				$mail->AddAddress($email_empresa);

				$message  = "<html><body>";
				$message .= "<table width='100%' bgcolor='#e0e0e0' cellpadding='0' cellspacing='0' border='0'>";
				$message .= "<tr><td>";
				$message .= "<table align='center' width='100%' border='0' cellpadding='0' cellspacing='0' style='max-width:650px; background-color:#fff; font-family:Verdana, Geneva, sans-serif;'>";
				$message .= "<thead>
				  <tr height='80'>
				  <th colspan='4' style='background-color:#424242; border-bottom:solid 1px #bdbdbd; font-family:Verdana, Geneva, sans-serif; color:#333; font-size:28px;padding-top:10px;padding-bottom:10px;' ><img src=\"cid:logo\" style='margin:0 auto;'></th>
				  </tr></thead>";
				$message .= "<tbody>";
				/*$message .= "<tr align='center' height='50' style='font-family:Verdana, Geneva, sans-serif;'>
			       <td style='background-color:#00a2d1; text-align:center;'><a href='http://www.programacion.net/articulos/c' style='color:#fff; text-decoration:none;'>C</a></td>
			       <td style='background-color:#00a2d1; text-align:center;'><a href='http://www.programacion.net/articulos/php' style='color:#fff; text-decoration:none;'>PHP</a></td>
			       <td style='background-color:#00a2d1; text-align:center;'><a href='http://www.programacion.net/articulos/asp' style='color:#fff; text-decoration:none;' >ASP</a></td>
			       <td style='background-color:#00a2d1; text-align:center;'><a href='http://www.programacion.net/articulos/java' style='color:#fff; text-decoration:none;' >Java</a></td>
			       </tr>";*/
				$message .= "<tr>
					<td colspan='4' style='padding:15px;'>
					<p style='font-size:20px;'>Nueva cuenta proveedor pendiente de aprobación</p>
					<hr />
					<p style='font-size:22px;'>DETALLE</p>
					<p style='font-size:15px; font-family:Verdana, Geneva, sans-serif;'>ID Interno: <span>".$id_generado."</span></p>
					<p style='font-size:15px; font-family:Verdana, Geneva, sans-serif;'>Nombre: <span>".$nombre." ".$apellido."</span></p>
					<p style='font-size:15px; font-family:Verdana, Geneva, sans-serif;'>Email: <span>".$email."</span></p>
					<p style='font-size:15px; font-family:Verdana, Geneva, sans-serif;'>Teléfono: <span>".$telefono."</span></p>
					<p style='font-size:15px; font-family:Verdana, Geneva, sans-serif;'>Rut Empresa: <span>".$rut."</span></p>
					<p style='font-size:15px; font-family:Verdana, Geneva, sans-serif;'>Razón Social: <span>".$razon_social."</span></p>
					<hr />
					</td>
					</tr>
					<tr>
					<td colspan='4' style='padding:15px;'>
					<p style='font-size:14px;'>Puede gestionar el registro desde su <a href='".$baseurl."/administracion/'>Panel de Administración</a></p>
					<br>
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

			if($checkbox0 == 'on'){
				$respuesta = array('response' => 'success', 'message' => 'Su cuenta de cliente ha sido creada con éxito! Ya puede navegar y realizar compras en nuestro portal. Su cuenta de Proveedor aún se encuentra pendiente de aprobación, le notificaremos vía correo electrónico cuando se encuentre activa.', 'url' => 'catalogo.html');
			}else{
				$respuesta = array('response' => 'success', 'message' => 'Su cuenta de cliente ha sido creada con éxito! Ya puede navegar y realizar compras en nuestro portal.', 'url' => 'catalogo.html');
			}

			/*$aleatorio = aleatoriedad();
			$valor = "06";
			$salt = '$2y$'.$valor.'$'.$aleatorio.'$';
			$clave_crypt = crypt($password, $salt);

			$sql_insert2 = array(
				'id' => '',
				'nombre' => $nombre_contacto_vendedor.' '.$apellido_contacto_vendedor,
				'usuario' => '',
				'password' => $clave_crypt,
				'email' => $email_contacto_vendedor,
				'fecha_add' => date('Y-m-d G:i:s'),
				'fecha_last' => date('Y-m-d G:i:s'),
				'privilegio' => 'proveedor',
				'estado' => 'NO',
				'id_asociado' => $id_generado
			);
			$sql_insert2_ = ingresar_registros($prefix.'admin', $sql_insert2);
			if($result_ = $conexion->query($sql_insert2_)){
				//$id_generado = $conexion->insert_id;
				
				//$_SESSION["admin_id"] = $id_generado;
				//$_SESSION["admin_privilegio"] = 'proveedor';

				$mail = new PHPMailer();
				$mail->Host = "localhost";
				$mail->From = $noreply;
				$mail->FromName = $dominio;
				$mail->Subject = "Notificación Nueva Cuenta Proveedor";
				$mail->AddAddress($email_empresa);
				$body = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>";
				$body.= "<html xmlns='http://www.w3.org/1999/xhtml'>
				<head>
				  <title>Notificación Nueva Cuenta Proveedor ".$dominio."</title><meta name='viewport' content='width=device-width, initial-scale=1.0'/>
				</head>
				<body style='width:100%;background-color:#f5f5f5;'>
				  <div style='max-width:600px;background-color:#fff;border-top: 4px solid#60b301;margin:0 auto;'>
				    <p style='text-align:center;background:#403f44;'><img src='".$baseurl."supplyme-images/logo.png' style='margin:0 auto;' width='180'></p>";
				    $body.= "<p style='text-align:center;font-size:19px;color:#60b301;font-family:Arial;'>Nueva cuenta proveedor pendiente de aprobación</p>
				    <p style='text-align:center;font-size:17px;color:#000;font-family:Arial;'><strong>DETALLE</strong></p>";
				     $body.= "<p style='text-align:center;font-size:15px;color:#000;font-family:Arial;'><strong>ID Interno:</strong> ".$id_generado."</p>";
				    $body.= "<p style='text-align:center;font-size:15px;color:#000;font-family:Arial;'><strong>Nombre Contacto:</strong> ".$nombre_contacto_vendedor.' '.$apellido_contacto_vendedor."</p>";
				    $body.= "<p style='text-align:center;font-size:15px;color:#000;font-family:Arial;'><strong>Email Contacto:</strong> ".$email_contacto_vendedor."</p>";
				    $body.= "<p style='text-align:center;font-size:15px;color:#000;font-family:Arial;'><strong>Razón Social:</strong> ".$razon_social."</p>";
				    $body.= "<p style='text-align:center;font-size:15px;color:#000;font-family:Arial;'><strong>Rut:</strong> ".$rut."</p><br>";
				    $body.= "<p style='color:#727272;text-align:center;'><small>Por favor no responda a este correo electrónico, la cuenta no es monitoreada.</small></p>";
				    $body.= "<p style='text-align:center;font-size:15px;color:#60b301;font-weight:500;font-family:Arial;padding-bottom:20px;'>Saludos, <strong style='font-size:16px; color:#60b301;'>".$dominio."</strong></p>";
				  $body.= "
				</div>
				</body>
				</html>";
				$mail->Body = utf8_decode($body);
				$mail->IsHTML(true);
				$mail->Send();

				$respuesta = array('response' => 'success', 'message' => 'Su cuenta ha sido creada con éxito y debe ser aprobada por un moderador. Le notificaremos vía email cuando se encuentre activa.', 'url' => '');
			}else{
				$respuesta = array('response' => 'error', 'message' => 'Ha ocurrido un error al intentar ingresar el registro');
			}*/
		}else{
			$respuesta = array('response' => 'error', 'message' => 'Ha ocurrido un error al intentar ingresar el registro');
		}
	}else{
		$respuesta = array('response' => 'error', 'message' => 'El email ingresado ya se encuentra en nuestros registros, por favor <a href="'.$baseurl.'login.html">Inicie Sesión</a>');
	}

	/*if($opcregistro == 'comprador'){
		$sql = "SELECT * FROM ".$prefix."comprador WHERE email = '".$email_contacto_comprador."' LIMIT 1";
		$result = $conexion->query($sql);
		$existe_registro = $result->num_rows;

		if($existe_registro == 0){
			$aleatorio = aleatoriedad();
			$valor = "06";
			$salt = '$2y$'.$valor.'$'.$aleatorio.'$';
			$clave_crypt = crypt($password, $salt);

			$sql_insert = array(
				'id' => '',
				'nombre' => $nombre_contacto_comprador,
				'apellido' => $apellido_contacto_comprador,
				'email' => $email_contacto_comprador,
				'telefono' => $telefono_contacto_comprador,
				'password' => $clave_crypt,
				'estado' => 'activo',
				'fecha_add' => date('Y-m-d G:i:s'),
				'fecha_update' => date('Y-m-d G:i:s')
			);
			$sql_insert_ = ingresar_registros($prefix.'comprador', $sql_insert);
			if($result_ = $conexion->query($sql_insert_)){
				$id_generado = $conexion->insert_id;

				$_SESSION["comprador_id"] = $id_generado;
				$_SESSION["comprador_email"] = $email_contacto_comprador;

				$respuesta = array('response' => 'success', 'message' => 'Su cuenta ha sido creada con éxito!', 'url' => 'catalogo.html');
			}else{
				$respuesta = array('response' => 'error', 'message' => 'Ha ocurrido un error al intentar ingresar el registro');
			}
		}else{
			$respuesta = array('response' => 'error', 'message' => 'El email ingresado ya se encuentra en nuestros registros, por favor <a href="'.$baseurl.'login.html">Inicie Sesión</a>');
		}
	}*/

	echo json_encode($respuesta);
}


if($accion == 'contactFormulario'){
	$nombre = $campos['nombre'];
	$email = $campos['email'];
	$comentario = $campos['comentario'];

	if(empty($nombre)){
	    $respuesta = array( 'response' => 'error', 'message'=>'¡Ha ocurrido un error! Por favor ingrese su nombre y apellido' );
	    echo json_encode($respuesta);
	    die;
	}

	if(!is_valid_email($email) || empty($email)){
	    $respuesta = array( 'response' => 'error', 'message'=>'¡Ha ocurrido un error! Correo electrónico inválido' );
	    echo json_encode($respuesta);
	    die;
	}

	if(empty($comentario) || strlen($comentario) < 4){
	    $respuesta = array( 'response' => 'error', 'message'=>'¡Ha ocurrido un error! Por favor ingrese su comentario' );
	    echo json_encode($respuesta);
	    die;
	}

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
	$mail->Subject = "Formulario de Contacto";
	//$mail->AddAddress($email_empresa);
	$mail->AddAddress($email_empresa_contacto);

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
		<p style='font-size:20px;'>Nueva solicitud de contacto</p>
		<hr />
		<p style='font-size:22px;'>DETALLE</p>
		<p style='font-size:15px; font-family:Verdana, Geneva, sans-serif;'>Nombre: <span>".$nombre."</span></p>
		<p style='font-size:15px; font-family:Verdana, Geneva, sans-serif;'>Email: <span>".$email."</span></p>
		<p style='font-size:15px; font-family:Verdana, Geneva, sans-serif;'>Comentario: <span>".$comentario."</span></p>
		<hr />
		</td>
		</tr>
		<tr>
		<td colspan='4' style='padding:15px;'>
		<br>
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

		$sql = array(
			'id' => '',
			'nombre' => $nombre,
			'email' => $email,
			'comentario' => $comentario,
			'fecha' => date('Y-m-d G:i:s'),
			'origen' => 'contacto'
		);
		$sql_ = ingresar_registros($prefix.'contacto', $sql);
		$result_ = $conexion->query($sql_);

		$respuesta = array('response' => 'success', 'message' => 'Hemos recibido tu solicitud, ¡gracias por contactarnos!', 'url' => 'contacto.html', 'time_out' => 'true');
	}else{
		$respuesta = array( 'response' => 'error', 'message'=>'¡Ha ocurrido un error! El Formulario no ha sido enviado.' );
	}


	echo json_encode($respuesta);
}

?>