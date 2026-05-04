@php
    $param = [
        'fileName' => $item['name'],
        'formCode' => $item['code'] ?? null,
        'id' => $notifyId,
    ];
@endphp

<tr class="hsmt-row cursor-pointer" data-url="https://muasamcong.mpi.gov.vn/egp/contractorfe/viewer"
    data-param='@json($param)'>
    <td style="width:25%">
        {{ $item['number'] }}
    </td>

    <td>
        <span class="mr-3">
            {{ $item['name'] }}
        </span>

        {{-- sau này gắn file vào đây --}}
    </td>
</tr>

@if (!empty($item['children']))
    @foreach ($item['children'] as $child)
        @include('frontend.components.hsmt-row', ['item' => $child])
    @endforeach
@endif



