<?php
session_start();
include 'config.php';


$loggedInUser = "Guest"; 
if (isset($_SESSION['user_id'])) {
    $u_id = $_SESSION['user_id'];
    $u_query = mysqli_query($conn, "SELECT fullname FROM users WHERE id = '$u_id' LIMIT 1");
    if ($u_row = mysqli_fetch_assoc($u_query)) {
        $loggedInUser = $u_row['fullname'];
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user_submit'])) {
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


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_user_submit'])) {
    $user_id  = mysqli_real_escape_string($conn, $_POST['user_id']);
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $role     = mysqli_real_escape_string($conn, $_POST['role']);
    $status   = mysqli_real_escape_string($conn, $_POST['status']);

    $update_sql = "UPDATE users SET fullname='$fullname', role='$role', status='$status' WHERE id='$user_id'";
    if (mysqli_query($conn, $update_sql)) {
        echo "<script>alert('User Updated Successfully!'); window.location='manage_user.php';</script>";
    }
}


if (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    if ($delete_id == $_SESSION['user_id']) {
        echo "<script>alert('Bawal i-delete ang sariling account!'); window.location='manage_user.php';</script>";
    } else {
        if (mysqli_query($conn, "DELETE FROM users WHERE id = '$delete_id'")) {
            echo "<script>alert('User Deleted!'); window.location='manage_user.php';</script>";
        }
    }
}


$query = "SELECT * FROM users ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inspiro | Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --deep-purple: #3b1845; --sidebar-width: 260px; }
        body { background-color: #f4f0e6; font-family: 'Inter', sans-serif; margin: 0; }
        
        .sidebar { width: var(--sidebar-width); height: 100vh; position: fixed; background: var(--deep-purple); color: white; z-index: 1000; }
        .sidebar-brand { padding: 25px; text-align: center; font-size: 1.5rem; font-weight: bold; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .nav-menu { padding: 20px 0; }
        .nav-item { padding: 12px 25px; display: flex; align-items: center; color: rgba(255,255,255,0.7); text-decoration: none; transition: 0.3s; }
        .nav-item:hover, .nav-item.active { background: rgba(255,255,255,0.1); color: white; border-left: 4px solid #a29bfe; }
        .nav-item i { margin-right: 15px; width: 20px; text-align: center; }

        .main-content { margin-left: var(--sidebar-width); min-height: 100vh; }
        .top-navbar { background-color: var(--deep-purple); padding: 10px 30px; display: flex; justify-content: space-between; align-items: center; color: white; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .top-navbar a { color: #d1d1d1; text-decoration: none; font-size: 0.85rem; }
        
        .page-header { padding: 20px 30px; display: flex; justify-content: space-between; align-items: center; }
        .table-container { background: white; border-radius: 12px; margin: 0 30px 30px 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; }
        .badge-active { background-color: #d1e7dd; color: #0f5132; padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 0.75rem; }
        .badge-inactive { background-color: #f8d7da; color: #842029; padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 0.75rem; }
        .btn-edit { background-color: #6f42c1; color: white; border: none; padding: 6px 12px; border-radius: 4px; }
        .btn-delete { background-color: #dc3545; color: white; border: none; padding: 6px 12px; border-radius: 4px; }
    </style>
</head>
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
            <button class="btn btn-primary" style="background:#6f42c1; border:none;" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="fas fa-plus me-2"></i> Add User
            </button>
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
                            <button class="btn btn-edit" 
                                onclick="openEditModal('<?php echo $row['id']; ?>', '<?php echo addslashes($row['fullname']); ?>', '<?php echo $row['role']; ?>', '<?php echo $row['status']; ?>')"
                                data-bs-toggle="modal" data-bs-target="#editUserModal">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="javascript:void(0);" onclick="confirmDelete('<?php echo $row['id']; ?>')" class="btn btn-delete ms-1">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background: var(--deep-purple);">
                    <h5 class="modal-title fw-bold">Add New User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Full Name</label>
                            <input type="text" name="fullname" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Username (Email)</label>
                            <input type="email" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Role</label>
                            <select name="role" class="form-select">
                                <option value="Technical Support">Technical Support</option>
                                <option value="Administrator">Administrator</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_user_submit" class="btn btn-primary" style="background:#6f42c1; border:none;">Save User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background: var(--deep-purple);">
                    <h5 class="modal-title fw-bold">Edit User Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body p-4">
                        <input type="hidden" name="user_id" id="edit_user_id">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Full Name</label>
                            <input type="text" name="fullname" id="edit_fullname" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Role</label>
                            <select name="role" id="edit_role" class="form-select">
                                <option value="Technical Support">Technical Support</option>
                                <option value="Administrator">Administrator</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Status</label>
                            <select name="status" id="edit_status" class="form-select">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="edit_user_submit" class="btn btn-primary" style="background:#6f42c1; border:none;">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openEditModal(id, name, role, status) {
            document.getElementById('edit_user_id').value = id;
            document.getElementById('edit_fullname').value = name;
            document.getElementById('edit_role').value = role;
            document.getElementById('edit_status').value = status;
        }

        function confirmDelete(id) {
            if (confirm("Master, sigurado ka bang gusto mong i-delete ang user na ito?")) {
                window.location.href = "manage_user.php?delete_id=" + id;
            }
        }
    </script>
</body>
</html>