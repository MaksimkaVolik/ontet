<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <meta name="description" content="<?= htmlspecialchars($description) ?>">
    <link href="/assets/css/main.css" rel="stylesheet">
</head>
<body>
    <?php include '../partials/header.php'; ?>
    
    <main class="container">
        <h1>Добро пожаловать на форум</h1>
        <!-- Контент главной страницы -->
    </main>
    
    <?php include '../partials/footer.php'; ?>
</body>
</html>