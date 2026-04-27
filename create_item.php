<?php
session_start();
include 'config.php';

// Security check - Siguraduhin na may role session para hindi ma-kick out
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$display_name = $_SESSION['user'] ?? "Master";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inspiro | Asset Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --deep-purple: #3b1845;
            --sidebar-width: 260px;
        }

        body { background-color: #f4f0e6; margin: 0; display: flex; }

        /* SIDEBAR STYLE */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--deep-purple);
            color: white;
            position: fixed;
            left: 0;
            top: 0;
        }
        .sidebar-brand { padding: 25px; font-size: 1.5rem; font-weight: bold; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .nav-item { 
            padding: 15px 25px; display: flex; align-items: center; 
            color: #b19db9; text-decoration: none; transition: 0.3s; 
        }
        .nav-item:hover, .nav-item.active { background: rgba(255,255,255,0.1); color: white; border-left: 4px solid #007bff; }
        .nav-item i { margin-right: 15px; color: #007bff; width: 20px; text-align: center; }

        /* MAIN CONTENT STYLE - Ito ang nag-aayos ng pwesto */
        .main-wrapper {
            margin-left: var(--sidebar-width); /* Eto ang magtutulak sa form pakanan */
            width: calc(100% - var(--sidebar-width));
            min-height: 100vh;
        }
        
        .top-navbar {
            background: #4b2354; color: white; padding: 12px 30px;
            display: flex; justify-content: space-between; align-items: center;
        }

        .content-body { padding: 40px; }
        .card-custom {
            background: white; border-radius: 12px; padding: 35px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        
        .form-label { font-weight: 600; color: #555; font-size: 0.85rem; }
        .btn-save { background: #6f42c1; color: white; border: none; padding: 12px 50px; border-radius: 8px; }
    </style>
</head>
<body>

    <?php include 'aside.php'; ?>

    <div class="main-wrapper">
        <nav class="top-navbar">
            <div class="nav-links small">
                <a href="index.php" class="text-white text-decoration-none me-3">Dashboard</a>
                <a href="view_inventory.php" class="text-white text-decoration-none">Assets</a>
            </div>
            <div class="user-info small">
                Master <strong><?php echo htmlspecialchars($display_name); ?></strong> | <a href="logout.php" class="text-white">Logout</a>
            </div>
        </nav>

        <div class="content-body">
            <h4 class="fw-bold mb-4" style="color: var(--deep-purple);">ASSET REGISTRATION</h4>
            
            <div class="card-custom">
                <form action="process_asset.php" method="POST">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label">Asset Tag</label>
                            <input type="text" name="asset_tag" class="form-control" placeholder="Scan QR/Barcode">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Serial Number</label>
                            <input type="text" name="serial_number" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Brand/Model</label>
                            <input type="text" name="brand_model" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select">
                                <option>Desktop</option>
                                <option>Laptop</option>
                                <option>Monitor</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Location</label>
                            <select name="location" class="form-select">
                                <option>BDO</option>
                                <option>Grab COE</option>
                                <option>Shark Ninja</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Inventory Date</label>
                            <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option>Active</option>
                                <option>Replacement</option>
                            </select>
                        </div>
                    </div>
                    <div class="text-center mt-5">
                        <button type="submit" class="btn-save">Save Asset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>