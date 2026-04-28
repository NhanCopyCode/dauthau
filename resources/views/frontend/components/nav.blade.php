 <div class="w-100">
     <ul class="nav nav-tabs nav-scroll nav-title border--none style-6I4kv" id="style-6I4kv"
         style="white-space: nowrap; overflow-x: auto; overflow-y: hidden;">

         <!-- TAB: TBMT -->
         <li class="nav-item" style="display: flex !important;">
             <a href="javascript:void(0)" class="tab-tmbt {{ request('tab', 'tmbt') == 'tmbt' ? 'active' : '' }}"
                 onclick="changeTab('tmbt')">
                 Thông báo mời thầu
             </a>
         </li>

         <!-- TAB: CGTT -->
         {{-- @if ($hasCgtt)
             <li class="nav-item">
                 <a href="javascript:void(0)" class="tab-cgtt {{ request('tab') == 'cgtt' ? 'active' : '' }}"
                     onclick="changeTab('cgtt')">
                     Phòng chào giá trực tuyến
                 </a>
             </li>
         @endif --}}

         <!-- TAB: KQ CGTT -->
         @if ($hasCgtt && str_contains($stepCode, 'step-3'))
             <li class="nav-item" id="tab-kqcgtt-wrapper">
                 <a href="javascript:void(0)" class="tab-kqcgtt {{ request('tab') == 'kqcgtt' ? 'active' : '' }}"
                     onclick="changeTab('kqcgtt')">
                     Kết quả chào giá trực tuyến
                 </a>
             </li>
         @endif

         <!-- TAB: KQLCNT -->
         @if ($stepCode === 'notify-contractor-step-4-kqlcnt')
             <li class="nav-item" id="tab-kqlcnt-wrapper">
                 <a href="javascript:void(0)" class="tab-kqlcnt {{ request('tab') == 'kqlcnt' ? 'active' : '' }}"
                     onclick="changeTab('kqlcnt')">
                     Kết quả lựa chọn nhà thầu
                 </a>
             </li>
         @endif

         <!-- TAB: CONTRACT -->
         @if ($hasContract)
             <li class="nav-item" id="tab-contract-wrapper">
                 <a href="javascript:void(0)" class="tab-contract {{ request('tab') == 'contract' ? 'active' : '' }}"
                     onclick="changeTab('contract')">
                     Thông tin chủ yếu của hợp đồng
                 </a>
             </li>
         @endif

     </ul>
 </div>
