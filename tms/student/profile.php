<?php
require_once __DIR__ . '/../includes/init.php';
Auth::requireRole('student');

$studentModel = new Student();
$studentId = $_SESSION['user_id'];
$student = $studentModel->findById($studentId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    ];
    if (!empty($_POST['password'])) $data['password'] = $_POST['password'];

    if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $filename = 'student_' . time() . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
            $dest = __DIR__ . '/../assets/uploads/students/' . $filename;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $dest)) {
                $data['photo_path'] = 'assets/uploads/students/' . $filename;
                $_SESSION['photo_path'] = $data['photo_path'];
            }
        }
    }

    $studentModel->update($studentId, $data);
    $_SESSION['user_name'] = $data['first_name'] . ' ' . $data['last_name'];
    flash('success', 'Profile updated successfully.');
    header('Location: profile.php');
    exit;
}

$pageTitle = 'My Profile';
$activeMenu = 'profile';
require_once __DIR__ . '/../includes/dashboard_header.php';
?>

<div style="display:grid; grid-template-columns: 1fr 1.6fr; gap:22px;">
  <div class="panel">
    <div class="panel-body" style="text-align:center;">
      <img class="avatar-lg" src="<?= !empty($student['photo_path']) ? '../' . e($student['photo_path']) : 'https://api.qrserver.com/v1/create-qr-code/?size=96x96&data=' . urlencode($student['email']) ?>" alt="">
      <h4 style="margin-top:14px;"><?= e($student['first_name'] . ' ' . $student['last_name']) ?></h4>
      <div class="text-soft" style="font-size:13px;"><?= e($student['email']) ?></div>
      <div style="margin-top:14px;">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=110x110&data=<?= urlencode($student['qr_code']) ?>" alt="QR" style="border:1px solid var(--line); border-radius:8px; padding:6px;">
        <div class="text-soft" style="font-size:12px; margin-top:6px;"><?= e($student['qr_code']) ?></div>
      </div>
    </div>
  </div>

  <div class="panel">
    <div class="panel-header"><h3>Edit Profile</h3></div>
    <div class="panel-body">
      <form method="POST" action="profile.php" enctype="multipart/form-data">
        <div class="form-row">
          <div class="form-group"><label>First Name</label><input type="text" name="first_name" value="<?= e($student['first_name']) ?>" required></div>
          <div class="form-group"><label>Last Name</label><input type="text" name="last_name" value="<?= e($student['last_name']) ?>" required></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Email</label><input type="email" name="email" value="<?= e($student['email']) ?>" required></div>
          <div class="form-group"><label>Phone</label><input type="tel" name="phone" value="<?= e($student['phone'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Birthday</label><input type="date" name="birthday" value="<?= e($student['birthday'] ?? '') ?>"></div>
          <div class="form-group"><label>Profile Photo</label><input type="file" name="photo" accept="image/*"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Parent Name</label><input type="text" name="parent_name" value="<?= e($student['parent_name'] ?? '') ?>"></div>
          <div class="form-group"><label>Parent Phone</label><input type="tel" name="parent_phone" value="<?= e($student['parent_phone'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Stream</label><input type="text" name="stream" value="<?= e($student['stream'] ?? '') ?>"></div>
          <div class="form-group"><label>A/L Education</label><input type="text" name="al_education" value="<?= e($student['al_education'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Subject 1</label><input type="text" name="subject1" value="<?= e($student['subject1'] ?? '') ?>"></div>
          <div class="form-group"><label>Subject 2</label><input type="text" name="subject2" value="<?= e($student['subject2'] ?? '') ?>"></div>
          <div class="form-group"><label>Subject 3</label><input type="text" name="subject3" value="<?= e($student['subject3'] ?? '') ?>"></div>
        </div>
        <div class="form-group"><label>New Password (leave blank to keep unchanged)</label><input type="password" name="password"></div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/dashboard_footer.php'; ?>
