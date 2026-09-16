<?php
require_once __DIR__ . '/../includes/init.php';
Auth::requireRole('tutor');

$timetableModel = new Timetable();
$myClasses = $timetableModel->forTutor($_SESSION['user_id']);

$pageTitle = 'My Timetable';
$activeMenu = 'timetable';
require_once __DIR__ . '/../includes/dashboard_header.php';
?>

<div class="panel">
  <div class="panel-header"><h3>My Scheduled Classes (<?= count($myClasses) ?>)</h3></div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Subject</th><th>Date</th><th>Time</th><th>Classroom</th></tr></thead>
      <tbody>
        <?php if (!$myClasses): ?>
          <tr><td colspan="4" class="text-soft">No classes scheduled for you yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($myClasses as $t): ?>
          <tr>
            <td><span class="badge badge-brass"><?= e($t['subject']) ?></span></td>
            <td><?= e(date('D, d M Y', strtotime($t['class_date']))) ?></td>
            <td><?= e(date('h:i A', strtotime($t['start_time']))) ?> &ndash; <?= e(date('h:i A', strtotime($t['end_time']))) ?></td>
            <td><?= e($t['classroom'] ?: '-') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/dashboard_footer.php'; ?>
