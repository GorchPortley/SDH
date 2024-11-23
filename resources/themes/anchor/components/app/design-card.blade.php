@props([
    'id' => '',
    'name' => '',
    'tag'=>'',
    'card_image'=>'',
    'category'=>'',
    'price'=>'',
    'build_cost'=>'',
    'impedance'=>'',
    'power'=>'',
])

create-designs-page
<div class="flex flex-col bg-white dark:bg-zinc-800 dark:text-white border border-gray-200 dark:border-zinc-700 rounded-lg overflow-hidden h-[600px] w-full">
    <!-- Image Section (40% of height) -->
    <div class="h-2/5 w-full">
        <a href="designs/design/{{$id}}">
        <img src="{{ $card_image }}" class="w-full h-full object-cover">
        </a>
    </div>

    <!-- Content Section (60% of height) -->
    <div class="h-3/5 flex flex-col p-4">
        <!-- Title and Basic Info (20% of content height) -->
        <div class="h-1/5 text-center">
            <h5 class="text-lg font-bold">
                <a href="/designs/design/{{ $id }}" class="hover:text-purple-600 dark:hover:text-purple-400">{{ $name }}</a>
            </h5>
            <p class="text-gray-600 dark:text-zinc-400 italic text-sm">{{ $tag }}</p>
        </div>

        <!-- Pricing (15% of content height) -->
        <div class="h-[15%] text-center flex flex-col justify-center">
            <p class="text-purple-950 dark:text-purple-300">Design Price: ${{ $price }}</p>
            <p class="text-gray-600 dark:text-zinc-400">Build Cost: ${{ $build_cost }}</p>
        </div>

create-designs-page
        <!-- Specs Table (45% of content height) -->
        <div class="h-[45%] flex items-center">
            <table class="w-full text-sm">
                <tr class="border-b dark:border-zinc-700">
                    <td class="py-1 text-gray-600 dark:text-zinc-400">Design Type</td>
                    <td class="py-1 text-right font-medium">{{ $category }}</td>
                </tr>
                <tr class="border-b dark:border-zinc-700">
                    <td class="py-1 text-gray-600 dark:text-zinc-400">Impedance</td>
                    <td class="py-1 text-right font-medium">{{ $impedance }}</td>
                </tr>
                <tr>
                    <td class="py-1 text-gray-600 dark:text-zinc-400">Power Rating</td>
                    <td class="py-1 text-right font-medium">{{ $power }}</td>
                </tr>
            </table>
        <!-- Footer with Add to Cart and Enter Room buttons -->
        <div class="">
            <livewire:add-to-cart-button :designId="$id"></livewire:add-to-cart-button>
            <x-button href="designs/design/{{$id}}" tag="a">Enter Room</x-button>
Speaker-Design-Hub
        </div>

        <!-- Buttons (20% of content height) -->
        <div class="h-1/5 flex justify-center space-x-4 items-center">
            <x-button class="w-32">Add To Cart</x-button>
            <x-button href="designs/design/{{$id}}" tag="a" class="w-32">Enter Room</x-button>
        </div>
    </div>
</div>
