{{-- Alarme sonore : prévient l'administrateur dès qu'un client déclare un paiement. --}}
<div id="pay-alarm-bar" class="pay-alarm-bar" role="alert" aria-live="assertive" hidden>
    <div class="pay-alarm-message">
        <span class="pay-alarm-icon" aria-hidden="true">💳</span>
        <span>
            <strong><span id="pay-alarm-count">0</span> <span id="pay-alarm-count-label">paiements à vérifier</span></strong>
            <small>Contrôlez le montant et la référence avant de confirmer.</small>
        </span>
    </div>
    <div class="pay-alarm-actions">
        <a href="{{ route('admin.payments.index') }}" class="pay-alarm-view">Vérifier maintenant <span aria-hidden="true">→</span></a>
        <button type="button" id="pay-alarm-stop" class="pay-alarm-mute">Couper le son</button>
    </div>
</div>

<button type="button" id="pay-alarm-pill" class="pay-alarm-pill" aria-label="Activer et tester l’alarme sonore des paiements">
    <span class="pay-alarm-pill-icon" aria-hidden="true">🔔</span>
    <span class="pay-alarm-pill-copy"><strong>Alarme de paiement</strong><small id="pay-alarm-state">Activation du son requise</small></span>
    <span id="pay-alarm-pill-count" class="pay-alarm-pill-count" hidden>0</span>
</button>

