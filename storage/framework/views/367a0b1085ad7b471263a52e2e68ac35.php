
<?php $__env->startSection('htmlheader_title'); ?>
    <?php echo e(trans('message.role.roles')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('main-content'); ?>
    <div class="box">
        <div class="content-header pb-5">
            <h5 class="float-left">
                <?php echo e(trans('message.role.roles')); ?>

            </h5>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('RolesController@store')): ?>
                <a href="<?php echo e(url('/admin/roles/create')); ?>" class="btn btn-default float-right" title="<?php echo e(__('message.new_add')); ?>">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i> <span class="hidden-xs">
                        <?php echo e(__('message.new_add')); ?></span>
                </a>
            <?php endif; ?>
        </div>
        <div class="box-body no-padding">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th class="text-center"><?php echo e(trans('message.role.id')); ?></th>
                        <th><?php echo e(trans('message.role.name')); ?></th>
                        <th><?php echo e(trans('message.role.label')); ?></th>
                        <th></th>
                    </tr>
                    <?php ($stt = ($roles->currentPage() - 1) * $roles->perPage()); ?>
                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="text-center"><?php echo e(++$stt); ?></td>
                            <td><a href="<?php echo e(url('/admin/roles', $item->id)); ?>"><?php echo e($item->name); ?></a></td>
                            <td><?php echo e($item->label); ?></td>
                            <td class="dropdown text-center">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="fal fa-tools"></i>
                                </button>
                                <div class="dropdown-menu p-0">
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('RolesController@show')): ?>
                                        <a href="<?php echo e(url('/admin/roles/' . $item->id)); ?>"
                                            title="<?php echo e(__('message.role.view_role')); ?>"><button
                                                class="btn btn-info dropdown-item"><i class="fas fa-eye"></i>
                                                <?php echo e(__('message.role.view_role')); ?></button></a>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('RolesController@update')): ?>
                                        <a href="<?php echo e(url('/admin/roles/' . $item->id . '/edit')); ?>"
                                            title="<?php echo e(__('message.role.edit_role')); ?>"><button
                                                class="btn btn-primary dropdown-item"><i class="far fa-edit"
                                                    aria-hidden="true"></i> <?php echo e(__('message.role.edit_role')); ?></button></a>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('RolesController@destroy')): ?>
                                        <?php echo Form::open([
                                            'method' => 'DELETE',
                                            'url' => ['/admin/roles', $item->id],
                                            'style' => 'display:inline',
                                        ]); ?>

                                        <?php echo Form::button('<i class="far fa-trash-alt" aria-hidden="true"></i> ' . __('message.role.delete_role'), [
                                            'type' => 'submit',
                                            'class' => 'btn btn-danger dropdown-item',
                                            'title' => __('message.role.delete_role'),
                                            'onclick' => 'return confirm("' . __('message.confirm_delete') . '")',
                                        ]); ?>

                                        <?php echo Form::close(); ?>

                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <div class="box-footer clearfix">
                <?php echo $roles->appends(['search' => Request::get('search')])->render(); ?>

            </div>
        </div>
    </div>
    <?php echo $__env->make('sweetalert::alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\HueSoft\crawler_muathau\huesoft_dauthau\resources\views/admin/roles/index.blade.php ENDPATH**/ ?>