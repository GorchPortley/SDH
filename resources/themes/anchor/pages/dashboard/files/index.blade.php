<?php

use function Laravel\Folio\{middleware, name};

middleware('auth');
name('dashboard.files');
?>

<x-layouts.app>
    @volt('files')
    <x-app.container>
    <x-app.heading
        title="File Management"
        description="Manage your files here, this is your personal folder containing all the documents uploaded through the create/edit dialogs">
    </x-app.heading>

    <iframe src="/laravel-filemanager" class="rounded-lg" style="width: 100%; height: 800px; overflow: hidden;"></iframe>

    </x-app.container>
    @endvolt
</x-layouts.app>
