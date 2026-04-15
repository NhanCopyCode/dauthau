@extends('layouts.frontend')

@section('content')
    <div class="bg-gray-50 min-h-screen">

        <div class="max-w-7xl mx-auto p-4 md:p-6">

            {{-- <form method="GET" class="bg-white border border-gray-200 rounded-xl p-4 mb-4">

                <!-- ================= PRIMARY FILTER ================= -->
                <div class="grid grid-cols-12 gap-4 items-start">

                    <!-- SEARCH -->
                    <div class="col-span-12 md:col-span-5">
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Tên gói thầu, mã TBMT..."
                                class="w-full h-10 pl-9 pr-3 text-sm border border-gray-200 rounded-lg
                                    focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">

                            <svg class="w-4 h-4 absolute left-3 top-3 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-width="2" d="M21 21l-4.35-4.35M16 10a6 6 0 11-12 0 6 6 0 0112 0z" />
                            </svg>
                        </div>
                    </div>

                    <!-- PROVINCE -->
                    <div class="col-span-12 sm:col-span-6 md:col-span-4">
                        <select name="province"
                            class="w-full h-10 px-3 text-sm border border-gray-200 rounded-lg bg-white
                                focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            <option value="">Tỉnh</option>
                            @foreach ($provinces ?? [] as $province)
                                <option value="{{ $province }}"
                                    {{ request('province') == $province ? 'selected' : '' }}>
                                    {{ $province }}
                                </option>
                            @endforeach
                        </select>
                    </div>



                    <!-- ACTION -->
                    <div class="col-span-12 sm:col-span-6 md:col-span-3 flex gap-2 justify-around">
                        <button
                            class="px-6 h-10 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-500 cursor-pointer">
                            Tìm
                        </button>

                        <a href="{{ route('tenders.index') }}"
                            class="px-4 h-10 text-sm border border-gray-200 rounded-lg hover:bg-gray-100 flex items-center cursor-pointer">
                            Reset
                        </a>

                        <button type="button" onclick="toggleAdvanced()"
                            class="px-3 h-10 text-sm border border-gray-200 rounded-lg hover:bg-gray-100 cursor-pointer">
                            Nâng cao
                        </button>
                    </div>
                </div>

                <!-- ================= SECONDARY FILTER ================= -->
                <div class="mt-4 pt-4 border-t border-gray-100">

                    <div class="grid grid-cols-12 gap-4">

                        <!-- PRICE FILTER -->
                        <div class="col-span-12 ">
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">

                                <p class="text-sm font-medium text-gray-700 mb-2">
                                    Giá gói thầu
                                </p>

                                <div class="grid grid-cols-2 gap-3">

                                    <select id="pricePreset"
                                        class="col-span-2 h-10 px-3 text-sm border border-gray-200 rounded-lg bg-white
                                    focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                        <option value="">Chọn khoảng giá</option>
                                        <option value="0-1000000000">Dưới 1 tỷ</option>
                                        <option value="1000000000-5000000000">1 – 5 tỷ</option>
                                        <option value="5000000000-10000000000">5 – 10 tỷ</option>
                                        <option value="10000000000-50000000000">10 – 50 tỷ</option>
                                        <option value="50000000000-999999999999">Trên 50 tỷ</option>
                                    </select>

                                    <input type="text" id="price_min_display"
                                        value="{{ request('price_min') ? number_format(request('price_min'), 0, ',', '.') : '' }}"
                                        placeholder="Từ" class="h-10 px-3 text-sm border border-gray-200 rounded-lg">

                                    <input type="text" id="price_max_display"
                                        value="{{ request('price_max') ? number_format(request('price_max'), 0, ',', '.') : '' }}"
                                        placeholder="Đến" class="h-10 px-3 text-sm border border-gray-200 rounded-lg">
                                </div>

                                <div class="mt-2 text-xs text-gray-500">
                                    <span id="priceDisplay"></span>
                                </div>

                                <input type="hidden" name="price_min" id="price_min" value="{{ request('price_min') }}">
                                <input type="hidden" name="price_max" id="price_max" value="{{ request('price_max') }}">
                            </div>
                        </div>

                    </div>
                </div>

                <!-- ================= ADVANCED FILTER ================= -->
                <div id="advancedFilter"
                    class="{{ request()->investor || request()->public_from || request()->public_to || request()->close_from || request()->close_to ? '' : 'hidden' }} mt-4 pt-4 border-t border-gray-100">

                    <div class="grid grid-cols-12 gap-4">

                        <input type="text" name="investor" value="{{ request('investor') }}" placeholder="Chủ đầu tư"
                            class="col-span-12 md:col-span-3 h-10 px-3 text-sm border border-gray-200 rounded-lg">

                        <div class="col-span-12 md:col-span-4 flex gap-2">
                            <input type="text" name="public_from" value="{{ request('public_from') }}"
                                placeholder="Ngày đăng từ"
                                class="date-picker w-full h-10 px-2 text-sm border border-gray-200 rounded-lg">

                            <input type="text" name="public_to" value="{{ request('public_to') }}" placeholder="Đến"
                                class="date-picker w-full h-10 px-2 text-sm border border-gray-200 rounded-lg">
                        </div>

                        <div class="col-span-12 md:col-span-4 flex gap-2">
                            <input type="text" name="close_from" value="{{ request('close_from') }}"
                                placeholder="Đóng thầu từ"
                                class="date-picker w-full h-10 px-2 text-sm border border-gray-200 rounded-lg">

                            <input type="text" name="close_to" value="{{ request('close_to') }}" placeholder="Đến"
                                class="date-picker w-full h-10 px-2 text-sm border border-gray-200 rounded-lg">
                        </div>

                    </div>
                </div>

            </form> --}}
            <form method="GET" class="bg-white border border-gray-200 rounded-xl p-4 md:p-5 space-y-4">

                <!-- ================= PRIMARY ================= -->
                <div class="grid gap-3 md:gap-4 md:grid-cols-12">

                    <!-- SEARCH -->
                    <div class="md:col-span-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Tìm kiếm
                        </label>

                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Tên gói thầu, mã TBMT..."
                                class="w-full h-11 pl-10 pr-3 text-sm border border-gray-200 rounded-lg
                    focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-width="2" d="M21 21l-4.35-4.35M16 10a6 6 0 11-12 0 6 6 0 0112 0z" />
                            </svg>
                        </div>
                    </div>

                    <!-- PROVINCE -->
                    <div class="md:col-span-4">
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Tỉnh / Thành
                        </label>

                        <select name="province"
                            class="w-full h-11 px-3 text-sm border border-gray-200 rounded-lg bg-white
                focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Chọn tỉnh</option>
                            @foreach ($provinces ?? [] as $province)
                                <option value="{{ $province }}"
                                    {{ request('province') == $province ? 'selected' : '' }}>
                                    {{ $province }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- ACTION -->
                    <div class="md:col-span-3 flex items-end flex-col sm:flex-row gap-2">
                        <button
                            class="w-full sm:flex-1 h-11 text-sm font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-500 cursor-pointer">
                            Tìm kiếm
                        </button>

                        <a href="{{ route('tenders.index') }}"
                            class="w-full sm:w-auto h-11 px-4 text-sm border border-gray-200 rounded-lg flex items-center justify-center hover:bg-gray-100">
                            Reset
                        </a>

                        <button type="button" onclick="toggleAdvanced()"
                            class="w-full sm:w-auto h-11 px-4 text-sm border border-gray-200 rounded-lg hover:bg-gray-100">
                            Nâng cao
                        </button>
                    </div>
                </div>

                <!-- ================= PRICE ================= -->
                <div class="pt-4 border-t border-gray-100 space-y-3">

                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-1">
                            Giá gói thầu
                        </p>

                        <!-- PRESET -->
                        <select id="pricePreset"
                            class="w-full h-11 px-3 text-sm border border-gray-200 rounded-lg bg-white
                focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Chọn khoảng giá</option>
                            <option value="0-1000000000">Dưới 1 tỷ</option>
                            <option value="1000000000-5000000000">1 – 5 tỷ</option>
                            <option value="5000000000-10000000000">5 – 10 tỷ</option>
                            <option value="10000000000-50000000000">10 – 50 tỷ</option>
                            <option value="50000000000-999999999999">Trên 50 tỷ</option>
                        </select>
                    </div>

                    <!-- CUSTOM RANGE -->
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" id="price_min_display"
                            value="{{ request('price_min') ? number_format(request('price_min'), 0, ',', '.') : '' }}"
                            placeholder="Từ"
                            class="h-11 px-3 text-sm border border-gray-200 rounded-lg
                focus:outline-none focus:ring-2 focus:ring-blue-500">

                        <input type="text" id="price_max_display"
                            value="{{ request('price_max') ? number_format(request('price_max'), 0, ',', '.') : '' }}"
                            placeholder="Đến"
                            class="h-11 px-3 text-sm border border-gray-200 rounded-lg
                focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <p class="text-xs text-gray-500" id="priceDisplay"></p>

                    <input type="hidden" name="price_min" id="price_min" value="{{ request('price_min') }}">
                    <input type="hidden" name="price_max" id="price_max" value="{{ request('price_max') }}">
                </div>

                <!-- ================= ADVANCED ================= -->
                <div id="advancedFilter"
                    class="{{ request()->investor || request()->public_from || request()->public_to || request()->close_from || request()->close_to ? '' : 'hidden' }} pt-4 border-t border-gray-100 space-y-3">

                    <div class="grid gap-3 md:grid-cols-12">

                        <!-- INVESTOR -->
                        <div class="md:col-span-3">
                            <input type="text" name="investor" value="{{ request('investor') }}"
                                placeholder="Chủ đầu tư"
                                class="w-full h-11 px-3 text-sm border border-gray-200 rounded-lg
                    focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- PUBLIC DATE -->
                        <div class="md:col-span-4 grid grid-cols-2 gap-2">
                            <input type="text" name="public_from" value="{{ request('public_from') }}"
                                placeholder="Ngày đăng từ"
                                class="date-picker h-11 px-3 text-sm border border-gray-200 rounded-lg">

                            <input type="text" name="public_to" value="{{ request('public_to') }}" placeholder="Đến"
                                class="date-picker h-11 px-3 text-sm border border-gray-200 rounded-lg">
                        </div>

                        <!-- CLOSE DATE -->
                        <div class="md:col-span-5 grid grid-cols-2 gap-2">
                            <input type="text" name="close_from" value="{{ request('close_from') }}"
                                placeholder="Đóng thầu từ"
                                class="date-picker h-11 px-3 text-sm border border-gray-200 rounded-lg">

                            <input type="text" name="close_to" value="{{ request('close_to') }}" placeholder="Đến"
                                class="date-picker h-11 px-3 text-sm border border-gray-200 rounded-lg">
                        </div>

                    </div>
                </div>

            </form>


            <div class="my-6">
                @include('frontend.components.pagination', ['paginator' => $tenders])
            </div>
            @include('frontend.components.list-tenders')

            <!-- ================= PAGINATION ================= -->


        </div>

    </div>
@endsection

@section('scripts-footer')
    <script>
        function toggleAdvanced() {
            const el = document.getElementById('advancedFilter');
            el.classList.toggle('hidden');
        }

        flatpickr(".date-picker", {
            locale: "vn",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d/m/Y",
            allowInput: true
        });

        function formatCurrency(value) {
            if (!value) return '';
            return Number(value).toLocaleString('vi-VN');
        }

        function parseCurrency(value) {
            return value.replace(/\D/g, '');
        }

        // 👉 PRESET = FILL INPUT (KHÔNG GIỮ STATE RIÊNG)
        document.getElementById('pricePreset').addEventListener('change', function() {
            const value = this.value;
            if (!value) return;

            const [min, max] = value.split('-');

            setPrice(min, max);
        });

        // 👉 INPUT = SOURCE OF TRUTH
        ['price_min_display', 'price_max_display'].forEach(id => {
            const input = document.getElementById(id);

            input.addEventListener('input', function() {
                let raw = parseCurrency(this.value);
                this.value = formatCurrency(raw);

                const min = parseCurrency(document.getElementById('price_min_display').value);
                const max = parseCurrency(document.getElementById('price_max_display').value);

                setPrice(min, max);

                // 👉 reset preset (tránh conflict)
                document.getElementById('pricePreset').value = '';
            });
        });

        // 👉 CENTRAL STATE HANDLER
        function setPrice(min, max) {
            document.getElementById('price_min').value = min;
            document.getElementById('price_max').value = max;

            updateDisplay(min, max);
        }

        // 👉 DISPLAY
        function updateDisplay(min, max) {
            const el = document.getElementById('priceDisplay');

            if (!min && !max) {
                el.innerText = 'Chưa chọn khoảng giá';
                return;
            }

            el.innerText = `${formatCurrency(min)} → ${formatCurrency(max)} VNĐ`;
        }

        // 👉 INIT FROM SERVER (QUAN TRỌNG)
        document.addEventListener('DOMContentLoaded', function() {
            const min = document.getElementById('price_min').value;
            const max = document.getElementById('price_max').value;

            updateDisplay(min, max);
        });
    </script>
@endsection
