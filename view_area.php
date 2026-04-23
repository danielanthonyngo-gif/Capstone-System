<?php
session_start(); 
include 'config.php';

$display_name = "Guest"; 
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    // Ginamit natin ang 'fullname' base sa picture na sinend mo
    $user_query = mysqli_query($conn, "SELECT fullname FROM users WHERE id = '$user_id' LIMIT 1");
    if ($row = mysqli_fetch_assoc($user_query)) {
        $display_name = $row['fullname'];
    }
}


$alpha_areas = [
    ['name' => 'BDO'], ['name' => 'BDO Insure'], ['name' => 'BDO Life'], 
    ['name' => 'Pacsan'], ['name' => 'BDO Core'], ['name' => 'Flight Center']
];
$beta_areas = [
    ['name' => 'Grab Support'], ['name' => 'Grab COE'], ['name' => 'Shark Ninja'], 
    ['name' => 'Hallmark'], ['name' => 'ANA'], ['name' => 'AUB']
];
$other_areas = [
    ['name' => "Manila Doctor's Hospital"], ['name' => 'Ignite'], ['name' => 'Viagogo']
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inspiro | View Areas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --deep-purple: #3b1845; --sidebar-bg: #2b1233; --sidebar-width: 250px; }
        body { background-color: #f4f0ec; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; }
        .sidebar { width: var(--sidebar-width); height: 100vh; position: fixed; background: var(--sidebar-bg); color: white; z-index: 100; }
        .brand { padding: 25px; text-align: center; font-size: 1.5rem; font-weight: 700; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .nav-item { padding: 12px 25px; display: block; color: #d1d1d1; text-decoration: none; border-left: 4px solid transparent; transition: 0.3s; }
        .nav-item:hover { background: rgba(255,255,255,0.05); color: white; }
        .nav-item.active { background: #3d1a4a; color: white; border-left: 4px solid #a29bfe; }
        .content { margin-left: var(--sidebar-width); }
        .top-nav { background: var(--deep-purple); padding: 10px 30px; color: white; display: flex; justify-content: space-between; align-items: center; }
        .area-card { background: white; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: 0.3s; overflow: hidden; border: none; }
        .area-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        .card-header-custom { background: var(--deep-purple); color: white; padding: 10px; text-align: center; font-weight: 600; font-size: 0.8rem; }
        .pc-icon { color: #5dade2; font-size: 2.5rem; margin: 15px 0; }
        .stat-line { border-top: 1px solid #f0f0f0; padding: 10px 0; }
    </style>
</head>
<body>

<?php include 'aside.php'; ?>

<div class="content">
    <nav class="top-nav">
        <div class="d-flex gap-3">
            <a href="#" class="text-white-50 text-decoration-none small">Dashboard</a>
            <a href="#" class="text-white fw-bold text-decoration-none small border-bottom border-2">Assets</a>
            <a href="#" class="text-white-50 text-decoration-none small">Users</a>
        </div>
        <div class="small">Welcome, <strong><?php echo htmlspecialchars($display_name); ?></strong> | <a href="logout.php" class="text-white text-decoration-none">Logout</a></div>
    </nav>

    <div class="p-4">
        <h4 class="fw-bold mb-4" style="color: var(--deep-purple); border-bottom: 3px solid #6f42c1; display: inline-block;">Alpha Building</h4>
        <div class="row g-4 mb-5">
            <?php 
            foreach(array_merge($alpha_areas, $other_areas) as $area): 
                $name = $area['name'];
                $safe_name = mysqli_real_escape_string($conn, $name);
                $res = mysqli_query($conn, "SELECT COUNT(*) as t FROM assets WHERE location = '$safe_name'");
                $count = mysqli_fetch_assoc($res)['t'] ?? 0;
            ?>
            <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                <a href="inventory_page.php?location=<?php echo urlencode($name); ?>" class="text-decoration-none">
                    <div class="area-card text-center">
                        <div class="card-header-custom"><?php echo strtoupper($name); ?></div>
                        <div class="p-3">
                            <i class="fas fa-desktop pc-icon"></i>
                            <div class="row stat-line g-0">
                                <div class="col-6 border-end">
                                    <h5 class="m-0 fw-bold text-dark"><?php echo $count; ?></h5>
                                    <small class="text-muted">IN USE</small>
                                </div>
                                <div class="col-6">
                                    <h5 class="m-0 fw-bold text-dark">0</h5>
                                    <small class="text-muted">AVAIL</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>

        <h4 class="fw-bold mb-4" style="color: var(--deep-purple); border-bottom: 3px solid #6f42c1; display: inline-block;">Beta Building</h4>
        <div class="row g-4">
            <?php foreach($beta_areas as $area): 
                $name = $area['name'];
                $safe_name = mysqli_real_escape_string($conn, $name);
                $res = mysqli_query($conn, "SELECT COUNT(*) as t FROM assets WHERE location = '$safe_name'");
                $count = mysqli_fetch_assoc($res)['t'] ?? 0;
            ?>
            <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                <a href="inventory_page.php?location=<?php echo urlencode($name); ?>" class="text-decoration-none">
                    <div class="area-card text-center">
                        <div class="card-header-custom"><?php echo strtoupper($name); ?></div>
                        <div class="p-3">
                            <i class="fas fa-desktop pc-icon"></i>
                            <div class="row stat-line g-0">
                                <div class="col-6 border-end">
                                    <h5 class="m-0 fw-bold text-dark"><?php echo $count; ?></h5>
                                    <small class="text-muted">IN USE</small>
                                </div>
                                <div class="col-6">
                                    <h5 class="m-0 fw-bold text-dark">0</h5>
                                    <small class="text-muted">AVAIL</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

</body>
</html>