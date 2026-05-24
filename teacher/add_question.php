<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (file_exists("../config/database.php")) {
    include("../config/database.php");
} else {
    die("Error: database.php not found at ../config/database.php");
}

$teacher_name = $_SESSION['teacher'] ?? $_SESSION['teacher_name'] ?? $_SESSION['name'] ?? "Instructor";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../login.php");
    exit();
}

$status_msg = "";
if (isset($_POST['save_question'])) {
    $course_id = mysqli_real_escape_string($conn, $_POST['course_id']);
    $question  = mysqli_real_escape_string($conn, $_POST['question']);
    $optA      = mysqli_real_escape_string($conn, $_POST['optA']);
    $optB      = mysqli_real_escape_string($conn, $_POST['optB']);
    $optC      = mysqli_real_escape_string($conn, $_POST['optC']);
    $optD      = mysqli_real_escape_string($conn, $_POST['optD']);
    $correct   = mysqli_real_escape_string($conn, $_POST['correct']);

    $verify_course = mysqli_query($conn, "SELECT id FROM courses WHERE id='$course_id' AND teacher_name='$teacher_name'");
    if (mysqli_num_rows($verify_course) > 0) {
        $sql = "INSERT INTO questions (course_id, question, optA, optB, optC, optD, correct) 
                VALUES ('$course_id', '$question', '$optA', '$optB', '$optC', '$optD', '$correct')";

        if (mysqli_query($conn, $sql)) {
            $status_msg = "<div class='alert alert-success border-0 shadow-sm p-3 mb-3' style='font-size: 14px; border-radius: 8px;'><strong>Success!</strong> Question has been added to your bank. ✅</div>";
        } else {
            $status_msg = "<div class='alert alert-danger border-0 shadow-sm p-3 mb-3' style='font-size: 14px; border-radius: 8px;'><strong>Database Error:</strong> " . mysqli_error($conn) . "</div>";
        }
    } else {
        $status_msg = "<div class='alert alert-danger border-0 shadow-sm p-3 mb-3' style='font-size: 14px; border-radius: 8px;'><strong>Authorization Error:</strong> Invalid course target configuration.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Question | RECTEM CBT</title>
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

        .btn-submit {
            background: #0284c7;
            color: #ffffff;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
            border: none;
        }

        .btn-submit:hover {
            background: #0369a1;
        }
    </style>
</head>

<body>

    <?php include("../includes/teacher_sidebar.php"); ?>

    <div class="main-content">
        <div class="mb-4 pt-2">
            <h2 class="page-title">Add New Question</h2>
            <p class="page-subtitle">Append structured multiple-choice questions to your assigned curriculums</p>
        </div>
        
        <div class="row">
            <div class="col-xl-10">
                <?php echo $status_msg; ?>
                <div class="card border-0 shadow-sm p-4">
                    <form action="add_question.php" method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark mb-2" style="font-size: 13.5px;">Target Course Scope</label>
                            <select name="course_id" class="form-select" required>
                                <option value="">-- Select Registered Course --</option>
                                <?php
                                $courses = mysqli_query($conn, "SELECT * FROM courses WHERE teacher_name='$teacher_name'");
                                while ($c = mysqli_fetch_assoc($courses)) {
                                    echo "<option value='" . $c['id'] . "'>" . htmlspecialchars($c['course_code']) . " &mdash; " . htmlspecialchars($c['course_name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark mb-2" style="font-size: 13.5px;">Question Specification</label>
                            <textarea name="question" class="form-control" rows="4" placeholder="Type the examination query context here..." required></textarea>
                        </div>
                        
                        <h5 class="fw-bold text-dark mb-3" style="font-size: 14px;"><i class="fas fa-list-ol me-1 text-secondary"></i> Option Vectors</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-semibold mb-1">Option A</label>
                                <input type="text" name="optA" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-semibold mb-1">Option B</label>
                                <input type="text" name="optB" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-semibold mb-1">Option C</label>
                                <input type="text" name="optC" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-semibold mb-1">Option D</label>
                                <input type="text" name="optD" class="form-control" required>
                            </div>
                        </div>
                        
                        <hr class="my-4" style="border-color: #e2e8f0;">
                        
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
                            <div style="width: 100%; max-width: 260px;">
                                <label class="form-label text-success mb-2 fw-bold" style="font-size: 13.5px;"><i class="fas fa-check-circle me-1"></i> Solution Key</label>
                                <select name="correct" class="form-select border-success-subtle text-success fw-bold" style="background-color: #f0fdf4;">
                                    <option value="A">Option A</option>
                                    <option value="B">Option B</option>
                                    <option value="C">Option C</option>
                                    <option value="D">Option D</option>
                                </select>
                            </div>
                            <button type="submit" name="save_question" class="btn-submit px-4"><i class="fas fa-paper-plane me-2"></i> Deploy Question</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>