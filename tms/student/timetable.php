<?php
require_once __DIR__ . '/../includes/init.php';
Auth::requireRole('student');

$timetableModel = new Timetable();
$search = Validator::clean($_GET['q'] ?? '');
$entries = $timetableModel->all($search);

$pageTitle = 'Class Timetable';
$activeMenu = 'timetable';
require_once __DIR__ . '/../includes/dashboard_header.php';
?>

<div class="panel">
  <div class="panel-header">
    <h3>Timetable</h3>
    <form method="GET" action="timetable.php" class="search-box">
      <span class="ic">&#128269;</span>
      <input type="text" name="q" value="<?= e($search) ?>" placeholder="Search by subject or tutor...">
    </form>
  </div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Subject</th><th>Tutor</th><th>Date</th><th>Time</th><th>Classroom</th></tr></thead>
      <tbody>
        <?php if (!$entries): ?>
          <tr><td colspan="5" class="text-soft">No matching timetable entries found.</td></tr>
        <?php endif; ?>
        <?php foreach ($entries as $t): ?>
          <tr>
            <td><span class="badge badge-brass"><?= e($t['subject']) ?></span></td>
            <td><?= e($t['tutor_name']) ?></td>
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
