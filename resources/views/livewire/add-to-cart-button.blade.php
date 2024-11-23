<div>
    @if (session()->has('message'))
        <div class="text-sm text-green-600 mb-2">
            {{ session('message') }}
        </div>
    @endif

    @if($design->price <= 0)
        @if(auth()->user()->designPurchases()->where('design_id', $design->id)->exists())
            <button
                disabled
                class=""
            >
                Already Owned
            </button>
        @else
            <button
                wire:click="addToCart"
                class=""
            >
                Get Free Design
            </button>
        @endif
    @else
        @if(auth()->user()->cart?->items()->where('design_id', $design->id)->exists())
            <button
                disabled
                class=""
            >
                In Cart
            </button>
        @else
            <button
                wire:click="addToCart"
                class=""
            >
                Add to Cart (${{ number_format($design->price, 2) }})
            </button>
        @endif
    @endif
</div>
