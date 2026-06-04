<div id="content-clearhsmt" class="col-md-12 p-3 snipcss-oHmJ6 mt-6 tab-content-item hidden">
    <div class="card border--none"><!---->
        <div class="card-header">
            Thông tin làm rõ e-HSMT
            <!---->
        </div>
        <div class="col-md-12 py-3 pl-3 {{ count($yclrs) > 0 ? 'hidden' : '' }}">
            <div class="text-center text-black">
                Không có nội dung
            </div>
        </div>


        <div class="col-md-12 py-3">
            <div>

                @foreach ($yclrs as $index => $yclr)
                    <div style="font-weight: 500;color: black; padding: 10px 0;">
                        <span>Phiên bản : </span> {{ $yclr['version'] ?? '00' }}
                    </div>

                    @foreach ($yclr['qa_groups'] ?? [] as $group)
                        {{-- HEADER --}}
                        <div style="font-weight: 500;color: black; padding-bottom: 10px;">
                            <div>
                                <span>Tên yêu cầu làm rõ:</span>
                                {{ $group['header']['req_name'] ?? '' }}
                            </div>

                            <div>
                                <span>Ngày gửi yêu cầu:</span>
                                {{ !empty($group['header']['req_date'])
                                    ? \Carbon\Carbon::parse($group['header']['req_date'])->format('d/m/Y')
                                    : '' }}
                            </div>
                        </div>

                        {{-- TITLE --}}
                        <div style="font-weight: 500;color: black; padding-bottom: 10px;">
                            {{ $group['title'] ?? 'Nội dung hỏi đáp' }}
                        </div>

                        {{-- TABLE --}}
                        <table class="table border-dskhlc">
                            <thead>
                                <tr>
                                    <th class="table-active">Mục cần làm rõ</th>
                                    <th class="table-active">Nội dung cần làm rõ</th>
                                    <th class="table-active">Nội dung trả lời</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($group['items'] ?? [] as $qa)
                                    <tr>
                                        <td>{{ $qa['subject'] ?? '' }}</td>
                                        <td>{!! nl2br(e($qa['question'] ?? '')) !!}</td>
                                        <td>{!! nl2br(e($qa['answer'] ?? '')) !!}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- FILE + DATE --}}
                        @php
                            $qa = $group['items'][0] ?? null;
                        @endphp

                        @if ($qa)
                            @if (!empty($qa['files']['req_file_name']))
                                <div style="margin: 10px 0;">
                                    <b>File đính kèm nội dung cần làm rõ:</b>
                                    <span class="text-blue-4D7AE6">
                                        {{ $qa['files']['req_file_name'] }}
                                    </span>
                                </div>
                            @endif

                            @if (!empty($qa['files']['res_file_name']))
                                <div style="margin: 10px 0;">
                                    <b>File đính kèm nội dung trả lời:</b>
                                    <span class="text-blue-4D7AE6">
                                        {{ $qa['files']['res_file_name'] }}
                                    </span>
                                </div>
                            @endif

                            @if (!empty($qa['dates']['sign_res_date']))
                                <div style="margin: 10px 0;">
                                    <b>Ngày trả lời:</b>
                                    {{ \Carbon\Carbon::parse($qa['dates']['sign_res_date'])->format('d/m/Y') }}
                                </div>
                            @endif
                        @endif

                        @if (!$loop->last)
                            <hr style="margin: 30px 0; border-top: 1px solid #ccc;">
                        @endif
                    @endforeach
                @endforeach

            </div>
        </div>
    </div>
</div>
