<?php
session_start();
include("../config/database.php");

$teacher_name = $_SESSION['teacher'] ?? $_SESSION['teacher_name'] ?? $_SESSION['name'] ?? "Instructor";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Student Results | RECTEM CBT</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel=" icon" href="assets/images/launcher_iconn.png" type="image/png">
    <style>
        .main {
            margin-left: 260px;
            padding: 0;
        }

        .container {
            padding: 30px;
        }

        .res-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .res-table th,
        .res-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        .res-table th {
            background: #2c3e50;
            color: white;
            font-weight: 500;
        }

        .score-pill {
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: bold;
            background: #e8f5e9;
            color: #2e7d32;
        }

        .fail-pill {
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: bold;
            background: #ffebee;
            color: #c62828;
        }
    </style>
</head>

<body>

    <?php include("../includes/teacher_sidebar.php"); ?>

    <div class="main">
        <div class="topbar">
            <h2 style="margin:0;">Student Exam Performance</h2>
            <div class="user">Viewing Results for: <strong><?php echo $teacher_name; ?></strong></div>
        </div>

        <div class="container">
            <div class="card">
                <table class="res-table">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Reg No</th>
                            <th>Course</th>
                            <th>Score (%)</th>
                            <th>Date Completed</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Replace your existing $sql block with this one
                        $sql = "SELECT r.*, c.course_name, c.course_code 
        FROM results r 
        LEFT JOIN courses c ON r.course_id = c.id 
        WHERE c.teacher_name = '$teacher_name' 
        ORDER BY r.date_taken DESC";

                        $res = mysqli_query($conn, $sql);

                        if (!$res) {
                            // This will print the exact database error on the screen if it fails again
                            echo "<tr><td colspan='5' style='color:red; padding:20px;'>
            <strong>Database Error:</strong> " . mysqli_error($conn) . "
          </td></tr>";
                        } elseif (mysqli_num_rows($res) > 0) {
                            while ($row = mysqli_fetch_assoc($res)) {
                                // Calculate percentage safely to avoid division by zero
                                $total = ($row['total_questions'] > 0) ? $row['total_questions'] : 1;
                                $percentage = ($row['score'] / $total) * 100;

                                $pill_class = ($percentage >= 50) ? 'score-pill' : 'fail-pill';

                                echo "<tr>
                <td>" . htmlspecialchars(strtoupper($row['student_name'])) . "</td>
                <td>" . htmlspecialchars($row['student_reg_no']) . "</td>
                <td>" . htmlspecialchars($row['course_code'] ?? 'N/A') . "</td>
                <td><span class='$pill_class'>" . round($percentage) . "%</span></td>
                <td>" . date('M d, Y', strtotime($row['date_taken'])) . "</td>
              </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' style='text-align:center; padding:30px;'>No student records found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>