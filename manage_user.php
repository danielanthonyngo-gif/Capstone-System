<?php
session_start();
include 'config.php';

$loggedInUser = "Guest"; 
$userRole = ""; // Master, nilagay ko 'to para i-store ang role mo

if (isset($_SESSION['user_id'])) {
    $u_id = $_SESSION['user_id'];
    $u_query = mysqli_query($conn, "SELECT fullname, role FROM users WHERE id = '$u_id' LIMIT 1");
    if ($u_row = mysqli_fetch_assoc($u_query)) {
        $loggedInUser = $u_row['fullname'];
        $userRole = $u_row['role']; // Dito natin kinuha ang role mo
    }
}

// Check kung pwedeng mag-add
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user_submit'])) {
    // Master, double check: kung hindi Administrator, bawal ituloy ang insert
    if ($userRole !== 'Administrator') {
        echo "<script>alert('Error: Restricted Access! Administrators lang ang pwedeng mag-add ng user.'); window.location='manage_user.php';</script>";
        exit();
    }

    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']); 
    $role     = mysqli_real_escape_string($conn, $_POST['role']);
    $status   = "Active";

    $sql = "INSERT INTO users (fullname, username, password, role, status) VALUES ('$fullname', '$username', '$password', '$role', '$status')";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('User Added Successfully!'); window.location='manage_user.php';</script>";
    }
}

// ... (retain existing edit and delete logic) ...

$query = "SELECT * FROM users ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<body>

  <?php include 'aside.php'; ?>

    <div class="main-content">
        <nav class="top-navbar">
            <div class="d-flex gap-3">
                <a href="dashboard.php">Dashboard</a>
                <a href="view_inventory.php">Assets</a>
                <a href="manage_user.php" class="fw-bold border-bottom text-white">Users</a>
            </div>
            <div class="small">
                Welcome, <strong><?php echo htmlspecialchars($loggedInUser); ?></strong> | 
                <a href="logout.php" class="text-white ms-1">Logout</a>
            </div>
        </nav>

        <div class="page-header">
            <h4 class="fw-bold m-0" style="color: var(--deep-purple);">User Management</h4>
            
            <?php if ($userRole === 'Administrator'): ?>
                <button class="btn btn-primary" style="background:#6f42c1; border:none;" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="fas fa-plus me-2"></i> Add User
                </button>
            <?php endif; ?>
        </div>

        <div class="table-container">
            <table class="table table-hover align-middle mb-0">
                <thead style="background-color: var(--deep-purple); color: white;">
                    <tr>
                        <th class="ps-4">Full Name</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): 
                        $statClass = ($row['status'] == 'Active') ? 'badge-active' : 'badge-inactive';
                    ?>
                    <tr>
                        <td class="ps-4"><strong><?php echo htmlspecialchars($row['fullname']); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['role']); ?></td>
                        <td><span class="<?php echo $statClass; ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
                        <td class="text-center">
                            <?php if ($userRole === 'Administrator'): ?>
                                <button class="btn btn-edit" 
                                    onclick="openEditModal('<?php echo $row['id']; ?>', '<?php echo addslashes($row['fullname']); ?>', '<?php echo $row['role']; ?>', '<?php echo $row['status']; ?>')"
                                    data-bs-toggle="modal" data-bs-target="#editUserModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="javascript:void(0);" onclick="confirmDelete('<?php echo $row['id']; ?>')" class="btn btn-delete ms-1">
                                    <i class="fas fa-trash"></i>
                                </a>
                            <?php else: ?>
                                <span class="text-muted small">No Permission</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    </body>
</html>