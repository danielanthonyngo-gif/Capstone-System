<?php

$db_name = 'dbasset'; 
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tracking | <?php echo $location; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
   <style>
        body { background-color: #f4f0ec; font-family: 'Segoe UI', sans-serif; }
        .top-nav { background: #3b1845; color: white; padding: 12px 25px; display: flex; justify-content: space-between; align-items: center; }
        .stat-card { border-radius: 8px; padding: 10px; color: white; text-align: center; width: 120px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .btn-purple { background: #8e44ad; color: white; }
        .btn-purple:hover { background: #732d91; color: white; }
    </style>

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
                //sasasas
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
    <div class="top-nav">
        <div class="fw-bold fs-5">
            <a href="view_area.php" class="text-white text-decoration-none">inspiro <i class="fas fa-home ms-1"></i></a>
        </div>
        <div class="small">Welcome, <strong>Jayson Mateo</strong> | <a href="logout.php" class="text-white text-decoration-none">Logout</a></div>
    </div>

    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex gap-2">
                <!-- <button class="btn btn-purple btn-sm">+ New</button> -->
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#deployAssetModal">
                    + New
                </button>
                <button class="btn btn-success btn-sm"><i class="fas fa-file-export me-1"></i> Export</button>
                <a href="view_area.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Back to Areas</a>
            </div>
            <div class="w-25">
                <input type="text" class="form-control form-control-sm" placeholder="Search asset tag or serial...">
            </div>
        </div>

        <div class="d-flex gap-3 mb-4">
            <!-- <div class="stat-card bg-primary text-uppercase" style="background-color: #1a6fd3 !important;"><small>Replacement</small><h3 class="m-0"><?php echo $replacement; ?></h3></div> -->
            <!-- <div class="stat-card bg-danger text-uppercase"><small>Disposal</small><h3 class="m-0"><?php echo $disposal; ?></h3></div> -->
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
                        <!-- <th>Status</th> -->
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
                        <!-- <td><span class="badge bg-success opacity-75"><?php echo $row['status']; ?></span></td> -->
                        <td><button class="btn btn-xs btn-purple py-0 px-2" style="font-size: 0.7rem;">Pullout</button></td>
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

    <!-- Modal -->
    <div class="modal fade" id="deployAssetModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deployAssetLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header text-white" style="background-color: #3b1845;">
                    <h1 class="modal-title fs-5" id="deployAssetLabel">
                        <i class="bi bi-qr-code-scan me-2"></i>Deploy New Asset
                    </h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6 border-end">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-bold mb-0">Scan QR Code</label>
                                <span id="cameraStatus" class="badge bg-secondary">Inactive</span>
                            </div>
                            
                            <div class="position-relative">
                                <div id="reader" class="rounded border bg-light d-flex align-items-center justify-content-center overflow-hidden" style="width: 100%; min-height: 280px;">
                                    <div class="text-center p-3 text-muted" id="reader-placeholder">
                                        <i class="bi bi-camera-fill fs-1 d-block mb-2"></i>
                                        <span>Ready to Scan</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <div class="btn-group w-100 shadow-sm" role="group">
                                    <button type="button" class="btn btn-primary" onclick="toggleCamera()">
                                        <i class="bi bi-power"></i> <span id="btnPowerText">Start</span>
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" onclick="switchCamera()">
                                        <i class="bi bi-camera-reversing"></i> Switch
                                    </button>
                                </div>
                                <small class="text-muted d-block mt-2 text-center">
                                    <i class="bi bi-info-circle"></i> Point the camera at the asset's QR code.
                                </small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <form id="deployForm">
                                <div class="mb-3">
                                    <label for="assetTag" class="form-label fw-bold">Asset Tag / Serial</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-tag"></i></span>
                                        <input type="text" class="form-control bg-light fw-bold text-primary" id="assetTag" placeholder="Waiting for scan..." readonly>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="equipmentName" class="form-label fw-bold">Equipment Name</label>
                                    <input type="text" class="form-control border-2" id="equipmentName" placeholder="e.g. Dell Latitude 5420">
                                </div>

                                <div class="mb-3">
                                    <label for="location" class="form-label fw-bold">Location</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-geo-alt"></i></span>
                                        <input readonly type="text" class="form-control" id="location" value="BDO Main Office">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-link text-decoration-none text-muted" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary px-4 shadow-sm" id="confirmDeploy" style="background-color: #3b1845; border: none;">
                        Confirm Deployment
                    </button>
                </div>
            </div>
        </div>
    </div>

</body>

<script>
    let html5QrCode;
    let currentFacingMode = "environment"; // Default is back camera
    let isScanning = false;

    function toggleCamera() {
        if (!isScanning) {
            startScanner();
        } else {
            stopScanner();
        }
    }

    // async function startScanner() {
    //     const readerElement = document.getElementById("reader");
    //     const placeholder = document.getElementById("reader-placeholder");
    //     const statusBadge = document.getElementById("cameraStatus");
    //     const btnText = document.getElementById("btnPowerText");

    //     if (!html5QrCode) {
    //         html5QrCode = new Html5Qrcode("reader");
    //     }

    //     try {
    //         placeholder.classList.add('d-none');
    //         await html5QrCode.start(
    //             { facingMode: currentFacingMode }, 
    //             { fps: 15, qrbox: { width: 200, height: 200 } },
    //             onScanSuccess
    //         );
            
    //         isScanning = true;
    //         statusBadge.innerText = "Live";
    //         statusBadge.className = "badge bg-success";
    //         btnText.innerText = "Stop";
    //     } catch (err) {
    //         console.error("Camera access failed", err);
    //         alert("Camera error: Check permissions.");
    //         placeholder.classList.remove('d-none');
    //     }
    // }

    // Palitan ang startScanner function ng ganito:
    async function startScanner() {
        const statusBadge = document.getElementById("cameraStatus");
        const btnText = document.getElementById("btnPowerText");
        const placeholder = document.getElementById("reader-placeholder");

    
        if (window.location.protocol !== 'https:' && window.location.hostname !== 'localhost') {
            alert("Camera access requires an HTTPS connection.");
            return;
        }

        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("reader");
        }

        try {
            statusBadge.innerText = "Requesting...";
            

            await html5QrCode.start(
                { facingMode: currentFacingMode }, 
                { 
                    fps: 10, 
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0  
                },
                onScanSuccess
            );
            
            placeholder.classList.add('d-none');
            isScanning = true;
            statusBadge.innerText = "Live";
            statusBadge.className = "badge bg-success";
            btnText.innerText = "Stop";

        } catch (err) {
            console.error("Camera error:", err);
            statusBadge.innerText = "Denied";
            statusBadge.className = "badge bg-danger";
            
            
            if (err.name === "NotAllowedError") {
                alert("Permission denied. Please enable camera in your browser settings.");
            } else {
                alert("Camera error: " + err);
            }
        }
    }

    async function stopScanner() {
        if (html5QrCode && isScanning) {
            await html5QrCode.stop();
            isScanning = false;
            document.getElementById("cameraStatus").innerText = "Inactive";
            document.getElementById("cameraStatus").className = "badge bg-secondary";
            document.getElementById("btnPowerText").innerText = "Start";
            document.getElementById("reader-placeholder").classList.remove('d-none');
        }
    }

    function onScanSuccess(decodedText, decodedResult) {
        document.getElementById('assetTag').value = decodedText;
        
        if (navigator.vibrate) navigator.vibrate(100);
    
        stopScanner();
    }

    async function switchCamera() {
        currentFacingMode = (currentFacingMode === "environment") ? "user" : "environment";
        
        if (isScanning) {
            await stopScanner();
            await startScanner();
        } else {
            alert("Camera switched to: " + (currentFacingMode === "user" ? "Front" : "Back"));
        }
    }

    
    document.getElementById('deployAssetModal').addEventListener('hidden.bs.modal', function () {
        stopScanner();
    });
</script>
</html>