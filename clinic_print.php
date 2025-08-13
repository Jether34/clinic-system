<?php
session_start();
if (!isset($_POST['record_type']) || !isset($_POST['selected_ids'])) {
  echo "No records selected.";
  exit();
}

$conn = new mysqli("localhost", "root", "", "clinic_db");
$record_type = $_POST['record_type'];
$selected_ids = $_POST['selected_ids'];
$placeholders = implode(',', array_fill(0, count($selected_ids), '?'));

if ($record_type === 'student') {
  $query = "SELECT student_id, full_name, course, year_level, section_block, student_contact, case_and_verdict, consultation_date FROM student_consultations WHERE id IN ($placeholders)";
} elseif ($record_type === 'admin') {
  $query = "SELECT full_name, position, contact_number, log_type, time_action FROM admin_logbook WHERE id IN ($placeholders)";
} elseif ($record_type === 'visitor') {
  $query = "SELECT full_name, purpose, contact_number, time_action FROM visitor_logbook WHERE id IN ($placeholders)";
} else {
  echo "Invalid record type.";
  exit();
}

$stmt = $conn->prepare($query);
$stmt->bind_param(str_repeat('i', count($selected_ids)), ...$selected_ids);
$stmt->execute();
$results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Print Records</title>
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: #f4f6f8;
      margin: 0;
      padding: 30px;
      color: #1a1a1a;
    }
    h1 {
      text-align: center;
      color: #388e3c;
      margin-bottom: 20px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background: #ffffff;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }
    th, td {
      padding: 12px;
      border-bottom: 1px solid #eee;
      text-align: left;
      font-size: 14px;
    }
    th {
      background: #fbc02d;
      color: #1a1a1a;
    }
    tr:hover td {
      background: #f1f1f1;
    }
    @media print {
      button { display: none; }
    }
    .print-btn {
      margin: 20px auto;
      display: block;
      padding: 12px 24px;
      background: #388e3c;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
    }
  </style>
</head>
<body>

<h1> Palawan National School Official Clinic Printed Record</h1>

<table>
  <thead>
    <tr>
      <?php if ($record_type === 'student'): ?>
        <th>Student ID</th><th>Full Name</th><th>Course</th><th>Year Level</th><th>Section</th><th>Contact</th><th>Case and Verdict</th><th>Consultation Date</th>
      <?php elseif ($record_type === 'admin'): ?>
        <th>Full Name</th><th>Position</th><th>Contact</th><th>Log Type</th><th>Timestamp</th>
      <?php elseif ($record_type === 'visitor'): ?>
        <th>Full Name</th><th>Purpose</th><th>Contact</th><th>Timestamp</th>
      <?php endif; ?>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($results as $row): ?>
    <tr>
      <?php if ($record_type === 'student'): ?>
        <td><?= htmlspecialchars($row['student_id']) ?></td>
        <td><?= htmlspecialchars($row['full_name']) ?></td>
        <td><?= htmlspecialchars($row['course']) ?></td>
        <td><?= htmlspecialchars($row['year_level']) ?></td>
        <td><?= htmlspecialchars($row['section_block']) ?></td>
        <td><?= htmlspecialchars($row['student_contact']) ?></td>
        <td><?= htmlspecialchars($row['case_and_verdict']) ?></td>
        <td><?= date('F j, Y', strtotime($row['consultation_date'])) ?></td>
      <?php elseif ($record_type === 'admin'): ?>
        <td><?= htmlspecialchars($row['full_name']) ?></td>
        <td><?= htmlspecialchars($row['position']) ?></td>
        <td><?= htmlspecialchars($row['contact_number']) ?></td>
        <td><?= htmlspecialchars($row['log_type']) ?></td>
        <td><?= date('F j, Y h:i A', strtotime($row['time_action'])) ?></td>
      <?php elseif ($record_type === 'visitor'): ?>
        <td><?= htmlspecialchars($row['full_name']) ?></td>
        <td><?= htmlspecialchars($row['purpose']) ?></td>
        <td><?= htmlspecialchars($row['contact_number']) ?></td>
        <td><?= date('F j, Y h:i A', strtotime($row['time_action'])) ?></td>
      <?php endif; ?>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<button class="print-btn" onclick="window.print()">Print Page</button>

</body>
</html>
