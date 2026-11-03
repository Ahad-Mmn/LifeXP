(function () {
    'use strict';
    if (typeof Chart === 'undefined') { return; }   // library failed to load: page still works

    Chart.defaults.color = '#cbd5e1';
    Chart.defaults.borderColor = '#334155';

    function read(el, key) {
        try { return JSON.parse(el.dataset[key]); } catch (e) { return []; }
    }

    var week = document.getElementById('chart-week');
    if (week) {
        new Chart(week, {
            type: 'bar',
            data: {
                labels: read(week, 'labels'),
                datasets: [{
                    label: 'XP earned',
                    data: read(week, 'values'),
                    backgroundColor: '#6366f1',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });
    }

    var cat = document.getElementById('chart-cat');
    if (cat) {
        new Chart(cat, {
            type: 'doughnut',
            data: {
                labels: read(cat, 'labels'),
                datasets: [{
                    data: read(cat, 'values'),
                    backgroundColor: read(cat, 'colors'),
                    borderColor: '#1e293b'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }
})();