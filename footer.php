<footer class="page-footer footer-style-1 global_width">
    <div class="footer-top container">
        <div class="row lined pb-md-2">
            <?php
                $sql = "SELECT * FROM ".$prefix."hooks WHERE id = '1' LIMIT 1 ";
                $result = $conexion->query($sql);
                $data_hook = $result->fetch_array(MYSQLI_BOTH);
                if($data_hook['estado'] == 'activo'){
            ?>
            <div class="col-md-12 col-lg py-2 py-lg-0 text-center text-lg-left">
                <div class="footer-block">
                    <?php echo $data_hook['contenido']; ?>
                    <!--<div class="footer-logo"><a href="#"><img src="supplyme-images/logo_footer.png" class="img-fluid" alt=""></a></div>
                    <div class="footer-logo-text">
                        <p>With more than 30 years of experience we can proudly say that we are one of the best in business, a trusted supplier for more than 1000 companies.</p>
                    </div>-->
                </div>
            </div>
            <?php } ?>
            <div class="col-md col-lg">
                <div class="footer-block collapsed-mobile padding-left-30">
                    <div class="title">
                        <h4>Productos</h4>
                        <div class="toggle-arrow"></div>
                    </div>
                    <div class="collapsed-content">
                        <ul>
                            <li><a href="<?php echo $baseurl; ?>catalogo/pag-1/precio-todos/orden-nuevos.html">Agregados Recientemente</a></li>
                            <li><a href="<?php echo $baseurl; ?>catalogo/pag-1/precio-todos/orden-masvendidos.html">Más vendidos</a></li>
                            <li><a href="<?php echo $baseurl; ?>catalogo/pag-1/precio-todos/orden-masvistos.html">Más vistos</a></li>
                            <li><a href="<?php echo $baseurl; ?>proveedores.html">Proveedores</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <?php
                $sql = "SELECT * FROM ".$prefix."hooks WHERE id = '2' LIMIT 1 ";
                $result = $conexion->query($sql);
                $data_hook = $result->fetch_array(MYSQLI_BOTH);
                if($data_hook['estado'] == 'activo'){
            ?>
            <div class="col-md col-lg">
                <div class="footer-block collapsed-mobile">
                    <div class="title">
                        <h4>Contacto</h4>
                        <div class="toggle-arrow"></div>
                    </div>
                    <div class="collapsed-content">
                        <?php echo $data_hook['contenido']; ?>
                    </div>
                </div>
            </div>
            <?php } ?>
            <div class="col-md col-lg pb-3 pb-md-0 text-center-sm">
                <div class="footer-block footer-subscribe">
                    <div class="title">
                        <h4>Suscríbete</h4>
                    </div>
                    <div class="subscribe-wrap">
                        <div class="subscribe-form">
                            <?php
                                $sql = "SELECT * FROM ".$prefix."hooks WHERE id = '3' LIMIT 1 ";
                                $result = $conexion->query($sql);
                                $data_hook = $result->fetch_array(MYSQLI_BOTH);
                                if($data_hook['estado'] == 'activo'){
                            ?>
                            <div class="subscribe-form-text">
                                <?php echo $data_hook['contenido']; ?>
                            </div>
                            <?php } ?>
                            <form action="#" method="post" id="newsletterFormulario">
                                <div class="input-group">
                                    <div class="form-control-wrapper"><input type="text" class="form-control" name="emailnewsletter" placeholder="Ingresa tu email"></div>
                                    <div class="input-group-btn"><button class="btn btn-submit-form"><i class="icon-angle-right"></i></button></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <div class="row lined padding-top-20 padding-bottom-20">
                <div class="col-md-12">
                    <div class="footer-copyright">
                        <p class="footer-copyright-text"><span>© TODOS LOS DERECHOS RESERVADOS Supplyme SpA. Volcán Lascar 730, Santiago de Chile. /</span> <a href="<?php echo $baseurl; ?>cms/terminos-y-condiciones.html">Términos y condiciones</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>