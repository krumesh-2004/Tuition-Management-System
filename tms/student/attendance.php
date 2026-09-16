<?php
require_once __DIR__ . '/../includes/init.php';
Auth::requireRole('student');

$attendanceModel = new Attendance();
$records = $attendanceModel->forStudent($_SESSION['user_id']);
$total = count($records);
$present = count(array_filter($records, fn($r) => $r['status'] === 'Present'));
$rate = $total ? round(($present / $total) * 100) : 0;

$pageTitle = 'My Attendance';
$activeMenu = 'attendance';
require_once __DIR__ . '/../includes/dashboard_header.php';
?>

<div class="stat-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px,1fr));">
  <div class="stat-card c-teal"><div class="stat-bar"></div><div class="stat-label">Present</div><div class="stat-value"><?= $present ?></div></div>
  <div class="stat-card c-ink"><div class="stat-bar"></div><div class="stat-label">Total Records</div><div class="stat-value"><?= $total ?></div></div>
  <div class="stat-card c-brass"><div class="stat-bar"></div><div class="stat-label">Attendance Rate</div><div class="stat-value"><?= $rate ?>%</div></div>
</div>

<div class="panel">
  <div class="panel-header"><h3>Attendance History</h3></div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Date</th><th>Subject</th><th>Status</th></tr></thead>
      <tbody>
        <?php if (!$records): ?>
          <tr><td colspan="3" class="text-soft">No attendance records yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($records as $r): ?>
          <tr>
            <td><?= e(date('d M Y', strtotime($r['date']))) ?></td>
            <td><?= e($r['subject']) ?></td>
            <td>
              <?php $cls = $r['status'] === 'Present' ? 'badge-teal' : ($r['status'] === 'Late' ? 'badge-brass' : 'badge-rose'); ?>
              <span class="badge <?= $cls ?>"><?= e($r['status']) ?></span>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/dashboard_footer.php'; ?>