<style>
    .pay-alarm-bar[hidden], .pay-alarm-pill-count[hidden] { display: none !important; }
    .pay-alarm-bar { position: fixed; z-index: 9999; inset: 0 0 auto; display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 14px max(18px, calc((100vw - 1180px) / 2)); background: #9d211f; color: #fff; box-shadow: 0 8px 28px rgba(70, 14, 12, .35); }
    .pay-alarm-message, .pay-alarm-actions { display: flex; align-items: center; gap: 12px; }
    .pay-alarm-icon { display: grid; width: 42px; height: 42px; flex: none; place-items: center; border-radius: 50%; background: rgba(255, 255, 255, .16); font-size: 1.25rem; }
    .pay-alarm-message strong, .pay-alarm-message small { display: block; }
    .pay-alarm-message strong { font-size: 1rem; }
    .pay-alarm-message small { margin-top: 3px; color: rgba(255, 255, 255, .88); font-size: .8rem; }
    .pay-alarm-view, .pay-alarm-mute { display: inline-flex; min-height: 42px; align-items: center; justify-content: center; border-radius: 999px; padding: 9px 16px; font: inherit; font-size: .84rem; font-weight: 800; white-space: nowrap; }
    .pay-alarm-view { background: #fff; color: #8d201d; }
    .pay-alarm-mute { border: 1px solid rgba(255, 255, 255, .72); background: transparent; color: #fff; cursor: pointer; }
    .pay-alarm-pill { position: fixed; right: 16px; bottom: 16px; z-index: 9998; display: flex; align-items: center; gap: 10px; min-height: 58px; max-width: min(360px, calc(100vw - 32px)); border: 0; border-radius: 14px; padding: 10px 16px; background: #9d211f; color: #fff; text-align: left; cursor: pointer; box-shadow: 0 8px 24px rgba(70, 14, 12, .28); }
    .pay-alarm-pill:hover { background: #811b19; }
    .pay-alarm-pill-icon { font-size: 1.35rem; }
    .pay-alarm-pill-copy { min-width: 0; }
    .pay-alarm-pill-copy strong, .pay-alarm-pill-copy small { display: block; }
    .pay-alarm-pill-copy strong { font-size: .84rem; }
    .pay-alarm-pill-copy small { margin-top: 2px; color: rgba(255, 255, 255, .84); font-size: .72rem; }
    .pay-alarm-pill-count { display: grid; min-width: 24px; height: 24px; place-items: center; margin-left: auto; border-radius: 999px; background: #fff; color: #8d201d; font-size: .75rem; font-weight: 900; }
    @media (max-width: 620px) {
        .pay-alarm-bar { align-items: flex-start; flex-direction: column; gap: 10px; padding: 12px 14px; }
        .pay-alarm-message small { font-size: .74rem; }
        .pay-alarm-actions { width: 100%; }
        .pay-alarm-view, .pay-alarm-mute { flex: 1; min-height: 40px; padding: 8px 10px; font-size: .76rem; }
        .pay-alarm-pill { right: 10px; bottom: 10px; min-height: 52px; padding: 8px 12px; }
    }
    @media (prefers-reduced-motion: no-preference) {
        .pay-alarm-bar:not([hidden]) .pay-alarm-icon { animation: pay-alarm-pulse 1.3s ease-in-out infinite; }
    }
    @keyframes pay-alarm-pulse { 50% { transform: scale(1.08); } }
</style>

<script>
(function () {
    const ALERT_URL = @json(route('admin.payments.alert'));
    const KEY = 'mosnoky_pay_seen';
    const POLL_MS = 8000;

    const bar = document.getElementById('pay-alarm-bar');
    const countEl = document.getElementById('pay-alarm-count');
    const countLabelEl = document.getElementById('pay-alarm-count-label');
    const pillCountEl = document.getElementById('pay-alarm-pill-count');
    const stopBtn = document.getElementById('pay-alarm-stop');
    const pill = document.getElementById('pay-alarm-pill');
    const stateEl = document.getElementById('pay-alarm-state');
    const navPaymentCounts = document.querySelectorAll('[data-admin-payment-count]');

    let ctx = null;
    let alarmTimer = null;
    let titleTimer = null;
    let currentSignature = '';
    const baseTitle = document.title;

    function getSeen() { try { return localStorage.getItem(KEY) || ''; } catch (e) { return ''; } }
    function setSeen(v) { try { localStorage.setItem(KEY, v); } catch (e) {} }

    // --- Son -------------------------------------------------------------
    // Les navigateurs interdisent le son tant que la personne n'a pas cliqué
    // sur la page : le premier clic/toucher "débloque" l'alarme.
    function unlock() {
        try {
            if (!ctx) { ctx = new (window.AudioContext || window.webkitAudioContext)(); }
            const resume = ctx.state === 'suspended' ? ctx.resume() : Promise.resolve();

            return resume.then(function () { refreshState(); }).catch(function () { refreshState(); });
        } catch (e) {
            refreshState();

            return Promise.resolve();
        }
    }
    ['click', 'keydown', 'touchstart'].forEach(function (ev) {
        document.addEventListener(ev, unlock, { passive: true });
    });

    function soundReady() { return ctx && ctx.state === 'running'; }

    function updatePendingCount(value) {
        const pending = Math.max(0, Number(value) || 0);
        const hasPending = pending > 0;

        countEl.textContent = pending;
        countLabelEl.textContent = pending === 1 ? 'paiement à vérifier' : 'paiements à vérifier';
        pillCountEl.textContent = pending;
        pillCountEl.hidden = !hasPending;
        bar.hidden = !hasPending;
        navPaymentCounts.forEach(function (badge) {
            badge.textContent = pending;
            badge.hidden = !hasPending;
        });
    }

    // Sirène à deux tons, très audible (le volume dépend du volume de l'appareil)
    function siren() {
        if (!soundReady()) { return; }
        const t0 = ctx.currentTime;
        for (let i = 0; i < 4; i++) {
            const o = ctx.createOscillator();
            const g = ctx.createGain();
            o.type = 'square';
            o.frequency.value = (i % 2 === 0) ? 900 : 650;
            g.gain.setValueAtTime(0.0001, t0 + i * 0.3);
            g.gain.exponentialRampToValueAtTime(0.9, t0 + i * 0.3 + 0.02);
            g.gain.setValueAtTime(0.9, t0 + i * 0.3 + 0.24);
            g.gain.exponentialRampToValueAtTime(0.0001, t0 + i * 0.3 + 0.29);
            o.connect(g);
            g.connect(ctx.destination);
            o.start(t0 + i * 0.3);
            o.stop(t0 + i * 0.3 + 0.3);
        }
        if (navigator.vibrate) { navigator.vibrate([400, 150, 400, 150, 400]); }
    }

    // --- Alarme ----------------------------------------------------------
    function startAlarm() {
        if (alarmTimer) { return; }
        siren();
        alarmTimer = setInterval(siren, 2500);

        let on = false;
        titleTimer = setInterval(function () {
            on = !on;
            document.title = on ? '💰 PAIEMENT À VÉRIFIER' : baseTitle;
        }, 900);

        try {
            if ('Notification' in window && Notification.permission === 'granted') {
                new Notification('Mosnoky : paiement à vérifier', {
                    body: 'Un client vient de déclarer un paiement.',
                    tag: 'mosnoky-payment',
                    requireInteraction: true,
                });
            }
        } catch (e) { /* certains téléphones ne permettent pas ce type de notification */ }
    }

    function stopAlarm() {
        clearInterval(alarmTimer); alarmTimer = null;
        clearInterval(titleTimer); titleTimer = null;
        document.title = baseTitle;
    }

    stopBtn.addEventListener('click', function () {
        setSeen(currentSignature);
        stopAlarm();
    });

    // --- Interrogation du serveur ---------------------------------------
    async function poll() {
        try {
            const r = await fetch(ALERT_URL, {
                headers: { 'Accept': 'application/json' },
                cache: 'no-store',
                credentials: 'same-origin',
            });
            if (!r.ok) { return; }
            const d = await r.json();
            updatePendingCount(d.pending);

            if (!d.pending) { stopAlarm(); return; }

            currentSignature = d.signature || '';
            if (currentSignature === getSeen()) { return; }
            startAlarm();
        } catch (e) { /* réseau coupé : on réessaie au prochain tour */ }
    }

    // --- Bouton d'état ---------------------------------------------------
    function refreshState() {
        stateEl.textContent = soundReady() ? 'Son activé · toucher pour tester' : 'Touchez pour activer le son';
    }
    pill.addEventListener('click', function () {
        unlock().then(siren);
        try {
            if ('Notification' in window && Notification.permission === 'default') {
                Notification.requestPermission();
            }
        } catch (e) {}
    });

    refreshState();
    poll();
    setInterval(poll, POLL_MS);
})();
</script>
