<?php
use App\Utilities\AssetHelper;

$title = $title ?? 'Login';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title><?= htmlspecialchars($title) ?> | Church Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Church Reporting & Administrative Portal" name="description" />
    
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
    <div class="auth-page">
        <div class="container-fluid p-0">
            <div class="row g-0">
                <div class="col-xxl-3 col-lg-4 col-md-5">
                    <div class="auth-full-page-content d-flex p-sm-5 p-4">
                        <div class="w-100">
                            <div class="d-flex flex-column h-100">
                                <div class="mb-4 mb-md-5 text-center">
                                    <a href="<?= AssetHelper::url('/') ?>" class="d-block auth-logo">
                                        <img src="<?= AssetHelper::image('logo-sm.svg') ?>" alt="" height="28"> 
                                        <span class="logo-txt">Church Portal</span>
                                    </a>
                                </div>
                                <div class="auth-content my-auto">
                                    <?php require_once __DIR__ . '/../components/alerts.php'; ?>
                                    <?php if (isset($content)): ?>
                                        <?= $content ?>
                                    <?php endif; ?>
                                </div>
                                <div class="mt-4 mt-md-5 text-center">
                                    <p class="mb-0">© <script>document.write(new Date().getFullYear())</script> Church Portal. 
                                    Crafted with <i class="mdi mdi-heart text-danger"></i> for Church Administration</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end col -->
                <div class="col-xxl-9 col-lg-8 col-md-7">
                    <div class="auth-bg pt-md-5 p-4 d-flex">
                        <div class="bg-overlay bg-primary"></div>
                        <ul class="bg-bubbles">
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                        </ul>
                        <!-- end bubble effect -->
                        <div class="row justify-content-center align-items-center">
                            <div class="col-xl-7">
                                <div class="p-0 p-sm-4 px-xl-0">
                                    <div class="text-center text-white">
                                        <h3 class="mb-3">Welcome to Church Portal</h3>
                                        <p class="font-size-16">Manage your church operations efficiently</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container fluid -->
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
    <!-- password addon init -->
    <script src="<?= AssetHelper::js('pages/pass-addon.init.js') ?>"></script>
</body>
</html>

