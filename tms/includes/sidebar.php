<?php
/**
 * Role-aware sidebar. Expects $activeMenu (string) to be set by the page.
 * Auth::requireRole() must already have run before this is included.
 */
$role = $_SESSION['role'] ?? '';
$prefix = ''; // pages inside admin/student/tutor use relative "" since sidebar links stay within that folder, except dashboard root links.
$active = fn($key) => $activeMenu === $key ? 'active' : '';
?>
<aside class="sidebar">
  <div class="brand">
    <span class="logo-mark">T</span>
    <div>
      <div class="brand-title">Brightpath</div>
      <div class="brand-sub">Tuition Management</div>
    </div>
  </div>

  <nav>
    <?php if ($role === 'admin'): ?>
      <div class="nav-section-label">Overview</div>
      <a href="dashboard.php" class="nav-link <?= $active('dashboard') ?>"><span class="ic">&#9737;</span> Dashboard</a>

      <div class="nav-section-label">Management</div>
      <a href="students.php" class="nav-link <?= $active('students') ?>"><span class="ic">&#128101;</span> Students</a>
      <a href="tutors.php" class="nav-link <?= $active('tutors') ?>"><span class="ic">&#127891;</span> Tutors</a>
      <a href="timetable.php" class="nav-link <?= $active('timetable') ?>"><span class="ic">&#128197;</span> Timetable</a>
      <a href="attendance.php" class="nav-link <?= $active('attendance') ?>"><span class="ic">&#9989;</span> Attendance</a>
      <a href="payments.php" class="nav-link <?= $active('payments') ?>"><span class="ic">&#128176;</span> Payments</a>

      <div class="nav-section-label">Account</div>
      <a href="profile.php" class="nav-link <?= $active('profile') ?>"><span class="ic">&#128100;</span> My Profile</a>
      <a href="../logout.php" class="nav-link"><span class="ic">&#10143;</span> Logout</a>

    <?php elseif ($role === 'tutor'): ?>
      <div class="nav-section-label">Overview</div>
      <a href="dashboard.php" class="nav-link <?= $active('dashboard') ?>"><span class="ic">&#9737;</span> Dashboard</a>

      <div class="nav-section-label">My Work</div>
      <a href="timetable.php" class="nav-link <?= $active('timetable') ?>"><span class="ic">&#128197;</span> My Timetable</a>
      <a href="report.php" class="nav-link <?= $active('report') ?>"><span class="ic">&#128203;</span> Class Reports</a>

      <div class="nav-section-label">Account</div>
      <a href="profile.php" class="nav-link <?= $active('profile') ?>"><span class="ic">&#128100;</span> My Profile</a>
      <a href="../logout.php" class="nav-link"><span class="ic">&#10143;</span> Logout</a>

    <?php elseif ($role === 'student'): ?>
      <div class="nav-section-label">Overview</div>
      <a href="dashboard.php" class="nav-link <?= $active('dashboard') ?>"><span class="ic">&#9737;</span> Dashboard</a>

      <div class="nav-section-label">My Learning</div>
      <a href="attendance.php" class="nav-link <?= $active('attendance') ?>"><span class="ic">&#9989;</span> My Attendance</a>
      <a href="payment.php" class="nav-link <?= $active('payment') ?>"><span class="ic">&#128176;</span> My Payments</a>
      <a href="timetable.php" class="nav-link <?= $active('timetable') ?>"><span class="ic">&#128197;</span> Timetable</a>
      <a href="tutors.php" class="nav-link <?= $active('tutors') ?>"><span class="ic">&#127891;</span> Tutors</a>

      <div class="nav-section-label">Account</div>
      <a href="profile.php" class="nav-link <?= $active('profile') ?>"><span class="ic">&#128100;</span> My Profile</a>
      <a href="../logout.php" class="nav-link"><span class="ic">&#10143;</span> Logout</a>
    <?php endif; ?>
  </nav>

  <div class="user-box">
    <div class="avatar">
      <?php if (!empty($_SESSION['photo_path'])): ?>
        <img src="../<?= e($_SESSION['photo_path']) ?>" alt="">
      <?php else: ?>
        <?= e(strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1))) ?>
      <?php endif; ?>
    </div>
    <div>
      <div class="name"><?= e($_SESSION['user_name'] ?? '') ?></div>
      <div class="role"><?= e($role) ?></div>
    </div>
  </div>
</aside>
