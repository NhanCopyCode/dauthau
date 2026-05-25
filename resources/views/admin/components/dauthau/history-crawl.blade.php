  <!-- ================================ -->
  <!-- SECTION 3: Crawl History Table -->
  <!-- ================================ -->
  <div x-data="crawlHistory" x-init="init()" class="bg-zinc-900 border border-zinc-800 rounded-xl">
      <div class="p-4 border-b border-zinc-800">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
              <div>
                  <h2 class="text-sm font-semibold text-zinc-100">Lịch sử Crawl</h2>
                  <p class="text-xs text-zinc-500 mt-1">Danh sách các phiên thu thập gần đây</p>
              </div>
              <div class="flex items-center gap-3">
                  <!-- View State Toggle -->
                  <div class="flex items-center gap-1 p-1 bg-zinc-800 rounded-lg">
                      <button @click="tableViewState = 'data'"
                          :class="tableViewState === 'data' ? 'bg-zinc-700 text-zinc-100' :
                              'text-zinc-400 hover:text-zinc-100'"
                          class="px-3 py-1.5 text-xs font-medium rounded-md transition-colors">Data</button>
                      <button @click="tableViewState = 'loading'"
                          :class="tableViewState === 'loading' ? 'bg-zinc-700 text-zinc-100' :
                              'text-zinc-400 hover:text-zinc-100'"
                          class="px-3 py-1.5 text-xs font-medium rounded-md transition-colors">Loading</button>
                      <button @click="tableViewState = 'empty'"
                          :class="tableViewState === 'empty' ? 'bg-zinc-700 text-zinc-100' :
                              'text-zinc-400 hover:text-zinc-100'"
                          class="px-3 py-1.5 text-xs font-medium rounded-md transition-colors">Empty</button>
                  </div>
                  <!-- Search -->
                  <div class="relative">
                      <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-500" fill="none"
                          stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                      </svg>
                      <input type="text" x-model="tableSearch" placeholder="Tìm kiếm..."
                          class="w-48 h-9 bg-zinc-800 border border-zinc-700 rounded-lg pl-9 pr-3 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                  </div>
                  <!-- Filter -->
                  <div class="relative" x-data="{ open: false }">
                      <button @click="open = !open"
                          class="flex items-center gap-2 h-9 px-3 bg-zinc-800 border border-zinc-700 rounded-lg text-sm text-zinc-300 hover:text-zinc-100 transition-colors">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                          </svg>
                          <span x-text="tableFilter === 'all' ? 'Tất cả' : tableFilter"></span>
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 9l-7 7-7-7" />
                          </svg>
                      </button>
                      <div x-show="open" @click.away="open = false" x-transition
                          class="absolute right-0 mt-2 w-40 bg-zinc-900 border border-zinc-800 rounded-lg shadow-xl z-10">
                          <button @click="tableFilter = 'all'; open = false"
                              class="w-full px-3 py-2 text-left text-sm text-zinc-300 hover:bg-zinc-800/50 hover:text-zinc-100">Tất
                              cả</button>
                          <button @click="tableFilter = 'Completed'; open = false"
                              class="w-full px-3 py-2 text-left text-sm text-zinc-300 hover:bg-zinc-800/50 hover:text-zinc-100">Completed</button>
                          <button @click="tableFilter = 'Running'; open = false"
                              class="w-full px-3 py-2 text-left text-sm text-zinc-300 hover:bg-zinc-800/50 hover:text-zinc-100">Running</button>
                          <button @click="tableFilter = 'Failed'; open = false"
                              class="w-full px-3 py-2 text-left text-sm text-zinc-300 hover:bg-zinc-800/50 hover:text-zinc-100">Failed</button>
                          <button @click="tableFilter = 'Pending'; open = false"
                              class="w-full px-3 py-2 text-left text-sm text-zinc-300 hover:bg-zinc-800/50 hover:text-zinc-100">Pending</button>
                      </div>
                  </div>
              </div>
          </div>
      </div>

      <!-- Table -->
      <div class="table-container overflow-x-auto" @scroll="$el.classList.toggle('scrolled', $el.scrollTop > 0)">
          <!-- Loading State -->
          <template x-if="tableState  === 'loading'">
              <div class="p-4 space-y-3">
                  <template x-for="i in 5" :key="i">
                      <div class="flex items-center gap-4">
                          <div class="skeleton h-4 w-24 rounded"></div>
                          <div class="skeleton h-4 w-16 rounded"></div>
                          <div class="skeleton h-4 w-32 rounded"></div>
                          <div class="skeleton h-4 w-20 rounded"></div>
                          <div class="skeleton h-4 w-16 rounded"></div>
                          <div class="skeleton h-4 w-24 rounded"></div>
                          <div class="flex-1"></div>
                          <div class="skeleton h-8 w-24 rounded"></div>
                      </div>
                  </template>
              </div>
          </template>

          <!-- Empty State -->
          <template x-if="tableState  === 'empty'">
              <div class="py-16 text-center">
                  <div class="w-16 h-16 mx-auto bg-zinc-800 rounded-full flex items-center justify-center mb-4">
                      <svg class="w-8 h-8 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                      </svg>
                  </div>
                  <h3 class="text-sm font-medium text-zinc-300">Chưa có dữ liệu</h3>
                  <p class="text-xs text-zinc-500 mt-1">Khởi chạy một tác vụ crawl để bắt đầu</p>
              </div>
          </template>

          <!-- Data State -->
          <template x-if="tableState  === 'data'">
              <table class="w-full">
                  <thead class="sticky top-0 bg-zinc-900 z-10">
                      <tr class="border-b border-zinc-800">
                          <th class="text-left text-xs font-medium text-zinc-500 uppercase tracking-wide px-4 py-3">
                              ID
                          </th>

                          <th class="text-left text-xs font-medium text-zinc-500 uppercase tracking-wide px-4 py-3">
                              Loại
                          </th>

                          <!-- NEW -->
                          <th class="text-left text-xs font-medium text-zinc-500 uppercase tracking-wide px-4 py-3">
                              Khoảng crawl
                          </th>

                          <th class="text-left text-xs font-medium text-zinc-500 uppercase tracking-wide px-4 py-3">
                              Thời gian bắt đầu
                          </th>

                          <th class="text-left text-xs font-medium text-zinc-500 uppercase tracking-wide px-4 py-3">
                              Thời lượng
                          </th>

                          <th class="text-left text-xs font-medium text-zinc-500 uppercase tracking-wide px-4 py-3">
                              Trạng thái
                          </th>

                          <th class="text-left text-xs font-medium text-zinc-500 uppercase tracking-wide px-4 py-3">
                              Kết quả
                          </th>

                          <th class="text-right text-xs font-medium text-zinc-500 uppercase tracking-wide px-4 py-3">
                              Thao tác
                          </th>
                      </tr>
                  </thead>


                  <tbody class="divide-y divide-zinc-800/50">

                      <template x-for="task in filteredTasks" :key="task.id">
                          <tr class="hover:bg-zinc-800/30 transition-colors">

                              <!-- ID -->
                              <td class="px-4 py-3">
                                  <span class="font-mono text-sm text-zinc-300" x-text="'#' + task.id"></span>
                              </td>

                              <!-- TYPE -->
                              <td class="px-4 py-3">
                                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium"
                                      :class="{
                                          'bg-blue-500/10 text-blue-400': task.type === 'daily',
                                      
                                          'bg-violet-500/10 text-violet-400': task.type === 'full',
                                      
                                          'bg-amber-500/10 text-amber-400': task.type === 'range'
                                      }"
                                      x-text="
                                        task.type.charAt(0)
                                            .toUpperCase()
                                        +
                                        task.type.slice(1)
                                    "></span>
                              </td>

                              <!-- CRAWL RANGE -->
                              <td class="px-4 py-3">

                                  <!-- FULL -->
                                  <template x-if="task.type === 'full'">
                                      <span class="text-sm text-zinc-500">-</span>
                                  </template>

                                  <!-- DAILY -->
                                  <template
                                      x-if="
                                        task.type === 'daily'
                                        && task.from_date
                                    ">
                                      <span class="text-sm text-zinc-300 font-mono"
                                          x-text="
                                            formatDate(
                                                task.from_date
                                            )
                                        ">
                                      </span>
                                  </template>

                                  <!-- RANGE -->
                                  <template
                                      x-if="
                                        task.type === 'range'
                                        && task.from_date
                                        && task.to_date
                                    ">
                                      <span class="text-sm text-zinc-300 font-mono"
                                          x-text="
                                            formatDate(task.from_date)
                                            +
                                            ' → '
                                            +
                                            formatDate(task.to_date)
                                        ">
                                      </span>
                                  </template>

                              </td>

                              <!-- START TIME -->
                              <td class="px-4 py-3">
                                  <span class="text-sm text-zinc-300"
                                      x-text="
                        formatDate(
                            task.started_at
                        )
                    "></span>
                              </td>

                              <!-- DURATION -->
                              <td class="px-4 py-3">
                                  <span class="text-sm text-zinc-400 font-mono"
                                      x-text="
                        formatDuration(task)
                    "></span>
                              </td>

                              <!-- STATUS -->
                              <td class="px-4 py-3">
                                  <span
                                      class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-medium"
                                      :class="{
                                          'bg-emerald-500/10 text-emerald-500': task.status === 'completed',
                                      
                                          'bg-blue-500/10 text-blue-400': task.status === 'running',
                                      
                                          'bg-red-500/10 text-red-500': task.status === 'failed'
                                      }">

                                      <span class="w-1.5 h-1.5 rounded-full"
                                          :class="{
                                              'bg-emerald-500': task.status === 'completed',
                                          
                                              'bg-blue-400 animate-pulse': task.status === 'running',
                                          
                                              'bg-red-500': task.status === 'failed'
                                          }"></span>

                                      <span
                                          x-text="
                            task.status.charAt(0)
                            .toUpperCase()
                            +
                            task.status.slice(1)
                        "></span>
                                  </span>
                              </td>

                              <!-- RESULT -->
                              <td class="px-4 py-3">
                                  <span class="text-sm text-zinc-300"
                                      x-text="
                        (task.total_items ?? 0)
                        + ' items'
                    "></span>
                              </td>

                              <!-- ACTION -->
                              <td class="px-4 py-3">
                                  <div class="flex items-center justify-end gap-1">

                                      <!-- View -->
                                      <button
                                          class="p-1.5 text-zinc-500 hover:text-zinc-100 hover:bg-zinc-800 rounded transition-colors"
                                          title="Xem chi tiết">
                                          <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                              viewBox="0 0 24 24">
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0
                                                    3 3 0 016 0z" />

                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M2.458 12C3.732 7.943
                                                    7.523 5 12 5
                                                    c4.478 0 8.268 2.943
                                                    9.542 7
                                                    -1.274 4.057-5.064 7
                                                    -9.542 7
                                                    -4.477 0-8.268-2.943
                                                    -9.542-7z" />
                                          </svg>
                                      </button>

                                      <!-- Retry -->
                                      <template x-if="task.status === 'failed'">
                                          <button
                                              class="p-1.5 text-zinc-500 hover:text-amber-400 hover:bg-zinc-800 rounded transition-colors"
                                              title="Thử lại">
                                              <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                  viewBox="0 0 24 24">
                                                  <path stroke-linecap="round" stroke-linejoin="round"
                                                      stroke-width="2" d="M4 4v5h.582m15.356 2
                                                        A8.001 8.001 0 004.582 9
                                                        m0 0H9m11 11v-5h-.581
                                                        m0 0a8.003 8.003 0
                                                        01-15.357-2
                                                        m15.357 2H15" />
                                              </svg>
                                          </button>
                                      </template>

                                      <!-- Logs -->
                                      <button
                                          class="p-1.5 text-zinc-500 hover:text-zinc-100 hover:bg-zinc-800 rounded transition-colors"
                                          title="Xem logs">
                                          <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                              viewBox="0 0 24 24">
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 12h6m-6 4h6m2 5H7
                                                    a2 2 0 01-2-2V5a2 2 0
                                                    012-2h5.586a1 1 0
                                                    01.707.293
                                                    l5.414 5.414a1 1 0
                                                    01.293.707V19
                                                    a2 2 0 01-2 2z" />
                                          </svg>
                                      </button>

                                  </div>
                              </td>

                          </tr>
                      </template>

                  </tbody>
              </table>
          </template>
      </div>

      <!-- Pagination -->
      <template x-if="tableState  === 'data'">
          <div class="px-4 py-3 border-t border-zinc-800 flex items-center justify-between">
              <p class="text-xs text-zinc-500">Hiển thị <span class="font-medium text-zinc-300">1-10</span> trong
                  <span class="font-medium text-zinc-300">156</span> kết quả
              </p>
              <div class="flex items-center gap-1">
                  <button
                      class="w-8 h-8 flex items-center justify-center text-zinc-500 hover:text-zinc-100 hover:bg-zinc-800 rounded transition-colors disabled:opacity-50"
                      disabled>
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 19l-7-7 7-7" />
                      </svg>
                  </button>
                  <button
                      class="w-8 h-8 flex items-center justify-center text-zinc-100 bg-blue-600 rounded text-sm font-medium">1</button>
                  <button
                      class="w-8 h-8 flex items-center justify-center text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800 rounded text-sm font-medium transition-colors">2</button>
                  <button
                      class="w-8 h-8 flex items-center justify-center text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800 rounded text-sm font-medium transition-colors">3</button>
                  <span class="w-8 h-8 flex items-center justify-center text-zinc-500 text-sm">...</span>
                  <button
                      class="w-8 h-8 flex items-center justify-center text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800 rounded text-sm font-medium transition-colors">16</button>
                  <button
                      class="w-8 h-8 flex items-center justify-center text-zinc-500 hover:text-zinc-100 hover:bg-zinc-800 rounded transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                      </svg>
                  </button>
              </div>
          </div>
      </template>
  </div>

  <script>
      document.addEventListener('alpine:init', () => {

          Alpine.data('crawlHistory', () => ({

              tasks: @json($initialTasks ?? []),

              tableViewState: 'data',
              tableSearch: '',
              tableFilter: 'all',

              loading: true,
              error: null,

              polling: null,
              timer: null,

              isFetching: false,
              now: Date.now(),

              lastHasRunningTask: null,


              crawlStartedHandler: null,

              init() {
                  this.fetchHistory(true);
                  this.crawlStartedHandler =
                      () => {

                          console.log(
                              'crawl started → refresh history'
                          );
                          this.fetchHistory(
                              true
                          );
                      };

                  window.addEventListener(
                      'crawl-started',
                      this.crawlStartedHandler
                  );
                  this.timer =
                      setInterval(() => {
                          this.now =
                              Date.now();
                      }, 1000);
              },

              destroy() {

                  this.stopPolling();

                  if (this.timer) {

                      clearInterval(
                          this.timer
                      );

                      this.timer =
                          null;
                  }

                  if (
                      this.crawlStartedHandler
                  ) {

                      window.removeEventListener(
                          'crawl-started',
                          this
                          .crawlStartedHandler
                      );
                  }
              },

              get tableState() {

                  if (this.loading) {
                      return 'loading';
                  }

                  if (this.error) {
                      return 'error';
                  }

                  if (
                      this.filteredTasks
                      .length === 0
                  ) {
                      return 'empty';
                  }

                  return 'data';
              },

              get filteredTasks() {

                  return this.tasks.filter(
                      task => {

                          const keyword =
                              this.tableSearch
                              .trim()
                              .toLowerCase();

                          const searchFields = [
                                  task.id
                                  ?.toString(),

                                  task.type,

                                  task.status,

                                  task
                                  .processed_items
                                  ?.toString()
                              ]
                              .filter(Boolean)
                              .join(' ')
                              .toLowerCase();

                          const matchesSearch =
                              keyword === '' ||

                              searchFields.includes(
                                  keyword
                              );

                          const matchesFilter =
                              this
                              .tableFilter ===
                              'all' ||

                              task.status
                              ?.toLowerCase() ===
                              this
                              .tableFilter
                              .toLowerCase();

                          return (
                              matchesSearch &&
                              matchesFilter
                          );
                      }
                  );
              },

              async fetchHistory(
                  force = false
              ) {

                  if (
                      this.isFetching &&
                      !force
                  ) {
                      return;
                  }

                  this.isFetching =
                      true;

                  try {

                      if (
                          this.tasks
                          .length === 0 &&
                          !this.error
                      ) {

                          this.loading =
                              true;
                      }

                      this.error = null;

                      const response =
                          await fetch(
                              '/api/crawl/history', {
                                  headers: {
                                      Accept: 'application/json'
                                  }
                              }
                          );

                      if (!response.ok) {

                          throw new Error(
                              `HTTP ${response.status}`
                          );
                      }

                      const data =
                          await response.json();

                      this.tasks =
                          Array.isArray(
                              data.tasks
                          ) ?
                          data.tasks : [];

                      this.handlePolling();

                  } catch (error) {

                      console.error(
                          'History fetch failed:',
                          error
                      );

                      this.error =
                          'Không thể tải lịch sử crawl';

                  } finally {

                      this.loading =
                          false;

                      this.isFetching =
                          false;
                  }
              },

              handlePolling() {

                  const hasRunningTask =
                      this.tasks.some(
                          task =>
                          task.status ===
                          'running'
                      );
                  if (
                      this
                      .lastHasRunningTask ===
                      hasRunningTask
                  ) {
                      return;
                  }

                  this.lastHasRunningTask =
                      hasRunningTask;

                  this.startPolling();
              },

              startPolling() {

                  this.stopPolling();

                  const interval =
                      this
                      .lastHasRunningTask ?
                      2000 :
                      10000;

                  console.log(
                      `Polling every ${interval / 1000}s`
                  );

                  this.polling =
                      setInterval(() => {

                          this.fetchHistory();

                      }, interval);
              },

              stopPolling() {

                  if (this.polling) {

                      clearInterval(
                          this.polling
                      );

                      this.polling =
                          null;
                  }
              },

              parseDate(date) {

                  if (!date) {
                      return null;
                  }

                  return new Date(
                      date.replace(
                          ' ',
                          'T'
                      )
                  );
              },

              formatDuration(task) {

                  if (!task.started_at) {
                      return '-';
                  }

                  const start =
                      this.parseDate(
                          task.started_at
                      );

                  const end =
                      task.finished_at ?

                      this.parseDate(
                          task.finished_at
                      ) :

                      new Date(
                          this.now
                      );

                  if (!start || !end) {
                      return '-';
                  }

                  const seconds =
                      Math.max(
                          0,
                          Math.floor(
                              (end - start) /
                              1000
                          )
                      );

                  if (seconds < 60) {
                      return `${seconds}s`;
                  }

                  const hours =
                      Math.floor(
                          seconds / 3600
                      );

                  const mins =
                      Math.floor(
                          (seconds %
                              3600) / 60
                      );

                  const secs =
                      seconds % 60;

                  if (hours > 0) {
                      return `${hours}h ${mins}m`;
                  }

                  return `${mins}m ${secs}s`;
              },

              formatDate(date, withTime = true) {

                  if (!date) {
                      return '-';
                  }

                  if (
                      typeof date === 'string' &&
                      /^\d{4}-\d{2}-\d{2}$/.test(date)
                  ) {

                      const [
                          year,
                          month,
                          day
                      ] = date.split('-');

                      return withTime ?
                          `${day}/${month}/${year}` :
                          `${day}/${month}/${year}`;
                  }

                  const parsed =
                      this.parseDate(date);

                  if (!parsed) {
                      return '-';
                  }

                  return parsed
                      .toLocaleString(
                          'vi-VN', {
                              timeZone: 'Asia/Ho_Chi_Minh',

                              year: 'numeric',
                              month: '2-digit',
                              day: '2-digit',

                              ...(withTime && {
                                  hour: '2-digit',
                                  minute: '2-digit',
                                  second: '2-digit'
                              })
                          }
                      );
              }

          }));
      });
  </script>

  {{-- <tbody class="divide-y divide-zinc-800/50">
                      @forelse($crawlTasks as $task)
                          <tr class="hover:bg-zinc-800/30 transition-colors">

                              <td class="px-4 py-3">
                                  <span class="font-mono text-sm text-zinc-300">
                                      #{{ $task->id }}
                                  </span>
                              </td>

                              <td class="px-4 py-3">
                                  <span
                                      class="
                                        inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium
                                            @if ($task->type === 'daily') bg-blue-500/10 text-blue-400
                                            @elseif($task->type === 'full')
                                                bg-violet-500/10 text-violet-400
                                            @else
                                                bg-amber-500/10 text-amber-400 @endif
                                        ">
                                      {{ ucfirst($task->type) }}
                                  </span>
                              </td>

                              <td class="px-4 py-3">
                                  <span class="text-sm text-zinc-300">
                                      {{ optional($task->started_at)->format('d/m/Y H:i:s') }}
                                  </span>
                              </td>

                              <td class="px-4 py-3">
                                  <span class="text-sm text-zinc-400 font-mono">
                                      {{ $task->duration }}
                                  </span>
                              </td>

                              <td class="px-4 py-3">
                                  <span
                                      class="
                                        inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-medium

                                        @if ($task->status === 'completed') bg-emerald-500/10 text-emerald-500
                                        @elseif($task->status === 'running')
                                            bg-blue-500/10 text-blue-400
                                        @elseif($task->status === 'failed')
                                            bg-red-500/10 text-red-500
                                        @else
                                            bg-amber-500/10 text-amber-400 @endif
                                            ">
                                      <span
                                          class="
                        w-1.5 h-1.5 rounded-full

                        @if ($task->status === 'completed') bg-emerald-500
                        @elseif($task->status === 'running')
                            bg-blue-400 animate-pulse
                        @elseif($task->status === 'failed')
                            bg-red-500
                        @else
                            bg-amber-400 @endif
                    "></span>

                                      {{ ucfirst($task->status) }}
                                  </span>
                              </td>

                              <td class="px-4 py-3">
                                  <span class="text-sm text-zinc-300">
                                      {{ number_format($task->total_items) }}
                                      items
                                  </span>
                              </td>

                              <td class="px-4 py-3">
                                  <div class="flex items-center justify-end gap-1">

                                      <button
                                          class="p-1.5 text-zinc-500 hover:text-zinc-100 hover:bg-zinc-800 rounded transition-colors"
                                          title="Xem chi tiết">

                                          <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                              viewBox="0 0 24 24">
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5
                                c4.478 0 8.268 2.943 9.542 7
                                -1.274 4.057-5.064 7-9.542 7
                                -4.477 0-8.268-2.943-9.542-7z" />
                                          </svg>
                                      </button>

                                      @if ($task->status === 'failed')
                                          <button
                                              class="p-1.5 text-zinc-500 hover:text-amber-400 hover:bg-zinc-800 rounded transition-colors"
                                              title="Thử lại">

                                              <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                  viewBox="0 0 24 24">
                                                  <path stroke-linecap="round" stroke-linejoin="round"
                                                      stroke-width="2" d="M4 4v5h.582m15.356 2A8.001
                                    8.001 0 004.582 9m0 0H9m11
                                    11v-5h-.581m0 0a8.003 8.003
                                    0 01-15.357-2m15.357 2H15" />
                                              </svg>
                                          </button>
                                      @endif

                                      <button
                                          class="p-1.5 text-zinc-500 hover:text-zinc-100 hover:bg-zinc-800 rounded transition-colors"
                                          title="Xem logs">

                                          <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                              viewBox="0 0 24 24">
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 12h6m-6 4h6m2 5H7
                                a2 2 0 01-2-2V5a2 2 0
                                012-2h5.586a1 1 0 01.707.293
                                l5.414 5.414a1 1 0 01.293.707V19
                                a2 2 0 01-2 2z" />
                                          </svg>
                                      </button>

                                  </div>
                              </td>
                          </tr>
                      @empty
                          <tr>
                              <td colspan="7" class="py-16 text-center">
                                  <div
                                      class="w-16 h-16 mx-auto bg-zinc-800 rounded-full flex items-center justify-center mb-4">
                                      📭
                                  </div>

                                  <h3 class="text-sm font-medium text-zinc-300">
                                      Chưa có dữ liệu
                                  </h3>

                                  <p class="text-xs text-zinc-500 mt-1">
                                      Khởi chạy một tác vụ crawl để bắt đầu
                                  </p>
                              </td>
                          </tr>
                      @endforelse
                  </tbody> --}}
