<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (file_exists("../config/database.php")) {
    include("../config/database.php");
} else {
    die("Database config file not found at ../config/database.php");
}

$teacher_name = $_SESSION['teacher'] ?? $_SESSION['teacher_name'] ?? $_SESSION['name'] ?? "Instructor";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['del_course'])) {
    $course_id = mysqli_real_escape_string($conn, $_GET['del_course']);
    $sql = "DELETE FROM courses WHERE id = '$course_id' AND teacher_name = '$teacher_name'";
    mysqli_query($conn, $sql);
    header("Location: add_course.php?msg=Course Deleted Successfully");
    exit();
}

$message = "";
if (isset($_POST['register'])) {
    $c_name = mysqli_real_escape_string($conn, $_POST['course_name']);
    $c_code = mysqli_real_escape_string($conn, $_POST['course_code']);
    $query = "INSERT INTO courses (course_name, course_code, teacher_name) VALUES ('$c_name', '$c_code', '$teacher_name')";
    if (mysqli_query($conn, $query)) {
        $message = "<div class='alert alert-success shadow-sm p-2.5' style='font-size: 14px;'>Course Registered Successfully! ✅</div>";
    } else {
        $message = "<div class='alert alert-danger shadow-sm p-2.5' style='font-size: 14px;'>SQL Error: " . mysqli_error($conn) . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Course | RECTEM CBT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" href="../assets/images/launcher_iconn.png" type="image/png">
    <style>
        body {
            font-family: 'Sora', sans-serif;
            background: #f8fafc;
            font-size: 14px;
            color: #1e293b;
        }

        .main-content {
            margin-top: 75px;
            margin-left: 260px;
            padding: 35px;
            min-height: calc(100vh - 75px);
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.5px;
        }

        .page-subtitle {
            font-size: 14px;
            color: #64748b;
            margin-top: 2px;
        }

        .card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
            background: #ffffff;
            padding: 25px !important;
        }

        .form-label {
            font-weight: 600;
            color: #334155;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 11px 15px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            color: #334155;
            transition: all 0.2s;
        }

        .form-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        .table th {
            background: #1e293b !important;
            color: white !important;
            font-weight: 600;
            font-size: 14px;
            padding: 14px 12px !important;
        }

        .table td {
            font-size: 14px;
            padding: 14px 12px !important;
            color: #334155;
        }

        .badge-code {
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            background: #eff6ff;
            color: #1d4ed8;
        }

        .btn-del {
            color: #ef4444;
            font-weight: 600;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-del:hover {
            color: #b91c1c;
            text-decoration: underline;
        }

        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
                margin-top: 70px;
            }
        }
    </style>
</head>

<body>
    <?php include("../includes/teacher_sidebar.php"); ?>
    <div class="main-content">
        <div class="mb-4">
            <h2 class="page-title">Course Registration Terminal</h2>
            <p class="page-subtitle">Register and govern custom modules mapped directly to your instructor context</p>
        </div>

        <?php if (isset($_GET['msg'])) echo "<div class='alert alert-warning shadow-sm p-2.5 mb-3' style='font-size:14px;'>" . htmlspecialchars($_GET['msg']) . "</div>"; ?>

        <div class="row g-4">
            <div class="col-xl-4">
                <div class="card">
                    <h4 class="fw-bold text-dark mb-3" style="font-size: 16px;">Add New Stream</h4>
                    <?php echo $message; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Course Name</label>
                            <input type="text" name="course_name" class="form-input" placeholder="e.g., Introduction to Computing" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Course Code</label>
                            <input type="text" name="course_code" class="form-input" placeholder="e.g., COM 111" required>
                        </div>
                        <button type="submit" name="register" class="btn btn-dark w-100 py-2.5 fw-bold mt-1" style="border-radius:8px; font-size:14px; background:#1e293b;">Create Course</button>
                    </form>
                </div>
            </div>
            <div class="col-xl-8">
                <div class="card">
                    <h4 class="fw-bold text-dark mb-3" style="font-size: 16px;">Your Isolated Curriculums</h4>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle m-0">
                            <thead>
                                <tr>
                                    <th style="border-top-left-radius: 8px;">ID</th>
                                    <th>Course Code</th>
                                    <th>Course Name</th>
                                    <th class="text-center" style="border-top-right-radius: 8px;">Action Target</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM courses WHERE teacher_name = '$teacher_name' ORDER BY id DESC";
                                $q = mysqli_query($conn, $sql);
                                if (mysqli_num_rows($q) == 0) {
                                    echo "<tr><td colspan='4' class='text-muted text-center py-4'>No streams registered yet.</td></tr>";
                                } else {
                                    while ($row = mysqli_fetch_array($q)) {
                                        echo "<tr>
                                                <td class='text-secondary fw-semibold'>#{$row['id']}</td>
                                                <td><span class='badge-code'>{$row['course_code']}</span></td>
                                                <td class='fw-semibold text-dark'>" . htmlspecialchars($row['course_name']) . "</td>
                                                <td class='text-center'>
                                                    <a href='add_course.php?del_course={$row['id']}' class='btn-del' onclick=\"return confirm('Confirm deletion?')\"><i class='fas fa-trash-alt me-1'></i> Delete</a>
                                                </td>
                                              </tr>";
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>