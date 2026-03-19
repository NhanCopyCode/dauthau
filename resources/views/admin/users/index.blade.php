@extends('admin.layouts.admin')
@section('htmlheader_title')
    {{ __('message.user.users') }}
@endsection
@section('contentheader_title')
@endsection
@section('contentheader_description')
@endsection
@section('main-content')
    <div class="box">
        <div class="content-header border-bottom pb-5">
            <h5 class="float-left">
                {{ __('message.user.users') }}
            </h5>
            @can('UsersController@store')
                <a href="{{ url('/admin/users/create') }}" class="btn btn-default float-right" title="{{ __('message.new_add') }}">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i> <span class="hidden-xs">
                        {{ __('message.new_add') }}</span>
                </a>
            @endcan
        </div>
        <div class="box-header">
            <div class="box-tools">
                {!! Form::open(['method' => 'GET', 'url' => '/admin/users', 'class' => 'pull-left', 'role' => 'search']) !!}
                <div class="input-group" style="margin-right: 5px; display:flex;">
                    <div class="select-group" style="margin-right: 5px; width:206px;">
                        {!! Form::select('role_id', $roles ?? [], \Request::get('role_id'), [
                            'class' => 'form-control input-sm select2 ',
                            'id' => 'role_id',
                        ]) !!}
                    </div>
                    <input type="text" value="{{ \Request::get('search') }}" class="form-control input-sm" name="search"
                        placeholder="{{ __('message.search_keyword') }}" autocomplete="off" style="margin-right: 5px;">
                    <button class="btn btn-default btn-sm" type="submit">
                        <i class="fa fa-search"></i> {{ __('message.search') }}
                    </button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        @php($stt = ($users->currentPage() - 1) * $users->perPage())
        <div class="box-body no-padding">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th class="text-center">{{ trans('message.index') }}</th>
                        <th>@sortablelink('name', __('message.user.name'))</th>
                        <th>@sortablelink('username', __('message.user.username'))</th>
                        <th>@sortablelink('email', __('message.user.email'))</th>
                        <th>{{ __('message.user.role') }}</th>
                        <th>@sortablelink('active', __('message.user.active'))</th>
                        <th></th>
                    </tr>
                    @foreach ($users as $item)
                        <tr>
                            <td class="text-center">{{ ++$stt }}</td>
                            <td><a href="{{ url('/admin/users', $item->id) }}">{{ $item->name }}</a></td>
                            <td><a href="{{ url('/admin/users', $item->id) }}">{{ $item->username }}</a></td>
                            <td>{{ $item->email }}</td>
                            <td>
                                @foreach ($item->roles as $index => $role)
                                    <span class="badge label-{{ $role->color }}">{{ $role->label }}</span>
                                @endforeach
                            </td>
                            <td>{{ $item->active == Config('settings.active') ? __('message.yes') : __('message.no') }}
                            </td>
                            <td class="dropdown text-center">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="fal fa-tools"></i>
                                </button>
                                <div class="dropdown-menu p-0">
                                    @can('UsersController@show')
                                        <a href="{{ url('/admin/users/' . $item->id) }}"
                                            title="{{ __('message.user.view_user') }}"><button
                                                class="btn btn-info dropdown-item"><i class="fas fa-eye"></i>
                                                {{ __('message.user.view_user') }}</button></a>
                                    @endcan
                                    @can('UsersController@update')
                                        <a href="{{ url('/admin/users/' . $item->id . '/edit') }}"
                                            title="{{ __('message.user.edit_user') }}"><button
                                                class="btn btn-primary dropdown-item"><i class="far fa-edit"
                                                    aria-hidden="true"></i> {{ __('message.user.edit_user') }}</button></a>
                                    @endcan
                                    @can('UsersController@destroy')
                                        {!! Form::open([
                                            'method' => 'DELETE',
                                            'url' => ['/admin/users', $item->id],
                                            'style' => 'display:inline',
                                        ]) !!}
                                        {!! Form::button('<i class="far fa-trash-alt" aria-hidden="true"></i> ' . __('message.user.delete_user'), [
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
                </tbody>
            </table>
            <div class="box-footer clearfix">
                {!! $users->appends(\Request::except('page'))->render() !!}
            </div>
        </div>
    </div>
@endsection
