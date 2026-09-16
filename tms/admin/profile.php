<?php
require_once __DIR__ . '/../includes/init.php';
Auth::requireRole('admin');

$db = Database::getInstance();
$adminId = $_SESSION['user_id'];
$admin = $db->selectOne("SELECT * FROM admins WHERE admin_id = :id", ['id' => $adminId]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = Validator::clean($_POST['name']);
    $email = Validator::clean($_POST['email']);
    $params = ['name' => $name, 'email' => $email, 'id' => $adminId];
    $sql = "UPDATE admins SET name = :name, email = :email";

    if (!empty($_POST['password'])) {
        $sql .= ", password = :password";
        $params['password'] = User::hashPassword($_POST['password']);
    }

    if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $filename = 'admin_' . time() . '.' . $ext;
            $dest = __DIR__ . '/../assets/uploads/students/' . $filename;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $dest)) {
                $sql .= ", photo_path = :photo_path";
                $params['photo_path'] = 'assets/uploads/students/' . $filename;
                $_SESSION['photo_path'] = $params['photo_path'];
            }
        }
    }

    $sql .= " WHERE admin_id = :id";
    $db->execute($sql, $params);
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;
    flash('success', 'Profile updated successfully.');
    header('Location: profile.php');
    exit;
}

$pageTitle = 'My Profile';
$activeMenu = 'profile';
require_once __DIR__ . '/../includes/dashboard_header.php';
?>

<div class="panel" style="max-width:560px;">
  <div class="panel-header"><h3>Admin Profile</h3></div>
  <div class="panel-body">
    <div style="text-align:center; margin-bottom:20px;">
      <img class="avatar-lg" src="<?= !empty($admin['photo_path']) ? '../' . e($admin['photo_path']) : 'https://api.qrserver.com/v1/create-qr-code/?size=96x96&data=' . urlencode($admin['email']) ?>" alt="">
    </div>
    <form method="POST" action="profile.php" enctype="multipart/form-data">
      <div class="form-group"><label>Full Name</label><input type="text" name="name" value="<?= e($admin['name']) ?>" required></div>
      <div class="form-group"><label>Email</label><input type="email" name="email" value="<?= e($admin['email']) ?>" required></div>
      <div class="form-group"><label>New Password (leave blank to keep unchanged)</label><input type="password" name="password"></div>
      <div class="form-group"><label>Profile Photo</label><input type="file" name="photo" accept="image/*"></div>
      <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/dashboard_footer.php'; ?>
