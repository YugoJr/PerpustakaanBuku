<?php
// controller/BookController.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/BukuModel.php';
require_once __DIR__ . '/../model/AkunModel.php'; // Tambahkan ini

class BookController {
    private $db;
    private $bukuModel;
    private $akunModel; // Tambahkan property

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->bukuModel = new BukuModel($this->db);
        $this->akunModel = new AkunModel($this->db); // Inisialisasi
    }

    public function index() {
        // Pindahkan semua logic ke DashboardController
        // Redirect ke dashboard admin agar data statistics ter-load
        header("Location: index.php?controller=dashboard&action=admin");
        exit;
    }

    public function create() {
        if($_POST) {
            $title = $_POST['title'] ?? '';
            $author = $_POST['author'] ?? '';
            $year = $_POST['year'] ?? '';
            $genre = $_POST['genre'] ?? '';
            
            // Handle file upload
            $gambar = $this->handleFileUpload();
            
            if($this->bukuModel->createBook($title, $author, $year, $genre, $gambar)) {
                $_SESSION['success_message'] = "Buku berhasil ditambahkan!";
                // Redirect ke DASHBOARD ADMIN, bukan book index
                header("Location: index.php?controller=dashboard&action=admin");
                exit;
            } else {
                $error = "Gagal menambah buku!";
                include __DIR__ . '/../view/books/create.php';
            }
        } else {
            include __DIR__ . '/../view/books/create.php';
        }
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if(!$id) {
            header("Location: index.php?controller=dashboard&action=admin");
            exit;
        }

        if($_POST) {
            $title = $_POST['title'] ?? '';
            $author = $_POST['author'] ?? '';
            $year = $_POST['year'] ?? '';
            $genre = $_POST['genre'] ?? '';
            
            // Handle file upload
            $gambar = $this->handleFileUpload();
            
            if($this->bukuModel->updateBook($id, $title, $author, $year, $genre, $gambar)) {
                $_SESSION['success_message'] = "Buku berhasil diupdate!";
                // Redirect ke DASHBOARD ADMIN, bukan book index
                header("Location: index.php?controller=dashboard&action=admin");
                exit;
            } else {
                $error = "Gagal mengupdate buku!";
                $book = $this->bukuModel->getBookById($id);
                include __DIR__ . '/../view/books/edit.php';
            }
        } else {
            $book = $this->bukuModel->getBookById($id);
            if(!$book) {
                header("Location: index.php?controller=dashboard&action=admin");
                exit;
            }
            include __DIR__ . '/../view/books/edit.php';
        }
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if($id) {
            if($this->bukuModel->deleteBook($id)) {
                $_SESSION['success_message'] = "Buku berhasil dihapus!";
            } else {
                $_SESSION['error_message'] = "Gagal menghapus buku!";
            }
        }
        // Redirect ke DASHBOARD ADMIN, bukan book index
        header("Location: index.php?controller=dashboard&action=admin");
        exit;
    }

    public function deleteImage() {
        $id = $_GET['id'] ?? null;
        if($id) {
            $book = $this->bukuModel->getBookById($id);
            if($book && $book['gambar']) {
                // Hapus file gambar
                $file_path = __DIR__ . '/../uploads/' . $book['gambar'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
                
                // Update database - set gambar to null
                $this->bukuModel->updateBook($id, $book['title'], $book['author'], $book['year'], $book['genre'], null);
                $_SESSION['success_message'] = "Gambar berhasil dihapus!";
            }
        }
        header("Location: index.php?controller=book&action=edit&id=" . $id);
        exit;
    }

    private function handleFileUpload() {
        if(isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/';
            
            // Create uploads directory if not exists
            if(!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileExtension = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if(in_array(strtolower($fileExtension), $allowedExtensions)) {
                // Generate unique filename
                $filename = uniqid() . '_' . time() . '.' . $fileExtension;
                $filePath = $uploadDir . $filename;
                
                if(move_uploaded_file($_FILES['gambar']['tmp_name'], $filePath)) {
                    return $filename;
                }
            }
        }
        return null;
    }
}
?>