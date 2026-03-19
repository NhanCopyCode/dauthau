@extends('admin.layouts.admin')
@section('htmlheader_title')
    {{ __('message.user.users') }}
@endsection
@section('contentheader_title')
    {{ __('message.user.users') }}
@endsection
@section('contentheader_description')
@endsection
@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="/home"><i class="fa fa-home"></i> {{ __('message.dashboard') }}</a></li>
        <li><a href="{{ url('/admin/users') }}">{{ __('message.user.users') }}</a></li>
        <li class="active">{{ __('message.edit_title') }}</li>
    </ol>
@endsection

@section('main-content')
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">{{ __('message.edit_title') }}</h3>
            <div class="box-tools">
                <a href="{{ url('/admin/users') }}" class="btn btn-default"><i class="fa fa-arrow-left"
                        aria-hidden="true"></i> <span class="hidden-xs"> {{ __('message.lists') }}</span></a>
            </div>
        </div>

        {!! Form::model($user, [
            'method' => 'PATCH',
            'url' => ['/admin/users', $user->id],
            'files' => true,
            'class' => 'form-horizontal',
        ]) !!}

        @include ('admin.users.form', ['submitButtonText' => __('message.update')])

        {!! Form::close() !!}
    </div>
@endsection
