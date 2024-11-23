<?php

use function Laravel\Folio\{name};
use Livewire\Volt\Component;
use App\Models\CartItem;
use App\Models\DesignPurchase;

name('cart');

new class extends Component
{
    public function removeItem($cartItemId)
    {
        CartItem::find($cartItemId)->delete();
        $this->dispatch('cart-updated');
    }

    public function checkout()
    {
        try {
            $cart = auth()->user()->cart;
            $total = $cart->total();

            // Process payment with Stripe/Paddle
            $payment = auth()->user()->charge(
                $total * 100,
                'Design Purchase'
            );

            // Create purchase records
            foreach($cart->items as $item) {
                DesignPurchase::create([
                    'user_id' => auth()->id(),
                    'design_id' => $item->design_id,
                    'transaction_id' => $payment->id,
                    'amount' => $item->price,
                    'purchased_at' => now()
                ]);
            }

            // Clear cart
            $cart->items()->delete();

            session()->flash('message', 'Purchase successful!');
            return redirect()->route('my-designs');

        } catch (\Exception $e) {
            session()->flash('error', 'Payment failed: ' . $e->getMessage());
        }
    }

    public function with(): array
    {
        return [
            'items' => auth()->user()->cart?->items->load('design') ?? collect(),
            'total' => auth()->user()->cart?->total() ?? 0
        ];
    }
}; ?>

<x-layouts.app>
    @volt('cart')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold mb-8">Shopping Cart</h1>

        @if($items->count() > 0)
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow overflow-hidden">
                <!-- Cart Items -->
                <div class="divide-y divide-gray-200 dark:divide-zinc-700">
                    @foreach($items as $item)
                        <div class="p-6 flex items-center justify-between">
                            <div class="flex items-center">
                                @if($item->design->card_image)
                                    <img src="{{ $item->design->card_image }}"
                                         class="w-16 h-16 object-cover rounded">
                                @endif
                                <div class="ml-4">
                                    <h3 class="font-medium dark:text-white">{{ $item->design->name }}</h3>
                                    <p class="text-gray-500 dark:text-zinc-400">
                                        Designed by {{ $item->design->designer->name }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-4">
                                <span class="font-medium dark:text-white">
                                    ${{ number_format($item->price, 2) }}
                                </span>
                                <button
                                    wire:click="removeItem({{ $item->id }})"
                                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                >
                                    Remove
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Cart Summary -->
                <div class="bg-gray-50 dark:bg-zinc-900 p-6">
                    <div class="flex justify-between text-lg font-medium dark:text-white">
                        <span>Total</span>
                        <span>${{ number_format($total, 2) }}</span>
                    </div>

                    <button
                        wire:click="checkout"
                        wire:loading.attr="disabled"
                        class="mt-4 w-full bg-purple-600 text-white py-2 px-4 rounded hover:bg-purple-700 disabled:opacity-50"
                    >
                        <span wire:loading.remove>Proceed to Checkout</span>
                        <span wire:loading>Processing...</span>
                    </button>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500 dark:text-zinc-400">Your cart is empty</p>
                <a href="{{ route('designs') }}"
                   class="mt-4 inline-block text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300"
                >
                    Continue Shopping
                </a>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mt-4 bg-red-50 dark:bg-red-900/50 text-red-600 dark:text-red-400 p-4 rounded">
                {{ session('error') }}
            </div>
        @endif
    </div>
    @endvolt
</x-layouts.app>
