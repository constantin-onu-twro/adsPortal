<?php
require_once 'config/config.php';
require_once 'classes/Ad.php';
require_once 'classes/Category.php';
require_once 'classes/Translation.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$translator = new Translation();
$ad = new Ad();
$category = new Category();

$categories = $category->getCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $tags = explode(',', $_POST['tags']);
    $category_id = $_POST['category_id'];

    $images = [];
    if (!empty($_FILES['images']['name'][0])) {
        $uploadDir = 'uploads/';
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        
        foreach ($_FILES['images']['name'] as $key => $name) {
            $tmpName = $_FILES['images']['tmp_name'][$key];
            $fileType = pathinfo($name, PATHINFO_EXTENSION);
            
            if (in_array($fileType, $allowedTypes)) {
                $uniqueName = uniqid('img_', true) . '.' . $fileType;
                $filePath = $uploadDir . $uniqueName;
                
                if (move_uploaded_file($tmpName, $filePath)) {
                    $images[] = $uniqueName;
                }
            }
        }
    }

    $ad_id = $ad->createAd($user_id, $title, $description, $location, $tags, $images, $category_id);
    
    header('Location: view_ad.php?id=' . $ad_id);
    exit();
}

include 'templates/header.php';
?>

<div class="container">
    <h2><?php echo $translator->translate('post_ad'); ?></h2>
    <form action="post_ad.php" method="post" enctype="multipart/form-data">
        <label for="title"><?php echo $translator->translate('title'); ?></label>
        <input type="text" name="title" id="title" required>
        
        <label for="description"><?php echo $translator->translate('description'); ?></label>
        <textarea name="description" id="description" maxlength="5000" required></textarea>
        
        <label for="location"><?php echo $translator->translate('location'); ?></label>
        <input type="text" name="location" id="location" required>
        
        <label for="tags"><?php echo $translator->translate('tags'); ?> (comma separated)</label>
        <input type="text" name="tags" id="tags">
        
        <label for="category_id"><?php echo $translator->translate('category'); ?></label>
        <select name="category_id" id="category_id" required>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
            <?php endforeach; ?>
        </select>
        
        <label for="images"><?php echo $translator->translate('images'); ?></label>
        <input type="file" name="images[]" id="images" multiple>
        
        <button type="submit"><?php echo $translator->translate('post_ad'); ?></button>
    </form>
</div>

<?php include 'templates/footer.php'; ?>
