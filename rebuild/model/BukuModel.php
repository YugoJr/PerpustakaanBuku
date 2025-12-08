<?php
// model/BukuModel.php
class BukuModel {
    private $conn;
    private $table_name = "buku";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllBooks() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function createBook($title, $author, $year, $genre, $gambar = null) {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET title=:title, author=:author, year=:year, genre=:genre, gambar=:gambar";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":author", $author);
        $stmt->bindParam(":year", $year);
        $stmt->bindParam(":genre", $genre);
        $stmt->bindParam(":gambar", $gambar);

        return $stmt->execute();
    }

    public function getBookById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateBook($id, $title, $author, $year, $genre, $gambar = null) {
        if ($gambar) {
            $query = "UPDATE " . $this->table_name . " 
                     SET title=:title, author=:author, year=:year, genre=:genre, gambar=:gambar 
                     WHERE id=:id";
        } else {
            $query = "UPDATE " . $this->table_name . " 
                     SET title=:title, author=:author, year=:year, genre=:genre 
                     WHERE id=:id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":author", $author);
        $stmt->bindParam(":year", $year);
        $stmt->bindParam(":genre", $genre);
        $stmt->bindParam(":id", $id);
        
        if ($gambar) {
            $stmt->bindParam(":gambar", $gambar);
        }

        return $stmt->execute();
    }

public function deleteBook($id) {
    try {
        // Mulai transaction
        $this->conn->beginTransaction();

        // 1. Hapus dulu data peminjaman yang terkait dengan buku ini
        $queryPeminjaman = "DELETE FROM peminjaman WHERE id_buku = ?";
        $stmtPeminjaman = $this->conn->prepare($queryPeminjaman);
        $stmtPeminjaman->bindParam(1, $id);
        $stmtPeminjaman->execute();

        // 2. Hapus file gambar jika ada
        $book = $this->getBookById($id);
        if ($book && $book['gambar']) {
            $file_path = __DIR__ . '/../uploads/' . $book['gambar'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        // 3. Hapus buku
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $result = $stmt->execute();

        // Commit transaction
        $this->conn->commit();
        return $result;

    } catch (Exception $e) {
        // Rollback transaction jika ada error
        $this->conn->rollBack();
        error_log("Error deleting book: " . $e->getMessage());
        return false;
    }
}

    public function getTotalBooks() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getRecentBooks($limit = 5) {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function getPopularBooks($limit = 5) {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY RAND() LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function searchBooks($search = '', $genre = '') {
        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";
        
        $params = [];
        
        if (!empty($search)) {
            $query .= " AND (title LIKE :search OR author LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        if (!empty($genre)) {
            $query .= " AND genre = :genre";
            $params[':genre'] = $genre;
        }
        
        $query .= " ORDER BY id DESC";
        
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt;
    }

    public function getAllGenres() {
        $query = "SELECT DISTINCT genre FROM " . $this->table_name . " WHERE genre IS NOT NULL AND genre != '' ORDER BY genre";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>