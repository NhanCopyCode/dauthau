@extends('admin.layouts.auth')
@section('htmlheader_title')
    {{ __('adminlang.register') }}
@endsection
@section('content')

    <body class="hold-transition register-page">
        <div id="app" class="register-box w-50">
            <div class="register-box-company">
                <div class="register-logo border-bottom py-4">
                    <img src="{{ asset($settings['company_logo']) }}" alt="logo">
                </div>

                <div class="register-box-body">
                    <h4 class="login-box-msg" style="color: #0052ad;text-transform: uppercase;">
                        {{ __('adminlang.registermember') }}</h4>
                    <form action="{{ url('/register') }}" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        {{-- <h4>{{ __('message.user.account_info') }}</h4> --}}
                        {{-- <h5>THÔNG TIN CÁ NHÂN</h5> --}}
                        <div class="row">
                            <div class="col-sm-6">
                                @if (config('auth.providers.users.field', 'email') === 'username')
                                    <div class="form-group has-feedback">
                                        <label for="username">{{ __('adminlang.username') }}: <font color="red">*</font>
                                            </label>
                                        <input type="text" class="form-control"
                                            placeholder="{{ __('adminlang.username') }}" required name="username"
                                            id="username" value="{{ old('username') }}" autofocus />
                                        {!! $errors->first('username', '<p class="help-block">:message</p>') !!}
                                    </div>
                                @endif
                                <div class="form-group has-feedback">
                                    <label for="password">{{ __('adminlang.password') }}: <font color="red">*</font>
                                        </label>
                                    <input type="password" class="form-control" placeholder="{{ __('adminlang.password') }}"
                                        required name="password" id="password" />
                                    {!! $errors->first('password', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="form-group has-feedback">
                                    <label for="password_confirmation">{{ __('adminlang.retypepassword') }}:
                                        <font color="red">*</font>
                                    </label>
                                    <input type="password" class="form-control"
                                        placeholder="{{ __('adminlang.retypepassword') }}" required
                                        name="password_confirmation" id="password_confirmation" />
                                    {!! $errors->first('password_confirmation', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="form-group has-feedback">
                                    <label for="name">{{ __('adminlang.fullname') }}: <font color="red">*</font>
                                        </label>
                                    <input type="text" class="form-control" placeholder="{{ __('adminlang.fullname') }}"
                                        required name="name" id="name" value="{{ old('name') }}" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group has-feedback">
                                    <label for="email">{{ __('adminlang.email') }}: <font color="red">*</font>
                                        </label>
                                    <input type="email" class="form-control" placeholder="{{ __('adminlang.email') }}"
                                        required name="email" id="email" value="{{ old('email') }}" />
                                    {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="form-group has-feedback">
                                    <label for="profile_birthday">{{ trans('message.user.birthday') }}: </label>
                                    <input type="text" class="form-control datepicker"
                                        placeholder="{{ trans('message.user.birthday') }}" name="profile[birthday]"
                                        id="profile_birthday" value="{{ old('profile.birthday') }}" autocomplete="off" />
                                </div>
                                <div class="form-group has-feedback">
                                    <label for="profile_address">{{ trans('message.user.address') }}: </label>
                                    <input type="text" class="form-control"
                                        placeholder="{{ trans('message.user.address') }}" name="profile[address]"
                                        id="profile_address" value="{{ old('profile.address') }}" />
                                </div>
                                <div class="form-group has-feedback">
                                    <label></label>
                                    <div class="checkbox_register icheck">
                                        <label>
                                            <input type="radio" name="role"
                                                {{ old('role') !== config('settings.roles.agent_admin') ? 'checked' : '' }}
                                                value="{{ config('settings.roles.customer') }}" />
                                            {{ __('message.role.role_customer') }}
                                        </label>
                                        &nbsp;&nbsp;
                                        <label>
                                            <input type="radio" name="role"
                                                {{ old('role') === config('settings.roles.agent_admin') ? 'checked' : '' }}
                                                value="{{ config('settings.roles.agent_admin') }}" />
                                            {{ __('message.role.role_agent') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group has-feedback">
                            <label>
                                <div class="checkbox_register icheck">
                                    <label>
                                        <input type="checkbox" name="terms" required />
                                    </label>
                                </div>
                            </label>
                            <button type="button" class="btn btn-link" data-toggle="modal"
                                data-target="#termsModal">{{ __('adminlang.terms') }}</button>
                        </div>
                        <button type="submit"
                            class="btn btn-register btn-lg btn-block my-4">{{ __('adminlang.register') }}</button>
                        <p class="text-center">{{ __('adminlang.membreship') }} <a
                                href="{{ url('/login') }}">{{ __('adminlang.login') }}</a></p>
                </div>
                </form>

                {{-- @include('admin.auth.partials.social_login') --}}


            </div>
        </div>

        @include('admin.layouts.partials.scripts_auth')

        @include('admin.auth.terms')

        <script>
            $(function() {
                $('input').iCheck({
                    checkboxClass: 'icheckbox_square-blue',
                    radioClass: 'iradio_square-blue',
                    increaseArea: '20%'
                });
            });

            $(function() {
                $('.datepicker').datepicker({
                    autoclose: true,
                    language: '{{ app()->getLocale() }}',
                    format: 'dd/mm/yyyy',
                    todayHighlight: true,
                });
            })
        </script>
    </body>
@endsection
