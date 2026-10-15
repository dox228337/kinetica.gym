<!DOCTYPE html>
<html lang="<?= e($_SESSION['lang'] ?? 'en') ?>" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= e($title ?? 'Checkout | Kinetica Warsaw') ?></title>

    <!-- Google Fonts: Outfit & Inter & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

    <!-- Core Stylesheet -->
    <link rel="stylesheet" href="/assets/css/main.css">
    <style>
        .checkout-layout-body {
            background-color: var(--bg-base, #0a0c10);
            color: var(--text-primary, #f1f5f9);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow-x: hidden;
        }

        .checkout-top-nav {
            border-bottom: 1px solid var(--border-subtle, rgba(255, 255, 255, 0.08));
            background: rgba(10, 12, 16, 0.85);
            backdrop-filter: blur(16px);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .checkout-nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 72px;
        }

        .checkout-nav-left, .checkout-nav-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .security-badge-header {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-family: var(--font-mono, monospace);
            font-size: 0.75rem;
            color: #4ade80;
            background: rgba(74, 222, 128, 0.08);
            border: 1px solid rgba(74, 222, 128, 0.2);
            padding: 5px 12px;
            border-radius: 999px;
            letter-spacing: 0.03em;
        }

        .return-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--text-secondary, #94a3b8);
            font-size: 0.88rem;
            font-weight: 500;
            transition: color var(--transition-fast, 0.2s);
        }

        .return-link:hover {
            color: #fff;
        }

        .checkout-main-content {
            flex-grow: 1;
            padding: 40px 0 60px;
        }

        .checkout-footer {
            border-top: 1px solid var(--border-subtle, rgba(255, 255, 255, 0.08));
            padding: 24px 0;
            text-align: center;
            font-size: 0.82rem;
            color: var(--text-muted, #64748b);
        }

        @media (max-width: 640px) {
            .security-badge-header span.badge-long-text {
                display: none;
            }
        }
    </style>
</head>
<body class="checkout-layout-body">
    <?php 
    $lang = $_SESSION['lang'] ?? 'en'; 
    $homeUrl = '/' . ($lang === 'pl' ? 'pl' : 'en');
    ?>

    <!-- Minimal, High-Trust Checkout Header -->
    <header class="checkout-top-nav">
        <div class="container checkout-nav-inner">
            <div class="checkout-nav-left">
                <a href="<?= $homeUrl ?>" class="brand-logo" aria-label="Kinetica Home">
                    <span class="brand-icon">⚡</span>
                    <span class="brand-text">KINETICA<span class="brand-dot">.</span></span>
                    <span class="brand-location">WARSAW</span>
                </a>
            </div>

            <div class="checkout-nav-right">
                <div class="security-badge-header">
                    <span>🔒</span>
                    <span class="badge-long-text">256-Bit SSL Encrypted Sandbox</span>
                </div>
                <a href="<?= $homeUrl ?>#pricing" class="return-link">
                    <span><?= $lang === 'pl' ? '← Wróć do oferty' : '← Back to Passes' ?></span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Checkout View -->
    <main class="checkout-main-content">
        <?= $content ?>
    </main>

    <!-- Clean Trust Footer -->
    <footer class="checkout-footer">
        <div class="container">
            <p>© <?= date('Y') ?> Kinetica Biomechanics Club Sp. z o.o. • Warsaw, Złota 44 • Biometric NFC Turnstiles</p>
            <p style="margin-top: 4px; font-size: 0.75rem; opacity: 0.7;">
                <?= $lang === 'pl' ? 'Symulator płatności dla celów demonstracyjnych (Sandbox Mode) • Brak rzeczywistego obciążenia karty' : 'Simulated payment sandbox for demo purposes • No actual card charges applied' ?>
            </p>
        </div>
    </footer>
</body>
</html>
