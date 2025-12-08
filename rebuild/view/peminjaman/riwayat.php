<?php
// view/peminjaman/riwayat.php
if(!isset($_SESSION['user_id'])) {
    header("Location: index.php?controller=auth&action=login");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Peminjaman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .status-dipinjam { color: #dc3545; font-weight: bold; }
        .status-dikembalikan { color: #198754; font-weight: bold; }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php?controller=dashboard&action=user">
                <i class="fas fa-book-reader"></i> Perpustakaan Digital
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    <i class="fas fa-user"></i> <?php echo $_SESSION['username']; ?>
                </span>
                <a class="nav-link" href="index.php?controller=auth&action=logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-history"></i> Riwayat Peminjaman</h2>
            <div>
                <a href="index.php?controller=peminjaman&action=index" class="btn btn-primary me-2">
                    <i class="fas fa-list"></i> Peminjaman Aktif
                </a>
                <a href="index.php?controller=peminjaman&action=create" class="btn btn-success">
                    <i class="fas fa-plus"></i> Pinjam Buku Baru
                </a>
            </div>
        </div>

        <?php if($riwayatPeminjaman->rowCount() > 0): ?>
        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Judul Buku</th>
                                <th>Penulis</th>
                                <th>Tanggal Pinjam</th>
                                <th>Tanggal Kembali</th>
                                <th>Status</th>
                                <th>Durasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($peminjaman = $riwayatPeminjaman->fetch(PDO::FETCH_ASSOC)): 
                                $statusClass = $peminjaman['status'] == 'dipinjam' ? 'status-dipinjam' : 'status-dikembalikan';
                                $statusText = $peminjaman['status'] == 'dipinjam' ? 'Dipinjam' : 'Dikembalikan';
                                
                                // Hitung durasi peminjaman
                                $tglPinjam = new DateTime($peminjaman['tanggal_pinjam']);
                                $tglKembali = $peminjaman['tanggal_kembali'] ? new DateTime($peminjaman['tanggal_kembali']) : new DateTime();
                                $durasi = $tglPinjam->diff($tglKembali)->days;
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($peminjaman['title']); ?></td>
                                <td><?php echo htmlspecialchars($peminjaman['author']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($peminjaman['tanggal_pinjam'])); ?></td>
                                <td>
                                    <?php if($peminjaman['tanggal_kembali']): ?>
                                        <?php echo date('d/m/Y', strtotime($peminjaman['tanggal_kembali'])); ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="<?php echo $statusClass; ?>">
                                        <i class="fas fa-<?php echo $peminjaman['status'] == 'dipinjam' ? 'clock' : 'check'; ?>"></i>
                                        <?php echo $statusText; ?>
                                    </span>
                                </td>
                                <td><?php echo $durasi; ?> hari</td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="card shadow">
            <div class="card-body text-center py-5">
                <i class="fas fa-history fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Belum ada riwayat peminjaman</h4>
                <p class="text-muted">Mulai pinjam buku untuk melihat riwayat di sini</p>
                <a href="index.php?controller=peminjaman&action=create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Pinjam Buku Pertama
                </a>
            </div>
        </div>
        <?php endif; ?>

        <div class="mt-3">
            <a href="index.php?controller=dashboard&action=user" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>