<?php

$db_name = 'database'; 
$conn = mysqli_connect("localhost", "root", "", $db_name);

$location = isset($_GET['location']) ? mysqli_real_escape_string($conn, $_GET['location']) : 'BDO';


$active = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM assets WHERE location = '$location' AND status = 'Active'"))['t'] ?? 0;
$disposal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM assets WHERE location = '$location' AND status = 'For Disposal'"))['t'] ?? 0;
$replacement = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM assets WHERE location = '$location' AND status = 'Replacement'"))['t'] ?? 0;


$assets = mysqli_query($conn, "SELECT * FROM assets WHERE location = '$location'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tracking | <?php echo $location; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f0ec; font-family: 'Segoe UI', sans-serif; }
        .top-nav { background: #3b1845; color: white; padding: 12px 25px; display: flex; justify-content: space-between; align-items: center; }
        .stat-card { border-radius: 8px; padding: 10px; color: white; text-align: center; width: 120px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .btn-purple { background: #8e44ad; color: white; }
        .btn-purple:hover { background: #732d91; color: white; }
    </style>
</head>
<body>

<div class="top-nav">
    <div class="fw-bold fs-5">
        <a href="view_area.php" class="text-white text-decoration-none">inspiro <i class="fas fa-home ms-1"></i></a>
    </div>
    <div class="small">Welcome, <strong>Jayson Mateo</strong> | <a href="logout.php" class="text-white text-decoration-none">Logout</a></div>
</div>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex gap-2">
            <button class="btn btn-purple btn-sm">+ New</button>
            <button class="btn btn-success btn-sm"><i class="fas fa-file-export me-1"></i> Export</button>
            <a href="view_area.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Back to Areas</a>
        </div>
        <div class="w-25">
            <input type="text" class="form-control form-control-sm" placeholder="Search asset tag or serial...">
        </div>
    </div>

    <div class="d-flex gap-3 mb-4">
        <div class="stat-card bg-primary text-uppercase" style="background-color: #1a6fd3 !important;"><small>Replacement</small><h3 class="m-0"><?php echo $replacement; ?></h3></div>
        <div class="stat-card bg-danger text-uppercase"><small>Disposal</small><h3 class="m-0"><?php echo $disposal; ?></h3></div>
        <div class="stat-card bg-success text-uppercase"><small>Active</small><h3 class="m-0"><?php echo $active; ?></h3></div>
    </div>

    <h5 class="fw-bold mb-3"><?php echo htmlspecialchars($location); ?></h5>
    
    <div class="bg-white rounded shadow-sm overflow-hidden">
        <table class="table table-hover mb-0">
            <thead style="background: #3b1845; color: white;">
                <tr class="small">
                    <th>Inventory Date</th>
                    <th>Asset Tag</th>
                    <th>Serial Number</th>
                    <th>Brand/Model</th>
                    <th>Type</th>
                    <th>Year/Model</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody class="small">
                <?php while($row = mysqli_fetch_assoc($assets)): ?>
                <tr>
                    <td>4/18/2026</td>
                    <td><strong><?php echo $row['asset_tag']; ?></strong></td>
                    <td><?php echo $row['serial_number']; ?></td>
                    <td><?php echo $row['brand_model']; ?></td>
                    <td>Desktop</td>
                    <td>2020--2023</td>
                    <td><?php echo $row['location']; ?></td>
                    <td><span class="badge bg-success opacity-75"><?php echo $row['status']; ?></span></td>
                    <td><button class="btn btn-xs btn-purple py-0 px-2" style="font-size: 0.7rem;">Edit</button></td>
                </tr>
                <?php endwhile; ?>
                
                <?php if(mysqli_num_rows($assets) == 0): ?>
                <tr><td colspan="9" class="text-center py-4 text-muted">No assets found for this location.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>