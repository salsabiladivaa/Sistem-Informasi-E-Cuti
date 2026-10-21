<?php
session_start();
require_once 'config/database.php';

$controller = isset($_GET['controller']) ? $_GET['controller'] : 'auth';
$action = isset($_GET['action']) ? $_GET['action'] : 'login';

if (!isset($_SESSION['user_id']) && $controller !== 'auth') {
    header("Location: index.php?controller=auth&action=login");
    exit();
}

switch ($controller) {
    case 'auth':
        require_once 'controllers/AuthController.php';
        $auth = new AuthController($pdo);
        if ($action == 'login') $auth->login();
        elseif ($action == 'logout') $auth->logout();
        break;

    case 'dashboard':
        require_once 'controllers/DashboardController.php';
        $dash = new DashboardController($pdo);
        $dash->index();
        break;

    case 'pegawai':
        require_once 'controllers/PegawaiController.php';
        $peg = new PegawaiController($pdo);
        $peg->index();
        break;

    case 'cuti':
        require_once 'controllers/CutiController.php';
        $cuti = new CutiController($pdo);
        if ($action == 'jenis') $cuti->jenis();
        elseif ($action == 'create') $cuti->create();
        elseif ($action == 'history') $cuti->history();
        break;

    default:
        header("Location: index.php?controller=dashboard&action=index");
        break;
}
?>