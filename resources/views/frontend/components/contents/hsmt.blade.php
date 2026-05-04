<div id="content-hsmt" class="col-md-12 p-3 snipcss-oHmJ6 mt-6 tab-content-item hidden">

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

                        <a href="javascript:void(0)" class="hsmt-download tags-fileAttach"
                            data-url="https://muasamcong.mpi.gov.vn/egp/contractorfe/viewer"
                            data-param='@json($param)'>
                            Tải tất cả biểu mẫu webform
                        </a></span>
                    <span id="style-jkftm" class="style-jkftm"><span class="tags-fileAttach style-ZvloL"
                            id="style-ZvloL">Tải tất cả file đính kèm</span></span>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tree as $part)
                <tr>
                    <td colspan="2" style="padding:0">
                        <table style="width:100%">
                            <tbody>

                                {{-- PHẦN --}}
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

                                {{-- CHILD --}}
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
    <div style=" padding:20px 30px;" class="bg-white flex items-center justify-center gap-2 rounded-2xl">
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
