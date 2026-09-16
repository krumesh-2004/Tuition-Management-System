<?php
require_once __DIR__ . '/../includes/init.php';
Auth::requireRole('admin');

$attendanceModel = new Attendance();
$studentModel = new Student();
$qrMessage = null;
$qrSuccess = false;

// Mark attendance by QR code / manual entry of QR value
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_qr'])) {
    $qr = Validator::clean($_POST['qr_code'] ?? '');
    $subject = Validator::clean($_POST['subject'] ?? '');
    $status = $_POST['status'] ?? 'Present';

    if ($qr === '' || $subject === '') {
        $qrMessage = 'Please provide both a QR code and subject.';
    } else {
        $result = $attendanceModel->markByQrCode($qr, $subject, $status);
        $qrMessage = $result['message'];
        $qrSuccess = $result['success'];
    }
}

// Manual attendance entry (by selecting a student)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_manual'])) {
    $attendanceModel->markManual(
        (int)$_POST['student_id'],
        $_POST['date'],
        Validator::clean($_POST['subject']),
        $_POST['status']
    );
    flash('success', 'Attendance recorded successfully.');
    header('Location: attendance.php');
    exit;
}

if (isset($_GET['delete'])) {
    $attendanceModel->delete((int)$_GET['delete']);
    flash('success', 'Attendance record removed.');
    header('Location: attendance.php');
    exit;
}

$records = $attendanceModel->all();
$students = $studentModel->all();

$pageTitle = 'Attendance';
$pageSubtitle = 'Mark and monitor student attendance';
$activeMenu = 'attendance';
require_once __DIR__ . '/../includes/dashboard_header.php';
?>

<div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:22px;">
  <div class="panel">
    <div class="panel-header"><h3>Mark by QR Code</h3></div>
    <div class="panel-body">
      <?php if ($qrMessage): ?>
        <div class="alert <?= $qrSuccess ? 'alert-success' : 'alert-error' ?>"><?= e($qrMessage) ?></div>
      <?php endif; ?>
      <form method="POST" action="attendance.php">
        <div class="form-group">
          <label>Student QR Code</label>
          <input type="text" name="qr_code" placeholder="e.g. QR-STU-0001" required autofocus>
          <div class="help-text">Scan or type the student's QR code value.</div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Subject</label><input type="text" name="subject" placeholder="e.g. Mathematics" required></div>
          <div class="form-group"><label>Status</label>
            <select name="status">
              <option value="Present">Present</option>
              <option value="Late">Late</option>
              <option value="Absent">Absent</option>
            </select>
          </div>
        </div>
        <button type="submit" name="mark_qr" class="btn btn-primary">Mark Attendance</button>
      </form>
    </div>
  </div>

  <div class="panel">
    <div class="panel-header"><h3>Mark Manually</h3></div>
    <div class="panel-body">
      <form method="POST" action="attendance.php">
        <div class="form-group">
          <label>Student</label>
          <select name="student_id" required>
            <option value="">Select Student</option>
            <?php foreach ($students as $s): ?>
              <option value="<?= $s['student_id'] ?>"><?= e($s['first_name'] . ' ' . $s['last_name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Date</label><input type="date" name="date" value="<?= date('Y-m-d') ?>" required></div>
          <div class="form-group"><label>Subject</label><input type="text" name="subject" required></div>
        </div>
        <div class="form-group"><label>Status</label>
          <select name="status">
            <option value="Present">Present</option>
            <option value="Late">Late</option>
            <option value="Absent">Absent</option>
          </select>
        </div>
        <button type="submit" name="mark_manual" class="btn btn-outline">Record Attendance</button>
      </form>
    </div>
  </div>
</div>

<div class="panel">
  <div class="panel-header"><h3>Attendance Records (<?= count($records) ?>)</h3></div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Student</th><th>Date</th><th>Subject</th><th>Status</th><th></th></tr></thead>
      <tbody>
        <?php if (!$records): ?>
          <tr><td colspan="5" class="text-soft">No attendance records yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($records as $r): ?>
          <tr>
            <td><?= e($r['first_name'] . ' ' . $r['last_name']) ?></td>
            <td><?= e(date('d M Y', strtotime($r['date']))) ?></td>
            <td><?= e($r['subject']) ?></td>
            <td>
              <?php $cls = $r['status'] === 'Present' ? 'badge-teal' : ($r['status'] === 'Late' ? 'badge-brass' : 'badge-rose'); ?>
              <span class="badge <?= $cls ?>"><?= e($r['status']) ?></span>
            </td>
            <td><a href="attendance.php?delete=<?= $r['attendance_id'] ?>" class="btn btn-danger btn-sm" data-confirm="Delete this record?">Delete</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/dashboard_footer.php'; ?>
