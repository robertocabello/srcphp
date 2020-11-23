<?php include('includes/config.php');

$idorden = $campos['token_ws'];
$result = $campos_['result'];
$actualizacion = '0';

if($idorden != '' && $idorden > 0){
  $sql = "SELECT * FROM seo_webpay WHERE webpay_token = '".$idorden."' ";
  $result = $conexion->query($sql);
  $fila = $result->fetch_array(MYSQLI_BOTH);
  $existe_transaccion = $result->num_rows;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1">
    <meta name="description" content="">

    <title>Error en el pago | <?php echo $sitio; ?></title>

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

                    <h1>Error de Pago</h1>

                    <?php if($idorden != ''){ ?>
                        <h4>La transacción con orden de compra <strong>#<?=$idorden ?></strong> no se ha realizado.</h4>
                    <?php }else{ ?>
                        <h4>La transacción no se ha realizado.</h4>
                    <?php } ?>
                
                    <div class="clearfix">
                      <ul class="simple-list margin-top-20">
                        <li><strong>Posibles causas de este rechazo:</strong></li>
                        <li><span>- Error en el ingreso de los datos de su tarjeta de crédito o débito (fecha y/o código de seguridad)</span></li>
                        <li><span>- Su tarjeta de crédito o débito no cuenta con el cupo necesario para cancelar la compra.</span></li>
                        <li><span>- Tarjeta aún no habilitada en el sistema financiero</span></li>
                        <li><span>- Ocurrió un inconveniente con la tarjeta.</span></li>
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