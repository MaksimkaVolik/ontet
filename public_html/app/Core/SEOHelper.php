<?php
namespace Core;

class SEOHelper {
    public static function generateMetaTags($title, $description, $url, $image = null) {
        $tags = [
            '<title>' . htmlspecialchars($title) . '</title>',
            '<meta name="description" content="' . htmlspecialchars($description) . '">',
            
            // Open Graph
            '<meta property="og:title" content="' . htmlspecialchars($title) . '">',
            '<meta property="og:description" content="' . htmlspecialchars($description) . '">',
            '<meta property="og:url" content="' . htmlspecialchars($url) . '">',
            '<meta property="og:type" content="website">',
            
            // Twitter Card
            '<meta name="twitter:card" content="summary">',
            '<meta name="twitter:title" content="' . htmlspecialchars($title) . '">',
            '<meta name="twitter:description" content="' . htmlspecialchars($description) . '">'
        ];

        if ($image) {
            $tags[] = '<meta property="og:image" content="' . htmlspecialchars($image) . '">';
            $tags[] = '<meta name="twitter:image" content="' . htmlspecialchars($image) . '">';
        }

        return implode("\n", $tags);
    }
}