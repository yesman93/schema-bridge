<?php

use Lumio\View\Components\Img;


$show_sidenav = true;
$show_nav = true;



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

        <link rel="shortcut icon" href="/favicon.ico?v=<?=CACHE_VERSION?>" type="image/png" />
        <link rel="sitemap" type="application/xml" title="Sitemap" href="/sitemap.xml" />

        <?php $this->include_css(); ?>
        <?php $this->include_js(); ?>

    </head>
    <body class="bg-body-secondary">

    <?php if ($show_sidenav) { $this->partial('sidenav.inc'); } ?>

    <main class="">

        <?php if ($show_nav) { $this->partial('nav.inc'); } ?>

        <div class="container-fluid pt-3 px-3 pb-3 mt-0">
            <?php echo $this->content(); ?>
        </div>

    </main>


    <?php $this->flash_messages(); ?>


    </body>

</html>
<?php




