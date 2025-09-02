<?php



?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <title><?=$this->title()?></title>

    <meta name="description" content="<?=$this->description()?>" />
    <meta name="keywords" content="<?=$this->keywords()?>" />

    <link rel="shortcut icon" href="/favicon.ico?v=<?=CACHE_VERSION?>" type="image/png" />
    <link rel="sitemap" type="application/xml" title="Sitemap" href="/sitemap.xml" />

    <?php $this->include_css(); ?>
    <?php $this->include_js(); ?>

</head>


<body>


    <div class="modal-header no-print d-print-none <?=$this->_class_modal_header?> p-3 border-bottom">
        <h5 class="modal-title pe-3" id="general_modal_label" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="<?=$this->title(false)?>">
            <?=$this->title(false)?>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="window.parent.Modalio.close();" style="position:relative;z-index:10">
        </button>
    </div>

    <div class="modal-body p-3">
        <?php
        echo $this->content();
        ?>
    </div>


</body>
</html>
<?php




