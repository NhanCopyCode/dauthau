@extends('admin.layouts.admin')
@section('htmlheader_title')
    {{ trans('message.role.roles') }}
@endsection
@section('main-content')
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans('message.detail') }}</h3>
            <div class="box-tools">
                <a href="{{ url('/admin/roles') }}" class="btn btn-default mr-1"><i class="fa fa-arrow-left"
                        aria-hidden="true"></i> <span class="hidden-xs"> {{ trans('message.lists') }}</span></a>
                <a href="{{ url('/admin/roles/' . $role->id . '/edit') }}" title="{{ trans('message.edit_title') }}"><button
                        class="btn btn-default mr-1"><i class="far fa-edit" aria-hidden="true"></i>
                        {{ trans('message.edit_title') }}</button></a>
                {!! Form::open([
                    'method' => 'DELETE',
                    'url' => ['/admin/roles', $role->id],
                    'style' => 'display:inline',
                ]) !!}
                {!! Form::button('<i class="far fa-trash-alt" aria-hidden="true"></i> ' . trans('message.delete'), [
                    'type' => 'submit',
                    'class' => 'btn btn-default',
                    'title' => trans('message.delete'),
                    'onclick' => 'return confirm("' . trans('message.role.confirm_delete') . '")',
                ]) !!}
                {!! Form::close() !!}
            </div>
        </div>
        <div class="box-body table-responsive no-padding">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>{{ trans('message.role.id') }}</th>
                        <th>{{ trans('message.role.name') }}</th>
                        <th>{{ trans('message.role.label') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $role->id }}</td>
                        <td> {{ $role->name }} </td>
                        <td> {{ $role->label }} </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
