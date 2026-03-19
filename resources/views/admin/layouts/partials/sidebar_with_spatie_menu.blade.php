<aside class="main-sidebar">
    <section class="sidebar">

        {{-- USER PANEL --}}
        @auth
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="{{ Auth::user()->avatar ?? asset('images/default-avatar.png') }}" class="img-circle"
                        alt="User Image">
                </div>
                <div class="pull-left info">
                    <p>{{ Auth::user()->name }}</p>
                    <a href="#">
                        <i class="fa fa-circle text-success"></i> Online
                    </a>
                </div>
            </div>
        @endauth


        {{-- SEARCH --}}
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Tìm kiếm...">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-flat">
                        <i class="fa fa-search"></i>
                    </button>
                </span>
            </div>
        </form>


        {{-- MENU (dynamic từ DB - thay cho Menu::sidebar()) --}}
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">MENU</li>

            @if (isset($menus) && $menus->count())
                @foreach ($menus as $menu)
                    {{-- MENU CHA --}}
                    <li class="treeview {{ request()->is($menu->url . '*') ? 'active' : '' }}">
                        <a href="#">
                            <i class="{{ $menu->icon ?? 'fa fa-circle' }}"></i>
                            <span>{{ $menu->name }}</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>

                        {{-- MENU CON --}}
                        @if ($menu->children && $menu->children->count())
                            <ul class="treeview-menu">
                                @foreach ($menu->children as $child)
                                    <li class="{{ request()->is($child->url . '*') ? 'active' : '' }}">
                                        <a href="{{ url($child->url) }}">
                                            <i class="fa fa-circle-o"></i>
                                            {{ $child->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                    </li>
                @endforeach
            @else
                {{-- fallback nếu chưa có menu --}}
                <li>
                    <a href="{{ url('/admin') }}">
                        <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                    </a>
                </li>
            @endif

        </ul>

    </section>
</aside>
