<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// 1. DATABASE FIRST - Move this to the top so $conn is ready
if (file_exists("../config/database.php")) {
    include("../config/database.php");
} else {
    die("Database config file not found at ../config/database.php");
}

// 2. Define $teacher_name
$teacher_name = $_SESSION['teacher'] ?? $_SESSION['teacher_name'] ?? $_SESSION['name'] ?? "Instructor";

// 3. Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../login.php");
    exit();
}

// 4. HANDLE DELETE ACTION (SAFE VERSION)
if (isset($_GET['del_course'])) {

    $course_id = $_GET['del_course'];

    $sql = "DELETE FROM courses WHERE id = '$course_id'";
    $run = mysqli_query($conn, $sql);

    if (!$run) {
        die("SQL ERROR: " . mysqli_error($conn));
    }

    header("Location: add_course.php?msg=Course Deleted Successfully");
    exit();
}
// 5. HANDLE REGISTER FORM
$message = "";
if (isset($_POST['register'])) {
    $c_name = mysqli_real_escape_string($conn, $_POST['course_name']);
    $c_code = mysqli_real_escape_string($conn, $_POST['course_code']);

    $query = "INSERT INTO courses (course_name, course_code, teacher_name) VALUES ('$c_name', '$c_code', '$teacher_name')";

    if (mysqli_query($conn, $query)) {
        $message = "<div style='color:green; font-weight:bold; margin-bottom:15px;'>Course Registered Successfully! ✅</div>";
    } else {
        $message = "<div style='color:red; margin-bottom:15px;'>SQL Error: " . mysqli_error($conn) . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register Course | RECTEM CBT</title>
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

        .form-input {
            width: 100%;
            padding: 12px;
            border: 1px solid #dce1e4;
            border-radius: 6px;
            box-sizing: border-box;
        }

        .course-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            background: white;
        }

        .course-table th,
        .course-table td {
            padding: 12px;
            border: 1px solid #eee;
            text-align: left;
        }

        .btn-del {
            color: white;
            background: white;
            padding: 6px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }

        .btn-del:hover {
            background: #c0392b;
        }

        .btn-del {
            color: #e74c3c;
            text-decoration: none;
            font-weight: bold;
        }

        .btn-del:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <?php include("../includes/teacher_sidebar.php"); ?>

    <div class="main">
        <div class="topbar">
            <h2 style="margin:0;">Register New Course</h2>
            <div>Teacher: <strong><?php echo $teacher_name; ?></strong></div>
        </div>

        <div class="container">
            <?php if (isset($_GET['msg'])) echo "<div style='color:orange; font-weight:bold; margin-bottom:15px;'>" . htmlspecialchars($_GET['msg']) . "</div>"; ?>

            <div class="card" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <?php echo $message; ?>
                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">Course Name</label>
                        <input type="text" name="course_name" class="form-input" placeholder="e.g. Introduction to Computing" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Course Code</label>
                        <input type="text" name="course_code" class="form-input" placeholder="e.g. COM 111" required>
                    </div>
                    <button type="submit" name="register" class="btn-primary" style="width: 100%; padding: 12px; cursor: pointer;">Create Course</button>
                </form>
            </div>

            <h3 style="margin-top:40px;">Your Registered Courses</h3>
            <table class="course-table card">
                <thead>
                    <tr style="background: #f8f9fa;">
                        <th>#</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Debugging: Check if connection exists
                    if (!isset($conn)) {
                        echo "<tr><td colspan='4' style='color:red;'>Error: Connection variable \$conn is not defined! Check database.php</td></tr>";
                    } else {
                        // Run query and catch errors
                        $sql = "SELECT * FROM courses";
                        $q = mysqli_query($conn, $sql);

                        if (!$q) {
                            // This will tell us if the table name is wrong or column is missing
                            echo "<tr><td colspan='4' style='color:red;'>SQL Error: " . mysqli_error($conn) . "</td></tr>";
                        } elseif (mysqli_num_rows($q) == 0) {
                            echo "<tr><td colspan='4' style='text-align:center;'>Database table 'questions' is empty.</td></tr>";
                        } else {
                            while ($row = mysqli_fetch_array($q)) {
                    ?>
                                <tr>
                                    <td>#<?php echo $row['id']; ?></td>

                                    <td><?php echo htmlspecialchars($row['course_code']); ?></td>

                                    <td><?php echo htmlspecialchars($row['course_name']); ?></td>

                                    <td>
                                        <a href="add_course.php?del_course=<?php echo $row['id']; ?>"
                                            class="btn-del"
                                            onclick="return confirm('Are you sure you want to delete this course?')">
                                            Delete
                                        </a>
                                    </td>
                                </tr>
                    <?php
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>