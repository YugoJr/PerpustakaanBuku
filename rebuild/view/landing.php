<?php
// view/landing.php - Tambahkan di bagian paling atas
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/BukuModel.php';

$database = new Database();
$db = $database->getConnection();
$bukuModel = new BukuModel($db);

// Handle search and filter
$search = $_GET['search'] ?? '';
$genre = $_GET['genre'] ?? '';

// Get all books first
$allBooks = $bukuModel->getAllBooks();
$filteredBooks = [];

// Apply filters
while($book = $allBooks->fetch(PDO::FETCH_ASSOC)) {
    $matchSearch = empty($search) || 
                  stripos($book['title'], $search) !== false || 
                  stripos($book['author'], $search) !== false;
    
    // PERBAIKAN: Filter genre yang benar
    $matchGenre = empty($genre);
    if (!empty($genre) && !empty($book['genre'])) {
        // Case-insensitive comparison for genre
        $matchGenre = strtolower(trim($book['genre'])) === strtolower(trim($genre));
    }
    
    if ($matchSearch && $matchGenre) {
        $filteredBooks[] = $book;
    }
}

// Convert to object for consistency
$recentBooks = new class($filteredBooks) {
    private $books;
    private $position = 0;
    
    public function __construct($books) {
        $this->books = $books;
    }
    
    public function rowCount() {
        return count($this->books);
    }
    
    public function fetch($mode = PDO::FETCH_ASSOC) {
        if ($this->position < count($this->books)) {
            return $this->books[$this->position++];
        }
        return false;
    }
    
    // Reset pointer for re-use
    public function execute() {
        $this->position = 0;
    }
};

$totalBooks = $bukuModel->getTotalBooks();

// Get actual genres from database instead of hardcoded
$genresResult = $bukuModel->getAllGenres();
$genres = ['' => 'Semua Genre'];

while($genreRow = $genresResult->fetch(PDO::FETCH_ASSOC)) {
    if (!empty($genreRow['genre'])) {
        $genres[strtolower($genreRow['genre'])] = $genreRow['genre'];
    }
}

// If no genres in database, use default ones
if (count($genres) <= 1) {
    $genres = [
        '' => 'Semua Genre',
        'fiksi' => 'Fiksi',
        'non-fiksi' => 'Non-Fiksi', 
        'romance' => 'Romance',
        'misteri' => 'Misteri',
        'sains' => 'Sains',
        'sejarah' => 'Sejarah',
        'teknologi' => 'Teknologi',
        'biografi' => 'Biografi',
        'komik' => 'Komik'
    ];
}

// Get statistics for stats section
$totalBooksCount = $bukuModel->getTotalBooks();
$uniqueGenres = [];
$allBooksForStats = $bukuModel->getAllBooks();
while($book = $allBooksForStats->fetch(PDO::FETCH_ASSOC)) {
    if (!empty($book['genre'])) {
        $uniqueGenres[$book['genre']] = true;
    }
}
$totalUniqueGenres = count($uniqueGenres);

