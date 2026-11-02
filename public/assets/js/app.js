// Ask for confirmation before submitting any form that has data-confirm
document.addEventListener('submit', function (e) {
    var message = e.target.getAttribute('data-confirm');
    if (message && !window.confirm(message)) {
        e.preventDefault();
    }
});