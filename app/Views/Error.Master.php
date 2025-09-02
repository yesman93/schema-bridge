<?php

?>
<!DOCTYPE html>
<html lang="cs">

    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="x-ua-compatible" content="ie=edge">

        <title>Something went wrong!</title>

        <link rel="shortcut icon" href="/favicon.ico" type="image/png" />

        <link rel="stylesheet" href="/assets/css/ext/bootstrap-5.3.5/bootstrap.min.css" />
        <link rel="stylesheet" href="/assets/css/ext/fontawesome/css/all.css" />

    </head>
    <body class="bg-body-secondary">

        <main class="">

            <div class="container py-5">
                <div class="text-center">
                    <div class="pb-5 mb-5">
                        <img src="/assets/images/logo/logo-mini.png" alt="Lumio" class="img-fluid mb-4" style="max-width: 60px;">
                    </div>
                    <div class="mb-4 text-danger">
                        <i class="fas fa-exclamation-triangle fa-6x"></i>
                    </div>
                    <h1 class="display-4">Something went wrong!</h1>
                    <h1 class="display-4">[@{code}]</h1>
                    <p class="lead py-4 my-4">@{message}</p>
                    <a href="javascript:history.back();" class="btn btn-primary mt-3">
                        <i class="far fa-arrow-left me-3"></i>Go Back
                    </a>
                    <div class="d-flex justify-content-center mt-5">
                        <div class="text-start text-info">
                            @{trace}
                        </div>
                    </div>
                </div>
            </div>

        </main>

    </body>

</html>
<?php


