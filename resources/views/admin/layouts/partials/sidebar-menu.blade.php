<ul class="sidebar-menu" data-widget="tree">

    @foreach ($menus as $item)

        {{-- MENU KHÔNG CÓ CHILD --}}
        @if (!isset($item['child']))

            @if (!isset($item['permission']) || auth()->user()->can($item['permission']))
                <li class="{{ request()->is($item['href'].'*') ? 'active' : '' }}">
                    <a href="{{ url($item['href']) }}">
                        <i style="margin-right: 8px; display: inline-block;" class="{{ $item['icon'] }}"></i>
                        <span>{{ $item['title'] }}</span>
                    </a>
                </li>
            @endif

        @else

            @php
                $hasVisibleChild = collect($item['child'])->contains(function ($child) {
                    return !isset($child['permission']) || auth()->user()->can($child['permission']);
                });
            @endphp

            @if ($hasVisibleChild)

                <li class="treeview 
                    {{ collect($item['child'])->pluck('href')->contains(fn($url) => request()->is($url.'*')) ? 'active' : '' }}">

                    <a href="#">
                        <i style="margin-right: 8px; display: inline-block;" class="{{ $item['icon'] }}"></i>
                        <span>{{ $item['title'] }}</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-right  pull-right"></i>
                        </span>
                    </a>

                    <ul class="treeview-menu">
                        @foreach ($item['child'] as $child)

                            @if (!isset($child['permission']) || auth()->user()->can($child['permission']))
                                <li class="{{ request()->is($child['href'].'*') ? 'active' : '' }}">
                                    <a href="{{ url($child['href']) }}">
                                        <i style="margin-right: 8px; display: inline-block;" class="{{ $child['icon'] }}"></i>
                                        {{ $child['title'] }}
                                    </a>
                                </li>
                            @endif

                        @endforeach
                    </ul>
                </li>

            @endif

        @endif

    @endforeach
</ul>