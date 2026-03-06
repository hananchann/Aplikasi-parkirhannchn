<?php
session_start();
require_once '../../controllers/AuthController.php';
AuthController::checkRole(['admin']);

require_once '../../config/koneksi.php';

// Get log activities with pagination
$limit = 50;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$stmt = $conn->prepare("
    SELECT l.*, u.nama, u.role 
    FROM log_aktivitas l
    JOIN users u ON l.id_user = u.id_user
    ORDER BY l.waktu DESC
    LIMIT ? OFFSET ?
");
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$logs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get total count
$total_stmt = $conn->prepare("SELECT COUNT(*) as total FROM log_aktivitas");
$total_stmt->execute();
$total_result = $total_stmt->get_result()->fetch_assoc();
$total_pages = ceil($total_result['total'] / $limit);

$page_title = "Log Aktivitas";
include '../layouts/header.php';
include '../layouts/sidebar.php';
?>

<div class="container-fluid">
    <h2 class="mb-4"><i class="fas fa-history me-2"></i>Log Aktivitas Sistem</h2>
    
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-list me-2"></i>Riwayat Aktivitas</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Waktu</th>
                            <th>User</th>
                            <th>Role</th>
                            <th>Aktivitas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $index => $log): ?>
                        <tr>
                            <td><?php echo $offset + $index + 1; ?></td>
                            <td><?php echo date('d/m/Y H:i:s', strtotime($log['waktu'])); ?></td>
                            <td><?php echo htmlspecialchars($log['nama']); ?></td>
                            <td>
                                <?php
    $badge_class = '';
    switch ($log['role']) {
        case 'admin':
            $badge_class = 'bg-primary';
            break;
        case 'petugas':
            $badge_class = 'bg-success';
            break;
        case 'owner':
            $badge_class = 'bg-warning';
            break;
    }
?>
                                <span class="badge <?php echo $badge_class; ?>">
                                    <?php echo strtoupper($log['role']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($log['aktivitas']); ?></td>
                        </tr>
                        <?php
endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                    <?php
    endfor; ?>
                </ul>
            </nav>
            <?php
endif; ?>
        </div>
    </div>
</div>

    </div></div>
<?php include '../layouts/footer.php'; ?>
