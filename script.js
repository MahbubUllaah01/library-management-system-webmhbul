// Confirm before destructive actions
document.querySelectorAll('[data-confirm]').forEach(function (btn) {
  btn.addEventListener('click', function (e) {
    if (!confirm(btn.getAttribute('data-confirm'))) e.preventDefault();
  });
});
// Client-side validation: trim inputs and show browser messages on submit
document.querySelectorAll('form[data-validate]').forEach(function (form) {
  form.addEventListener('submit', function (e) {
    form.querySelectorAll('input[type=text],input:not([type])').forEach(function (i) { i.value = i.value.trim(); });
    if (!form.checkValidity()) { e.preventDefault(); form.reportValidity(); }
  });
});
// Auto-hide status messages
setTimeout(function () {
  document.querySelectorAll('.note').forEach(function (n) { n.style.display = 'none'; });
}, 5000);
