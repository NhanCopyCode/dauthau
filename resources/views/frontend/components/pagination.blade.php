@props(['paginator'])

@php
    $current = $paginator->currentPage();
    $last = $paginator->lastPage();

    $pages = [];

    // Always include first & last
    $pages[] = 1;
    $pages[] = $last;

    // Include range around current
    for ($i = $current - 2; $i <= $current + 2; $i++) {
        if ($i > 1 && $i < $last) {
            $pages[] = $i;
        }
    }

    $pages = array_unique($pages);
    sort($pages);
@endphp

@if ($last > 1)
    <div class="flex flex-col items-end justify-between gap-3 mt-4">

        <!-- LEFT: PAGE SIZE -->
        <form method="GET" class="flex items-center gap-2 text-sm">
            @foreach (request()->except('per_page', 'page') as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach

            <span class="text-gray-500">Hiển thị</span>

            <select name="per_page" onchange="this.form.submit()"
                class="h-10 px-2  border border-gray-200 rounded-md text-sm w-14">
                @foreach ([10, 20, 30, 50, 100] as $size)
                    <option value="{{ $size }}" {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                        {{ $size }}
                    </option>
                @endforeach
            </select>

            <span class="text-gray-500">bản ghi / trang</span>
        </form>

        <!-- RIGHT: PAGINATION -->
        <div class="flex items-center gap-1 text-sm">

            <!-- PREV -->
            <a href="{{ $paginator->previousPageUrl() }}"
                class="px-2 h-8 flex items-center border rounded-md
            {{ $current == 1 ? 'opacity-40 pointer-events-none' : 'hover:bg-gray-100' }}">
                ‹
            </a>

            @php $prevPage = null; @endphp

            @foreach ($pages as $page)
                @if ($prevPage && $page - $prevPage > 1)
                    <span class="px-2">...</span>
                @endif

                <a href="{{ $paginator->url($page) }}"
                    class="px-3 h-8 flex items-center rounded-md border
                {{ $page == $current ? 'bg-blue-600 text-white border-blue-600' : 'hover:bg-gray-100 border-gray-200' }}">
                    {{ $page }}
                </a>

                @php $prevPage = $page; @endphp
            @endforeach

            <!-- NEXT -->
            <a href="{{ $paginator->nextPageUrl() }}"
                class="px-2 h-8 flex items-center border rounded-md
            {{ $current == $last ? 'opacity-40 pointer-events-none' : 'hover:bg-gray-100' }}">
                ›
            </a>

        </div>
    </div>
@endif
