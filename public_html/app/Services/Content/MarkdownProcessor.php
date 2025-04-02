<?php
declare(strict_types=1);

namespace App\Services\Content;

use League\CommonMark\GithubFlavoredMarkdownConverter;

class MarkdownProcessor {
    private GithubFlavoredMarkdownConverter $converter;

    public function __construct() {
        $this->converter = new GithubFlavoredMarkdownConverter([
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
        ]);
    }

    public function toHtml(string $markdown): string {
        return $this->converter->convert($markdown);
    }

    public function sanitize(string $html): string {
        $purifier = new \HTMLPurifier();
        return $purifier->purify($html);
    }

    public function extractMentions(string $text): array {
        preg_match_all('/@([a-zA-Z0-9_]+)/', $text, $matches);
        return array_unique($matches[1]);
    }
}