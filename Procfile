web: php artisan serve --host=0.0.0.0 --port=$PORT

# Queue workers - mỗi queue một process type riêng
crawl_worker: php artisan queue:work --queue=crawl --tries=3 --timeout=120 --sleep=3 --backoff=5
detail_worker: php artisan queue:work --queue=detail --tries=3 --timeout=120 --sleep=3 --backoff=5
sub_worker: php artisan queue:work --queue=sub --tries=3 --timeout=120 --sleep=3 --backoff=5
hsmt_worker: php artisan queue:work --queue=hsmt --tries=3 --timeout=120 --sleep=3 --backoff=5