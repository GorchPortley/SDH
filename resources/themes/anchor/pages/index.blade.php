<?php
    use function Laravel\Folio\{name};
    name('home');
?>

<x-layouts.marketing
    :seo="[
        'title'         => setting('site.title', 'sdLabs.cc'),
        'description'   => setting('site.description', 'Speaker Design Marketplace'),
        'image'         => url('/og_image.png'),
        'type'          => 'website'
    ]"
>

        <x-marketing.sections.hero />

</x-layouts.marketing>
