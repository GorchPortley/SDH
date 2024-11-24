<div x-data="{ sidebarOpen: false }" @open-sidebar.window="sidebarOpen = true"
     x-init="
        $watch('sidebarOpen', function(value){
            if(value){ document.body.classList.add('overflow-hidden'); } else { document.body.classList.remove('overflow-hidden'); }
        });
    "
     class="relative z-50 w-screen md:w-auto" x-cloak>
    {{-- Backdrop for mobile --}}
    <div x-show="sidebarOpen" @click="sidebarOpen=false"
         class="fixed top-0 right-0 z-50 w-screen h-screen duration-300 ease-out bg-black/20 dark:bg-white/10"></div>

    {{-- Sidebar --}}
    <div :class="{ '-translate-x-full': !sidebarOpen }"
         class="fixed top-0 left-0 flex items-stretch -translate-x-full overflow-hidden lg:translate-x-0 z-50 h-dvh md:h-screen transition-[width,transform] duration-150 ease-out bg-purple-950 dark:bg-zinc-900 w-64 group @if(config('wave.dev_bar')){{ 'pb-10' }}@endif">
        <div class="flex flex-col justify-between w-full overflow-auto md:h-full h-svh pt-4 pb-2.5">
            <div class="relative flex flex-col">
                <button x-on:click="sidebarOpen=false"
                        class="flex items-center justify-center flex-shrink-0 w-10 h-10 ml-4 rounded-md lg:hidden text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 dark:hover:bg-zinc-700/70 hover:bg-gray-200/70">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                        </svg>
                </button>

                <div class="flex items-center px-5 space-x-2">
                    <a href="/" class="flex justify-center items-center py-4 pl-0.5 space-x-1 font-bold text-white">
                        <h1>sdLabs.cc</h1>
                    </a>
                </div>

                <div
                    class="flex flex-col justify-start items-center px-4 space-y-1.5 w-full h-full text-slate-600 dark:text-zinc-400">
                    <x-app.sidebar-link href="{{route('dashboard')}}" icon="phosphor-house"
                                        active="Request::is('dashboard')">Dashboard
                    </x-app.sidebar-link>
                    <x-app.sidebar-link href="{{route('dashboard.designs')}}" icon="phosphor-intersect-three"
                                        active="Request::is('dashboard.designs')">Designs
                    </x-app.sidebar-link>
                    <x-app.sidebar-link href="{{route('dashboard.drivers')}}" icon="phosphor-circles-three"
                                        active="Request::is('dashboard.drivers')">Drivers
                    </x-app.sidebar-link>
                    <x-app.sidebar-link href="{{route('dashboard.files')}}" icon="phosphor-folder"
                                        active="Request::is('dashboard.files')">Files
                    </x-app.sidebar-link>
                </div>
            </div>

            <div class="relative px-2.5 space-y-1.5 text-zinc-700 dark:text-zinc-400">

                <div x-show="sidebarTip" x-data="{ sidebarTip: $persist(true) }" class="px-1 py-3" x-collapse x-cloak>
                    <div
                        class="relative w-full px-4 py-3 space-y-1 border rounded-lg bg-zinc-50 text-zinc-700 dark:text-zinc-100 dark:bg-zinc-800 border-zinc-200/60 dark:border-zinc-700">
                        <button @click="sidebarTip=false"
                                class="absolute top-0 right-0 z-50 p-1.5 mt-2.5 mr-2.5 rounded-full opacity-80 cursor-pointer hover:opacity-100 hover:bg-zinc-100 hover:dark:bg-zinc-700 hover:dark:text-zinc-300 text-zinc-500 dark:text-zinc-400">
                            <x-phosphor-x-bold class="w-3 h-3"/>
                        </button>
                        <h5 class="pb-1 text-sm font-bold -translate-y-0.5">Edit This Section</h5>
                        <p class="block pb-1 text-xs opacity-80 text-balance">You can edit any aspect of your user
                            dashboard. This section can be found inside your theme component/app/sidebar file.</p>
                    </div>
                </div>

                <div class="w-full h-px my-2 bg-slate-100 dark:bg-zinc-700"></div>
                <x-app.user-menu/>
            </div>
        </div>
    </div>
</div>
