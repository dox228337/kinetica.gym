<!DOCTYPE html>
<html lang="<?= e($lang ?? 'en') ?>" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    
    <!-- Primary SEO Meta Tags -->
    <title><?= e($meta['title'] ?? 'Kinetica Gym Warsaw') ?></title>
    <meta name="title" content="<?= e($meta['title'] ?? '') ?>">
    <meta name="description" content="<?= e($meta['description'] ?? '') ?>">
    <meta name="keywords" content="<?= e($meta['keywords'] ?? '') ?>">
    <meta name="author" content="Kinetica Biomechanics Club Warsaw">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

    <!-- Multilingual Alternate & Canonical SEO Links -->
    <link rel="canonical" href="https://kinetica.gym<?= e($current_url ?? '/en') ?>">
    <link rel="alternate" hreflang="en" href="https://kinetica.gym/en">
    <link rel="alternate" hreflang="pl" href="https://kinetica.gym/pl">
    <link rel="alternate" hreflang="x-default" href="https://kinetica.gym/en">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://kinetica.gym<?= e($current_url ?? '/en') ?>">
    <meta property="og:title" content="<?= e($meta['title'] ?? '') ?>">
    <meta property="og:description" content="<?= e($meta['description'] ?? '') ?>">
    <meta property="og:image" content="https://kinetica.gym/assets/images/hero.jpg">
    <meta property="og:site_name" content="Kinetica Gym Warsaw">
    <meta property="og:locale" content="<?= ($lang ?? ($_SESSION['lang'] ?? 'en')) === 'pl' ? 'pl_PL' : 'en_US' ?>">

    <!-- Twitter Cards -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://kinetica.gym<?= e($current_url ?? '/en') ?>">
    <meta property="twitter:title" content="<?= e($meta['title'] ?? '') ?>">
    <meta property="twitter:description" content="<?= e($meta['description'] ?? '') ?>">
    <meta property="twitter:image" content="https://kinetica.gym/assets/images/hero.jpg">

    <!-- Schema.org JSON-LD Structured Data for Warsaw Local Business / Health Club -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "HealthClub",
      "name": "Kinetica Biomechanics Club",
      "image": "https://kinetica.gym/assets/images/hero.jpg",
      "@id": "https://kinetica.gym",
      "url": "https://kinetica.gym",
      "telephone": "+48228904420",
      "priceRange": "149 PLN - 499 PLN",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "ul. Złota 44",
        "addressLocality": "Warszawa",
        "postalCode": "00-120",
        "addressCountry": "PL"
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": 52.2319,
        "longitude": 21.0067
      },
      "openingHoursSpecification": [
        {
          "@type": "OpeningHoursSpecification",
          "dayOfWeek": [
            "Monday",
            "Tuesday",
            "Wednesday",
            "Thursday",
            "Friday",
            "Saturday",
            "Sunday"
          ],
          "opens": "00:00",
          "closes": "23:59"
        }
      ],
      "sameAs": [
        "https://instagram.com/kinetica.warsaw",
        "https://facebook.com/kinetica.warsaw"
      ]
    }
    </script>

    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

    <!-- Core Stylesheet -->
    <link rel="stylesheet" href="/assets/css/main.css">
