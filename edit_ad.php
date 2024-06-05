<?php 
require_once 'config/config.php';
require_once 'classes/Ad.php';
require_once 'classes/Database.php';
require_once 'classes/Category.php';
require_once 'classes/Translation.php';

session_start();

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: index.php');
    exit();
}



$ad = new Ad();
$category = new Category();
$categories = $category->getCategories();
$translator = new Translation();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Prelucrare editare anunț
    $ad->updateAd($_POST['id'], $_POST['title'], $_POST['description'], $_POST['location'], $_POST['category'], $_FILES['new_images'], $_POST['tags'], $_POST['remove_images'], $_POST['rotate_images']);
    header('Location: index.php');
    exit();
} else {
    // Afișare formular editare anunț
    $adData = $ad->getAdById($_GET['id']);
}

include 'templates/header.php';
?>

<div class="container">
    <h2>Edit Ad</h2>
    <form action="edit_ad.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $adData['id']; ?>">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" class="form-control" value="<?php echo htmlspecialchars($adData['title']); ?>">
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control"><?php echo htmlspecialchars($adData['description']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="location">Location</label>
            <input type="text" name="location" id="location" class="form-control" value="<?php echo htmlspecialchars($adData['location']); ?>">
        </div>
        <div class="form-group">
            <label for="category">Category</label>
            <select name="category" id="category" class="form-control">
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>" <?php if ($adData['category_id'] == $category['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="tags">Tags</label>
            <input type="text" name="tags" id="tags" class="form-control" value="<?php echo htmlspecialchars(implode(', ', $adData['tags'])); ?>">
        </div>
        <div class="form-group">
            <label>Current Images</label>
            <div id="current-images">
                <?php foreach ($adData['images'] as $image): ?>
                    <div class="image-wrapper" data-image="<?php echo $image['image']; ?>">
                        <img src="uploads/<?php echo htmlspecialchars($image['image']); ?>" alt="" class="current-image">
                        <button type="button" class="btn btn-danger remove-image">Remove</button>
                        <button type="button" class="btn btn-info rotate-image">Rotate</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <input type="hidden" name="remove_images" id="remove_images" value="">
            <input type="hidden" name="rotate_images" id="rotate_images" value="">
        </div>
        <div class="form-group">
            <label for="new_images">New Images</label>
            <input type="file" name="new_images[]" id="new_images" class="form-control" multiple>
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const removeImages = [];
        const rotateImages = [];

        document.querySelectorAll('.remove-image').forEach(button => {
            button.addEventListener('click', function () {
                const imageWrapper = this.closest('.image-wrapper');
                const image = imageWrapper.getAttribute('data-image');
                removeImages.push(image);
                document.getElementById('remove_images').value = JSON.stringify(removeImages);
                imageWrapper.remove();
            });
        });

        document.querySelectorAll('.rotate-image').forEach(button => {
            button.addEventListener('click', function () {
                const imageWrapper = this.closest('.image-wrapper');
                const image = imageWrapper.getAttribute('data-image');
                rotateImages.push(image);
                document.getElementById('rotate_images').value = JSON.stringify(rotateImages);
                // Optionally, add a visual rotation effect
                const img = imageWrapper.querySelector('img');
                img.style.transform = img.style.transform === 'rotate(90deg)' ? 'rotate(180deg)' : 'rotate(90deg)';
            });
        });
    });
</script>

<?php include 'templates/footer.php'; ?>
