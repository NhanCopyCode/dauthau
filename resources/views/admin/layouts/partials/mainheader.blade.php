<!-- Main Header -->
<header class="main-header">

    <!-- Logo -->
    <a href="{{ url('/admin') }}" class="logo" style="background:white !important">
        <img class="dashboard-image logo-lg"
            src="{{ \DB::table('settings')->where('key', 'company_logo')->value('value') != null
                ? url(\DB::table('settings')->where('key', 'company_logo')->value('value'))
                : asset('images/logoFB.png') }}"
            style="width:170px;">
    </a>

    <!-- Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">

        <!-- Toggle -->
        <a class="p-2" href="#" data-toggle="push-menu" role="button">
            <i class="fas fa-bars"></i>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">

                @guest
                    <li><a href="{{ url('/register') }}">Đăng ký</a></li>
                    <li><a href="{{ url('/login') }}">Đăng nhập</a></li>
                @else
                    {{-- LANGUAGE --}}
                    @php($languages = \App\Models\Language::getLanguages())
                    @if ($languages->count() > 1)
                        <li class="dropdown mr-3">
                            <a href="#" class="dropdown-toggle language" data-toggle="dropdown">
                                <span class="text-uppercase">{{ app()->getLocale() }}</span>
                                <i class="fal fa-globe-africa"></i>
                            </a>

                            <ul class="dropdown-menu menu" style="width: 150px;">
                                @foreach ($languages as $item)
                                    <li class="my-1">
                                        <a style="padding: 10px;"
                                            onclick="document.getElementById('locale_client').value='{{ $item->prefix }}'; document.getElementById('frmLag').submit(); return false;"
                                            href="#">
                                            <img src="{{ asset('img/' . $item->prefix . '.png') }}">
                                            &nbsp; {{ $item->name }}
                                        </a>
                                        <hr>
                                    </li>
                                @endforeach
                            </ul>

                            <form method="POST" action="{{ url('admin/change_locale') }}" id="frmLag">
                                @csrf
                                <input type="hidden" id="locale_client" name="locale_client">
                            </form>
                        </li>
                    @endif

                    {{-- USER --}}
                    <li class="dropdown user user-menu" id="user_menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">

                            {!! Auth::user()->showAvatar(['class' => 'user-image'], asset(config('settings.avatar_default'))) !!}

                            <span class="hidden-xs">{{ Auth::user()->name }}</span>
                        </a>

                        <ul class="dropdown-menu mt-2">

                            <li class="mb-2">
                                <a href="{{ url('admin/profile') }}">
                                    <i class="fa fa-user"></i> Hồ sơ
                                </a>
                            </li>

                            <li class="mt-2">
                                <a href="{{ url('/logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fa fa-sign-out"></i> Đăng xuất
                                </a>

                                <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display:none;">
                                    @csrf
                                </form>
                            </li>

                        </ul>
                    </li>

                @endguest

            </ul>
        </div>
    </nav>
</header>
