<?php
$dashboardPage = $_SESSION['role'] . '_dashboard.php';
$settingsUrl = '../' . $_SESSION['role'] . '/settings.php';
?>

<header class="header">
    <div class="dashboard-header">
        <h1><a href="<?php echo $dashboardPage; ?>"><?php echo strtoupper($_SESSION['role']); ?> DASHBOARD</a></h1>
        <div class="logout">
            <div class="user-dropdown">
                <a href="#" class="d-flex align-items-center link-dark text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" data-bs-bs-target="#" aria-expanded="false">
                    <span class="me-2" style="color: white;"><?php echo  $_SESSION['username']; ?></span>
                </a>
                <ul class="dropdown-menu text-small">
                    <a class="dropdown-item" href="<?php echo $settingsUrl; ?>">Settings</a>
                   <a class="dropdown-item" href="../logout.php">Logout</a>
                </ul>
            </div>
        </div>
    </div>
</header>