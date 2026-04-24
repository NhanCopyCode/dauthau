<?php
return [
    'base_url' => env('CRAWLER_BASE_URL'),
    'token' => env('CRAWLER_TOKEN'),
    'page_size' => env('CRAWLER_PAGE_SIZE', 50),
    'delay' => env('CRAWLER_DELAY', 1),
    'detail_url' => env('CRAWLER_DETAIL_URL'),
    'detail_apis' => [
        'hsmt' => 'lcnt_tbmcgtt_hsmt',
        'ldt' => 'lcnt_tbmt_ttc_ldt',
        'adb' => 'lcnt_tbmt_ttc_vk_adb',
        'reoffer_online' => 'online-reoffer/detail',
    ],
    'hsmt' => "lcnt_tbmt_hsmt"
];