(function () {
    'use strict';

    var form = document.getElementById('board-form');
    var box = document.getElementById('board-results');
    if (!form || !box) { return; }

    var controller = null;

    function load() {
        var url = 'index.php?' + new URLSearchParams(new FormData(form)).toString();

        if (controller) { controller.abort(); }
        controller = new AbortController();
        box.classList.add('loading');

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
            window.LifeXP.setHtml(box, html);
            box.classList.remove('loading');
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
