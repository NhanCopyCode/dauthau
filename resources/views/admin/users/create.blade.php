@extends('admin.layouts.admin')
@section('htmlheader_title')
    {{ __('message.user.users') }}
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
            <h3 class="box-title">{{ __('message.new_add') }}</h3>
            <div class="box-tools">
                <a href="{{ url('/admin/users') }}" class="btn btn-default"><i class="fa fa-arrow-left" aria-hidden="true"></i>
                    <span class="hidden-xs"> {{ __('message.lists') }}</span></a>
            </div>
        </div>

        {!! Form::open(['url' => '/admin/users', 'class' => 'form-horizontal', 'files' => true]) !!}

        @include ('admin.users.form')

        {!! Form::close() !!}
    </div>
@endsection
