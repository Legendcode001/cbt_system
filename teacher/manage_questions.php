<?php
session_start();
include("../config/database.php");

$teacher_name = $_SESSION['teacher'] ?? $_SESSION['teacher_name'] ?? $_SESSION['name'] ?? "Instructor";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../login.php");
    exit();
}

// Handle Delete Question securely
if (isset($_GET['delete_id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete_id']);

    $verify_ownership = "SELECT q.id FROM questions q 
                         INNER JOIN courses c ON q.course_id = c.id 
                         WHERE q.id = '$id' AND c.teacher_name = '$teacher_name'";

    $check_result = mysqli_query($conn, $verify_ownership);

    if (mysqli_num_rows($check_result) > 0) {
        mysqli_query($conn, "DELETE FROM questions WHERE id = '$id'");
        header("Location: manage_questions.php?msg=Question Deleted Successfully");
    } else {
        header("Location: manage_questions.php?msg=Unauthorized Action Request");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Questions | RECTEM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" href="../assets/images/launcher_iconn.png" type="image/png">
    <style>
        body {
            font-family: 'Sora', sans-serif;
            background: #f8fafc;
            font-size: 16px;
            color: #1e293b;
        }

        .main-content {
            margin-top: 80px;
            margin-left: 280px;
            padding: 45px;
            min-height: calc(100vh - 80px);
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.5px;
        }

        .page-subtitle {
            font-size: 15px;
            color: #64748b;
            margin-top: 4px;
        }

        .card {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.02);
            background: #ffffff;
            padding: 35px !important;
        }

        .table th {
            background: #1e293b !important;
            color: white !important;
            font-weight: 600;
            font-size: 15px;
            padding: 18px 16px !important;
        }

        .table td {
            font-size: 15px;
            padding: 22px 16px !important;
            color: #334155;
        }

        .badge-course {
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 13px;
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 600;
            display: inline-block;
        }

        .badge-correct {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            background: #dcfce7;
            color: #15803d;
            font-weight: 700;
        }

        .btn-drop {
            color: #ef4444;
            font-weight: 600;
            text-decoration: none;
            transition: 0.2s;
            font-size: 15px;
        }

        .btn-drop:hover {
            color: #b91c1c;
            text-decoration: underline;
        }

        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                padding: 30px 20px;
                margin-top: 70px;
            }
        }
    </style>
</head>

<body>
    <?php include("../includes/teacher_sidebar.php"); ?>

    <div class="main-content">
        <div class="mb-5">
            <h2 class="page-title">Question Bank Management</h2>
            <p class="page-subtitle">Review, update, or clear configuration items from your active testing inventories</p>
        </div>

        <?php if (isset($_GET['msg'])) echo "<div class='alert alert-info shadow-sm p-3 mb-4' style='font-size:15px;'>" . htmlspecialchars($_GET['msg']) . "</div>"; ?>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle m-0">
                    <thead>
                        <tr>
                            <th style="border-top-left-radius: 10px; width: 25%;">Course Scope</th>
                            <th style="width: 50%;">Question Details</th>
                            <th class="text-center" style="width: 13%;">Key</th>
                            <th class="text-center" style="border-top-right-radius: 10px; width: 12%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT questions.*, courses.course_name, courses.course_code 
                                FROM questions 
                                INNER JOIN courses ON questions.course_id = courses.id
                                WHERE courses.teacher_name = '$teacher_name'
                                ORDER BY questions.id DESC";

                        $res = mysqli_query($conn, $sql);

                        if (!$res) {
                            echo "<tr><td colspan='4' class='text-danger'>Query Error: " . mysqli_error($conn) . "</td></tr>";
                        } elseif (mysqli_num_rows($res) > 0) {
                            while ($row = mysqli_fetch_assoc($res)) {
                        ?>
                                <tr>
                                    <td>
                                        <span class="badge-course">
                                            <?php echo htmlspecialchars($row['course_code'] . " - " . $row['course_name']); ?>
                                        </span>
                                    </td>
                                    <td class="text-dark fw-medium" style="white-space: normal; word-wrap: break-word; line-height: 1.6;">
                                        <?php echo htmlspecialchars($row['question']); ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge-correct">Option <?php echo $row['correct']; ?></span>
                                    </td>
                                    <td class="text-center">
                                        <a href="manage_questions.php?delete_id=<?php echo $row['id']; ?>"
                                            class="btn-drop"
                                            onclick="return confirm('Are you sure you want to drop this target question record?')">
                                            <i class="fas fa-trash-alt me-1"></i> Drop
                                        </a>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center text-muted py-5' style='font-size: 15px;'>
                                    <i class='fas fa-folder-open fa-2x mb-3 d-block text-secondary' style='opacity: 0.5;'></i>
                                    No custom question instances found matching your credentials.<br>
                                    <a href='add_question.php' class='btn btn-primary btn-sm mt-3 px-4 py-2 fw-semibold' style='font-size: 14px; border-radius: 8px;'>Add First Question</a>
                                  </td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>