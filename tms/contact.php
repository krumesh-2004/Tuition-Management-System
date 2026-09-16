<?php
require_once __DIR__ . '/includes/init.php';

$sent = false;
$errors = [];
$old = ['name' => '', 'email' => '', 'subject' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = [
        'name'    => Validator::clean($_POST['name'] ?? ''),
        'email'   => Validator::clean($_POST['email'] ?? ''),
        'subject' => Validator::clean($_POST['subject'] ?? ''),
        'message' => Validator::clean($_POST['message'] ?? ''),
    ];

    $v = (new Validator())
        ->required($old, ['name' => 'Name', 'email' => 'Email', 'message' => 'Message'])
        ->email($old, 'email');

    if ($v->fails()) {
        $errors[] = $v->first();
    } else {
        $db = Database::getInstance();
        $db->insert(
            "INSERT INTO contact_messages (name, email, subject, message) VALUES (:name, :email, :subject, :message)",
            $old
        );
        $sent = true;
        $old = ['name' => '', 'email' => '', 'subject' => '', 'message' => ''];
    }
}

$pageTitle = 'Contact';
require_once __DIR__ . '/includes/public_header.php';
?>
<section class="section">
  <h2>Contact Us</h2>
  <p class="lede">Have a question about enrolling or how the system works? Send us a message below.</p>

  <div style="max-width:560px;">
    <?php if ($sent): ?>
      <div class="alert alert-success">Thank you &mdash; your message has been received. We'll get back to you soon.</div>
    <?php endif; ?>
    <?php foreach ($errors as $err): ?>
      <div class="alert alert-error"><?= e($err) ?></div>
    <?php endforeach; ?>

    <form method="POST" action="contact.php">
      <div class="form-row">
        <div class="form-group">
          <label>Your Name</label>
          <input type="text" name="name" value="<?= e($old['name']) ?>" required>
        </div>
        <div class="form-group">
          <label>Your Email</label>
          <input type="email" name="email" value="<?= e($old['email']) ?>" required>
        </div>
      </div>
      <div class="form-group">
        <label>Subject</label>
        <input type="text" name="subject" value="<?= e($old['subject']) ?>">
      </div>
      <div class="form-group">
        <label>Message</label>
        <textarea name="message" rows="5" required><?= e($old['message']) ?></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Send Message</button>
    </form>
  </div>
</section>
<?php require_once __DIR__ . '/includes/public_footer.php'; ?>
