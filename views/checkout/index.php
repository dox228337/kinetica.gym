<?php
$isPl = ($lang ?? ($_SESSION['lang'] ?? 'en')) === 'pl';
$price = $cycle === 'annual' ? ($plan['price_annual'] * 12) : $plan['price_monthly'];
?>
<div class="checkout-page-container">
    <div class="container max-w-900">
        <div class="checkout-header text-center">
            <span class="checkout-badge">SANDBOX SIMULATOR • PORTFOLIO DEMO</span>
            <h1 class="checkout-title"><?= $isPl ? 'Aktywacja Karnetu Klubowego' : 'Membership Pass Activation' ?></h1>
            <p class="checkout-subtitle">
                <?= $isPl 
                    ? 'Symuluj proces aktywacji karnetu i polskich płatności (BLIK, Apple Pay, Karta) w bezpiecznym środowisku sandbox bez używania prawdziwych danych bankowych.' 
                    : 'Simulate real-time Polish payment workflows (BLIK, Apple Pay, Card) in a secure sandbox without real banking credentials.' ?>
            </p>
        </div>

        <div class="checkout-grid" id="checkoutGrid">
            <!-- Left Column: Selected Pass Summary -->
            <div class="checkout-summary-card">
                <span class="plan-badge-tag"><?= e($plan['badge']) ?></span>
                <h2 class="plan-summary-title"><?= e($plan['name']) ?> Pass</h2>
                <p class="plan-summary-desc"><?= e($plan['desc']) ?></p>

                <div class="summary-price-box">
                    <span class="summary-price-amount">
                        <?= $price ?> PLN
                    </span>
                    <span class="summary-price-cycle">
                        <?php if ($cycle === 'annual'): ?>
                            <?= $isPl ? 'Rozliczane rocznie (Zniżka -20% naliczona)' : 'Billed annually (-20% discount applied)' ?>
                        <?php else: ?>
                            <?= $isPl ? 'Rozliczane miesięcznie (Rezygnuj w każdej chwili)' : 'Billed monthly (Cancel anytime)' ?>
                        <?php endif; ?>
                    </span>
                </div>

                <div class="summary-features-list">
                    <strong><?= $isPl ? 'Zawarte przywileje:' : 'Included Privileges:' ?></strong>
                    <ul>
                        <?php foreach ($plan['features'] as $f): ?>
                            <?php if ($f['included']): ?>
                                <li>✓ <?= e($f['text']) ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="summary-club-note">
                    📍 Warsaw Flagship • Złota 44 • 24/7 Biometric Smart Turnstile
                </div>
            </div>

            <!-- Right Column: Interactive Payment Simulator Form -->
            <div class="checkout-form-card" id="checkoutFormCard">
                <h3 class="form-card-title"><?= $isPl ? 'Wybierz Metodę Płatności' : 'Choose Simulated Payment Method' ?></h3>

                <div class="payment-tabs" role="tablist">
                    <button type="button" class="pay-tab-btn active" onclick="switchPayTab('blik')" id="tabBlik">
                        <span>⚡ BLIK</span>
                    </button>
                    <button type="button" class="pay-tab-btn" onclick="switchPayTab('apple_pay')" id="tabApple">
                        <span> Apple Pay</span>
                    </button>
                    <button type="button" class="pay-tab-btn" onclick="switchPayTab('card')" id="tabCard">
                        <span>💳 <?= $isPl ? 'Karta' : 'Card' ?></span>
                    </button>
                </div>

                <form id="simulatedPayForm" onsubmit="handleSimulatedPayment(event)">
                    <input type="hidden" id="selectedPlanInput" value="<?= e($plan['id']) ?>">
                    <input type="hidden" id="selectedCycleInput" value="<?= e($cycle) ?>">
                    <input type="hidden" id="selectedMethodInput" value="blik">

                    <!-- Athlete Details -->
                    <div class="form-group">
                        <label for="payName"><?= $isPl ? 'Imię i Nazwisko' : 'Full Name' ?></label>
                        <input type="text" id="payName" class="form-input" value="<?= e($user['full_name'] ?? 'Alex Morgan') ?>" required>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="payEmail"><?= $isPl ? 'Adres E-mail (do wysyłki karnetu)' : 'Email Address (for Digital Pass delivery)' ?></label>
                            <input type="email" id="payEmail" class="form-input" value="<?= e($user['email'] ?? 'athlete@example.com') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="payPhone"><?= $isPl ? 'Numer Telefonu' : 'Phone Number' ?></label>
                            <input type="tel" id="payPhone" class="form-input" value="<?= e($user['phone'] ?? '+48 500 123 456') ?>" required>
                        </div>
                    </div>

                    <!-- TAB 1: BLIK Simulator -->
                    <div class="payment-tab-content" id="tabContent_blik">
                        <div class="blik-box">
                            <div class="blik-logo-row">
                                <span class="blik-logo">⚡ BLIK</span>
                                <small><?= $isPl ? 'Wpisz dowolny 6-cyfrowy kod testowy' : 'Enter any 6-digit test code' ?></small>
                            </div>
                            <input type="text" id="blikCode" class="form-input blik-input" placeholder="777 888" maxlength="7" value="777 888">
                            <span class="blik-hint"><?= $isPl ? '✓ Tryb Sandbox: dowolne 6 cyfr zasymuluje pomyślną autoryzację banku' : '✓ Sandbox Mode: any 6 digits will simulate a successful bank authorization' ?></span>
                        </div>
                    </div>

                    <!-- TAB 2: Apple Pay Simulator -->
                    <div class="payment-tab-content" id="tabContent_apple_pay" style="display:none;">
                        <div class="apple-pay-box">
                            <div class="apple-pay-preview">
                                <span>Pay Biometric Touch ID / Face ID</span>
                                <small><?= $isPl ? 'Podpięta karta testowa • Warsaw PLN' : 'Linked to Test Card • Warsaw PLN' ?></small>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: Card Simulator -->
                    <div class="payment-tab-content" id="tabContent_card" style="display:none;">
                        <div class="card-simulator-box">
                            <div class="form-group">
                                <label><?= $isPl ? 'Numer Karty (Sandbox Demo)' : 'Card Number (Test Sandbox)' ?></label>
                                <input type="text" class="form-input" value="4242 •••• •••• 4242" readonly>
                            </div>
                            <div class="form-row-2">
                                <div class="form-group">
                                    <label><?= $isPl ? 'Ważność' : 'Expires' ?></label>
                                    <input type="text" class="form-input" value="12/28" readonly>
                                </div>
                                <div class="form-group">
                                    <label>CVC / CVV</label>
                                    <input type="text" class="form-input" value="789" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sandbox-security-note">
                        🛡️ <strong><?= $isPl ? 'Aktywny Tryb Symulatora:' : 'Sandbox Mode Active:' ?></strong> 
                        <?= $isPl 
                            ? 'Żadne rzeczywiste środki nie zostaną pobrane. System w pełni zapisze transakcję w bazie danych, aktywuje karnet i wygeneruje cyfrowy token NFC.' 
                            : 'No real funds will be charged. This demonstrates full end-to-end checkout, DB ledger creation, and digital pass issuance.' ?>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg" id="paySubmitBtn">
                        <span id="btnText"><?= $isPl ? 'Symuluj Płatność' : 'Simulate Payment' ?> (<?= $price ?> PLN) →</span>
                        <span id="btnLoader" style="display:none;">⏳ <?= $isPl ? 'Autoryzacja transakcji w toku...' : 'Authorizing Sandbox Transaction...' ?></span>
                    </button>
                </form>
            </div>

            <!-- Receipt & Order Confirmation Screen (Hidden Initially) -->
            <div class="checkout-receipt-card" id="checkoutReceiptCard" style="display:none;">
                <div class="receipt-icon">🏆</div>
                <h2 class="receipt-title"><?= $isPl ? 'Płatność Zaakceptowana w Trybie Sandbox!' : 'Payment Successfully Simulated!' ?></h2>
                <p class="receipt-subtitle">
                    <?= $isPl 
                        ? 'Twój karnet został pomyślnie aktywowany w bazie danych klubu i powiązany z cyfrowym tokenem bramki RFID/NFC.' 
                        : 'Your pass is active in the database and linked to your digital NFC token.' ?>
                </p>

                <div class="receipt-details-table">
                    <div class="receipt-row">
                        <span><?= $isPl ? 'Numer referencyjny:' : 'Transaction Reference:' ?></span>
                        <code id="receiptTxRef">SIM-BLIK-849201</code>
                    </div>
                    <div class="receipt-row">
                        <span><?= $isPl ? 'Karnet klubowy:' : 'Membership Pass:' ?></span>
                        <strong id="receiptPlanName"><?= e($plan['name']) ?> Pass</strong>
                    </div>
                    <div class="receipt-row">
                        <span><?= $isPl ? 'Kwota:' : 'Amount Paid:' ?></span>
                        <strong class="receipt-price" id="receiptAmount"><?= $price ?> PLN</strong>
                    </div>
                    <div class="receipt-row">
                        <span>Status:</span>
                        <span class="badge-approved">✓ APPROVED (SANDBOX)</span>
                    </div>
                    <div class="receipt-row">
                        <span><?= $isPl ? 'Kod bramki turnikietowej:' : 'Turnstile NFC Code:' ?></span>
                        <code id="receiptActivationCode">NFC-849201</code>
                    </div>
                </div>

                <div class="receipt-actions">
                    <a href="/dashboard" class="btn btn-primary btn-block btn-lg">
                        <span><?= $isPl ? 'Przejdź do Mojego Panelu →' : 'Enter My Personal Dashboard →' ?></span>
                    </a>
                    <a href="/<?= $isPl ? 'pl' : 'en' ?>" class="btn btn-outline btn-block">
                        <span><?= $isPl ? 'Powrót do Strony Głównej' : 'Back to Club Homepage' ?></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentMethod = 'blik';

