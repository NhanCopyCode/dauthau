{{-- <div id="content-hsmt" class="col-md-12 p-3 snipcss-oHmJ6 mt-6 tab-content-item hidden">

    <table class="table table-expand table-Stt table-hsmt">
        <thead>
            <tr id="style-eNkIZ" class="style-eNkIZ">
                <th id="style-BOwvj" class="style-BOwvj">STT</th>
                <th><span>Tên phần/ Tên chương</span> <span id="style-CMBsz" class="style-CMBsz">
                        @php
                            $param = [
                                'fileName' => 'Hồ sơ mời thầu',
                                'formCode' => 'ALL',
                                'id' => $tenderDetail->tender->notify_id,
                            ];
                        @endphp

                        <a href="javascript:void(0)" class="hsmt-download tags-fileAttach font-bold" style="font-weight: 600"
                            data-url="https://muasamcong.mpi.gov.vn/egp/contractorfe/viewer"
                            data-param='@json($param)'>
                            Tải tất cả biểu mẫu webform
                        </a></span>
                  
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tree as $part)
                <tr>
                    <td colspan="2" style="padding:0">
                        <table style="width:100%">
                            <tbody>

                                <tr>
                                    <td style="width:15%">
                                        {{ $part['number'] }}
                                    </td>

                                    <td>
                                        <span style="font-weight:bold; color:#be8a4b">
                                            {{ $part['name'] }}
                                        </span>
                                    </td>
                                </tr>

                                @foreach ($part['children'] as $child)
                                    @include('frontend.components.hsmt-row', ['item' => $child])
                                @endforeach

                            </tbody>
                        </table>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div> --}}

