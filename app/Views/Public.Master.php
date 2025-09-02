<?php

use Lumio\View\Components\Img;




?>
<!DOCTYPE html>
<html lang="cs">

    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="x-ua-compatible" content="ie=edge">

        <title><?=$this->title()?></title>

        <meta name="description" content="<?=$this->description()?>" />
        <meta name="keywords" content="<?=$this->keywords()?>" />

        <?php $this->meta_csrf(); ?>

        <?php $this->og(); ?>

        <link rel="shortcut icon" href="/favicon.ico?v=<?=CACHE_VERSION?>" type="image/png" />
        <link rel="sitemap" type="application/xml" title="Sitemap" href="/sitemap.xml" />

        <?php $this->include_css(); ?>
        <?php $this->include_js(); ?>

    </head>
    <body class="bg-body-secondary body-public">



    <?php
    // navbar
    //include 'includes/nav_public.inc.php';
    ?>



    <header class="header-fixed header-public " id="header_main">

        <nav class="navbar navbar-expand-lg navbar-public bg-light border-bottom">

            <div class="container px-2 px-sm-0">
                <div class="bg-nav rounded d-flex flex-nowrap w-100 justify-content-between px-3">

                    <a class="navbar-brand col-5 col-lg-3 d-flex align-items-center order-1 me-0" href="/">
                        <?php

                        Img::build('logo/logo-row.png', [
                            'alt' => 'logo',
                            'height' => 40,
                            'class' => 'logo-img w-auto w-md-auto',
                        ])->render();

                        ?>
                    </a>

                    <div class="justify-content-center col-auto order-3 order-lg-2">

                        <ul class="navbar-nav text-white py-3 py-lg-3 flex-row align-items-center h-100">

                            <li class="nav-item">
                                <a href="/login/" class="nav-link mx-4 d-inline-block">
                                    <i class="fal fa-user me-md-1"></i>
                                    <span class="d-none d-md-inline-block"><?php __t('Log in'); ?></span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="/registrace/" class="btn btn-primary text-white d-inline-block text-wrap px-1 px-sm-3">
                                    <span class="d-none d-md-inline-block"><?php __t('Register'); ?></span>
                                </a>
                            </li>

                        </ul>

                    </div>

                </div>
            </div>

        </nav>

    </header>










    <main class="">

        <div class="container-fluid pb-0 px-1 mt-0">
            <?php


            echo $this->content();


            ?>
        </div>

    </main>

    <!-- -------------------------- FOOTER -------------------------- -->
    <footer class="">
        <div class="bg-dark text-white text-center p-5">
            <?php

            Img::build('logo/logo-column-light.png', [
                'alt' => 'logo',
                'style' => 'width:180px',
                'class' => 'avatar avatar-lg d-block mx-auto mb-2',
            ])->render();

            ?>
            <div><small><?php echo \Lumio\Config::get('app.app_name') ?> &copy <?php echo date('Y'); ?></small></div>


            <div class="d-flex justify-content-center gap-5 navbar mt-5">
                <a href="/terms-and-conditions/" class="nav-link text-uppercase "><small><?php __t('Terms and conditions'); ?></small></a>
                <a href="/personal-data/" class="nav-link text-uppercase "><small><?php __t('Personal data'); ?></small></a>
            </div>


            <hr class="my-5" />

            <div class="container">
                <div class="row">
                    <div class="col-auto mx-auto opacity-75">
                        <div class="mb-3">
                            <small><small><?php __t('Provozovatel'); ?></small></small>
                        </div>
                        <div class="mb-3 fw-light">
                            Name Surname
                            <br />
                            Street 123
                            <br />
                            123 45 City
                            <br />
                        </div>
                        <div class="mb-3 fw-light">
                            <i class="fal fa-phone me-2" aria-hidden="true"></i>
                            <a href="tel:+420 123 456 789" class="text-decoration-none text-white">
                                +420 123 456 789
                            </a>
                            <br />
                            <i class="fal fa-envelope me-2" aria-hidden="true"></i>
                            <a href="mailto:info@lumio.com" class="text-decoration-none text-white">
                                info@lumio.com
                            </a>
                            <br />
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </footer>


    <?php $this->flash_messages(); ?>

    </body>

</html>
<?php




