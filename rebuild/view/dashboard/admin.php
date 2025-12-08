<?php
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php?controller=auth&action=login");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .book-image {
            width: 60px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }
        .book-image-placeholder {
            width: 60px;
            height: 80px;
            background: #f8f9fa;
            border: 1px dashed #dee2e6;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }
        .table th {
            border-top: none;
            font-weight: 600;
        }
        .genre-badge {
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 12px;
        }
        .genre-fiksi { background: #e3f2fd; color: #1976d2; }
        .genre-non-fiksi { background: #f3e5f5; color: #7b1fa2; }
        .genre-romance { background: #fce4ec; color: #c2185b; }
        .genre-misteri { background: #e8eaf6; color: #303f9f; }
        .genre-sains { background: #e8f5e8; color: #388e3c; }
        .genre-sejarah { background: #fff3e0; color: #f57c00; }
        .genre-teknologi { background: #fbe9e7; color: #d84315; }
        .genre-biografi { background: #e0f2f1; color: #00796b; }
        .genre-komik { background: #fff8e1; color: #ff8f00; }
        .genre-fantasi { background: #f1f8e9; color: #689f38; }
        .genre-horror { background: #ffebee; color: #d32f2f; }
        .genre-petualangan { background: #e0f7fa; color: #0097a7; }
        .genre-pendidikan { background: #e8f5e8; color: #2e7d32; }
        .genre-agama { background: #fffde7; color: #f9a825; }
        .genre-kesehatan { background: #e0f2f1; color: #00695c; }
        .genre-bisnis { background: #f3e5f5; color: #7b1fa2; }
        .genre-seni { background: #fce4ec; color: #ad1457; }
        .genre-musik { background: #fff3e0; color: #ef6c00; }
        .genre-olahraga { background: #e8f5e8; color: #2e7d32; }
        .feature-card {
            transition: transform 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        
        .search-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .search-input {
            border-radius: 25px 0 0 25px;
            border: 2px solid #28a745;
            border-right: none;
            padding: 10px 15px;
        }
        .search-btn {
            border-radius: 0 25px 25px 0;
            background: #28a745;
            border: 2px solid #28a745;
            color: white;
            padding: 10px 20px;
        }
        .filter-select {
            border-radius: 25px;
            border: 2px solid #28a745;
            padding: 10px 15px;
        }

        /* Custom Styles for Books Grid */
        #booksGrid {
            scrollbar-width: thin;
            scrollbar-color: #28a745 #f8f9fa;
        }

        #booksGrid::-webkit-scrollbar {
            width: 8px;
        }

        #booksGrid::-webkit-scrollbar-track {
            background: #f8f9fa;
            border-radius: 10px;
        }

        #booksGrid::-webkit-scrollbar-thumb {
            background: #28a745;
            border-radius: 10px;
        }

        #booksGrid::-webkit-scrollbar-thumb:hover {
            background: #218838;
        }

        .book-card {
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            overflow: hidden;
        }

        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            border-color: #28a745;
        }

        .book-image-large {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #f8f9fa;
        }

        .book-image-placeholder-large {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }

        .book-title {
            font-weight: 600;
            color: #2c3e50;
            font-size: 1rem;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 2.8em;
        }

        .book-author {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .book-year {
            color: #95a5a6;
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }

        .book-details {
            padding: 0 5px;
        }

        .book-actions {
            margin-top: 10px;
        }

        .book-actions .btn {
            font-size: 0.8rem;
            padding: 5px 10px;
        }

        /* Genre Badge Styles */
        .genre-badge {
            font-size: 0.75rem;
            padding: 6px 12px;
            border-radius: 15px;
            font-weight: 500;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .book-image-large {
                height: 180px;
            }
            
            .book-image-placeholder-large {
                height: 180px;
            }
            
            #booksGrid {
                max-height: 500px;
            }
        }

        @media (max-width: 576px) {
            .book-image-large {
                height: 160px;
            }
            
            .book-image-placeholder-large {
                height: 160px;
            }
            
            .book-title {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-book"></i> Perpustakaan Digital - Admin
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    <i class="fas fa-user-shield"></i> <?php echo $_SESSION['username']; ?>
                </span>
                <a class="nav-link" href="index.php?controller=auth&action=logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
                </a>
                <span> 
                <a class="nav-link" href="index.php?controller=dashboard&action=user">
                    <i class="fas fa-user"></i> Switch Mode
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
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

        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-gradient-primary text-white shadow" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="card-title">Selamat Datang, <?php echo $_SESSION['username']; ?>! 👋</h2>
                                <p class="card-text">Panel Administrasi Perpustakaan Digital</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <i class="fas fa-user-shield fa-5x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4 justify-content-center">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Buku</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo isset($totalBooks) ? $totalBooks : '0'; ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-book fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Users</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo isset($totalUsers) ? $totalUsers : '0'; ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Role</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Admin</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4 justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h6 class="m-0 font-weight-bold"><i class="fas fa-bolt"></i> Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-center flex-wrap">
                            <div class="mx-2 mb-3">
                                <a href="index.php?controller=book&action=create" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Tambah Buku
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="search-section mb-4">
            <form id="searchForm" class="row g-3 align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" 
                               class="form-control search-input" 
                               id="searchInput" 
                               placeholder="Cari judul atau penulis buku..." 
                               onkeyup="filterBooks()">
                        <button class="btn search-btn" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <select class="form-select filter-select" id="genreFilter" onchange="filterBooks()">
                        <option value="">Semua Genre</option>
                        <?php 
                        $genres = [
                            'Fiksi', 'Non-Fiksi', 'Romance', 'Misteri', 'Sains', 'Sejarah', 
                            'Teknologi', 'Biografi', 'Komik', 'Fantasi', 'Horror', 'Petualangan',
                            'Pendidikan', 'Agama', 'Kesehatan', 'Bisnis', 'Seni', 'Musik', 'Olahraga'
                        ];
                        foreach($genres as $genreItem): ?>
                            <option value="<?php echo strtolower($genreItem); ?>">
                                <?php echo $genreItem; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-secondary w-100" onclick="resetFilters()" style="border-radius: 25px;">
                        <i class="fas fa-undo me-1"></i>Reset
                    </button>
                </div>
            </form>
        </div>

        <!-- Daftar Buku Section -->
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="m-0"><i class="fas fa-book"></i> Daftar Buku</h5>
                            <span class="badge bg-light text-dark" id="bookCount">
                                <?php echo ($books && $books->rowCount() > 0) ? $books->rowCount() : '0'; ?> Buku
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Books Grid with Scroll -->
                        <div id="booksGrid" class="row" style="max-height: 600px; overflow-y: auto; padding: 10px;">
                            <?php 
                            if($books && $books->rowCount() > 0): 
                                $books->execute(); // Reset pointer
                                $bookIndex = 0;
                                while($row = $books->fetch(PDO::FETCH_ASSOC)): 
                                    // Determine genre class
                                    $genreClass = 'bg-secondary';
                                    if (!empty($row['genre'])) {
                                        $genreSlug = strtolower(str_replace(' ', '-', $row['genre']));
                                        $genreClass = "genre-$genreSlug";
                                    }
                            ?>
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-4 book-item" 
                                 data-title="<?php echo htmlspecialchars(strtolower($row['title'])); ?>"
                                 data-author="<?php echo htmlspecialchars(strtolower($row['author'])); ?>"
                                 data-genre="<?php echo !empty($row['genre']) ? htmlspecialchars(strtolower($row['genre'])) : ''; ?>">
                                <div class="card book-card h-100 shadow-sm">
                                    <div class="card-body text-center p-3">
                                        <!-- Book Image -->
                                        <div class="mb-3">
                                            <?php if(!empty($row['gambar'])): ?>
                                                <img src="uploads/<?php echo htmlspecialchars($row['gambar']); ?>" 
                                                     alt="<?php echo htmlspecialchars($row['title']); ?>" 
                                                     class="book-image-large"
                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="book-image-placeholder-large" style="display: none;">
                                                    <i class="fas fa-book fa-3x"></i>
                                                </div>
                                            <?php else: ?>
                                                <div class="book-image-placeholder-large">
                                                    <i class="fas fa-book fa-3x"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Book Details -->
                                        <div class="book-details">
                                            <h6 class="book-title mb-2"><?php echo htmlspecialchars($row['title']); ?></h6>
                                            
                                            <p class="book-author mb-2">
                                                <i class="fas fa-user-edit text-muted me-1"></i>
                                                <?php echo htmlspecialchars($row['author']); ?>
                                            </p>
                                            
                                            <p class="book-year mb-2">
                                                <i class="fas fa-calendar-alt text-muted me-1"></i>
                                                <?php echo htmlspecialchars($row['year']); ?>
                                            </p>
                                            
                                            <div class="book-genre mb-3">
                                                <?php if(!empty($row['genre'])): ?>
                                                    <span class="badge genre-badge <?php echo $genreClass; ?>">
                                                        <i class="fas fa-tag me-1"></i>
                                                        <?php echo htmlspecialchars($row['genre']); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-muted genre-badge">
                                                        <i class="fas fa-times me-1"></i>
                                                        Belum diatur
                                                    </span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="book-actions">
                                                <a href="index.php?controller=book&action=edit&id=<?php echo $row['id']; ?>" 
                                                   class="btn btn-warning btn-sm me-1" 
                                                   title="Edit Buku">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <a href="index.php?controller=book&action=delete&id=<?php echo $row['id']; ?>" 
                                                   class="btn btn-danger btn-sm" 
                                                   onclick="return confirm('Yakin ingin menghapus buku <?php echo addslashes($row['title']); ?>?')"
                                                   title="Hapus Buku">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </a>
                                            </div>

                                            <small class="text-muted d-block mt-2">
                                                ID: <?php echo $row['id']; ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php 
                                $bookIndex++;
                                endwhile;
                            else:
                            ?>
                            <div class="col-12 text-center py-5 no-books-message">
                                <i class="fas fa-book fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">Tidak ada data buku</h4>
                                <p class="text-muted mb-4">Belum ada buku yang ditambahkan.</p>
                                <a href="index.php?controller=book&action=create" class="btn btn-primary btn-lg">
                                    <i class="fas fa-plus"></i> Tambah Buku Pertama
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- No Results Message (Hidden by default) -->
                        <div id="noResultsMessage" class="col-12 text-center py-5" style="display: none;">
                            <i class="fas fa-search fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">Tidak ada hasil ditemukan</h4>
                            <p class="text-muted mb-4">Tidak ada buku yang sesuai dengan kriteria pencarian Anda.</p>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetFilters()">
                                <i class="fas fa-undo me-1"></i> Reset Pencarian
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="row mt-4 justify-content-center">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0"><?php 
                                    // Hitung ulang total books
                                    $totalBooksCount = $books ? $books->rowCount() : 0;
                                    echo $totalBooksCount;
                                ?></h4>
                                <p class="mb-0">Total Buku</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-book fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0"><?php 
                                    // Count books with genre
                                    $booksWithGenre = 0;
                                    if ($books) {
                                        $books->execute(); // Reset pointer
                                        while($row = $books->fetch(PDO::FETCH_ASSOC)) {
                                            if (!empty($row['genre'])) {
                                                $booksWithGenre++;
                                            }
                                        }
                                    }
                                    echo $booksWithGenre;
                                ?></h4>
                                <p class="mb-0">Dengan Genre</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-tag fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0"><?php 
                                    $booksWithoutGenre = $totalBooksCount - $booksWithGenre;
                                    echo $booksWithoutGenre;
                                ?></h4>
                                <p class="mb-0">Tanpa Genre</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-exclamation-circle fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0"><?php 
                                    // Count unique genres
                                    $uniqueGenres = [];
                                    if ($books) {
                                        $books->execute(); // Reset pointer
                                        while($row = $books->fetch(PDO::FETCH_ASSOC)) {
                                            if (!empty($row['genre'])) {
                                                $uniqueGenres[$row['genre']] = true;
                                            }
                                        }
                                    }
                                    echo count($uniqueGenres);
                                ?></h4>
                                <p class="mb-0">Genre Unik</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-layer-group fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function filterBooks() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const genreFilter = document.getElementById('genreFilter').value.toLowerCase();
            const bookItems = document.querySelectorAll('.book-item');
            const noResultsMessage = document.getElementById('noResultsMessage');
            const noBooksMessage = document.querySelector('.no-books-message');
            let visibleBooks = 0;

            bookItems.forEach(item => {
                const title = item.getAttribute('data-title');
                const author = item.getAttribute('data-author');
                const genre = item.getAttribute('data-genre');
                
                const matchesSearch = title.includes(searchInput) || author.includes(searchInput);
                // PERBAIKAN: Gunakan exact match untuk genre
                const matchesGenre = !genreFilter || genre === genreFilter;
                
                if (matchesSearch && matchesGenre) {
                    item.style.display = 'block';
                    visibleBooks++;
                } else {
                    item.style.display = 'none';
                }
            });

            // Update book count
            document.getElementById('bookCount').textContent = visibleBooks + ' Buku';

            // Show/hide messages
            if (visibleBooks === 0) {
                if (bookItems.length > 0) {
                    // There are books but none match the filter
                    noResultsMessage.style.display = 'block';
                    if (noBooksMessage) noBooksMessage.style.display = 'none';
                } else {
                    // No books at all in the system
                    if (noBooksMessage) noBooksMessage.style.display = 'block';
                    noResultsMessage.style.display = 'none';
                }
            } else {
                noResultsMessage.style.display = 'none';
                if (noBooksMessage) noBooksMessage.style.display = 'none';
            }
        }

        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('genreFilter').value = '';
            filterBooks();
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Add search functionality to the search button
            document.querySelector('.search-btn').addEventListener('click', filterBooks);
            
            // Add Enter key support for search
            document.getElementById('searchInput').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    filterBooks();
                }
            });

            // Handle broken images
            const images = document.querySelectorAll('.book-image-large');
            images.forEach(img => {
                img.onerror = function() {
                    this.style.display = 'none';
                    const placeholder = this.nextElementSibling;
                    if (placeholder && placeholder.classList.contains('book-image-placeholder-large')) {
                        placeholder.style.display = 'flex';
                    }
                };
            });

            // Smooth scroll for books grid
            const booksGrid = document.getElementById('booksGrid');
            if (booksGrid) {
                booksGrid.style.scrollBehavior = 'smooth';
            }
        });
    </script>
</body>
</html>