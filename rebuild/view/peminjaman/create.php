<?php
// view/peminjaman/create.php
if(!isset($_SESSION['user_id'])) {
    header("Location: index.php?controller=auth&action=login");
    exit;
}

// Get unique genres from books
$genres = [];
$books->execute(); // Reset pointer
while($book = $books->fetch(PDO::FETCH_ASSOC)) {
    if (!empty($book['genre'])) {
        $genres[$book['genre']] = $book['genre'];
    }
}
$books->execute(); // Reset pointer again for main loop
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pinjam Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
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
        .book-card {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            height: 100%;
        }
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .book-card.selected {
            border-color: #28a745;
            background-color: #e8f5e8;
        }
        .book-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .book-image-placeholder {
            width: 100%;
            height: 200px;
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            margin-bottom: 10px;
        }
        .book-title {
            font-weight: bold;
            font-size: 1rem;
            margin-bottom: 5px;
            color: #333;
        }
        .book-author {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        .book-info {
            color: #888;
            font-size: 0.8rem;
            margin-bottom: 5px;
        }
        .book-genre {
            font-size: 0.75rem;
            background: #28a745;
            color: white;
            padding: 3px 10px;
            border-radius: 15px;
            display: inline-block;
            margin-top: 5px;
        }
        .no-books {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }
        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .selected-book-section {
            background: #e8f5e8;
            border-left: 4px solid #28a745;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .selected-book-image {
            width: 80px;
            height: 100px;
            object-fit: cover;
            border-radius: 6px;
            margin-right: 15px;
        }
        .selected-book-image-placeholder {
            width: 80px;
            height: 100px;
            background: #f8f9fa;
            border: 1px dashed #dee2e6;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            margin-right: 15px;
        }
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
        <?php if(isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="fas fa-hand-holding"></i> Pinjam Buku Baru</h4>
                    </div>
                    <div class="card-body">
                        <!-- Search and Filter Section -->
                        <div class="search-section">
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
                                        <?php foreach($genres as $genreItem): ?>
                                            <option value="<?php echo htmlspecialchars($genreItem); ?>">
                                                <?php echo htmlspecialchars($genreItem); ?>
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

                        <form method="POST" action="index.php?controller=peminjaman&action=store">
                            <!-- Selected Book Display -->
                            <div id="selectedBookSection" class="selected-book-section" style="display: none;">
                                <h6 class="mb-3">Buku yang Dipilih:</h6>
                                <div class="d-flex align-items-center">
                                    <div id="selectedBookImage"></div>
                                    <div class="flex-grow-1">
                                        <h5 id="selectedBookTitle" class="mb-1"></h5>
                                        <p class="book-author mb-1" id="selectedBookAuthor"></p>
                                        <p class="book-info mb-1">
                                            Tahun: <span id="selectedBookYear"></span> | 
                                            Genre: <span id="selectedBookGenre"></span>
                                        </p>
                                    </div>
                                </div>
                                <input type="hidden" id="id_buku" name="id_buku" value="">
                            </div>

                            <!-- Books Grid -->
                            <div class="mb-4">
                                <h6 class="mb-3">Pilih Buku:</h6>
                                <div id="booksGrid" class="books-grid">
                                    <?php 
                                    $booksFound = false;
                                    while($book = $books->fetch(PDO::FETCH_ASSOC)): 
                                        $booksFound = true;
                                    ?>
                                        <div class="book-card card" 
                                             data-book-id="<?php echo $book['id']; ?>"
                                             data-title="<?php echo htmlspecialchars($book['title']); ?>"
                                             data-author="<?php echo htmlspecialchars($book['author']); ?>"
                                             data-year="<?php echo $book['year']; ?>"
                                             data-genre="<?php echo !empty($book['genre']) ? htmlspecialchars($book['genre']) : 'Tidak ada genre'; ?>"
                                             data-image="<?php echo !empty($book['gambar']) ? htmlspecialchars($book['gambar']) : ''; ?>"
                                             onclick="selectBook(this)">
                                            <div class="card-body text-center">
                                                <?php if(!empty($book['gambar'])): ?>
                                                    <img src="uploads/<?php echo htmlspecialchars($book['gambar']); ?>" 
                                                         class="book-image" 
                                                         alt="<?php echo htmlspecialchars($book['title']); ?>"
                                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <div class="book-image-placeholder" style="display: none;">
                                                        <i class="fas fa-book fa-2x"></i>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="book-image-placeholder">
                                                        <i class="fas fa-book fa-2x"></i>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <div class="book-title"><?php echo htmlspecialchars($book['title']); ?></div>
                                                <div class="book-author"><?php echo htmlspecialchars($book['author']); ?></div>
                                                <div class="book-info">Tahun: <?php echo $book['year']; ?></div>
                                                <?php if(!empty($book['genre'])): ?>
                                                    <div class="book-genre"><?php echo htmlspecialchars($book['genre']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                    
                                    <?php if(!$booksFound): ?>
                                        <div class="col-12">
                                            <div class="no-books">
                                                <i class="fas fa-book fa-3x mb-3"></i>
                                                <h5>Tidak ada buku tersedia</h5>
                                                <p>Silakan hubungi administrator untuk informasi lebih lanjut.</p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Informasi Peminjaman</h6>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled">
                                            <li><i class="fas fa-calendar-check text-success"></i> Tanggal Pinjam: <?php echo date('d/m/Y'); ?></li>
                                            <li><i class="fas fa-calendar-times text-warning"></i> Batas Kembali: <?php echo date('d/m/Y', strtotime('+7 days')); ?></li>
                                            <li><i class="fas fa-clock text-info"></i> Durasi: 7 Hari</li>
                                        </ul>
                                        <div class="alert alert-info">
                                            <small>
                                                <i class="fas fa-info-circle"></i> 
                                                Buku harus dikembalikan maksimal 7 hari dari tanggal peminjaman.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="index.php?controller=dashboard&action=user" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                 <button type="submit" class="btn btn-success" id="submitBtn" disabled>
                                     <i class="fas fa-check"></i> Konfirmasi Pinjam
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let selectedBook = null;

        function selectBook(bookElement) {
            // Remove selection from previously selected book
            if (selectedBook) {
                selectedBook.classList.remove('selected');
            }
            
            // Add selection to clicked book
            bookElement.classList.add('selected');
            selectedBook = bookElement;
            
            // Update selected book info
            const bookId = bookElement.getAttribute('data-book-id');
            const title = bookElement.getAttribute('data-title');
            const author = bookElement.getAttribute('data-author');
            const year = bookElement.getAttribute('data-year');
            const genre = bookElement.getAttribute('data-genre');
            const image = bookElement.getAttribute('data-image');
            
            // Update hidden input
            document.getElementById('id_buku').value = bookId;
            
            // Update selected book display
            document.getElementById('selectedBookTitle').textContent = title;
            document.getElementById('selectedBookAuthor').textContent = 'Oleh: ' + author;
            document.getElementById('selectedBookYear').textContent = year;
            document.getElementById('selectedBookGenre').textContent = genre;
            
            // Update image
            const imageContainer = document.getElementById('selectedBookImage');
            if (image) {
                imageContainer.innerHTML = `<img src="uploads/${image}" class="selected-book-image" alt="${title}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">`;
            } else {
                imageContainer.innerHTML = '<div class="selected-book-image-placeholder"><i class="fas fa-book"></i></div>';
            }
            
            // Show selected book section
            document.getElementById('selectedBookSection').style.display = 'block';
            
            // Enable submit button
            document.getElementById('submitBtn').disabled = false;
        }

        function filterBooks() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const genreFilter = document.getElementById('genreFilter').value.toLowerCase();
            const bookCards = document.querySelectorAll('.book-card');
            let visibleBooks = 0;
            
            bookCards.forEach(card => {
                const title = card.getAttribute('data-title').toLowerCase();
                const author = card.getAttribute('data-author').toLowerCase();
                const genre = card.getAttribute('data-genre').toLowerCase();
                
                const matchesSearch = title.includes(searchInput) || author.includes(searchInput);
                const matchesGenre = !genreFilter || genre.includes(genreFilter);
                
                if (matchesSearch && matchesGenre) {
                    card.style.display = 'block';
                    visibleBooks++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Show no books message if no books match the filter
            const noBooksElement = document.querySelector('.no-books');
            if (noBooksElement) {
                noBooksElement.style.display = visibleBooks === 0 ? 'block' : 'none';
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
        });
    </script>
</body>
</html>