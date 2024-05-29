<?php
require_once 'config/config.php';
require_once 'classes/Translation.php';
require_once 'classes/Ad.php';

$translator = new Translation();
$ad = new Ad();
$ads = $ad->getAdsByTag('');

include 'templates/header.php';
?>

<div class="ads">
    <?php foreach ($ads as $ad): ?>
        <div class="ad">
            <h3><?php echo htmlspecialchars($ad['title']); ?></h3>
            <p><?php echo htmlspecialchars($ad['description']); ?></p>
            <p><?php echo htmlspecialchars($ad['location']); ?></p>
            <p><?php echo htmlspecialchars(implode(', ', array_column($ad['tags'], 'tag'))); ?></p>
        </div>
    <?php endforeach; ?>
</div>

<?php include 'templates/footer.php'; ?>
