<?php
use App\Utilities\AssetHelper;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>404 Error | Church Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?= AssetHelper::image('favicon.ico') ?>">

    <!-- preloader css -->
    <link rel="stylesheet" href="<?= AssetHelper::css('preloader.min.css') ?>" type="text/css" />

    <!-- Bootstrap Css -->
    <link href="<?= AssetHelper::css('bootstrap.min.css') ?>" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="<?= AssetHelper::css('icons.min.css') ?>" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="<?= AssetHelper::css('app.min.css') ?>" id="app-style" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="my-5 pt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-center mb-5">
                        <h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1>
                        <h4 class="text-uppercase">Sorry, page not found</h4>
                        <div class="mt-5 text-center">
                            <a class="btn btn-primary waves-effect waves-light" href="<?= AssetHelper::url('/') ?>">Back to Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-10 col-xl-8">
                    <div>
                        <img src="<?= AssetHelper::image('error-img.png') ?>" alt="" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script src="<?= AssetHelper::lib('jquery/jquery.min.js') ?>"></script>
    <script src="<?= AssetHelper::lib('bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= AssetHelper::lib('metismenu/metisMenu.min.js') ?>"></script>
    <script src="<?= AssetHelper::lib('simplebar/simplebar.min.js') ?>"></script>
    <script src="<?= AssetHelper::lib('node-waves/waves.min.js') ?>"></script>
    <script src="<?= AssetHelper::lib('feather-icons/feather.min.js') ?>"></script>
    <!-- pace js -->
    <script src="<?= AssetHelper::lib('pace-js/pace.min.js') ?>"></script>
</body>
</html>
