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

<div class="flex border-2">
    <div class="">
        <div id="" class="">
            <div class="">
                <div class="">
                    <img src="{{ $card_image }}">
                </div>
            </div>
        </div>

        <div class="">
            <h5 class="">
                <a href="/designs/design/{{ $id }}" class="">{{ $name }}</a>
            </h5>
            <p class="">Designed By:</p>
            <p>{{ $tag }}</p>
            <p>Design Price: ${{ $price }}</p>
            <p>Build Cost: ${{ $build_cost }}</p>
        </div>

        <!-- Footer with Add to Cart and Enter Room buttons -->
        <div class="">
            <livewire:add-to-cart-button :designId="$id"></livewire:add-to-cart-button>
            <x-button href="designs/design/{{$id}}" tag="a">Enter Room</x-button>
        </div>

        <!-- Collapsible section with hidden overlay -->
        <div class="">
            <div class="">
                <table class="">
                    <tr>
                        <th>Spec</th>
                        <th>Value</th>
                    </tr>
                    <tr>
                        <td>Design Type</td>
                        <td>{{ $category }}</td>
                    </tr>
                    <tr>
                        <td>Impedance</td>
                        <td>{{ $impedance }}</td>
                    </tr>
                    <tr>
                        <td>Power Rating</td>
                        <td>{{ $power }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
