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
        <li class="active">{{ __('categories.category') }}</li>
    </ol>
@endsection
@section('main-content')
    <div class="box">
        <div class="content-header border-bottom pb-5">
            <h5 class="float-left">
                {{ __('message.lists') }}
            </h5>
            @can('CategoryController@store')
                <a href="{{ url('/admin/categories/create') }}" class="btn btn-default float-right"
                    title="{{ __('message.new_add') }}">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i> <span class="hidden-xs">
                        {{ __('message.new_add') }}</span>
                </a>
            @endcan
        </div>
        <div class="box-header">
            <div class="box-tools">
                {!! Form::open(['method' => 'GET', 'url' => '/admin/categories', 'class' => 'pull-left', 'role' => 'search']) !!}
                <div class="input-group" style="margin-right: 5px; display:flex;">
                    <input type="text" value="{{ \Request::get('search') }}" class="form-control input-sm" name="search"
                        placeholder="{{ __('message.search_keyword') }}" style="width: 250px; margin-right: 5px;">
                    <button class="btn btn-secondary btn-sm" type="submit">
                        <i class="fa fa-search"></i> {{ __('message.search') }}
                    </button>
                </div>
                {!! Form::close() !!}

            </div>
        </div>
        @php($index = ($categories->currentPage() - 1) * $categories->perPage())
        <div class="box-body no-padding">
            <table class="table table-bordered table-hover">
                <tbody>
                    <tr>
                        <th class="text-center" style="width: 3.5%;">
                            <input type="checkbox" name="chkAll" id="chkAll" />
                        </th>
                        <th class="text-center" style="width: 3.5%">{{ trans('message.index') }}</th>
                        <th>@sortablelink('title', trans('categories.title'))</th>
                        <th class="text-center">{{ trans('categories.slug') }}</th>
                        <th class="text-center">{{ trans('categories.description_index') }}</th>
                        <th class="text-center">{{ trans('categories.active') }}</th>
                        <th class="text-center">@sortablelink('updated_at', trans('categories.updated_at'))</th>
                        <th style="width: 7%;"></th>
                    </tr>
                    @foreach ($categories as $item)
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" name="chkId" id="chkId" value="{{ $item->id }}"
                                    data-id="{{ $item->id }}" />
                            </td>
                            <td class="text-center">{{ ++$index }}</td>
                            @can('CategoryController@show')
                                <td><a href="{{ url('/admin/categories') . '/' . $item->id }}"
                                        style="color: black;">{{ $item->name }}</a></td>
                            @endcan
                            <td class="text-center">{{ $item->slug }}</td>
                            <td class="text-center">{!! $item->description !!}</td>
                            <td class="text-center">{!! $item->active == config('settings.active') ? '<i class="fa fa-check text-primary"></i>' : '' !!}</td>
                            <td class="text-center">
                                {{ Carbon\Carbon::parse($item->updated_at)->format(config('settings.format.date')) }}</td>
                            <td class="dropdown">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="fal fa-tools"></i>
                                </button>
                                <div class="dropdown-menu p-0">
                                    @can('CategoryController@show')
                                        <a href="{{ url('/admin/categories/' . $item->id) }}"
                                            title="{{ __('message.user.view_user') }}"><button
                                                class="btn btn-info btn-sm dropdown-item"><i class="fas fa-eye"></i>
                                                {{ __('message.view') }}</button></a>
                                    @endcan
                                    @can('CategoryController@update')
                                        <a href="{{ url('/admin/categories/' . $item->id . '/edit') }}"
                                            title="{{ __('message.user.edit_user') }}"><button
                                                class="btn btn-primary btn-sm dropdown-item"><i class="far fa-edit"
                                                    aria-hidden="true"></i>
                                                {{ __('message.edit') }}</button></a>
                                    @endcan
                                    @can('CategoryController@destroy')
                                        {!! Form::open([
                                            'method' => 'DELETE',
                                            'url' => ['/admin/categories', $item->id],
                                            'style' => 'display:inline',
                                        ]) !!}
                                        {!! Form::button('<i class="far fa-trash-alt" aria-hidden="true"></i> ' . __('message.delete'), [
                                            'type' => 'submit',
                                            'class' => 'btn btn-danger btn-sm dropdown-item show_confirm',
                                            'title' => __('message.user.delete_user'),
                                            // 'onclick'=>'return confirm("'.__('message.confirm_delete').'")'
                                        ]) !!}
                                        {!! Form::close() !!}
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="box-footer clearfix">
            {!! $categories->appends(\Request::except('page'))->render() !!}
        </div>
    </div>
@endsection
