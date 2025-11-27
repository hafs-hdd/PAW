<?php
session_start();
require_once 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Attendance System</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>

  <nav aria-label="Main navigation">
    <ul>
      <li><a href="index.php">Home</a></li>
      <li><a href="take_attendance.php">Take Attendance</a></li>
      <li><a href="create_session.php">Create Session</a></li>
      <li><a href="list_sessions.php">View Sessions</a></li>
      <li><a href="list_students.php">All Students</a></li>
    </ul>
  </nav>

  <div class="container">

    <h1>Student Attendance System</h1>

    <div class="search-container">
      <label for="nameSearch">
        <span aria-hidden="true">🔍</span>
        Search by Name:
      </label>
      <input type="text" id="nameSearch" placeholder="Enter first or last name..." class="search-input">
    </div>

    <div class="report-controls">
      <button id="showReportBtn">
        <span aria-hidden="true">📊</span> Show Report
      </button>
      <button id="highlightExcellentBtn" class="highlight-btn">
        <span aria-hidden="true">⭐</span> Highlight Excellent
      </button>
      <button id="resetColorsBtn" class="reset-btn">
        <span aria-hidden="true">🔄</span> Reset Colors
      </button>
    </div>

    <div class="sorting-controls">
      <button id="sortAbsencesAsc" class="sort-btn absence-sort">
        Sort by Absences (Ascending)
      </button>
      <button id="sortParticipationDesc" class="sort-btn participation-sort">
        Sort by Participation (Descending)
      </button>
    </div>

    <h2>Attendance List</h2>

    <div class="table-container">
      <table id="mainTable" role="table">
        <thead>
          <tr>
            <th rowspan="2">Last Name</th>
            <th rowspan="2">First Name</th>
            <th rowspan="2">Group</th>

            <th colspan="2">S1</th>
            <th colspan="2">S2</th>
            <th colspan="2">S3</th>
            <th colspan="2">S4</th>
            <th colspan="2">S5</th>
            <th colspan="2">S6</th>

            <th rowspan="2">Absences</th>
            <th rowspan="2">Participation</th>
            <th rowspan="2">Message</th>
          </tr>
          <tr>
            <th>P</th><th>Pa</th>
            <th>P</th><th>Pa</th>
            <th>P</th><th>Pa</th>
            <th>P</th><th>Pa</th>
            <th>P</th><th>Pa</th>
            <th>P</th><th>Pa</th>
          </tr>
        </thead>

        <tbody id="attendance-body">
          <?php
          $conn = getDBConnection();
          if ($conn) {
              try {
                  $stmt = $conn->query("SELECT * FROM students ORDER BY last_name, first_name");
                  $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
                  
                  foreach ($students as $student) {
                      echo '<tr data-student-id="' . $student['student_id'] . '">';
                      echo '<td>' . htmlspecialchars($student['last_name']) . '</td>';
                      echo '<td>' . htmlspecialchars($student['first_name']) . '</td>';
                      echo '<td>' . htmlspecialchars($student['group_name']) . '</td>';
                      
                      for ($i = 0; $i < 12; $i++) {
                          echo '<td></td>';
                      }
                      
                      echo '<td>0</td><td>0</td><td>New student</td>';
                      echo '</tr>';
                  }
              } catch(PDOException $e) {
                  echo '<tr><td colspan="18">No students in database</td></tr>';
              }
          }
          ?>
        </tbody>
      </table>
    </div>

    <div id="sortMessage" class="sort-message"></div>

    <div id="reportArea" class="report-area hidden">
      <h2>Report</h2>
      <p id="totalStudents">Total Students: 0</p>
      <p id="presentCount">Present (students with ≥1 P): 0</p>
      <p id="participationCount">Participated (students with ≥1 Pa): 0</p>

      <h3>Chart</h3>
      <div class="chart-container">
        <div class="bar">
          <div id="barTotal" class="bar-fill" style="height:0px"></div>
          <div class="bar-label">Total</div>
        </div>
        <div class="bar">
          <div id="barPresent" class="bar-fill bar-present" style="height:0px"></div>
          <div class="bar-label">Present</div>
        </div>
        <div class="bar">
          <div id="barParticipation" class="bar-fill bar-part" style="height:0px"></div>
          <div class="bar-label">Participated</div>
        </div>
      </div>
    </div>

    <h2>Add New Student</h2>

    <form id="add-student-form" class="add-form" method="POST" action="add_student.php">
      <div class="form-group">
        <label for="studentId">Student ID:</label>
        <input type="text" id="studentId" name="studentId" required>
        <div id="studentIdError" class="error-message"></div>
      </div>

      <div class="form-group">
        <label for="lastName">Last Name:</label>
        <input type="text" id="lastName" name="lastName" required>
        <div id="lastNameError" class="error-message"></div>
      </div>

      <div class="form-group">
        <label for="firstName">First Name:</label>
        <input type="text" id="firstName" name="firstName" required>
        <div id="firstNameError" class="error-message"></div>
      </div>

      <div class="form-group">
        <label for="group">Group:</label>
        <input type="text" id="group" name="group" required>
        <div id="groupError" class="error-message"></div>
      </div>

      <button type="submit" class="primary-btn">Add Student</button>
      
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
    </form>

  </div>

  <div class="modal-overlay" id="modalOverlay">
    <div class="modal-box">
      <h3>Student Information</h3>
      <p id="modalContent"></p>
      <button id="closeModal">Close</button>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="script.js"></script>

</body>
</html>