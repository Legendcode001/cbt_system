<?php
include("config/database.php");

$message = "";

if (isset($_POST['register'])) {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // 1. Determine which table to use
    if ($role == "teacher") {
        $table = "teachers";
        $status = "pending";
    } else {
        $table = "users";
        $status = "approved";
    }

    // 2. Check if email already exists in BOTH tables to prevent duplicates
    $check_users = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $check_users->bind_param("s", $email);
    $check_users->execute();
    $res1 = $check_users->get_result();

    $check_teachers = $conn->prepare("SELECT email FROM teachers WHERE email = ?");
    $check_teachers->bind_param("s", $email);
    $check_teachers->execute();
    $res2 = $check_teachers->get_result();

    if ($res1->num_rows > 0 || $res2->num_rows > 0) {
        $message = "Error: This email is already registered!";
    } else {
        // 3. Insert into the correct table using a Prepared Statement
        if ($role == "teacher") {
            // Note: I excluded the 'role' column since 'teachers' is its own table
            $stmt = $conn->prepare("INSERT INTO teachers (name, email, password, status) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $password, $status);
        } else {
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $password, $role, $status);
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
    <link href="https://fonts.googleapis.com/css2?family=Lobster&family=Playfair+Display&family=Roboto&display=swap"
        rel="stylesheet">
    <link rel=" icon" href="assets/images/launcher_iconn.png" type="image/png">

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