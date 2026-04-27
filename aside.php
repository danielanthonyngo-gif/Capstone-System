<aside class="sidebar">
    <div class="brand-section">
        <i class="fas fa-cube me-2"></i> INSPIRO
    </div>
    <nav
     class="nav-menu">
        <a href="index.php" class="nav-item active"><i class="fas fa-th-large"></i> Dashboards</a>
        <a href="view_area.php" class="nav-item"><i class="fas fa-map-marker-alt"></i> View Areas</a>
        <a href="view_inventory.php" class="nav-item"><i class="fas fa-boxes"></i> View Inventory</a>
        <a href="create_item.php" class="nav-item"><i class="fas fa-plus-circle"></i> Assets Management</a>
        <a href="manage_user.php" class="nav-item"><i class="fas fa-users-cog"></i> Manage Users</a>
    </nav><?php
// Aside.php - Sidebar Component
$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
    :root {
        --sidebar-bg: #3b1845; 
        --sidebar-hover: rgba(255, 255, 255, 0.1);
        --accent-color: #a29bfe; 
        --sidebar-width: 260px;
    }

    /* Sidebar Main Style */
    .sidebar {
        width: var(--sidebar-width);
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        background-color: var(--sidebar-bg);
        color: white;
        z-index: 1000;
        box-shadow: 2px 0 10px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
    }

    /* Brand Section */
    .brand-section {
        padding: 25px;
        font-size: 1.4rem;
        font-weight: 700;
        background: rgba(0,0,0,0.2);
        text-align: center;
        letter-spacing: 2px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    /* Navigation Menu */
    .nav-menu {
        padding-top: 20px;
    }

    .nav-item {
        padding: 14px 25px;
        display: flex;
        align-items: center;
        color: rgba(255, 255, 255, 0.8) !important;
        text-decoration: none !important; /* Tanggal blue underline */
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
        font-size: 0.95rem;
    }

    .nav-item i {
        margin-right: 15px;
        width: 25px;
        text-align: center;
        font-size: 1.1rem;
    }

    /* Hover and Active State */
    .nav-item:hover, .nav-item.active {
        background: var(--sidebar-hover);
        color: white !important;
        border-left: 4px solid var(--accent-color);
    }

    /* Content Adjustment */
    .content-wrapper, .content {
        margin-left: var(--sidebar-width);
        transition: all 0.3s ease;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .sidebar { left: -260px; } /* Tago sidebar sa maliit na screen */
        .content-wrapper, .content { margin-left: 0; }
    }
</style>

<div class="sidebar">
    <div class="brand-section">
        <i class="fas fa-box"></i>
        <span>INSPIRO</span>
    </div>

    <nav class="nav-menu">
        <a href="index.php" class="nav-item <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
            <i class="fas fa-th-large"></i> 
            <span>Dashboards</span>
        </a>

        <a href="view_area.php" class="nav-item <?php echo ($current_page == 'view_area.php') ? 'active' : ''; ?>">
            <i class="fas fa-map-marker-alt"></i> 
            <span>View Areas</span>
        </a>

        <a href="view_inventory.php" class="nav-item <?php echo ($current_page == 'view_inventory.php') ? 'active' : ''; ?>">
            <i class="fas fa-boxes"></i> 
            <span>View Inventory</span>
        </a>

        <a href="create_item.php" class="nav-item <?php echo ($current_page == 'create_item.php') ? 'active' : ''; ?>">
            <i class="fas fa-plus-circle"></i> 
            <span>Assets Management</span>
        </a>

        <a href="manage_user.php" class="nav-item <?php echo ($current_page == 'manage_user.php') ? 'active' : ''; ?>">
            <i class="fas fa-users-cog"></i> 
            <span>Manage Users</span>
        </a>
    </nav>
</div>
</aside>