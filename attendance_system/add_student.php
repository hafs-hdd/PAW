<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['studentId'] ?? '';
    $last_name = $_POST['lastName'] ?? '';
    $first_name = $_POST['firstName'] ?? '';
    $group = $_POST['group'] ?? '';
    
    $errors = [];
    if (empty($student_id)) $errors[] = "Student ID is required";
    if (empty($last_name)) $errors[] = "Last name is required";
    if (empty($first_name)) $errors[] = "First name is required";
    if (empty($group)) $errors[] = "Group is required";
    
    if (empty($errors)) {
        $conn = getDBConnection();
        if ($conn) {
            try {
                $stmt = $conn->prepare("INSERT INTO students (student_id, last_name, first_name, group_name) VALUES (?, ?, ?, ?)");
                $stmt->execute([$student_id, $last_name, $first_name, $group]);
                $_SESSION['message'] = "Student added successfully!";
            } catch(PDOException $e) {
                $_SESSION['error'] = "Error: Student ID already exists";
            }
        } else {
            $_SESSION['error'] = "Database connection failed";
        }
    } else {
        $_SESSION['error'] = implode("<br>", $errors);
    }
}

header("Location: index.php");
exit();
?>