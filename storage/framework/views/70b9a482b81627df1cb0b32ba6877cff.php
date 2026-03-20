<div class="box-body">
    <table class="table table-striped table-bordered">
        <tr>
            <td><?php echo e(__('message.user.name')); ?> <span class="label-required"></span></td>
            <td>
                <div class="<?php echo e($errors->has('name') ? ' has-error' : ''); ?>">
                    <?php echo Form::text('name', null, ['class' => 'form-control input-sm', 'required' => 'required']); ?>

                    <?php echo $errors->first('name', '<p class="help-block">:message</p>'); ?>

                </div>
            </td>
            <td><?php echo e(__('message.user.avatar')); ?></td>
            <td>
                <div class="<?php echo e($errors->has('profile.avatar') ? ' has-error' : ''); ?>">
                    <?php echo Form::file('profile[avatar]', null); ?>

                    <?php
                    if(isset($submitButtonText))
                    echo $user->showAvatar();
                    ?>
                    <?php echo $errors->first('profile.avatar', '<p class="help-block">:message</p>'); ?>

                </div>
            </td>
        </tr>
        <tr>
            <td><?php echo e(__('message.user.email')); ?> <span class="label-required"></span></td>
            <td>
                <div class="<?php echo e($errors->has('email') ? ' has-error' : ''); ?>">
                    <?php echo Form::email('email', null, ['class' => 'form-control input-sm', 'required' => 'required']); ?>

                    <?php echo $errors->first('email', '<p class="help-block">:message</p>'); ?>

                </div>
            </td>
            <td><?php echo e(__('message.user.gender')); ?></td>
            <td>
                <div class="<?php echo e($errors->has('profile.gender') ? ' has-error' : ''); ?>">
                    <label for="boy">
                        <?php echo Form::radio('profile[gender]', 1, isset($submitButtonText)?null:true, ['class' => 'flat-blue', 'id' => 'boy']); ?>

                        <?php echo e(__('message.user.gender_male')); ?>

                    </label>&nbsp;
                    <label for="girl">
                        <?php echo Form::radio('profile[gender]', 0, null, ['class' => 'flat-blue', 'id' => 'girl']); ?>

                        <?php echo e(__('message.user.gender_female')); ?>

                    </label>
                    <?php echo $errors->first('profile.gender', '<p class="help-block">:message</p>'); ?>

                </div>
            </td>
        </tr>
        <tr>
            <td><?php echo e(__('message.user.username')); ?> <span class="label-required"></span></td>
            <td>
                <?php
                $readonly = [];
                if(isset($isProfile) && $isProfile){
                $readonly = ['readonly' => 'readonly'];
                }
                ?>
                <div class="<?php echo e($errors->has('username') ? ' has-error' : ''); ?>">
                    <?php echo Form::text('username', null, array_merge(['class' => 'form-control input-sm', 'required' => 'required'], $readonly)); ?>

                    <?php echo $errors->first('username', '<p class="help-block">:message</p>'); ?>

                </div>
            </td>
            <td><?php echo e(__('message.user.phone')); ?></td>
            <td>
                <div class="<?php echo e($errors->has('profile.phone') ? ' has-error' : ''); ?>">
                    <?php echo Form::text('profile[phone]', null, ['class' => 'form-control input-sm']); ?>

                    <?php echo $errors->first('profile.phone', '<p class="help-block">:message</p>'); ?>

                </div>
            </td>
        </tr>
        <tr>
            <?php
            $required = [];
            if(!isset($submitButtonText)){
            $required = ['required' => 'required'];
            }
            ?>
            <?php if(isset($isProfile) && $isProfile): ?>
            <td><?php echo e(__('message.user.password_current')); ?></td>
            <td>
                <div class="<?php echo e($errors->has('password_current') ? ' has-error' : ''); ?>">
                    <?php echo Form::password('password_current', ['class' => 'form-control input-sm']); ?>

                    <?php echo $errors->first('password_current', '<p class="help-block">:message</p>'); ?>

                </div>
            </td>
            <?php else: ?>
            <td><?php echo e(isset($isProfile) && $isProfile?__('message.user.password_new'):__('message.user.password')); ?> <span class="<?php echo e($required?'label-required':''); ?>"></span></td>
            <td>
                <div class="<?php echo e($errors->has('password') ? ' has-error' : ''); ?>">
                    <?php echo Form::password('password', array_merge(['class' => 'form-control input-sm'], $required)); ?>

                    <?php echo $errors->first('password', '<p class="help-block">:message</p>'); ?>

                </div>
            </td>
            <?php endif; ?>
            <td><?php echo e(__('message.user.address')); ?></td>
            <td>
                <div class="<?php echo e($errors->has('profile.address') ? ' has-error' : ''); ?>">
                    <?php echo Form::text('profile[address]', null, ['class' => 'form-control input-sm']); ?>

                    <?php echo $errors->first('profile.address', '<p class="help-block">:message</p>'); ?>

                </div>
            </td>
        </tr>
        <tr>
            <td><?php echo e(isset($isProfile) && $isProfile?__('message.user.password_new_confirmation'):__('message.user.password_confirmation')); ?> <span class="<?php echo e($required?'label-required':''); ?>"></span></td>
            <td>
                <div class="<?php echo e($errors->has('password_confirmation') ? ' has-error' : ''); ?>">
                    <?php echo Form::password('password_confirmation', array_merge(['class' => 'form-control input-sm'], $required)); ?>

                    <?php echo $errors->first('password_confirmation', '<p class="help-block">:message</p>'); ?>

                </div>
            </td>
            <td><?php echo e(__('message.user.birthday')); ?></td>
            <td>
                <div class="<?php echo e($errors->has('profile.birthday') ? ' has-error' : ''); ?>">
                    <?php echo Form::text('profile[birthday]', null, ['class' => 'form-control input-sm datepicker']); ?>

                    <?php echo $errors->first('profile.birthday', '<p class="help-block">:message</p>'); ?>

                </div>
            </td>
        </tr>
        <tr>
            <?php if(!isset($isProfile) || !$isProfile): ?>
            <td><?php echo e(__('message.user.role')); ?> </td>
            <td>
                <div class="<?php echo e($errors->has('roles') ? 'has-error' : ''); ?>">
                    <?php echo Form::select('roles[]', $roles, isset($user_roles) ? $user_roles : [], ['class' => 'form-control input-sm select2', 'required'=>'required']); ?>

                </div>
            </td>
            <?php endif; ?>
            <td><?php echo e(__('message.user.position')); ?></td>
            <td>
                <div class="<?php echo e($errors->has('profile.position') ? ' has-error' : ''); ?>">
                    <?php echo Form::text('profile[position]', null, ['class' => 'form-control input-sm']); ?>

                    <?php echo $errors->first('profile.position', '<p class="help-block">:message</p>'); ?>

                </div>
            </td>
        </tr>
        <?php if(!isset($isProfile) || !$isProfile): ?>
        <tr>
            <td style="border-right-width: 0"></td>
            <td colspan="3" style="border-left-width: 0">
                <div class="<?php echo e($errors->has('active') ? ' has-error' : ''); ?>">
                    <label>
                        <?php echo Form::checkbox('active', Config("settings.active") , isset($submitButtonText)?null:true ,['class' => 'flat-blue']); ?>

                        <?php echo e(__('message.user.active')); ?>

                    </label>
                    <?php echo $errors->first('active', '<p class="help-block">:message</p>'); ?>

                </div>
            </td>
        </tr>
        <?php endif; ?>
    </table>
</div>
<div class="box-footer">
    <?php echo Form::button('<i class="fa fa-check-circle"></i> ' . $text = isset($submitButtonText) ? $submitButtonText : __('message.save'), ['class' => 'btn btn-info mr-2', 'type'=>'submit']); ?>

    <?php if(!isset($isProfile) || !$isProfile): ?>
    <a href="<?php echo e(url('/admin/users')); ?>" class="btn btn-default"><i class="fas fa-times"></i> <?php echo e(__('message.close')); ?></a>
    <?php endif; ?>
</div>
<?php $__env->startSection('scripts-footer'); ?>
<script>
    $(function() {
        $('.datepicker').datepicker({
            autoclose: true,
            language: '<?php echo e(app()->getLocale()); ?>',
            format: 'dd/mm/yyyy',
            todayHighlight: true,
        });
    })
</script>
<?php $__env->stopSection(); ?><?php /**PATH D:\HueSoft\crawler_muathau\huesoft_dauthau\resources\views/admin/users/form.blade.php ENDPATH**/ ?>