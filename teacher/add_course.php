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
        $message = "<div class='alert alert-success border-0 shadow-sm p-3 mb-3' style='font-size: 14px; border-radius: 8px;'>Course Registered Successfully! ✅</div>";
    } else {
        $message = "<div class='alert alert-danger border-0 shadow-sm p-3 mb-3' style='font-size: 14px; border-radius: 8px;'>SQL Error: " . mysqli_error($conn) . "</div>";
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

        .badge-code {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid rgba(22, 163, 74, 0.15);
        }

        .btn-del {
            color: #ef4444;
            font-weight: 600;
            text-decoration: none;
            font-size: 13.5px;
            padding: 6px 12px;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .btn-del:hover {
            color: #b91c1c;
            background: rgba(239, 68, 68, 0.05);
        }
    </style>
</head>

<body>

    <?php include("../includes/teacher_sidebar.php"); ?>
    
    <div class="main-content">
        <div class="mb-4 pt-2">
            <h2 class="page-title">Course Registration Terminal</h2>
            <p class="page-subtitle">Register and govern custom modules mapped directly to your instructor context</p>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div class='alert alert-warning border-0 shadow-sm p-3 mb-4' style='font-size:14px; border-radius: 8px;'>
                <i class="fas fa-exclamation-triangle me-2"></i> <?php echo htmlspecialchars($_GET['msg']); ?>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-xl-4">
                <div class="card border-0 shadow-sm p-4">
                    <h4 class="fw-bold text-dark mb-3" style="font-size: 16px;">Add New Stream</h4>
                    <?php echo $message; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark mb-2" style="font-size: 13.5px;">Course Name</label>
                            <input type="text" name="course_name" class="form-control" placeholder="e.g., Introduction to Computing" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark mb-2" style="font-size: 13.5px;">Course Code</label>
                            <input type="text" name="course_code" class="form-control" placeholder="e.g., COM 111" required>
                        </div>
                        <button type="submit" name="register" class="btn btn-primary w-100 py-2.5 fw-bold mt-2" style="border-radius:8px; font-size:14px;">
                            <i class="fas fa-plus me-1"></i> Create Course
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="col-xl-8">
                <div class="card border-0 shadow-sm p-4">
                    <h4 class="fw-bold text-dark mb-3" style="font-size: 16px;">Your Isolated Curriculums</h4>
                    <div class="table-responsive">
                        <table class="table align-middle m-0">
                            <thead>
                                <tr>
                                    <th style="background: #f8fafc; color: #64748b; font-weight: 600; border-top-left-radius: 8px;">ID</th>
                                    <th style="background: #f8fafc; color: #64748b; font-weight: 600;">Course Code</th>
                                    <th style="background: #f8fafc; color: #64748b; font-weight: 600;">Course Name</th>
                                    <th class="text-center" style="background: #f8fafc; color: #64748b; font-weight: 600; border-top-right-radius: 8px;">Action Target</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM courses WHERE teacher_name = '$teacher_name' ORDER BY id DESC";
                                $q = mysqli_query($conn, $sql);
                                if (mysqli_num_rows($q) == 0) {
                                    echo "<tr><td colspan='4' class='text-muted text-center py-4' style='font-size: 14px;'>No streams registered yet.</td></tr>";
                                } else {
                                    while ($row = mysqli_fetch_array($q)) {
                                        echo "<tr>
                                                <td class='text-secondary fw-semibold' style='font-size: 13.5px;'>#{$row['id']}</td>
                                                <td><span class='badge-code'>{$row['course_code']}</span></td>
                                                <td class='fw-semibold text-dark' style='font-size: 14px;'>" . htmlspecialchars($row['course_name']) . "</td>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>