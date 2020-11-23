<?php include('includes/config.php'); ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1">
    <meta name="description" content="<?php echo seo_meta_descripcion(1, $prefix); ?>">
    <meta name="author" content="<?php echo $author; ?>">
    <base href="<?php echo $baseurl; ?>">

    <title><?php echo seo_meta_titulo(1, $prefix); ?> | <?php echo $sitio; ?></title>

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
    <link href="js/vendor/bootstrap/bootstrap.css" rel="stylesheet">
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

<body class="home-page is-dropdn-click has-slider" id="home">
    
    <?php include('header.php'); ?>

    <div class="page-content">
        <!-- BN Slider 1 -->
        <div class="holder fullwidth full-nopad mt-0">
            <div class="container">
                <div class="bnslider-wrapper">
                    <div class="bnslider-arrows container-fluid">
                        <div></div>
                    </div>

                    <div class="bnslider bnslider--lg bnslider--darkarrows keep-scale" id="bnslider-001" data-slick='{"arrows": true, "dots": false}' data-autoplay="true" data-speed="5000" data-start-width="1920" data-start-height="620" data-start-mwidth="480" data-start-mheight="578">
                        <?php
                            $sql = "SELECT * FROM ".$prefix."banner WHERE id <> '' AND estado = 'activo' AND tipo = 'slider_principal' ORDER BY orden ASC ";
                            $result = $conexion->query($sql);
                            while($data_sql = $result->fetch_array(MYSQLI_BOTH)){
                        ?>
                        <div class="bnslider-slide bnslide-fashion-4">
                            <div class="bnslider-image-mobile" style="background-image: url('<?php echo $baseurl.'supplyme-images/banners/1920X850_'.$data_sql['archivo']; ?>');"></div>
                            <div class="bnslider-image" style="background-image: url('<?php echo $baseurl.'supplyme-images/banners/1920X850_'.$data_sql['archivo']; ?>');"></div>
                            <div class="bnslider-text-wrap bnslider-overlay">
                                <?php if($data_sql['texto_posicion'] != '' && $data_sql['texto_posicion'] != '0'){ ?> 
                                <div class="bnslider-text-content txt-middle txt-<?php echo $data_sql['texto_posicion']; ?>">
                                    <div class="bnslider-text-content-flex">
                                        <div class="bnslider-vert w-50 mx-0 <?php if($data_sql['texto_posicion'] == 'center'){ ?>border-half<?php } ?>" data-animation="zoomIn" data-animation-delay="0s">
                                            <?php if(!empty($data_sql['texto1'])){ ?><div class="bnslider-text bnslider-text--lg text-center bannertext-1" data-animation="popIn" data-animation-delay=".8s" style="color: #ffc501;"><?php echo $data_sql['texto1']; ?></div><?php } ?>
                                            <?php if(!empty($data_sql['texto2']) && $data_sql['texto2'] != '<p><br></p>'){ ?><div class="bnslider-text bnslider-text--xs text-center bannertext-2" data-animation="zoomIn" data-animation-delay="1.6s" style="color: #88c000;font-size:48px;<?php if($data_sql['texto_posicion'] != 'center'){ ?>background: #000000b0;padding: 20px;border-radius: 6px;<?php } ?>"><?php echo $data_sql['texto2']; ?></div><?php } ?>
                                            <?php if(!empty($data_sql['texto_boton']) && !empty($data_sql['link'])){ ?><div class="btn-wrap double-mt text-center bannertext-3" data-animation="fadeInUp" data-animation-delay="2s"><a href="<?php echo $data_sql['link']; ?>" class="btn-decor" style="color: #ffffff;background:#7ed320;padding:18px 44px 16px 44px;font-size:18px;"><?php echo $data_sql['texto_boton']; ?></a></div><?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        <?php } ?>
                        <!--<div class="bnslider-slide bnslide-fashion-3">
                            <div class="bnslider-image-mobile" style="background-image: url('supplyme-images/banner-mobile-1.jpg');"></div>
                            <div class="bnslider-image" style="background-image: url('supplyme-images/banner2.jpg');"></div>
                            <div class="bnslider-text-wrap bnslider-overlay">
                                <div class="bnslider-text-content txt-middle txt-center">
                                    <div class="bnslider-text-content-flex container">
                                        <div class="bnslider-vert border-half" data-animation="zoomIn" data-animation-delay="0s">
                                            <div class="bnslider-text bnslider-text--xxs text-center bannertext-1" data-animation="fadeInUp" data-animation-delay=".5s" style="padding-left:100px;padding-right:100px;"><img src="supplyme-images/logo-banner.png" width="200"></div>
                                            <div class="bnslider-text bnslider-text--sm text-center bannertext-2" data-animation="fadeInUp" data-animation-delay="1s" style="color: #88c000;font-size:55px; background: #000000b0;padding: 40px;border-radius: 6px;">EXPERTOS <br>EN QUESO</div>
                                            <div class="btn-wrap double-mt text-center bannertext-3" data-animation="fadeInUp" data-animation-delay="2s"><a href="#" class="btn-decor" style="color: #ffffff;background:#60b301;padding:18px 44px 16px 44px;font-size:20px;">COMPRAR</a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>-->
                        <!--<div class="bnslider-slide bnslide-fashion-2">
                            <div class="bnslider-image-mobile" style="background-image: url('supplyme-images/banner-mobile-1.jpg');"></div>
                            <div class="bnslider-image" style="background-image: url('supplyme-images/banner2.jpg');"></div>
                            <div class="bnslider-text-wrap bnslider-overlay">
                                <div class="bnslider-text-content txt-middle txt-left">
                                    <div class="bnslider-text-content-flex container">
                                        <div class="bnslider-vert w-50 mx-0" data-animation="fadeIn" data-animation-delay="0.5s">
                                            <div class="bnslider-text bnslider-text--md text-center bannertext-1" data-animation="pulsate" data-animation-delay="0.8s" style="font-weight: 700"><img src="supplyme-images/logo-banner.png" width="250"></div>
                                            <div class="bnslider-text bnslider-text--sm text-center bannertext-2" data-animation="fadeInUp" data-animation-delay="1.6s" style="color: #88c000;font-size:55px;">EXPERTOS <br>EN QUESO</div>
                                            <div class="btn-wrap double-mt text-center bannertext-3" data-animation="fadeInUp" data-animation-delay="2s"><a href="#" class="btn-decor" style="color: #ffffff;background:#60b301;padding:18px 44px 16px 44px;font-size:20px;">COMPRAR</a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>-->
                    </div>

                    <div class="bnslider-loader">
                        <div class="loader-wrap">
                            <div class="dots">
                                <div class="dot one"></div>
                                <div class="dot two"></div>
                                <div class="dot three"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!--<div class="bnslider-dots vert-dots container-fluid"></div>-->
                </div>
            </div>
        </div>

        <!-- //BN Slider 1 -->
        <div class="holder fullboxed bgcolor mt-0 py-2 py-sm-3">
            <div class="container">
                <div class="title-with-right">
                    <h2 class="h1-style-min">Categorías</h2>
                    <div class="btn-ver-todos">
                        <a href="<?php echo $baseurl; ?>catalogo.html">Ver todos</a>
                    </div>
                </div>
                <div class="row bnr-grid categories-carousel slick-arrows-aside-simple margin-top-30">
                    <?php
                        $sql = "SELECT * FROM ".$prefix."banner WHERE id <> '' AND estado = 'activo' AND tipo = 'subbanners' ORDER BY orden ASC ";
                        $result = $conexion->query($sql);
                        while($data_sql = $result->fetch_array(MYSQLI_BOTH)){
                    ?>
                    <div class="col-sm-4">
                        <a href="<?php echo $data_sql['link']; ?>" class="bnr-wrap" title="<?php echo $data_sql['titulo']; ?>">
                            <div class="bnr bnr1 bnr--style-1 bnr--right bnr--middle bnr-hover-scale" data-fontratio="5.55">
                                <img src="<?php echo $baseurl.'supplyme-images/banners/361X256_'.$data_sql['archivo']; ?>" data-src="<?php echo $baseurl.'supplyme-images/banners/361X256_'.$data_sql['archivo']; ?>" alt="<?php echo $data_sql['titulo']; ?>" class="lazyload">
                                <span class="bnr-caption text-center">
                                    <span class="bnr-text-wrap">
                                        <span class="bnr-text2"><?php echo $data_sql['titulo']; ?></span>
                                        <?php if(!empty($data_sql['texto1'])){ ?><span class="bnr-text1"><?php echo $data_sql['texto1']; ?></span><?php } ?>
                                        <?php if(!empty($data_sql['texto_boton'])){ ?><span class="btn-decor bnr-btn"><?php echo $data_sql['texto_boton']; ?></span><?php } ?>
                                    </span>
                                </span>
                            </div>
                        </a>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="holder holder-gris margin-top-0 padding-top-30">
            <div class="container">

                <div class="text-center">
                    <h2 class="h1-style">Productos destacados</h2>
                </div>

                <div class="prd-grid product-listing data-to-show-5 data-to-show-md-4 data-to-show-sm-2 js-category-grid">
                    <?php
                        $existe_regla = 0;
                        
                        $sql = "SELECT prod.id as id, prod.codigo as codigo, prod.producto as producto, prod.categoria as categoria, prod.subcategoria as subcategoria, prod.unidad_medida as unidad_medida, prod.empaque as empaque, prod.precio as precio, prod.moneda as moneda, prod.descripcion as descripcion, prod.stock as stock, prod.estado as estado, prod.fecha_add as fecha_add, prod.fecha_update as fecha_update, prod.id_asociado as id_asociado, prod.token as token, prov.razon_social as razon_social, prov.seourl as seourl_proveedor FROM ".$prefix."producto as prod, ".$prefix."proveedor as prov WHERE prod.estado = 'activo' AND prov.estado = 'activo' AND prod.id_asociado = prov.id ORDER BY RAND(), prod.fecha_add LIMIT 15";
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

                            if(isset($existe_precio_especial) && ($existe_precio_especial > 0)){
                                $precio_publicado = ((formato_int($data_precio_especial['precio']) * $margen) + formato_int($data_precio_especial['precio']));
                            }else{
                                $precio_publicado = ((formato_int($data_sql['precio']) * $margen) + formato_int($data_sql['precio']));
                            }

                            if(!empty($_SESSION["admin_id"]) && $_SESSION["admin_privilegio"] == 'comprador'){
                                $sql1 = "SELECT * FROM ".$prefix."proveedor_reglas WHERE id_cliente = '".$_SESSION["admin_id"]."' AND id_proveedor = '".$data_sql['id']."' ";
                                $result1 = $conexion->query($sql1);
                                $existe_regla = $result1->num_rows;
                            }

                            if(isset($existe_regla) && ($existe_regla == 0)){
                    ?>
                    <div class="prd prd-has-loader prd-new prd-popular">
                        <div class="prd-inside">
                            <div class="prd-img-area">
                                <a href="<?php echo $url; ?>" class="prd-img">
                                    <?php if($data_imagen_portada['archivo'] != ''){ ?>
                                        <img src="<?php echo $baseurl; ?>/supplyme-images/productos/800X800_<?php echo $data_imagen_portada['archivo']; ?>" data-srcset="supplyme-images/productos/800X800_<?php echo $data_imagen_portada['archivo']; ?>" alt="<?php echo $data_sql['producto']; ?>" class="js-prd-img lazyload">
                                    <?php }else{ ?>
                                        <img src="<?php echo $baseurl; ?>/supplyme-images/default_img.jpg" data-srcset="supplyme-images/default_img.jpg" alt="<?php echo $data_sql['producto']; ?>" class="js-prd-img lazyload">
                                    <?php } ?>
                                </a>
                                <div class="gdw-loader"></div>
                            </div>
                            <div class="prd-info">
                                <h2 class="prd-title text-center"><a href="<?php echo $url; ?>"><?php echo $data_sql['producto']; ?></a></h2>
                                <div class="prd-tag prd-hidemobile text-center"><a href="<?php echo $baseurl; ?>catalogo/proveedores/<?php echo $data_sql['seourl_proveedor']; ?>/pag-1/precio-todos/orden-todos.html" style="color:#777;font-size:15px;"><?php echo $data_sql['razon_social']; ?></a></div>
                                <div class="prd-price">
                                    <div class="price-new"><?php echo formato_precio($precio_publicado); ?></div>
                                    <?php if(isset($existe_precio_especial) && $existe_precio_especial > 0){ ?>
                                        <div class="price-old"><?php echo $data_sql['precio']; ?></div>
                                    <?php } ?>
                                    <?php if(!isset($_SESSION["admin_privilegio"]) || $_SESSION["admin_privilegio"] != 'proveedor'){ ?><form action="#"><input type="hidden"> <button class="btn btn-grid-cart" data-fancybox data-type="ajax" data-src="<?php echo $baseurl; ?>cart-popup.php?id=<?php echo $cadena; ?>&qty=1"><span>Agregar</span></button></form><?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } } ?>
                </div>
            </div>
        </div>

        <div class="holder mt-0 section-features">
            <div class="container">
                <div class="row no-gutters shop-features-style2-2">
                    <?php
                        $sql = "SELECT * FROM ".$prefix."banner WHERE id <> '' AND estado = 'activo' AND tipo = 'beneficios' ORDER BY orden ASC ";
                        $result = $conexion->query($sql);
                        while($data_sql = $result->fetch_array(MYSQLI_BOTH)){
                    ?>
                    <div class="col-sm-4">
                        <?php if(!empty($data_sql['link'])){ ?><a href="<?php echo $data_sql['link']; ?>" class="shop-feature" title="<?php echo $data_sql['titulo']; ?>"><?php } ?>
                            <div class="shop-feature-icon"><img src="<?php echo $baseurl.'supplyme-images/banners/300X300_'.$data_sql['archivo']; ?>" width="120" alt="<?php echo $data_sql['titulo']; ?>"></div>
                            <div class="shop-feature-text">
                                <div class="text1"><?php echo $data_sql['titulo']; ?></div>
                                <div class="text2"><?php echo $data_sql['texto1']; ?></div>
                            </div>
                        <?php if(!empty($data_sql['link'])){ ?></a><?php } ?>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <?php
            $sql = "SELECT * FROM ".$prefix."banner WHERE id = '18' AND estado = 'activo' AND tipo = 'background' LIMIT 1 ";
            $result = $conexion->query($sql);
            $data_sql = $result->fetch_array(MYSQLI_BOTH);
        ?>
        <div class="holder section-clientes padding-top-70 padding-bottom-70 margin-top-0" style="background-image:url('<?php echo $baseurl.'supplyme-images/banners/3000X1305_'.$data_sql['archivo']; ?>');">
            <div class="container">
                <h2 class="h1-style text-center">¿Qué dicen nuestros clientes?</h2>
                <div class="brand-prd-carousel vert-dots margin-bottom-0" data-slick='{"fade": true,"autoplay": true,"dots": false,"autoplaySpeed": 3000}'>
                    <?php
                        $sql = "SELECT * FROM ".$prefix."testimonio WHERE id <> '' AND estado = 'activo' ORDER BY orden DESC ";
                        $result = $conexion->query($sql);
                        while($data_sql = $result->fetch_array(MYSQLI_BOTH)){
                    ?>
                    <div class="brand-prd">
                        <div class="brand-prd-info">
                            <div class="inside">
                                <div class="text-center">
                                    <img src="<?php echo $baseurl.'supplyme-images/testimonios/'.$data_sql['archivo']; ?>" data-src="<?php echo $baseurl.'supplyme-images/testimonios/'.$data_sql['archivo']; ?>" alt="<?php echo $data_sql['empresa']; ?>" class="lazyload" width="110">
                                </div>
                                <div class="text-center desc-1"><?php echo $data_sql['texto']; ?></div>
                                <p class="text-center desc-2"><b><?php echo $data_sql['nombre']; ?></b><br>
                                <strong><?php echo $data_sql['empresa']; ?></strong>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php
            $sql = "SELECT * FROM ".$prefix."proveedor WHERE estado = 'activo' AND cuenta_proveedor = 'SI' AND archivo != '' ORDER BY razon_social ASC ";
            $result = $conexion->query($sql);
            $qty_proveedores = $result->num_rows;

            if($qty_proveedores > 0){
        ?>
        <div class="holder-gris">
            <div class="container">
                <ul class="brand-carousel js-brand-carousel slick-arrows-aside-simple">
                    <?php
                        while($data_sql = $result->fetch_array(MYSQLI_BOTH)){
                            if(!empty($_SESSION["admin_id"]) && $_SESSION["admin_privilegio"] == 'comprador'){
                                $sql1 = "SELECT * FROM ".$prefix."proveedor_reglas WHERE id_cliente = '".$_SESSION["admin_id"]."' AND id_proveedor = '".$data_sql['id']."' ";
                                $result1 = $conexion->query($sql1);
                                $existe_regla = $result1->num_rows;
                            }
                            if(isset($existe_regla) && ($existe_regla == 0)){
                    ?>
                        <li><a href="<?php echo $baseurl; ?>catalogo/proveedores/<?php echo $data_sql['seourl']; ?>/pag-1/precio-todos/orden-todos.html"><img src="<?php echo $baseurl; ?>/supplyme-images/proveedores/<?php echo $data_sql['archivo']; ?>" data-src="<?php echo $baseurl; ?>/supplyme-images/proveedores/<?php echo $data_sql['archivo']; ?>" class="lazyload" alt="<?php echo $data_sql['razon_social']; ?>" style="max-width:150px;"></a></li>
                    <?php } } ?>
                </ul>
            </div>
        </div>
        <?php } ?>
    </div>
    
    <?php include('footer.php'); ?>

    <?php include('includes/scripts.php'); ?>
</body>

</html>