<?php

use function Laravel\Folio\{name};
use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Driver;
use Illuminate\Support\Facades\DB;

name('drivers');

new class extends Component {

    public $drivers;

    public function mount()
    {
        $this->drivers = Driver::query()
            ->where('active', 1)
            ->get();
    }

} ?>

<x-layouts.marketing>
    @volt('drivers')
    <div>
        <div class="hidden md:block h-300px mb-2">
            <x-app.design-browser-banner></x-app.design-browser-banner>
        </div>
        <div class="md:block lg:flex gap-4">
            <!-- Filter section (1 part) -->
            <div class="sticky top-12 z-10 lg:w-1/4 flex-shrink-0 mx-2">
                <x-app.design-browser-filters></x-app.design-browser-filters>
            </div>
            <!-- Grid section (4 parts) -->
            <div class="lg:w-4/5 border-1 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 p-4">
                @foreach($drivers as $driver)
                    <x-app.driver-card
                        id="{{$driver->id}}"
                        model="{{$driver->model}}"
                        tag="{{$driver->tag}}"
                        card_image="{{$driver->card_image}}"
                        category="{{$driver->category}}"
                        price="{{$driver->price}}"
                        brand="{{$driver->brand}}"
                        impedance="{{$driver->impedance}}"
                        power="{{$driver->power}}"
                    />
                @endforeach
            </div>
        </div>
    </div>
    @endvolt
</x-layouts.marketing>