{{-- <div id="content-hsmt" class="col-md-12 p-3 mt-6 tab-content-item hidden">
    <table class="table table-expand table-Stt table-hsmt">
        <thead>
            <tr>
                <th style="width:15%">
                    STT
                </th>

                <th>

                    <span>
                        Tên phần/ Tên chương
                    </span>

                    @if (($hsmtView['type'] ?? null) === 'online')
                        <span style="float:right">

                            @php
                                $param = [
                                    'fileName' => 'Hồ sơ mời thầu',
                                    'formCode' => 'ALL',
                                    'id' => $tender->notify_id,
                                ];
                            @endphp

                            <a href="javascript:void(0)" class="hsmt-download tags-fileAttach font-bold"
                                style="font-weight:600" data-url="https://muasamcong.mpi.gov.vn/egp/contractorfe/viewer"
                                data-param='@json($param)'>
                                Tải tất cả biểu mẫu webform
                            </a>

                        </span>
                    @endif

                </th>

            </tr>

        </thead>

        <tbody>

            @if (empty($hsmtView))

                <tr>

                    <td colspan="2">

                        <div class="text-center py-3">
                            Không có hồ sơ mời thầu
                        </div>

                    </td>

                </tr>
            @elseif ($hsmtView['type'] === 'online')
                @foreach ($hsmtView['data'] as $part)
                    <tr>

                        <td colspan="2" style="padding:0">

                            <table style="width:100%">

                                <tbody>


                                    <tr>

                                        <td style="width:15%">

                                            {{ $part['number'] }}

                                        </td>

                                        <td>

                                            <span
                                                style="
                                                    font-weight:bold;
                                                    color:#be8a4b
                                                ">
                                                {{ $part['name'] }}
                                            </span>

                                        </td>

                                    </tr>
                                    @foreach ($part['children'] as $child)
                                        <tr>
                                            <td style="width:15%">

                                                {{ $child['number'] }}
                                            </td>
                                            <td>
                                                <div>
                                                    {{ $child['name'] }}
                                                </div>
                                                @if (!empty($child['attachments']))
                                                    <div
                                                        style="
                                                            margin-top:4px;
                                                        ">
                                                        @foreach ($child['attachments'] as $attachment)
                                                            @php
                                                                $files = $attachment['files'] ?? [];
                                                            @endphp

                                                            @if (is_array($files))
                                                                @foreach ($files as $file)
                                                                    @php
                                                                        $fileName =
                                                                            $file['fileName'] ??
                                                                            ($file['name'] ?? null);

                                                                        $fileId =
                                                                            $file['fileId'] ?? ($file['id'] ?? null);
                                                                    @endphp

                                                                    @if ($fileName)
                                                                        <div>

                                                                            <span class="file-download-all"
                                                                                style="
                                                                                    color:#0f4871;
                                                                                    cursor:pointer;
                                                                                "
                                                                                data-file-id="{{ $fileId }}">
                                                                                {{ $fileName }}
                                                                            </span>

                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        @endforeach

                                                    </div>
                                                @endif

                                            </td>

                                        </tr>
                                    @endforeach

                                </tbody>

                            </table>

                        </td>

                    </tr>
                @endforeach
            @elseif ($hsmtView['type'] === 'offline')
                @foreach ($hsmtView['data'] as $file)
                    <tr>

                        <td>

                            {{ $loop->iteration }}

                        </td>

                        <td>

                            <div
                                style="
                                    font-weight:600;
                                    margin-bottom:4px;
                                ">
                                {{ $file['label'] }}
                            </div>

                            <span class="file-download-all"
                                style="
                                    color:#0f4871;
                                    cursor:pointer;
                                "
                                data-file-id="{{ $file['file_id'] }}">
                                {{ $file['file_name'] }}
                            </span>

                        </td>

                    </tr>
                @endforeach

            @endif

        </tbody>

    </table>

</div> --}}
{{-- <div id="content-hsmt" class="col-md-12 p-3 snipcss-oHmJ6 mt-6 tab-content-item hidden">

    <table class="table table-expand table-Stt table-hsmt">

        <thead>

            <tr id="style-eNkIZ" class="style-eNkIZ">

                <th id="style-BOwvj" class="style-BOwvj">
                    STT
                </th>

                <th>

                    <span>
                        Tên phần/ Tên chương
                    </span>

                    @if (($hsmtView['type'] ?? null) === 'online')
                        <span id="style-CMBsz" class="style-CMBsz">

                            @php
                                $param = [
                                    'fileName' => 'Hồ sơ mời thầu',
                                    'formCode' => 'ALL',
                                    'id' => $tender->notify_id,
                                ];
                            @endphp

                            <a href="javascript:void(0)" class="hsmt-download tags-fileAttach font-bold"
                                style="font-weight:600" data-url="https://muasamcong.mpi.gov.vn/egp/contractorfe/viewer"
                                data-param='@json($param)'>
                                Tải tất cả biểu mẫu webform
                            </a>

                        </span>
                    @endif

                </th>

            </tr>

        </thead>

        <tbody>

            @if (empty($hsmtView))

                <tr>

                    <td colspan="2">

                        <div class="text-center py-3">

                            Không có hồ sơ mời thầu

                        </div>

                    </td>

                </tr>

            @elseif ($hsmtView['type'] === 'online')
                @foreach ($hsmtView['data'] as $part)
                    <tr>

                        <td colspan="2" style="padding:0">

                            <table style="width:100%">

                                <tbody>

                                    <tr>

                                        <td style="width:15%">

                                            {{ $part['number'] }}

                                        </td>

                                        <td>

                                            <span
                                                style="
                                                    font-weight:bold;
                                                    color:#be8a4b
                                                ">
                                                {{ $part['name'] }}
                                            </span>

                                        </td>

                                    </tr>

                                    @foreach ($part['children'] as $child)
                                        <tr>

                                            <td style="width:15%">

                                                {{ $child['number'] }}

                                            </td>

                                            <td>

                                                <div>

                                                    {{ $child['name'] }}

                                                </div>

                                                @if (!empty($child['attachments']))
                                                    <div style="margin-top:4px;">

                                                        @foreach ($child['attachments'] as $attachment)
                                                            @php
                                                                $files = $attachment['files'] ?? [];
                                                            @endphp

                                                            @if (is_array($files))
                                                                @foreach ($files as $file)
                                                                    @php

                                                                        $fileName =
                                                                            $file['fileName'] ??
                                                                            ($file['name'] ?? 'Tải file');

                                                                        $formCode = $attachment['form_code'] ?? null;

                                                                        $param = [
                                                                            'fileName' => $fileName,
                                                                            'formCode' => $formCode,
                                                                            'id' => $tender->notify_id,
                                                                        ];

                                                                    @endphp

                                                                    <div>

                                                                        <a href="javascript:void(0)"
                                                                            class="hsmt-row tags-fileAttach"
                                                                            style="
                                                                                color:#0f4871;
                                                                                cursor:pointer;
                                                                            "
                                                                            data-url="https://muasamcong.mpi.gov.vn/egp/contractorfe/viewer"
                                                                            data-param='@json($param)'>
                                                                            {{ $fileName }}
                                                                        </a>

                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        @endforeach

                                                    </div>
                                                @endif

                                            </td>

                                        </tr>
                                    @endforeach

                                </tbody>

                            </table>

                        </td>

                    </tr>
                @endforeach

              
            @elseif ($hsmtView['type'] === 'offline')
                @foreach ($hsmtView['data'] as $file)
                    @php

                        $param = [
                            'fileId' => $file['file_id'],
                            'fileName' => $file['file_name'],
                        ];

                    @endphp
                    <tr>
                        <td>
                            {{ $loop->iteration }}
                        </td>
                        <td>
                            <div
                                style="
                                    font-weight:600;
                                    margin-bottom:4px;
                                ">
                                {{ $file['label'] }}
                            </div>

                            <a href="javascript:void(0)" class="hsmt-download tags-fileAttach"
                                style="
                                    color:#0f4871;
                                    cursor:pointer;
                                "
                                data-url="https://muasamcong.mpi.gov.vn/egp/contractorfe/download"
                                data-param='@json($param)'>
                                {{ $file['file_name'] }}
                            </a>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div> --}}

