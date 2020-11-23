<?php
if(!empty($_SESSION["cart_id"])){
    $sql = "SELECT SUM(cantidad) as suma FROM ".$prefix."carro_detalle WHERE token = '".$_SESSION["cart_id"]."' ";
    $result = $conexion->query($sql);
    $data_sql_carro = $result->fetch_array(MYSQLI_BOTH);
    if($data_sql_carro['suma'] > 0){
        $total_carro = $data_sql_carro['suma'];
    }else{
        $total_carro = 0;
    }
}else{
    $total_carro = 0;
}
?>
<div class="body-preloader">
    <div class="loader-wrap">
        <div class="dots">
            <div class="dot one"></div>
            <div class="dot two"></div>
            <div class="dot three"></div>
        </div>
    </div>
</div>
<header class="hdr global_width hdr_sticky hdr-mobile-style2">
    <!-- Mobile Menu -->
    <div class="mobilemenu js-push-mbmenu">
        <div class="mobilemenu-content">
            <div class="mobilemenu-close mobilemenu-toggle">CERRAR</div>
            <div class="mobilemenu-scroll">
                <div class="mobilemenu-search"></div>
                <div class="nav-wrapper show-menu">
                    <div class="nav-toggle"><span class="nav-back"><i class="icon-arrow-left"></i></span> <span class="nav-title"></span></div>
                    <ul class="nav nav-level-1">
                        <li><a href="<?php echo $baseurl; ?>index.html" title="Inicio">Inicio</a></li>
                        <li><a href="<?php echo $baseurl; ?>catalogo.html" title="Categorías">Todas las Categorías</a><span class="arrow"></span>
                            <ul class="nav-level-2">
                            	<?php
                                    $sql = "SELECT * FROM ".$prefix."categorias WHERE estado = 'activo' ORDER BY categoria ASC ";
                                    $result = $conexion->query($sql);
                                    while($data_sql_while = $result->fetch_array(MYSQLI_BOTH)){
                                        $sql2 = "SELECT * FROM ".$prefix."subcategorias WHERE estado = 'activo' AND categoria = '".$data_sql_while['id']."' ORDER BY subcategoria ASC ";
                                        $result2 = $conexion->query($sql2);
                                        $qty_subcategorias2 = $result2->num_rows;
                                ?>
                                <li>
                                	<a href="<?php echo $baseurl; ?>catalogo/productos/<?php echo $data_sql_while['seourl']; ?>/todas/pag-1/precio-todos/orden-todos.html" title="<?php echo $data_sql_while['categoria']; ?>"><?php echo $data_sql_while['categoria']; ?></a><span class="arrow"></span>
                                    <?php if($qty_subcategorias2 > 0){ ?>
                                        <ul class="nav-level-3">
                                            <?php
                                                while($data_sql_2 = $result2->fetch_array(MYSQLI_BOTH)){
                                            ?>
                                                <li><a href="<?php echo $baseurl; ?>catalogo/productos/<?php echo $data_sql_while['seourl']; ?>/<?php echo $data_sql_2['seourl']; ?>/pag-1/precio-todos/orden-todos.html" title="<?php echo $data_sql_2['subcategoria']; ?>"><?php echo $data_sql_2['subcategoria']; ?></a></li>
                                            <?php } ?>
                                        </ul>
                                    <?php } ?>
                                </li>
                                <?php } ?>
                            </ul>
                        </li>
                        <li><a href="<?php echo $baseurl; ?>catalogo.html" title="Proveedores">Proveedores</a><span class="arrow"></span>
                            <ul class="nav-level-2">
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
                                <li>
                                    <a href="<?php echo $baseurl; ?>catalogo/proveedores/<?php echo utf8_decode($data_sql['seourl']); ?>/pag-1/precio-todos/orden-todos.html" title="<?php echo $data_sql['razon_social']; ?>"><?php echo $data_sql['razon_social']; ?></a><span class="arrow"></span>
                                </li>
                                <?php } } ?>
                            </ul>
                        </li>
                        <li><a href="<?php echo $baseurl; ?>nosotros.html" title="Nosotros">Nosotros</a></li>
                        <li><a href="<?php echo $baseurl; ?>faq.html" title="FAQ">FAQ</a></li>
                        <li><a href="<?php echo $baseurl; ?>contacto.html" title="Contacto">Contacto</a></li>
                        <li><a href="<?php echo $baseurl; ?>login.html" title="Mi Cuenta">Mi Cuenta</a></li>
                        <li><a href="<?php echo $baseurl; ?>carro.html" title="Carro de Compra">Carro de Compra</a></li>
                    </ul>
                </div>
                <div class="mobilemenu-bottom">
                    <div class="mobilemenu-currency"></div>
                    <div class="mobilemenu-language"></div>
                    <div class="mobilemenu-settings"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Mobile Menu -->
    <div class="hdr-mobile show-mobile">
        <div class="hdr-content">
            <div class="container">
                <!-- Menu Toggle -->
                <div class="menu-toggle"><a href="#" class="mobilemenu-toggle"><i class="icon icon-menu"></i></a></div>
                <!-- /Menu Toggle -->
                <div class="logo-holder"><a href="index.html" class="logo"><img src="supplyme-images/logo.png" srcset="supplyme-images/logo.png" alt=""></a></div>
                <div class="hdr-mobile-right">
                    <div class="search-holder">
                        <div class="search">
                            <button type="submit" class="search-button"><i class="icon-search2"></i></button>
                            <input type="text" class="search-input" name="searchword" placeholder="Buscar..." value="<?php if(!empty($searchword)){ echo $searchword; } ?>">
                        </div>
                    </div>
                    <div class="minicart-holder"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="hdr-desktop hide-mobile">
        <div class="hdr-content hide-mobile">
            <div class="">
                <div class="row padding-bottom-20 padding-left-30 padding-right-30">
                    <div class="col-auto logo-holder col-md-2 col-lg-2 padding-top-10"><a href="index.html" class="logo"><img src="supplyme-images/logo.png" srcset="supplyme-images/logo.png" alt=""></a></div>
                    <div class="custom-col col-md-4 col-lg-5">
                        <?php
                            $sql = "SELECT * FROM ".$prefix."hooks WHERE id = '4' LIMIT 1 ";
                            $result = $conexion->query($sql);
                            $data_hook = $result->fetch_array(MYSQLI_BOTH);
                            if($data_hook['estado'] == 'activo'){
                        ?>
                        <div class="hdr-icn-text" style="padding-top: 3px;">
                            <div>
                                <?php echo $data_hook['contenido']; ?>
                                <!--<img src="supplyme-images/header-icon-phone.png">
                                <a href="tel:+5622266788">+562 226 6788</a>-->
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <!--navigation-->
                    <div class="nav-holder col-md-6 col-lg-5">
                        <div class="hdr-nav">
                            <!--mmenu-->
                            <ul class="mmenu mmenu-js">
                                <li><a href="index.html" <?php if($fichero == 'index.php'){ ?>class="active"<?php } ?>>Home</a></li>
                                <li><a href="catalogo.html" <?php if($fichero == 'catalogo.php'){ ?>class="active"<?php } ?>>Comprar</a></li>
                                <?php
                                    $sql = "SELECT * FROM ".$prefix."cms WHERE tipo = 'nosotros' AND estado = 'activo' AND seourl != '' LIMIT 1 ";
                                    $result = $conexion->query($sql);
                                    $qty_cms = $result->num_rows;
                                    if($qty_cms > 0){
                                    $data_sql_menu = $result->fetch_array(MYSQLI_BOTH);
                                ?>
                                    <li><a href="cms/<?php echo $data_sql_menu['seourl'] ?>.html">Nosotros</a></li>
                                <?php } ?>
                                <li><a href="faq.html">FAQ</a></li>
                                <li><a href="contacto.html">Contacto</a></li>
                                <li><a href="login.html" <?php if(isset($_SESSION["admin_id"])){ ?>class="not-before"<?php } ?>><?php if(isset($_SESSION["admin_id"])){ ?><span class="icon-person"></span><?php }else{ ?>Mi Cuenta<?php } ?></a></li>
                            </ul>
                            <!--/mmenu-->
                        </div>
                    </div>
                    <!--//navigation-->
                    <div class="minicart-holder main-cart col-md-2 col-lg-1 padding-left-0 padding-right-0">
                        <div class="minicart minicart-js">
                            <a href="carro.html">
                                <div><img src="<?php echo $baseurl; ?>supplyme-images/header-icon-cart.png"></div>
                                <div><span class="minicart-qty"><?php echo $total_carro; ?></span></div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row hidden-block-header">
                    <?php if($fichero != 'index.php'){ ?><div class="col-auto col-sm-3"></div><?php } ?>
                    <div class="col search-holder <?php if($fichero != 'index.php'){ ?>col-sm-8<?php } ?>">

                        <div class="search">
                            <button type="submit" class="search-button"><i class="icon-search2"></i></button>
                            <input type="text" id="mainbuscador" class="search-input" name="searchword" placeholder="Busca productos por nombre, proveedor, categoría o sku" value="<?php if(!empty($searchword)){ echo $searchword; } ?>">
                        </div>

                    </div>
                    <div class="minicart-holder <?php if($fichero != 'index.php'){ ?>col-sm-1<?php } ?>" style="padding-left:0px;padding-right:0px;">
                        <div class="minicart minicart-js">
                            <a href="carro.html">
                                <div><img src="<?php echo $baseurl; ?>supplyme-images/header-icon-cart.png"></div>
                                <div><span class="minicart-qty"><?php echo $total_carro; ?></span></div>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="sticky-holder compensate-for-scrollbar">
        <div class="">
            <div class="row" style="margin-left:0px;margin-right:0px;">
                <a href="#" class="mobilemenu-toggle show-mobile"><i class="icon icon-menu"></i></a>
                <div class="col-auto logo-holder-s padding-top-10 padding-bottom-10"><a href="index.html" class="logo"><img src="supplyme-images/logo.png" srcset="supplyme-images/logo.png" alt=""></a></div>
                <div class="custom-col col-md-2 col-lg-5">
                    <?php
                        $sql = "SELECT * FROM ".$prefix."hooks WHERE id = '4' LIMIT 1 ";
                        $result = $conexion->query($sql);
                        $data_hook = $result->fetch_array(MYSQLI_BOTH);
                        if($data_hook['estado'] == 'activo'){
                    ?>
                    <div class="hdr-icn-text" style="padding-top: 3px;">
                        <div>
                            <?php echo $data_hook['contenido']; ?>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <!--navigation-->
                <div class="prev-menu-scroll icon-angle-left prev-menu-js"></div>
                <div class="nav-holder-s"></div>
                <div class="next-menu-scroll icon-angle-right next-menu-js"></div>
                <!--//navigation-->
                <div class="col-auto minicart-holder-s"></div>
            </div>
    </div>
</header>