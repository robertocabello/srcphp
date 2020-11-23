<?php include('includes/config.php');

$qty = $campos['qty'];
$id = $campos['id'];
$id_ = decrypt($id, $sitio);

$id_get = explode("-", $id_);

$total_price_carro = 0;

if($id_get[1] > 0){
    $estatus_carro = '0';

    $sql = "SELECT prod.id as id, prod.codigo as codigo, prod.producto as producto, prod.categoria as categoria, prod.subcategoria as subcategoria, prod.unidad_medida as unidad_medida, prod.empaque as empaque, prod.precio as precio, prod.moneda as moneda, prod.descripcion as descripcion, prod.stock as stock, prod.estado as estado, prod.fecha_add as fecha_add, prod.fecha_update as fecha_update, prod.id_asociado as id_asociado, prod.token as token FROM ".$prefix."producto as prod, ".$prefix."proveedor as prov WHERE prod.estado = 'activo' AND prov.estado = 'activo' AND prod.id_asociado = prov.id AND prod.id = '".$id_get[1]."' LIMIT 1";
    $result = $conexion->query($sql);
    $existe_registro = $result->num_rows;
    $data_sql = $result->fetch_array(MYSQLI_BOTH);

    $precio_add = $data_sql['precio'];
    if($data_sql['stock'] == '-1'){
        $stock_actual = '100000000';
    }else{
        $stock_actual = $data_sql['stock'];
    }

    $cadena = encrypt('registro-'.$data_sql['id'], $sitio);
    
    if(!empty($_SESSION["admin_id"]) && $_SESSION["admin_privilegio"] == 'comprador'){
        $sql1 = "SELECT * FROM ".$prefix."proveedor_reglas WHERE id_cliente = '".$_SESSION["admin_id"]."' AND id_proveedor = '".$data_sql['id_asociado']."' ";
        $result1 = $conexion->query($sql1);
        $existe_regla = $result1->num_rows;

        if($existe_regla > 0){
            $estatus_carro = '-1';
        }
    }

    if($existe_registro > 0 && $estatus_carro != '-1'){
        if(!empty($_SESSION["admin_id"])){
            $sql2 = "SELECT * FROM ".$prefix."producto_precio WHERE id_producto = '".$data_sql['id']."' AND id_comprador = '".$_SESSION["admin_id"]."' LIMIT 1 ";
            $result2 = $conexion->query($sql2);
            $existe_precio_especial = $result2->num_rows;
            $data_precio_especial = $result2->fetch_array(MYSQLI_BOTH);

            if($existe_precio_especial > 0){ $precio_add = $data_precio_especial['precio']; }
        }

        $sql3 = "SELECT * FROM ".$prefix."producto_imagen WHERE token = '".$data_sql['token']."' AND portada = 'SI' LIMIT 1 ";
        $result3 = $conexion->query($sql3);
        $data_imagen_portada = $result3->fetch_array(MYSQLI_BOTH);
        if($data_imagen_portada['archivo'] == ''){
            $sql4 = "SELECT * FROM ".$prefix."producto_imagen WHERE token = '".$data_sql['token']."' ORDER BY RAND() LIMIT 1 ";
            $result4 = $conexion->query($sql4);
            $data_imagen_portada = $result4->fetch_array(MYSQLI_BOTH);
        }

        if(empty($_SESSION["cart_id"])){
            $token = aleatoriedad();
            $_SESSION["cart_id"] = $token;
        }else{
            $token = $_SESSION["cart_id"];
        }

        if(!empty($token)){
            //$token = aleatoriedad();
            //$_SESSION["cart_id"] = aleatoriedad();

            $sql5 = "SELECT * FROM ".$prefix."carro_detalle WHERE token = '".$token."' AND id_producto = '".$data_sql['id']."' ";
            $result5 = $conexion->query($sql5);
            $existe_en_carro = $result5->num_rows;
            $data_carro_actual = $result5->fetch_array(MYSQLI_BOTH);

            if($existe_en_carro > 0){
                if(($stock_actual) < ($data_carro_actual['cantidad'] + $qty)){
                    $stock_query = $stock_actual;
                }else{
                    $stock_query = $data_carro_actual['cantidad'] + $qty;
                }

                $update1 = "UPDATE ".$prefix."carro_detalle SET cantidad = ".$stock_query." WHERE token = '".$token."' AND id_producto = '".$data_sql['id']."' ";
                if($conexion->query($update1)){
                    $estatus_carro = '1';
                }
            }else{
                if($stock_actual < $qty){
                    $stock_query = $stock_actual;
                }else{
                    $stock_query = $qty;
                }

                $sql_insert = array(
                    'id' => '',
                    'token' => $token,
                    'id_producto' => $id_get[1],
                    'producto' => $data_sql['producto'],
                    'cantidad' => $stock_query,
                    'precio' => $precio_add,
                    'id_proveedor' => $data_sql['id_asociado'],
                    'fecha_add' => date('Y-m-d G:i:s')
                );
                $sql_ = ingresar_registros($prefix.'carro_detalle', $sql_insert);
                if($result_ = $conexion->query($sql_)){
                    $estatus_carro = '1';
                }
            }

            $sql20 = "SELECT id, token, id_producto, producto, SUM(cantidad) as suma_cantidad, precio, id_proveedor, fecha_add FROM ".$prefix."carro_detalle WHERE token = '".$token."' AND id_proveedor = '".$data_sql['id_asociado']."' GROUP BY id_producto ";
            $result20 = $conexion->query($sql20);
            $data_productos = $result20->fetch_array(MYSQLI_BOTH);

            if($data_sql['stock'] == '-1'){
                $item_qty_actual = $data_productos['suma_cantidad'];
                $item_max = '10000000';
            }else{
                if($data_sql['stock'] < $data_productos['suma_cantidad']){
                    $item_qty_actual = $data_sql['stock'];
                }else{
                    $item_qty_actual = $data_productos['suma_cantidad'];
                }
                $item_max = $data_sql['stock'];
            }


            $sql5 = "SELECT * FROM ".$prefix."carro_detalle WHERE token = '".$token."' ";
            $result5 = $conexion->query($sql5);
            $total_qty_carro = $result5->num_rows;
            while($data_carro = $result5->fetch_array(MYSQLI_BOTH)){
                $data_precio = str_replace("$ ", "", $data_carro['precio']);
                $data_precio = str_replace(".", "", $data_precio);

                $total_price_carro = $total_price_carro + ($data_carro['cantidad'] * $data_precio);
            }

            $sql6 = "SELECT SUM(cantidad) as suma FROM ".$prefix."carro_detalle WHERE token = '".$token."' ";
            $result6 = $conexion->query($sql6);
            $data_carro_actual = $result6->fetch_array(MYSQLI_BOTH);
            $qty_carro_actual = $data_carro_actual['suma'];

            $sql7 = "SELECT SUM(cantidad) as suma FROM ".$prefix."carro_detalle WHERE token = '".$token."' AND id_producto = '".$id_get[1]."' ";
            $result7 = $conexion->query($sql7);
            $data_carro_this = $result7->fetch_array(MYSQLI_BOTH);
            $qty_carro_actual_this = $data_carro_this['suma'];

            

        }
?>

<?php if($estatus_carro == '1'){ ?>
<div id="modalCheckOut" class="modal--checkout">
    <div class="modal-header">
        <div class="modal-header-title"><i class="icon icon-check-box"></i><span>Producto añadido a tu carrito</span></div>
    </div>
    <div class="modal-content">
        <div class="modal-body">
            <div class="modalchk-prd">
                <div class="row h-font">
                    <div class="modalchk-prd-image col">
                        <?php if($data_imagen_portada['archivo'] != ''){ ?>
                            <img src="<?php echo $baseurl; ?>/supplyme-images/productos/<?php echo $data_imagen_portada['archivo']; ?>" alt="<?php echo $data_sql['producto']; ?>">
                        <?php }else{ ?>
                            <img src="<?php echo $baseurl; ?>/supplyme-images/default_img.jpg" alt="<?php echo $data_sql['producto']; ?>">
                        <?php } ?>
                    </div>
                    <div class="modalchk-prd-info col">
                        <h2 class="modalchk-title"><?php echo $data_sql['producto']; ?></h2>
                        <div class="modalchk-price"><?php echo formato_precio($precio_add); ?></div>
                        <div class="qty qty-changer">
                            <span class="label-options">Cantidad:</span>
                            <fieldset>
                                <input type="button" value="&#8210;" class="decrease">
                                <input type="text" class="qty-input" value="<?php echo $qty_carro_actual_this; ?>" data-item="<?php echo $cadena; ?>" data-min="1" data-max="<?php echo $item_max; ?>">
                                <input type="button" value="+" class="increase">
                            </fieldset>
                        </div>
                        <!--<div class="prd-options"><span class="label-options">Cantidad:</span><span class="prd-options-val"><?php echo $qty; ?></span></div>-->
                    </div>
                    <div class="modalchk-prd-actions col">
                        <h3 class="modalchk-title">Hay <span class="custom-color"><?php echo $qty_carro_actual; ?></span> artículos en tu carrito</h3>
                        <div class="prd-options"><span class="label-options">Total:</span><span class="modalchk-total-price"><?php echo formato_precio($total_price_carro); ?></span></div>
                        <div class="modalchk-custom"><img src="<?php echo $baseurl; ?>supplyme-images/pago-webpay.png" width="120" alt="Webpay Plus <?php echo $sitio; ?>"></div>
                        <div class="modalchk-btns-wrap"><a href="<?php echo $baseurl; ?>carro.html" class="btn">Ir a la caja</a> <a href="#" class="btn btn--alt" data-fancybox-close>Continuar comprando</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php }else{ ?>

<div id="modalCheckOut" class="modal--checkout">
    <div class="modal-header">
        <div class="modal-header-title"><span>Error</span></div>
    </div>
    <div class="modal-content">
        <div class="modal-body">
            <h4>Ha ocurrido un error al realizar la acción</h4>
        </div>
    </div>
</div>

<?php } ?>

<?php }else{ ?>

<div id="modalCheckOut" class="modal--checkout">
    <div class="modal-header">
        <div class="modal-header-title"><span>Error</span></div>
    </div>
    <div class="modal-content">
        <div class="modal-body">
            <h4><?php if($estatus_carro == '-1'){ ?>Proveedor deshabilitado. No es posible añadir el producto seleccionado a su carro de compra.<?php }else{ ?>Producto no encontrado<?php } ?></h4>
        </div>
    </div>
</div>

<?php } } ?>

