<?php

use function Laravel\Folio\{name};
use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Design;
use Illuminate\Support\Facades\DB;

name('designs');

new class extends Component {

    public $designs;

    public function mount()
    {
        $this->designs = Design::query()
            ->where('active', 1)
            ->get();
    }

} ?>

<x-layouts.marketing>
    @volt('designs')
    <div>
    <div class="hidden md:block h-300px mb-2">
        <x-app.design-browser-banner></x-app.design-browser-banner>
    </div>
    <div class="md:block lg:flex">
        <div class="sticky top-12  z-10 block lg:w-1/4 mx-2">
            <x-app.design-browser-filters></x-app.design-browser-filters>
            </div>
        <div class="border-1 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 p-4">
    @foreach($designs as $design)
       <x-app.design-card
           id="{{$design->id}}"
           name="{{$design->name}}"
           tag="{{$design->tag}}"
           card_image="{{$design->card_image}}"
           category="{{$design->category}}"
           price="{{$design->price}}"
           build_cost="{{$design->build_cost}}"
           impedance="{{$design->impedance}}"
           power="{{$design->power}}"
       />
    @endforeach
    </div>
        </div>
    </div>
    @endvolt
</x-layouts.marketing>

