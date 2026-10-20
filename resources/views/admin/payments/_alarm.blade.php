{{-- Alarme sonore : prévient l'administrateur dès qu'un client déclare un paiement. --}}
<div id="pay-alarm-bar" style="display:none; position:fixed; left:0; right:0; top:0; z-index:9999; background:#b1372f; color:#fff; padding:14px 18px; text-align:center; font-weight:800; font-size:1rem; box-shadow:0 6px 24px rgba(0,0,0,.3);">
    <span>💰 NOUVEAU PAIEMENT À VÉRIFIER</span>
    <a href="{{ route('admin.payments.index') }}" style="margin-left:14px; background:#fff; color:#b1372f; padding:7px 16px; border-radius:999px; font-weight:800;">Voir</a>
    <button type="button" id="pay-alarm-stop" style="margin-left:8px; background:transparent; color:#fff; border:1px solid rgba(255,255,255,.7); padding:7px 16px; border-radius:999px; font-weight:800; cursor:pointer;">Arrêter le son</button>
</div>

<button type="button" id="pay-alarm-pill" style="position:fixed; right:14px; bottom:14px; z-index:9998; border:1px solid var(--line); background:var(--card); color:var(--ink); padding:9px 14px; border-radius:999px; font-size:.8rem; font-weight:700; cursor:pointer; box-shadow:0 6px 20px rgba(0,0,0,.12);">
    🔔 Alarme paiements : <span id="pay-alarm-state">…</span>
</button>

<script>
(function () {
    const ALERT_URL = @json(route('admin.payments.alert'));
    const ON_PAYMENTS_PAGE = @json(request()->routeIs('admin.payments.index'));
    const KEY = 'mosnoky_pay_seen';
    const POLL_MS = 8000;

    const bar = document.getElementById('pay-alarm-bar');
    const stopBtn = document.getElementById('pay-alarm-stop');
    const pill = document.getElementById('pay-alarm-pill');
    const stateEl = document.getElementById('pay-alarm-state');

    let ctx = null;
    let alarmTimer = null;
    let titleTimer = null;
    let currentSignature = '';
    let firstPoll = true;
    const baseTitle = document.title;

    function getSeen() { try { return localStorage.getItem(KEY) || ''; } catch (e) { return ''; } }
    function setSeen(v) { try { localStorage.setItem(KEY, v); } catch (e) {} }

    // --- Son -------------------------------------------------------------
    // Les navigateurs interdisent le son tant que la personne n'a pas cliqué
    // sur la page : le premier clic/toucher "débloque" l'alarme.
    function unlock() {
        try {
            if (!ctx) { ctx = new (window.AudioContext || window.webkitAudioContext)(); }
            if (ctx.state === 'suspended') { ctx.resume(); }
        } catch (e) {}
        refreshState();
    }
    ['click', 'keydown', 'touchstart'].forEach(function (ev) {
        document.addEventListener(ev, unlock, { passive: true });
    });

    function soundReady() { return ctx && ctx.state === 'running'; }

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
        bar.style.display = 'block';
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
        bar.style.display = 'none';
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

            if (!d.pending) { stopAlarm(); return; }

            currentSignature = d.signature || '';
            if (currentSignature === getSeen()) { return; }

            if (ON_PAYMENTS_PAGE) {
                // Déjà sur la page : pas de grosse alarme, on marque comme vu.
                setSeen(currentSignature);
                if (!firstPoll) {
                    siren();
                    setTimeout(function () { location.reload(); }, 1500);
                }
                return;
            }
            startAlarm();
        } catch (e) { /* réseau coupé : on réessaie au prochain tour */ }
        finally { firstPoll = false; }
    }

    // --- Bouton d'état ---------------------------------------------------
    function refreshState() {
        stateEl.textContent = soundReady() ? 'activée ✅' : 'cliquez ici pour activer le son 🔇';
    }
    pill.addEventListener('click', function () {
        unlock();
        siren(); // petit test pour vérifier que le son fonctionne
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
