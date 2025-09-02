<?php





?>
<div class="container py-5">
    <div class="text-center">
        <div class="pb-5 mb-5">
            <img src="/assets/images/logo/logo-mini.png" alt="Lumio" class="img-fluid mb-4" style="max-width: 60px;">
        </div>
        <div class="mb-4 text-danger">
            <i class="fas fa-layer-group fa-6x"></i>
        </div>
        <h1 class="display-4">View error</h1>
        <p class="lead py-4 my-4"><?=$this->error_message?></p>
        <a href="javascript:history.back();" class="btn btn-primary mt-3">
            <i class="far fa-arrow-left me-3"></i>Go Back
        </a>
    </div>
</div>
<?php




