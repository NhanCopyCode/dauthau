@extends('layouts.frontend')
@section('style')
    <link rel="stylesheet" href="{{ asset('css/dauthau.css') }}">
@endsection
@section('content')
    <div class="bg-gray-50 ">


        <div class="max-w-7xl mx-auto p-4 md:p-6">
            <div class="mb-4 flex items-center justify-between">

                <!-- Back button -->
                <a href="{{ url('/') }}"
                    class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-blue-600 transition group">

                    <!-- Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-4 h-4 transition-transform duration-200 group-hover:-translate-x-1" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>

                    <span class="font-medium">Quay lại trang chủ</span>
                </a>

            </div>
            @include('frontend.components.tbmt')


            @include('frontend.components.cgtt')
        </div>

    </div>
@endsection

@section('scripts-footer')
    <script>
        function changeTab(tab) {

            const url = new URL(window.location);
            url.searchParams.set('tab', tab);
            window.history.replaceState({}, '', url);

            document.querySelectorAll('#style-6I4kv a').forEach(el => {
                el.classList.remove('active', 'text-blue-600', 'border-blue-600');
            });

            const activeTabs = document.querySelectorAll('.tab-' + tab);

            if (activeTabs.length) {
                activeTabs.forEach(el => {
                    el.classList.add('active', 'text-blue-600', 'border-blue-600');
                });
            }

            document.querySelectorAll('.main-tab-content').forEach(el => {
                el.classList.add('hidden');
            });

            const activeContent = document.getElementById('content-' + tab + '-wrapper');

            if (activeContent) {
                activeContent.classList.remove('hidden');
            }
        }

        changeTab('{{ request('tab', 'tmbt') }}');
    </script>

    <script>
        function changeInnerTab(tab) {
            
            // ===== 1. active tab =====
            document.querySelectorAll('#khlcnt-tabs .tags-tab').forEach(el => {
                el.classList.remove('active', 'text-blue-600', 'border-blue-600');
            });

            const activeTab = document.querySelector(`[data-tab="${tab}"]`);
            if (activeTab) {
                activeTab.classList.add('active', 'text-blue-600', 'border-blue-600');
            }

            // ===== 2. hide all content =====
            document.querySelectorAll('.tab-content-item').forEach(el => {
                el.classList.add('hidden');
            });

            // ===== 3. show đúng content =====
            const activeContent = document.getElementById('content-' + tab);
            if (activeContent) {
                activeContent.classList.remove('hidden');
            }

            // ===== 4. update URL (optional nhưng nên có) =====
            const url = new URL(window.location);
            url.searchParams.set('sub_tab', tab);
            window.history.replaceState({}, '', url);
        }


        // ===== 5. bind click (event delegation - chuẩn senior) =====
        document.getElementById('khlcnt-tabs').addEventListener('click', function(e) {
            const tab = e.target.dataset.tab;

            if (tab) {
                changeInnerTab(tab);
            }
        });


        // ===== 6. init từ URL =====
        function initInnerTab() {
            const params = new URLSearchParams(window.location.search);
            const tab = params.get('sub_tab') || 'ttc';

            changeInnerTab(tab);
        }

        initInnerTab();
    </script>
@endsection
