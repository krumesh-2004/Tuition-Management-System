<?php
require_once __DIR__ . '/../includes/init.php';
Auth::requireRole('tutor');

$tutorModel = new Tutor();
$tutorId = $_SESSION['user_id'];
$tutor = $tutorModel->findById($tutorId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name'    => Validator::clean($_POST['name']),
        'email'   => Validator::clean($_POST['email']),
        'subject' => Validator::clean($_POST['subject']),
        'gender'  => $_POST['gender'] ?? 'Other',
        'phone'   => Validator::clean($_POST['phone'] ?? ''),
    ];
    if (!empty($_POST['password'])) $data['password'] = $_POST['password'];

    if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $filename = 'tutor_' . time() . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
            $dest = __DIR__ . '/../assets/uploads/tutors/' . $filename;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $dest)) {
                $data['photo_path'] = 'assets/uploads/tutors/' . $filename;
                $_SESSION['photo_path'] = $data['photo_path'];
            }
        }
    }

    $tutorModel->update($tutorId, $data);
    $_SESSION['user_name'] = $data['name'];
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
      <?php if (!empty($tutor['photo_path'])): ?>
        <img class="avatar-lg" src="../<?= e($tutor['photo_path']) ?>" alt="">
      <?php else: ?>
        <div class="avatar-lg" style="display:flex; align-items:center; justify-content:center; margin:0 auto; font-family:'Fraunces',serif; font-size:28px; color:var(--ink);"><?= e(strtoupper(substr($tutor['name'],0,1))) ?></div>
      <?php endif; ?>
      <h4 style="margin-top:14px;"><?= e($tutor['name']) ?></h4>
      <div class="text-soft" style="font-size:13px;"><?= e($tutor['email']) ?></div>
      <div style="margin-top:10px;"><span class="badge badge-brass"><?= e($tutor['subject']) ?></span></div>
    </div>
  </div>

  <div class="panel">
    <div class="panel-header"><h3>Edit Profile</h3></div>
    <div class="panel-body">
      <form method="POST" action="profile.php" enctype="multipart/form-data">
        <div class="form-row">
          <div class="form-group"><label>Full Name</label><input type="text" name="name" value="<?= e($tutor['name']) ?>" required></div>
          <div class="form-group"><label>Email</label><input type="email" name="email" value="<?= e($tutor['email']) ?>" required></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Subject</label><input type="text" name="subject" value="<?= e($tutor['subject']) ?>" required></div>
          <div class="form-group"><label>Phone</label><input type="tel" name="phone" value="<?= e($tutor['phone'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Gender</label>
            <select name="gender">
              <?php foreach (['Male', 'Female', 'Other'] as $g): ?>
                <option value="<?= $g ?>" <?= $tutor['gender'] === $g ? 'selected' : '' ?>><?= $g ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group"><label>Profile Photo</label><input type="file" name="photo" accept="image/*"></div>
        </div>
        <div class="form-group"><label>New Password (leave blank to keep unchanged)</label><input type="password" name="password"></div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/dashboard_footer.php'; ?>
