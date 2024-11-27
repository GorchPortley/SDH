@props([
    'id' => '',
    'brand' => '',
    'model' => '',
    'tag'=>'',
    'card_image'=>'',
    'category'=>'',
    'price'=> '',
    'impedance' => '',
    'power' => ''
])

<div class="bg-white dark:bg-zinc-800 dark:text-white border border-gray-200 dark:border-zinc-700 rounded-lg hover:shadow-lg overflow-hidden flex flex-col">
    <!-- Image Section (Fixed 200x200) -->
    <div class="w-200px h-200px">
        <a href="designs/design/{{$id}}">
            <img src="{{$appUrl = config('app.url')}}/storage/{{$card_image}}"
                 class="w-200px h-200px object-cover">
        </a>
    </div>
    <!-- Content Section -->
    <div class="flex flex-col p-2 space-y-4">
        <!-- Rest of the content remains the same -->
        <!-- Title and Basic Info -->
        <div class="text-center">
            <h5 class="text-lg font-bold mb-1">
                <a href="/designs/design/{{ $id }}" class="hover:text-purple-600 dark:hover:text-purple-400 truncate block">{{$model}}<p class="text-gray-600 dark:text-zinc-400 text-md">{{ $brand }}</p></a>
            </h5>
            <p class="text-gray-600 dark:text-zinc-400 italic text-sm">{{ $tag }}</p>
        </div>
        <!-- Pricing -->
        <table>
            <tbody>
            <tr class="border-t">
                <tr>
                <th scope="row">Driver Price</th>
                <td>${{ $price }}</td>
            </tr>
            <tr>
                <th scope="row">Impedance</th>
                <td>{{ $impedance }}ohm</td>
            </tr>
            <tr>
                <th scope="row">Power Rating</th>
                <td>{{ $power }}W</td>
            </tr>
            </tbody>
        </table>
        <!-- Buttons -->
        <div class="flex justify-center space-x-4 m-2">
            <x-button href="designs/design/{{$id}}" tag="a">Enter Room</x-button>
        </div>
    </div>
</div>
