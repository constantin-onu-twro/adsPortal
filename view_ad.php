<?php
require_once 'config/config.php';
require_once 'classes/Ad.php';
require_once 'classes/Translation.php';

$translator = new Translation();
$ad = new Ad();

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$ad_id = $_GET['id'];
$adDetails = $ad->getAdById($ad_id);

if (!$adDetails) {
    header('Location: index.php');
    exit();
}

include 'templates/header.php';
?>

<div class="ad-details">
    <h2><?php echo htmlspecialchars($adDetails['title']); ?></h2>
    <p><?php echo htmlspecialchars($adDetails['description']); ?></p>
    <p><?php echo htmlspecialchars($adDetails['location']); ?></p>
    <p><?php echo htmlspecialchars(implode(', ', array_column($adDetails['tags'], 'tag'))); ?></p>
    <div class="ad-images">
        <?php foreach ($adDetails['images'] as $image): ?>
            <img src="uploads/ad_images/<?php echo htmlspecialchars($image['image']); ?>" alt="Ad Image">
        <?php endforeach; ?>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
