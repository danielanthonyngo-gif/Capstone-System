<?php
include 'config.php';
session_start();

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query sa database
    $result = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        // I-check kung tama ang hashed password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user['fullname'];
            $_SESSION['user_id'] = $user['id'];

            header("Location: index.php");
            exit(); 
        } else {
            $error_msg = "Maling password! Subukan ulit.";
        }
    } else {
        $error_msg = "Invalid username! Hindi pa rehistrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --brand-purple: #6f42c1; /* Classic Purple */
            --dark-purple: #321b5c;  /* Darker Purple for Background */
            --hover-purple: #59359a; /* Hover state */
        }
        body {
            /* Dark Purple Background */
            background: var(--dark-purple);
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
        }
        .login-card {
            margin-top: 100px;
            border: 1px solid #dee2e6;
            border-radius: 15px;
            background: #ffffff;
        }
        .card-header {
            /* Purple Header */
            background-color: var(--brand-purple) !important;
            border-radius: 15px 15px 0 0 !important;
            border: none;
        }
        .btn-purple {
            /* Purple Button */
            background-color: var(--brand-purple);
            color: white;
            border: none;
            transition: 0.3s;
            font-weight: 600;
        }
        .btn-purple:hover {
            background-color: var(--hover-purple);
            color: white;
        }
        .text-purple {
            color: var(--brand-purple) !important;
        }
        /* Custom Focus Ring for Inputs */
        .form-control:focus {
            border-color: var(--brand-purple);
            box-shadow: 0 0 0 0.25rem rgba(111, 66, 193, 0.25);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            
            <?php if(isset($error_msg)): ?>
                <div class="alert alert-danger mt-3" role="alert">
                    <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <div class="card login-card shadow-lg">
                <div class="text-white text-center py-3">
                    <img src="logooo.png" alt="Inspiro Logo" style="width: 200px; max-width: 100%; height: auto; margin-bottom: 10px;">
                    <h4 class="mb-0 text-purple fw-semibold">INSPIRO RELIA INC.</h4>
                </div>
                <div class="card-body p-4">
                    <form method="POST"> 
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Address</label>
                            <input type="email" name="username" class="form-control" placeholder="name@example.com" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="********" required>
                        </div>

                        <div class="d-grid">
                            <button name="login" class="btn btn-purple btn-lg" type="submit">Log in</button>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        <small class="text-muted">Forgot Password?</small> 
                        <a href="#" class="text-purple text-decoration-none fw-bold">Click here</a>
                    </div>
                <div class="card-footer text-center py-3 bg-transparent border-0">
                    <small class="text-muted">Don't have an account? 
                        <a href="register.php" class="text-purple text-decoration-none fw-bold">Sign Up</a>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>