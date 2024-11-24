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
    <div class="bg-white dark:bg-zinc-900 dark:text-white">
        <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="flex py-4" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li><a href="/designs" class="text-gray-500 hover:text-gray-700">Designs</a></li>
                    <li class="text-gray-400">/</li>
                    <li class="text-gray-900">{{ $design->name }}</li>
                </ol>
            </nav>

            <!-- Design Header -->

            <div class="">
                <!-- Image section -->
                <div class="lg:grid lg:grid-cols-2">
                <div class="aspect-w-16 aspect-h-9 w-full align-middle rounded-lg mb-2 lg:mb-0">
              <!--change src to appropriate url --><img src="https://cong.test/storage/{{$design->card_image}}" alt="{{ $design->name }}" class="w-full h-auto">
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
                            @if($design->category)
                                <div>
                                    <dt class="text-sm text-gray-500">Category</dt>
                                    <dd class="mt-1 text-lg font-medium text-gray-900">{{ $design->category }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    <!-- Components -->
                    @if($design->components->count() > 0)
                        <div class="mt-8 border-t border-gray-200 pt-2">
                            <h2 class="text-xl font-semibold text-gray-900 border-b-2 p-2 border-zinc-400">Components</h2>
                            <table class="mt-4 divide-y divide-gray-200">
                                @foreach($design->components as $component)
                                    <div class="border-b py-2">
                                        <div class="flex justify-between">
                                            <div class="flex-1">
                                                <h4 class="text-lg font-medium text-gray-900">
                                                   {{$component->driver->brand}} - {{$component->driver->model}}
                                                </h4>
                                                <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                                                    <span>{{$component->position}}</span>
                                                    <span>•</span>
                                                    <span>{{$component->driver->size}}</span>
                                                    <span>•</span>
                                                    <span>{{$component->driver->category}}</span>
                                                </div>
                                            </div>
                                            <div class="ml-4 flex flex-col items-end">
                                                <span class="text-sm font-medium text-gray-900">Qty: {{$component->quantity}}</span>
                                                <span class="mt-1 text-sm text-gray-500">
                                {{$component->low_frequency}} Hz - {{$component->high_frequency}} Hz
                            </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </table>
                        </div>
                    @endif
                </div>
            </div>
<div>
                    <!-- Description -->
                    <div class="border-gray-200 pt-8">
                        <h2 class="text-xl font-semibold text-gray-900">About this Design</h2>
                        <div class="mt-4">
                            {!! str($design->summary)->sanitizeHtml() !!}
                        </div>
                    </div>
</div>

                @if($design->price < 0.01 || $design->sales()->where('user_id', auth()->id())->exists())
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
                            {!! str($design->description)->sanitizeHtml() !!}
                        </div>
                    @endif
                @endif
                @if($design->sales()->where('user_id', auth()->id())->doesntExist())
                    <div class="w-full bg-zinc-600 h-80">
                        <p class="text-white align-middle justify-center">Sorry, you need Access for this section</p>
                        <div>
                            @endif
                </div>
            </div>
        </main>
    </div>
    @endvolt
</x-layouts.marketing>
