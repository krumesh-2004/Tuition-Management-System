<?php
require_once __DIR__ . '/../includes/init.php';
Auth::requireRole('tutor');

$tutorModel = new Tutor();
$tutorId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_report'])) {
    $v = (new Validator())->required(
        [
            'report_date'    => $_POST['report_date'] ?? '',
            'subject'        => $_POST['subject'] ?? '',
            'students_count' => $_POST['students_count'] ?? '',
            'income'         => $_POST['income'] ?? '',
        ],
        [
            'report_date'    => 'Date',
            'subject'        => 'Subject',
            'students_count' => 'Students count',
            'income'         => 'Income',
        ]
    )->numeric(['students_count' => $_POST['students_count'] ?? ''], 'students_count', 'Students count')
     ->numeric(['income' => $_POST['income'] ?? ''], 'income', 'Income');

    if ($v->fails()) {
        flash('error', $v->first());
    } else {
        $tutorModel->submitReport($tutorId, [
            'report_date'    => $_POST['report_date'],
            'subject'        => Validator::clean($_POST['subject']),
            'students_count' => (int)$_POST['students_count'],
            'income'         => (float)$_POST['income'],
            'notes'          => Validator::clean($_POST['notes'] ?? ''),
        ]);
        flash('success', 'Class report submitted successfully.');
    }
    header('Location: report.php');
    exit;
}

$reports = $tutorModel->getReports($tutorId);
$totalIncome = array_sum(array_column($reports, 'income'));
$totalStudents = array_sum(array_column($reports, 'students_count'));

$pageTitle = 'Class Reports';
$pageSubtitle = 'Daily class card and income report (FR-14)';
$activeMenu = 'report';
require_once __DIR__ . '/../includes/dashboard_header.php';
?>

<div class="stat-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px,1fr));">
  <div class="stat-card c-teal"><div class="stat-bar"></div>
    <div class="stat-label">Total Income Reported</div>
    <div class="stat-value">Rs. <?= number_format($totalIncome, 0) ?></div>
  </div>
  <div class="stat-card c-brass"><div class="stat-bar"></div>
    <div class="stat-label">Total Student Attendances</div>
    <div class="stat-value"><?= (int)$totalStudents ?></div>
  </div>
  <div class="stat-card c-ink"><div class="stat-bar"></div>
    <div class="stat-label">Reports Submitted</div>
    <div class="stat-value"><?= count($reports) ?></div>
  </div>
</div>

<div class="panel">
  <div class="panel-header"><h3>Submit Daily Class Report</h3></div>
  <div class="panel-body">
    <form method="POST" action="report.php">
      <div class="form-row">
        <div class="form-group"><label>Date</label><input type="date" name="report_date" value="<?= date('Y-m-d') ?>" required></div>
        <div class="form-group"><label>Subject</label><input type="text" name="subject" required></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Students Count</label><input type="number" name="students_count" min="0" required></div>
        <div class="form-group"><label>Income (Rs.)</label><input type="number" step="0.01" name="income" min="0" required></div>
      </div>
      <div class="form-group"><label>Notes</label><input type="text" name="notes" placeholder="e.g. Practical session included"></div>
      <button type="submit" name="save_report" class="btn btn-primary">Submit Report</button>
    </form>
  </div>
</div>

<div class="panel">
  <div class="panel-header"><h3>My Report History</h3></div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Date</th><th>Subject</th><th>Students</th><th>Income</th><th>Notes</th></tr></thead>
      <tbody>
        <?php if (!$reports): ?>
          <tr><td colspan="5" class="text-soft">No reports submitted yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($reports as $r): ?>
          <tr>
            <td><?= e(date('d M Y', strtotime($r['report_date']))) ?></td>
            <td><span class="badge badge-brass"><?= e($r['subject']) ?></span></td>
            <td><?= (int)$r['students_count'] ?></td>
            <td>Rs. <?= number_format($r['income'], 2) ?></td>
            <td class="text-soft"><?= e($r['notes'] ?: '-') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/dashboard_footer.php'; ?>
