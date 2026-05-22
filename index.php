<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Lobster&family=Playfair+Display&family=Roboto&display=swap"
        rel="stylesheet">
    <link rel=" icon" href="assets/images/launcher_iconn.png" type="image/png">

    <title>RECTEM CBT SYSTEM</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f5f7fa;
        }

        .hero {
            background: #0d6efd;
            color: white;
            padding: 80px 0;
        }

        .sidebar-logo {
            margin-top: 5px;
            width: 70px;
            margin-left: auto;
            height: 80px;
            object-fit: contain;
        }

        .feature-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        footer {
            background: #0d6efd;
            color: white;
            padding: 15px;
        }
    </style>

</head>

<body>


    <!-- NAVBAR -->

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <img src="assets/images/launcher_icon.png" alt="Logo" class="sidebar-logo">


        <div class="container">

            <a class="navbar-brand fw-bold" href="#">
                RECTEM CBT SYSTEM
            </a>

            <div>

                <a href="login.php" class="btn btn-light me-2">
                    Login
                </a>

                <a href="register.php" class="btn btn-warning">
                    Register
                </a>

            </div>

        </div>

    </nav>


    <!-- HERO SECTION -->

    <section class="hero text-center">

        <div class="container">

            <h1 class="display-4 fw-bold">
                Welcome to RECTEM CBT SYSTEM
            </h1>

            <p class="lead mt-3">
                A modern Computer-Based Testing platform for students and lecturers.
            </p>

            <a href="login.php" class="btn btn-light btn-lg mt-3">
                Start Exam
            </a>

        </div>

    </section>


    <!-- FEATURES SECTION -->

    <section class="container mt-5">

        <div class="row text-center">


            <div class="col-md-4">

                <div class="card feature-card p-4">

                    <h4>Online Exams</h4>

                    <p>
                        Take exams securely anywhere inside the school CBT environment.
                    </p>

                </div>

            </div>


            <div class="col-md-4">

                <div class="card feature-card p-4">

                    <h4>Instant Results</h4>

                    <p>
                        Get your score immediately after submission.
                    </p>

                </div>

            </div>


            <div class="col-md-4">

                <div class="card feature-card p-4">

                    <h4>Teacher Dashboard</h4>

                    <p>
                        Teachers can upload questions and manage exams easily.
                    </p>

                </div>

            </div>


        </div>

    </section>


    <!-- INSTRUCTIONS -->

    <section class="container mt-5">

        <div class="card shadow">

            <div class="card-header bg-primary text-white">

                Exam Instructions

            </div>

            <div class="card-body">

                <ul>

                    <li>Login before starting your exam</li>

                    <li>Do not refresh during exam</li>

                    <li>Each question carries equal marks</li>

                    <li>Exam submits automatically when time ends</li>

                    <li>Ensure stable internet connection</li>

                </ul>

            </div>

        </div>

    </section>


    <!-- FOOTER -->

    <footer class="text-center mt-5">

        RECTEM CBT Portal © <?php echo date("Y"); ?>

    </footer>


</body>

</html>