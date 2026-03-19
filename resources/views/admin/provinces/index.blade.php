@extends('admin.layouts.admin')
@section('style')
    @toastr_css
    <style>
        .select2 {
            width: 250px;
        }

        ul.pagination {
            float: right;
        }
    </style>
@endsection
@section('htmlheader_title')
    {{ __('Tỉnh') }}
@endsection
@section('contentheader_title')
    {{ __('Tỉnh') }}
@endsection
@section('contentheader_description')
@endsection
@section('main-content')
    <div class="box">
        <div class="content-header border-bottom pb-5">
            <h5 class="float-left">
                {{ __('Danh sách') }}
            </h5>
            @can('ProvinceController@store')
                <a href="{{ url('/admin/provinces/create') }}" class="btn btn-default float-right"
                    title="{{ __('message.new_add') }}">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i> <span class="hidden-xs">
                        {{ __('message.new_add') }}</span>
                </a>
            @endcan
        </div>
        <div class="box-header">
            <div class="box-tools" style="display: flex;">

                {!! Form::open(['method' => 'GET', 'url' => '/admin/provinces', 'class' => 'pull-left', 'role' => 'search']) !!}
                <div class="input-group">
                    <input type="text" value="{{ \Request::get('search') }}" class="form-control input-sm" name="search"
                        placeholder="{{ __('message.search_keyword') }}" width="200">
                    <span class="input-group-btn">
                        <button class="btn btn-default " type="submit">
                            <i class="fa fa-search"></i> {{ __('message.search') }}
                        </button>
                    </span>
                </div>
                {!! Form::close() !!}

            </div>
        </div>
        @php($index = ($data->currentPage() - 1) * $data->perPage())
        <div class="box-body table-responsive no-padding">
            <table class="table table-bordered table-hover">
                <tbody>
                    <tr>
                        <th class="text-center">{{ trans('message.index') }}</th>
                        <th class="text-center"width="5%">{{ __('Code') }}</th>
                        <th width="15%" class="text-left">@sortablelink('name', __('Tên'))</th>
                        <th width="15%" class="text-center">@sortablelink('updated_at', __('Ngày cập nhật'))</th>
                        <th width="7%"></th>
                    </tr>
                    @foreach ($data as $item)
                        <tr>
                            <td class="text-center" style="width:5%">{{ ++$index }}</td>
                            <td class="text-center">{{ $item->gso_id }}</td>
                            <td class="text-left">{{ $item->name }}</td>
                            <td class="text-center">{{ Carbon\Carbon::parse($item->updated_at)->format('d/m/Y H:i') }}</td>
                            <td class="dropdown text-center">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="fal fa-tools"></i>
                                </button>
                                <div class="dropdown-menu p-0">
                                    @can('ProvinceController@show')
                                        <a href="{{ url('/admin/provinces/' . $item->id) }}"
                                            title="{{ __('Xem') }}"><button class="btn btn-info dropdown-item"><i
                                                    class="fas fa-eye"></i> {{ __('Xem') }}</button></a>
                                    @endcan
                                    @can('ProvinceController@update')
                                        <a href="{{ url('/admin/provinces/' . $item->id . '/edit') }}"
                                            title="{{ __('Sửa') }}"><button class="btn btn-primary dropdown-item"><i
                                                    class="far fa-edit" aria-hidden="true"></i>
                                                {{ __('Sửa') }}</button></a>
                                    @endcan
                                    @can('ProvinceController@destroy')
                                        {!! Form::open([
                                            'method' => 'DELETE',
                                            'url' => ['/admin/provinces', $item->id],
                                            'style' => 'display:inline',
                                        ]) !!}
                                        {!! Form::button('<i class="far fa-trash-alt" aria-hidden="true"></i> ' . __('Xóa'), [
                                            'type' => 'submit',
                                            'class' => 'btn btn-danger dropdown-item',
                                            'title' => __('message.user.delete_user'),
                                            'onclick' => 'return confirm("' . __('message.confirm_delete') . '")',
                                        ]) !!}
                                        {!! Form::close() !!}
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @if ($data->count() == 0)
                        <tr>
                            <td class="text-center" colspan="9"> {{ __('message.no-item') }}
                        </tr>
                    @endif
                </tbody>
            </table>
            <div class="box-footer clearfix">
                {!! $data->appends(\Request::except('page'))->render() !!}
            </div>
        </div>
    </div>
@endsection
@section('scripts-footer')
    @toastr_js
    @toastr_render
@endsection
