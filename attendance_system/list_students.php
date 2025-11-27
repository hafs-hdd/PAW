<?php
require_once 'db_connect.php';

$conn = getDBConnection();
$students = [];

if ($conn) {
    try {
        $stmt = $conn->query("SELECT * FROM students ORDER BY last_name, first_name");
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $error = "Error loading students";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>All Students</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <nav aria-label="Main navigation">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="take_attendance.php">Take Attendance</a></li>
            <li><a href="create_session.php">Create Session</a></li>
            <li><a href="list_students.php">All Students</a></li>
        </ul>
    </nav>

    <div class="container">
        <h1>All Students</h1>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student ID</th>
                        <th>Last Name</th>
                        <th>First Name</th>
                        <th>Group</th>
                        <th>Date Added</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo $student['id']; ?></td>
                        <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                        <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($student['group_name']); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($student['created_at'])); ?></td>
                        <td>
                            <a href="update_student.php?id=<?php echo $student['id']; ?>" class="action-btn">Edit</a>
                            <a href="delete_student.php?id=<?php echo $student['id']; ?>" class="action-btn delete" onclick="return confirm('Delete this student?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>