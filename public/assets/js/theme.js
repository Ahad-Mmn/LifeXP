(function () {
    'use strict';
    var KEY = 'lifexp-theme';
    var theme = 'light';
    try { theme = localStorage.getItem(KEY) || 'light'; } catch (e) {}
    document.documentElement.setAttribute('data-theme', theme);

    document.addEventListener('DOMContentLoaded', function () {
        var btn = document.getElementById('theme-toggle');
        if (!btn) { return; }
        btn.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
        btn.addEventListener('click', function () {
            var next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            btn.setAttribute('aria-pressed', next === 'dark' ? 'true' : 'false');
            try { localStorage.setItem(KEY, next); } catch (e) {}
        });
    });
})();