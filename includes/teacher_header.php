<?php
session_start();

// Check if teacher_id exists. If not, they aren't logged in.
if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../login.php");
    exit(); // Always use exit() after a header redirect!
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Teacher Dashboard</title>
    <!-- Bootstrap integration for structural components -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" href="../assets/images/launcher_iconn.png" type="image/png">

    <style>
        :root {
            --navy-dark: #0a1628;
            --navy-mid: #112240;
            --gold: #f4a916;
        }

        body {
            font-family: 'Sora', 'Segoe UI', sans-serif;
            background: #f5f7fa;
        }

        /* Modernized Top Bar */
        .teacher-topbar {
            background: linear-gradient(135deg, var(--navy-dark) 0%, var(--navy-mid) 100%);
            color: #ffffff;
            padding: 15px 30px;
            box-shadow: 0 4px 12px rgba(10, 22, 40, 0.15);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
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

        .btn-logout {
            color: rgba(255, 255, 255, 0.75);
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 6px 16px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .btn-logout:hover {
            color: #ffffff;
            background: rgba(220, 53, 69, 0.2);
            border-color: #dc3545;
        }
    </style>
</head>

<body>

    <!-- MODERNIZED TOP NAVIGATION BAR -->
    <div class="teacher-topbar">
        <!-- Left Side: Brand Context -->
        <div class="brand-section">
            <span style="font-size: 20px;">📋</span>
            <div>RECTEM CBT <span style="color: var(--gold); font-size: 12px; font-weight: 400; margin-left: 5px;">| Teacher Panel</span></div>
        </div>

        <!-- Right Side: Dynamic Greeting & Session Info -->
        <div class="user-greeting-section">
            <div class="text-end d-none d-sm-block">
                <span style="font-size: 11px; color: #8899aa; display: block; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
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

            <!-- Meta verification marker -->
            <span class="teacher-badge">
                🆔 Staff Code: #00<?php echo $_SESSION['teacher_id']; ?>
            </span>

            <!-- Action Button -->
            <a href="../logout.php" class="btn-logout">Logout</a>
        </div>
    </div>

</body>

</html>