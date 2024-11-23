<div>
@if($design->price <= 0)
    <button
        wire:click="addToCart"
        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded"
    >
        Get Free Design
    </button>
@else
    <button
        wire:click="addToCart"
        class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded"
    >
        Add to Cart (${{ number_format($design->price, 2) }})
    </button>
    @endif
</div>