<?php
$currentLang = $lang ?? ($_SESSION['lang'] ?? 'en');
$currentUrl = $current_url ?? ('/' . $currentLang);
$navAbout = $nav['about'] ?? ($currentLang === 'pl' ? 'O Klubie' : 'About');
$navCourses = $nav['courses'] ?? ($currentLang === 'pl' ? 'Darmowe Kursy' : 'Free Courses');
$navPricing = $nav['pricing'] ?? ($currentLang === 'pl' ? '4 Karnety' : 'Pricing');
$navPerks = $nav['perks'] ?? ($currentLang === 'pl' ? 'Dla Klubowiczów' : 'Perks');
$navZones = $nav['zones'] ?? ($currentLang === 'pl' ? 'Strefy Klubu' : 'Zones');
$navFaq = $nav['faq'] ?? 'FAQ';
$heroBadge = $hero['badge'] ?? ($currentLang === 'pl' ? '⚡ 24/7 Biomechanika i Biohacking w Warszawie' : '⚡ 24/7 Warsaw Biomechanics & Biohacking');
?>
<body data-lang="<?= e($currentLang) ?>">

    <!-- Top Urgency Banner -->
    <aside class="top-announcement-bar" role="region" aria-label="Announcement">
        <div class="container announcement-content">
            <span class="pulse-indicator"></span>
            <span class="announcement-text"><?= e($heroBadge) ?></span>
            <button type="button" class="announcement-cta open-free-modal-btn"><?= $currentLang === 'pl' ? 'Odbierz kursy' : 'Claim courses' ?> →</button>
        </div>
    </aside>

    <!-- Main Navigation Header -->
    <header class="site-header" id="header">
        <div class="container header-container">
            <!-- Brand Logo -->
            <a href="<?= e($currentUrl) ?>" class="brand-logo" aria-label="Kinetica Home">
                <span class="brand-icon">⚡</span>
                <span class="brand-text">KINETICA<span class="brand-dot">.</span></span>
                <span class="brand-location">WARSAW</span>
            </a>

            <!-- Desktop Nav Menu -->
            <nav class="desktop-nav" aria-label="Main Navigation">
                <ul class="nav-list">
                    <li><a href="#about" class="nav-link"><?= e($navAbout) ?></a></li>
                    <li><a href="#free-courses" class="nav-link highlight-nav"><?= e($navCourses) ?> <span class="badge-free">FREE</span></a></li>
                    <li><a href="#pricing" class="nav-link"><?= e($navPricing) ?></a></li>
                    <li><a href="#perks" class="nav-link"><?= e($navPerks) ?></a></li>
                    <li><a href="#zones" class="nav-link"><?= e($navZones) ?></a></li>
                    <li><a href="#faq" class="nav-link"><?= e($navFaq) ?></a></li>
                </ul>
            </nav>

            <!-- Header Utility Actions -->
            <div class="header-actions">
                <!-- Member Login / Dashboard CTA -->
                <?php if (\App\Core\Auth::check()): ?>
                    <a href="/dashboard" class="btn btn-primary btn-sm">
                        <span>👤 <?= e(\App\Core\Auth::user()['full_name'] ?? 'Dashboard') ?></span>
                    </a>
                    <?php if (\App\Core\Auth::isEmployee()): ?>
                        <a href="/crm" class="btn btn-secondary btn-sm" title="Open Kinetica CRM" style="border-color: rgba(212, 255, 0, 0.4); color: var(--accent-lime);">
                            <span>⚡ CRM</span>
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="/login" class="btn btn-outline btn-sm">
                        <span><?= $currentLang === 'pl' ? 'Strefa Klienta' : 'Member Sign In' ?></span>
                    </a>
                <?php endif; ?>

                <!-- Language Switcher (SEO: Separate dedicated pages /en & /pl) -->
                <div class="lang-switcher" id="langSwitcher" role="navigation" aria-label="Language selection">
                    <a href="/en" 
                       class="lang-btn <?= $lang === 'en' ? 'active' : '' ?>" 
                       data-target-lang="en" 
                       title="Switch to English"
                       aria-current="<?= $lang === 'en' ? 'page' : 'false' ?>">
                        <span class="flag-icon">🇬🇧</span>
                        <span class="lang-text">EN</span>
                    </a>
                    <span class="lang-sep">/</span>
                    <a href="/pl" 
                       class="lang-btn <?= $lang === 'pl' ? 'active' : '' ?>" 
                       data-target-lang="pl" 
                       title="Przełącz na język polski"
                       aria-current="<?= $lang === 'pl' ? 'page' : 'false' ?>">
                        <span class="flag-icon">🇵🇱</span>
                        <span class="lang-text">PL</span>
                    </a>
                </div>

                <!-- Mobile Menu Hamburger -->
                <button type="button" class="mobile-menu-toggle" id="mobileMenuBtn" aria-label="Toggle navigation menu" aria-expanded="false">
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Navigation Drawer -->
    <div class="mobile-drawer" id="mobileDrawer" aria-hidden="true">
        <div class="mobile-drawer-inner">
            <div class="mobile-drawer-header">
                <div class="drawer-brand">
                    <span class="brand-text">KINETICA<span class="brand-dot">.</span></span>
                    <span class="drawer-brand-sub">WARSAW</span>
                </div>
                <button type="button" class="drawer-close-btn" id="closeDrawerBtn" aria-label="Close menu">&times;</button>
            </div>
            <nav class="mobile-nav">
                <a href="#about" class="mobile-nav-link">
                    <span class="nav-link-text"><?= e($nav['about']) ?></span>
                    <span class="nav-arrow">→</span>
                </a>
                <a href="#free-courses" class="mobile-nav-link highlight-nav">
                    <span class="nav-link-text"><?= e($nav['courses']) ?></span>
                    <span class="nav-badge-free">100% Free</span>
                </a>
                <a href="#pricing" class="mobile-nav-link">
                    <span class="nav-link-text"><?= e($nav['pricing']) ?></span>
                    <span class="nav-arrow">→</span>
                </a>
                <a href="#perks" class="mobile-nav-link">
                    <span class="nav-link-text"><?= e($nav['perks']) ?></span>
                    <span class="nav-arrow">→</span>
                </a>
                <a href="#zones" class="mobile-nav-link">
                    <span class="nav-link-text"><?= e($nav['zones']) ?></span>
                    <span class="nav-arrow">→</span>
                </a>
                <a href="#faq" class="mobile-nav-link">
                    <span class="nav-link-text"><?= e($nav['faq']) ?></span>
                    <span class="nav-arrow">→</span>
                </a>
            </nav>
            <div class="mobile-drawer-footer">
                <?php if (\App\Core\Auth::check()): ?>
                    <a href="/dashboard" class="btn btn-primary btn-block drawer-action-btn">
                        <span>👤 <?= e(\App\Core\Auth::user()['full_name'] ?? 'Dashboard') ?></span>
                    </a>
                    <?php if (\App\Core\Auth::isEmployee()): ?>
                        <a href="/crm" class="btn btn-secondary btn-block drawer-action-btn" style="border-color: rgba(212, 255, 0, 0.4); color: var(--accent-lime);">
                            <span>⚡ Open Club CRM</span>
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="/login" class="btn btn-outline btn-block drawer-action-btn">
                        <span><?= $currentLang === 'pl' ? 'Strefa Klienta (Logowanie)' : 'Member Portal (Sign In)' ?></span>
                    </a>
                <?php endif; ?>
                <button type="button" class="btn btn-secondary btn-block drawer-action-btn open-free-modal-btn">
                    <span>🎁 <?= e($nav['free_btn'] ?? ($currentLang === 'pl' ? 'Darmowe Kursy' : 'Free Courses')) ?></span>
                </button>
                <div class="mobile-lang-row">
                    <span class="lang-label"><?= $currentLang === 'pl' ? 'Język / Lang:' : 'Language:' ?></span>
                    <div class="mobile-lang-pills">
                        <a href="/en" class="lang-btn <?= $currentLang === 'en' ? 'active' : '' ?>">🇬🇧 English</a>
                        <a href="/pl" class="lang-btn <?= $currentLang === 'pl' ? 'active' : '' ?>">🇵🇱 Polski</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="drawer-backdrop" id="drawerBackdrop"></div>

    <!-- Main Page Content -->
    <main id="mainContent">
        <?= $content ?>
    </main>

    <!-- Site Footer -->
    <footer class="site-footer">
        <div class="container footer-container">
            <div class="footer-top-grid">
                <!-- Brand Info -->
                <div class="footer-col brand-col">
                    <div class="brand-logo footer-logo">
                        <span class="brand-icon">⚡</span>
                        <span class="brand-text">KINETICA<span class="brand-dot">.</span></span>
                    </div>
                    <p class="footer-desc"><?= e($footer['about'] ?? ($currentLang === 'pl' ? 'Kinetica Biomechanics Club w centrum Warszawy. Najnowocześniejszy obiekt treningowy i odnowy biologicznej.' : 'Kinetica Biomechanics Club in Warsaw. State of the art training and physical recovery facility.')) ?></p>
                    <div class="footer-badges">
                        <span class="verified-badge">✓ ISO Certified Facility</span>
                        <span class="verified-badge">✓ Official Eleiko Partner</span>
                    </div>
                </div>

                <!-- Navigation Quicklinks -->
                <div class="footer-col">
                    <h4 class="footer-heading"><?= $currentLang === 'pl' ? 'Nawigacja' : 'Navigation' ?></h4>
                    <ul class="footer-links">
                        <li><a href="#about"><?= e($navAbout) ?></a></li>
                        <li><a href="#free-courses"><?= e($navCourses) ?></a></li>
                        <li><a href="#pricing"><?= e($navPricing) ?></a></li>
                        <li><a href="#perks"><?= e($navPerks) ?></a></li>
                        <li><a href="#zones"><?= e($navZones) ?></a></li>
                        <li><a href="#faq"><?= e($navFaq) ?></a></li>
                    </ul>
                </div>

                <!-- 4 Passes Quicklinks -->
                <div class="footer-col">
                    <h4 class="footer-heading"><?= $currentLang === 'pl' ? 'Karnety Klubowe' : 'Membership Passes' ?></h4>
                    <ul class="footer-links">
                        <li><a href="#pricing" data-target-plan="standart">Standart Pass (179 PLN)</a></li>
                        <li><a href="#pricing" data-target-plan="standart_plus">StandartPlus Pass (239 PLN)</a></li>
                        <li><a href="#pricing" data-target-plan="premium">Premium 24/7 (319 PLN)</a></li>
                        <li><a href="#pricing" data-target-plan="max">Max VIP All-Inclusive (499 PLN)</a></li>
                    </ul>
                </div>

                <!-- Contact & Location in Warsaw -->
                <div class="footer-col">
                    <h4 class="footer-heading"><?= $currentLang === 'pl' ? 'Warszawa • Kontakt' : 'Warsaw • Location' ?></h4>
                    <ul class="footer-contact-list">
                        <li>📍 <?= e($footer['address'] ?? 'ul. Złota 44, 00-120 Warszawa') ?></li>
                        <li>🕒 <?= e($footer['hours'] ?? 'Pon - Ndz: 24/7 (Bramki Biometryczne)') ?></li>
                        <li>📞 <a href="tel:+48228904420"><?= e($footer['phone'] ?? '+48 22 890 44 20') ?></a></li>
                        <li>✉️ <a href="mailto:contact@kinetica.gym"><?= e($footer['email'] ?? 'contact@kinetica.gym') ?></a></li>
                    </ul>
                </div>
            </div>

            <!-- Footer Bottom Bar -->
            <div class="footer-bottom-bar">
                <p class="copyright"><?= e($footer['copyright'] ?? ('© ' . date('Y') . ' Kinetica Biomechanics Club Sp. z o.o.')) ?></p>
                <div class="footer-bottom-links">
                    <a href="#privacy"><?= e($footer['privacy'] ?? ($currentLang === 'pl' ? 'Polityka Prywatności' : 'Privacy Policy')) ?></a>
                    <span>•</span>
                    <a href="#terms"><?= e($footer['terms'] ?? ($currentLang === 'pl' ? 'Regulamin' : 'Terms & Conditions')) ?></a>
                    <span>•</span>
                    <a href="<?= e($switch_url ?? ($currentLang === 'pl' ? '/en' : '/pl')) ?>" class="footer-lang-switch">
                        <?= e($switch_label ?? ($currentLang === 'pl' ? '🇬🇧 Switch to English' : '🇵🇱 Przełącz na język polski')) ?>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- MODAL 1: Free Registration / Lead Magnet (Unlock 12+ Free Courses) -->
    <div class="modal-overlay" id="freeModal" aria-hidden="true">
        <div class="modal-dialog" role="dialog" aria-modal="true" aria-labelledby="freeModalTitle">
            <button type="button" class="modal-close-btn" id="closeFreeModalBtn" aria-label="Close modal">&times;</button>
            
            <!-- Step 1: Lead Form -->
            <div class="modal-body" id="freeModalFormContainer">
                <div class="modal-badge">🎁 <?= $lang === 'pl' ? '100% ZA DARMO • BEZ KARTY' : '100% FREE • NO CARD REQUIRED' ?></div>
                <h3 class="modal-title" id="freeModalTitle"><?= e($modals['free_title']) ?></h3>
                <p class="modal-subtitle"><?= e($modals['free_subtitle']) ?></p>

                <form class="lead-capture-form" id="leadCaptureForm" novalidate>
                    <div class="form-group">
                        <label for="leadName"><?= e($modals['name_label']) ?></label>
                        <input type="text" id="leadName" name="name" class="form-input" placeholder="<?= e($modals['name_placeholder']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="leadEmail"><?= e($modals['email_label']) ?> <span class="required-star">*</span></label>
                        <input type="email" id="leadEmail" name="email" class="form-input" placeholder="<?= e($modals['email_placeholder']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="leadPhone"><?= e($modals['phone_label']) ?></label>
                        <input type="tel" id="leadPhone" name="phone" class="form-input" placeholder="<?= e($modals['phone_placeholder']) ?>">
                    </div>

                    <div class="form-group">
                        <label for="leadGoal"><?= e($modals['goal_label']) ?></label>
                        <select id="leadGoal" name="goal" class="form-input form-select">
                            <?php foreach ($modals['goal_options'] as $goal): ?>
                                <option value="<?= e($goal) ?>"><?= e($goal) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-consent">
                        <label class="checkbox-label">
                            <input type="checkbox" name="consent" required checked>
                            <span><?= $lang === 'pl' ? 'Wyrażam zgodę na bezpłatny dostęp do materiałów edukacyjnych oraz powiadomienia o nowych kursach Kinetica.' : 'I agree to receive free access credentials and new educational biomechanics updates from Kinetica.' ?></span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg" id="submitLeadBtn">
                        <span class="btn-text"><?= e($modals['submit_free']) ?></span>
                        <span class="btn-loader" style="display:none;">⏳</span>
                    </button>
                </form>
            </div>

            <!-- Step 2: Instant Success State -->
            <div class="modal-body success-state" id="freeModalSuccessContainer" style="display:none;">
                <div class="success-icon">🎉</div>
                <h3 class="modal-title"><?= e($modals['success_free_title']) ?></h3>
                <p class="modal-subtitle"><?= e($modals['success_free_desc']) ?></p>
                
                <div class="access-token-box">
                    <span class="token-label"><?= $currentLang === 'pl' ? 'Twój Cyfrowy Kod Dostępu:' : 'Your Digital Access Token:' ?></span>
                    <strong class="token-code" id="generatedToken">KIN-7849-VIP</strong>
                    <span class="token-hint"><?= $currentLang === 'pl' ? '✓ 12 Wideokursów odblokowanych w aplikacji' : '✓ 12 Video Masterclasses Unlocked' ?></span>
                </div>

                <div class="bonus-box">
                    <h4>☕ <?= $currentLang === 'pl' ? 'Bonus dla Zarejestrowanych:' : 'Exclusive Registered Bonus:' ?></h4>
                    <p><?= $currentLang === 'pl' ? 'Pokaż ten kod na recepcji przy ul. Złotej 44 w Warszawie i odbierz bezpłatną konsultację z trenerem oraz kawę w strefie lounge!' : 'Show this code at the reception at Złota 44 in Warsaw to claim your free coach consultation and artisan coffee in the lounge!' ?></p>
                </div>

                <button type="button" class="btn btn-primary btn-block close-modal-btn">
                    <?= $currentLang === 'pl' ? 'Przejdź do Platformy Streamingowej' : 'Proceed to Streaming Platform' ?>
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL 2: Plan Checkout / Join Plan Modal -->
    <div class="modal-overlay" id="orderModal" aria-hidden="true">
        <div class="modal-dialog" role="dialog" aria-modal="true" aria-labelledby="orderModalTitle">
            <button type="button" class="modal-close-btn" id="closeOrderModalBtn" aria-label="Close modal">&times;</button>
            
            <div class="modal-body" id="orderModalFormContainer">
                <div class="modal-badge" id="orderPlanBadge">PREMIUM 24/7</div>
                <h3 class="modal-title" id="orderModalTitle"><?= e($modals['order_title'] ?? 'Aktywacja Karnetu') ?></h3>
                
                <!-- Order Summary Card -->
                <div class="order-summary-box">
                    <div class="order-summary-row">
                        <span class="summary-label"><?= e($modals['order_plan_label'] ?? 'Plan') ?>:</span>
                        <strong class="summary-val" id="orderPlanName">Premium</strong>
                    </div>
                    <div class="order-summary-row">
                        <span class="summary-label"><?= e($modals['order_cycle_label'] ?? 'Cykl') ?>:</span>
                        <strong class="summary-val" id="orderCycleName">Monthly</strong>
                    </div>
                    <div class="order-summary-row total-row">
                        <span class="summary-label"><?= e($modals['order_total_label'] ?? 'Suma') ?>:</span>
                        <strong class="summary-price" id="orderTotalPrice">319 PLN</strong>
                    </div>
                </div>

                <form class="plan-checkout-form" id="planCheckoutForm" novalidate>
                    <input type="hidden" id="checkoutPlanInput" name="plan" value="premium">
                    <input type="hidden" id="checkoutCycleInput" name="cycle" value="monthly">

                    <div class="form-group">
                        <label for="orderName"><?= e($modals['name_label'] ?? 'Imię') ?></label>
                        <input type="text" id="orderName" name="name" class="form-input" placeholder="<?= e($modals['name_placeholder'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="orderEmail"><?= e($modals['email_label'] ?? 'Email') ?> <span class="required-star">*</span></label>
                        <input type="email" id="orderEmail" name="email" class="form-input" placeholder="<?= e($modals['email_placeholder'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="orderPhone"><?= e($modals['phone_label'] ?? 'Telefon') ?></label>
                        <input type="tel" id="orderPhone" name="phone" class="form-input" placeholder="<?= e($modals['phone_placeholder'] ?? '') ?>" required>
                    </div>

                    <!-- Payment Method Selector -->
                    <div class="form-group">
                        <label><?= e($modals['order_pay_method'] ?? 'Płatność') ?></label>
                        <div class="payment-methods-grid">
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="blik" checked>
                                <span class="payment-card">
                                    <span class="pay-icon">⚡</span>
                                    <span class="pay-name">BLIK</span>
                                </span>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="apple_pay">
                                <span class="payment-card">
                                    <span class="pay-icon"></span>
                                    <span class="pay-name">Apple Pay</span>
                                </span>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="card">
                                <span class="payment-card">
                                    <span class="pay-icon">💳</span>
                                    <span class="pay-name">Card / Visa</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="form-guarantee-note">
                        🛡️ <?= $currentLang === 'pl' ? 'Bezpieczna płatność SSL. 14 dni na bezpłatną rezygnację.' : 'Secure 256-bit SSL encryption. 14-day full satisfaction guarantee.' ?>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg" id="submitOrderBtn">
                        <span class="btn-text"><?= e($modals['submit_order'] ?? 'Aktywuj') ?></span>
                        <span class="btn-loader" style="display:none;">⏳</span>
                    </button>
                </form>
            </div>

            <!-- Order Success Confirmation -->
            <div class="modal-body success-state" id="orderModalSuccessContainer" style="display:none;">
                <div class="success-icon">🏆</div>
                <h3 class="modal-title"><?= e($modals['success_order_title'] ?? 'Sukces') ?></h3>
                <p class="modal-subtitle"><?= e($modals['success_order_desc'] ?? '') ?></p>
                
                <div class="access-token-box">
                    <span class="token-label"><?= $currentLang === 'pl' ? 'Twój Identyfikator Karnetu:' : 'Pass Activation Code:' ?></span>
                    <strong class="token-code" id="orderActivationCode">NFC-924810</strong>
                    <span class="token-hint"><?= $currentLang === 'pl' ? 'Dodaj do Apple Wallet / Google Pay' : 'Add to Apple Wallet / Google Pay' ?></span>
                </div>

                <a href="/dashboard" class="btn btn-primary btn-block">
                    <?= $currentLang === 'pl' ? 'Przejdź do Mojego Panelu' : 'Enter My Dashboard' ?> →
                </a>
            </div>
        </div>
    </div>

    <!-- Core Interactive Client Script -->
    <script src="/assets/js/main.js"></script>
</body>
</html>
