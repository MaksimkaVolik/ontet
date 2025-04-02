<?php
header('Content-Type: application/xml');
require __DIR__ . '/../app/bootstrap.php';

$db = new Core\Database('localhost', 'mvolikfg_2', 'Mvolik683', 'mvolikfg_forum');

// Получаем данные для карты сайта
$threads = $db->query("SELECT slug, updated_at FROM threads ORDER BY updated_at DESC LIMIT 1000")->fetchAll();
$categories = $db->query("SELECT slug FROM categories")->fetchAll();

echo '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

// Главная страница
echo '<url>
    <loc>https://otvetforum.ru/</loc>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
</url>';

// Категории
foreach ($categories as $category) {
    echo '<url>
        <loc>https://otvetforum.ru/forum/' . htmlspecialchars($category['slug']) . '</loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>';
}

// Темы
foreach ($threads as $thread) {
    echo '<url>
        <loc>https://otvetforum.ru/thread/' . htmlspecialchars($thread['slug']) . '</loc>
        <lastmod>' . date('Y-m-d', strtotime($thread['updated_at'])) . '</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>';
}

echo '</urlset>';