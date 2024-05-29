<?php
require_once 'config/config.php';
require_once 'classes/User.php';
require_once 'classes/Translation.php';

session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$translator = new Translation();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $user = new User();
    $loggedInUser = $user->login($username, $password);

    if ($loggedInUser) {
        $_SESSION['user_id'] = $loggedInUser['id'];
        $_SESSION['is_admin'] = $loggedInUser['is_admin'];
        header('Location: index.php');
        exit();
    } else {
        $error = $translator->translate('invalid_credentials');
    }
}

include 'templates/header.php';
?>

<h2><?php echo $translator->translate('login'); ?></h2>

<?php if (isset($error)): ?>
    <p><?php echo $error; ?></p>
<?php endif; ?>

<form action="login.php" method="POST">
    <label for="username"><?php echo $translator->translate('username'); ?>:</label>
    <input type="text" id="username" name="username" required>
    <label for="password"><?php echo $translator->translate('password'); ?>:</label>
    <input type="password" id="password" name="password" required>
    <button type="submit"><?php echo $translator->translate('login'); ?></button>
</form>

<?php include 'templates/footer.php'; ?>
