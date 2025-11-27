<?php
session_start();
require_once 'db_connect.php';

$id = $_GET['id'] ?? 0;

if ($id && $conn = getDBConnection()) {
    try {
        $stmt = $conn->prepare("UPDATE attendance_sessions SET status = 'closed' WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['message'] = "Session closed successfully!";
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error closing session";
    }
}

header("Location: list_sessions.php");
exit;
?>