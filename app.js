(function () {
    'use strict';

    // ---------- Helpers ----------
    // textContent keeps server text from ever being read as HTML
    function toast(text, type) {
        var box = document.getElementById('toasts');
        if (!box) {
            box = document.createElement('div');
            box.id = 'toasts';
            document.body.append(box);
        }
        var t = document.createElement('div');
        t.className = 'toast toast-' + (type || 'success');
        t.textContent = text;
        box.append(t);
        setTimeout(function () {
            t.classList.add('toast-hide');
            setTimeout(function () { t.remove(); }, 400);
        }, 3500);
    }

    // Replace an element's contents with server-rendered HTML.
    // DOMPurify strips anything unsafe first; the result is inert nodes, so scripts never run.
    // If the sanitiser did not load, nothing is inserted.
    function setHtml(el, html) {
        if (!window.DOMPurify) { return; }
        var clean = window.DOMPurify.sanitize(html, {
            RETURN_DOM_FRAGMENT: true,
            SANITIZE_DOM: false
        });
        el.replaceChildren(clean);
    }

    window.LifeXP = { setHtml: setHtml };

    // Only plain colour values are accepted from data- attributes
    var SAFE_COLOUR = /^(#[0-9a-fA-F]{3,8}|var\(--[a-z0-9-]+\)|rgba?\([0-9., %]+\)|hsla?\([0-9., %deg]+\)|(linear|radial)-gradient\([#0-9a-zA-Z%.,() \-]*\))$/;
    function safeColour(value) {
        return (value && SAFE_COLOUR.test(value.trim())) ? value.trim() : null;
    }

    // Colours set from data- attributes, so templates need no inline style attributes
    function applyStyles(root) {
        root = root || document;
        root.querySelectorAll('[data-width]').forEach(function (el) {
            var w = parseInt(el.getAttribute('data-width'), 10);
            if (!isNaN(w)) { el.style.width = Math.max(0, Math.min(100, w)) + '%'; }
        });
        root.querySelectorAll('[data-bg]').forEach(function (el) {
            var c = safeColour(el.getAttribute('data-bg'));
            if (c) { el.style.setProperty('background', c); }
        });
        root.querySelectorAll('[data-color]').forEach(function (el) {
            var c = safeColour(el.getAttribute('data-color'));
            if (c) { el.style.setProperty('color', c); }
        });
        root.querySelectorAll('[data-border]').forEach(function (el) {
            var c = safeColour(el.getAttribute('data-border'));
            if (c) { el.style.setProperty('border-left-color', c); }
        });
    }
    applyStyles();

    // ---------- 1. Confirm before risky actions ----------
    document.addEventListener('submit', function (e) {
        var message = e.target.getAttribute && e.target.getAttribute('data-confirm');
        if (message && !window.confirm(message)) {
            e.preventDefault();
        }
    });

    // ---------- 2. Live search ----------
    var form = document.getElementById('filter-form');
    var results = document.getElementById('quest-results');

    if (form && results) {
        var timer = null;
        var controller = null;

        var runSearch = function () {
            var url = 'index.php?' + new URLSearchParams(new FormData(form)).toString();

            if (controller) { controller.abort(); }
            controller = new AbortController();
            results.classList.add('loading');

            fetch(url, {
                headers: { 'X-Requested-With': 'fetch' },
                credentials: 'same-origin',
                signal: controller.signal
            })
            .then(function (res) {
                if (res.redirected) { window.location.reload(); return null; }
                if (!res.ok) { throw new Error('Bad response'); }
                return res.text();
            })
            .then(function (html) {
                if (html === null) { return; }
                setHtml(results, html);
                applyStyles(results);
                results.classList.remove('loading');
            })
            .catch(function (err) {
                if (err.name === 'AbortError') { return; }
                results.classList.remove('loading');
                toast('Search failed. Please try again.', 'error');
            });
        };

        form.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(runSearch, 300);
        });
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            clearTimeout(timer);
            runSearch();
        });
        document.addEventListener('quests:refresh', runSearch);
    }

    // ---------- 3. Quest detail panel ----------
    var dialog = document.getElementById('quest-dialog');
    var dialogBody = document.getElementById('quest-dialog-body');

    if (dialog && dialogBody && typeof dialog.showModal === 'function') {
        var openQuest = function (url) {
            fetch(url, {
                headers: { 'X-Requested-With': 'fetch' },
                credentials: 'same-origin'
            })
            .then(function (res) {
                if (res.redirected) { window.location.reload(); return null; }
                if (!res.ok) { throw new Error('Bad response'); }
                return res.text();
            })
            .then(function (html) {
                if (html === null) { return; }
                setHtml(dialogBody, html);
                applyStyles(dialogBody);
                if (!dialog.open) { dialog.showModal(); }
            })
            .catch(function () { toast('Could not open that quest.', 'error'); });
        };

        document.addEventListener('click', function (e) {
            var link = e.target.closest ? e.target.closest('.js-open-quest') : null;
            if (link) {
                e.preventDefault();
                openQuest(link.getAttribute('href'));
                return;
            }
            if (e.target.closest && e.target.closest('[data-close-dialog]')) {
                dialog.close();
                return;
            }
            if (e.target === dialog) { dialog.close(); }   // click on the backdrop
        });
    }

    // ---------- 4. Complete a quest without reloading ----------
    document.addEventListener('submit', function (e) {
        var f = e.target;
        if (!f.classList || !f.classList.contains('js-complete')) { return; }
        if (e.defaultPrevented) { return; }
        e.preventDefault();

        var btn = f.querySelector('button[type=submit]');
        if (btn) { btn.disabled = true; }

        fetch('index.php?page=ajax_complete', {
            method: 'POST',
            body: new FormData(f),
            headers: { 'X-Requested-With': 'fetch' },
            credentials: 'same-origin'
        })
        .then(function (res) {
            return res.json().then(function (data) { return { status: res.status, data: data }; });
        })
        .then(function (r) {
            var d = r.data;
            if (!d.ok) {
                toast(d.message || 'Could not complete quest.', 'error');
                if (r.status === 401 || r.status === 409) {
                    setTimeout(function () { window.location.reload(); }, 1200);
                } else if (btn) {
                    btn.disabled = false;
                }
                return;
            }

            if (dialog && dialog.open) { dialog.close(); }

            var msg = d.earned > 0 ? '+' + d.earned + ' XP' : 'Quest complete';
            if (d.bonus_pct > 0 && d.earned > 0) { msg += ' (+' + d.bonus_pct + '% streak bonus)'; }
            msg += ' \u00B7 Streak: ' + d.streak;
            toast(msg, 'success');

            if (d.xp_note) { toast(d.xp_note, 'error'); }
            if (d.leveled_up) {
                toast('LEVEL UP! Level ' + d.new_level + ' - ' + d.new_title, 'levelup');
            }
            (d.new_badges || []).forEach(function (name) {
                toast('Badge unlocked: ' + name, 'levelup');
            });

            document.dispatchEvent(new CustomEvent('quests:refresh'));
        })
        .catch(function () {
            toast('Something went wrong. Please try again.', 'error');
            if (btn) { btn.disabled = false; }
        });
    });

    // ---------- 5. Username availability check ----------
    var nameInput = document.querySelector('[data-check-username]');
    if (nameInput) {
        var status = document.getElementById('username-status');
        var nameTimer = null;
        var seq = 0;

        nameInput.addEventListener('input', function () {
            clearTimeout(nameTimer);
            var value = nameInput.value.trim();
            if (value === '') {
                status.textContent = '';
                status.className = 'field-status';
                return;
            }
            nameTimer = setTimeout(function () {
                var mine = ++seq;
                fetch('index.php?page=ajax_username&u=' + encodeURIComponent(value), {
                    headers: { 'X-Requested-With': 'fetch' },
                    credentials: 'same-origin'
                })
                .then(function (res) { return res.json(); })
                .then(function (d) {
                    if (mine !== seq) { return; }
                    status.textContent = d.message;
                    status.className = 'field-status ' + (d.available ? 'ok' : 'bad');
                })
                .catch(function () { status.textContent = ''; });
            }, 400);
        });
    }

    // ---------- 6. Password strength meter (advisory only; the server enforces the rules) ----------
    var pwInput = document.querySelector('[data-strength]');
    var meter = document.getElementById('strength');
    if (pwInput && meter) {
        var labels = ['', 'Weak', 'Fair', 'Good', 'Strong'];
        var meterText = meter.querySelector('.strength-text');

        pwInput.addEventListener('input', function () {
            var p = pwInput.value;
            var level = 0;
            if (p.length > 0) {
                var s = 0;
                if (p.length >= 8) { s++; }
                if (p.length >= 12) { s++; }
                if (/[a-z]/.test(p) && /[A-Z]/.test(p)) { s++; }
                if (/\d/.test(p)) { s++; }
                if (/[^A-Za-z0-9]/.test(p)) { s++; }
                level = s <= 1 ? 1 : (s === 2 ? 2 : (s === 3 ? 3 : 4));
            }
            meter.setAttribute('data-level', String(level));
            meterText.textContent = labels[level];
        });
    }

        // ---------- Captcha image refresh ----------
    document.addEventListener('click', function (e) {
        var btn = e.target.closest ? e.target.closest('[data-captcha-refresh]') : null;
        var img = document.getElementById('captcha-img');
        if (btn && img) {
            img.src = 'index.php?page=captcha_image&new=1&r=' + Date.now();
        }
    });
})();
