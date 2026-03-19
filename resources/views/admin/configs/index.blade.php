@extends('admin.layouts.admin')
@section('style')
    <style>
        .select2 {
            width: 250px;
        }
    </style>
@endsection
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
        <li class="active">{{ __('configs.title') }}</li>
    </ol>
@endsection
@section('main-content')
    <div class="box">
        <div class="content-header border-bottom pb-5">
            <h5 class="float-left">
                {{ __('message.lists') }}
            </h5>
            @can('ConfigController@store')
                <a href="{{ url('/admin/configs/create') }}" class="btn btn-default float-right"
                    title="{{ __('message.new_add') }}">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i> <span class="hidden-xs">
                        {{ __('message.new_add') }}</span>
                </a>
            @endcan
        </div>

        <div class="box-header">
            <div class="box-tools">
                {!! Form::open(['method' => 'GET', 'url' => '/admin/configs', 'class' => 'pull-left', 'role' => 'search']) !!}
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
        @php($index = ($data->currentPage() - 1) * $data->perPage())
        <div class="box-body  no-padding">
            <table class="table table-bordered table-hover">
                <tbody>
                    <tr>
                        <th class="text-center" style="width: 3.5%">{{ trans('message.index') }}</th>
                        <th class="text-left">@sortablelink('title', trans('configs.title'))</th>
                        <th class="text-center">{{ trans('promotions.name') }}</th>
                        <th class="text-center">{{ trans('services.created_at') }}</th>
                        <th style="width: 7%"></th>
                    </tr>
                    @foreach ($data as $item)
                        <tr>
                            <td class="text-center">{{ ++$index }}</td>
                            <td class="text-left">{{ optional($item->user)->name }}</td>
                            <td class="text-left">{{ optional($item->promotion)->name }}</td>
                            <td class="text-center">
                                {{ Carbon\Carbon::parse($item->created_at)->format(config('settings.format.date')) }}</td>
                            @can('ConfigController@show')
                                <td><a href="{{ url('/admin/configs') . '/' . $item->id }}"
                                        style="color: black;">{{ $item->title }}</a></td>
                            @endcan
                            <td class="dropdown">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="fal fa-tools"></i>
                                </button>
                                <div class="dropdown-menu p-0">
                                    @can('ConfigController@show')
                                        <a href="{{ url('/admin/configs/' . $item->id) }}"
                                            title="{{ __('message.user.view_user') }}">
                                            <button class="btn btn-info btn-sm dropdown-item"><i class="fas fa-eye"></i>
                                                {{ __('message.view') }}</button>
                                        </a>
                                    @endcan
                                    @can('ConfigController@update')
                                        <a href="{{ url('/admin/configs/' . $item->id . '/edit') }}"
                                            title="{{ __('message.user.edit_user') }}">
                                            <button class="btn btn-primary btn-sm dropdown-item"><i class="far fa-edit"
                                                    aria-hidden="true"></i> {{ __('message.edit') }}
                                            </button>
                                        </a>
                                    @endcan
                                    @can('ConfigController@destroy')
                                        {!! Form::open([
                                            'method' => 'DELETE',
                                            'url' => ['/admin/configs', $item->id],
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
                @if ($data->count() == 0)
                    <tr>
                        <td class="text-center" colspan="9">{{ trans('Không có dữ liệu') }}</td>
                    </tr>
                @endif
            </table>
        </div>
        <div class="box-footer clearfix">
            <div class="page-footer pull-right">
                {!! $data->appends(\Request::except('page'))->render() !!}
            </div>
        </div>
    </div>
    @include('sweetalert::alert')
@endsection
@section('scripts-footer')
    @toastr_js
    @toastr_render
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>

    <script type="text/javascript">
        $(function() {
            $('#chkAll').on('click', function() {
                $("input:checkbox").prop('checked', $(this).prop("checked"));
            });
        });
        $('#btn-act').on('click', '.btn-act', function(e) {
            e.preventDefault();
            let action = $(this).data('action');
            console.log(action);
            ajaxCategory(action);
        });

        function ajaxCategory(action) {
            let chkId = $("input[name='chkId']:checked");
            let actTxt = '',
                successAlert = '',
                classAlert = '';
            switch (action) {
                case 'activeNews':
                    actTxt = 'duyệt';
                    classAlert = 'alert-success';
                    break;
                case 'deleteNews':
                    actTxt = 'xóa';
                    classAlert = 'alert-danger';
                    break;
            }
            if (chkId.length != 0) {
                swal({
                        title: 'Bạn có muốn ' + actTxt +
                            ' tin tức này không?',
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((willDelete) => {
                        if (willDelete) {
                            var arrId = '';
                            $("input[name='chkId']:checked").map((val, key) => {
                                arrId += key.value + ',';
                            });
                            axios.get('{{ url('/admin/ajax') }}/' + action, {
                                    params: {
                                        ids: arrId
                                    }
                                })
                                .then((response) => {
                                    if (response.data.success === 'ok') {
                                        location.reload(true);
                                    }
                                })
                                .catch((error) => {})
                        }
                    });
            } else {
                swal("Lỗi!", 'Vui lòng chọn tin tức để  ' + actTxt + '!', "error")
            }
        }
    </script>
@endsection
