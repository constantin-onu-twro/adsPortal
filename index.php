<?php
require_once 'config/config.php';
require_once 'classes/Ad.php';
require_once 'classes/Translation.php';

session_start();

$translator = new Translation();
$ad = new Ad();
$ads = $ad->getAds();

$ad_details = $ad->getAdById($ad_id);
$ad_images = $ad->getAdImages($ad_id); // Obține imaginile pentru anunț

include 'templates/header.php';
?>

<h2><?php echo $translator->translate('ads'); ?></h2>
<div class="container">
    <h2><?php echo $translator->translate('all_ads'); ?></h2>
    <div class="ads">
        <?php foreach ($ads as $ad): ?>
            <div class="ad">
                <h3><?php echo htmlspecialchars($ad['title']); ?></h3>
                <p><?php echo htmlspecialchars($ad['description']); ?></p>
                <p><strong><?php echo $translator->translate('location'); ?>:</strong> <?php echo htmlspecialchars($ad['location']); ?></p>
                <p><strong><?php echo $translator->translate('category'); ?>:</strong> <?php echo htmlspecialchars($ad['category_name']); ?></p>
                
                <p><strong><?php echo $translator->translate('tags'); ?>:</strong> <?php echo htmlspecialchars(implode(', ', array_column($ad['tags'], 'tag'))); ?></p>
                
                <div class="gallery">
                    <?php foreach ($ad['images'] as $image): ?>
                        <img width="100px" src="uploads/<?php echo htmlspecialchars($image['image']); ?>" alt="<?php echo htmlspecialchars($ad['title']); ?>" class="gallery-img">
                    <?php endforeach; ?>
                </div>

                <?php if (!empty($ad['tags'])): ?>
                    <div class="tags">
                        <strong><?php echo $translator->translate('tags'); ?>:</strong>
                        <?php foreach ($ad['tags'] as $tag): ?>
                            <span class="tag"><?php echo htmlspecialchars($tag['tag']); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <a href="view_ad.php?id=<?php echo $ad['id']; ?>"><?php echo $translator->translate('view_ad'); ?></a>
                
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                    <a href="edit_ad.php?id=<?php echo $ad['id']; ?>" class="btn btn-warning"><?php echo $translator->translate('edit'); ?></a>
                    <a href="delete_ad.php?id=<?php echo $ad['id']; ?>" class="btn btn-danger"><?php echo $translator->translate('delete'); ?></a>
                <?php endif; ?>
                
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'templates/footer.php'; ?>