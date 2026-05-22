<?php
session_start();
include("../config/database.php");

$teacher_name = $_SESSION['teacher'] ?? $_SESSION['teacher_name'] ?? $_SESSION['name'] ?? "Instructor";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../login.php");
    exit();
}

// Handle Delete Question
if (isset($_GET['delete_id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    mysqli_query($conn, "DELETE FROM questions WHERE id = '$id'");
    header("Location: manage_questions.php?msg=Question Deleted");
    exit();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Manage Questions | RECTEM</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel=" icon" href="assets/images/launcher_iconn.png" type="image/png">
    <style>
        .main {
            margin-left: 260px;
            padding: 20px;
        }

        .q-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        .q-table th,
        .q-table td {
            padding: 12px;
            border: 1px solid #eee;
            text-align: left;
        }

        .q-table th {
            background: #2c3e50;
            color: white;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            background: #e8f5e9;
            color: #27ae60;
        }
    </style>
</head>

<body>
    <?php include("../includes/teacher_sidebar.php"); ?>

    <div class="main">
        <div class="topbar">
            <h2>Question Bank Management</h2>
            <span>Welcome, <?php echo $teacher_name; ?></span>
        </div>

        <div class="container" style="margin-top: 20px;">
            <?php if (isset($_GET['msg'])) echo "<p style='color:orange;'>" . $_GET['msg'] . "</p>"; ?>

            <div class="card">
                <table class="q-table">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Question Details</th>
                            <th>Correct</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // We use a LEFT JOIN so that even if the course is deleted, the question still shows
                        $sql = "SELECT questions.*, courses.course_name 
            FROM questions 
            LEFT JOIN courses ON questions.course_id = courses.id";

                        $res = mysqli_query($conn, $sql);

                        if (!$res) {
                            // This will print an error if your table names are wrong
                            echo "<tr><td colspan='4' style='color:red;'>Query Error: " . mysqli_error($conn) . "</td></tr>";
                        } elseif (mysqli_num_rows($res) > 0) {
                            while ($row = mysqli_fetch_assoc($res)) {
                        ?>
                                <tr>
                                    <td><span class="badge" style="background:#e3f2fd; color:#0d47a1; padding:5px; border-radius:4px;">
                                            <?php echo $row['course_name'] ?? 'General/Deleted'; ?>
                                        </span></td>
                                    <td><?php echo htmlspecialchars($row['question']); ?></td>
                                    <td><strong><?php echo $row['correct']; ?></strong></td>
                                    <td>
                                        <a href="manage_questions.php?delete_id=<?php echo $row['id']; ?>"
                                            style="color:red; text-decoration:none; font-weight:bold;"
                                            onclick="return confirm('Delete this question?')">Delete</a>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='4' style='text-align:center; padding:30px;'>
                No questions found in the database. <br>
                <a href='add_question.php' class='btn-primary' style='text-decoration:none; display:inline-block; margin-top:10px;'>Add First Question</a>
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