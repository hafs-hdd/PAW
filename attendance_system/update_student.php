<?php
session_start();
require_once 'db_connect.php';

$id = $_GET['id'] ?? 0;
$student = null;

if ($conn = getDBConnection()) {
    if ($id) {
        $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
        $stmt->execute([$id]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $student_id = $_POST['student_id'] ?? '';
        $last_name = $_POST['last_name'] ?? '';
        $first_name = $_POST['first_name'] ?? '';
        $group_name = $_POST['group_name'] ?? '';

        if (!empty($student_id) && !empty($last_name) && !empty($first_name) && !empty($group_name)) {
            $stmt = $conn->prepare("UPDATE students SET student_id=?, last_name=?, first_name=?, group_name=? WHERE id=?");
            if ($stmt->execute([$student_id, $last_name, $first_name, $group_name, $id])) {
                $_SESSION['message'] = "Student updated successfully!";
                header("Location: list_students.php");
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Update Student</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav aria-label="Main navigation">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="list_students.php">Students List</a></li>
        </ul>
    </nav>
    <div class="container">
        <h1>Update Student</h1>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <?php if ($student): ?>
        <form method="POST" class="add-form">
            <div class="form-group">
                <label>Student ID:</label>
                <input type="text" name="student_id" value="<?php echo htmlspecialchars($student['student_id']); ?>" required>
            </div>
            <div class="form-group">
                <label>Last Name:</label>
                <input type="text" name="last_name" value="<?php echo htmlspecialchars($student['last_name']); ?>" required>
            </div>
            <div class="form-group">
                <label>First Name:</label>
                <input type="text" name="first_name" value="<?php echo htmlspecialchars($student['first_name']); ?>" required>
            </div>
            <div class="form-group">
                <label>Group:</label>
                <input type="text" name="group_name" value="<?php echo htmlspecialchars($student['group_name']); ?>" required>
            </div>
            <button type="submit" class="primary-btn">Update Student</button>
        </form>
        <?php else: ?>
            <p class="error-message">Student not found</p>
        <?php endif; ?>
    </div>
</body>
</html>