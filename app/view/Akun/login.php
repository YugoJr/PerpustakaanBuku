<?php
require_once __DIR__ . '/../../controller/akuncontroller.php'; // sesuaikan path
session_start();

$db = new Database();
$conn = $db->getConnection();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? ''); // username atau email
    $password = $_POST['password'] ?? '';

    if ($identifier === '' || $password === '') {
        $errors[] = 'Isi semua bidang.';
    } else {
        try {
            $stmt = $conn->prepare("SELECT id, username, email, password FROM users WHERE username = ? OR email = ? LIMIT 1");
            $stmt->execute([$identifier, $identifier]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: /perpustakaanbuku/'); // arahkan ke dashboard
                exit;
            } else {
                $errors[] = 'Username/email atau password salah.';
            }
        } catch (Exception $e) {
            $errors[] = 'Terjadi kesalahan pada server.';
        }
    }
}
?>
<link rel="stylesheet" href="stylelogin.css">

<?php foreach ($errors as $err): ?>
    <div style="color:red;"><?php echo htmlspecialchars($err); ?></div>
<?php endforeach; ?>

<form method="POST" action="">
    <input type="text" name="identifier" placeholder="Username atau Email" value="<?php echo htmlspecialchars($_POST['identifier'] ?? ''); ?>"><br>
    <input type="password" name="password" placeholder="Password"><br>
    <button type="submit">Masuk</button>
</form>
