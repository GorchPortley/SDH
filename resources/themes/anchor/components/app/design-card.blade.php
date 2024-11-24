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
<div class="bg-white dark:bg-zinc-800 dark:text-white border border-gray-200 dark:border-zinc-700 rounded-lg hover:shadow-lg overflow-hidden flex flex-col">
    <!-- Image Section (Fixed 200x200) -->
    <div class="w-200px h-200px">
        <a href="designs/design/{{$id}}">
            <!--change src to appropriate url --><img src="https://cong.test/storage/{{ $card_image }}"
                 class="w-200px h-200px object-cover">
        </a>
    </div>
    <!-- Content Section -->
    <div class="flex flex-col p-2 space-y-4">
        <!-- Rest of the content remains the same -->
        <!-- Title and Basic Info -->
        <div class="text-center">
            <h5 class="text-lg font-bold mb-1">
                <a href="/designs/design/{{ $id }}" class="hover:text-purple-600 dark:hover:text-purple-400 truncate block">{{ $name }}</a>
            </h5>
            <p class="text-gray-600 dark:text-zinc-400 italic text-sm">{{ $tag }}</p>
        </div>
        <!-- Pricing -->
        <table>
            <tbody>
            <tr class="border-t">
                <th scope="row">Design Price</th>
                <td>${{ $price }}</td>
            </tr>
            <tr>
                <th scope="row">Build Cost</th>
                <td>${{ $build_cost }}</td>
            </tr>
            </tbody>
        </table>
        <!-- Buttons -->
        <div class="flex justify-center space-x-4 m-2">
            <livewire:add-to-cart-button :design-id="$id" />
            <x-button href="designs/design/{{$id}}" tag="a">Enter Room</x-button>
        </div>
        <!-- Specs Table -->
        <div class="flex-grow">
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
        </div>
    </div>
</div>
