<?php
session_start();
include("../config/database.php");

// 1. Strict Role Protection
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../login.php");
    exit();
}


$admin_id = 1;
$success_msg = "";
$error_msg = "";

// 2. Fetch current administrator info from the dedicated 'admin' table
$query = mysqli_query($conn, "SELECT username, name FROM admin WHERE id = '$admin_id'");
$admin_data = mysqli_fetch_assoc($query);

// 3. Handle Form Submissions Safely
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $new_name     = mysqli_real_escape_string($conn, trim($_POST['admin_name']));
    $new_password = $_POST['new_password'];
    $confirm_pwd  = $_POST['confirm_password'];

    if (empty($new_username) || empty($new_name)) {
        $error_msg = "Username and Display Name cannot be empty.";
    }
    // ⚠️ CRUCIAL: Check if the username ends with @gmail.com
    elseif (!filter_var($new_username, FILTER_VALIDATE_EMAIL) || !str_ends_with(strtolower($new_username), '@gmail.com')) {
        $error_msg = "The admin username must be a valid email address ending in @gmail.com.";
    } else {
        // Option A: Updating profile data WITHOUT changing password
        if (empty($new_password)) {
            $update_sql = "UPDATE admin SET username = '$new_username', name = '$new_name' WHERE id = '$admin_id'";
            if (mysqli_query($conn, $update_sql)) {
                $success_msg = "Admin account details updated successfully!";
                $admin_data['username'] = $new_username;
                $admin_data['name'] = $new_name;
            } else {
                $error_msg = "Failed to update profile data.";
            }
        }
        // Option B: Updating profile data AND securely resetting the password
        else {
            if ($new_password !== $confirm_pwd) {
                $error_msg = "New passwords do not match.";
            } elseif (strlen($new_password) < 6) {
                $error_msg = "Password must be at least 6 characters long.";
            } else {
                $secure_hash = password_hash($new_password, PASSWORD_BCRYPT);

                $update_sql = "UPDATE admin SET username = '$new_username', name = '$new_name', password = '$secure_hash' WHERE id = '$admin_id'";
                if (mysqli_query($conn, $update_sql)) {
                    $success_msg = "Admin username and Password updated successfully!";
                    $admin_data['username'] = $new_username;
                    $admin_data['name'] = $new_name;
                } else {
                    $error_msg = "Database update error.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Security Settings — RECTEM CBT</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy: #0a1628;
            --navy-light: #1d3461;
            --gold: #f4a916;
            --white: #fff;
            --off-white: #f0f4f8;
            --border: #e2e8f0;
            --success: #22c55e;
            --danger: #ef4444;
        }

        body {
            font-family: 'Sora', sans-serif;
            background: var(--off-white);
            color: var(--navy);
            padding: 40px 20px;
        }

        .profile-card {
            max-width: 500px;
            margin: 0 auto;
            background: var(--white);
            padding: 30px;
            border-radius: 12px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        h2 {
            font-size: 20px;
            margin-bottom: 20px;
            border-bottom: 2px solid var(--gold);
            padding-bottom: 8px;
        }

        .alert {
            padding: 12px;
            border-radius: 6px;
            font-size: 13px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-success {
            background: #dcfce7;
            color: var(--success);
        }

        .alert-danger {
            background: #fee2e2;
            color: var(--danger);
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 6px;
            text-transform: uppercase;
            color: var(--navy-light);
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-family: 'Sora', sans-serif;
            font-size: 14px;
            outline: none;
        }

        input:focus {
            border-color: var(--gold);
        }

        .btn-save {
            background: var(--navy);
            color: var(--white);
            border: none;
            padding: 12px 20px;
            width: 100%;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-save:hover {
            background: var(--navy-light);
        }

        .back-btn {
            display: block;
            text-align: center;
            margin-top: 15px;
            font-size: 13px;
            color: var(--navy-light);
            text-decoration: none;
        }
    </style>
</head>

<body>

    <div class="profile-card">
        <h2>🔒 Admin Security Credentials</h2>

        <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success"><?= $success_msg ?></div>
        <?php endif; ?>

        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger"><?= $error_msg ?></div>
        <?php endif; ?>

        <form method="POST" action="admin_profile.php">
            <div class="form-group">
                <label>Display Name</label>
                <input type="text" name="admin_name" value="<?= htmlspecialchars($admin_data['name'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label>Admin Username</label>
                <input type="email" name="username" value="<?= htmlspecialchars($admin_data['username'] ?? '') ?>" required>
            </div>

            <hr style="margin: 25px 0; border: 0; border-top: 1px dashed var(--border);">
            <p style="font-size:11px; color:gray; margin-bottom:15px;">Leave password fields blank if you don't want to change it.</p>

            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" placeholder="••••••••">
            </div>

            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" placeholder="••••••••">
            </div>

            <button type="submit" class="btn-save">Save Changes</button>
        </form>

        <a href="dashboard.php" class="back-btn">← Back to Admin Panel</a>
    </div>

</body>

</html>