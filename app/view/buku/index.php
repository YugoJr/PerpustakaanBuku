<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Perpustakaan Buku</title>
    <meta name="description" content="Perpustakaan sederhana - daftar buku dan pencarian" />
    <link rel="stylesheet" href="../app/view/asset/style.css"/>
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <a class="brand" href="/">Perpustakaan<span class="muted">Buku</span></a>
            <button id="menuToggle" class="menu-toggle" aria-label="Buka menu">☰</button>
            <nav class="main-nav" id="mainNav" aria-label="Main navigation">
                <a href="#" class="active">Beranda</a>
                <a href="../app/view/Akun/register.php">Masuk</a>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container">
                <h1>Selamat datang di Perpustakaan Buku</h1>
                <p class="lead">Temukan buku favoritmu, jelajahi koleksi, dan pinjam dengan mudah.</p>

                <div class="search-row">
                    <input id="searchInput" type="search" placeholder="Cari judul, penulis, atau kata kunci..." aria-label="Cari buku" />
                    <select id="filterCategory" aria-label="Filter kategori">
                        <option value="all">Semua Kategori</option>
                        <option value="fik">Fiksi</option>
                        <option value="nonfik">Non-fiksi</option>
                        <option value="edu">Pendidikan</option>
                    </select>
                </div>
            </div>
        </section>

        <section id="koleksi" class="books-section container">
            <h2>Daftar Buku</h2>
            <p class="muted small">Menampilkan koleksi contoh. Gunakan pencarian untuk menyaring.</p>
<a href="../public/index.php?action=create">Tambah Buku</a>
<table border="1" cellpadding="6">
    <tr><th>ID</th><th>Buku</th><th>Author</th><th>Tahun</th><th>Aksi</th></tr>
    <?php foreach ($data as $row): ?>
        <tr>
            <td><?= $row['id']; ?></td>
            <td><?= $row['title']; ?></td>
            <td><?= $row['author']; ?></td>
            <td><?= $row['year']; ?></td>
            <td>
                <a href="index.php?action=edit&id=<?= $row['id']; ?>">Edit</a> | 
                <a href="index.php?action=delete&id=<?= $row['id']; ?>" onclick="return confirm('Yakin?')">Hapus</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
            <div id="booksGrid" class="books-grid" aria-live="polite"></div>
            <template id="bookTemplate">
                <article class="book-card">
                    <img class="cover" src="" alt="Sampul buku" />
                    <div class="card-body">
                        <h3 class="title"></h3>
                        <p class="author muted"></p>
                        <p class="desc small"></p>
                        <div class="meta">
                            <span class="category"></span>
                            <button class="btn btn-outline btn-details">Detail</button>
                        </div>
                    </div>
                </article>
            </template>
        </section>

        <section id="about" class="container about">
            <h2>Tentang</h2>
            <p class="small muted">Ini adalah aplikasi perpustakaan sederhana.</p>
        </section>
    </main>

    <footer class="site-footer">
        <div class="container">
            <p>&copy; <span id="year"></span> Perpustakaan Buku &ndash; dibuat rapi untuk demonstrasi.</p>
        </div>
    </footer>

    <noscript>
        <div class="no-js">JavaScript diperlukan untuk pengalaman penuh. Silakan aktifkan JavaScript di browser Anda.</div>
    </noscript>

    <script src="script.js" defer></script>
</body>
</html>