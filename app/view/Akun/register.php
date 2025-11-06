<?php

require_once __DIR__ . '/../../controller/akuncontroller.php'; // sesuaikan jika lokasi berbeda
$db = new Database();
$conn = $db->getConnection();

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validasi sederhana
    if ($username === '') $errors[] = 'Username wajib diisi.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email tidak valid.';
    if (strlen($password) < 6) $errors[] = 'Password minimal 6 karakter.';

    if (empty($errors)) {
        try {
            // cek duplikat username/email
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                $errors[] = 'Username atau email sudah terdaftar.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $insert = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                if ($insert->execute([$username, $email, $hash])) {
                    // redirect ke login setelah sukses
                    header('Location: /perpustakaanbuku/app/view/Akun/login.php');
                    exit;
                } else {
                    $errors[] = 'Gagal menyimpan data. Coba lagi.';
                }
            }
        } catch (Exception $e) {
            $errors[] = 'Terjadi kesalahan pada server.';
        }
    }
}
?>
<?php foreach ($errors as $err): ?>
    <div style="color:red;"><?php echo htmlspecialchars($err); ?></div>
<?php endforeach; ?>

<?php if ($success): ?>
    <div style="color:green;"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<link rel="stylesheet" href="register.css">

<form method="POST" action="">
    <a href="javascript:history.back()" class="back-btn">← Kembali</a>
    <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"><br>
    <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"><br>
    <input type="password" name="password" placeholder="Password"><br>
    <button type="submit">Daftar</button>
</form>
