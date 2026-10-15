<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Kinetica Authentication') ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/auth.css">
</head>
<body class="auth-body">
    <div class="auth-ambient glow-cyan"></div>
    <div class="auth-ambient glow-lime"></div>

    <div class="auth-wrapper">
        <header class="auth-header">
            <?php $backLang = ($_SESSION['lang'] ?? 'en') === 'pl' ? 'pl' : 'en'; ?>
            <a href="/<?= $backLang ?>" class="brand-logo" aria-label="Back to Kinetica Home">
                <span class="brand-icon">⚡</span>
                <span class="brand-text">KINETICA<span class="brand-dot">.</span></span>
                <span class="brand-location">WARSAW</span>
            </a>
            <a href="/<?= $backLang ?>" class="back-home-link">← Return to Club Website</a>
        </header>

        <main class="auth-main-card">
            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <span class="alert-icon">⚠️</span>
                    <span><?= e($error) ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <span class="alert-icon">✓</span>
                    <span><?= e($success) ?></span>
                </div>
            <?php endif; ?>

            <?= $content ?>
        </main>

        <footer class="auth-footer">
            <p>© <?= date('Y') ?> Kinetica Biomechanics Club Sp. z o.o. • Warsaw, Złota 44</p>
        </footer>
    </div>
</body>
</html>
