<?php
require_once 'db_connect.php';

$conn = getDBConnection();

if ($conn) {
    // جدول الطلاب
    $sql1 = "CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id VARCHAR(20) NOT NULL UNIQUE,
        last_name VARCHAR(100) NOT NULL,
        first_name VARCHAR(100) NOT NULL,
        group_name VARCHAR(10) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    // جدول جلسات الحضور
    $sql2 = "CREATE TABLE IF NOT EXISTS attendance_sessions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        course_id VARCHAR(50) NOT NULL,
        group_id VARCHAR(10) NOT NULL,
        session_date DATE NOT NULL,
        opened_by INT NOT NULL,
        status ENUM('open', 'closed') DEFAULT 'open',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $conn->exec($sql1);
    $conn->exec($sql2);
    
    // إضافة بيانات تجريبية
    $conn->exec("INSERT IGNORE INTO students (student_id, last_name, first_name, group_name) VALUES 
        ('1001', 'Smith', 'John', 'A'),
        ('1002', 'Johnson', 'Emma', 'A'),
        ('1003', 'Williams', 'Michael', 'B')");
    
    $conn->exec("INSERT IGNORE INTO attendance_sessions (course_id, group_id, session_date, opened_by, status) VALUES 
        ('MATH101', 'A', CURDATE(), 1, 'open'),
        ('PHY101', 'B', CURDATE(), 1, 'closed')");
    
    echo "Database setup complete! <a href='index.php'>Go to System</a>";
} else {
    echo "Database connection failed. Check XAMPP MySQL.";
}
?>