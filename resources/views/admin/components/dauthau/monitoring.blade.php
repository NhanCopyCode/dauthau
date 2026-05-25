   <!-- ================================ -->
   <!-- SECTION 4: Crawl Monitoring Timeline -->
   <!-- ================================ -->
   <div class="bg-zinc-900 border border-zinc-800 rounded-xl">
       <div class="p-4 border-b border-zinc-800">
           <div class="flex items-center justify-between">
               <div>
                   <h2 class="text-sm font-semibold text-zinc-100">Activity Timeline</h2>
                   <p class="text-xs text-zinc-500 mt-1">Theo dõi hoạt động crawl theo thời gian thực</p>
               </div>
               <div class="flex items-center gap-2">
                   <span class="relative flex h-2 w-2">
                       <span
                           class="pulse-dot absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                       <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                   </span>
                   <span class="text-xs text-zinc-400">Live</span>
               </div>
           </div>
       </div>
       <div class="p-4">
           <div class="relative">
               <!-- Timeline Line -->
               <div class="absolute left-3 top-2 bottom-2 w-px bg-zinc-800"></div>

               <!-- Timeline Items -->
               <div class="space-y-4">
                   <template x-for="(event, index) in timelineEvents" :key="index">
                       <div class="relative flex gap-4 pl-8">
                           <!-- Dot -->
                           <div :class="{
                               'bg-emerald-500 shadow-emerald-500/30 shadow-lg': event.type === 'success',
                               'bg-blue-500 shadow-blue-500/30 shadow-lg animate-pulse': event.type === 'running',
                               'bg-amber-500 shadow-amber-500/30 shadow-lg': event.type === 'warning',
                               'bg-red-500 shadow-red-500/30 shadow-lg': event.type === 'error',
                               'bg-zinc-600': event.type === 'info'
                           }"
                               class="absolute left-1.5 top-1 w-3 h-3 rounded-full"></div>

                           <!-- Content -->
                           <div class="flex-1 min-w-0">
                               <div class="flex items-start justify-between gap-4">
                                   <div>
                                       <p class="text-sm text-zinc-200" x-text="event.message"></p>
                                       <p class="text-xs text-zinc-500 mt-0.5" x-text="event.details"></p>
                                   </div>
                                   <span class="text-xs text-zinc-600 whitespace-nowrap" x-text="event.time"></span>
                               </div>
                           </div>
                       </div>
                   </template>
               </div>
           </div>
       </div>
   </div>
