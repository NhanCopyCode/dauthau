
<?php $__env->startSection('htmlheader_title'); ?>
    <?php echo e(__('message.user.users')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheader_title'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheader_description'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('main-content'); ?>
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?php echo e(__('message.new_add')); ?></h3>
            <div class="box-tools">
                <a href="<?php echo e(url('/admin/users')); ?>" class="btn btn-default"><i class="fa fa-arrow-left" aria-hidden="true"></i>
                    <span class="hidden-xs"> <?php echo e(__('message.lists')); ?></span></a>
            </div>
        </div>

        <?php echo Form::open(['url' => '/admin/users', 'class' => 'form-horizontal', 'files' => true]); ?>


        <?php echo $__env->make('admin.users.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <?php echo Form::close(); ?>

    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\HueSoft\crawler_muathau\huesoft_dauthau\resources\views/admin/users/create.blade.php ENDPATH**/ ?>