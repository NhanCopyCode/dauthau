<?php
return [
    'base_url' => env('CRAWLER_BASE_URL'),
    'token' => env('CRAWLER_TOKEN'),
    'page_size' => env('CRAWLER_PAGE_SIZE', 50),
    'delay' => env('CRAWLER_DELAY', 1),
];