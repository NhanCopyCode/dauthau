<div id="content-kien-nghi" class="col-md-12 p-3 snipcss-oHmJ6 mt-6 tab-content-item hidden">
    <div class="card border--none"><!---->
        <div class="card-header">
            Kiến nghị
            <!---->
        </div>
        @if ($knData->isEmpty())
            {{-- ❌ KHÔNG có dữ liệu --}}
            <div class="col-md-12 py-3 pl-3">
                <div class="text-center text-black">
                    Không có nội dung
                </div>
            </div>
        @else
            <div class="col-md-12 py-3 pl-3">
                <div>

                    @foreach ($knData as $version)
                        <div style="font-weight: 500; padding-top: 8px; padding-bottom: 10px; color: rgb(38, 38, 38);">
                            <span style="font-weight: 500;">
                                Phiên bản :
                            </span> {{ $version['version'] }}
                        </div>

                        <table class="table table-expand">
                            <thead>
                                <tr>
                                    <td scope="col" style="width: 60px; font-weight: 600;">STT</td>
                                    <td scope="col" style="width: 20%; font-weight: 600;">Loại kiến nghị</td>
                                    <td scope="col" style="width: 20%; font-weight: 600;">Tên kiến nghị</td>
                                    <td scope="col" style="font-weight: 600;">Nội dung kiến nghị</td>
                                </tr>
                            </thead>

                            <tbody style="border-top: 2px solid rgb(222, 226, 230);">

                                @foreach ($version['items'] as $index => $row)
                                    <tr>
                                        {{-- STT --}}
                                        <td>{{ $index + 1 }}</td>

                                        {{-- Loại kiến nghị (fix cứng tạm vì API không có field) --}}
                                        <td>Những vấn đề liên quan đến e-HSMT</td>

                                        {{-- Tên kiến nghị --}}
                                        <td>{{ $row['req_name'] }}</td>

                                        {{-- Nội dung --}}
                                        <td>

                                            {{-- Ngày gửi --}}
                                            @if ($row['req_date'])
                                                <span>Ngày gửi kiến nghị</span>:
                                                <span>
                                                    {{ \Carbon\Carbon::parse($row['req_date'])->format('d/m/Y') }}
                                                </span>
                                                <br>
                                            @endif

                                            {{-- Nội dung kiến nghị --}}
                                            <span>Nội dung kiến nghị :</span>
                                            <span>{{ $row['req_content'] }}</span>
                                            <br>

                                            {{-- Nội dung trả lời --}}
                                            @if ($row['res_content'])
                                                <span>Nội dung trả lời :</span>
                                                <span>{{ $row['res_content'] }}</span>
                                                <br>
                                            @endif

                                            {{-- File trả lời --}}
                                            @if ($row['res_file_name'])
                                                <span>File đính kèm nội dung trả lời :</span>
                                                <span class="text-blue-4D7AE6" style="cursor: pointer;"
                                                    onclick="downloadFile('{{ $row['res_file_id'] }}')">
                                                    {{ $row['res_file_name'] }}
                                                </span>
                                                <br>
                                            @endif

                                            {{-- Ngày trả lời --}}
                                            @if ($row['res_date'])
                                                <span>Ngày trả lời kiến nghị :</span>
                                                <span>
                                                    {{ \Carbon\Carbon::parse($row['res_date'])->format('d/m/Y') }}
                                                </span>
                                            @endif

                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    @endforeach

                </div>
            </div>

        @endif
    </div>
</div>
