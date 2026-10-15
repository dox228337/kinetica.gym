/**
 * Kinetica Gym Warsaw — Client Interactive Scripts
 */

document.addEventListener('DOMContentLoaded', () => {
    initLanguageSwitcher();
    initBillingToggle();
    initModals();
    initFaqAccordion();
    initMobileNav();
    initScrollHeader();
});

/**
 * 1. Language Switcher: Preserves current section hash when switching between /en and /pl
 */
function initLanguageSwitcher() {
    const langButtons = document.querySelectorAll('.lang-switcher .lang-btn, .mobile-lang-row .lang-btn');
    
    langButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            const currentHash = window.location.hash;
            const targetHref = btn.getAttribute('href');
            
            if (targetHref && currentHash) {
                e.preventDefault();
                // Navigate to target language with the same hash
                window.location.href = targetHref + currentHash;
            }
        });
    });
}

/**
 * 2. Billing Cycle Toggle (Monthly vs Annual -20%)
 */
let currentBillingCycle = 'monthly';

function initBillingToggle() {
    const monthlyBtn = document.getElementById('billingMonthlyBtn');
    const annualBtn = document.getElementById('billingAnnualBtn');
    const planCards = document.querySelectorAll('.pricing-card');

    if (!monthlyBtn || !annualBtn) return;

    function setCycle(cycle) {
        currentBillingCycle = cycle;

        if (cycle === 'monthly') {
            monthlyBtn.classList.add('active');
            annualBtn.classList.remove('active');
        } else {
            annualBtn.classList.add('active');
            monthlyBtn.classList.remove('active');
        }

        planCards.forEach(card => {
            const planId = card.getAttribute('data-plan-id');
            const priceMonthly = card.getAttribute('data-price-monthly');
            const priceAnnual = card.getAttribute('data-price-annual');
            const priceEl = document.getElementById(`priceVal_${planId}`);
            const annualNoteEl = document.getElementById(`annualNote_${planId}`);

            if (priceEl) {
                priceEl.style.opacity = '0';
                setTimeout(() => {
                    priceEl.textContent = cycle === 'monthly' ? priceMonthly : priceAnnual;
                    priceEl.style.opacity = '1';
                }, 150);
            }

            if (annualNoteEl) {
                annualNoteEl.style.display = cycle === 'annual' ? 'block' : 'none';
            }
        });
    }

    monthlyBtn.addEventListener('click', () => setCycle('monthly'));
    annualBtn.addEventListener('click', () => setCycle('annual'));
}

/**
 * 3. Modals Management (Free Lead Magnet & Plan Checkout)
 */
function initModals() {
    const freeModal = document.getElementById('freeModal');
    const orderModal = document.getElementById('orderModal');
    const openFreeBtns = document.querySelectorAll('.open-free-modal-btn');
    const closeBtns = document.querySelectorAll('.modal-close-btn, .close-modal-btn');
    const planSelectBtns = document.querySelectorAll('.btn-select-plan');

    // Open Free Modal
    openFreeBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            openModal(freeModal);
        });
    });

    // Plan Selection: Route guest to register with plan or member directly to checkout
    planSelectBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const planId = btn.getAttribute('data-plan-id');
            const isLoggedIn = btn.getAttribute('data-logged-in') === '1';
            const cycle = currentBillingCycle || 'monthly';

            if (isLoggedIn) {
                window.location.href = `/checkout?plan=${encodeURIComponent(planId)}&cycle=${encodeURIComponent(cycle)}`;
            } else {
                window.location.href = `/register?plan=${encodeURIComponent(planId)}&cycle=${encodeURIComponent(cycle)}`;
            }
        });
    });

    // Close Modals
    closeBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            closeAllModals();
        });
    });

    // Close on overlay backdrop click
    [freeModal, orderModal].forEach(modal => {
        if (!modal) return;
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeAllModals();
            }
        });
    });

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeAllModals();
        }
    });

    // Setup forms inside modals
    setupLeadForm();
    setupOrderForm();
}

function openModal(modal) {
    if (!modal) return;
    document.body.style.overflow = 'hidden';
    modal.classList.add('open');
    modal.setAttribute('aria-hidden', 'false');
}

function closeAllModals() {
    document.body.style.overflow = '';
    const openModals = document.querySelectorAll('.modal-overlay.open');
    openModals.forEach(m => {
        m.classList.remove('open');
        m.setAttribute('aria-hidden', 'true');
    });
}

function setupOrderModal(planId, planName, planBadge) {
    const orderPlanBadge = document.getElementById('orderPlanBadge');
    const orderPlanName = document.getElementById('orderPlanName');
    const orderCycleName = document.getElementById('orderCycleName');
    const orderTotalPrice = document.getElementById('orderTotalPrice');
    const checkoutPlanInput = document.getElementById('checkoutPlanInput');
    const checkoutCycleInput = document.getElementById('checkoutCycleInput');

    const card = document.getElementById(`planCard_${planId}`);
    let price = '179 PLN';
    if (card) {
        const pMonthly = card.getAttribute('data-price-monthly');
        const pAnnual = card.getAttribute('data-price-annual');
        price = (currentBillingCycle === 'monthly' ? pMonthly : pAnnual) + ' PLN/mo';
    }

    const isPl = document.body.getAttribute('data-lang') === 'pl';
    const cycleLabel = currentBillingCycle === 'monthly' 
        ? (isPl ? 'Miesięczna' : 'Monthly')
        : (isPl ? 'Roczna (-20%)' : 'Annual (20% OFF)');

    if (orderPlanBadge) orderPlanBadge.textContent = planBadge;
    if (orderPlanName) orderPlanName.textContent = planName;
    if (orderCycleName) orderCycleName.textContent = cycleLabel;
    if (orderTotalPrice) orderTotalPrice.textContent = price;
    if (checkoutPlanInput) checkoutPlanInput.value = planId;
    if (checkoutCycleInput) checkoutCycleInput.value = currentBillingCycle;

    // Reset view
    const formContainer = document.getElementById('orderModalFormContainer');
    const successContainer = document.getElementById('orderModalSuccessContainer');
    if (formContainer) formContainer.style.display = 'block';
    if (successContainer) successContainer.style.display = 'none';
}

