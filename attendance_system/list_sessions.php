<?php
require_once 'db_connect.php';

$sessions = [];
if ($conn = getDBConnection()) {
    try {
        $stmt = $conn->query("SELECT * FROM attendance_sessions ORDER BY created_at DESC");
        $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $error = "Error loading sessions";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sessions List</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav aria-label="Main navigation">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="create_session.php">Create Session</a></li>
            <li><a href="list_sessions.php">View Sessions</a></li>
        </ul>
    </nav>
    <div class="container">
        <h1>Attendance Sessions</h1>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="success-message"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Course</th>
                        <th>Group</th>
                        <th>Date</th>
                        <th>Professor ID</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sessions as $session): ?>
                    <tr>
                        <td><?php echo $session['id']; ?></td>
                        <td><?php echo htmlspecialchars($session['course_id']); ?></td>
                        <td><?php echo htmlspecialchars($session['group_id']); ?></td>
                        <td><?php echo $session['session_date']; ?></td>
                        <td><?php echo $session['opened_by']; ?></td>
                        <td>
                            <span class="status-<?php echo $session['status']; ?>">
                                <?php echo ucfirst($session['status']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($session['status'] == 'open'): ?>
                                <a href="close_session.php?id=<?php echo $session['id']; ?>" class="action-btn">Close Session</a>
                            <?php else: ?>
                                <span class="action-btn disabled">Closed</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>