<?php

namespace App\Services\Admin\Content;

use DOMDocument;
use DOMElement;
use DOMNode;

class SanitizesRichText
{
    /**
     * @var array<string, array<int, string>>
     */
    private array $allowed = [
        'a' => ['href', 'title', 'target', 'rel'],
        'blockquote' => [],
        'br' => [],
        'code' => [],
        'em' => [],
        'h2' => [],
        'h3' => [],
        'h4' => [],
        'li' => [],
        'ol' => [],
        'p' => [],
        'pre' => [],
        'strong' => [],
        'ul' => [],
    ];

    public function sanitize(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return $html;
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML(
            '<!doctype html><html><body>'.$html.'</body></html>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD,
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $body = $document->getElementsByTagName('body')->item(0);

        if (! $body instanceof DOMNode) {
            return strip_tags($html);
        }

        $this->sanitizeNode($body);

        $output = '';

        foreach ($body->childNodes as $child) {
            $output .= $document->saveHTML($child);
        }

        return trim($output);
    }

    private function sanitizeNode(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if (! $child instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($child->tagName);

            if (! array_key_exists($tag, $this->allowed)) {
                $this->unwrap($child);

                continue;
            }

            foreach (iterator_to_array($child->attributes) as $attribute) {
                $name = strtolower($attribute->name);
                $value = trim($attribute->value);

                if (! in_array($name, $this->allowed[$tag], true) || $this->hasUnsafeValue($name, $value)) {
                    $child->removeAttribute($attribute->name);
                }
            }

            if ($tag === 'a') {
                $child->setAttribute('rel', 'nofollow noopener noreferrer');
            }

            $this->sanitizeNode($child);
        }
    }

    private function unwrap(DOMElement $element): void
    {
        $parent = $element->parentNode;

        if (! $parent instanceof DOMNode) {
            $element->remove();

            return;
        }

        while ($element->firstChild) {
            $parent->insertBefore($element->firstChild, $element);
        }

        $parent->removeChild($element);
    }

    private function hasUnsafeValue(string $name, string $value): bool
    {
        if (str_starts_with($name, 'on')) {
            return true;
        }

        if (! in_array($name, ['href'], true)) {
            return false;
        }

        return preg_match('/^\s*(javascript|data|vbscript):/i', $value) === 1;
    }
}
