<!-- Hero Section -->
<section class="hero-section" id="about">
    <div class="ambient-glow glow-cyan"></div>
    <div class="ambient-glow glow-lime"></div>
    
    <div class="container hero-container">
        <div class="hero-content">
            <div class="hero-badge">
                <span class="badge-dot"></span>
                <span><?= e($hero['badge']) ?></span>
            </div>

            <h1 class="hero-title">
                <?= e($hero['title_line1']) ?><br>
                <span class="text-gradient"><?= e($hero['title_highlight']) ?>.</span>
            </h1>

            <p class="hero-subtitle">
                <?= e($hero['subtitle']) ?>
            </p>

            <div class="hero-cta-group">
                <button type="button" class="btn btn-primary btn-lg open-free-modal-btn">
                    <span class="btn-icon">⚡</span>
                    <span><?= e($hero['cta_free']) ?></span>
                </button>
                <a href="#pricing" class="btn btn-outline btn-lg">
                    <span><?= e($hero['cta_pricing']) ?></span>
                    <span class="btn-arrow">↓</span>
                </a>
            </div>

            <div class="hero-stats-grid">
                <div class="stat-card">
                    <strong class="stat-number"><?= e($hero['stat_members']) ?></strong>
                    <span class="stat-label"><?= e($hero['stat_members_label']) ?></span>
                </div>
                <div class="stat-card">
                    <strong class="stat-number"><?= e($hero['stat_space']) ?></strong>
                    <span class="stat-label"><?= e($hero['stat_space_label']) ?></span>
                </div>
                <div class="stat-card">
                    <strong class="stat-number"><?= e($hero['stat_rating']) ?></strong>
                    <span class="stat-label"><?= e($hero['stat_rating_label']) ?></span>
                </div>
                <div class="stat-card">
                    <strong class="stat-number"><?= e($hero['stat_access']) ?></strong>
                    <span class="stat-label"><?= e($hero['stat_access_label']) ?></span>
                </div>
            </div>
        </div>

        <div class="hero-media-wrapper">
            <div class="hero-media-card">
                <div class="media-frame">
                    <img src="/assets/images/hero.jpg" alt="Kinetica Biomechanics Gym Interior Warsaw" class="hero-img" width="1280" height="720" fetchpriority="high">
                    <div class="media-overlay"></div>
                </div>

                <!-- Floating Telemetry Badges -->
                <div class="telemetry-badge telemetry-top">
                    <span class="telemetry-indicator live"></span>
                    <div class="telemetry-info">
                        <strong>AI Joint Sensor Array</strong>
                        <small>Precision: 99.4% • 60 FPS</small>
                    </div>
                </div>

                <div class="telemetry-badge telemetry-bottom">
                    <span class="telemetry-icon">❄️</span>
                    <div class="telemetry-info">
                        <strong>Cryotherapy Pod</strong>
                        <small>-110°C • Recovery Ready</small>
                    </div>
                </div>

                <div class="telemetry-badge telemetry-center">
                    <span class="telemetry-indicator pulse"></span>
                    <div class="telemetry-info">
                        <strong>Warsaw Flagship</strong>
                        <small>Złota 44 • Open 24/7</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 2: Free Courses For All Registered Users (Lead Magnet Slogan) -->
