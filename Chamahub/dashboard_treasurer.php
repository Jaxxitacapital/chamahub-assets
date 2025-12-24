<?php
require_once(__DIR__ . '/includes/auth.php');
require_once(__DIR__ . '/includes/db_chamahub.php');

if (!isset($_SESSION['user_data']) || $_SESSION['user_data']['role'] !== 'treasurer') {
    header("Location: ../index.php");
    exit();
}

// Fetch financials
$totalContributions = $pdo->query("SELECT SUM(amount) FROM contributions")->fetchColumn() ?? 0;
$totalExpenses = $pdo->query("SELECT SUM(amount) FROM expenses")->fetchColumn() ?? 0;
$balance = $totalContributions - $totalExpenses;
$pendingMembers = $pdo->query("SELECT COUNT(*) FROM members WHERE status = 'pending_payment'")->fetchColumn() ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Treasurer Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/luxon/build/global/luxon.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    #wrapper {
        display: flex;
        height: 100vh;
    }
    #sidebar-wrapper {
        min-width: 250px;
        max-width: 250px;
    }
    #page-content-wrapper {
        flex: 1;
        padding: 20px;
        background-color: #f8f9fa;
    }
    .card { transition: transform 0.3s ease; }
    .card:hover { transform: scale(1.05); }
    .dark-mode {
        background-color: #212529 !important;
        color: #f8f9fa !important;
    }
    .dark-mode .card {
        background-color: #343a40 !important;
    }
  </style>
</head>
<body>
<div class="d-flex" id="wrapper">
  <!-- Sidebar -->
  <div class="bg-dark border-end" id="sidebar-wrapper">
    <div class="sidebar-heading text-white py-4 px-3">💼 Treasurer</div>
    <div class="list-group list-group-flush">
      <a href="#" class="list-group-item list-group-item-action text-white bg-dark">📊 Dashboard</a>
      <a href="record_contribution.php" class="list-group-item list-group-item-action text-white bg-dark">💸 Record Contributions</a>
      <a href="transactions.php" class="list-group-item list-group-item-action text-white bg-dark">📑 Transactions</a>
      <a href="expenses.php" class="list-group-item list-group-item-action text-white bg-dark">📤 Expenses</a>
      <a href="payment_status.php" class="list-group-item list-group-item-action text-white bg-dark">⏳ Payment Status</a>
      <a href="withdrawal_requests.php" class="list-group-item list-group-item-action text-white bg-dark">💳 Withdrawal Requests</a>
      <a href="reports.php" class="list-group-item list-group-item-action text-white bg-dark">📈 Reports</a>
      <a href="settings.php" class="list-group-item list-group-item-action text-white bg-dark">⚙️ Settings</a>
      <a href="logout.php" class="list-group-item list-group-item-action text-white bg-dark">🚪 Logout</a>
    </div>
  </div>

  <!-- Page Content -->
  <div id="page-content-wrapper">
    <div class="container-fluid px-4">
      <div class="d-flex justify-content-between align-items-center">
        <h1 class="mt-4" id="greeting">Welcome!</h1>
        <button class="btn btn-outline-secondary" id="darkToggle">🌓 Toggle Dark Mode</button>
      </div>
      <p>Here's the financial overview for your Chama:</p>

      <div class="row">
        <div class="col-md-3">
          <div class="card text-white bg-success mb-3">
            <div class="card-body">
              <h5 class="card-title">💰 Total Contributions</h5>
              <p class="card-text">KES <?= number_format($totalContributions) ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card text-white bg-danger mb-3">
            <div class="card-body">
              <h5 class="card-title">📉 Expenses</h5>
              <p class="card-text">KES <?= number_format($totalExpenses) ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card text-white bg-primary mb-3">
            <div class="card-body">
              <h5 class="card-title">🏦 Balance</h5>
              <p class="card-text">KES <?= number_format($balance) ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card text-white bg-warning mb-3">
            <div class="card-body">
              <h5 class="card-title">⏳ Pending Payments</h5>
              <p class="card-text"><?= $pendingMembers ?> members</p>
            </div>
          </div>
        </div>
      </div>

      <canvas id="financeChart" height="100"></canvas>
      <div id="liveClock" class="mt-3 fw-bold text-secondary fs-5"></div>

      <hr>
      <h4 class="mt-4">📋 Recent Transactions</h4>
      <table id="txnTable" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>Date</th>
            <th>Member</th>
            <th>Amount (KES)</th>
            <th>Type</th>
            <th>Reference</th>
          </tr>
        </thead>
        <tbody>
        <?php
          $stmt = $pdo->query("SELECT t.*, m.name FROM transactions t JOIN members m ON t.member_id = m.id ORDER BY t.date DESC LIMIT 10");
          while ($row = $stmt->fetch()) {
            echo "<tr>
                    <td>{$row['date']}</td>
                    <td>{$row['name']}</td>
                    <td>".number_format($row['amount'])."</td>
                    <td>{$row['type']}</td>
                    <td>{$row['reference']}</td>
                  </tr>";
          }
        ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
const DateTime = luxon.DateTime;

document.addEventListener("DOMContentLoaded", () => {
  const greetingEl = document.getElementById("greeting");
  const hour = new Date().getHours();
  greetingEl.innerText = hour < 12 ? "Good Morning, Treasurer!" : hour < 18 ? "Good Afternoon, Treasurer!" : "Good Evening, Treasurer!";

  setInterval(() => {
    document.getElementById("liveClock").innerText = "🕒 " + DateTime.now().toFormat("HH:mm:ss - dd LLL yyyy");
  }, 1000);

  const ctx = document.getElementById("financeChart").getContext("2d");
  new Chart(ctx, {
    type: "bar",
    data: {
      labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
      datasets: [
        {
          label: "Contributions",
          data: [5000, 7000, 4000, 8000, 6000, 9000],
          backgroundColor: "#198754"
        },
        {
          label: "Expenses",
          data: [1000, 2000, 1500, 1000, 500, 1200],
          backgroundColor: "#dc3545"
        }
      ]
    }
  });

  $('#txnTable').DataTable({
    dom: 'Bfrtip',
    buttons: ['excelHtml5', 'csvHtml5'],
    paging: true,
    searching: true
  });

  document.getElementById("darkToggle").addEventListener("click", () => {
    document.body.classList.toggle("dark-mode");
  });
});
</script>
</body>
</html>
