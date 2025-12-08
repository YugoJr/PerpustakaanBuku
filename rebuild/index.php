<?php
session_start();

// Define base URL untuk path yang konsisten
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']));
define('BASE_PATH', __DIR__);

$action = $_GET['action'] ?? 'index';
$controller = $_GET['controller'] ?? '';

// Simple autoloader for controllers
function loadController($controllerName) {
    $controllerFile = BASE_PATH . '/controller/' . $controllerName . '.php';
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        return true;
    }
    return false;
}

// Load models untuk landing page
function loadLandingData() {
    require_once BASE_PATH . '/config/database.php';
    require_once BASE_PATH . '/model/BukuModel.php';
    require_once BASE_PATH . '/model/AkunModel.php';
    
    $database = new Database();
    $db = $database->getConnection();
    $bukuModel = new BukuModel($db);
    $akunModel = new AkunModel($db);
    
    // Get statistics
    $totalBooks = $bukuModel->getTotalBooks();
    $totalUsers = $akunModel->getTotalUsers();
    
    // Hitung genre unik
    $genres = $bukuModel->getAllGenres();
    $totalUniqueGenres = $genres->rowCount();
    
    return [
        'totalBooks' => $totalBooks,
        'totalUsers' => $totalUsers,
        'totalUniqueGenres' => $totalUniqueGenres
    ];
}

// Jika tidak ada controller dan action, tampilkan landing page
if (empty($controller) && empty($action)) {
    $data = loadLandingData();
    extract($data);
    include BASE_PATH . '/view/landing.php';
    exit;
}

// Routing
switch($controller) {
    case 'auth':
        if (loadController('AuthController')) {
            $authController = new AuthController();
            switch($action) {
                case 'login':
                    $authController->login();
                    break;
                case 'register':
                    $authController->register();
                    break;
                case 'logout':
                    $authController->logout();
                    break;
                default:
                    $authController->login();
            }
        } else {
            die("AuthController tidak ditemukan!");
        }
        break;
    
    case 'book':
        // Check if user is logged in
        if(!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
        
        if (loadController('BookController')) {
            $bookController = new BookController();
            switch($action) {
                case 'index':
                    $bookController->index();
                    break;
                case 'create':
                    $bookController->create();
                    break;
                case 'edit':
                    $bookController->edit();
                    break;
                case 'delete':
                    $bookController->delete();
                    break;
                case 'deleteImage':
                    $bookController->deleteImage();
                    break;
                default:
                    $bookController->index();
            }
        } else {
            die("BookController tidak ditemukan!");
        }
        break;
    
    case 'peminjaman':
        // Check if user is logged in
        if(!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
        
        if (loadController('PeminjamanController')) {
            $peminjamanController = new PeminjamanController();
            switch($action) {
                case 'index':
                    $peminjamanController->index();
                    break;
                case 'create':
                    $peminjamanController->create();
                    break;
                case 'store':
                    $peminjamanController->store();
                    break;
                case 'kembalikan':
                    $peminjamanController->kembalikan();
                    break;
                case 'riwayat':
                    $peminjamanController->riwayat();
                    break;
                default:
                    $peminjamanController->index();
            }
        } else {
            die("PeminjamanController tidak ditemukan!");
        }
        break;
    
    case 'dashboard':
        // Check if user is logged in
        if(!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
        
        if (loadController('DashboardController')) {
            $dashboardController = new DashboardController();
            switch($action) {
                case 'admin':
                    $dashboardController->admin();
                    break;
                case 'user':
                    $dashboardController->user();
                    break;
                case 'index':
                    $dashboardController->index();
                    break;
                default:
                    $dashboardController->index();
            }
        } else {
            // Fallback ke dashboard sederhana
            if($_SESSION['role'] == 'admin') {
                include BASE_PATH . '/view/dashboard/admin.php';
            } else {
                include BASE_PATH . '/view/dashboard/user.php';
            }
        }
        break;

    case 'landing':
        $data = loadLandingData();
        extract($data);
        include BASE_PATH . '/view/landing.php';
        break;
    
    default:
        // Default ke landing page jika controller tidak dikenali
        $data = loadLandingData();
        extract($data);
        include BASE_PATH . '/view/landing.php';
        exit;
}
?>