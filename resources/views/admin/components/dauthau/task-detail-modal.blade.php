<div x-data="taskDetail()" x-cloak>
    <div x-show="show" x-transition class="fixed inset-0 z-[100] flex items-start justify-center p-4">
        <div @click="close()" class="absolute inset-0 bg-black/60"></div>

        <div x-show="show" x-transition
            class="relative w-full max-w-2xl bg-zinc-900 border border-zinc-800 rounded-lg shadow-lg overflow-hidden">
            <div class="p-4 border-b border-zinc-800 flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-sm font-semibold text-zinc-100">Chi tiết Crawl <span class="font-mono">#<span
                                x-text="task?.id"></span></span></h3>
                    <div class="text-xs text-zinc-400 mt-1">
                        <span x-text="task?.display_type || (task?.type || '')"></span>
                        <span class="mx-2">•</span>
                        <span x-text="formatStatus(task?.status)"></span>
                    </div>
                </div>
                <div>
                    <button type="button" @click="close()" class="text-zinc-400 hover:text-zinc-100">Close</button>
                </div>
            </div>

            <div class="p-4 space-y-3 max-h-[60vh] overflow-auto">
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <div class="text-xs text-zinc-400">Khoảng crawl</div>
                        <template x-if="task?.type === 'full'">
                            <div class="text-sm text-zinc-200">Toàn bộ dữ liệu</div>
                        </template>
                        <template x-if="task?.type === 'daily'">
                            <div class="text-sm text-zinc-200 font-mono" x-text="task?.crawl_range?.from || '-'"></div>
                        </template>
                        <template x-if="task?.type === 'range'">
                            <div class="text-sm text-zinc-200 font-mono"
                                x-text="(task?.crawl_range?.from || '-') + ' → ' + (task?.crawl_range?.to || '-')">
                            </div>
                        </template>
                        <template x-if="!task || !task.type">
                            <div class="text-sm text-zinc-500">—</div>
                        </template>
                    </div>
                    <div>
                        <div class="text-xs text-zinc-400">Thời gian</div>
                        <div class="text-sm text-zinc-200 font-mono"
                            x-text="(task?.started_at || '-') + (task?.finished_at ? (' → ' + task.finished_at) : '')">
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-zinc-400">Duration</div>
                        <div class="text-sm text-zinc-200 font-mono" x-text="durationLabel"></div>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="p-3 bg-zinc-900 border border-zinc-800 rounded">
                        <div class="text-xs text-zinc-400">Yêu cầu API</div>
                        <div class="text-sm text-zinc-200 font-semibold"
                            x-text="metrics.items.processed.toLocaleString()"></div>
                        <div class="text-xs text-zinc-500">requests đã gửi</div>
                        <div class="text-xs text-zinc-500">Lỗi: <span class="text-red-400"
                                x-text="task?.failed_items || 0"></span></div>
                    </div>
                    <div class="p-3 bg-zinc-900 border border-zinc-800 rounded">
                        <div class="text-xs text-zinc-400">Pages</div>
                        <div class="text-sm text-zinc-200 font-semibold"
                            x-text="metrics.pages.processed + '/' + metrics.pages.total"></div>
                        <div class="text-xs text-zinc-400">Completed: <span class="text-zinc-200"
                                x-text="metrics.pages.percent + '%'"></span></div>
                    </div>
                    <div class="p-3 bg-zinc-900 border border-zinc-800 rounded col-span-1">
                        <div class="text-xs text-zinc-400">Trạng thái</div>
                        <template x-if="task?.status === 'running'">
                            <div class="text-sm text-yellow-400 font-semibold flex items-center gap-1">
                                <span>⏳</span> Đang chạy...
                            </div>
                        </template>
                        <template x-if="task?.status === 'completed' && !task?.error">
                            <div class="text-sm text-emerald-400 font-semibold flex items-center gap-1">
                                <span>✅</span> Hoàn tất
                            </div>
                        </template>

                        <template x-if="task?.status === 'completed_with_errors'">
                            <div class="text-sm text-amber-400 font-semibold flex items-center gap-2">
                                <span>⚠️</span>
                                <span>Completed with errors</span>
                                <span class="text-xs text-zinc-400">(</span>
                                <span class="text-xs text-red-400 font-mono" x-text="task?.failed_items || 0"></span>
                                <span class="text-xs text-zinc-400"> failed)</span>
                            </div>
                        </template>

                        <template x-if="task?.status === 'failed'">
                            <div class="text-sm text-red-400 font-semibold flex items-start gap-1">
                                <span>❌</span>
                                <span class="break-words" x-text="task?.error || 'Có lỗi xảy ra'"></span>
                            </div>
                        </template>
                        <template x-if="task?.status === 'pending'">
                            <div class="text-sm text-zinc-400 font-semibold flex items-center gap-1">
                                <span>⏸️</span> Chờ xử lý
                            </div>
                        </template>
                        <template x-if="!task">
                            <div class="text-sm text-zinc-500">—</div>
                        </template>
                    </div>
                </div>

                <!-- Timeline and pagination removed as requested -->

                <div x-show="debugVisible" x-transition>
                    <div class="text-xs text-zinc-400">Recent logs (debug)</div>
                    <div class="mt-2 space-y-2">
                        <template x-for="log in logs" :key="log.id">
                            <div class="bg-zinc-900 border border-zinc-800 rounded p-2 font-mono text-xs">
                                <div class="flex items-center gap-2">
                                    <div class="text-zinc-400" x-text="log.created_at"></div>
                                    <div class="flex-1 text-zinc-200 truncate" x-text="log.message"></div>
                                    <div class="text-xs text-zinc-400" x-text="(log.level || '').toUpperCase()"></div>
                                </div>
                                <template x-if="log.context">
                                    <pre class="mt-2 text-xs text-zinc-200 bg-zinc-900 border border-zinc-800 p-2 rounded overflow-auto"
                                        x-text="JSON.stringify(log.context, null, 2)"></pre>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function taskDetail() {
            return {
                show: false,
                task: null,
                metrics: {
                    items: {
                        processed: 0,
                        total: 0,
                        percent: 0
                    },
                    pages: {
                        processed: 0,
                        total: 0,
                        percent: 0
                    },
                    logs: {
                        info: 0,
                        warning: 0,
                        error: 0
                    }
                },
                logs: [],
                debugVisible: false,

                get durationLabel() {
                    if (!this.task) return '-';
                    // Running task: show simple placeholder
                    if (this.task.status === 'running') return '0h - 0m';
                    // Completed/failed task: calculate actual duration
                    if (this.task.duration_seconds === null || this.task.duration_seconds === undefined) return '-';
                    const raw = Number(this.task.duration_seconds);
                    if (!isFinite(raw) || raw <= 0) return '-';
                    const total = Math.round(raw);
                    const h = Math.floor(total / 3600);
                    const m = Math.floor((total % 3600) / 60);
                    const s = total % 60;
                    if (h > 0) return `${h}h ${m}m ${s}s`;
                    if (m > 0) return `${m}m ${s}s`;
                    return `${s}s`;
                },

                init() {
                    window.addEventListener('open-task-detail', async (ev) => {
                        const t = ev.detail?.task || ev.detail;
                        if (!t || !t.id) return;
                        await this.open(t.id);
                    });
                },

                async open(taskId) {
                    this.show = true;
                    this.task = null;
                    this.metrics = {
                        items: {
                            processed: 0,
                            total: 0,
                            percent: 0
                        },
                        pages: {
                            processed: 0,
                            total: 0,
                            percent: 0
                        },
                        logs: {
                            info: 0,
                            warning: 0,
                            error: 0
                        }
                    };
                    this.logs = [];
                    this.debugVisible = false;
                    try {
                        const resp = await fetch(
                            `/crawl-tasks/${taskId}/detail`, {
                                credentials: 'same-origin',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                        if (!resp.ok) throw new Error('HTTP ' + resp.status);
                        const payload = await resp.json();
                        this.task = payload.task || null;
                        this.metrics = payload.metrics || this.metrics;
                        this.logs = payload.debug_logs || [];
                    } catch (err) {
                        console.error('Failed to fetch task detail', err);
                        alert('Không thể tải chi tiết task: ' + (err.message || ''));
                        this.close();
                    }
                },

                async toggleDebug() {
                    this.debugVisible = !this.debugVisible;
                    if (this.debugVisible && this.logs.length === 0 && this.task) {
                        try {
                            const resp = await fetch(`/crawl-tasks/${this.task.id}/detail?debug=1`, {
                                credentials: 'same-origin',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                            if (!resp.ok) throw new Error('HTTP ' + resp.status);
                            const payload = await resp.json();
                            this.logs = payload.debug_logs || [];
                        } catch (err) {
                            console.error('Failed to fetch debug logs', err);
                            alert('Không thể tải debug logs: ' + (err.message || ''));
                        }
                    }
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
                    return String(status).split('_').map(s => s.charAt(0).toUpperCase() + s.slice(1)).join(' ');
                },



                close() {
                    this.show = false;
                    this.task = null;
                    this.metrics = {
                        items: {
                            processed: 0,
                            total: 0,
                            percent: 0
                        },
                        pages: {
                            processed: 0,
                            total: 0,
                            percent: 0
                        },
                        logs: {
                            info: 0,
                            warning: 0,
                            error: 0
                        }
                    };
                    this.logs = [];
                    this.debugVisible = false;
                }
            }
        }
    </script>
</div>
