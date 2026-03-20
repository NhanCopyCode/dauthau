<head>
    <meta charset="UTF-8">
    <title> <?php echo e(strip_tags(Config("settings.app_logo"))); ?> - <?php echo $__env->yieldContent('htmlheader_title', 'Trang chủ'); ?> </title>
    <link rel="shortcut icon" href="<?php echo e(asset('img/favicon.ico')); ?>" />
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta name="theme-color" content="#ffffff">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link rel="manifest" href="<?php echo e(asset('manifest.json')); ?>">
    <link href="<?php echo e(url (mix ('/css/all.css') )); ?>" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('css/fontawesome-iconpicker.css')); ?>" >
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <?php echo $__env->yieldContent('css'); ?>
</head>
<?php /**PATH D:\HueSoft\crawler_muathau\huesoft_dauthau\resources\views/admin/layouts/partials/htmlheader.blade.php ENDPATH**/ ?>