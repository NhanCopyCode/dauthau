<tr>
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