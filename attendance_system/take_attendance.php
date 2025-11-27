<?php
session_start();
require_once 'db_connect.php';

$students = [];
$conn = getDBConnection();
if ($conn) {
    try {
        $stmt = $conn->query("SELECT * FROM students ORDER BY last_name, first_name");
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $error = "Error loading students";
    }
}

$today = date('Y-m-d');
$attendance_file = "attendance_files/attendance_$today.json";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (file_exists($attendance_file)) {
        $_SESSION['error'] = "Attendance for today has already been taken.";
    } else {
        $attendance_data = [];
        
        foreach ($_POST['attendance'] as $student_id => $status) {
            $attendance_data[] = [
                'student_id' => $student_id,
                'status' => $status,
                'date' => $today
            ];
        }
        
        if (!is_dir('attendance_files')) {
            mkdir('attendance_files', 0777, true);
        }
        
        if (file_put_contents($attendance_file, json_encode($attendance_data, JSON_PRETTY_PRINT))) {
            $_SESSION['message'] = "Attendance saved successfully!";
        } else {
            $_SESSION['error'] = "Failed to save attendance.";
        }
    }
    header("Location: take_attendance.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Take Attendance</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <nav aria-label="Main navigation">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="take_attendance.php">Take Attendance</a></li>
            <li><a href="create_session.php">Create Session</a></li>
        </ul>
    </nav>

    <div class="container">
        <h1>Take Attendance - <?php echo date('Y-m-d'); ?></h1>
        
        <?php
        if (isset($_SESSION['message'])) {
            echo '<div class="success-message">' . $_SESSION['message'] . '</div>';
            unset($_SESSION['message']);
        }
        if (isset($_SESSION['error'])) {
            echo '<div class="error-message">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
        ?>
        
        <?php if (file_exists($attendance_file)): ?>
            <div class="error-message">
                Attendance for today has already been taken.
            </div>
        <?php else: ?>
            <form method="POST">
                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Last Name</th>
                            <th>First Name</th>
                            <th>Group</th>
                            <th>Present</th>
                            <th>Absent</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                            <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($student['group_name']); ?></td>
                            <td>
                                <input type="radio" name="attendance[<?php echo $student['student_id']; ?>]" value="present" required>
                            </td>
                            <td>
                                <input type="radio" name="attendance[<?php echo $student['student_id']; ?>]" value="absent" required>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit" class="primary-btn">Submit Attendance</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>