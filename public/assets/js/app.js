(function () {
    'use strict';

    // ---------- Helpers ----------
    // textContent (not innerHTML) so server text can never inject HTML
    function toast(text, type) {
        var box = document.getElementById('toasts');
        if (!box) {
            box = document.createElement('div');
            box.id = 'toasts';
            document.body.appendChild(box);
        }
        var t = document.createElement('div');
        t.className = 'toast toast-' + (type || 'success');
        t.textContent = text;
        box.appendChild(t);
        setTimeout(function () {
            t.classList.add('toast-hide');
            setTimeout(function () { t.remove(); }, 400);
        }, 3500);
    }

    // ---------- 1. Confirm before delete (from Step 4) ----------
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

            if (controller) { controller.abort(); }   // cancel the previous request
            controller = new AbortController();
            results.classList.add('loading');

            fetch(url, {
                headers: { 'X-Requested-With': 'fetch' },
                credentials: 'same-origin',
                signal: controller.signal
            })
            .then(function (res) {
                if (res.redirected) { window.location.reload(); return null; }   // session expired
                if (!res.ok) { throw new Error('Bad response'); }
                return res.text();
            })
            .then(function (html) {
                if (html === null) { return; }
                results.innerHTML = html;               // HTML already escaped by Twig
                results.classList.remove('loading');
                history.replaceState(null, '', url);    // keep the URL shareable
            })
            .catch(function (err) {
                if (err.name === 'AbortError') { return; }
                results.classList.remove('loading');
                toast('Search failed. Please try again.', 'error');
            });
        };

        form.addEventListener('input', function () {   // fires for text, selects and dates
            clearTimeout(timer);
            timer = setTimeout(runSearch, 300);         // wait until typing pauses
        });
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            clearTimeout(timer);
            runSearch();
        });
    }

    // ---------- 3. Complete a quest without reloading ----------
    function markDone(card) {
        card.classList.add('quest-done');
        var actions = card.querySelector('.quest-actions');
        var completeForm = actions.querySelector('.js-complete');
        var editLink = actions.querySelector('a.btn');
        if (completeForm) { completeForm.remove(); }
        if (editLink) { editLink.remove(); }
        var tag = document.createElement('span');
        tag.className = 'tag tag-done';
        tag.textContent = 'Completed';
        actions.insertBefore(tag, actions.firstChild);
    }

    // Event delegation: still works after live search replaces the list
    document.addEventListener('submit', function (e) {
        var f = e.target;
        if (!f.classList || !f.classList.contains('js-complete')) { return; }
        e.preventDefault();

        var btn = f.querySelector('button');
        btn.disabled = true;

        fetch('index.php?page=ajax_complete', {
            method: 'POST',
            body: new FormData(f),                      // includes the CSRF token
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
                } else {
                    btn.disabled = false;
                }
                return;
            }

            markDone(f.closest('.quest'));

            var msg = '+' + d.earned + ' XP';
            if (d.bonus_pct > 0) { msg += ' (+' + d.bonus_pct + '% streak bonus)'; }
            msg += ' \u00B7 Streak: ' + d.streak;
            toast(msg, 'success');

            if (d.leveled_up) {
                toast('LEVEL UP! Level ' + d.new_level + ' - ' + d.new_title, 'levelup');
            }

            (d.new_badges || []).forEach(function (name) {
                toast('Badge unlocked: ' + name, 'levelup');
            });
        })
        .catch(function () {
            toast('Something went wrong. Please try again.', 'error');
            btn.disabled = false;
        });
    });

    // ---------- 4. Username availability check ----------
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
                    if (mine !== seq) { return; }       // ignore out-of-date answers
                    status.textContent = d.message;
                    status.className = 'field-status ' + (d.available ? 'ok' : 'bad');
                })
                .catch(function () { status.textContent = ''; });
            }, 400);
        });
    }
        // ---------- 5. Password strength meter (advisory only; the server enforces the rules) ----------
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
            meter.setAttribute('data-level', String(level));   // CSS draws the bar (no inline styles)
            meterText.textContent = labels[level];
        });
    }
})();