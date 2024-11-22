<?php

use function Laravel\Folio\{name};
use Livewire\Volt\Component;
use App\Models\Design;

name('design');

new class extends Component {
    public Design $design;

    public function mount(string $id)
    {
        $this->design = Design::with(['designer', 'components.driver'])->findOrFail($id);
    }
}; ?>

<x-layouts.marketing>
    @volt('design')
    <div class="bg-white">
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
            <div class="lg:grid lg:grid-cols-2 lg:gap-x-8 lg:items-start">
                <!-- Image section -->
                <div class="aspect-w-16 aspect-h-9 w-full rounded-lg overflow-hidden mb-8 lg:mb-0">
                    @if($design->card_image)
                        <img src="{{ $design->card_image }}" alt="{{ $design->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                            <span class="text-gray-400">No image available</span>
                        </div>
                    @endif
                </div>

                <!-- Design info -->
                <div class="px-4 lg:px-8">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $design->name }}</h1>
                    @if($design->tag)
                        <p class="mt-2 text-lg text-gray-600 italic">{{ $design->tag }}</p>
                    @endif

                    <div class="mt-4">
                        <p class="text-sm text-gray-500">Designed by</p>
                        <p class="text-lg font-medium text-gray-900">{{ $design->designer->name }}</p>
                    </div>

                    <!-- Key Specifications -->
                    <div class="mt-8 border-t border-gray-200 pt-8">
                        <h2 class="text-xl font-semibold text-gray-900">Specifications</h2>
                        <dl class="mt-4 grid grid-cols-2 gap-4">
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
                        <div class="mt-8 border-t border-gray-200 pt-8">
                            <h2 class="text-xl font-semibold text-gray-900">Components</h2>
                            <ul class="mt-4 divide-y divide-gray-200">
                                @foreach($design->components as $component)
                                    <li class="py-4">
                                        <div class="flex justify-between">
                                            <div class="flex-1">
                                                <h4 class="text-lg font-medium text-gray-900">
                                                    Driver Brand and Model here
                                                </h4>
                                                <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                                                    <span>Component Position</span>
                                                    <span>•</span>
                                                    <span>Component Size</span>
                                                    <span>•</span>
                                                    <span>Component Category</span>
                                                </div>
                                                @if($component->description)
                                                    <p class="mt-2 text-sm text-gray-600">Component Description</p>
                                                @endif
                                            </div>
                                            <div class="ml-4 flex flex-col items-end">
                                                <span class="text-sm font-medium text-gray-900">Qty: Component Quantyti</span>
                                                @if($component->low_frequency || $component->high_frequency)
                                                    <span class="mt-1 text-sm text-gray-500">
                                    Component Low Hz - Component High Hz
                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Description -->
                    <div class="mt-8 border-t border-gray-200 pt-8">
                        <h2 class="text-xl font-semibold text-gray-900">About this Design</h2>
                        <div class="mt-4 text-gray-500">
                            <html>{{ $design->description }}</html>
                        </div>
                    </div>

                    <!-- Bill of Materials -->
                    @if($design->bill_of_materials)
                        <div class="mt-8 border-t border-gray-200 pt-8">
                            <h2 class="text-xl font-semibold text-gray-900">Bill of Materials</h2>
                            <div class="mt-4">
                                <ul class="divide-y divide-gray-200">
                                    @foreach($design->bill_of_materials as $item)
                                        <li class="py-3 flex justify-between">
                                            <span class="text-gray-900">{{ $item['name'] ?? 'Unknown Item' }}</span>
                                            @if(isset($item['quantity']))
                                                <span class="text-gray-500">x{{ $item['quantity'] }}</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <!-- Summary -->
                    @if($design->summary)
                        <div class="mt-8 border-t border-gray-200 pt-8">
                            <h2 class="text-xl font-semibold text-gray-900">Summary</h2>
                            <p class="mt-4 text-gray-500">{{ $design->summary }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
    @endvolt
</x-layouts.marketing>
