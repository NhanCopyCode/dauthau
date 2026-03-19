@extends('admin.layouts.auth')

@section('htmlheader_title')
    Log in
@endsection

@section('content')

    <body class="hold-transition login-page">
        <div id="app" v-cloak>
            <div class="login-box">
                <div class="login-logo">
                    <a href="{{ url('/home') }}">{!! Config('settings.app_logo') !!}</a>
                </div><!-- /.login-logo -->

                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <p><i class="fa fa-fw fa-check"></i> {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="login-box-body">
                    <p class="login-box-msg"> {{ __('adminlang.siginsession') }} </p>

                    <login-form name="{{ config('auth.providers.users.field', 'email') }}"
                        domain="{{ config('auth.defaults.domain', '') }}"></login-form>

                    {{-- @include('admin.auth.partials.social_login') --}}

                    {{-- <a href="{{ url('/password/reset') }}">{{  __('adminlang.forgotpassword') }}</a><br> --}}
                    {{-- <a href="{{ url('/register') }}" class="text-center">{{  __('adminlang.registermember') }}</a> --}}

                </div>

            </div>
        </div>
        @include('admin.layouts.partials.scripts_auth')

        <script>
            $(function() {
                $('input').iCheck({
                    checkboxClass: 'icheckbox_square-blue',
                    radioClass: 'iradio_square-blue',
                    increaseArea: '20%' // optional
                });
            });
        </script>
    </body>

@endsection
