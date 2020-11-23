<?php 
include('includes/config.php');
if(isset($_SESSION["admin_id"])){ header ("Location: administracion/dashboard.html"); }
//if(isset($_SESSION["comprador_id"])){ header ("Location: mi-cuenta.html"); }
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1">
    <meta name="description" content="<?php echo seo_meta_descripcion(7, $prefix); ?>">
    <meta name="author" content="<?php echo $author; ?>">
    <base href="<?php echo $baseurl; ?>">

    <title><?php echo seo_meta_titulo(7, $prefix); ?> | <?php echo $sitio; ?></title>

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
    <link href="css/bootstrap-select.css" rel="stylesheet">
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
                <div class="row justify-content-center">
                    <div class="col-sm-8 col-md-6">
                        <div class="box-login">
                            <h2 class="text-center">Regístrate</h2>
                            <div class="form-wrapper">
                                <form id="registerFormulario" method="post">
                                    <input name="latitud" type="hidden" id="latitud-direccion" value="">
                                    <input name="longitud" type="hidden" id="longitud-direccion" value="">

                                    <div class="form-opc2">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Nombre Encargado de pedidos</label>
                                                    <input type="text" class="form-control" name="nombre" placeholder="Nombre">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Apellido Encargado de pedidos</label>
                                                    <input type="text" class="form-control" name="apellido" placeholder="Apellido">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Email</label>
                                                    <input type="email" class="form-control" name="email" placeholder="E-mail">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Teléfono Encargado de pedidos</label>
                                                    <div style="position:relative;">
                                                        <span style="font-family: 'Circular Std Book';position: absolute;left: 15px;top: 8px;color: #999999;text-transform: none;font-size: 15px;font-weight: 400;">(+56)</span>
                                                        <input type="text" class="form-control" name="telefono" placeholder="" style="text-indent: 38px;" maxlength="9" onKeyPress="return soloNumeros(event)">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label>Rut Empresa</label>
                                                    <input type="email" class="form-control rutf" name="rut" placeholder="76889009-0">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label>Nombre Fantasía</label>
                                                    <input type="text" class="form-control" name="razon_social" placeholder="Razón Social">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label>Actividad Económica</label>
                                                    <select class="form-control selectpicker" name="giro" data-live-search="true" data-size="12" data-dropup-auto="false">
                                                        <option value="0">Seleccione</option>
                                                        <?php
                                                            $sql = "SELECT * FROM ".$prefix."giros WHERE id <> '' AND giro <> '' AND estado = 'activo' GROUP BY giro ORDER BY giro ASC  ";
                                                            $result = $conexion->query($sql);
                                                            while($data_sql = $result->fetch_array(MYSQLI_BOTH)){
                                                        ?>
                                                        <option value="<?php echo $data_sql['codigo']; ?>"><?php echo $data_sql['giro']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label>Dirección</label>
                                                    <input type="text" class="form-control" name="direccion" id="input_direccion" placeholder="Dirección">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label>Comuna</label>
                                                    <select class="form-control selectpicker" name="comuna" data-live-search="true" data-size="10" data-dropup-auto="false">
                                                        <option value="0">Seleccione</option>
                                                        <?php
                                                            $sql = "SELECT * FROM ".$prefix."comunas WHERE id <> '' AND estado = 'activo' ORDER BY comuna ASC  ";
                                                            $result = $conexion->query($sql);
                                                            while($data_sql = $result->fetch_array(MYSQLI_BOTH)){
                                                                if($data_sql['comuna'] != ''){
                                                        ?>
                                                        <option value="<?php echo $data_sql['codigo']; ?>"><?php echo $data_sql['comuna']; ?></option>
                                                        <?php } } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <!--<div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Ciudad</label>
                                                    <select class="form-control selectpicker" name="ciudad" data-live-search="true" data-size="10" data-dropup-auto="false">
                                                        <option value="0">Seleccione</option>
                                                        <?php
                                                            $sql = "SELECT * FROM ".$prefix."ciudades WHERE id <> '' ORDER BY ciudad ASC  ";
                                                            $result = $conexion->query($sql);
                                                            while($data_sql = $result->fetch_array(MYSQLI_BOTH)){
                                                                if($data_sql['ciudad'] != ''){
                                                        ?>
                                                        <option value="<?php echo $data_sql['codigo']; ?>"><?php echo $data_sql['ciudad']; ?></option>
                                                        <?php } } ?>
                                                    </select>
                                                </div>
                                            </div>-->
                                        </div>
                                    </div>

                                    <div class="clearfix margin-top-40">
                                        <input id="checkbox0" name="checkbox0" type="checkbox">
                                        <label for="checkbox0">Soy proveedor</label>
                                    </div>

                                    <div class="form-opc1 opc-hidden" style="height: 0px;visibility: hidden;">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Nombre Encargado de los pedidos</label>
                                                    <input type="text" class="form-control" name="nombre_contacto" placeholder="Nombre">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Apellido Encargado de los pedidos</label>
                                                    <input type="text" class="form-control" name="apellido_contacto" placeholder="Apellido">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Email Encargado de los pedidos</label>
                                                    <input type="email" class="form-control" name="email_contacto" placeholder="E-mail">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Teléfono Encargado de los pedidos</label>
                                                    <div style="position:relative;">
                                                        <span style="font-family: 'Circular Std Book';position: absolute;left: 15px;top: 8px;color: #999999;text-transform: none;font-size: 15px;font-weight: 400;">(+56)</span>
                                                        <input type="text" class="form-control" name="telefono_contacto" placeholder="" style="text-indent: 38px;" maxlength="9" onKeyPress="return soloNumeros(event)">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Crear contraseña</label>
                                        <input type="password" class="form-control" name="password" maxlength="22" placeholder="Password" autocomplete="off">
                                    </div>
                                    
                                    <div class="clearfix margin-top-40">
                                        <?php
                                            $sql = "SELECT * FROM ".$prefix."cms WHERE tipo = 'terminosycondiciones' AND estado = 'activo' AND seourl != '' LIMIT 1 ";
                                            $result = $conexion->query($sql);
                                            $qty_cms = $result->num_rows;
                                            $data_sql = $result->fetch_array(MYSQLI_BOTH);
                                        ?>
                                        <input id="checkbox1" name="checkbox1" type="checkbox">
                                        <label for="checkbox1">Acepto los términos y condiciones <?php if($qty_cms > 0){ ?>(<a href="<?php echo $baseurl; ?>cms/<?php echo $data_sql['seourl'] ?>.html" target="_blank">Leer</a>)<?php } ?></label>
                                    </div>
                                    <div class="text-center margin-top-20">
                                        <button class="btn btn--full btn--lg btn-submit-form">Registrar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include('footer.php'); ?>

    <?php include('includes/scripts.php'); ?>
    <script src="https://maps.googleapis.com/maps/api/js?v=3&libraries=places&callback=initMain&key=AIzaSyDluzFoj_qqThh8z_krzxM3tmQPXPRteWI"
        async></script>

    <script type="text/javascript">
        function soloNumeros(e){
            var key = window.Event ? e.which : e.keyCode;
            return ((key >= 48 && key <= 57) || (key==8)); 
        }

        $('input[name="nombre"]').keyup(function(){
            var this_value = $(this).val();
            $('input[name="nombre_contacto"]').val(this_value);
        });

        $('input[name="apellido"]').keyup(function(){
            var this_value = $(this).val();
            $('input[name="apellido_contacto"]').val(this_value);
        });

        $('input[name="email"]').keyup(function(){
            var this_value = $(this).val();
            $('input[name="email_contacto"]').val(this_value);
        });

        $('input[name="telefono"]').keyup(function(){
            var this_value = $(this).val();
            $('input[name="telefono_contacto"]').val(this_value);
        });

        $('input[type=checkbox][name=checkbox0]').change(function(){
            if($(this).is(':checked')){
                $('.form-opc1').removeClass('opc-hidden');
            }else{
                $('.form-opc1').addClass('opc-hidden');
            }
        });

        $('.rutf').Rut({
            format_on: 'keyup'
        });

        var initMain = function(){
          initMap1();
        };

        var map = null;
        var infoWindow = null;

        function initMap1(){
          var caso = 'direccion';
          var latitud_inicio = parseFloat('<?php if(!empty($data_sql["latitud"])){ echo $data_sql["latitud"]; }else{ echo $latitud_inicio; } ?>');
          var longitud_inicio = parseFloat('<?php if(!empty($data_sql["longitud"])){ echo $data_sql["longitud"]; }else{ echo $longitud_inicio; } ?>');

          /*var map = new google.maps.Map(document.getElementById('map-contact-'+caso), {
            center: {lat: latitud_inicio, lng: longitud_inicio},
            zoom: 13,
            mapTypeId: google.maps.MapTypeId.ROADMAP
          });*/
          var input = (document.getElementById('input_'+caso));

          var types = document.getElementById('type-selector');
          //map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
          //map.controls[google.maps.ControlPosition.TOP_LEFT].push(types);

          var options = {
            componentRestrictions: {country: "CL"}
          };

          var autocomplete = new google.maps.places.Autocomplete(input, options);
          //autocomplete.bindTo('bounds', map);

          //var infowindow = new google.maps.InfoWindow();
          /*var marker = new google.maps.Marker({
            map: map,
            draggable: true,
            position: {lat: latitud_inicio, lng: longitud_inicio}
          });*/

          /*google.maps.event.addListener(marker, 'dragend', function(){
            var markerLatLng = marker.getPosition();
            $('#latitud-'+caso).val(markerLatLng.lat());
            $('#longitud-'+caso).val(markerLatLng.lng());
          });*/

          autocomplete.addListener('place_changed', function(){
            //infowindow.close();
            //marker.setVisible(false);
            var place = autocomplete.getPlace();
            var latitud = place.geometry.location.lat;
            var longitud = place.geometry.location.lng;

            $('#latitud-'+caso).val(latitud);
            $('#longitud-'+caso).val(longitud);

            if (!place.geometry) {
              window.alert("Dirección no ha sido encontrada: '" + place.name + "'");
              return;
            }

            /*if (place.geometry.viewport){ // If the place has a geometry, then present it on a map.
              map.fitBounds(place.geometry.viewport);
            } else {
              map.setCenter(place.geometry.location);
              map.setZoom(17);
            }*/

            //marker.setPosition(place.geometry.location);
            //marker.setVisible(true);

            var address = '';
            if (place.address_components) {
              address = [
              (place.address_components[0] && place.address_components[0].short_name || ''),
              (place.address_components[1] && place.address_components[1].short_name || ''),
              (place.address_components[2] && place.address_components[2].short_name || '')
              ].join(' ');
            }

            //infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
            //infowindow.open(map, marker);
          });
        }
    </script>
</body>

</html>