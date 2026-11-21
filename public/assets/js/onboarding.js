(function () {
    'use strict';

    document.querySelectorAll('[data-onboarding]').forEach(function (form) {
        var min = parseInt(form.getAttribute('data-min'), 10) || 0;
        var counter = form.querySelector('#pick-count');
        var submit = form.querySelector('[data-submit]');

        form.querySelectorAll('[data-accent]').forEach(function (el) {
            var colour = el.getAttribute('data-accent');
            if (/^#[0-9a-fA-F]{6}$/.test(colour)) {
                el.style.background = colour;
            }
        });

        function refresh() {
            var count = 0;
            form.querySelectorAll('input[type=checkbox]').forEach(function (box) {
                var label = box.closest('.pick');
                if (label) { label.classList.toggle('selected', box.checked); }
                if (box.checked) { count++; }
            });
            if (counter) { counter.textContent = String(count); }
            if (submit && min > 0) { submit.disabled = count < min; }
        }

        form.addEventListener('change', refresh);
        refresh();
    });
})();