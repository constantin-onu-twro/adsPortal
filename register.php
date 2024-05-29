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
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = new User();
    $registered = $user->register($username, $email, $password);

    if ($registered) {
        header('Location: login.php');
        exit();
    } else {
        $error = $translator->translate('registration_failed');
    }
}

include 'templates/header.php';
?>

<h2><?php echo $translator->translate('register'); ?></h2>

<?php if (isset($error)): ?>
    <p><?php echo $error; ?></p>
<?php endif; ?>

<form action="register.php" method="POST">
    <label for="username"><?php echo $translator->translate('username'); ?>:</label>
    <input type="text" id="username" name="username" required>
    <label for="email"><?php echo $translator->translate('email'); ?>:</label>
    <input type="email" id="email" name="email" required>
    <label for="password"><?php echo $translator->translate('password'); ?>:</label>
    <input type="password" id="password" name="password" required>
    <button type="submit"><?php echo $translator->translate('register'); ?></button>
</form>

<?php include 'templates/footer.php'; ?>
