@extends('admin.layouts.admin')
@section('htmlheader_title')
    {{ __('message.banner') }}
@endsection
@section('contentheader_title')
    {{ __('message.banner') }}
@endsection
@section('contentheader_description')
@endsection

@section('main-content')
    <div class="box">
        <div class="content-header border-bottom pb-5">
            <h5 class="float-left">
                {{ __('message.banner') }}
            </h5>
            @can('BannerController@store')
                <div class="aa" style="float:right;">

                    <a href="{{ url('/admin/banners/create') }}" class="btn btn-default float-right"
                        title="{{ __('message.new_add') }}">
                        <i class="fa fa-plus-circle" aria-hidden="true"></i> <span class="hidden-xs">
                            {{ __('message.new_add') }}</span>
                    </a>
                </div>
            @endcan
        </div>
        <div class="box-header">
            <div class="box-tools">
                {!! Form::open(['method' => 'GET', 'url' => '/admin/banners', 'class' => 'pull-left', 'role' => 'search']) !!}
                <div class="input-group" style="margin-right: 5px; display:flex;">

                    <input type="text" value="{{ \Request::get('search') }}" class="form-control input-sm" name="search"
                        placeholder="{{ __('message.search_keyword') }}" style="width: 250px; margin-right: 5px;">
                    <button class="btn btn-default btn-sm" type="submit">
                        <i class="fa fa-search"></i> {{ __('message.search') }}
                    </button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        @php($index = ($banners->currentPage() - 1) * $banners->perPage())
        <div class="box-bodyno-padding">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <th class="text-center">{{ trans('message.index') }}</th>
                        <th class="text-center">{{ trans('sliders.image') }}</th>
                        <th>@sortablelink('title', trans('sliders.name'))</th>
                        <th>{{ trans('sliders.link') }}</th>
                        <th class="text-center">{{ trans('sliders.active') }}</th>
                        <th>@sortablelink('updated_at', trans('sliders.updated_at'))</th>
                        <th></th>
                    </tr>
                    @foreach ($banners as $item)
                        <tr>
                            <td class="text-center">{{ ++$index }}</td>
                            <td class="text-center" style="width: 10%"><img width="100" src="{{ asset($item->image) }}"
                                    alt="{{ $item->title }}" /></a></td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->link }}</td>
                            <td class="text-center">{!! $item->active == config('settings.active') ? '<i class="fa fa-check text-primary"></i>' : '' !!}</td>
                            <td>{{ Carbon\Carbon::parse($item->updated_at)->format(config('settings.format.datetime')) }}
                            </td>
                            <td class="dropdown">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="fal fa-tools"></i>
                                </button>
                                <div class="dropdown-menu p-0">
                                    @can('BannerController@show')
                                        <a href="{{ url('/admin/banners/' . $item->id) }}"
                                            title="{{ __('message.user.view_user') }}"><button
                                                class="btn btn-info btn-sm dropdown-item"><i class="fas fa-eye"></i>
                                                {{ __('message.view') }}</button></a>
                                    @endcan
                                    @can('BannerController@update')
                                        <a href="{{ url('/admin/banners/' . $item->id . '/edit') }}"
                                            title="{{ __('message.user.edit_user') }}"><button
                                                class="btn btn-primary btn-sm dropdown-item"><i class="far fa-edit"
                                                    aria-hidden="true"></i> {{ __('message.edit') }}</button></a>
                                    @endcan
                                    @can('BannerController@destroy')
                                        {!! Form::open([
                                            'method' => 'DELETE',
                                            'url' => ['/admin/banners', $item->id],
                                            'style' => 'display:inline',
                                        ]) !!}
                                        {!! Form::button('<i class="far fa-trash-alt" aria-hidden="true"></i> ' . __('message.delete'), [
                                            'type' => 'submit',
                                            'class' => 'btn btn-danger btn-sm dropdown-item',
                                            'title' => __('message.user.delete_user'),
                                            'onclick' => 'return confirm("' . __('message.confirm_delete') . '")',
                                        ]) !!}
                                        {!! Form::close() !!}
                                    @endcan
                                </div>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="box-footer clearfix">
                {!! $banners->appends(\Request::except('page'))->render() !!}
            </div>
        </div>
    </div>
    @include('sweetalert::alert')
@endsection
@section('scripts-footer')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
@endsection
