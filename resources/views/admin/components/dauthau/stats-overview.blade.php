<div x-data="statsOverview" x-init="init()" class="grid grid-cols-2 lg:grid-cols-5 gap-4">
    <!-- Total Crawled -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4">
        <div class="flex items-center justify-between">
            <span class="text-xs font-medium text-zinc-500 uppercase tracking-wide">Tổng thu thập</span>
            <div class="w-8 h-8 bg-blue-500/10 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                </svg>
            </div>
        </div>
        <p class="mt-3 text-2xl font-bold text-zinc-100 font-mono"><span x-text="formatNumber(totalItems)">0</span></p>
        <p class="mt-1 text-xs text-zinc-500"><span x-text="weeklyChangeText">&nbsp;</span></p>
    </div>

    <!-- Today's Crawled -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4">
        <div class="flex items-center justify-between">
            <span class="text-xs font-medium text-zinc-500 uppercase tracking-wide">Hôm nay</span>
            <div class="w-8 h-8 bg-emerald-500/10 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
            </div>
        </div>
        <p class="mt-3 text-2xl font-bold text-zinc-100 font-mono"><span x-text="formatNumber(todayItems)">0</span></p>
        <p class="mt-1 text-xs text-zinc-400"><span x-text="currentProgress"></span></p>
        <p class="mt-1 text-xs text-emerald-500"><span x-text="todayChangeText">&nbsp;</span></p>
    </div>

    <!-- Average Duration -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4">
        <div class="flex items-center justify-between">
            <span class="text-xs font-medium text-zinc-500 uppercase tracking-wide">Thời gian
                TB</span>
            <div class="w-8 h-8 bg-amber-500/10 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
        <p class="mt-3 text-2xl font-bold text-zinc-100 font-mono"><span x-text="avgDuration">--</span></p>
        <p class="mt-1 text-xs text-zinc-500">Mỗi phiên crawl</p>
    </div>

    <!-- Last Status -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4">
        <div class="flex items-center justify-between">
            <span class="text-xs font-medium text-zinc-500 uppercase tracking-wide">Trạng thái
                cuối</span>
            <div class="w-8 h-8 bg-emerald-500/10 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
        <p class="mt-3 text-lg font-semibold text-emerald-500"><span x-text="lastStatus">--</span></p>
        <p class="mt-1 text-xs text-zinc-500"><span x-text="lastTime">--</span></p>
    </div>

    <!-- Active Queue Jobs -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4">
        <div class="flex items-center justify-between">
            <span class="text-xs font-medium text-zinc-500 uppercase tracking-wide">Queue
                Jobs</span>
            <div class="w-8 h-8 bg-violet-500/10 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
        </div>
        <p class="mt-3 text-2xl font-bold text-zinc-100 font-mono"><span x-text="runningJobs">0</span></p>
        <p class="mt-1 text-xs text-blue-400">Đang xử lý</p>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {

        Alpine.data('statsOverview', () => ({

            totalItems: 0,

            todayItems: 0,

            avgDuration: '--',

            lastStatus: '--',

            lastTime: '--',

            runningJobs: 0,

            weeklyChangeText: '',

            todayChangeText: '',

            currentProgress: '--',

            timer: null,

            init() {
                this.fetchStats();
                this.timer = setInterval(() => this.fetchStats(), 10000);
            },

            destroy() {
                if (this.timer) {
                    clearInterval(this.timer);
                    this.timer = null;
                }
            },

            formatNumber(value) {
                try {
                    return Number(value).toLocaleString('en-US');
                } catch (e) {
                    return value;
                }
            },

            async fetchStats() {
                try {
                    const res = await fetch('/api/crawl/stats', {
                        headers: {
                            Accept: 'application/json'
                        }
                    });
                    if (!res.ok) return;
                    const data = await res.json();

                    this.totalItems = data.total_items ?? 0;
                    this.todayItems = data.today_items ?? 0;
                    this.avgDuration = data.avg_duration ?? '--';
                    this.lastStatus = (data.last_status ?? '--').charAt(0).toUpperCase() + (data
                        .last_status ?? '').slice(1);
                    this.lastTime = data.last_time ?? '--';
                    this.runningJobs = data.running_jobs ?? 0;
                    this.currentProgress = data.current_progress ?? '--';

                } catch (error) {
                    console.error('Failed to fetch stats', error);
                }
            }

        }));
    });
</script>
