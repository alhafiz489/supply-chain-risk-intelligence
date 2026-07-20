<?php

namespace App\Http\Middleware;

use App\Services\AutomaticTranslationService;
use Closure;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TranslateUserWeb
{
    public function __construct(
        private readonly AutomaticTranslationService $translation
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $targetLocale = strtolower((string) $request->session()->get(
            'translation_target_locale',
            $request->session()->get('locale', 'en')
        ));

        if (
            ! config('services.translation.enabled', false)
            || in_array($targetLocale, ['en', 'id'], true)
            || $request->is('admin/*')
            || ! str_contains((string) $response->headers->get('Content-Type'), 'text/html')
        ) {
            return $response;
        }

        $html = $response->getContent();

        if (! is_string($html) || trim($html) === '') {
            return $response;
        }

        $translated = $this->translateHtml($html, $targetLocale);
        $response->setContent($translated);
        $response->headers->set('Content-Length', (string) strlen($translated));

        return $response;
    }

    private function translateHtml(string $html, string $targetLocale): string
    {
        $protectedBlocks = [];
        $html = preg_replace_callback(
            '/<(script|style|pre|code|svg)\b[^>]*>.*?<\/\1>/isu',
            function (array $match) use (&$protectedBlocks): string {
                $token = 'TRANSLATION-PROTECTED-'.count($protectedBlocks);
                $protectedBlocks[$token] = $match[0];

                return '<!--'.$token.'-->';
            },
            $html
        ) ?? $html;

        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML(
            '<?xml encoding="UTF-8">'.$html,
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $xpath = new DOMXPath($document);
        $htmlElement = $document->getElementsByTagName('html')->item(0);

        if ($htmlElement instanceof DOMElement) {
            $htmlElement->setAttribute('lang', $targetLocale);
            $htmlElement->setAttribute(
                'dir',
                session('selected_text_direction', 'ltr') === 'rtl' ? 'rtl' : 'ltr'
            );
        }

        $nodes = $xpath->query(
            '//text()[normalize-space() and not(ancestor::script) and not(ancestor::style)'
            .' and not(ancestor::code) and not(ancestor::pre) and not(ancestor::svg)'
            .' and not(ancestor::*[@translate="no"])]'
        );

        $elements = $xpath->query('//*[@placeholder or @title or @aria-label]');
        $textNodes = [];
        $attributes = [];
        $sourceTexts = [];

        if ($nodes !== false) {
            foreach (iterator_to_array($nodes) as $node) {
                if ($node instanceof DOMNode && $this->isTranslatable($node->nodeValue ?? '')) {
                    $textNodes[] = $node;
                    $sourceTexts[] = trim($node->nodeValue ?? '');
                }
            }
        }

        if ($elements !== false) {
            foreach ($elements as $element) {
                if (! $element instanceof DOMElement || $element->getAttribute('translate') === 'no') {
                    continue;
                }

                foreach (['placeholder', 'title', 'aria-label'] as $attribute) {
                    $value = $element->getAttribute($attribute);

                    if ($value !== '' && $this->isTranslatable($value)) {
                        $attributes[] = [$element, $attribute, $value];
                        $sourceTexts[] = $value;
                    }
                }
            }
        }

        $translations = $this->translation->translateMany(
            $sourceTexts,
            $targetLocale,
            'en'
        );

        foreach ($textNodes as $node) {
            $source = trim($node->nodeValue ?? '');
            $node->nodeValue = $this->replacePreservingWhitespace(
                $node->nodeValue ?? '',
                $translations[$source] ?? $source
            );
        }

        foreach ($attributes as [$element, $attribute, $value]) {
            $element->setAttribute($attribute, $translations[$value] ?? $value);
        }

        $result = $document->saveHTML();

        if (! is_string($result)) {
            return $html;
        }

        foreach ($protectedBlocks as $token => $block) {
            $result = str_replace('<!--'.$token.'-->', $block, $result);
        }

        return preg_replace('/<\?xml encoding="UTF-8"\?>/', '', $result) ?: $html;
    }

    private function isTranslatable(string $text): bool
    {
        $text = trim($text);

        return mb_strlen($text) >= 2
            && preg_match('/\p{L}/u', $text) === 1
            && ! preg_match('/^(https?:\/\/|[\w.+-]+@[\w.-]+$)/iu', $text);
    }

    private function replacePreservingWhitespace(string $text, string $translation): string
    {
        preg_match('/^\s*/u', $text, $leading);
        preg_match('/\s*$/u', $text, $trailing);

        return ($leading[0] ?? '')
            .$translation
            .($trailing[0] ?? '');
    }
}
