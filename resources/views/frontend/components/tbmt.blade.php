   <div id="content-tmbt-wrapper"
       class="flex flex-col-reverse md:flex-row align-items-start content-body main-tab-content">
       <div class="flex flex-column align-items-start content-body__left">
           <div class="flex flex-column align-items-start w-100">
               <div class="flex justify-content-between w-100">
                   <h5 class="gen-DLL-view-detai"> {{ $tenderDetail->tender->name }} </h5>
                   <div class="col-2 pr-0 style-J3tqA" id="style-J3tqA"> </div>
               </div>

               @include('frontend.components.nav')

           </div>
           <div class="w-full">
               <div id="content-tmbt" class="tab-pane">
                 
                   <ul class="nav flex-wrap gap-4 lg:nav-tabs isTag border--none" id="khlcnt-tabs">

                       <li class="nav-item">
                           <a href="javascript:void(0)" class="tags-tab text-nowrap active" data-tab="ttc">
                               Thông tin chung
                           </a>
                       </li>

                       <li class="nav-item">
                           <a href="javascript:void(0)" class="tags-tab text-nowrap" data-tab="hsmt">
                               Hồ sơ mời thầu
                           </a>
                       </li>

                       <li class="nav-item">
                           <a href="javascript:void(0)" class="tags-tab text-nowrap" data-tab="clearhsmt">
                               Làm rõ HSMT
                           </a>
                       </li>

                       <li class="nav-item">
                           <a href="javascript:void(0)" class="tags-tab text-nowrap" data-tab="conference">
                               Hội nghị tiền đấu thầu
                           </a>
                       </li>

                       <li class="nav-item">
                           <a href="javascript:void(0)" class="tags-tab text-nowrap" data-tab="kien-nghi">
                               Kiến nghị
                           </a>
                       </li>

                   </ul>
                   @include('frontend.components.contents.ttc')
                   @include('frontend.components.contents.hsmt')
                   @include('frontend.components.contents.lrhsmt')
                   @include('frontend.components.contents.conference')
                   @include('frontend.components.contents.kien-nghi')
               </div>

           </div>
       </div>
       <div class="md:content-body__right w-full">
           <div class="detail__children-2 flex flex-col gap-6">

               <div class="px-4 py-6 md:detail__children-2__view detail__children-2__view-grey">

                   <p class="detail__children-2__text-14 text-center">
                       Thời điểm đóng thầu
                   </p>

                   <p class="detail__children-2__text-16 text-orange-500 text-center">
                       {{ $tenderDetail->effective_close_time_formatted ?? '—' }}
                   </p>

                   <p class="detail__children-2__text-20 text-primary text-center">
                       {{ $tenderDetail->effective_close_date_formatted ?? '—' }}
                   </p>

                   <p class="text-sm text-orange-500 mt-4 text-center">
                       <span>Còn lại</span>:
                       {{ $tenderDetail->effective_countdown ?? '—' }}
                   </p>

               </div>

               <div class="px-4 py-6 md:detail__children-2__view detail__children-2__view-warning">

                   <p class="text-base text-orange-500 font-bold">
                       Khuyến nghị việc tham dự thầu:
                   </p>

                   <p class="text-sm mt-2">
                       Khuyến nghị nhà thầu cần nộp thầu sớm để có thời gian khắc phục,
                       hỗ trợ trong trường hợp lỗi kỹ thuật hoặc sự cố gần thời điểm đóng thầu.
                   </p>

               </div>

           </div>
       </div>


   </div>
