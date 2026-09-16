<?php
require_once __DIR__ . '/../includes/init.php';
Auth::requireRole('admin');

$studentModel = new Student();
$action = $_GET['action'] ?? 'list';
$editId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editRow = null;

// Handle delete
if ($action === 'delete' && $editId) {
    $studentModel->delete($editId);
    flash('success', 'Student record deleted.');
    header('Location: students.php');
    exit;
}

// Handle create/update submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_student'])) {
    $data = [
        'first_name'   => Validator::clean($_POST['first_name']),
        'last_name'    => Validator::clean($_POST['last_name']),
        'email'        => Validator::clean($_POST['email']),
        'phone'        => Validator::clean($_POST['phone'] ?? ''),
        'birthday'     => $_POST['birthday'] ?? null,
        'parent_name'  => Validator::clean($_POST['parent_name'] ?? ''),
        'parent_phone' => Validator::clean($_POST['parent_phone'] ?? ''),
        'stream'       => Validator::clean($_POST['stream'] ?? ''),
        'subject1'     => Validator::clean($_POST['subject1'] ?? ''),
        'subject2'     => Validator::clean($_POST['subject2'] ?? ''),
        'subject3'     => Validator::clean($_POST['subject3'] ?? ''),
        'al_education' => Validator::clean($_POST['al_education'] ?? ''),
        'status'       => $_POST['status'] ?? 'Active',
    ];
    if (!empty($_POST['password'])) $data['password'] = $_POST['password'];

    // photo upload
    if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $filename = 'student_' . time() . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
            $dest = __DIR__ . '/../assets/uploads/students/' . $filename;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $dest)) {
                $data['photo_path'] = 'assets/uploads/students/' . $filename;
            }
        }
    }

    $postedId = (int)($_POST['student_id'] ?? 0);
    if ($postedId) {
        $studentModel->update($postedId, $data);
        flash('success', 'Student updated successfully.');
    } else {
        $data['password'] = $data['password'] ?? bin2hex(random_bytes(4));
        $studentModel->register($data);
        flash('success', 'Student added successfully.');
    }
    header('Location: students.php');
    exit;
}

if ($action === 'edit' && $editId) {
    $editRow = $studentModel->findById($editId);
}

$search = Validator::clean($_GET['q'] ?? '');
$students = $studentModel->all($search);

$pageTitle = 'Students';
$pageSubtitle = 'Manage student registration and records';
$activeMenu = 'students';
require_once __DIR__ . '/../includes/dashboard_header.php';
?>

<?php if ($action === 'add' || $action === 'edit'): ?>
  <div class="panel">
    <div class="panel-header">
      <h3><?= $action === 'edit' ? 'Edit Student' : 'Add New Student' ?></h3>
      <a href="students.php" class="btn btn-outline btn-sm">Cancel</a>
    </div>
    <div class="panel-body">
      <form method="POST" action="students.php" enctype="multipart/form-data">
        <input type="hidden" name="student_id" value="<?= $editRow['student_id'] ?? '' ?>">
        <div class="form-row">
          <div class="form-group"><label>First Name</label><input type="text" name="first_name" value="<?= e($editRow['first_name'] ?? '') ?>" required></div>
          <div class="form-group"><label>Last Name</label><input type="text" name="last_name" value="<?= e($editRow['last_name'] ?? '') ?>" required></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Email</label><input type="email" name="email" value="<?= e($editRow['email'] ?? '') ?>" required></div>
          <div class="form-group"><label>Phone</label><input type="tel" name="phone" value="<?= e($editRow['phone'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Birthday</label><input type="date" name="birthday" value="<?= e($editRow['birthday'] ?? '') ?>"></div>
          <div class="form-group"><label>Status</label>
            <select name="status">
              <option value="Active" <?= ($editRow['status'] ?? 'Active') === 'Active' ? 'selected' : '' ?>>Active</option>
              <option value="Inactive" <?= ($editRow['status'] ?? '') === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Parent Name</label><input type="text" name="parent_name" value="<?= e($editRow['parent_name'] ?? '') ?>"></div>
          <div class="form-group"><label>Parent Phone</label><input type="tel" name="parent_phone" value="<?= e($editRow['parent_phone'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Stream</label><input type="text" name="stream" value="<?= e($editRow['stream'] ?? '') ?>"></div>
          <div class="form-group"><label>A/L Education</label><input type="text" name="al_education" value="<?= e($editRow['al_education'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Subject 1</label><input type="text" name="subject1" value="<?= e($editRow['subject1'] ?? '') ?>"></div>
          <div class="form-group"><label>Subject 2</label><input type="text" name="subject2" value="<?= e($editRow['subject2'] ?? '') ?>"></div>
          <div class="form-group"><label>Subject 3</label><input type="text" name="subject3" value="<?= e($editRow['subject3'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Photo</label><input type="file" name="photo" accept="image/*"></div>
          <div class="form-group"><label>Password <?= $action === 'edit' ? '(leave blank to keep unchanged)' : '' ?></label><input type="password" name="password" <?= $action === 'edit' ? '' : 'required' ?>></div>
        </div>
        <button type="submit" name="save_student" class="btn btn-primary">Save Student</button>
      </form>
    </div>
  </div>
<?php endif; ?>

<div class="panel">
  <div class="panel-header">
    <h3>All Students (<?= count($students) ?>)</h3>
    <div class="flex-between">
      <form method="GET" action="students.php" class="search-box">
        <span class="ic">&#128269;</span>
        <input type="text" name="q" value="<?= e($search) ?>" placeholder="Search students...">
      </form>
      <a href="students.php?action=add" class="btn btn-brass btn-sm">+ Add Student</a>
    </div>
  </div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Student</th><th>Contact</th><th>Stream / Subjects</th><th>QR Code</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        <?php if (!$students): ?>
          <tr><td colspan="6" class="text-soft">No students found.</td></tr>
        <?php endif; ?>
        <?php foreach ($students as $s): ?>
          <tr>
            <td>
              <div style="display:flex; align-items:center; gap:10px;">
                <img class="avatar-sm" src="<?= !empty($s['photo_path']) ? '../' . e($s['photo_path']) : 'https://api.qrserver.com/v1/create-qr-code/?size=36x36&data=' . urlencode($s['qr_code']) ?>" alt="">
                <div>
                  <div style="font-weight:600;"><?= e($s['first_name'] . ' ' . $s['last_name']) ?></div>
                  <div class="text-soft" style="font-size:12px;"><?= e($s['email']) ?></div>
                </div>
              </div>
            </td>
            <td><?= e($s['phone'] ?: '-') ?></td>
            <td><?= e($s['stream'] ?: '-') ?><br><span class="text-soft" style="font-size:12px;"><?= e(implode(', ', array_filter([$s['subject1'], $s['subject2'], $s['subject3']]))) ?></span></td>
            <td><span class="badge badge-ink"><?= e($s['qr_code']) ?></span></td>
            <td><span class="badge <?= $s['status'] === 'Active' ? 'badge-teal' : 'badge-rose' ?>"><?= e($s['status']) ?></span></td>
            <td>
              <a href="students.php?action=edit&id=<?= $s['student_id'] ?>" class="btn btn-outline btn-sm">Edit</a>
              <a href="students.php?action=delete&id=<?= $s['student_id'] ?>" class="btn btn-danger btn-sm" data-confirm="Delete this student record?">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/dashboard_footer.php'; ?>
