<?php
session_start();

// Check if teacher_id exists. If not, they aren't logged in.
if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <!-- Framework Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" href="../assets/images/launcher_iconn.png" type="image/png">

    <style>
        :root {
            --navy-dark: #0a1628;
            --navy-mid: #112240;
            --gold: #f4a916;
            --sidebar-width: 260px;
            --topbar-height: 70px;
        }

        body {
            font-family: 'Sora', 'Segoe UI', sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 0;
        }

        /* 1. FIXED TOP NAVIGATION BAR */
        .teacher-topbar {
            background: linear-gradient(135deg, var(--navy-dark) 0%, var(--navy-mid) 100%);
            color: #ffffff;
            height: var(--topbar-height);
            padding: 0 30px;
            box-shadow: 0 4px 12px rgba(10, 22, 40, 0.15);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            /* Stays above the sidebar */
        }

        .brand-section {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 16px;
        }

        .user-greeting-section {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .teacher-badge {
            background: rgba(244, 169, 22, 0.15);
            border: 1px solid rgba(244, 169, 22, 0.3);
            color: var(--gold);
            font-size: 11px;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* 2. ADJUSTED SIDEBAR */
        .sidebar {
            width: var(--sidebar-width);
            background: #ffffff;
            position: fixed;
            top: var(--topbar-height);
            /* Starts EXACTLY where topbar ends */
            bottom: 0;
            left: 0;
            z-index: 1020;
            overflow-y: auto;
            border-right: 1px solid #e3e6ec;
            padding: 25px 0;
            transition: all 0.3s;
        }

        .sidebar h3 {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #a0aec0;
            padding: 0 25px;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar ul li a {
            display: flex;
            align-items: center;
            padding: 12px 25px;
            color: #4a5568;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
            border-left: 4px solid transparent;
        }

        .sidebar ul li a i {
            width: 24px;
            font-size: 16px;
            color: #718096;
            transition: all 0.2s;
        }

        /* Active / Hover States */
        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background: rgba(13, 110, 253, 0.04);
            color: #0d6efd;
            border-left-color: #0d6efd;
        }

        .sidebar ul li a:hover i,
        .sidebar ul li a.active i {
            color: #0d6efd;
        }

        /* 3. MAIN WORKSPACE CONTENT WRAPPER */
        .main-content {
            margin-top: var(--topbar-height);
            /* Pushes down past topbar */
            margin-left: var(--sidebar-width);
            /* Pushes right past sidebar */
            padding: 40px;
            min-height: calc(100vh - var(--topbar-height));
        }

        /* Responsive Breakpoint for smaller viewports */
        @media (max-width: 768px) {
            .sidebar {
                left: -var(--sidebar-width);
            }

            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            .sidebar.show {
                left: 0;
            }
        }
    </style>
</head>

<body>

    <!-- 1. TOP BAR -->
    <div class="teacher-topbar">
        <div class="brand-section">
            <span style="font-size: 20px;">📋</span>
            <div>RECTEM CBT <span style="color: var(--gold); font-size: 12px; font-weight: 400; margin-left: 5px;">| Staff</span></div>
        </div>

        <div class="user-greeting-section">
            <div class="text-end d-none d-sm-block">
                <span style="font-size: 10px; color: #8899aa; display: block; font-weight: 600; text-transform: uppercase;">
                    <?php
                    date_default_timezone_set('Africa/Lagos');
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
                <span style="font-weight: 600; color: #ffffff; font-size: 14px;">
                    <?php echo isset($_SESSION['name']) ? ucwords(strtolower($_SESSION['name'])) : 'Facilitator'; ?>
                </span>
            </div>

            <span class="teacher-badge">
                🆔 Staff Code: #00<?php echo $_SESSION['teacher_id']; ?>
            </span>
        </div>
    </div>

    <!-- 2. SIDEBAR NAVIGATION -->
    <div class="sidebar">
        <h3>Teacher Menu</h3>
        <ul>
            <li>
                <a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
            </li>
            <li>
                <a href="add_question.php"><i class="fas fa-plus-circle"></i> Add Question</a>
            </li>
            <li>
                <a href="add_course.php"><i class="fas fa-book"></i> Register Course</a>
            </li>
            <li>
                <a href="manage_exam.php"><i class="fas fa-tasks"></i> Manage Exam</a>
            </li>
            <li>
                <a href="manage_questions.php"><i class="fas fa-edit"></i> Manage Questions</a>
            </li>
            <li class="mt-4 border-top pt-2">
                <a href="../logout.php" style="color: #dc3545;"><i class="fas fa-sign-out-alt" style="color: #dc3545;"></i> Logout</a>
            </li>
        </ul>
    </div>

    <!-- 3. MAIN SYSTEM WORKSPACE PANEL -->
    <div class="main-content">
        <!-- 
          Place your remaining page body content right here! 
          Example: <h2>Dashboard Overview</h2>
        -->