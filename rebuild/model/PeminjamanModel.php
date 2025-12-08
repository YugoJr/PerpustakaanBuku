<?php
// model/PeminjamanModel.php
class PeminjamanModel {
    private $conn;
    private $table_name = "peminjaman";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Method untuk user
    public function pinjamBuku($id_user, $id_buku) {
        // Cek apakah buku sudah dipinjam dan belum dikembalikan
        $query = "SELECT id FROM " . $this->table_name . " 
                 WHERE id_buku = :id_buku AND status = 'dipinjam'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_buku", $id_buku);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            return false; // Buku sedang dipinjam
        }

        $query = "INSERT INTO " . $this->table_name . " 
                 SET id_user=:id_user, id_buku=:id_buku, 
                     tanggal_pinjam=CURDATE(), 
                     tanggal_kembali=DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_user", $id_user);
        $stmt->bindParam(":id_buku", $id_buku);
        return $stmt->execute();
    }

    public function kembalikanBuku($id_peminjaman) {
        $query = "UPDATE " . $this->table_name . " 
                 SET tanggal_kembali=CURDATE(), status='dikembalikan' 
                 WHERE id=:id AND status = 'dipinjam'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id_peminjaman);
        return $stmt->execute();
    }

    public function getPeminjamanAktif($id_user) {
        $query = "SELECT p.*, b.title, b.author, b.year 
                 FROM " . $this->table_name . " p 
                 JOIN buku b ON p.id_buku = b.id 
                 WHERE p.id_user = :id_user AND p.status = 'dipinjam' 
                 ORDER BY p.tanggal_pinjam DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_user", $id_user);
        $stmt->execute();
        return $stmt;
    }

    public function getRiwayatPeminjaman($id_user) {
        $query = "SELECT p.*, b.title, b.author, b.year 
                 FROM " . $this->table_name . " p 
                 JOIN buku b ON p.id_buku = b.id 
                 WHERE p.id_user = :id_user 
                 ORDER BY p.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_user", $id_user);
        $stmt->execute();
        return $stmt;
    }

    public function getTotalDipinjam($id_user) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                 WHERE id_user = :id_user AND status = 'dipinjam'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_user", $id_user);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getTotalRiwayat($id_user) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                 WHERE id_user = :id_user";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_user", $id_user);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Method untuk admin
    public function getTotalPeminjamanAktif() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                 WHERE status = 'dipinjam'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getTotalPeminjamanHariIni() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                 WHERE DATE(tanggal_pinjam) = CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getAllPeminjamanAktif() {
        $query = "SELECT p.*, b.title, b.author, a.username 
                 FROM " . $this->table_name . " p 
                 JOIN buku b ON p.id_buku = b.id 
                 JOIN akun a ON p.id_user = a.id_akun 
                 WHERE p.status = 'dipinjam' 
                 ORDER BY p.tanggal_pinjam DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>