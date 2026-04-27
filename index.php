<?php
session_start();
include 'config.php';

if (!$conn) {
    die("<div style='color:red; padding:20px;'>Master, mali ang database settings mo: " . mysqli_connect_error() . "</div>");
}

// asassa
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT fullname FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user_data = mysqli_fetch_assoc($result);
    $display_name = $user_data['fullname'];
} else {
    $display_name = "User"; 
}

// --- 3. DYNAMIC ASSET TRACKING ---
// Kukunin natin ang actual counts mula sa 'assets' table base sa status nila
$count_in_use = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM assets WHERE status='Active'"))['total'];
$count_disposal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM assets WHERE status='For Disposal'"))['total'];
$count_replacement = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM assets WHERE status='Replacement'"))['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inspiro | Asset Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { 
            --primary-purple: #6f42c1; 
            --deep-purple: #3b1845;
            --accent-red: #dc3545;
            --bg-light: #f0f2f5;
            --sidebar-width: 260px;
        }

        body { 
            background-color: var(--bg-light); 
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }

        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            background: var(--deep-purple);
            color: white;
            z-index: 1000;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }

        .brand-section {
            padding: 25px;
            font-size: 1.5rem;
            font-weight: 700;
            background: rgba(0,0,0,0.2);
            text-align: center;
        }

        .nav-menu { padding: 20px 0; }
        .nav-item {
            padding: 12px 25px;
            display: flex;
            align-items: center;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: 0.3s;
            border-left: 4px solid transparent;
        }

        .nav-item i { margin-right: 15px; width: 20px; text-align: center; }
        .nav-item:hover, .nav-item.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left: 4px solid #a29bfe;
        }

        .content-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        .top-header {
            background: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .btn-logout {
            background: #fff5f5;
            color: var(--accent-red);
            padding: 8px 15px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: 0.3s;
            border: 1px solid #ffe3e3;
        }
        .btn-logout:hover { background: var(--accent-red); color: white; }

        .dashboard-container { padding: 30px; }
        
        .welcome-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            border-left: 5px solid var(--primary-purple);
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        }

        .status-card {
            border: none;
            border-radius: 15px;
            color: white;
            padding: 20px;
            position: relative;
        }
        .card-icon { font-size: 2rem; opacity: 0.3; position: absolute; right: 20px; bottom: 20px; }

        .calendar-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .current-day {
            background-color: var(--primary-purple) !important;
            color: white !important;
            padding: 8px 14px;
            border-radius: 50%;
            font-weight: bold;
            display: inline-block;
            box-shadow: 0 4px 8px rgba(111, 66, 193, 0.4);
        }

        @media (max-width: 992px) {
            .sidebar { display: none; }
            .content-wrapper { margin-left: 0; }
        }
    </style>
</head>
<body>

<?php include 'aside.php'; ?>

<div class="content-wrapper">
    <header class="top-header">
        <div class="header-left">
            <h5 class="m-0 fw-bold text-secondary">Computer Asset</h5>
        </div>
        <div class="user-profile d-flex align-items-center gap-3">
            <span class="d-none d-md-inline text-muted">Logged in as: <strong><?php echo htmlspecialchars($display_name); ?></strong></span>
            <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </header>

    <div class="dashboard-container">
        <div class="welcome-card">
            <h2 class="m-0"><b>Welcome back,</b> <span class="text-danger fw-bold"><?php echo htmlspecialchars($display_name); ?>!</span></h2>
            <p class="text-muted mb-0">Today is <strong><?php echo date('l, F j, Y'); ?></strong></p>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="status-card bg-success">
                    <p class="mb-1 text-uppercase small fw-bold text-white-50">In Use</p>
                    <h2 class="display-5 fw-bold mb-0"><?php echo $count_in_use; ?></h2>
                    <i class="fas fa-check-circle card-icon"></i>
                </div>
            </div>
            <div class="col-md-4">
                <div class="status-card bg-danger">
                    <p class="mb-1 text-uppercase small fw-bold text-white-50">For Disposal</p>
                    <h2 class="display-5 fw-bold mb-0"><?php echo $count_disposal; ?></h2>
                    <i class="fas fa-trash-alt card-icon"></i>
                </div>
            </div>
            <div class="col-md-4">
                <div class="status-card bg-primary">
                    <p class="mb-1 text-uppercase small fw-bold text-white-50">Replacement</p>
                    <h2 class="display-5 fw-bold mb-0"><?php echo $count_replacement; ?></h2>
                    <i class="fas fa-sync-alt card-icon"></i>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="calendar-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="fw-bold mb-0">Asset Schedule</h5>
                            <small class="text-muted">Month of <?php echo date('F Y'); ?></small>
                        </div>
                        <span class="badge bg-light text-dark border p-2">
                            <?php echo date('M 1') . " - " . date('M t, Y'); ?>
                        </span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-borderless text-center align-middle">
                            <thead class="text-muted small fw-bold">
                                <tr>
                                    <th>MON</th><th>TUE</th><th>WED</th><th>THU</th><th>FRI</th><th>SAT</th><th>SUN</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <?php
                                    $today = date('j'); 
                                    $daysInMonth = date('t'); 
                                    $firstDayOfMonth = date('N', strtotime(date('Y-m-01'))); 

                                    for ($i = 1; $i < $firstDayOfMonth; $i++) {
                                        echo "<td></td>";
                                    }

                                    for ($day = 1; $day <= $daysInMonth; $day++) {
                                        $spanClass = ($day == $today) ? 'class="current-day"' : '';
                                        echo "<td class='p-3'><span $spanClass>$day</span></td>";

                                        if (($day + $firstDayOfMonth - 1) % 7 == 0) {
                                            echo "</tr><tr>";
                                        }
                                    }
                                    ?>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>