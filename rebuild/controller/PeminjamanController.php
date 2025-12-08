<?php
// controller/PeminjamanController.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/BukuModel.php';
require_once __DIR__ . '/../model/PeminjamanModel.php';

class PeminjamanController {
    private $db;
    private $bukuModel;
    private $peminjamanModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->bukuModel = new BukuModel($this->db);
        $this->peminjamanModel = new PeminjamanModel($this->db);
    }

    // Menampilkan daftar peminjaman aktif user
    public function index() {
        $peminjamanAktif = $this->peminjamanModel->getPeminjamanAktif($_SESSION['user_id']);
        include __DIR__ . '/../view/peminjaman/index.php';
    }

    // Form untuk meminjam buku baru
    public function create() {
        $books = $this->bukuModel->getAllBooks();
        include __DIR__ . '/../view/peminjaman/create.php';
    }

    // Proses menyimpan peminjaman baru
    public function store() {
        if($_POST) {
            $id_buku = $_POST['id_buku'] ?? '';
            
            if($this->peminjamanModel->pinjamBuku($_SESSION['user_id'], $id_buku)) {
                $_SESSION['success_message'] = "Buku berhasil dipinjam!";
                header("Location: index.php?controller=peminjaman&action=index");
                exit;
            } else {
                $_SESSION['error_message'] = "Gagal meminjam buku. Buku mungkin sedang dipinjam oleh orang lain.";
                header("Location: index.php?controller=peminjaman&action=create");
                exit;
            }
        } else {
            header("Location: index.php?controller=peminjaman&action=create");
            exit;
        }
    }

    // Proses mengembalikan buku
    public function kembalikan() {
        $id_peminjaman = $_GET['id'] ?? '';
        
        if($id_peminjaman && $this->peminjamanModel->kembalikanBuku($id_peminjaman)) {
            $_SESSION['success_message'] = "Buku berhasil dikembalikan!";
        } else {
            $_SESSION['error_message'] = "Gagal mengembalikan buku.";
        }
        
        header("Location: index.php?controller=peminjaman&action=index");
        exit;
    }

    // Menampilkan riwayat peminjaman
    public function riwayat() {
        $riwayatPeminjaman = $this->peminjamanModel->getRiwayatPeminjaman($_SESSION['user_id']);
        include __DIR__ . '/../view/peminjaman/riwayat.php';
    }
}
?>