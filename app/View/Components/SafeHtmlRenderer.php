<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\HtmlString;
use DOMDocument;
use DOMXPath;

class SafeHtmlRenderer extends Component
{
    protected $allowedTags = [
        'p', 'div', 'span', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'strong', 'em', 'u', 'strike', 'br', 'hr',
        'ul', 'ol', 'li',
        'table', 'thead', 'tbody', 'tr', 'th', 'td',
        'img', 'a',
        'blockquote', 'pre', 'code',
        'section', 'article', 'header', 'footer'
    ];

    protected $allowedStyles = [
        // Layout
        'display', 'position', 'top', 'right', 'bottom', 'left',
        'float', 'clear', 'visibility', 'opacity',
        'z-index', 'overflow', 'clip',

        // Box model
        'margin', 'margin-top', 'margin-right', 'margin-bottom', 'margin-left',
        'padding', 'padding-top', 'padding-right', 'padding-bottom', 'padding-left',
        'height', 'width', 'max-height', 'max-width', 'min-height', 'min-width',

        // Visual formatting
        'background', 'background-color', 'background-image', 'background-repeat',
        'background-attachment', 'background-position', 'background-size',
        'border', 'border-top', 'border-right', 'border-bottom', 'border-left',
        'border-width', 'border-color', 'border-style', 'border-radius',
        'box-shadow', 'outline',

        // Typography
        'color', 'font', 'font-family', 'font-size', 'font-weight', 'font-style',
        'text-align', 'text-decoration', 'text-transform', 'text-indent',
        'line-height', 'letter-spacing', 'word-spacing', 'white-space',

        // Flexbox
        'flex', 'flex-basis', 'flex-direction', 'flex-flow', 'flex-grow',
        'flex-shrink', 'flex-wrap', 'justify-content', 'align-items',
        'align-content', 'align-self', 'order',

        // Grid
        'grid', 'grid-template-columns', 'grid-template-rows', 'grid-column',
        'grid-row', 'grid-area', 'grid-gap',

        // Transforms
        'transform', 'transition',

        // Tables
        'border-collapse', 'border-spacing', 'table-layout',

        // Lists
        'list-style', 'list-style-type', 'list-style-position', 'list-style-image'
    ];

    protected $allowedAttributes = [
        'class', 'id', 'style',
        'href' => ['a'],
        'src' => ['img'],
        'alt' => ['img'],
        'title',
        'target' => ['a'],
        'width' => ['img', 'table', 'th', 'td'],
        'height' => ['img'],
        'colspan' => ['th', 'td'],
        'rowspan' => ['th', 'td'],
        'data-*'  // Allow data attributes
    ];

    public function __construct(
        public string $content,
        public bool $preserveStyles = true
    ) {}

    protected function sanitizeStyle(string $style): string
    {
        $styles = explode(';', $style);
        $sanitizedStyles = [];

        foreach ($styles as $declaration) {
            $declaration = trim($declaration);
            if (empty($declaration)) continue;

            $parts = explode(':', $declaration, 2);
            if (count($parts) !== 2) continue;

            $property = trim($parts[0]);
            $value = trim($parts[1]);

            // Check if the property is in our allowed list
            if (in_array($property, $this->allowedStyles)) {
                // Additional checks for potentially dangerous values
                if (!preg_match('/javascript|expression|behavior|url\s*\(/i', $value)) {
                    $sanitizedStyles[] = $property . ': ' . $value;
                }
            }
        }

        return implode('; ', $sanitizedStyles);
    }

    protected function sanitizeHtml(string $html): string
    {
        // Preserve certain entities
        $html = str_replace(['&nbsp;', '&amp;', '&lt;', '&gt;', '&quot;', '&apos;'],
            ['⚡nbsp⚡', '⚡amp⚡', '⚡lt⚡', '⚡gt⚡', '⚡quot⚡', '⚡apos⚡'],
            $html);

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'),
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOERROR);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $nodes = $xpath->query('//*');

        foreach ($nodes as $node) {
            if (!in_array(strtolower($node->nodeName), $this->allowedTags)) {
                $node->parentNode->removeChild($node);
                continue;
            }

            // Store allowed attributes temporarily
            $preservedAttrs = [];
            foreach ($node->attributes as $attr) {
                $attrName = strtolower($attr->name);
                $attrValue = $attr->value;

                // Handle style attributes
                if ($attrName === 'style' && $this->preserveStyles) {
                    $attrValue = $this->sanitizeStyle($attrValue);
                    if (!empty($attrValue)) {
                        $preservedAttrs['style'] = $attrValue;
                    }
                }
                // Handle data attributes
                elseif (strpos($attrName, 'data-') === 0) {
                    $preservedAttrs[$attrName] = $attrValue;
                }
                // Handle other allowed attributes
                elseif (isset($this->allowedAttributes[$attrName])) {
                    if (is_array($this->allowedAttributes[$attrName])) {
                        if (in_array($node->nodeName, $this->allowedAttributes[$attrName])) {
                            $preservedAttrs[$attrName] = $attrValue;
                        }
                    } else {
                        $preservedAttrs[$attrName] = $attrValue;
                    }
                }
            }

            // Remove all existing attributes
            while ($node->attributes->length > 0) {
                $node->removeAttribute($node->attributes->item(0)->name);
            }

            // Restore preserved attributes
            foreach ($preservedAttrs as $name => $value) {
                $node->setAttribute($name, $value);
            }
        }

        $html = $dom->saveHTML();

        // Restore preserved entities
        $html = str_replace(['⚡nbsp⚡', '⚡amp⚡', '⚡lt⚡', '⚡gt⚡', '⚡quot⚡', '⚡apos⚡'],
            ['&nbsp;', '&amp;', '&lt;', '&gt;', '&quot;', '&apos;'],
            $html);

        return $html;
    }

    public function render()
    {
        $sanitizedHtml = $this->sanitizeHtml($this->content);
        return new HtmlString($sanitizedHtml);
    }
}
