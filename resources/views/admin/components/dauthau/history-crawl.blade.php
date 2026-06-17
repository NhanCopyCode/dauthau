  <!-- ================================ -->
  <!-- SECTION 3: Crawl History Table -->
  <!-- ================================ -->
  <div x-data="crawlHistory" x-init="init()" class="bg-zinc-900 border border-zinc-800 rounded-xl">

      <div class="p-4 border-b border-zinc-800">
          <div class="flex flex-col gap-4">
              <!-- Header -->
              <div>
                  <h2 class="text-sm font-semibold text-zinc-100">Lịch sử Crawl</h2>
                  <p class="text-xs text-zinc-500 mt-1">Danh sách các phiên thu thập gần đây</p>
              </div>

              <!-- Form (AJAX: prevents full page reload) -->
              <form @submit.prevent="applyFilters()" class="flex flex-col lg:flex-row lg:items-end gap-3">

                  <!-- Filter Groups -->
                  <div class="flex flex-col sm:flex-row gap-3 flex-1 min-w-0">

                      <!-- Khoảng dữ liệu crawl -->
                      <div class="flex flex-col gap-1 flex-1 min-w-0">
                          <label class="text-xs text-zinc-400">Khoảng dữ liệu crawl</label>
                          <div class="flex items-center gap-2">
                              <input type="date" name="from_date" x-model="fromDate"
                                  value="{{ request('from_date') }}"
                                  class="h-9 flex-1 min-w-0 bg-zinc-800 border border-zinc-700 rounded-lg px-2 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                              <span class="text-zinc-500 text-xs shrink-0">→</span>
                              <input type="date" name="to_date" x-model="toDate" value="{{ request('to_date') }}"
                                  class="h-9 flex-1 min-w-0 bg-zinc-800 border border-zinc-700 rounded-lg px-2 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                          </div>
                      </div>

                      <!-- Thời gian crawl -->
                      <div class="flex flex-col gap-1 flex-1 min-w-0">
                          <label class="text-xs text-zinc-400">Thời gian crawl</label>
                          <div class="flex items-center gap-2">
                              <input type="date" name="crawl_started_from" x-model="startedFrom"
                                  value="{{ request('crawl_started_from') }}"
                                  class="h-9 flex-1 min-w-0 bg-zinc-800 border border-zinc-700 rounded-lg px-2 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                              <span class="text-zinc-500 text-xs shrink-0">→</span>
                              <input type="date" name="crawl_started_to" x-model="startedTo"
                                  value="{{ request('crawl_started_to') }}"
                                  class="h-9 flex-1 min-w-0 bg-zinc-800 border border-zinc-700 rounded-lg px-2 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                          </div>
                      </div>
                  </div>

                  <!-- Actions + Status Filter -->
                  <div class="flex items-center gap-2 shrink-0">
                      <!-- Status dropdown -->
                      <div class="relative" @click.away="statusOpen = false">
                          <input type="hidden" name="status" x-bind:value="tableFilter">
                          <button type="button" @click="statusOpen = !statusOpen"
                              class="flex items-center gap-2 h-9 px-3 bg-zinc-800 border border-zinc-700 rounded-lg text-sm text-zinc-300 hover:text-zinc-100 transition-colors whitespace-nowrap">
                              <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                              </svg>
                              <span x-text="tableFilter === 'all' ? 'Tất cả' : formatStatus(tableFilter)"></span>
                              <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 9l-7 7-7-7" />
                              </svg>
                          </button>
                          <div x-show="statusOpen" x-transition
                              class="absolute right-0 mt-2 w-40 bg-zinc-900 border border-zinc-800 rounded-lg shadow-xl z-20">
                              <button type="button" @click="tableFilter = 'all'; statusOpen = false"
                                  class="w-full px-3 py-2 text-left text-sm text-zinc-300 hover:bg-zinc-800/50 hover:text-zinc-100">Tất
                                  cả</button>
                              <button type="button" @click="tableFilter = 'running'; statusOpen = false"
                                  class="w-full px-3 py-2 text-left text-sm text-zinc-300 hover:bg-zinc-800/50 hover:text-zinc-100">Running</button>
                              <button type="button" @click="tableFilter = 'completed'; statusOpen = false"
                                  class="w-full px-3 py-2 text-left text-sm text-zinc-300 hover:bg-zinc-800/50 hover:text-zinc-100">Completed</button>
                              <button type="button" @click="tableFilter = 'completed_with_errors'; statusOpen = false"
                                  class="w-full px-3 py-2 text-left text-sm text-zinc-300 hover:bg-zinc-800/50 hover:text-zinc-100">Completed
                                  (with errors)</button>
                              <button type="button" @click="tableFilter = 'failed'; statusOpen = false"
                                  class="w-full px-3 py-2 text-left text-sm text-zinc-300 hover:bg-zinc-800/50 hover:text-zinc-100">Failed</button>
                          </div>
                      </div>

                      <button type="submit"
                          class="h-9 px-4 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-sm font-medium transition-colors whitespace-nowrap">
                          Tìm
                      </button>
                      <button type="button" @click.prevent="confirmRetryAll()" x-show="failedCount > 0"
                          class="h-9 inline-flex items-center gap-1.5 px-3 bg-amber-600 hover:bg-amber-500 text-white rounded-lg text-sm font-medium transition-colors whitespace-nowrap"
                          title="Retry tất cả task bị lỗi">
                          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                          </svg>
                          <span>Retry</span>
                          <span
                              class="inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[11px] font-bold bg-white/20 rounded-full"
                              x-text="failedCount"></span>
                      </button>
                      <button type="button" @click.prevent="resetFilters()"
                          class="h-9 inline-flex items-center px-3 bg-zinc-800 text-zinc-300 border border-zinc-700 rounded-lg text-sm hover:text-zinc-100 transition-colors whitespace-nowrap">
                          Reset
                      </button>
                  </div>
              </form>
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

                          <th class="text-left text-xs font-medium text-zinc-500 uppercase tracking-wide px-4 py-3">
                              Lỗi
                          </th>

                          <th class="text-right text-xs font-medium text-zinc-500 uppercase tracking-wide px-4 py-3">
                              Thao tác
                          </th>
                      </tr>
                  </thead>

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
                              <template x-if="task.type === 'daily'">
                                  <span class="text-sm text-zinc-300"
                                      x-text="task.from_date ? formatDate(task.from_date, false) : '-'"></span>
                              </template>

                              <!-- RANGE -->
                              <template x-if="task.type === 'range'">
                                  <span class="text-sm text-zinc-300"
                                      x-text="(task.from_date ? formatDate(task.from_date, false) : '-') + ' → ' + (task.to_date ? formatDate(task.to_date, false) : '-')"></span>
                              </template>

                          </td>

                          <!-- STARTED AT -->
                          <td class="px-4 py-3">
                              <span class="text-sm text-zinc-300 font-mono"
                                  x-text="task.started_at ? formatDate(task.started_at, true) : '-'"></span>
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
                              <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-medium"
                                  :class="{
                                      'bg-emerald-500/10 text-emerald-500': task.status === 'completed',
                                      'bg-amber-500/10 text-amber-400': task.status === 'completed_with_errors',
                                      'bg-blue-500/10 text-blue-400': task.status === 'running',
                                      'bg-red-500/10 text-red-500': task.status === 'failed'
                                  }">

                                  <span class="w-1.5 h-1.5 rounded-full"
                                      :class="{
                                          'bg-emerald-500': task.status === 'completed',
                                          'bg-amber-400': task.status === 'completed_with_errors',
                                          'bg-blue-400 animate-pulse': task.status === 'running',
                                          'bg-red-500': task.status === 'failed'
                                      }"></span>

                                  <span x-text="formatStatus(task.status)"></span>
                              </span>
                          </td>

                          <!-- RESULT -->
                          <td class="px-4 py-3">
                              <span class="text-sm text-zinc-300" x-html="formatResult(task)"></span>
                          </td>

                          <!-- FAILED ITEMS -->
                          <td class="px-4 py-3">
                              <span class="text-sm text-red-400 font-mono" x-text="(task.failed_items || 0)"></span>
                          </td>

                          <!-- ACTION -->
                          <td class="px-4 py-3">
                              <div class="flex items-center justify-end gap-1">

                                  <!-- View (only for authenticated users) -->
                                  @auth
                                      <button
                                          class="p-1.5 text-zinc-500 hover:text-zinc-100 hover:bg-zinc-800 rounded transition-colors"
                                          @click="window.dispatchEvent(new CustomEvent('open-task-detail', { detail: task }))"
                                          title="Xem chi tiết">
                                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0
                                                                        3 3 0 016 0z" />

                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943
                                                                        7.523 5 12 5
                                                                        c4.478 0 8.268 2.943
                                                                        9.542 7
                                                                        -1.274 4.057-5.064 7
                                                                        -9.542 7
                                                                        -4.477 0-8.268-2.943
                                                                        -9.542-7z" />
                                          </svg>
                                      </button>
                                  @else
                                      <a href="{{ route('login') }}"
                                          class="p-1.5 text-zinc-500 hover:text-zinc-100 hover:bg-zinc-800 rounded transition-colors"
                                          title="Đăng nhập để xem chi tiết">
                                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0
                                                                        3 3 0 016 0z" />
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943
                                                                        7.523 5 12 5
                                                                        c4.478 0 8.268 2.943
                                                                        9.542 7
                                                                        -1.274 4.057-5.064 7
                                                                        -9.542 7
                                                                        -4.477 0-8.268-2.943
                                                                        -9.542-7z" />
                                          </svg>
                                      </a>
                                  @endauth

                                  <!-- Retry -->
                                  <template x-if="task.status === 'failed' || task.status === 'completed_with_errors'">
                                      <button @click="confirmRetry(task)"
                                          class="p-1.5 text-zinc-500 hover:text-amber-400 hover:bg-zinc-800 rounded transition-colors"
                                          title="Thử lại">
                                          <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                              viewBox="0 0 24 24">
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M4 4v5h.582m15.356 2
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
                                      @click="openLogs(task)" title="Xem logs">
                                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7
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
          <div class="px-4 py-3 border-t border-zinc-800 flex items-center justify-end">
              {{-- <p class="text-xs text-zinc-500">Hiển thị
                  <span class="font-medium text-zinc-300"
                      x-text="( (currentPage-1)*perPage + 1 ) + '-' + Math.min(currentPage*perPage, total)"></span>
                  trong <span class="font-medium text-zinc-300" x-text="total"></span> kết quả
              </p> --}}

              <div class="flex items-center gap-1">
                  <button @click="prevPage()" :disabled="currentPage === 1"
                      class="w-8 h-8 flex items-center justify-center text-zinc-500 hover:text-zinc-100 hover:bg-zinc-800 rounded transition-colors disabled:opacity-50">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 19l-7-7 7-7" />
                      </svg>
                  </button>

                  <template x-for="p in pages" :key="p">
                      <button @click="goToPage(p)"
                          :class="p === currentPage ?
                              'w-8 h-8 flex items-center justify-center text-zinc-100 bg-blue-600 rounded text-sm font-medium' :
                              'w-8 h-8 flex items-center justify-center text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800 rounded text-sm font-medium transition-colors'"
                          x-text="p"></button>
                  </template>

                  <span x-show="lastPage > pages[pages.length-1]"
                      class="w-8 h-8 flex items-center justify-center text-zinc-500 text-sm">...</span>

                  <button x-show="lastPage > pages[pages.length-1]" @click="goToPage(lastPage)"
                      class="w-8 h-8 flex items-center justify-center text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800 rounded text-sm font-medium transition-colors"
                      x-text="lastPage"></button>

                  <button @click="nextPage()" :disabled="currentPage === lastPage"
                      class="w-8 h-8 flex items-center justify-center text-zinc-500 hover:text-zinc-100 hover:bg-zinc-800 rounded transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                      </svg>
                  </button>
              </div>
          </div>
      </template>

      <!-- Logs Slide-over (moved inside the crawlHistory scope) -->
      <div x-show="showLogsModal" x-cloak class="fixed inset-0 z-50 flex" aria-hidden="true">
          <!-- Overlay -->
          <div @click="closeLogs()" x-show="showLogsModal" x-transition.opacity
              class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

          <!-- Panel -->
          <div x-show="showLogsModal" x-transition:enter="transform transition"
              x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
              x-transition:leave="transform transition" x-transition:leave-start="translate-x-0"
              x-transition:leave-end="translate-x-full"
              class="relative ml-auto w-full max-w-[550px] h-full bg-zinc-900 border-l border-zinc-800 shadow-xl crawl-logs-panel flex flex-col">

              <div class="shrink-0 flex items-center justify-between p-4 border-b border-zinc-800">
                  <div>
                      <h3 class="text-sm font-semibold text-zinc-100">Logs phiên crawl <span class="font-mono">#<span
                                  x-text="selectedTask ? selectedTask.id : ''"></span></span></h3>
                      <div class="mt-1">
                          <span class="inline-flex items-center gap-2 px-2 py-0.5 rounded text-xs font-medium"
                              :class="{
                                  'bg-emerald-500/10 text-emerald-500': selectedTask && selectedTask
                                      .status === 'completed',
                                  'bg-amber-500/10 text-amber-400': selectedTask && selectedTask
                                      .status === 'completed_with_errors',
                                  'bg-blue-500/10 text-blue-400': selectedTask && selectedTask.status === 'running',
                                  'bg-red-500/10 text-red-500': selectedTask && selectedTask.status === 'failed'
                              }">
                              <span class="w-1.5 h-1.5 rounded-full"
                                  :class="{
                                      'bg-emerald-500': selectedTask && selectedTask.status === 'completed',
                                      'bg-amber-400': selectedTask && selectedTask.status === 'completed_with_errors',
                                      'bg-blue-400 animate-pulse': selectedTask && selectedTask.status === 'running',
                                      'bg-red-500': selectedTask && selectedTask.status === 'failed'
                                  }"></span>
                              <span x-text="selectedTask ? formatStatus(selectedTask.status) : '-'">
                              </span>
                          </span>
                      </div>
                  </div>

                  <div class="flex items-center gap-2">
                      <button @click="closeLogs()"
                          class="p-2 rounded-md text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12" />
                          </svg>
                      </button>
                  </div>
              </div>

              <div class="shrink-0 px-4 py-3 border-b border-zinc-800 flex items-center justify-between">
                  <div class="flex items-center gap-2">
                      <div class="text-sm text-zinc-300 font-medium">Logs</div>
                      <div class="text-xs text-zinc-500">(mới nhất ở cuối)</div>
                  </div>
                  <div class="flex items-center gap-2">
                      <button @click="(logLifecycleFilter='all', logsPage=1, fetchLogs())"
                          :class="logLifecycleFilter === 'all' ? 'bg-zinc-800 text-zinc-100' : 'text-zinc-400'"
                          class="px-2 py-1 rounded">All</button>
                      <button @click="(logLifecycleFilter='success', logsPage=1, fetchLogs())"
                          :class="logLifecycleFilter === 'success' ? 'bg-emerald-700 text-zinc-900' : 'text-zinc-400'"
                          class="px-2 py-1 rounded">Success</button>
                      <button @click="(logLifecycleFilter='failed', logsPage=1, fetchLogs())"
                          :class="logLifecycleFilter === 'failed' ? 'bg-red-600 text-zinc-100' : 'text-zinc-400'"
                          class="px-2 py-1 rounded">Failed</button>
                  </div>
              </div>

              <div class="flex-1 min-h-0 overflow-y-auto overflow-x-hidden p-4">
                  <!-- Loading skeleton -->
                  <template x-if="logsLoading">
                      <div class="space-y-3">
                          <template x-for="i in 6" :key="i">
                              <div class="flex items-start gap-3">
                                  <div class="skeleton h-6 w-6 rounded"></div>
                                  <div class="flex-1 space-y-2">
                                      <div class="skeleton h-4 w-3/4 rounded"></div>
                                      <div class="skeleton h-3 w-1/2 rounded"></div>
                                  </div>
                                  <div class="skeleton h-6 w-12 rounded"></div>
                              </div>
                          </template>
                      </div>
                  </template>

                  <!-- Empty state -->
                  <template x-if="!logsLoading && logs.length === 0">
                      <div class="text-center py-12">
                          <div
                              class="w-12 h-12 mx-auto bg-zinc-800 rounded-full flex items-center justify-center mb-4">
                              <svg class="w-6 h-6 text-zinc-600" fill="none" stroke="currentColor"
                                  viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6" />
                              </svg>
                          </div>
                          <div class="text-sm font-medium text-zinc-300">Chưa có logs</div>
                          <div class="text-xs text-zinc-500 mt-1">Logs sẽ hiển thị khi có sự kiện</div>
                      </div>
                  </template>

                  <!-- Logs list -->
                  <template x-if="filteredLogs.length > 0">
                      <div class="logs-list flex-1 overflow-auto space-y-3">
                          <template x-for="log in filteredLogs" :key="log.id">
                              <div class="bg-zinc-900 border border-zinc-800 rounded-lg p-3 font-mono text-xs">
                                  <div class="flex items-start gap-3">
                                      <div class="w-44 text-zinc-400 text-xs"> <span x-text="log.created_at"></span>
                                      </div>
                                      <div class="flex-1">
                                          <div class="flex items-center gap-2">
                                              <div class="text-zinc-100 text-sm truncate"
                                                  x-text="(log.context && log.context.job_name) ? log.context.job_name : (log.queue ? log.queue : log.message)">
                                              </div>

                                              <div class="ml-2">
                                                  <span
                                                      class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                                      :class="{
                                                          'bg-emerald-500/10 text-emerald-500': (log
                                                              .derived_status || '') === 'success',
                                                          'bg-blue-500/10 text-blue-400': (log.derived_status ||
                                                              '') === 'running',
                                                          'bg-red-500/10 text-red-400': (log.derived_status ||
                                                              '') === 'failed'
                                                      }"
                                                      x-text="(log.event_type) ? log.event_type : ((log.derived_status || log.context && log.context.status) ? (log.derived_status || (log.context && log.context.status)).toUpperCase() : (log.level || '').toUpperCase())"></span>
                                              </div>

                                              <div class="ml-2 text-zinc-500 text-xs"
                                                  x-text="log.context && log.context.execution_time_ms ? (log.context.execution_time_ms >= 1000 ? Math.round(log.context.execution_time_ms/1000) + 's' : log.context.execution_time_ms + 'ms') : ''">
                                              </div>

                                              <template x-if="log.context && log.context.error_type === 'timeout'">
                                                  <div
                                                      class="ml-2 inline-flex items-center px-2 py-0.5 text-xs rounded bg-amber-500 text-zinc-900">
                                                      TIMEOUT</div>
                                              </template>

                                              <div class="ml-auto">
                                                  <span
                                                      class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                                      :class="{
                                                          'bg-blue-500/10 text-blue-400': log.level === 'info',
                                                          'bg-amber-500/10 text-amber-400': log
                                                              .level === 'warning',
                                                          'bg-red-500/10 text-red-400': log.level === 'error'
                                                      }"
                                                      x-text="log.level ? log.level.toUpperCase() : ''"></span>
                                              </div>
                                          </div>

                                          <div class="mt-2 text-zinc-300 text-sm" x-text="log.message"></div>

                                          <div class="mt-2">
                                              <button @click="toggleContext(log.id)"
                                                  class="text-xs text-zinc-400 hover:text-zinc-100">View
                                                  details</button>
                                              <template x-if="expandedContexts[log.id]">
                                                  <div class="mt-2 space-y-2">
                                                      <template x-if="log.context && log.context.stacktrace">
                                                          <pre class="max-h-48 overflow-auto text-xs text-zinc-200 bg-zinc-900 border border-zinc-800 p-3 rounded font-mono"
                                                              x-text="log.context.stacktrace"></pre>
                                                      </template>
                                                      <pre class="max-h-48 overflow-auto text-xs text-zinc-200 bg-zinc-900 border border-zinc-800 p-3 rounded font-mono"
                                                          x-text="JSON.stringify(log.context ? (Object.assign({}, log.context, { message: undefined })) : {}, null, 2)"></pre>
                                                  </div>
                                              </template>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </template>
                      </div>
                  </template>

              </div>

              <!-- Logs pagination controls (compact, stable rendering) -->
              <div class="shrink-0 px-4 py-3 border-t border-zinc-800 flex flex-col sm:flex-row sm:items-center sm:justify-between sticky bottom-0 bg-zinc-900 z-10"
                  x-show="logs.length > 0">
                  <div class="flex items-center gap-2 mb-2 sm:mb-0">
                      <div class="text-xs text-zinc-500">Hiển thị
                          <span class="text-zinc-300" x-text="logsFrom"></span>-<span class="text-zinc-300"
                              x-text="logsTo"></span>
                          / <span class="text-zinc-300" x-text="logsTotal"></span> logs
                      </div>


                      <div class="flex items-center gap-2">
                          <select
                              @change="
                                    logsPerPage = parseInt($event.target.value);
                                    logsPage = 1;
                                    fetchLogs()
                                "
                              class="
                                    h-8
                                    min-w-[110px]
                                    flex-shrink-0
                                    appearance-none
                                    bg-zinc-800
                                    border border-zinc-700
                                    rounded-md
                                    px-3 pr-8
                                    text-xs text-zinc-200
                                    whitespace-nowrap
                                    focus:outline-none
                                    focus:ring-1
                                    focus:ring-blue-500
                                    hover:border-zinc-600
                                    transition
                                ">
                              <option value="25">25 / trang</option>
                              <option value="50">50 / trang</option>
                              <option value="100" selected>100 / trang</option>
                              <option value="200">200 / trang</option>
                              <option value="500">500 / trang</option>
                          </select>

                          <!-- custom dropdown icon -->
                          <div class="pointer-events-none -ml-7 text-zinc-400 text-[10px]">
                              ▼
                          </div>
                      </div>
                  </div>

                  <div class="flex items-center justify-end gap-1 sm:gap-2">
                      <button @click="logsPrevPage()" :disabled="logsPage <= 1"
                          class="w-16 h-8 flex items-center justify-center rounded text-xs font-medium text-zinc-300 bg-zinc-800 hover:bg-zinc-700 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                          ← Trước
                      </button>

                      <!-- Compact pages (use templates to avoid Alpine DOM mismatch) -->
                      <div class="flex items-center gap-1 sm:gap-2 whitespace-nowrap">
                          <template x-for="(p, idx) in logsPages()" :key="`${p.key}-${idx}`">
                              <template x-if="p.isEllipsis">
                                  <span class="px-2 text-xs text-zinc-500">...</span>
                              </template>

                              <template x-if="!p.isEllipsis">
                                  <button @click="if (p.page !== logsPage) { logsPage = p.page; fetchLogs(); }"
                                      :aria-current="p.page === logsPage ? 'true' : 'false'"
                                      :class="p.page === logsPage ?
                                          'w-8 h-8 flex items-center justify-center rounded text-sm font-medium text-white bg-blue-600' :
                                          'w-8 h-8 flex items-center justify-center rounded text-sm text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800 transition-colors'"
                                      x-text="p.page"></button>
                              </template>
                          </template>
                      </div>

                      <button @click="logsNextPage()" :disabled="logsPage >= logsLastPage"
                          class="w-16 h-8 flex items-center justify-center rounded text-xs font-medium text-zinc-300 bg-zinc-800 hover:bg-zinc-700 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                          Sau →
                      </button>
                  </div>
              </div>
          </div>
      </div>

      <!-- Toast Container -->
      <div x-show="retryToasts.length > 0"
          class="fixed bottom-6 right-6 z-[60] flex flex-col gap-2 max-w-sm w-full pointer-events-none">
          <template x-for="(toast, idx) in retryToasts" :key="toast.id">
              <div x-show="toast.show" x-transition:enter="transform transition ease-out duration-300"
                  x-transition:enter-start="translate-x-full opacity-0"
                  x-transition:enter-end="translate-x-0 opacity-100"
                  x-transition:leave="transform transition ease-in duration-200"
                  x-transition:leave-start="translate-x-0 opacity-100"
                  x-transition:leave-end="translate-x-full opacity-0"
                  class="pointer-events-auto bg-zinc-900 border border-zinc-700 rounded-lg shadow-2xl p-3 overflow-hidden">

                  <!-- Progress bar -->
                  <div class="absolute bottom-0 left-0 h-0.5 bg-amber-500/60"
                      :style="'width: ' + (100 - (toast.elapsed / toast.duration) * 100) + '%'"
                      x-show="toast.status === 'loading'"></div>

                  <div class="flex items-start gap-3">
                      <!-- Icon -->
                      <template x-if="toast.status === 'loading'">
                          <svg class="w-5 h-5 shrink-0 mt-0.5 text-amber-400 animate-spin" fill="none"
                              viewBox="0 0 24 24">
                              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                  stroke-width="4" />
                              <path class="opacity-75" fill="currentColor"
                                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                          </svg>
                      </template>
                      <template x-if="toast.status === 'success'">
                          <svg class="w-5 h-5 shrink-0 mt-0.5 text-emerald-400" fill="none" stroke="currentColor"
                              viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>
                      </template>
                      <template x-if="toast.status === 'error'">
                          <svg class="w-5 h-5 shrink-0 mt-0.5 text-red-400" fill="none" stroke="currentColor"
                              viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>
                      </template>

                      <!-- Content -->
                      <div class="flex-1 min-w-0">
                          <div class="text-sm font-medium text-zinc-100 truncate" x-text="toast.title"></div>
                          <div class="text-xs text-zinc-400 mt-0.5" x-text="toast.description"></div>
                          <template x-if="toast.status === 'loading' && toast.taskLabel">
                              <div class="text-[11px] text-amber-500/80 mt-1 font-mono" x-text="toast.taskLabel">
                              </div>
                          </template>
                      </div>

                      <!-- Close -->
                      <button @click="removeToast(toast.id)"
                          class="shrink-0 text-zinc-500 hover:text-zinc-300 transition-colors">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12" />
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
                  // date range filter (YYYY-MM-DD strings)
                  fromDate: null,
                  toDate: null,
                  startedFrom: null,
                  startedTo: null,
                  statusOpen: false,

                  loading: true,
                  error: null,

                  polling: null,
                  timer: null,

                  // pagination
                  currentPage: 1,
                  perPage: 10,
                  total: 0,
                  lastPage: 1,

                  isFetching: false,
                  now: Date.now(),

                  lastHasRunningTask: null,


                  crawlStartedHandler: null,

                  // Logs modal state
                  showLogsModal: false,
                  selectedTask: null,
                  logs: [],
                  logsLoading: false,
                  logsPage: 1,
                  logsPerPage: 200,
                  logsLastPage: 1,
                  logsTotal: 0,
                  logsFrom: 0,
                  logsTo: 0,
                  logsSummary: null,
                  logsInterval: null,
                  expandedContexts: {},
                  // ui filters
                  logLevelFilter: 'all',
                  logLifecycleFilter: 'all',

                  // retry state
                  retryInProgress: false,
                  retryToasts: [],
                  toastIdCounter: 0,

                  init() {
                      // Initialize filters from URL if present
                      const paramsInit = new URLSearchParams(window.location.search);
                      this.fromDate = paramsInit.get('from_date') || null;
                      this.toDate = paramsInit.get('to_date') || null;
                      this.startedFrom = paramsInit.get('crawl_started_from') || null;
                      this.startedTo = paramsInit.get('crawl_started_to') || null;
                      this.tableFilter = paramsInit.get('status') || this.tableFilter;
                      this.currentPage = parseInt(paramsInit.get('page')) || this.currentPage;

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

                  formatStatus(status) {
                      if (!status) return '-';
                      const maps = {
                          'completed_with_errors': 'Completed with errors',
                          'completed': 'Completed',
                          'running': 'Running',
                          'failed': 'Failed',
                          'pending': 'Pending'
                      };
                      if (maps[status]) return maps[status];
                      return status.split('_').map(s => s.charAt(0).toUpperCase() + s.slice(1)).join(' ');
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
                      // Server supplies filtered tasks via API; no client-side reductions
                      return this.tasks || [];
                  },

                  get failedCount() {
                      return (this.tasks || []).filter(t => t.status === 'failed' || t.status ===
                          'completed_with_errors').length;
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

                          // include current page URL query params (keyword/status/from_date/to_date)
                          const params = new URLSearchParams(window.location.search);
                          params.set('page', this.currentPage);
                          params.set('per_page', this.perPage);

                          // include UI filter values if present
                          if (this.fromDate) params.set('from_date', this.fromDate);
                          else params.delete('from_date');
                          if (this.toDate) params.set('to_date', this.toDate);
                          else params.delete('to_date');
                          if (this.startedFrom) params.set('crawl_started_from', this.startedFrom);
                          else params.delete('crawl_started_from');
                          if (this.startedTo) params.set('crawl_started_to', this.startedTo);
                          else params.delete('crawl_started_to');
                          if (this.tableFilter && this.tableFilter !== 'all') params.set('status',
                              this.tableFilter);
                          else params.delete('status');

                          const response = await fetch(`/api/crawl/history?${params.toString()}`, {
                              headers: {
                                  Accept: 'application/json'
                              },
                          });

                          if (!response.ok) {

                              throw new Error(
                                  `HTTP ${response.status}`
                              );
                          }

                          const data =
                              await response.json();

                          this.tasks = Array.isArray(data.tasks) ? data.tasks : [];

                          if (data.pagination) {
                              this.currentPage = data.pagination.current_page || 1;
                              this.lastPage = data.pagination.last_page || 1;
                              this.perPage = data.pagination.per_page || this.perPage;
                              this.total = data.pagination.total || 0;
                          }

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

                  applyFilters() {
                      // reset to first page
                      this.currentPage = 1;

                      // update URL query params (keeps back/forward behavior)
                      const params = new URLSearchParams(window.location.search);
                      if (this.fromDate) params.set('from_date', this.fromDate);
                      else params.delete('from_date');
                      if (this.toDate) params.set('to_date', this.toDate);
                      else params.delete('to_date');
                      if (this.startedFrom) params.set('crawl_started_from', this.startedFrom);
                      else params.delete('crawl_started_from');
                      if (this.startedTo) params.set('crawl_started_to', this.startedTo);
                      else params.delete('crawl_started_to');
                      if (this.tableFilter && this.tableFilter !== 'all') params.set('status', this
                          .tableFilter);
                      else params.delete('status');
                      params.set('page', this.currentPage);

                      const newUrl = `${window.location.pathname}?${params.toString()}`;
                      window.history.pushState({}, '', newUrl);

                      // fetch with new filters
                      this.fetchHistory(true);
                  },

                  resetFilters() {
                      this.fromDate = null;
                      this.toDate = null;
                      this.startedFrom = null;
                      this.startedTo = null;
                      this.tableFilter = 'all';
                      this.currentPage = 1;

                      // Remove filter-related query params
                      const params = new URLSearchParams(window.location.search);
                      params.delete('from_date');
                      params.delete('to_date');
                      params.delete('crawl_started_from');
                      params.delete('crawl_started_to');
                      params.delete('status');
                      params.set('page', this.currentPage);

                      const newUrl = params.toString() ?
                          `${window.location.pathname}?${params.toString()}` : window.location.pathname;
                      window.history.pushState({}, '', newUrl);

                      this.fetchHistory(true);
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

                  goToPage(page) {
                      if (page < 1 || page > this.lastPage || page === this.currentPage) return;
                      this.currentPage = page;
                      this.fetchHistory(true);
                  },

                  prevPage() {
                      if (this.currentPage > 1) {
                          this.goToPage(this.currentPage - 1);
                      }
                  },

                  nextPage() {
                      if (this.currentPage < this.lastPage) {
                          this.goToPage(this.currentPage + 1);
                      }
                  },

                  get pages() {
                      const pages = [];
                      const start = Math.max(1, this.currentPage - 2);
                      const end = Math.min(this.lastPage, start + 4);
                      for (let i = start; i <= end; i++) pages.push(i);
                      return pages;
                  },

                  get filteredLogs() {
                      if (!this.logs || this.logs.length === 0) return [];
                      let items = this.logs;

                      // lifecycle filter: support all / success / failed
                      if (this.logLifecycleFilter && this.logLifecycleFilter !== 'all') {
                          const want = this.logLifecycleFilter.toLowerCase();

                          const isSuccess = (l) => {
                              const ds = (l.derived_status || '').toString().trim().toLowerCase();
                              if (ds === 'success') return true;

                              const ctxStatus = l.context && l.context.status ? String(l.context
                                  .status).toUpperCase() : '';
                              if (ctxStatus && (ctxStatus.includes('SUCCESS') || ctxStatus
                                      .includes('COMPLETE') || ctxStatus.includes('COMPLETED') ||
                                      ctxStatus.includes('DONE') || ctxStatus.includes('FINISH')))
                                  return true;

                              const et = (l.event_type || '').toString().toUpperCase();
                              const msg = (l.message || '').toString().toUpperCase();
                              return (et.includes('SUCCESS') || et.includes('COMPLETE') || et
                                  .includes('COMPLETED') || et.includes('DONE') || msg
                                  .includes('SUCCESS') || msg.includes('COMPLETE') || msg
                                  .includes('COMPLETED') || msg.includes('DONE') || msg
                                  .includes('FINISHED'));
                          };

                          const isFailed = (l) => {
                              const ds = (l.derived_status || '').toString().trim().toLowerCase();
                              if (ds === 'failed') return true;

                              const level = (l.level || '').toString().toLowerCase();
                              if (level === 'error') return true;

                              const et = (l.event_type || '').toString().toUpperCase();
                              const msg = (l.message || '').toString().toUpperCase();
                              return (et.includes('FAILED') || et.includes('ERROR') || et
                                  .includes('EXCEPTION') || et.includes('TIMEOUT') || et
                                  .includes('PERMANENTLY FAILED') || et.includes(
                                      'CRITICAL') || msg.includes('FAILED') || msg.includes(
                                      'ERROR') || msg.includes('EXCEPTION') || msg.includes(
                                      'TIMEOUT') || msg.includes('PERMANENTLY FAILED') || msg
                                  .includes('CRITICAL'));
                          };

                          if (want === 'success') {
                              items = items.filter(l => isSuccess(l));
                          } else if (want === 'failed') {
                              items = items.filter(l => isFailed(l));
                          }
                      }

                      // keep legacy level filter if set
                      if (this.logLevelFilter && this.logLevelFilter !== 'all') {
                          items = items.filter(l => (l.level || '').toLowerCase() === this
                              .logLevelFilter.toLowerCase());
                      }

                      return items;
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

                  // Logs fetching and modal
                  async openLogs(task) {
                      this.selectedTask = task;
                      this.showLogsModal = true;

                      // Reset pagination for the new task
                      this.logsPage = 1;
                      this.logsLastPage = 1;
                      this.logsTotal = 0;
                      this.logsFrom = 0;
                      this.logsTo = 0;
                      this.logsSummary = null;
                      this.logLifecycleFilter = 'all';
                      this.logLevelFilter = 'all';

                      await this.fetchLogs();

                      if (task.status === 'running') {
                          this.startLogsPolling();
                      }
                  },

                  closeLogs() {
                      this.showLogsModal = false;
                      this.selectedTask = null;
                      this.logs = [];
                      this.logsLoading = false;
                      this.logsPage = 1;
                      this.logsLastPage = 1;
                      this.logsTotal = 0;
                      this.logsFrom = 0;
                      this.logsTo = 0;
                      this.logsSummary = null;
                      this.logLifecycleFilter = 'all';
                      this.logLevelFilter = 'all';
                      this.stopLogsPolling();
                  },

                  toggleContext(logId) {
                      if (!logId) return;
                      this.expandedContexts[logId] = !this.expandedContexts[logId];
                  },

                  async fetchLogs() {
                      if (!this.selectedTask) return;
                      this.logsLoading = true;
                      try {
                          const resp = await fetch(
                              `/crawl-tasks/${this.selectedTask.id}/logs?page=${this.logsPage}&per_page=${this.logsPerPage}&lifecycle=${encodeURIComponent(this.logLifecycleFilter || 'all')}`, {
                                  credentials: 'same-origin',
                                  headers: {
                                      'Accept': 'application/json',
                                      'X-Requested-With': 'XMLHttpRequest'
                                  }
                              });
                          if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
                          const payload = await resp.json();

                          // Accept backward-compatible shape
                          this.logs = Array.isArray(payload.logs) ? payload.logs : (Array.isArray(
                              payload.data) ? payload.data : []);

                          // pagination meta
                          if (payload.pagination) {
                              this.logsPage = payload.pagination.current_page || this.logsPage;
                              this.logsLastPage = payload.pagination.last_page || 1;
                              this.logsPerPage = payload.pagination.per_page || this.logsPerPage;
                              this.logsTotal = payload.pagination.total || 0;
                              this.logsFrom = payload.pagination.from || 0;
                              this.logsTo = payload.pagination.to || 0;
                          } else {
                              this.logsPage = 1;
                              this.logsLastPage = 1;
                              this.logsPerPage = this.logs.length;
                              this.logsTotal = this.logs.length;
                              this.logsFrom = this.logs.length ? 1 : 0;
                              this.logsTo = this.logs.length;
                          }

                          this.logsSummary = payload.summary || null;

                          // Stop polling if task finished
                          if (this.selectedTask && this.selectedTask.status !== 'running') this
                              .stopLogsPolling();

                          // Auto-scroll to latest when running
                          if (this.selectedTask && this.selectedTask.status === 'running') {
                              setTimeout(() => {
                                  const el = document.querySelector(
                                      '.crawl-logs-panel .logs-list');
                                  if (el) el.scrollTop = el.scrollHeight;
                              }, 50);
                          }
                      } catch (err) {
                          console.error('Fetch logs failed', err);
                      } finally {
                          this.logsLoading = false;
                      }
                  },

                  async logsPrevPage() {
                      if (this.logsPage > 1) {
                          this.logsPage -= 1;
                          await this.fetchLogs();
                      }
                  },

                  async logsNextPage() {
                      if (this.logsPage < this.logsLastPage) {
                          this.logsPage += 1;
                          await this.fetchLogs();
                      }
                  },

                  logsPages() {
                      const raw = [];
                      const last = Math.max(1, parseInt(this.logsLastPage || 1, 10));
                      const current = Math.max(1, Math.min(last, parseInt(this.logsPage || 1, 10)));

                      const pushPage = (n) => raw.push({
                          key: `p-${n}`,
                          page: n,
                          isEllipsis: false
                      });
                      const pushEll = (id) => raw.push({
                          key: `e-${id}`,
                          isEllipsis: true
                      });

                      // Always show first
                      pushPage(1);

                      // Left ellipsis when there's a gap between first and the window
                      if (current > 3 && last > 4) pushEll('left');

                      // Pages around current (ensure within 2..last-1)
                      const start = Math.max(2, current - 1);
                      const end = Math.min(last - 1, current + 1);
                      for (let i = start; i <= end; i++) {
                          if (i > 1 && i < last) pushPage(i);
                      }

                      // Right ellipsis
                      if (current < last - 2 && last > 4) pushEll('right');

                      // Always show last
                      if (last > 1) pushPage(last);

                      // Normalize: remove duplicate pages and consecutive ellipses
                      const out = [];
                      const seen = new Set();
                      for (const item of raw) {
                          if (item.isEllipsis) {
                              if (out.length && out[out.length - 1].isEllipsis) continue;
                              out.push(item);
                          } else {
                              if (seen.has(item.page)) continue;
                              seen.add(item.page);
                              out.push(item);
                          }
                      }

                      return out;
                  },

                  async confirmRetry(task) {
                      if (!confirm(`Thực hiện thử lại các job failed cho task #${task.id}?`)) return;
                      await this.performRetry(task);
                  },

                  async performRetry(task) {
                      this.retryInProgress = true;
                      const toast = this.addToast(
                          'loading',
                          `Task #${task.id}`,
                          'Đang retry...',
                          this.getTaskLabel(task)
                      );
                      try {
                          const resp = await fetch(`/api/crawl/tasks/${task.id}/retry`, {
                              method: 'POST',
                              headers: {
                                  'Accept': 'application/json',
                                  'Content-Type': 'application/json'
                              },
                              body: JSON.stringify({}),
                          });

                          if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
                          const payload = await resp.json();
                          const queued = payload.queued || 0;
                          const skipped = (payload.skipped_success || 0) + (payload.skipped_running ||
                              0);
                          const failed = payload.failed_infer || 0;
                          const total = queued + skipped + failed;

                          this.updateToast(toast.id, 'success',
                              `Task #${task.id}`,
                              `Đã retry ${queued}/${total} job` +
                              (failed > 0 ? `, ${failed} không xác định được` : '')
                          );
                          // Refresh history immediately so the task status shows 'running'
                          this.fetchHistory(true);
                      } catch (err) {
                          console.error('Retry failed', err);
                          this.updateToast(toast.id, 'error',
                              `Task #${task.id}`,
                              'Retry thất bại: ' + (err.message || '')
                          );
                      } finally {
                          this.retryInProgress = false;
                      }
                  },

                  async confirmRetryAll() {
                      if (!confirm('Thực hiện thử lại tất cả các job failed trên hệ thống?')) return;
                      await this.performRetryAll();
                  },

                  async performRetryAll() {
                      this.retryInProgress = true;
                      const toast = this.addToast(
                          'loading',
                          'Tất cả task bị lỗi',
                          'Đang retry...'
                      );
                      try {
                          const resp = await fetch(`/api/crawl/tasks/retry-failed`, {
                              method: 'POST',
                              headers: {
                                  'Accept': 'application/json',
                                  'Content-Type': 'application/json'
                              },
                              body: JSON.stringify({}),
                          });

                          if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
                          const payload = await resp.json();
                          const queued = payload.queued || 0;
                          const skipped = (payload.skipped_success || 0) + (payload.skipped_running ||
                              0);
                          const failed = payload.failed_infer || 0;
                          const tasksProcessed = payload.tasks_processed || 0;
                          const total = queued + skipped + failed;

                          this.updateToast(toast.id, 'success',
                              `${tasksProcessed} task đã xử lý`,
                              `Đã retry ${queued}/${total} job` +
                              (failed > 0 ? `, ${failed} không xác định được` : '')
                          );
                      } catch (err) {
                          console.error('Retry all failed failed', err);
                          this.updateToast(toast.id, 'error',
                              'Retry all thất bại',
                              err.message || ''
                          );
                      } finally {
                          this.retryInProgress = false;
                          // refresh history to reflect newly queued jobs/status
                          this.fetchHistory(true);
                      }
                  },

                  startLogsPolling() {
                      this.stopLogsPolling();
                      if (this.logsInterval) return; // prevent duplicates
                      this.logsInterval = setInterval(async () => {
                          if (!this.showLogsModal || !this.selectedTask) return this
                              .stopLogsPolling();
                          await this.fetchLogs();
                          // If task is no longer running, stop polling
                          if (this.selectedTask && this.selectedTask.status !== 'running')
                              this.stopLogsPolling();
                      }, 2000);
                  },

                  stopLogsPolling() {
                      if (this.logsInterval) {
                          clearInterval(this.logsInterval);
                          this.logsInterval = null;
                      }
                  },

                  // ── Toast helpers ──────────────────────────────────
                  addToast(status, title, description, taskLabel) {
                      const id = ++this.toastIdCounter;
                      this.retryToasts.push({
                          id,
                          status,
                          title,
                          description,
                          taskLabel: taskLabel || null,
                          show: true,
                          elapsed: 0,
                          duration: 5000,
                      });
                      // Auto-remove success/error toasts after 5s
                      if (status !== 'loading') {
                          setTimeout(() => this.removeToast(id), 5000);
                      }
                      return {
                          id
                      };
                  },

                  updateToast(id, status, title, description) {
                      const toast = this.retryToasts.find(t => t.id === id);
                      if (!toast) return;
                      toast.status = status;
                      toast.title = title;
                      toast.description = description;
                      // Auto-remove after update
                      setTimeout(() => this.removeToast(id), 5000);
                  },

                  removeToast(id) {
                      const idx = this.retryToasts.findIndex(t => t.id === id);
                      if (idx === -1) return;
                      this.retryToasts[idx].show = false;
                      setTimeout(() => {
                          this.retryToasts.splice(idx, 1);
                      }, 300);
                  },

                  getTaskLabel(task) {
                      if (!task) return '';
                      const fmt = (d) => {
                          if (!d) return '';
                          if (typeof d === 'string' && /^\d{4}-\d{2}-\d{2}$/.test(d)) {
                              const [y, m, day] = d.split('-');
                              return `${day}/${m}/${y}`;
                          }
                          try {
                              const p = this.parseDate(d);
                              return p ? p.toLocaleDateString('vi-VN', {
                                  day: '2-digit',
                                  month: '2-digit',
                                  year: 'numeric'
                              }) : '';
                          } catch {
                              return '';
                          }
                      };
                      const type = (task.type || '').toLowerCase();
                      if (type === 'full') return 'Full - Toàn bộ dữ liệu';
                      if (type === 'daily') return 'Daily - ' + fmt(task.from_date);
                      if (type === 'range') return 'Range - ' + fmt(task.from_date) + ' → ' + fmt(task
                          .to_date);
                      return type.charAt(0).toUpperCase() + type.slice(1);
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

                  formatResult(task) {
                      const processed = Number(task.processed_items || 0);
                      const total = Number(task.total_items || 0);

                      // If task completed, normalize to final snapshot
                      const status = (task.status || '').toString().toLowerCase();
                      if (status === 'completed' || status === 'completed_with_errors') {
                          const t = total;
                          return `${t}/${t} items`;
                      }

                      // Normal case: processed <= total
                      if (processed <= total) {
                          return `${processed}/${total} items`;
                      }

                      // processed > total: show total/total (+N mới)
                      const newCount = processed - total;
                      const main = `${total}/${total} items`;
                      const extra =
                          `<span class="ml-1 text-xs text-amber-400" title="Phát hiện dữ liệu mới trong lúc crawl">(+${newCount} dữ liệu liên quan)</span>`;
                      return `${main} ${extra}`;
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

      @include('admin.components.dauthau.task-detail-modal')
