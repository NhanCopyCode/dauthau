@extends('admin.layouts.admin')
@section('htmlheader_title')
    {{ __('message.user.users') }}
@endsection
@section('main-content')
    <div class="box">
        <div class="box-header">
            <h5 class="float-left">{{ __('message.detail') }}</h5>
            <div class="box-tools">
                <a href="{{ url('/admin/users') }}" title="{{ __('message.lists') }}" class="btn btn-default mr-1"><i
                        class="fa fa-arrow-left"></i> <span class="hidden-xs"> {{ __('message.lists') }}</span></a>
                @can('UsersController@update')
                    <a href="{{ url('/admin/users/' . $user->id . '/edit') }}" class="btn btn-default mr-1"><i
                            class="far fa-edit"></i> <span class="hidden-xs"> {{ __('message.edit') }}</span></a>
                @endcan
                @can('UsersController@destroy')
                    {!! Form::open([
                        'method' => 'DELETE',
                        'url' => ['/admin/users', $user->id],
                        'style' => 'display:inline',
                    ]) !!}
                    {!! Form::button(
                        '<i class="far fa-trash-alt"></i> <span class="hidden-xs"> ' . __('message.delete') . '</span>',
                        [
                            'type' => 'submit',
                            'class' => 'btn btn-default',
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
                        <th>{{ __('message.user.active') }}</th>
                        <td>{{ $user->active == Config('settings.active') ? __('message.yes') : __('message.no') }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('message.user.avatar') }}</th>
                        <td>{!! $user->showAvatar() !!}</td>
                    </tr>

                    <tr>
                        <th>{{ __('message.user.name') }}</th>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('message.user.username') }}</th>
                        <td>{{ $user->username }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('message.user.email') }}</th>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('message.user.role') }}</th>
                        <td>
                            @foreach ($user->roles as $index => $role)
                                <span class="badge label-{{ $role->color }}">{{ $role->label }}</span>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('message.user.gender') }}</th>
                        <td>{{ isset($user->profile) ? $user->profile->textGender : '' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('message.user.phone') }}</th>
                        <td>{{ isset($user->profile) ? $user->profile->phone : '' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('message.user.address') }}</th>
                        <td>{{ isset($user->profile) ? $user->profile->address : '' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('message.user.birthday') }}</th>
                        <td>{{ isset($user->profile) ? Carbon\Carbon::parse($user->profile->birthday)->format(config('settings.format.date')) : '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('message.user.position') }}</th>
                        <td>{{ isset($user->profile) ? $user->profile->position : '' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
