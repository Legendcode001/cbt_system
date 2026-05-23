<?php
include("config/database.php");

$message = "";

if (isset($_POST['register'])) {

    $name = $_POST['name'];
    $matric = isset($_POST['matric']) ? trim($_POST['matric']) : "";
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // 1. Determine which table and initial status to use
    if ($role == "teacher") {
        $table = "teachers";
        $status = "pending";
    } else {
        $table = "users";
        $status = "approved";
    }

    // Initialize row count triggers
    $matric_exists = false;
    $email_exists = false;

    // 2. ONLY check matric number if the registering user is a student
    if ($role == "student" && !empty($matric)) {
        $check_matric = $conn->prepare("SELECT matric FROM users WHERE matric = ?");
        $check_matric->bind_param("s", $matric);
        $check_matric->execute();
        $res_matric = $check_matric->get_result();
        if ($res_matric->num_rows > 0) {
            $matric_exists = true;
        }
    }

    // 3. Check if email already exists in BOTH tables to prevent duplicates
    // Check users table
    $check_user_email = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $check_user_email->bind_param("s", $email);
    $check_user_email->execute();
    $res_email1 = $check_user_email->get_result();

    // Check teachers table
    $check_teacher_email = $conn->prepare("SELECT email FROM teachers WHERE email = ?");
    $check_teacher_email->bind_param("s", $email);
    $check_teacher_email->execute();
    $res_email2 = $check_teacher_email->get_result();

    if ($res_email1->num_rows > 0 || $res_email2->num_rows > 0) {
        $email_exists = true;
    }

    // 4. Handle errors or proceed to insertion
    if ($matric_exists) {
        $message = "Error: This Matric Number is already registered!";
    } elseif ($email_exists) {
        $message = "Error: This email is already registered!";
    } else {
        // 5. Insert into the correct table using a Prepared Statement
        if ($role == "teacher") {
            $stmt = $conn->prepare("INSERT INTO teachers (name, email, password, status) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $password, $status);
        } else {
            $stmt = $conn->prepare("INSERT INTO users (name, matric, email, password, role, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $name, $matric, $email, $password, $role, $status);
        }

        if ($stmt->execute()) {
            if ($role == "teacher") {
                $message = "Registration successful! Please wait for admin approval.";
            } else {
                $message = "Registration successful! You can login now.";
            }
        } else {
            $message = "Oops! Something went wrong during registration.";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Register | RECTEM CBT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lobster&family=Playfair+Display&family=Roboto&display=swap" rel="stylesheet">
    <link rel="icon" href="assets/images/launcher_iconn.png" type="image/png">

    <style>
        body {
            background: #f5f7fa;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>RECTEM CBT Registration</h4>
                    </div>

                    <div class="card-body">
                        <?php if ($message != "") { ?>
                            <div class="alert alert-info">
                                <?php echo $message; ?>
                            </div>
                        <?php } ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label>Full Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label>Matric Number For Student Only</label>
                                <input type="text" name="matric" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label>Register As</label>
                                <select name="role" class="form-control" required>
                                    <option value="">Select Role</option>
                                    <option value="student">Student</option>
                                    <option value="teacher">Teacher</option>
                                </select>
                            </div>

                            <button name="register" class="btn btn-primary w-100">
                                Register
                            </button>

                            <div class="text-center mt-3">
                                Already have account?
                                <a href="login.php">Login here</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>