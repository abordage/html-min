<?php

declare(strict_types=1);

namespace Abordage\HtmlMin;

class HtmlMin
{
    protected bool $findDoctypeInDocument = true;
    protected bool $removeHtmlComments = true;
    protected bool $removeBlankLinesInScriptElements = false;
    protected bool $removeWhitespaceBetweenTags = true;
    protected bool $removeTrailingSlashes = false;

    /**
     * Void elements that should not have trailing slashes in HTML5.
     *
     * @see https://html.spec.whatwg.org/multipage/syntax.html#void-elements
     */
    protected const VOID_ELEMENTS = [
        'area',
        'base',
        'br',
        'col',
        'embed',
        'hr',
        'img',
        'input',
        'link',
        'meta',
        'source',
        'track',
        'wbr',
    ];

    /** @var array<string> */
    protected array $ignoreElements = [
        'pre',
        'textarea',
        'script',
    ];

    /** @var array<string, string> */
    protected array $skippedElements = [];

    /**
     * Enable or disable DOCTYPE detection in a document.
     * If enabled and DOCTYPE is not found, minification will be skipped.
     */
    public function findDoctypeInDocument(bool $enable = true): self
    {
        $this->findDoctypeInDocument = $enable;

        return $this;
    }

    /**
     * Enable or disable removing blank lines in script elements.
     */
    public function removeBlankLinesInScriptElements(bool $enable = true): self
    {
        $this->removeBlankLinesInScriptElements = $enable;

        return $this;
    }

    /**
     * Enable or disable removing whitespace between tags.
     */
    public function removeWhitespaceBetweenTags(bool $enable = true): self
    {
        $this->removeWhitespaceBetweenTags = $enable;

        return $this;
    }

    /**
     * Enable or disable removing trailing slashes from void elements.
     * Converts XHTML-style <tag /> to HTML5-style <tag>.
     */
    public function removeTrailingSlashes(bool $enable = true): self
    {
        $this->removeTrailingSlashes = $enable;

        return $this;
    }

    /**
     * Minify HTML string.
     */
    public function minify(string $html): string
    {
        if ($this->findDoctypeInDocument && $this->doctypeNotFound($html)) {
            return $html;
        }

        if ($this->removeHtmlComments) {
            $html = $this->removeHtmlComments($html);
        }

        if ($this->removeBlankLinesInScriptElements) {
            $html = $this->trimScriptElements($html);
        }

        $html = $this->collapseWhitespaces($html);

        if ($this->removeTrailingSlashes) {
            $html = $this->removeVoidElementSlashes($html);
        }

        return trim($html);
    }

    protected function doctypeNotFound(string $html): bool
    {
        $string = substr(trim($html), 0, 100);

        return stripos($string, '<!DOCTYPE') === false;
    }

    protected function removeHtmlComments(string $html): string
    {
        // Remove HTML comments except Livewire and Knockout.js markers
        return (string) preg_replace('~<!--[^]><!\[](?!Livewire|ko |/ko)(.*?)[^]]-->~s', '', $html);
    }

    protected function trimScriptElements(string $html): string
    {
        if (preg_match_all('~<script[^>]*>(.*)</script>~Uuis', $html, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $replace = trim((string) preg_replace('~^\p{Z}+|\p{Z}+$|^\s+~m', '', $match[1]));
                $html = str_replace($match[1], $replace, $html);
            }
        }

        return $html;
    }

    protected function collapseWhitespaces(string $html): string
    {
        $html = $this->hideElements($html);

        $whitespaceCollapses = [
            '~\s+~u' => ' ',
        ];

        if ($this->removeWhitespaceBetweenTags) {
            $whitespaceCollapses += [
                '~> +<~' => '><',
                '~(<[a-z]+[^>]*>) +~i' => '$1',
                '~ +(</[a-z]+)~i' => '$1',
            ];
        }

        $html = (string) preg_replace(array_keys($whitespaceCollapses), array_values($whitespaceCollapses), $html);

        return $this->restoreElements($html);
    }

    protected function hideElements(string $html): string
    {
        foreach ($this->ignoreElements as $element) {
            $pattern = '~<' . $element . '[^>]*>(.*)</' . $element . '>~Uuis';
            if (preg_match_all($pattern, $html, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $this->skippedElements['#' . md5($match[1]) . '#'] = $match[1];
                }
            }
        }

        if ($this->skippedElements !== []) {
            $html = str_replace(array_values($this->skippedElements), array_keys($this->skippedElements), $html);
        }

        return $html;
    }

    protected function restoreElements(string $html): string
    {
        if ($this->skippedElements !== []) {
            return str_replace(array_keys($this->skippedElements), array_values($this->skippedElements), $html);
        }

        return $html;
    }

    protected function removeVoidElementSlashes(string $html): string
    {
        $pattern = '/<(' . implode('|', self::VOID_ELEMENTS) . ')([^>]*?)\s*\/>/i';

        return (string) preg_replace($pattern, '<$1$2>', $html);
    }
}
