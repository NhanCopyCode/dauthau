@extends('admin.layouts.errors')

@section('htmlheader_title')
    {{ __('adminlang.pagenotfound') }}
@endsection

@section('main-content')
    <div class="error-page">
        <h2 class="headline text-yellow"> 404</h2>
        <div class="error-content">
            <h3><i class="fa fa-warning text-yellow"></i> Oops! {{ __('adminlang.pagenotfound') }}.</h3>
            <p>
                {{ __('adminlang.notfindpage') }}
                {{ __('adminlang.mainwhile') }} <a href='{{ url('/home') }}'>{{ __('adminlang.returndashboard') }}</a>
                {{ __('adminlang.usingsearch') }}
            </p>
            <form class='search-form'>
                <div class='input-group'>
                    <input type="text" name="search" class='form-control' placeholder="{{ __('adminlang.search') }}" />
                    <div class="input-group-btn">
                        <button type="submit" name="submit" class="btn btn-warning btn-flat"><i
                                class="fa fa-search"></i></button>
                    </div>
                </div><!-- /.input-group -->
            </form>
        </div><!-- /.error-content -->
    </div><!-- /.error-page -->
@endsection
