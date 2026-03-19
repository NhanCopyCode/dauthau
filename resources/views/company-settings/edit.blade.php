@extends('admin.layouts.admin')
@section('htmlheader_title')
    {{ __('companies.settings') }}
@endsection
@section('contentheader_title')
    {{ __('companies.settings') }}
@endsection
@section('contentheader_description')
@endsection
@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{ url('admin') }}"><i class="fa fa-home"></i> {{ __('message.dashboard') }}</a></li>
        <li class="active">{{ __('companies.settings') }}</li>
    </ol>
@endsection

@section('main-content')
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">{{ __('message.edit_title') }}</h3>
        </div>

        {!! Form::model($setting, [
            'method' => 'PATCH',
            'url' => ['/company-settings'],
            'class' => 'form-horizontal',
            'files' => true,
        ]) !!}

        <div class="box-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <p><i class="fa fa-fw fa-check"></i> {{ $error }}</p>
                    @endforeach
                </div>
            @endif
            @foreach (\App\Models\CompanySetting::$key as $key => $default)
                <div class="form-group {{ $errors->has($key) ? 'has-error' : '' }}">
                    {!! Form::label('key', trans('companies.settings_' . $key), ['class' => 'col-md-3 control-label']) !!}
                    <div class="col-md-6">
                        @if (!empty(\App\Models\CompanySetting::$key_type[$key]))
                            {!! Form::checkbox($key, $default, isset($setting[$key]) ? $setting[$key] : $default, [
                                'class' => 'flat-blue form-control',
                            ]) !!}
                        @else
                            {!! Form::text($key, isset($setting[$key]) ? $setting[$key] : $default, ['class' => 'form-control input-sm']) !!}
                        @endif
                        {!! $errors->first($key, '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            @endforeach
        </div>
        <div class="box-footer">
            {!! Form::submit(__('message.update'), ['class' => 'btn btn-primary']) !!}
        </div>

        {!! Form::close() !!}
    </div>
@endsection
