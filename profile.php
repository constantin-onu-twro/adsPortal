<?php
require_once 'config/config.php';
require_once 'classes/User.php';
require_once 'classes/Translation.php';
require_once 'classes/Database.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$translator = new Translation();
$user = new User();

$userData = $user->getUserById($_SESSION['user_id']);
$profileImages = $user->getProfileImages($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_image'])) {
    $image = $_FILES['profile_image']['name'];
    $target = 'uploads/profile_images/' . basename($image);
    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target)) {
        $user->addProfileImage($_SESSION['user_id'], $image);
    }
    header('Location: profile.php');
    exit();
}

include 'templates/header.php';
?>

<h2><?php echo $translator->translate('profile'); ?></h2>
<p><?php echo $translator->translate('username'); ?>: <?php echo htmlspecialchars($userData['username']); ?></p>
<p><?php echo $translator->translate('email'); ?>: <?php echo htmlspecialchars($userData['email']); ?></p>
<p><?php echo $translator->translate('description'); ?>: <?php echo htmlspecialchars($userData['description']); ?></p>

<h3><?php echo $translator->translate('your_images'); ?></h3>
<form action="profile.php" method="POST" enctype="multipart/form-data">
    <input type="file" name="profile_image">
    <button type="submit"><?php echo $translator->translate('add_image'); ?></button>

</form>
<div class="profile-images">
    <?php foreach ($profileImages as $image): ?>
        <div class="profile-image">
            <img src="uploads/profile_images/<?php echo htmlspecialchars($image['image']); ?>" alt="Profile Image">
            <form action="delete_image.php" method="POST">
                <input type="hidden" name="image_id" value="<?php echo $image['id']; ?>">
                <button type="submit"><?php echo $translator->translate('remove_image'); ?></button>
            </form>
        </div>
    <?php endforeach; ?>
</div>
<?php include 'templates/footer.php'; ?>