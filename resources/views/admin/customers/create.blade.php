@extends('admin.layouts.admin')
@section('htmlheader_title')
    {{ __('customers.title') }}
@endsection
@section('contentheader_title')
    {{ __('customers.title') }}
@endsection
@section('contentheader_description')
@endsection
@section('style')
    <style>
        span.select2.select2-container.select2-container--default {
            width: 100% !important;
        }
    </style>
@endsection
@section('main-content')
    <div class="box">
        <div class="box-header ">
            <h3 class="box-title">{{ __('message.new_add') }}</h3>
            <div class="box-tools">
                <a href="{{ url('admin/customers') }}" class="btn btn-default"><i class="fa fa-arrow-left"
                        aria-hidden="true"></i> <span class="hidden-xs"> {{ __('message.lists') }}</span></a>
            </div>
        </div>
        {!! Form::open(['url' => 'admin/customers', 'class' => 'form-horizontal', 'files' => true]) !!}

        @include('admin.customers.form')

        {!! Form::close() !!}
    </div>
@endsection
