<aside class="main-sidebar">
    <section class="sidebar">

        <?php

            $arLink = [
                [
                    'icon' => 'fas fa-tv',
                    'title' => __('message.home'),
                    'href' => 'admin',
                ],

                [
                    'icon' => 'fa fa-th',
                    'title' => __('message.system'),
                    'child' => [
                        [
                            'icon' => 'dot fa fa-circle',
                            'title' => __('message.configuration'),
                            'href' => 'admin/settings',
                            'permission' => 'SettingController',
                        ],
                        [
                            'icon' => 'dot fa fa-circle',
                            'title' => __('message.roles'),
                            'href' => 'admin/roles',
                            'permission' => 'RolesController@index',
                        ],
                         [
                            'icon' => 'dot fa fa-circle',
                            'title' => __('message.users'),
                            'href' => 'admin/users',
                            'permission' => 'UsersController@index',
                        ],
                    ],
                ],
                [
                    'icon' => 'fad fa-newspaper',
                    'title' => __('message.service_management'),
                    'child' => [
                        [
                            'icon' => 'dot fa fa-circle',
                            'title' => __('Loại dịch vụ'),
                            'href' => 'admin/service-categories',
                            'permission' => 'ServiceCategoryController@index',
                        ],
                        [
                            'icon' => 'dot fa fa-circle',
                            'title' => __('Dịch vụ'),
                            'href' => 'admin/services',
                            'permission' => 'ServiceController@index',
                        ],
                    ],
                ],
                [
                    'icon' => 'fad fa-images',
                    'title' => __('message.image_management'),
                    'child' => [
                        [
                            'icon' => 'dot fa fa-circle',
                            'title' => __('Quản lý loại hình ảnh'),
                            'href' => 'admin/image-categories',
                            'permission' => 'ImageCategoryController@index',
                        ],
                        [
                            'icon' => 'dot fa fa-circle',
                            'title' => __('Quản lý hình ảnh'),
                            'href' => 'admin/images',
                            'permission' => 'ImageController@index',
                        ],
                    ],
                ],
                [
                    'icon' => 'fas fa-house-user',
                    'title' => __('message.room_type_branch_management'),
                    'child' => [
                        [
                            'icon' => 'dot fa fa-circle',
                            'title' => __('Quản lý loại phòng'),
                            'href' => 'admin/room-types',
                            'permission' => 'RoomTypeController@index',
                        ],
                        [
                            'icon' => 'dot fa fa-circle',
                            'title' => __('message.branch_management'),
                            'href' => 'admin/branches',
                            'permission' => 'BranchController@index',
                        ],
                        [
                            'icon' => 'dot fa fa-circle',
                            'title' => __('Chi nhánh - Loại phòng'),
                            'href' => 'admin/branch-room-types',
                            'permission' => 'BranchRoomTypeController@index',
                        ],
                    ],
                ],
                [
                    'icon' => 'fad fa-users',
                    'title' => __('message.human_resource_management'),
                    'child' => [
                        [
                            'icon' => 'dot fa fa-circle',
                            'title' => __('message.staff_management'),
                            'href' => 'admin/employees',
                            'permission' => 'EmployeeController@index',
                        ],
                        [
                            'icon' => 'dot fa fa-circle',
                            'title' => __('message.assigned_services_management'),
                            'href' => 'admin/employee-services',
                            'permission' => 'EmployeeServiceController@index',
                        ],
                    ],
                ],
                // [
                //     'icon' => 'fad fa-clock',
                //     'title' => __('message.working_shift_management'),
                //     'child' => [
                //         [
                //             'icon' => 'dot fa fa-circle',
                //             'title' => __('message.shift_management'),
                //             'href' => 'admin/working-shifts',
                //             'permission' => 'WorkingShiftController@index',
                //         ],
                //         [
                //             'icon' => 'dot fa fa-circle',
                //             'title' => __('message.employee_shift_assignment'),
                //             'href' => 'admin/employee-working-shifts',
                //             'permission' => 'EmployeeWorkingShiftController@index',
                //         ],
                //     ],
                // ],

                [
                    'icon' => 'fad fa-percent',
                    'title' => __('message.promotion_management'),
                    'href' => 'admin/promotions',
                    'permission' => 'PromotionController@index',
                ],
                [
                    'icon' => 'fad fa-newspaper',
                    'title' => __('message.post_management'),
                    'child' => [
                        [
                            'icon' => 'dot fa fa-circle',
                            'title' => __('Loại bài viết'),
                            'href' => 'admin/post-categories',
                            'permission' => 'PostCategoryController@index',
                        ],
                        [
                            'icon' => 'dot fa fa-circle',
                            'title' => __('Bài viết'),
                            'href' => 'admin/posts',
                            'permission' => 'PostController@index',
                        ],
                        [
                            'icon' => 'dot fa fa-circle',
                            'title' => __('Bình luận bài viết'),
                            'href' => 'admin/post-comments',
                            'permission' => 'PostCommentController@index',
                        ],
                    ],
                ],
                [
                    'icon' => 'fas fa-user',
                    'title' => __('message.membership_management'),
                    'href' => 'admin/memberships',
                    'permission' => 'MembershipController@index',
                ],

                [
                    'icon' => 'fad fa-calendar',
                    'title' => __('message.booking_management'),
                    'href' => 'admin/bookings',
                    'permission' => 'BookingController@index',
                ],
                [
                    'icon' => 'fad fa-user',
                    'title' => __('message.customer_management'),
                    'href' => 'admin/customers',
                    'permission' => 'CustomerController@index',
                ],
                // [
                //     'icon' => 'fad fa-suitcase',
                //     'title' => __('message.utilities'),
                //     'child' => [
                //         [
                //             'icon' => 'dot fa fa-circle',
                //             'title' => __('Tỉnh'),
                //             'href' => 'admin/provinces',
                //             'permission' => 'ProvinceController@index',
                //         ],
                //         [
                //             'icon' => 'dot fa fa-circle',
                //             'title' => __('Huyện'),
                //             'href' => 'admin/districts',
                //             'permission' => 'DistrictController@index',
                //         ],
                //         [
                //             'icon' => 'dot fa fa-circle',
                //             'title' => __('Xã'),
                //             'href' => 'admin/wards',
                //             'permission' => 'WardController@index',
                //         ],
                //     ],
                // ],
            ];
        ?>


        <?php echo $__env->make('admin.layouts.partials.sidebar-menu', ['menus' => $arLink], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    </section>
</aside>
<?php /**PATH D:\HueSoft\crawler_muathau\huesoft_dauthau\resources\views/admin/layouts/partials/sidebar.blade.php ENDPATH**/ ?>