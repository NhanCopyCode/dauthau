@extends('admin.layouts.admin')
@section('htmlheader_title')
    {{ __('sliders.slider') }}
@endsection
@section('contentheader_title')
    {{ __('sliders.slider') }}
@endsection
@section('contentheader_description')
@endsection


@section('main-content')
    <div class="box">
        <div class="box-header ">
            <h3 class="box-title">{{ __('message.new_add') }}</h3>
            <div class="box-tools">
                <a href="{{ url('/admin/banners') }}" class="btn btn-default"><i class="fa fa-arrow-left"
                        aria-hidden="true"></i> <span class="hidden-xs"> {{ __('message.lists') }}</span></a>
            </div>
        </div>
        {!! Form::open(['url' => '/admin/banners', 'class' => 'form-horizontal', 'files' => true]) !!}

        @include('admin.banners.form')

        {!! Form::close() !!}
    </div>
@endsection
