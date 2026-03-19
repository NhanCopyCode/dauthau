@extends('admin.layouts.admin')
@section('htmlheader_title')
    {{ __('customers.title') }}
@endsection
@section('contentheader_title')
    {{ __('customers.title') }}
@endsection
@section('contentheader_description')
@endsection
@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{ url('admin') }}"><i class="fa fa-home"></i> {{ __('message.dashboard') }}</a></li>
        <li><a href="{{ url('/admin/customers') }}">{{ __('sliders.slider') }}</a></li>
        <li class="active">{{ __('message.edit_title') }}</li>
    </ol>
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
        <div class="box-header with-border">
            <h3 class="box-title">{{ __('message.edit_title') }}</h3>
            <div class="box-tools">
                <a href="{{ url('/admin/customers') }}" class="btn btn-warning btn-sm"><i class="fa fa-arrow-left"
                        aria-hidden="true"></i> <span class="hidden-xs">{{ __('message.lists') }}</span></a>
            </div>
        </div>

        {!! Form::model($customer, [
            'method' => 'PATCH',
            'url' => ['admin/customers', $customer->id],
            'class' => 'form-horizontal',
            'files' => true,
        ]) !!}

        @include ('admin.customers.form', ['submitButtonText' => __('message.update')])

        {!! Form::close() !!}
    </div>
@endsection
