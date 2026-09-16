<?php
require_once __DIR__ . '/includes/init.php';

// If already logged in, go straight to the right dashboard
if (Auth::isLoggedIn()) {
    header('Location: ' . Auth::dashboardUrlForRole($_SESSION['role']));
    exit;
}

$errors = [];
$old = ['email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
    $email = Validator::clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $old['email'] = $email;

    $v = (new Validator())->required(['email' => $email, 'password' => $password], ['email' => 'Email', 'password' => 'Password']);
    if ($v->fails()) {
        $errors[] = $v->first();
    } else {
        $result = Auth::attemptLogin($email, $password);
        if ($result['success']) {
            header('Location: ' . Auth::dashboardUrlForRole($result['role']));
            exit;
        } else {
            $errors[] = $result['message'];
        }
    }
}

$pageTitle = 'Home';
require_once __DIR__ . '/includes/public_header.php';
?>

<section class="hero">
  <div class="hero-inner">
    <h1>Run your tuition classes without the paperwork.</h1>
    <p class="lede">Brightpath brings student registration, attendance, fee payments, tutor management and timetables into one place &mdash; built for small and medium tuition institutes.</p>
    <div class="hero-actions">
      <a href="register.php" class="btn btn-brass">Register as a Student</a>
      <a href="#login" class="btn btn-outline" style="border-color:#fff;color:#fff;">Login</a>
    </div>
  </div>
</section>

<section class="section" id="login">
  <div style="display:grid; grid-template-columns: 1.1fr 1fr; gap: 46px; align-items:flex-start;">
    <div>
      <h2>Everything one class needs</h2>
      <p class="lede">A single system for admins, tutors and students &mdash; each with their own dashboard and permissions.</p>
      <div class="feature-grid">
        <div class="feature-card">
          <div class="feature-icon">&#128101;</div>
          <h4>Student Records</h4>
          <p>Registration with QR-code identity, profile, subjects and education history.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">&#9989;</div>
          <h4>QR Attendance</h4>
          <p>Mark attendance by scanning a student's QR code, reflected instantly on their profile.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">&#128176;</div>
          <h4>Fee Payments</h4>
          <p>Record monthly payments, track status, and notify parents automatically.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">&#128197;</div>
          <h4>Timetable</h4>
          <p>Admins schedule classes; students and tutors search and view them instantly.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">&#127891;</div>
          <h4>Tutor Management</h4>
          <p>Add, update and manage tutor profiles, subjects and daily class reports.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">&#128274;</div>
          <h4>Secure Access</h4>
          <p>Role-based dashboards with hashed passwords and session-based authentication.</p>
        </div>
      </div>
    </div>

    <div class="auth-card">
      <h2>Login</h2>
      <p class="lede">Admins, tutors and students all sign in here.</p>

      <?php foreach ($errors as $err): ?>
        <div class="alert alert-error"><?= e($err) ?></div>
      <?php endforeach; ?>

      <form method="POST" action="index.php#login">
        <div class="form-group">
          <label>Email address</label>
          <input type="email" name="email" value="<?= e($old['email']) ?>" placeholder="you@example.com" required>
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" required>
        </div>
        <button type="submit" name="login_submit" class="btn btn-primary btn-block">Login</button>
      </form>
      <div class="auth-foot">
        New student? <a href="register.php">Create an account</a>
      </div>
      <div class="auth-foot text-soft" style="margin-top:14px; font-size:12px;">
        Demo logins &mdash; Admin: admin@tms.com / admin123 &middot; Tutor: kasun@tms.com / tutor123 &middot; Student: amal@student.com / student123
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/public_footer.php'; ?>