<div id="content-hsmt" class="col-md-12 p-3 snipcss-oHmJ6 mt-6 tab-content-item hidden">

    <table class="table table-expand table-Stt table-hsmt">

        <thead>

            <tr id="style-eNkIZ" class="style-eNkIZ">

                <th id="style-BOwvj" class="style-BOwvj">
                    STT
                </th>

                <th>

                    <span>
                        Tên phần/ Tên chương
                    </span>

                    {{-- DOWNLOAD ALL --}}
                    @if (($hsmtView['type'] ?? null) === 'online')
                        <span id="style-CMBsz" class="style-CMBsz">

                            @php
                                $param = [
                                    'fileName' => 'Hồ sơ mời thầu',
                                    'formCode' => 'ALL',
                                    'id' => $tender->notify_id,
                                ];
                            @endphp

                            <a href="javascript:void(0)" class="hsmt-download tags-fileAttach font-bold"
                                style="font-weight:600" data-url="https://muasamcong.mpi.gov.vn/egp/contractorfe/viewer"
                                data-param='@json($param)'>
                                Tải tất cả biểu mẫu webform
                            </a>

                        </span>
                    @endif

                </th>

            </tr>

        </thead>

        <tbody>

            {{-- EMPTY --}}
            @if (empty($hsmtView))

                <tr>

                    <td colspan="2">

                        <div class="text-center py-3">

                            Không có hồ sơ mời thầu

                        </div>

                    </td>

                </tr>

                {{-- ===================================================== --}}
                {{-- ONLINE --}}
                {{-- ===================================================== --}}
            @elseif ($hsmtView['type'] === 'online')
                @foreach ($hsmtView['data'] as $part)
                    <tr>

                        <td colspan="2" style="padding:0">

                            <table style="width:100%">

                                <tbody>

                                    {{-- PART --}}
                                    <tr>

                                        <td style="width:15%">

                                            {{ $part['number'] }}

                                        </td>

                                        <td>

                                            <span
                                                style="
                                                    font-weight:bold;
                                                    color:#be8a4b
                                                ">
                                                {{ $part['name'] }}
                                            </span>

                                        </td>

                                    </tr>

                                    {{-- CHILDREN --}}
                                    @foreach ($part['children'] as $child)
                                        @php

                                            $param = [
                                                'fileName' => $child['name'],
                                                'formCode' => $child['code'] ?? null,
                                                'id' => $tender->notify_id,
                                            ];

                                        @endphp

                                        <tr>

                                            <td style="width:15%">

                                                {{ $child['number'] }}

                                            </td>

                                            <td>

                                                {{-- CLICKABLE ITEM --}}
                                                @if (!empty($child['code']))
                                                    <a href="javascript:void(0)" class="hsmt-row tags-fileAttach"
                                                        style="
                                                            color:#0f4871;
                                                            cursor:pointer;
                                                            text-decoration:none;
                                                        "
                                                        data-url="https://muasamcong.mpi.gov.vn/egp/contractorfe/viewer"
                                                        data-param='@json($param)'>
                                                        {{ $child['name'] }}
                                                    </a>
                                                @else
                                                    <span>

                                                        {{ $child['name'] }}

                                                    </span>
                                                @endif

                                                {{-- ATTACHMENT TEXT --}}
                                                @if (!empty($child['attachments_text']))
                                                    <div style="margin-top:4px">

                                                        @foreach ($child['attachments_text'] as $text)
                                                            <div>

                                                                {{ $text }}

                                                            </div>
                                                        @endforeach

                                                    </div>
                                                @endif

                                            </td>

                                        </tr>
                                    @endforeach

                                </tbody>

                            </table>

                        </td>

                    </tr>
                @endforeach

                {{-- ===================================================== --}}
                {{-- OFFLINE --}}
                {{-- ===================================================== --}}
            @elseif ($hsmtView['type'] === 'offline')
                @foreach ($hsmtView['data'] as $file)
                    @php

                        $param = [
                            'fileId' => $file['file_id'],
                            'fileName' => $file['file_name'],
                        ];

                    @endphp

                    <tr>

                        <td>

                            {{ $loop->iteration }}

                        </td>

                        <td>

                            <div
                                style="
                                    font-weight:600;
                                    margin-bottom:4px;
                                ">
                                {{ $file['label'] }}
                            </div>

                            <a href="javascript:void(0)" class="hsmt-download tags-fileAttach"
                                style="
                                    color:#0f4871;
                                    cursor:pointer;
                                "
                                data-url="https://muasamcong.mpi.gov.vn/egp/contractorfe/download"
                                data-param='@json($param)'>
                                {{ $file['file_name'] }}
                            </a>

                        </td>

                    </tr>
                @endforeach

            @endif

        </tbody>

    </table>

