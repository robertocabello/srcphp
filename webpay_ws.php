<?php
	include('includes/config.php');
	include("class.phpmailer.php");

	require_once('webpay-sdk-php/libwebpay/webpay.php');
	require_once('webpay-sdk-php/cert-normal.php');
	include('includes/dompdf/dompdf_config.inc.php');

	$id_pedido = $campos['id_pedido'];


	$webpay_settings = array(
		"MODO" => "PRODUCCION",
		"PRIVATE_KEY" => $certificate['private_key'],
		"PUBLIC_CERT" => $certificate['public_cert'],
		"WEBPAY_CERT" => $certificate['webpay_cert'],
		"COMMERCE_CODE" => $certificate['commerce_code'],
		"URL_RETURN" => $baseurl."webpay_ws.php?action=result",
		"URL_FINAL" => $baseurl."webpay_ws.php?action=end",
	);


	$webpay = new WebPaySOAP($webpay_settings); // Creación objeto WebPay
	$webpay = $webpay->getNormalTransaction(); // Creación de Transaccion Normal con certificados y llave privada para ambiente de integración

	$action = isset($_GET["action"])? $_GET["action"]: 'init'; // Resultado action

	switch ($action){
		default: 
	 		$tx_step = "Init";
	  		
			if($id_pedido > 0){

				$sql = "SELECT * FROM ".$prefix."webpay WHERE id_webpay = '".$id_pedido."' AND status='1' ";
	    		$resultA = $conexion->query($sql);
	    		$data_webpay = $resultA->fetch_array(MYSQLI_BOTH);

	    		if($data_webpay['total'] > 0){
	    			$id = $data_webpay['id_webpay'];
	    			$cost = $data_webpay['total'];

	    			$titulo_log = 'log-'.$id_pedido.'.log';

					$archivo_log = "./webpay-sdk-php/logs/".$titulo_log;
					$registro_log = fopen($archivo_log, "a+");
					fwrite($registro_log, "Inicio Transacción"."\n");
	    		}

			}

			//Datos a enviar
	  		$request = array(
	  			"amount"    => $cost, // monto a cobrar 
	  			"buyOrder"  => $id, // numero orden de compra
	  			"sessionId" => uniqid(), // idsession local 
	  		);

	        // Iniciamos Transaccion
	 		$result = $webpay->initTransaction($request["amount"], $request["sessionId"], $request["buyOrder"]);
			$webpay_token = $result["token_ws"];

			$sql2 = "UPDATE ".$prefix."webpay SET webpay_token = '".$webpay_token."' WHERE id_webpay = '".$request["buyOrder"]."' AND status='1' ";
			$conexion->query($sql2);

			// Verificamos respuesta de inicio en webpay
			if (strlen($webpay_token)) {

				/* Log RESPONSE init */
 				fwrite($registro_log, 'RESPONSE('); 
 				while (list($key, $val) = each($result)){
			        fwrite($registro_log, "$key=$val;"."\n");
			    }
			    fwrite($registro_log, ');');
			    /* Fin Log RESPONSE init */

				$next_page = $result["url"];

			} else {
				//$webpay_token = $_POST["token_ws"];
				$webpay_token = $idorden;
	            $next_page = $baseurl.'error_en_el_pago.php';
			}

			break;

		case "result":

	 		$tx_step = "Get Result";
			if (!isset($_POST["token_ws"])) break;
			
			$webpay_token = $_POST["token_ws"];
			$request = array( 
				"token"  => $_POST["token_ws"]
			);

			$sql_comprobacion = "SELECT * FROM ".$prefix."webpay WHERE webpay_token = '".$webpay_token."' ";
	    	$result_comprobacion = $conexion->query($sql_comprobacion);
	    	$comprobacion = mysqli_num_rows($result_comprobacion);

			// Rescatamos resultado y datos de la transaccion
			$result = $webpay->getTransactionResult($request["token"]);

			// Verificamos resultado del pago
			if ($result->detailOutput->responseCode === 0 AND $comprobacion > 0){

				$fecha = strtotime($result->transactionDate);
				$fecha = strftime('%Y-%m-%d %H:%M:%S', $fecha);

				/*Orden de compra enviada por el comercio al inicio de la transacción*/
				//$result->buyOrder;
				/*ID sesión local*/
				//$result->sessionId;
				/*Número tarjeta*/
				//$result->cardDetail->cardNumber;
				/*Expiración tarjeta*/
				//$result->cardDetail->cardExpirationDate;
				/*Código de autorización*/
				//$result->detailOutput->authorizationCode;
				/*Tipo de Pago*/
				//$result->detailOutput->paymentTypeCode;
				/*Número de cuotas*/
				//$result->detailOutput->sharesNumber;
				/*Monto de la transacción*/
				//$result->detailOutput->amount;
				/*Fecha transacción*/
				//$result->transactionDate;

				$sql_ = "SELECT * FROM ".$prefix."webpay WHERE webpay_token = '".$_POST["token_ws"]."' ";
		    	$result_ = $conexion->query($sql_);
		    	$data_webpay = $result_->fetch_array(MYSQLI_BOTH);

		    	$sql_p = "SELECT * FROM ".$prefix."pedido WHERE token = '".$data_webpay['token_carro']."' ";
	    		$result_p = $conexion->query($sql_p);
	    		$data_pedido = $result_p->fetch_array(MYSQLI_BOTH);

		    	if($result->detailOutput->paymentTypeCode == 'VN'){ $tipo_pago = "Crédito"; $tipo_cuotas = "Sin Cuotas"; } 
				if($result->detailOutput->paymentTypeCode == 'VC'){ $tipo_pago = "Crédito"; $tipo_cuotas = "Cuotas normales"; }
				if($result->detailOutput->paymentTypeCode == 'SI'){ $tipo_pago = "Crédito"; $tipo_cuotas = "Sin interés"; }
				if($result->detailOutput->paymentTypeCode == 'CI'){ $tipo_pago = "Crédito"; $tipo_cuotas = "Cuotas Comercio"; } 
				if($result->detailOutput->paymentTypeCode == 'VD'){ $tipo_pago = "Débito"; $tipo_cuotas = "Venta Débito"; }

				$sql_update1 = "UPDATE ".$prefix."webpay SET status = '2', numero_tarjeta='".$result->cardDetail->cardNumber."', exp_tarjeta='".$result->cardDetail->cardExpirationDate."', codigo_autorizacion='".$result->detailOutput->authorizationCode."', tipo_pago='".$result->detailOutput->paymentTypeCode."', numero_cuotas='".$result->detailOutput->sharesNumber."' WHERE webpay_token='".$_POST['token_ws']."' ";
				if($conexion->query($sql_update1)){

					$sql_update2 = "UPDATE ".$prefix."pedido SET status_pago = '2', metodo_pago='webpay plus' WHERE token='".$data_webpay['token_carro']."' ";
					if($conexion->query($sql_update2)){

						$sql_pdf = "SELECT * FROM ".$prefix."pedido WHERE token = '".$data_webpay['token_carro']."' LIMIT 1";
						$result_pdf = $conexion->query($sql_pdf);
						$data_sql_pdf = $result_pdf->fetch_array(MYSQLI_BOTH);

						if($data_sql_pdf['status_pago'] == '1'){ $estado_pago = '<span style="color:#444;">PENDIENTE</span>'; }
				  		if($data_sql_pdf['status_pago'] == '2'){ $estado_pago = '<span style="color:#444;">PAGADO</span>'; }

				  		if($data_sql_pdf['metodo_pago'] == 'webpay plus'){ $metodo_pago = '<span style="color:#444;">Webpay plus</span>'; }
				  		if($data_sql_pdf['metodo_pago'] == 'credito'){ $metodo_pago = '<span style="color:#444;">Crédito</span>'; }


				  		$sql_masivo = "SELECT id_proveedor FROM ".$prefix."carro_detalle WHERE token = '".$data_sql_pdf['token']."' GROUP BY id_proveedor ";
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
							                    <tr><td><h2>Orden de compra Nº '.$data_sql_pdf['id'].'</h2></td></tr>
							                    <tr><td><p><strong>Dirección:</strong> '.$data_sql_pdf['direccion'].'</p></td></tr>
							                    <tr><td><p><strong>Comuna:</strong> '.$data_sql_pdf['comuna'].'</p></td></tr>
							                    <tr><td><p><strong>Ciudad:</strong> '.$data_sql_pdf['ciudad'].'</p><br><br><br></td></tr>
							                </table>
							            </td>
							            <td valign="top">
							                <table border="0" cellspadding="0" cellspacing="0">
							                    <tr>
							                        <td><br><br><strong>Fecha:</strong></td>
							                        <td><br><br>'.$data_sql_pdf['fecha_add'].'</td>
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
							        $sql = "SELECT cd.producto as producto, cd.cantidad as cantidad, cd.precio as precio_unitario, pro.razon_social as razon_social, prod.codigo as codigo, prod.unidad_medida as unidad_medida FROM ".$prefix."carro_detalle as cd, ".$prefix."proveedor as pro, ".$prefix."producto as prod WHERE cd.token = '".$data_sql_pdf['token']."' AND cd.id_proveedor = pro.id AND cd.id_producto = prod.id ";
									$sql .= " AND pro.id = '".$data_sql_while_masivo["id_proveedor"]."' ";
									$resultB = $conexion->query($sql);
									while($data_sql_while = $resultB->fetch_array(MYSQLI_BOTH)){

										$precio_unitario_item = formato_int($data_sql_while['precio_unitario']);
			                            $precio_total_item = ($precio_unitario_item*$data_sql_while['cantidad']);

			                            $precio_por_proveedor = $precio_por_proveedor + $precio_total_item;
			                            $iva_por_proveedor = $precio_por_proveedor * 0.19;

			                            $resta_por_proveedor = $precio_por_proveedor - $iva_por_proveedor;

										$body_html .= '<tr>';
										$body_html .= '<td>'.$data_sql_while['razon_social'].'</td>';
										$body_html .= '<td>'.$data_sql_while['codigo'].'</td>';
										$body_html .= '<td>'.$data_sql_while['producto'].'</td>';
										$body_html .= '<td>'.$data_sql_while['cantidad'].' '.$data_sql_while['unidad_medida'].'</td>';
										$body_html .= '<td>'.formato_precio($data_sql_while['precio_unitario']).'</td>';
										$body_html .= '<td>'.formato_precio($data_sql_while['cantidad'] * formato_int($data_sql_while['precio_unitario'])).'</td>';
										$body_html .= '</tr>';
									}

									$body_html .= '<tr><td colspan="5"></td><td><strong>TOTAL: '.formato_precio($precio_por_proveedor).'</strong></td></tr>';

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
							    $pdf_proveedor = 'pedidos/pedido'.$data_sql_pdf['token'].'-'.$data_sql_info['id'].'.pdf';
							    $pdf_proveedor_title = $data_sql_pdf['token'].'-'.$data_sql_info['id'].'.pdf';

							    file_put_contents('pedidos/pedido'.$data_sql_pdf['token'].'-'.$data_sql_info['id'].'.pdf', $pdf);

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
									<p style='font-size:20px;'>Orden de compra Nº ".$data_pedido['id']."</p>
									<hr />
									<p style='font-size:22px;'>DETALLE</p>
									<p style='font-size:15px; font-family:Verdana, Geneva, sans-serif;'>Dirección: <span>".$data_pedido['direccion']."</span></p>
									<p style='font-size:15px; font-family:Verdana, Geneva, sans-serif;'>Comuna: <span>".$data_pedido['comuna']."</span></p>
									<p style='font-size:15px; font-family:Verdana, Geneva, sans-serif;'>Ciudad: <span>".$data_pedido['ciudad']."</span></p>
									<p style='font-size:15px; font-family:Verdana, Geneva, sans-serif;'>Monto Total: <span>".formato_precio($precio_por_proveedor)."</span></p>
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
					                    <tr><td><h2>Orden de compra Nº '.$data_sql_pdf['id'].'</h2></td></tr>
					                    <tr><td><p><strong>Dirección:</strong> '.$data_sql_pdf['direccion'].'</p></td></tr>
					                    <tr><td><p><strong>Comuna:</strong> '.$data_sql_pdf['comuna'].'</p></td></tr>
					                    <tr><td><p><strong>Ciudad:</strong> '.$data_sql_pdf['ciudad'].'</p><br><br><br></td></tr>
					                </table>
					            </td>
					            <td valign="top">
					                <table border="0" cellspadding="0" cellspacing="0">
					                    <tr>
					                        <td><br><br><strong>Fecha:</strong></td>
					                        <td><br><br>'.$data_sql_pdf['fecha_add'].'</td>
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

					        $sql = "SELECT cd.producto as producto, cd.cantidad as cantidad, cd.precio as precio_unitario, pro.razon_social as razon_social, prod.codigo as codigo, prod.unidad_medida as unidad_medida FROM ".$prefix."carro_detalle as cd, ".$prefix."proveedor as pro, ".$prefix."producto as prod WHERE cd.token = '".$data_sql_pdf['token']."' AND cd.id_proveedor = pro.id AND cd.id_producto = prod.id ";
							if($_SESSION["admin_privilegio"] == 'proveedor'){
								$sql .= " AND pro.id = '".$_SESSION["admin_id"]."' ";
							}
							$resultC = $conexion->query($sql);
							while($data_sql_while = $resultC->fetch_array(MYSQLI_BOTH)){
								$body_html .= '<tr>';
								$body_html .= '<td>'.$data_sql_while['razon_social'].'</td>';
								$body_html .= '<td>'.$data_sql_while['codigo'].'</td>';
								$body_html .= '<td>'.$data_sql_while['producto'].'</td>';
								$body_html .= '<td>'.$data_sql_while['cantidad'].' '.$data_sql_while['unidad_medida'].'</td>';
								$body_html .= '<td>'.formato_precio($data_sql_while['precio_unitario']).'</td>';
								$body_html .= '<td>'.formato_precio($data_sql_while['cantidad'] * formato_int($data_sql_while['precio_unitario'])).'</td>';
								$body_html .= '</tr>';
							}

							$iva_calculo = ($data_sql_pdf['total'] - ($data_sql_pdf['total']/1.19));

							$body_html .= '<tr><td colspan="4"></td><td><strong>SUBTOTAL: </strong></td><td><strong>'.formato_precio($data_sql_pdf['total']/1.19).'</strong></td></tr>';
							$body_html .= '<tr><td colspan="4"></td><td><strong>IVA (19%): </strong></td><td><strong>'.formato_precio($iva_calculo).'</strong></td></tr>';
							$body_html .= '<tr><td colspan="4"></td><td><strong>TOTAL: </strong></td><td><strong>'.formato_precio($data_sql_pdf['total']).'</strong></td></tr>';

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
					    $pdf_general = 'pedidos/pedido'.$data_sql_pdf['token'].'.pdf';
					    $pdf_general_title = $data_sql_pdf['token'].'.pdf';

					    file_put_contents('pedidos/pedido'.$data_sql_pdf['token'].'.pdf', $pdf);


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
						$mail->addAttachment($pdf_general, $pdf_general_title);
						$mail->AddAddress($data_pedido['email']);
						//$mail->AddAddress($email_empresa);
						$mail->addBCC($email_empresa);

						$iva_calculo = ($data_pedido['total'] - ($data_pedido['total']/1.19));

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
							<p style='font-size:20px;'>Orden de compra Nº ".$data_pedido['id']."</p>
							<hr />
							<p style='font-size:22px;'>DETALLE</p>
							<p style='font-size:15px; font-family:Verdana, Geneva, sans-serif;'>Dirección: <span>".$data_pedido['direccion']."</span></p>
							<p style='font-size:15px; font-family:Verdana, Geneva, sans-serif;'>Comuna: <span>".$data_pedido['comuna']."</span></p>
							<p style='font-size:15px; font-family:Verdana, Geneva, sans-serif;'>Ciudad: <span>".$data_pedido['ciudad']."</span></p>
							<p style='font-size:15px; font-family:Verdana, Geneva, sans-serif;'>Subtotal: <span>".formato_precio($data_pedido['total']/1.19)."</span></p>
							<p style='font-size:15px; font-family:Verdana, Geneva, sans-serif;'>IVA (19%): <span>".formato_precio($iva_calculo)."</span></p>
							<p style='font-size:15px; font-family:Verdana, Geneva, sans-serif;'>Monto Total: <span>".formato_precio($data_pedido['total'])."</span></p>
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

						$sql__ = "SELECT * FROM ".$prefix."carro_detalle WHERE token = '".$data_webpay['token_carro']."' ";
					    $result__ = $conexion->query($sql__);
					    while($carro_actual = $result__->fetch_array(MYSQLI_BOTH)){
					    	$cantidad = $carro_actual['cantidad'];

					    	$sql__pro = "SELECT * FROM ".$prefix."producto WHERE id = '".$carro_actual['id_producto']."' ";
						    $result__pro = $conexion->query($sql__pro);
						    $producto_actual = $result__pro->fetch_array(MYSQLI_BOTH);
						    if($producto_actual['stock'] != '-1'){
						    	$sql_update3 = "UPDATE ".$prefix."producto SET stock = stock-".$cantidad." WHERE id='".$carro_actual['id_producto']."' ";
								$conexion->query($sql_update3);
						    }
					    }
					}

				}

				$next_page = $result->urlRedirection;
				$next_page_title = "Finalizar Pago";
				
			} else {
				//$webpay_token = $_POST["token_ws"];

				$webpay_token = $result->buyOrder;
	            $next_page = $baseurl.'error_en_el_pago.php';
			}

			break;

		case "end":
	 		$tx_step = "End";
			$request= '';
			$result = $_POST;
			$webpay_token = $_POST["token_ws"];

		    if($webpay_token!=''){
				$next_page = $baseurl.'exito.php';
			}else{
				$webpay_token = $_POST['TBK_ORDEN_COMPRA'];
	            $next_page = $baseurl.'error_en_el_pago.php';
			}

			break;
	}

	if(strlen($next_page)){
		echo '<body onload="document.formulariows.submit();"><form action="'.$next_page.'" method="post" id="formulariows" name="formulariows">
			<input type="hidden" name="token_ws" value="'.$webpay_token.'">
		</form></body>';
	}
?>