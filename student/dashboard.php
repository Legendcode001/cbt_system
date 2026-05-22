<?php
include("../includes/auth.php");

if ($_SESSION['role'] != "student") {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>

    <title>Student Dashboard | RECTEM CBT</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lobster&family=Playfair+Display&family=Roboto&display=swap"
        rel="stylesheet">
    <link rel=" icon" href="assets/images/launcher_iconn.png" type="image/png">

    <style>
        body {
            background: #f5f7fa;
        }

        .sidebar {
            height: 100vh;
            background: #0d6efd;
            color: white;
            position: fixed;
            width: 220px;
            padding-top: 20px;
        }

        /* Control the Logo size so it doesn't break the sidebar width */
        .sidebar-logo {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }

        .sidebar a {
            color: white;
            display: block;
            padding: 12px;
            text-decoration: none;
        }

        .sidebar a:hover {
            background: #084298;
        }

        .content {
            margin-left: 230px;
            padding: 20px;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
    </style>

</head>

<body>


    <!-- SIDEBAR -->

    <div class="sidebar">
        <h4 class="mb-0 ms-2">RECTEM CBT</h4>

        <hr>

        <a href="dashboard.php">Dashboard</a>

        <a href="exam.php">Start Exam</a>

        <a href="result.php">View Results</a>

        <a href="../logout.php">Logout</a>

    </div>



    <!-- CONTENT AREA -->

    <div class="content">

        <h3>

            Welcome,
            <?php echo $_SESSION['name']; ?>

        </h3>

        <hr>


        <div class="row mt-4">

            <div class="col-md-4">

                <div class="card p-4">

                    <h5>Available Exams</h5>

                    <p>Start your assigned exams here.</p>

                    <a href="exam.php" class="btn btn-primary">

                        Start Exam

                    </a>

                </div>

            </div>



            <div class="col-md-4">

                <div class="card p-4">

                    <h5>Your Results</h5>

                    <p>Check performance after submission.</p>

                    <a href="result.php" class="btn btn-success">

                        View Results

                    </a>

                </div>

            </div>



            <div class="col-md-4">

                <div class="card p-4">

                    <h5>Instructions</h5>

                    <ul>

                        <li>No refresh during exam</li>

                        <li>Timer auto submits exam</li>

                        <li>Answer all questions</li>

                    </ul>

                </div>

            </div>

        </div>

    </div>


</body>

</html>