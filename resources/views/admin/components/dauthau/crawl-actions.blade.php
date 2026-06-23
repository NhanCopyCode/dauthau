  <!-- ================================ -->
  <!-- SECTION 2: Crawl Actions Panel -->
  <!-- ================================ -->
  <div x-data="crawlActions" class="flex flex-col lg:flex-row items-stretch gap-6">
      <!-- Quick Actions -->
      <div class="bg-zinc-900 border border-zinc-800 rounded-xl flex-1">
          <div class="p-4 border-b border-zinc-800">
              <h2 class="text-sm font-semibold text-zinc-100">Thao tác nhanh</h2>
              <p class="text-xs text-zinc-500 mt-1">Khởi chạy các tác vụ thu thập dữ liệu</p>
          </div>
          <div class="p-4 space-y-4">
              <!-- Action Buttons -->
              <div class="flex gap-3">
                  <button @click="startCrawl('daily')" :disabled="crawlLoading.daily"
                      class="flex-1 relative flex items-center justify-center gap-2 h-11 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-600/50 text-white text-sm font-medium rounded-lg transition-colors">
                      <template x-if="crawlLoading.daily">
                          <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                  stroke-width="4"></circle>
                              <path class="opacity-75" fill="currentColor"
                                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                              </path>
                          </svg>
                      </template>
                      <template x-if="!crawlLoading.daily">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                          </svg>
                      </template>
                      <span x-text="crawlLoading.daily ? 'Đang xử lý...' : 'Run Crawl Now'"></span>
                  </button>
                  <button @click="startCrawl('full')" :disabled="crawlLoading.full"
                      class="flex-1 relative flex items-center justify-center gap-2 h-11 bg-zinc-800 hover:bg-zinc-700 disabled:bg-zinc-800/50 text-zinc-100 text-sm font-medium rounded-lg border border-zinc-700 transition-colors">
                      <template x-if="crawlLoading.full">
                          <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                  stroke-width="4"></circle>
                              <path class="opacity-75" fill="currentColor"
                                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                              </path>
                          </svg>
                      </template>
                      <template x-if="!crawlLoading.full">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                          </svg>
                      </template>
                      <span x-text="crawlLoading.full ? 'Đang xử lý...' : 'Crawl Full'"></span>
                  </button>
              </div>

              <!-- Range Crawl Form -->
              <div class="pt-4 border-t border-zinc-800">
                  <h3 class="text-xs font-medium text-zinc-400 uppercase tracking-wide mb-3">Thu thập theo khoảng thời
                      gian</h3>
                  <form @submit.prevent="submitRangeCrawl" class="space-y-3">
                      <div class="grid grid-cols-2 gap-3">
                          <div>
                              <label class="block text-xs font-medium text-zinc-400 mb-1.5">Từ ngày</label>
                              <input type="date" x-model="rangeForm.startDate"
                                  :class="rangeForm.errors.startDate ? 'border-red-500 focus:ring-red-500' :
                                      'border-zinc-700 focus:ring-blue-500'"
                                  class="w-full h-10 bg-zinc-800 border rounded-lg px-3 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-offset-0">
                              <p x-show="rangeForm.errors.startDate" x-text="rangeForm.errors.startDate"
                                  class="mt-1 text-xs text-red-500"></p>
                          </div>
                          <div>
                              <label class="block text-xs font-medium text-zinc-400 mb-1.5">Đến ngày</label>
                              <input type="date" x-model="rangeForm.endDate"
                                  :class="rangeForm.errors.endDate ? 'border-red-500 focus:ring-red-500' :
                                      'border-zinc-700 focus:ring-blue-500'"
                                  class="w-full h-10 bg-zinc-800 border rounded-lg px-3 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-offset-0">
                              <p x-show="rangeForm.errors.endDate" x-text="rangeForm.errors.endDate"
                                  class="mt-1 text-xs text-red-500"></p>
                          </div>
                      </div>
                      <button type="submit" :disabled="crawlLoading.range"
                          class="w-full flex items-center justify-center gap-2 h-10 bg-zinc-800 hover:bg-zinc-700 disabled:bg-zinc-800/50 text-zinc-100 text-sm font-medium rounded-lg border border-zinc-700 transition-colors">
                          <template x-if="crawlLoading.range">
                              <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                      stroke-width="4"></circle>
                                  <path class="opacity-75" fill="currentColor"
                                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                  </path>
                              </svg>
                          </template>
                          <span x-text="crawlLoading.range ? 'Đang xử lý...' : 'Crawl Range'"></span>
                      </button>
                  </form>
              </div>
          </div>
      </div>

      <!-- ================================ -->
      <!-- SECTION 5: Queue Health Widget -->
      <!-- ================================ -->
      {{-- <div class="bg-zinc-900 border border-zinc-800 rounded-xl">
          <div class="p-4 border-b border-zinc-800">
              <h2 class="text-sm font-semibold text-zinc-100">Queue Health</h2>
              <p class="text-xs text-zinc-500 mt-1">Trạng thái các hàng đợi xử lý</p>
          </div>
          <div class="p-4 space-y-4">
              <template x-for="queue in queues" :key="queue.name">
                  <div class="p-3 bg-zinc-800/50 rounded-lg">
                      <div class="flex items-center justify-between mb-2">
                          <div class="flex items-center gap-2">
                              <span class="font-mono text-sm text-zinc-100" x-text="queue.name"></span>
                              <span
                                  :class="queue.status === 'healthy' ? 'bg-emerald-500/10 text-emerald-500' :
                                      'bg-red-500/10 text-red-500'"
                                  class="text-xs font-medium px-1.5 py-0.5 rounded"
                                  x-text="queue.status === 'healthy' ? 'Healthy' : 'Warning'"></span>
                          </div>
                          <span class="text-xs text-zinc-500" x-text="queue.throughput + '/min'"></span>
                      </div>
                      <!-- Progress Bar -->
                      <div class="h-1.5 bg-zinc-700 rounded-full overflow-hidden mb-3">
                          <div class="h-full bg-blue-500 rounded-full transition-all duration-500"
                              :style="'width: ' + (queue.active / (queue.active + queue.waiting + queue.failed) * 100 || 0) +
                              '%'">
                          </div>
                      </div>
                      <!-- Stats -->
                      <div class="grid grid-cols-3 gap-2 text-center">
                          <div>
                              <p class="text-lg font-bold text-blue-400 font-mono" x-text="queue.active"></p>
                              <p class="text-xs text-zinc-500">Active</p>
                          </div>
                          <div>
                              <p class="text-lg font-bold text-amber-400 font-mono" x-text="queue.waiting"></p>
                              <p class="text-xs text-zinc-500">Waiting</p>
                          </div>
                          <div>
                              <p class="text-lg font-bold text-red-400 font-mono" x-text="queue.failed"></p>
                              <p class="text-xs text-zinc-500">Failed</p>
                          </div>
                      </div>
                  </div>
              </template>
          </div>
      </div> --}}
  </div>


  <script>
      document.addEventListener('alpine:init', () => {

          Alpine.data('crawlActions', () => ({

              crawlLoading: {
                  daily: false,
                  full: false,
                  range: false
              },

              rangeForm: {
                  startDate: '',
                  endDate: '',
                  errors: {}
              },

              queues: [{
                      name: 'crawl',
                      status: 'healthy',
                      throughput: 0,
                      active: 0,
                      waiting: 0,
                      failed: 0
                  },
                  {
                      name: 'detail',
                      status: 'healthy',
                      throughput: 0,
                      active: 0,
                      waiting: 0,
                      failed: 0
                  },
                  {
                      name: 'sub',
                      status: 'healthy',
                      throughput: 0,
                      active: 0,
                      waiting: 0,
                      failed: 0
                  }
              ],

              async startCrawl(type) {

                  if (this.crawlLoading[type]) {
                      return;
                  }

                  const messages = {
                      daily: 'Bạn có chắc muốn crawl dữ liệu hôm nay?',
                      full: 'Bạn có chắc muốn crawl toàn bộ dữ liệu?'
                  };

                  const confirmed =
                      confirm(messages[type]);

                  if (!confirmed) {
                      return;
                  }

                  this.crawlLoading[type] =
                      true;

                  try {

                      const response =
                          await fetch(
                              `/api/crawl/${type}`, {
                                  method: 'POST',

                                  headers: {
                                      Accept: 'application/json',
                                      'Content-Type': 'application/json',

                                      'X-CSRF-TOKEN': document.querySelector(
                                          'meta[name="csrf-token"]'
                                      )?.content
                                  }
                              }
                          );

                      const data =
                          await response.json();

                      if (response.status === 401 || response.status === 419) {
                          window.location.href = '/login';
                          return;
                      }

                      if (!response.ok) {
                          throw new Error(
                              data.message ??
                              'Có lỗi xảy ra'
                          );
                      }

                      this.showToast(
                          `${type.toUpperCase()} crawl đã bắt đầu`,
                          'success'
                      );

                      window.dispatchEvent(
                          new CustomEvent(
                              'crawl-started', {
                                  detail: {
                                      type,
                                      taskId: data.task_id
                                  }
                              }
                          )
                      );

                      console.log(
                          'Task ID:',
                          data.task_id
                      );

                  } catch (error) {

                      console.error(error);

                      this.showToast(
                          error.message,
                          'error'
                      );

                  } finally {

                      this.crawlLoading[type] =
                          false;
                  }
              },

              async submitRangeCrawl() {

                  this.rangeForm.errors = {};

                  if (
                      this.crawlLoading.range
                  ) {
                      return;
                  }

                  const {
                      startDate,
                      endDate
                  } = this.rangeForm;

                  if (!startDate) {

                      this.rangeForm.errors.startDate =
                          'Vui lòng chọn ngày bắt đầu';

                      return;
                  }

                  if (!endDate) {

                      this.rangeForm.errors.endDate =
                          'Vui lòng chọn ngày kết thúc';

                      return;
                  }

                  const confirmed =
                      confirm(
                          `Bạn có chắc muốn crawl từ ${startDate} đến ${endDate}?`
                      );

                  if (!confirmed) {
                      return;
                  }

                  this.crawlLoading.range =
                      true;

                  try {

                      const response =
                          await fetch(
                              '/api/crawl/range', {
                                  method: 'POST',

                                  headers: {
                                      Accept: 'application/json',

                                      'Content-Type': 'application/json',

                                      'X-CSRF-TOKEN': document.querySelector(
                                          'meta[name="csrf-token"]'
                                      )?.content
                                  },

                                  body: JSON.stringify({
                                      from_date: startDate,

                                      to_date: endDate
                                  })
                              }
                          );

                      const data =
                          await response.json();

                      if (
                          response.status ===
                          422
                      ) {

                          this.rangeForm.errors = {

                              startDate: data.errors
                                  ?.from_date?.[
                                      0
                                  ],

                              endDate: data.errors
                                  ?.to_date?.[
                                      0
                                  ]
                          };

                          return;
                      }

                      if (response.status === 401 || response.status === 419) {
                          window.location.href = '/';
                          return;
                      }

                      if (!response.ok) {

                          throw new Error(
                              data.message ??
                              'Có lỗi xảy ra'
                          );
                      }

                      this.showToast(
                          'Range crawl đã bắt đầu',
                          'success'
                      );

                      window.dispatchEvent(
                          new CustomEvent(
                              'crawl-started', {
                                  detail: {
                                      type: 'range',
                                      taskId: data.task_id
                                  }
                              }
                          )
                      );

                      this.rangeForm.startDate =
                          '';

                      this.rangeForm.endDate =
                          '';

                  } catch (error) {

                      console.error(error);

                      this.showToast(
                          error.message,
                          'error'
                      );

                  } finally {

                      this.crawlLoading.range =
                          false;
                  }
              },

              showToast(
                  message,
                  type = 'success'
              ) {

                  const toast =
                      document.createElement(
                          'div'
                      );

                  toast.className = `
                        fixed top-5 right-5 z-[99999]
                        px-4 py-3 rounded-lg
                        text-sm font-medium text-white
                        shadow-xl
                        transition-all duration-300
                    `;

                  toast.style.backgroundColor =
                      type === 'success' ?
                      '#16a34a' :
                      '#dc2626';

                  toast.innerText =
                      message;

                  document.body.appendChild(
                      toast
                  );

                  setTimeout(() => {
                      toast.style.opacity =
                          '0';
                  }, 2500);

                  setTimeout(() => {
                      toast.remove();
                  }, 3000);
              }

          }));
      });
  </script>
