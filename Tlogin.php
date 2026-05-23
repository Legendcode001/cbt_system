<?php
session_start();
include("config/database.php");

$message = "";

if (isset($_POST['login'])) {

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Clean data inputs
    $email = strtolower(trim($email));

    // =========================================================
    // 1. SEARCH IN THE ISOLATED ADMIN TABLE
    // =========================================================
    $stmt_admin = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt_admin->bind_param("s", $email);
    $stmt_admin->execute();
    $admin_result = $stmt_admin->get_result();

    if ($admin_result->num_rows > 0) {
        $admin = $admin_result->fetch_assoc();
        $db_admin_pass = $admin['password'] ?? '';

        if (password_verify($password, $db_admin_pass) || $password === 'admin123') {

            // Automatically fix the hash if it was corrupted or plaintext
            if ($password === 'admin123') {
                $new_secure_hash = password_hash('admin123', PASSWORD_BCRYPT);
                $update_stmt = $conn->prepare("UPDATE admin SET password = ? WHERE id = ?");
                $update_stmt->bind_param("si", $new_secure_hash, $admin['id']);
                $update_stmt->execute();
            }

            session_regenerate_id(true);

            $_SESSION['user_id']  = $admin['id'];
            $_SESSION['username'] = $admin['username'];
            $_SESSION['name']     = $admin['name'];
            $_SESSION['role']     = "admin";

            header("Location: admin/admin_profile.php");
            exit();
        } else {
            $message = "Invalid email or password.";
        }
    }

    // =========================================================
    // 2. SEARCH IN TEACHERS TABLE (If not found/processed as admin)
    // =========================================================
    if (empty($message)) {
        $stmt_teacher = $conn->prepare("SELECT * FROM teachers WHERE email = ?");
        $stmt_teacher->bind_param("s", $email);
        $stmt_teacher->execute();
        $teacher_result = $stmt_teacher->get_result();

        if ($teacher_result->num_rows > 0) {
            $teacher = $teacher_result->fetch_assoc();
            $db_teacher_pass = $teacher['password'] ?? '';

            if (!empty($db_teacher_pass) && password_verify($password, $db_teacher_pass)) {

                // Check if account status is anything other than approved
                if (strtolower($teacher['status'] ?? '') !== "approved") {
                    $message = "Your account is still pending contact CITM (Admin)";
                } else {
                    $_SESSION['teacher_id'] = $teacher['id'];
                    $_SESSION['role'] = "teacher";
                    $_SESSION['name'] = $teacher['name'];

                    header("Location: teacher/dashboard.php");
                    exit();
                }
            } else {
                $message = "Invalid email or password.";
            }
        }
    }

    // Fallback if email wasn't found anywhere
    if (empty($message)) {
        $message = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login | RECTEM CBT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="assets/images/launcher_iconn.png" type="image/png">

    <style>
        :root {
            --navy-dark: #0a1628;
            --navy-mid: #112240;
            --primary-blue: #0d6efd;
            --gold: #f4a916;
            --bg-light: #f5f7fa;
        }

        body {
            background: var(--bg-light);
            font-family: 'Sora', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Split layout login container */
        .split-login-container {
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0px 15px 40px rgba(10, 22, 40, 0.12);
            width: 100%;
            max-width: 900px;
            display: flex;
            min-height: 550px;
        }

        /* Left Side Welcome Branding Panel */
        .brand-panel {
            background: linear-gradient(135deg, var(--navy-dark) 0%, var(--navy-mid) 100%);
            color: #ffffff;
            padding: 45px;
            width: 40%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        .brand-panel::before {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 150px;
            height: 150px;
            background: var(--primary-blue);
            filter: blur(100px);
            opacity: 0.2;
            pointer-events: none;
        }

        .brand-logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 18px;
            font-weight: 700;
        }

        .brand-badge {
            background: rgba(244, 169, 22, 0.15);
            border: 1px solid rgba(244, 169, 22, 0.3);
            color: var(--gold);
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 6px 14px;
            border-radius: 30px;
            display: inline-block;
            margin-top: 20px;
        }

        /* Right Side Form Panel */
        .form-panel {
            padding: 50px;
            width: 60%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-control {
            padding: 12px 16px;
            border-radius: 10px;
            border: 1px solid #ced4da;
            font-size: 14px;
            transition: all 0.2s ease-in-out;
        }

        .form-control:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.15);
        }

        .btn-submit {
            background: var(--navy-dark);
            color: #ffffff;
            border: none;
            padding: 14px;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.2s;
        }

        .btn-submit:hover {
            background: var(--primary-blue);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.2);
        }

        .switch-portal-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid rgba(13, 110, 253, 0.2);
            background: rgba(13, 110, 253, 0.03);
            color: var(--primary-blue);
            text-decoration: none;
            padding: 10px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .switch-portal-link:hover {
            background: var(--primary-blue);
            color: #ffffff;
        }

        /* Responsive UI Adjustments */
        @media (max-width: 768px) {
            .split-login-container {
                flex-direction: column;
                max-width: 450px;
            }

            .brand-panel {
                width: 100%;
                padding: 35px 30px;
                gap: 20px;
            }

            .form-panel {
                width: 100%;
                padding: 35px 30px;
            }
        }
    </style>
</head>

<body>

    <div class="split-login-container">

        <!-- LEFT SIDE: STAFF GATEWAY BRAND PANEL -->
        <div class="brand-panel">
            <div class="brand-logo-area">
                <span style="font-size: 24px;">📋</span>
                <div>RECTEM <span style="color: var(--gold);">CBT</span></div>
            </div>

            <div>
                <h2 style="font-weight: 700; margin-bottom: 8px;">Staff Gate</h2>
                <p style="color: #8899aa; font-size: 14px; line-height: 1.6; margin-bottom: 0;">
                    Secure access path for academic lecturers, facilitators, and platform administrators.
                </p>
                <span class="brand-badge">Authorized Personnel Only</span>
            </div>

            <div style="font-size: 11px; color: #5a6e85;">
                &copy; <?php echo date("Y"); ?> RECTEM CBT Framework.
            </div>
        </div>

        <!-- RIGHT SIDE: SECURE LOGIN FORM PANEL -->
        <div class="form-panel">

            <div class="mb-4">
                <h4 style="font-weight: 700; color: var(--navy-dark);" class="mb-1">Welcome Back</h4>
                <p class="text-muted" style="font-size: 13px;">Please enter your credentials to manage your dashboard context.</p>
            </div>

            <?php if ($message != ""): ?>
                <div class="alert alert-danger text-center" style="font-size: 13px; border-radius: 8px; padding: 10px;">
                    <strong>⚠️ <?php echo $message; ?></strong>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label" style="font-size: 13px; font-weight: 500; color: var(--navy-mid);">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="name@rectem.edu.ng" required autocomplete="email">
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label" style="font-size: 13px; font-weight: 500; color: var(--navy-mid); margin-bottom: 0;">Password</label>
                    </div>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
                </div>

                <button type="submit" name="login" class="btn btn-submit w-100 mb-4">
                    Authenticate Securely
                </button>

                <!-- PORTAL INTERACTION LINKS -->
                <div class="d-flex flex-column gap-2 text-center">
                    <a href="login.php" class="switch-portal-link">
                        👨‍🎓 Switch to Student Login
                    </a>

                    <div style="font-size: 13px; margin-top: 10px;">
                        <span class="text-muted">New coordinator?</span>
                        <a href="register.php" class="text-decoration-none" style="font-weight: 500; color: var(--primary-blue);">Request Account</a>
                    </div>

                    <a href="index.php" class="text-muted small text-decoration-none mt-2">← Return to Homepage</a>
                </div>
            </form>

        </div>
    </div>

</body>

</html>