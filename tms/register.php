<?php
require_once __DIR__ . '/includes/init.php';

$errors = [];
$success = null;
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = [
        'first_name'   => Validator::clean($_POST['first_name'] ?? ''),
        'last_name'    => Validator::clean($_POST['last_name'] ?? ''),
        'email'        => Validator::clean($_POST['email'] ?? ''),
        'phone'        => Validator::clean($_POST['phone'] ?? ''),
        'birthday'     => $_POST['birthday'] ?? '',
        'parent_name'  => Validator::clean($_POST['parent_name'] ?? ''),
        'parent_phone' => Validator::clean($_POST['parent_phone'] ?? ''),
        'stream'       => Validator::clean($_POST['stream'] ?? ''),
        'subject1'     => Validator::clean($_POST['subject1'] ?? ''),
        'subject2'     => Validator::clean($_POST['subject2'] ?? ''),
        'subject3'     => Validator::clean($_POST['subject3'] ?? ''),
        'al_education'  => Validator::clean($_POST['al_education'] ?? ''),
    ];
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    $v = (new Validator())
        ->required($old, ['first_name' => 'First name', 'last_name' => 'Last name', 'email' => 'Email'])
        ->email($old, 'email')
        ->required(['password' => $password], ['password' => 'Password'])
        ->minLength(['password' => $password], 'password', 6, 'Password')
        ->passwordsMatch(['password' => $password, 'confirm_password' => $confirm], 'password', 'confirm_password');

    $studentModel = new Student();
    if (!$v->fails() && $studentModel->findByEmail($old['email'])) {
        $errors[] = 'An account with this email already exists.';
    } elseif ($v->fails()) {
        $errors[] = $v->first();
    } else {
        $photoPath = null;
        if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                $filename = 'student_' . time() . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
                $dest = __DIR__ . '/assets/uploads/students/' . $filename;
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $dest)) {
                    $photoPath = 'assets/uploads/students/' . $filename;
                }
            }
        }

        $data = array_merge($old, ['password' => $password, 'photo_path' => $photoPath]);
        $id = $studentModel->register($data);
        $student = $studentModel->findById($id);
        $success = $student;
    }
}

$pageTitle = 'Student Registration';
require_once __DIR__ . '/includes/public_header.php';
?>
<section class="section">
  <?php if ($success): ?>
    <div class="auth-card" style="max-width:520px; margin:0 auto;">
      <h2>Registration successful!</h2>
      <p class="lede">Welcome, <?= e($success['first_name']) ?>. Your account has been created. Use your QR code below for attendance, and log in with your email and password.</p>
      <div class="qr-box">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=<?= urlencode($success['qr_code']) ?>" alt="QR Code">
        <div>
          <div class="text-soft" style="font-size:12px;">QR CODE ID</div>
          <div style="font-family:'Fraunces',serif; font-size:18px; color:var(--ink);"><?= e($success['qr_code']) ?></div>
        </div>
      </div>
      <a href="index.php" class="btn btn-primary btn-block" style="margin-top:20px;">Go to Login</a>
    </div>
  <?php else: ?>
    <h2>Student Registration</h2>
    <p class="lede">Create your student account to view attendance, payments, timetables and tutor details.</p>

    <?php foreach ($errors as $err): ?>
      <div class="alert alert-error"><?= e($err) ?></div>
    <?php endforeach; ?>

    <form method="POST" action="register.php" enctype="multipart/form-data" class="panel" style="max-width:760px;">
      <div class="panel-body">
        <h3 class="mt-0" style="margin-bottom:16px;">Personal Details</h3>
        <div class="form-row">
          <div class="form-group">
            <label>First Name</label>
            <input type="text" name="first_name" value="<?= e($old['first_name'] ?? '') ?>" required>
          </div>
          <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="last_name" value="<?= e($old['last_name'] ?? '') ?>" required>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?= e($old['email'] ?? '') ?>" required>
          </div>
          <div class="form-group">
            <label>Phone</label>
            <input type="tel" name="phone" value="<?= e($old['phone'] ?? '') ?>">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Birthday</label>
            <input type="date" name="birthday" value="<?= e($old['birthday'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Profile Photo</label>
            <input type="file" name="photo" accept="image/*">
          </div>
        </div>

        <h3 style="margin:20px 0 16px;">Parent / Guardian</h3>
        <div class="form-row">
          <div class="form-group">
            <label>Parent Name</label>
            <input type="text" name="parent_name" value="<?= e($old['parent_name'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Parent Phone (for SMS notifications)</label>
            <input type="tel" name="parent_phone" value="<?= e($old['parent_phone'] ?? '') ?>">
          </div>
        </div>

        <h3 style="margin:20px 0 16px;">Academic Details</h3>
        <div class="form-row">
          <div class="form-group">
            <label>Stream</label>
            <select name="stream">
              <option value="">Select Stream</option>
              <?php foreach (['Physical Science', 'Biological Science', 'Commerce', 'Arts', 'Technology'] as $s): ?>
                <option value="<?= $s ?>" <?= ($old['stream'] ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>A/L Education Info</label>
            <input type="text" name="al_education" value="<?= e($old['al_education'] ?? '') ?>" placeholder="e.g. A/L 2026">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Subject 1</label>
            <input type="text" name="subject1" value="<?= e($old['subject1'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Subject 2</label>
            <input type="text" name="subject2" value="<?= e($old['subject2'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Subject 3</label>
            <input type="text" name="subject3" value="<?= e($old['subject3'] ?? '') ?>">
          </div>
        </div>

        <h3 style="margin:20px 0 16px;">Account Security</h3>
        <div class="form-row">
          <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
          </div>
          <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" required>
          </div>
        </div>

        <button type="submit" class="btn btn-primary">Create Account</button>
        <a href="index.php" class="btn btn-outline">Cancel</a>
      </div>
    </form>
  <?php endif; ?>
</section>
<?php require_once __DIR__ . '/includes/public_footer.php'; ?>
