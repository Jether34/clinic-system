<?php
session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $conn = new mysqli("localhost", "root", "", "clinic_db");

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';

  $stmt = $conn->prepare("SELECT id, username, password, full_name FROM admins WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();
    if (password_verify($password, $admin['password'])) {
      $_SESSION['admin_logged_in'] = true;
      $_SESSION['admin_name'] = $admin['full_name'];
      header("Location: admin_dashboard.php");
      exit();
    } else {
      $error = "❌ Incorrect password.";
    }
  } else {
    $error = "⚠️ Username not found.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Administrator Login</title>
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

    .login-container {
      background-color: var(--white);
      padding: 50px;
      border-radius: 16px;
      max-width: 640px;
      margin: 40px auto;
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
      animation: fadeIn 0.8s ease;
      display: flex;
      flex-direction: column;
      gap: 25px;
    }

    .login-container h2 {
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

    input[type="text"],
    input[type="password"] {
      width: 100%;
      font-size: 18px;
      padding: 16px 18px;
      border-radius: 10px;
      border: 1px solid #ccc;
      background-color: #f9f9f9;
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    input:focus {
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

    .btn-login {
      background-color: var(--green);
      color: var(--white);
    }

    .btn-login:hover {
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

    .error-msg {
      color: red;
      text-align: center;
      font-weight: bold;
      font-size: 15px;
      margin-top: -10px;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @media screen and (max-width: 768px) {
      .login-container {
        padding: 30px 20px;
      }
    }
  </style>
</head>
<body>

<header>
  Administrator Login — Palawan National School Prefix of Discipline
</header>

<form method="POST" class="login-container">
  <h2>Admin Access</h2>

  <?php if (!empty($error)): ?>
    <div class="error-msg"><?= $error ?></div>
  <?php endif; ?>

  <label for="username">Username</label>
  <input type="text" name="username" id="username" required placeholder="Enter your username" />

  <label for="password">Password</label>
  <input type="password" name="password" id="password" required placeholder="Enter your password" />

  <div class="form-actions">
    <button type="submit" class="btn-login">Log In</button>
    <button type="button" class="btn-back" onclick="location.href='index.html'">Home</button>
  </div>
</form>

<footer>
  © Palawan National School Prefix of Discipline. All rights reserved.
</footer>

</body>
</html>

