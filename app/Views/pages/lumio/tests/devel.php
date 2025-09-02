<?php





?>
<div class="my-5 py-5">
<div class="container my-5 py-5">
<?php




$form = \Lumio\View\Components\Form::build(new \Lumio\DTO\View\FormSetup(
    enctype: \Lumio\View\Components\Form::ENCTYPE_MULTIPART
));


$form->text(new \Lumio\DTO\View\FormInput(
    name: 'text',
    label: __tx('Text input'),
));

$form->submit();



?>
<div class="row">
    <div class="col-6">
        <?php

        vdump($_SESSION['__lcsrf_names']);

        ?>
    </div>
    <div class="col-6">
        <?php

        vdump($_SESSION['__lcsrf_tokens']);

        ?>
    </div>
</div>
<?php







?>
</div>
</div>
<?php














