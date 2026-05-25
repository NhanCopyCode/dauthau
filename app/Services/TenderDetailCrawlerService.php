<?php

namespace App\Services;

use App\Exceptions\TemporaryCrawlerException;
use App\Models\Tender;
use App\Models\TenderDetail;
use App\Services\TenderDetailResolverService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TenderDetailCrawlerService
{

    protected HsmtService $hsmtService;

    protected TenderDetailResolverService $resolver;

    public function __construct(
        HsmtService $hsmtService,
        TenderDetailResolverService $resolver
    ) {

        $this->hsmtService =
            $hsmtService;

        $this->resolver =
            $resolver;
    }

    // public function handle(
    //     Tender $tender
    // ): ?TenderDetail {

    //     try {

    //         $strategy = $this
    //             ->resolver
    //             ->buildStrategy(
    //                 $tender
    //             );

    //         Log::info(
    //             'DETAIL STRATEGY',
    //             [
    //                 'tender_id'
    //                 => $tender->id,
    //                 'strategy'
    //                 => $strategy,
    //                 'process_apply'
    //                 => $tender->process_apply,
    //                 'step_code'
    //                 => $tender->step_code,
    //             ]
    //         );

    //         $mapped = null;
    //         $type = null;

    //         foreach (
    //             $strategy
    //             as $candidateType
    //         ) {

    //             try {

    //                 Log::info(
    //                     'DETAIL API PROBE',
    //                     [
    //                         'tender_id'
    //                         => $tender->id,
    //                         'type'
    //                         => $candidateType,
    //                     ]
    //                 );

    //                 $candidateMapped =
    //                     $this->tryType(
    //                         $candidateType,
    //                         $tender
    //                     );


    //                 if (
    //                     !empty($candidateMapped)
    //                     && !empty($candidateMapped['notify_no'] ?? null)
    //                 ) {

    //                     $mapped =
    //                         $candidateMapped;

    //                     $type =
    //                         $candidateType;

    //                     Log::info(
    //                         'DETAIL API MATCHED',
    //                         [
    //                             'tender_id'
    //                             => $tender->id,
    //                             'type'
    //                             => $candidateType,
    //                             'notify_no'
    //                             => $mapped['notify_no'] ?? null,
    //                         ]
    //                     );

    //                     break;
    //                 }

    //                 Log::warning(
    //                     'DETAIL API EMPTY',
    //                     [
    //                         'tender_id'
    //                         => $tender->id,
    //                         'type'
    //                         => $candidateType,
    //                     ]
    //                 );
    //             } catch (
    //                 TemporaryCrawlerException $e
    //             ) {

    //                 Log::warning(
    //                     'DETAIL API TEMP FAILURE',
    //                     [
    //                         'tender_id'
    //                         => $tender->id,
    //                         'type'
    //                         => $candidateType,
    //                         'error'
    //                         => $e->getMessage(),
    //                     ]
    //                 );

    //                 continue;
    //             } catch (\Throwable $e) {

    //                 Log::warning(
    //                     'DETAIL API FAILED',
    //                     [
    //                         'tender_id'
    //                         => $tender->id,
    //                         'type'
    //                         => $candidateType,
    //                         'error'
    //                         => $e->getMessage(),
    //                     ]
    //                 );
    //             }
    //         }

    //         if (
    //             empty($mapped)
    //             || empty($mapped['notify_no'] ?? null)
    //         ) {

    //             Log::warning(
    //                 'Mapped data empty or invalid',
    //                 [
    //                     'tender_id'
    //                     => $tender->id,
    //                     'process_apply'
    //                     => $tender->process_apply,
    //                     'step_code'
    //                     => $tender->step_code,
    //                     'strategy'
    //                     => $strategy,
    //                 ]
    //             );

    //             return null;
    //         }

    //         $tenderDetail =
    //             TenderDetail::updateOrCreate(
    //                 [
    //                     'tender_id'
    //                     => $tender->id
    //                 ],
    //                 $this->mapToModel(
    //                     $mapped,
    //                     $tender
    //                 )
    //             );

    //         Log::info(
    //             'TENDER DETAIL SAVED',
    //             [
    //                 'tender_id'
    //                 => $tender->id,
    //                 'type'
    //                 => $type,
    //                 'notify_no'
    //                 => $mapped['notify_no'] ?? null,
    //             ]
    //         );

    //         return $tenderDetail;
    //     } catch (\Throwable $e) {

    //         Log::error(
    //             'Crawler handle failed',
    //             [
    //                 'tender_id'
    //                 => $tender->id,
    //                 'error'
    //                 => $e->getMessage(),
    //             ]
    //         );

    //         throw $e;
    //     }
    // }

    // public function handle(Tender $tender): ?TenderDetail 
    // {
    //     try {
    //         $strategy = $this->resolver->buildStrategy($tender);

    //         Log::info('DETAIL STRATEGY', [
    //             'tender_id'     => $tender->id,
    //             'strategy'      => $strategy,
    //             'process_apply' => $tender->process_apply,
    //             'step_code'     => $tender->step_code,
    //         ]);

    //         $mapped = null;
    //         $type = null;

    //         foreach ($strategy as $candidateType) {
    //             try {
    //                 Log::info('DETAIL API PROBE', [
    //                     'tender_id' => $tender->id,
    //                     'type'      => $candidateType,
    //                 ]);

    //                 // Sử dụng Helper retry() của Laravel để bọc đầu API
    //                 // Thử lại tối đa 3 lần. Nếu lỗi, lần 1 nghỉ 2000ms, lần 2 nghỉ 4000ms...
    //                 $candidateMapped = retry(3, function () use ($candidateType, $tender) {

    //                     $result = $this->tryType($candidateType, $tender);

    //                     // KỸ THUẬT QUAN TRỌNG: Chặn lỗi ngầm của WAF
    //                     // Nếu API trả về dạng chuỗi HTML lỗi thay vì mảng dữ liệu sạch (hoặc trống)
    //                     if (is_string($result) && str_contains($result, '<!DOCTYPE html')) {
    //                         throw new TemporaryCrawlerException("WAF Anti-bot detected! Returned HTML instead of JSON.");
    //                     }

    //                     // Nếu kết quả trống, có thể do nghẽn mạng tạm thời, cũng đưa vào diện nghi ngờ để retry
    //                     if (empty($result)) {
    //                         throw new TemporaryCrawlerException("Empty response from API source.");
    //                     }

    //                     return $result;

    //                 }, function ($exception, $attempt) {
    //                     // Hàm tính toán thời gian sleep tăng tiến (Exponential Backoff với Jitter ngẫu nhiên)
    //                     // Lần 1: ~2 giây | Lần 2: ~4 giây | Lần 3: ~8 giây
    //                     return (pow(2, $attempt) * 1000) + random_int(100, 500);
    //                 });

    //                 /**
    //                  * Kiểm tra dữ liệu hợp lệ sau khi đã vượt qua các vòng retry thành công
    //                  */
    //                 if (!empty($candidateMapped) && !empty($candidateMapped['notify_no'] ?? null)) {
    //                     $mapped = $candidateMapped;
    //                     $type = $candidateType;

    //                     Log::info('DETAIL API MATCHED', [
    //                         'tender_id' => $tender->id,
    //                         'type'      => $candidateType,
    //                         'notify_no' => $mapped['notify_no'] ?? null,
    //                     ]);

    //                     break; // Tìm thấy nguồn chuẩn, thoát vòng lặp nguồn chiến lược
    //                 }

    //                 Log::warning('DETAIL API EMPTY AFTER RETRIES', [
    //                     'tender_id' => $tender->id,
    //                     'type'      => $candidateType,
    //                 ]);

    //             } catch (TemporaryCrawlerException $e) {
    //                 // Toàn bộ các lượt retry của nguồn này đều thất bại do Anti-bot hoặc nghẽn nặng
    //                 Log::warning('DETAIL API TEMP FAILURE (ALL RETRIES EXHAUSTED)', [
    //                     'tender_id' => $tender->id,
    //                     'type'      => $candidateType,
    //                     'error'     => $e->getMessage(),
    //                 ]);

    //                 // Đổi Token hoặc đổi IP Proxy ở đây nếu bạn đã cấu hình Proxy Pool công nghệ cao
    //                 // $this->proxyManager->rotateProxy(); 

    //                 continue; // Chuyển sang nguồn tiếp theo trong mảng Strategy
    //             } catch (\Throwable $e) {
    //                 // Các lỗi hệ thống nghiêm trọng khác (Lỗi Code, Lỗi DB...)
    //                 Log::warning('DETAIL API CRITICAL FAILED', [
    //                     'tender_id' => $tender->id,
    //                     'type'      => $candidateType,
    //                     'error'     => $e->getMessage(),
    //                 ]);
    //             }
    //         }

    //         if (empty($mapped) || empty($mapped['notify_no'] ?? null)) {
    //             Log::warning('Mapped data empty or invalid', [
    //                 'tender_id'     => $tender->id,
    //                 'process_apply' => $tender->process_apply,
    //                 'step_code'     => $tender->step_code,
    //                 'strategy'      => $strategy,
    //             ]);

    //             return null;
    //         }

    //         $tenderDetail = TenderDetail::updateOrCreate(
    //             ['tender_id' => $tender->id],
    //             $this->mapToModel($mapped, $tender)
    //         );

    //         Log::info('TENDER DETAIL SAVED', [
    //             'tender_id' => $tender->id,
    //             'type'      => $type,
    //             'notify_no' => $mapped['notify_no'] ?? null,
    //         ]);

    //         return $tenderDetail;

    //     } catch (\Throwable $e) {
    //         Log::error('Crawler handle failed', [
    //             'tender_id' => $tender->id,
    //             'error'     => $e->getMessage(),
    //         ]);

    //         throw $e;
    //     }
    // }

    // public function handle(Tender $tender): ?TenderDetail
    // {
    //     try {
    //         $strategy = $this->resolver->buildStrategy($tender);

    //         Log::info('DETAIL STRATEGY', [
    //             'tender_id'     => $tender->id,
    //             'strategy'      => $strategy,
    //             'process_apply' => $tender->process_apply,
    //             'step_code'     => $tender->step_code,
    //         ]);

    //         $mapped = null;
    //         $type = null;

    //         foreach ($strategy as $candidateType) {
    //             try {
    //                 Log::info('DETAIL API PROBE', [
    //                     'tender_id' => $tender->id,
    //                     'type'      => $candidateType,
    //                 ]);

    //                 /**
    //                  * FIX BUG 1 & TỐI ƯU RATE LIMIT ĐỘNG:
    //                  * - Thay $exception->getAttempt() bằng biến tự tăng $attempt của Laravel.
    //                  * - Mở rộng kiểm tra nội dung HTML lỗi từ WAF/Load Balancer.
    //                  */
    //                 $candidateMapped = retry(3, function () use ($candidateType, $tender) {

    //                     $result = $this->tryType($candidateType, $tender);

    //                     // KỸ THUẬT CHẶN LỖI: Nhận diện cả trang HTML WAF lỗi 200 lẫn thông báo hạ tầng can't be displayed
    //                     if (is_string($result) && (
    //                         str_contains($result, '<!DOCTYPE html') ||
    //                         str_contains($result, 'This page can\'t be displayed') ||
    //                         str_contains($result, '<title>Error</title>')
    //                     )) {
    //                         throw new TemporaryCrawlerException("Hạ tầng MPI phản hồi trang HTML lỗi (Nghẽn/Rate Limit động).");
    //                     }

    //                     if (empty($result)) {
    //                         throw new TemporaryCrawlerException("API phản hồi chuỗi trống (Timeout ngầm).");
    //                     }

    //                     return $result;
    //                 }, function ($exception, $attempt) {
    //                     /** * 
    //                      * @var \Throwable $exception 
    //                      * @var int $attempt
    //                      */
    //                     /**
    //                      * CHIẾN LƯỢC PHỤC HỒI (BACKOFF STRATEGY):
    //                      * Nếu dính Rate Limit động, ta cần giãn cách thời gian đủ lâu để hệ thống bên kia nhả IP.
    //                      * - Lần 1 lỗi: Ngủ ~3 giây trước khi thử lại.
    //                      * - Lần 2 lỗi: Ngủ ~6.5 giây trước khi thử lại.
    //                      */
    //                     $sleepTime = (pow(1.8, $attempt) * 1500) + random_int(300, 900);

    //                     Log::warning("Gặp sự cố tải trang gói thầu. Tiến hành ngủ ẩn danh {$sleepTime}ms trước khi thử lại lượt {$attempt}...", [
    //                         'error' => $exception->getMessage()
    //                     ]);

    //                     return $sleepTime;
    //                 });

    //                 /**
    //                  * Kiểm tra dữ liệu hợp lệ sau khi đã vượt qua các vòng retry thành công
    //                  */
    //                 if (!empty($candidateMapped) && !empty($candidateMapped['notify_no'] ?? null)) {
    //                     $mapped = $candidateMapped;
    //                     $type = $candidateType;

    //                     Log::info('DETAIL API MATCHED', [
    //                         'tender_id' => $tender->id,
    //                         'type'      => $candidateType,
    //                         'notify_no' => $mapped['notify_no'] ?? null,
    //                     ]);

    //                     break; // Tìm thấy nguồn chuẩn, thoát vòng lặp nguồn chiến lược
    //                 }

    //                 Log::warning('DETAIL API EMPTY AFTER RETRIES', [
    //                     'tender_id' => $tender->id,
    //                     'type'      => $candidateType,
    //                 ]);
    //             } catch (TemporaryCrawlerException $e) {
    //                 // Toàn bộ 3 lượt retry của nguồn này đều thất bại hoàn toàn
    //                 Log::warning('DETAIL API TEMP FAILURE (ALL RETRIES EXHAUSTED)', [
    //                     'tender_id' => $tender->id,
    //                     'type'      => $candidateType,
    //                     'error'     => $e->getMessage(),
    //                 ]);

    //                 // Do không dùng Proxy/Token, ta tiếp tục (continue) để thử vận may với Type tiếp theo trong mảng Strategy
    //                 continue;
    //             } catch (\Throwable $e) {
    //                 // Các lỗi hệ thống nghiêm trọng ngoài ý muốn (Lỗi cú pháp mã hóa model, lỗi DB kết nối...)
    //                 Log::error('DETAIL API CRITICAL FAILED', [
    //                     'tender_id' => $tender->id,
    //                     'type'      => $candidateType,
    //                     'error'     => $e->getMessage(),
    //                 ]);
    //             }
    //         }

    //         if (empty($mapped) || empty($mapped['notify_no'] ?? null)) {
    //             Log::warning('Mapped data empty or invalid after checking all strategies', [
    //                 'tender_id'     => $tender->id,
    //                 'process_apply' => $tender->process_apply,
    //                 'step_code'     => $tender->step_code,
    //                 'strategy'      => $strategy,
    //             ]);

    //             return null;
    //         }

    //         $tenderDetail = TenderDetail::updateOrCreate(
    //             ['tender_id' => $tender->id],
    //             $this->mapToModel($mapped, $tender)
    //         );

    //         Log::info('TENDER DETAIL SAVED SUCCESS', [
    //             'tender_id' => $tender->id,
    //             'type'      => $type,
    //             'notify_no' => $mapped['notify_no'] ?? null,
    //         ]);

    //         return $tenderDetail;
    //     } catch (\Throwable $e) {
    //         Log::error('Crawler handle fatal global failed', [
    //             'tender_id' => $tender->id,
    //             'error'     => $e->getMessage(),
    //         ]);

    //         throw $e;
    //     }
    // }

    // public function handle(Tender $tender): ?TenderDetail
    // {
    //     try {
    //         $strategy = $this->resolver->buildStrategy($tender);

    //         Log::info('DETAIL STRATEGY', [
    //             'tender_id'     => $tender->id,
    //             'strategy'      => $strategy,
    //             'process_apply' => $tender->process_apply,
    //             'step_code'     => $tender->step_code,
    //         ]);

    //         $mapped = null;
    //         $type = null;

    //         foreach ($strategy as $candidateType) {
    //             try {
    //                 Log::info('DETAIL API PROBE', [
    //                     'tender_id' => $tender->id,
    //                     'type'      => $candidateType,
    //                 ]);

    //                 // Bọc đầu gọi API bằng Helper retry của Laravel
    //                 $candidateMapped = retry(3, function () use ($candidateType, $tender) {

    //                     $result = $this->tryType($candidateType, $tender);

    //                     // Chặn triệt để bẫy HTML lỗi từ hạ tầng Muasamcong
    //                     if (is_string($result) && (
    //                         str_contains($result, '<!DOCTYPE html') ||
    //                         str_contains($result, 'This page can\'t be displayed') ||
    //                         str_contains($result, '<title>Error</title>')
    //                     )) {
    //                         throw new TemporaryCrawlerException("Hạ tầng MPI phản hồi trang HTML lỗi (Nghẽn/Rate Limit động).");
    //                     }

    //                     if (empty($result)) {
    //                         throw new TemporaryCrawlerException("API phản hồi chuỗi trống (Timeout ngầm).");
    //                     }

    //                     return $result;
    //                 }, function ($exception, $attempt) {
    //                     /**
    //                      * SỬA LỖI TOÁN TỬ (UNSUPPORTED OPERAND TYPES):
    //                      * Ép kiểu tường minh về số nguyên (int) để bảo vệ toán tử lũy thừa pow().
    //                      * Đồng thời thêm PHPDoc block để dập tắt hoàn toàn gạch đỏ cảnh báo trên VS Code.
    //                      * * @var \Throwable $exception
    //                      * @var int $attempt
    //                      */
    //                     $currentAttempt = (int) $attempt;

    //                     // Tính toán thời gian sleep lũy tiến an toàn
    //                     $sleepTime = (int) (pow(1.8, $currentAttempt) * 1500) + random_int(300, 900);

    //                     Log::warning("Gặp sự cố tải trang gói thầu. Tiến hành ngủ ẩn danh {$sleepTime}ms trước khi thử lại lượt {$currentAttempt}...", [
    //                         'error' => $exception->getMessage()
    //                     ]);

    //                     return $sleepTime;
    //                 });

    //                 /**
    //                  * Kiểm tra dữ liệu hợp lệ sau khi vượt qua các vòng retry thành công
    //                  */
    //                 if (!empty($candidateMapped) && !empty($candidateMapped['notify_no'] ?? null)) {
    //                     $mapped = $candidateMapped;
    //                     $type = $candidateType;

    //                     Log::info('DETAIL API MATCHED', [
    //                         'tender_id' => $tender->id,
    //                         'type'      => $candidateType,
    //                         'notify_no' => $mapped['notify_no'] ?? null,
    //                     ]);

    //                     break; // Thoát vòng lặp strategy khi đã lấy được dữ liệu chuẩn
    //                 }

    //                 Log::warning('DETAIL API EMPTY AFTER RETRIES', [
    //                     'tender_id' => $tender->id,
    //                     'type'      => $candidateType,
    //                 ]);
    //             } catch (TemporaryCrawlerException $e) {
    //                 // Toàn bộ các lượt retry của nguồn này đều thất bại do dính chặn HTML
    //                 Log::warning('DETAIL API TEMP FAILURE (ALL RETRIES EXHAUSTED)', [
    //                     'tender_id' => $tender->id,
    //                     'type'      => $candidateType,
    //                     'error'     => $e->getMessage(),
    //                 ]);

    //                 continue; // Chuyển sang nguồn dự phòng tiếp theo trong mảng Strategy
    //             } catch (\Throwable $e) {
    //                 // Bắt các lỗi hệ thống nghiêm trọng khác
    //                 Log::error('DETAIL API CRITICAL FAILED', [
    //                     'tender_id' => $tender->id,
    //                     'type'      => $candidateType,
    //                     'error'     => $e->getMessage(),
    //                 ]);
    //             }
    //         }

    //         if (empty($mapped) || empty($mapped['notify_no'] ?? null)) {
    //             Log::warning('Mapped data empty or invalid after checking all strategies', [
    //                 'tender_id'     => $tender->id,
    //                 'process_apply' => $tender->process_apply,
    //                 'step_code'     => $tender->step_code,
    //                 'strategy'      => $strategy,
    //             ]);

    //             return null;
    //         }

    //         $tenderDetail = TenderDetail::updateOrCreate(
    //             ['tender_id' => $tender->id],
    //             $this->mapToModel($mapped, $tender)
    //         );

    //         Log::info('TENDER DETAIL SAVED SUCCESS', [
    //             'tender_id' => $tender->id,
    //             'type'      => $type,
    //             'notify_no' => $mapped['notify_no'] ?? null,
    //         ]);

    //         return $tenderDetail;
    //     } catch (\Throwable $e) {
    //         Log::error('Crawler handle fatal global failed', [
    //             'tender_id' => $tender->id,
    //             'error'     => $e->getMessage(),
    //         ]);

    //         throw $e;
    //     }
    // }

    public function handle(Tender $tender): ?TenderDetail
    {
        try {
            $strategy = $this->resolver->buildStrategy($tender);

            Log::info('DETAIL STRATEGY', [
                'tender_id'     => $tender->id,
                'strategy'      => $strategy,
                'process_apply' => $tender->process_apply,
                'step_code'     => $tender->step_code,
            ]);

            $mapped = null;
            $type = null;

            foreach ($strategy as $candidateType) {
                try {
                    Log::info('DETAIL API PROBE', [
                        'tender_id' => $tender->id,
                        'type'      => $candidateType,
                    ]);

                    $candidateMapped = null;
                    $maxAttempts = 3;

                    // THAY THẾ HELPER RETRY BẰNG VÒNG LẶP FOR ĐỂ KIỂM SOÁT BIẾN TUYỆT ĐỐI
                    for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
                        try {
                            $result = $this->tryType($candidateType, $tender);

                            // Chặn đứng và nhận diện bẫy HTML lỗi từ hạ tầng Muasamcong
                            if (is_string($result) && (
                                str_contains($result, '<!DOCTYPE html') ||
                                str_contains($result, 'This page can\'t be displayed') ||
                                str_contains($result, '<title>Error</title>')
                            )) {
                                throw new TemporaryCrawlerException("Hạ tầng MPI phản hồi trang HTML lỗi (Nghẽn/Rate Limit động).");
                            }

                            if (empty($result)) {
                                throw new TemporaryCrawlerException("API phản hồi chuỗi trống (Timeout ngầm).");
                            }

                            // Nếu lấy được dữ liệu JSON sạch, gán kết quả và bẻ gãy vòng lặp for (Thành công)
                            $candidateMapped = $result;
                            break;
                        } catch (TemporaryCrawlerException $e) {
                            // Nếu đã cố gắng đến lần thứ 3 (lần cuối) mà vẫn lỗi, ném lỗi ra ngoài để chuyển đổi Strategy
                            if ($attempt === $maxAttempts) {
                                throw $e;
                            }

                            // Tính toán thời gian ngủ lũy tiến (Exponential Backoff với Jitter ngẫu nhiên)
                            // Sử dụng biến $attempt từ vòng lặp for đảm bảo không bao giờ lỗi "operand types"
                            $sleepTime = (int) (pow(1.8, $attempt) * 1500) + random_int(300, 900);

                            Log::warning("Gặp sự cố tải trang gói thầu. Tiến hành ngủ ẩn danh {$sleepTime}ms trước khi thử lại lượt " . ($attempt + 1) . "...", [
                                'tender_id' => $tender->id,
                                'type'      => $candidateType,
                                'error'     => $e->getMessage()
                            ]);

                            // Chuyển đổi mili-giây sang micro-giây cho hàm usleep
                            usleep($sleepTime * 1000);
                        }
                    }

                    /**
                     * Kiểm tra dữ liệu hợp lệ sau khi vượt qua các vòng retry thành công
                     */
                    if (!empty($candidateMapped) && !empty($candidateMapped['notify_no'] ?? null)) {
                        $mapped = $candidateMapped;
                        $type = $candidateType;

                        Log::info('DETAIL API MATCHED', [
                            'tender_id' => $tender->id,
                            'type'      => $candidateType,
                            'notify_no' => $mapped['notify_no'] ?? null,
                        ]);

                        break; // Thoát hẳn vòng lặp foreach lớn khi đã lấy được nguồn dữ liệu chuẩn
                    }

                    Log::warning('DETAIL API EMPTY AFTER RETRIES', [
                        'tender_id' => $tender->id,
                        'type'      => $candidateType,
                    ]);
                } catch (TemporaryCrawlerException $e) {
                    // Chỉ khi toàn bộ 3 lượt thử của API hiện tại đều thất bại hoàn toàn, mới ghi nhận lỗi và nhảy sang Type tiếp theo
                    Log::warning('DETAIL API TEMP FAILURE (ALL RETRIES EXHAUSTED)', [
                        'tender_id' => $tender->id,
                        'type'      => $candidateType,
                        'error'     => $e->getMessage(),
                    ]);

                    continue; // Chuyển sang nguồn dự phòng tiếp theo trong mảng Strategy
                } catch (\Throwable $e) {
                    // Bắt các lỗi hệ thống nghiêm trọng ngoài ý muốn để tránh làm sập Queue
                    Log::error('DETAIL API CRITICAL FAILED', [
                        'tender_id' => $tender->id,
                        'type'      => $candidateType,
                        'error'     => $e->getMessage(),
                    ]);
                }
            }

            if (empty($mapped) || empty($mapped['notify_no'] ?? null)) {
                Log::warning('Mapped data empty or invalid after checking all strategies', [
                    'tender_id'     => $tender->id,
                    'process_apply' => $tender->process_apply,
                    'step_code'     => $tender->step_code,
                    'strategy'      => $strategy,
                ]);

                return null;
            }

            $tenderDetail = TenderDetail::updateOrCreate(
                ['tender_id' => $tender->id],
                $this->mapToModel($mapped, $tender)
            );

            Log::info('TENDER DETAIL SAVED SUCCESS', [
                'tender_id' => $tender->id,
                'type'      => $type,
                'notify_no' => $mapped['notify_no'] ?? null,
            ]);

            return $tenderDetail;
        } catch (\Throwable $e) {
            Log::error('Crawler handle fatal global failed', [
                'tender_id' => $tender->id,
                'error'     => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    private function fetchLdt(Tender $tender): array
    {
        return $this->callApi('ldt', $tender->egp_id, $tender);
    }

    private function fetchAbd(Tender $tender): array
    {
        return $this->callApi('adb', $tender->egp_id, $tender);
    }

    private function fetchReofferOnline(Tender $tender): array
    {
        return $this->callApi('reoffer_online', $tender->egp_id, $tender);
    }

    private function fetchHSMT(Tender $tender): array
    {
        return $this->callApi('hsmt', $tender->egp_id, $tender);
    }


    private function callApi(
        string $type,
        string $id,
        Tender $tender
    ): array {

        $endpoint =
            config(
                "crawler.detail_apis.$type"
            );

        $token =
            config('crawler.token');

        $url =
            "https://muasamcong.mpi.gov.vn/o/egp-portal-contractor-selection-v2/services/{$endpoint}?token={$token}";

        $payload =
            $this->buildPayloadByType(
                $type,
                $id,
                $tender
            );

        $startedAt =
            microtime(true);

        try {

            Log::info(
                'API REQUEST START',
                [
                    'tender_id' =>
                    $tender->id,

                    'type' =>
                    $type,

                    'url' =>
                    $url,

                    'payload' =>
                    $payload,
                ]
            );

            $response = Http::timeout(20)
                ->connectTimeout(10)
                ->withHeaders([
                    'Accept' =>
                    'application/json, text/plain, */*',

                    'Content-Type' =>
                    'application/json',

                    'Origin' =>
                    'https://muasamcong.mpi.gov.vn',

                    'Referer' =>
                    'https://muasamcong.mpi.gov.vn/',

                    'User-Agent' =>
                    'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',

                    'X-Requested-With' =>
                    'XMLHttpRequest',
                ])
                ->withOptions([
                    'verify' => false,
                    'allow_redirects' => true,
                ])
                ->post(
                    $url,
                    $payload
                );

            $durationMs = round(
                (
                    microtime(true)
                    - $startedAt
                ) * 1000,
                2
            );

            $status =
                $response->status();

            $rawBody =
                $response->body();

            $contentType =
                strtolower(
                    $response->header(
                        'Content-Type'
                    ) ?? ''
                );

            Log::info(
                'API RESPONSE',
                [
                    'tender_id' =>
                    $tender->id,

                    'type' =>
                    $type,

                    'status' =>
                    $status,

                    'duration_ms' =>
                    $durationMs,

                    'content_type' =>
                    $contentType,

                    'body_preview' =>
                    mb_substr(
                        $rawBody,
                        0,
                        1000
                    ),
                ]
            );

            if (
                in_array(
                    $status,
                    [
                        429,
                        500,
                        502,
                        503,
                        504,
                    ]
                )
            ) {

                throw new TemporaryCrawlerException(
                    "Temporary HTTP {$status}"
                );
            }

            if (
                !$response->successful()
            ) {

                throw new \Exception(
                    "Permanent HTTP {$status}"
                );
            }
            if (
                blank($rawBody)
            ) {

                throw new TemporaryCrawlerException(
                    'API empty body'
                );
            }
            if (
                str_contains(
                    $contentType,
                    'text/html'
                )
                || str_starts_with(
                    trim($rawBody),
                    '<!DOCTYPE html'
                )
                || str_starts_with(
                    trim($rawBody),
                    '<html'
                )
            ) {

                throw new TemporaryCrawlerException(
                    'MPI returned HTML page'
                );
            }
            $json =
                json_decode(
                    $rawBody,
                    true
                );

            if (
                json_last_error()
                !== JSON_ERROR_NONE
            ) {

                throw new TemporaryCrawlerException(
                    'Invalid JSON: '
                        . json_last_error_msg()
                );
            }

            if (
                !is_array($json)
            ) {

                throw new TemporaryCrawlerException(
                    'Response not array'
                );
            }

            Log::info(
                'API SUCCESS',
                [
                    'tender_id' =>
                    $tender->id,

                    'type' =>
                    $type,
                ]
            );

            return $json;
        } catch (
            TemporaryCrawlerException $e
        ) {

            Log::warning(
                'TEMP API FAILURE',
                [
                    'tender_id' =>
                    $tender->id,

                    'type' =>
                    $type,

                    'error' =>
                    $e->getMessage(),
                ]
            );

            throw $e;
        } catch (\Throwable $e) {

            Log::error(
                'API EXCEPTION',
                [
                    'tender_id' =>
                    $tender->id,

                    'type' =>
                    $type,

                    'error' =>
                    $e->getMessage(),

                    'class' =>
                    get_class($e),
                ]
            );

            throw $e;
        }
    }
    private function buildPayloadByType(string $type, string $id, Tender $tender): array
    {
        switch ($type) {
            case 'hsmt':
                return [
                    'id' => $id,
                    'processApply' =>  'LDT'
                ];

            default:
                return [
                    'id' => $id
                ];
        }
    }


    private function isValidLdtData(array $data): bool
    {
        if (empty($data)) {
            return false;
        }

        $main =
            data_get($data, 'bidoNotifyContractorM') ??
            data_get($data, 'bidNoContractorResponse.bidNotification');

        if (!empty($main)) {
            return true;
        }

        return !empty(data_get($data, 'notifyNo'));
    }


    private function handleLdt(array $data): array
    {
        $source1 = data_get($data, 'bidoNotifyContractorM', []);
        $source2 = data_get($data, 'bidNoContractorResponse.bidNotification', []);

        $main = array_replace_recursive($source1, $source2);

        $plan = data_get($data, 'bidpPlanDetail', []) ?? [];
        $approval = data_get($data, 'bidInvContractorOfflineDTO', []) ?? [];

        return $this->buildCommonData($main, $plan, $approval, $data);
    }

    private function handleAbd(array $data): array
    {
        $main =
            data_get($data, 'bidoNotifyContractorP')
            ?? [];

        $plan = data_get($data, 'bidpPlanDetail', []) ?? [];
        $approval = data_get($data, 'bidInvContractorOfflineDTO', []) ?? [];

        return $this->buildCommonData($main, $plan, $approval, $data);
    }


    private function handleReoffer(array $data, array $hsmtData): array
    {
        $main = $data;
        $plan = data_get($data, 'bidDetail', []);
        $approval = [];

        $main = array_merge($main, [
            'hsmt' => $hsmtData
        ]);

        $lotRaw = data_get($data, 'bidoListContractorReofferPassedDTOList', []);
        $lotTable = $this->buildLotTable($lotRaw);

        $hsmtParsed = $this->parseHsmt($hsmtData);
        $scopeTree = $this->buildScopeTree($hsmtParsed['rows']);

        $scopeTable = [
            'columns' => $hsmtParsed['columns'],
            'rows' => $scopeTree
        ];

        return array_merge(
            $this->buildCommonData($main, $plan, $approval, $data),
            [
                'lot_table' => $lotTable,
                'scope_table' => $scopeTable
            ]
        );
    }

    private function buildScopeTree(array $rows): array
    {
        $map = [];
        $tree = [];

        foreach ($rows as $row) {
            $row['children'] = [];
            $map[$row['id']] = $row;
        }

        foreach ($map as $id => &$node) {
            $parent = $node['parent'] ?? 0;

            if ($parent == 0) {
                $tree[] = &$node;
            } else {
                if (isset($map[$parent])) {
                    $map[$parent]['children'][] = &$node;
                }
            }
        }

        return array_values($tree);
    }

    private function parseHsmt(array $data): array
    {
        $formContentRaw = data_get($data, 'bidoInvBiddingDTO.0.formContent');
        $formValueRaw = data_get($data, 'bidoInvBiddingDTO.0.formValue');
        if (!$formContentRaw || !$formValueRaw) {
            return ['columns' => [], 'rows' => []];
        }

        $formContent = json_decode($formContentRaw, true);
        $formValue = json_decode($formValueRaw, true);

        $columns = collect($formContent)->map(function ($col) {
            return [
                'key' => $col['columnName'],
                'title' => $col['columnTitle'],
                'type' => $col['columnType'] ?? 'text'
            ];
        })->values()->toArray();

        $rows = data_get($formValue, 'Table', []);

        return [
            'columns' => $columns,
            'rows' => $rows
        ];
    }

    private function buildLotTable(array $list): array
    {
        return [
            'columns' => [
                ['key' => 'lot_no', 'title' => 'Mã lô'],
                ['key' => 'lot_name', 'title' => 'Tên lô'],
                ['key' => 'price_init', 'title' => 'Giá trần'],
                ['key' => 'price_step', 'title' => 'Bước giá'],
            ],
            'rows' => collect($list)->map(function ($item) {
                return [
                    'lot_no' => data_get($item, 'lotNo'),
                    'lot_name' => data_get($item, 'lotName'),
                    'price_init' => $this->toDecimal(data_get($item, 'priceInit')),
                    'price_step' => $this->toDecimal(data_get($item, 'priceStep')),
                ];
            })->values()->toArray()
        ];
    }

    private function buildCommonData(array $main, array $plan, array $approval, array $data): array
    {
        return array_merge(
            $this->mapCoreFields($main, $plan),
            $this->enrichExtraFields($main, $plan, $approval, $data)
        );
    }

    private function mapCoreFields(array $main, array $plan): array
    {
        return [
            'notify_no' => data_get($main, 'notifyNo'),
            'notify_version' => data_get($main, 'notifyVersion'),
            'plan_no' => data_get($main, 'planNo') ?? data_get($plan, 'planNo'),
            'plan_id' => data_get($plan, 'planId'),

            'public_date' => $this->parseDate(data_get($main, 'publicDate')),
            'plan_type' => data_get($main, 'planType'),
            'plan_name' => data_get($main, 'pName') ?? data_get($main, 'planName') ?? data_get($plan, 'planName'),

            'bid_name' => data_get($main, 'bidName'),
            'bid_no' => data_get($main, 'bidNo'),
            'investor_name' => data_get($main, 'investorName') ?? data_get($main, 'procuringEntityName'),

            'capital_detail' => data_get($main, 'capitalDetail') ?? data_get($plan, 'capitalDetail'),
        ];
    }

    private function enrichExtraFields(array $main, array $plan, array $approval, array $data): array
    {
        return [
            'invest_field' => data_get($main, 'investField'),
            'bid_form' => data_get($main, 'bidForm'),
            'contract_type' => data_get($main, 'contractType') ?? data_get($main, 'cType'),
            'is_agree_frame' => $this->toBool(data_get($main, 'isAgreeFrame')) ?? 0,

            'is_domestic' => $this->toBool(data_get($main, 'isDomestic')),
            'bid_mode' => data_get($main, 'bidMode'),
            'contract_period' => data_get($main, 'contractPeriod')
                ?? data_get($plan, 'cperiod'),
            'contract_period_unit' => data_get($main, 'contractPeriodUnit') ?? data_get($main, 'cPeriodUnit')
                ?? data_get($plan, 'cperiodUnit'),

            'is_multi_lot' => $this->toBool(
                data_get($main, 'isMultiLot')
                    ?? data_get($plan, 'isMultiLot'),
            ),
            'lot_count' => $this->resolveLotCount($main, $plan),
            'ceiling_price' => data_get($main, 'priceInit'),
            'price_step' => data_get($main, 'priceStep'),
            'bid_validity_period_reoffer' => data_get($main, 'bidValidityPeriod'),
            'bid_validity_period_unit_reoffer' => 'D',

            'is_online_bidding' => $this->toBool(data_get($main, 'isInternet')),
            'issue_location' => data_get($main, 'issueLocation'),
            'receive_location' => data_get($main, 'receiveLocation'),
            'execution_location' => data_get($main, 'executionLocation'),

            'reoffer_start_time' => $this->parseDate(data_get($main, 'reofferCloseDate')),
            'reoffer_end_time'   => $this->parseDate(data_get($main, 'reofferOpenDate')),

            'bid_close_date' => $this->parseDate(data_get($main, 'bidCloseDate')),
            'bid_open_date' => $this->parseDate(data_get($main, 'bidOpenDate')),
            'bid_open_location' => data_get($main, 'bidOpenLocation'),
            'bid_validity_period' => data_get($main, 'bidValidityPeriod'),
            'bid_validity_period_unit' => data_get($main, 'bidValidityPeriodUnit') ?? 'D',

            'bid_guarantee_amount' => $this->toDecimal(data_get($main, 'guaranteeValue') ?? data_get($main, 'bidGuaranteeValue')),
            'bid_guarantee_form' => data_get($main, 'guaranteeForm') ?? data_get($main, 'bidGuaranteeForm'),
            'bid_submission_fee' => $this->calculateBidSubmissionFee($main),

            'work_type' => data_get($main, 'workType'),

            'approval_decision_number' => data_get($approval, 'decisionNo'),
            'approval_decision_date' => $this->parseDate(data_get($approval, 'decisionDate')),
            'approval_agency' => data_get($approval, 'decisionAgency'),
            'approval_file_name' => data_get($approval, 'decisionFileName'),
            'modification_file_name' => data_get($approval, 'otherFileName'),

            'contractors' => $this->mapContractors(
                is_array(data_get($data, 'contractorsResultAll'))
                    ? data_get($data, 'contractorsResultAll')
                    : []
            ),

            'delay_list' => data_get($main, 'delayDTOList') ?? data_get($data, 'delayList'),

            'raw_json' => [
                $data
            ]


        ];
    }

    private function mapContractors(array $contractors = []): array
    {
        return collect($contractors)->map(function ($item) {
            return [
                'id' => data_get($item, 'id'),
                'contractor_code' => data_get($item, 'contractorCode'),
                'contractor_name' => data_get($item, 'contractorName'),

                'times' => data_get($item, 'times'),

                'reoffer_price' => data_get($item, 'reofferPrice'),
                'reoffer_price_final' => data_get($item, 'reofferPriceFinal'),

                'lot_no' => data_get($item, 'lotNo'),
                'lot_name' => data_get($item, 'lotName'),

                'reoffer_date' => $this->parseDate(data_get($item, 'reofferDate')),

                'is_newest' => (int) data_get($item, 'isNewest', 0),

                'form_value' => $this->safeJsonDecode(data_get($item, 'formValue')),
            ];
        })->values()->toArray();
    }

    private function safeJsonDecode(?string $json): ?array
    {
        if (empty($json)) return null;

        try {
            return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            Log::warning('Invalid JSON in contractor form_value', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }


    private function resolveLotCount(array $main, array $plan): int
    {
        $lotList = data_get($main, 'lotDTOList');

        if (is_array($lotList) && count($lotList)) {
            return count($lotList);
        }

        $contractors = data_get($main, 'bidoListContractorReofferPassedDTOList', []);

        if (!is_array($contractors) || empty($contractors)) {
            return 0;
        }

        return collect($contractors)
            ->pluck('lotNo')
            ->filter()
            ->unique()
            ->count();
    }

    private function calculateBidSubmissionFee(array $main): ?int
    {
        $bidForm = data_get($main, 'bidForm');
        $isInternet = (int) data_get($main, 'isInternet');
        $investField = data_get($main, 'investField');

        if ($investField === 'TV' && $bidForm === 'TVCN') {
            return null;
        }

        if ($isInternet !== 1) {
            return null;
        }

        $feeMap = [
            'DTRR' => 330000,
            'DTHC' => 330000,
            'MSTT' => 330000,
            'CHCT' => 220000,
            'CHCTRG' => 220000,
        ];

        return $feeMap[$bidForm] ?? null;
    }
    private function mapToModel(array $data, Tender $tender): array
    {
        return array_merge($data, [
            'tender_id' => $tender->id,

            'lot_table' => isset($data['lot_table'])
                ? $data['lot_table']
                : null,

            'scope_table' => isset($data['scope_table'])
                ? $data['scope_table']
                : null,
        ]);
    }

    private function parseDate(?string $date): ?string
    {
        if (!$date) return null;

        try {
            return Carbon::parse($date)->format('Y-m-d H:i:s');
        } catch (\Exception) {
            return null;
        }
    }

    private function toBool($value): ?int
    {
        return is_null($value) ? null : (int) $value;
    }

    private function toDecimal($value): ?float
    {
        return is_null($value) ? null : (float) $value;
    }

    private function detectTypeFromTender(
        Tender $tender
    ): string {

        $stepCode = strtolower(
            trim($tender->step_code ?? '')
        );

        $processApply = strtolower(
            trim($tender->process_apply ?? '')
        );

        if (
            str_contains(
                $stepCode,
                'reoffer'
            )
        ) {
            return 'reoffer';
        }

        return match ($processApply) {

            'ldt' => 'ldt',

            'adb',
            'khac' => 'adb',

            default => $this->guessType(
                $tender
            ),
        };
    }

    private function guessType(
        Tender $tender
    ): string {

        Log::warning(
            'Unknown process_apply',
            [
                'tender_id' => $tender->id,
                'process_apply'
                => $tender->process_apply,
                'step_code'
                => $tender->step_code,
            ]
        );

        if (
            strtolower(
                $tender->plan_type ?? ''
            ) === 'khac'
        ) {
            return 'adb';
        }

        return 'ldt';
    }


    private function tryType(
        string $type,
        Tender $tender
    ): ?array {

        return match ($type) {

            'adb'
            => $this->tryAdb(
                $tender
            ),

            'ldt'
            => $this->tryLdt(
                $tender
            ),

            'reoffer'
            => $this->tryReoffer(
                $tender
            ),

            default
            => null,
        };
    }

    private function tryAdb(
        Tender $tender
    ): ?array {

        $data = $this->fetchAbd(
            $tender
        );

        if (empty($data)) {

            Log::warning(
                'ADB EMPTY',
                [
                    'tender_id'
                    => $tender->id,
                ]
            );

            return null;
        }

        /**
         * Map trước
         */
        $mapped = $this
            ->handleAbd(
                $data
            );

        /**
         * Validate mapped result
         */
        if (
            !$this->isValidMapped(
                $mapped
            )
        ) {

            Log::warning(
                'ADB INVALID',
                [
                    'tender_id'
                    => $tender->id,
                    'mapped'
                    => $mapped,
                ]
            );

            return null;
        }

        Log::info(
            'ADB MAPPED',
            [
                'tender_id'
                => $tender->id,
                'notify_no'
                => $mapped['notify_no'] ?? null,
            ]
        );

        return $mapped;
    }


    private function tryLdt(
        Tender $tender
    ): ?array {

        $ldtData =
            $this->fetchLdt(
                $tender
            );

        if (
            !$this->isValidLdtData(
                $ldtData
            )
        ) {

            Log::warning(
                'LDT INVALID STRUCTURE',
                [
                    'tender_id'
                    => $tender->id,
                ]
            );

            return null;
        }

        $mapped =
            $this->handleLdt(
                $ldtData
            );

        /**
         * Validate mapped
         */
        if (
            !$this->isValidMapped(
                $mapped
            )
        ) {

            Log::warning(
                'LDT INVALID MAPPED',
                [
                    'tender_id'
                    => $tender->id,
                    'mapped'
                    => $mapped,
                ]
            );

            return null;
        }

        Log::info(
            'LDT MAPPED',
            [
                'tender_id'
                => $tender->id,
                'notify_no'
                => $mapped['notify_no'] ?? null,
            ]
        );

        return $mapped;
    }

    private function tryReoffer(
        Tender $tender
    ): ?array {

        $reofferData =
            $this->fetchReofferOnline(
                $tender
            );

        if (
            empty($reofferData)
        ) {

            Log::warning(
                'REOFFER EMPTY',
                [
                    'tender_id'
                    => $tender->id,
                ]
            );

            return null;
        }

        if (
            empty(data_get(
                $reofferData,
                'notifyNo'
            ))
            && empty(data_get(
                $reofferData,
                'bidName'
            ))
        ) {

            Log::warning(
                'REOFFER INVALID STRUCTURE',
                [
                    'tender_id'
                    => $tender->id,
                ]
            );

            return null;
        }

        $hsmtData =
            $this->fetchHSMT(
                $tender
            );

        $mapped =
            $this->handleReoffer(
                $reofferData,
                $hsmtData
            );

        if (
            !$this->isValidMapped(
                $mapped
            )
        ) {

            Log::warning(
                'REOFFER INVALID MAPPED',
                [
                    'tender_id'
                    => $tender->id,
                    'mapped'
                    => $mapped,
                ]
            );

            return null;
        }

        Log::info(
            'REOFFER MAPPED',
            [
                'tender_id'
                => $tender->id,
                'notify_no'
                => $mapped['notify_no'] ?? null,
            ]
        );

        return $mapped;
    }

    private function isValidMapped(
        ?array $mapped
    ): bool {

        if (
            empty($mapped)
        ) {
            return false;
        }

        /**
         * notify_no là strongest signal
         */
        if (
            !empty($mapped['notify_no'] ?? null)
        ) {
            return true;
        }

        /**
         * fallback
         */
        if (
            !empty($mapped['bid_name'] ?? null)
        ) {
            return true;
        }

        if (
            !empty($mapped['approve_no'] ?? null)
        ) {
            return true;
        }

        return false;
    }
}
