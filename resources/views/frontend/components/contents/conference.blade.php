<div id="content-conference" class="col-md-12 p-3 snipcss-oHmJ6 mt-6 tab-content-item hidden">
    <div class="card border--none"><!---->
        <div class="card-header">
            Hội nghị tiền đấu thầu
            <!---->
        </div>
        <div class="col-md-12 py-3 pl-3 {{ count($hntdts) > 0 ? 'hidden' : '' }}">
            <div class="text-center">
                Không có nội dung
            </div>
        </div>
        <div>
            <div class="col-md-12 p-3">
                <div class="table-wrapper">
                    <div id="table-scroll">

                        @foreach ($hntdts as $hntdt)
                            <div
                                style="font-weight: 500; padding-top: 8px; padding-bottom: 10px; color: rgb(38, 38, 38);">
                                <span style="font-weight: 500;">
                                    Phiên bản
                                    : </span> {{ $hntdt['version'] ?? '00' }}
                            </div>

                            <table class="table border-dskhlc">
                                <thead>
                                    <tr>
                                        <th scope="col" class="table-active-st" style="width: 8% !important;">STT
                                        </th>
                                        <th scope="col" class="table-active">Nội dung giấy mời hội nghị tiền đấu thầu
                                        </th>
                                        <th scope="col" class="table-active">File đính kèm nội dung giấy mời</th>
                                        <th scope="col" class="table-active">Thời điểm đăng tải giấy mời</th>
                                        <th scope="col" class="table-active">Nội dung biên bản hội nghị tiền đấu thầu
                                        </th>
                                        <th scope="col" class="table-active">File đính kèm biên bản họp</th>
                                        <th scope="col" class="table-active">Thời điểm đăng tải biên bản họp</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($hntdt['rows'] as $row)
                                        <tr>
                                            <td class="lf-th-content">{{ $row['stt'] }}</td>

                                            <td class="lf-th-content">
                                                {{ $row['content'] }}
                                            </td>

                                            <td class="lf-th-content">
                                                @if (!empty($row['content_file_name']))
                                                    <span style="color: rgb(43, 189, 238) !important; cursor: pointer;"
                                                        onclick="downloadFile('{{ $row['content_file_id'] }}')">
                                                        {{ $row['content_file_name'] }}
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="lf-th-content">
                                                {{ \Carbon\Carbon::parse($row['content_date'])->format('d/m/Y H:i') }}
                                            </td>

                                            <td class="lf-th-content">
                                                {{ $row['report'] }}
                                            </td>

                                            <td class="lf-th-content">
                                                @if (!empty($row['report_file_name']))
                                                    <span style="color: rgb(43, 189, 238) !important; cursor: pointer;"
                                                        onclick="downloadFile('{{ $row['report_file_id'] }}')">
                                                        {{ $row['report_file_name'] }}
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="lf-th-content">
                                                {{ \Carbon\Carbon::parse($row['report_date'])->format('d/m/Y H:i') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
