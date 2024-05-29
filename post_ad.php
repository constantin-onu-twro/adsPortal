<?php
require_once 'config/config.php';
require_once 'classes/User.php';
require_once 'classes/Ad.php';
require_once 'classes/Translation.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$translator = new Translation();
$user = new User();
$ad = new Ad();
$categories = $ad->getCategories();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $tags = explode(',', $_POST['tags']);
    $images = $_FILES['images'];

    $imagePaths = [];
    foreach ($images['tmp_name'] as $key => $tmp_name) {
        $image = $images['name'][$key];
        $target = 'uploads/ad_images/' . basename($image);
        if (move_uploaded_file($tmp_name, $target)) {
            $imagePaths[] = $image;
        }
    }

    $ad->createAd($_SESSION['user_id'], $title, $description, $location, $tags, $imagePaths);
    header('Location: index.php');
    exit();
}

include 'templates/header.php';
?>

<form action="post_ad.php" method="POST" enctype="multipart/form-data">
    <label for="title"><?php echo $translator->translate('title'); ?>:</label>
    <input type="text" id="title" name="title" required>
    <label for="description"><?php echo $translator->translate('description'); ?>:</label>
    <textarea id="description" name="description" required></textarea>
    <label for="location"><?php echo $translator->translate('location'); ?>:</label>
    <input type="text" id="location" name="location" required>
    <label for="tags"><?php echo $translator->translate('tags'); ?>:</label>
    <input type="text" id="tags" name="tags" placeholder="Comma separated tags" required>
    <label for="images"><?php echo $translator->translate('images'); ?>:</label>
    <input type="file" id="images" name="images[]" multiple>
    <button type="submit"><?php echo $translator->translate('post_ad'); ?></button>
</form>

<?php include 'templates/footer.php'; ?>
