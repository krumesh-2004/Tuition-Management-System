<?php
require_once __DIR__ . '/../includes/init.php';
Auth::requireRole('student');

$studentId = $_SESSION['user_id'];
$studentModel = new Student();
$student = $studentModel->findById($studentId);

$attendanceModel = new Attendance();
$attendance = $attendanceModel->forStudent($studentId);
$presentCount = count(array_filter($attendance, fn($a) => $a['status'] === 'Present'));

$paymentModel = new Payment();
$payments = $paymentModel->forStudent($studentId);
$pendingCount = count(array_filter($payments, fn($p) => $p['status'] !== 'Paid'));

$timetableModel = new Timetable();
$upcoming = array_slice(array_filter($timetableModel->all(), fn($t) => $t['class_date'] >= date('Y-m-d')), 0, 5);

$pageTitle = 'My Dashboard';
$pageSubtitle = 'Welcome back, ' . $student['first_name'] . '!';
$activeMenu = 'dashboard';
require_once __DIR__ . '/../includes/dashboard_header.php';
?>

<div class="stat-grid">
  <div class="stat-card c-teal"><div class="stat-bar"></div>
    <div class="stat-label">Days Present</div>
    <div class="stat-value"><?= $presentCount ?></div>
  </div>
  <div class="stat-card c-rose"><div class="stat-bar"></div>
    <div class="stat-label">Pending Payments</div>
    <div class="stat-value"><?= $pendingCount ?></div>
  </div>
  <div class="stat-card c-brass"><div class="stat-bar"></div>
    <div class="stat-label">Total Classes Recorded</div>
    <div class="stat-value"><?= count($attendance) ?></div>
  </div>
</div>

<div style="display:grid; grid-template-columns: 1.1fr 1fr; gap:22px;">
  <div class="panel">
    <div class="panel-header"><h3>My QR Code</h3></div>
    <div class="panel-body qr-box">
      <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=<?= urlencode($student['qr_code']) ?>" alt="QR Code">
      <div>
        <div class="text-soft" style="font-size:12px;">Show this to your tutor for attendance</div>
        <div style="font-family:'Fraunces',serif; font-size:18px; color:var(--ink); margin-top:4px;"><?= e($student['qr_code']) ?></div>
      </div>
    </div>
  </div>

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
            <div class="text-soft" style="font-size:12.5px;"><?= e($u['tutor_name']) ?> &middot; <?= e($u['classroom'] ?: '-') ?></div>
          </div>
          <div style="text-align:right;">
            <div style="font-size:13px;"><?= e(date('d M', strtotime($u['class_date']))) ?></div>
            <div class="text-soft" style="font-size:12px;"><?= e(date('h:i A', strtotime($u['start_time']))) ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/dashboard_footer.php'; ?>
