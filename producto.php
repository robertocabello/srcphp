<?php include('includes/config.php');

$token = $campos['token'];
$existe_precio_especial = 0;

if(!empty($token)){
    $sql = "SELECT * FROM ".$prefix."producto WHERE token = '".$token."' LIMIT 1";
    $result = $conexion->query($sql);
    $data_sql = $result->fetch_array(MYSQLI_BOTH);

    if($data_sql['estado'] != 'activo'){
        header ("Location: catalogo.html");
    }else{
        $cadena = encrypt('registro-'.$data_sql['id'], $sitio);

        if($data_sql['stock'] == '-1'){
            //$item_qty_actual = $data_productos['suma_cantidad'];
            $item_max = '10000000';
        }else{
            /*if($data_sql['stock'] < $data_productos['suma_cantidad']){
                $item_qty_actual = $data_sql['stock'];
            }else{
                $item_qty_actual = $data_productos['suma_cantidad'];
            }*/
            $item_max = $data_sql['stock'];
        }

        $update = "UPDATE ".$prefix."producto SET vistas = vistas + 1 WHERE token = '".$token."' ";
        $conexion->query($update);

        $sql2 = "SELECT * FROM ".$prefix."producto as prod, ".$prefix."proveedor as prov WHERE prod.categoria = '".$data_sql['categoria']."' AND prod.estado = 'activo' AND prov.estado = 'activo' AND prod.id_asociado = prov.id AND prod.id <> '".$data_sql['id']."' ";
        //echo $sql2;
        //$sql = "SELECT prod.id as id, prod.codigo as codigo, prod.producto as producto, prod.categoria as categoria, prod.subcategoria as subcategoria, prod.unidad_medida as unidad_medida, prod.empaque as empaque, prod.precio as precio, prod.moneda as moneda, prod.descripcion as descripcion, prod.stock as stock, prod.estado as estado, prod.fecha_add as fecha_add, prod.fecha_update as fecha_update, prod.id_asociado as id_asociado, prod.token as token FROM ".$prefix."producto as prod, ".$prefix."proveedor as prov WHERE prod.estado = 'activo' AND prov.estado = 'activo' AND prod.id_asociado = prov.id AND prod.categoria = '".$data_sql['categoria']."' AND prod.id <> '".$data_sql['id']."' ORDER BY RAND() LIMIT 12";
        $result2 = $conexion->query($sql2);
        $qty_relacionados_por_categoria = $result2->num_rows;

        $sql2 = "SELECT * FROM ".$prefix."producto as prod, ".$prefix."proveedor as prov WHERE prod.id_asociado = '".$data_sql['id_asociado']."' AND prod.estado = 'activo' AND prov.estado = 'activo' AND prod.id_asociado = prov.id AND prod.id <> '".$data_sql['id']."' ";
        $result2 = $conexion->query($sql2);
        $qty_relacionados_por_proveedor = $result2->num_rows;

        $sql2 = "SELECT * FROM ".$prefix."producto_imagen WHERE token = '".$data_sql['token']."' AND portada = 'SI' LIMIT 1 ";
        $result2 = $conexion->query($sql2);
        $qty_imagenes = $result2->num_rows;
        $portada_producto = $result2->fetch_array(MYSQLI_BOTH);
        if($portada_producto['archivo'] == ''){
            $sql3 = "SELECT * FROM ".$prefix."producto_imagen WHERE token = '".$data_sql['token']."' ORDER BY RAND() LIMIT 1 ";
            $result3 = $conexion->query($sql3);
            $qty_imagenes = $result3->num_rows;
            $portada_producto = $result3->fetch_array(MYSQLI_BOTH);
        }

        $sql2 = "SELECT * FROM ".$prefix."medidas WHERE codigo = '".$data_sql['unidad_medida']."' ";
        $result2 = $conexion->query($sql2);
        $unidad_medida = $result2->fetch_array(MYSQLI_BOTH);

        $sql2 = "SELECT * FROM ".$prefix."categorias WHERE id = '".$data_sql['categoria']."' ";
        $result2 = $conexion->query($sql2);
        $data_categoria = $result2->fetch_array(MYSQLI_BOTH);

        $sql2 = "SELECT * FROM ".$prefix."subcategorias WHERE id = '".$data_sql['subcategoria']."' ";
        $result2 = $conexion->query($sql2);
        $data_subcategoria = $result2->fetch_array(MYSQLI_BOTH);

        if(!empty($_SESSION["admin_id"])){
            $sql2 = "SELECT * FROM ".$prefix."producto_precio WHERE id_producto = '".$data_sql['id']."' AND id_comprador = '".$_SESSION["admin_id"]."' LIMIT 1 ";
            $result2 = $conexion->query($sql2);
            $existe_precio_especial = $result2->num_rows;
            $data_precio_especial = $result2->fetch_array(MYSQLI_BOTH);
        }

        if($existe_precio_especial > 0){
            $precio_publicado = ((formato_int($data_precio_especial['precio']) * $margen) + formato_int($data_precio_especial['precio']));
        }else{
            $precio_publicado = ((formato_int($data_sql['precio']) * $margen) + formato_int($data_sql['precio']));
        }
    }

    if(empty($_SESSION["cart_id"])){
        $token_cart = aleatoriedad();
        $_SESSION["cart_id"] = $token_cart;
    }else{
        $token_cart = $_SESSION["cart_id"];
    }

    $sql6 = "SELECT SUM(cantidad) as suma FROM ".$prefix."carro_detalle WHERE token = '".$token_cart."' ";
    $result6 = $conexion->query($sql6);
    $data_carro_actual = $result6->fetch_array(MYSQLI_BOTH);
    $qty_carro_actual = $data_carro_actual['suma'];
}else{
    header ("Location: catalogo.html");
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1">
    <meta name="description" content="<?php echo getSubString($data_sql['descripcion'], 160); ?>">
    <meta name="author" content="<?php echo $author; ?>">
    <base href="<?php echo $baseurl; ?>">

    <title><?php echo $data_sql['producto']; ?> | Supplyme</title>

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
</head>

<body class="home-page is-dropdn-click has-slider" id="detalle">
    
    <?php include('header.php'); ?>

    <div class="page-content">
        <div class="holder mt-0 holder-gris padding-top-20">

            <div class="container">

                <div class="row">

                    <!-- Left column -->
                    <div class="col-lg-3 aside aside--left fixed-col js-filter-col">
                        <div class="fixed-col_container">
                            <div class="contentfilter-block">
                                <div class="margin-bottom-15">
                                    <span class="label-filter">Todas las categorías</span>
                                    <span class="icon">-</span>
                                </div>
                                <div class="filter-close">CLOSE</div>
                                <div class="sidebar-block sidebar-block--mobile d-block d-lg-none">
                                    <div class="d-flex align-items-center">
                                        <div class="selected-label">(6) FILTER</div>
                                        <div class="selected-count ml-auto">SELECTED <span><b>25 items</b></span></div>
                                    </div>
                                </div>
                                <!--<div class="sidebar-block filter-group-block open">
                                    <div class="sidebar-block_title"><span>Current Selection</span>
                                        <div class="toggle-arrow"></div>
                                    </div>
                                    <div class="sidebar-block_content">
                                        <div class="selected-filters-wrap">
                                            <ul class="selected-filters">
                                                <li><a href="#">Men</a></li>
                                                <li><a href="#">Red</a></li>
                                                <li><a href="#">Nike</a></li>
                                                <li><a href="#">Above $200</a></li>
                                                <li><a href="#">S</a></li>
                                            </ul>
                                            <div class="d-flex align-items-center"><a href="#" class="clear-filters"><span>Clear All</span></a>
                                                <div class="selected-count ml-auto d-none d-lg-block">SELECTED<span><b>25 items</b></span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>-->
                                <div class="sidebarleft-collapsed open">
                                    <?php
                                        $contador1 = 0;
                                        $sql = "SELECT * FROM ".$prefix."categorias WHERE estado = 'activo' ORDER BY categoria ASC ";
                                        $result = $conexion->query($sql);
                                        while($data_sql_while = $result->fetch_array(MYSQLI_BOTH)){
                                            $contador1 = $contador1 + 1;

                                            $sql_2 = "SELECT * FROM ".$prefix."subcategorias WHERE estado = 'activo' AND categoria = '".$data_sql_while['id']."' ORDER BY subcategoria ASC ";
                                            $result_2 = $conexion->query($sql_2);
                                            $qty_subcategorias = $result_2->num_rows;
                                    ?>
                                    <div class="sidebar-block filter-group-block collapsed <?php if($data_sql['categoria'] == $data_sql_while['codigo']){ ?>open item-selected<?php } ?>">
                                        <div class="sidebar-block_title"><span><a href="<?php echo $baseurl; ?>catalogo/productos/<?php echo $data_sql_while['seourl']; ?>/todas/pag-1/precio-todos/orden-todos.html"><?php echo $data_sql_while['categoria']; ?></a></span><?php if($qty_subcategorias > 0){ ?><span class="icon"><?php if($data_sql['codigo'] == $data_sql_while['codigo']){ echo '-'; }else{ echo '+'; } ?></span><?php } ?></div>
                                        <?php if($qty_subcategorias > 0){ ?>
                                        <div class="sidebar-block_content">
                                            <ul class="category-list">
                                                <?php
                                                    while($data_sql_while_2 = $result_2->fetch_array(MYSQLI_BOTH)){
                                                ?>
                                                    <li <?php if($data_sql['subcategoria'] == $data_sql_while_2['codigo']){ ?>class="active"<?php } ?>><a href="<?php echo $baseurl; ?>catalogo/productos/<?php echo $data_sql_while['seourl']; ?>/<?php echo $data_sql_while_2['seourl']; ?>/pag-1/precio-todos/orden-todos.html"><?php echo $data_sql_while_2['subcategoria']; ?></a></li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="contentfilter-block margin-top-20">
                                <div class="margin-bottom-15">
                                    <span class="label-filter">Todos los proveedores</span>
                                    <span class="icon">-</span>
                                </div>
                                <div class="sidebarleft-collapsed open">
                                    <?php
                                        $contador1 = 0;
                                        $sql = "SELECT * FROM ".$prefix."proveedor WHERE estado = 'activo' AND cuenta_proveedor = 'SI' ORDER BY razon_social ASC ";
                                        $result = $conexion->query($sql);
                                        while($data_sql_while = $result->fetch_array(MYSQLI_BOTH)){
                                            $contador1 = $contador1 + 1;

                                            if(!empty($_SESSION["admin_id"])){
                                                $sql2 = "SELECT * FROM ".$prefix."proveedor_reglas WHERE id_proveedor = '".$data_sql_while['id']."' AND id_cliente = '".$_SESSION["admin_id"]."' ";
                                                $result2 = $conexion->query($sql2);
                                                $existe_regla = $result2->num_rows;
                                            }

                                            if($existe_regla == 0){
                                    ?>
                                        <div class="sidebar-block filter-group-block collapsed">
                                            <div class="sidebar-block_title <?php if($data_sql_while['seourl'] == $data_proveedor['seourl']){ ?>active item-proveedor<?php } ?>"><a href="<?php echo $baseurl; ?>catalogo/proveedores/<?php echo $data_sql_while['seourl']; ?>/pag-1/precio-todos/orden-todos.html"><?php echo $data_sql_while['razon_social']; ?></a></div>
                                        </div>
                                    <?php } } ?>
                                </div>
                                <!--<div class="margin-top-20">
                                    <a href="#" class="filter-see-more">+ Ver Más</a>
                                </div>-->
                            </div>
                        </div>
                    

                    </div>

                    <div class="col-lg-9 aside">
                        <div class="holder mt-0">
                            <div class="container">
                                <ul class="breadcrumbs"> 
                                    <li><a href="<?php echo $baseurl; ?>catalogo/<?php echo $data_categoria['seourl']; ?>/todas/pag-1/precio-todos/orden-todos.html"><?php echo $data_categoria['categoria']; ?></a></li>
                                    <li><a href="<?php echo $baseurl; ?>catalogo/<?php echo $data_categoria['seourl']; ?>/<?php echo $data_subcategoria['seourl']; ?>/pag-1/precio-todos/orden-todos.html"><?php echo $data_subcategoria['subcategoria']; ?></a></li>
                                    <li class="breadcrumbs-active"><span><?php echo $data_sql['producto']; ?></span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="prd-block prd-block--mobile-image-first prd-block--prv-left js-prd-gallery" id="prdGallery100">
                            <?php if($qty_imagenes > 0){ ?>
                            <div class="col-md-6 col-xl-6" style="float: left;">
                                <div class="prd-block_info js-prd-m-holder mb-2 mb-md-0"></div><!-- Product Gallery -->
                                <div class="prd-block_main-image main-image--slide js-main-image--slide">
                                    <div class="prd-block_main-image-holder js-main-image-zoom" data-zoomtype="inner">
                                        <div class="prd-block_main-image-video js-main-image-video">
                                            <video loop muted preload="metadata" controls="controls">
                                                <source src="#">
                                            </video>
                                            <div class="gdw-loader"></div>
                                        </div>
                                        <div class="prd-has-loader">
                                            <div class="gdw-loader"></div>
                                            <img src="<?php echo $baseurl; ?>supplyme-images/productos/800X800_<?php echo $portada_producto['archivo']; ?>" class="zoom" alt="<?php echo $data_sql['producto']; ?>" data-zoom-image="<?php echo $baseurl; ?>supplyme-images/productos/800X800_<?php echo $portada_producto['archivo']; ?>">
                                        </div>
                                        <div class="prd-block_main-image-next slick-next js-main-image-next">NEXT</div>
                                        <div class="prd-block_main-image-prev slick-prev js-main-image-prev">PREV</div>
                                    </div>
                                </div>
                                <div class="product-previews-wrapper">
                                    <div class="product-previews-carousel" id="previewsGallery100">
                                        <?php
                                            $sql2 = "SELECT * FROM ".$prefix."producto_imagen WHERE token = '".$data_sql['token']."' ORDER BY RAND() LIMIT 8 ";
                                            $result2 = $conexion->query($sql2);
                                            while($galeria = $result2->fetch_array(MYSQLI_BOTH)){
                                        ?>
                                            <a href="#" data-image="<?php echo $baseurl; ?>supplyme-images/productos/800X800_<?php echo $galeria['archivo']; ?>" data-zoom-image="<?php echo $baseurl; ?>supplyme-images/productos/800X800_<?php echo $galeria['archivo']; ?>">
                                                <img src="<?php echo $baseurl; ?>supplyme-images/productos/800X800_<?php echo $galeria['archivo']; ?>" alt="<?php echo $data_sql['producto']; ?>">
                                            </a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            <div class="<?php if($qty_imagenes > 0){ ?>col-md-6 col-xl-6<?php }else{ ?>col-md-12 col-xl-12<?php } ?>" style="float: left;">
                                <div class="prd-block_info">
                                    <div class="js-prd-d-holder prd-holder">
                                        <div class="prd-block_title-wrap">
                                            <h1 class="prd-block_title"><?php echo $data_sql['producto']; ?></h1>
                                            <!--<div class="prd-block__labels"><span class="prd-label--new">OFERTA</span></div>-->
                                        </div>
                                        <div class="prd-block_price margin-top-10">
                                            <span class="prd-block_price--actual"><?php echo formato_precio($precio_publicado); ?></span>
                                            <?php if($existe_precio_especial > 0){ ?>
                                                <span class="prd-block_price--old"><?php echo $data_sql['precio']; ?></span>
                                            <?php } ?>
                                        </div>
                                        <?php if($data_sql['descripcion'] != ''){ ?>
                                        <div class="prd-block_description">
                                            <p class="title">Descripción</p>
                                            <p><?php echo $data_sql['descripcion']; ?></p>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <div class="prd-block_info">
                                        <ul>
                                            <?php if($unidad_medida['medida'] != ''){ ?>
                                                <li><span>Medida</span><span><?php echo $unidad_medida['medida']; ?></span></li>
                                            <?php } ?>
                                            <li><span>Categoría</span><span><?php echo $data_categoria['categoria']; ?> > <?php echo $data_subcategoria['subcategoria']; ?></span></li>
                                            <li><span>Código</span><span><?php echo $data_sql['codigo']; ?></span></li>
                                            <?php if($data_sql['ficha_tecnica'] != ''){ ?>
                                                <li><span>Ficha</span><span><a href="<?php echo $baseurl.'supplyme-images/productos/'.$data_sql['ficha_tecnica']; ?>" target="_blank">Ficha técnica</a></span></li>
                                            <?php } ?>
                                            <!--<li><span>Marca</span><span><a href="#">Guallarauco</a></span></li>-->
                                            <!--<li><span>Formato despacho</span><span>Caja de 12 unidades</span></li>-->
                                        </ul>
                                    </div>
                                    <div class="prd-block_options topline row">
                                        <div class="prd-block_qty col-md-7" style="float:left;">
                                            <span class="option-label">Cantidad:</span>
                                            <div class="qty qty-changer">
                                                <fieldset>
                                                <input type="button" value="&#8210;" class="decrease_prod">
                                                <input type="text" class="qty-input-prod" value="1" data-min="1" data-max="<?php echo $item_max; ?>">
                                                <input type="button" value="+" class="increase_prod">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="prd-block_actions col-md-5 margin-top-0 margin-bottom-0 padding-top-0 padding-bottom-0" style="float:left;">
                                            <div class="btn-wrap">
                                                <?php if($_SESSION["admin_privilegio"] != 'proveedor'){ ?>
                                                <button class="btn btn--add-to-cart" data-id="<?php echo $cadena; ?>" data-fancybox data-type="ajax" style="margin-top: 19px;">
                                                    <span>Agregar</span>
                                                </button>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>

                        <?php if($qty_relacionados_por_categoria > 0 || $qty_relacionados_por_proveedor > 0){ ?>
                        <div class="container-productos-relacionados margin-top-50">
                            <div class="title-wrap text-left">
                                <h2 class="h1-style">Productos relacionados</h2>
                            </div>
                            <div class="prd-grid prd-carousel js-prd-carousel slick-arrows-aside-simple slick-arrows-mobile-lg data-to-show-4 data-to-show-md-3 data-to-show-sm-3 data-to-show-xs-2" data-slick='{"slidesToShow": 4, "slidesToScroll": 2, "responsive": [{"breakpoint": 992,"settings": {"slidesToShow": 3, "slidesToScroll": 1}},{"breakpoint": 768,"settings": {"slidesToShow": 2, "slidesToScroll": 1}},{"breakpoint": 480,"settings": {"slidesToShow": 2, "slidesToScroll": 1}}]}'>
                                <?php
                                    //$sql2 = "SELECT * FROM ".$prefix."producto WHERE categoria = '".$data_sql['categoria']."' AND id <> '".$data_sql['id']."' ORDER BY RAND() LIMIT 12 ";
                                    if($qty_relacionados_por_categoria > 0){
                                        $sql = "SELECT prod.id as id, prod.codigo as codigo, prod.producto as producto, prod.categoria as categoria, prod.subcategoria as subcategoria, prod.unidad_medida as unidad_medida, prod.empaque as empaque, prod.precio as precio, prod.moneda as moneda, prod.descripcion as descripcion, prod.stock as stock, prod.estado as estado, prod.fecha_add as fecha_add, prod.fecha_update as fecha_update, prod.id_asociado as id_asociado, prod.token as token, prov.razon_social as razon_social, prov.seourl as seourl_proveedor FROM ".$prefix."producto as prod, ".$prefix."proveedor as prov WHERE prod.estado = 'activo' AND prov.estado = 'activo' AND prod.id_asociado = prov.id AND prod.categoria = '".$data_sql['categoria']."' AND prod.id <> '".$data_sql['id']."' ORDER BY RAND() LIMIT 12";
                                    }elseif($qty_relacionados_por_proveedor > 0){
                                        $sql = "SELECT prod.id as id, prod.codigo as codigo, prod.producto as producto, prod.categoria as categoria, prod.subcategoria as subcategoria, prod.unidad_medida as unidad_medida, prod.empaque as empaque, prod.precio as precio, prod.moneda as moneda, prod.descripcion as descripcion, prod.stock as stock, prod.estado as estado, prod.fecha_add as fecha_add, prod.fecha_update as fecha_update, prod.id_asociado as id_asociado, prod.token as token, prov.razon_social as razon_social, prov.seourl as seourl_proveedor FROM ".$prefix."producto as prod, ".$prefix."proveedor as prov WHERE prod.estado = 'activo' AND prov.estado = 'activo' AND prod.id_asociado = prov.id AND prod.id_asociado = '".$data_sql['id_asociado']."' AND prod.id <> '".$data_sql['id']."' ORDER BY RAND() LIMIT 12";
                                    }
                                    $result = $conexion->query($sql);
                                    while($data_sql = $result->fetch_array(MYSQLI_BOTH)){

                                        if(!empty($_SESSION["admin_id"])){
                                            $sql2 = "SELECT * FROM ".$prefix."producto_precio WHERE id_producto = '".$data_sql['id']."' AND id_comprador = '".$_SESSION["admin_id"]."' LIMIT 1 ";
                                            $result2 = $conexion->query($sql2);
                                            $existe_precio_especial = $result2->num_rows;
                                            $data_precio_especial = $result2->fetch_array(MYSQLI_BOTH);
                                        }

                                        $sql3 = "SELECT * FROM ".$prefix."producto_imagen WHERE token = '".$data_sql['token']."' AND portada = 'SI' LIMIT 1 ";
                                        $result3 = $conexion->query($sql3);
                                        $data_imagen_portada = $result3->fetch_array(MYSQLI_BOTH);
                                        if($data_imagen_portada['archivo'] == ''){
                                            $sql4 = "SELECT * FROM ".$prefix."producto_imagen WHERE token = '".$data_sql['token']."' ORDER BY RAND() LIMIT 1 ";
                                            $result4 = $conexion->query($sql4);
                                            $data_imagen_portada = $result4->fetch_array(MYSQLI_BOTH);
                                        }

                                        $url = 'producto/'.$data_sql['token'].'/'.generateSeoURL($data_sql['producto']).'.html';

                                        $cadena = encrypt('registro-'.$data_sql['id'], $sitio);

                                        if($existe_precio_especial > 0){
                                            $precio_publicado = ((formato_int($data_precio_especial['precio']) * $margen) + formato_int($data_precio_especial['precio']));
                                        }else{
                                            $precio_publicado = ((formato_int($data_sql['precio']) * $margen) + formato_int($data_sql['precio']));
                                        }
                                ?>
                                <div class="prd prd-has-loader prd-new prd-popular">
                                    <div class="prd-inside">
                                        <div class="prd-img-area">
                                            <a href="<?php echo $url; ?>" class="prd-img">
                                                <?php if($data_imagen_portada['archivo'] != ''){ ?>
                                                    <img src="<?php echo $baseurl; ?>/supplyme-images/productos/800X800_<?php echo $data_imagen_portada['archivo']; ?>" data-srcset="<?php echo $baseurl; ?>supplyme-images/productos/800X800_<?php echo $data_imagen_portada['archivo']; ?>" alt="<?php echo $data_sql['producto']; ?>" class="js-prd-img lazyload">
                                                <?php }else{ ?>
                                                    <img src="<?php echo $baseurl; ?>/supplyme-images/default_img.jpg" data-srcset="<?php echo $baseurl; ?>supplyme-images/default_img.jpg" alt="<?php echo $data_sql['producto']; ?>" class="js-prd-img lazyload">
                                                <?php } ?>
                                            </a>
                                            <div class="gdw-loader"></div>
                                        </div>
                                        <div class="prd-info">
                                            <h2 class="prd-title text-center"><a href="<?php echo $url; ?>"><?php echo $data_sql['producto']; ?></a></h2>
                                            <div class="prd-tag prd-hidemobile text-center"><a href="<?php echo $baseurl; ?>catalogo/proveedores/<?php echo $data_sql['seourl_proveedor']; ?>/pag-1/precio-todos/orden-todos.html" style="color:#777;font-size:15px;"><?php echo $data_sql['razon_social']; ?></a></div>
                                            <div class="prd-price">
                                                <div class="price-new"><?php echo formato_precio($precio_publicado); ?></div>
                                                <?php if($existe_precio_especial > 0){ ?>
                                                    <div class="price-old"><?php echo $data_sql['precio']; ?></div>
                                                <?php } ?>
                                                <?php if(!isset($_SESSION["admin_privilegio"]) || $_SESSION["admin_privilegio"] != 'proveedor'){ ?><form action="#"><input type="hidden"> <button class="btn btn-grid-cart" data-fancybox data-type="ajax" data-src="<?php echo $baseurl; ?>cart-popup.php?id=<?php echo $cadena; ?>&qty=1"><span>Agregar</span></button></form><?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        <?php } ?>
                    </div>

                </div>

            </div>
        </div>

    </div>
    
    <?php include('footer.php'); ?>

    <?php include('includes/scripts.php'); ?>

    <script type="text/javascript">

    //$('span.minicart-qty').html('<?php echo $qty_carro_actual; ?>');

    $('.btn--add-to-cart').click(function(){
        var qty = $('input.qty-input-prod').val();
        var id = $(this).data('id');
        var url = '<?php echo $baseurl; ?>cart-popup.php?id=' + id + '&qty=' + qty;
        
        $.fancybox.open({
            src: url,
            type: 'ajax'
        });

        return false;
    });

    $(document).on('click', '.decrease_prod, .increase_prod', function (e) {
        var $this = $(e.target),
            input = $this.parent().find('.qty-input-prod'),
            v = $this.hasClass('decrease_prod') ? input.val() - 1 : input.val() * 1 + 1,
            min = input.attr('data-min') ? input.attr('data-min') : 1,
            max = input.attr('data-max') ? input.attr('data-max') : false;
        if (v >= min) {
            if (!max == false && v > max) {
                return false
            } else input.val(v);
        }
        e.preventDefault();
    });
    $(document).on('change', '.qty-input-prod', function (e) {
        var input = $(e.target),
            min = input.attr('data-min') ? input.attr('data-min') : 1,
            max = input.attr('data-max'),
            v = parseInt(input.val());

        if (v > max){
            input.val(max);
        }else if (v < min) {
            input.val(min);
        }
    });
    </script>
</body>

</html>