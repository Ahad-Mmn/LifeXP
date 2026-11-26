(function () {
    'use strict';

    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Counters rise from zero to their value
    document.querySelectorAll('[data-count]').forEach(function (el) {
        var target = parseInt(el.getAttribute('data-count'), 10);
        if (isNaN(target) || target <= 0 || reduceMotion) { return; }

        var start = null;
        var duration = 900;

        function step(timestamp) {
            if (start === null) { start = timestamp; }
            var progress = Math.min(1, (timestamp - start) / duration);
            el.textContent = Math.round(target * progress).toLocaleString();
            if (progress < 1) { window.requestAnimationFrame(step); }
        }
        window.requestAnimationFrame(step);
    });

    // Top players widget updates without reloading
    var form = document.getElementById('home-board-form');
    var box = document.getElementById('home-board-results');
    if (!form || !box) { return; }

    var controller = null;

    function load() {
        var params = new URLSearchParams(new FormData(form));
        var pageUrl = 'index.php?' + params.toString();
        params.set('page', 'home_board');

        if (controller) { controller.abort(); }
        controller = new AbortController();
        box.classList.add('loading');

        fetch('index.php?' + params.toString(), {
            headers: { 'X-Requested-With': 'fetch' },
            credentials: 'same-origin',
            signal: controller.signal
        })
        .then(function (res) {
            if (!res.ok) { throw new Error('Bad response'); }
            return res.text();
        })
        .then(function (html) {
            box.innerHTML = html;
            box.classList.remove('loading');
            history.replaceState(null, '', pageUrl);
        })
        .catch(function (err) {
            if (err.name === 'AbortError') { return; }
            box.classList.remove('loading');
        });
    }

    form.addEventListener('change', load);
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        load();
    });
})();