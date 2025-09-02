<?php

use Lumio\View\Components\Img;




$class_sidenav_show = 'show';







?>
    <aside class="d-flex flex-column flex-shrink-0 py-3 text-bg-dark position-fixed h-100 main-sidebar <?=$class_sidenav_show?>" id="main_sidebar">

        <div class="d-flex justify-content-between justify-content-md-center align-items-center px-3 mx-md-3">
            <div>
                <a href="/" class="d-flex align-items-center text-white text-decoration-none">
                    <?php

                    Img::build('logo/logo-column-wm-light.png', [
                        'alt' => 'logo',
                        'style' => 'height: 80px',
                        'class' => 'w-auto p-y ps-2 pe-2 img-logo-private',
                    ])->render();

                    ?>
                </a>
            </div>
            <div class="d-md-none me-n3">
                <a href="javascript:;" class="btn btn-default text-white border-0 py-1 pe-3" onclick="toggle_main_sidebar();">
                    <i class="far fa-arrow-left"></i>
                </a>
            </div>
        </div>

        <hr class="mx-3 my-4" />

        <div class="overflow-auto">
            <?php

            // self::print_nav();


            $stack = \Lumio\Routing\History::all();

            if ($stack) foreach ($stack as $entry) {

                ?>
                <div class="px-3">
                    <a href="<?=$entry['uri']?>" class="nav-link"><?=$entry['label']?></a>
                </div>
                <?php
            }




            ?>
        </div>
    </aside>
<?php








