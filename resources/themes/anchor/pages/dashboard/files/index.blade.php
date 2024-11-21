<?php

use function Laravel\Folio\{middleware, name};

middleware('auth');
name('dashboard.files');
?>

<x-layouts.app>
    @volt('files')
    <iframe src="/laravel-filemanager" class="rounded-lg" style="width: 100%; height: 875px; overflow: hidden;"></iframe>
    @endvolt
</x-layouts.app>
