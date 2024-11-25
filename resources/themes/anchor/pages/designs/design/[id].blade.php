<?php

use function Laravel\Folio\{name};
use Livewire\Volt\Component;
use App\Models\Design;

name('design');

new class extends Component {
    public Design $design;

    public function mount(string $id)
    {
        $this->design = Design::with(['designer', 'components.driver', 'sales'])->findOrFail($id);
    }
}; ?>

<x-layouts.marketing>
    @volt('design')
    <div class="bg-white dark:bg-zinc-900 dark:text-white" x-data="{
        publicSectionOpen: true,
        lockedSectionOpen: true
    }">
        <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="flex py-4" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li><a href="/designs" class="text-gray-500 hover:text-gray-700">Designs</a></li>
                    <li class="text-gray-400">/</li>
                    <li class="text-gray-900">{{ $design->name }}</li>
                </ol>
            </nav>
            <hr class="my-6 border-t-2 border-gray-300">

{{--Begin Key Details section--}}

            <div class="">
                <!-- Image section -->
                <div class="lg:grid lg:grid-cols-2">
                    <div class="aspect-w-16 aspect-h-9 w-full rounded-lg mb-2 lg:mb-0 flex items-center justify-center">
                        <img
                            src="{{$appUrl = config('app.url')}}/storage/{{ $design->card_image }}"
                            alt="{{ $design->name }}"
                            class="w-full h-auto object-contain"
                        >
                    </div>

                <!-- Design info -->
                <div class="px-4 lg:px-8">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $design->name }}</h1>
                    @if($design->tag)
                        <p class="mt-1 text-lg text-gray-600 italic">{{ $design->tag }}</p>
                    @endif

                    <div class="mt-2">
                        <p class="text-sm text-gray-500">Designed by</p>
                        <p class="text-lg font-medium text-gray-900">{{ $design->designer->name }}</p>
                    </div>

                    <!-- Key Specifications -->
                    <div class="mt-2 border-t border-gray-200 pt-4">
                        <h2 class="text-xl font-semibold text-gray-900">Specifications</h2>
                        <dl class="mt-2 grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm text-gray-500">Power Handling</dt>
                                <dd class="mt-1 text-lg font-medium text-gray-900">{{ $design->power }}W</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">Impedance</dt>
                                <dd class="mt-1 text-lg font-medium text-gray-900">{{ $design->impedance }}Ω</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">Plans Price</dt>
                                <dd class="mt-1 text-lg font-medium text-gray-900">${{ number_format($design->price, 2) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">Estimated Build Cost</dt>
                                <dd class="mt-1 text-lg font-medium text-gray-900">${{ number_format($design->build_cost, 2) }}</dd>
                            </div>
                                <div>
                                    <dt class="text-sm text-gray-500">Category</dt>
                                    <dd class="mt-1 text-lg font-medium text-gray-900">{{ $design->category }}</dd>
                                </div>
                            <div>
                                <dd class="mt-4">
                                    <livewire:add-to-cart-button :design-id="$design->id" /></dd>
                            </div>
                        </dl>
                    </div>
                </div>
{{--      End Key Details Section              --}}
                </div>
                    {{-- Collapsible Public Section --}}
                    <div class="mt-8 col-span-2">
                        <button
                            @click="publicSectionOpen = !publicSectionOpen"
                            class="w-full flex justify-between items-center p-4 bg-gray-100 hover:bg-gray-200 rounded-t-lg transition-colors"
                        >
                            <h2 class="text-xl font-bold">Frequency Response & Design Overview</h2>
                            <span x-text="publicSectionOpen ? '−' : '+'" class="text-2xl"></span>
                        </button>

                        <div x-show="publicSectionOpen" x-transition>
                            <div class="w-auto py-4 col-span-2">
                                <livewire:frequency-response-viewer :design="$design" />


                            <div>
                                <div class="border-gray-200 pt-8">
                                    <h2 class="text-xl font-semibold text-gray-900">About this Design</h2>
                                    <div class="mt-4">
                                        <x-safe-html-renderer :content="$design->summary" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                    {{-- Collapsible Locked Section --}}
                    <div class="mt-8">
                        <button
                            @click="lockedSectionOpen = !lockedSectionOpen"
                            class="w-full flex justify-between items-center p-4 bg-gray-100 hover:bg-gray-200 rounded-t-lg transition-colors"
                        >
                            <h2 class="text-xl font-bold">Build Details & Full Description</h2>
                            <span x-text="lockedSectionOpen ? '−' : '+'" class="text-2xl"></span>
                        </button>

                        <div x-show="lockedSectionOpen" x-transition>
                            @if(auth()->check())
                                @if($design->price < 0.01 || $design->sales()->where('user_id', auth()->id())->exists() || auth()->user()->hasRole('admin'))
                                    <!-- Bill of Materials -->
                                    @if($design->bill_of_materials)
                                        <div class="mt-8 border-t border-gray-200 pt-8">
                                            <h2 class="text-xl font-semibold text-gray-900">Bill of Materials</h2>
                                            <div class="mt-4">
                                                <ul class="divide-y divide-gray-200">
                                                    @foreach($design->bill_of_materials as $material=>$quantity)
                                                        <li class="py-3 flex justify-between">
                                                            <span class="text-gray-900">{{ $material ?? 'Unknown Item' }}</span>
                                                            <div class="flex items-center space-x-4">
                                                                <span class="text-gray-500">x{{ $quantity }}</span>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Main Description -->
                                    @if($design->description)
                                        <div class="mt-8 border-t border-gray-200 pt-8">
                                            <h2 class="text-xl font-semibold text-gray-900">Full Description</h2>
                                            <x-safe-html-renderer :content="$design->description" />
                                        </div>
                                    @endif
                                @else
                                    {{-- User is logged in but doesn't have access --}}
                                    <div class="mt-8 border-t border-gray-200 pt-8">
                                        <div class="w-full bg-zinc-600 p-8 rounded-lg text-center">
                                            <p class="text-white text-lg">Sorry, you need Access for this section</p>
                                            <a href="{{ route('shop.show', $design->id) }}" class="mt-4 inline-block px-4 py-2 bg-white text-zinc-600 rounded-md hover:bg-zinc-100">
                                                Purchase Access
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            @else
                                {{-- Guest user --}}
                                <div class="mt-8 border-t border-gray-200 pt-8">
                                    <div class="w-full bg-zinc-600 p-8 rounded-lg text-center">
                                        <p class="text-white text-lg">Sorry, you need to be logged in to access this section</p>
                                        <div class="mt-4 space-x-4">
                                            <a href="{{ route('login') }}" class="inline-block px-4 py-2 bg-white text-zinc-600 rounded-md hover:bg-zinc-100">
                                                Login
                                            </a>
                                            <a href="{{ route('register') }}" class="inline-block px-4 py-2 bg-white text-zinc-600 rounded-md hover:bg-zinc-100">
                                                Register
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

        </main>
    </div>
    @endvolt
</x-layouts.marketing>
