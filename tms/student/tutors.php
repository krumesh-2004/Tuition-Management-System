<?php
require_once __DIR__ . '/../includes/init.php';
Auth::requireRole('student');

$tutorModel = new Tutor();
$search = Validator::clean($_GET['q'] ?? '');
$tutors = $tutorModel->all($search);

$pageTitle = 'Our Tutors';
$activeMenu = 'tutors';
require_once __DIR__ . '/../includes/dashboard_header.php';
?>

<div class="panel">
  <div class="panel-header">
    <h3>Search Tutors</h3>
    <form method="GET" action="tutors.php" class="search-box">
      <span class="ic">&#128269;</span>
      <input type="text" name="q" value="<?= e($search) ?>" placeholder="Search by name or subject...">
    </form>
  </div>
</div>

<div class="stat-grid" style="grid-template-columns: repeat(auto-fit, minmax(240px,1fr));">
  <?php if (!$tutors): ?>
    <div class="empty-state" style="grid-column: 1/-1;">
      <div class="ic">&#128269;</div>
      No tutors found matching "<?= e($search) ?>".
    </div>
  <?php endif; ?>
  <?php foreach ($tutors as $t): ?>
    <div class="panel" style="margin-bottom:0;">
      <div class="panel-body" style="text-align:center;">
        <?php if (!empty($t['photo_path'])): ?>
          <img class="avatar-lg" src="../<?= e($t['photo_path']) ?>" alt="">
        <?php else: ?>
          <div class="avatar-lg" style="display:flex; align-items:center; justify-content:center; margin:0 auto; font-family:'Fraunces',serif; font-size:28px; color:var(--ink);"><?= e(strtoupper(substr($t['name'],0,1))) ?></div>
        <?php endif; ?>
        <h4 style="margin-top:14px; margin-bottom:2px;"><?= e($t['name']) ?></h4>
        <div class="text-soft" style="font-size:13px; margin-bottom:8px;"><?= e($t['email']) ?></div>
        <span class="badge badge-brass"><?= e($t['subject']) ?></span>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/../includes/dashboard_footer.php'; ?>
