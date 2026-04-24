 <div id="content-ttc" class="tab-content-item">
     <div id="info-general" class="tab-pane m-t-16">
         <div class="flex justify-end">
             <a href="{{ route('tenders.download', ['egp_id' => $tenderDetail->tender->egp_id]) }}"
                 class="inline-flex items-center gap-2 px-4 py-2 mb-2 text-sm font-medium text-white 
                        bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 hover:text-white
                        focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 
                        transition-all duration-200 my-4">

                 <!-- Icon download -->
                 <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M12 4v12m0 0l-4-4m4 4l4-4" />
                 </svg>

                 <span>Tải TBMT</span>
             </a>
         </div>
         <div class="card border--none">
             <div class="card-header"> Thông tin cơ bản </div>
             <div class="card-body flex flex-column align-items-start infomation">
                 <div class="flex flex-row align-items-start infomation__content">
                     <div class="flex items-start infomation__content__title"> Mã TBMT </div>
                     <div> {{ $tenderDetail->notify_no }} </div>
                 </div>
                 <div class="flex flex-row align-items-start infomation__content">
                     <div class="flex items-start infomation__content__title"> Ngày đăng tải
                     </div>
                     <div>{{ $tenderDetail->tender->public_date_label }} </div>
                 </div>
                 <div class="flex flex-row align-items-start infomation__content">
                     <div class="flex items-start infomation__content__title"> Phiên bản thay
                         đổi
                     </div>
                     <div><span class="text-blue-4D7AE6"> <select class="form-select style-ncBWd" id="style-ncBWd">
                                 <option value="5da86095-bf1c-4bb8-a5bf-f3ecd82b58cb">
                                     {{ $tenderDetail->notify_version }}</option>
                             </select> </span></div>
                 </div>
             </div>
         </div>
         <div class="card border--none">
             <div class="card-header"> Thông tin chung của KHLCNT </div>
             <div class="card-body flex flex-column align-items-start infomation">
                 <div class="flex flex-row align-items-start infomation__content">
                     <div class="flex items-start infomation__content__title"> Mã KHLCNT </div>
                     <div class="text-blue-4D7AE6 style-rBg9H" id="style-rBg9H">
                         <a href="{{ route('khlcnt.show', $tenderDetail->plan_id) }}" >
                             {{ $tenderDetail->plan_no }}
                         </a>
                     </div>
                 </div>
                 <div class="flex flex-row align-items-start infomation__content">
                     <div class="flex items-start infomation__content__title"> Phân loại KHLCNT
                     </div>
                     <div> {{ $tenderDetail->plan_type_label }} </div>
                 </div>
                 <div class="flex flex-row align-items-start infomation__content">
                     <div class="flex items-start infomation__content__title"> Tên dự toán mua
                         sắm </div>
                     <div> {{ $tenderDetail->projectName ?? $tenderDetail->plan_name }} </div>
                 </div>
             </div>
         </div>


         <div class="card border--none">
             <div class="card-header"> Thông tin gói thầu </div>

             <div class="card-body flex flex-column align-items-start infomation">

                 <div class="flex items-start infomation__content">
                     <div class="flex items-start infomation__content__title"> Tên gói thầu
                     </div>
                     <div> {{ $tenderDetail->tender->name ?? '—' }} </div>
                 </div>

                 <div class="flex items-start infomation__content">
                     <div class="flex items-start infomation__content__title"> Chủ đầu tư </div>
                     <div> {{ $tenderDetail->investor_name ?? '—' }} </div>
                 </div>

                 <div class="flex items-start infomation__content">
                     <div class="flex items-start infomation__content__title"> Chi tiết nguồn
                         vốn
                     </div>
                     <div> {{ $tenderDetail->capital_detail ?? '—' }} </div>
                 </div>

                 <div class="flex items-start infomation__content">
                     <div class="flex items-start infomation__content__title"> Lĩnh vực </div>
                     <div>{{ $tenderDetail->tender->invest_field_label ?? '—' }} </div>
                 </div>

                 <div class="flex items-start infomation__content">
                     <div class="flex items-start infomation__content__title"> Hình thức lựa
                         chọn
                         nhà thầu </div>
                     <div> {{ $tenderDetail->bid_form_label ?? '—' }} </div>
                 </div>

                 <div class="flex items-start infomation__content">
                     <div class="flex items-start infomation__content__title"> Loại hợp đồng
                     </div>
                     <div> {{ $tenderDetail->contract_type_label ?? '—' }} </div>
                 </div>

                 {{-- FIX 1: is_domestic --}}
                 <div class="flex items-start infomation__content">
                     <div class="flex items-start infomation__content__title"> Trong nước / Quốc
                         tế </div>
                     <div>
                         {{ is_null($tenderDetail->is_domestic) ? '—' : ($tenderDetail->is_domestic ? 'Trong nước' : 'Quốc tế') }}
                     </div>
                 </div>

                 {{-- FIX 2: bid_mode --}}
                 <div class="flex items-start infomation__content">
                     <div class="flex items-start infomation__content__title"> Phương thức lựa
                         chọn nhà thầu
                     </div>
                     <div> {{ $tenderDetail->bid_mode_label ?? '—' }} </div>
                 </div>

                 {{-- <div class="flex items-start infomation__content">
                                                <div class="flex items-start infomation__content__title"> Địa điểm thực
                                                    hiện
                                                    gói thầu </div>
                                                <div>
                                                    @foreach ($tenderDetail->tender->location_full as $loc)
                                                        <p>{{ $loc }}</p>
                                                    @endforeach
                                                </div>
                                            </div> --}}

                 <div class="flex items-start infomation__content">
                     <div class="flex items-start infomation__content__title">
                         Thời gian thực hiện gói thầu
                     </div>
                     <div>
                         {{ $tenderDetail->contract_period_label }}
                     </div>
                 </div>
                 <div class="flex items-start infomation__content {{ $tenderDetail->is_multi_lot > 0 ? '' : 'hidden' }}">
                     <div class="flex items-start infomation__content__title"> Gói thầu có nhiều
                         phần/lô </div>
                     <div>
                         {{ $tenderDetail->is_multi_lot_label }}
                     </div>
                 </div>

                 <div class="flex items-start infomation__content {{ $tenderDetail->is_multi_lot > 0 ? '' : 'hidden' }}">
                     <div class="flex items-start infomation__content__title"> Số lượng phần
                         (lô) </div>
                     <div>
                         {{ $tenderDetail->lot_count }}
                     </div>
                 </div>


             </div>
         </div>

         @if (!$tenderDetail->is_reoffer)
             <div class="card border--none">
                 <div class="card-header"> Cách thức dự thầu </div>

                 <div class="card-body flex flex-column align-items-start infomation">

                     <div class="flex items-start infomation__content">
                         <div class="flex items-start infomation__content__title"> Hình thức dự
                             thầu
                         </div>
                         <div>
                             {{ $tenderDetail->bid_participation_form }}
                         </div>
                     </div>

                     <div class="flex items-start infomation__content">
                         <div class="flex items-start infomation__content__title"> Địa điểm phát
                             hành e-HSMT </div>
                         <div>
                             {{ $tenderDetail->issue_location ? 'Website: ' . $tenderDetail->issue_location : '—' }}
                         </div>
                     </div>

                     <div class="flex items-start infomation__content">
                         <div class="flex items-start infomation__content__title"> Chi phí nộp
                             e-HSDT </div>
                         <div>
                             {{ $tenderDetail->bid_submission_fee ? number_format($tenderDetail->bid_submission_fee, 0, ',', '.') . ' VND' : 'Miễn phí' }}
                         </div>
                     </div>

                     <div class="flex items-start infomation__content">
                         <div class="flex items-start infomation__content__title"> Địa điểm nhận
                             e-HSDT </div>
                         <div>
                             {{ $tenderDetail->receive_location ? 'Website: ' . $tenderDetail->receive_location : '—' }}
                         </div>
                     </div>

                     <div class="flex items-start infomation__content">
                         <div class="flex items-start infomation__content__title"> Địa điểm thực
                             hiện gói thầu </div>
                         <div>
                             @foreach ($tenderDetail->tender->location_full as $loc)
                                 <p>{{ $loc }}</p>
                             @endforeach
                         </div>
                     </div>

                 </div>
             </div>
             <div class="card border--none">
                 <div class="card-header"> Thông tin dự thầu </div>

                 <div class="card-body flex flex-column align-items-start infomation">
                     <div class="flex items-start infomation__content">
                         <div class="flex items-start infomation__content__title"> Thời điểm
                             đóng
                             thầu </div>
                         <div>
                             {{ $tenderDetail->bid_close_date ? \Carbon\Carbon::parse($tenderDetail->bid_close_date)->format('d/m/Y H:i') : '—' }}
                         </div>
                     </div>

                     <div class="flex items-start infomation__content">
                         <div class="flex items-start infomation__content__title"> Thời điểm mở
                             thầu
                         </div>
                         <div>
                             {{ $tenderDetail->bid_open_date ? \Carbon\Carbon::parse($tenderDetail->bid_open_date)->format('d/m/Y H:i') : '—' }}
                         </div>
                     </div>

                     <div class="flex items-start infomation__content">
                         <div class="flex items-start infomation__content__title"> Địa điểm mở
                             thầu
                         </div>
                         <div>
                             {{ $tenderDetail->bid_open_location ?? '—' }}
                         </div>
                     </div>

                     <div class="flex items-start infomation__content">
                         <div class="flex items-start infomation__content__title"> Hiệu lực hồ
                             sơ dự
                             thầu </div>
                         <div>
                             {{ $tenderDetail->bid_validity_period ? $tenderDetail->bid_validity_period . ' ngày' : '—' }}
                         </div>
                     </div>

                     <div
                         class="flex items-start infomation__content {{ $tenderDetail->bid_guarantee_amount ? '' : 'hidden' }}">
                         <div class="infomation__content__title">
                             Số tiền bảo đảm dự thầu
                         </div>

                         <div>
                             {{ $tenderDetail->bid_guarantee_display }}

                             @if ($tenderDetail->show_bid_guarantee_policy)
                                 <span>
                                     {{ $tenderDetail->bid_guarantee_policy_text }}
                                 </span>
                             @endif
                         </div>
                     </div>

                     <div class="flex items-start infomation__content">
                         <div class="flex items-start infomation__content__title"> Hình thức đảm
                             bảo
                             dự thầu </div>
                         <div>
                             {{ $tenderDetail->bid_guarantee_form ?? '—' }}
                         </div>
                     </div>

                     @if ($tenderDetail->work_type)
                         <div class="flex items-start infomation__content">
                             <div class="flex items-start infomation__content__title"> Loại công
                                 trình</div>
                             <div>
                                 {{ $tenderDetail->work_type_name }}
                             </div>
                         </div>
                     @endif

                 </div>
             </div>

             <div class="card border--none">
                 <div class="card-header"> Thông tin quyết định phê duyệt </div>

                 <div class="card-body flex flex-column align-items-start infomation">

                     <div class="flex items-start infomation__content">
                         <div class="flex items-start infomation__content__title"> Số quyết định
                             phê
                             duyệt </div>
                         <div>
                             {{ $tenderDetail->approval_decision_number ?? '—' }}
                         </div>
                     </div>

                     <div class="flex items-start infomation__content">
                         <div class="flex items-start infomation__content__title"> Ngày phê
                             duyệt
                         </div>
                         <div>
                             {{ $tenderDetail->approval_decision_date
                                 ? \Carbon\Carbon::parse($tenderDetail->approval_decision_date)->format('d/m/Y')
                                 : '—' }}
                         </div>
                     </div>

                     <div class="flex items-start infomation__content">
                         <div class="flex items-start infomation__content__title"> Cơ quan ban
                             hành
                             quyết định </div>
                         <div>
                             {{ $tenderDetail->approval_agency ?? '—' }}
                         </div>
                     </div>

                     <div class="flex items-start infomation__content">
                         <div class="flex items-start infomation__content__title"> Quyết định
                             phê
                             duyệt </div>
                         <div>
                             @if ($tenderDetail->approval_file_name)
                                 <a href="{{ asset('storage/files/' . $tenderDetail->approval_file_name) }}"
                                     target="_blank" class="text-blue-600 hover:underline">
                                     {{ $tenderDetail->approval_file_name }}
                                 </a>
                             @else
                                 —
                             @endif
                         </div>
                     </div>

                 </div>
             </div>

             @if (!empty($tenderDetail->delay_list) && count($tenderDetail->delay_list) > 0)
                 <div class="card border--none ">
                     <div class="card-header">
                         Thông tin gia hạn
                     </div>

                     <div class="card-body item-table  w-full lg:w-[1014px] mt-5 overflow-x-auto"
                         style="padding: 0 !important;">
                         <table class="table table-notStt table-expand min-w-[800px]">
                             <thead class="thead">
                                 <tr>
                                     <th class="table-active" style="width: 6%">STT</th>
                                     <th class="table-active">Thời điểm gia hạn thành công</th>
                                     <th class="table-active">Thời điểm đóng thầu cũ</th>
                                     <th class="table-active">Thời điểm đóng thầu sau gia hạn
                                     </th>
                                     <th class="table-active">Thời điểm mở thầu cũ</th>
                                     <th class="table-active">Thời điểm mở thầu sau gia hạn</th>
                                     <th class="table-active">Lý do</th>
                                 </tr>
                             </thead>

                             <tbody>
                                 @foreach ($tenderDetail->delay_list as $index => $it)
                                     <tr>
                                         {{-- STT --}}
                                         <td class="lf-th-content">
                                             Lần {{ $index + 1 }}
                                         </td>

                                         {{-- createdDate --}}
                                         <td class="lf-th-content">
                                             {{ \Carbon\Carbon::parse($it['createdDate'])->format('d/m/Y H:i') }}
                                         </td>

                                         {{-- bidCloseDate --}}
                                         <td class="lf-th-content">
                                             {{ \Carbon\Carbon::parse($it['bidCloseDate'])->format('d/m/Y H:i') }}
                                         </td>

                                         {{-- bidCloseDelayDate --}}
                                         <td
                                             class="lf-th-content {{ ($it['createdBy'] ?? '') == 'root' ? 'font-weight-bold' : '' }}">
                                             {{ \Carbon\Carbon::parse($it['bidCloseDelayDate'])->format('d/m/Y H:i') }}
                                         </td>

                                         {{-- bidOpenDate --}}
                                         <td class="lf-th-content">
                                             {{ \Carbon\Carbon::parse($it['bidOpenDate'])->format('d/m/Y H:i') }}
                                         </td>

                                         {{-- bidOpenDelayDate --}}
                                         <td
                                             class="lf-th-content {{ ($it['createdBy'] ?? '') == 'root' ? 'font-weight-bold' : '' }}">
                                             {{ \Carbon\Carbon::parse($it['bidOpenDelayDate'])->format('d/m/Y H:i') }}
                                         </td>

                                         {{-- reason --}}
                                         <td
                                             class="lf-th-content {{ ($it['createdBy'] ?? '') == 'root' ? 'text-danger' : '' }}">
                                             {{ $it['reason'] }}

                                             @if (($it['createdBy'] ?? '') == 'root')
                                                 <br>
                                                 <span>
                                                     Lưu ý: Việc đánh giá E–HSQT, E–HSDST,
                                                     E–HSDT... trước thời điểm hệ thống gặp sự cố
                                                 </span>
                                             @endif
                                         </td>
                                     </tr>
                                 @endforeach
                             </tbody>

                         </table>
                     </div>
                 </div>
             @endif
         @else
             <div class="card border--none">
                 <div class="card-header"> Thông tin chào giá </div>

                 <div class="card-body flex flex-column align-items-start infomation">

                     <div class="flex items-start infomation__content">
                         <div class="flex items-start infomation__content__title">Thời điểm bắt
                             đầu
                             chào giá trực tuyến
                         </div>
                         <div>
                             {{ optional($tenderDetail->reoffer_start_time)->format('d/m/Y H:i') ?? '—' }}
                         </div>
                     </div>

                     <div class="flex items-start infomation__content">
                         <div class="flex items-start infomation__content__title"> Thời điểm kết
                             thúc chào giá trực tuyến
                         </div>
                         <div>
                             {{ optional($tenderDetail->reoffer_end_time)->format('d/m/Y H:i') ?? '—' }}
                         </div>
                     </div>

                     @if ($tenderDetail->is_multi_lot != 1)
                         @if ($tenderDetail->ceiling_price)
                             <div class="flex items-start infomation__content">
                                 <div class="flex items-start infomation__content__title">Giá
                                     trần </div>
                                 <div>{{ $tenderDetail->ceiling_price_display }}</div>
                             </div>
                         @endif

                         @if ($tenderDetail->price_step)
                             <div class="flex items-start infomation__content">
                                 <div class="flex items-start infomation__content__title">Bước
                                     giá </div>
                                 <div>{{ $tenderDetail->price_step_display }}</div>
                             </div>
                         @endif
                     @endif

                     <div class="flex items-start infomation__content">
                         <div class="flex items-start infomation__content__title"> Hiệu lực của
                             đơn
                             dự thầu </div>
                         <div>
                             {{ $tenderDetail->bid_validity_period_label }}
                         </div>
                     </div>

                     @if (count($tenderDetail->lot_table_rows) > 0)
                         <div class="card-body item-table" style="padding: 0px !important; max-height: max-content;">

                             <table class="table table-notStt">
                                 <thead class="thead">
                                     <tr>
                                         <th scope="col" style="width: 8%;">STT</th>

                                         @foreach ($tenderDetail->lot_table_columns as $col)
                                             <th scope="col">
                                                 {{ $col['title'] }}

                                                 {{-- thêm (VND) cho đúng UI --}}
                                                 @if (in_array($col['key'], ['price_init', 'price_step']))
                                                     (VND)
                                                 @endif
                                             </th>
                                         @endforeach
                                     </tr>
                                 </thead>

                                 @foreach ($tenderDetail->lot_table_rows as $index => $row)
                                     <tbody>
                                         <tr>
                                             <td>{{ $index + 1 }}</td>

                                             @foreach ($tenderDetail->lot_table_columns as $col)
                                                 @php
                                                     $value = $row[$col['key']] ?? null;
                                                 @endphp

                                                 <td>
                                                     @if (in_array($col['key'], ['price_init', 'price_step']) && is_numeric($value))
                                                         {{-- format giống hệ thống đấu thầu --}}
                                                         {{ number_format($value, 0, ',', '.') }}
                                                     @else
                                                         {{ $value ?? '—' }}
                                                     @endif
                                                 </td>
                                             @endforeach

                                         </tr>
                                     </tbody>
                                 @endforeach

                             </table>
                         </div>
                     @endif


                 </div>
             </div>

             <div class="card border--none">
                 <div class="card-header lg:w-[1014px]">{{ $tenderDetail->scope_title }}</div>

                 <span class="my-5" style="font-weight: 600;color: #000;">{{ $tenderDetail->scope_chapter_name }}</span>
                 <div class="pb-px-24 font-weight-bold card-body item-table w-full lg:w-[1014px]">
                     <a href="{{ route('tenders.export.excel', $tenderDetail->id) }}"
                         class="btn btn-primary button-back table-expand"
                         style="float: right; margin-top: 0px; margin-bottom: 5px;background: #be8a4b;border:none;">Xuất
                         Excel</a>
                 </div>
                 <div class="card-body item-table w-full lg:w-[1014px] ">

                     <div class="table-scroll">
                         <table class="table table-notStt">
                             <thead class="thead sticky z-20 top-0">
                                 <tr class="thead-sticky">
                                     @foreach ($tenderDetail->scope_table_columns as $col)
                                         <th style="width: {{ $tenderDetail->getScopeColumnWidth($col['key']) }}">
                                             {{ $col['title'] }}
                                         </th>
                                     @endforeach
                                 </tr>
                             </thead>

                             <tbody>
                                 @foreach ($tenderDetail->scope_table_rows as $row)
                                     {{-- ROW CHA --}}
                                     <tr class="row-parent">
                                         @foreach ($tenderDetail->scope_table_columns as $col)
                                             @php
                                                 $value = $row[$col['key']] ?? null;
                                             @endphp

                                             <td>
                                                 {{ $tenderDetail->formatScopeValue($col['key'], $value) }}
                                             </td>
                                         @endforeach
                                     </tr>

                                     {{-- ROW CON --}}
                                     @if (!empty($row['children']))
                                         @foreach ($row['children'] as $child)
                                             <tr class="row-child">
                                                 @foreach ($tenderDetail->scope_table_columns as $col)
                                                     @php
                                                         $value = $child[$col['key']] ?? null;
                                                     @endphp

                                                     <td class="pl-child">
                                                         {{ $tenderDetail->formatScopeValue($col['key'], $value) }}
                                                     </td>
                                                 @endforeach
                                             </tr>
                                         @endforeach
                                     @endif
                                 @endforeach
                             </tbody>
                         </table>
                     </div>

                 </div>
             </div>
         @endif
     </div>

 </div>
