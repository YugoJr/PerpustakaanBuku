<?php
if(!isset($_SESSION['user_id'])) {
    header("Location: index.php?controller=auth&action=login");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .feature-card {
            transition: transform 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .book-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 12px;
            overflow: hidden;
            height: 100%;
        }
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .book-image {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
        .book-image-placeholder {
            height: 200px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }
        .book-title {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            font-size: 1rem;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .book-author {
            color: #7f8c8d;
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }
        .book-year {
            color: #95a5a6;
            font-size: 0.8rem;
        }
        .genre-badge {
            font-size: 0.7rem;
            padding: 3px 8px;
            border-radius: 10px;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">
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

        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-gradient-primary text-white shadow" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="card-title">Selamat Datang, <?php echo $_SESSION['username']; ?>! 👋</h2>
                                <p class="card-text">Jelajahi koleksi buku menarik di perpustakaan digital kami.</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <i class="fas fa-book-open fa-5x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="row mb-4 justify-content-center">
            <div class="col-md-3 mb-3">
                <div class="card feature-card border-left-success shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Buku Tersedia</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalBooks; ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-book fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card feature-card border-left-primary shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Sedang Dipinjam</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalDipinjam; ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-hand-holding fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card feature-card border-left-info shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Riwayat Pinjam</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalRiwayat; ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-history fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card feature-card border-left-warning shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Status</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Aktif</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-check fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<!-- Features -->
<div class="row mb-4 justify-content-center">
    <div class="col-lg-6 col-md-6 mb-3">
        <div class="card feature-card h-100">
            <div class="card-body text-center">
                <i class="fas fa-hand-holding fa-3x text-success mb-3"></i>
                <h5 class="card-title">Pinjam Buku</h5>
                <p class="card-text">Pinjam buku yang tersedia untuk dibaca</p>
                <a href="index.php?controller=peminjaman&action=create" class="btn btn-success btn-sm">Pinjam Buku</a>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-md-6 mb-3">
        <div class="card feature-card h-100">
            <div class="card-body text-center">
                <i class="fas fa-undo-alt fa-3x text-info mb-3"></i>
                <h5 class="card-title">Kembalikan Buku</h5>
                <p class="card-text">Kembalikan buku yang sudah selesai dibaca</p>
                <a href="index.php?controller=peminjaman&action=index" class="btn btn-info btn-sm">Lihat Peminjaman</a>
            </div>
        </div>
    </div>
</div>

        <!-- Books Section -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h5 class="m-0"><i class="fas fa-book"></i> Semua Buku Tersedia (<?php echo $totalBooks; ?>)</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php if($allBooks->rowCount() > 0): ?>
                        <?php while($book = $allBooks->fetch(PDO::FETCH_ASSOC)): 
                            // Determine genre class
                            $genreClass = 'bg-secondary';
                            if (!empty($book['genre'])) {
                                $genreSlug = strtolower(str_replace(' ', '-', $book['genre']));
                                $genreClass = "bg-primary";
                            }
                        ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <div class="card book-card h-100">
                                <div class="position-relative">
                                    <?php if(!empty($book['gambar'])): ?>
                                        <img src="<?php echo BASE_URL; ?>/uploads/<?php echo htmlspecialchars($book['gambar']); ?>"  
                                             class="book-image" 
                                             alt="<?php echo htmlspecialchars($book['title']); ?>"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="book-image-placeholder" style="display: none;">
                                            <i class="fas fa-book fa-3x"></i>
                                        </div>
                                    <?php else: ?>
                                        <div class="book-image-placeholder">
                                            <i class="fas fa-book fa-3x"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if(!empty($book['genre'])): ?>
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <span class="badge genre-badge <?php echo $genreClass; ?>">
                                                <i class="fas fa-tag me-1"></i>
                                                <?php echo htmlspecialchars($book['genre']); ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h6 class="book-title"><?php echo htmlspecialchars($book['title']); ?></h6>
                                    <p class="book-author">Oleh: <?php echo htmlspecialchars($book['author']); ?></p>
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="book-year"><?php echo htmlspecialchars($book['year']); ?></span>
                                            <small class="text-muted">ID: <?php echo $book['id']; ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5">
                            <i class="fas fa-book fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada buku tersedia</h5>
                            <p class="text-muted">Silakan hubungi admin untuk menambahkan buku.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
        <!-- Peminjaman Aktif -->
        <?php if(isset($peminjamanAktif) && $peminjamanAktif->rowCount() > 0): ?>
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-warning text-white">
                        <h5 class="m-0"><i class="fas fa-hand-holding"></i> Peminjaman Aktif</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Judul Buku</th>
                                        <th>Tanggal Pinjam</th>
                                        <th>Batas Kembali</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($peminjaman = $peminjamanAktif->fetch(PDO::FETCH_ASSOC)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($peminjaman['title']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($peminjaman['tanggal_pinjam'])); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($peminjaman['tanggal_kembali'])); ?></td>
                                        <td>
                                            <a href="index.php?controller=peminjaman&action=kembalikan&id=<?php echo $peminjaman['id']; ?>" 
                                               class="btn btn-warning btn-sm"
                                               onclick="return confirm('Yakin ingin mengembalikan buku ini?')">
                                                <i class="fas fa-undo-alt me-1"></i>Kembalikan
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle broken images
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('.book-image');
            images.forEach(img => {
                img.onerror = function() {
                    this.style.display = 'none';
                    const placeholder = this.nextElementSibling;
                    if (placeholder && placeholder.classList.contains('book-image-placeholder')) {
                        placeholder.style.display = 'flex';
                    }
                };
            });
        });
    </script>
</body>
</html>