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
        $this->designs = DB::table('designs')->where('active', 1)->get();
    }

} ?>

<x-layouts.marketing>
    @volt('designs')
    <div>
        <div class="border-2 hidden md:block h-60 mb-2">BANNER</div>
    <div class="md:block lg:flex">

        <div class="sticky top-12 block lg:w-1/4 bg-white mr-2"><x-app.design-browser-filters></x-app.design-browser-filters></div>
    <div class="border-2 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 p-4">
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
       ></x-app.design-card>
    @endforeach
    </div>
        </div>
    </div>
    @endvolt
</x-layouts.marketing>

