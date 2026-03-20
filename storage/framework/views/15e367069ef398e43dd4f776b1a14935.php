
<?php $__env->startSection('htmlheader_title'); ?>
    <?php echo e(trans('settings.setting_management')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('main-content'); ?>
    <div id="alert"></div>
    <div class="box">
        <div class="content-header pb-5">
            <h5 class="float-left">
                <?php echo e(trans('settings.setting_management')); ?>

            </h5>
        </div>
        <ul class="nav nav-tabs">
            <?php for($i = 0; $i < count($tabs); $i++): ?>
                <li>
                    <a class="nav-link <?php echo e($i == 0 ? 'active' : ''); ?>" href="#<?php echo e($tabs[$i]); ?>"
                        aria-controls="<?php echo e($tabs[$i]); ?>" role="tab" data-toggle="tab">
                        <?php echo e(trans('settings.' . $tabs[$i])); ?>

                    </a>
                </li>
            <?php endfor; ?>
        </ul>
        <?php echo Form::open([
            'method' => 'PATCH',
            'url' => ['admin/settings'],
            'class' => 'form-horizontal',
            'files' => true,
            'id' => 'settings',
        ]); ?>

        <div class="tab-content">
            <?php for($i = 0; $i < count($tabs); $i++): ?>
                <div role="tabpanel" class="tab-pane <?php echo e($i == 0 ? 'active' : ''); ?>" id="<?php echo e($tabs[$i]); ?>">
                    <div class="box-body">
                        <?php $__currentLoopData = $data[$tabs[$i]]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="form-group <?php echo e($errors->has('value') ? 'has-error' : ''); ?>">
                                <?php echo Form::label('description', $item['description'], ['class' => 'col-md-3 control-label']); ?>

                                <div class="col-md-6">
                                    <?php if($item['type'] == 'image'): ?>
                                        <?php if($item['value']): ?>
                                            <img src=<?php echo e(asset($item['value'])); ?> alt="logo" width="80">
                                        <?php endif; ?>
                                        <input name=<?php echo e($item['key']); ?> type="file" accept="image/*">
                                    <?php elseif($item['type'] == 'number'): ?>
                                        <?php echo Form::number($item['key'], $item['value'], ['class' => 'form-control input-sm', 'id' => $item['key']]); ?>

                                    <?php elseif($item['type'] == 'checkbox'): ?>
                                        <?php echo Form::checkbox($item['key'], config('settings.active'), $item['value'], [
                                            'class' => 'flat-blue',
                                            'id' => $item['key'],
                                        ]); ?>

                                    <?php elseif($item['type'] == 'textarea'): ?>
                                        <textarea name=<?php echo e($item['key']); ?> rows="5" class="form-control"><?php echo e($item['value']); ?></textarea>
                                    <?php elseif($item['type'] == 'select'): ?>
                                        <select name="<?php echo e($item['key']); ?>" class="form-control input-sm">
                                            <?php $__currentLoopData = config('theme.option_code'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($key); ?>"
                                                    <?php echo e($item['value'] === $key ? 'selected' : ''); ?>><?php echo e($val); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    <?php else: ?>
                                        <?php echo Form::text($item['key'], $item['value'], ['class' => 'form-control input-sm', 'id' => $item['key']]); ?>

                                    <?php endif; ?>
                                    <?php echo $errors->first($item['key'], '<p class="help-block">:message</p>'); ?>

                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endfor; ?>
            <div class="box-footer">
                <?php echo Form::button('<i class="fa fa-check-circle"></i> ' . __('message.update'), [
                    'class' => 'btn btn-info',
                    'type' => 'submit',
                ]); ?>

            </div>
        </div>
        <?php echo Form::close(); ?>

    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts-footer'); ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const fileInputs = document.querySelectorAll('input[type="file"][accept="image/*"]');

            fileInputs.forEach(function(input) {

                input.addEventListener('change', function(e) {
                    const file = e.target.files[0];

                    if (!file) return;

                    if (!file.type.startsWith('image/')) {
                        alert('Vui lòng chọn file ảnh');
                        return;
                    }

                    const reader = new FileReader();

                    reader.onload = function(event) {
                        let img = input.parentElement.querySelector('img');

                        if (!img) {
                            img = document.createElement('img');
                            img.style.width = '80px';
                            img.style.marginTop = '10px';
                            input.parentElement.prepend(img);
                        }

                        img.src = event.target.result;
                    };

                    reader.readAsDataURL(file);
                });

            });

        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\HueSoft\crawler_muathau\huesoft_dauthau\resources\views/settings/index.blade.php ENDPATH**/ ?>