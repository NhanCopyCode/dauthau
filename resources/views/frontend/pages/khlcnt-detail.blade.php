 @extends('layouts.frontend')
 @section('style')
     <link rel="stylesheet" href="{{ asset('css/dauthau.css') }}">
 @endsection
 @section('content')
     <div class="bg-gray-50 ">


         <div class="max-w-7xl mx-auto p-4 md:p-6">
             <div class="mb-4 flex items-center justify-between">

                 <!-- Back button -->
                 <a href="{{ route('tenders.show', ['egp_id' => $tender->egp_id]) }}"
                     class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-blue-600 transition group">

                     <!-- Icon -->
                     <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-4 h-4 transition-transform duration-200 group-hover:-translate-x-1" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                     </svg>

                     <span class="font-medium">Quay lại</span>
                 </a>

             </div>
             <div class="bg-gray-50 ">


                 <div class="max-w-7xl mx-auto p-4 md:p-6">
                     <div class="d-flex flex-row align-items-start content-body">
                         <div class="d-flex flex-column align-items-start content-body__left">
                             <div class="d-flex flex-column align-items-start w-100">
                                 <div class="d-flex justify-content-between w-100">
                                     <h5>Xem thông tin kế hoạch lựa chọn nhà thầu</h5>
                                     <div class="pr-0 col-2" style="display: none;"><!----> <!----></div>
                                 </div>
                                 <div class="w-100">
                                     <ul class="nav nav-tabs nav-scroll nav-title border--none">

                                         <li class="new_nav_tab">
                                             <a href="{{ route('khlcnt.show', $tender->detail->plan_id) }}"
                                                 class="{{ request()->routeIs('khlcnt.show') ? 'active' : '' }}">
                                                 Thông tin chung
                                             </a>
                                         </li>

                                         <li class="new_nav_tab">
                                             <a href="{{ route('khlcnt.detail', [
                                                 'id' => $tender->bid_id,
                                                 'plan_id' => $tender->detail->plan_id,
                                             ]) }}"
                                                 class="{{ request()->routeIs('khlcnt.detail') ? 'active' : '' }}">
                                                 Thông tin gói thầu
                                             </a>
                                         </li>

                                     </ul>
                                 </div>
                             </div>
                             <div class="tab-content">
                                 <div id="tab1" class="tab-pane active">
                                     <div class="card border--none mb-0">
                                         <div class="card-body d-flex flex-column align-items-start infomation"></div>
                                     </div>
                                     <div class="card border--none">
                                         <div class="card-body item-table m-0" style="padding: 0px !important;">
                                             <table class="table">
                                                 <thead class="thead">
                                                     <tr>
                                                         <th scope="col">STT</th>
                                                         <th scope="col" style="z-index: 0 !important;">Tên gói thầu</th>
                                                         <th scope="col" style="width: 30%;">Dự toán gói thầu được duyệt
                                                             sau khi phê duyệt KHLCNT</th>
                                                         <th scope="col" style="width: 140px;">Giá gói thầu</th>
                                                         <th scope="col">Số thông báo liên kết</th>
                                                     </tr>
                                                 </thead>
                                                 <tbody>
                                                     @php
                                                         $notify = json_decode(
                                                             data_get($detail, 'linkNotifyInfo'),
                                                             true,
                                                         );
                                                     @endphp
                                                     @forelse ($lots as $index => $lot)
                                                         @php

                                                             $price = number_format(
                                                                 data_get($lot, 'lotPrice') ??
                                                                     data_get($lot, 'bidPrice'),
                                                                 0,
                                                                 ',',
                                                                 '.',
                                                             );

                                                             $notifyNo = data_get($notify, 'notifyNo');
                                                         @endphp

                                                         <tr>
                                                             <td class="lf-th-content">
                                                                 {{ $index + 1 }}
                                                             </td>

                                                             <td class="lf-th-content">
                                                                 <a href="#" class="text-info"
                                                                     style="color:#4D7AE5 !important;">
                                                                     {{ data_get($lot, 'lotName') ?? data_get($lot, 'bidName') }}
                                                                 </a>
                                                             </td>

                                                             <td class="lf-th-content">
                                                                 {{ data_get($lot, 'lotEstimatePrice')
                                                                     ? number_format(data_get($lot, 'lotEstimatePrice'), 0, ',', '.') . ' VND'
                                                                     : '-' }}
                                                             </td>

                                                             <td class="lf-th-content">
                                                                 <span>
                                                                     {{ $price }}
                                                                     {{ data_get($lot, 'bidPriceUnit', 'VND') }}
                                                                 </span>
                                                             </td>

                                                             <td class="lf-th-content"
                                                                 style="color:#40a9ff; cursor:pointer;">
                                                                 @if ($notifyNo)
                                                                     <span>{{ $notifyNo }}</span>
                                                                 @else
                                                                     -
                                                                 @endif
                                                             </td>
                                                         </tr>

                                                     @empty
                                                         <tr>
                                                             <td colspan="5" class="text-center">
                                                                 Không có dữ liệu gói thầu
                                                             </td>
                                                         </tr>
                                                     @endforelse
                                                 </tbody>
                                             </table>
                                         </div>
                                     </div>
                                     @php
                                         $price = data_get($detail, 'bidPrice');
                                         $priceText = $price ? number_format($price, 0, ',', '.') . ' VND' : '-';

                                         // JSON notify
                                         $notify = json_decode(data_get($detail, 'linkNotifyInfo'), true);

                                         // Location
                                         $location = collect($locations)->pluck('provName')->filter()->join(', ');

                                         // Mapping
                                         $mapBidField = [
                                             'HH' => 'Hàng hóa',
                                             'XL' => 'Xây lắp',
                                             'TV' => 'Tư vấn',
                                         ];

                                         $mapBidForm = [
                                             'CHCT' => 'Chào hàng cạnh tranh',
                                             'DTRR' => 'Đấu thầu rộng rãi',
                                             'CDTRG' => 'Chỉ định thầu rút gọn',
                                         ];

                                         $mapBidMode = [
                                             '1_MTHS' => 'Một giai đoạn một túi hồ sơ',
                                         ];

                                         $mapCtype = [
                                             'TG' => 'Trọn gói',
                                         ];

                                         // helper
                                         $field = $mapBidField[data_get($detail, 'bidField')] ?? 'Khác';
                                         $form = $mapBidForm[data_get($detail, 'bidForm')] ?? '-';
                                         $mode = $mapBidMode[data_get($detail, 'bidMode')] ?? '-';
                                         $ctype = $mapCtype[data_get($detail, 'ctype')] ?? '-';
                                     @endphp


                                     <div class="card border--none">
                                         <div class="card-header">
                                             Thông tin chi tiết gói thầu
                                         </div>

                                         <div class="card-body d-flex flex-column align-items-start infomation">

                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">Tên chủ đầu tư</div>
                                                 <div>{{ data_get($detail, 'investorName') ?? '-' }}</div>
                                             </div>

                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">Quy trình áp dụng</div>
                                                 <div>Luật Đấu thầu/ Áp dụng Luật Đấu thầu</div>
                                             </div>

                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">Tên gói thầu</div>
                                                 <div>{{ data_get($detail, 'bidName') }}</div>
                                             </div>

                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">Tóm tắt công việc chính của gói
                                                     thầu</div>
                                                 <div>{{ data_get($detail, 'generalTasks') ?? '-' }}</div>
                                             </div>

                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">Đấu thầu qua mạng</div>
                                                 <div>{{ data_get($detail, 'isInternet') ? 'Qua mạng' : 'Không' }}</div>
                                             </div>

                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">Trong nước/ Quốc tế</div>
                                                 <div>{{ data_get($detail, 'isDomestic') ? 'Trong nước' : 'Quốc tế' }}
                                                 </div>
                                             </div>

                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">Giá gói thầu</div>
                                                 <div>{{ $priceText }}</div>
                                             </div>

                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">Giá gói thầu bằng chữ</div>
                                                 <div>
                                                     <span>
                                                         {{ \App\Services\NumberToVietnameseService::convert($price) }}

                                                     </span>
                                                 </div>
                                             </div>

                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">Lĩnh vực</div>
                                                 <div>{{ $field }}</div>
                                             </div>

                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">Sơ tuyển</div>
                                                 <div>{{ data_get($detail, 'isPrequalification') ? 'Có' : 'Không' }}</div>
                                             </div>

                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">Hình thức LCNT</div>
                                                 <div>{{ $form }}</div>
                                             </div>

                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">Phương thức LCNT</div>
                                                 <div>{{ $mode }}</div>
                                             </div>

                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">Loại hợp đồng</div>
                                                 <div>{{ $ctype }}</div>
                                             </div>

                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">Chi tiết nguồn vốn</div>
                                                 <div>{{ data_get($detail, 'capitalDetail') ?? '-' }}</div>
                                             </div>

                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">Gói thầu đấu thầu trước</div>
                                                 <div>{{ data_get($detail, 'bidBefore') ? 'Có' : 'Không' }}</div>
                                             </div>

                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">Gói thầu mua sắm tập trung</div>
                                                 <div>{{ data_get($detail, 'isConcentrateShopping') ? 'Có' : 'Không' }}
                                                 </div>
                                             </div>

                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">Thời gian tổ chức LCNT</div>
                                                 <div>{{ data_get($detail, 'bidTime') ?? '-' }}</div>
                                             </div>

                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">Thời gian bắt đầu LCNT</div>
                                                 <div>
                                                     {{ data_get($detail, 'bidStartQuarter') }}
                                                     {{ data_get($detail, 'bidStartYear') }}
                                                 </div>
                                             </div>

                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">Thời gian thực hiện gói thầu</div>
                                                 <div>
                                                     {{ data_get($detail, 'cperiod') }}
                                                     <span>{{ data_get($detail, 'cperiodUnit') == 'D' ? 'ngày' : 'tháng' }}</span>
                                                 </div>
                                             </div>

                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">Địa điểm thực hiện</div>
                                                 <div>{{ $location ?: '-' }}</div>
                                             </div>

                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">Có nhiều phần/lô?</div>
                                                 <div>{{ data_get($detail, 'isMultiLot') ? 'Có' : 'Không' }}</div>
                                             </div>

                                         </div>
                                     </div>
                                 </div>

                             </div>
                         </div>
                         <div class="content-body__right">
                             <div class="detail__children-2">
                                 @php
                                     $publicDate = data_get($plan, 'publicDate');

                                     $time = $publicDate ? \Carbon\Carbon::parse($publicDate)->format('H:i') : '--:--';
                                     $date = $publicDate
                                         ? \Carbon\Carbon::parse($publicDate)->format('d/m/Y')
                                         : '--/--/----';
                                 @endphp
                                 <div class="detail__children-2__view detail__children-2__view-grey tab1 tab2">
                                     <p class="detail__children-2__text-14 detail__children-2__text-center">Thời gian đăng
                                         tải</p>
                                     <p
                                         class="detail__children-2__text-16 detail__children-2__text-orange detail__children-2__text-center">
                                         {{ $time }} </p>
                                     <p
                                         class="detail__children-2__text-20 detail__children-2__text-primary detail__children-2__text-center">
                                         {{ $date }} </p>
                                 </div>
                                 <div class="detail__children-2__view detail__children-2__view-grey tab1 tab2">
                                     <p class="detail__children-2__text-14"><!----> <span>Chủ đầu tư</span></p>
                                     <p class="detail__children-2__text-16"> {{ data_get($plan, 'investorName') }} </p>
                                     </p>
                                     <p class="detail__children-2__text-14">Số lượng gói thầu</p>
                                     <p class="detail__children-2__text-16"> {{ data_get($plan, 'bidPack') }}</p>
                                     <p class="detail__children-2__text-14">
                                         Tổng mức đầu tư
                                         <!---->
                                     </p>
                                     <p class="detail__children-2__text-16">
                                         {{ number_format(data_get($plan, 'investTotal'), 0, ',', '.') }} VND</p>
                                 </div>
                                 <div class="detail__children-2__view detail__children-2__view-primary tab1 tab2">
                                     <p class="detail__children-2__text-16-b detail__children-2__text-primary">Quyết định
                                         phê duyệt
                                     </p>
                                     <div class="detail__children-2__view__item d-flex justify-content-between"
                                         style="cursor: pointer;">
                                         <div class="d-flex" style="align-items: self-start;"><img
                                                 src="/o/egp-portal-contractor-selection-v2/images/icons/ic_pdf_file.svg"
                                                 alt="" class="content__button__icon">
                                             <p class="detail__children-2__text-14 detail__children-2__text-blue align-self-end"
                                                 style="word-break: break-all;"> {{ data_get($plan, 'decisionFileName') }}
                                             </p>
                                         </div> <img
                                             src="/o/egp-portal-contractor-selection-v2/images/icons/download_Outline.svg"
                                             alt="" class="content__button__icon">
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>




         </div>

     </div>
 @endsection

 @section('scripts-footer')
 @endsection
