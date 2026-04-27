<?php
session_start();
include 'config.php';

// 1. Proteksyon: Kung hindi naka-login, balik sa login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Kunin ang location mula sa URL (Default ay 'BDO')
$location = isset($_GET['location']) ? mysqli_real_escape_string($conn, $_GET['location']) : 'BDO';

// 3. Stats Query base sa location
$active = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM assets WHERE location = '$location' AND status = 'Active'"))['t'] ?? 0;

// 4. Kunin ang listahan ng assets
$assets = mysqli_query($conn, "SELECT * FROM assets WHERE location = '$location'");

// Para manatiling 'active' ang View Areas sa sidebar
$current_page = 'view_area.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tracking | <?php echo htmlspecialchars($location); ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>

    <style>
        :root { 
            --primary-purple: #6f42c1; 
            --deep-purple: #3b1845;
            --bg-light: #f4f0ec;
            --sidebar-width: 260px;
        }

        body { background-color: var(--bg-light); font-family: 'Segoe UI', sans-serif; margin: 0; }
        
        .content-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        .top-nav { 
            background: var(--deep-purple); 
            color: white; 
            padding: 12px 25px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }

        .stat-card { 
            border-radius: 8px; 
            padding: 15px; 
            color: white; 
            text-align: center; 
            width: 140px; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
        }

        .btn-purple { background: #8e44ad; color: white; border: none; }
        .btn-purple:hover { background: #732d91; color: white; }

        @media (max-width: 992px) {
            .content-wrapper { margin-left: 0; }
        }
            .search-wrapper {
        position: relative;
        max-width: 350px;
        width: 100%;
    }

    .search-wrapper input {
        padding: 12px 20px 12px 45px;
        border-radius: 15px;
        border: 1px solid #e0e0e0;
        background: #ffffff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }

    .search-wrapper input:focus {
        border-color: var(--primary-purple);
        box-shadow: 0 4px 15px rgba(111, 66, 193, 0.15);
        outline: none;
    }

    .search-wrapper i {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: #adb5bd;
    }

    /* Enhanced Button Styles */
    .action-btn {
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        border: none;
    }

    .btn-new { background: #0d6efd; color: white; }
    .btn-export { background: #198754; color: white; }
    .btn-back { background: #6c757d; color: white; }

    .action-btn:hover {
        transform: translateY(-2px);
        filter: brightness(1.1);
        color: white;
    }

    /* Active Assets Modern Card */
    .active-asset-card {
        background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
        color: white;
        padding: 20px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 260px;
        box-shadow: 0 10px 20px rgba(39, 174, 96, 0.2);
        border: 1px solid rgba(255,255,255,0.2);
    }

    .active-asset-card .icon-circle {
        background: rgba(255, 255, 255, 0.2);
        width: 50px;
        height: 50px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }

    .active-asset-card .label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: 0.9;
        font-weight: 700;
    }

    .active-asset-card .count {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
    }
    </style>
</head>
<body>

    <?php include 'aside.php'; ?>
    
    <div class="content-wrapper">
        <div class="top-nav">
            <div class="fw-bold fs-5">
                <a href="view_area.php" class="text-white text-decoration-none">inspiro <i class="fas fa-home ms-1"></i></a>
            </div>
            <div class="small">
                Welcome, <strong><?php echo $_SESSION['user']; ?></strong> | 
                <a href="logout.php" class="text-white text-decoration-none"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>

        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#deployAssetModal">
                        <i class="fas fa-plus-circle me-1"></i> + New
                    </button>
                    <button class="btn btn-success shadow-sm"><i class="fas fa-file-export me-1"></i> Export</button>
                    <a href="view_area.php" class="btn btn-secondary shadow-sm"><i class="fas fa-arrow-left me-1"></i> Back to Areas</a>
                </div>
                <div class="w-25">
                    <input type="text" class="form-control" placeholder="Search asset tag...">
                </div>
            </div>

            <div class="d-flex gap-3 mb-4">
                <div class="stat-card bg-success text-uppercase">
                    <small>Active Assets</small>
                    <h3 class="m-0 fw-bold"><?php echo $active; ?></h3>
                </div>
            </div>

            <h5 class="fw-bold mb-3 text-dark text-uppercase"><?php echo htmlspecialchars($location); ?> INVENTORY</h5>
            
            <div class="bg-white rounded shadow-sm overflow-hidden border">
                <table class="table table-hover mb-0 align-middle">
                    <thead style="background: var(--deep-purple); color: white;">
                        <tr class="small">
                            <th class="py-3 px-4">Inventory Date</th>
                            <th>Asset Tag</th>
                            <th>Serial Number</th>
                            <th>Brand/Model</th>
                            <th>Type</th>
                            <th>Year/Model</th>
                            <th>Location</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        <?php while($row = mysqli_fetch_assoc($assets)): ?>
                        <tr>
                            <td class="px-4"><?php echo date("m/d/Y", strtotime($row['created_at'] ?? 'now')); ?></td>
                            <td><strong><?php echo $row['asset_tag']; ?></strong></td>
                            <td><?php echo $row['serial_number']; ?></td>
                            <td><?php echo $row['brand_model']; ?></td>
                            <td><?php echo isset($row['type']) ? $row['type'] : 'N/A'; ?></td>
                            <td><?php echo $row['year_model'] ?? 'N/A'; ?></td>
                            <td><span class="badge bg-light text-dark border"><?php echo $row['location']; ?></span></td>
                            <td class="text-center">
                                <button class="btn btn-purple btn-sm px-3 rounded-pill">Pullout</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        
                        <?php if(mysqli_num_rows($assets) == 0): ?>
                        <tr><td colspan="8" class="text-center py-5 text-muted">Walang assets na nahanap sa location na ito.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deployAssetModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header text-white" style="background-color: var(--deep-purple);">
                    <h1 class="modal-title fs-5"><i class="bi bi-qr-code-scan me-2"></i>Deploy New Asset</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" onclick="stopScanner()"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6 border-end">
                            <label class="form-label fw-bold">Scan QR Code <span id="cameraStatus" class="badge bg-secondary ms-2">Inactive</span></label>
                            <div id="reader" class="rounded border bg-light overflow-hidden" style="min-height: 280px;">
                                <div class="text-center p-5 text-muted" id="reader-placeholder">
                                    <i class="bi bi-camera fs-1"></i><p>Ready to Scan</p>
                                </div>
                            </div>
                            <div class="mt-3 btn-group w-100">
                                <button type="button" class="btn btn-primary" onclick="toggleCamera()" id="btnPowerText">Start Camera</button>
                                <button type="button" class="btn btn-outline-primary" onclick="switchCamera()">Switch</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <form id="deployForm">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Asset Tag / Serial</label>
                                    <input type="text" class="form-control bg-light fw-bold" id="assetTag" readonly placeholder="Waiting for scan...">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Equipment Name</label>
                                    <input type="text" class="form-control" id="equipmentName" placeholder="e.g. Dell Optiplex 7010">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Current Location</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($location); ?>" readonly>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal" onclick="stopScanner()">Cancel</button>
                    <button type="button" class="btn btn-primary px-4" style="background-color: var(--deep-purple); border: none;">Confirm Deployment</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let html5QrCode;
        let isScanning = false;
        let currentFacingMode = "environment";

        async function toggleCamera() {
            if (!isScanning) {
                if (!html5QrCode) html5QrCode = new Html5Qrcode("reader");
                try {
                    document.getElementById("reader-placeholder").classList.add('d-none');
                    await html5QrCode.start({ facingMode: currentFacingMode }, { fps: 10, qrbox: 250 }, onScanSuccess);
                    isScanning = true;
                    updateUI(true);
                } catch (err) { alert("Camera Error: " + err); }
            } else { stopScanner(); }
        }

        async function stopScanner() {
            if (html5QrCode && isScanning) {
                await html5QrCode.stop();
                isScanning = false;
                updateUI(false);
                document.getElementById("reader-placeholder").classList.remove('d-none');
            }
        }

        function onScanSuccess(decodedText) {
            document.getElementById('assetTag').value = decodedText;
            if (navigator.vibrate) navigator.vibrate(100);
            stopScanner();
        }

        function updateUI(active) {
            const status = document.getElementById("cameraStatus");
            const btn = document.getElementById("btnPowerText");
            status.innerText = active ? "Live" : "Inactive";
            status.className = active ? "badge bg-success ms-2" : "badge bg-secondary ms-2";
            btn.innerText = active ? "Stop Camera" : "Start Camera";
        }

        async function switchCamera() {
            currentFacingMode = (currentFacingMode === "environment") ? "user" : "environment";
            if (isScanning) { await stopScanner(); toggleCamera(); }
        }
    </script>
</body>
</html>