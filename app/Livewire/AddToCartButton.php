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
            DesignPurchase::create([
                'user_id' => auth()->id(),
                'design_id' => $this->design->id,
                'transaction_id' => 'FREE-' . Str::random(10),
                'amount' => 0.00,
                'purchased_at' => now()
            ]);

            session()->flash('message', 'Free design added to your library!');
            return;  // Add return to prevent adding free items to cart
        }

        $cart = auth()->user()->cart ?? Cart::create([
            'user_id' => auth()->id()
        ]);

        $cart->items()->create([
            'design_id' => $this->design->id,
            'price' => $this->design->price
        ]);

        $this->dispatch('cart-updated');
    }

    public function render()  // Add render method
    {
        return view('livewire.add-to-cart-button');
    }
}
