@extends('admin.layouts.admin')
@section('htmlheader_title')
    {{ __('companies.profile') }}
@endsection
@section('contentheader_title')
    {{ __('companies.profile') }}
@endsection
@section('contentheader_description')
@endsection
@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="/home"><i class="fa fa-home"></i> {{ __('message.dashboard') }}</a></li>
        <li class="active">{{ __('companies.profile') }}</li>
    </ol>
@endsection

@section('main-content')
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">{{ __('message.edit_title') }}</h3>
        </div>

        {!! Form::model($company, [
            'method' => 'POST',
            'url' => ['/companies-profile'],
            'files' => true,
            'class' => 'form-horizontal',
        ]) !!}

        @include ('company-settings.form', ['submitButtonText' => __('message.update'), 'profile' => true])

        {!! Form::close() !!}
    </div>
@endsection
