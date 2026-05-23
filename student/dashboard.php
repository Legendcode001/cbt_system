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
    <link rel=" icon" href="../assets/images/launcher_iconn.png" type="image/png">

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

        .rounded-4 {
            border-radius: 16px !important;
        }

        .px-2\.5 {
            padding-left: 0.65rem !important;
            padding-right: 0.65rem !important;
        }

        .py-1\.5 {
            padding-top: 0.4rem !important;
            padding-bottom: 0.4rem !important;
        }

        .dashboard-welcome-card {
            border-left: 5px solid #0d6efd !important;
            /* Matches your core Bootstrap primary brand color */
            transition: transform 0.2s ease;
        }

        .dashboard-welcome-card:hover {
            transform: translateY(-2px);
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

        <!-- 1. GREETING COMPONENT CONTAINER -->
        <div class="p-4 mb-4 bg-white border-0 shadow-sm rounded-4 position-relative overflow-hidden dashboard-welcome-card">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <!-- Dynamic Time-of-Day Greeting Prefix -->
                    <span class="text-uppercase tracking-wider text-primary fw-bold" style="font-size: 11px; letter-spacing: 1px;">
                        <?php
                        date_default_timezone_set('Africa/Lagos'); // Keeps time synced with your environment
                        $hour = date('H');
                        if ($hour < 12) {
                            echo "🌅 Good Morning";
                        } elseif ($hour < 16) {
                            echo "☀️ Good Afternoon";
                        } else {
                            echo "🌙 Good Evening";
                        }
                        ?>
                    </span>

                    <!-- Formatted Student Full Name Display -->
                    <h2 class="mt-1 mb-2 fw-bold text-dark" style="font-family: 'Sora', 'Segoe UI', sans-serif;">
                        <?php
                        if (!empty($_SESSION['student_name'])) {
                            echo ucwords(strtolower($_SESSION['student_name']));
                        } else {
                            echo "Student Portal";
                        }
                        ?>
                    </h2>

                    <!-- Student Metadata Row -->
                    <div class="d-flex flex-wrap gap-2 align-items-center mt-2 text-muted" style="font-size: 13px;">
                        <span class="badge bg-light text-secondary border px-2.5 py-1.5 rounded-3 fw-medium">
                            🆔 <?php echo $_SESSION['student_reg_no'] ?? 'No Matric Key Found'; ?>
                        </span>
                        <span class="mx-1">•</span>
                        <span>Active Examination Session</span>
                    </div>
                </div>

                <!-- Quick Access Metric Right-Side (Optional visual anchor) -->
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-2 bg-success bg-opacity-10 text-success rounded-pill" style="font-size: 12px; font-weight: 600;">
                        <span class="d-inline-block bg-success rounded-circle" style="width: 8px; height: 8px;"></span>
                        Portal Connection Verified
                    </div>
                </div>
            </div>
        </div>


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