</div>



<div id="hsmt-loading"
    style="
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.4);
    z-index:9999;
    align-items:center;
    justify-content:center;
">
    <div style=" padding:20px 30px;" class="bg-white flex items-center justify-center gap-2 rounded-2xl text-black">
        <div class="loader"></div>
        <div style="
        border-radius:8px;
        font-weight:bold;
    ">
            Đang tải file, vui lòng chờ...
        </div>
    </div>
</div>

<script>
    let isDownloading = false;
    const loadingEl = document.getElementById("hsmt-loading");

    function showLoading() {
        loadingEl.style.display = "flex";
    }

    function hideLoading() {
        loadingEl.style.display = "none";
    }
    document.addEventListener("click", async function(e) {

        const target = e.target.closest(".hsmt-row, .hsmt-download");

        if (!target) return;

        e.preventDefault();

        if (isDownloading) return;
        isDownloading = true;

        showLoading();
        const url = target.dataset.url;
        let param;

        try {
            param = JSON.parse(target.dataset.param);
        } catch (err) {
            console.error("Param parse lỗi:", err);
            hideLoading();
            isDownloading = false;
            return;
        }

        try {
            const res = await fetch("/download-hsmt", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    url,
                    param
                })
            });

            if (!res.ok) {
                throw new Error("Download failed: " + res.status);
            }

            const blob = await res.blob();

            const a = document.createElement("a");
            a.href = URL.createObjectURL(blob);
            a.download = (param.fileName || "download") + ".pdf";
            document.body.appendChild(a);
            a.click();
            a.remove();

        } catch (err) {
            console.error(err);
            alert("Download lỗi");
        } finally {
            hideLoading();
            isDownloading = false;
        }
    });
</script>
