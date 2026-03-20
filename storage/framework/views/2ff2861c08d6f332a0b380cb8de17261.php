
<?php $__env->startSection('htmlheader_title'); ?>
    <?php echo e(trans('message.role.roles')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheader_title'); ?>
    <?php echo e(trans('message.role.roles')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheader_description'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <ol class="breadcrumb">
        <li><a href="<?php echo e(url('home')); ?>"><i class="fa fa-home"></i> <?php echo e(trans('message.dashboard')); ?></a></li>
        <li><a href="<?php echo e(url('/admin/roles')); ?>"><?php echo e(trans('message.role.roles')); ?></a></li>
        <li class="active"><?php echo e(trans('message.new_add')); ?></li>
    </ol>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('main-content'); ?>
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?php echo e(trans('message.new_add')); ?></h3>
            <div class="box-tools">
                <a href="<?php echo e(url('/admin/roles')); ?>" class="btn btn-default"><i class="fa fa-arrow-left"
                        aria-hidden="true"></i> <span class="hidden-xs"> <?php echo e(trans('message.lists')); ?></span></a>
            </div>
        </div>

        <?php echo Form::open(['url' => '/admin/roles', 'class' => 'form-horizontal']); ?>


        <?php echo $__env->make('admin.roles.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <?php echo Form::close(); ?>

    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\HueSoft\crawler_muathau\huesoft_dauthau\resources\views/admin/roles/create.blade.php ENDPATH**/ ?>