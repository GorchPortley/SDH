<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\Design;
use App\Models\DesignPurchase;
use Illuminate\Support\Str;
use Livewire\Component;

class AddToCartButton extends Component
{
    public $designId;  // Change to accept ID instead of Design object
    public ?Design $design = null;  // Store Design object

    public function mount($designId)
    {
        $this->designId = $designId;
        $this->design = Design::findOrFail($designId);
    }

    public function addToCart()
    {
        if ($this->design->price <= 0) {
            // Check for existing free design
            $existingPurchase = DesignPurchase::where([
                'user_id' => auth()->id(),
                'design_id' => $this->design->id
            ])->exists();

            if ($existingPurchase) {
                session()->flash('message', 'You already own this design!');
                return;
            }

            DesignPurchase::create([
                'user_id' => auth()->id(),
                'design_id' => $this->design->id,
                'transaction_id' => 'FREE-' . Str::random(10),
                'amount' => 0.00,
                'purchased_at' => now()
            ]);

            session()->flash('message', 'Free design added to your library!');
            return;
        }

        // For paid designs, check cart for duplicates
        $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);

        // Check if design already in cart
        $existingCartItem = $cart->items()->where('design_id', $this->design->id)->exists();

        if ($existingCartItem) {
            session()->flash('message', 'This design is already in your cart!');
            return;
        }

        $cart->items()->create([
            'design_id' => $this->design->id,
            'price' => $this->design->price
        ]);

        session()->flash('message', 'Design added to cart!');
        $this->dispatch('cart-updated');
    }
    public function render()  // Add render method
    {
        return view('livewire.add-to-cart-button');
    }
}
