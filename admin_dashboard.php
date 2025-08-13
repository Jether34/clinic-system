<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
  header("Location: admin_login.php");
  exit();
}

$conn = new mysqli("localhost", "root", "", "clinic_db");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$total_login = $conn->query("SELECT COUNT(*) as total FROM admin_logbook WHERE log_type='login'")->fetch_assoc()['total'];
$total_logout = $conn->query("SELECT COUNT(*) as total FROM admin_logbook WHERE log_type='logout'")->fetch_assoc()['total'];
$today_total = $conn->query("SELECT COUNT(*) as total FROM admin_logbook WHERE DATE(time_action)=CURDATE()")->fetch_assoc()['total'];

$search = $_GET['search'] ?? '';
$record_type = $_GET['record_type'] ?? '';
$search_results = [];

if ($record_type) {
  $like = "%$search%";

  if ($record_type === 'student') {
    $query = $search
      ? "SELECT * FROM student_consultations WHERE full_name LIKE ? ORDER BY consultation_date DESC"
      : "SELECT * FROM student_consultations ORDER BY consultation_date DESC";
  } elseif ($record_type === 'visitor') {
    $query = $search
      ? "SELECT * FROM visitor_logbook WHERE full_name LIKE ? ORDER BY time_action DESC"
      : "SELECT * FROM visitor_logbook ORDER BY time_action DESC";
  }

  $stmt = $conn->prepare($query);
  if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
  }

  if ($search && strpos($query, '?') !== false) {
    $stmt->bind_param("s", $like);
  }

  $stmt->execute();
  $result = $stmt->get_result();
  $search_results = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>PNS Clinic Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: #f9f9fb;
      margin: 0;
      padding: 0;
      color: #333;
      font-size: 15px;
    }

    .dashboard-header {
      background: #2e7d32;
      color: white;
      padding: 24px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-radius: 0 0 12px 12px;
    }

    .status {
      background: #fbc02d;
      color: #1a1a1a;
      padding: 6px 14px;
      border-radius: 6px;
      font-weight: 600;
      font-size: 14px;
      margin-left: 16px;
    }

    .datetime {
      background: white;
      color: #1a1a1a;
      padding: 8px 16px;
      border-radius: 6px;
      font-weight: 600;
      font-size: 15px;
    }

    .control-panel {
      display: flex;
      flex-wrap: wrap;
      gap: 16px;
      justify-content: center;
      margin: 30px 40px;
      align-items: center;
    }

    .control-panel form {
      display: flex;
      flex-wrap: wrap;
      gap: 14px;
      align-items: center;
    }

    .control-panel select,
    .control-panel input {
      padding: 12px;
      font-size: 15px;
      border-radius: 8px;
      border: 1px solid #ccc;
      min-width: 220px;
    }

    .control-panel button {
      padding: 12px 20px;
      font-size: 15px;
      border-radius: 8px;
      border: none;
      font-weight: 600;
      cursor: pointer;
    }

    .btn-green { background: #388e3c; color: white; }
    .btn-yellow { background: #fbc02d; color: #1a1a1a; }
    .btn-black { background: #1a1a1a; color: white; }

    .summary-cards {
      display: flex;
      justify-content: center;
      gap: 30px;
      flex-wrap: wrap;
      margin-bottom: 30px;
    }

    .card {
      background: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      width: 240px;
      text-align: center;
    }

    .card h3 {
      font-size: 18px;
      color: #2e7d32;
      margin-bottom: 8px;
    }

    .card p {
      font-size: 24px;
      color: #fbc02d;
      font-weight: bold;
    }

    table {
      width: 95%;
      margin: 0 auto;
      border-collapse: collapse;
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
      margin-top: 10px;
      font-size: 14px;
    }

    th, td {
      padding: 10px;
      border-bottom: 1px solid #eee;
      text-align: left;
    }

    th {
      background: #fbc02d;
      color: #1a1a1a;
      font-weight: 600;
    }

    tr:hover td {
      background: #f9f9f9;
    }

    .print-btn {
      display: block;
      margin: 30px auto;
      padding: 12px 24px;
      background: #388e3c;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 15px;
      font-weight: bold;
      cursor: pointer;
    }

    .dashboard-footer {
      margin-top: 40px;
      padding: 20px;
      text-align: center;
      background: linear-gradient(to right, #388e3c, #2e7d32);
      color: white;
      font-size: 14px;
      border-radius: 8px;
    }

    @media print {
      body * {
        visibility: hidden;
      }
      .printable, .printable * {
        visibility: visible;
      }
      .printable {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
      }
    }
  </style>
</head>
<body>

<div class="dashboard-header">
  <div>
    <h1>PNS Prefix of Discipline</h1>
    <span class="status">System Online</span>
  </div>
  <div>
    <span class="datetime" id="live-time"></span>
  </div>
</div>

<div class="control-panel">
  <form method="GET">
    <select name="record_type" required>
      <option value="">Select Record Type</option>
      <option value="student" <?= $record_type === 'student' ? 'selected' : '' ?>>Student</option>
      <option value="visitor" <?= $record_type === 'visitor' ? 'selected' : '' ?>>Visitor</option>
    </select>
    <input type="text" name="search" placeholder="Search by name..." value="<?= htmlspecialchars($search) ?>" />
    <button type="submit" class="btn-green">Search</button>
    <button type="button" onclick="window.location.href='admin_dashboard.php'" class="btn-yellow">Clear</button>
    <button type="button" onclick="window.location.href='index.html'" class="btn-black"> Home</button>
  </form>
</div>

<div class="summary-cards">
  <div class="card"><h3>Total Logins</h3><p><?= $total_login ?></p></div>
  <div class="card"><h3>Total Logouts</h3><p><?= $total_logout ?></p></div>
  <div class="card"><h3>Today's Logs</h3><p><?= $today_total ?></p></div>
</div>

<?php if ($record_type && count($search_results) > 0): ?>
  <form id="print-form">
    <table>
      <thead>
        <tr>
          <th>Select</th>
          <?php if ($record_type === 'student'): ?>
            <th>Student ID</th>
            <th>Full Name</th>
            <th>Age</th>
            <th>Gender</th>
                       <th>Year Level</th>
            <th>Course</th>
            <th>Section</th>
            <th>Address</th>
            <th>Consultation Date</th>
            <th>Offense Type</th>
            <th>Case</th>
            <th>Involves</th>
            <th>Verdict</th>
          <?php elseif ($record_type === 'visitor'): ?>
            <th>ID</th>
            <th>Log Type</th>
            <th>Full Name</th>
            <th>Purpose</th>
            <th>Contact Number</th>
            <th>Time Action</th>
            <th>Created At</th>
          <?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($search_results as $row): ?>
          <tr class="printable">
            <td><input type="checkbox" class="print-check" /></td>
            <?php if ($record_type === 'student'): ?>
              <td><?= htmlspecialchars($row['student_id']) ?></td>
              <td><?= htmlspecialchars($row['full_name']) ?></td>
              <td><?= htmlspecialchars($row['age']) ?></td>
              <td><?= htmlspecialchars($row['gender']) ?></td>
              <td><?= htmlspecialchars($row['year_level']) ?></td>
              <td><?= htmlspecialchars($row['course']) ?></td>
              <td><?= htmlspecialchars($row['section_block']) ?></td>
              <td><?= htmlspecialchars($row['address']) ?></td>
              <td><?= htmlspecialchars($row['consultation_date']) ?></td>
              <td><?= htmlspecialchars($row['offense_type']) ?></td>
              <td><?= htmlspecialchars($row['case']) ?></td>
              <td><?= isset($row['involves']) ? htmlspecialchars($row['involves'] ?: 'None') : 'None' ?></td>
              <td><?= htmlspecialchars($row['verdict']) ?></td>
            <?php elseif ($record_type === 'visitor'): ?>
              <td><?= htmlspecialchars($row['id']) ?></td>
              <td><?= ucfirst($row['log_type']) ?></td>
              <td><?= htmlspecialchars($row['full_name']) ?></td>
              <td><?= htmlspecialchars($row['purpose']) ?></td>
              <td><?= htmlspecialchars($row['contact_number']) ?></td>
              <td><?= htmlspecialchars($row['time_action']) ?></td>
              <td><?= htmlspecialchars($row['created_at']) ?></td>
            <?php endif; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <button type="button" class="print-btn" onclick="printSelected()">Print Selected</button>
  </form>
<?php elseif ($record_type): ?>
  <p style="text-align:center; font-size:16px; margin-top:20px;">No records found.</p>
<?php endif; ?>

<div class="dashboard-footer">
  © Palawan National School Prefix of Discipline. All rights reserved.
</div>

<script>
  function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('en-PH', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    const dateString = now.toLocaleDateString('en-PH', { year: 'numeric', month: '2-digit', day: '2-digit' });
    document.getElementById('live-time').textContent = `${timeString} — ${dateString}`;
  }
  setInterval(updateTime, 1000);
  updateTime();

  function printSelected() {
    const rows = document.querySelectorAll('.printable');
    rows.forEach(row => {
      row.style.display = 'none';
    });

    const checked = document.querySelectorAll('.print-check:checked');
    checked.forEach(box => {
      box.closest('tr').style.display = '';
    });

    window.print();

    rows.forEach(row => {
      row.style.display = '';
    });
  }
</script>

</body>
</html>
