<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <i class="fas fa-parking"></i>
        <h4>Sistem Parkir</h4>
        <p class="user-role"><?php echo ucfirst($_SESSION['role']); ?></p>
    </div>
    
    <div class="sidebar-menu">
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="/views/admin/dashboard.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="/views/admin/users.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                <span>Kelola User</span>
            </a>
            <a href="/views/admin/tarif.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'tarif.php' ? 'active' : ''; ?>">
                <i class="fas fa-money-bill"></i>
                <span>Kelola Tarif</span>
            </a>
            <a href="/views/admin/area.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'area.php' ? 'active' : ''; ?>">
                <i class="fas fa-map-marked-alt"></i>
                <span>Kelola Area</span>
            </a>
            <a href="/views/admin/kendaraan.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'kendaraan.php' ? 'active' : ''; ?>">
                <i class="fas fa-car"></i>
                <span>Jenis Kendaraan</span>
            </a>
            <a href="/views/admin/log.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'log.php' ? 'active' : ''; ?>">
                <i class="fas fa-history"></i>
                <span>Log Aktivitas</span>
            </a>
        <?php
elseif ($_SESSION['role'] === 'petugas'): ?>
            <a href="/views/petugas/dashboard.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="/views/petugas/masuk.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'masuk.php' ? 'active' : ''; ?>">
                <i class="fas fa-sign-in-alt"></i>
                <span>Kendaraan Masuk</span>
            </a>
            <a href="/views/petugas/keluar.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'keluar.php' ? 'active' : ''; ?>">
                <i class="fas fa-sign-out-alt"></i>
                <span>Kendaraan Keluar</span>
            </a>
        <?php
elseif ($_SESSION['role'] === 'owner'): ?>
            <a href="/views/owner/dashboard.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
        <?php
endif; ?>
        
        <a href="/logout.php" class="menu-item logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</div>

<div class="main-content">
    <div class="topbar">
        <button class="btn-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="topbar-right">
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?php echo $_SESSION['nama']; ?></span>
            </div>
        </div>
    </div>
    
    <div class="content-wrapper">
