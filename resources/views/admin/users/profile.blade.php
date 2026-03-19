@extends('admin.layouts.admin')
@section('htmlheader_title')
    {{ __('message.user.profile') }}
@endsection
@section('contentheader_title')
@endsection
@section('contentheader_description')
@endsection
@section('breadcrumb')
@endsection
@section('main-content')
    <div class="box">
        <div class="box-header with-border">
            <h5>{{ __('message.user.profile') }}</h5>
        </div>

        {!! Form::model($user, [
            'method' => 'POST',
            'url' => ['/admin/profile'],
            'files' => true,
            'class' => 'form-horizontal',
        ]) !!}

        @include ('admin.users.form-profile', [
            'submitButtonText' => __('message.update'),
            'isProfile' => true,
        ])

        {!! Form::close() !!}
    </div>
@endsection
