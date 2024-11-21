@props([
    'id' => null,
    'width' => 'md',
    'slideOver' => false,
])

@php
    $widthClass = match($width) {
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        'full' => 'max-w-full',
        default => 'max-w-md'
    };
@endphp

<div
    x-data="{ open: false }"
    x-on:open-modal.window="if ($event.detail === '{{ $id }}') open = true"
    x-on:close-modal.window="if ($event.detail === '{{ $id }}') open = false"
    x-on:keydown.escape.window="open = false"
    {{ $attributes }}
>
    {{-- Trigger Button --}}
    <div x-on:click="open = true">
        {{ $trigger }}
    </div>

    {{-- Modal Backdrop --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 bg-gray-500/75 dark:bg-gray-900/75"
        @click="open = false"
        x-cloak
    ></div>

    {{-- Modal/Slide-over Panel --}}
    <div
        x-show="open"
        class="fixed inset-0 z-50 overflow-y-auto"
        x-cloak
    >
        <div class="min-h-screen px-4 flex items-center justify-center">
            <div
                x-show="open"
                x-trap.inert.noscroll="open"
                @click.away="open = false"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="{{ $slideOver ? 'translate-x-full' : 'translate-y-4 opacity-0 scale-95' }}"
                x-transition:enter-end="{{ $slideOver ? 'translate-x-0' : 'translate-y-0 opacity-100 scale-100' }}"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="{{ $slideOver ? 'translate-x-0' : 'translate-y-0 opacity-100 scale-100' }}"
                x-transition:leave-end="{{ $slideOver ? 'translate-x-full' : 'translate-y-4 opacity-0 scale-95' }}"
                class="{{ $widthClass }} w-full {{ $slideOver ? 'fixed right-0 inset-y-0' : 'relative' }} bg-white dark:bg-gray-800 rounded-lg shadow-xl"
            >
                {{-- Close Button --}}
                <button
                    @click="open = false"
                    class="absolute right-4 top-4 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300"
                >
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                {{-- Header --}}
                @if (isset($header))
                    <div class="px-2 py-6 sm:px-6 border-b border-gray-200 dark:border-gray-700 bg-white">
                        {{ $header }}
                    </div>
                @endif

                {{-- Content --}}
                <div class="px-4 py-6 sm:px-6">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
