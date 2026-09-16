<?php
require_once __DIR__ . '/../includes/init.php';
Auth::requireRole('admin');

$tutorModel = new Tutor();
$action = $_GET['action'] ?? 'list';
$editId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editRow = null;

if ($action === 'delete' && $editId) {
    $tutorModel->delete($editId);
    flash('success', 'Tutor record deleted.');
    header('Location: tutors.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_tutor'])) {
    $data = [
        'name'    => Validator::clean($_POST['name']),
        'email'   => Validator::clean($_POST['email']),
        'subject' => Validator::clean($_POST['subject']),
        'gender'  => $_POST['gender'] ?? 'Other',
        'phone'   => Validator::clean($_POST['phone'] ?? ''),
        'status'  => $_POST['status'] ?? 'Active',
    ];
    if (!empty($_POST['password'])) $data['password'] = $_POST['password'];

    if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $filename = 'tutor_' . time() . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
            $dest = __DIR__ . '/../assets/uploads/tutors/' . $filename;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $dest)) {
                $data['photo_path'] = 'assets/uploads/tutors/' . $filename;
            }
        }
    }

    $postedId = (int)($_POST['tutor_id'] ?? 0);
    if ($postedId) {
        $tutorModel->update($postedId, $data);
        flash('success', 'Tutor updated successfully.');
    } else {
        $data['password'] = $data['password'] ?? bin2hex(random_bytes(4));
        $tutorModel->add($data);
        flash('success', 'Tutor added successfully.');
    }
    header('Location: tutors.php');
    exit;
}

if ($action === 'edit' && $editId) {
    $editRow = $tutorModel->findById($editId);
}

$search = Validator::clean($_GET['q'] ?? '');
$tutors = $tutorModel->all($search);

$pageTitle = 'Tutors';
$pageSubtitle = 'Manage tutor profiles and subjects';
$activeMenu = 'tutors';
require_once __DIR__ . '/../includes/dashboard_header.php';
?>

<?php if ($action === 'add' || $action === 'edit'): ?>
  <div class="panel">
    <div class="panel-header">
      <h3><?= $action === 'edit' ? 'Edit Tutor' : 'Add New Tutor' ?></h3>
      <a href="tutors.php" class="btn btn-outline btn-sm">Cancel</a>
    </div>
    <div class="panel-body">
      <form method="POST" action="tutors.php" enctype="multipart/form-data">
        <input type="hidden" name="tutor_id" value="<?= $editRow['tutor_id'] ?? '' ?>">
        <div class="form-row">
          <div class="form-group"><label>Full Name</label><input type="text" name="name" value="<?= e($editRow['name'] ?? '') ?>" required></div>
          <div class="form-group"><label>Email</label><input type="email" name="email" value="<?= e($editRow['email'] ?? '') ?>" required></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Subject</label><input type="text" name="subject" value="<?= e($editRow['subject'] ?? '') ?>" required></div>
          <div class="form-group"><label>Phone</label><input type="tel" name="phone" value="<?= e($editRow['phone'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Gender</label>
            <select name="gender">
              <?php foreach (['Male', 'Female', 'Other'] as $g): ?>
                <option value="<?= $g ?>" <?= ($editRow['gender'] ?? '') === $g ? 'selected' : '' ?>><?= $g ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group"><label>Status</label>
            <select name="status">
              <option value="Active" <?= ($editRow['status'] ?? 'Active') === 'Active' ? 'selected' : '' ?>>Active</option>
              <option value="Inactive" <?= ($editRow['status'] ?? '') === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Photo</label><input type="file" name="photo" accept="image/*"></div>
          <div class="form-group"><label>Password <?= $action === 'edit' ? '(leave blank to keep unchanged)' : '' ?></label><input type="password" name="password" <?= $action === 'edit' ? '' : 'required' ?>></div>
        </div>
        <button type="submit" name="save_tutor" class="btn btn-primary">Save Tutor</button>
      </form>
    </div>
  </div>
<?php endif; ?>

<div class="panel">
  <div class="panel-header">
    <h3>All Tutors (<?= count($tutors) ?>)</h3>
    <div class="flex-between">
      <form method="GET" action="tutors.php" class="search-box">
        <span class="ic">&#128269;</span>
        <input type="text" name="q" value="<?= e($search) ?>" placeholder="Search tutors...">
      </form>
      <a href="tutors.php?action=add" class="btn btn-brass btn-sm">+ Add Tutor</a>
    </div>
  </div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Tutor</th><th>Subject</th><th>Contact</th><th>Gender</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        <?php if (!$tutors): ?>
          <tr><td colspan="6" class="text-soft">No tutors found.</td></tr>
        <?php endif; ?>
        <?php foreach ($tutors as $t): ?>
          <tr>
            <td>
              <div style="display:flex; align-items:center; gap:10px;">
                <div class="avatar-sm" style="display:flex;align-items:center;justify-content:center;font-weight:600;color:var(--ink);">
                  <?php if (!empty($t['photo_path'])): ?><img class="avatar-sm" src="../<?= e($t['photo_path']) ?>" alt=""><?php else: ?><?= e(strtoupper(substr($t['name'],0,1))) ?><?php endif; ?>
                </div>
                <div>
                  <div style="font-weight:600;"><?= e($t['name']) ?></div>
                  <div class="text-soft" style="font-size:12px;"><?= e($t['email']) ?></div>
                </div>
              </div>
            </td>
            <td><span class="badge badge-brass"><?= e($t['subject']) ?></span></td>
            <td><?= e($t['phone'] ?: '-') ?></td>
            <td><?= e($t['gender']) ?></td>
            <td><span class="badge <?= $t['status'] === 'Active' ? 'badge-teal' : 'badge-rose' ?>"><?= e($t['status']) ?></span></td>
            <td>
              <a href="tutors.php?action=edit&id=<?= $t['tutor_id'] ?>" class="btn btn-outline btn-sm">Edit</a>
              <a href="tutors.php?action=delete&id=<?= $t['tutor_id'] ?>" class="btn btn-danger btn-sm" data-confirm="Delete this tutor?">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/dashboard_footer.php'; ?>
