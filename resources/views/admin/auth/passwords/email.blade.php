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
                    <form class="general-form" method="POST" action="{{ route('password.email') }}">
                        {{ csrf_field() }}
                        <label for="email">Nhập email của bạn để đặt lại mật khẩu</label>
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <input id="email" type="email" class="form-control" name="email" placeholder="Email"
                                value="{{ old('email') }}" required>
                            {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
                        </div>
                        <button type="submit" class="btn btn-submit btn-lg btn-block my-4"
                            data-loading-text="<svg viewBox='0 0 32 32' width='25' height='25'>
                            <circle class='spinner' cx='16' cy='16' r='14' fill='none'></circle>
                        </svg>">
                            {{ __('adminlang.submit') }}
                        </button>
                        <p><a href="{{ url('/login') }}">{{ __('adminlang.login') }}</a></p>
                    </form>
                </div>
            </div>
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.2.0/js/bootstrap.min.js"></script>
        <script>
            $('form').submit(function() {
                $('.btn-submit').button('loading');
            });
        </script>
    </body>
@endsection