// Placeholder for total users (you might want to get this from database)
$totalUsers = 150; // Example number
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Perpustakaan Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        .feature-icon {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 1rem;
        }
        .cta-button {
            background: #667eea;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .cta-button:hover {
            background: #764ba2;
            color: white;
            transform: translateY(-2px);
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
            height: 300px;
            object-fit: cover;
            width: 100%;
        }
        .book-image-placeholder {
            height: 300px;
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
            font-size: 1.1rem;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .book-author {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        .book-year {
            color: #95a5a6;
            font-size: 0.8rem;
        }
        .section-title {
            position: relative;
            margin-bottom: 3rem;
            text-align: center;
            color: #2c3e50;
        }
        .section-title:after {
            content: '';
            display: block;
            width: 60px;
            height: 3px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 10px auto;
            border-radius: 2px;
        }
        .view-more-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 10px 25px;
            border-radius: 25px;
            color: white;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        .view-more-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .search-section {
            background: #f8f9fa;
            padding: 40px 0;
        }
        .search-box {
            max-width: 800px;
            margin: 0 auto;
        }
        .search-input {
            border-radius: 25px 0 0 25px;
            border: 2px solid #667eea;
            border-right: none;
            padding: 12px 20px;
            font-size: 1rem;
        }
        .search-input:focus {
            box-shadow: none;
            border-color: #764ba2;
        }
        .search-btn {
            border-radius: 0 25px 25px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: 2px solid #667eea;
            color: white;
            padding: 12px 25px;
            transition: all 0.3s ease;
        }
        .search-btn:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            color: white;
        }
        .filter-select {
            border-radius: 25px;
            border: 2px solid #667eea;
            padding: 12px 20px;
            font-size: 1rem;
            background: white;
        }
        .filter-select:focus {
            box-shadow: none;
            border-color: #764ba2;
        }
        .active-filters {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        .filter-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        .clear-filters {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        .clear-filters:hover {
            color: #764ba2;
        }
        .results-count {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .book-genre-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(102, 126, 234, 0.9);
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-book"></i> Perpustakaan Digital
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php?controller=auth&action=login">Login</a>
                <a class="nav-link" href="index.php?controller=auth&action=register">Register</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="display-4 fw-bold mb-4">Selamat Datang di Perpustakaan Digital</h1>
            <p class="lead mb-4">Temukan dan kelola koleksi buku dengan mudah dalam satu platform</p>
            <div class="mt-4">
                <a href="index.php?controller=auth&action=register" class="cta-button me-3">Mulai Sekarang</a>
                <a href="index.php?controller=auth&action=login" class="btn btn-outline-light">Login</a>
            </div>
        </div>
    </section>

    <!-- Search Section -->
    <section class="search-section">
        <div class="container">
            <div class="search-box">
                <form method="GET" action="" class="row g-3 align-items-center">
                    <input type="hidden" name="controller" value="landing">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control search-input" 
                                   name="search" 
                                   placeholder="Cari judul atau penulis buku..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                            <button class="btn search-btn" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select filter-select" name="genre">
                            <?php foreach($genres as $value => $label): ?>
                                <option value="<?php echo $value; ?>" 
                                    <?php echo $genre == $value ? 'selected' : ''; ?>>
                                    <?php echo $label; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn view-more-btn w-100">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                    </div>
                </form>

                <!-- Active Filters -->
                <?php if(!empty($search) || !empty($genre)): ?>
                <div class="active-filters mt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold me-3">Filter Aktif:</span>
                            <?php if(!empty($search)): ?>
                                <span class="filter-badge me-2">
                                    <i class="fas fa-search me-1"></i> "<?php echo htmlspecialchars($search); ?>"
                                </span>
                            <?php endif; ?>
                            <?php if(!empty($genre) && isset($genres[$genre])): ?>
                                <span class="filter-badge me-2">
                                    <i class="fas fa-tag me-1"></i> <?php echo $genres[$genre]; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <a href="?" class="clear-filters">
                            <i class="fas fa-times me-1"></i>Hapus Filter
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="feature-icon">📖</div>
                    <h4>Koleksi Lengkap</h4>
                    <p>Akses berbagai jenis buku dari berbagai genre dan kategori</p>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-icon">🔍</div>
                    <h4>Pencarian Mudah</h4>
                    <p>Temukan buku yang Anda cari dengan sistem pencarian yang powerful</p>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-icon">👨‍💼</div>
                    <h4>Manajemen Terpusat</h4>
                    <p>Kelola inventaris buku dengan sistem yang terintegrasi</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Books Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="section-title mb-0">
                    <?php echo (!empty($search) || !empty($genre)) ? 'Hasil Pencarian' : 'Koleksi Buku Terbaru'; ?>
                </h2>
                <div class="results-count">
                    Menampilkan <?php echo $recentBooks->rowCount(); ?> dari <?php echo $totalBooks; ?> buku
                </div>
            </div>
            
            <div class="row">
                <?php if($recentBooks->rowCount() > 0): ?>
                    <?php while($book = $recentBooks->fetch(PDO::FETCH_ASSOC)): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card book-card h-100">
                            <div class="position-relative">
                                <?php if(!empty($book['gambar'])): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($book['gambar']); ?>" 
                                         class="book-image" 
                                         alt="<?php echo htmlspecialchars($book['title']); ?>"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <?php endif; ?>
                                <div class="book-image-placeholder" style="<?php echo !empty($book['gambar']) ? 'display: none;' : ''; ?>">
                                    <i class="fas fa-book fa-4x"></i>
                                </div>
                                <?php if(!empty($book['genre'])): ?>
                                    <span class="book-genre-badge"><?php echo htmlspecialchars($book['genre']); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="book-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                                <p class="book-author">Oleh: <?php echo htmlspecialchars($book['author']); ?></p>
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="book-year">Tahun: <?php echo htmlspecialchars($book['year']); ?></span>
                                        <small class="text-muted">ID: <?php echo $book['id']; ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <div class="alert alert-info py-5">
                            <i class="fas fa-search fa-3x mb-3"></i>
                            <h4><?php echo (!empty($search) || !empty($genre)) ? 'Buku tidak ditemukan' : 'Belum ada buku dalam koleksi'; ?></h4>
                            <p class="mb-3">
                                <?php if(!empty($search) || !empty($genre)): ?>
                                    Tidak ada buku yang sesuai dengan kriteria pencarian Anda.
                                <?php else: ?>
                                    Silakan daftar sebagai admin untuk menambahkan buku pertama.
                                <?php endif; ?>
                            </p>
                            <?php if(!empty($search) || !empty($genre)): ?>
                                <a href="?" class="btn btn-primary">
                                    <i class="fas fa-undo me-2"></i>Tampilkan Semua Buku
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if($recentBooks->rowCount() > 0 && (empty($search) && empty($genre))): ?>
            <div class="text-center mt-5">
                <a href="index.php?controller=auth&action=register" class="btn view-more-btn">
                    <i class="fas fa-book-reader me-2"></i>Daftar untuk Melihat Semua Buku
                </a>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-5">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3 mb-3">
                    <div class="stats-number"><?php echo $totalBooksCount; ?></div>
                    <p>Judul Buku</p>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stats-number"><?php echo $totalUniqueGenres; ?></div>
                    <p>Genre Buku</p>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stats-number"><?php echo $totalUsers; ?></div>
                    <p>Pengguna</p>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stats-number">24/7</div>
                    <p>Akses Online</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-primary text-white">
        <div class="container text-center">
            <h2 class="mb-4">Siap Memulai Perjalanan Membaca Anda?</h2>
            <p class="lead mb-4">Bergabunglah dengan komunitas pembaca kami dan temukan dunia pengetahuan yang tak terbatas</p>
            <a href="index.php?controller=auth&action=register" class="btn btn-light btn-lg">
                <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p>&copy; 2024 Perpustakaan Digital. All rights reserved.</p>
        </div>
    </footer>

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