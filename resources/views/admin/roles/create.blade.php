@extends('admin.layouts.admin')
@section('htmlheader_title')
    {{ trans('message.role.roles') }}
@endsection
@section('contentheader_title')
    {{ trans('message.role.roles') }}
@endsection
@section('contentheader_description')
@endsection
@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}"><i class="fa fa-home"></i> {{ trans('message.dashboard') }}</a></li>
        <li><a href="{{ url('/admin/roles') }}">{{ trans('message.role.roles') }}</a></li>
        <li class="active">{{ trans('message.new_add') }}</li>
    </ol>
@endsection
@section('main-content')
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans('message.new_add') }}</h3>
            <div class="box-tools">
                <a href="{{ url('/admin/roles') }}" class="btn btn-default"><i class="fa fa-arrow-left"
                        aria-hidden="true"></i> <span class="hidden-xs"> {{ trans('message.lists') }}</span></a>
            </div>
        </div>

        {!! Form::open(['url' => '/admin/roles', 'class' => 'form-horizontal']) !!}

        @include ('admin.roles.form')

        {!! Form::close() !!}
    </div>
@endsection
