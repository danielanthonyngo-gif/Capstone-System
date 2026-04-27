<?php
session_start();
include 'config.php';

$display_name = "Guest"; 
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $user_query = mysqli_query($conn, "SELECT fullname FROM users WHERE id = '$user_id' LIMIT 1");
    if ($row = mysqli_fetch_assoc($user_query)) {
        $display_name = $row['fullname'];
    }
}

if (isset($_POST['update_asset'])) {
    $id = mysqli_real_escape_string($conn, $_POST['asset_id']);
    $tag = mysqli_real_escape_string($conn, $_POST['asset_tag']);
    $serial = mysqli_real_escape_string($conn, $_POST['serial_number']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand_model']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $update_query = "UPDATE assets SET asset_tag='$tag', serial_number='$serial', brand_model='$brand', location='$location', status='$status' WHERE id='$id'";
    
    if(mysqli_query($conn, $update_query)) {
        header("Location: view_inventory.php?success=1");
        exit();
    }
}

$count_replacement = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM assets WHERE status='Replacement'"))['total'];
$count_disposal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM assets WHERE status='For Disposal'"))['total'];
$count_active = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM assets WHERE status='Active'"))['total'];

$assets_result = mysqli_query($conn, "SELECT * FROM assets ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inspiro | Inventory</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    
    <style>
        :root { --deep-purple: #3b1845; --bg-cream: #f4f0e6; --sidebar-width: 260px; }
        body { background-color: var(--bg-cream); font-family: 'Inter', sans-serif; }
        .sidebar { width: var(--sidebar-width); height: 100vh; position: fixed; background: var(--deep-purple); color: white; z-index: 1000; }
        .brand-section { padding: 25px; font-size: 1.5rem; font-weight: 700; background: rgba(0,0,0,0.2); text-align: center; }
        .nav-menu { padding: 20px 0; }
        .nav-item { padding: 12px 25px; display: flex; align-items: center; color: rgba(255,255,255,0.7); text-decoration: none; transition: 0.3s; }
        .nav-item:hover, .nav-item.active { background: rgba(255,255,255,0.1); color: white; border-left: 4px solid #a29bfe; }
        .nav-item i { margin-right: 15px; width: 20px; text-align: center; }
        .content-wrapper { margin-left: var(--sidebar-width); min-height: 100vh; }
        .top-navbar { background-color: var(--deep-purple); padding: 10px 30px; display: flex; justify-content: space-between; align-items: center; color: white; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .top-navbar a { color: #d1d1d1; text-decoration: none; margin-right: 20px; font-size: 0.9rem; }
        .inventory-container { padding: 30px; }
        .status-summary { display: flex; gap: 15px; margin-bottom: 25px; }
        .summary-card { padding: 10px 25px; border-radius: 8px; color: white; display: flex; flex-direction: column; align-items: center; min-width: 140px; }
        .bg-replacement { background-color: #fffff; }
        .bg-disposal { background-color: #dc3545; }
        .bg-active { background-color: #198754; }
        .table-card { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .custom-table thead { background-color: var(--deep-purple); color: white; }
        .modal-content { background-color: rgb(255, 255, 255) !important; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .badge-active { background-color: #d1e7dd; color: #0f5132; padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 0.75rem; }
        .badge-disposal { background-color: #f8d7da; color: #842029; padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 0.75rem; }
        .badge-replacement { background-color: #cfe2ff; color: #ffffff; padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 0.75rem; }
        .qr-display-container { background: #f8f9fa; border: 1px dashed #dee2e6; border-radius: 8px; padding: 15px; }
        
        /* New Search Style */
        .search-control { border-radius: 20px; padding-left: 40px; }
        .search-wrapper { position: relative; width: 300px; }
        .search-wrapper i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #aaa; }
    </style>
</head>
<body>

<?php include 'aside.php'; ?>

<div class="content-wrapper">
    <nav class="top-navbar">
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="view_inventory.php" class="fw-bold border-bottom text-white">Assets</a>
        </div>
        <div class="small">Welcome, <strong><?php echo htmlspecialchars($display_name); ?></strong> | <a href="logout.php" class="text-white text-decoration-none">Logout</a></div>
    </nav>

    <div class="inventory-container">
        <div class="status-summary">
            <div class="summary-card bg-replacement"><strong>REPLACEMENT</strong><h3><?php echo $count_replacement; ?></h3></div>
            <div class="summary-card bg-disposal"><strong>DISPOSAL</strong><h3><?php echo $count_disposal; ?></h3></div>
            <div class="summary-card bg-active"><strong>ACTIVE</strong><h3><?php echo $count_active; ?></h3></div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-3">
                <h5 class="fw-bold m-0" style="color: var(--deep-purple);">INVENTORY LIST</h5>
                <div class="search-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" id="assetSearch" class="form-control search-control" placeholder="Search assets...">
                </div>
            </div>
            <button onclick="exportToPDF()" class="btn btn-danger shadow-sm">
                <i class="fas fa-file-pdf me-2"></i> Export to PDF
            </button>
        </div>

        <div class="table-card" id="pdfContent">
            <table id="inventoryTable" class="table table-hover custom-table mb-0 text-center">
                <thead>
                    <tr>
                        <th>Inventory Date</th>
                        <th>Asset Tag</th>
                        <th>Serial Number</th>
                        <th>Brand/Model</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th class="no-export">Action</th>
                    </tr>
                </thead>
                <tbody id="inventoryBody">
                    <?php while($row = mysqli_fetch_assoc($assets_result)): 
                        $statusClass = 'badge-active';
                        if($row['status'] == 'For Disposal') $statusClass = 'badge-disposal';
                        if($row['status'] == 'Replacement') $statusClass = 'badge-replacement';
                        
                        $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . $row['asset_tag'];
                    ?>
                    <tr>
                        <td><?php echo $row['inventory_date']; ?></td>
                        <td><strong><?php echo $row['asset_tag']; ?></strong></td>
                        <td><?php echo $row['serial_number']; ?></td>
                        <td><?php echo $row['brand_model']; ?></td>
                        <td><?php echo $row['location']; ?></td>
                        <td><span class="<?php echo $statusClass; ?>"><?php echo $row['status']; ?></span></td>
                        <td class="no-export">
                            <button class="btn btn-sm btn-outline-primary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editModal<?php echo $row['id']; ?>">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                        </td>
                    </tr>

                    <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <form action="" method="POST">
                                    <div class="modal-header" style="background: var(--deep-purple); color: white;">
                                        <h5 class="modal-title fw-bold">Edit Asset: <?php echo $row['asset_tag']; ?></h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="row">
                                            <div class="col-md-4 text-center border-end">
                                                <label class="fw-bold mb-2">Asset QR Code</label>
                                                <div class="qr-display-container">
                                                    <img src="<?php echo $qrCodeUrl; ?>" alt="QR" class="img-fluid shadow-sm mb-2">
                                                    <p class="small text-muted mb-0"><?php echo $row['asset_tag']; ?></p>
                                                </div>
                                                <a href="<?php echo $qrCodeUrl; ?>" target="_blank" class="btn btn-sm btn-light border mt-3 w-100">
                                                    <i class="fas fa-external-link-alt me-1"></i> Open QR
                                                </a>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="hidden" name="asset_id" value="<?php echo $row['id']; ?>">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Asset Tag</label>
                                                    <input type="text" name="asset_tag" class="form-control bg-light" value="<?php echo $row['asset_tag']; ?>" readonly>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Serial Number</label>
                                                        <input type="text" name="serial_number" class="form-control" value="<?php echo $row['serial_number']; ?>" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Brand/Model</label>
                                                        <input type="text" name="brand_model" class="form-control" value="<?php echo $row['brand_model']; ?>" required>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Location</label>
                                                    <input type="text" name="location" class="form-control" value="<?php echo $row['location']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Status</label>
                                                    <select name="status" class="form-select">
                                                        <option value="Active" <?php if($row['status']=='Active') echo 'selected'; ?>>Active</option>
                                                        <option value="For Disposal" <?php if($row['status']=='For Disposal') echo 'selected'; ?>>For Disposal</option>
                                                        <option value="Replacement" <?php if($row['status']=='Replacement') echo 'selected'; ?>>Replacement</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 bg-light">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" name="update_asset" class="btn btn-primary" style="background: var(--deep-purple); border:none;">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Search Functionality
document.getElementById('assetSearch').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#inventoryBody tr');
    
    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});

// Export to PDF Functionality
function exportToPDF() {
    const element = document.getElementById("pdfContent");
    const actions = document.querySelectorAll('.no-export');
    
    // Hide actions before capturing
    actions.forEach(el => el.style.display = 'none');

    const opt = {
        margin: 0.5,
        filename: 'Inspiro_Inventory_Report.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'landscape' }
    };

    html2pdf().set(opt).from(element).save().then(() => {
        // Show actions back
        actions.forEach(el => el.style.display = '');
    });
}
</script>

</body>
</html>