<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Decode incoming JSON data
$data = json_decode(file_get_contents("php://input"), true);

// Connect to MySQL
$conn = new mysqli("localhost", "root", "", "school_db");

// Handle connection error
if ($conn->connect_error) {
  echo json_encode([
    "status" => "error",
    "message" => "DB connection failed",
    "error" => $conn->connect_error
  ]);
  exit();
}

// Time-based status logic
date_default_timezone_set("Asia/Manila");
$hour = date("H");
$min = date("i");
$status = "Unknown";

if ($hour < 7 || ($hour == 7 && $min <= 30)) {
  $status = "Present";
} elseif (($hour == 7 && $min >= 31) || $hour == 8 || $hour == 9) {
  $status = "Late";
} elseif ($hour >= 10) {
  $status = "Absent";
}

// Optional: auto timestamp of scan
$timestamp = date("Y-m-d H:i:s");

// Prepare SQL Insert
$stmt = $conn->prepare("INSERT INTO scans (student_id, name, school, grade_level, strand, status, scan_time) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $data["id"], $data["name"], $data["school"], $data["grade"], $data["strand"], $status, $timestamp);

// Execute and check result
if ($stmt->execute()) {
  echo json_encode([
    "status" => "success",
    "recordedStatus" => $status,
    "scanTime" => $timestamp
  ]);
} else {
  echo json_encode([
    "status" => "error",
    "message" => $stmt->error
  ]);
}

$stmt->close();
$conn->close();
?>