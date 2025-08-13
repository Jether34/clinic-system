<?php
session_start();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $conn = new mysqli("localhost", "root", "", "clinic_db");

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $log_type = $_POST['log_type'] ?? '';
  $full_name = $_POST['full_name'] ?? '';
  $position = $_POST['position'] ?? '';
  $contact_number = $_POST['contact_number'] ?? '';

  $stmt = $conn->prepare("INSERT INTO admin_logbook (log_type, full_name, position, contact_number) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssss", $log_type, $full_name, $position, $contact_number);

  if ($stmt->execute()) {
    $success = "✅ Logbook entry submitted successfully.";
  } else {
    $error = "❌ Failed to submit log entry.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Administrator Logbook</title>
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

	body {
	overscroll-behavior-x: none;
	touch-action: pan-y; 
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

    .form-container {
      background-color: var(--white);
      padding: 40px;
      border-radius: 16px;
      max-width: 640px;
      margin: 40px auto;
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
      animation: fadeIn 0.8s ease;
      display: flex;
      flex-direction: column;
      gap: 25px;
    }

    .form-container h2 {
      text-align: center;
      color: var(--green);
      font-size: 22px;
      margin-bottom: 10px;
    }

    label {
      font-weight: 600;
      font-size: 17px;
      color: var(--black);
    }

    input, select {
      width: 100%;
      font-size: 18px;
      padding: 16px 18px;
      border-radius: 10px;
      border: 1px solid #ccc;
      background-color: #f9f9f9;
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    input:focus, select:focus {
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
      padding: 14px 32px;
      font-size: 17px;
      font-weight: bold;
      border-radius: 10px;
      border: none;
      cursor: pointer;
      transition: background 0.3s ease, transform 0.2s ease;
    }

    .btn-submit {
      background-color: var(--green);
      color: var(--white);
    }

    .btn-submit:hover {
      background-color: #2e7d32;
      transform: scale(1.05);
    }

    .btn-back {
      background-color: var(--black);
      color: var(--white);
    }

    .btn-back:hover {
      background-color: #333;
      transform: scale(1.05);
    }

    .message {
      text-align: center;
      font-weight: bold;
      color: red;
      font-size: 15px;
      margin-top: -10px;
    }

    .success {
      color: green;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @media screen and (max-width: 768px) {
      .form-container {
        padding: 30px 20px;
      }
    }
  </style>
</head>
<body>

<header>
  Staff Logbook — Palawan National School Perfix of Discipline
</header>

<form method="POST" class="form-container">
  <h2>Record Your Log</h2>

  <?php if (!empty($error)): ?>
    <div class="message"><?= $error ?></div>
  <?php elseif (!empty($success)): ?>
    <div class="message success"><?= $success ?></div>
  <?php endif; ?>

  <label for="log_type">Log Type</label>
  <select name="log_type" id="log_type" required>
    <option value="" disabled selected>-- Select Log Type --</option>
    <option value="login">Login</option>
    <option value="logout">Logout</option>
  </select>

  <label for="full_name">Full Name</label>
  <input type="text" name="full_name" id="full_name" required placeholder="Enter your full name" />

  <label for="position">Position</label>
  <input type="text" name="position" id="position" required placeholder="Enter your position" />

  <label for="contact_number">Contact Number</label>
  <input type="text" name="contact_number" id="contact_number" required placeholder="Enter your contact number" />

  <div class="form-actions">
    <button type="submit" class="btn-submit">Submit</button>
    <button type="button" class="btn-back" onclick="location.href='index.html'">Back to Homepage</button>
  </div>
</form>

<footer>
  © Palawan National School Prefix of Discipline. All rights reserved.
</footer>

</body>
</html>
