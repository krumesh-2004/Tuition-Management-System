// Tuition Management System - small UI interactions

document.addEventListener('DOMContentLoaded', function () {
  // Mobile sidebar toggle
  var toggle = document.getElementById('sidebarToggle');
  var sidebar = document.querySelector('.sidebar');
  if (toggle && sidebar) {
    toggle.addEventListener('click', function () {
      sidebar.classList.toggle('open');
    });
  }

  // Auto-dismiss alerts after 5 seconds
  document.querySelectorAll('.alert[data-autohide]').forEach(function (el) {
    setTimeout(function () {
      el.style.transition = 'opacity .4s ease';
      el.style.opacity = '0';
      setTimeout(function () { el.remove(); }, 400);
    }, 5000);
  });

  // Confirm before delete actions
  document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('click', function (e) {
      var msg = el.getAttribute('data-confirm') || 'Are you sure?';
      if (!confirm(msg)) e.preventDefault();
    });
  });

  // Simple client-side table search (data-search-target points to a table id)
  document.querySelectorAll('[data-live-search]').forEach(function (input) {
    var targetId = input.getAttribute('data-live-search');
    var table = document.getElementById(targetId);
    if (!table) return;
    input.addEventListener('keyup', function () {
      var q = input.value.toLowerCase();
      table.querySelectorAll('tbody tr').forEach(function (row) {
        row.style.display = row.textContent.toLowerCase().indexOf(q) > -1 ? '' : 'none';
      });
    });
  });
});
