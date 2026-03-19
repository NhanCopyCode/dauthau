@extends('admin.layouts.admin')

@section('htmlheader_title', 'Trang chủ')

@section('style')
    <style>
        /* CSS riêng nếu cần */
    </style>
@endsection

@section('main-content')
    <div class="container-fluid">

        <div class="row">

            <div class="col-md-3">
                <a href="{{ url('/admin/users') }}">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning "><i class="fa fa-users"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Member total</span>
                            <span class="info-box-number">
                                9
                            </span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                </a>
            </div>



        </div>
    </div>
@endsection
