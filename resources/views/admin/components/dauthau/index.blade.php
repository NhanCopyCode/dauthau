<!DOCTYPE html>
<html lang="vi" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đấu Thầu Admin Panel - Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full bg-zinc-925 text-zinc-100 font-sans antialiased">

    <!-- Main App Container -->
    <div x-data="dashboardApp()" x-cloak class="h-full flex">

        <!-- ========================================== -->
        <!-- START: components/sidebar.blade.php -->
        <!-- ========================================== -->
        <aside :class="sidebarOpen ? 'w-64' : 'w-16'"
            class="h-full bg-zinc-900 border-r border-zinc-800 flex flex-col transition-all duration-300 ease-in-out flex-shrink-0">
            <!-- Logo -->
            <div class="h-14 flex items-center border-b border-zinc-800 px-4">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <span x-show="sidebarOpen" x-transition:enter="transition-opacity duration-200"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        class="font-semibold text-sm text-zinc-100 whitespace-nowrap">Đấu Thầu Admin</span>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 py-4 px-2 space-y-1 overflow-y-auto">
                <template x-for="item in menuItems" :key="item.id">
                    <div class="relative group">
                        <a :href="item.href"
                            :class="[
                                item.active ?
                                'bg-zinc-800 text-zinc-100' :
                                'text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800/50',
                                sidebarOpen ? 'px-3' : 'px-0 justify-center'
                            ]"
                            class="flex items-center gap-3 h-10 rounded-lg transition-colors duration-150">
                            <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" x-html="item.icon">
                            </div>
                            <span x-show="sidebarOpen" x-text="item.label"
                                x-transition:enter="transition-opacity duration-200"
                                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                class="text-sm font-medium whitespace-nowrap"></span>
                            <span x-show="item.badge && sidebarOpen" x-text="item.badge"
                                class="ml-auto text-xs font-medium bg-blue-600/20 text-blue-400 px-2 py-0.5 rounded-full"></span>
                        </a>
                        <!-- Tooltip for collapsed state -->
                        <div x-show="!sidebarOpen" x-transition:enter="transition-opacity duration-150"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            class="absolute left-full top-1/2 -translate-y-1/2 ml-2 px-2.5 py-1.5 bg-zinc-800 text-zinc-100 text-xs font-medium rounded-md shadow-lg border border-zinc-700 whitespace-nowrap opacity-0 group-hover:opacity-100 pointer-events-none z-50">
                            <span x-text="item.label"></span>
                            <div
                                class="absolute right-full top-1/2 -translate-y-1/2 border-4 border-transparent border-r-zinc-800">
                            </div>
                        </div>
                    </div>
                </template>
            </nav>

            <!-- Collapse Toggle -->
            <div class="p-2 border-t border-zinc-800">
                <button @click="sidebarOpen = !sidebarOpen" :class="sidebarOpen ? 'px-3' : 'px-0 justify-center'"
                    class="w-full flex items-center gap-3 h-10 text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800/50 rounded-lg transition-colors duration-150">
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0">
                        <svg :class="sidebarOpen ? '' : 'rotate-180'" class="w-5 h-5 transition-transform duration-300"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                        </svg>
                    </div>
                    <span x-show="sidebarOpen" x-transition:enter="transition-opacity duration-200"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        class="text-sm font-medium whitespace-nowrap">Thu gọn</span>
                </button>
            </div>
        </aside>
        <!-- ========================================== -->
        <!-- END: components/sidebar.blade.php -->
        <!-- ========================================== -->

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            <!-- ========================================== -->
            <!-- START: components/header.blade.php -->
            <!-- ========================================== -->
            <header
                class="h-14 bg-zinc-900 border-b border-zinc-800 flex items-center justify-between px-6 flex-shrink-0">
                <!-- Left: Breadcrumbs & Title -->
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2 text-sm">
                        <a href="#" class="text-zinc-500 hover:text-zinc-300 transition-colors">Admin</a>
                        <svg class="w-4 h-4 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        <span class="text-zinc-100 font-medium">Dashboard</span>
                    </div>
                </div>

                <!-- Right: Status, Notifications, Profile -->
                <div class="flex items-center gap-4">
                    <!-- System Status -->
                    <div class="flex items-center gap-2 px-3 py-1.5 bg-zinc-800/50 rounded-lg">
                        <span class="relative flex h-2 w-2">
                            <span
                                class="pulse-dot absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span class="text-xs font-medium text-zinc-300">Hệ thống hoạt động</span>
                    </div>

                    <!-- Notifications -->
                    <div class="relative" x-data="notifications" @click.away="open = false">
                        <button @click="open = !open"
                            class="cursor-pointer relative w-9 h-9 flex items-center justify-center text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span x-show="unreadCount > 0"
                                class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 rounded-full text-[10px] font-semibold text-white flex items-center justify-center"
                                x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
                        </button>
                        <!-- Notification Dropdown -->
                        <div x-show="open" x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-1"
                            class="absolute right-0 mt-2 w-80 bg-zinc-900 border border-zinc-800 rounded-xl shadow-2xl z-50">
                            <div class="p-4 border-b border-zinc-800 flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-zinc-100">Thông báo</h3>
                                <button x-show="unreadCount > 0" @click="markAllAsRead()"
                                    class="text-xs text-blue-400 hover:text-blue-300">Đã đọc tất cả</button>
                            </div>

                            <div class="max-h-72 overflow-y-auto">
                                <template x-if="items.length === 0">
                                    <div class="p-4 text-center text-sm text-zinc-500">Chưa có thông báo</div>
                                </template>

                                <template x-for="n in items" :key="n.id">
                                    <div @click="markAsRead(n.id)"
                                        class="px-3 py-2.5 hover:bg-zinc-800/50 border-b border-zinc-800/40 cursor-pointer transition-opacity"
                                        :class="{
                                            'opacity-60': n.read,
                                            'border-l-2 border-emerald-500': !n.read && n.type === 'completed',
                                            'border-l-2 border-red-500/40': !n.read && n.type === 'failed',
                                            'border-l-2 border-zinc-700': n.read,
                                        }">
                                        <div class="flex gap-3 items-start">

                                            {{-- Icon --}}
                                            <div class="flex-shrink-0 mt-0.5 w-8 h-8 rounded-lg flex items-center justify-center"
                                                :class="n.type === 'completed' ? 'bg-emerald-500/10' : 'bg-red-500/10'">
                                                <template x-if="n.type === 'completed'">
                                                    <svg class="w-4 h-4 text-emerald-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </template>
                                                <template x-if="n.type === 'failed'">
                                                    <svg class="w-4 h-4 text-red-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M12 9v2m0 4h.01" />
                                                    </svg>
                                                </template>
                                            </div>

                                            {{-- Content --}}
                                            <div class="min-w-0 flex-1">

                                                {{-- Row 1: type pill + date range (or message fallback) --}}
                                                <template x-if="n.crawl">
                                                    <div class="flex items-center gap-2">
                                                        <span
                                                            class="px-1.5 py-0.5 rounded-full text-[11px] font-medium"
                                                            :class="n.crawl.type === 'daily' ?
                                                                'bg-blue-500/10 text-blue-400' :
                                                                (n.crawl.type === 'full' ?
                                                                    'bg-violet-500/10 text-violet-400' :
                                                                    (n.crawl.type === 'range' ?
                                                                        'bg-amber-500/10 text-amber-400' :
                                                                        'bg-zinc-800 text-zinc-400'))"
                                                            x-text="n.crawl.label">
                                                        </span>
                                                        <p class="text-sm font-medium leading-tight truncate"
                                                            :class="n.read ? 'text-zinc-400' : 'text-zinc-100'">
                                                            <span
                                                                x-text="n.crawl.date_range ? n.crawl.date_range : n.message"></span>
                                                        </p>
                                                    </div>
                                                </template>

                                                <template x-if="!n.crawl">
                                                    <p class="text-sm font-medium leading-tight truncate"
                                                        :class="n.read ? 'text-zinc-400' : 'text-zinc-100'"
                                                        x-text="n.message"></p>
                                                </template>

                                                {{-- Row 2: [N mục] · [duration]    [time ago] --}}
                                                <div class="mt-1 flex items-center gap-1.5 text-xs text-zinc-500">

                                                    <span
                                                        x-show="n.crawl && (n.crawl.total_items !== null && n.crawl.total_items !== undefined)"
                                                        class="flex items-center gap-1">
                                                        <span class="text-zinc-700">·</span>
                                                        <span x-text="n.crawl.total_items + ' mục'"></span>
                                                    </span>

                                                    <span
                                                        x-show="n.crawl && n.crawl.duration !== null && n.crawl.duration !== undefined && n.crawl.duration !== ''"
                                                        class="flex items-center gap-1">
                                                        <span class="text-zinc-700">·</span>
                                                        <span x-text="n.crawl.duration"></span>
                                                    </span>

                                                    <span class="ml-auto text-zinc-600" x-text="n.created_at"></span>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Toast Notifications -->
                        <template x-for="(toast, idx) in toasts" :key="toast.id">
                            <div x-data x-init="setTimeout(() => { removeToast(toast.id) }, 5000)" x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 translate-x-full"
                                x-transition:enter-end="opacity-100 translate-x-0"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100 translate-x-0"
                                x-transition:leave-end="opacity-0 translate-x-full"
                                class="fixed bottom-4 right-4 z-[9999] max-w-sm bg-zinc-900 border border-zinc-800 rounded-lg shadow-2xl overflow-hidden"
                                :style="`bottom: ${1 + idx * 4.5}rem`">
                                <div class="p-4 flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                                        :class="toast.type === 'completed' ? 'bg-emerald-500/10' : 'bg-red-500/10'">
                                        <template x-if="toast.type === 'completed'">
                                            <svg class="w-4 h-4 text-emerald-500" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </template>
                                        <template x-if="toast.type === 'failed'">
                                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01" />
                                            </svg>
                                        </template>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium"
                                            :class="toast.type === 'completed' ? 'text-emerald-400' : 'text-red-400'"
                                            x-text="toast.message"></p>
                                        <p class="text-xs text-zinc-500 mt-0.5" x-text="toast.created_at"></p>
                                    </div>
                                    <button @click="removeToast(toast.id)"
                                        class="text-zinc-500 hover:text-zinc-300 flex-shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- User Profile -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="flex items-center gap-3 px-2 py-1.5 hover:bg-zinc-800 rounded-lg transition-colors cursor-pointer">
                            <div
                                class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                <span
                                    class="text-sm font-semibold text-white">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                            <div class="text-left hidden sm:block">
                                <p class="text-sm font-medium text-zinc-100">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-zinc-500">{{ Auth::user()->roleLabel() }}</p>
                            </div>
                            <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <!-- Profile Dropdown -->
                        <div x-show="open" @click.away="open = false"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-1"
                            class="absolute right-0 mt-2 w-56 bg-zinc-900 border border-zinc-800 rounded-xl shadow-2xl z-50">
                            <div class="p-3 border-b border-zinc-800">
                                <p class="text-sm font-medium text-zinc-100">{{ Auth::user()->email }}</p>
                                <p class="text-xs text-zinc-500 mt-0.5">{{ Auth::user()->roleLabel() }}</p>
                            </div>
                            <div class="py-1">
                                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                    @csrf
                                    <button type="submit"
                                        class="w-full flex items-center gap-3 px-3 py-2 text-sm text-red-400 hover:text-red-300 hover:bg-zinc-800/50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <!-- ========================================== -->
            <!-- END: components/header.blade.php -->
            <!-- ========================================== -->

            <!-- ========================================== -->
            <!-- START: dashboard/index.blade.php -->
            <!-- ========================================== -->
            <main class="flex-1 overflow-y-auto bg-zinc-925">
                <div class="p-6 space-y-6">

                    <!-- Page Header -->
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-xl font-semibold text-zinc-100">Dashboard</h1>
                            <p class="text-sm text-zinc-500 mt-1">Tổng quan hệ thống thu thập dữ liệu đấu thầu</p>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-zinc-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Cập nhật: <span x-text="currentTime"></span></span>
                        </div>
                    </div>

                    <!-- ================================ -->
                    <!-- SECTION 1: Stats Overview -->
                    <!-- ================================ -->
                    @include('admin.components.dauthau.stats-overview')


                    {{-- Crawl actions --}}
                    @include('admin.components.dauthau.crawl-actions')

                    {{-- Crawl history table --}}
                    @include('admin.components.dauthau.history-crawl')



                </div>
            </main>
            <!-- ========================================== -->
            <!-- END: dashboard/index.blade.php -->
            <!-- ========================================== -->

        </div>

        <!-- ========================================== -->
        <!-- Confirmation Modal -->
        <!-- ========================================== -->
        <div x-show="confirmModal.open" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
            @click.self="confirmModal.open = false">
            <div x-show="confirmModal.open" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="w-full max-w-md bg-zinc-900 border border-zinc-800 rounded-xl shadow-2xl">
                <div class="p-6">
                    <div class="w-12 h-12 bg-blue-500/10 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-zinc-100">Xác nhận thao tác</h3>
                    <p class="text-sm text-zinc-400 mt-2" x-text="confirmModal.message"></p>
                </div>
                <div
                    class="px-6 py-4 bg-zinc-800/30 border-t border-zinc-800 flex items-center justify-end gap-3 rounded-b-xl">
                    <button @click="confirmModal.open = false"
                        class="px-4 py-2 text-sm font-medium text-zinc-300 hover:text-zinc-100 transition-colors">Hủy</button>
                    <button @click="executeConfirmedAction"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">Xác
                        nhận</button>
                </div>
            </div>
        </div>

    </div>

    <script>
        function dashboardApp() {
            return {
                // Sidebar State
                sidebarOpen: true,

                // Menu Items
                menuItems: [{
                        id: 'dashboard',
                        label: 'Dashboard',
                        href: '#',
                        active: true,
                        icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>'
                    },
                    // {
                    //     id: 'tenders',
                    //     label: 'Tender Management',
                    //     href: '#',
                    //     active: false,
                    //     icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
                    //     badge: '24,532'
                    // },
                    // {
                    //     id: 'history',
                    //     label: 'Crawl History',
                    //     href: '#',
                    //     active: false,
                    //     icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                    // },
                    // {
                    //     id: 'monitoring',
                    //     label: 'Crawl Monitoring',
                    //     href: '#',
                    //     active: false,
                    //     icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>'
                    // },
                    // {
                    //     id: 'queue',
                    //     label: 'Queue Status',
                    //     href: '#',
                    //     active: false,
                    //     icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>',
                    //     badge: '12'
                    // },
                    // {
                    //     id: 'logs',
                    //     label: 'Logs',
                    //     href: '#',
                    //     active: false,
                    //     icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>'
                    // },
                    // {
                    //     id: 'settings',
                    //     label: 'Settings',
                    //     href: '#',
                    //     active: false,
                    //     icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>'
                    // }
                ],

                // Current Time
                currentTime: new Date().toLocaleTimeString('vi-VN', {
                    hour: '2-digit',
                    minute: '2-digit'
                }),

                // Crawl Loading States
                crawlLoading: {
                    daily: false,
                    full: false,
                    range: false
                },

                // Range Form
                rangeForm: {
                    startDate: '',
                    endDate: '',
                    errors: {
                        startDate: '',
                        endDate: ''
                    }
                },

                // Confirmation Modal
                confirmModal: {
                    open: false,
                    type: '',
                    message: ''
                },

                // Table State
                tableViewState: 'data',
                tableSearch: '',
                tableFilter: 'all',

                // Queue Data
                queues: [{
                        name: 'crawl_queue',
                        active: 4,
                        waiting: 23,
                        failed: 0,
                        status: 'healthy',
                        throughput: 45
                    },
                    {
                        name: 'detail_queue',
                        active: 6,
                        waiting: 156,
                        failed: 2,
                        status: 'healthy',
                        throughput: 120
                    },
                    {
                        name: 'hsmt_queue',
                        active: 2,
                        waiting: 89,
                        failed: 1,
                        status: 'warning',
                        throughput: 34
                    }
                ],

                // Crawl History Data
                crawlHistory: [{
                        id: '1847',
                        type: 'Daily',
                        startTime: '10:45 AM, 20/05/2024',
                        duration: '3m 42s',
                        status: 'Completed',
                        result: '352 tenders'
                    },
                    {
                        id: '1846',
                        type: 'Full',
                        startTime: '06:00 AM, 20/05/2024',
                        duration: '2h 15m',
                        status: 'Completed',
                        result: '1,247 tenders'
                    },
                    {
                        id: '1845',
                        type: 'Daily',
                        startTime: '10:45 AM, 19/05/2024',
                        duration: '4m 12s',
                        status: 'Completed',
                        result: '298 tenders'
                    },
                    {
                        id: '1844',
                        type: 'Range',
                        startTime: '02:30 PM, 18/05/2024',
                        duration: '45m 23s',
                        status: 'Running',
                        result: '— processing'
                    },
                    {
                        id: '1843',
                        type: 'Daily',
                        startTime: '10:45 AM, 18/05/2024',
                        duration: '3m 58s',
                        status: 'Failed',
                        result: 'Connection timeout'
                    },
                    {
                        id: '1842',
                        type: 'Daily',
                        startTime: '10:45 AM, 17/05/2024',
                        duration: '4m 05s',
                        status: 'Completed',
                        result: '312 tenders'
                    },
                    {
                        id: '1841',
                        type: 'Full',
                        startTime: '06:00 AM, 17/05/2024',
                        duration: '—',
                        status: 'Pending',
                        result: 'Queued'
                    },
                    {
                        id: '1840',
                        type: 'Range',
                        startTime: '03:15 PM, 16/05/2024',
                        duration: '1h 02m',
                        status: 'Completed',
                        result: '892 tenders'
                    }
                ],

                // Timeline Events
                timelineEvents: [{
                        type: 'running',
                        message: 'Detail crawler đang xử lý',
                        details: 'Tender ID: VN-2024-0847523 • Queue: detail_queue',
                        time: 'Đang chạy'
                    },
                    {
                        type: 'success',
                        message: 'HSMT crawler hoàn thành',
                        details: '15 hồ sơ mời thầu đã tải xuống',
                        time: '2 phút trước'
                    },
                    {
                        type: 'info',
                        message: 'Queue dispatched',
                        details: '45 jobs đã thêm vào detail_queue',
                        time: '5 phút trước'
                    },
                    {
                        type: 'success',
                        message: 'Crawl Daily hoàn thành',
                        details: '352 tenders mới đã thu thập từ muasamcong.mpi.gov.vn',
                        time: '8 phút trước'
                    },
                    {
                        type: 'info',
                        message: 'Crawl Daily bắt đầu',
                        details: 'Triggered by: Scheduled Task',
                        time: '12 phút trước'
                    },
                    {
                        type: 'warning',
                        message: 'Rate limit warning',
                        details: 'Approaching API rate limit (85/100 requests)',
                        time: '15 phút trước'
                    },
                    {
                        type: 'error',
                        message: 'Connection timeout',
                        details: 'Failed to connect to database after 3 retries',
                        time: '1 giờ trước'
                    }
                ],

                // Computed
                get filteredHistory() {
                    let filtered = this.crawlHistory;

                    if (this.tableFilter !== 'all') {
                        filtered = filtered.filter(item => item.status === this.tableFilter);
                    }

                    if (this.tableSearch) {
                        const search = this.tableSearch.toLowerCase();
                        filtered = filtered.filter(item =>
                            item.id.toLowerCase().includes(search) ||
                            item.type.toLowerCase().includes(search) ||
                            item.result.toLowerCase().includes(search)
                        );
                    }

                    return filtered;
                },

                // Methods
                openConfirmModal(type) {
                    this.confirmModal.type = type;
                    this.confirmModal.message = type === 'daily' ?
                        'Bạn có chắc chắn muốn khởi chạy Crawl Daily? Hệ thống sẽ thu thập tất cả tender mới trong ngày hôm nay.' :
                        'Bạn có chắc chắn muốn khởi chạy Crawl Full? Quá trình này có thể mất từ 2-3 giờ và sử dụng nhiều tài nguyên hệ thống.';
                    this.confirmModal.open = true;
                },

                executeConfirmedAction() {
                    const type = this.confirmModal.type;
                    this.confirmModal.open = false;
                    this.crawlLoading[type] = true;

                    // Simulate API call
                    setTimeout(() => {
                        this.crawlLoading[type] = false;
                    }, 3000);
                },

                submitRangeCrawl() {
                    // Reset errors
                    this.rangeForm.errors = {
                        startDate: '',
                        endDate: ''
                    };

                    // Validate
                    let hasErrors = false;
                    if (!this.rangeForm.startDate) {
                        this.rangeForm.errors.startDate = 'Vui lòng chọn ngày bắt đầu';
                        hasErrors = true;
                    }
                    if (!this.rangeForm.endDate) {
                        this.rangeForm.errors.endDate = 'Vui lòng chọn ngày kết thúc';
                        hasErrors = true;
                    }
                    if (this.rangeForm.startDate && this.rangeForm.endDate && this.rangeForm.startDate > this.rangeForm
                        .endDate) {
                        this.rangeForm.errors.endDate = 'Ngày kết thúc phải sau ngày bắt đầu';
                        hasErrors = true;
                    }

                    if (hasErrors) return;

                    // Submit
                    this.crawlLoading.range = true;
                    setTimeout(() => {
                        this.crawlLoading.range = false;
                        this.rangeForm.startDate = '';
                        this.rangeForm.endDate = '';
                    }, 2000);
                },

                // Init     
                init() {
                    // Update time every minute
                    setInterval(() => {
                        this.currentTime = new Date().toLocaleTimeString('vi-VN', {
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    }, 60000);
                }
            }
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('notifications', () => ({
                open: false,
                unreadCount: 0,
                items: [],
                toasts: [],
                lastId: 0,
                polling: null,

                init() {
                    this.fetchNotifications();
                    this.polling = setInterval(() => this.fetchNotifications(), 5000);
                },

                destroy() {
                    if (this.polling) {
                        clearInterval(this.polling);
                        this.polling = null;
                    }
                },

                async fetchNotifications() {
                    try {
                        const res = await fetch('/notifications', {
                            headers: {
                                Accept: 'application/json'
                            }
                        });
                        if (!res.ok) return;
                        const data = await res.json();
                        console.log('Fetched notifications', data);

                        // Detect new notifications for toast
                        if (this.lastId > 0) {
                            for (const n of data.notifications || []) {
                                if (n.id > this.lastId && !n.read) {
                                    this.toasts.push(n);
                                    // Auto-remove toast after 5s
                                    setTimeout(() => {
                                        this.toasts = this.toasts.filter(t => t.id !== n
                                            .id);
                                    }, 5000);
                                }
                            }
                        }

                        if (data.notifications && data.notifications.length > 0) {
                            this.lastId = Math.max(...data.notifications.map(n => n.id));
                        }

                        this.unreadCount = data.unread_count ?? 0;
                        this.items = data.notifications ?? [];
                    } catch (err) {
                        console.error('Fetch notifications failed', err);
                    }
                },

                async markAsRead(id) {
                    try {
                        await fetch(`/notifications/${id}/read`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]')?.content
                            }
                        });
                        const n = this.items.find(i => i.id === id);
                        if (n) n.read = true;
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                    } catch (err) {
                        console.error('Mark as read failed', err);
                    }
                },

                async markAllAsRead() {
                    try {
                        await fetch('/notifications/read-all', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]')?.content
                            }
                        });
                        this.items.forEach(n => n.read = true);
                        this.unreadCount = 0;
                    } catch (err) {
                        console.error('Mark all as read failed', err);
                    }
                },

                removeToast(id) {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }
            }));
        });
    </script>

</body>

</html>
