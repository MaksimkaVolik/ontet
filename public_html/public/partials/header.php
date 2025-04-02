<header class="header">
    <nav class="navbar">
        <a href="/" class="logo">OtvetForum</a>
        <form action="/search" method="get" class="search-form">
            <input type="text" name="q" placeholder="Поиск...">
            <button type="submit">Найти</button>
        </form>
        <div class="auth-buttons">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/profile" class="btn">Профиль</a>
                <a href="/logout" class="btn">Выйти</a>
            <?php else: ?>
                <a href="/login" class="btn">Войти</a>
                <a href="/register" class="btn">Регистрация</a>
            <?php endif; ?>
        </div>
    </nav>
</header>