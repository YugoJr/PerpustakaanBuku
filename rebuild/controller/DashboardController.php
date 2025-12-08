<?php
// controller/DashboardController.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/AkunModel.php';
require_once __DIR__ . '/../model/BukuModel.php';
require_once __DIR__ . '/../model/PeminjamanModel.php';

class DashboardController {
    private $db;
    private $akunModel;
    private $bukuModel;
    private $peminjamanModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->akunModel = new AkunModel($this->db);
        $this->bukuModel = new BukuModel($this->db);
        $this->peminjamanModel = new PeminjamanModel($this->db);
    }

    public function admin() {
        // Cek role admin
        if($_SESSION['role'] != 'admin') {
            header("Location: index.php?controller=dashboard&action=user");
            exit;
        }

        // Inisialisasi semua variable dengan default value
        $totalBooks = 0;
        $totalUsers = 0;
        $recentBooks = null;
        $books = null;
        $booksWithGenre = 0;
        $booksWithoutGenre = 0;
        $totalUniqueGenres = 0;
        $totalPeminjamanAktif = 0;
        $totalPeminjamanHariIni = 0;

        try {
            // Get statistics for admin dashboard
            $totalBooks = $this->bukuModel->getTotalBooks();
            $totalUsers = $this->akunModel->getTotalUsers();
            $recentBooks = $this->bukuModel->getRecentBooks(5);
            $books = $this->bukuModel->getAllBooks(); // Untuk daftar buku lengkap
            
            // Hitung statistik tambahan untuk bagian bawah
            if ($books) {
                $totalBooksCount = $books->rowCount();
                // Reset pointer untuk perhitungan
                $books->execute();
                
                $uniqueGenres = [];
                while($row = $books->fetch(PDO::FETCH_ASSOC)) {
                    if (!empty($row['genre'])) {
                        $booksWithGenre++;
                        $uniqueGenres[$row['genre']] = true;
                    }
                }
                $booksWithoutGenre = $totalBooksCount - $booksWithGenre;
                $totalUniqueGenres = count($uniqueGenres);
                
                // Reset pointer lagi untuk view
                $books->execute();
            }
            
            // Tambahkan statistik peminjaman untuk admin
            $totalPeminjamanAktif = $this->peminjamanModel->getTotalPeminjamanAktif();
            $totalPeminjamanHariIni = $this->peminjamanModel->getTotalPeminjamanHariIni();
            
        } catch (Exception $e) {
            error_log("Error in DashboardController::admin(): " . $e->getMessage());
            // Tetap lanjut dengan default values
        }
        
        // Debug info
        echo "<!-- Debug: totalBooks = $totalBooks, totalUsers = $totalUsers -->";
        
        include __DIR__ . '/../view/dashboard/admin.php';
    }

public function user() {
    // Get data for user dashboard
    $totalBooks = $this->bukuModel->getTotalBooks();
    
    // GANTI INI: Ambil SEMUA buku, bukan hanya recent/popular
    $allBooks = $this->bukuModel->getAllBooks(); // Method ini sudah ada di BukuModel
    
    // Tambahkan data peminjaman untuk user
    $totalDipinjam = $this->peminjamanModel->getTotalDipinjam($_SESSION['user_id']);
    $totalRiwayat = $this->peminjamanModel->getTotalRiwayat($_SESSION['user_id']);
    $peminjamanAktif = $this->peminjamanModel->getPeminjamanAktif($_SESSION['user_id']);
    
    include __DIR__ . '/../view/dashboard/user.php';
}

    public function index() {
        // Redirect to appropriate dashboard based on role
        if($_SESSION['role'] == 'admin') {
            header("Location: index.php?controller=dashboard&action=admin");
        } else {
            header("Location: index.php?controller=dashboard&action=user");
        }
        exit;
    }
}
?>