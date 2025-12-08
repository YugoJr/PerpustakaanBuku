<?php
// view/books/edit.php
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php?controller=auth&action=login");
    exit;
}

if(!isset($book) || !$book) {
    header("Location: index.php?controller=book&action=index");
    exit;
}

// Sample genres
$genres = [
    '' => 'Pilih Genre',
    'Fiksi' => 'Fiksi',
    'Non-Fiksi' => 'Non-Fiksi',
    'Romance' => 'Romance',
    'Misteri' => 'Misteri',
    'Sains' => 'Sains',
    'Sejarah' => 'Sejarah',
    'Teknologi' => 'Teknologi',
    'Biografi' => 'Biografi',
    'Komik' => 'Komik',
    'Fantasi' => 'Fantasi',
    'Horror' => 'Horror',
    'Petualangan' => 'Petualangan',
    'Pendidikan' => 'Pendidikan',
    'Agama' => 'Agama',
    'Kesehatan' => 'Kesehatan',
    'Bisnis' => 'Bisnis',
    'Seni' => 'Seni',
    'Musik' => 'Musik',
    'Olahraga' => 'Olahraga'
];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .image-preview {
            max-width: 200px;
            max-height: 250px;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 10px;
            margin-top: 10px;
        }
        .image-preview img {
            max-width: 100%;
            max-height: 200px;
            object-fit: contain;
        }
        .current-image {
            max-width: 150px;
            max-height: 200px;
            object-fit: cover;
            border-radius: 4px;
            border: 2px solid #dee2e6;
        }
        .form-label {
            font-weight: 600;
        }
        .required::after {
            content: " *";
            color: #dc3545;
        }
        .image-container {
            text-align: center;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php?controller=dashboard&action=index">
                <i class="fas fa-book"></i> Perpustakaan Digital
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    <i class="fas fa-user-shield"></i> <?php echo $_SESSION['username']; ?> (<?php echo $_SESSION['role']; ?>)
                </span>
                <a class="nav-link" href="index.php?controller=auth&action=logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-warning text-white">
                        <h4 class="mb-0"><i class="fas fa-edit"></i> Edit Buku</h4>
                    </div>
                    <div class="card-body">
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="title" class="form-label required">Judul Buku</label>
                                        <input type="text" class="form-control" id="title" name="title" 
                                               value="<?php echo htmlspecialchars($book['title']); ?>" required>
                                        <div class="form-text">Judul lengkap buku.</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="author" class="form-label required">Penulis</label>
                                        <input type="text" class="form-control" id="author" name="author" 
                                               value="<?php echo htmlspecialchars($book['author']); ?>" required>
                                        <div class="form-text">Nama lengkap penulis.</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="year" class="form-label required">Tahun Terbit</label>
                                        <input type="number" class="form-control" id="year" name="year" 
                                               value="<?php echo htmlspecialchars($book['year']); ?>"
                                               min="1900" max="<?php echo date('Y'); ?>" required>
                                        <div class="form-text">Tahun terbit buku.</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="genre" class="form-label required">Genre</label>
                                        <select class="form-select" id="genre" name="genre" required>
                                            <?php foreach($genres as $value => $label): ?>
                                                <option value="<?php echo $value; ?>" 
                                                    <?php echo ($book['genre'] == $value) ? 'selected' : ''; ?>>
                                                    <?php echo $label; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="form-text">Pilih genre yang sesuai dengan buku.</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Gambar Saat Ini</label>
                                        <div class="image-container">
                                            <?php if(!empty($book['gambar'])): ?>
                                                <img src="uploads/<?php echo htmlspecialchars($book['gambar']); ?>" 
                                                     alt="<?php echo htmlspecialchars($book['title']); ?>" 
                                                     class="current-image mb-2"
                                                     onerror="this.style.display='none'; document.getElementById('noCurrentImage').style.display='block';">
                                                <div id="noCurrentImage" style="display: none;">
                                                    <div class="text-muted py-3 border rounded">
                                                        <i class="fas fa-image fa-2x mb-2"></i><br>
                                                        <small>Gambar tidak ditemukan</small>
                                                    </div>
                                                </div>
                                                <div class="form-text">
                                                    <small>
                                                        <i class="fas fa-info-circle"></i> 
                                                        Gambar saat ini. Upload gambar baru untuk mengganti.
                                                    </small>
                                                </div>
                                            <?php else: ?>
                                                <div class="text-muted py-4 border rounded">
                                                    <i class="fas fa-image fa-2x mb-2"></i><br>
                                                    <small>Tidak ada gambar</small>
                                                </div>
                                                <div class="form-text">
                                                    <small>
                                                        <i class="fas fa-info-circle"></i> 
                                                        Buku ini belum memiliki gambar.
                                                    </small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="gambar" class="form-label">Gambar Baru</label>
                                        <input type="file" class="form-control" id="gambar" name="gambar" 
                                               accept="image/*" onchange="previewNewImage(this)">
                                        <div class="form-text">
                                            <small>
                                                <i class="fas fa-info-circle"></i> 
                                                Kosongkan jika tidak ingin mengubah gambar. 
                                                Format: JPG, PNG, GIF, WebP. Maksimal 2MB.
                                            </small>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Preview Gambar Baru</label>
                                        <div class="image-preview" id="imagePreview" style="display: none;">
                                            <img id="preview" src="#" alt="Preview Gambar Baru" class="img-fluid">
                                            <div class="text-center mt-2">
                                                <small class="text-muted">Preview gambar baru</small>
                                            </div>
                                        </div>
                                        <div id="noNewPreview" class="text-muted text-center py-4 border rounded">
                                            <i class="fas fa-image fa-2x mb-2"></i><br>
                                            <small>Belum ada gambar baru yang dipilih</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="confirm" required>
                                    <label class="form-check-label" for="confirm">
                                        Saya yakin data yang diubah sudah benar
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <a href="index.php?controller=book&action=index" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <div>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Update Buku
                                    </button>
                                    <?php if(!empty($book['gambar'])): ?>
                                        <a href="index.php?controller=book&action=deleteImage&id=<?php echo $book['id']; ?>" 
                                           class="btn btn-outline-danger btn-sm"
                                           onclick="return confirm('Yakin ingin menghapus gambar buku ini?')">
                                            <i class="fas fa-trash"></i> Hapus Gambar
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h6><i class="fas fa-info-circle text-primary"></i> Informasi:</h6>
                        <ul class="mb-0">
                            <li>ID Buku: <strong><?php echo $book['id']; ?></strong></li>
                            <li>Genre saat ini: <strong><?php echo !empty($book['genre']) ? $book['genre'] : 'Belum diatur'; ?></strong></li>
                            <li>Data terakhir diupdate: <strong><?php echo date('d/m/Y H:i'); ?></strong></li>
                            <li>Field bertanda <span class="required"></span> wajib diisi</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewNewImage(input) {
            const preview = document.getElementById('preview');
            const imagePreview = document.getElementById('imagePreview');
            const noNewPreview = document.getElementById('noNewPreview');
            
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const fileSize = file.size / 1024 / 1024; // MB
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                
                // Validasi tipe file
                if (!allowedTypes.includes(file.type)) {
                    alert('Format file tidak didukung. Harap pilih file gambar (JPG, PNG, GIF, WebP).');
                    input.value = '';
                    return;
                }
                
                // Validasi ukuran file (max 2MB)
                if (fileSize > 2) {
                    alert('Ukuran file terlalu besar. Maksimal 2MB.');
                    input.value = '';
                    return;
                }
                
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    imagePreview.style.display = 'block';
                    noNewPreview.style.display = 'none';
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                imagePreview.style.display = 'none';
                noNewPreview.style.display = 'block';
            }
        }

        // Validasi form sebelum submit
        document.querySelector('form').addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const author = document.getElementById('author').value.trim();
            const year = document.getElementById('year').value;
            const genre = document.getElementById('genre').value;
            const confirm = document.getElementById('confirm').checked;
            
            if (!title || !author || !year || !genre || !confirm) {
                e.preventDefault();
                alert('Harap lengkapi semua field yang wajib diisi dan centang konfirmasi.');
            }
        });

        // Initialize preview state
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('imagePreview').style.display = 'none';
            document.getElementById('noNewPreview').style.display = 'block';
        });

        // Handle broken current image
        document.addEventListener('DOMContentLoaded', function() {
            const currentImage = document.querySelector('.current-image');
            if (currentImage) {
                currentImage.onerror = function() {
                    this.style.display = 'none';
                    const noCurrentImage = document.getElementById('noCurrentImage');
                    if (noCurrentImage) {
                        noCurrentImage.style.display = 'block';
                    }
                };
            }
        });
    </script>
</body>
</html>