<?php include('includes/config.php');

if(isset($campos['searchword'])){
    $searchword = $campos['searchword'];
}
if(isset($campos['proveedor'])){
    $proveedor = $campos['proveedor'];
}
if(isset($campos['categoria'])){
    $categoria = $campos['categoria'];
}
if(isset($campos['subcategoria'])){
    $subcategoria = $campos['subcategoria'];
}
$pagina = $campos['pagina'];
$precio = $campos['precio'];
$orden = $campos['orden'];

if(!empty($pagina)){
    $pagina_get = explode("-", $pagina);
    $pagina = $pagina_get[1];
}else{
    $pagina = 1;
}

if(!empty($precio)){
    $precio_get = explode("-", $precio);
    $precio = $precio_get[1];
}else{
    $precio = 1;
}

if(!empty($orden)){
    $orden_get = explode("-", $orden);
    $orden = $orden_get[1];
}else{
    $orden = 1;
}

if($pagina == 1){
    $limite_inferior = 0;
    //$limite_superior = 6;
}else{
    $mult = $pagina - 1;
    $limite_inferior = $mult * 6;
    //$limite_superior = $limite_inferior + 6;
}

/*echo $proveedor.'<br>';
echo $categoria.'<br>';
echo $subcategoria.'<br>';
echo $pagina.'<br>';
echo $precio.'<br>';
echo $orden.'<br>';*/

if(!empty($proveedor)){
    $sql = "SELECT * FROM ".$prefix."proveedor WHERE seourl = '".$proveedor."' LIMIT 1";
    $result = $conexion->query($sql);
    $data_proveedor = $result->fetch_array(MYSQLI_BOTH);

    if($data_proveedor['estado'] != 'activo'){
        header ("Location: ".$baseurl."catalogo.html");
    }

    $seotitulo = 'Catálogo de Productos - '.$data_proveedor['razon_social'];
    $seodescripcion = seo_meta_descripcion(2, $prefix);
}

if(!empty($categoria)){
    $sql = "SELECT * FROM ".$prefix."categorias WHERE seourl = '".$categoria."' LIMIT 1";
    $result = $conexion->query($sql);
    $data_categoria = $result->fetch_array(MYSQLI_BOTH);

    if($data_categoria['estado'] != 'activo'){
        header ("Location: ".$baseurl."catalogo.html");
    }

    if(!empty($subcategoria) && $subcategoria != 'todas'){
        $sql = "SELECT * FROM ".$prefix."subcategorias WHERE seourl = '".$subcategoria."' LIMIT 1";
        $result = $conexion->query($sql);
        $data_subcategoria = $result->fetch_array(MYSQLI_BOTH);

        if($data_subcategoria['estado'] != 'activo'){
            header ("Location: ".$baseurl."catalogo/".$categoria."/pag-1.html");
        }

        $seotitulo = 'Catálogo de Productos - '.$data_categoria['meta_titulo'].' - '.$data_subcategoria['meta_titulo'];
        $seodescripcion = $data_subcategoria['meta_descripcion'];
    }else{
        $seotitulo = 'Catálogo de Productos - '.$data_categoria['meta_titulo'];
        $seodescripcion = $data_categoria['meta_descripcion'];
    }
}elseif(empty($proveedor)){
    $seotitulo = seo_meta_titulo(2, $prefix);
    $seodescripcion = seo_meta_descripcion(2, $prefix);
}

