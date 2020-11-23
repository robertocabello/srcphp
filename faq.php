<?php include('includes/config.php'); ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1">
    <meta name="description" content="<?php echo seo_meta_descripcion(5, $prefix); ?>">
    <meta name="author" content="<?php echo $author; ?>">
    <base href="<?php echo $baseurl; ?>">

    <title><?php echo seo_meta_titulo(5, $prefix); ?> | <?php echo $sitio; ?></title>
    
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
        <div class="holder mt-0 global-width padding-top-50 padding-bottom-50 holder-gris">
            <div class="container">
                <div class="simple-filter js-simple-filter">
                    <div class="text-center">
                        <h2 class="h1-style h1-style-faq">FAQ</h2>
                        <div class="simple-filter-tabs js-simple-filters-tabs">
                            <?php
                                $sql = "SELECT * FROM ".$prefix."cms_categoria WHERE tipo = 'faq' AND estado = 'activo' ORDER BY orden ASC ";
                                $result = $conexion->query($sql);
                                while($data_sql = $result->fetch_array(MYSQLI_BOTH)){
                            ?>
                                <span class="js-simple-filter-label" data-filter=".<?php echo $data_sql['seourl']; ?>"><?php echo $data_sql['titulo']; ?></span>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="faq-wrapper simple-filter-wrap">
                        <?php
                            $sql = "SELECT c.id as id, c.titulo as titulo, c.contenido as contenido, cc.seourl as seourl FROM ".$prefix."cms as c, ".$prefix."cms_categoria as cc WHERE c.tipo = 'faq' AND c.categoria = cc.id AND c.estado = 'activo' ORDER BY c.orden ASC ";
                            $result = $conexion->query($sql);
                            while($data_sql = $result->fetch_array(MYSQLI_BOTH)){
                        ?>
                        <div class="faq-item js-simple-filter-item <?php echo $data_sql['seourl']; ?>">
                            <div class="panel">
                                <div class="panel-heading">
                                    <a data-toggle="collapse" href="#faq<?php echo $data_sql['id']; ?>" class="collapsed">
                                        <div class="panel-title"><?php echo $data_sql['titulo']; ?></div>
                                    </a>
                                </div>
                                <div id="faq<?php echo $data_sql['id']; ?>" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <?php echo $data_sql['contenido']; ?>
                                    </div>
                                </div>
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
</body>

</html>