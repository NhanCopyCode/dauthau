@extends('layouts.frontend')
@section('style')
    <link rel="stylesheet" href="{{ asset('css/dauthau.css') }}">
@endsection
@section('content')
    <div class="bg-gray-50 ">


        <div class="max-w-7xl mx-auto p-4 md:p-6">
            <div class="mb-4 flex items-center justify-between">

                <!-- Back button -->
                <a href="{{ url('/') }}"
                    class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-blue-600 transition group">

                    <!-- Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-4 h-4 transition-transform duration-200 group-hover:-translate-x-1" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>

                    <span class="font-medium">Quay lại trang chủ</span>
                </a>

            </div>
            <div class="d-flex flex-col-reverse md:flex-row align-items-start content-body ">
                <div class="d-flex flex-column align-items-start content-body__left">
                    <div class="d-flex flex-column align-items-start w-100">
                        <div class="d-flex justify-content-between w-100">
                            <h5 class="gen-DLL-view-detai"> {{ $tender->tender->name }} </h5>
                            <div class="col-2 pr-0 style-J3tqA" id="style-J3tqA"> </div>
                        </div>
                        <div class="w-100">
                            <ul class="nav nav-tabs nav-scroll nav-title border--none style-6I4kv" id="style-6I4kv">
                                <li class="nav-item" style="display: flex !important;">
                                    <a data-toggle="tab" href="#tenderNotice" class="active">Thông báo mời thầu</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="tab-content">
                        <div id="tenderNotice" class="tab-pane active">
                            <ul class="nav flex-wrap gap-4 lg:nav-tabs isTag border--none">
                                <li class="nav-item"><a data-toggle="tab" href="#info-general"
                                        class="tags-tab text-nowrap active">Thông
                                        tin
                                        chung</a></li>
                                <li class="nav-item"><a data-toggle="tab" href="#file-tender-invitation"
                                        class="el-tooltip tags-tab item text-nowrap" aria-describedby="el-tooltip-5933"
                                        tabindex="0">
                                        Hồ
                                        sơ mời thầu </a></li>
                                <li class="nav-item"><a data-toggle="tab" href="#clear-HSMT" class="tags-tab text-nowrap">
                                        Làm rõ HSMT
                                    </a>
                                </li>
                                <li class="nav-item"><a data-toggle="tab" href="#conference"
                                        class="tags-tab text-nowrap">Hội nghị tiền
                                        đấu
                                        thầu</a></li>
                                <li class="nav-item"><a data-toggle="tab" href="#kien-nghi"
                                        class="tags-tab text-nowrap">Kiến nghị</a>
                                </li>
                            </ul>
                            <div id="nav-tabContent" class="tab-content">
                                <div id="info-general" class="tab-pane m-t-16 active">
                                    <a href="{{ route('tenders.download', ['egp_id' => $tender->tender->egp_id]) }}"
                                        class="mb-2"><span class="tags-fileAttach style-t7HmN" id="style-t7HmN">Tải
                                            TBMT</span></a>
                                    <div class="card border--none">
                                        <div class="card-header"> Thông tin cơ bản </div>
                                        <div class="card-body d-flex flex-column align-items-start infomation">
                                            <div class="d-flex flex-row align-items-start infomation__content">
                                                <div class="flex items-start infomation__content__title"> Mã TBMT </div>
                                                <div> {{ $tender->notify_no }} </div>
                                            </div>
                                            <div class="d-flex flex-row align-items-start infomation__content">
                                                <div class="flex items-start infomation__content__title"> Ngày đăng tải
                                                </div>
                                                <div>{{ $tender->tender->public_date_label }} </div>
                                            </div>
                                            <div class="d-flex flex-row align-items-start infomation__content">
                                                <div class="flex items-start infomation__content__title"> Phiên bản thay đổi
                                                </div>
                                                <div><span class="text-blue-4D7AE6"> <select class="form-select style-ncBWd"
                                                            id="style-ncBWd">
                                                            <option value="5da86095-bf1c-4bb8-a5bf-f3ecd82b58cb">
                                                                {{ $tender->notify_version }}</option>
                                                        </select> </span></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card border--none">
                                        <div class="card-header"> Thông tin chung của KHLCNT </div>
                                        <div class="card-body d-flex flex-column align-items-start infomation">
                                            <div class="d-flex flex-row align-items-start infomation__content">
                                                <div class="flex items-start infomation__content__title"> Mã KHLCNT </div>
                                                <div class="text-blue-4D7AE6 style-rBg9H" id="style-rBg9H">
                                                    {{ $tender->plan_no }}
                                                </div>
                                            </div>
                                            <div class="d-flex flex-row align-items-start infomation__content">
                                                <div class="flex items-start infomation__content__title"> Phân loại KHLCNT
                                                </div>
                                                <div> {{ $tender->plan_type_label }} </div>
                                            </div>
                                            <div class="d-flex flex-row align-items-start infomation__content">
                                                <div class="flex items-start infomation__content__title"> Tên dự toán mua
                                                    sắm </div>
                                                <div> {{ $tender->projectName ?? $tender->plan_name }} </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="card border--none">
                                        <div class="card-header"> Thông tin gói thầu </div>

                                        <div class="card-body d-flex flex-column align-items-start infomation">

                                            <div class="flex items-start infomation__content">
                                                <div class="flex items-start infomation__content__title"> Tên gói thầu
                                                </div>
                                                <div> {{ $tender->tender->name ?? '—' }} </div>
                                            </div>

                                            <div class="flex items-start infomation__content">
                                                <div class="flex items-start infomation__content__title"> Chủ đầu tư </div>
                                                <div> {{ $tender->investor_name ?? '—' }} </div>
                                            </div>

                                            <div class="flex items-start infomation__content">
                                                <div class="flex items-start infomation__content__title"> Chi tiết nguồn vốn
                                                </div>
                                                <div> {{ $tender->capital_detail ?? '—' }} </div>
                                            </div>

                                            <div class="flex items-start infomation__content">
                                                <div class="flex items-start infomation__content__title"> Lĩnh vực </div>
                                                <div>{{ $tender->tender->invest_field_label ?? '—' }} </div>
                                            </div>

                                            <div class="flex items-start infomation__content">
                                                <div class="flex items-start infomation__content__title"> Hình thức lựa
                                                    chọn
                                                    nhà thầu </div>
                                                <div> {{ $tender->bid_form_label ?? '—' }} </div>
                                            </div>

                                            <div class="flex items-start infomation__content">
                                                <div class="flex items-start infomation__content__title"> Loại hợp đồng
                                                </div>
                                                <div> {{ $tender->contract_type_label ?? '—' }} </div>
                                            </div>

                                            {{-- FIX 1: is_domestic --}}
                                            <div class="flex items-start infomation__content">
                                                <div class="flex items-start infomation__content__title"> Trong nước / Quốc
                                                    tế </div>
                                                <div>
                                                    {{ is_null($tender->is_domestic) ? '—' : ($tender->is_domestic ? 'Trong nước' : 'Quốc tế') }}
                                                </div>
                                            </div>

                                            {{-- FIX 2: bid_mode --}}
                                            <div class="flex items-start infomation__content">
                                                <div class="flex items-start infomation__content__title"> Phương thức lựa
                                                    chọn nhà thầu
                                                </div>
                                                <div> {{ $tender->bid_mode_label ?? '—' }} </div>
                                            </div>

                                            {{-- <div class="flex items-start infomation__content">
                                                <div class="flex items-start infomation__content__title"> Địa điểm thực
                                                    hiện
                                                    gói thầu </div>
                                                <div>
                                                    @foreach ($tender->tender->location_full as $loc)
                                                        <p>{{ $loc }}</p>
                                                    @endforeach
                                                </div>
                                            </div> --}}

                                            <div class="flex items-start infomation__content">
                                                <div class="flex items-start infomation__content__title">
                                                    Thời gian thực hiện gói thầu
                                                </div>
                                                <div>
                                                    {{ $tender->contract_period_label }}
                                                </div>
                                            </div>
                                            <div class="flex items-start infomation__content">
                                                <div class="flex items-start infomation__content__title"> Gói thầu có nhiều
                                                    phần/lô </div>
                                                <div>
                                                    {{ $tender->is_multi_lot_label }}
                                                </div>
                                            </div>

                                            <div
                                                class="flex items-start infomation__content {{ $tender->is_multi_lot ? '' : 'hidden' }}">
                                                <div class="flex items-start infomation__content__title"> Số lượng phần
                                                    (lô) </div>
                                                <div>
                                                    {{ $tender->lot_count }}
                                                </div>
                                            </div>


                                        </div>
                                    </div>

                                    @if (!$tender->is_reoffer)
                                        <div class="card border--none">
                                            <div class="card-header"> Cách thức dự thầu </div>

                                            <div class="card-body d-flex flex-column align-items-start infomation">

                                                <div class="flex items-start infomation__content">
                                                    <div class="flex items-start infomation__content__title"> Hình thức dự
                                                        thầu
                                                    </div>
                                                    <div>
                                                        {{ $tender->bid_participation_form }}
                                                    </div>
                                                </div>

                                                <div class="flex items-start infomation__content">
                                                    <div class="flex items-start infomation__content__title"> Địa điểm phát
                                                        hành e-HSMT </div>
                                                    <div>
                                                        {{ $tender->issue_location ? 'Website: ' . $tender->issue_location : '—' }}
                                                    </div>
                                                </div>

                                                <div class="flex items-start infomation__content">
                                                    <div class="flex items-start infomation__content__title"> Chi phí nộp
                                                        e-HSDT </div>
                                                    <div>
                                                        {{ $tender->bid_submission_fee
                                                            ? number_format($tender->bid_submission_fee, 0, ',', '.') . ' VND'
                                                            : 'Miễn phí' }}
                                                    </div>
                                                </div>

                                                <div class="flex items-start infomation__content">
                                                    <div class="flex items-start infomation__content__title"> Địa điểm nhận
                                                        e-HSDT </div>
                                                    <div>
                                                        {{ $tender->receive_location ? 'Website: ' . $tender->receive_location : '—' }}
                                                    </div>
                                                </div>

                                                <div class="flex items-start infomation__content">
                                                    <div class="flex items-start infomation__content__title"> Địa điểm thực
                                                        hiện gói thầu </div>
                                                    <div>
                                                        @foreach ($tender->tender->location_full as $loc)
                                                            <p>{{ $loc }}</p>
                                                        @endforeach
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="card border--none">
                                            <div class="card-header"> Thông tin dự thầu </div>

                                            <div class="card-body d-flex flex-column align-items-start infomation">
                                                <div class="flex items-start infomation__content">
                                                    <div class="flex items-start infomation__content__title"> Thời điểm
                                                        đóng
                                                        thầu </div>
                                                    <div>
                                                        {{ $tender->bid_close_date ? \Carbon\Carbon::parse($tender->bid_close_date)->format('d/m/Y H:i') : '—' }}
                                                    </div>
                                                </div>

                                                <div class="flex items-start infomation__content">
                                                    <div class="flex items-start infomation__content__title"> Thời điểm mở
                                                        thầu
                                                    </div>
                                                    <div>
                                                        {{ $tender->bid_open_date ? \Carbon\Carbon::parse($tender->bid_open_date)->format('d/m/Y H:i') : '—' }}
                                                    </div>
                                                </div>

                                                <div class="flex items-start infomation__content">
                                                    <div class="flex items-start infomation__content__title"> Địa điểm mở
                                                        thầu
                                                    </div>
                                                    <div>
                                                        {{ $tender->bid_open_location ?? '—' }}
                                                    </div>
                                                </div>

                                                <div class="flex items-start infomation__content">
                                                    <div class="flex items-start infomation__content__title"> Hiệu lực hồ
                                                        sơ dự
                                                        thầu </div>
                                                    <div>
                                                        {{ $tender->bid_validity_period ? $tender->bid_validity_period . ' ngày' : '—' }}
                                                    </div>
                                                </div>

                                                <div
                                                    class="flex items-start infomation__content {{ $tender->bid_guarantee_amount ? '' : 'hidden' }}">
                                                    <div class="infomation__content__title">
                                                        Số tiền bảo đảm dự thầu
                                                    </div>

                                                    <div>
                                                        {{ $tender->bid_guarantee_display }}

                                                        @if ($tender->show_bid_guarantee_policy)
                                                            <span>
                                                                {{ $tender->bid_guarantee_policy_text }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="flex items-start infomation__content">
                                                    <div class="flex items-start infomation__content__title"> Hình thức đảm
                                                        bảo
                                                        dự thầu </div>
                                                    <div>
                                                        {{ $tender->bid_guarantee_form ?? '—' }}
                                                    </div>
                                                </div>

                                                @if ($tender->work_type)
                                                    <div class="flex items-start infomation__content">
                                                        <div class="flex items-start infomation__content__title"> Loại công
                                                            trình</div>
                                                        <div>
                                                            {{ $tender->work_type_name }}
                                                        </div>
                                                    </div>
                                                @endif

                                            </div>
                                        </div>

                                        <div class="card border--none">
                                            <div class="card-header"> Thông tin quyết định phê duyệt </div>

                                            <div class="card-body d-flex flex-column align-items-start infomation">

                                                <div class="flex items-start infomation__content">
                                                    <div class="flex items-start infomation__content__title"> Số quyết định
                                                        phê
                                                        duyệt </div>
                                                    <div>
                                                        {{ $tender->approval_decision_number ?? '—' }}
                                                    </div>
                                                </div>

                                                <div class="flex items-start infomation__content">
                                                    <div class="flex items-start infomation__content__title"> Ngày phê
                                                        duyệt
                                                    </div>
                                                    <div>
                                                        {{ $tender->approval_decision_date
                                                            ? \Carbon\Carbon::parse($tender->approval_decision_date)->format('d/m/Y')
                                                            : '—' }}
                                                    </div>
                                                </div>

                                                <div class="flex items-start infomation__content">
                                                    <div class="flex items-start infomation__content__title"> Cơ quan ban
                                                        hành
                                                        quyết định </div>
                                                    <div>
                                                        {{ $tender->approval_agency ?? '—' }}
                                                    </div>
                                                </div>

                                                <div class="flex items-start infomation__content">
                                                    <div class="flex items-start infomation__content__title"> Quyết định
                                                        phê
                                                        duyệt </div>
                                                    <div>
                                                        @if ($tender->approval_file_name)
                                                            <a href="{{ asset('storage/files/' . $tender->approval_file_name) }}"
                                                                target="_blank" class="text-blue-600 hover:underline">
                                                                {{ $tender->approval_file_name }}
                                                            </a>
                                                        @else
                                                            —
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        @if (!empty($tender->delay_list) && count($tender->delay_list) > 0)
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
                                                            @foreach ($tender->delay_list as $index => $it)
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

                                            <div class="card-body d-flex flex-column align-items-start infomation">

                                                <div class="flex items-start infomation__content">
                                                    <div class="flex items-start infomation__content__title">Thời điểm bắt
                                                        đầu
                                                        chào giá trực tuyến
                                                    </div>
                                                    <div>
                                                        {{ optional($tender->reoffer_start_time)->format('d/m/Y H:i') ?? '—' }}
                                                    </div>
                                                </div>

                                                <div class="flex items-start infomation__content">
                                                    <div class="flex items-start infomation__content__title"> Thời điểm kết
                                                        thúc chào giá trực tuyến
                                                    </div>
                                                    <div>
                                                        {{ optional($tender->reoffer_end_time)->format('d/m/Y H:i') ?? '—' }}
                                                    </div>
                                                </div>

                                                @if ($tender->is_multi_lot != 1)
                                                    @if ($tender->ceiling_price)
                                                        <div class="flex items-start infomation__content">
                                                            <div class="flex items-start infomation__content__title">Giá
                                                                trần </div>
                                                            <div>{{ $tender->ceiling_price_display }}</div>
                                                        </div>
                                                    @endif

                                                    @if ($tender->price_step)
                                                        <div class="flex items-start infomation__content">
                                                            <div class="flex items-start infomation__content__title">Bước
                                                                giá </div>
                                                            <div>{{ $tender->price_step_display }}</div>
                                                        </div>
                                                    @endif
                                                @endif

                                                <div class="flex items-start infomation__content">
                                                    <div class="flex items-start infomation__content__title"> Hiệu lực của
                                                        đơn
                                                        dự thầu </div>
                                                    <div>
                                                        {{ $tender->bid_validity_period_label }}
                                                    </div>
                                                </div>

                                                @if (count($tender->lot_table_rows) > 0)
                                                    <div class="card-body item-table"
                                                        style="padding: 0px !important; max-height: max-content;">

                                                        <table class="table table-notStt">
                                                            <thead class="thead">
                                                                <tr>
                                                                    <th scope="col" style="width: 8%;">STT</th>

                                                                    @foreach ($tender->lot_table_columns as $col)
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

                                                            @foreach ($tender->lot_table_rows as $index => $row)
                                                                <tbody>
                                                                    <tr>
                                                                        <td>{{ $index + 1 }}</td>

                                                                        @foreach ($tender->lot_table_columns as $col)
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
                                            <div class="card-header lg:w-[1014px]">{{ $tender->scope_title }}</div>

                                            <span class="my-5"
                                                style="font-weight: 600;color: #000;">{{ $tender->scope_chapter_name }}</span>
                                            <div
                                                class="pb-px-24 font-weight-bold card-body item-table w-full lg:w-[1014px]">
                                                <a href="{{ route('tenders.export.excel', $tender->id) }}"
                                                    class="btn btn-primary button-back table-expand"
                                                    style="float: right; margin-top: 0px; margin-bottom: 5px;background: #be8a4b;border:none;">Xuất
                                                    Excel</a>
                                            </div>
                                            <div class="card-body item-table w-full lg:w-[1014px] ">

                                                <div class="table-scroll">
                                                    <table class="table table-notStt">
                                                        <thead class="thead sticky z-20 top-0">
                                                            <tr class="thead-sticky">
                                                                @foreach ($tender->scope_table_columns as $col)
                                                                    <th
                                                                        style="width: {{ $tender->getScopeColumnWidth($col['key']) }}">
                                                                        {{ $col['title'] }}
                                                                    </th>
                                                                @endforeach
                                                            </tr>
                                                        </thead>

                                                        <tbody>
                                                            @foreach ($tender->scope_table_rows as $row)
                                                                {{-- ROW CHA --}}
                                                                <tr class="row-parent">
                                                                    @foreach ($tender->scope_table_columns as $col)
                                                                        @php
                                                                            $value = $row[$col['key']] ?? null;
                                                                        @endphp

                                                                        <td>
                                                                            {{ $tender->formatScopeValue($col['key'], $value) }}
                                                                        </td>
                                                                    @endforeach
                                                                </tr>

                                                                {{-- ROW CON --}}
                                                                @if (!empty($row['children']))
                                                                    @foreach ($row['children'] as $child)
                                                                        <tr class="row-child">
                                                                            @foreach ($tender->scope_table_columns as $col)
                                                                                @php
                                                                                    $value =
                                                                                        $child[$col['key']] ?? null;
                                                                                @endphp

                                                                                <td class="pl-child">
                                                                                    {{ $tender->formatScopeValue($col['key'], $value) }}
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
                                <div id="file-tender-invitation" class="tab-pane m-t-16 fade">
                                    <div>
                                        <div class="col-md-12 pl-0 pr-0">
                                            <div class="mb-3 style-bbkZC" id="style-bbkZC"><span
                                                    class="tags-fileAttach style-MJ1F2" id="style-MJ1F2">Tải tất cả file
                                                    đính
                                                    kèm</span></div>
                                            <table class="table table-expand table-Stt">
                                                <tbody>
                                                    <tr>
                                                        <td>Hồ sơ mời thầu</td>
                                                        <td><span id="undefined,undefined"
                                                                class="file-download-all style-sAEqc"> </span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Nội dung đính kèm khác</td>
                                                        <td> </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div id="clear-HSMT" class="tab-pane m-t-16 fade">
                                    <div class="card border--none">
                                        <div class="card-header"> Thông tin làm rõ e-HSMT </div>
                                        <div class="col-md-12 py-3 pl-3">
                                            <div class="text-center"> Không có nội dung </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="conference" class="tab-pane m-t-16 fade">
                                    <div class="card border--none">
                                        <div class="card-header"> Hội nghị tiền đấu thầu </div>
                                        <div class="col-md-12 py-3 pl-3">
                                            <div class="text-center"> Không có nội dung </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="kien-nghi" class="tab-pane m-t-16 fade">
                                    <div class="card border--none">
                                        <div class="card-header"> Kiến nghị </div>
                                        <div class="col-md-12 py-3 pl-3">
                                            <div class="text-center"> Không có nội dung kiến nghị </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="dsntdkt" class="tab-pane fade"></div>
                        <div id="contractorSelectionResults" class="tab-pane fade"> </div>
                    </div>
                </div>
                <div class="md:content-body__right w-full">
                    <div class="detail__children-2 flex flex-col gap-6">

                        <div class="px-4 py-6 md:detail__children-2__view detail__children-2__view-grey">

                            <p class="detail__children-2__text-14 text-center">
                                Thời điểm đóng thầu
                            </p>

                            <p class="detail__children-2__text-16 text-orange-500 text-center">
                                {{ $tender->effective_close_time_formatted ?? '—' }}
                            </p>

                            <p class="detail__children-2__text-20 text-primary text-center">
                                {{ $tender->effective_close_date_formatted ?? '—' }}
                            </p>

                            <p class="text-sm text-orange-500 mt-4 text-center">
                                <span>Còn lại</span>:
                                {{ $tender->effective_countdown ?? '—' }}
                            </p>

                        </div>

                        <div class="px-4 py-6 md:detail__children-2__view detail__children-2__view-warning">

                            <p class="text-base text-orange-500 font-bold">
                                Khuyến nghị việc tham dự thầu:
                            </p>

                            <p class="text-sm mt-2">
                                Khuyến nghị nhà thầu cần nộp thầu sớm để có thời gian khắc phục,
                                hỗ trợ trong trường hợp lỗi kỹ thuật hoặc sự cố gần thời điểm đóng thầu.
                            </p>

                        </div>

                    </div>
                </div>


            </div>
        </div>

    </div>
@endsection

@section('scripts-footer')
    <script>
        axios.get(`/tenders/${$tender->tender->egp_id}/download`, {
            responseType: 'blob'
        }).then(res => {
            const url = window.URL.createObjectURL(new Blob([res.data]));
            const a = document.createElement('a');
            a.href = url;
            a.download = 'Thông báo mời thầu.pdf';
            a.click();
        });
    </script>
@endsection
