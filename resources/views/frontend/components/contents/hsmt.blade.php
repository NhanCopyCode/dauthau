<div id="content-hsmt" class="col-md-12 p-3 snipcss-oHmJ6 mt-6 tab-content-item hidden">
   
    <table class="table table-expand table-Stt table-hsmt">
        <thead>
            <tr id="style-eNkIZ" class="style-eNkIZ">
                <th id="style-BOwvj" class="style-BOwvj">STT</th>
                <th><span>Tên phần/ Tên chương</span> <span id="style-CMBsz" class="style-CMBsz"><a target="_blank" href="https://muasamcong.mpi.gov.vn/egp/contractorfe/viewer?formCode=ALL&id={{$tenderDetail->tender->notify_id}}"
                            class="tags-fileAttach style-WcBAr" id="style-WcBAr">Tải tất cả biểu mẫu
                            webform</a></span>
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
