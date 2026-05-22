<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// 1. Database Connection
if (file_exists("../config/database.php")) {
    include("../config/database.php");
} else {
    die("Error: database.php not found at ../config/database.php");
}

// 2. Identify Teacher
$teacher_name = $_SESSION['teacher'] ?? $_SESSION['teacher_name'] ?? $_SESSION['name'] ?? "Instructor";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../login.php");
    exit();
}

// 3. Handle Form Submission
$status_msg = "";
if (isset($_POST['save_question'])) {
    // Sanitize Inputs
    $course_id = mysqli_real_escape_string($conn, $_POST['course_id']);
    $question  = mysqli_real_escape_string($conn, $_POST['question']);
    $optA      = mysqli_real_escape_string($conn, $_POST['optA']);
    $optB      = mysqli_real_escape_string($conn, $_POST['optB']);
    $optC      = mysqli_real_escape_string($conn, $_POST['optC']);
    $optD      = mysqli_real_escape_string($conn, $_POST['optD']);
    $correct   = mysqli_real_escape_string($conn, $_POST['correct']);

    // INSERT Query - Verify these column names match your DB exactly
    $sql = "INSERT INTO questions (course_id, question, optA, optB, optC, optD, correct) 
            VALUES ('$course_id', '$question', '$optA', '$optB', '$optC', '$optD', '$correct')";

    if (mysqli_query($conn, $sql)) {
        $status_msg = "<div style='padding:15px; background:#d4edda; color:#155724; border-radius:5px; margin-bottom:20px;'>
                        <strong>Success!</strong> Question has been added to the bank.
                      </div>";
    } else {
        // This will print the EXACT error if it fails
        $status_msg = "<div style='padding:15px; background:#f8d7da; color:#721c24; border-radius:5px; margin-bottom:20px;'>
                        <strong>Database Error:</strong> " . mysqli_error($conn) . "
                      </div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Question | RECTEM CBT</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel=" icon" href="assets/images/launcher_iconn.png" type="image/png">
    <style>
        .main {
            margin-left: 260px;
            padding: 0;
        }

        .container {
            padding: 30px;
            max-width: 900px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #34495e;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #dce1e4;
            border-radius: 6px;
            box-sizing: border-box;
            font-family: inherit;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .btn-submit {
            background: #2c3e50;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        .btn-submit:hover {
            background: #34495e;
        }
    </style>
</head>

<body>

    <?php include("../includes/teacher_sidebar.php"); ?>

    <div class="main">
        <div class="topbar">
            <h2 style="margin:0;">Add New Question</h2>
            <div>Logged in: <strong><?php echo $teacher_name; ?></strong></div>
        </div>

        <div class="container">
            <?php echo $status_msg; ?>

            <div class="card" style="background:white; padding:30px; border-radius:10px; box-shadow:0 4px 15px rgba(0,0,0,0.05);">
                <form action="add_question.php" method="POST">

                    <div class="form-group">
                        <label class="form-label">Target Course</label>
                        <select name="course_id" class="form-control" required>
                            <option value="">-- Select Course --</option>
                            <?php
                            // Fetch courses assigned to this teacher
                            $courses = mysqli_query($conn, "SELECT * FROM courses WHERE teacher_name='$teacher_name'");
                            while ($c = mysqli_fetch_assoc($courses)) {
                                echo "<option value='" . $c['id'] . "'>" . $c['course_code'] . " - " . $c['course_name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Question Text</label>
                        <textarea name="question" class="form-control" rows="4" placeholder="Enter the examination question here..." required></textarea>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Option A</label>
                            <input type="text" name="optA" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Option B</label>
                            <input type="text" name="optB" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Option C</label>
                            <input type="text" name="optC" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Option D</label>
                            <input type="text" name="optD" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group" style="width: 200px;">
                        <label class="form-label">Correct Option</label>
                        <select name="correct" class="form-control" style="border: 2px solid #27ae60;">
                            <option value="A">Option A</option>
                            <option value="B">Option B</option>
                            <option value="C">Option C</option>
                            <option value="D">Option D</option>
                        </select>
                    </div>

                    <button type="submit" name="save_question" class="btn-submit">
                        Save Question to Bank
                    </button>
                </form>
            </div>
        </div>
    </div>

</body>

</html>