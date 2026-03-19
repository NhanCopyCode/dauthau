@extends('admin.layouts.admin')
@section('htmlheader_title')
    {{ __('modules.modules') }}
@endsection
@section('contentheader_title')
    {{ __('modules.modules') }}
@endsection
@section('contentheader_description')
@endsection
@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{ url('admin') }}"><i class="fa fa-home"></i> {{ __('message.dashboard') }}</a></li>
        <li class="active">{{ __('modules.modules') }}</li>
    </ol>
@endsection
@section('main-content')
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">{{ __('message.lists') }}</h3>
            <div class="box-tools">
            </div>
        </div>
        <div class="box-body table-responsive no-padding">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('modules.name') }}
                        </th>
                        <th width="15%">
                            {{ trans('modules.active') }}
                        </th>
                    </tr>
                    @foreach ($modules as $index => $item)
                        <tr>
                            <td>
                                <b>{{ $item->name }}</b><br>
                                {{ $item->description }}
                                @php
                                    $re = $item->getRequires();
                                @endphp
                                @if (count($re))
                                    <div class="text-danger">
                                        <small>
                                            {{ __('modules.requires') }}: {{ implode(', ', $re) }}
                                        </small>
                                    </div>
                                @endif
                            </td>
                            <td>
                                {!! Form::open([
                                    'method' => 'PUT',
                                    'url' => ['/settings/modules', $item->alias],
                                    'style' => 'display:inline',
                                    'class' => 'moduleActive',
                                ]) !!}
                                {!! Form::button(
                                    '<i class="fa ' .
                                        ($item->active ? 'fa-times text-danger' : 'fa-check text-success') .
                                        '" aria-hidden="true"></i> ' .
                                        __('modules.active_' . $item->active . '_action'),
                                    [
                                        'type' => 'submit',
                                        'class' => 'btn btn-social ' . ($item->active ? 'btn-success' : 'btn-danger') . ' btn-xs',
                                        'title' => __('modules.active_' . $item->active . '_action'),
                                        'onclick' =>
                                            "return confirm('" .
                                            __('modules.active_' . $item->active . '_confirm') .
                                            "') ?  moduleActive('" .
                                            $item->alias .
                                            "', '" .
                                            $item->active .
                                            "') : ''",
                                    ],
                                ) !!}
                                {!! Form::close() !!}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
