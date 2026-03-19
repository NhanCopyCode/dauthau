@extends('admin.layouts.auth')

@section('htmlheader_title')
    {{ __('adminlang.passwordreset') }}
@endsection

@section('content')

    <body class="reset-page">
        <div id="app">
            <div class="reset-box">
                <div class="reset-logo border-bottom text-center py-4">
                    <img src="{{ asset($settings['company_logo']) }}" alt="logo">
                </div>
                <div class="reset-box-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    <form class="general-form" method="POST" action="{{ route('password.request') }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <input id="email" type="email" class="form-control" name="email" placeholder="Email"
                                value="{{ $email }}" required autofocus>
                            {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <input id="password" type="password" class="form-control" name="password"
                                placeholder="{{ __('adminlang.password') }}" required>
                            {!! $errors->first('password', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
                                placeholder="{{ __('adminlang.retypepassword') }}" required>
                            {!! $errors->first('password-confirm', '<p class="help-block">:message</p>') !!}
                        </div>
                        <button type="submit" class="btn btn-submit btn-lg btn-block my-4"
                            data-loading-text="<div class='loader'></div>">
                            {{ __('adminlang.passwordreset') }}
                        </button>
                        <p class="text-center">{{ __('adminlang.membreship') }} <a
                                href="{{ url('/login') }}">{{ __('adminlang.login') }}</a></p>
                    </form>
                </div>
            </div>
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.2.0/js/bootstrap.min.js"></script>
        <script>
            $('form').submit(function() {
                $('.btn-submit').button('loading');
            });
        </script>
    </body>
@endsection