<section class="section free-courses-section" id="free-courses">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-tag"><?= e($free_courses_section['tag']) ?></span>
            <h2 class="section-title highlight-slogan"><?= e($free_courses_section['title']) ?></h2>
            <p class="section-subtitle max-w-700 mx-auto"><?= e($free_courses_section['subtitle']) ?></p>
        </div>

        <!-- Slogan Highlight Banner -->
        <div class="free-perks-banner">
            <div class="perks-banner-inner">
                <?php foreach ($free_courses_section['highlight_points'] as $point): ?>
                    <div class="banner-point">
                        <span class="point-icon">✓</span>
                        <span class="point-text"><?= e($point) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 4 Free Courses Grid -->
        <div class="courses-grid">
            <?php foreach ($free_courses_section['courses_list'] as $idx => $course): ?>
                <div class="course-card" data-course-index="<?= $idx ?>">
                    <div class="course-header">
                        <span class="course-badge"><?= e($course['badge']) ?></span>
                        <span class="course-duration"><?= e($course['duration']) ?></span>
                    </div>
                    <h3 class="course-title"><?= e($course['title']) ?></h3>
                    <p class="course-coach">👨‍🏫 <?= e($course['coach']) ?></p>
                    <p class="course-desc"><?= e($course['desc']) ?></p>
                    <div class="course-footer">
                        <span class="free-status-tag">🎁 100% FREE</span>
                        <button type="button" class="btn btn-sm btn-outline open-free-modal-btn">
                            <?= $lang === 'pl' ? 'Oglądaj za darmo' : 'Stream Free' ?> →
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Big Free Registration Call to Action Box -->
        <div class="free-cta-banner">
            <div class="free-cta-content">
                <span class="free-cta-badge"><?= $lang === 'pl' ? 'BEZPŁATNA REJESTRACJA KLUBU' : 'FREE COMMUNITY REGISTRATION' ?></span>
                <h3><?= $lang === 'pl' ? 'Dołącz do 14 000+ osób uczących się prawidłowej biomechaniki' : 'Join 14,000+ athletes mastering injury-free biomechanics' ?></h3>
                <p><?= $lang === 'pl' ? 'Wpisz swoje imię i adres e-mail, aby w 10 sekund otrzymać natychmiastowy dostęp do wszystkich 12 wideokursów oraz e-booków z planami żywieniowymi.' : 'Enter your name and email to receive instantaneous access credentials to our complete 12 video masterclasses library and nutrition guidebooks.' ?></p>
            </div>
            <div class="free-cta-action">
                <button type="button" class="btn btn-primary btn-lg open-free-modal-btn pulse-button">
                    <?= e($free_courses_section['cta_button']) ?>
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Section 3: 4 Membership Passes (Standart, StandartPlus, Premium, Max) -->
<section class="section pricing-section" id="pricing">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-tag"><?= e($pricing_section['tag']) ?></span>
            <h2 class="section-title"><?= e($pricing_section['title']) ?></h2>
            <p class="section-subtitle max-w-700 mx-auto"><?= e($pricing_section['subtitle']) ?></p>
            
            <!-- Interactive Billing Cycle Switcher (Monthly vs Annual -20%) -->
            <div class="billing-switcher" role="radiogroup" aria-label="Billing cycle selector">
                <button type="button" class="billing-btn active" id="billingMonthlyBtn" data-cycle="monthly">
                    <?= e($pricing_section['toggle_monthly']) ?>
                </button>
                <button type="button" class="billing-btn" id="billingAnnualBtn" data-cycle="annual">
                    <span><?= e($pricing_section['toggle_annual']) ?></span>
                    <span class="save-badge">-20%</span>
                </button>
            </div>
        </div>

        <!-- 4 Pricing Cards Grid -->
        <div class="pricing-grid">
            <?php foreach ($pricing_section['plans'] as $plan): ?>
                <div class="pricing-card <?= $plan['popular'] ? 'featured' : '' ?>" 
                     id="planCard_<?= e($plan['id']) ?>"
                     data-plan-id="<?= e($plan['id']) ?>"
                     data-plan-name="<?= e($plan['name']) ?>"
                     data-price-monthly="<?= $plan['price_monthly'] ?>"
                     data-price-annual="<?= $plan['price_annual'] ?>">
                    
                    <?php if ($plan['popular']): ?>
                        <div class="popular-ribbon"><?= e($plan['badge']) ?></div>
                    <?php else: ?>
                        <div class="plan-top-badge"><?= e($plan['badge']) ?></div>
                    <?php endif; ?>

                    <div class="plan-header">
                        <h3 class="plan-name"><?= e($plan['name']) ?></h3>
                        <p class="plan-desc"><?= e($plan['desc']) ?></p>
                    </div>

                    <div class="plan-price-wrapper">
                        <div class="price-display">
                            <span class="price-val" id="priceVal_<?= e($plan['id']) ?>"><?= $plan['price_monthly'] ?></span>
                            <span class="price-currency"><?= e($pricing_section['currency']) ?></span>
                            <span class="price-period"><?= e($pricing_section['per_month']) ?></span>
                        </div>
                        <div class="price-annual-note" id="annualNote_<?= e($plan['id']) ?>" style="display:none;">
                            <?= $lang === 'pl' ? 'Rozliczane rocznie (' . ($plan['price_annual'] * 12) . ' zł/rok)' : 'Billed annually (' . ($plan['price_annual'] * 12) . ' PLN/yr)' ?>
                        </div>
                    </div>

                    <ul class="plan-features-list">
                        <?php foreach ($plan['features'] as $f): ?>
                            <li class="feature-item <?= $f['included'] ? 'included' : 'excluded' ?> <?= !empty($f['highlight']) ? 'highlighted-feature' : '' ?>">
                                <span class="feature-icon"><?= $f['included'] ? '✓' : '✕' ?></span>
                                <span class="feature-text"><?= e($f['text']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="plan-action">
                        <?php 
                        $isLoggedIn = \App\Core\Auth::check();
                        $targetUrl = $isLoggedIn 
                            ? '/checkout?plan=' . urlencode($plan['id']) . '&cycle=monthly'
                            : '/register?plan=' . urlencode($plan['id']) . '&cycle=monthly';
                        ?>
                        <a href="<?= $targetUrl ?>" 
                           class="btn <?= $plan['popular'] ? 'btn-primary' : 'btn-outline' ?> btn-block btn-select-plan"
                           data-plan-id="<?= e($plan['id']) ?>"
                           data-plan-name="<?= e($plan['name']) ?>"
                           data-plan-badge="<?= e($plan['badge']) ?>"
                           data-logged-in="<?= $isLoggedIn ? '1' : '0' ?>">
                            <?= e($plan['btn_text']) ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="pricing-guarantee-bar">
            <span><?= e($pricing_section['guarantee']) ?></span>
        </div>
    </div>
</section>

<!-- Section 4: Exclusive Advantages for Club Members -->
<section class="section perks-section" id="perks">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-tag"><?= e($perks_section['tag']) ?></span>
            <h2 class="section-title"><?= e($perks_section['title']) ?></h2>
            <p class="section-subtitle max-w-700 mx-auto"><?= e($perks_section['subtitle']) ?></p>
        </div>

        <div class="perks-grid">
            <?php foreach ($perks_section['items'] as $item): ?>
                <div class="perk-card">
                    <div class="perk-icon-wrapper"><?= $item['icon'] ?></div>
                    <h3 class="perk-title"><?= e($item['title']) ?></h3>
                    <p class="perk-desc"><?= e($item['desc']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Paid vs Registered Comparison Callout -->
        <div class="perks-comparison-box">
            <div class="comparison-header">
                <h3><?= $lang === 'pl' ? 'Darmowe Konto vs. Płatny Karnet Klubowy' : 'Free Registered Account vs. Active Club Membership' ?></h3>
                <p><?= $lang === 'pl' ? 'Transparentne zasady: każdy może zacząć od darmowych kursów, a płatny karnet otwiera fizyczny obiekt i technologie biohackingu w Warszawie.' : 'Clear transparency: anyone can begin with free courses, while paid passes grant complete access to Warsaw’s physical facility and biohacking tech.' ?></p>
            </div>
            <div class="comparison-columns">
                <div class="comp-col comp-free">
                    <div class="comp-title">🎁 <?= $lang === 'pl' ? 'Zarejestrowany Użytkownik' : 'Registered Free User' ?></div>
                    <ul class="comp-list">
                        <li>✓ 12+ Wideokursów w jakości 4K</li>
                        <li>✓ E-booki z planami żywieniowymi</li>
                        <li>✓ Dostęp do społeczności online</li>
                        <li>✓ Zaproszenie na darmowe oprowadzanie</li>
                        <li class="comp-muted">✕ Wstęp na siłownię i maszyny</li>
                        <li class="comp-muted">✕ Kriokomora i sauny</li>
                        <li class="comp-muted">✕ Treningi z trenerem personalnym</li>
                    </ul>
                </div>
                <div class="comp-col comp-paid">
                    <div class="comp-title">⚡ <?= $lang === 'pl' ? 'Klubowicz Kinetica (Płatny Karnet)' : 'Kinetica Member (Paid Pass)' ?></div>
                    <ul class="comp-list">
                        <li>✓ Wszystko z konta darmowego</li>
                        <li>✓ Nieograniczony wstęp na strefę 1 400 m²</li>
                        <li>✓ Sprzęt Technogym Biostrength & Eleiko</li>
                        <li>✓ Dostęp biometryczny 24/7 (Premium & Max)</li>
                        <li>✓ Kriokomora -110°C i kapsuły infrared</li>
                        <li>✓ Zajęcia grupowe & Treningi Personalne</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 5: Facility & High-Tech Zones -->
<section class="section zones-section" id="zones">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-tag"><?= e($zones_section['tag']) ?></span>
            <h2 class="section-title"><?= e($zones_section['title']) ?></h2>
            <p class="section-subtitle max-w-700 mx-auto"><?= e($zones_section['subtitle']) ?></p>
        </div>

        <div class="zones-grid">
            <?php foreach ($zones_section['zones'] as $zone): ?>
                <div class="zone-card">
                    <div class="zone-img-wrapper">
                        <img src="<?= e($zone['img']) ?>" alt="<?= e($zone['title']) ?>" class="zone-img" loading="lazy" width="600" height="340">
                        <div class="zone-overlay"></div>
                    </div>
                    <div class="zone-info">
                        <h3 class="zone-title"><?= e($zone['title']) ?></h3>
                        <p class="zone-desc"><?= e($zone['desc']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Section 6: Member Reviews & Social Proof -->
<section class="section reviews-section" id="reviews">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-tag"><?= e($reviews_section['tag']) ?></span>
            <h2 class="section-title"><?= e($reviews_section['title']) ?></h2>
            <p class="section-subtitle max-w-700 mx-auto"><?= e($reviews_section['subtitle']) ?></p>
        </div>

        <div class="reviews-grid">
            <?php foreach ($reviews_section['reviews'] as $rev): ?>
                <div class="review-card">
                    <div class="review-stars">
                        <?php for ($i = 0; $i < $rev['rating']; $i++): ?>★<?php endfor; ?>
                    </div>
                    <p class="review-quote">"<?= e($rev['text']) ?>"</p>
                    <div class="review-author">
                        <div class="author-avatar"><?= mb_substr($rev['name'], 0, 1) ?></div>
                        <div class="author-details">
                            <strong class="author-name"><?= e($rev['name']) ?></strong>
                            <span class="author-role"><?= e($rev['role']) ?></span>
                            <span class="author-pass-badge"><?= e($rev['pass']) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Section 7: Comprehensive FAQ Accordion -->
<section class="section faq-section" id="faq">
    <div class="container max-w-900">
        <div class="section-header text-center">
            <span class="section-tag"><?= e($faq_section['tag']) ?></span>
            <h2 class="section-title"><?= e($faq_section['title']) ?></h2>
            <p class="section-subtitle"><?= e($faq_section['subtitle']) ?></p>
        </div>

        <div class="faq-accordion" id="faqAccordion">
            <?php foreach ($faq_section['items'] as $idx => $item): ?>
                <div class="faq-item <?= $idx === 0 ? 'active' : '' ?>" id="faqItem_<?= $idx ?>">
                    <button type="button" class="faq-question" aria-expanded="<?= $idx === 0 ? 'true' : 'false' ?>" aria-controls="faqAnswer_<?= $idx ?>">
                        <span class="faq-q-text"><?= e($item['q']) ?></span>
                        <span class="faq-chevron">▾</span>
                    </button>
                    <div class="faq-answer" id="faqAnswer_<?= $idx ?>" style="<?= $idx === 0 ? 'max-height: 500px;' : '' ?>">
                        <div class="faq-answer-inner">
                            <p><?= e($item['a']) ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Section 8: Final Bottom Conversion CTA Banner -->
<section class="section cta-bottom-section">
    <div class="container">
        <div class="cta-bottom-box">
            <div class="ambient-glow glow-cyan"></div>
            <div class="ambient-glow glow-lime"></div>

            <div class="cta-bottom-content">
                <span class="section-tag"><?= $lang === 'pl' ? 'DOŁĄCZ DO NAS W WARSZAWIE' : 'JOIN US IN WARSAW' ?></span>
                <h2 class="cta-bottom-title"><?= e($cta_bottom['title']) ?></h2>
                <p class="cta-bottom-subtitle"><?= e($cta_bottom['subtitle']) ?></p>
                <div class="cta-bottom-buttons">
                    <button type="button" class="btn btn-primary btn-lg open-free-modal-btn pulse-button">
                        <span class="btn-icon">⚡</span>
                        <span><?= e($cta_bottom['btn_free']) ?></span>
                    </button>
                    <a href="#pricing" class="btn btn-outline btn-lg">
                        <span><?= e($cta_bottom['btn_pass']) ?></span>
                        <span class="btn-arrow">→</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
