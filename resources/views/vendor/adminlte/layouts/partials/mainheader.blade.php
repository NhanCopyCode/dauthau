<!-- Main Header -->
<header class="main-header">

    <!-- Logo -->
    <a href="{{ url('/admin') }}" class="logo" style="background:white !important">
        <img class="dashboard-image logo-lg"
            src="{{ \DB::table('settings')->where('key', 'company_logo')->value('value') != null ? url(\DB::table('settings')->where('key', 'company_logo')->value('value')) : asset('images/logoFB.png') }}"
            style="width:170px;">
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a class="p-2" href="#" data-toggle="push-menu" role="button">
            <i class="fas fa-bars"></i>
        </a>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                @if (Auth::guest())
                    <li><a href="{{ url('/register') }}">{{ __('adminlang.register') }}</a></li>
                    <li><a href="{{ url('/login') }}">{{ __('adminlang.login') }}</a></li>
                @else
                    <!-- Notifications Menu -->
                    @php($languages = \App\Models\Language::getLanguages())
                    @if ($languages->count() > 1)
                        <li class="dropdown mr-3">
                            <!-- Menu Toggle Button -->
                            <a href="#" class="dropdown-toggle language" data-toggle="dropdown"
                                aria-expanded="true">
                                <span class="text-uppercase">{{ \App::getLocale() }}</span>
                                <i class="fal fa-globe-africa "></i> </a>
                            <ul class="dropdown dropdown-menu menu" style="width: 150px;">
                                @foreach ($languages as $item)
                                    <li class="my-1">
                                        <a style="padding: 10px;"
                                            onclick="document.getElementById('locale_client').value = '{{ $item->prefix }}';document.getElementById('frmLag').submit();return
                            false;"
                                            href="javascript:;">
                                            <img src="{{ asset('img/' . $item->prefix . '.png') }}" alt="">
                                            &nbsp{{ __($item->name) }}
                                        </a>
                                        <hr>
                                    </li>
                                @endforeach
                            </ul>
                            {!! Form::open([
                                'method' => 'POST',
                                'url' => 'admin/change_locale',
                                'class' => 'form-inline navbar-select',
                                'id' => 'frmLag',
                            ]) !!}
                            <input type="hidden" id="locale_client" name="locale_client" value="">
                            {!! Form::close() !!}
                        </li>
                    @endif

                    &nbsp
                    <!-- User Account Menu -->
                    <li class="dropdown user user-menu" id="user_menu">
                        <!-- Menu Toggle Button -->
                        <a href="#" class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <!-- The user image in the navbar-->
                            {!! Auth::user()->showAvatar(['class' => 'user-image'], asset(config('settings.avatar_default'))) !!}
                            <span class="hidden-xs">{{ Auth::user()->name }}</span>

                        </a>
                        <ul class="dropdown-menu mt-2">
                            <li class="mb-2">
                                <a href="{{ url('admin/profile') }}"><i class="fa fa-user"></i>
                                    {{ __('adminlang.profile') }}
                                </a>
                            </li>
                            <li class="mt-2">
                                <a href="{{ url('/logout') }}" id="logout"
                                    onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                    <i class="fa fa-sign-out"></i> {{ __('adminlang.signout') }}
                                </a>
                                <form id="logout-form" action="{{ url('/logout') }}" method="POST"
                                    style="display: none;">
                                    {{ csrf_field() }}
                                    <input type="submit" value="logout">
                                </form>
                            </li>
                        </ul>
                    </li>
            </ul>
            </li>
            @endif
            </ul>
        </div>
    </nav>
</header>
