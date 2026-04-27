<?php
include 'config.php'; 
session_start();

// --- 1. ACCESS CONTROL (Administrator Only) ---aaaa
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$check_admin = mysqli_query($conn, "SELECT role FROM users WHERE id = '$user_id'");
$user_data = mysqli_fetch_assoc($check_admin);

$allowed = ['Admin', 'Administrator'];

if (!$user_data || !in_array($user_data['role'], $allowed)) {
    echo "<script>
            alert('Access Denied: Mga Administrator lamang ang pwedeng mag-access nito.');
            window.location.href='index.php';
          </script>";
    exit();
}
// --- END OF ACCESS CONTROL ---

if (isset($_POST['register'])) {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; 
    $role = mysqli_real_escape_string($conn, $_POST['role']); 

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (fullname, username, password, role) VALUES ('$fullname', '$username', '$hashed_password', '$role')";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Account Created Successfully!'); window.location='index.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inspiro | Register Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --brand-purple: #3b1845;
            --accent-purple: #6f42c1;
        }
        body {
            background-color: #f4f0e6;
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px; /* Space para sa mobile */
            font-family: 'Segoe UI', sans-serif;
        }
        
        /* DITO MO MA-AADJUST ANG LAKI NG CARD */
        .register-card {
            width: 100%;
            max-width: 550px; /* PALITAN MO ITO: e.g., 600px kung gusto mo mas malapad */
            border: none;
            border-radius: 15px;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            overflow: hidden;
        }

        .card-header {
            background-color: var(--brand-purple) !important;
            color: white !important;
            text-align: center;
            padding: 30px;
        }
        .btn-purple {
            background-color: var(--accent-purple);
            color: white;
            font-weight: bold;
            padding: 12px;
            border: none;
            transition: 0.3s;
        }
        .btn-purple:hover {
            background-color: #59359a;
            color: white;
        }
        .brand-logo {
            font-weight: 800;
            letter-spacing: 3px;
            display: block;
            text-transform: uppercase;
            font-size: 1.5rem;
        }
        .form-label {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #555;
        }
    </style>
</head>
<body>

    <div class="register-card">
        <div class="card-header">
            <span class="brand-logo">INSPIRO</span>
            <p class="mb-0 opacity-75">User Registration Portal</p>
        </div>
        <div class="card-body p-4 p-md-5"> <form method="POST"> 
                <div class="mb-3">
                    <label class="form-label fw-bold">Full Name</label>
                    <input type="text" name="fullname" class="form-control" placeholder="Juan Dela Cruz" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Email / Username</label>
                    <input type="text" name="username" class="form-control" placeholder="juan.inspiro@example.com" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Account Role</label>
                    <select name="role" class="form-select" required>
                        <option value="" selected disabled>Select Role</option>
                        <option value="Administrator">Administrator</option>
                        <option value="Technical Support">Technical Support</option>
                        <option value="Staff">Staff</option>
                    </select>
                </div>

                <div class="d-grid gap-2 text-center">
                    <button name="register" class="btn btn-purple" type="submit">Create User Account</button>
                    <a href="index.php" class="btn btn-link text-decoration-none text-muted small mt-2">Back to Dashboard</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>