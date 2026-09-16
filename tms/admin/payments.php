<?php
require_once __DIR__ . '/../includes/init.php';
Auth::requireRole('admin');

$paymentModel = new Payment();
$studentModel = new Student();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_payment'])) {
    $paymentModel->record([
        'student_id'   => (int)$_POST['student_id'],
        'amount'       => (float)$_POST['amount'],
        'month'        => Validator::clean($_POST['month']),
        'payment_date' => $_POST['payment_date'],
        'status'       => $_POST['status'],
        'notes'        => Validator::clean($_POST['notes'] ?? ''),
    ]);
    flash('success', 'Payment recorded' . ($_POST['status'] === 'Paid' ? ' and parent notified.' : '.'));
    header('Location: payments.php');
    exit;
}

if (isset($_GET['update_status'], $_GET['status'])) {
    $paymentModel->updateStatus((int)$_GET['update_status'], $_GET['status']);
    flash('success', 'Payment status updated.');
    header('Location: payments.php');
    exit;
}

if (isset($_GET['delete'])) {
    $paymentModel->delete((int)$_GET['delete']);
    flash('success', 'Payment record deleted.');
    header('Location: payments.php');
    exit;
}

$payments = $paymentModel->all();
$students = $studentModel->all();
$totalIncome = $paymentModel->totalIncome();

$pageTitle = 'Payments';
$pageSubtitle = 'Record and track monthly fee payments';
$activeMenu = 'payments';
require_once __DIR__ . '/../includes/dashboard_header.php';
?>

<div class="stat-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px,1fr));">
  <div class="stat-card c-teal"><div class="stat-bar"></div>
    <div class="stat-label">Total Collected</div>
    <div class="stat-value">Rs. <?= number_format($totalIncome, 0) ?></div>
  </div>
</div>

<div class="panel">
  <div class="panel-header"><h3>Record New Payment</h3></div>
  <div class="panel-body">
    <form method="POST" action="payments.php">
      <div class="form-row">
        <div class="form-group"><label>Student</label>
          <select name="student_id" required>
            <option value="">Select Student</option>
            <?php foreach ($students as $s): ?>
              <option value="<?= $s['student_id'] ?>"><?= e($s['first_name'] . ' ' . $s['last_name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group"><label>Amount (Rs.)</label><input type="number" step="0.01" name="amount" required></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Month</label><input type="text" name="month" placeholder="e.g. September 2026" required></div>
        <div class="form-group"><label>Payment Date</label><input type="date" name="payment_date" value="<?= date('Y-m-d') ?>"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Status</label>
          <select name="status">
            <option value="Paid">Paid</option>
            <option value="Pending">Pending</option>
            <option value="Overdue">Overdue</option>
          </select>
        </div>
        <div class="form-group"><label>Notes</label><input type="text" name="notes"></div>
      </div>
      <button type="submit" name="save_payment" class="btn btn-primary">Save Payment</button>
      <div class="help-text">Marking a payment "Paid" automatically sends a fee-received notification to the parent.</div>
    </form>
  </div>
</div>

<div class="panel">
  <div class="panel-header"><h3>Payment History (<?= count($payments) ?>)</h3></div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Student</th><th>Month</th><th>Amount</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        <?php if (!$payments): ?>
          <tr><td colspan="6" class="text-soft">No payments recorded yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($payments as $p): ?>
          <tr>
            <td><?= e($p['first_name'] . ' ' . $p['last_name']) ?></td>
            <td><?= e($p['month']) ?></td>
            <td>Rs. <?= number_format($p['amount'], 2) ?></td>
            <td><?= e($p['payment_date'] ?? '-') ?></td>
            <td>
              <?php $cls = $p['status'] === 'Paid' ? 'badge-teal' : ($p['status'] === 'Pending' ? 'badge-brass' : 'badge-rose'); ?>
              <span class="badge <?= $cls ?>"><?= e($p['status']) ?></span>
            </td>
            <td>
              <?php if ($p['status'] !== 'Paid'): ?>
                <a href="payments.php?update_status=<?= $p['payment_id'] ?>&status=Paid" class="btn btn-outline btn-sm">Mark Paid</a>
              <?php endif; ?>
              <a href="payments.php?delete=<?= $p['payment_id'] ?>" class="btn btn-danger btn-sm" data-confirm="Delete this payment record?">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/dashboard_footer.php'; ?>