<script type="text/javascript">
    $('span.minicart-qty').html('<?php echo $qty_carro_actual; ?>');

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

                setTimeout(function () {
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

                            if(obj.carro_existente != ''){
                                $('.modalchk-title span.custom-color').text(obj.carro_existente);
                                $('span.minicart-qty').html(obj.carro_existente);
                            }
                            if(obj.total_carro_actual != ''){
                                $('span.modalchk-total-price').html(obj.total_carro_actual);
                            }
                            //validaciones();
                        }

                        if(obj.reload == 'true'){
                          location.reload();
                        }else{
                          /*if(obj.precio_unitario != ''){
                            $this.closest('.cart-table-prd').find('span.precio_unitario').text(obj.precio_unitario);
                          }
                          if(obj.total_item != ''){
                            $this.closest('.cart-table-prd').find('span.precio_linea').text(obj.total_item);
                          }*/
                          /*if(obj.total_carro != ''){
                            $('span.card-total-price').text(obj.total_carro);
                            $('span.cart-total-price').text(obj.total_carro);
                          }*/
                        }

                      }
                    });
                }, 500);
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
        
        setTimeout(function () {
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
                    if(obj.carro_existente != ''){
                        $('.modalchk-title span.custom-color').text(obj.carro_existente);
                        $('span.minicart-qty').html(obj.carro_existente);
                    }
                    if(obj.total_carro_actual != ''){
                        $('span.modalchk-total-price').html(obj.total_carro_actual);
                    }
                    //validaciones();
                }

                if(obj.reload == 'true'){
                  location.reload();
                }else{
                  /*if(obj.precio_unitario != ''){
                    input.closest('.cart-table-prd').find('span.precio_unitario').text(obj.precio_unitario);
                  }
                  if(obj.total_item != ''){
                    input.closest('.cart-table-prd').find('span.precio_linea').text(obj.total_item);
                  }*/
                  /*if(obj.total_carro != ''){
                    $('span.card-total-price').text(obj.total_carro);
                    $('span.cart-total-price').text(obj.total_carro);
                  }*/
                }

              }
            });
        }, 500);
    });
</script>