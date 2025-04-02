<!DOCTYPE html>
<html lang="ru">
<head>
    <title><?= htmlspecialchars($thread['title'], ENT_QUOTES) ?></title>
    <meta property="og:title" content="<?= htmlspecialchars($thread['title'], ENT_QUOTES) ?>">
    <meta property="og:description" content="<?= htmlspecialchars(substr(strip_tags($thread['content']), 0, 200), ENT_QUOTES) ?>">
</head>
<body>
    <article>
        <h1><?= htmlspecialchars($thread['title'], ENT_QUOTES) ?></h1>
        <div class="content"><?= htmlspecialchars($thread['content'], ENT_QUOTES) ?></div>
        
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <div class="author">
                    <?= htmlspecialchars($post['username'], ENT_QUOTES) ?>
                </div>
                <div class="content"><?= htmlspecialchars($post['content'], ENT_QUOTES) ?></div>
            </div>
        <?php endforeach; ?>
    </article>
</body>
</html>