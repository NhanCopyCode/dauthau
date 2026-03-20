<div class="box-body">
    <div class="form-group<?php echo e($errors->has('name') ? ' has-error' : ''); ?>">
        <?php echo Form::label('name', trans('message.role.name'), ['class' => 'col-md-4 control-label']); ?>

        <div class="col-md-6">
            <?php echo Form::text('name', null, ['class' => 'form-control', 'required' => 'required']); ?>

            <?php echo $errors->first('name', '<p class="help-block">:message</p>'); ?>

        </div>
    </div>
    <div class="form-group<?php echo e($errors->has('label') ? ' has-error' : ''); ?>">
        <?php echo Form::label('label', trans('message.role.label'), ['class' => 'col-md-4 control-label']); ?>

        <div class="col-md-6">
            <?php echo Form::text('label', null, ['class' => 'form-control']); ?>

            <?php echo $errors->first('label', '<p class="help-block">:message</p>'); ?>

        </div>
    </div>
    <?php ($rolePermission = isset($role)?$role->Permissions->pluck('name')->toArray():[]); ?>
    <div class="form-group<?php echo e($errors->has('label') ? ' has-error' : ''); ?>">
        <div class="col-xs-12">
            <h5 class="alert alert-info alert-dismissible"><?php echo e(trans('message.role.permissions')); ?></h5>
        </div>
        <div class="clearfix"></div>
        
        <div class="col-xs-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col" class="text-center"><?php echo e(trans('message.index')); ?></th>
                        <th scope="col" class="text-center"><?php echo e(trans('message.role.function')); ?></th>
                        <th scope="col" class="text-center"><?php echo e(trans('message.view')); ?></th>
                        <th scope="col" class="text-center"><?php echo e(trans('message.add')); ?></th>
                        <th scope="col" class="text-center"><?php echo e(trans('message.edit')); ?></th>
                        <th scope="col" class="text-center"><?php echo e(trans('message.delete')); ?></th>
                        <th scope="col" class="text-center"><?php echo e(trans('message.role.approved')); ?></th>
                        <th class="text-center">
                            <input type="checkbox" name="chkAll" id="chkAll" />
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php ($counter=0); ?>
                    <?php ($strModule=""); ?>
                    <?php ($arrPers=""); ?>
                    <?php ($index=""); ?>
                    <?php ($create=""); ?>
                    <?php ($update=""); ?>
                    <?php ($destroy=""); ?>
                    <?php ($active=""); ?>
                    <?php ($rchk=""); ?>

                    <?php ($test = $permissions->pluck('name')); ?>
                    <?php for($i = 0; $i < count($permissions); $i++): ?> <?php ($permission=$permissions[$i]); ?> <?php ($arrPers=explode("@", $permission ->name)); ?>

                        <?php if(!empty($arrPers) && count($arrPers) > 1): ?>
                        
                        <?php if(strcmp($strModule, $arrPers[0])!=0): ?>
                        <?php ($counter++); ?>
                        <?php ($strModule = $arrPers[0]); ?>
                        <?php if(in_array($strModule."@index", $rolePermission)||(strcmp($strModule, "ReportsController")==0 && in_array("ReportsController@numberBookingByDate", $rolePermission))): ?>
                        <?php ($index = 'checked'); ?>
                        <?php endif; ?>
                        <?php if(in_array($strModule."@store", $rolePermission)): ?>
                        <?php ($create = 'checked'); ?>
                        <?php endif; ?>
                        <?php if(in_array($strModule."@update", $rolePermission)): ?>
                        <?php ($update = 'checked'); ?>
                        <?php endif; ?>
                        <?php if(in_array($strModule."@destroy", $rolePermission)): ?>
                        <?php ($destroy = 'checked'); ?>
                        <?php endif; ?>
                        <?php if(in_array($strModule."@active", $rolePermission)): ?>
                        <?php ($active = 'checked'); ?>
                        <?php endif; ?>
                        <?php if(strcmp($index, "checked") == 0 && strcmp($create, "checked") == 0 &&
                        strcmp($update, "checked") ==0 && strcmp($destroy, "checked") == 0 &&
                        strcmp($active, "checked") ==0): ?>
                        <?php ($rchk = 'checked'); ?>
                        <?php else: ?>
                        <?php ($rchk = ""); ?>
                        <?php endif; ?>
                        <tr>
                            <td class="text-center"><span><?php echo e($counter); ?></span></td>
                            <td><span><?php echo e($permission->label); ?></span></td>
                            <td class="text-center">
                                <div class="custom-control custom-checkbox">
                                    <?php if($test->contains($strModule."@index") || $test->contains($strModule."@numberBookingByDate")): ?>
                                    <input type="checkbox" class="custom-control" name="permissions[]" id=<?php echo e($strModule); ?> <?php echo e($index); ?> value=<?php echo e($strModule."@index"); ?>>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="custom-control custom-checkbox">
                                    <?php if($test->contains($strModule."@store")): ?>
                                    <input type="checkbox" class="custom-control" name="permissions[]" id=<?php echo e($strModule); ?> <?php echo e($create); ?> value=<?php echo e($strModule."@store"); ?>>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="custom-control custom-checkbox">
                                    <?php if($test->contains($strModule."@update")): ?>
                                    <input type="checkbox" class="custom-control" name="permissions[]" id=<?php echo e($strModule); ?> <?php echo e($update); ?> value=<?php echo e($strModule."@update"); ?>>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="custom-control custom-checkbox">
                                    <?php if($test->contains($strModule."@destroy")): ?>
                                    <input type="checkbox" class="custom-control" name="permissions[]" id=<?php echo e($strModule); ?> <?php echo e($destroy); ?> value=<?php echo e($strModule."@destroy"); ?>>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="custom-control custom-checkbox">
                                    <?php if($test->contains($strModule."@active")): ?>
                                    <input type="checkbox" class="custom-control" name="permissions[]" id=<?php echo e($strModule); ?> <?php echo e($active); ?> value=<?php echo e($strModule."@active"); ?>>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="chkRole" id="chkRole" <?php echo e($rchk); ?> onclick="ActiveModule($(this), '<?php echo e($strModule); ?>');" />
                                </div>
                            </td>
                        </tr>
                        <?php ($index=""); ?>
                        <?php ($create=""); ?>
                        <?php ($update=""); ?>
                        <?php ($destroy=""); ?>
                        <?php ($active=""); ?>
                        <?php endif; ?>

                        <?php elseif(strcmp($permission->name, "SettingController")==0): ?>
                        <?php ($counter++); ?>
                        <?php if(in_array($permission->name, $rolePermission)): ?>
                        <?php ($index = 'checked'); ?>
                        <?php endif; ?>
                        <tr>
                            <td class="text-center"><span><?php echo e($counter); ?></span></td>
                            <td><span><?php echo e($permission->label); ?></span></td>
                            <td class="text-center">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control" name="permissions[]" id=<?php echo e($permission->name); ?> <?php echo e($index); ?> value=<?php echo e($permission->name); ?>>
                                </div>
                            </td>
                            <td class="text-center">
                            </td>
                            <td class="text-center">
                            </td>
                            <td class="text-center">
                            </td>
                            <td class="text-center">
                            </td>
                            <td class="text-center">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="chkRole" id="chkRole" <?php echo e($index); ?> onclick="ActiveModule($(this), '<?php echo e($permission->name); ?>');" />
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php endfor; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="box-footer">
    <?php echo Form::button('<i class="fa fa-check-circle"></i> ' . $text = isset($submitButtonText) ? $submitButtonText : __('message.new_add'), ['class' => 'btn btn-info mr-2', 'type'=>'submit']); ?>

</div>
<?php $__env->startSection('scripts-footer'); ?>
<script type="text/javascript">
    function ActiveModule(parent, chkName) {
        $("input[id=" + chkName + "]").prop('checked', parent.prop("checked"));
    }
    $(function() {
        $('#chkAll').on('click', function(e) {
            $("input:checkbox").prop('checked', $(this).prop("checked"));
        });
        $('form').submit(function(e) {
            var $lst = $("input[name='" + "permissions[]" + "']:not(:checked)");
            $lst.prop('name', "p1");
            return true;
        });
    })
</script>
<?php $__env->stopSection(); ?><?php /**PATH D:\HueSoft\crawler_muathau\huesoft_dauthau\resources\views/admin/roles/form.blade.php ENDPATH**/ ?>