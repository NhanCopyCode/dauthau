<ul class="sidebar-menu" data-widget="tree">

    <?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

        
        <?php if(!isset($item['child'])): ?>

            <?php if(!isset($item['permission']) || auth()->user()->can($item['permission'])): ?>
                <li class="<?php echo e(request()->is($item['href'].'*') ? 'active' : ''); ?>">
                    <a href="<?php echo e(url($item['href'])); ?>">
                        <i style="margin-right: 8px; display: inline-block;" class="<?php echo e($item['icon']); ?>"></i>
                        <span><?php echo e($item['title']); ?></span>
                    </a>
                </li>
            <?php endif; ?>

        <?php else: ?>

            <?php
                $hasVisibleChild = collect($item['child'])->contains(function ($child) {
                    return !isset($child['permission']) || auth()->user()->can($child['permission']);
                });
            ?>

            <?php if($hasVisibleChild): ?>

                <li class="treeview 
                    <?php echo e(collect($item['child'])->pluck('href')->contains(fn($url) => request()->is($url.'*')) ? 'active' : ''); ?>">

                    <a href="#">
                        <i style="margin-right: 8px; display: inline-block;" class="<?php echo e($item['icon']); ?>"></i>
                        <span><?php echo e($item['title']); ?></span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-right  pull-right"></i>
                        </span>
                    </a>

                    <ul class="treeview-menu">
                        <?php $__currentLoopData = $item['child']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                            <?php if(!isset($child['permission']) || auth()->user()->can($child['permission'])): ?>
                                <li class="<?php echo e(request()->is($child['href'].'*') ? 'active' : ''); ?>">
                                    <a href="<?php echo e(url($child['href'])); ?>">
                                        <i style="margin-right: 8px; display: inline-block;" class="<?php echo e($child['icon']); ?>"></i>
                                        <?php echo e($child['title']); ?>

                                    </a>
                                </li>
                            <?php endif; ?>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </li>

            <?php endif; ?>

        <?php endif; ?>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</ul><?php /**PATH D:\HueSoft\crawler_muathau\huesoft_dauthau\resources\views/admin/layouts/partials/sidebar-menu.blade.php ENDPATH**/ ?>