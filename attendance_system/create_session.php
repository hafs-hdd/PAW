<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = $_POST['course_id'] ?? '';
    $group_id = $_POST['group_id'] ?? '';
    $professor_id = $_POST['professor_id'] ?? '';

    if (!empty($course_id) && !empty($group_id) && !empty($professor_id)) {
        $conn = getDBConnection();
        if ($conn) {
            try {
                $stmt = $conn->prepare("INSERT INTO attendance_sessions (course_id, group_id, session_date, opened_by) VALUES (?, ?, CURDATE(), ?)");
                $stmt->execute([$course_id, $group_id, $professor_id]);
                $session_id = $conn->lastInsertId();
                $_SESSION['message'] = "Session created successfully! Session ID: " . $session_id;
                header("Location: list_sessions.php");
                exit;
            } catch(PDOException $e) {
                $_SESSION['error'] = "Error creating session: " . $e->getMessage();
            }
        }
    } else {
        $_SESSION['error'] = "All fields are required";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Session</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav aria-label="Main navigation">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="list_sessions.php">View Sessions</a></li>
        </ul>
    </nav>
    <div class="container">
        <h1>Create Attendance Session</h1>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form method="POST" class="add-form">
            <div class="form-group">
                <label>Course ID:</label>
                <input type="text" name="course_id" required>
            </div>
            <div class="form-group">
                <label>Group ID:</label>
                <input type="text" name="group_id" required>
            </div>
            <div class="form-group">
                <label>Professor ID:</label>
                <input type="number" name="professor_id" required>
            </div>
            <button type="submit" class="primary-btn">Create Session</button>
        </form>
    </div>
</body>
</html>