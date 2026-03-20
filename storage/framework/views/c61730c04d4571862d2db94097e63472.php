<!-- Main Header -->
<header class="main-header">

    <!-- Logo -->
    <a href="<?php echo e(url('/admin')); ?>" class="logo" style="background:white !important">
        <img class="dashboard-image logo-lg"
            src="<?php echo e(\DB::table('settings')->where('key', 'company_logo')->value('value') != null
                ? url(\DB::table('settings')->where('key', 'company_logo')->value('value'))
                : asset('images/logoFB.png')); ?>"
            style="width:170px;">
    </a>

    <!-- Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">

        <!-- Toggle -->
        <a class="p-2" href="#" data-toggle="push-menu" role="button">
            <i class="fas fa-bars"></i>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">

                <?php if(auth()->guard()->guest()): ?>
                    <li><a href="<?php echo e(url('/register')); ?>">Đăng ký</a></li>
                    <li><a href="<?php echo e(url('/login')); ?>">Đăng nhập</a></li>
                <?php else: ?>
                    
                    <?php ($languages = \App\Models\Language::getLanguages()); ?>
                    <?php if($languages->count() > 1): ?>
                        <li class="dropdown mr-3">
                            <a href="#" class="dropdown-toggle language" data-toggle="dropdown">
                                <span class="text-uppercase"><?php echo e(app()->getLocale()); ?></span>
                                <i class="fal fa-globe-africa"></i>
                            </a>

                            <ul class="dropdown-menu menu" style="width: 150px;">
                                <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="my-1">
                                        <a style="padding: 10px;"
                                            onclick="document.getElementById('locale_client').value='<?php echo e($item->prefix); ?>'; document.getElementById('frmLag').submit(); return false;"
                                            href="#">
                                            <img src="<?php echo e(asset('img/' . $item->prefix . '.png')); ?>">
                                            &nbsp; <?php echo e($item->name); ?>

                                        </a>
                                        <hr>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>

                            <form method="POST" action="<?php echo e(url('admin/change_locale')); ?>" id="frmLag">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" id="locale_client" name="locale_client">
                            </form>
                        </li>
                    <?php endif; ?>

                    
                    <li class="dropdown user user-menu" id="user_menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">

                            <?php echo Auth::user()->showAvatar(['class' => 'user-image'], asset(config('settings.avatar_default'))); ?>


                            <span class="hidden-xs"><?php echo e(Auth::user()->name); ?></span>
                        </a>

                        <ul class="dropdown-menu mt-2">

                            <li class="mb-2">
                                <a href="<?php echo e(url('admin/profile')); ?>">
                                    <i class="fa fa-user"></i> Hồ sơ
                                </a>
                            </li>

                            <li class="mt-2">
                                <a href="<?php echo e(url('/logout')); ?>"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fa fa-sign-out"></i> Đăng xuất
                                </a>

                                <form id="logout-form" action="<?php echo e(url('/logout')); ?>" method="POST" style="display:none;">
                                    <?php echo csrf_field(); ?>
                                </form>
                            </li>

                        </ul>
                    </li>

                <?php endif; ?>

            </ul>
        </div>
    </nav>
</header>
<?php /**PATH D:\HueSoft\crawler_muathau\huesoft_dauthau\resources\views/admin/layouts/partials/mainheader.blade.php ENDPATH**/ ?>