<?php

$show_sidenav = true;
$show_breadcrumb = true;

$logged = true;



?>
    <header class="py-3 px-4 border-bottom position-sticky top-0 z-5 bg-white shadow-sm">
        <div class="container-fluid px-0">
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                <?php

                if ($show_sidenav) {

                    ?>
                    <div class="">
                        <a href="javascript:;" class="py-3 px-4 main-sidebar-toggle" onclick="toggle_main_sidebar();">
                            <i class="far fa-bars fa-lg text-dark"></i>
                        </a>
                    </div>

                    <div class="">
                        <?php

                        \Lumio\View\Components\Breadcrumb::build()->render();

                        ?>
                    </div>
                    <?php
                }

                ?>

                <nav aria-label="breadcrumb" class="d-none d-lg-block text-sm">
                    <?php
                    if (!empty($show_breadcrumb)) {
//                        \Mark\Libs\Utilities\Breadcrumb::render();
                    }
                    ?>
                    <h6 class="font-weight-bolder mb-0"><?php echo $this->_page_label ?? ''; ?></h6>
                </nav>

                <div class="col-auto mb-0 me-3 ms-auto"></div>

                <?php /*<form class="col-auto mb-0 me-3 ms-auto" role="search">
                <input type="search" class="form-control" placeholder="<?php __t('Hledat...'); ?>" aria-label="Search">
            </form>*/ ?>



                <?php
                if ($logged) {

                    $this->partial('user_menu.inc');


                } else {
                    ?>
                    <a href="javascript:;" class="nav-link text-body font-weight-bold px-0">
                    <i class="fa fa-sign-in me-1" aria-hidden="true"></i>
                    <span class="d-sm-inline d-none"><?php __t('Přihlášení'); ?></span>
                    </a><?php __t('Přihlásit se'); ?>
                    <?php
                }
                ?>
            </div>
        </div>
    </header>
<?php









