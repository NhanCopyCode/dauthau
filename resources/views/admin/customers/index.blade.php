@extends('admin.layouts.admin')
@section('htmlheader_title')
    {{ __('customers.title') }}
@endsection
@section('contentheader_title')
    {{ __('customers.title') }}
@endsection
@section('contentheader_description')
@endsection
@section('main-content')
    @include('admin.customers.modal')
    @include('admin.customers.import')
    <div class="box">
        <div class="content-header border-bottom pb-5">
            <h5 class="float-left">
                {{ trans('message.customers') }}
            </h5>
        </div>
        <div class="box-header">
            <div class="box-tools" style="display: flex;">
                {!! Form::open([
                    'method' => 'GET',
                    'url' => 'customers/export',
                    'class' => 'pull-left pr-2',
                    'role' => 'search',
                ]) !!}
                <button type="submit" class="btn btn-info btn-md" title="Xuất dữ liệu">
                    <i class="far fa-file-excel"></i>
                </button>
                <input type="hidden" name="keyword" value="{{ \Request::get('search') }}">
                <input type="hidden" name="from"
                    value="{{ empty(Request::get('from')) ? null : Request::get('from') }}" />
                <input type="hidden" name="to" value="{{ empty(Request::get('to')) ? null : Request::get('to') }}" />
                <input type="hidden" name="promotion_id"
                    value="{{ empty(Request::get('promotion_id')) ? null : Request::get('promotion_id') }}" />
                <input type="hidden" name="active"
                    value="{{ empty(Request::get('active')) ? null : Request::get('active') }}" />
                <input type="hidden" name="province_id"
                    value="{{ empty(Request::get('province_id')) ? null : Request::get('province_id') }}" />
                <input type="hidden" name="district_id"
                    value="{{ empty(Request::get('district_id')) ? null : Request::get('district_id') }}" />
                <input type="hidden" name="ward_id"
                    value="{{ empty(Request::get('ward_id')) ? null : Request::get('ward_id') }}" />
                <input type="hidden" name="old_id"
                    value="{{ empty(Request::get('old_id')) ? null : Request::get('old_id') }}" />
                {!! Form::close() !!}

                {!! Form::open(['method' => 'GET', 'url' => '/admin/customers', 'class' => 'pull-left', 'role' => 'search']) !!}
                <div class="input-group">
                    <a class="btn btn-md btn-info" data-toggle="modal" data-target="#exampleModalCenter">
                        <i class="fa fa-envelope"></i>&nbsp;Gửi Mail
                    </a>
                    &nbsp;&nbsp;
                    <a class="btn btn-md btn-info" data-toggle="modal" data-target="#importModalCenter">
                        <i class="fa fa-cloud-upload"></i>&nbsp;Import
                    </a>
                    &nbsp;&nbsp;
                    <div class="input-group1" style="margin-right:5px">
                        <button type="button" class="btn btn-default" id="daterange-btn">
                            @if (empty(Request::get('from')))
                                <span>
                                    <i class="far fa-calendar-alt"></i> {{ __('promotions.date') }}
                                </span>
                            @else
                                <span>
                                    {{ Carbon\Carbon::parse(Request::get('from'))->format('d/m/Y') }} -
                                    {{ Carbon\Carbon::parse(Request::get('to'))->format('d/m/Y') }}
                                </span>
                            @endif
                            <i class="fa fa-caret-down"></i>
                        </button>
                        <input type="hidden" name="from"
                            value="{{ empty(Request::get('from')) ? null : Request::get('from') }}" />
                        <input type="hidden" name="to"
                            value="{{ empty(Request::get('to')) ? null : Request::get('to') }}" />
                    </div>
                    <div class="select-group" style="width: 120px;margin-right:35px">
                        {!! Form::select('old_id', $olds ?? [], \Request::get('old_id'), [
                            'class' => 'form-control input-sm select2',
                        ]) !!}
                    </div>
                    <div class="select-group" style="width: 120px;margin-right:35px">
                        {!! Form::select('province_id', $provinces ?? [], \Request::get('province_id'), [
                            'class' => 'form-control input-sm select2',
                            'id' => 'city_id',
                        ]) !!}
                    </div>
                    <div class="select-group" style="width: 120px;margin-right:35px">
                        {!! Form::select('district_id', $districts ?? [], \Request::get('district_id'), [
                            'class' => 'form-control input-sm select2',
                            'id' => 'district_id',
                        ]) !!}
                    </div>
                    <div class="select-group" style="width: 120px;margin-right:35px">
                        {!! Form::select('ward_id', $wards ?? [], \Request::get('ward_id'), [
                            'class' => 'form-control input-sm select2',
                            'id' => 'ward_id',
                        ]) !!}
                    </div>
                    <div class="select-group" style="width: 120px;margin-right:35px">
                        {!! Form::select('promotion_id', $promotions, \Request::get('promotion_id'), [
                            'class' => 'form-control input-sm select2',
                        ]) !!}
                    </div>
                    <div class="select-group" style="width: 120px;margin-right:35px">
                        {!! Form::select('active', \App\Models\Customer::$TYPE, \Request::get('active'), [
                            'class' => 'form-control input-sm select2',
                        ]) !!}
                    </div>
                    <input type="text" value="{{ \Request::get('search') }}" class="form-control input-sm"
                        name="search" placeholder="{{ __('message.search_keyword') }}" style="width: 150px;">
                    <span class="input-group-btn">
                        <button class="btn btn-default " type="submit">
                            <i class="fa fa-search"></i> {{ __('message.search') }}
                        </button>
                    </span>
                </div>
                {!! Form::close() !!}

            </div>
        </div>
        @include('admin.customers.error')
        @php($index = ($customers->currentPage() - 1) * $customers->perPage())
        <div class="box-body no-padding">
            <table class="table table-bordered table-hover table-responsive">
                <tbody>
                    <tr>
                        <th class="text-center" style="width: 3.5%;">
                            <input type="checkbox" name="chkAll" id="chkAll" />
                        </th>
                        <th class="text-center">{{ trans('message.index') }}</th>
                        <th>@sortablelink('name', trans('customers.name'))</th>
                        <th>@sortablelink('phone', trans('customers.phone'))</th>
                        <th class="text-left">{{ __('Tỉnh') }}</th>
                        <th class="text-left">{{ __('Huyện') }}</th>
                        <th class="text-left">{{ __('Xã') }}</th>
                        <th class="text-left">{{ trans('promotions.name') }}</th>
                        <th class="text-left">{{ __('Thông tin khác') }}</th>
                        <th>@sortablelink('active', __('message.user.active'))</th>
                        <th>@sortablelink('created_at', __('Ngày đăng ký'))</th>
                        <th></th>
                    </tr>
                    @foreach ($customers as $item)
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" name="chkId" id="chkId" value="{{ $item->id }}"
                                    data-id="{{ $item->id }}" />
                            </td>
                            <td class="text-center">{{ ++$index }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->phone }}</td>
                            <td class="text-left">{{ optional($item->province)->name }}</td>
                            <td class="text-left">{{ optional($item->district)->name }}</td>
                            <td class="text-left">{{ optional($item->ward)->name }}</td>
                            <td class="text-left">{{ optional($item->promotion)->name }}</td>
                            <td>
                                @if ($item->input != null)
                                    @foreach (json_decode($item->input, true) as $keyI => $valueI)
                                        @if ($keyI != 'form-embed')
                                            {{ str_replace('_', ' ', $keyI) }}:
                                            {{ $valueI }}
                                            <br>
                                        @endif
                                    @endforeach
                                @endif
                            </td>
                            <td>{{ $item->active == Config('settings.active') ? __('message.yes') : __('message.no') }}
                            </td>
                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format(config('settings.format.datetime')) }}
                            </td>
                            <td class="dropdown">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="fal fa-tools"></i>
                                </button>
                                <div class="dropdown-menu p-0">
                                    @can('CustomerController@show')
                                        <a href="{{ url('/admin/customers/' . $item->id) }}"
                                            title="{{ __('message.user.view_user') }}">
                                            <button class="btn btn-info btn-sm dropdown-item"><i class="fas fa-eye"></i>
                                                {{ __('message.view') }}</button>
                                        </a>
                                    @endcan
                                    @can('CustomerController@destroy')
                                        {!! Form::open([
                                            'method' => 'DELETE',
                                            'url' => ['/admin/customers', $item->id],
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
                    @if ($customers->count() == 0)
                        <tr>
                            <td class="text-center" colspan="9">{{ __('Không có dữ liệu') }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
            <div class="box-footer clearfix">
                <div id="btn-act">
                    @can('CustomerController@destroy')
                        <a href="#" id="deleteCustomers" data-action="deleteCustomers"
                            class="btn-act btn btn-danger btn-sm" title="{{ __('message.delete') }}">
                            <i class="fas fa-trash-alt" aria-hidden="true"></i>
                        </a>
                    @endcan
                    &nbsp;
                    @can('CustomerController@active')
                        <a href="#" id="activeCustomers" data-action="activeCustomers"
                            class="btn-act btn btn-success btn-sm" title="{{ __('message.approved') }}">
                            <i class="fa fa-check" aria-hidden="true"></i>
                        </a>
                    @endcan
                </div>
                {!! $customers->appends(\Request::except('page'))->render() !!}
            </div>
        </div>
    </div>

    @include('sweetalert::alert')
@endsection
@section('scripts-footer')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jquery.filer@1.3.0/css/jquery.filer.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/jquery.filer@1.3.0/css/themes/jquery.filer-dragdropbox-theme.css">
    <script src="{{ asset('plugins/jquery.filer.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
    <script type="text/javascript" src="{{ asset('plugins/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script type="text/javascript">
        $('.file_task').filer({
            limit: 1,
            maxSize: 5,
            extensions: ['xlsx', 'xlsm', 'xml', 'xls'],
            showThumbs: true,
            changeInput: '<div class="jFiler-input-dragDrop"><div class="jFiler-input-inner"><div class="jFiler-input-icon"><i class="icon-jfi-cloud-up-o"></i></div><div class="jFiler-input-text"><h3>Nhấp vào để tải tệp lên</h3></div></div></div>',
            dragDrop: {
                dragEnter: null,
                dragLeave: null,
                drop: null,
            },
            addMore: true,

        });

        $(function() {
            let from = moment().startOf('day');
            @if (!empty(Request::get('from')))
                from = moment('{{ Request::get('from') }}');
            @endif
            let to = moment().endOf('day');
            @if (!empty(Request::get('to')))
                to = moment('{{ Request::get('to') }}');
            @endif
            $('#daterange-btn').daterangepicker({
                    ranges: {
                        '{{ __('reports.in_day') }}': [moment().startOf('day'), moment().endOf('day')],
                        '{{ __('reports.yesterday') }}': [moment().subtract(1, 'day'), moment().subtract(1,
                            'day')],
                        '{{ __('reports.last_7_days') }}': [moment().subtract(6, 'days'), moment()],
                        '{{ __('reports.last_30_days') }}': [moment().subtract(29, 'days'), moment()],
                        '{{ __('reports.this_month') }}': [moment().startOf('month'), moment().endOf(
                            'month')],
                        '{{ __('reports.last_month') }}': [moment().subtract(1, 'month').startOf('month'),
                            moment().subtract(1, 'month').endOf('month')
                        ],
                        '{{ __('reports.three_month') }}': [moment().subtract(3, 'months'), moment()],

                        '{{ __('reports.six_month') }}': [moment().subtract(6, 'months'), moment()],

                        '{{ __('reports.this_year') }}': [moment().startOf('year'), moment().endOf(
                            'year')],
                        '{{ __('reports.last_year') }}': [moment().subtract(1, 'year').startOf('year'),
                            moment().subtract(1, 'year').endOf(
                                'year')
                        ],

                    },
                    startDate: from,
                    endDate: to,
                    "locale": {
                        "format": "{{ strtoupper(config('settings.format.date_js')) }}",
                        "separator": " - ",
                        "applyLabel": "{{ __('reports.apply') }}",
                        "cancelLabel": "{{ __('reports.close') }}",
                        "fromLabel": "{{ __('reports.from') }}",
                        "toLabel": "{{ __('reports.to') }}",
                        "customRangeLabel": "{{ __('reports.custom') }}",
                        "daysOfWeek": [
                            "{{ __('reports.Su') }}",
                            "{{ __('reports.Mo') }}",
                            "{{ __('reports.Tu') }}",
                            "{{ __('reports.We') }}",
                            "{{ __('reports.Th') }}",
                            "{{ __('reports.Fr') }}",
                            "{{ __('reports.Sa') }}"
                        ],
                        "monthNames": [
                            "{{ __('reports.january') }}",
                            "{{ __('reports.february') }}",
                            "{{ __('reports.march') }}",
                            "{{ __('reports.april') }}",
                            "{{ __('reports.may') }}",
                            "{{ __('reports.june') }}",
                            "{{ __('reports.july') }}",
                            "{{ __('reports.august') }}",
                            "{{ __('reports.september') }}",
                            "{{ __('reports.october') }}",
                            "{{ __('reports.november') }}",
                            "{{ __('reports.december') }}"
                        ],
                        "firstDay": 1
                    }
                },
                function(start, end) {
                    $('#daterange-btn span').html(start.format(
                        '{{ strtoupper(config('settings.format.date_js')) }}') + ' - ' + end.format(
                        '{{ strtoupper(config('settings.format.date_js')) }}'));
                    $('[name=from]').val(start.format('YYYY-MM-DD'));
                    $('[name=to]').val(end.format('YYYY-MM-DD'));
                }
            )
        });
    </script>
    <script type="text/javascript">
        $('#city_id').change(function() {
            $('#district_id').html('<option>{{ __('message.loading') }}</option>');
            $('#ward_id').html('<option value="0">{{ __('message.please_select') }}</option>');
            let city_id = $(this).val();
            axios.get('{{ url('ajax/getDistricts?id=') }}' + city_id)
                .then(function(response) {
                    const data = response.data;
                    $('#district_id').html('<option>{{ __('message.please_select') }}</option>');
                    $.each(data, function(key, value) {
                        $('#district_id').append('<option value="' + key + '">' + value + '</option>');
                    })
                })
                .catch(function(error) {
                    console.log(error);
                })
        });

        $('#district_id').change(function() {
            $('#ward_id').html('<option value="0">{{ __('message.loading') }}</option>');
            let district_id = $(this).val();
            axios.get('{{ url('ajax/getWards?id=') }}' + district_id)
                .then(function(response) {
                    const data = response.data;
                    $('#ward_id').html('<option value="0">{{ __('message.please_select') }}</option>');
                    $.each(data, function(key, value) {
                        $('#ward_id').append('<option value="' + key + '">' + value + '</option>');
                    })
                })
                .catch(function(error) {
                    console.log(error)
                })
        });
        $('.show_confirm').click(function(event) {
            var form = $(this).closest("form");
            var name = $(this).data("name");
            event.preventDefault();
            swal({
                    title: `
                        Bạn có chắc chắn muốn xóa bản ghi này không?`,
                    text: "Nếu bạn xóa nó, nó sẽ biến mất vĩnh viễn.",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        form.submit();
                    }
                });
        });
    </script>
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
                case 'activeCustomers':
                    actTxt = 'kích hoạt';
                    classAlert = 'alert-success';
                    break;
                case 'deleteCustomers':
                    actTxt = 'xóa';
                    classAlert = 'alert-danger';
                    break;
            }
            if (chkId.length != 0) {
                swal({
                        title: 'Bạn có muốn ' + actTxt +
                            ' tài khoản này không?',
                        text: "",
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
                            axios.get('{{ url('/ajax') }}/' + action, {
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
                swal("Lỗi!", 'Vui lòng chọn tài khoản để  ' + actTxt + '!', "error")
            }
        }
    </script>
@endsection