function switchPayTab(method) {
    currentMethod = method;
    document.getElementById('selectedMethodInput').value = method;

    // Toggle tab active states
    document.querySelectorAll('.pay-tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.payment-tab-content').forEach(c => c.style.display = 'none');

    if (method === 'blik') {
        document.getElementById('tabBlik').classList.add('active');
        document.getElementById('tabContent_blik').style.display = 'block';
    } else if (method === 'apple_pay') {
        document.getElementById('tabApple').classList.add('active');
        document.getElementById('tabContent_apple_pay').style.display = 'block';
    } else if (method === 'card') {
        document.getElementById('tabCard').classList.add('active');
        document.getElementById('tabContent_card').style.display = 'block';
    }
}

async function handleSimulatedPayment(e) {
    e.preventDefault();

    const submitBtn = document.getElementById('paySubmitBtn');
    const btnText = document.getElementById('btnText');
    const btnLoader = document.getElementById('btnLoader');

    if (submitBtn.disabled) return;

    btnText.style.display = 'none';
    btnLoader.style.display = 'inline-block';
    submitBtn.disabled = true;

    const payload = {
        plan: document.getElementById('selectedPlanInput').value,
        cycle: document.getElementById('selectedCycleInput').value,
        payment_method: document.getElementById('selectedMethodInput').value,
        name: document.getElementById('payName').value.trim(),
        email: document.getElementById('payEmail').value.trim(),
        phone: document.getElementById('payPhone').value.trim()
    };

    try {
        const response = await fetch('/checkout/process', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const data = await response.json();

        // 1.2s delay to simulate authentic payment handshake
        setTimeout(() => {
            document.getElementById('checkoutFormCard').style.display = 'none';
            const receiptCard = document.getElementById('checkoutReceiptCard');
            receiptCard.style.display = 'block';

            if (data.receipt) {
                document.getElementById('receiptTxRef').textContent = data.receipt.transaction_ref;
                document.getElementById('receiptPlanName').textContent = data.receipt.plan_name;
                document.getElementById('receiptAmount').textContent = data.receipt.amount_paid;
                document.getElementById('receiptActivationCode').textContent = data.receipt.activation_code;
            }
        }, 1200);

    } catch (err) {
        alert('Simulation completed.');
        window.location.href = '/dashboard';
    }
}
</script>
