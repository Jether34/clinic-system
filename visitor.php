<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $conn = new mysqli("localhost", "root", "", "clinic_db");

  $log_type = $_POST['log_type'] ?? '';
  $full_name = $_POST['full_name'] ?? '';
  $purpose = $_POST['purpose'] ?? '';
  $contact_number = $_POST['contact_number'] ?? '';
  $time_action = date('Y-m-d H:i:s');

  if ($log_type && $full_name && $purpose && $contact_number) {
    $stmt = $conn->prepare("INSERT INTO visitor_logbook (log_type, full_name, purpose, contact_number, time_action) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $log_type, $full_name, $purpose, $contact_number, $time_action);
    $stmt->execute();
    $success = true;
  } else {
    $error = "Please fill out all fields.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Visitor Logbook</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    :root {
      --green: #388e3c;
      --yellow: #fbc02d;
      --white: #ffffff;
      --black: #1a1a1a;
      --bg: #f4f6f8;
      --glow: 0 0 12px rgba(60, 200, 60, 0.6);
    }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      margin: 0;
      padding: 0;
      color: var(--black);
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
	
	body {
	overscroll-behavior-x: none;
	touch-action: pan-y; 
}


    .header {
      background: var(--green);
      color: var(--white);
      padding: 20px 30px;
      text-align: center;
      font-size: 26px;
      font-weight: bold;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .form-container {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      padding: 30px 20px;
    }

    form {
      background: var(--white);
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 640px;
      animation: fadeIn 0.8s ease;
    }

    h2 {
      margin-top: 0;
      margin-bottom: 30px;
      font-size: 24px;
      color: var(--green);
      text-align: center;
    }

    label {
      display: block;
      margin-bottom: 10px;
      font-weight: 600;
      font-size: 16px;
    }

    input, select {
      width: 100%;
      padding: 16px;
      margin-bottom: 24px;
      border-radius: 10px;
      border: 1px solid #ccc;
      font-size: 17px;
      transition: box-shadow 0.3s ease;
    }

    input:focus, select:focus {
      box-shadow: var(--glow);
      outline: none;
    }

    button {
      width: 100%;
      padding: 16px;
      font-size: 17px;
      font-weight: bold;
      border: none;
      border-radius: 10px;
      background: var(--green);
      color: var(--white);
      cursor: pointer;
      transition: transform 0.2s ease, box-shadow 0.3s ease;
    }

    button:hover {
      transform: scale(1.03);
      box-shadow: var(--glow);
    }

    .footer {
      background: linear-gradient(to right, #388e3c, #2e7d32);
      color: #fff;
      text-align: center;
      padding: 20px;
      font-size: 14px;
      box-shadow: 0 -2px 6px rgba(0,0,0,0.1);
    }

    .message {
      text-align: center;
      margin-bottom: 20px;
      font-weight: bold;
      color: var(--green);
    }

    .error {
      color: red;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to   { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

<div class="header">Visitor Logbook Portal</div>

<div class="form-container">
  <form method="POST">
    <h2>Visitor Entry Form</h2>

    <?php if (isset($success)): ?>
      <div class="message">✅ Visitor log submitted successfully!</div>
    <?php elseif (isset($error)): ?>
      <div class="message error">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <label for="log_type">Log Type</label>
    <select name="log_type" id="log_type" required>
      <option value="">Select Type</option>
      <option value="entry">Entry</option>
      <option value="exit">Exit</option>
    </select>

    <label for="full_name">Full Name</label>
    <input type="text" name="full_name" id="full_name" required />

    <label for="purpose">Purpose of Visit</label>
    <input type="text" name="purpose" id="purpose" required />

    <label for="contact_number">Contact Number</label>
    <input type="text" name="contact_number" id="contact_number" required />

    <button type="submit">Submit</button>

    <a href="index.html" style="text-align:center; display:block; margin-top:20px;">
      <button type="button" style="
        background: var(--black);
        color: var(--white);
        padding: 14px;
        font-size: 17px;
        border: none;
        border-radius: 10px;
        font-weight: bold;
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.3s ease;">
        Home
      </button>
    </a>
  </form>
</div>

<div class="footer">© Palawan National School Prefix of Discipline. All rights reserved.</div>

</body>
</html>
