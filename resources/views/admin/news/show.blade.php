@extends('admin.layouts.admin')
@section('htmlheader_title')
    {{ __('news.news') }}
@endsection
@section('contentheader_title')
    {{ __('news.news') }}
@endsection
@section('contentheader_description')
@endsection
@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{ url('admin') }}"><i class="fa fa-home"></i> {{ __('message.dashboard') }}</a></li>
        <li><a href="{{ url('/admin/news') }}">{{ __('news.news') }}</a></li>
        <li class="active">{{ __('message.detail') }}</li>
    </ol>
@endsection
@section('main-content')
    <div class="box">
        <div class="box-header">
            <h5 class="float-left">{{ __('message.detail') }}</h5>
            <div class="box-tools">
                <a href="{{ url('/admin/news') }}" title="{{ __('message.lists') }}" class="btn btn-default btn-sm mr-1"><i
                        class="fa fa-arrow-left"></i> <span class="hidden-xs"> {{ __('message.lists') }}</span></a>
                @can('NewsController@update')
                    <a href="{{ url('/admin/news/' . $news->id . '/edit') }}" class="btn btn-default btn-sm mr-1"><i
                            class="far fa-edit"></i> <span class="hidden-xs">
                            {{ __('message.edit') }}</span></a>
                @endcan
                @can('NewsController@destroy')
                    {!! Form::open([
                        'method' => 'DELETE',
                        'url' => ['/admin/news', $news->id],
                        'style' => 'display:inline',
                    ]) !!}
                    {!! Form::button(
                        '<i class="far fa-trash-alt"></i> <span class="hidden-xs"> ' . __('message.delete') . '</span>',
                        [
                            'type' => 'submit',
                            'class' => 'btn btn-default btn-sm',
                            'title' => 'Xoá',
                            'onclick' => 'return confirm("' . __('message.confirm_delete') . '?")',
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
                        <th style="width: 15%;"> {{ trans('theme::news.title') }} </th>
                        <td> {{ $news->{'title'} }} </td>
                    </tr>
                    {{-- <tr>
                        <th> {{ trans('theme::news.url') }} </th>
                        <td> <a href="">{{ $news->{'slug'} }}</a> </td>
                    </tr>
                <tr> --}}
                    <th> {{ trans('theme::news.category') }} </th>
                    <td> {{ !empty($news->category) ? $news->category->{'name'} : '' }} </td>
                    </tr>
                    <tr>
                        <th> {{ trans('theme::news.image') }} </th>
                        <td>
                            @if (!empty($news->image))
                                <img width="100" src="{{ asset($news->image) }}" alt="{{ $news->{'title'} }}" />
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th> {{ trans('theme::news.description') }} </th>
                        <td>{{ $news->{'description'} }}</td>
                    </tr>
                    <tr>
                        <th> {{ trans('theme::news.content') }} </th>
                        <td> {!! htmlspecialchars_decode($news->{'content_vi' . $locale}) !!} </td>
                    </tr>
                    <tr>
                        <th> {{ trans('theme::news.updated_at') }} </th>
                        <td> {{ Carbon\Carbon::parse($news->updated_at)->format(config('settings.format.datetime')) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