function setupLeadForm() {
    const form = document.getElementById('leadCaptureForm');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = document.getElementById('submitLeadBtn');
        const btnText = submitBtn.querySelector('.btn-text');
        const btnLoader = submitBtn.querySelector('.btn-loader');

        if (btnText && btnLoader) {
            btnText.style.display = 'none';
            btnLoader.style.display = 'inline-block';
        }

        const formData = new FormData(form);
        const payload = Object.fromEntries(formData.entries());

        try {
            const res = await fetch('/api/register-lead', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();

            // Transition to success screen
            document.getElementById('freeModalFormContainer').style.display = 'none';
            const successContainer = document.getElementById('freeModalSuccessContainer');
            successContainer.style.display = 'block';

            if (data.user && data.user.access_token) {
                const tokenEl = document.getElementById('generatedToken');
                if (tokenEl) tokenEl.textContent = data.user.access_token;
            }
        } catch (err) {
            // Simulated fallback if network issue
            document.getElementById('freeModalFormContainer').style.display = 'none';
            document.getElementById('freeModalSuccessContainer').style.display = 'block';
        } finally {
            if (btnText && btnLoader) {
                btnText.style.display = 'inline-block';
                btnLoader.style.display = 'none';
            }
        }
    });
}

function setupOrderForm() {
    const form = document.getElementById('planCheckoutForm');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = document.getElementById('submitOrderBtn');
        const btnText = submitBtn.querySelector('.btn-text');
        const btnLoader = submitBtn.querySelector('.btn-loader');

        if (btnText && btnLoader) {
            btnText.style.display = 'none';
            btnLoader.style.display = 'inline-block';
        }

        const formData = new FormData(form);
        const payload = Object.fromEntries(formData.entries());

        try {
            const res = await fetch('/api/order-pass', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();

            document.getElementById('orderModalFormContainer').style.display = 'none';
            const successContainer = document.getElementById('orderModalSuccessContainer');
            successContainer.style.display = 'block';

            if (data.order && data.order.activation_code) {
                const codeEl = document.getElementById('orderActivationCode');
                if (codeEl) codeEl.textContent = data.order.activation_code;
            }
        } catch (err) {
            document.getElementById('orderModalFormContainer').style.display = 'none';
            document.getElementById('orderModalSuccessContainer').style.display = 'block';
        } finally {
            if (btnText && btnLoader) {
                btnText.style.display = 'inline-block';
                btnLoader.style.display = 'none';
            }
        }
    });
}

/**
 * 4. FAQ Accordion
 */
function initFaqAccordion() {
    const items = document.querySelectorAll('.faq-item');

    items.forEach(item => {
        const btn = item.querySelector('.faq-question');
        const answer = item.querySelector('.faq-answer');

        if (!btn || !answer) return;

        btn.addEventListener('click', () => {
            const isActive = item.classList.contains('active');

            // Close all items
            items.forEach(otherItem => {
                otherItem.classList.remove('active');
                const otherBtn = otherItem.querySelector('.faq-question');
                const otherAns = otherItem.querySelector('.faq-answer');
                if (otherBtn) otherBtn.setAttribute('aria-expanded', 'false');
                if (otherAns) otherAns.style.maxHeight = '0';
            });

            // Toggle clicked item
            if (!isActive) {
                item.classList.add('active');
                btn.setAttribute('aria-expanded', 'true');
                answer.style.maxHeight = answer.scrollHeight + 30 + 'px';
            }
        });
    });
}

/**
 * 5. Mobile Navigation Drawer
 */
function initMobileNav() {
    const mobileBtn = document.getElementById('mobileMenuBtn');
    const closeBtn = document.getElementById('closeDrawerBtn');
    const drawer = document.getElementById('mobileDrawer');
    const backdrop = document.getElementById('drawerBackdrop');
    const navLinks = document.querySelectorAll('.mobile-nav-link');

    if (!mobileBtn || !drawer || !backdrop) return;

    function openDrawer() {
        drawer.classList.add('open');
        backdrop.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeDrawer() {
        drawer.classList.remove('open');
        backdrop.classList.remove('show');
        document.body.style.overflow = '';
    }

    mobileBtn.addEventListener('click', openDrawer);
    if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
    backdrop.addEventListener('click', closeDrawer);

    navLinks.forEach(link => {
        link.addEventListener('click', closeDrawer);
    });
}

/**
 * 6. Header Scroll Effect
 */
function initScrollHeader() {
    const header = document.getElementById('header');
    if (!header) return;

    window.addEventListener('scroll', () => {
        if (window.scrollY > 40) {
            header.style.background = 'rgba(8, 9, 13, 0.95)';
            header.style.boxShadow = '0 10px 30px rgba(0, 0, 0, 0.6)';
        } else {
            header.style.background = 'rgba(9, 10, 15, 0.85)';
            header.style.boxShadow = 'none';
        }
    });
}
