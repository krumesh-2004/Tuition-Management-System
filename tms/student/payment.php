<?php
require_once __DIR__ . '/../includes/init.php';
Auth::requireRole('student');

$paymentModel = new Payment();
$payments = $paymentModel->forStudent($_SESSION['user_id']);

$notifier = new Notification();
$notifications = $notifier->forStudent($_SESSION['user_id']);

$pageTitle = 'My Payments';
$activeMenu = 'payment';
require_once __DIR__ . '/../includes/dashboard_header.php';
?>

<div class="panel">
  <div class="panel-header"><h3>Payment History</h3></div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Month</th><th>Amount</th><th>Date</th><th>Status</th><th>Notes</th></tr></thead>
      <tbody>
        <?php if (!$payments): ?>
          <tr><td colspan="5" class="text-soft">No payment records yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($payments as $p): ?>
          <tr>
            <td><?= e($p['month']) ?></td>
            <td>Rs. <?= number_format($p['amount'], 2) ?></td>
            <td><?= e($p['payment_date'] ?? '-') ?></td>
            <td>
              <?php $cls = $p['status'] === 'Paid' ? 'badge-teal' : ($p['status'] === 'Pending' ? 'badge-brass' : 'badge-rose'); ?>
              <span class="badge <?= $cls ?>"><?= e($p['status']) ?></span>
            </td>
            <td class="text-soft"><?= e($p['notes'] ?: '-') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="panel">
  <div class="panel-header"><h3>Notifications Sent to Parent</h3></div>
  <div class="panel-body">
    <?php if (!$notifications): ?>
      <div class="text-soft">No notifications sent yet.</div>
    <?php endif; ?>
    <?php foreach ($notifications as $n): ?>
      <div style="padding:10px 0; border-bottom:1px solid var(--line);">
        <div style="font-size:13.5px;"><?= e($n['message']) ?></div>
        <div class="text-soft" style="font-size:12px; margin-top:3px;">via <?= e($n['channel']) ?> &middot; <?= e(date('d M Y, h:i A', strtotime($n['sent_at']))) ?></div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/dashboard_footer.php'; ?>
