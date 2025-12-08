<?php
if(!isset($_SESSION['user_id'])) {
    header("Location: index.php?controller=auth&action=login");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Peminjaman Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
        <?php if(isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-hand-holding"></i> Peminjaman Aktif</h2>
            <div>
                <a href="index.php?controller=peminjaman&action=create" class="btn btn-success me-2">
                    <i class="fas fa-plus"></i> Pinjam Buku Baru
                </a>
                <a href="index.php?controller=peminjaman&action=riwayat" class="btn btn-info">
                    <i class="fas fa-history"></i> Riwayat Peminjaman
                </a>
            </div>
        </div>

        <?php if($peminjamanAktif->rowCount() > 0): ?>
        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Judul Buku</th>
                                <th>Penulis</th>
                                <th>Tanggal Pinjam</th>
                                <th>Batas Kembali</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($peminjaman = $peminjamanAktif->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($peminjaman['title']); ?></td>
                                <td><?php echo htmlspecialchars($peminjaman['author']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($peminjaman['tanggal_pinjam'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($peminjaman['tanggal_kembali'])); ?></td>
                                <td>
                                    <a href="index.php?controller=peminjaman&action=kembalikan&id=<?php echo $peminjaman['id']; ?>" 
                                       class="btn btn-warning btn-sm" 
                                       onclick="return confirm('Yakin ingin mengembalikan buku ini?')">
                                        <i class="fas fa-undo-alt"></i> Kembalikan
                                    </a>
                                </td>
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
                <i class="fas fa-book-open fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Belum ada peminjaman aktif</h4>
                <p class="text-muted">Silakan pinjam buku terlebih dahulu</p>
                <a href="index.php?controller=peminjaman&action=create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Pinjam Buku
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