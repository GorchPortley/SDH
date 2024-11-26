<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\HtmlString;

class SafeHtmlRenderer extends Component
{
    public function __construct(
        public string $content
    ) {}

    public function render()
    {
        // Wrap content in a full HTML document if it's not already
        $fullHtml = $this->content;
        if (!str_contains($this->content, '<html')) {
            $fullHtml = "
                <!DOCTYPE html>
                <html>
                <head>
                    <base target='_parent'>
                    <style>
                        body { margin: 0; }
                        img { max-width: 100%; height: auto; }
                    </style>
                </head>
                <body>
                    {$this->content}
                </body>
                </html>
            ";
        }

        // Create a sandboxed iframe with style permissions
        $html = '<iframe
            srcdoc="' . htmlspecialchars($fullHtml, ENT_QUOTES) . '"
            sandbox="allow-same-origin allow-scripts allow-modals allow-popups allow-forms"
            style="width: 100%; border: none; min-height: 100px; overflow: hidden;"
            onload="this.style.height = this.contentDocument.documentElement.scrollHeight + \'px\';"
            scrolling="no"
        ></iframe>';

        return new HtmlString($html);
    }
}
