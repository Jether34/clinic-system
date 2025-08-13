<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "clinic_db");
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    function safe($key) {
        return isset($_POST[$key]) ? htmlspecialchars(trim($_POST[$key])) : '';
    }

    // Adviser
    $adviser_name = safe('adviser_name');
    $adviser_department = safe('adviser_department');

    // Offender
    $offender_name = safe('offender_name');
    $offender_grade_section = safe('offender_grade_section');
    $offender_age = (int)safe('offender_age');
    $offender_gender = safe('offender_gender');
    $offender_address = safe('offender_address');
    $offender_strand = safe('offender_strand');
    $offender_id = safe('offender_id');

    // Aggrieved
    $aggrieved_name = safe('aggrieved_name');
    $aggrieved_grade_section = safe('aggrieved_grade_section');
    $aggrieved_age = (int)safe('aggrieved_age');
    $aggrieved_gender = safe('aggrieved_gender');
    $aggrieved_address = safe('aggrieved_address');
    $aggrieved_strand = safe('aggrieved_strand');
    $aggrieved_id = safe('aggrieved_id');

    // Incident
    $incident_description = safe('incident_description');
    $incident_location = safe('incident_location');
    $incident_datetime = safe('incident_datetime');
    $others_involved = safe('others_involved');

    // Witness
    $witness_name = safe('witness_name');
    $witness_department = safe('witness_department');

    // Actions
    $action_by_adviser = safe('action_by_adviser');
    $initial_by_prefect = safe('initial_by_prefect');
    $recommendation_by_prefect = safe('recommendation_by_prefect');

    // Timestamp
    $submitted_at = date("Y-m-d H:i:s");

    $sql = "INSERT INTO student_consultations (
        adviser_name, adviser_department,
        offender_name, offender_grade_section, offender_age, offender_gender, offender_address, offender_strand, offender_id,
        aggrieved_name, aggrieved_grade_section, aggrieved_age, aggrieved_gender, aggrieved_address, aggrieved_strand, aggrieved_id,
        incident_description, incident_location, incident_datetime, others_involved,
        witness_name, witness_department,
        action_by_adviser, initial_by_prefect, recommendation_by_prefect,
        submitted_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) die("Prepare failed: " . $conn->error);

    $stmt->bind_param("ssssisssssssisssssssssssss",
        $adviser_name, $adviser_department,
        $offender_name, $offender_grade_section, $offender_age, $offender_gender, $offender_address, $offender_strand, $offender_id,
        $aggrieved_name, $aggrieved_grade_section, $aggrieved_age, $aggrieved_gender, $aggrieved_address, $aggrieved_strand, $aggrieved_id,
        $incident_description, $incident_location, $incident_datetime, $others_involved,
        $witness_name, $witness_department,
        $action_by_adviser, $initial_by_prefect, $recommendation_by_prefect,
        $submitted_at
    );

    if ($stmt->execute()) {
        echo "<script>alert('Incident report submitted successfully!'); window.location.href='student.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Incident & Investigation Report Form</title>
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
      padding-top: 100px;
      padding-bottom: 100px;
    }
    header, footer {
      position: fixed;
      left: 0;
      right: 0;
      background-color: var(--green);
      color: var(--white);
      text-align: center;
      padding: 16px 20px;
      font-size: 20px;
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
      font-size: 12px;
      border-top: 4px solid var(--yellow);
    }
    form {
      max-width: 1000px;
      margin: 0 auto;
      display: flex;
      flex-direction: column;
      gap: 24px;
      animation: fadeIn 0.8s ease;
      padding: 0 16px;
    }
    .form-row {
      display: flex;
      flex-wrap: wrap;
      gap: 16px;
    }
    .form-section {
      background-color: var(--white);
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      flex: 1;
      min-width: 280px;
    }
    .section-title {
      font-size: 18px;
      font-weight: bold;
      color: var(--green);
      border-left: 6px solid var(--yellow);
      padding-left: 10px;
      margin-bottom: 16px;
    }
    .form-group {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }
    label {
      font-weight: 600;
      font-size: 14px;
    }
    input, select, textarea {
      font-size: 14px;
      padding: 10px 12px;
      border-radius: 8px;
      border: 1px solid #ccc;
      background-color: #f9f9f9;
      width: 100%;
    }
    textarea {
      resize: vertical;
      min-height: 80px;
    }
    .form-actions {
      display: flex;
      justify-content: center;
      gap: 16px;
      margin-top: 40px;
      flex-wrap: wrap;
    }
    button {
      padding: 12px 24px;
      font-size: 15px;
      font-weight: bold;
      border-radius: 8px;
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
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @media screen and (max-width: 768px) {
      header, footer {
        font-size: 16px;
        padding: 12px;
      }
      .section-title {
        font-size: 16px;
      }
      label {
        font-size: 13px;
      }
      input, select, textarea {
        font-size: 13px;
        padding: 8px 10px;
      }
      button {
        font-size: 14px;
        padding: 10px 20px;
      }
    }
  </style>
</head>
<body>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "clinic_db");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    function safe($key) {
        return isset($_POST[$key]) ? htmlspecialchars(trim($_POST[$key])) : '';
    }

    $adviser_name = safe('adviser_name');
    $adviser_department = safe('adviser_department');

    $offender_name = safe('offender_name');
    $offender_grade_section = safe('offender_grade_section');
    $offender_age = (int)safe('offender_age');
    $offender_gender = safe('offender_gender');
    $offender_address = safe('offender_address');
    $offender_strand = safe('offender_strand');
    $offender_id = safe('offender_id');

    $aggrieved_name = safe('aggrieved_name');
    $aggrieved_grade_section = safe('aggrieved_grade_section');
    $aggrieved_age = (int)safe('aggrieved_age');
    $aggrieved_gender = safe('aggrieved_gender');
    $aggrieved_address = safe('aggrieved_address');
    $aggrieved_strand = safe('aggrieved_strand');
    $aggrieved_id = safe('aggrieved_id');

    $incident_description = safe('incident_description');
    $incident_location = safe('incident_location');
    $incident_datetime = safe('incident_datetime');
    $others_involved = safe('others_involved');

    $witness_name = safe('witness_name');
    $witness_department = safe('witness_department');

    $action_by_adviser = safe('action_by_adviser');
    $initial_by_prefect = safe('initial_by_prefect');
    $recommendation_by_prefect = safe('recommendation_by_prefect');

    $sql = "INSERT INTO student_consultations (
        adviser_name, adviser_department,
        offender_name, offender_grade_section, offender_age, offender_gender, offender_address, offender_strand, offender_id,
        aggrieved_name, aggrieved_grade_section, aggrieved_age, aggrieved_gender, aggrieved_address, aggrieved_strand, aggrieved_id,
        incident_description, incident_location, incident_datetime, others_involved,
        witness_name, witness_department,
        action_by_adviser, initial_by_prefect, recommendation_by_prefect
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssssisssssssisssssssssssss",
        $adviser_name, $adviser_department,
        $offender_name, $offender_grade_section, $offender_age, $offender_gender, $offender_address, $offender_strand, $offender_id,
        $aggrieved_name, $aggrieved_grade_section, $aggrieved_age, $aggrieved_gender, $aggrieved_address, $aggrieved_strand, $aggrieved_id,
        $incident_description, $incident_location, $incident_datetime, $others_involved,
        $witness_name, $witness_department,
        $action_by_adviser, $initial_by_prefect, $recommendation_by_prefect
    );

    if ($stmt->execute()) {
        echo "<script>alert('Incident report submitted successfully!'); window.location.href='incident.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Incident & Investigation Report Form</title>
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
      padding-top: 100px;
      padding-bottom: 100px;
    }
    header, footer {
      position: fixed;
      left: 0;
      right: 0;
      background-color: var(--green);
      color: var(--white);
      text-align: center;
      padding: 16px 20px;
      font-size: 20px;
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
      font-size: 12px;
      border-top: 4px solid var(--yellow);
    }
    form {
      max-width: 1000px;
      margin: 0 auto;
      display: flex;
      flex-direction: column;
      gap: 24px;
      animation: fadeIn 0.8s ease;
      padding: 0 16px;
    }
    .form-row {
      display: flex;
      flex-wrap: wrap;
      gap: 16px;
    }
    .form-section {
      background-color: var(--white);
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      flex: 1;
      min-width: 280px;
    }
    .section-title {
      font-size: 18px;
      font-weight: bold;
      color: var(--green);
      border-left: 6px solid var(--yellow);
      padding-left: 10px;
      margin-bottom: 16px;
    }
    .form-group {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }
    label {
      font-weight: 600;
      font-size: 14px;
    }
    input, select, textarea {
      font-size: 14px;
      padding: 10px 12px;
      border-radius: 8px;
      border: 1px solid #ccc;
      background-color: #f9f9f9;
      width: 100%;
    }
    textarea {
      resize: vertical;
      min-height: 80px;
    }
    .form-actions {
      display: flex;
      justify-content: center;
      gap: 16px;
      margin-top: 40px;
      flex-wrap: wrap;
    }
    button {
      padding: 12px 24px;
      font-size: 15px;
      font-weight: bold;
      border-radius: 8px;
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
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @media screen and (max-width: 768px) {
      header, footer {
        font-size: 16px;
        padding: 12px;
      }
      .section-title {
        font-size: 16px;
      }
      label {
        font-size: 13px;
      }
      input, select, textarea {
        font-size: 13px;
        padding: 8px 10px;
      }
      button {
        font-size: 14px;
        padding: 10px 20px;
      }
    }
  </style>
</head>
<body>
<header>Incident & Investigation Report Form</header>
<form method="POST" action="">
  <div class="form-row">
    <!-- Adviser Info -->
    <div class="form-section">
      <div class="section-title">Adviser Information</div>
      <div class="form-group">
        <label for="adviser_name">Name</label>
        <input type="text" name="adviser_name" required />
        <label for="adviser_department">Department</label>
        <input type="text" name="adviser_department" required />
      </div>
    </div>

    <!-- Offender Info -->
    <div class="form-section">
      <div class="section-title">Offender Information</div>
      <div class="form-group">
        <label for="offender_name">Name</label>
        <input type="text" name="offender_name" required />
        <label for="offender_grade_section">Grade & Section</label>
        <input type="text" name="offender_grade_section" required />
        <label for="offender_age">Age</label>
        <input type="number" name="offender_age" required />
        <label for="offender_gender">Gender</label>
        <select name="offender_gender" required>
          <option value="">Select</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select>
        <label for="offender_address">Address</label>
        <input type="text" name="offender_address" required />
        <label for="offender_strand">Strand</label>
        <input type="text" name="offender_strand" required />
        <label for="offender_id">Student ID</label>
        <input type="text" name="offender_id" required />
      </div>
    </div>

    <!-- Aggrieved Info -->
    <div class="form-section">
      <div class="section-title">Aggrieved Information</div>
      <div class="form-group">
        <label for="aggrieved_name">Name</label>
        <input type="text" name="aggrieved_name" required />
        <label for="aggrieved_grade_section">Grade & Section</label>
        <input type="text" name="aggrieved_grade_section" required />
        <label for="aggrieved_age">Age</label>
        <input type="number" name="aggrieved_age" required />
        <label for="aggrieved_gender">Gender</label>
        <select name="aggrieved_gender" required>
          <option value="">Select</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select>
        <label for="aggrieved_address">Address</label>
        <input type="text" name="aggrieved_address" required />
        <label for="aggrieved_strand">Strand</label>
        <input type="text" name="aggrieved_strand" required />
        <label for="aggrieved_id">Student ID</label>
        <input type="text" name="aggrieved_id" required />
      </div>
    </div>
  </div>

  <!-- New row to match container layout for remaining sections -->
  <div class="form-row">
    <!-- Incident Details -->
    <div class="form-section">
      <div class="section-title">Incident Details</div>
      <div class="form-group">
        <label for="incident_description">Description</label>
        <textarea name="incident_description" required></textarea>
        <label for="incident_location">Location</label>
        <input type="text" name="incident_location" required />
        <label for="incident_datetime">Date & Time</label>
        <input type="datetime-local" name="incident_datetime" required />
        <label for="others_involved">Others Involved</label>
        <input type="text" name="others_involved" />
      </div>
    </div>

    <!-- Witness Info -->
    <div class="form-section">
      <div class="section-title">Witness Information</div>
      <div class="form-group">
        <label for="witness_name">Name</label>
        <input type="text" name="witness_name" required />
        <label for="witness_department">Department</label>
        <input type="text" name="witness_department" required />
      </div>
    </div>

    <!-- Actions & Recommendations -->
    <div class="form-section">
      <div class="section-title">Actions & Recommendations</div>
      <div class="form-group">
        <label for="action_by_adviser">Action Taken by Adviser</label>
        <textarea name="action_by_adviser" required></textarea>
        <label for="initial_by_prefect">Initial Action by Prefect</label>
        <textarea name="initial_by_prefect" required></textarea>
        <label for="recommendation_by_prefect">Recommendation by Prefect</label>
        <textarea name="recommendation_by_prefect" required></textarea>
      </div>
    </div>
  </div>

  <!-- Buttons -->
  <div class="form-actions">
    <button type="submit" class="btn-submit">Submit</button>
    <button type="button" class="btn-back" onclick="window.location.href='index.html'">Back</button>
  </div>
</form>
<footer>Palawan National School | Incident Reporting System</footer>
</body>
</html>