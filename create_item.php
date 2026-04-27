<?php
session_start();
include 'config.php';


if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


$display_name = "User"; 
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $user_query = "SELECT fullname FROM users WHERE id = '$user_id'";
    $user_result = mysqli_query($conn, $user_query);
    if ($user_result && mysqli_num_rows($user_result) > 0) {
        $u_data = mysqli_fetch_assoc($user_result);
        $display_name = $u_data['fullname'];
    }
}

$location = isset($_GET['location']) ? mysqli_real_escape_string($conn, $_GET['location']) : 'BDO';


$active = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM assets WHERE location = '$location' AND status = 'Active'"))['t'] ?? 0;
$disposal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM assets WHERE location = '$location' AND status = 'For Disposal'"))['t'] ?? 0;
$replacement = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM assets WHERE location = '$location' AND status = 'Replacement'"))['t'] ?? 0;

$assets = mysqli_query($conn, "SELECT * FROM assets WHERE location = '$location'");

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $asset_tag = mysqli_real_escape_string($conn, $_POST['asset_tag']);
    $serial = mysqli_real_escape_string($conn, $_POST['serial_number']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand_model']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $year = mysqli_real_escape_string($conn, $_POST['year_model']);
    $area = mysqli_real_escape_string($conn, $_POST['area']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $sql = "INSERT INTO assets (inventory_date, asset_tag, serial_number, brand_model, asset_type, year_model, location, status) 
            VALUES ('$date', '$asset_tag', '$serial', '$brand', '$type', '$year', '$area', '$status')";

    if (mysqli_query($conn, $sql)) {
        $message = "<div class='alert alert-success'>Master, na-save na ang bagong item!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inspiro | Create Item</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { 
            --deep-purple: #3b1845;
            --primary-purple: #6f42c1;
            --bg-cream: #f4f0e6;
            --sidebar-width: 260px;
        }

        body { background-color: var(--bg-cream); font-family: 'Inter', sans-serif; }

        .sidebar { width: var(--sidebar-width); height: 100vh; position: fixed; background: var(--deep-purple); color: white; z-index: 1000; }
        .brand-section { padding: 25px; font-size: 1.5rem; font-weight: 700; background: rgba(0,0,0,0.2); text-align: center; }
        .nav-menu { padding: 20px 0; }
        .nav-item { padding: 12px 25px; display: flex; align-items: center; color: rgba(255,255,255,0.7); text-decoration: none; transition: 0.3s; }
        .nav-item:hover, .nav-item.active { background: rgba(255,255,255,0.1); color: white; border-left: 4px solid #a29bfe; }
        .nav-item i { margin-right: 15px; width: 20px; text-align: center; }

        .content-wrapper { margin-left: var(--sidebar-width); min-height: 100vh; }
        .top-navbar { background-color: #4b2354; padding: 10px 30px; display: flex; justify-content: space-between; align-items: center; color: white; }
        .top-navbar a { color: #d1d1d1; text-decoration: none; margin-right: 20px; font-size: 0.9rem; }
        .top-navbar a.active { color: white; font-weight: bold; border-bottom: 2px solid white; }

        .create-container { padding: 30px; }
        .page-header { background-color: var(--deep-purple); color: white; padding: 15px 25px; border-radius: 8px 8px 0 0; font-weight: 700; text-transform: uppercase; font-size: 1rem; margin-bottom: 0; }
        .form-card { background: white; border-radius: 0 0 8px 8px; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .form-label { font-size: 0.85rem; color: #888; font-weight: 600; margin-bottom: 5px; }
        .form-control, .form-select { border-radius: 6px; padding: 10px 15px; font-size: 0.9rem; border: 1px solid #ddd; }
        .barcode-visual { background-color: #f8f9fa; border: 1px solid #eee; border-radius: 6px; padding: 10px; margin-top: 5px; display: flex; align-items: center; justify-content: center; color: #ccc; font-size: 0.8rem; height: 40px; }
        .action-btns { margin-top: 30px; display: flex; justify-content: center; gap: 15px; }
        .btn-save { background-color: var(--primary-purple); color: white; border: none; padding: 10px 30px; border-radius: 6px; font-weight: 600; transition: 0.3s; }
        .btn-cancel { background-color: #adb5bd; color: white; border: none; padding: 10px 30px; border-radius: 6px; font-weight: 600; text-decoration: none; }
    </style>
</head>
<body>

<?php include 'aside.php'; ?>

<div class="content-wrapper">
    <nav class="top-navbar">
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="view_inventory.php">Assets</a>
        </div>
        <div>Welcome, <strong><?php echo htmlspecialchars($display_name); ?></strong> | <a href="logout.php" class="text-white">Logout</a></div>
    </nav>

    <div class="create-container">
        <?php echo $message; ?>
        <h5 class="page-header">CREATE ITEM</h5>

        <div class="form-card">
            <form action="" method="POST">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Asset Tag</label>
                            <input type="text" class="form-control" name="asset_tag" required placeholder="Scan or type asset tag">
                            <div class="barcode-visual"><i class="fas fa-barcode"></i> ||||||||||||||||||| barcode</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Serial Number</label>
                            <input type="text" class="form-control" name="serial_number" required placeholder="Scan or type serial number">
                            <div class="barcode-visual"><i class="fas fa-barcode"></i> ||||||||||||||||||| barcode</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Brand/Model</label>
                            <input type="text" class="form-control" name="brand_model" required placeholder="e.g. Dell Optiplex 7010 SFF">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <select class="form-select" name="type">
                                <option value="Desktop">Desktop</option>
                                <option value="Laptop">Laptop</option>
                                <option value="Server">Server</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Area / Location</label>
                            <select class="form-select" name="area">
                                <option value="BDO">BDO</option>
                                <option value="Grab COE">Grab COE</option>
                                <option value="Shark Ninja">Shark Ninja</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Inventory Date</label>
                            <input type="date" class="form-control" name="date" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Year/Model</label>
                            <input type="text" class="form-control" name="year_model" placeholder="e.g. 2023">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="Active">Active</option>
                                <option value="For Disposal">For Disposal</option>
                                <option value="Replacement">Replacement</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="action-btns">
                    <button type="submit" class="btn-save">Save Item</button>
                    <a href="view_inventory.php" class="btn-cancel">Cancel</a>
                </div>
            </form>
        </div>

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
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>