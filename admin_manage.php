<?php
require_once 'config/config.php';
require_once 'classes/Admin.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SESSION['is_admin'] != 1) {
    header('Location: index.php');
    exit();
}

$admin = new Admin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    switch ($action) {
        case 'delete_ad':
            $ad_id = $_POST['ad_id'];
            $admin->deleteAd($ad_id);
            break;
        case 'delete_user':
            $user_id = $_POST['user_id'];
            $admin->deleteUser($user_id);
            break;
        // Add more cases for other actions
    }
}

header('Location: admin_dashboard.php');
exit();
?>
