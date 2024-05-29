<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classifieds</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php"><?php echo $translator->translate('home'); ?></a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="profile.php"><?php echo $translator->translate('profile'); ?></a></li>
                    <li><a href="logout.php"><?php echo $translator->translate('logout'); ?></a></li>
                <?php else: ?>
                    <li><a href="login.php"><?php echo $translator->translate('login'); ?></a></li>
                    <li><a href="register.php"><?php echo $translator->translate('register'); ?></a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>
