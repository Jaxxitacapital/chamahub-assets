<?php
require_once(__DIR__ . '/includes/auth.php');
require_once(__DIR__ . '/includes/db_chamahub.php');

if (!isset($_SESSION['user_data']) || $_SESSION['user_data']['role'] !== 'treasurer') {
    header("Location: ../index.php");
    exit();
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['request_id'])) {
    $action = $_POST['action'];
    $id = intval($_POST['request_id']);
    if ($action === 'approve') {
        $stmt = $pdo->prepare("UPDATE withdrawal_requests SET status='approved', processed_at=NOW() WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Request #$id approved.";
    } elseif ($action === 'reject') {
        $stmt = $pdo->prepare("UPDATE withdrawal_requests SET status='rejected', processed_at=NOW() WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Request #$id rejected.";
    }
}

$requests = $pdo->query("
  SELECT wr.id, m.name AS member, wr.amount, wr.request_date, wr.status, wr.processed_at
  FROM withdrawal_requests wr
  JOIN users m ON wr.member_id = m.id
  ORDER BY wr.request_date DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Withdrawal Requests</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet"/>
  <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet"/>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
</head>
<body class="bg-light">
<div class="container py-4">
  <h2 class="mb-4">💳 Withdrawal Requests</h2>
  <?php if ($message): ?>
    <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>
  <table id="requestsTable" class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>ID</th><th>Member</th><th>Amount</th><th>Requested On</th><th>Status</th><th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($requests as $r): ?>
        <tr>
          <td><?= $r['id'] ?></td>
          <td><?= htmlspecialchars($r['member']) ?></td>
          <td><?= number_format($r['amount'], 2) ?></td>
          <td><?= htmlspecialchars($r['request_date']) ?></td>
          <td><?= htmlspecialchars($r['status']) ?></td>
          <td>
            <!-- View Modal Button -->
            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modal<?= $r['id'] ?>">View</button>

            <!-- Modal -->
            <div class="modal fade" id="modal<?= $r['id'] ?>" tabindex="-1" aria-labelledby="modalLabel<?= $r['id'] ?>" aria-hidden="true">
              <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                  <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalLabel<?= $r['id'] ?>">Withdrawal Request #<?= $r['id'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <p><strong>Member:</strong> <?= htmlspecialchars($r['member']) ?></p>
                    <p><strong>Amount:</strong> KSh <?= number_format($r['amount'], 2) ?></p>
                    <p><strong>Status:</strong> <?= htmlspecialchars($r['status']) ?></p>
                    <p><strong>Requested On:</strong> <?= htmlspecialchars($r['request_date']) ?></p>
                    <?php if ($r['status'] !== 'pending'): ?>
                      <p><strong>Processed At:</strong> <?= htmlspecialchars($r['processed_at'] ?? 'N/A') ?></p>
                    <?php endif; ?>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div>

            <?php if ($r['status'] === 'pending'): ?>
              <form method="POST" class="d-inline">
                <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                <button name="action" value="approve" class="btn btn-sm btn-success">Approve</button>
              </form>
              <form method="POST" class="d-inline">
                <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                <button name="action" value="reject" class="btn btn-sm btn-danger">Reject</button>
              </form>
            <?php else: ?>
              <span class="text-muted">✔ Processed</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  $(document).ready(function () {
    $('#requestsTable').DataTable({
      dom: 'Bfrtip',
      buttons: ['excelHtml5', 'csvHtml5'],
      responsive: true
    });
  });
</script>
</body>
</html>
