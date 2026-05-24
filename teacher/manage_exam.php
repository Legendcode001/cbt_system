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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Results | RECTEM CBT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" href="../assets/images/launcher_iconn.png" type="image/png">
    
    <style>
        .page-title {
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.5px;
        }

        .page-subtitle {
            font-size: 13.5px;
            color: #64748b;
            margin-top: 2px;
        }

        /* Clean matching styles for the table matrix cells */
        .table th {
            background: #f8fafc !important;
            color: #64748b !important;
            font-weight: 600;
            font-size: 13.5px;
            padding: 14px 16px !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }

        .table td {
            font-size: 14px;
            padding: 14px 16px !important;
            color: #334155;
            border-bottom: 1px solid #f1f5f9 !important;
        }

        /* Passing Status Pill */
        .score-pill {
            padding: 5px 14px;
            border-radius: 8px;
            font-weight: 700;
            background: #f0fdf4;
            color: #16a34a;
            font-size: 12.5px;
            border: 1px solid rgba(22, 163, 74, 0.15);
            display: inline-block;
        }

        /* Failing Status Pill */
        .fail-pill {
            padding: 5px 14px;
            border-radius: 8px;
            font-weight: 700;
            background: #fef2f2;
            color: #ef4444;
            font-size: 12.5px;
            border: 1px solid rgba(239, 68, 68, 0.15);
            display: inline-block;
        }

        .badge-course {
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 12px;
            background: #f1f5f9;
            color: #475569;
            font-weight: 600;
            border: 1px solid #e2e8f0;
        }
    </style>
</head>

<body>

    <?php include("../includes/teacher_sidebar.php"); ?>

    <div class="main-content">
        <div class="mb-4 pt-2">
            <h2 class="page-title">Student Exam Performance</h2>
            <p class="page-subtitle">Real-time grades processed instantly across modules assigned to your profile</p>
        </div>
        
        <div class="card border-0 shadow-sm p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle m-0">
                    <thead>
                        <tr>
                            <th style="border-top-left-radius: 8px;">Student Name</th>
                            <th>Reg No</th>
                            <th>Target Course</th>
                            <th>Score (%)</th>
                            <th style="border-top-right-radius: 8px;">Date Completed</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT r.*, c.course_name, c.course_code 
                                FROM results r 
                                LEFT JOIN courses c ON r.course_id = c.id 
                                WHERE c.teacher_name = '$teacher_name' 
                                ORDER BY r.date_taken DESC";

                        $res = mysqli_query($conn, $sql);

                        if (mysqli_num_rows($res) > 0) {
                            while ($row = mysqli_fetch_assoc($res)) {
                                $total = ($row['total_questions'] > 0) ? $row['total_questions'] : 1;
                                $percentage = ($row['score'] / $total) * 100;
                                $pill_class = ($percentage >= 50) ? 'score-pill' : 'fail-pill';

                                echo "<tr>
                                        <td class='fw-bold text-dark' style='font-size: 13.5px;'>" . htmlspecialchars(strtoupper($row['student_name'])) . "</td>
                                        <td class='text-secondary fw-medium' style='font-size: 13.5px;'>" . htmlspecialchars($row['student_reg_no']) . "</td>
                                        <td><span class='badge-course'>" . htmlspecialchars($row['course_code'] ?? 'N/A') . "</span></td>
                                        <td><span class='$pill_class'>" . round($percentage) . "%</span></td>
                                        <td class='text-muted fw-medium' style='font-size: 13.5px;'>" . date('M d, Y', strtotime($row['date_taken'])) . "</td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center text-muted py-4' style='font-size: 14px;'>No student records recorded.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>