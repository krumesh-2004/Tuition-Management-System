<?php
require_once __DIR__ . '/../includes/init.php';
Auth::requireRole('tutor');

$tutorId = $_SESSION['user_id'];
$tutorModel = new Tutor();
$tutor = $tutorModel->findById($tutorId);

$timetableModel = new Timetable();
$myClasses = $timetableModel->forTutor($tutorId);
$upcoming = array_slice(array_filter($myClasses, fn($c) => $c['class_date'] >= date('Y-m-d')), 0, 5);

$reports = $tutorModel->getReports($tutorId);
$totalIncome = array_sum(array_column($reports, 'income'));

$pageTitle = 'Tutor Dashboard';
$pageSubtitle = 'Welcome back, ' . $tutor['name'];
$activeMenu = 'dashboard';
require_once __DIR__ . '/../includes/dashboard_header.php';
?>

<div class="stat-grid">
  <div class="stat-card c-brass"><div class="stat-bar"></div>
    <div class="stat-label">Subject</div>
    <div class="stat-value" style="font-size:20px;"><?= e($tutor['subject']) ?></div>
  </div>
  <div class="stat-card c-ink"><div class="stat-bar"></div>
    <div class="stat-label">Scheduled Classes</div>
    <div class="stat-value"><?= count($myClasses) ?></div>
  </div>
  <div class="stat-card c-teal"><div class="stat-bar"></div>
    <div class="stat-label">Total Income Reported</div>
    <div class="stat-value">Rs. <?= number_format($totalIncome, 0) ?></div>
  </div>
</div>

<div style="display:grid; grid-template-columns: 1fr 1fr; gap:22px;">
  <div class="panel">
    <div class="panel-header"><h3>Upcoming Classes</h3></div>
    <div class="panel-body">
      <?php if (!$upcoming): ?>
        <div class="text-soft">No upcoming classes scheduled.</div>
      <?php endif; ?>
      <?php foreach ($upcoming as $u): ?>
        <div class="flex-between" style="padding:10px 0; border-bottom:1px solid var(--line);">
          <div>
            <div style="font-weight:600;"><?= e($u['subject']) ?></div>
            <div class="text-soft" style="font-size:12.5px;"><?= e($u['classroom'] ?: '-') ?></div>
          </div>
          <div style="text-align:right;">
            <div style="font-size:13px;"><?= e(date('d M', strtotime($u['class_date']))) ?></div>
            <div class="text-soft" style="font-size:12px;"><?= e(date('h:i A', strtotime($u['start_time']))) ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="panel">
    <div class="panel-header"><h3>Quick Actions</h3></div>
    <div class="panel-body" style="display:flex; flex-direction:column; gap:10px;">
      <a href="report.php" class="btn btn-primary btn-block">+ Submit Class Report</a>
      <a href="timetable.php" class="btn btn-outline btn-block">View My Timetable</a>
      <a href="profile.php" class="btn btn-outline btn-block">Update My Profile</a>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/dashboard_footer.php'; ?>
