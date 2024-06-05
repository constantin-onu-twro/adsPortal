<?php
require_once 'config/config.php';
require_once 'classes/Ad.php';
require_once 'classes/Translation.php';
require_once 'classes/Database.php';

session_start();

$translator = new Translation();
$ad = new Ad();
$adDetails = $ad->getAdById($_GET['id']);

include 'templates/header.php';
?>

<div class="container">
    <h2><?php echo htmlspecialchars($adDetails['title']); ?></h2>
    <p><?php echo $translator->translate('location'); ?>: <?php echo htmlspecialchars($ad_details['location']); ?></p>
    <p><?php echo $translator->translate('category'); ?>: <?php echo htmlspecialchars($ad_details['category_name']); ?></p>
    
    <h3><?php echo $translator->translate('tags'); ?></h3>
    <p><?php echo htmlspecialchars(implode(', ', explode(',', $adDetails['tags']))); ?></p>
    
    <h3><?php echo $translator->translate('images'); ?></h3>
    <div class="gallery">
        <?php foreach ($adDetails['images'] as $image): ?>
            <div class="image">
                <img src="uploads/<?php echo htmlspecialchars($image['image']); ?>" alt="Ad Image">
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
