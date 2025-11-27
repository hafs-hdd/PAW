<?php
session_start();
require_once 'db_connect.php';

$id = $_GET['id'] ?? 0;

if ($id && $conn = getDBConnection()) {
    try {
        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['message'] = "Student deleted successfully!";
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error deleting student";
    }
}

header("Location: list_students.php");
exit;
?>