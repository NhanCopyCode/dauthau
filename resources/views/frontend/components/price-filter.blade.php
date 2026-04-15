<div class="w-full">

    <!-- LABEL -->
    <label class="block text-sm font-medium text-gray-700 mb-2">
        Giá gói thầu
    </label>

    <!-- GROUP -->
    <div class="space-y-3">

        <!-- PRESET -->
        <div>
            <p class="text-xs text-gray-500 mb-1">Chọn nhanh</p>

            <select id="pricePreset"
                class="w-full h-9 px-3 text-sm border border-gray-200 rounded-lg bg-white
                       focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                <option value="">Chọn khoảng giá</option>
                <option value="0-1000000000">Dưới 1 tỷ</option>
                <option value="1000000000-5000000000">1 – 5 tỷ</option>
                <option value="5000000000-10000000000">5 – 10 tỷ</option>
                <option value="10000000000-50000000000">10 – 50 tỷ</option>
                <option value="50000000000-999999999999">Trên 50 tỷ</option>
            </select>
        </div>

        <!-- CUSTOM -->
        <div>
            <p class="text-xs text-gray-500 mb-1">Hoặc nhập khoảng</p>

            <div class="grid grid-cols-2 gap-3">
                <input type="text" id="price_min_display"
                    value="{{ request('price_min') ? number_format(request('price_min'), 0, ',', '.') : '' }}"
                    placeholder="Từ"
                    class="w-full h-9 px-3 text-sm border border-gray-200 rounded-lg
                           focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">

                <input type="text" id="price_max_display"
                    value="{{ request('price_max') ? number_format(request('price_max'), 0, ',', '.') : '' }}"
                    placeholder="Đến"
                    class="w-full h-9 px-3 text-sm border border-gray-200 rounded-lg
                           focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
            </div>
        </div>

    </div>

    <!-- DISPLAY -->
    <div class="mt-2 text-xs text-gray-500">
        <span id="priceDisplay"></span>
    </div>

    <!-- HIDDEN -->
    <input type="hidden" name="price_min" id="price_min" value="{{ request('price_min') }}">
    <input type="hidden" name="price_max" id="price_max" value="{{ request('price_max') }}">

</div>
