<!DOCTYPE html>
<html lang="en">

<?php $__env->startSection('htmlheader'); ?>
    <?php echo $__env->make('admin.layouts.partials.htmlheader', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->yieldSection(); ?>

<?php echo $__env->yieldContent('style'); ?>

<body class="skin-blue-light sidebar-mini">
    <div id="app" v-cloak>
        <div class="wrapper">

            <?php echo $__env->make('admin.layouts.partials.mainheader', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <?php echo $__env->make('admin.layouts.partials.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <div class="content-wrapper">
                <section class="content">
                    <?php echo $__env->yieldContent('main-content'); ?>
                </section>
            </div>

            <?php echo $__env->make('admin.layouts.partials.controlsidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <?php echo $__env->make('admin.layouts.partials.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        </div>
    </div>

    <?php $__env->startSection('scripts'); ?>
        <?php echo $__env->make('admin.layouts.partials.scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->yieldSection(); ?>

    <?php echo $__env->yieldContent('scripts-footer'); ?>
    <?php if(session('success')): ?>
        <script>
            toastr.success("<?php echo e(session('success')); ?>");
        </script>
    <?php endif; ?>
</body>

</html>
<?php /**PATH D:\HueSoft\crawler_muathau\huesoft_dauthau\resources\views/admin/layouts/admin.blade.php ENDPATH**/ ?>