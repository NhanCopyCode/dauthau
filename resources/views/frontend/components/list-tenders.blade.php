  <div class="space-y-4">
      @if ($tenders->count() > 0)
          @foreach ($tenders as $tender)
              <div
                  class="block bg-white border border-gray-200 rounded-2xl p-4 md:p-5 
                     hover:shadow-lg hover:border-blue-300 transition duration-200">

                  <!-- ================= HEADER ================= -->
                  <div class="flex items-center justify-between mb-2">

                      <!-- Mã TBMT -->
                      <span class="text-xs text-gray-500 font-medium">
                          {{ $tender->notify_no }}
                      </span>

                      <!-- Status -->
                      <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">
                          Chưa đóng thầu
                      </span>
                  </div>

                  <!-- ================= TITLE ================= -->
                  <a href="{{ route('tenders.show', ['egp_id' => $tender->egp_id]) }}"
                      class="text-lg md:text-xl font-semibold text-blue-500 leading-snug line-clamp-2 mb-3">
                      {{ $tender->name }}
                  </a>

                  <!-- ================= BODY ================= -->
                  <div class="flex flex-col lg:flex-row lg:items-center gap-4">

                      <!-- LEFT INFO -->
                      <div class="flex-1">

                          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-sm">

                              <!-- Chủ đầu tư -->
                              <div>
                                  <p class="text-gray-500">Chủ đầu tư</p>
                                  <p class="font-medium text-slate-800 line-clamp-2">
                                      {{ $tender->investor }}
                                  </p>
                              </div>

                              <!-- Lĩnh vực -->
                              <div>
                                  <p class="text-gray-500">Lĩnh vực</p>
                                  <p class="font-medium">
                                      {{ $tender->invest_field_label }}
                                  </p>
                              </div>

                              <!-- Hình thức -->
                              <div>
                                  <p class="text-gray-500">Hình thức</p>
                                  <p class="font-medium">
                                      {{ $tender->bid_form ?? '—' }}
                                  </p>
                              </div>

                              <!-- Địa điểm -->
                              <div>
                                  <p class="text-gray-500">Địa điểm</p>
                                  <p class="font-medium">
                                      {{ $tender->province }}
                                  </p>
                              </div>

                              <!-- Ngày đăng -->
                              <div>
                                  <p class="text-gray-500">Ngày đăng</p>
                                  <p>
                                      {{ optional($tender->public_date)->format('d/m/Y') }}
                                  </p>
                              </div>

                              <!-- Giá -->
                              <div>
                                  <p class="text-gray-500">Giá gói thầu</p>
                                  <p class="font-semibold text-blue-800">
                                      {{ number_format($tender->bid_price, 0, ',', '.') }} đ
                                  </p>
                              </div>

                          </div>
                      </div>

                      <!-- ================= DEADLINE ================= -->
                      <div class="lg:w-40 flex-shrink-0">

                          @php
                              $close = $tender->bid_close_date;
                              $isUrgent = $close && $close->diffInDays(now()) <= 2;
                          @endphp

                          <div
                              class="border rounded-xl p-3 text-center 
                        {{ $isUrgent ? 'border-red-200 bg-red-50' : 'border-gray-200 bg-gray-50' }}">

                              <p class="text-xs text-gray-500 mb-1">
                                  Đóng thầu
                              </p>

                              @if ($close)
                                  <!-- Giờ -->
                                  <p
                                      class="text-lg font-semibold 
                                {{ $isUrgent ? 'text-red-600' : 'text-slate-900' }}">
                                      {{ $close->format('H:i') }}
                                  </p>

                                  <!-- Ngày -->
                                  <p class="text-sm text-gray-500">
                                      {{ $close->format('d/m/Y') }}
                                  </p>
                              @else
                                  <p class="text-sm text-gray-400">—</p>
                              @endif

                          </div>

                      </div>

                  </div>

              </div>
          @endforeach
      @else
          <!-- EMPTY STATE -->
          <div class="bg-white border border-gray-200 rounded-xl p-10 text-center">

              <div class="flex flex-col items-center gap-3">

                  <!-- Icon -->
                  <div class="w-12 h-12 flex items-center justify-center rounded-full bg-gray-100">
                      <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-width="2"
                              d="M9 17v-2a4 4 0 014-4h3M9 17H6a2 2 0 01-2-2V7a2 2 0 012-2h6l2 2h4a2 2 0 012 2v2" />
                      </svg>
                  </div>

                  <!-- Text -->
                  <div>
                      <p class="text-sm font-medium text-gray-700">
                          Không tìm thấy kết quả
                      </p>
                      <p class="text-xs text-gray-500 mt-1">
                          Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm
                      </p>
                  </div>

                  <!-- Action -->
                  <a href="{{ route('tenders.index') }}"
                      class="mt-2 px-4 py-2 text-sm border border-gray-200 rounded-lg hover:bg-gray-100">
                      Xóa bộ lọc
                  </a>

              </div>

          </div>
      @endif
  </div>
