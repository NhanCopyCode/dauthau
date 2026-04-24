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
                                         <div class="card-header">
                                             Thông tin cơ bản
                                         </div>
                                         <div class="card-body d-flex flex-column align-items-start infomation">
                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">
                                                     Mã KHLCNT
                                                 </div>
                                                 <div>
                                                     {{ data_get($plan, 'planNo') }}
                                                 </div>
                                             </div>
                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">
                                                     Tên KHLCNT
                                                 </div>
                                                 <div>
                                                     {{ data_get($plan, 'name') }}

                                                 </div>
                                             </div>
                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">
                                                     Phiên bản thay đổi
                                                 </div>
                                                 <div class="text-blue-4D7AE6"><select class="form-select"
                                                         style="border-width: medium; border-style: none; border-color: currentcolor; border-image: initial; color: rgb(77, 122, 229);">

                                                         <option value="60b00ebf-5ab8-4a37-891c-6faf6db34569">
                                                             {{ data_get($plan, 'planVersion') }}
                                                         </option>

                                                     </select></div>
                                             </div>
                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">
                                                     Trạng thái đăng tải
                                                 </div>
                                                 <div>
                                                     Đã đăng tải
                                                 </div>
                                             </div>
                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">
                                                     Tên dự toán mua sắm
                                                 </div>
                                                 <div>
                                                     {{ data_get($plan, 'pname') }}
                                                 </div>
                                             </div>
                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">
                                                     Chủ đầu tư
                                                 </div>
                                                 <div>
                                                     {{ data_get($plan, 'investorName') }}
                                                 </div>
                                             </div>


                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">
                                                     Số lượng gói thầu
                                                 </div>
                                                 <div>
                                                     {{ data_get($plan, 'bidPack') }}

                                                 </div>
                                             </div>
                                             {{-- <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">
                                                     Thời gian thực hiện dự án
                                                 </div>
                                                 <div>
                                                     3
                                                     <span>Năm</span> <!----> <!---->
                                                 </div>
                                             </div>
                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">
                                                     Nhóm dự án
                                                 </div>
                                                 <div><span> Nhóm C </span></div>
                                             </div>
                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">
                                                     Hình thức quản lý dự án
                                                 </div>
                                                 <div><span> Chủ đầu tư thuê tư vấn quản lý dự án </span></div>
                                             </div>
                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">
                                                     Có sử dụng vốn ODA
                                                 </div>
                                                 <div><!----> <span>Không</span></div>
                                             </div> <!---->
                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">
                                                     Địa điểm thực hiện
                                                 </div>
                                                 <div>
                                                     Tỉnh An Giang
                                                 </div>
                                             </div> --}}
                                         </div>
                                     </div>
                                     <div class="card border--none">
                                         <div class="card-header">
                                             Thông tin tổng mức đầu tư
                                         </div> <!----> <!---->
                                         <div class="card-body d-flex flex-column align-items-start infomation">
                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title"><!----> <span>Tổng mức đầu
                                                         tư</span></div>
                                                 <div><span>{{ number_format(data_get($plan, 'investTotal'), 0, ',', '.') }}
                                                         VND</span> <!----></div>
                                             </div> <!----> <!---->
                                             {{-- <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">
                                                     Nguồn vốn đầu tư
                                                 </div>
                                                 <div><!----> <span>Không sử dụng vốn ODA</span></div>
                                             </div>  --}}
                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">
                                                     Số tiền bằng chữ
                                                 </div>
                                                 <div>
                                                     {{ \App\Services\NumberToVietnameseService::convert(data_get($plan, 'investTotal')) }}
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                     <div class="card border--none">
                                         <div class="card-header">
                                             Thông tin quyết định phê duyệt
                                         </div>
                                         <div class="card-body d-flex flex-column align-items-start infomation">
                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">
                                                     Số quyết định phê duyệt
                                                 </div>
                                                 <div>
                                                     {{ data_get($plan, 'decisionNo') }}

                                                 </div>
                                             </div>
                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">
                                                     Ngày phê duyệt
                                                 </div>
                                                 <div>
                                                     {{ data_get($plan, 'decisionDate') }}

                                                 </div>
                                             </div>
                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">
                                                     Cơ quan ban hành quyết định
                                                 </div>
                                                 <div>
                                                     {{ data_get($plan, 'decisionAgency') }}
                                                 </div>
                                             </div>
                                             <div class="d-flex flex-row align-items-start infomation__content">
                                                 <div class="infomation__content__title">
                                                     Quyết định phê duyệt
                                                 </div>
                                                 <div>
                                                     <div class="tags-fileAttach" style="cursor: pointer;">
                                                         {{ data_get($plan, 'decisionFileName') }}

                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                     </div> <!---->
                                     <div class="card border--none card-expand view-detail">
                                         <div class="card-header">
                                             Danh sách gói thầu
                                         </div>
                                         <div class="card-body item-table" style="padding: 0px !important;"><!---->
                                             <div class="table-wrapper max-w-full md:max-w-[972px]" style=" margin-top: 20px; overflow-x: scroll;">
                                                 <table id="table-pack" class="table table-expand"
                                                     style="overflow-x: scroll;">
                                                     <thead class="thead">
                                                         <tr>
                                                             <th rowspan="2" style="width:50px">STT</th>
                                                             <th rowspan="2" style="width:150px">Tên chủ đầu tư</th>
                                                             <th colspan="2" style="width:250px">Tên gói thầu</th>
                                                             <th rowspan="2">Lĩnh vực</th>
                                                             <th rowspan="2" style="width:170px">Giá gói thầu (VND)</th>
                                                             <th rowspan="2">Chi tiết nguồn vốn</th>
                                                             <th rowspan="2">Hình thức LCNT</th>
                                                             <th rowspan="2">Phương thức LCNT</th>
                                                             <th rowspan="2">Thời gian tổ chức LCNT</th>
                                                             <th rowspan="2">Thời gian bắt đầu LCNT</th>
                                                             <th rowspan="2">Loại hợp đồng</th>
                                                             <th rowspan="2">Thời gian thực hiện</th>
                                                             <th rowspan="2">Tùy chọn mua thêm</th>
                                                             <th rowspan="2">Tình trạng TBMT</th>
                                                         </tr>
                                                         <tr>
                                                             <th>Tên gói thầu</th>
                                                             <th>Tóm tắt công việc</th>
                                                         </tr>
                                                     </thead>
                                                     <tbody>
                                                         @foreach ($packages as $index => $item)
                                                             <tr>
                                                                 <td>{{ $index + 1 }}</td>

                                                                 <td>{{ $item['investorName'] ?? '—' }}</td>

                                                                 <td>{{ $item['bidName'] ?? '—' }}</td>

                                                                 <td>{{ $item['generalTasks'] ?? '—' }}</td>

                                                                 <td>
                                                                     {{ match ($item['bidField'] ?? null) {
                                                                         'XL' => 'Xây lắp',
                                                                         'HH' => 'Hàng hóa',
                                                                         'TV' => 'Tư vấn',
                                                                         'PTV' => 'Phi tư vấn',
                                                                         default => '—',
                                                                     } }}
                                                                 </td>

                                                                 <td style="white-space: nowrap;">
                                                                     {{ number_format($item['bidPrice'] ?? 0) }}
                                                                     {{ $item['bidPriceUnit'] ?? '' }}
                                                                 </td>

                                                                 <td>{{ $item['capitalDetail'] ?? '—' }}</td>

                                                                 <td>
                                                                     {{ match ($item['bidForm'] ?? null) {
                                                                         'DTRR' => 'Đấu thầu rộng rãi',
                                                                         'CDTRG' => 'Chỉ định thầu rút gọn',
                                                                         'CHCT' => 'Chào hàng cạnh tranh',
                                                                         default => $item['bidForm'] ?? '—',
                                                                     } }}
                                                                 </td>

                                                                 <td>
                                                                     {{ match ($item['bidMode'] ?? null) {
                                                                         '1_MTHS' => 'Một giai đoạn một túi hồ sơ',
                                                                         '2_MTHS' => 'Một giai đoạn hai túi hồ sơ',
                                                                         default => '—',
                                                                     } }}
                                                                 </td>

                                                                 <td>{{ $item['bidTime'] ?? '—' }}</td>

                                                                 <td>
                                                                     @if (!empty($item['bidStartQuarter']))
                                                                         Quý
                                                                         {{ $item['bidStartQuarter'] }}/{{ $item['bidStartYear'] }}
                                                                     @else
                                                                         —
                                                                     @endif
                                                                 </td>

                                                                 <td>
                                                                     {{ match ($item['ctype'] ?? null) {
                                                                         'TG' => 'Trọn gói',
                                                                         'DGCD' => 'Đơn giá cố định',
                                                                         default => '—',
                                                                     } }}
                                                                 </td>

                                                                 <td>
                                                                     {{ $item['cperiod'] ?? 0 }}
                                                                     {{ match ($item['cperiodUnit'] ?? null) {
                                                                         'D' => 'ngày',
                                                                         'M' => 'tháng',
                                                                         'Y' => 'năm',
                                                                         default => '',
                                                                     } }}
                                                                 </td>

                                                                 <td>
                                                                     {{ $item['additionalChoise'] ? 'Có' : 'Không áp dụng' }}
                                                                 </td>

                                                                 <td class="lf-th-content">
                                                                     @php
                                                                         $link = !empty($item['linkNotifyInfo'])
                                                                             ? json_decode(
                                                                                 $item['linkNotifyInfo'],
                                                                                 true,
                                                                             )
                                                                             : null;
                                                                     @endphp

                                                                     @if (!empty($link['notifyNo']))
                                                                         <span style="color:#40a9ff;cursor:pointer;">
                                                                             Đã có TBMT
                                                                         </span>
                                                                     @else
                                                                         —
                                                                     @endif
                                                                 </td>
                                                             </tr>
                                                         @endforeach
                                                     </tbody>
                                                 </table>
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
