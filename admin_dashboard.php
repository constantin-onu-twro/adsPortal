<?php
session_start();
require_once 'config/config.php';
require_once 'classes/User.php';
require_once 'classes/Translation.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit();
}

$translator = new Translation();
$user = new User();

include 'templates/admin_header.php';
?>

<h2><?php echo $translator->translate('admin_dashboard'); ?></h2>

<h3><?php echo $translator->translate('ads'); ?></h3>
<div class="ads">
    <?php foreach ($ads as $ad): ?>
        <div class="ad">
            <h3><?php echo htmlspecialchars($ad['title']); ?></h3>
            <p><?php echo htmlspecialchars($ad['description']); ?></p>
            <p><?php echo htmlspecialchars($ad['location']); ?></p>
            <p><?php echo htmlspecialchars(implode(', ', array_column($ad['tags'], 'tag'))); ?></p>
            <form action="admin_manage.php" method="POST">
                <input type="hidden" name="ad_id" value="<?php echo $ad['id']; ?>">
                <button type="submit" name="action" value="delete_ad"><?php echo $translator->translate('delete_ad'); ?></button>
            </form>
        </div>
    <?php endforeach; ?>
</div>

<h3><?php echo $translator->translate('users'); ?></h3>
<div class="users">
    <?php foreach ($users as $user): ?>
        <div class="user">
            <p><?php echo htmlspecialchars($user['username']); ?></p>
            <form action="admin_manage.php" method="POST">
                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                <button type="submit" name="action" value="delete_user"><?php echo $translator->translate('delete_user'); ?></button>
            </form>
        </div>
    <?php endforeach; ?>
</div>

<h3><?php echo $translator->translate('conversations'); ?></h3>
<div class="conversations">
    <?php foreach ($conversations as $conversation): ?>
        <div class="conversation">
            <!-- Display conversation details -->
        </div>
    <?php endforeach; ?>
</div>

<?php include 'templates/footer.php'; ?>
