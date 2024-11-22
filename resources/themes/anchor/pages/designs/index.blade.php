<?php

use function Laravel\Folio\{name};
use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Design;
use Illuminate\Support\Facades\DB;

name('designs');

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'tailwind';
    // Filters
    public string $search = '';
    public string $sortField = 'name';
    public string $sortDirection = 'asc';
    public array $selectedCategories = [];
    public array $powerRange = [0, 1000];
    public array $impedanceRange = [0, 16];
    public array $priceRange = [0, 10000];

    // Available filter options
    public array $categories = [];

    public function mount()
    {

        $this->categories = DB::table('designs')
            ->select('category')
            ->distinct()
            ->where('active', 1)
            ->whereNotNull('category')
            ->pluck('category')
            ->toArray();
    }

    public function getDesignsProperty()
    {
        return Design::query()
            ->with('designer') // Eager load the designer relationship
            ->where('active', 1)
            ->when($this->search, function ($query) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('tag', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedCategories, function ($query) {
                $query->whereIn('category', $this->selectedCategories);
            })
            ->when($this->priceRange, function ($query) {
                $query->whereBetween('price', $this->priceRange);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(12);
    }

    public function toggleCategory($category)
    {
        if (in_array($category, $this->selectedCategories)) {
            $this->selectedCategories = array_diff($this->selectedCategories, [$category]);
        } else {
            $this->selectedCategories[] = $category;
        }
    }

    public function toggleSort($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
}; ?>

<x-layouts.marketing>
    @volt('designs')
    <div class="bg-white">
        <div>
            <!-- Mobile filter dialog -->
            <div x-data="{ open: false }" class="relative z-40 lg:hidden">
                <div x-show="open" class="fixed inset-0 bg-black bg-opacity-25"></div>

                <div x-show="open" class="fixed inset-0 z-40 flex">
                    <div class="relative ml-auto flex h-full w-full max-w-xs flex-col overflow-y-auto bg-white py-4 pb-12 shadow-xl">
                        <!-- Filter content for mobile -->
                        <div class="px-4">
                            <h2 class="text-lg font-medium text-gray-900">Filters</h2>

                            <!-- Categories -->
                            <div class="mt-4">
                                <h3 class="text-sm font-medium text-gray-900">Categories</h3>
                                <div class="space-y-2">
                                    @foreach($categories as $category)
                                        <div class="flex items-center">
                                            <input type="checkbox"
                                                   wire:click="toggleCategory('{{ $category }}')"
                                                   @checked(in_array($category, $selectedCategories))
                                                   class="h-4 w-4 rounded border-gray-300">
                                            <label class="ml-3 text-sm text-gray-600">{{ $category }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-baseline justify-between border-b border-gray-200 pb-6 pt-8">
                    <h1 class="text-4xl font-bold tracking-tight text-gray-900">Speaker Designs</h1>

                    <!-- Sort -->
                    <div class="flex items-center">
                        <div class="relative inline-block text-left">
                            <select wire:model="sortField" class="rounded-md border-gray-300 py-2 text-sm font-medium text-gray-700">
                                <option value="name">Name</option>
                                <option value="price">Price</option>
                                <option value="power">Power</option>
                                <option value="impedance">Impedance</option>
                                <option value="created_at">Newest</option>
                            </select>
                        </div>
                    </div>
                </div>

                <section aria-labelledby="products-heading" class="pb-24 pt-6">
                    <div class="grid grid-cols-1 gap-x-8 gap-y-10 lg:grid-cols-4">
                        <!-- Filters -->
                        <div class="hidden lg:block">
                            <!-- Search -->
                            <div class="mb-6">
                                <input type="text"
                                       wire:model.live.debounce.300ms="search"
                                       placeholder="Search designs..."
                                       class="w-full rounded-md border-gray-300">
                            </div>

                            <!-- Categories -->
                            <div class="border-b border-gray-200 pb-6">
                                <h3 class="text-sm font-medium text-gray-900">Categories</h3>
                                <div class="space-y-2 mt-2">
                                    @foreach($categories as $category)
                                        <div class="flex items-center">
                                            <input type="checkbox"
                                                   wire:click="toggleCategory('{{ $category }}')"
                                                   @checked(in_array($category, $selectedCategories))
                                                   class="h-4 w-4 rounded border-gray-300">
                                            <label class="ml-3 text-sm text-gray-600">{{ $category }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>

                        <!-- Product grid -->
                        <div class="lg:col-span-3">
                            <div class="grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach($this->designs as $design)
                                    <a href="/designs/design/{{ $design->id }}" class="group relative bg-white rounded-lg shadow hover:shadow-lg transition-shadow duration-200">
                                        <!-- Card Image -->
                                        <div class="aspect-w-16 aspect-h-9 w-full rounded-t-lg overflow-hidden">
                                            @if($design->card_image)
                                                <img
                                                    src="{{ $design->card_image }}"
                                                    alt="{{ $design->name }}"
                                                    class="w-full h-full object-cover"
                                                >
                                            @else
                                                <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                                    <span class="text-gray-400">No image available</span>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="p-4">
                                            <!-- Designer info -->
                                            <div class="flex items-center mb-4">
                                                <span class="text-sm text-gray-500">By {{ $design->designer->name }}</span>
                                            </div>

                                            <!-- Design info -->
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                {{ $design->name }}
                                            </h3>
                                            @if($design->tag)
                                                <p class="mt-1 text-sm text-gray-600 italic">{{ $design->tag }}</p>
                                            @endif

                                            <!-- Technical specs -->
                                            <div class="mt-4 space-y-2">
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-gray-500">Category:</span>
                                                    <span class="text-sm font-medium">{{ $design->category }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-gray-500">Power:</span>
                                                    <span class="text-sm font-medium">{{ $design->power }}W</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-gray-500">Impedance:</span>
                                                    <span class="text-sm font-medium">{{ $design->impedance }}Ω</span>
                                                </div>
                                            </div>

                                            <!-- Pricing -->
                                            <div class="mt-4 border-t pt-4">
                                                <div class="flex justify-between items-baseline">
                                                    <div>
                                                        <span class="text-sm text-gray-500">Plans:</span>
                                                        <span class="ml-1 text-lg font-bold text-gray-900">${{ number_format($design->price, 2) }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-sm text-gray-500">Build Cost:</span>
                                                        <span class="ml-1 text-base font-medium text-gray-900">${{ number_format($design->build_cost, 2) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>

                            <!-- Pagination -->
                            <div class="mt-8">
                                {{ $this->designs->links() }}
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>
    @endvolt
</x-layouts.marketing>
