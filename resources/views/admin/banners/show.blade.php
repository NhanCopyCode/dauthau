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
        <div class="box-header">
            <h5 class="float-left">{{ __('message.detail') }}</h5>
            <div class="box-tools">
                <a href="{{ url('/admin/banners') }}" title="{{ __('message.lists') }}" class="btn btn-default btn-sm mr-1"><i
                        class="fa fa-arrow-left"></i> <span class="hidden-xs"> {{ __('message.lists') }}</span></a>
                @can('BannerController@update')
                    <a href="{{ url('/admin/banners/' . $banners->id . '/edit') }}" class="btn btn-default btn-sm mr-1"><i
                            class="far fa-edit"></i> <span class="hidden-xs"> {{ __('message.edit') }}</span></a>
                @endcan
                @can('BannerController@destroy')
                    {!! Form::open([
                        'method' => 'DELETE',
                        'url' => ['/admin/banners', $banners->id],
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
                        <th> {{ trans('sliders.name') }} </th>
                        <td> {{ $banners->name }} </td>
                    </tr>
                    <tr>
                        <th> {{ trans('sliders.name_en') }} </th>
                        <td> {{ $banners->name_en }} </td>
                    </tr>
                    <tr>
                        <th> {{ trans('sliders.link') }} </th>
                        <td>
                            @if (!empty($banners->link))
                                <a href="{{ url($banners->link) }}">{{ url($banners->link) }}</a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th> {{ trans('sliders.image') }} </th>
                        <td>
                            @if (!empty($banners->image))
                                <img width="100" src="{{ asset($banners->image) }}" alt="anh" />
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th> {{ trans('sliders.active') }} </th>
                        <td>{!! $banners->active == config('settings.active') ? '<i class="fa fa-check text-primary"></i>' : '' !!}</td>
                    </tr>
                    <tr>
                        <th> {{ trans('sliders.updated_at') }} </th>
                        <td> {{ Carbon\Carbon::parse($banners->updated_at)->format(config('settings.format.datetime')) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
