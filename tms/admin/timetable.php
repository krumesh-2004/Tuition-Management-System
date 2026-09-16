<?php
require_once __DIR__ . '/../includes/init.php';
Auth::requireRole('admin');

$timetableModel = new Timetable();
$tutorModel = new Tutor();
$action = $_GET['action'] ?? 'list';
$editId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editRow = null;

if ($action === 'delete' && $editId) {
    $timetableModel->delete($editId);
    flash('success', 'Timetable entry removed.');
    header('Location: timetable.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_timetable'])) {
    $data = [
        'tutor_id'   => (int)$_POST['tutor_id'],
        'subject'    => Validator::clean($_POST['subject']),
        'class_date' => $_POST['class_date'],
        'start_time' => $_POST['start_time'],
        'end_time'   => $_POST['end_time'],
        'classroom'  => Validator::clean($_POST['classroom'] ?? ''),
    ];
    $postedId = (int)($_POST['timetable_id'] ?? 0);
    if ($postedId) {
        $timetableModel->update($postedId, $data);
        flash('success', 'Timetable updated successfully.');
    } else {
        $timetableModel->add($data);
        flash('success', 'Class scheduled successfully.');
    }
    header('Location: timetable.php');
    exit;
}

if ($action === 'edit' && $editId) {
    $editRow = $timetableModel->findById($editId);
}

$search = Validator::clean($_GET['q'] ?? '');
$entries = $timetableModel->all($search);
$tutors = $tutorModel->all();

$pageTitle = 'Timetable';
$pageSubtitle = 'Schedule and manage class timings';
$activeMenu = 'timetable';
require_once __DIR__ . '/../includes/dashboard_header.php';
?>

<?php if ($action === 'add' || $action === 'edit'): ?>
  <div class="panel">
    <div class="panel-header">
      <h3><?= $action === 'edit' ? 'Edit Class' : 'Schedule New Class' ?></h3>
      <a href="timetable.php" class="btn btn-outline btn-sm">Cancel</a>
    </div>
    <div class="panel-body">
      <form method="POST" action="timetable.php">
        <input type="hidden" name="timetable_id" value="<?= $editRow['timetable_id'] ?? '' ?>">
        <div class="form-row">
          <div class="form-group"><label>Tutor</label>
            <select name="tutor_id" required>
              <option value="">Select Tutor</option>
              <?php foreach ($tutors as $t): ?>
                <option value="<?= $t['tutor_id'] ?>" <?= (($editRow['tutor_id'] ?? 0) == $t['tutor_id']) ? 'selected' : '' ?>><?= e($t['name']) ?> (<?= e($t['subject']) ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group"><label>Subject</label><input type="text" name="subject" value="<?= e($editRow['subject'] ?? '') ?>" required></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Class Date</label><input type="date" name="class_date" value="<?= e($editRow['class_date'] ?? '') ?>" required></div>
          <div class="form-group"><label>Classroom</label><input type="text" name="classroom" value="<?= e($editRow['classroom'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Start Time</label><input type="time" name="start_time" value="<?= e($editRow['start_time'] ?? '') ?>" required></div>
          <div class="form-group"><label>End Time</label><input type="time" name="end_time" value="<?= e($editRow['end_time'] ?? '') ?>" required></div>
        </div>
        <button type="submit" name="save_timetable" class="btn btn-primary">Save Class</button>
      </form>
    </div>
  </div>
<?php endif; ?>

<div class="panel">
  <div class="panel-header">
    <h3>Class Schedule (<?= count($entries) ?>)</h3>
    <div class="flex-between">
      <form method="GET" action="timetable.php" class="search-box">
        <span class="ic">&#128269;</span>
        <input type="text" name="q" value="<?= e($search) ?>" placeholder="Search by subject/tutor...">
      </form>
      <a href="timetable.php?action=add" class="btn btn-brass btn-sm">+ Schedule Class</a>
    </div>
  </div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Subject</th><th>Tutor</th><th>Date</th><th>Time</th><th>Classroom</th><th>Actions</th></tr></thead>
      <tbody>
        <?php if (!$entries): ?>
          <tr><td colspan="6" class="text-soft">No classes scheduled.</td></tr>
        <?php endif; ?>
        <?php foreach ($entries as $t): ?>
          <tr>
            <td><span class="badge badge-brass"><?= e($t['subject']) ?></span></td>
            <td><?= e($t['tutor_name']) ?></td>
            <td><?= e(date('D, d M Y', strtotime($t['class_date']))) ?></td>
            <td><?= e(date('h:i A', strtotime($t['start_time']))) ?> &ndash; <?= e(date('h:i A', strtotime($t['end_time']))) ?></td>
            <td><?= e($t['classroom'] ?: '-') ?></td>
            <td>
              <a href="timetable.php?action=edit&id=<?= $t['timetable_id'] ?>" class="btn btn-outline btn-sm">Edit</a>
              <a href="timetable.php?action=delete&id=<?= $t['timetable_id'] ?>" class="btn btn-danger btn-sm" data-confirm="Remove this class?">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/dashboard_footer.php'; ?>
