<?php include('includes/config.php');

$url = $campos['url'];

if(!empty($url)){
    $sql = "SELECT * FROM ".$prefix."cms WHERE seourl = '".$url."' LIMIT 1 ";
    $result = $conexion->query($sql);
    $data_sql = $result->fetch_array(MYSQLI_BOTH);

    if($data_sql['estado'] != 'activo'){
        header ("Location: ".$baseurl."404.html");
    }

    if($data_sql['tipo'] == 'nosotros'){ $id_seo = '3'; }
    if($data_sql['tipo'] == 'terminosycondiciones'){ $id_seo = '4'; }

    $seotitulo = seo_meta_titulo($id_seo, $prefix);
    $seodescripcion = seo_meta_descripcion($id_seo, $prefix);
}else{
    header ("Location: ".$baseurl."404.html");
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
    <!--icon font-->
    <link href="fonts/icomoon/icomoon.css" rel="stylesheet">
    <!--custom font-->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i" rel="stylesheet">
</head>

<body class="home-page is-dropdn-click has-slider">
    
    <?php include('header.php'); ?>

    <div class="page-content">
        <!--<div class="holder fullboxed mt-0 py-5 py-md-10 bg-cover" style="background-image:url(supplyme-images/nosotros.jpg)">
            <div class="container">
                <div class="row">
                    <div class="col-md-7 mx-auto">
                        <div class="text-center">
                            <p><img src="supplyme-images/logo.png" alt="" class="img-fluid"></p>
                            <p>Lorem ipsum dolor sit amet conse ctetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam. Consetr ipsum dolor sit amet conse ctetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam. Consetr ipsum dolor sit amet conse ctetur adipisicing elit, sed do eiusmod.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>-->
        <div class="holder margin-top-50 margin-bottom-50">
            <div class="container">

            	<?php echo $data_sql['contenido']; ?>

                <!--<div class="template-text">
                    <h2 class="h1-style text-center h1-style-aboutus">Nosotros</h2>
                    <p class="text-center">Lorem ipsum dolor sit amet conset ctetur adipisicing elit, sed do eiusmod tempor incidid.</p>
                </div>
                <div class="row vert-margin margin-top-60">
                    <div class="col-sm-6 col-md-3 col-lg-3">
                        <div class="block-it text-center">
                            <div class="block-it-icon"><img src="https://supplyme.webecommerce.cl/supplyme-images/icono-nosotros-1.png"></div>
                            <h3 class="text-uppercase">Delivery</h3>
                            <p>Lorem ipsum dolor sit amet conset ctetur adipisicing elit, sed do eiusmod tempor incidid.</p>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3 col-lg-3">
                        <div class="block-it text-center">
                            <div class="block-it-icon"><img src="https://supplyme.webecommerce.cl/supplyme-images/icono-nosotros-2.png"></div>
                            <h3 class="text-uppercase">Promociones & Descuentos</h3>
                            <p>Lorem ipsum dolor sit amet conset ctetur adipisicing elit, sed do eiusmod tempor incidid.</p>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3 col-lg-3">
                        <div class="block-it text-center">
                            <div class="block-it-icon"><img src="https://supplyme.webecommerce.cl/supplyme-images/icono-nosotros-3.png"></div>
                            <h3 class="text-uppercase">Formas de Pago</h3>
                            <p>Lorem ipsum dolor sit amet conset ctetur adipisicing elit, sed do eiusmod tempor incidid.</p>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3 col-lg-3">
                        <div class="block-it text-center">
                            <div class="block-it-icon"><img src="https://supplyme.webecommerce.cl/supplyme-images/icono-nosotros-4.png"></div>
                            <h3 class="text-uppercase">Regalos a nuestros clientes</h3>
                            <p>Lorem ipsum dolor sit amet conset ctetur adipisicing elit, sed do eiusmod tempor incidid.</p>
                        </div>
                    </div>
                </div>-->
            </div>
        </div>
        <?php
            if($data_sql['tipo'] == 'nosotros'){
			$sql = "SELECT * FROM ".$prefix."proveedor WHERE estado = 'activo' AND cuenta_proveedor = 'SI' AND archivo != '' ORDER BY razon_social ASC ";
			$result = $conexion->query($sql);
			$qty_proveedores = $result->num_rows;

			if($qty_proveedores > 0){
        ?>
        <div class="holder padding-bottom-80 holder-gris" style="overflow:hidden;">
            <div class="container">
                <h2 class="h1-style text-center h1-style-aboutus margin-top-50">Nuestros Partners</h2>
                <ul class="brand-carousel js-brand-carousel slick-arrows-aside-simple">
                	<?php while($data_sql_while = $result->fetch_array(MYSQLI_BOTH)){
                        if(!empty($_SESSION["admin_id"]) && $_SESSION["admin_privilegio"] == 'comprador'){
                            $sql1 = "SELECT * FROM ".$prefix."proveedor_reglas WHERE id_cliente = '".$_SESSION["admin_id"]."' AND id_proveedor = '".$data_sql_while['id']."' ";
                            $result1 = $conexion->query($sql1);
                            $existe_regla = $result1->num_rows;
                        }
                        if($existe_regla == 0){
                    ?>
                    	<li><a href="<?php echo $baseurl; ?>catalogo/proveedores/<?php echo $data_sql_while['seourl']; ?>/pag-1/precio-todos/orden-todos.html"><img src="<?php echo $baseurl; ?>/supplyme-images/proveedores/<?php echo $data_sql_while['archivo']; ?>" data-src="<?php echo $baseurl; ?>/supplyme-images/proveedores/<?php echo $data_sql_while['archivo']; ?>" class="lazyload" alt="<?php echo $data_sql_while['razon_social']; ?>"></a></li>
                    <?php } } ?>
                </ul>
            </div>
        </div>
        <?php } } ?>
    </div>
    
    <?php include('footer.php'); ?>

    <?php include('includes/scripts.php'); ?>
</body>

</html>