@extends('admin.layouts.admin')
@section('htmlheader_title')
    {{ __('configs.title') }}
@endsection
@section('contentheader_title')
    {{ __('configs.title') }}
@endsection
@section('contentheader_description')
@endsection
@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{ url('admin') }}"><i class="fa fa-home"></i> {{ __('message.dashboard') }}</a></li>
        <li><a href="{{ url('/admin/configs') }}">{{ __('configs.title') }}</a></li>
        <li class="active">{{ __('message.detail') }}</li>
    </ol>
@endsection
@section('main-content')
    <div class="box">
        <div class="box-header">
            <h5 class="float-left">{{ __('message.detail') }}</h5>
            <div class="box-tools">
                <a href="{{ url('/admin/configs') }}" title="{{ __('message.lists') }}"
                    class="btn btn-default btn-sm mr-1"><i class="fa fa-arrow-left"></i> <span class="hidden-xs">
                        {{ __('message.lists') }}</span></a>
                @can('NewsController@update')
                    <a href="{{ url('/admin/configs/' . $data->id . '/edit') }}" class="btn btn-default btn-sm mr-1"><i
                            class="far fa-edit"></i> <span class="hidden-xs"> {{ __('message.edit') }}</span></a>
                @endcan
                @can('NewsController@destroy')
                    {!! Form::open([
                        'method' => 'DELETE',
                        'url' => ['/admin/configs', $data->id],
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
                        <th style="width: 15%;"> {{ __('Tên cấu hình') }} </th>
                        <td> {{ optional($data->user)->name }} </td>
                    </tr>
                    <tr>
                        <th style="width: 15%;"> {{ __('Chương trình') }} </th>
                        <td> {{ optional($data->promotion)->name }} </td>
                    </tr>
                    @foreach (json_decode($data->input) as $itemI)
                        <tr>
                            <th style="width: 15%;"> {{ $itemI->text }} </th>
                            <td> {{ $itemI->type }} </td>
                        </tr>
                    @endforeach
                    <tr>
                        <th> {{ trans('configs.updated_at') }} </th>
                        <td> {{ Carbon\Carbon::parse($data->updated_at)->format(config('settings.format.datetime')) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
