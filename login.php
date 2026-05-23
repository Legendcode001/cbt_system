<?php
session_start();
include("config/database.php");

$message = "";

if (isset($_POST['login'])) {

    $matric = $_POST['matric'] ?? '';
    $password = $_POST['password'] ?? '';

    // =========================================================
    // 1. SEARCH IN USERS TABLE (Students)
    // =========================================================
    $stmt = $conn->prepare("SELECT * FROM users WHERE matric = ?");
    $stmt->bind_param("s", $matric);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Match the Uppercase column name from your database
        $db_password = $user['PASSWORD'] ?? '';

        if (!empty($db_password) && password_verify($password, $db_password)) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['reg_no'] = $user['reg_no'];
            $_SESSION['NAME'] = $user['NAME'];

            if ($user['role'] == "student") {
                header("Location: student/dashboard.php");
                exit();
            }
        }
    }


    if (empty($message)) {
        $message = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login | RECTEM CBT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="assets/images/launcher_iconn.png" type="image/png">
    <style>
        body {
            background: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.1);
            margin-top: 10%;
        }

        .card-header {
            border-radius: 12px 12px 0 0 !important;
            padding: 20px;
        }

        .btn-primary {
            padding: 12px;
            font-weight: bold;
            border-radius: 8px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card login-card">
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0">RECTEM CBT Login</h4>
                    </div>
                    <div class="card-body p-4">

                        <?php if ($message != ""): ?>
                            <div class="alert alert-danger text-center">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="login.php">
                            <div class="mb-3">
                                <label class="form-label">Matric Number</label>
                                <input type="matric" name="matric" class="form-control" placeholder="R2018/420/001" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                            </div>

                            <button type="submit" name="login" class="btn btn-primary w-100">
                                Login to Account
                            </button>

                            <div class="text-center mt-3">
                                <span class="text-muted">Don't have an account?</span>
                                <a href="register.php" class="text-decoration-none">Register here</a>
                            </div>
                            <div class="text-center mt-2">
                                <a href="index.php" class="small text-muted">← Back to Home</a>
                            </div>
                            <div class="text-center mt-2">
                                <a href="Tlogin.php" class="small text-muted">Login as Teacher</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>