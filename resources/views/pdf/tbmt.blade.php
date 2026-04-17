<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            line-height: 1.4;
        }

        .container {
            width: 100%;
        }

        .title {
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 20px;
        }

        .section {
            margin-top: 10px;
        }

        .section-title {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
            word-wrap: break-word;
        }

        .label {
            width: 35%;
            font-weight: normal;
            background: #dddddd;
        }

        .value {
            width: 65%;
        }
    </style>
</head>

<body>

    <div class="container">

        <div class="title">
            THÔNG BÁO MỜI THẦU
        </div>

        <!-- Thông tin cơ bản -->
        <div class="section">
            <div class="section-title">Thông tin cơ bản</div>
            <table>
                <tr>
                    <td class="label">Mã TBMT</td>
                    <td class="value">{{ $tender->notify_no }}</td>
                </tr>
                <tr>
                    <td class="label">Ngày đăng tải</td>
                    <td>{{ $tender->public_date_label }}</td>
                </tr>
                <tr>
                    <td class="label">Phiên bản thay đổi</td>
                    <td>{{ $detail->notify_version }}</td>
                </tr>
            </table>
        </div>

        <!-- KHLCNT -->
        <div class="section">
            <div class="section-title">Thông tin chung của KHLCNT</div>
            <table>
                <tr>
                    <td class="label">Mã KHLCNT</td>
                    <td>{{ $detail->plan_no }}</td>
                </tr>
                <tr>
                    <td class="label">Phân loại KHLCNT</td>
                    <td>{{ $detail->plan_type_label }}</td>
                </tr>
                <tr>
                    <td class="label">Tên dự toán mua sắm</td>
                    <td>{{ $detail->plan_name }}</td>
                </tr>
            </table>
        </div>

        <!-- Gói thầu -->
        <div class="section">
            <div class="section-title">Thông tin gói thầu</div>
            <table>
                <tr>
                    <td class="label">Tên gói thầu</td>
                    <td>{{ $detail->bid_name }}</td>
                </tr>
                <tr>
                    <td class="label">Mã gói thầu</td>
                    <td>{{ $detail->bid_no }}</td>
                </tr>
                <tr>
                    <td class="label">Chủ đầu tư</td>
                    <td>{{ $detail->investor_name }}</td>
                </tr>
                <tr>
                    <td class="label">Nguồn vốn</td>
                    <td>{{ $detail->capital_detail }}</td>
                </tr>
                <tr>
                    <td class="label">Lĩnh vực</td>
                    <td>{{ $tender->invest_field_label }}</td>
                </tr>
                <tr>
                    <td class="label">Hình thức lựa chọn nhà thầu</td>
                    <td>{{ $detail->bid_form_label }}</td>
                </tr>
                <tr>
                    <td class="label">Loại hợp đồng</td>
                    <td>{{ $detail->contract_type_label }}</td>
                </tr>
                <tr>
                    <td class="label">Trong nước/ Quốc tế</td>
                    <td>{{ is_null($detail->is_domestic) ? '—' : ($detail->is_domestic ? 'Trong nước' : 'Quốc tế') }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Phương thức lựa chọn nhà thầu</td>
                    <td>{{ $detail->bid_mode_label }}</td>
                </tr>
                <tr>
                    <td class="label">Thời gian thực hiện gói thầu</td>
                    <td>{{ $detail->contract_period_label }}</td>
                </tr>
                <tr>
                    <td class="label">Gói thầu có nhiều phần/lô </td>
                    <td>{{ $detail->is_multi_lot_label }}</td>
                </tr>
                @if ($detail->is_multi_lot)
                    <tr>
                        <td class="label">Số lượng phần/lô</td>
                        <td>{{ $detail->lot_count }}</td>
                    </tr>
                @endif
            </table>
        </div>

        <!-- Cách thức dự thầu -->
        <div class="section">
            <div class="section-title">Cách thức dự thầu</div>
            <table>
                <tr>
                    <td class="label">Hình thức dự thầu</td>
                    <td>{{ $detail->bid_participation_form }}</td>
                </tr>
                <tr>
                    <td class="label">Địa điểm phát hành e-HSMT</td>
                    <td>{{ $detail->issue_location ? 'Website: ' . $detail->issue_location : '—' }}</td>
                </tr>
                <tr>
                    <td class="label">Chi phí nộp e-HSDT</td>
                    <td>
                        {{ $detail->bid_submission_fee
                            ? number_format($detail->bid_submission_fee, 0, ',', '.') . ' VND'
                            : 'Miễn phí / —' }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Địa điểm nhận e-HSDT</td>
                    <td>{{ $detail->receive_location ? 'Website: ' . $detail->receive_location : '—' }}</td>
                </tr>
                <tr>
                    <td class="label">Địa điểm thực hiện gói thầu</td>
                    <td>
                        @foreach ($tender->location_full as $loc)
                            <p>{{ $loc }}</p>
                        @endforeach
                    </td>
                </tr>
            </table>
        </div>

        <!-- Thông tin dự thầu -->
        <div class="section">
            <div class="section-title">Thông tin dự thầu</div>
            <table>
                <tr>
                    <td class="label">Thời điểm đóng thầu</td>
                    <td>{{ optional($detail->bid_close_date)->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <td class="label">Thời điểm mở thầu</td>
                    <td>{{ optional($detail->bid_open_date)->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <td class="label">Địa điểm mở thầu</td>
                    <td>{{ $detail->bid_open_location ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Hiệu lực hồ sơ dự thầu</td>
                    <td>
                        {{ $detail->bid_validity_period ? $detail->bid_validity_period . ' ngày' : '—' }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Số tiền đảm bảo dự thầu</td>
                    <td>
                        {{ $detail->bid_guarantee_display }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Hình thức đảm bảo dự thầu</td>
                    <td>{{ $detail->bid_guarantee_form ?? '—' }}</td>
                </tr>
            </table>
        </div>


        <!-- Thông tin quyết định phê duyệt -->
        <div class="section">
            <div class="section-title">Thông tin quyết định phê duyệt</div>
            <table>
                <tr>
                    <td class="label">Số quyết định phê duyệt</td>
                    <td>{{ $detail->approval_decision_number ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Ngày phê duyệt</td>
                    <td>
                        {{ $detail->approval_decision_date
                            ? \Carbon\Carbon::parse($tender->approval_decision_date)->format('d/m/Y')
                            : '—' }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Cơ quan ban hành quyết định</td>
                    <td>{{ $detail->approval_agency ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Quyết định phê duyệt</td>
                    <td>
                        @if ($detail->approval_file_name)
                            <a target="_blank" class="text-blue-600 hover:underline">
                                {{ $detail->approval_file_name }}
                            </a>
                        @else
                            —
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

</body>

</html>
