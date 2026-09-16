<?php
require_once __DIR__ . '/../includes/init.php';
Auth::requireRole('admin');

$adminModel = new Admin();
$stats = $adminModel->getStats();
$recent = $adminModel->recentActivity(6);

$pageTitle = 'Admin Dashboard';
$pageSubtitle = 'Overview of students, tutors, attendance and payments';
$activeMenu = 'dashboard';
require_once __DIR__ . '/../includes/dashboard_header.php';
?>

<div class="stat-grid">
  <div class="stat-card c-ink"><div class="stat-bar"></div>
    <div class="stat-label">Total Students</div>
    <div class="stat-value"><?= $stats['students'] ?></div>
  </div>
  <div class="stat-card c-brass"><div class="stat-bar"></div>
    <div class="stat-label">Total Tutors</div>
    <div class="stat-value"><?= $stats['tutors'] ?></div>
  </div>
  <div class="stat-card c-teal"><div class="stat-bar"></div>
    <div class="stat-label">Classes Today</div>
    <div class="stat-value"><?= $stats['classesToday'] ?></div>
  </div>
  <div class="stat-card c-rose"><div class="stat-bar"></div>
    <div class="stat-label">Pending Payments</div>
    <div class="stat-value"><?= $stats['pendingPay'] ?></div>
  </div>
  <div class="stat-card c-teal"><div class="stat-bar"></div>
    <div class="stat-label">Present Today</div>
    <div class="stat-value"><?= $stats['presentToday'] ?></div>
  </div>
  <div class="stat-card c-brass"><div class="stat-bar"></div>
    <div class="stat-label">Total Income Collected</div>
    <div class="stat-value">Rs. <?= number_format($stats['totalIncome'], 0) ?></div>
  </div>
</div>

<div style="display:grid; grid-template-columns: 1.3fr 1fr; gap:22px;">
  <div class="panel">
    <div class="panel-header">
      <h3>Recent Payments</h3>
      <a href="payments.php" class="btn btn-outline btn-sm">View All</a>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Student</th><th>Month</th><th>Amount</th><th>Date</th></tr></thead>
        <tbody>
          <?php if (!$recent): ?>
            <tr><td colspan="4" class="text-soft">No payments recorded yet.</td></tr>
          <?php endif; ?>
          <?php foreach ($recent as $r): ?>
            <tr>
              <td><?= e($r['first_name'] . ' ' . $r['last_name']) ?></td>
              <td><?= e($r['month']) ?></td>
              <td>Rs. <?= number_format($r['amount'], 2) ?></td>
              <td><?= e($r['payment_date'] ?? '-') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="panel">
    <div class="panel-header"><h3>Quick Actions</h3></div>
    <div class="panel-body" style="display:flex; flex-direction:column; gap:10px;">
      <a href="students.php?action=add" class="btn btn-primary btn-block">+ Add Student</a>
      <a href="tutors.php?action=add" class="btn btn-outline btn-block">+ Add Tutor</a>
      <a href="timetable.php?action=add" class="btn btn-outline btn-block">+ Schedule Class</a>
      <a href="attendance.php" class="btn btn-outline btn-block">Mark Attendance</a>
      <a href="payments.php?action=add" class="btn btn-outline btn-block">Record Payment</a>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/dashboard_footer.php'; ?>
