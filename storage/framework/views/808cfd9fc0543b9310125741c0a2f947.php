
<?php $__env->startSection('htmlheader_title'); ?>
    <?php echo e(__('message.user.users')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheader_title'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheader_description'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('main-content'); ?>
    <div class="box">
        <div class="content-header border-bottom pb-5">
            <h5 class="float-left">
                <?php echo e(__('message.user.users')); ?>

            </h5>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('UsersController@store')): ?>
                <a href="<?php echo e(url('/admin/users/create')); ?>" class="btn btn-default float-right" title="<?php echo e(__('message.new_add')); ?>">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i> <span class="hidden-xs">
                        <?php echo e(__('message.new_add')); ?></span>
                </a>
            <?php endif; ?>
        </div>
        <div class="box-header">
            <div class="box-tools">
                <?php echo Form::open(['method' => 'GET', 'url' => '/admin/users', 'class' => 'pull-left', 'role' => 'search']); ?>

                <div class="input-group" style="margin-right: 5px; display:flex;">
                    <div class="select-group" style="margin-right: 5px; width:206px;">
                        <?php echo Form::select('role_id', $roles ?? [], \Request::get('role_id'), [
                            'class' => 'form-control input-sm select2 ',
                            'id' => 'role_id',
                        ]); ?>

                    </div>
                    <input type="text" value="<?php echo e(\Request::get('search')); ?>" class="form-control input-sm" name="search"
                        placeholder="<?php echo e(__('message.search_keyword')); ?>" autocomplete="off" style="margin-right: 5px;">
                    <button class="btn btn-default btn-sm" type="submit">
                        <i class="fa fa-search"></i> <?php echo e(__('message.search')); ?>

                    </button>
                </div>
                <?php echo Form::close(); ?>

            </div>
        </div>
        <?php ($stt = ($users->currentPage() - 1) * $users->perPage()); ?>
        <div class="box-body no-padding">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th class="text-center"><?php echo e(trans('message.index')); ?></th>
                        <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('name', __('message.user.name')));?></th>
                        <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('username', __('message.user.username')));?></th>
                        <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('email', __('message.user.email')));?></th>
                        <th><?php echo e(__('message.user.role')); ?></th>
                        <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('active', __('message.user.active')));?></th>
                        <th></th>
                    </tr>
                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="text-center"><?php echo e(++$stt); ?></td>
                            <td><a href="<?php echo e(url('/admin/users', $item->id)); ?>"><?php echo e($item->name); ?></a></td>
                            <td><a href="<?php echo e(url('/admin/users', $item->id)); ?>"><?php echo e($item->username); ?></a></td>
                            <td><?php echo e($item->email); ?></td>
                            <td>
                                <?php $__currentLoopData = $item->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="badge label-<?php echo e($role->color); ?>"><?php echo e($role->label); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </td>
                            <td><?php echo e($item->active == Config('settings.active') ? __('message.yes') : __('message.no')); ?>

                            </td>
                            <td class="dropdown text-center">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="fal fa-tools"></i>
                                </button>
                                <div class="dropdown-menu p-0">
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('UsersController@show')): ?>
                                        <a href="<?php echo e(url('/admin/users/' . $item->id)); ?>"
                                            title="<?php echo e(__('message.user.view_user')); ?>"><button
                                                class="btn btn-info dropdown-item"><i class="fas fa-eye"></i>
                                                <?php echo e(__('message.user.view_user')); ?></button></a>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('UsersController@update')): ?>
                                        <a href="<?php echo e(url('/admin/users/' . $item->id . '/edit')); ?>"
                                            title="<?php echo e(__('message.user.edit_user')); ?>"><button
                                                class="btn btn-primary dropdown-item"><i class="far fa-edit"
                                                    aria-hidden="true"></i> <?php echo e(__('message.user.edit_user')); ?></button></a>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('UsersController@destroy')): ?>
                                        <?php echo Form::open([
                                            'method' => 'DELETE',
                                            'url' => ['/admin/users', $item->id],
                                            'style' => 'display:inline',
                                        ]); ?>

                                        <?php echo Form::button('<i class="far fa-trash-alt" aria-hidden="true"></i> ' . __('message.user.delete_user'), [
                                            'type' => 'submit',
                                            'class' => 'btn btn-danger dropdown-item',
                                            'title' => __('message.user.delete_user'),
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
                <?php echo $users->appends(\Request::except('page'))->render(); ?>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\HueSoft\crawler_muathau\huesoft_dauthau\resources\views/admin/users/index.blade.php ENDPATH**/ ?>