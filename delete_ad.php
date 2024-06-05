<? 
require_once 'config/config.php';
require_once 'classes/Ad.php';

session_start();

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: index.php');
    exit();
}

require_once 'config/config.php';
require_once 'classes/Ad.php';

$ad = new Ad();
if (isset($_GET['id'])) {
    $ad->deleteAd($_GET['id']);
}

header('Location: index.php');
exit();
?>