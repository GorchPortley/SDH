<div>

    @if($design->price <= 0)
        @if(auth()->user()->designPurchases()->where('design_id', $design->id)->exists())
            <x-button
                    disabled
                color="success"
            >
                Already Owned
            </x-button>
        @else
            <x-button
                wire:click="addToCart"
                color="success"
            >
                Get Free Design
            </x-button>
        @endif
    @else
        @if(auth()->user()->cart?->items()->where('design_id', $design->id)->exists())
            <x-button
                disabled
            >
                Design Added
            </x-button>
        @else
            <x-button
                wire:click="addToCart"
                color="danger"
            >
                Add to Cart
            </x-button>
        @endif
    @endif
</div>
