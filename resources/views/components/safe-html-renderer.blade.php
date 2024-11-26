{{-- resources/views/components/safe-html-renderer.blade.php --}}
<div {{ $attributes->merge(['class' => 'safe-html-content']) }}>
    {!! $slot !!}
    {{ $content }}
</div>
