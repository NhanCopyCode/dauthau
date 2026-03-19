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

    <div class="box">
        <div class="box-header">
            <h5 class="float-left">{{ __('message.detail') }}</h5>
            <div class="box-tools">
                <a href="{{ url('/admin/customers') }}" title="{{ __('message.lists') }}"
                    class="btn btn-default btn-sm mr-1"><i class="fa fa-arrow-left"></i> <span class="hidden-xs">
                        {{ __('message.lists') }}</span></a>
                @can('CustomerController@destroy')
                    {!! Form::open([
                        'method' => 'DELETE',
                        'url' => ['/admin/customers', $customer->id],
                        'style' => 'display:inline',
                    ]) !!}
                    {!! Form::button(
                        '<i class="far fa-trash-alt"></i> <span class="hidden-xs"> ' . __('message.delete') . '</span>',
                        [
                            'type' => 'submit',
                            'class' => 'btn btn-default btn-sm',
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
                        <td class="text-center" style="width:25%"><strong>Mã QR</strong></td>
                        <td style="width:75%" colspan="4"><strong>Thông tin</strong></td>
                    </tr>
                    <tr>
                        <td rowspan="13">
                            <div id="qrcode" style="display: flex; justify-content: center"></div>
                            <div style="display: flex; justify-content: center; margin-top: 10px;">
                                <button id="downloadBtn" class="btn btn-sm btn-danger">Tải QR code về máy</button>
                            </div>
                        </td>
                    </tr>
                    @if ($customer->input != null)
                        <tr>
                            <th>{{ __('Họ tên') }}</th>
                            <td>{{ $customer->name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Email') }}</th>
                            <td>{{ $customer->email }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Điên thoại') }}</th>
                            <td>{{ $customer->phone }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Ngày sinh') }}</th>
                            <td>{{ \Carbon\Carbon::parse($customer->birthday)->format(config('settings.format.datetime')) }}
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('Địa chỉ') }}</th>
                            <td>{{ $customer->address }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Tỉnh') }}</th>
                            <td>{{ optional($customer->province)->name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Huyện') }}</th>
                            <td>{{ optional($customer->district)->name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Xã') }}</th>
                            <td>{{ optional($customer->ward)->name }}</td>
                        </tr>
                        @foreach (json_decode($customer->input, true) as $keyI => $valueI)
                            @if ($keyI != 'form-embed')
                                <tr>
                                    <th> {{ str_replace('_', ' ', $keyI) }} </th>
                                    <td> {{ $valueI }} </td>
                                </tr>
                            @endif
                        @endforeach
                    @else
                        <tr>
                            <th>{{ __('Họ tên') }}</th>
                            <td>{{ $customer->name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Email') }}</th>
                            <td>{{ $customer->email }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Điên thoại') }}</th>
                            <td>{{ $customer->phone }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Ngày sinh') }}</th>
                            <td>{{ \Carbon\Carbon::parse($customer->birthday)->format(config('settings.format.datetime')) }}
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('Địa chỉ') }}</th>
                            <td>{{ $customer->address }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Tỉnh') }}</th>
                            <td>{{ optional($customer->province)->name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Huyện') }}</th>
                            <td>{{ optional($customer->district)->name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Xã') }}</th>
                            <td>{{ optional($customer->ward)->name }}</td>
                        </tr>
                    @endif
                    <tr>
                        <th> {{ __('Kích hoạt') }} </th>
                        <td> {{ $customer->active == 1 ? 'Có' : 'Không' }} </td>
                    </tr>
                    <tr>
                        <th>{{ __('message.user.avatar') }}</th>
                        <td>{!! $customer->showAvatar($customer->avatar) !!}</td>
                    </tr>
                    <tr>
                        <th> {{ __('Ngày đăng ký') }} </th>
                        <td>{{ \Carbon\Carbon::parse($customer->created_at)->format(config('settings.format.datetime')) }}
                        </td>
                    </tr>
                    <tr>
                        <th> {{ __('Ngày quét') }} </th>
                        <td> {{ $customer->active == 1 ? \Carbon\Carbon::parse($customer->updated_at)->format(config('settings.format.datetime')) : '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

@endsection
@section('scripts-footer')
    <script src="{{ url('js/qrcode.min.js') }}"></script>
    <script type="text/javascript">
        const downloadBtn = document.getElementById('downloadBtn');
        const qrCodeDiv = document.getElementById('qrcode');

        var url = '{{ url('scan-qr.html') }}' +
            '?customer_id=' + encodeURIComponent({{ $customer->id }}) +
            '&promotion_id=' + encodeURIComponent({{ $customer->promotion_id }});

        var qrcode = new QRCode(
            "qrcode", {
                text: url,
                width: 200,
                height: 200,
                colorDark: "#000000",
                colorLight: "#FFFFFF",
                correctLevel: QRCode.CorrectLevel.M
            }
        );
        downloadBtn.addEventListener('click', () => {
            const canvas = document.querySelector('#qrcode canvas');
            const qrCodeURL = canvas.toDataURL('image/png');
            const downloadLink = document.createElement('a');
            downloadLink.href = qrCodeURL;
            downloadLink.download = 'qr_code.png';
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        });
    </script>
@endsection