$sql = "SELECT det.id_producto as idproducto, SUM(det.cantidad) as cantidad FROM ".$prefix."pedido as ped, ".$prefix."carro_detalle as det WHERE ped.token = det.token AND ped.status_pago = '2' GROUP BY idproducto ";
$result = $conexion->query($sql);
while($data_mas_vendidos = $result->fetch_array(MYSQLI_BOTH)){
    $update = "UPDATE ".$prefix."producto SET ventas = '".$data_mas_vendidos['cantidad']."' WHERE id = '".$data_mas_vendidos['idproducto']."' ";
    $conexion->query($update);
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1">
    <meta name="description" content="<?php echo $seodescripcion; ?>">
    <meta name="author" content="<?php echo $author; ?>">
    <base href="<?php echo $baseurl; ?>">

    <title><?php echo $seotitulo; ?> | <?php echo $sitio; ?></title>

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
    <link href="css/jPages.css" rel="stylesheet">
    <!--icon font-->
    <link href="fonts/icomoon/icomoon.css" rel="stylesheet">
    <!--custom font-->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i" rel="stylesheet">
</head>

<body class="home-page is-dropdn-click has-slider" id="catalogo">
    
    <?php include('header.php'); ?>

    <div class="page-content">
        <div class="holder mt-0 holder-gris padding-top-20"> 
            <div class="container">
                <!-- Two columns -->
                
                <div class="row">
                    <!-- Left column -->
                    <div class="col-lg-3 aside aside--left fixed-col js-filter-col">
                        <div class="fixed-col_container">
                            <div class="contentfilter-block">
                                <div class="filter-close margin-bottom-20">CERRAR</div>
                                <div class="margin-bottom-15">
                                    <span class="label-filter">Todas las categorías</span>
                                    <span class="icon">-</span>
                                </div>
                                <!--<div class="sidebar-block sidebar-block--mobile d-block d-lg-none">
                                    <div class="d-flex align-items-center">
                                        <div class="selected-label">(6) FILTER</div>
                                        <div class="selected-count ml-auto">SELECTED <span><b>25 items</b></span></div>
                                    </div>
                                </div>-->
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
                                        while($data_sql = $result->fetch_array(MYSQLI_BOTH)){
                                            $contador1 = $contador1 + 1;

                                            $sql_2 = "SELECT * FROM ".$prefix."subcategorias WHERE estado = 'activo' AND categoria = '".$data_sql['id']."' ORDER BY subcategoria ASC ";
                                            $result_2 = $conexion->query($sql_2);
                                            $qty_subcategorias = $result_2->num_rows;
                                    ?>
                                    <div class="sidebar-block filter-group-block collapsed <?php if((isset($data_categoria['codigo'])) && ($data_categoria['codigo'] == $data_sql['codigo'])){ ?>open item-selected<?php } ?>" data-url="<?php echo $data_sql['seourl']; ?>">
                                        <div class="sidebar-block_title"><span><a href="catalogo/productos/<?php echo $data_sql['seourl']; ?>/todas/pag-1/precio-todos/orden-todos.html"><?php echo $data_sql['categoria']; ?></a></span><?php if($qty_subcategorias > 0){ ?><span class="icon"><?php if((isset($data_categoria['codigo'])) && ($data_categoria['codigo'] == $data_sql['codigo'])){ echo '-'; }else{ echo '+'; } ?></span><?php } ?></div>
                                        <?php if($qty_subcategorias > 0){ ?>
                                        <div class="sidebar-block_content">
                                            <ul class="category-list">
                                                <?php
                                                    while($data_sql_2 = $result_2->fetch_array(MYSQLI_BOTH)){
                                                ?>
                                                    <li <?php if((isset($data_subcategoria['codigo'])) && ($data_subcategoria['codigo'] == $data_sql_2['codigo'])){ ?>class="active"<?php } ?> data-url="<?php echo $data_sql_2['seourl']; ?>"><a href="catalogo/productos/<?php echo $data_sql['seourl']; ?>/<?php echo $data_sql_2['seourl']; ?>/pag-1/precio-todos/orden-todos.html"><?php echo $data_sql_2['subcategoria']; ?></a></li>
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
                                        while($data_sql = $result->fetch_array(MYSQLI_BOTH)){
                                            $contador1 = $contador1 + 1;
                                            $existe_regla = 0;

                                            if(!empty($_SESSION["admin_id"])){
                                                $sql2 = "SELECT * FROM ".$prefix."proveedor_reglas WHERE id_proveedor = '".$data_sql['id']."' AND id_cliente = '".$_SESSION["admin_id"]."' ";
                                                $result2 = $conexion->query($sql2);
                                                $existe_regla = $result2->num_rows;
                                            }

                                            if($existe_regla == 0){
                                    ?>
                                        <div class="sidebar-block filter-group-block collapsed">
                                            <div class="sidebar-block_title <?php if((isset($data_proveedor['seourl'])) && ($data_sql['seourl'] == $data_proveedor['seourl'])){ ?>active item-proveedor<?php } ?>" data-url="<?php echo $data_sql['seourl']; ?>"><a href="<?php echo $baseurl; ?>catalogo/proveedores/<?php echo utf8_decode($data_sql['seourl']); ?>/pag-1/precio-todos/orden-todos.html"><?php echo $data_sql['razon_social']; ?></a></div>
                                        </div>
                                    <?php } } ?>
                                </div>
                                <!--<div class="margin-top-20">
                                    <a href="#" class="filter-see-more">+ Ver Más</a>
                                </div>-->
                            </div>
                        </div>
                    

                    </div>
                    <!-- /Left column -->
                    <!-- Center column -->
                    <div class="col-lg aside">
                        <div class="prd-grid-wrap">

                            <!-- Filter Row -->
                            <div class="filter-row invisible padding-bottom-10">
                                <div class="row row-1 d-lg-none align-items-center">
                                    <div class="col">
                                        <h1 class="category-title"><?php if($data_categoria['categoria'] != ''){ echo $data_categoria['categoria']; }elseif($data_proveedor['razon_social'] != ''){ echo $data_proveedor['razon_social']; }else{ echo 'Destacados'; } ?></h1>
                                    </div>
                                </div>
                                <div class="row row-1 padding-bottom-10">

                                    <div class="col col-left d-none d-lg-flex align-items-left justify-content-left">
                                        <div class="page-title text-left d-none d-lg-block">
                                            <div class="title">
                                                <h1 class="margin-bottom-0 h1-style-category text-uppercase"><?php if($data_categoria['categoria'] != ''){ echo $data_categoria['categoria']; }elseif($data_proveedor['razon_social'] != ''){ echo $data_proveedor['razon_social']; }else{ echo 'Destacados'; } ?></h1>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-right ml-auto d-none d-lg-flex align-items-center">
                                        <div class="items-count"></div>

                                        <div class="paginas"></div>

                                        <!--<div class="customBtns">
                                            <span class="arrowPrev"><i class="icon-angle-left"></i></span>
                                            <span class="arrowNext"><i class="icon-angle-right"></i></span>
                                        </div>-->

                                        <!--<div class="show-count-holder">
                                            <div class="select-wrapper-sm">
                                                <select class="form-control input-sm">
                                                    <option value="featured">12</option>
                                                    <option value="rating">36</option>
                                                    <option value="price">100</option>
                                                </select>
                                            </div>
                                        </div>-->
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-right w-100 d-flex align-items-right">
                                        <div class="sort-by-holder">
                                            <div class="select-label d-none d-lg-inline">Precio:</div>
                                            <div class="select-wrapper-sm d-none d-lg-inline-block margin-right-20">
                                                <select class="form-control input-sm mdb-select" name="filtro-precio">
                                                    <option value="0" <?php if(empty($precio) || $precio == 'todos'){ echo 'selected'; }?>>Seleccionar</option>
                                                    <option value="masbajos" <?php if($precio == 'masbajos'){ echo 'selected'; }?>>Más Bajos</option>
                                                    <option value="masaltos" <?php if($precio == 'masaltos'){ echo 'selected'; }?>>Más Altos</option>
                                                </select>
                                            </div>
                                            <div class="select-label d-none d-lg-inline">Ordenar:</div>
                                            <div class="select-wrapper-sm d-none d-lg-inline-block">
                                                <select class="form-control input-sm" name="filtro-orden">
                                                    <option value="0" <?php if(empty($orden) || $orden == 'todos'){ echo 'selected'; }?>>Seleccionar</option>
                                                    <option value="nuevos" <?php if($orden == 'nuevos'){ echo 'selected'; }?>>Más Nuevos</option>
                                                    <option value="masvendidos" <?php if($orden == 'masvendidos'){ echo 'selected'; }?>>Más Vendidos</option>
                                                    <option value="masvistos" <?php if($orden == 'masvistos'){ echo 'selected'; }?>>Más Vistos</option>
                                                </select>
                                            </div>
                                            <div class="dropdown d-flex d-lg-none align-items-center justify-content-center">
                                                <span class="select-label">Precio:</span>
                                                <div class="select-wrapper-sm">
                                                    <select class="form-control input-sm" name="filtro-precio">
                                                        <option value="0" <?php if(empty($precio) || $precio == 'todos'){ echo 'selected'; }?>>Seleccionar</option>
                                                        <option value="masbajos" <?php if($precio == 'masbajos'){ echo 'selected'; }?>>Más Bajos</option>
                                                        <option value="masaltos" <?php if($precio == 'masaltos'){ echo 'selected'; }?>>Más Altos</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="filter-button d-lg-none"><a href="#" class="fixed-col-toggle">FILTROS</a></div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Filter Row -->
                            <!-- Products Grid -->
                            <div class="prd-grid product-listing data-to-show-4 data-to-show-md-3 data-to-show-sm-2 js-category-grid margin-top-0" id="gridContainer">
                                <?php
                                    if(!empty($_SESSION["admin_id"]) && $_SESSION["admin_privilegio"] == 'comprador'){
                                        $sql1 = "SELECT * FROM ".$prefix."proveedor_reglas WHERE id_cliente = '".$_SESSION["admin_id"]."' ";
                                        $result1 = $conexion->query($sql1);
                                        $qty_reglas = $result1->num_rows;
                                    }

                                    $sql = "SELECT prod.id as id, prod.codigo as codigo, prod.producto as producto, prod.categoria as categoria, prod.subcategoria as subcategoria, prod.unidad_medida as unidad_medida, prod.empaque as empaque, prod.precio as precio, prod.moneda as moneda, prod.descripcion as descripcion, prod.stock as stock, prod.estado as estado, prod.fecha_add as fecha_add, prod.fecha_update as fecha_update, prod.id_asociado as id_asociado, prod.token as token, prod.ventas as ventas, prov.razon_social as razon_social, prov.seourl as seourl_proveedor  ";
                                    /*if(!empty($searchword)){
                                        $arr_search = explode(" ", $searchword);
                                        $sql .= " , MATCH(prod.producto) AGAINST(' ";
                                        foreach($arr_search as $key){
                                            $sql .= "+".$key." ";
                                        }
                                        $sql .= "' IN BOOLEAN MODE) as relevancia ";
                                    }*/
                                    $sql .= " FROM ".$prefix."producto as prod, ".$prefix."proveedor as prov ";
                                    if(!empty($_SESSION["admin_id"]) && $_SESSION["admin_privilegio"] == 'comprador' && $qty_reglas > 0){
                                        $sql .= " , ".$prefix."proveedor_reglas as reg ";
                                    }
                                    $sql .= " WHERE prod.estado = 'activo' AND prov.estado = 'activo' AND prod.id_asociado = prov.id ";
                                    if(!empty($_SESSION["admin_id"]) && $_SESSION["admin_privilegio"] == 'comprador' && $qty_reglas > 0){
                                        $sql .= " AND (prod.id_asociado <> reg.id_proveedor) AND (reg.id_cliente = '".$_SESSION["admin_id"]."') ";
                                    }
                                    if(!empty($proveedor)){
                                        $sql .= " AND (prov.id = '".$data_proveedor['id']."') ";
                                    }
                                    if(!empty($categoria)){
                                        $sql .= " AND (prod.categoria = '".$data_categoria['id']."') ";
                                    }
                                    if(!empty($subcategoria) && $subcategoria != 'todas'){
                                        $sql .= " AND (prod.subcategoria = '".$data_subcategoria['id']."') ";
                                    }
                                    if(!empty($searchword)){
                                        $arr_search = explode(" ", $searchword);
                                        $sql .= " AND ((MATCH(prod.producto) AGAINST(' ";
                                        foreach($arr_search as $key){
                                            $sql .= "".$key." ";
                                        }
                                        $sql .= "' IN BOOLEAN MODE)) ";
                                    }
                                    if(!empty($searchword)){
                                        $sql .= " OR (prod.producto LIKE '%".$searchword."%' OR prod.codigo LIKE '%".$searchword."%' OR prov.razon_social LIKE '%".$searchword."%' OR prod.categoria_txt LIKE '%".$searchword."%' OR prod.subcategoria_txt LIKE '%".$searchword."%')) ";
                                    }
                                    if($precio == 'masbajos'){
                                        $sql .= " ORDER BY prod.precio ASC ";
                                    }elseif($precio == 'masaltos'){
                                        $sql .= " ORDER BY prod.precio DESC";
                                    }elseif($orden == 'nuevos'){
                                        $sql .= " ORDER BY fecha_add DESC ";
                                    }elseif($orden == 'masvendidos'){
                                        $sql .= " ORDER BY ventas DESC ";
                                    }elseif($orden == 'masvistos'){
                                        $sql .= " ORDER BY vistas DESC ";
                                    }else{
                                        $sql .= " ORDER BY RAND() ";
                                    }
                                    //$sql .= " LIMIT ".$limite_inferior.",6 ";
                                    //echo $sql;
                                    $result = $conexion->query($sql);
                                    $qty_resultados = $result->num_rows;
                                    while($data_sql = $result->fetch_array(MYSQLI_BOTH)){
                                        $existe_precio_especial = 0;

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
                                                <?php if($existe_precio_especial > 0){ ?>
                                                    <div class="price-old"><?php echo formato_precio($data_sql['precio']); ?></div>
                                                <?php } ?>
                                                <?php if(!isset($_SESSION["admin_privilegio"]) || $_SESSION["admin_privilegio"] != 'proveedor'){ ?><form action="#"><input type="hidden"> <button class="btn btn-grid-cart" data-fancybox data-type="ajax" data-src="<?php echo $baseurl; ?>cart-popup.php?id=<?php echo $cadena; ?>&qty=1"><span>Agregar</span></button></form><?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>

                            <?php if($qty_resultados == 0){ ?>
                                <div class="holder margin-top-50 margin-bottom-50 sin-resultados">
                                    <div class="container">
                                        <p>No existen resultados</p>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="loader-wrap">
                                <div class="dots">
                                    <div class="dot one"></div>
                                    <div class="dot two"></div>
                                    <div class="dot three"></div>
                                </div>
                            </div>
                            <!-- /Products Grid -->
                            <div class="row row-1 padding-bottom-10 margin-top-50">
                                <div class="col-right ml-auto d-none d-lg-flex align-items-center">
                                    <div class="items-count"></div>

                                    <div class="paginas"></div>

                                    <!--<div class="customBtns">
                                        <span class="arrowPrev"><i class="icon-angle-left"></i></span>
                                        <span class="arrowNext"><i class="icon-angle-right"></i></span>
                                    </div>-->

                                    <!--<div class="show-count-holder">
                                        <div class="select-wrapper-sm">
                                            <select class="form-control input-sm">
                                                <option value="featured">12</option>
                                                <option value="rating">36</option>
                                                <option value="price">100</option>
                                            </select>
                                        </div>
                                    </div>-->
                                </div>
                            </div>
                            <!--<div class="show-more d-flex mt-4 mt-md-6 justify-content-center align-items-start"><a href="#" class="btn btn--alt js-product-show-more" data-load="ajax/ajax-products-load.html">show more</a>
                                <ul class="pagination mt-0">
                                    <li class="active"><a href="#">1</a></li>
                                    <li><a href="#">2</a></li>
                                    <li><a href="#">3</a></li>
                                    <li><a href="#">4</a></li>
                                    <li><a href="#">5</a></li>
                                </ul>
                            </div>-->
                            
                        </div>
                    </div>
                    <!-- /Center column -->
                </div>
                <!-- /Two columns -->
            </div>
        </div>
    </div>
    
    <?php include('footer.php'); ?>

    <?php include('includes/scripts.php'); ?>

    <script type="text/javascript">
        /*$(window).load(function(){
            var proveedor = '<?php echo $proveedor; ?>';
            var categoria = '<?php echo $categoria; ?>';
            var subcategoria = '<?php echo $subcategoria; ?>';
            var pagina = '<?php echo $pagina; ?>';
            var precio = '<?php echo $precio; ?>';
            var orden = '<?php echo $orden; ?>';

            $.ajax({
              url: 'enviar.php',
              type: 'post',
              data: {'accion':'load_catalogo', 'proveedor':proveedor, 'categoria':categoria, 'subcategoria':subcategoria, 'pagina':pagina, 'precio':precio, 'orden':orden},
              dataType: 'html',
              success: function(response){
                $('ul.pagination').html(response);
                console.log(response);
              }
            });

            return false;
        });*/

        $(function(){
          $("div.paginas").jPages({
            containerID : "gridContainer",
            perPage     : 16,
            first: false,
            last: false,
            callback    : function(pages, items){
                //console.log(pages.current);
                //console.log(pages.count);
                //console.log(items.range.start);
                //console.log(items.range.end);
                //console.log(items.count);

                $('.items-count').html('Mostrando ' + items.range.end + ' de ' + items.count);

                //$("#legend1").html("Page " + pages.current + " of " + pages.count);
                //$("#legend2").html(items.range.start + " - " + items.range.end + " of " + items.count);
            }
          });
        });
    </script>
</body>

</html>