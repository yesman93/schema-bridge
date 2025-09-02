<?php





?>
<div class="container py-5">
    <div class="text-center">
        <div class="mb-4 text-danger">
            <i class="far fa-ban fa-6x"></i>
        </div>
        <h1 class="display-4">CSRF validation failed</h1>
        <h1 class="display-4">[<?=$this->error_code?>]</h1>
        <p class="lead py-4 my-4"><?=$this->error_message?></p>
        <a href="javascript:history.back();" class="btn btn-primary mt-3">
            <i class="far fa-arrow-left me-3"></i>Go Back
        </a>
    </div>
</div>
<?php




