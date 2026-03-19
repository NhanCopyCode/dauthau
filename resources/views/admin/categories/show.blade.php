@extends('admin.layouts.admin')
@section('htmlheader_title')
    {{ __('categories.category') }}
@endsection
@section('contentheader_title')
    {{ __('categories.category') }}
@endsection
@section('contentheader_description')
@endsection
@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{ url('admin') }}"><i class="fa fa-home"></i> {{ __('message.dashboard') }}</a></li>
        <li><a href="{{ url('/admin/categories') }}">{{ __('categories.category') }}</a></li>
        <li class="active">{{ __('message.detail') }}</li>
    </ol>
@endsection
@section('main-content')
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">{{ __('message.detail') }}</h3>
            <div class="box-tools">
                <a href="{{ url('/admin/categories') }}" class="btn btn-warning btn-sm"><i class="fa fa-arrow-left"
                        aria-hidden="true"></i> <span class="hidden-xs">{{ trans('message.lists') }}</span></a>
                @can('CategoryController@update')
                    <a href="{{ url('/admin/categories/' . $category->id . '/edit') }}" class="btn btn-primary btn-sm"><i
                            class="fa fa-pencil-square-o" aria-hidden="true"></i> <span
                            class="hidden-xs">{{ __('message.edit') }}</span></a>
                @endcan
                @can('CategoryController@destroy')
                    {!! Form::open([
                        'method' => 'DELETE',
                        'url' => ['admin/categories', $category->id],
                        'style' => 'display:inline',
                    ]) !!}
                    {!! Form::button(
                        '<i class="fa fa-trash-o" aria-hidden="true"></i> <span class="hidden-xs">' . __('message.delete') . '</span>',
                        [
                            'type' => 'submit',
                            'class' => 'btn btn-danger btn-sm',
                            'title' => __('message.delete'),
                            'onclick' => 'return confirm("' . __('message.confirm_delete') . '")',
                        ],
                    ) !!}
                    {!! Form::close() !!}
                @endcan
            </div>
        </div>
        <div class="box-body table-responsive no-padding">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <th> {{ trans('theme::categories.title') }} </th>
                        <td> {{ $category->name }} </td>
                    </tr>
                    <tr>
                        <th> {{ trans('theme::categories.url') }} </th>
                        <td> <a href="">{{ $category->slug_ }}</a> </td>

                    </tr>
                    <tr>
                        <th> {{ trans('theme::categories.avatar') }} </th>
                        <td>
                            @if (!empty($category->avatar))
                                <img width="100" src="{{ asset($category->avatar) }}" alt="{{ $category->title }}" />
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th> {{ trans('theme::categories.description') }} </th>
                        <td>{!! $category->description !!}</td>
                    </tr>
                    <tr>
                        <th> {{ trans('theme::categories.updated_at') }} </th>
                        <td> {{ Carbon\Carbon::parse($category->updated_at)->format(config('settings.format.datetime')) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
