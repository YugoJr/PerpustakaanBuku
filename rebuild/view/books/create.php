<?php
// view/books/create.php
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php?controller=auth&action=login");
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
    <title>Tambah Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .image-preview {
            max-width: 200px;
            max-height: 250px;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 10px;
            display: none;
            margin-top: 10px;
        }
        .image-preview img {
            max-width: 100%;
            max-height: 200px;
            object-fit: contain;
        }
        .form-label {
            font-weight: 600;
        }
        .required::after {
            content: " *";
            color: #dc3545;
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
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="fas fa-plus-circle"></i> Tambah Buku Baru</h4>
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
                                               placeholder="Masukkan judul buku" required>
                                        <div class="form-text">Contoh: Laskar Pelangi, Bumi Manusia, dll.</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="author" class="form-label required">Penulis</label>
                                        <input type="text" class="form-control" id="author" name="author" 
                                               placeholder="Masukkan nama penulis" required>
                                        <div class="form-text">Nama lengkap penulis buku.</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="year" class="form-label required">Tahun Terbit</label>
                                        <input type="number" class="form-control" id="year" name="year" 
                                               min="0" max="<?php echo date('Y'); ?>" 
                                               placeholder="Tahun terbit" required>
                                        <div class="form-text">Tahun terbit buku (0 - <?php echo date('Y'); ?>).</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="genre" class="form-label required">Genre</label>
                                        <select class="form-select" id="genre" name="genre" required>
                                            <?php foreach($genres as $value => $label): ?>
                                                <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="form-text">Pilih genre yang sesuai dengan buku.</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="gambar" class="form-label">Gambar Buku</label>
                                        <input type="file" class="form-control" id="gambar" name="gambar" 
                                               accept="image/*" onchange="previewImage(this)">
                                        <div class="form-text">
                                            <small>
                                                <i class="fas fa-info-circle"></i> 
                                                Format yang didukung: JPG, JPEG, PNG, GIF, WebP. 
                                                Maksimal ukuran: 2MB.
                                            </small>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Preview Gambar</label>
                                        <div class="image-preview" id="imagePreview">
                                            <img id="preview" src="#" alt="Preview Gambar" class="img-fluid">
                                            <div class="text-center mt-2">
                                                <small class="text-muted">Preview gambar akan muncul di sini</small>
                                            </div>
                                        </div>
                                        <div id="noPreview" class="text-muted text-center py-4 border rounded">
                                            <i class="fas fa-image fa-2x mb-2"></i><br>
                                            <small>Belum ada gambar yang dipilih</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="confirm" required>
                                    <label class="form-check-label" for="confirm">
                                        Saya yakin data yang dimasukkan sudah benar
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <a href="index.php?controller=book&action=index" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Buku
                                </a>
                                <button type="submit" class="btn btn-success" id="submitBtn">
                                    <i class="fas fa-save"></i> Simpan Buku
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
        function previewImage(input) {
            const preview = document.getElementById('preview');
            const imagePreview = document.getElementById('imagePreview');
            const noPreview = document.getElementById('noPreview');
            
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
                    noPreview.style.display = 'none';
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                imagePreview.style.display = 'none';
                noPreview.style.display = 'block';
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
            document.getElementById('noPreview').style.display = 'block';
        });
    </script>
</body>
</html>