@extends('admin.layouts.admin')
@section('htmlheader_title')
    {{ trans('message.role.roles') }}
@endsection
@section('main-content')
    <div class="box">
        <div class="content-header pb-5">
            <h5 class="float-left">
                {{ trans('message.role.roles') }}
            </h5>
            @can('RolesController@store')
                <a href="{{ url('/admin/roles/create') }}" class="btn btn-default float-right" title="{{ __('message.new_add') }}">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i> <span class="hidden-xs">
                        {{ __('message.new_add') }}</span>
                </a>
            @endcan
        </div>
        <div class="box-body no-padding">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th class="text-center">{{ trans('message.role.id') }}</th>
                        <th>{{ trans('message.role.name') }}</th>
                        <th>{{ trans('message.role.label') }}</th>
                        <th></th>
                    </tr>
                    @php($stt = ($roles->currentPage() - 1) * $roles->perPage())
                    @foreach ($roles as $item)
                        <tr>
                            <td class="text-center">{{ ++$stt }}</td>
                            <td><a href="{{ url('/admin/roles', $item->id) }}">{{ $item->name }}</a></td>
                            <td>{{ $item->label }}</td>
                            <td class="dropdown text-center">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="fal fa-tools"></i>
                                </button>
                                <div class="dropdown-menu p-0">
                                    @can('RolesController@show')
                                        <a href="{{ url('/admin/roles/' . $item->id) }}"
                                            title="{{ __('message.role.view_role') }}"><button
                                                class="btn btn-info dropdown-item"><i class="fas fa-eye"></i>
                                                {{ __('message.role.view_role') }}</button></a>
                                    @endcan
                                    @can('RolesController@update')
                                        <a href="{{ url('/admin/roles/' . $item->id . '/edit') }}"
                                            title="{{ __('message.role.edit_role') }}"><button
                                                class="btn btn-primary dropdown-item"><i class="far fa-edit"
                                                    aria-hidden="true"></i> {{ __('message.role.edit_role') }}</button></a>
                                    @endcan
                                    @can('RolesController@destroy')
                                        {!! Form::open([
                                            'method' => 'DELETE',
                                            'url' => ['/admin/roles', $item->id],
                                            'style' => 'display:inline',
                                        ]) !!}
                                        {!! Form::button('<i class="far fa-trash-alt" aria-hidden="true"></i> ' . __('message.role.delete_role'), [
                                            'type' => 'submit',
                                            'class' => 'btn btn-danger dropdown-item',
                                            'title' => __('message.role.delete_role'),
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
                {!! $roles->appends(['search' => Request::get('search')])->render() !!}
            </div>
        </div>
    </div>
    @include('sweetalert::alert')
@endsection
