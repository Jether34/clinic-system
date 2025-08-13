<?php
session_start();
$error = '';
$results = [];

$conn = new mysqli("localhost", "root", "", "clinic_db");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $record_type = $_POST['record_type'] ?? '';
  $log_type = $_POST['log_type'] ?? '';
  $time_filter = $_POST['time_filter'] ?? '';
  $search_term = $_POST['search_term'] ?? '';

  $table = '';
  $search_fields = '';
  $has_log_type = false;
  $has_time_action = false;

  if ($record_type === 'students') {
    $table = 'student_consultations';
    $search_fields = "(full_name LIKE '%$search_term%' OR course LIKE '%$search_term%' OR symptoms LIKE '%$search_term%' OR consultation_purpose LIKE '%$search_term%')";
    $has_log_type = true;
    $has_time_action = true;
  } elseif ($record_type === 'visitors') {
    $table = 'visitor_logbook';
    $search_fields = "(full_name LIKE '%$search_term%' OR purpose LIKE '%$search_term%' OR department LIKE '%$search_term%')";
    $has_log_type = true;
    $has_time_action = true;
  } elseif ($record_type === 'admins') {
    $table = 'admin_logbook';
    $search_fields = "(full_name LIKE '%$search_term%' OR position LIKE '%$search_term%')";
    $has_log_type = true;
    $has_time_action = true;
  }

  if ($table) {
    $conditions = [];

    if ($has_log_type && $log_type !== '') {
      $conditions[] = "log_type = '$log_type'";
    }

    if ($has_time_action && $time_filter !== '') {
      if ($time_filter === 'today') {
        $conditions[] = "DATE(time_action) = CURDATE()";
      } elseif ($time_filter === 'this_week') {
        $conditions[] = "YEARWEEK(time_action, 1) = YEARWEEK(CURDATE(), 1)";
      } elseif ($time_filter === 'this_month') {
        $conditions[] = "MONTH(time_action) = MONTH(CURDATE()) AND YEAR(time_action) = YEAR(CURDATE())";
      }
    }

    if ($search_term !== '') {
      $conditions[] = $search_fields;
    }

    $where = count($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
    $query = "SELECT * FROM $table $where ORDER BY 1 DESC";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $results[] = $row;
      }
    } else {
      $error = "No results found based on your filters.";
    }
  } else {
    $error = "Please select a valid record type.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Clinic Monitoring Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    :root {
      --green: #388e3c;
      --yellow: #fbc02d;
      --white: #ffffff;
      --black: #1a1a1a;
      --bg: #f4f6f8;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Inter', sans-serif;
      background-color: var(--bg);
      color: var(--black);
      padding-top: 80px;
      padding-bottom: 80px;
    }

    header, footer {
      position: fixed;
      left: 0;
      right: 0;
      background-color: var(--green);
      color: var(--white);
      text-align: center;
      padding: 20px 30px;
      font-size: 24px;
      font-weight: bold;
      z-index: 1000;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    header {
      top: 0;
      border-bottom: 4px solid var(--yellow);
    }

    footer {
      bottom: 0;
      font-size: 14px;
      border-top: 4px solid var(--yellow);
    }

    .monitor-container {
      max-width: 720px;
      margin: 40px auto;
      padding: 40px;
      background-color: var(--white);
      border-radius: 16px;
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
      animation: fadeIn 0.8s ease;
      display: flex;
      flex-direction: column;
      gap: 25px;
    }

    .monitor-container h2 {
      text-align: center;
      color: var(--green);
      font-size: 22px;
    }

    label {
      font-weight: 600;
      font-size: 17px;
    }

    select, input[type="text"] {
      width: 100%;
      font-size: 17px;
      padding: 14px;
      border-radius: 10px;
      border: 1px solid #ccc;
      background-color: #f9f9f9;
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    select:focus, input:focus {
      border-color: var(--green);
      box-shadow: 0 0 0 3px rgba(56, 142, 60, 0.2);
      outline: none;
    }

    .form-actions {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-top: 10px;
    }

    button {
      padding: 12px 28px;
      font-size: 16px;
      font-weight: bold;
      border-radius: 10px;
      border: none;
      cursor: pointer;
      transition: background 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
    }

    .btn-green {
      background-color: var(--green);
      color: var(--white);
    }

    .btn-green:hover {
      background-color: #2e7d32;
      transform: scale(1.05);
      box-shadow: 0 0 14px rgba(56, 142, 60, 0.4);
    }

    .btn-black {
      background-color: var(--black);
      color: var(--white);
    }

    .btn-black:hover {
      background-color: #333;
      transform: scale(1.05);
      box-shadow: 0 0 14px rgba(251, 192, 45, 0.4);
    }

    .message {
      text-align: center;
      color: red;
      font-weight: bold;
      font-size: 15px;
      margin-top: -10px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      font-size: 15px;
    }

    th, td {
      padding: 12px;
      border: 1px solid #ccc;
      text-align: left;
    }

    th {
      background-color: var(--green);
      color: var(--white);
    }

    tr:hover {
      background-color: #f1f1f1;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @media screen and (max-width: 768px) {
      .monitor-container {
        padding: 30px 20px;
      }
    }
  </style>
</head>
<body>

<header>
  Clinic Monitoring Dashboard
</header>

<form method="POST" class="monitor-container">
  <h2>Search Clinic Records</h2>

  <?php if (!empty($error)): ?>
    <div class="message"><?= $error ?></div>
  <?php endif; ?>

  <label for="record_type">Select Record Type</label>
  <select name="record_type" id="record_type" required>
    <option value="" disabled selected>-- Select Record Type --</option>
    <option value="students">Student Consultations</option>
    <option value="visitors">Visitor Logbook</option>
    <option value="admins">Admin Logbook</option>
  </select>

  <label for="log_type">Log Type</label>
  <select name="log_type" id="log_type">
    <option value="" disabled selected>-- Select Log Type --</option>
    <option value="login">Login</option>
    <option value="logout">Logout</option>
    <option value="entry">Entry</option>
    <option value="exit">Exit</option>
  </select>

  <label for="time_filter">Time Filter</label>
  <select name="time_filter" id="time_filter">
    <option value="" disabled selected>-- Select Time Filter --</option>
    <option value="today">Today</option>
    <option value="this_week">This Week</option>
    <option value="this_month">This Month</option>
  </select>

  <label for="search_term">Search</label>
  <input type="text" name="search_term" id="search_term" placeholder="Type keyword here..." />

  <div class="form-actions">
    <button type="submit" class="btn-green">Search</button>
    <button type="button" class="btn-black" onclick="location.href='admin_dashboard.php'">Back to Admin Dashboard</button>
  </div>

  <?php if (!empty($results)): ?>
    <table>
      <thead>
        <tr>
          <?php foreach ($results[0] as $column => $value): ?>
            <th><?= ucfirst(str_replace('_', ' ', $column)) ?></th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($results as $row): ?>
          <tr>
            <?php foreach ($row as $value): ?>
              <td><?= htmlspecialchars($value) ?></td>
            <?php endforeach; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</form>

<footer>
  © Palawan National School Clinic. All rights reserved.
</footer>

</body>
</html>
