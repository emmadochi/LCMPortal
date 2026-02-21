<?php
use App\Utilities\AssetHelper;

$title = $title ?? 'Church Portal';
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
    <div class="container-fluid py-4">
        <?php if (isset($content)): ?>
            <?= $content ?>
        <?php endif; ?>
    </div>

    <!-- JAVASCRIPT -->
    <script src="<?= AssetHelper::lib('jquery/jquery.min.js') ?>"></script>
    <script src="<?= AssetHelper::lib('bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= AssetHelper::lib('metismenu/metisMenu.min.js') ?>"></script>
    <script src="<?= AssetHelper::lib('simplebar/simplebar.min.js') ?>"></script>
    <script src="<?= AssetHelper::lib('node-waves/waves.min.js') ?>"></script>
    <script src="<?= AssetHelper::lib('feather-icons/feather.min.js') ?>"></script>
    <script src="<?= AssetHelper::lib('pace-js/pace.min.js') ?>"></script>
    <script src="<?= AssetHelper::js('app.js') ?>"></script>
</body>
</html>